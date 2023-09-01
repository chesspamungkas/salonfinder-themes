<?php 
namespace DV\core\models;

class User {
  public static $CLASS=null;

  public static function getFactory() {
    if(self::$CLASS===null) {
      self::$CLASS = new Cart;
    }
    return self::$CLASS;
  }

  public static function register() {
    add_action( 'wp_ajax_nopriv_sf_get_cart', [self::getFactory(), 'getCart'] );
    add_action( 'wp_ajax_sf_get_cart', [self::getFactory(), 'getCart'] );
    add_filter( 'wp_password_change_notification_email', [self::getFactory(), 'changePasswordChangeEmailContent'], 10, 3 );
  }
  
  function changePasswordChangeEmailContent( $wp_password_change_notification_email, $user, $blogname ){
    $wp_password_change_notification_email['message'] = 'Hi '. $user->user_firstname .', <br> <br>
  
    Congratulations! Your password has been reset successfully! <br> <br>
  
  
    Login Now: <a href="'. home_url('/profile/login/') .'">'. home_url('/profile/login/') .'</a> <br> <br>
  
  
    Stay beautiful! <br> <br>
    
  
    This auto-generated email was sent by Daily Vanity Pte Ltd. <br> <br>
    
    
    201 Henderson Road #06-13/14 Singapore 159545';
    
    $wp_password_change_notification_email['to'] = $user->user_email;
    
    return $wp_password_change_notification_email;
  }
}