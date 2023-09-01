<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility.
if ( empty( $product ) || (!$product->is_visible() && post_password_required($product->ID)) ) {
	return;
}
$product = new \DV\core\models\Product($product);
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
?>
<div <?php wc_product_class( 'col-12 col-md-6 col-lg-4 col-xl-4', $product ); ?>>
	<div class="row card content-poduct-item ml-1 mr-1  flex-row">	
		<div class="col-5 col-md-12 p-2 p-md-0">			
			<a href="<?php echo get_permalink($product->get_id()); ?>" class="imageContainer">
				<?php // do_action( 'woocommerce_before_shop_loop_item_title' ); ?>
				<?php echo $product->get_image( 'dvsf-card', [ 'class' => 'service-image card-img-top', 'alt'=>$product->get_name() ] ); ?>  
				<div class="discount-rate p-3">
					<?php if($product->is_on_sale()): ?>
						<span class="discounted rounded-pill p-1 px-2">
							<?php echo '-' . $product->getDiscountPercent() . '%';?>
						</span>
					<?php endif; ?>
				</div>
			</a>
		</div>
		<div class="col-7 pl-2 pt-2 pr-2 col-md-12">
			<div class="card-body pb-2">
				<a href="<?php echo \DV\core\models\Merchant::makeURL($product->getMerchant()); ?>" class="merchant-link poppins-medium">
					<?php echo $product->getMerchant()->name; ?>
				</a>
				<h5 class="card-title product-name">
					<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>
					<?php //do_action( 'woocommerce_shop_loop_item_title' ); ?>
					<?php echo $product->get_title(); ?>
					<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
				</h5>
				<?php //do_action( 'woocommerce_after_shop_loop_item_title' ); ?>
				<div class="priceRow">
					<div class="priceWrapper row">
						<div class="amount col">
							<?php if($product->is_on_sale()): ?>
								<span class="regular-price poppins-medium">S$<?php echo number_format( $product->get_variation_regular_price(), 2 ); ?></span> 
								<span class="sale-price poppins-bold">S$<?php echo number_format( $product->get_variation_sale_price(), 2 ); ?></span>
							<?php else: ?>
							<div class="promo-price">
								<span class="normal-price">S$<?php echo number_format( $product->get_variation_regular_price(), 2 ); ?></span>
							</div>
							<?php endif; ?>
						</div>
					</div>				
				</div>
			</div>
		</div>
	<?php
	/**
	 * Hook: woocommerce_before_shop_loop_item.
	 *
	 * @hooked woocommerce_template_loop_product_link_open - 10
	 */
	//do_action( 'woocommerce_before_shop_loop_item' );

	/**
	 * Hook: woocommerce_before_shop_loop_item_title.
	 *
	 * @hooked woocommerce_show_product_loop_sale_flash - 10
	 * @hooked woocommerce_template_loop_product_thumbnail - 10
	 */
	//do_action( 'woocommerce_before_shop_loop_item_title' );

	/**
	 * Hook: woocommerce_shop_loop_item_title.
	 *
	 * @hooked woocommerce_template_loop_product_title - 10
	 */
	//do_action( 'woocommerce_shop_loop_item_title' );

	/**
	 * Hook: woocommerce_after_shop_loop_item_title.
	 *
	 * @hooked woocommerce_template_loop_rating - 5
	 * @hooked woocommerce_template_loop_price - 10
	 */
	//do_action( 'woocommerce_after_shop_loop_item_title' );

	/**
	 * Hook: woocommerce_after_shop_loop_item.
	 *
	 * @hooked woocommerce_template_loop_product_link_close - 5
	 * @hooked woocommerce_template_loop_add_to_cart - 10
	 */
	//do_action( 'woocommerce_after_shop_loop_item' );
	?>
	</div>
</div>
