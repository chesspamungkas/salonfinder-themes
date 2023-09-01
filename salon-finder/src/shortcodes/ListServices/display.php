<div id="search-results" class="mt-3">
  <div class="row">
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
    <?php foreach($products as $_product): $product = new \DV\core\models\Product($_product); ?> 
      <?php if( !$isMobile && !$isIOS) { ?>
        <div class="card list-product">
          <a href="<?php echo get_permalink($product->get_id()); if (!empty($product->getCheapestVariantID())) {echo '?varid='.$product->getCheapestVariantID();} ?>" class="imageContainer">
            <?php echo $product->get_image( 'dvsf-card', [ 'class' => 'service-image card-img-top', 'alt'=>$product->get_name() ] ); ?>  
          </a>
          <div class="card-body">
            <a href="<?php echo site_url('search-result') . '?search=&location=null&searchType=salon&salonID=' . $product->getMerchant()->term_id; ?>?varid=<?php echo $product->getCheapestVariantID(); ?>" class="merchant-link poppins-medium">
              <?php echo $product->getMerchant()->name; ?>
            </a>
            <h5 class="card-title product-name mt-2">
              <a href="<?php echo get_permalink( $product->get_id() ); ?>?varid=<?php echo $product->getCheapestVariantID(); ?>" id="product_<?php echo $product->get_id(); ?>"><?php echo $product->get_title(); ?></a>
            </h5>
            <div class="priceRow">
              <?php echo $this->render( 'ListServices/_price', [ 'product' => $product ] ); ?>
            </div>
          </div>
        </div>
      <?php } else { ?>
        <div class="card list-product">
          <div class="col-md-12">
            <div class="row">
              <div class="col-md-5 p-2">
                <a href="<?php echo get_permalink($product->get_id()); if (!empty($product->getCheapestVariantID())) {echo '?varid='.$product->getCheapestVariantID();} ?>" class="imageContainer">
                  <?php echo $_product->get_image( 'list-services-mobile', [ 'class' => 'service-image card-img-top', 'alt'=>$product->get_name() ] ); ?>  
                </a>
              </div>
              <div class="col-md-7 pl-2 pt-2 pr-2">
                <div class="card-body pb-2">
                  <a href="<?php echo site_url('search-result') . '?search=&location=null&searchType=salon&salonID=' . $product->getMerchant()->term_id; ?>?varid=<?php echo $product->getCheapestVariantID(); ?>" class="merchant-link poppins-medium">
                    <?php echo $product->getMerchant()->name; ?>
                  </a>
                  <h5 class="card-title product-name">
                    <a href="<?php echo get_permalink( $product->get_id() ); ?>?varid=<?php echo $product->getCheapestVariantID(); ?>" id="product_<?php echo $product->get_id(); ?>"><?php echo $_product->get_title(); ?></a>
                  </h5>
                  <div class="priceRow">
                    <?php echo $this->render( 'ListServices/_price', [ 'product' => $product ] ); ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php }?>
    <?php endforeach;?>
  </div>
</div>