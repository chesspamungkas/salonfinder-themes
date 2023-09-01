<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'WOOCOMMERCE_CHECKOUT' ) ) {
	define( 'WOOCOMMERCE_CHECKOUT', true );
}

// do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}
?>
<style>
	#page-container {
		background-color: #fafafa;
	}

	.woocommerce-billing-fields {
		background-color: #ffffff;
		padding-top: 40px;
		padding-bottom: 20px;
	}

	#order_review {
		background-color: #ffffff;
		margin-top: 20px;
		padding-bottom: 0;
	}

	.order-summary {
		background-color: #ffffff;
		padding: 90px 80px 50px 85px;
	}

	table.sub-total {
		background-color: #ffffff;
		padding-left:0px;
	}

	td.enter-voucher, td.apply-voucher {
		padding-top: 9px !important;
	}

	.product-name {
		font-size: 16px;
		min-height: 100px;
		letter-spacing: 0.4px !important;
		color: #22201B !important;
	}

	.product-total {
		font-size: 10px !important;
		text-align: right;
		letter-spacing: 0.5px !important;
		color: #626262 !important;
	}

	.cart_item > .product-thumbnail, 
	.cart_item > .product-name, 
	.cart_item > .product-quantity, 
	.cart_item > .product-subtotal {
		border-top: 0 !important;
	}

	.cart_item:not(:last-child) > .product-thumbnail, 
	.cart_item:not(:last-child) > .product-name, 
	.cart_item:not(:last-child) > .product-quantity, 
	.cart_item:not(:last-child) > .product-subtotal {
		border-top: 0 !important;
		border-bottom: 1px solid #f1f1f1;
	}

	tr.order-total {
		height: 50px !important;
	}

	@media (max-width: 480px) {
		.order-summary {
			padding: 50px 20px;
		}
	}
</style>
<div class="profile-body-container">
	<h1 class="checkout-title poppins-bold mb-4">Checkout</h1>
	<div id="profile">
		<form class="checkout_coupon woocommerce-form-coupon" method="post" id="woocommerce-form-coupon">
			<table class="checkout-coupon">
				<tr>
					<td class="enter-voucher">
						<input type="text" name="coupon_code" class="input-text coupon-code poppins-medium" placeholder="Enter Voucher" id="hidden_coupon_code" value="" />
					</td>
					<td class="apply-voucher" style="padding-right:0;">
						<button type="submit" class="button apply-coupon inter-bold" value="APPLY">APPLY</button>
					</td>
				</tr>
			</table>
		</form>
		<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

			<?php if ( $checkout->get_checkout_fields() ) : ?>

				<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

				<?php do_action( 'woocommerce_checkout_billing' ); ?>

				<?php do_action( 'woocommerce_checkout_shipping' ); ?>

				<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

			<?php endif; ?>
			
			<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

			<div id="order_review" class="woocommerce-checkout-review-order">
				<?php do_action( 'woocommerce_checkout_order_review' ); ?>
			</div>
			
		</form>
		
	</div>
</div>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
<script>
	jQuery(document).ready(function($){
		$('#order_review').on('click', '#checkout_apply_coupon', function(event){
			event.preventDefault()
			$('#hidden_coupon_code').val($('#coupon_code').val())
			$('form.checkout_coupon').submit();
		});
		$( document.body ).on( 'updated_checkout', function(event, code) {
			var msg = $( '.woocommerce-error, .woocommerce-message' ).clone()
			$( '.woocommerce-error, .woocommerce-message' ).remove();
			$('#coupon_checkout_msg_container').html(msg)
			$('#coupon_code').val('')
		});
	});
    
</script>