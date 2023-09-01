<?php 
namespace DV\shortcodes\SearchResultsListing;

use DV\core\models\Merchant;
use DV\core\RenderShortCode;
use DV\core\models\Outlet;
use DV\core\models\Product;
use ParagonIE\Sodium\Core\Curve25519\Ge\P2;

class SearchResultsListing extends RenderShortCode {
  private $_passwordProtect = false;
  private $_searchTerm = null;

  private $_locatedMerchantTermIDs = null;

  public static $_FILTERS_SEARCH_RESULT_TITLE = "SEARCH_RESULT_TITLE";
  public static $_ACTIONS_EMPTY_SEARCH_RESULT = "EMPTY_SEARCH_RESULT";

  public static function register() {
    $class = new SearchResultsListing();
    add_shortcode( 'search-results-listing', [$class, 'searchResults'] );
    add_action('pre_get_posts', [$class, 'addingVisibilityToQuery']);
    // add_action('pre_get_posts', [$class, 'productSearchQuery']); // Don't need to have number of post in title. So not quering.
    add_filter(self::$_FILTERS_SEARCH_RESULT_TITLE, [$class, 'transformTitle']);
    add_filter( 'wpseo_title', [$class, 'populateTitle'] );
    add_filter( 'wpseo_opengraph_title', [$class, 'populateTitle'] );
    add_filter( 'wpseo_twitter_title', [$class, 'populateTitle'] );
    add_filter( 'wpseo_twitter_description', [$class, 'populateDescription'] );
    add_filter( 'wpseo_opengraph_desc', [$class, 'populateDescription'] );
    add_filter( 'wpseo_metadesc', [$class, 'populateDescription'] );


    add_filter( 'wpseo_canonical', [$class, 'populateURL'] );
    add_filter( 'wpseo_twitter_site', [$class, 'populateURL'] );
    add_filter( 'wpseo_opengraph_url', [$class, 'populateURL'] );
    //add_filter( 'wpseo_metadesc', [$class, 'metaDescriptionSearchResult'] );
  }

  function setUpQuery($query, $args) {
    $query->query_vars = array_merge($query->query_vars, $args);
    return $query;
  }

  private function queryProductCategory($searchText, $commonArgs) {
    $termExists = get_terms([
      'taxonomy'=>'product_cat',
      'name__like'=>$searchText,        
    ]);
    if(!($termExists instanceof \WP_Error) && count($termExists)) {
      $termIDs = [];
      foreach($termExists as $termExist) {
        $termIDs[] = $termExist->term_id;
      }
      $args = $commonArgs;
      $args['tax_query'][] = [
        "taxonomy"=>'product_cat',
        "terms"=>  $termIDs,
      ];
      $query = new \WP_Query($args);
      if($query->found_posts>0) {
        return $args;
      }
    }
    return false;
  }

  private function queryMerchant($searchText, $commonArgs) {
    $termExists = get_terms([
      'taxonomy'=>'merchant',
      'name__like'=>$searchText,
    ]);
    if(!($termExists instanceof \WP_Error) && count($termExists)) {
      $termIDs = [];
      foreach($termExists as $termExist) {
        $termIDs[] = $termExist->term_id;
      }
      $args = $commonArgs;
      $args['tax_query'][] = [
        "taxonomy"=>'merchant',
        "terms"=>  $termIDs,
      ];
      $query = new \WP_Query($args);
      if($query->found_posts>0) {
        return $args;
      }
    }
    return false;
  }

  function productSearchQuery($query) {
    if($query->is_search() && $query->is_main_query() && get_query_var('s', false)) {
      $searchText = get_query_var('s', '');
      unset( $query->query_vars['s'] );
      $commonArgs = $this->addingLocationMerchange([
        'post_type'=>'product',
        'posts_per_page' => 12,
        'has_password'=>false,
        'paged'=>get_query_var('paged', 1),
        'orderby'   => 'meta_value_num',
        'meta_key'  => '_price',
        'order' => 'asc',
        'tax_query'=> [
          [
            'taxonomy'      => 'product_visibility',
            'field'         => 'name',
            'terms'         => [ 'exclude-from-search', 'hidden' ],
            'operator'      => 'NOT IN'
          ]
        ]
      ]);
      if(get_query_var('preview', false)) {
        $commonArgs['has_password'] = true;
      }
      
      add_filter( 'posts_where', [$this, 'makeTitleSearchOnly'] );
      $query = new \WP_Query($commonArgs);
      remove_filter('posts_where', [$this, 'makeTitleSearchOnly']);
      if($query->found_posts>0) {
        add_filter( 'posts_where', [$this, 'makeTitleSearchOnly'] );
        return $this->setUpQuery($query, $commonArgs);        
      }
      $args = $this->queryProductCategory($searchText, $commonArgs);
      if($args) {
        return $this->setUpQuery($query, $args);
      }
      $args = $this->queryMerchant($searchText, $commonArgs);
      if($args) {
        return $this->setUpQuery($query, $args);
      }
      $commonArgs['s'] = $searchText;
      return $this->setUpQuery($query, $commonArgs);
    }
  }

  function populateDescription($desc) {
    global $wp_query;
    $location = explode(',',get_query_var('location', ''));
      if(!$location[0]) {
        $location[0] = 'all';
      }
    $locationString = implode(', ', array_map('ucfirst', $location));
    if(is_tax(['product_cat', 'merchant'])) {
      $termObject = get_queried_object();      
      if($termObject->parent) {
        $parentCategory = get_term($termObject->parent, $termObject->taxonomy);
        return "We have sourced around {$wp_query->found_posts} {$parentCategory->name} for {$termObject->name} deals for you to enjoy in {$locationString} of Singapore! You may be able to find {$parentCategory->name} for {$termObject->name} deals with discounts as big as 90%.";
      } else {
        return "We have sourced around {$wp_query->found_posts} {$termObject->name} deals for you to enjoy in {$locationString} of Singapore! You may be able to find {$termObject->name} deals with discounts as big as 90%.";
      }
    }
    if($wp_query->is_search()) {
      $searchText = $wp_query->get('s', '');
      return "We have sourced {$searchText} deals for you to enjoy in the {$locationString} part of Singapore! You may be able to find beauty services deals with discounts as big as 90%";
    }
  }


  function populateURL($url) {    
    if(is_tax(['product_cat'])) {
      $termObject = get_queried_object();
      return Product::makeCategoryURL($termObject);
    }

    if(is_tax('merchant')) {
      $termObject = get_queried_object();
      return Merchant::makeURL($termObject);
      
    }
    // global $wp_query;
    // if($wp_query->is_search()) {
    //   return Merchant::makeURL($termObject);
    // }
    return $url;
  }

  function populateTitle($title) {
    global $wp_query;
    $location = explode(',',get_query_var('location', ''));
      if(!$location[0]) {
        $location[0] = 'all';
      }
    $locationString = implode(', ', array_map('ucfirst', $location));
    if(is_tax(['product_cat'])) {
      $termObject = get_queried_object();      
      if($termObject->parent) {
        $parentCategory = get_term($termObject->parent, $termObject->taxonomy);
        return "{$wp_query->found_posts} {$termObject->name} {$parentCategory->name} deals in {$locationString} of Singapore";
      } else {
        return "{$wp_query->found_posts} {$termObject->name} deals in {$locationString} of Singapore to suit your beauty concerns and needs";
      }
      
    }
    if(is_tax('merchant')) {
      $termObject = get_queried_object();
      if($termObject->parent) {
        $parentCategory = get_term($termObject->parent, $termObject->taxonomy);
        return "{$parentCategory->name} {$termObject->name} reviews, deals, and services in {$locationString} of Singapore";
      } else {
        return "{$termObject->name} reviews, deals, and services in {$locationString} of Singapore";
      }
      
    }
    if($wp_query->is_search()) {
      $searchText = $wp_query->get('s', '');
      return "{$searchText} in {$locationString} of Singapore";
    }
  }

  function addNextLinkAttr() {
    return 'class="load-more load-more-btn searchNextPage"';
  }

  function getNextPageLink() {
    $location = get_query_var('location', 'all');
    if($location == '') {
      $location = 'all';
    }
    $currentPage = get_query_var('paged', 1);
    if($currentPage === 0) {
      $currentPage = 1;
    }
    $onSales = get_query_var('on_sales', false);
    $nextPage = $currentPage + 1;
    $path = "services";
    $searchTerm = null;
    if(is_tax('product_cat')) {
      $termObject = get_queried_object();
      $searchTerm = $termObject->slug;
      $path = "services";
    } else if(is_tax('merchant')){
      $termObject = get_queried_object();
      $searchTerm = $termObject->slug;
      $path = "salons";
    } else if($onSales) {
      $path = "beauty-deals";
    } else {
      $searchTerm = get_query_var('s', '');
      $path = "search";
    }
    $searchPath = "";
    if($searchTerm !== null) {
      $searchPath = '/'.$searchTerm.'/'.$location;
    }
    return site_url($path.$searchPath.'/page/'.$nextPage);
  }

  function getType() {
    $type = get_query_var('searchType', 'salon');
    return $type;
  }

  function getMerchantNOutletIDfromLocation($location, $query = []) {
    $query = array_merge($query, [
      'hide_empty'=>true,
      'taxonomy'=>'merchant',
    ]);
    $postalCode = get_postal_codes((array)$location);
    if($postalCode && is_array($postalCode)) {
      $searchMeta = [];
      foreach($postalCode as $_postalCode) {
        foreach($_postalCode as $pcode) {
          $searchMeta[] = [
            'key'     => 'outlet_postalcode',
            'value'   => '^'.$pcode.'.+',
            'compare' => 'REGEXP'
          ];
        }
      }
      $query['meta_query'] = array_merge(['relation'=>'OR'], $searchMeta);
    }

    $merchantTerms = get_terms($query);   
   
    return $merchantTerms;
  }

  public function makeTitleSearchOnly($where) {
    global $wpdb;
    $searchText = get_query_var('s');
    $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $wpdb->esc_like( $searchText ) ) . '%\'';
    return $where;
  }

  public function addingLocationMerchange($argArray) {
    
    if(!$this->_locatedMerchantTermIDs) {
      $this->_locatedMerchantTermIDs = [];
      $location = explode(',',get_query_var('location', ''));
      
      if($location && count($location)>0 && $location[0] != '') {
        $locatedMerchantTerms = $this->getMerchantNOutletIDfromLocation($location);
        foreach($locatedMerchantTerms as $locatedMerchantTerm) {
          $this->_locatedMerchantTermIDs[] = $locatedMerchantTerm->term_id;
        }
      }
    }
    if(count($this->_locatedMerchantTermIDs)) {
      $existing = (isset($argArray['tax_query']) && count($argArray['tax_query']))?$argArray['tax_query']:[];
      $existing[] = [
        "taxonomy"=>'merchant',
        "terms"=>  $this->_locatedMerchantTermIDs,
      ];
      $existing['relation'] = 'AND';
      $argArray['tax_query'] = $existing;
    }
    return $argArray;
  }

  public function displaySearchResult() {        
    $location = explode(',',get_query_var('location', ''));
    $searchText = get_query_var('s');
    $onSales = get_query_var('on_sales', false);
    $notfound = false;    
    $commonArgs = $this->addingLocationMerchange([
      'post_type'=>'product',
      'posts_per_page' => 12,
      'has_password'=>false,
      'paged'=>get_query_var('paged', 1),
      'orderby'   => 'meta_value_num',
      'meta_key'  => '_price',
      'order' => 'asc',
      'tax_query'=> [
        [
        	'taxonomy'      => 'product_visibility',
        	'field'         => 'name',
        	'terms'         => [ 'exclude-from-search', 'hidden' ],
        	'operator'      => 'NOT IN'
        ]
      ]
    ]);
    if(get_query_var('preview', false)) {
      $commonArgs['has_password'] = true;
    }
    if($onSales) {
      $args = $commonArgs;
      $product_ids_on_sale = wc_get_product_ids_on_sale();
      $args['post__in'] = $product_ids_on_sale;
      $query = new \WP_Query($args);
    } else {
      add_filter( 'posts_where', [$this, 'makeTitleSearchOnly'] );
      $query = new \WP_Query($commonArgs);
      remove_filter('posts_where', [$this, 'makeTitleSearchOnly']);
    }
    if($query->found_posts<=0) {
      $termExists = get_terms([
        'taxonomy'=>'product_cat',
        'name__like'=>$searchText,        
      ]);
      if(!($termExists instanceof \WP_Error) && count($termExists)) {
        $termIDs = [];
        foreach($termExists as $termExist) {
          $termIDs[] = $termExist->term_id;
        }
        $args = $commonArgs;
        $args['tax_query'][] = [
          "taxonomy"=>'product_cat',
          "terms"=>  $termIDs,
        ];
        $query = new \WP_Query($args);
      }
    }
    
    if($query->found_posts<=0) {
      $termExists = get_terms([
        'taxonomy'=>'merchant',
        'name__like'=>$searchText,
      ]);
      if(!($termExists instanceof \WP_Error) && count($termExists)) {
        $termIDs = [];
        foreach($termExists as $termExist) {
          $termIDs[] = $termExist->term_id;
        }
        $args = $commonArgs;
        $args['tax_query'][] = [
          "taxonomy"=>'merchant',
          "terms"=>  $termIDs,
        ];
        $query = new \WP_Query($args);
      }
    }
    if($query->found_posts<=0) {
      $args = $commonArgs;
      $args['s'] = $searchText;
      $query = new \WP_Query($args);
    }

    if($query->found_posts<=0) {
      $args = $commonArgs;
      $query = new \WP_Query($args);
      $notfound = true;
      do_action(self::$_ACTIONS_EMPTY_SEARCH_RESULT, $searchText);
    }
    echo $this->render('SearchResultsListing/_list', ['query'=>$query, 'title'=>$onSales?"Beauty Deals":get_query_var('s',''), 'location'=>$location, 'notfound'=>$notfound]);
  }

  function searchResults() {

    add_filter('next_posts_link_attributes', [$this, 'addNextLinkAttr']);
    if(is_tax())
      return $this->displayServiceResults();    
    if(is_search()) {
      return $this->displaySearchResult();
    }
    if(is_product()) {
      return $this->displayServiceResults();
    }
    remove_filter('next_posts_link_attributes', [$this, 'addNextLinkAttr']);
  }

  

  public function addingVisibilityToQuery($query){
    if(is_tax(['product_cat', 'merchant'])) {
      $preview = get_query_var('preview', false);
      $location = $this->addingLocationMerchange([
        
      ]);
      
      if($preview) {
        $location['tax_query'][] = [
          'taxonomy'      => 'product_visibility',
          'field'         => 'name',
          'terms'         => [ 'exclude-from-search', 'exclude-from-catalog', 'hidden' ],
          'operator'      => 'IN'
        ];
        $query->set('has_password', true);
      } else {
        $location['tax_query'][] = [
          'taxonomy'      => 'product_visibility',
          'field'         => 'name',
          'terms'         => [ 'exclude-from-search', 'exclude-from-catalog', 'hidden' ],
          'operator'      => 'NOT IN'
        ];
        $query->set('has_password', false);
      }
      $query->set('tax_query', $location['tax_query']);
      $query->set('orderby', 'meta_value_num');
      $query->set('meta_key', '_price');
      $query->set('order', 'asc');
      $query->set('posts_per_page', 12);
    }
    return $query;
  }

  function displayServiceResults() {
    global $wp_query;
    $location = explode(',',get_query_var('location', ''));
    $notfound = false;
    $termObject = get_queried_object();
    $preview_product = get_query_var('preview', false);
    if($preview_product && post_password_required($wp_query->posts[0]->ID)) {
      echo $this->render('SearchResultsListing/_password-preview', ['query'=>$wp_query]);  
      return;
    }
    echo $this->render('SearchResultsListing/_list', ['query'=>$wp_query, 'title'=>$termObject->name, 'location'=>$location, 'notfound'=>$notfound]);
  }

  function transformTitle($title) {
    if(is_tax()) {
      $termObject = get_queried_object();
      switch(strtoupper($title)) {
        case 'NAIL':  return $title. ' Parlours';
          break;
        case 'HAIR': return $title. ' Salons';
          break;
        // default: $returnTitle .= $title;
      }
      $parentCategory = get_term($termObject->parent, $termObject->taxonomy);
      if($parentCategory && !($parentCategory instanceof \WP_Error)) {
        switch(strtoupper($parentCategory->name)) {
          case 'MASSAGE': 
              if(strtoupper($termObject->name) == 'FOOT') {
                return $title. ' reflexology';
              }
              return $title. ' massage';
            break;
          case 'MAKEUP':
            return $title. ' makeup';
            break;
          case 'HAIR REMOVAL':
              if(strtoupper($termObject->name) == 'BODY') {
                return $title . ' hair removal services';
              }
            break;
          case 'WEIGHT MANAGEMENT':
              if(strtoupper($termObject->name) == 'WHOLE BODY') {
                return $title. 'Whole body weight loss';
              }
            break;
        }
      }
    }
    return $title;
  }
}
