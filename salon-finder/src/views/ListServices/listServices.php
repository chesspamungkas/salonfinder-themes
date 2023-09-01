<div id="search-results">
  <div class="row">
    <?php foreach($products as $_product): $product = new \DV\SF\models\Product($_product); ?> 
      <div class="col-md-12 col-lg-4 my-2">
        <div class="border container">
          <div class="imageRow d-lg-block d-md-none d-sm-none d-none row">
            <a href="<?php echo get_permalink($product->get_id()); if (!empty($product->getCheapestVariantID())) {echo '?varid='.$product->getCheapestVariantID();} ?>" class="imageContainer">
              <?php if($product->is_on_sale()): ?>
                <span class="discounted rounded p-1 position-absolute mt-2 ml-2 d-block d-sm-none">
                  <?php echo '-' . $product->getDiscountPercent() . '%'; ?>
                </span>
              <?php endif; ?>
              <?= $product->get_image('dvsf-card', ['class'=>'service-image', 'alt'=>$product->get_name()]);  ?>
            </a>
          </div>
          <div class="row">
            <div class="col-xs-4 col-sm-4 col-lg-4 d-block col-4 d-sm-block d-md-block d-lg-none position-relative py-3 pr-0">
                <a class="d-block" href="<?php echo get_permalink($product->get_id()); if (!empty($product->getCheapestVariantID())) {echo '?varid='.$product->getCheapestVariantID();} ?>">
                  <?= $product->get_image('dvsf-card', ['class'=>'service-image rounded', 'alt'=>$product->get_name()]);  ?>
                </a>
                <?php if($product->is_on_sale()): ?>
                  <div class="discounted rounded p-1 d-block mt-1">
                    <?php echo '-' . $product->getDiscountPercent() . '%'; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="col-md-8 col-lg-12 col-sm-8 col-xs-8 col-8 py-3">
              <div class="row">
                <div class="col">
                  <h2 class="title p-0">
                    <a href="<?= get_permalink($product->get_id()) ?>?varid=<?php echo $product->getCheapestVariantID(); ?>" id="product_<?= $product->get_id() ?>"><?= $product->get_title(); ?></a>
                    <?php if($product->meta_exists('duration')): ?>
                      <span class="duration dur_active"><?= $product->get_meta('duration'); ?></span>
                    <?php endif; ?>
                  </h2>
                </div>
              </div>
              <div class="row priceRow d-sm-block d-md-block d-block d-lg-none">
                <div class="col service-price text-left">
                  <?php if($product->is_on_sale()): ?>
                    <div class="promo-price">
                      <span class="regular-price">S$<?php echo number_format( $product->getCheapestRegularPrice(), 2 ); ?></span>
                      <span class="sale-price">S$<?php echo number_format( $product->getCheapestSalesPrice(), 2 ); ?></span>
                    </div>
                  <?php else: ?>
                    <div class="promo-price">
                      <span class="sale-price">S$<?php echo number_format( $product->getCheapestRegularPrice(), 2 ); ?></span>
                    </div>
                  <?php endif; ?>
                </div>    
              </div> 
              <div class="row nameRow">
                <div class="col merchantName">
                  <a href="<?= site_url('search-result').'?search=&location=null&searchType=salon&salonID='.$product->getMerchant()->term_id; ?>?varid=<?php echo $product->getCheapestVariantID(); ?>">
                    <?= $product->getMerchant()->name; ?>
                  </a>
                </div>
              </div>
              <div class="d-lg-block d-md-none d-sm-none d-xs-none d-none border-top pt-2">
                <div class="priceRow">
                  <?= \DV\SF\helpers\Views::render('ListServices/_price', ['product'=>$product]) ?>
                </div> 
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>