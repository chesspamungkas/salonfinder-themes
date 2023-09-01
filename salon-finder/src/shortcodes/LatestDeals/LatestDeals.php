<?php
namespace DV\shortcodes\LatestDeals;
use DV\core\RenderShortCode;
use DV\core\models\Merchant;
use DV\core\Views;

class LatestDeals extends RenderShortCode {
  public static function init( $args ) {
    $model = new LatestDeals();
    $model->args = shortcode_atts( array(
    //   'header' => "Header",
    //   'subheader'=>''
    ), $args );
    $model->generate();
  }

  public function addJoinMerchant($join) {
    global $wpdb;
    $merchangSlug = Merchant::$TERM_NAME;
    $join .= " 
      inner join {$wpdb->term_relationships} sfrelationship on sfrelationship.object_id = {$wpdb->posts}.ID
      inner join {$wpdb->term_taxonomy} sftaxonomy on sfrelationship.term_taxonomy_id = sftaxonomy.term_taxonomy_id and sftaxonomy.taxonomy='{$merchangSlug}'
    ";
    return $join;
  }

  public function addGroupBy($group) {
    global $wpdb;
    $group = "
      sftaxonomy.term_id
    ";
    return $group;
  }

  public function addSearchMerchantOnly($where) {
    global $wpdb;
    $merchants = get_terms([
      'parent'=>0,
      'taxonomy'=>Merchant::$TERM_NAME,
      'fields'=>'ids'
    ]);
    $where .= ' and sftaxonomy.term_id in ('.implode(',', $merchants).')';
    return $where;
  }

  public function generate() {
    
    $products = [];
    if ( false === ( $products = get_transient( 'random_sales_product' ) ) ) {
      // It wasn't there, so regenerate the data and save the transient
      $randomProductIDs = wc_get_product_ids_on_sale();
      shuffle($randomProductIDs);
      $from = new \DateTimeZone('GMT');
      $to = new \DateTimeZone('Asia/Singapore');        
      $currDate = new \DateTime('now', $from);
      $currDate->setTimezone($to);
      $time = $currDate->format('Y-m-d h:i:sa');  
      $time_future = date('Y-m-d h:i:sa', strtotime($time . ' +1 day'));
      $time_future = explode(' ', $time_future)[0].' 6:00:00am';    
      $timeFirst  = strtotime($time);
      $timeSecond = strtotime($time_future);        
      $seconds_until_next_day = $timeSecond - $timeFirst;
      add_filter('posts_join', [$this, 'addJoinMerchant'] );
      add_filter('posts_groupby', [$this, 'addGroupBy'] );
      // add_filter('posts_fields', [$this, 'changeSelect'] );
      add_filter('posts_where', [$this, 'addSearchMerchantOnly'] );
      //add_filter( 'posts_request', [$this,'dump_request'] );

      $products = wc_get_products( array(
        'include' => $randomProductIDs,
        'status'=>'publish',
        'has_password' => false,
      ));
      remove_filter('posts_join', [$this, 'addJoinMerchant'] );
      remove_filter('posts_groupby', [$this, 'addGroupBy'] );
      // remove_filter('posts_fields', [$this, 'changeSelect'] );
      remove_filter('posts_where', [$this, 'addSearchMerchantOnly'] );

      set_transient('random_sales_product', $products, $seconds_until_next_day);
    }

    return Views::render( 'ListServices/listServicesWithoutMobile', [ 'products' => $products, 'title' => 'Latest Deals', 'viewAll' => BASE_PATH . '/beauty-deals/' ] );
    // echo $this->render('LatestDeals/display', [ 'products' => $products ]);
  }
}