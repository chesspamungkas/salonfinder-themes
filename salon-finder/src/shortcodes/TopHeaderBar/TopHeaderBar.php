<?php
namespace DV\shortcodes\TopHeaderBar;
use DV\core\RenderShortCode;

class TopHeaderBar extends RenderShortCode {
  public static function init( $args ) {
    $model = new TopHeaderBar();
    $model->args = shortcode_atts( array(
    //   'header' => "Header",
    //   'subheader'=>''
    ), $args );
    $model->generate();
  }

  public function generate() {
    echo $this->render('TopHeaderBar/display', $this->args);
  }
}