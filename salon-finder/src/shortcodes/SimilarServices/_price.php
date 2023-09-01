<div class="priceWrapper row">  
  <div class="col-2 discount-rate p-0">
    <div class="p-2">
      <?php if( $product->is_on_sale() ): ?>
        <span class="discounted rounded-pill p-1 px-1">
          <?php echo '-' . $product->getDiscountPercent() . '%';?>
        </span>
      <?php endif; ?>
    </div>
  </div>
  <div class="amount col desktop-view">
    <?php if( $product->is_on_sale() ): ?>
      <span class="regular-price">S$<?php echo number_format( $product->getCheapestRegularPrice(), 2 ); ?></span><br/><span class="sale-price">S$<?php echo number_format( $product->getCheapestSalesPrice(), 2 ); ?></span>
    <?php else: ?>
      <div class="promo-price">
        <span class="normal-price">S$<?php echo number_format( $product->getCheapestRegularPrice(), 2 ); ?></span>
      </div>
    <?php endif; ?>
  </div>
  <div class="amount col mobile-view">
    <?php if( $product->is_on_sale() ): ?>
      <span class="regular-price">S$<?php echo number_format( $product->getCheapestRegularPrice(), 2 ); ?></span><span class="sale-price">S$<?php echo number_format( $product->getCheapestSalesPrice(), 2 ); ?></span>
    <?php else: ?>
      <div class="promo-price">
        <span class="normal-price">S$<?php echo number_format( $product->getCheapestRegularPrice(), 2 ); ?></span>
      </div>
    <?php endif; ?>
  </div>
</div>