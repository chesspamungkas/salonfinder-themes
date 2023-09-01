<?php 
namespace DV\core\models;

class Outlet {
  public $queriedOutlet = null;
  public $merchant = null;

  public $outletMetaKey = '';

  private $_allProducts = [];
  public $promoProducts = [];
  public $otherProducts = [];

  private $_meta = [];

  private $otherOutlets = null;

  public $mainCategories = [];

  public $allProducts = [];

  public static $TERM_NAME = 'merchant';


  function __construct($id=null) {
    if($id) {
        $this->queriedOutlet = get_term($id, 'merchant');
    } else {
        $this->queriedOutlet = get_queried_object();
    }
    $this->outletMetaKey = 'merchant_'.$this->queriedOutlet->term_id;
    if($this->queriedOutlet->parent >0) {
      $this->merchant = get_term($this->queriedOutlet->parent);
    }
    $this->getProducts();
  }

  public function getGalleryImageAlt($index) {
    return $this->merchant->name." ".$this->queriedOutlet->name." Interior Shots ".$index;
  }

  public function getGallery($index=null) {
    $gallery = $this->getMeta('outlet_shopimages');
    if($gallery !== null) {
      if($index !== null) {
        return isset($gallery[$index])?$gallery[$index]:null;
      }
      return $gallery;
    }
    return [];
  }

  public function hasGallery() {
    return ($this->getGallery() && count($this->getGallery()))?true:false;
  }

  public function getOutletName() {

  }

  public function getMerchantOutlets() {
    if(!$this->otherOutlets)
      $this->otherOutlets = get_term_children($this->merchant->term_id, 'merchant');    
    return $this->otherOutlets;
  }

  public function joinMerchant($join) {
    global $wpdb;
    $join .= " 
    left join {$wpdb->term_relationships} r on r.object_id = {$wpdb->posts}.ID
    inner JOIN {$wpdb->term_taxonomy} merchantTerm ON (merchantTerm.term_id = {$this->queriedOutlet->term_id} and r.term_taxonomy_id = merchantTerm.term_taxonomy_id and merchantTerm.taxonomy = 'merchant')";
    return $join;
  }

  public function getMeta($key) {
    if(!isset($this->_meta[$key])) {
      $this->_meta[$key] = get_field($key, $this->outletMetaKey);
    }
    return $this->_meta[$key];
  }

  static function toDisplay($productID) {
    $status = ( isset($_GET['preview']) && $_GET['preview'] == 'true' ) ? array( 'publish', 'preview' ) : array( 'publish' );
    return in_array(get_post_status( $productID ), $status );
  }

  static function displayOpeningHours($time) {
    $returningTime = date_create_from_format('h:i A',$time);
    $timezone = 'Asia/Singapore';//get_option('timezone_string');
    date_timezone_set($returningTime, timezone_open($timezone));
    return $returningTime->format('g:i a');
  }

  function getAllProducts() {
    $arg = [
      'visibility' => 'catalog',
      'paginate'=> false,
      'status'=>'publish',
      'limit' => -1
    ];
    add_filter('posts_join', [$this, 'joinMerchant'] );
    $products = wc_get_products($arg);
    remove_filter('posts_join', [$this, 'joinMerchant'] );
    $promotionProducts = [];
    $otherProducts = [];
    foreach($products as $product) {
      if($product->is_on_sale()) {
        $promotionProducts[] = $product;
      } else {
        $otherProducts[] = $product;
      }
    }
    return array_merge($promotionProducts,$otherProducts ); 
  }
  
  function getProducts() {
    $arg = [
      'post_type'=>'product',
      'posts_per_page'=>-1,
      'status'=>'published',
      'fields'=>'ids'
    ];
    add_filter('posts_join', [$this, 'joinMerchant'] );
    $query = new \WP_Query( $arg );
    $productVariates = $query->get_posts();
    remove_filter('posts_join', [$this, 'joinMerchant'] );
    $loadedCats = [];
    foreach($productVariates as $productID) {
      $product = new Product($productID);
      $productVariate = $product->getOutletVariant($this->queriedOutlet->term_id);
      if($productVariate) {
        $this->_allProducts[] = $productVariate;
        if($productVariate->is_on_sale()) {
          $this->promoProducts[] = $productVariate;
        } else {
          $categories = $product->get_category_ids();
          foreach($categories as $catID) {
            if(isset($this->otherProducts[$catID])) {
              $this->otherProducts[$catID][] = [
                'variant'=>$productVariate,
                'product'=>$product,
              ];
              continue;
            }
            if(!isset($loadedCats[$catID])) {
              $loadedCats[$catID] = get_term($catID, 'product_cat');
            }
            if($loadedCats[$catID]->parent == 0) {
              $this->otherProducts[$catID] = [];
              $this->otherProducts[$catID][] = [
                'variant'=>$productVariate,
                'product'=>$product,
              ];
              $this->mainCategories[$catID] = $loadedCats[$catID];
              continue;       
            }
          }        
        }
      }
    }
    $sortedProduct = [];
    foreach($this->otherProducts as $catID=>$otherProduct) {
      $variantProducts = [];
      $parentProduct = [];
      foreach($otherProduct as $_otherProduct) {
        $variantProducts[] = $_otherProduct['variant'];
        $parentProduct[$_otherProduct['variant']->get_id()] = $_otherProduct['product'];
      }
      $sortedProduct = ProductVariant::sortProductByPrice($variantProducts);
      $this->otherProducts[$catID] = [];
      foreach($sortedProduct as $_sortedProduct) {
        $this->otherProducts[$catID][] = [
          'variant'=>$_sortedProduct,
          'product'=>$parentProduct[$_sortedProduct->get_id()],
        ];
      }
    }
    $this->promoProducts = ProductVariant::sortProductByPrice($this->promoProducts);
    return array_merge($this->promoProducts,$sortedProduct );
  }
}