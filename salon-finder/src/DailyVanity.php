<?php
namespace DV;

use DV\actions\EmptySearchResultAction\EmptySearchResultAction;
use DV\core\models\Product;
use DV\shortcodes\CustomerVoucher\CustomerVoucher;
use DV\shortcodes\SearchResults\SearchResults;
use DV\shortcodes\SearchResultsListing\SearchResultsListing;
use DV\shortcodes\WebSearch\WebSearch;

class DailyVanity {

  static function init() {
    $own = new DailyVanity();
    $own->_init();
    //add_action( 'init', [$own, '_init'] );
    define('DV_SHORTCODE_PATH', __DIR__.'/shortcodes/');
  }

  public function _init() {    
    core\ThemeBase::init();
    core\ShortCode::init();
    core\models\Merchant::register();
    Product::register();
    core\controllers\MerchantController::init();
    core\models\Collection::register();
    SearchResults::register();
    SearchResultsListing::register();
    CustomerVoucher::register();
    WebSearch::register();
    core\models\ImageSize::init();
    EmptySearchResultAction::register();
  }
}