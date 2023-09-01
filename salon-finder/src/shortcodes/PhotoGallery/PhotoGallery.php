<?php
namespace DV\shortcodes\PhotoGallery;

use DV\core\RenderShortCode;
use DV\core\models\Outlet;

class PhotoGallery extends RenderShortCode {

  public static function init( $args ) {
    $model = new PhotoGallery();
    $model->args = shortcode_atts( array(
    'image-size' => "full-size",
    //   'subheader'=>''
    ), $args );
    $model->generate();
  }

  public function getImageSizeFromAttachmentArray($imageArray, $size) {
    if(isset($imageArray['sizes']) && isset($imageArray['sizes'][$size])) {
      return $imageArray['sizes'][$size];
    } else if(isset($imageArray['url'])) {
      return $imageArray['url'];
    }
    return null;
  }

  public function generate() {
    global $product;

    $merchantObj = $product->getMerchant();
    $merchant = new \DV\core\models\Outlet( $merchantObj );

    $images = array();

    if( has_post_thumbnail() ) {
      $images[] = wp_get_attachment_image_url( $product->get_image_id(), $this->args['image-size'] );
    } 

    $outletImage = $merchant->getMeta( 'image' );
    if($this->getImageSizeFromAttachmentArray($outletImage, $this->args['image-size'])) {
      $images[] = $this->getImageSizeFromAttachmentArray($outletImage, $this->args['image-size']);
    }

    $outletObjs = $product->getOutlets();
    if($outletObjs && count($outletObjs)) {
      foreach($outletObjs as $outletObj) {
        $outlet = new \DV\core\models\Outlet( $outletObj );
        $gallaryImages = $outlet->getGallery();
        foreach($gallaryImages as $outletImage) {
          if($this->getImageSizeFromAttachmentArray($outletImage, $this->args['image-size'])) {
            $images[] = $this->getImageSizeFromAttachmentArray($outletImage, $this->args['image-size']);
          }
        }
      }
    }

    echo $this->render( 'PhotoGallery/display', [
        'photos'      => $images
    ] );
  }
}