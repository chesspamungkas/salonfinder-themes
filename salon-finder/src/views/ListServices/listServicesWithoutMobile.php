<?php 
  do_action( 'slider-container-start', ['title' => $title, 'view-all-link' => $viewAll] );
  foreach($products as $_product): 
    $product = new \DV\core\models\Product($_product); 
    // print_r( get_permalink( $product->get_id() ) );
?> 
  <div class="card list-product">
    <!--img src="..." class="card-img-top" alt="..."-->
    <a href="<?php echo get_permalink($product->get_id()); ?>" class="imageContainer">
      <?php echo $_product->get_image( 'dvsf-card', [ 'class' => 'service-image card-img-top' ] ); ?>
    </a>
    <div class="card-body">
      <a href="<?php echo \DV\core\models\Merchant::makeURL($product->getMerchant()); ?>" class="merchant-link">
        <?php echo $product->getMerchant()->name; ?>
      </a>
      <h5 class="card-title product-name">
        <a href="<?php echo get_permalink($product->get_id()); ?>" id="product_<?php echo $product->get_id(); ?>"><?php echo $_product->get_title(); ?></a>
      </h5>
      <div class="priceRow">
        <?php echo \DV\core\Views::render( 'ListServices/_price', [ 'product' => $product ] ); ?>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php
  do_action( 'slider-container-end' ); 
?>
