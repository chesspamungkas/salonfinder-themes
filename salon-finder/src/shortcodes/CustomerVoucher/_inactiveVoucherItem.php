<?php 
// $product = new \DV\core\models\Product($item->get_product()->get_parent_id());
$product = $this->getProductItem( $item->get_product() );
$displayDate = new \DateTime($voucherMeta['expiryDate']);
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

foreach( get_the_terms( $product->get_ID(), 'product_cat' ) as $cat ) {
  if( $cat->parent == 0 ) {
    $catSlug = $cat->slug;
  }
}
?>
<?php if( !$isMobile && !$isIOS ) :?>
  <div class="card-profile">
    <div class="pl-4 pr-4 pt-4 pb-3">
      <table width="100%">
        <tr>
          <td class="profile-product product-name eb-garamond-medium">
            <a target="blank" href="<?php echo get_permalink($product->get_id()).'?varid='.$item->get_product()->get_id() ?>"><?= $product->get_title(); ?></a>
          </td>
          <td rowspan="3" text-align="right" width="224" height="116">
            <?php echo $product->get_image( 'dvsf-card', [ 'class' => 'service-image card-img-top', 'alt'=>$product->get_name() ] ); ?>  
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
      <div class="profile-product voucher-expired poppins-medium pt-3 pb-3 pl-4">
        <i class="fas fa-check mr-2"></i> this voucher has been cancelled/refunded
      </div>
    <div class="pt-2 pl-5 pr-5">
        <table width="100%">
          <tr>
            <td colspan="2" style="padding:0;">
              <table class="profile-product voucher-info poppins-medium" width="100%" style="text-align:center;">
                <tr>
                  <!-- <td width="50%" class="profile-product voucher-info review-service pt-3"><i class="fas fa-pen mr-2"></i> review this service!</td> -->
                  <td colspan="2" width="100%" class="profile-product voucher-info pt-1 pb-1"><a href="<?php echo site_url(); ?>/services/<?php echo $catSlug; ?>" class="find-similar-link poppins-medium">Find similar</a></td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
    </div>
  </div>
<?php else: ?>
  <div class="card-profile">
    <div class="p-3">
      <table width="100%">
        <tr>
          <td class="profile-product product-name eb-garamond-medium" colspan="2">
            <a target="blank" href="<?php echo get_permalink($product->get_id()).'?varid='.$item->get_product()->get_id() ?>"><?= $product->get_title(); ?></a>
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
                    <?php echo $product->get_image( 'dvsf-card', [ 'class' => 'service-image card-img-top', 'alt'=>$product->get_name() ] ); ?>  
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </div>
      <div class="profile-product voucher-expired poppins-medium pt-3 pb-3 pl-4">
        <i class="fas fa-check mr-2"></i> this voucher has been cancelled/refunded
      </div>
    <div>
        <table width="100%">
          <tr>
            <td colspan="2" style="padding:0;">
              <table class="profile-product voucher-info poppins-medium" width="100%" style="text-align:center;">
                <tr>
                  <!-- <td width="50%" class="profile-product voucher-info review-service pt-3"><i class="fas fa-pen mr-2"></i> review this service!</td> -->
                  <td colspan="2" width="100%" class="profile-product voucher-info pt-1 pb-1"><a href="<?php echo site_url(); ?>/services/<?php echo $catSlug; ?>" class="find-similar-link poppins-medium">Find similar</a></td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
    </div>
  </div>
<?php endif; ?>