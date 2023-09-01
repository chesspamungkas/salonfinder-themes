<?php

global $post;
global $product;

$product = new \DV\core\models\Product($product);
$outlet = new \DV\core\models\Outlet($product->getMerchant());

?>
<style>
    body.noscroll {
        overflow-y: hidden !important;
    }

    .photo-gallery {
        width: 900px;
        height: 467px;
        margin-left: auto;
        margin-right: auto;
    }

    .photo-gallery > .slick-list {
        height: 467px;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
    }

    .photo-gallery > .slick-list > .slick-track > .photo-gallery-item {
        background-color: #fff;
    }

    .photo-gallery > .next-btn {
        right: -20px;
    }

    .photo-gallery > .prev-btn {
        left: -20px;
    }

    .photo-gallery-caption {
        width: 900px;
        margin: 20px auto;
        font-family: 'Poppins', sans-serif;
        font-size: 12px;
        font-weight: 500;
        color: #c1bfb7;
    }

    .photo-gallery-bg {
        position: absolute;
        top: 0;
        width: 100%;
        min-height: 380px;
        z-index: -1;
    }

    .see-all-photo {
        position: absolute;
        bottom: 80px;
        right: 130px;
        font-family: 'Poppins', sans-serif;
        font-size: 11px;
        font-weight: 500;
        color: #5d5d4e;
        text-transform: uppercase;
        background-color: #fff;
        padding: 5px 15px;
        border-radius: 20px;
    }

    .see-all-photo > a, .see-all-photo > a:hover {
        color: #5d5d4e;
        text-decoration: none;
    }

    .see-all-photo > a > .far {
        color: #c1bfb7;
        font-size: 13px;
        margin-right: 5px;
    }
        
    #category-navbar {
        padding: 0;
    }

    .mfp-bg {
        z-index: 9999 !important;
    }

    .mfp-wrap {
        z-index: 9999 !important;
    }

    .mfp-close-btn-in .mfp-close {
        color: $dvPinkColor !important;
    }

    #photo-gallery-popup-wrapper {
        width: 900px;
        height: 467px;
        margin-left: auto;
        margin-right: auto;
    }

    #photo-gallery-popup-wrapper > .mfp-close {
        right: 180px;
        top: -50px;
    }

    #photo-gallery-popup-wrapper > .photo-gallery-popup > .next-btn {
        right: -80px;
    }

    #photo-gallery-popup-wrapper > .photo-gallery-popup > .prev-btn {
        left: -80px;
    }

    #photo-gallery-popup-wrapper > .photo-gallery-popup-nav {
        margin-top: 10px;
        text-align: center;
    }

    #photo-gallery-popup-wrapper > .photo-gallery-popup-nav > .photo-gallery-item {
        max-width: 100px;
        display: inline-block;
        margin: 0 5px;
        cursor: pointer;
    }

    #photo-gallery-popup-wrapper > .photo-gallery-popup-nav > .current-photo {
        border: 1px solid #fff;
    }

    @media ( max-width: 480px ) {
        .photo-gallery {
            width: 95%;
            height: calc( 95vw * 0.519 );
        }

        .photo-gallery > .slick-list {
            height: calc( 95vw * 0.519 );
        }

        .photo-gallery > .slick-list > .slick-track {
            position: unset;
        }

        .photo-gallery > .next-btn {
            width: 30px !important;
            right: -20px;
        }

        .photo-gallery > .prev-btn {
            width: 30px !important;
            left: -20px;
        }

        .photo-gallery-caption {
            width: 100%;
            padding-left: .65rem;
        }

        .photo-gallery-bg {
            /* min-height: 200px; */
            display: none !important;
        }

        .see-all-photo {
            position: absolute;
            bottom: 90px;
            right: 20px;
            font-size: 9px;
        }

        #photo-gallery-popup-wrapper {
            width: 93%;
            height: calc( 93vw * 0.519 );
        }

        #photo-gallery-popup-wrapper > .photo-gallery-popup {
            margin: 0 auto;
        }

        #photo-gallery-popup-wrapper > .photo-gallery-popup > .slick-list {
            height: calc( 93vw * 0.519 );
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }

        #photo-gallery-popup-wrapper > .photo-gallery-popup > .slick-list > .slick-track {
            position: unset;
        }

        #photo-gallery-popup-wrapper > .mfp-close {
            right: 0;
            top: -50px;
        }

        #photo-gallery-popup-wrapper > .photo-gallery-popup > .slick-arrow {
            top: 40%;
        }

        #photo-gallery-popup-wrapper > .photo-gallery-popup > .prev-btn {
            width: 30px !important;
            left: -15px;
        }

        #photo-gallery-popup-wrapper > .photo-gallery-popup > .next-btn {
            width: 30px !important;
            right: -15px;
        }

        #photo-gallery-popup-wrapper > .photo-gallery-popup-nav > .photo-gallery-item {
            max-width: 60px;
        }
    }

    @media (max-width: 365px) {
        .photo-gallery-caption {
            padding-left: .5rem;
        }
    }
</style>
<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/node_modules/slick-lightbox/dist/slick-lightbox.css">
<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/node_modules/magnific-popup/dist/magnific-popup.css">

<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/node_modules/slick-lightbox/dist/slick-lightbox.min.js"></script> 
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/node_modules/magnific-popup/dist/jquery.magnific-popup.min.js"></script> 

<div class="container-fluid px-0 single-product">
    <?php if(post_password_required()): ?>
        <div class="row no-gutters">
            <div class="col text-center mt-5 poppins-medium" style="min-height: 300px;">
                <?php echo get_the_password_form( $post ); ?>
            </div>
        </div>
    <?php else: ?>
        <div class="row no-gutters">
            <div class="col">
                <div class="container" id="breadcrumb-container">
                    <div class="row no-gutters">
                        <div class="col">
                            <?php echo do_shortcode( '[breadcrumb]' ); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row no-gutters">
            <div class="col">
                <?php echo do_shortcode( '[photo-gallery]' ); ?>
                <div class="photo-gallery-bg gray-gradient-bg"></div>
            </div>
        </div>
        <div class="row no-gutters">
            <div class="col">
                <?php echo do_shortcode( '[product-details]' ); ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    jQuery( document ).ready( function($) {
        if( $( window ).width() > 481 ) {
            $( '#main' ).css( 'margin-top', '80px' );
        }
    } );
</script>