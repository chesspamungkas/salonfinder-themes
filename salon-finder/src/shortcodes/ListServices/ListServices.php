<?php 
namespace DV\shortcodes\ListServices;
use DV\core\RenderShortCode;
use DV\core\models\Product;

class ListServices {
  private $_passwordProtect = false;
  private $_searchTerm = null;

  static function register() {
    $class = new ListServices();
    add_shortcode( 'list-services', [$class, 'listServices'] );
  }

  function dump_request( $input ) {

    var_dump($input);

    return $input;
  }

  function listServices($args) {
    extract( shortcode_atts(array(
      'products' => []
      ),$args ));
      echo $this->render('shortcodes/ListServices/listServices', ['products'=> $products]);
  }
}