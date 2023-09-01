<?php
namespace DV\shortcodes\SimilarServices;

use DV\core\RenderShortCode;
use DV\core\ThemeBase;

class SimilarServices extends RenderShortCode {
  public static function init( $args ) {
    $model = new SimilarServices();
    $model->args = shortcode_atts( array(
        'product_id'    => 0,
        'page'          => 1,
        'limit'         => 9,
        'paginate'      => true
    ), $args );
    $model->generate();
  }

  public function generate() {
    if( !$this->args['product_id'] ) {
        global $product;
        $product_id = $product->get_id();
    } else {
        $product_id = $this->args['product_id'];
    }

    $terms = wp_get_post_terms( $product_id, 'product_cat' );

    if($terms instanceof WP_Error) {
        return false;
    }

    // $mainTerm = null;
    // $subTerm = null;
    $catSlugs = array();

    foreach($terms as $term) {
    //     if($_term->parent == 0) {
    //         $mainTerm = $_term;
    //     } else {
    //         $subTerm = $_term;
    //     }
        $catSlugs[] = $term->slug;
    }

    // $mainCatProducts = [];
    // $subCatProducts = [];
    $products = [];

    // if( $mainTerm ) {
    //     $mainCatProducts = wc_get_products( [
    //         'orderby'=>'rand',
    //         'category'=> [ $mainTerm->slug ],
    //         'exclude'=> [ $product_id ]
    //     ] );
    // }

    $excludeID = [ $product_id ];

    // foreach( $mainCatProducts as $prod ) {
    //     $excludeID[] = $prod->get_id();
    // }

    // if( $subTerm ) {
    //     $subCatProducts = wc_get_products( [
    //         'orderby'=>'rand',
    //         'category'=> [$subTerm->slug],
    //         'exclude'=> $excludeID,
    //     ] );
    // }

    // $products = array_merge($mainCatProducts, $subCatProducts);

    $products = wc_get_products( [
        'orderby'       => 'date',
        'status'        => 'publish',
        'limit'         => $this->args['limit'],
        'page'          => $this->args['page'],
        'paginate'      => $this->args['paginate'],
        'category'      => $catSlugs,
        'exclude'       => $excludeID,
        'has_password'  => FALSE
    ] );

    // print_r( $products->products );

    echo $this->render( 'SimilarServices/display', [
        'product_id'    => $product_id,
        'products'      => $products->products,
        'page'          => $this->args['page'],
        'total_pages'   => $products->max_num_pages
    ] );
  }
}