<header>
    <h2><?php _e( 'Customer details', 'woocommerce' ); ?></h2>
</header>
<dl class="customer_details">
<?php
    if ( $order->get_billing_first_name() ) echo '<strong>' . __( 'First Name:', 'woocommerce' ) . '</strong> ' . $order->get_billing_first_name() . '<br/>';
    if ( $order->get_billing_last_name() ) echo '<strong>' . __( 'Last Name:', 'woocommerce' ) . '</strong> ' . $order->get_billing_last_name() . '<br/>';

    if ( $order->get_billing_email() ) echo '<strong>' . __( 'Email:', 'woocommerce' ) . '</strong> ' . $order->get_billing_email() . '<br/>';
    if ( $order->get_billing_phone() ) echo '<strong>' . __( 'Telephone:', 'woocommerce' ) . '</strong> ' . $order->get_billing_phone() . '<br/>';


    // Additional customer details hook
    do_action( 'woocommerce_order_details_after_customer_details', $order );
?>
</dl>

<?php if ( ! wc_ship_to_billing_address_only() && $order->needs_shipping_address() && get_option( 'woocommerce_calc_shipping' ) !== 'no' ) : ?>

<div class="col2-set addresses">

    <div class="col-1">

<?php endif; ?>



<?php if ( ! wc_ship_to_billing_address_only() && $order->needs_shipping_address() && get_option( 'woocommerce_calc_shipping' ) !== 'no' ) : ?>

    </div><!-- /.col-1 -->

    <div class="col-2">

        <header class="title">
            <h3><?php _e( 'Shipping Address', 'woocommerce' ); ?></h3>
        </header>
        <address>
            <?php
                if ( ! $order->get_formatted_shipping_address() ) _e( 'N/A', 'woocommerce' ); else echo $order->get_formatted_shipping_address();
            ?>
        </address>

    </div><!-- /.col-2 -->

</div><!-- /.col2-set -->

<?php endif; ?>

<div class="clear"></div>
