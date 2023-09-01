<?php
namespace DV\shortcodes\Collections;
use DV\core\RenderShortCode;
use DV\core\models\Collection;
use DV\core\Views;

class Collections extends RenderShortCode {
  public static function init( $args ) {
    $model = new Collections();
    $model->args = shortcode_atts( array(
      'featured'=>0,
      'header'=>null,
      'limit'=>15,
    ), $args );
    $model->generate();
  }

  public function generate() {
    $to = new \DateTimeZone('Asia/Singapore'); 
    $currDate = new \DateTime('now', $to);
    $metaQuery = array(
      array(
        'key'=>'start_date',
        'type'=>'DATETIME',
        'compare'=>'<=',
        'value'=>$currDate->format('Y-m-d h:i:s'),
      ),
      array(
        'key'=>'end_date',
        'type'=>'DATETIME',
        'compare'=>'>=',
        'value'=>$currDate->format('Y-m-d h:i:s'),
      ),
      array(
        'key'=>'featured',
        'value'=>$this->args['featured'],
        'orderby'=>'start_date',
        'order'=>'DESC'
      )
    );
    $arg = array(
      'taxonomy'=>Collection::$TERM_NAME,
      'meta_query'=> $metaQuery,
      'number'=>$this->args['limit'],
    );

    $terms = get_terms($arg);
    foreach($terms as $term) {        
      $products = wc_get_products( array(
        'status'=>'publish',
        'has_password' => false,
        'posts_per_page'=>15,
        'tax_query' => array(
          array(
            'taxonomy' => 'collection',
            'field' => 'id',
            'terms' => $term->term_id,
          ),
        ),
      ));
      $image = null;
      if($this->args['featured']) {
        $attachmentID = get_term_meta($term->term_id, 'image', true);          
        $image = wp_get_attachment_image_src($attachmentID, 'full');
      }
      echo $this->render( 'Collections/display', [ 'products' => $products, 'backgroundimage'=>$image?$image[0]:'', 'title' => $term->name, 'viewAll' => get_term_link($term, Collection::$TERM_NAME), 'termId' => $term->term_id, 'featured'=>$this->agrs['featured'] ] );
    }
  }
}