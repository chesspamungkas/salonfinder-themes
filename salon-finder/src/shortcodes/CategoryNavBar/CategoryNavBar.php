<?php
namespace DV\shortcodes\CategoryNavBar;
use DV\core\RenderShortCode;

class CategoryNavBar extends RenderShortCode {
  public static function init( $args ) {
    $model = new CategoryNavBar();
    $model->args = shortcode_atts( array(
      'multiple_rows' => 'yes',
    ), $args );
    $model->generate();
  }

  public function generate() {
    if ( false === ( $navbar = get_transient( 'category_navbar' ) ) ) {
      $from = new \DateTimeZone('GMT');
      $to = new \DateTimeZone('Asia/Singapore');      
      $currDate = new \DateTime('now', $from);
      $currDate->setTimezone($to);
      $time = $currDate->format('Y-m-d h:i:sa');  
      $time_future = date('Y-m-d h:i:sa', strtotime($time . ' +1 day'));
      $time_future = explode(' ', $time_future)[0].' 4:00:00am';    
      $timeFirst  = strtotime($time);
      $timeSecond = strtotime($time_future);        
      $seconds_until_next_day = $timeSecond - $timeFirst;
      if ( $navbar = wp_get_nav_menu_items( 'category-navbar-menu' ) ) {
        foreach( $navbar as $menu_item ) {
          $menu_item->category_image = get_field( 'category_image', $menu_item->ID );
        }
        set_transient( 'category_navbar', $navbar, $seconds_until_next_day );
      }
    }

    // print_r( $navbar );
    
    if( $this->args['multiple_rows'] == 'yes' ) {
      // four rows and three columns
      echo $this->render( 'CategoryNavBar/_multiple', [ 'navbar' => $navbar, 'menu_count' => count( $navbar ) ] );
    } else {
      // single row with slider
      echo $this->render( 'CategoryNavBar/_single', [ 'navbar' => $navbar, 'menu_count' => count( $navbar ) ] );
    }
    
  }
}