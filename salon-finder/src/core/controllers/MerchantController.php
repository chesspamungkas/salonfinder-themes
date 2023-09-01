<?php 
namespace DV\core\controllers;

use DV\core\models\Merchant;
use DV\core\models\Product;

class MerchantController extends \DV\core\Controller {

  public $queryVars = [
    'location'=>'([^&]+)',
    'advertiser_slug'=>'([^&]+)',
    'preview'=>'([^&]+)',
    'on_sales'=>'([^&]+)',
    'preview_sf_id'=>'([^&]+)',
    'salon_sf_id'=>'([^&]+)',
    'services_sf_id'=>'([^&]+)',

  ];

  public function addRoutes() {
    flush_rewrite_rules();
    // Begin Service
      add_rewrite_rule(
        '^services/?$',
        'index.php?product_cat=&location=',
        'top'
      );
      add_rewrite_rule(
        '^services/([^/]*)/?$',
        'index.php?product_cat=$matches[1]&location=',
        'top'
      );
      add_rewrite_rule(
        '^services/([^/]*)/(all|[^/]*)/?$',
        'index.php?product_cat=$matches[1]&location=$matches[2]',
        'top'
      );
      add_rewrite_rule(
        '^services/([^/]*)/(all|[^/]*)/page/?([0-9]{1,})/?$',
        'index.php?product_cat=$matches[1]&location=$matches[2]&paged=$matches[3]',
        'top'
      );
    // End Service

    // Begin Salon
      add_rewrite_rule(
        '^salons/?$',
        'index.php?taxonomy=merchant',
        'top'
      );
      
      add_rewrite_rule(
        '^salons/([^/]*)/?$',
        'index.php?taxonomy=merchant&term=$matches[1]',
        'top'
      );
      add_rewrite_rule(
        '^salons/([^/]*)/(all|[^/]*)/?$',
        'index.php?taxonomy=merchant&term=$matches[1]&location=$matches[2]',
        'top'
      );
      add_rewrite_rule(
        '^salons/([^/]*)/(all|[^/]*)/page/?([0-9]{1,})/?$',
        'index.php?taxonomy=merchant&term=$matches[1]&location=$matches[2]&paged=$matches[3]',
        'top'
      );
    // End Salon


    // Linked used in API

    add_rewrite_rule(
      '^services-by-id/([^/]+)/?$',
      'index.php?services_sf_id=$matches[1]',
      'top' 
    );
    add_rewrite_rule(
      '^salons-by-id/([^/]+)/?$',
      'index.php?salon_sf_id=$matches[1]',
      'top' 
    );

    add_rewrite_rule(
      '^preview-by-id/([^/]+)/?$',
      'index.php?preview_sf_id=$matches[1]',
      'top' 
    );

    add_rewrite_rule(
      '^preview/([^/]+)/?$',
      'index.php?taxonomy=merchant&term=$matches[1]&paged=1&preview=1',
      'top' 
    );
    add_rewrite_rule(
      '^preview/([^/]+)/page/([0-9]{1,})/?',
      'index.php?taxonomy=merchant&term=$matches[1]&paged=$matches[2]&preview=1',
      'top' 
    );

    // Begin Latest Deals
    add_rewrite_rule(
      '^beauty-deals/?$',
      'index.php?s=&location=&on_sales=1&paged=1',
      'top'
    );
    add_rewrite_rule(
      '^beauty-deals/page/?([0-9]{1,})/?$',
      'index.php?s=&location=&on_sales=1&paged=$matches[1]',
      'top'
    );
    // End Latest Deals

    add_rewrite_rule(
      '^search/?$',
      'index.php',
      'top'
    );
    add_rewrite_rule(
      '^search/([^/]*)/?$',
      'index.php?s=$matches[1]',
      'top'
    );
    add_rewrite_rule(
      '^search/([^/]*)/(all|[^/]*)/?$',
      'index.php?s=$matches[1]&location=$matches[2]',
      'top'
    );
    add_rewrite_rule(
      '^search/([^/]*)/(all|[^/]*)/page/?([0-9]{1,})/?$',
      'index.php?s=$matches[1]&paged=$matches[3]&location=$matches[2]',
      'top'
    );
    add_rewrite_rule(
      '^search/([^/]*)/page/?([0-9]{1,})/?$',
      'index.php?s=$matches[1]&paged=$matches[2]&location=',
      'top'
    );
  }

  public function templateRedirect() {
    if(is_search()) {
      add_filter( 'template_include', function() {
        return get_stylesheet_directory() . '/templates/search-result.php';
      });
    }
    if (get_query_var('advertiser_slug')) {
      add_filter( 'template_include', function() {
          return get_stylesheet_directory() . '/page-search-preview.php';
      });
    }
    if(get_query_var('preview_sf_id')) {
      $term = Merchant::findByForeignKey(get_query_var('preview_sf_id', 0));
      if($term) {
        wp_redirect( site_url( '/preview/'.$term->slug ), 301 );
        die;
      }
      wp_redirect(home_url());
      die;
    }

    if(get_query_var('services_sf_id')) {
      $product = Product::findByForeignKey(get_query_var('services_sf_id', 0));
      if($product) {
        wp_redirect( get_permalink($product->get_id()), 301 );
        die;
      }
      wp_redirect(home_url());
      die;
    }

    if(get_query_var('salon_sf_id')) {
      $term = Merchant::findByForeignKey(get_query_var('salon_sf_id', 0));
      if($term) {
        wp_redirect( Merchant::makeURL($term), 301 );
        die;
      }
      wp_redirect(home_url());
      die;
    }
  }
}