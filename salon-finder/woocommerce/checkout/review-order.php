<?php
/**
 * Review order table
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/review-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
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
?>
<style>
	#order_summary, #sub_total {
		background-color: #ffffff;
		/* margin-top: 20px; */
		/* padding-bottom: 20px; */
		padding: 50px;
	}
</style>

<table class="shop_table woocommerce-checkout-review-order-table">
	<tbody>
		<tr>
			<td class="product-name poppins-medium" colspan="3" style="padding-bottom:10px;">Order Summary</td>
			<td class="product-total poppins-medium"><a class="edit-cart" href="<?php echo home_url("/cart"); ?>"><i class="fas fa-pen"></i> EDIT CART</a></td>
		</tr>
		<?php
		do_action( 'woocommerce_review_order_before_cart_contents' );

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
			// print_r($_product->get_id());die();
			$merchant_name = [];
			if($_product->has_child()) {
				$merchant_name = get_the_terms($_product->get_ID(), 'merchant');
			} else {
				$merchant_name = get_the_terms($_product->get_parent_id(), 'merchant');
			}
			if( !$isMobile && !$isIOS) {
				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
					?>
					<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?> cart-item-top">
						<td class="product-thumbnail" style="vertical-align:top; width: 140px;">
							<?php
							$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image('woocommerce_cart_item_thumbnail'), $cart_item, $cart_item_key );

							if ( ! $product_permalink ) {
								echo $thumbnail; // PHPCS: XSS ok.
							} else {
								printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
							}
							?>
						</td>
						<td class="product-name" style="vertical-align:top;padding-top:28px">
							<?php
							// print_r($merchant_name."asdasdas");die();
							foreach( $merchant_name as $term ) {
								if( $term->parent == 0 ) {
									$merchant_name_by_var = $term->name;
									$merchant_id = $term->term_id;
								}
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
							?>
						</td>
						<td class="product-quantity poppins-semibold" data-title="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>" width="200px" style="vertical-align:top;padding-top:62px;font-size:12px;">
							<?php echo apply_filters( 'woocommerce_checkout_cart_item_quantity', ' <!--strong class="product-quantity"-->QTY: ' . sprintf( '&nbsp;%s', $cart_item['quantity'] ) . '<!--/strong-->', $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</td>
						<td class="product-subtotal poppins-semibold" data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>" style="vertical-align:top;padding-top:62px;font-size:12px;">
						<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</td>
					</tr>
					<?php
				}
			}
			else
			{
				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
					?>
					<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
						<td class="product-thumbnail" style="vertical-align:top;border:none;width: 30%;">
							<?php
							$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image('woocommerce_cart_item_thumbnail'), $cart_item, $cart_item_key );

							if ( ! $product_permalink ) {
								echo $thumbnail; // PHPCS: XSS ok.
							} else {
								printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
							}
							?>
						</td>
						<td class="product-name" style="vertical-align:top;padding:10px 0 10px 10px;max-width:200px;border:none;">
							<?php
							foreach( $merchant_name as $term ) {
								if( $term->parent == 0 ) {
									$merchant_name_by_var = $term->name;
									$merchant_id = $term->term_id;
								}
							}

							if ( ! $product_permalink ) {
								$product_name = change_promotion_title( $_product->get_name(), $_product->get_id());
								echo $product_name . '&nbsp;';
							} else {
								$product_name = get_the_title($product_id);
								echo "<table width='100%'><tr style='height:auto;'><td colspan='2' style='border:none;padding:0;'>";
								echo "<a href=" . site_url('search-result') . "?search=&location=&searchType=salon&salonID=" . $merchant_id . " class='merchant-link poppins-medium'>";
								echo $merchant_name_by_var;
								echo "</a>";
								echo "</td><//tr>";
								echo "<tr style='height:auto;'><td colspan='2' style='border:none;padding-top:0;max-width:200px;'>";
								echo "<h5 class='card-title product-name mt-2'>";
								echo sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $product_name, $merchant_name_by_var);
								echo "</h5>";
								echo "</td><//tr>";
								echo "<tr style='height:auto;'>";
								echo "<td class='product-subtotal poppins-semibold' style='border:none;font-size:12px;letter-spacing: 1.44px;text-align:left;'>";
								echo apply_filters( 'woocommerce_checkout_cart_item_quantity', ' QTY: ' . sprintf( '&nbsp;%s', $cart_item['quantity'] ), $cart_item, $cart_item_key );
								echo "</td>";
								echo "<td class='product-subtotal poppins-semibold' style='border:none;font-size:12px;letter-spacing: 1.44px;text-align:right;'>";
								echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
								echo "</td>";
								echo "</tr>";
								echo "</table>";
							}
							?>
						</td>
					</tr>
					<?php
				}
			}
		}

		do_action( 'woocommerce_review_order_after_cart_contents' );
		?>
	</tbody>
	<tfoot>
    <tr class="coupon_checkout_msg">
      <td colspan="6" style="border:none;padding:0;" id="coupon_checkout_msg_container">
      </td>
    </tr>
		<tr class="coupon_checkout">
			<td colspan="6" style="border:none;padding:0;">
				<?php
				if ( ! defined( 'ABSPATH' ) ) {
				exit; // Exit if accessed directly
				}
				// echo var_dump(wc_coupons_enabled());
				if ( ! wc_coupons_enabled() ) {
				return;
				}
				?>
				
        <table class="checkout-coupon">
          <tr>
            <td class="enter-voucher">
              <input type="text" name="coupon_code" class="input-text coupon-code poppins-medium" placeholder="Enter Voucher" id="coupon_code" value="" />
            </td>
            <td class="apply-voucher" style="padding-right:0;">
              <button id="checkout_apply_coupon" class="button apply-coupon inter-bold" name="apply_coupon" value="APPLY">APPLY</button>
            </td>
          </tr>
        </table>
				
			</td>
		</tr>
		<tr class="cart-subtotal poppins-medium" style="height:10px; ">
			<th style="border:none;padding-top:50px;text-align:left;width:200px;" class="subtotal-checkout poppins-medium"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
			<td colspan="5" style="border:none;padding-top:50px;padding-right:0px;text-align:right;" class="subtotal-checkout poppins-medium">S<?php wc_cart_totals_subtotal_html(); ?></td>
		</tr>

		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?> poppins-medium" style="height:10px;">
				<th style="border:none;text-align:left;" class="subtotal-checkout poppins-medium"><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
				<td colspan="5" style="border:none;padding: 0;text-align:right;" class="subtotal-checkout poppins-medium"><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>

			<?php do_action( 'woocommerce_review_order_before_shipping' ); ?>

			<?php wc_cart_totals_shipping_html(); ?>

			<?php do_action( 'woocommerce_review_order_after_shipping' ); ?>

		<?php endif; ?>

		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<tr class="fee">
				<th><?php echo esc_html( $fee->name ); ?></th>
				<td><?php wc_cart_totals_fee_html( $fee ); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
			<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
				<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited ?>
					<tr class="tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
						<th><?php echo esc_html( $tax->label ); ?></th>
						<td><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr class="tax-total">
					<th><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></th>
					<td><?php wc_cart_totals_taxes_total_html(); ?></td>
				</tr>
			<?php endif; ?>
		<?php endif; ?>

		<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

		<tr class="order-total poppins-medium">
			<td colspan="4" style="padding:0;text-align:right;">
				<table class="total-order">
					<tr>
						<td style="border:none;padding:0;text-align:left;" class="ordertotal-checkout poppins-bold">
							Order Total :
						</td>
						<td style="border:none;padding:0px;text-align:right;" class="ordertotalnum-checkout poppins-bold">
							S<?php wc_cart_totals_order_total_html(); ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>

		<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>
		<tr>
			<td colspan="4" style="border-top: 0;padding-right:0;"><input type="submit" class="button alt" name="woocommerce_checkout_place_order" id="place_order" value="MAKE PAYMENT" data-value="MAKE PAYMENT" /></td>
		</tr>
	</tfoot>
</table>