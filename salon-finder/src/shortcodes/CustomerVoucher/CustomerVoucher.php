<?php 
namespace DV\shortcodes\CustomerVoucher;
use DV\core\RenderShortCode;
use DV\core\models\OrderItemProduct;

class CustomerVoucher extends RenderShortCode {
  private $_passwordProtect = false;
  private $_searchTerm = null;
  const STATUS_REDEEMED = 2;
  const STATUS_INACTIVE = 0;
  const STATUS_ACTIVE = 1;

  static function register() {
    $class = new CustomerVoucher();
    add_shortcode( 'valid-voucher', [$class, 'validVoucher'] );
    add_shortcode( 'redeemed-voucher', [$class, 'redeemedVoucher'] );
    add_shortcode( 'inactive-voucher', [$class, 'inactiveVoucher'] );
    add_shortcode( 'expired-voucher', [$class, 'expiredVoucher'] );
  }

  public function getProductItem( $item ) {
    if( $item->has_child() ) {
      $product = new \DV\core\models\Product( $item->get_id() );
    } else {
      $product = new \DV\core\models\Product( $item->get_parent_id() );
    }

    return $product;
  }

  function redeemedVoucher($args=[]) {
    $paged = 1;
    $limit = 30;
    $queryArgs = [
      'status'=>['pending','processing','completed'],
      'customer_id'=>get_current_user_id(),
      'paginate'=>true,
      // 'paged'=>$paged,
      // 'limit'=>$limit
    ];

    $count = 0;

    // add_filter('posts_join', [$this, 'joinOrderItem'] );
    add_filter('posts_join', [$this, 'joinOrderItemPartialRedeemed'] );
    add_filter('posts_fields', [$this, 'selectOrderItemID'] );
    add_filter('posts_groupby', [$this, 'removeGrouping'] );
    // add_filter( 'posts_request', [$this,'dump_request'] );
    $query = wc_get_orders($queryArgs);
    
    // remove_filter('posts_join', [$this, 'joinOrderItem'] );
    remove_filter('posts_join', [$this, 'joinOrderItemPartialRedeemed'] );
    remove_filter('posts_fields', [$this, 'selectOrderItemID'] );
    remove_filter('posts_groupby', [$this, 'removeGrouping'] );
    // echo '<pre>'; print_r($query); echo '</pre>';
    if($query->total) {
      $orderItems = [];
      foreach($query->orders as $order) {
        // echo '<pre>'; print_r($order->get_items('line_item')); echo '</pre>';
        foreach($order->get_items('line_item') as $orderItem) {
          // print_R($orderItem->get_id()."<br>");
          if(!isset($orderItems[$orderItem->get_id()])) {
            if($orderItem->meta_exists(\DV\helpers\Constants::$VOUCHER_CODE)) {
              $metaArray = $orderItem->get_meta(\DV\helpers\Constants::$VOUCHER_CODE, true);
              foreach($metaArray as $voucherCode=>$meta) {
                // if( $meta['redeemed'] == 1 ) {
                if($meta['status'] == self::STATUS_REDEEMED || $meta['redeemed'] == 1) {
                  $order_Item = \DV\core\models\OrderItemProduct::init($orderItem);
                  $orderItems[$orderItem->get_id()] = array(
                    'order'=>$order,
                    'item'=>$order_Item,
                    'voucherMeta'=> $meta,
                    'voucherCode'=> $voucherCode
                  );

                  $count++;
                }
              }
            }
          }
        }
      }

      // print_r($orderItems);
      echo $this->render('CustomerVoucher/redeemedVoucher', [
        'orderItems'=>$orderItems, 
        'total'=>$count
      ] );
      // return "";
    }
  }
  
  function inactiveVoucher($args=[]) {
    $paged = 1;
    $limit = 30;
    $queryArgs = [
      'status'=>['pending','processing','completed'],
      'customer_id'=>get_current_user_id(),
      'paginate'=>true,
      // 'paged'=>$paged,
      // 'limit'=>$limit
    ];

    $count = 0;

    // print_r($queryArgs);
    // add_filter('posts_join', [$this, 'joinOrderItem'] );
    // add_filter( 'posts_request', [$this,'dump_request'] );
    $query = wc_get_orders($queryArgs);
    // echo '<pre>'; print_r($query); echo '</pre>';
    // remove_filter('posts_join', [$this, 'joinOrderItem'] );
    // print_r($query->orders);
    // echo '<pre>'; print_r($query->orders); echo '</pre>';
    // print_r( $query->total );
    if($query->total) {
      $orderItems = [];
      foreach($query->orders as $order) {
        // print_r($order->get_items('line_item'));
        // echo '<pre>'; print_r($order->get_items('line_item')); echo '</pre>';
        foreach($order->get_items('line_item') as $orderItem) {
          // print_R($orderItem->get_id()."<br>");
          if(!isset($orderItems[$orderItem->get_id()])) {
            // print_r(\DV\helpers\Constants::$VOUCHER_CODE);
            // print_r($orderItem->meta_exists(\DV\helpers\Constants::$VOUCHER_CODE)."asasdasdas");
            if($orderItem->meta_exists(\DV\helpers\Constants::$VOUCHER_CODE)) {
              $metaArray = $orderItem->get_meta(\DV\helpers\Constants::$VOUCHER_CODE, true);
              // print_r($metaArray);
              foreach($metaArray as $voucherCode=>$meta) {
                // print_r($meta['status']);
                if( $meta['status'] == 0 && $meta['redeemed'] != 1) {
                  // print_r($orderItem);
                  $order_Item = \DV\core\models\OrderItemProduct::init($orderItem);
                  // print_r($orderItem);
                  $orderItems[$orderItem->get_id()] = array(
                    'order'=>$order,
                    'item'=>$order_Item,
                    'voucherMeta'=> $meta,
                    'voucherCode'=> $voucherCode
                  );

                  $count++;
                }
              }
            }
          }
        }
      }

      // print_r($orderItems);
      echo $this->render('CustomerVoucher/inactiveVoucher', [
        'orderItems'=>$orderItems, 
        'total'=>$count
      ] );
      // return "";
    }
  }

  function dump_request( $input ) {

    var_dump($input);

    return $input;
  }

  function validVoucher($args=[]) {
    $paged = 1;
    $limit = 30;
    extract(shortcode_atts(array(
      'paged' => 1,
      'limit' => 30,
    ), $args));
    $queryArgs = [
      'status'=>['pending','processing','completed'],
      'customer_id'=>get_current_user_id(),
      'paginate'=>true,
      // 'paged'=>$paged,
      // 'limit'=>$limit
    ];

    $count = 0;
    
    add_filter('posts_join', [$this, 'joinOrderItem'] );
    // add_filter( 'posts_request', [$this,'dump_request'] );
    $query = wc_get_orders($queryArgs);
    // echo '<pre>'; print_r($query); echo '</pre>';
    remove_filter('posts_join', [$this, 'joinOrderItem'] );
    if($query->total) {
      $orderItems = [];
      foreach($query->orders as $order) {
        // echo '<pre>'; print_r($order->get_id()); echo '</pre>';
        // echo '<pre>'; print_r($order->get_items('line_item' )); echo '</pre>';
        foreach($order->get_items('line_item' ) as $orderItem) {
          // echo '<pre>'; print_r($orderItems[$orderItem->get_id()); echo '</pre>';
          if(!isset($orderItems[$orderItem->get_id()])) {
            // echo '<pre>'; print_r($orderItem->meta_exists(\DV\helpers\Constants::$VOUCHER_CODE)); echo '</pre>';
            if($orderItem->meta_exists(\DV\helpers\Constants::$VOUCHER_CODE)) {
              $metaArray = $orderItem->get_meta(\DV\helpers\Constants::$VOUCHER_CODE, true);
              // echo '<pre>'; print_r($metaArray); echo '</pre>';
              foreach($metaArray as $voucherCode=>$meta) {
                if( strtotime( $meta['expiryDate'] ) >= time() && $meta['status'] == 1 && $meta['redeemed'] == 0 ) {
                  $orderItems[] = [
                    'order'=>$order,
                    'item'=>\DV\core\models\OrderItemProduct::init($orderItem),
                    'voucherMeta'=> $meta,
                    'voucherCode'=> $voucherCode
                  ];
                  $count++;
                }
              }
            }
          }
        }
      }
      echo $this->render('CustomerVoucher/validVoucher', [
        'orderItems'=>$orderItems, 
        'total'=>$count
        // 'total'=>$query->total, 
        // 'maxPage'=>$query->max_num_pages, 
        // 'paged'=>$paged
      ] );
    }
  }
  
  function expiredVoucher($args=[]) {
    $paged = 1;
    $limit = 30;
    extract(shortcode_atts(array(
      'paged' => 1,
      'limit' => 30,
    ), $args));
    $queryArgs = [
      'status'=>['pending','processing','completed'],
      'customer_id'=>get_current_user_id(),
      'paginate'=>true,
      // 'paged'=>$paged,
      // 'limit'=>$limit
    ];

    $count = 0;
    
    add_filter('posts_join', [$this, 'joinOrderItem'] );
    // add_filter( 'posts_request', [$this,'dump_request'] );
    $query = wc_get_orders($queryArgs);
    // echo '<pre>'; print_r($query); echo '</pre>';
    remove_filter('posts_join', [$this, 'joinOrderItem'] );
    if($query->total) {
      $orderItems = [];
      foreach($query->orders as $order) {
        // echo '<pre>'; print_r($order->get_id()); echo '</pre>';
        // echo '<pre>'; print_r($order->get_items('line_item' )); echo '</pre>';
        foreach($order->get_items('line_item' ) as $orderItem) {
          if(!isset($orderItems[$orderItem->get_id()])) {
            if($orderItem->meta_exists(\DV\helpers\Constants::$VOUCHER_CODE)) {
              $metaArray = $orderItem->get_meta(\DV\helpers\Constants::$VOUCHER_CODE, true);
              foreach($metaArray as $voucherCode=>$meta) {
                if( strtotime( $meta['expiryDate'] ) < time() && $meta['status'] == 1 && $meta['redeemed'] == 0 ) {
                  $orderItems[] = [
                    'order'=>$order,
                    'item'=>\DV\core\models\OrderItemProduct::init($orderItem),
                    'voucherMeta'=> $meta,
                    'voucherCode'=> $voucherCode
                  ];
                  $count++;
                }
              }
            }
          }
        }
      }
      echo $this->render('CustomerVoucher/expiredVoucher', [
        'orderItems'=>$orderItems, 
        'total'=>$count
        // 'total'=>$query->total, 
        // 'maxPage'=>$query->max_num_pages, 
        // 'paged'=>$paged
      ] );
    }
  }

  function joinOrderItem($join) {
    global $wpdb;
    $join .= " 
      left join {$wpdb->prefix}woocommerce_order_items orderItem on orderItem.order_id = {$wpdb->posts}.ID
      inner join {$wpdb->prefix}woocommerce_order_itemmeta woim on woim.order_item_id = orderItem.order_item_id and woim.meta_key ='".OrderItemProduct::VOUCHER_REDEEMED."' and woim.meta_value = 'NO'
    ";
    return $join;
  }
  function joinOrderItemPartialRedeemed($join) {
    global $wpdb;
    $join .= " 
      left join {$wpdb->prefix}woocommerce_order_items orderItem on orderItem.order_id = {$wpdb->posts}.ID
      inner join {$wpdb->prefix}woocommerce_order_itemmeta woim on woim.order_item_id = orderItem.order_item_id and woim.meta_key ='".OrderItemProduct::VOUCHER_HAS_REDEEMED."' and woim.meta_value = 'YES'
    ";
    return $join;
  }
  function selectOrderItemID($select) {
    global $wpdb;
    $select = "{$wpdb->posts}.*, woim.*";
    return $select;
  }

  function removeGrouping($group) {
    global $wpdb;
    return "";
  }
}