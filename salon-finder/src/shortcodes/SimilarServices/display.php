<?php if( $page == 1 ): ?>
<div class="container-fluid p-0 m-0">
    <div class="row m-0 p-0 services-list">
<?php endif; ?>
        <?php 
        foreach( $products as $_product ): 
            $product = new \DV\core\models\Product( $_product ); 
        ?> 
        <div class="col-12 col-md-4 px-md-2 px-0 card-wrapper">
            <div class="card">
                <div class="row no-gutters desktop-view">
                    <a href="<?php echo get_permalink( $product->get_id() ) ; ?>" class="imageContainer col-5 col-md-12">
                        <?php echo $product->get_image( 'dvsf-card', [ 'class' => 'service-image card-img-top' ] ); ?>
                    </a>
                    <div class="card-body px-3 text-left">
                        <a href="<?php echo \DV\core\models\Merchant::makeURL($product->getMerchant()); ?>" class="merchant-link">
                            <?php echo $product->getMerchant()->name; ?>
                        </a>
                        <h5 class="card-title product-name">
                            <a href="<?php echo get_permalink( $product->get_id() ); ?>" id="product_<?php echo $product->get_id(); ?>"><?php echo $product->get_title(); ?></a>
                        </h5>
                        <div class="priceRow">
                            <?php echo $this->render( 'SimilarServices/_price', [ 'product' => $product ] ); ?>
                        </div>
                    </div>
                </div>
                <div class="row no-gutters mobile-view">
                    <div class="col-4">
                        <a href="<?php echo get_permalink( $product->get_id() ); ?>" class="imageContainer col-5 col-md-12 p-0">
                            <?php echo $product->get_image( 'dvsf-card', [ 'class' => 'service-image card-img-top' ] ); ?>
                        </a>
                    </div>
                    <div class="col-8">
                        <div class="card-body text-left">
                            <a href="<?php echo \DV\core\models\Merchant::makeURL($product->getMerchant()); ?>" class="merchant-link">
                                <?php echo $product->getMerchant()->name; ?>
                            </a>
                            <h5 class="card-title product-name">
                                <a href="<?php echo get_permalink( $product->get_id() ); ?>" id="product_<?php echo $product->get_id(); ?>"><?php echo $product->get_title(); ?></a>
                            </h5>
                            <div class="priceRow">
                                <?php echo $this->render( 'SimilarServices/_price', [ 'product' => $product ] ); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
<?php if( $page == 1 ): ?>
    </div>
    <?php if( $page < $total_pages ): ?>
    <div class="row">
        <div class="col pt-5">
            <a class="load-more load-more-btn similar-services-load-more">Load More <i class="fa fa-arrow-down" aria-hidden="true"></i></a>
        </div>
    </div>
    <?php endif; ?>
</div>
<script type="text/javascript">
    var page = <?php echo $page; ?>;
    var totalPages = <?php echo $total_pages; ?>;
    var productID = <?php echo $product_id; ?>;
    var nonce = "<?php echo wp_create_nonce("get_similar_services_nonce"); ?>";
</script>
<script src="<?php echo get_template_directory_uri(); ?>/src/js/SimilarServices/SimilarServices.js"></script>
<?php endif; ?>