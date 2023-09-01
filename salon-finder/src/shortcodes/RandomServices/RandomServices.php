<?php
namespace DV\shortcodes\RandomServices;
use DV\core\RenderShortCode;
use DV\core\models\Merchant;

class RandomServices extends RenderShortCode {
  public static function init( $args ) {
    $model = new RandomServices();
    // print_r($args);die();
    $model->args = shortcode_atts( array(
      'product_id' => '',
    ), $args );

    $model->generate();
  }

  public function generate() {
    $randomID = [];
    $random_products = array();
    $categoryid = array();
    if ( false === ( $products = get_transient( 'random_services' ) ) ) {
      $from = new \DateTimeZone('GMT');
      $to = new \DateTimeZone('Asia/Singapore');        
      $currDate = new \DateTime('now', $from);
      $currDate->setTimezone($to);
      $time = $currDate->format('Y-m-d h:i:sa');  
      $test = $currDate->format('N');
      $time_future =date("Y-m-d h:i:sa",strtotime("next Monday"));  
      $time_future = explode(' ', $time_future)[0].' 3:00:00am';

      $timeFirst  = strtotime($time);
      $timeSecond = strtotime($time_future);        
      $seconds_until_next_day = $timeSecond - $timeFirst;
      $cat_args = array(
          'status'=>'publish',
          'has_password' => false
      );

      $product_categories = ['facial','hair','massage', 'brows-lashes', 'nail', 'hair-removal'];
      foreach ($product_categories as $category) {
        $args = array( 
          'post_type' =>'product',
          'status'=>'publish',
          'has_password' => false,
          'posts_per_page' => 5,
          'product_cat' => $category, 
          'orderby' => 'rand' 
        );
        $row_array[$category] = array();
        $loop = new \WP_Query( $args );
        while ( $loop->have_posts() ) {
          $loop->the_post();  
          $row_array[$category][] = array(
            $loop->post->ID,
          );
        } 
      }

      array_push($random_products,$row_array);
      set_transient('random_services', $random_products, $seconds_until_next_day);
    }
    else
    {
      $cartids = explode(',',$this->args['product_id']);
      $parentIDs = array();
      // print_r($cartids);
      foreach($cartids as $cartid) {
        $variation = wc_get_product($cartid);
        $parent_id = $variation->get_parent_id();
        array_push($parentIDs,$parent_id);
      }
      $random_services = get_transient( 'random_services' );
      $randomIDs = array();
      // print_r($random_services[0]."<br>");die();
      foreach ($random_services[0] as $key => $parent) {
        foreach ($parent as $child) {
          // print_r($key. " -- " . $child[0]."<br>");
          array_push($randomIDs,$child[0]);
        }
      }
      $results=array_diff($randomIDs,$parentIDs);
      $result = implode(',',$results);
      // print_r($result);
      // print_r("<br>");
      $product_categories = ['facial','hair','massage', 'brows-lashes', 'nail', 'hair-removal'];
      $random_product_ids = array();
      foreach ($product_categories as $category) {
        $args = array( 
          'post_type' =>'product',
          'status'=>'publish',
          'has_password' => false,
          'posts_per_page' => 3, 
          'post__in' => explode(',',$result),
          'product_cat' => $category, 
          'orderby' => 'rand' 
        );
        // $row_array[$category] = array();
        $loop = new \WP_Query( $args );
        // print_r($loop);
        while ( $loop->have_posts() ) {
          $loop->the_post();  
          // print_r($category . " - " . $loop->post->ID."<br>");
          // $the_id = $loop->post->post_parent > 0 ? $loop->post->post_parent : $loop->post->ID;
          // $row_array[] = array(
          //   $loop->post->ID,
          // );
          array_push($random_product_ids,$loop->post->ID);
          // array_push($random_products,$row_array);
        } 
      }
      // print_r($row_array);
      
      // array_push($random_product_ids,$row_array);
      // print_r($random_product_ids);
      $products = wc_get_products( array(
        'include' => $random_product_ids,
        'status'=>'publish',
        'has_password' => false,
        'posts_per_page' => 18,
        'orderby'        => 'meta_value_num',
        'meta_key'       => '_price',
        'order'          => 'asc'
      ));
      // print_r($products);
    }
  ?>
  <div class="search-results">
    <div class="row no-gutters">
        <div class="col mt-4 mb-5" id="services-header">
            <span class="consider-services poppins-bold">You may also want to consider these services!</span>
        </div>
    </div>
    <div class="row justify-content-between services-content">
      <?php  
      echo $this->render('RandomServices/display', [ 'products' => $products ] );
      ?>
    </div>
  </div>
  <?php
  }
}