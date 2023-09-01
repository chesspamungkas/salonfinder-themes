<div class="container" id="category-navbar">
    <div class="row justify-content-center p-md-3 px-0">
        <div class="col cat-slider px-0">
        <?php 
            $numOfItem = 1;
            foreach( $navbar as $menu_item ):
                echo $this->render( 'CategoryNavBar/_item2', [ 'menu_item' => $menu_item, 'numOfItem' => $numOfItem, 'menu_count' => $menu_count ] ); 

                $numOfItem++;
            endforeach;
        ?>
        </div>
    </div>
</div>
<script>
    jQuery( document ).ready( function( $ ) {
        $('.cat-slider').slick( {
            dots: false,
            infinite: false,
            speed: 300,
            slidesToShow: 6,
            slidesToScroll: 6,
            arrows: true,
            prevArrow: '<i class="fas fa-chevron-left prev-btn"></i>',
            nextArrow: '<i class="fas fa-chevron-right next-btn"></i>',
            responsive: [
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 6,
                        slidesToScroll: 6
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        // arrows: false,
                        // swipeToSlide: true,
                        slidesToShow: 2,
                        slidesToScroll: 2
                    }
                }
            ]
        } );
    } );
</script>