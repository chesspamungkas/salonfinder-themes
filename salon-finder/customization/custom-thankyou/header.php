<?php
$order_data = $order->get_data();
?>
<p><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. Your order has been received.', 'woocommerce' ), $order ); ?></p>
<div class="thanku-content pb-4">
    <h2 class="whats-next poppins-bold pt-3 pb-4">What’s Next?</h1>
    <ul class="thanku-confirmation">
        <li>Your confirmation voucher has been sent to "<?php echo $order_data['billing']['email'];?>". Please check your spam/junk folder if it is not in your inbox in the next 2 minutes.</li>
        <li style="color: #EA4A7F;">Hotmail, Outlook, and Live users may have trouble receiving it. If this happens, you may go to the "Purchase" page located in the "My Account" page. In it you can click on the "Email Voucher" button or just use the voucher code or QR code that is available in that page.</li>
        <li>If it is in the spam/junk, you will need to add us into your address book or mark us as not spam in order to receive important notifications from us regarding your voucher.</li>
        <li>The merchant's outlets contact details are in the email. You will need to contact them to book a session.</li>
        <li>The voucher email also includes the voucher and QR code. Only provide it to the merchant when you are physically at the location to redeem your service.</li>
        <li>You may also enter your account page to check your past purchases.</li>
    </ul>
</div>
<div class="thanku-button">
    <div class="thankucontainer mb-3">
        <a href="<?php echo site_url('/profile/'); ?>" class="woocommerce-Vaccount poppins-semibold">View Account Page</a>
        <a href="#" class="woocommerce-BuyGift poppins-semibold"><i class="fas fa-gift mr-2"></i> Buy As A Gift</a>
    </div>
</div>
<div class="clear"></div>
