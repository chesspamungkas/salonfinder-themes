<?php
namespace DV\shortcodes\WebSearch;

use DV\core\Constants;
use DV\core\models\Merchant;
use DV\core\models\Product;
use DV\core\RenderShortCode;
use DV\core\ThemeBase;

class WebSearch extends RenderShortCode {
    private $_passwordProtect = false;
    private $_searchTerm = null;

    private $_TRANSIENT_KEY = "WEB_SEARCH_CACHE";

    public static $_BEFORE_CONTENT_ACTION_HOOK = "before_websearch_content";
    public static $_AFTER_CONTENT_ACTION_HOOK = "after_websearch_content";

    public static function register() {
      if( !defined( 'WP_CLI' ) || !\WP_CLI ) { 
        ThemeBase::AddScript('websearch-js', get_template_directory_uri().'/src/.dist/webSearch.js', [], true);
        $model = new WebSearch();
        add_action('init', [$model, 'init']);
      }
      
    }

    public function init() {
      $results = $this->getLookupArray(); 
      $resultJson = json_encode($results);
      $salonBaseURL = site_url(Constants::Get('SALON_BASE_PATH'));
      $servicesBaseURL = site_url(Constants::Get('SERVICES_BASE_PATH'));
      ThemeBase::AddInlineScript('websearch-js', "var webresults = {$resultJson}; var salonBaseURL='{$salonBaseURL}'; var servicesBaseURL='{$servicesBaseURL}';",'before');
      add_shortcode( 'web-search', [$this, 'searchBox'] );
    }

    public function getLookupArray() {
      $result = get_transient($this->_TRANSIENT_KEY);
      if(!$result) {
        $result = $this->setUpSearchCache();
      }
      return unserialize($result);
    }

    public function setUpSearchCache() {
      $cacheResult = [];
      $productCategories = get_terms([
        'taxonomy' => 'product_cat',
        'exclude' => array(34),
        'hide_empty' => false,
      ]);
      //print_r(get_taxonomies());
      $cacheResult['productCategories'] = [];
      $services = [];
      $makeValueOnly = function ($termModel) {
        if($termModel['child'] && count($termModel['child'])) {
          $termModel['child'] = array_values($termModel['child']);
        }
        return $termModel;
      };
      foreach($productCategories as $productCategory) {
        if($productCategory->parent==0) {
          $existingChildren = isset($services[$productCategory->term_id]['child'])?$services[$productCategory->term_id]['child']:[];
          $services[$productCategory->term_id] = $productCategory->to_array();
          $services[$productCategory->term_id]['url'] = Product::makeCategoryURL($productCategory);
          $services[$productCategory->term_id]['child'] = $existingChildren;
        }
        if($productCategory->parent != 0) {
          $services[$productCategory->parent]['child'][$productCategory->term_id] = $productCategory->to_array();
          $services[$productCategory->parent]['child'][$productCategory->term_id]['url'] = Product::makeCategoryURL($productCategory);
        }
      }
      $cacheResult['productCategories'] = array_values(array_map($makeValueOnly,$services));
      $productCategories = null;
      unset($productCategories);
      $merchants = get_terms( array(
        'taxonomy' => 'merchant', 
        'hide_empty' => true
      ));
      $returnSalon = [];
      foreach($merchants as $merchant) {
        if($merchant->parent==0) {
          $existingChildren = isset($returnSalon[$merchant->term_id]['child'])?$returnSalon[$merchant->term_id]['child']:[];
          $returnSalon[$merchant->term_id] = $merchant->to_array();
          $returnSalon[$merchant->term_id]['url'] = Merchant::makeURL($merchant);
          $returnSalon[$merchant->term_id]['child'] = $existingChildren;
        }
        if($merchant->parent != 0) {
          $returnSalon[$merchant->parent]['child'][$merchant->term_id] = $merchant->to_array();
          $returnSalon[$merchant->parent]['child'][$merchant->term_id]['url'] = Merchant::makeURL($merchant);
        }
      }
      
      $cacheResult['merchant'] = array_values(array_map($makeValueOnly,$returnSalon));

      $cacheResult = serialize(esc_sql($cacheResult));
      set_transient($this->_TRANSIENT_KEY, $cacheResult, 24 * HOUR_IN_SECONDS);
      return $cacheResult;
    }

    public function searchBox() {
      $location = explode(',',get_query_var('location', ''));
      $searchText = get_query_var('s', '');
      $searchType = "search";
      $searchValue = urlencode(get_query_var('s', ''));
      if(is_tax(['product_cat', 'merchant'])) {
        $termObject = get_queried_object();
        $searchText = $termObject->name;
        $searchValue = $termObject->slug;
        
      }
      if(is_tax('merchant')) {
        $searchType = 'salons';
      }
      if(is_tax('product_cat')) {
        $searchType = 'services';
      }
      echo $this->render('WebSearch/display', ['location'=>$location, 'searchText'=>$searchText, 'searchType'=>$searchType, 'searchValue'=>$searchValue]);
    }
}