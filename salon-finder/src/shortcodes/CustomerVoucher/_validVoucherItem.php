<?php
$product = $this->getProductItem($item->get_product());

$displayDate = new \DateTime($voucherMeta['expiryDate']);
$useragent = $_SERVER['HTTP_USER_AGENT'];
$isMobile = false;
$isIOS = false;

$current_user = wp_get_current_user();

if (preg_match('/(Mobile|Android|Tablet|GoBrowser|[0-9]x[0-9]*|uZardWeb\/|Mini|Doris\/|Skyfire\/|iPhone|Fennec\/|Maemo|Iris\/|CLDC\-|Mobi\/)/uis', $useragent)) {
  $isMobile = true;
  $device = 'mobile';
  $isIOS = false;

  if (stripos($useragent, 'iphone') !== false || stripos($useragent, 'ipad') !== false) {
    // $isIOS = true;
    $isIOS = true;
  }
}

$voucher = $voucherCode;
$noQrMsg = 'Please contact our administrator at salonfinder@yourdomain.com.';
?>
<?php if (!$isMobile && !$isIOS) : ?>
  <div class="card-profile">
    <div class="pl-4 pr-4 pt-4 pb-3">
      <table width="100%">
        <tr>
          <td class="profile-product product-name eb-garamond-medium">
            <a target="blank" href="<?php echo get_permalink($product->get_id()) . '?varid=' . $item->get_product()->get_id() ?>"><?= $product->get_title(); ?></a>
          </td>
          <td rowspan="3" text-align="right" width="224" height="116">
            <?php echo $product->get_image('dvsf-card', ['class' => 'service-image card-img-top', 'alt' => $product->get_name()]); ?>
          </td>
        </tr>
        <tr>
          <td class="profile-product">
            <a href="<?php echo site_url('search-result') . '?search=&location=&searchType=salon&salonID=' . $product->getMerchant()->term_id; ?>?varid=<?php echo $product->getCheapestVariantID(); ?>" class="merchant-link poppins-medium">
              <?= $product->getMerchant()->name; ?>
            </a>
          </td>
        </tr>
        <tr>
          <td class="pt-3">
            <table class="profile-product voucher-info" width="100%">
              <tr>
                <td width="50%">
                  <table class="profile-product voucher-info">
                    <tr>
                      <td class="poppins-medium pr-5">PURCHASED</td>
                      <td class="poppins-semibold"><?= $order->get_date_paid()->date('d M Y'); ?></td>
                    </tr>
                    <tr>
                      <td class="poppins-medium pr-5">EXPIRES</td>
                      <td class="poppins-semibold">
                        <?= $displayDate->format('d M Y') ?>
                      </td>
                    </tr>
                    <tr>
                      <td class="poppins-medium pr-5">VOUCHER CODE</td>
                      <td class="poppins-semibold">
                        <?= $voucherCode; ?>
                      </td>
                    </tr>
                  </table>
                </td>
                <!-- <td width="50%">
                  <table class="profile-product voucher-info" width="100%">
                    <tr>
                      <td><a class="send-gift-btn poppins-medium" href="#"><i class="fas fa-gift mr-2"></i> Send as gift</a></td>
                      <td><a class="concierge-btn poppins-medium" href="#"><i class="fas fa-concierge-bell mr-2"></i> Concierge</a></td>
                    </tr>
                  </table>
                </td> -->
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </div>
    <?php /*if ($status=='gifted') {?>
      <div class="profile-product voucher-status poppins-medium pt-3 pb-3 pl-4">
        <i class="fas fa-gift mr-2"></i> gifted to recipient!
      </div>
    <?php } elseif ($status=='appointment') {?>
      <div class="profile-product voucher-status poppins-medium pt-3 pb-3 pl-4">
        <i class="fas fa-concierge-bell mr-2"></i> appointment booked: 6 nov 2020, 11:30 pm
      </div>
    <?php } elseif ($status=='expired') {?>
      <div class="profile-product voucher-expired poppins-medium pt-3 pb-3 pl-4">
        <i class="fas fa-check mr-2"></i> this voucher has ended
      </div>
    <?php }*/ ?>
    <div>
      <table width="100%" class="purchase-table">
        <tr style="border-top: 1px solid #F1F1F1;">
          <td colspan="2" style="padding:0;">
            <table class="profile-product voucher-info poppins-medium" width="100%" style="text-align:center;">
              <tr>
                <?php if (time() <= strtotime("+7 day", strtotime($order->get_date_paid()->date('Y-m-d') . ' 23:59:59'))) : ?>
                  <td class="profile-product voucher-info" width="33%" style="border-right: 1px solid #F1F1F1;"><a href="#<?php echo $voucher; ?>-cancel-voucher" class="find-similar-link view-qr-code <?php echo $voucher; ?>-btn">CANCEL</a></td>
                  <td class="profile-product voucher-info"><a href="#<?php echo $voucher; ?>-email-voucher" class="find-similar-link view-qr-code <?php echo $voucher; ?>-email-btn">EMAIL VOUCHER</a></td>
                  <?php if (isset($voucherMeta['qrCode'])) : ?>
                    <td class="profile-product voucher-info" width="33%" style="border-left: 1px solid #F1F1F1;"><a href="<?php echo $voucherMeta['qrCode']; ?>" class="find-similar-link view-qr-code poppins-medium">VIEW QR CODE</a></td>
                  <?php else : ?>
                    <td class="profile-product voucher-info" width="33%" style="border-left: 1px solid #F1F1F1;">
                      <a href="#no-qr-code" class="find-similar-link no-qr view-qr-code poppins-medium">VIEW QR CODE</a>
                      <div id="no-qr-code" class="p-4 mfp-hide mfp-container" style="position: relative; background: #fff; width: auto; max-width: 400px; margin: 20px auto; text-align: left;">
                        <p class="poppins-bold text-center"><?php echo $noQrMsg; ?></p>
                      </div>
                    </td>
                  <?php endif; ?>
                <?php else : ?>
                  <td class="profile-product voucher-info"><a href="#<?php echo $voucher; ?>-email-voucher" class="find-similar-link view-qr-code <?php echo $voucher; ?>-email-btn">EMAIL VOUCHER</a></td>
                  <?php if (isset($voucherMeta['qrCode'])) : ?>
                    <td class="profile-product voucher-info" width="50%" style="border-left: 1px solid #F1F1F1;"><a href="<?php echo $voucherMeta['qrCode']; ?>" class="find-similar-link view-qr-code poppins-medium">VIEW QR CODE</a></td>
                  <?php else : ?>
                    <td class="profile-product voucher-info" width="50%" style="border-left: 1px solid #F1F1F1;">
                      <a href="#no-qr-code" class="find-similar-link no-qr view-qr-code poppins-medium">VIEW QR CODE</a>
                      <div id="no-qr-code" class="p-4 mfp-hide mfp-container" style="position: relative; background: #fff; width: auto; max-width: 400px; margin: 20px auto; text-align: left;">
                        <p class="poppins-bold text-center"><?php echo $noQrMsg; ?></p>
                      </div>
                    </td>
                  <?php endif; ?>
                <?php endif; ?>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </div>
  </div>
<?php else : ?>
  <div class="card-profile">
    <div class="p-3">
      <table width="100%">
        <tr>
          <td class="profile-product product-name eb-garamond-medium" colspan="2">
            <a target="blank" href="<?php echo get_permalink($product->get_id()) . '?varid=' . $item->get_product()->get_id() ?>"><?= $product->get_title(); ?></a>
          </td>
        </tr>
        <tr>
          <td class="profile-product" colspan="2">
            <a href="<?php echo site_url('search-result') . '?search=&location=&searchType=salon&salonID=' . $product->getMerchant()->term_id; ?>?varid=<?php echo $product->getCheapestVariantID(); ?>" class="merchant-link poppins-medium">
              <?= $product->getMerchant()->name; ?>
            </a>
          </td>
        </tr>
        <tr>
          <td class="pt-3" colspan="2">
            <table class="profile-product voucher-info" width="100%">
              <tr>
                <td width="75%">
                  <table class="profile-product voucher-info">
                    <tr>
                      <td class="poppins-medium pr-5">PURCHASED</td>
                      <td class="poppins-semibold"><?= $order->get_date_paid()->date('d M Y'); ?></td>
                    </tr>
                    <tr>
                      <td class="poppins-medium pr-5">EXPIRES</td>
                      <td class="poppins-semibold">
                        <?= $displayDate->format('d M Y') ?>
                      </td>
                    </tr>
                    <tr>
                      <td class="poppins-medium pr-5">VOUCHER CODE</td>
                      <td class="poppins-semibold">
                        <?= $voucherCode; ?>
                      </td>
                    </tr>
                  </table>
                </td>
                <td>
                  <?php echo $product->get_image('dvsf-card', ['class' => 'service-image card-img-top', 'alt' => $product->get_name()]); ?>
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <!-- <tr>
          <td width="40%" class="pt-3"><a class="send-gift-btn poppins-medium" href="#"><i class="fas fa-gift mr-2"></i> Send as gift</a></td>
          <td class="pt-3"><a class="concierge-btn poppins-medium" href="#"><i class="fas fa-concierge-bell"></i> Concierge</a></td>
        </tr> -->
      </table>
    </div>
    <?php /*if ($status=='gifted') {?>
      <div class="profile-product voucher-status poppins-medium pt-3 pb-3 pl-4">
        <i class="fas fa-gift mr-2"></i> gifted to recipient!
      </div>
    <?php } elseif ($status=='appointment') {?>
      <div class="profile-product voucher-status poppins-medium pt-3 pb-3 pl-4">
        <i class="fas fa-concierge-bell mr-2"></i> appointment booked: 6 nov 2020, 11:30 pm
      </div>
    <?php } elseif ($status=='expired') {?>
      <div class="profile-product voucher-expired poppins-medium pt-3 pb-3 pl-4">
        <i class="fas fa-check mr-2"></i> this voucher has ended
      </div>
    <?php }*/ ?>
    <div>
      <table width="100%" class="purchase-table">
        <tr style="border-top: 1px solid #F1F1F1;">
          <td colspan="2" style="padding:0;">
            <table class="profile-product voucher-info poppins-medium" width="100%" style="text-align:center;">
              <tr>
                <?php if (time() <= strtotime("+7 day", strtotime($order->get_date_paid()->date('Y-m-d') . ' 23:59:59'))) : ?>
                  <td class="profile-product voucher-info" width="33%" style="border-right: 1px solid #F1F1F1;"><a href="#<?php echo $voucher; ?>-cancel-voucher" class="find-similar-link view-qr-code <?php echo $voucher; ?>-btn">CANCEL</a></td>
                  <td class="profile-product voucher-info"><a href="#<?php echo $voucher; ?>-email-voucher" class="find-similar-link view-qr-code <?php echo $voucher; ?>-email-btn">EMAIL VOUCHER</a></td>
                  <?php if (isset($voucherMeta['qrCode'])) : ?>
                    <td class="profile-product voucher-info" width="33%" style="border-left: 1px solid #F1F1F1;"><a href="<?php echo $voucherMeta['qrCode']; ?>" class="find-similar-link view-qr-code poppins-medium">VIEW QR CODE</a></td>
                  <?php else : ?>
                    <td class="profile-product voucher-info" width="33%" style="border-left: 1px solid #F1F1F1;">
                      <a href="#no-qr-code" class="find-similar-link no-qr view-qr-code poppins-medium">VIEW QR CODE</a>
                      <div id="no-qr-code" class="p-4 mfp-hide mfp-container" style="position: relative; background: #fff; width: auto; max-width: 400px; margin: 20px auto; text-align: left;">
                        <p class="poppins-bold text-center"><?php echo $noQrMsg; ?></p>
                      </div>
                    </td>
                  <?php endif; ?>
                <?php else : ?>
                  <td class="profile-product voucher-info"><a href="#<?php echo $voucher; ?>-email-voucher" class="find-similar-link view-qr-code <?php echo $voucher; ?>-email-btn">EMAIL VOUCHER</a></td>
                  <?php if (isset($voucherMeta['qrCode'])) : ?>
                    <td class="profile-product voucher-info" width="50%" style="border-left: 1px solid #F1F1F1;"><a href="<?php echo $voucherMeta['qrCode']; ?>" class="find-similar-link view-qr-code poppins-medium">VIEW QR CODE</a></td>
                  <?php else : ?>
                    <td class="profile-product voucher-info" width="50%" style="border-left: 1px solid #F1F1F1;">
                      <a href="#no-qr-code" class="find-similar-link no-qr view-qr-code poppins-medium">VIEW QR CODE</a>
                      <div id="no-qr-code" class="p-4 mfp-hide mfp-container" style="position: relative; background: #fff; width: auto; max-width: 400px; margin: 20px auto; text-align: left;">
                        <p class="poppins-bold text-center"><?php echo $noQrMsg; ?></p>
                      </div>
                    </td>
                  <?php endif; ?>
                <?php endif; ?>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </div>
  </div>
<?php endif; ?>

<!-- Email Voucher Popup -->
<div id="<?php echo $voucher; ?>-email-voucher" class="mfp-hide mfp-container" style="position: relative; background: #fff; width: auto; max-width: 400px; margin: 20px auto; text-align: left; padding: 0;">
  <div class="loading-div text-center" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgb( 0, 0, 0, .5); z-index: 9999; display: none;">
    <div class="spinner-border text-light" role="status" style="margin-top: 150px;">
      <span class="sr-only">Loading...</span>
    </div>
  </div>
  <p class="poppins-bold" style="font-size: 20px; padding-left: 20px; padding-right: 20px;">Email Voucher Details To Your Inbox?</p>
  <div class="container m-0 p-0">
    <div class="row m-0 p-0">
      <div class="col-8 pl-4 pr-2 pt-0 pb-3" style="line-height: 1.2;">
        <a href="<?php echo site_url('search-result') . '?search=&location=&searchType=salon&salonID=' . $product->getMerchant()->term_id; ?>?varid=<?php echo $product->getCheapestVariantID(); ?>" class="merchant-link poppins-medium" style="font-size: 8px;">
          <?= $product->getMerchant()->name; ?>
        </a><br />
        <a target="blank" href="<?php echo get_permalink($product->get_id()) . '?varid=' . $item->get_product()->get_id() ?>" class="eb-garamond-semibold" style="font-size: 16px;"><?= $product->get_title(); ?></a>
      </div>
      <div class="col-4 pl-2 pr-4 pt-0 pb-3">
        <?php echo $product->get_image('dvsf-card', ['class' => 'service-image card-img-top', 'alt' => $product->get_name()]); ?>
      </div>
    </div>
    <div class="row m-0 p-0">
      <div class="col-4 poppins-medium pl-4 pr-2 pt-0 pb-3" style="font-size: 10px; text-transform: uppercase;">Voucher Code</div>
      <div class="col-8 poppins-bold pl-2 pr-4 pt-0 pb-3" style="font-size: 10px;"><?= $voucherCode; ?></div>
    </div>
    <form method="POST" id="email-<?php echo $voucherCode; ?>-form" class="email-voucher-form">
      <div class="row m-0 p-0">
        <div class="col px-4 pt-0 pb-3">
          <div class="form-group mb-2">
            <input type="email" class="form-control poppins-medium" name="<?php echo $voucherCode; ?>-your-email" id="<?php echo $voucherCode; ?>-your-email" placeholder="Your Email" value="<?php echo $current_user->user_email; ?>" style="border-radius: 1rem; font-size: 12px; padding-top: 1.2rem; padding-bottom: 1.2rem;" />
          </div>
        </div>
      </div>
      <div class="row m-0 p-0">
        <!--div class="col-8 m-0 px-0 py-2 text-center" style="background-color: #000;"-->
        <button type="submit" class="poppins-medium send-email-btn align-middle col-8 m-0 px-0 py-3 text-center" style="background-color: #000;">Send To My Email</button>
        <!--/div-->
        <div class="col-4 m-0 px-0 py-3 text-center close-div-btn close-cancel-mfp" style="cursor:pointer; background-color: #f1f1f1;">Close</div>
      </div>
    </form>
  </div>
</div>

<!-- Cancel Voucher Popup -->
<div id="<?php echo $voucher; ?>-cancel-voucher" class="mfp-hide mfp-container" style="position: relative; background: #fff; width: auto; max-width: 400px; margin: 20px auto; text-align: left; padding: 0;">
  <div class="loading-div text-center" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgb( 0, 0, 0, .5); z-index: 9999; display: none;">
    <div class="spinner-border text-light" role="status" style="margin-top: 150px;">
      <span class="sr-only">Loading...</span>
    </div>
  </div>
  <form method="POST" id="<?php echo $voucher; ?>-form" class="cancel-voucher-form">
    <p class="poppins-bold" style="font-size: 20px; padding-left: 20px; padding-right: 20px;">Are You Sure You Want To Cancel This Voucher?</p>
    <input type="hidden" name="voucher_code" id="voucher-code" value="<?php echo $voucher; ?>" />
    <div class="form-check">
      <input class="form-check-input <?php echo $voucher; ?>-radio" type="radio" name="<?php echo $voucher; ?>Radios" id="<?php echo $voucher; ?>-radio-1" value="Bought Wrong" style="margin-top: .45rem;" checked>
      <label class="form-check-label poppins-medium" for="<?php echo $voucher; ?>-radio-1" style="font-size: 12px;">
        Bought Wrong
      </label>
    </div>
    <div class="form-check">
      <input class="form-check-input <?php echo $voucher; ?>-radio" type="radio" name="<?php echo $voucher; ?>Radios" id="<?php echo $voucher; ?>-radio-2" value="Can't Contact Merchant" style="margin-top: .45rem;">
      <label class="form-check-label poppins-medium" for="<?php echo $voucher; ?>-radio-2" style="font-size: 12px;">
        Can't Contact Merchant
      </label>
    </div>
    <div class="form-check">
      <input class="form-check-input <?php echo $voucher; ?>-radio" type="radio" name="<?php echo $voucher; ?>Radios" id="<?php echo $voucher; ?>-radio-3" value="Can't Book The Date/Time Preferred" style="margin-top: .45rem;">
      <label class="form-check-label poppins-medium" for="<?php echo $voucher; ?>-radio-3" style="font-size: 12px;">
        Can't Book The Date/Time Preferred
      </label>
    </div>
    <div class="form-check">
      <input class="form-check-input <?php echo $voucher; ?>-radio" type="radio" name="<?php echo $voucher; ?>Radios" id="<?php echo $voucher; ?>-radio-4" value="Found Better Deal From Merchant" style="margin-top: .45rem;">
      <label class="form-check-label poppins-medium" for="<?php echo $voucher; ?>-radio-4" style="font-size: 12px;">
        Found Better Deal From Merchant
      </label>
    </div>
    <div class="form-check">
      <input class="form-check-input <?php echo $voucher; ?>-radio" type="radio" name="<?php echo $voucher; ?>Radios" id="<?php echo $voucher; ?>-radio-5" value="Found Better Deal Elsewhere" style="margin-top: .45rem;">
      <label class="form-check-label poppins-medium" for="<?php echo $voucher; ?>-radio-5" style="font-size: 12px;">
        Found Better Deal Elsewhere
      </label>
    </div>
    <div class="form-check">
      <input class="form-check-input enable-textbox <?php echo $voucher; ?>-radio" type="radio" name="<?php echo $voucher; ?>Radios" id="<?php echo $voucher; ?>-radio-6" value="Others" style="margin-top: .45rem;">
      <label class="form-check-label poppins-medium" for="<?php echo $voucher; ?>-radio-6" style="font-size: 12px;">
        Others
      </label>
      <input type="text" name="<?php echo $voucher; ?>-radio-6-others" id="<?php echo $voucher; ?>-radio-6-text" value="" class="form-control poppins-medium" size="1000%" disabled="disabled" style="border-radius: 1rem; font-size: 12px; padding-top: 1.2rem; padding-bottom: 1.2rem;" />
    </div>

    <div class="container m-0 px-0 pt-3 pb-0">
      <div class="row m-0 p-0">
        <!--div class="col-8 m-0 px-0 py-2 text-center" style="background-color: #000;"-->
        <button type="submit" class="poppins-medium cont-cancel-btn align-middle col-8 m-0 px-0 py-3 text-center" style="background-color: #000;">Continue Cancellation</button>
        <!--/div-->
        <div class="col-4 m-0 px-0 py-3 text-center close-div-btn close-cancel-mfp" style="cursor:pointer; background-color: #f1f1f1;">Close</div>
      </div>
    </div>
  </form>
</div>

<script>
  jQuery(document).ready(function($) {
    var voucher = '<?php echo $voucher; ?>';
    var updatedOn = 'sfwebsite';
    var updatedBy = '<?php echo $current_user->ID; ?>';
    $('.' + voucher + '-btn').magnificPopup({
      type: 'inline',
      midClick: true // Allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source in href.
    });

    $('.' + voucher + '-email-btn').magnificPopup({
      type: 'inline',
      midClick: true // Allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source in href.
    });

    $('.no-qr').magnificPopup({
      type: 'inline',
      midClick: true // Allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source in href.
    });

    $('.' + voucher + '-radio').on('click', function() {
      $('#' + voucher + '-radio-6-text').prop('disabled', true);
      if ($(this).hasClass('enable-textbox')) {
        $('#' + voucher + '-radio-6-text').prop('disabled', false);
      }
    });

    $('.close-div-btn').on("click", function() {
      $.magnificPopup.close();
    });

    $('#' + voucher + '-form').submit(function(e) {
      e.preventDefault();

      $('.loading-div').show();

      var radios = $('.' + voucher + '-radio');
      var radioVal = radios.filter(':checked').val();
      var nonce = '<?php echo wp_create_nonce($voucher . '-nonce'); ?>';
      var str = '';

      if (radios.filter(':checked').hasClass('enable-textbox')) {
        if ($('#' + voucher + '-radio-6-text').val()) {
          var others = $('#' + voucher + '-radio-6-text').val();
          str = '&radio=' + radioVal + '&other=' + others + '&voucher=' + voucher + '&action=ajaxCancelVoucher&nonce=' + nonce;
        } else {
          alert('Please enter other reason in the textbox!');
          $('.loading-div').hide();
          return false;
        }
      } else {
        str = '&radio=' + radioVal + '&other=&voucher=' + voucher + '&action=ajaxCancelVoucher&nonce=' + nonce;
      }

      $.ajax({
        url: '<?php echo DARVIS_SF_API; ?>/order-vouchers/cancel',
        type: 'POST',
        async: false,
        dataType: 'json',
        data: JSON.stringify({
          'voucherCode': voucher,
          'updatedOn': updatedOn,
          'updatedBy': updatedBy
        }),
        contentType: 'application/json',
        complete: function(data) {
          $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: str,
            success: function(data) {
              $('.loading-div').hide();
              if (data) {
                $('#' + voucher + '-form').html('<p class="poppins-bold" style="font-size: 20px; padding-left: 20px; padding-right: 20px;">Successfully Cancelled</p><div class="p-3"><div class="alert alert-success poppins-semibold">Your voucher has been cancelled successfully. The status will be reflected in your account soon.</div><p class="poppins-medium" style="font-size: 12px;">This page will be automatically reloaded in <span id="seconds">5</span> seconds.</p></div>');

                countdownTimer();
              } else {
                $('#' + voucher + '-form').html('<div class="p-3"><div class="alert alert-danger">Sorry! Please try again later.</div></div>');
              }
            }
          });
        }
      });

      $('.loading-div').hide();
      return false;
    });

    // send email voucher ajax
    $('#email-' + voucher + '-form').submit(function(e) {
      e.preventDefault();

      $('.loading-div').show();

      var yourEmail = $('#' + voucher + '-your-email').val();
      // var nonce = '<?php //echo wp_create_nonce( $voucher . '-nonce' ); 
                      ?>';
      // var str = '';

      if (!yourEmail) {
        alert('Please enter your email in the textbox!');
        $('.loading-div').hide();
        return false;
      }

      $.ajax({
        url: '<?php echo DARVIS_SF_API; ?>/order-vouchers/send-email',
        type: 'POST',
        async: false,
        dataType: 'json',
        data: JSON.stringify({
          'voucherCode': voucher,
          'customerEmail': yourEmail
        }),
        contentType: 'application/json',
        complete: function(data) {
          $('.loading-div').hide();
          $('#email-' + voucher + '-form').html('<div class="p-3"><div class="alert alert-success poppins-semibold">Your voucher details has been sent to your email, please check your inbox.</div><p class="poppins-medium" style="font-size: 12px;">Closing in <span id="seconds">5</span> seconds.</p></div>');

          countdownTimer();
        }
      });
      return false;
    });
  });
</script>