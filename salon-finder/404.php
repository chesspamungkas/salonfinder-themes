<?php 
/**
* Template Name: 404 page
*/
global $wp_query;
$url=site_url();
wp_redirect($url);
exit;

?>