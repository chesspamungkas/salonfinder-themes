<?php
namespace DV\core\models;

class ImageSize {

  private static $_registeredImageSize = [
    'full-size'=>[900, 467, true],
    'gallery-image'=>[ 760, 394, true],
    'gallery-image-mobile'=>[ 450, 234, true],
    'service-thumbnail'=>[ 284, 147, true],
    'service-thumbnail-mobile'=>[ 150, 78, true],
    'sml_size'=>[300, 0, false],
    'mid_size'=>[600, 0, false],
    'lrg_size'=>[1200, 0, false],
    'sup_size'=>[2400, 0, false]
  ];
  
  public static function Add($key, $width=0, $height=0, $crop=false) {
    self::$_registeredImageSize = array_merge(self::$_registerCode, [$key=>[$width, $height, $crop]]);
  }

  public static function Del($key) {
    if(isset(self::$_registeredImageSize[$key])) {
      unset(self::$_registeredImageSize[$key]);
    }
  }

  public static function init() {
    foreach(self::$_registeredImageSize as $key=>$imageSize) {
      add_image_size( $key, $imageSize[0], $imageSize[1], $imageSize[2] );
    }
  }
}