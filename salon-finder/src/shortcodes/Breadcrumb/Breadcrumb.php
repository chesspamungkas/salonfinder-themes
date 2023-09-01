<?php
namespace DV\shortcodes\Breadcrumb;

use DV\core\RenderShortCode;
use DV\core\models\Product;

class Breadcrumb extends RenderShortCode {
  public static function init( $args ) {
    $model = new Breadcrumb();
    $model->args = shortcode_atts( array(
    //   'header' => "Header",
    //   'subheader'=>''
    ), $args );
    $model->generate();
  }

  public function generate() {
    global $post;
    global $product;
    $term = null;

    $product = Product::getFactory( $product );

    if(is_tax(['product_cat', 'merchant'])) {
      $term = get_queried_object();
    }

    echo $this->render( 'Breadcrumb/display', [
        'product'       => $product,
        'term'  => $term
    ] );
  }
}