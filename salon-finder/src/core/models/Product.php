<?php 
namespace DV\core\models;

use DV\core\Constants;
use SF\models\ProductVariant;

class Product extends \WC_Product_Variable {
  public $parentProduct = null;

  private $_content = null;

  private $_advertiser = null;
  private $_outlet = null;
  private $_outletCount = 0;

  private $_cheapestRegularPrice = null;
  private $_cheapestSalesPrice = null;
  private $_cheapestVariantID = null;
  private $_cheapestVariant = null;

  private $_parentCategory = null;
  private $_productCategory = null;

  public function getParentCategory($currentQueryTerm) {
    if($currentQueryTerm->parent && !$this->_parentCategory) {
      $this->_parentCategory = get_term($currentQueryTerm->parent);
    }
    return $this->_parentCategory;
  }

  public static function getFactory($product) {
    $product = new Product($product);
    return $product;
  }

  static function register() {
    $class = new Product();
    add_filter( 'woocommerce_product_data_store_cpt_get_products_query', [$class, 'previewProduct'], 10, 2 );
    add_filter( 'the_title', [$class, 'checkPromoTitle'], 10, 2 );
    add_filter( 'wpseo_title', [$class, 'changeMetaTitle'], 10, 1 ); 
    add_filter( 'wpseo_metadesc', [$class, 'changeMetaDescription'], 10, 1 ); 
    add_action( 'wpseo_register_extra_replacements', function() use($class) {
      wpseo_register_var_replacement( '%%promotion_product_count%%', [$class, 'SEOPromotionServices'], 'advanced', 'Some help text' );
    });
  } 

  public static function makeCategoryURL($productCategory) {
    $termObj = get_term($productCategory, 'product_cat');
    return site_url(Constants::Get('SERVICES_BASE_PATH')) . '/' .$termObj->slug;
  }

  public static function getVoucherValidity($voucherValidityDays, $format) {
    $date = new \DateTime('now', new \DateTimeZone('Asia/Singapore'));
    return $date->add(new \DateInterval('P'.$voucherValidityDays.'D'))->format($format);
  }

  public function SEOPromotionServices() {
    $randomProductIDs = wc_get_product_ids_on_sale();
    return count($randomProductIDs);
  }

  public function changeMetaTitle($title) {
    if(is_product()) {
      $_product = wc_get_product();
      $product = new Product($_product);
      $title = $product->get_title().' by '.$product->getMerchant()->name.' on Daily Vanity Salon Finder';
    }
    if(is_tax('product_cat')) {
      global $wp_query;
      $productCategory = get_queried_object(); 
      $location = get_query_var('location', null);
      if(!$location) {
        $title = $productCategory->name.' deals in Singapore to suit your beauty concerns and needs - We have '.$wp_query->found_posts.' of them';        
      } else {
        $title = $productCategory->name.' in '.$location.' of Singapore - We have '.$wp_query->found_posts.' of them';
      }
      if($productCategory->parent) {
        $parentCategory = $this->getParentCategory($productCategory);
        if(!$location) {
          $title = $parentCategory->name.' deals for '.$productCategory->name.' in Singapore - We have '.$wp_query->found_posts.' of them';
        } else {
          $title = $parentCategory->name.' deals for '.$productCategory->name.' in '.$location.' of Singapore - We have '.$wp_query->found_posts.' of them';
        }
      }
    }
    return $title;
  }

  public function changeMetaDescription($decriptions) {
    if(is_product()) {
      $_product = wc_get_product();
      $product = new Product($_product);
      $decriptions = 'Purchase the '.$product->get_name().' voucher from '.$product->getMerchant()->name.'. This is what you will get '.wp_strip_all_tags($product->get_description());
    }
    if(is_tax('product_cat')) {
      global $wp_query;
      $productCategory = get_queried_object(); 
      $location = get_query_var('location', null);
      if(!$location) {
        $decriptions = 'We have sourced around '.$wp_query->found_posts.' '.$productCategory->name.' deals for you to enjoy in Singapore! You may be able to find '.$productCategory->name.' deals with discounts as big as 90%';
      } else {        
        $decriptions = 'We have sourced around '.$wp_query->found_posts.' '.$productCategory->name.' deals for you to enjoy in the '.$location.' part of Singapore! You may be able to find '.$productCategory->name.' deals with discounts as big as 90%';
      }
      if($productCategory->parent) {
        $parentCategory = $this->getParentCategory($productCategory);
        if(!$location) {
          $decriptions = 'We have sourced around '.$wp_query->found_posts.' '.$parentCategory->name.' for '.$productCategory->name.' deals for you to enjoy in Singapore! You may be able to find '.$parentCategory->name.' for '.$productCategory->name.' deals with discounts as big as 90%';
        } else {          
          $decriptions = 'We have sourced around '.$wp_query->found_posts.' '.$parentCategory->name.' for '.$productCategory->name.' deals for you to enjoy in the '.$location.' part of Singapore! You may be able to find '.$parentCategory->name.' for '.$productCategory->name.' deals with discounts as big as 90%';
        }
      }
    }
    return $decriptions;
  }

  public function previewProduct( $query, $query_vars ) {
    if(isset($query_vars['previewProduct'])) 
      $query['has_password'] = $query_vars['previewProduct'];
    return $query;
  }

  public function checkPromoTitle($title, $id = null) {
    $product = wc_get_product($id);
    if($product) {
      if($product->is_on_sale()) {
        $promoText = $product->get_meta('promoName', true);
        if($promoText) {
          return $promoText;
        }
      }
    }
    return $title;
  }

  public function getSalesDescription() {
    if($this->is_on_sale()) {
      if($this->get_meta(ProductVariant::PROMOTEXT, true)) {
        return $this->get_meta(ProductVariant::PROMOTEXT, true);
      }
      if($this->getCheapestVariant() && $this->getCheapestVariant()->get_meta(ProductVariant::PROMOTEXT, true)) {
        if($this->getCheapestVariant()->get_meta(ProductVariant::PROMOTEXT, true)) {
          return $this->getCheapestVariant()->get_meta(ProductVariant::PROMOTEXT, true);
        }
        return $this->getCheapestVariant()->get_description();
      }
    }
    return "";
  }

  private function populateCorrectPrice() {
    $variants = $this->get_available_variations('objects');
    $this->_cheapestVariant = false;
    $this->_cheapestRegularPrice = 0;
      $this->_cheapestSalesPrice = 0;
    if($variants && isset($variants[0])) {
      $this->_cheapestRegularPrice = $variants[0]->get_regular_price();
      $this->_cheapestSalesPrice = $variants[0]->get_sale_price();
      $this->_cheapestVariant = $variants[0];
    }
  }

  public function getCheapestRegularPrice() {
    if($this->_cheapestRegularPrice === null) {
      $this->populateCorrectPrice();
    }
    return $this->_cheapestRegularPrice;
  }

  public function getCheapestSalesPrice() {
    if($this->_cheapestSalesPrice === null) {
      $this->populateCorrectPrice();
    }
    return $this->_cheapestSalesPrice;
  }

  public function getCheapestVariantID() {
    if($this->_cheapestVariantID === null) {
      $this->populateCorrectPrice();
    }
    return $this->_cheapestVariantID;
  }

  public function getCheapestVariant() {
    if($this->_cheapestVariantID === null) {
      $this->populateCorrectPrice();
    }
    return $this->_cheapestVariant;
  }

  public function getOutletVariant($outletID) {
    $variants=$this->get_available_variations();
    foreach($variants as $variant) {
      if(isset($variant['attributes']['attribute_branch']) && $variant['attributes']['attribute_branch'] == $outletID) {
        return new ProductVariant($variant['variation_id']);
      }
    }
    return null;
  }

  public function getDiscountPercent() {
    if($this->is_on_sale()) {
      $priceDiff = $this->get_variation_regular_price() - $this->get_variation_sale_price();
      return round( ( $priceDiff * 100 ) / $this->get_variation_regular_price() );
    }
    return 0;
  }

  private function populateAdvNOutlet() {
    $terms = wc_get_product_terms($this->get_id(), 'merchant');
    $this->_outlet = [];
    foreach($terms as $term) {
      if($term->parent == 0) {
        $this->_advertiser = $term;
      } else {
        $this->_outlet[] = $term;
      }
    }
    $this->_outletCount = count($this->_outlet);
  }

  public function getMerchant() {
    if($this->_advertiser === null) {
      $this->populateAdvNOutlet();
    }
    return $this->_advertiser;
  }

  public function getOutlets() {
    if($this->_outlet === null) {
      $this->populateAdvNOutlet();
    }
    return $this->_outlet;
  }

  public function getOutletCount() {
    if($this->_outlet === null) {
      $this->populateAdvNOutlet();
    }
    return $this->_outletCount;
  }

  public function getRedeemableOutlet() {
    $variants = $this->get_available_variations();
    $returnOutlets = [];
    foreach($variants as $variant) {      
      if($variant['variation_is_active'] && $variant['variation_is_visible']) {   
        $outlet = new Outlet($variant['attributes']['attribute_branch']);
        $product = new ProductVariant($variant['variation_id']);
        if($outlet && $product) {
          $returnOutlets[] = [
            'product'=> $product,
            'outlet'=> $outlet,
          ];
        }
        
      }
    }
    return $returnOutlets;
  }

  public static function findByForeignKey($id) {
    $args = array(
      'fk' => $id,
    );
    $products = wc_get_products( $args );
    if(count($products)>0) {
      return new Product($products[0]);  
    }
    return false;
  }

}