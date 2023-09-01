<div id="search-results-without-mobile" class="latestPromoContainer">
  <?php foreach($products as $_product): $product = new \DV\SF\models\Product($_product); ?> 
    <div class="my-1 col-md-12">
      <div class="border">
        <div class="imageRow">
          <a href="<?php echo get_permalink($product->get_id()); ?>" class="imageContainer">
            <?= $product->get_image('dvsf-card', ['class'=>'service-image']);  ?>
          </a>
        </div>
        <div class="container-fluid">
          <div class="row">
            <div class="py-3 col">
              <div class="row">
                <div class="col">
                  <h3 class="title p-0">
                    <a href="<?php echo get_permalink($product->get_id()); ?>"><?= $product->get_title(); ?></a>
                    <?php if($product->meta_exists('duration')): ?>
                      <span class="duration dur_active"><?= $product->get_meta('duration'); ?></span>
                    <?php endif; ?>
                  </h3>
                </div>
              </div>
              <div class="row nameRow">
                <div class="merchantName col">
                  <a href="<?php echo get_permalink($product->get_id()); ?>">
                    <?= $product->getMerchant()->name; ?>
                  </a>
                </div>
              </div>
              <div class="border-top pt-2">
                <div class="priceRow">
                  <?= \DV\SF\helpers\Views::render('ListServices/_price', ['product'=>$product]) ?>
                </div> 
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
  <div class="my-1 col-md-12" id="lastPromotionCard">
    <div class="border d-flex align-items-center justify-content-center">
      <a href="<?= get_permalink( get_page_by_path( 'beauty-deals' ) ) ?>" class="viewMorePromoBtn load-more-btn">View all promotions</a>
    </div>
  </div>
</div>