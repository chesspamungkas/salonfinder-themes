<?php 


namespace DV\actions\EmptySearchResultAction;

use DV\core\RenderShortCode;
use DV\shortcodes\SearchResultsListing\SearchResultsListing;

class EmptySearchResultAction extends RenderShortCode{
  public static $TERM_NAME = 'merchant';
  static function register() {
    $class = new EmptySearchResultAction();
    //add_filter( 'init', [$class, 'addTaxToProduct']);
    add_action( SearchResultsListing::$_ACTIONS_EMPTY_SEARCH_RESULT, [$class, 'emptySearchEmail'], 10);
  }


  function emptySearchEmail($searchText) {
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
      'search_type'=> get_query_var('s', ''),
      'keyword'=>$searchText,
      'timestamp'=>current_time('timestamp'),
      'datetime'=>date_format($datetime, 'd M Y h:i:s A \G\M\T\+\8'),
      'postal_location'=>get_query_var('location', 'All location'),
      'ip_address'=>$this->get_the_user_ip(),
      'page_url'=>$_SERVER['HTTP_REFERER']?$_SERVER['HTTP_REFERER']:"Direct Link",
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

    $subject = "Empty SF Search: ". $notFound['keyword'] . ' - ' . $notFound['postal_location'] . ' - ' . $notFound['wp_user_email'] . " (".$notFound['cloudflare_country'].")";

    add_filter( 'wp_mail_content_type', [$this, 'changeEmailContentType']);
    $emailContent = $this->render('../actions/EmptySearchResultAction/_emailContent', $notFound);
    wp_mail(SEARCH_EMPTY_TO_EMAIL, $subject, $emailContent);
    remove_filter( 'wp_mail_content_type', [$this, 'changeEmailContentType']);

    return $notFound;
  }
  function changeEmailContentType() {
    return 'text/html';
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

}
