<?php
namespace DV\core;

class ShortCode {
  private static $_registerCode = [
    'top-header-bar'=>[ '\DV\shortcodes\TopHeaderBar\TopHeaderBar', 'init' ],    
    'search-results'=>[ '\DV\shortcodes\SearchResults\SearchResults', 'init' ],
    'latest-deals'=>[ '\DV\shortcodes\LatestDeals\LatestDeals', 'init' ],
    'collections'=>[ '\DV\shortcodes\Collections\Collections', 'init' ],
    'cat-navbar'=>[ '\DV\shortcodes\CategoryNavBar\CategoryNavBar', 'init' ],
    'random-services'=>[ '\DV\shortcodes\RandomServices\RandomServices', 'init' ],
    'breadcrumb'=>[ 'DV\shortcodes\Breadcrumb\Breadcrumb', 'init' ],
    'photo-gallery'=>[ 'DV\shortcodes\PhotoGallery\PhotoGallery', 'init' ],
    'product-details'=>[ 'DV\shortcodes\ProductDetails\ProductDetails', 'init' ],
    'similar-services'=>[ 'DV\shortcodes\SimilarServices\SimilarServices', 'init' ]
  ];

  public static function Add($key, $class) {
    self::$_registerCode = array_merge(self::$_registerCode, [$key=>$class]);
  }

  public static function Del($key) {
    if(isset(self::$_registerCode[$key])) {
      unset(self::$_registerCode[$key]);
    }
  }

  public static function init() {
    foreach(self::$_registerCode as $key=>$class) {
      add_shortcode($key, $class);
    }
  }
}