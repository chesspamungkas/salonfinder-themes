<?php 
namespace DV\core\models;

class Collection {
  public static $TERM_NAME = 'collection';
  static function register() {
    $class = new Collection();
    add_filter( 'init', [$class, 'addTaxToProduct']);
    add_filter( 'init', [$class, 'registerCollectionTaxonomy']);
    add_filter( 'wpseo_title', [$class, 'changeMetaTitle'], 10, 1 ); 
    add_filter( 'wpseo_metadesc', [$class, 'changeMetaDescription'], 10, 1 ); 
  }

  public function changeMetaTitle($title) {
    if(is_tax(self::$TERM_NAME)) {
      $term = get_queried_object();
      $parentTerm = null;
      if($term->parent) {
        $parentTerm = get_term($term->parent, 'collection');
        return $parentTerm->name.' '.$term->name.' Latest Promotions, Services, Operating Hours - Daily Vanity Salon Finder';
      }
    }
    return $title;
  }
  public function changeMetaDescription($decriptions) {
    if(is_tax(self::$TERM_NAME)) {
      $term = get_queried_object();
      $parentTerm = null;
      if($term->parent) {
        $parentTerm = get_term($term->parent, 'collection');
        $outlet = new Outlet($term);
        return "Find out ".$parentTerm->name."'s latest promotions, services, pricing and operating hours via Daily Vanity Salon Finder. ".$outlet->getMeta('outlet_description');
      }
    }
    return $decriptions;
  }
  function addTaxToProduct() {
    $labels = [
      "name" => __( "Collections", "custom-post-type-ui" ),
      "singular_name" => __( "Collection", "custom-post-type-ui" ),
      "parent_item" => "",
      "parent_item_colon" => "",
    ];
  
    $args = [
      "label" => __( "Collections", "custom-post-type-ui" ),
      "labels" => $labels,
      "public" => true,
      "publicly_queryable" => true,
      "hierarchical" => true,
      "show_ui" => true,
      "show_in_menu" => true,
      "show_in_nav_menus" => true,
      "query_var" => true,
      "rewrite" => [ 'slug' => 'collection', 'with_front' => true, ],
      "show_admin_column" => false,
      "show_in_rest" => true,
      "rest_base" => "collection",
      "rest_controller_class" => "WP_REST_Terms_Controller",
      "show_in_quick_edit" => false,
      ];
    register_taxonomy( "collection", [ "product" ], $args );
  }
  function registerCollectionTaxonomy() {
    $labels = [
      "name" => __( "Collections", "custom-post-type-ui" ),
      "singular_name" => __( "Collection", "custom-post-type-ui" ),
    ];
  
    $args = [
      "label" => __( "Collections", "custom-post-type-ui" ),
      "labels" => $labels,
      "public" => true,
      "publicly_queryable" => true,
      "hierarchical" => true,
      "show_ui" => true,
      "show_in_menu" => true,
      "show_in_nav_menus" => true,
      "query_var" => true,
      "rewrite" => [ 'slug' => 'collection', 'with_front' => true, ],
      "show_admin_column" => false,
      "show_in_rest" => true,
      "rest_base" => "collection",
      "rest_controller_class" => "WP_REST_Terms_Controller",
      "show_in_quick_edit" => false,
      ];
    register_taxonomy( "collection", [ "product" ], $args );
  }  

}