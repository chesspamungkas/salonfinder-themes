<div class="container product-details px-md-0">
    <div class="row no-gutters">
        <div class="col-12 col-md-8">
            <h2 class="product-name"><?php echo apply_filters( 'filter_woocommerce_product_title', $product->get_title() ); ?></h2>
            <p class="product-merchant">
                <span class="merchant-name"><?php echo $merchant->name; ?></span>
                <?php if ( $product->getOutletCount() > 0 ) { ?>
                    <span class="outlets"> (<?php echo $product->getOutletCount(); ?> outlet<?php echo $product->getOutletCount()>1?'s':''; ?>)
                <?php } ?>
                </span>
            </p>
            <div class="product-pricing border-bottom pb-4 mb-5">
                <?php if($product->is_on_sale()): ?>
                    <span class="regular-price-strike">S$ <?= number_format($product->get_variation_regular_price(),2) ?></span>
                    <span class="sale-price">S$ <?= number_format($product->get_variation_sale_price(),2) ?></span>
                <?php else: ?>
                    <span class="regular-price">S$ <?= number_format($product->get_variation_regular_price(),2) ?></span>
                <?php endif; ?>
                <?php if($product->is_on_sale()): ?>
                    <span class="discounted rounded-pill p-1 px-2 mt-2">
                        <?php echo '-' . $product->getDiscountPercent() . '%';?>
                    </span>
                <?php endif; ?>
            </div>
            <div class="product-content border-bottom pb-5 mb-5">
                <h4>About The Service</h4>
                <?php the_content(); ?>
            </div>
            <div class="redeemable-outlets border-bottom pb-5 mb-5" id="outlet-locations">
                <h4>Outlets</h4>
                <ul class="outlet_oper redeem-at-content">
                    <?php foreach( $product->getRedeemableOutlet() as $outletProduct ): ?>
                        <li>
                            <p class="outlet-name"><?= $outletProduct['outlet']->queriedOutlet->name ?></p>
                            <p><?= $outletProduct['outlet']->getMeta('outlet_address'); ?>,<br/>Singapore <?= $outletProduct['outlet']->getMeta('outlet_postalcode'); ?></p>
                            <?php
                                echo $this->get_operating_hours( $outletProduct['outlet']->outletMetaKey );
                                // $outlet_oper = get_field( 'outlet_operating_hours', $outletProduct['outlet']->outletMetaKey );
                            ?>
                        </li>
                    <?php endforeach; ?>    
                </ul>
            </div>
            <div class="usage-terms pb-5 mb-5">
                <h4>Usage Terms</h4>
                <div>
                    <ul>
                        <li>This voucher can be redeemed from today to <?= \DV\core\models\Product::getVoucherValidity('60', 'd M Y'); ?> (60 days validity)</li>
                        <?php if( $product->is_on_sale() && $product->get_meta('promoTerms')===''): ?>
                            <?= str_replace( ' style="', ' data-style="', str_replace( '</ul>', '', str_replace( '<ul>','', $product->getSalesDescription() ) ) ); ?> 
                        <?php elseif ($product->is_on_sale() && $product->get_meta('promoTerms')): ?>
                            <?= str_replace( ' style="', ' data-style="', str_replace( '</ul>', '', str_replace( '<ul>','', $product->get_meta('promoTerms') ) ) ); ?>
                        <?php else: ?>
                            <?= str_replace( ' style="', ' data-style="', str_replace( '</ul>', '', str_replace( '<ul>','', $product->get_short_description() ) ) ); ?>
                        <?php endif; ?>
                        <li>Voucher cancellation is only allowed within the first seven days from purchase date. You may read our <a href="https://dailyvanity.sg/faq/"><u>FAQ</u></a> to understand the cancellation process and other matters.</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4 sidebar-panel">
            <div class="card">
                <div class="card-header text-center">
                    Get This Voucher
                    <?php if($product->is_on_sale()): ?>
                        <br/><span class="discounted">And Save <?php echo $product->getDiscountPercent() . '%'; ?>!</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="product-pricing text-center">
                        <?php if($product->is_on_sale()): ?>
                            <span class="regular-price-strike">S$ <?= number_format($product->get_variation_regular_price(),2) ?></span>
                            <span class="sale-price">S$ <?= number_format($product->get_variation_sale_price(),2) ?></span>
                        <?php else: ?>
                            <span class="regular-price">S$ <?= number_format($product->get_variation_regular_price(),2) ?></span>
                        <?php endif; ?>
                        <?php /*if($product->is_on_sale()): ?>
                            <span class="discounted rounded-pill p-1 px-2 mt-2">
                                <?php echo '-' . $product->getDiscountPercent() . '%';?>
                            </span>
                        <?php endif;*/ ?>
                    </div>
                    <div class="buy-voucher-btn text-center">
                        <form class="cart ng-pristine ng-valid variations_form" action="" method="post" enctype="multipart/form-data">
                            <button class="single_add_to_cart_button button alt">Buy Voucher</button>
                            <input type="hidden" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>" />
                            <input type="hidden" name="product_id" value="<?php echo absint( $product->get_id() ); ?>" />
                            <input type="hidden" name="variation_id" class="variation_id" value="<?= $product->getCheapestVariantID() ?>" />
                        </form>
                        <a href="#how-to-redeem" class="how-to-redeem-btn">How To Redeem?</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row no-gutters">
        <div class="col similar-services text-center border-bottom pb-5 mb-5">
            <h4>Similar Services</h4>
            <?php echo do_shortcode( '[similar-services]' ); ?>
        </div>
    </div>
    <div class="row no-gutters">
        <div class="col cat-navbar text-center">
            <h4>Explore More Beauty Treats Near You!</h4>
            <?php echo do_shortcode( '[cat-navbar multiple_rows="yes"]' ); ?>
        </div>
    </div>
</div>
<div id="how-to-redeem" class="mfp-hide">
    <?php echo apply_filters( 'the_content', $this->get_how_to_redeem() ); ?>
</div>
<script>
    jQuery( document ).ready( function( $ ) {
        if( $( window ).width() <= 480 ) {
            if( $( '.regular-price-strike' ).length ) {
                $( '.regular-price-strike' ).after( '<br/>' );
            }

            $( '.usage-terms' ).removeClass( 'mb-5' );

            $( '.merchant-name' ).after( '<br/>' );

            $( '.product-content' ).append( '<div class="redeemable-outlet-btn text-center"><a href="#outlet-locations" class="see-outlet-location-btn">See Outlet Locations</a></div>' );

            var outletClone = $( '#outlet-locations' ).clone();

            // console.log( outletClone.text() );

            $( '#outlet-locations' ).hide();

            $( '#page-container' ).prepend( '<div id="outlet-locations-clone"><div class="close-outlet-location-btn"><svg version="1.1" id="close-btn" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 25 25" style="enable-background:new 0 0 25 25;" xml:space="preserve" class="cursor-pointer close-btn"><g id="Component_10_1" transform="translate(1 1.414)"><g id="Group_1" transform="translate(0 -0.5)"><line id="Line_2" class="st0" x1="4.4" y1="18.7" x2="18.6" y2="4.5"></line><line id="Line_4" class="st0" x1="4.4" y1="4.5" x2="18.6" y2="18.7"></line></g></g></svg></div>' + outletClone.html() + '</div>' );

            $( '#outlet-locations-clone' ).hide();

            $( '.sidebar-panel' ).addClass( 'border-bottom pb-5 mb-5' );

            var howToRedeemClone = $( '#how-to-redeem' ).clone();

            $( '#page-container' ).prepend( '<div id="how-to-redeem-clone" style="display: none;"><div class="close-how-to-redeem-btn"><svg version="1.1" id="close-btn" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 25 25" style="enable-background:new 0 0 25 25;" xml:space="preserve" class="cursor-pointer close-btn"><g id="Component_10_1" transform="translate(1 1.414)"><g id="Group_1" transform="translate(0 -0.5)"><line id="Line_2" class="st0" x1="4.4" y1="18.7" x2="18.6" y2="4.5"></line><line id="Line_4" class="st0" x1="4.4" y1="4.5" x2="18.6" y2="18.7"></line></g></g></svg></div>' + howToRedeemClone.html() + '</div>' );

            $( '.how-to-redeem-btn' ).on( 'click', function( e ) {
                e.preventDefault();
                if( $( '#how-to-redeem-clone' ).is( ':hidden' ) ) {
                    $( '#how-to-redeem-clone' ).show();
                } else {
                    $( '#how-to-redeem-clone' ).hide();
                }
            } );

            $( '.close-how-to-redeem-btn > .close-btn' ).on( 'click', function( e ) {
                e.preventDefault();
                
                if( $( '#how-to-redeem-clone' ).is( ':visible' ) ) {
                    $( '#how-to-redeem-clone' ).hide();
                }
            } );
        } else {
            $( '.how-to-redeem-btn' ).magnificPopup( {
                type: 'inline',
                midClick: true
            } );
        }

        $( '.see-outlet-location-btn' ).on( 'click', function( e ) {
            e.preventDefault();
            var id = $( '.see-outlet-location-btn' ).attr( 'href' ).replace('#', '');
            // console.log( id );
            if( $( '#outlet-locations-clone' ).is( ':hidden' ) ) {
                $( '#outlet-locations-clone' ).show();
            }
        } );

        $( '.close-outlet-location-btn > .close-btn' ).on( 'click', function( e ) {
            e.preventDefault();
            
            if( $( '#outlet-locations-clone' ).is( ':visible' ) ) {
                $( '#outlet-locations-clone' ).hide();
            }
        } );
    } );
</script>
