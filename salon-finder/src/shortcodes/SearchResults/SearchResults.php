<?php 
namespace DV\shortcodes\SearchResults;
use DV\core\RenderShortCode;
use DV\core\models\Outlet;

class SearchResults extends RenderShortCode {
  private $_passwordProtect = false;
  private $_searchTerm = null;

  public static function register() {
    $class = new SearchResults();
    add_shortcode( 'search-results', [$class, 'searchResults'] );
    
  }

  function metaTitleSearchResult($title) {
    $pagename = get_query_var('pagename');  
    if(get_query_var('searchType', false) || get_query_var('preview', false))
      return $this->metaSearchResult('title', $title);
    return $title;
  }

  function metaDescriptionSearchResult($description) {
    $pagename = get_query_var('pagename');  
    if(get_query_var('searchType', false) || get_query_var('preview', false))
      return $this->metaSearchResult('description', $description);
    return $description;
  }

  function metaSearchResult($type, $originalText) {
    $results = get_query_var('searchResultShortcode', null);
    if(!$results && $this->getType()) {
      $page = get_query_var('page', 1);
      if($page == 0)
        $page = 1;
      if($this->getType() == 'salon') {
        $results = $this->setUpSalonData([
          'searchtext'=>get_query_var('search',''),
          'paged'=>$page,
          'salonid'=>get_query_var('salonID',0),
          'merchantslug'=>urldecode(get_query_var('advertiser_slug', '')),
          'preview_product'=>get_query_var('preview', false),
          'location'=> get_query_var('location',''), 
          'seotype'=>$type
        ]);
      }
      if($this->getType() == 'service') { 
        $results = $this->setUpServicesData([
          'searchtext'=>get_query_var('search',''),
          'paged'=>$page,
          'categoryid'=>get_query_var('categoryID',0),
          'merchantslug'=>urldecode(get_query_var('advertiser_slug', '')),
          'location'=> get_query_var('location',''),
          'seotype'=>$type
        ]);
      }
      if($this->getType() == 'beauty-deals') {
        $results = $this->setUpDealsData([
          'searchtext'=>get_query_var('search',''),
          'paged'=>$page,
          'categoryid'=>get_query_var('categoryID',0),
          'merchantslug'=>urldecode(get_query_var('advertiser_slug', '')),
          'location'=> get_query_var('location',''),
          'seotype'=>$type
        ]);
      }
    }
    switch($type) {
      case 'title': return $results['seoTitle'];
        break;
      case 'description': return $results['seoDescription'];
        break;
    }
  }

  function getType() {
    $type = get_query_var('searchType', 'salon');
    return $type;
  }

  function searchResults() {
    if($this->getType() == 'salon') {
      return $this->displaySalonResults();
    }
    if($this->getType() == 'service') {
      return $this->displayServiceResults();
    }
    if($this->getType() == 'beauty-deals') {
      return $this->displayDealsResults();
    }
  }

  

  function setUpNotFound($searchType, $location, $searchText) {
    global $wp;
    $returnTitle = '';
    $locationTitle = '-';
    if($location) {
      if (is_array($location)) {
        $location = array_filter($location);
      }
      else {
        $location = array_filter(explode(",",$location));
      }
      foreach($location as $key => $loc):
        $locationTitle .= $loc;
        if ($key !== array_key_last($location))
            $locationTitle .= ', ';
      endforeach;
    }
    $CFcountry = "";
    if(isset($_SERVER["HTTP_CF_IPCOUNTRY"])) {
      $CFcountry = $_SERVER["HTTP_CF_IPCOUNTRY"];
    }
    $userAgent = "ScrapingBot";
    if(isset($_SERVER['HTTP_USER_AGENT'])) {
      $userAgent = $_SERVER['HTTP_USER_AGENT'];
    }
    $datetime = new \DateTime();
    $notFound = [
      'search_type'=> $searchType,
      'keyword'=>$searchText,
      'timestamp'=>current_time('timestamp'),
      'datetime'=>date_format($datetime, 'd M Y h:i:s A \G\M\T\+\8'),
      'postal_location'=>$locationTitle,
      'ip_address'=>$this->get_the_user_ip(),
      'page_url'=>home_url( $wp->request ),
      'wp_user_email'=> '',
      'user_sso_id'=>'',
      'cloudflare_country'=> $CFcountry,
      'user_agent'=> $userAgent
    ];

    if(is_user_logged_in()) {
      $user = wp_get_current_user();
      $notFound['wp_user_email'] = $user->user_email;
      $notFound['user_sso_id'] = $user->get('sso_id')?$user->get('sso_id'):'';
    }
    else
    {
      $notFound['wp_user_email'] = 'UserUnknown';
    }

    $subject = "SF: ". $notFound['keyword'] . ' - ' . $notFound['postal_location'] . ' - ' . $notFound['wp_user_email'] . " (".$notFound['cloudflare_country'].")";

    // \DV\SF\helpers\SNSHelper::publish(\DV\SF\helpers\Constants::getFactory()->getConfig(\DV\SF\helpers\Constants::SNS_SEARCH_RESULT_TOPIC), $notFound);
    // To be change to use SNS email notification microservice
    $emailContent =<<<EMAIL
    <div>
      <b>Search Type:</b> {$notFound['search_type']} <br />
      <b>Keyword:</b> {$notFound['keyword']} <br />
      <b>Search Location:</b> {$notFound['postal_location']} <br />
      <b>Date and Time:</b> {$notFound['datetime']} <br />
      <b>IP:</b> {$notFound['ip_address']} <br />
      <b>CloudFlare Country Detect:</b> {$notFound['cloudflare_country']} <br />
      <b>Browser User Agent:</b> {$notFound['user_agent']} <br />
      <b>Previous URL:</b> {$notFound['page_url']} <br />
      <b>User Email:</b> {$notFound['wp_user_email']} <br />
    </div>
EMAIL;
    add_filter( 'wp_mail_content_type', [$this, 'changeEmailContentType']);
    wp_mail(SEARCH_EMPTY_TO_EMAIL, $subject, $emailContent);
    remove_filter( 'wp_mail_content_type', [$this, 'changeEmailContentType']);

    return $notFound;
  }

  function changeEmailContentType() {
    return 'text/html';
  }
  
  function setUpServicesData($args) {
    $paged = 0;
    $limit = 15;
    $searchtext = null;
    $categoryid = 0;
    $category = null;
    $title = '';
    $catSlug = [];
    $seotype = 'title';
    $location = null;
    $merchantslug = null;
    $notfound = '';
    // echo '<pre>'; print_r($args); echo '</pre>';die();
    extract($args, EXTR_OVERWRITE);
    $queryAry = [
      'visibility' => 'catalog',
      'status' => 'publish',
      'limit'=>$limit,
      'paginate'=> true,
      'orderby' => 'meta_value_num',
      'meta_key' => '_price',
      'order' => 'asc',
      'paged'=>$paged
    ];

    $metaTitle = '';
    $metaDescription = '';
    $parentTerm = null;
    if($location == 'null') {
      $location = null;
    }
    $category = null;
    // print_r($merchantslug);die();
    if($categoryid) {
      $category = get_term($categoryid, 'product_cat');
    } else if($merchantslug) {
      // $merchantslugexplode=array_filter(explode(",",$merchantslug));
      // foreach($merchantslugexplode as $merchantslug) {
        $category = get_term_by('slug', $merchantslug, 'product_cat');
      // }
    }

    if(!$category || is_wp_error($category)) {
      $category = null;   
    }
    // print_r($category);die();
    if(!$category) {
      $get_postal_code = array();
      $merchantslugexplode[] = $merchantslug;
      // print_r($merchantslugexplode);die();
      // foreach($merchantslugexplode as $merchantslug) {
        $get_postal_code = array_merge($get_postal_code,get_postal_codes($merchantslugexplode));
      // }

      if(!empty($get_postal_code) && !$location) {
        $location = $merchantslug;
        $merchantslug = null;
        set_query_var('location',$location);
      }
      elseif(!empty($get_postal_code) && $location) {
        $merchantslug = null;
        set_query_var('location',$location);
      } else {
        $queryAry['s'] = esc_sql($merchantslug);
        $searchtext = esc_sql($merchantslug);
        $title = $merchantslug;

        set_query_var('search',$merchantslug);
      }
    }
    
    $by_location=0;
    if($location) {
      if (is_array($location)) {
        $location=$location;
      }
      else {
        $location = array_filter(explode(",",$location));
      }
    }

    if($location) {
      $outletIDs = array();
      foreach($location as $index => $loc){
        $outletIDs = array_merge($outletIDs, $this->getMerchantNOutletIDfromLocation($loc, []));
      }

      // echo '<pre>'; print_r($outletIDs); echo '</pre>';die();
      if(count($outletIDs)) {
        $queryAry['tax_query'] = array(
          array(
            'taxonomy' => Outlet::$TERM_NAME,
            'field'    => 'id',
            'terms'    => $outletIDs,
          ),
        );
      }
      $title = "Services";
      $by_location=1;
      set_query_var('location', $location);
    } 
    // print_r($category);die();
    if($category) {
      $title = $category->name;
      $queryAry['category'] = $category->slug;
      if($category->parent) {
        $parentTerm = get_term($category->parent, 'product_cat');
      }
    }
    // echo '<pre>'; print_r($queryAry); echo '</pre>';die();
    $query = wc_get_products($queryAry);
    // echo '<pre>'; print_r($query->total); echo '</pre>';die();
    if($query->total<=0) {
      $metaDescription="Daily Vanity Salon Finder allows you to find the beauty salons and services near you. Discover Haircut, colouring, massage, facial and many more at the best price possible in Singapore!";
      $metaTitle = 'Sorry! This service cannot be found.';

      if ($merchantslug)
        $searchtext = esc_sql($merchantslug);
      else
        $searchtext = esc_sql($category);

      // print_r($searchtext);die();
      set_query_var('searchNotFound', $this->setUpNotFound('services', $location, $searchtext));
      unset($queryAry['s']);
      // unset($queryAry['tax_query']);
      unset($queryAry['category']);
      unset($queryAry['order']);

      $title = $metaTitle;
      $queryAry['limit'] = 18;
      $queryAry['orderby'] = 'rand';
      $notfound = 'yes';
      $limit = $queryAry['limit'];
      // echo '<pre>'; print_r($queryAry); echo '</pre>';die();
      $query = wc_get_products($queryAry);
      
    } else {
      if($parentTerm) {
        if(!$location) {
          $metaTitle = $parentTerm->name.' deals for '.$category->name.' in Singapore - We have '.$query->total.' of them';
          $metaDescription = 'We have sourced around '.$query->total.' '.$parentTerm->name.' for '.$category->name.' deals for you to enjoy in Singapore! You may be able to find '.$parentTerm->name.' for '.$category->name.' deals with discounts as big as 90%';
        } else {
          $metaTitle = $parentTerm->name.' deals for '.$category->name.' in '.$location.' of Singapore - We have '.$query->total.' of them';
          $metaDescription = 'We have sourced around '.$query->total.' '.$parentTerm->name.' for '.$category->name.' deals for you to enjoy in the '.$location.' part of Singapore! You may be able to find '.$parentTerm->name.' for '.$category->name.' deals with discounts as big as 90%';
        }
      }

      if(!$parentTerm && $category) {
        if(!$location) {
          $metaTitle = $category->name.' deals in Singapore to suit your beauty concerns and needs - We have '.$query->total.' of them';
          $metaDescription = 'We have sourced around '.$query->total.' '.$category->name.' deals for you to enjoy in Singapore! You may be able to find '.$category->name.' deals with discounts as big as 90%';
        } else {
          $metaTitle = $category->name.' in '.$location.' of Singapore - We have '.$query->total.' of them';
          $metaDescription = 'We have sourced around '.$query->total.' '.$category->name.' deals for you to enjoy in the '.$location.' part of Singapore! You may be able to find '.$category->name.' deals with discounts as big as 90%';
        }        
      }

      if(!$parentTerm && !$category && $searchtext) {
        $metaTitle="Daily Vanity Salon Finder Search Results";
        $metaDescription="Daily Vanity Salon Finder allows you to find the beauty salons and services near you. Discover Haircut, colouring, massage, facial and many more at the best price possible in Singapore!";
      }

      if(!$parentTerm && !$category && !$searchtext && $location) {
        $metaTitle = "Beauty deals from salons in the '.$location.' of Singapore - We have '.$query->total.' of them";
        $metaDescription = "We have sourced around '.$query->total.' beauty salon deals for you to enjoy in the '.$location.' part of Singapore! You may be able to find beauty services deals with discounts as big as 90%";
      }

      // print_r($queryAry);die();
      if(isset($queryAry['s'])) {
        if ($queryAry['s']==' ')
          $title = "Services";
      }
    }
    $getRequest = $_GET;
    $getRequest['page'] = $paged+1;
    // print_r($title);die();
    $title = $this->convertTitle($title, $parentTerm, $location, $query, $by_location, $notfound);
    // print_r($query);die();
    $result = ['query'=>$query, 'title'=>$title, 'preview'=>false, 'page'=>$paged, 'getRequest'=>$getRequest, 'seoTitle'=>$metaTitle, 'seoDescription'=>$metaDescription, 'location'=>$location, 'notfound'=>$notfound, 'limit'=>$limit];
    // print_r($result);die();
    set_query_var('searchResultShortcode', $result);
    return $result;
  }

  function setUpSalonData($args) {
    $paged = 0;
    $limit = 15;
    $searchtext = null;
    $salonid = 0;
    $location = null;
    $preview_product = false;
    $merchantslug = '';
    $parentTerm = null;
    $by_location = 0;
    $notfound = '';
    //  echo '<pre>'; print_r($args); echo '</pre>';die();
    extract($args, EXTR_OVERWRITE);
    $title = "";
    $outletIDs = array();
    $merchantName = [];
    $queryAry = [
      'status' => 'publish',
      'limit'=>$limit,
      'paginate'=> true,
      'previewProduct'=>$preview_product,
      'orderby' => 'meta_value_num',
      'meta_key' => '_price',
      'order' => 'asc',
      'paged'=>$paged
    ];

    if($location) {
      if (is_array($location)) {
        $location=$location;
      }
      else {
        $location = array_filter(explode(",",$location));
      }
    }

    // print_r($salonid);die();
    if($salonid == 0) {
      $args = [
        'hide_empty'=>true,
        'taxonomy'=>'merchant',
      ];
      if($searchtext) {
        $args['name__like'] = stripslashes_deep($searchtext);
        $title = $searchtext;
      }
      if($merchantslug) {
        $args['slug'] = $merchantslug;
      }
      $by_location=0;
      // print_r($location);die();
      if($location) {
        foreach($location as $index => $loc){
          $outletIDs = array_merge($outletIDs, $this->getMerchantNOutletIDfromLocation($loc, $args));
        }
        $by_location=1;
        set_query_var('location', $location);
      } else {
        // echo '<pre>'; print_r($args); echo '</pre>';die();
        $merchantTerms = get_terms($args);     
        // echo '<pre>'; print_r($merchantTerms); echo '</pre>';die();
        if($preview_product) {
          if($merchantTerms[0]) {
            $title = $merchantTerms[0]->name;
          }
        }
        foreach($merchantTerms as $merchantTerm) {
          // echo '<pre>'; print_r($merchantTerm); echo '</pre>';
          if($merchantTerm->parent>0) {
            $outletIDs = array_merge($outletIDs, array($merchantTerm->term_id));
          } else {
            $outletModelID = get_term_children($merchantTerm->term_id, Outlet::$TERM_NAME);
            $outletIDs = array_merge($outletIDs, $outletModelID);
            // echo '<pre>'; print_r($outletID); echo '</pre>';die();
          }
        }
        if($searchtext){
          $title = $merchantTerms[0]->name;
        }
        
        // echo '<pre>'; print_r($title); echo '</pre>';die();
        // echo '<pre>'; print_r($outletIDs); echo '</pre>';die();
      }
      
    } else {
      $outletIDs = $salonid;
      $term = get_term($salonid, Outlet::$TERM_NAME);
      
      if($term) {
        $title = $term->name;
      }
      set_query_var('location', $location);
    }
    // print_r($preview_product);die();
    if(!$preview_product) {
      $queryAry['visibility'] = 'catalog';
    }
    else{
      $queryAry['has_password']  = true;
    }
      
    if ($merchantslug)
      $searchtext = esc_sql($merchantslug);
    // echo '<pre>'; print_r($outletIDs); echo '</pre>';die();
    if($outletIDs) {
      $queryAry['tax_query'] = array(
        array(
          'taxonomy' => Outlet::$TERM_NAME,
          'field'    => 'id',
          'terms'    => $outletIDs
        ),
      );
      //  echo '<pre>'; print_r($queryAry); echo '</pre>';die();
      $query = wc_get_products($queryAry);
      // echo '<pre>'; print_r($query->total); echo '</pre>';die();
      if ($query->total<=0) {
        $title = 'Sorry! This service cannot be found.';

        unset($args['name__like']);
        unset($queryAry['order']);
        unset($queryAry['tax_query']);

        $queryAry['limit'] = 18;
        $queryAry['orderby'] = 'rand';
        $notfound = 'yes';
        $limit = $queryAry['limit'];
        $preview_product = 0;
        // echo '<pre>'; print_r($queryAry); echo '</pre>';die();
      
        $query = wc_get_products($queryAry);
        // print_r($query);die();
        $seoTitle="Daily Vanity Salon Finder Search Results";
        $metaDescription="Daily Vanity Salon Finder allows you to find the beauty salons and services near you. Discover Haircut, colouring, massage, facial and many more at the best price possible in Singapore!";
        set_query_var('searchNotFound', $this->setUpNotFound('salon', $location, $searchtext));
      }
      else{
          // print_r($query);die();
        $seoTitle = $title. ' deals in Singapore to suit your beauty concerns and needs';
        $metaDescription = 'We have sourced around '.$query->total.' '.$title.' deals for you to enjoy in Singapore! You may be able to find '.$title.' deals with discounts at big as 90%';
      }
    } else {
      $title = 'Sorry! This service cannot be found.';

      unset($args['name__like']);
      unset($args['slug']);

      if($location) {
        foreach($location as $index => $loc){
          $outletIDs = array_merge($outletIDs, $this->getMerchantNOutletIDfromLocation($loc, $args));
        }
      }
      // echo '<pre>'; print_r($outletIDs); echo '</pre>';die();
      $queryAry['tax_query'] = array(
        array(
          'taxonomy' => Outlet::$TERM_NAME,
          'field'    => 'id',
          'terms'    => $outletIDs,
        ),
      );

      $queryAry['limit'] = 18;
      $queryAry['orderby'] = 'rand';
      $notfound = 'yes';
      $limit = $queryAry['limit'];
     
      // echo '<pre>'; print_r($queryAry); echo '</pre>';die();
      $query = wc_get_products($queryAry);

      $seoTitle="Daily Vanity Salon Finder Search Results";
      $metaDescription="Daily Vanity Salon Finder allows you to find the beauty salons and services near you. Discover Haircut, colouring, massage, facial and many more at the best price possible in Singapore!";
      set_query_var('searchNotFound', $this->setUpNotFound('salon', $location, $searchtext));
    }
    $getRequest = $_GET;
    $getRequest['page'] = $paged+1;
    // print_r($title);die();
    $title = $this->convertTitle($title, $parentTerm, $location, $query, $by_location, $notfound);
    // print_r($query);die();
    $result = ['query'=>$query, 'title'=>$title, 'preview'=>$preview_product, 'page'=>$paged, 'seoTitle'=>$seoTitle, 'seoDescription'=>$metaDescription, 'getRequest'=>$getRequest, 'location'=>$location, 'notfound'=>$notfound, 'limit'=>$limit];
    // echo '<pre>'; print_r($result); echo '</pre>';die();
    set_query_var('searchResultShortcode', $result);
    return $result;
  }
  
  function setUpDealsData($args) {
    $paged = 0;
    $limit = 15;
    $searchtext = null;
    $categoryid = 0;
    $category = null;
    $title = '';
    $catSlug = [];
    $seotype = 'title';
    $location = null;
    $merchantslug = null;
    $randomProductIDs = wc_get_product_ids_on_sale();
    // print_r($args);die();
    extract($args, EXTR_OVERWRITE);
    // print_r($location);die();
    $queryAry = [
      'include'    => $randomProductIDs,
      'visibility' => 'catalog',
      'status' => 'publish',
      'limit'=>$limit,
      'paginate'=> true,
      'orderby' => 'meta_value_num',
      'meta_key' => '_price',
      'order' => 'asc',
      'paged'=>$paged
    ];

    $metaTitle = '';
    $metaDescription = '';
    $parentTerm = null;
    if($location == 'null') {
      $location = null;
    }
    $category = null;

    if($categoryid) {
      $category = get_term($categoryid, 'product_cat');
    } else if($merchantslug) {
      // $merchantslugexplode=array_filter(explode(",",$merchantslug));
      // foreach($merchantslugexplode as $merchantslug) {
        $category = get_term_by('slug', $merchantslug, 'product_cat');
      // }
    }

    if(!$category || is_wp_error($category)) {
      $category = null;   
    }

    if(!$category) {
      $get_postal_code = array();
      // print_r($merchantslug);die();
      // foreach($merchantslugexplode as $merchantslug) {
        $get_postal_code = array_merge($get_postal_code,get_postal_codes($merchantslug));
      // }

      if(!empty($get_postal_code) && !$location) {
        $location = $merchantslug;
        $merchantslug = null;
        set_query_var('location',$location);
      }
      elseif(!empty($get_postal_code) && $location) {
        // $location = $merchantslugexplode;
        $merchantslug = null;
        set_query_var('location',$location);
      } else {
        $queryAry['s'] = esc_sql($merchantslug);
        $searchtext = esc_sql($merchantslug);
        $title = $merchantslug;
        // print_r($merchantslug);die();

        set_query_var('search',$merchantslug);
      }
    }
    
    $by_location=0;
    if($location) {
      if (is_array($location)) {
        $location=$location;
      }
      else {
        $location = array_filter(explode(",",$location));
      }
    }

    if($location) {
      $outletIDs = array();
      foreach($location as $index => $loc){
        // $outlet[] = $this->getMerchantNOutletIDfromLocation($loc, []);
        $outletIDs = array_merge($outletIDs, $this->getMerchantNOutletIDfromLocation($loc, []));
      }

      if(count($outletIDs)) {
        $queryAry['tax_query'] = array(
          array(
            'taxonomy' => Outlet::$TERM_NAME,
            'field'    => 'id',
            'terms'    => $outletIDs,
          ),
        );
      }
      $title = "Beauty Deals";
      $by_location=1;
      set_query_var('location', $location);
    } 

    if($category) {
      $title = $category->name;
      $queryAry['category'] = $category->slug;
      if($category->parent) {
        $parentTerm = get_term($category->parent, 'product_cat');
      }
    }
    // echo '<pre>'; print_r($queryAry); echo '</pre>';die();
    $query = wc_get_products($queryAry);
    // echo '<pre>'; print_r($query->total); echo '</pre>';die();
    if($query->total<=0) {
      $metaDescription="Daily Vanity Salon Finder allows you to find the beauty salons and services near you. Discover Haircut, colouring, massage, facial and many more at the best price possible in Singapore!";
      $metaTitle = 'Sorry! There are no latest beauty deals for now.';

      // set_query_var('searchNotFound', $this->setUpNotFound('services', $location, $queryAry['s']));
      unset($queryAry['s']);
      // unset($queryAry['tax_query']);
      unset($queryAry['category']);
      unset($queryAry['order']);
      // if ( !isset( $_SESSION['random'] ) ) {
      //   $_SESSION['random'] = rand();
      // }
      // $orderby = $_SESSION['random'];
      // print_r(rand($orderby));die();
      $title = $metaTitle;
      $queryAry['limit'] = 18;
      $queryAry['orderby'] = 'rand';
      $notfound = 'yes';
      $limit = $queryAry['limit'];
      // echo '<pre>'; print_r($queryAry); echo '</pre>';die();
      $query = wc_get_products($queryAry);
      
    } else {
      if($parentTerm) {
        if(!$location) {
          $metaTitle = $parentTerm->name.' deals for '.$category->name.' in Singapore - We have '.$query->total.' of them';
          $metaDescription = 'We have sourced around '.$query->total.' '.$parentTerm->name.' for '.$category->name.' deals for you to enjoy in Singapore! You may be able to find '.$parentTerm->name.' for '.$category->name.' deals with discounts as big as 90%';
        } else {
          $metaTitle = $parentTerm->name.' deals for '.$category->name.' in '.$location.' of Singapore - We have '.$query->total.' of them';
          $metaDescription = 'We have sourced around '.$query->total.' '.$parentTerm->name.' for '.$category->name.' deals for you to enjoy in the '.$location.' part of Singapore! You may be able to find '.$parentTerm->name.' for '.$category->name.' deals with discounts as big as 90%';
        }
      }

      if(!$parentTerm && $category) {
        if(!$location) {
          $metaTitle = $category->name.' deals in Singapore to suit your beauty concerns and needs - We have '.$query->total.' of them';
          $metaDescription = 'We have sourced around '.$query->total.' '.$category->name.' deals for you to enjoy in Singapore! You may be able to find '.$category->name.' deals with discounts as big as 90%';
        } else {
          $metaTitle = $category->name.' in '.$location.' of Singapore - We have '.$query->total.' of them';
          $metaDescription = 'We have sourced around '.$query->total.' '.$category->name.' deals for you to enjoy in the '.$location.' part of Singapore! You may be able to find '.$category->name.' deals with discounts as big as 90%';
        }        
      }

      if(!$parentTerm && !$category && $searchtext) {
        $metaTitle="Daily Vanity Salon Finder Search Results";
        $metaDescription="Daily Vanity Salon Finder allows you to find the beauty salons and services near you. Discover Haircut, colouring, massage, facial and many more at the best price possible in Singapore!";
      }

      if(!$parentTerm && !$category && !$searchtext && $location) {
        $metaTitle = "Beauty deals from salons in the '.$location.' of Singapore - We have '.$query->total.' of them";
        $metaDescription = "We have sourced around '.$query->total.' beauty salon deals for you to enjoy in the '.$location.' part of Singapore! You may be able to find beauty services deals with discounts as big as 90%";
      }

      if ($queryAry['s']==' ')
        $title = "Beauty Deals";
    }
    $getRequest = $_GET;
    $getRequest['page'] = $paged+1;
    
    $title = $this->convertTitle( 'Beauty Deals', $parentTerm, $location, $query, $by_location, $notfound);
    $result = ['query'=>$query, 'title'=>$title, 'preview'=>false, 'page'=>$paged, 'getRequest'=>$getRequest, 'seoTitle'=>$metaTitle, 'seoDescription'=>$metaDescription, 'location'=>$location, 'notfound'=>$notfound, 'limit'=>$limit];

    set_query_var('searchResultShortcode', $result);
    return $result;
  }

  function get_the_user_ip() {
    if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {    
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
  }

  function displayServiceResults() {
    $queryResults = get_query_var('searchResultShortcode');
    $query = $queryResults['query'];
    $title = $queryResults['title'];
    $preview = $queryResults['preview'];
    $paged = $queryResults['page'];
    $getRequest = $queryResults['getRequest'];
    $getRequest = $_GET;
    $getRequest['page'] = $paged+1;
    $location = $queryResults['location'];
    $notfound = $queryResults['notfound'];
    $limit = $queryResults['limit'];

    echo $this->render('SearchResults/_list', ['query'=>$query, 'title'=>$title, 'preview'=>$preview, 'paged'=>$paged, 'getRequest'=>$getRequest, 'location'=>$location, 'notfound'=>$notfound, 'limit'=>$limit]);
  }

  function displaySalonResults() {
    $queryResults = get_query_var('searchResultShortcode');
    $query = $queryResults['query'];
    $title = $queryResults['title'];
    $preview_product = $queryResults['preview'];
    $paged = $queryResults['page'];
    $getRequest = $queryResults['getRequest'];
    $getRequest = $_GET;
    $getRequest['page'] = $paged+1;
    $location = $queryResults['location'];
    $notfound = $queryResults['notfound'];
    $limit = $queryResults['limit'];
    if($preview_product && $query->total>0 && $query->products[0]->is_visible()) {
    ?>
      <div class="container-fluid px-0 single-product">
        <div class="col text-center poppins-medium" style="min-height: 300px; margin-top:8rem !important;">
          <?php
          echo get_the_password_form($query->products[0]->get_id());
          ?>
        </div>
      </div>
    <?php
    }
    else{
      echo $this->render('SearchResults/_list', ['query'=>$query, 'title'=>$title, 'preview'=>$preview_product, 'paged'=>$paged, 'getRequest'=>$getRequest, 'location'=>$location, 'notfound'=>$notfound, 'limit'=>$limit]);
    }
  }

  function displayDealsResults() {
    $queryResults = get_query_var('searchResultShortcode');
    $query = $queryResults['query'];
    $title = $queryResults['title'];
    $preview = $queryResults['preview'];
    $paged = $queryResults['page'];
    $getRequest = $queryResults['getRequest'];
    $getRequest = $_GET;
    $getRequest['page'] = $paged+1;
    $location = $queryResults['location'];
    $notfound = $queryResults['notfound'];
    $limit = $queryResults['limit'];
    echo $this->render('SearchResults/_list', ['query'=>$query, 'title'=>$title, 'preview'=>$preview, 'paged'=>$paged, 'getRequest'=>$getRequest, 'location'=>$location, 'notfound'=>$notfound, 'limit'=>$limit]);
  }

  function convertTitle($title, $parentTerm, $location=null, $query, $by_location, $notfound) {
    $returnTitle = '';

    if( empty( $title ) ) {
      if($this->getType() == 'service')
        $title = 'Services';
      elseif($this->getType() == 'salon')
        $title = 'Salons';
    }

    if($notfound=='yes' || $title=='Services' ){
      $returnTitle = "<h1 class='poppins-bold' style='text-transform: none;'>" . $title;
    }
    else {
      // $title = "<h1 class='search-title poppins-bold'><span class='pink-title'>" . $title . "</span>";
      // if ($by_location==1)
        $returnTitle = "<h1 class='poppins-bold'><span class='pink-title'>" . $title;
      // else
      //   $returnTitle = "<h1 class='poppins-bold'>" . $title. "";
    }
      
    if($notfound!=='yes') {
      switch(strtoupper($title)) {
        case 'NAIL': $returnTitle .= ' Parlours';
          break;
        case 'HAIR': $returnTitle .= ' Salons';
          break;
        // default: $returnTitle .= $title;
      }
      if($parentTerm) {
        switch(strtoupper($parentTerm->name)) {
          case 'MASSAGE': 
              if(strtoupper($title) == 'FOOT') {
                $returnTitle .= ' reflexology';
              }
              $returnTitle .= ' massage';
            break;
          case 'MAKEUP':
              $returnTitle .= ' makeup';
            break;
          case 'HAIR REMOVAL':
              if(strtoupper($title) == 'BODY') {
                $returnTitle .= ' hair removal services';
              }
            break;
          case 'WEIGHT MANAGEMENT':
              if(strtoupper($title) == 'WHOLE BODY') {
                $returnTitle .= 'Whole body weight loss';
              }
            break;
        }
      }
      $returnTitle .= "</span>";

      if($query->total>0)
      {
        if($notfound!=='yes')
        {
          if ($by_location=1) {
            
            // $locations = array_filter(explode(",",$location));
            // print_r($locations);die();
            if($location) {
              $returnTitle .= ' in the ';
              foreach ($location as $key => $loc) {
                $returnTitle .= $loc;
                if ($key !== array_key_last($location))
                  $returnTitle .= ', ';
              }
              $returnTitle .= ' part of Singapore';
            } else {
              $returnTitle .= ' in Singapore';
            }
          }
        }
      }
      else {
        $returnTitle .= ' in Singapore';
      }
    }

    $returnTitle .= " </h1>";
    return $returnTitle;
  }
}
