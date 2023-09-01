<?php 
namespace DV\core\models;

class ProductVariant extends \WC_Product_Variation {
  public $parentProduct = null;

  const PROMOTEXT = '__promotionText';

  private $_content = null;
  public function getParent() {
    if($this->parentProduct === null)
      $this->parentProduct = wc_get_product($this->get_id());
    return $this->parentProduct;
  }

  public function setParent($parent) {
    $this->parentProduct = $parent;
  }

  public static function sortProductByPrice($products) {
    $returnProducts = [];
    foreach($products as $vProduct) {        
      if(count($returnProducts)==0) {
        $returnProducts[] = $vProduct;
        continue;
      }
      $sorted = false;
      $i = 0;
      while(!$sorted) {
        if($returnProducts[$i]->get_price()>$vProduct->get_price()) {
          array_splice($returnProducts, $i+1, 0, [$vProduct]);
          $sorted = true;
        }
        $i++;
        if(count($returnProducts) <= $i) {
          $returnProducts[] = $vProduct;
          $sorted = true;
        }
      }
    }
    return $returnProducts;
  }

  public function getAttributeName($attribute) {
    $returnName  = '';
    $variation_attributes = $this->get_attributes();
    foreach ( $variation_attributes as $name => $value ) {
      // If this is a term slug, get the term's nice name.
      if ( '' === $value || strtoupper($name) != strtoupper($attribute)) {
        continue;
      }
      $term = get_term($value, 'merchant');
      if($term)
        return $term->name;
    }
    return $returnName;
  }

  public function getDescription($context = 'view') {
    if($this->get_meta(self::PROMOTEXT, true) == "") {
      return $this->getParent()->get_short_description();
    }
    return $this->get_meta(self::PROMOTEXT, true);
  }

  public function getFormattedAttributes() {
    $return = '';
    $variation_attributes = $this->get_attributes();
		$product              = $this;
		$variation_name       = $this->get_title();
    $list_type = 'dl';

    if ( is_array( $variation_attributes ) ) {

      $return = '<' . $list_type . ' class="variation">';

      $variation_list = array();
      //$variation_list[] = '<dt>Service:</dt><dd>' . rawurldecode( $variation_name ) . '</dd>';
      foreach ( $variation_attributes as $name => $value ) {
        // If this is a term slug, get the term's nice name.
        
        if ( '' === $value || strtoupper($name) != strtoupper("Advertiser")) {
          continue;
        }
        $term = get_term($value, 'merchant');

        $variation_list[] = '<dt>Provider:</dt><dd>' . rawurldecode( $term->name ) . '</dd>';
      }

      $return .= implode( '', $variation_list );
      $return .= '</' . $list_type . '>';
    }
    return $return;
  }


}