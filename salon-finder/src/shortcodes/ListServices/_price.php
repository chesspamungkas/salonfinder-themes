<?php 
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
<div class="priceWrapper row">  
  <div class="col-2 discount-rate p-0">
    <?php if( !$isMobile && !$isIOS) : ?>
      <div class="p-3">
    <?php else: ?>
      <div class="p-2">
    <?php endif; ?> 
      <?php if($product->is_on_sale()): ?>
        <?php if( !$isMobile && !$isIOS) : ?>
          <span class="discounted rounded-pill p-1 px-2">
        <?php else: ?>
          <span class="discounted rounded-pill px-1">
        <?php endif; ?>   
          <?php echo '-' . $product->getDiscountPercent() . '%';?>
        </span>
      <?php endif; ?>
    </div>
  </div>
  <div class="amount col">
    <?php if($product->is_on_sale()): ?>
      <?php if( !$isMobile && !$isIOS) : ?>
        <span class="regular-price">S$<?php echo number_format( $product->getCheapestRegularPrice(), 2 ); ?></span><br/><span class="sale-price">S$<?php echo number_format( $product->getCheapestSalesPrice(), 2 ); ?></span>
      <?php else: ?>
        <span class="regular-price poppins-medium">S$<?php echo number_format( $product->getCheapestRegularPrice(), 2 ); ?></span> <span class="sale-price poppins-bold ml-2">S$<?php echo number_format( $product->getCheapestSalesPrice(), 2 ); ?></span>
      <?php endif; ?>
    <?php else: ?>
      <div class="promo-price">
        <span class="normal-price">S$<?php echo number_format( $product->getCheapestRegularPrice(), 2 ); ?></span>
      </div>
    <?php endif; ?>
  </div>
</div>