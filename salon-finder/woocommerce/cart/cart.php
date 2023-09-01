<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.8.0
 */

defined( 'ABSPATH' ) || exit;

$useragent = $_SERVER['HTTP_USER_AGENT'];
$isMobile = false;
$isIOS = false;

if( preg_match('/(Mobile|Android|Tablet|GoBrowser|[0-9]x[0-9]*|uZardWeb\/|Mini|Doris\/|Skyfire\/|iPhone|Fennec\/|Maemo|Iris\/|CLDC\-|Mobi\/)/uis',$useragent) ) {
  $isMobile = true;
  $device = 'mobile';
  $isIOS = false;

  if( stripos( $useragent, 'iphone' ) !== false || stripos( $useragent, 'ipad' ) !== false ) {
    // $isIOS = true;
    $isIOS = true;
  }
}

do_action( 'woocommerce_before_cart' ); ?>
<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
	<?php do_action( 'woocommerce_before_cart_table' ); ?>
	<h1 class="cart-title poppins-bold mb-3">Your Shopping Cart</h1>
		<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0" width="100%">
			<!-- <thead>
				<tr>
					<th class="product-remove">&nbsp;</th>
					<th class="product-thumbnail">&nbsp;</th>
					<th class="product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
					<th class="product-price"><?php esc_html_e( 'Price', 'woocommerce' ); ?></th>
					<th class="product-quantity"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></th>
					<th class="product-subtotal"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
				</tr>
			</thead> -->
			<tbody>
				<?php do_action( 'woocommerce_before_cart_contents' ); ?>

				<?php
				$postIDs=array();
				foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
					if( !$isMobile && !$isIOS) {
						$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
						$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
						$merchant_name = [];
						if($_product->has_child()) {
							$merchant_name = get_the_terms($_product->get_id(), 'merchant');
						} else {
							$merchant_name = get_the_terms($_product->get_parent_id(), 'merchant');
						}

						array_push($postIDs,$cart_item['product_id']);
						if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
							$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
							?>
							<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>" style="border-bottom: 1px solid #e8d9d9 !important;">
								<td class="product-thumbnail" style="vertical-align:top;">
									<?php
									$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image('woocommerce_cart_item_thumbnail'), $cart_item, $cart_item_key );

									if ( ! $product_permalink ) {
										echo $thumbnail; // PHPCS: XSS ok.
									} else {
										printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
									}
									?>
								</td>

								<td class="product-name" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>" width="auto" style="vertical-align:top;">
									<?php
									if(!empty($merchant_name)) {
										foreach( $merchant_name as $term ) {
											if( $term->parent == 0 ) {
												$merchant_name_by_var = $term->name;
												$merchant_id = $term->term_id;
											}
										}
									}
									else {
										$merchant_name_by_var = '';
										$merchant_id = '';
									}

									if ( ! $product_permalink ) {
										$product_name = change_promotion_title( $_product->get_name(), $_product->get_id());
										echo $product_name . '&nbsp;';
									} else {
										$product_name = get_the_title($product_id);
										echo "<table width='100%'><tr style='height:auto;'><td style='border:none;padding:0 10px;'>";
										echo "<a href=" . site_url('search-result') . "?search=&location=&searchType=salon&salonID=" . $merchant_id . " class='merchant-link poppins-medium'>";
										echo $merchant_name_by_var;
										echo "</a>";
										echo "</td><//tr>";
										echo "<tr style='height:auto;'><td style='border:none;padding-top:0;max-width:200px;'>";
										echo "<h5 class='card-title product-name mt-2'>";
										echo sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $product_name, $merchant_name_by_var);
										echo "</h5>";
										echo "</td><//tr></table>";
									}

									// Meta data.
									// echo wc_get_formatted_cart_item_data($cart_item);

									// Backorder notification.
									if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) {
										echo '<p class="backorder_notification">' . esc_html__('Available on backorder', 'woocommerce') . '</p>';
									}
									?>
								</td>

								<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>" width="200px" style="vertical-align:top;padding-top:55px;">
									<?php
									if ( $_product->is_sold_individually() ) {
										$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
									} else {
										$product_quantity = woocommerce_quantity_input(
											array(
												'input_name'   => "cart[{$cart_item_key}][qty]",
												'input_value'  => $cart_item['quantity'],
												'max_value'    => $_product->get_max_purchase_quantity(),
												'min_value'    => '0',
												'product_name' => $_product->get_name(),
											),
											$_product,
											false
										);
									}
									
									echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // PHPCS: XSS ok.
									?>
								</td>

								<td class="product-subtotal" data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>" style="vertical-align:top;padding-top:63px;">
									<?php
										echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
									?>
								</td>
							</tr>
							<?php
						}
					}
					else
					{
						$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
						$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
						$merchant_name = [];
						if($_product->has_child()) {
							$merchant_name = get_the_terms($_product->get_id(), 'merchant');
						} else {
							$merchant_name = get_the_terms($_product->get_parent_id(), 'merchant');
						}
						array_push($postIDs,$cart_item['product_id']);
						if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
							$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
							?>
							<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
								<td class="product-thumbnail">
									<?php
									$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image('woocommerce_cart_item_thumbnail'), $cart_item, $cart_item_key );

									if ( ! $product_permalink ) {
										echo $thumbnail; // PHPCS: XSS ok.
									} else {
										printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
									}
									?>
								</td>

								<td class="product-name" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>" width="65%" style="vertical-align:top;padding:10px;max-width:200px;">
									<?php
									if(!empty($merchant_name)) {
										foreach( $merchant_name as $term ) {
											if( $term->parent == 0 ) {
												$merchant_name_by_var = $term->name;
												$merchant_id = $term->term_id;
											}
										}
									}
									else {
										$merchant_name_by_var = '';
										$merchant_id = '';
									}

									if ( ! $product_permalink ) {
										$product_name = change_promotion_title( $_product->get_name(), $_product->get_id());
										echo $product_name . '&nbsp;';
									} else {
										$product_name = get_the_title($product_id);
										echo "<table width='100%'><tr style='height:auto;'><td style='border:none;padding:0;'>";
										echo "<a href=" . site_url('search-result') . "?search=&location=&searchType=salon&salonID=" . $merchant_id . " class='merchant-link poppins-medium'>";
										echo $merchant_name_by_var;
										echo "</a>";
										echo "</td></tr>";
										echo "<tr style='height:auto;'><td style='border:none;padding-top:5px;'>";
										echo "<h5 class='card-title product-name'>";
										echo sprintf('<a href="%s" class="product-link">%s</a>', esc_url($product_permalink), $product_name, $merchant_name_by_var);
										echo "</h5>";
										echo "</td></tr>";
										echo "<tr style='height:auto;'>";
										echo "<td class='product-subtotal' style='border:none;'>";
										echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
										echo "</td></tr>";
										echo "</table>";
									}

									// Meta data.
									// echo wc_get_formatted_cart_item_data($cart_item);

									// Backorder notification.
									if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) {
										echo '<p class="backorder_notification">' . esc_html__('Available on backorder', 'woocommerce') . '</p>';
									}
									?>
								</td>

								<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>" width="10%" style="text-align:center !important;">
									<?php
									if ( $_product->is_sold_individually() ) {
										$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
									} else {
										$product_quantity = woocommerce_quantity_input(
											array(
												'input_name'   => "cart[{$cart_item_key}][qty]",
												'input_value'  => $cart_item['quantity'],
												'max_value'    => $_product->get_max_purchase_quantity(),
												'min_value'    => '0',
												'product_name' => $_product->get_name(),
											),
											$_product,
											false
										);
									}
									
									echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // PHPCS: XSS ok.
									?>
								</td>

								<!-- <td class="product-subtotal" data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>">
									<?php
										echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
									?>
								</td> -->
							</tr>
							<?php
						}
					}
				}
				?>

				<?php do_action( 'woocommerce_cart_contents' ); ?>

				<tr style="height:0;">
					<td colspan="6" class="actions">
						<button type="submit" class="button woocommerce-Button" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>"><?php esc_html_e( 'Update cart', 'woocommerce' ); ?></button>

						<?php do_action( 'woocommerce_cart_actions' ); ?>

						<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
					</td>
				</tr>

				<?php do_action( 'woocommerce_after_cart_contents' ); ?>
			</tbody>
		</table>
	<?php do_action( 'woocommerce_after_cart_table' ); ?>
</form>

<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>

<div class="cart-collaterals">
	<?php
		/**
		 * Cart collaterals hook.
		 *
		 * @hooked woocommerce_cross_sell_display
		 * @hooked woocommerce_cart_totals - 10
		 */
		do_action( 'woocommerce_cart_collaterals' );
	?>
</div>

<?php 
// print_r ($postIDs."asd");
$product_cart_id = WC()->cart->generate_cart_id( 28176 );
$in_cart = WC()->cart->find_product_in_cart( $product_cart_id );
if ( $in_cart ) {
	$notice = 'Product ID ' . $product_id . ' is in the Cart!';
	wc_print_notice( $notice, 'notice' );
}
else
{
	// print_r("zxc");
}
// print_r($postIDs);


$list = implode(', ', $postIDs);
// print_r($List);die();

// echo '<pre>'; var_dump($postIDs); echo'</pre>';
// print_r(json_encode($postIDs));
// $x = 1;
// $length = count($postIDs);
// foreach($postIDs as $value){
// 	print_r($value."<BR>");
// 	$values = $value . ",";
// 	$x++;
// }
// print_r($values);
echo do_shortcode('[random-services product_id="' . $list . '"]');
// do_action( 'woocommerce_after_cart' );
?>
