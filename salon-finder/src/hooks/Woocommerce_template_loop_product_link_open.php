<?php

namespace DV\hooks;

class Woocommerce_template_loop_product_link_open {
  public static function init() {
    $model = new Woocommerce_template_loop_product_link_open();
    $model->args = shortcode_atts( array(
    //   'header' => "Header",
    //   'subheader'=>''
    ), $args );
    $model->generate();
  }
}