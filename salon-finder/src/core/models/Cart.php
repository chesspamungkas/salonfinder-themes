<?php 
namespace DV\core\models;

class Cart {
  public $parentProduct = null;

  private $_content = null;

  public static $CLASS=null;

  public static function getFactory() {
    if(self::$CLASS===null) {
      self::$CLASS = new Cart;
    }
    return self::$CLASS;
  }

  public static function register() {
    add_action( 'wp_ajax_nopriv_sf_get_cart', [self::getFactory(), 'getCart'] );
    add_action( 'wp_ajax_sf_get_cart', [self::getFactory(), 'getCart'] );
  }

  public function getCart() {
    $cart = WC()->cart;
    $items = [];
    foreach($cart->get_cart_contents() as $hash=>$item) {
      $item['line_totalHTML'] = wc_price($item['line_total']);
      $item['line_subtotalHTML'] = wc_price($item['line_subtotal']);
      $items[$hash] = $item;
    }
    $cartTotal = $cart->get_totals();
    wp_send_json([
      'items'=>$items,
      'totalHTML'=>wc_price($cartTotal['total']),
      'subtotalHTML'=> wc_price($cart->get_subtotal()),
      'total'=>$cart->get_totals(),
      'subtotal'=>$cart->get_subtotal()
    ]);    
    die();
  }
}