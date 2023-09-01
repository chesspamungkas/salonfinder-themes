<?php 
namespace DV\core\models;

use DV\core\Constants;

class Merchant {
  public static $TERM_NAME = 'merchant';
  public static $FOREIGN_KEY = 'sf_advertiser_id';
  static function register() {
    $class = new Merchant();
    //add_filter( 'init', [$class, 'addTaxToProduct']);
    add_filter( 'init', [$class, 'registerMerchantTaxonomy'], true);
    add_filter( 'wpseo_title', [$class, 'changeMetaTitle'], 10, 1 ); 
    add_filter( 'wpseo_metadesc', [$class, 'changeMetaDescription'], 10, 1 ); 
  }

  public static function findByForeignKey($id) {
	  $args = array(
		  'hide_empty' => false, // also retrieve terms which are not used yet
		  'meta_query' => array(
			  array(
				  'key'       => static::$FOREIGN_KEY,
				  'value'     => $id,
				  'compare'   => '='
			  )
		  ),
		  'taxonomy'  => static::$TERM_NAME,
    );
    
    $terms = get_terms( $args );
	  if($terms instanceof \WP_Error) {
		  return $terms;
	  }
	  return $terms[0];
  }

  public static function makeURL($merchant) {
    $termObj = get_term($merchant, 'merchant');
    return site_url(Constants::Get('SALON_BASE_PATH')) . '/' .$termObj->slug;
  }

  public function changeMetaTitle($title) {
    if(is_tax('merchant')) {
      $term = get_queried_object();
      $parentTerm = null;
      if($term->parent) {
        $parentTerm = get_term($term->parent, 'merchant');
        return $parentTerm->name.' '.$term->name.' Latest Promotions, Services, Operating Hours - Daily Vanity Salon Finder';
      }
    }
    return $title;
  }
  public function changeMetaDescription($decriptions) {
    if(is_tax('merchant')) {
      $term = get_queried_object();
      $parentTerm = null;
      if($term->parent) {
        $parentTerm = get_term($term->parent, 'merchant');
        $outlet = new Outlet($term);
        return "Find out ".$parentTerm->name."'s latest promotions, services, pricing and operating hours via Daily Vanity Salon Finder. ".$outlet->getMeta('outlet_description');
      }
    }
    return $decriptions;
  }
  function addTaxToProduct() {
    $labels = [
      "name" => __( "Merchants", "custom-post-type-ui" ),
      "singular_name" => __( "Merchant", "custom-post-type-ui" ),
    ];
  
    $args = [
      "label" => __( "Merchants", "custom-post-type-ui" ),
      "labels" => $labels,
      "public" => true,
      "publicly_queryable" => true,
      "hierarchical" => true,
      "show_ui" => true,
      "show_in_menu" => true,
      "show_in_nav_menus" => true,
      "query_var" => true,
      "rewrite" => [ 'slug' => 'merchant', 'with_front' => true, ],
      "show_admin_column" => false,
      "show_in_rest" => true,
      "rest_base" => "merchant",
      "rest_controller_class" => "WP_REST_Terms_Controller",
      "show_in_quick_edit" => false,
      ];
    print_r(register_taxonomy( "merchant", [ "product" ], $args ));
  }
  function registerMerchantTaxonomy() {
    $labels = [
      "name" => __( "Merchants", "custom-post-type-ui" ),
      "singular_name" => __( "Merchant", "custom-post-type-ui" ),
    ];
  
    $args = [
      "label" => __( "Merchants", "custom-post-type-ui" ),
      "labels" => $labels,
      "public" => true,
      "publicly_queryable" => true,
      "hierarchical" => true,
      "show_ui" => true,
      "show_in_menu" => true,
      "show_in_nav_menus" => true,
      "query_var" => true,
      "rewrite" => [ 'slug' => 'merchant', 'with_front' => true, ],
      "show_admin_column" => false,
      "show_in_rest" => true,
      "rest_base" => "merchant",
      "rest_controller_class" => "WP_REST_Terms_Controller",
      "show_in_quick_edit" => false,
      ];
    register_taxonomy( "merchant", [ "product" ], $args );
  }  
}