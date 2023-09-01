<div class="container">
    <div class="row no-gutters">
        <div class="col">
            <div class="photo-gallery">
                <?php
                // print_r( $photos );
                if (count($photos)) {
                    foreach ($photos as $k => $v) {
                        echo '<div class="photo-gallery-item"><img src="' . $v . '" /></div>';
                    }
                }
                ?>
            </div>
            <div style="cursor: pointer;"><a href="#photo-gallery-popup-wrapper" class="see-all-photo see-all-photo-btn"><i class="far fa-images"></i> See All Photos</a></div>
            <div class="photo-gallery-caption">PICTURES SHOWN ARE FOR ILLUSTRATION PURPOSE ONLY. ACTUAL SERVICE MAY VARY.</div>
        </div>
    </div>
</div>

<div id="photo-gallery-popup-wrapper">
    <div class="photo-gallery-popup">
        <?php
        if (count($photos)) {
            foreach ($photos as $k => $v) {
                echo '<div class="photo-gallery-item"><img src="' . $v . '" /></div>';
            }
        }
        ?>
    </div>
    <div class="photo-gallery-popup-nav">
        <?php
        if (count($photos)) {
            foreach ($photos as $k => $v) {
                $class = "";
                if ($k == 0) {
                    $class = " current-photo";
                }
                echo '<div class="photo-gallery-item' . $class . '"><img src="' . $v . '" /></div>';
            }
        }
        ?>
    </div>
</div>

<script>
    jQuery(document).ready(function($) {
        $('#photo-gallery-popup-wrapper').addClass('mfp-hide');

        $('.photo-gallery').slick({
            dots: false,
            infinite: true,
            speed: 300,
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: true,
            prevArrow: '<img src="prev-arrow.png" class="prev-btn" style="width: 40px; height: auto;" />',
            nextArrow: '<img src="next-arrow.png" class="next-btn" style="width: 40px; height: auto;" />',
            lazyLoad: 'ondemand',
            // adaptiveHeight: true,
            responsive: [{
                breakpoint: 480,
                settings: {
                    dots: false,
                    infinite: true,
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    lazyLoad: 'ondemand'
                    // centerMode: true,
                    // centerPadding: '60px'
                }
            }]
        });

        $('.see-all-photo-btn').magnificPopup({
            type: 'inline',
            midClick: true,
            fixedContentPos: true,
            callbacks: {
                open: function() {
                    // $( 'body' ).addClass( 'noscroll' );
                    $('.photo-gallery-popup').slick({
                        dots: false,
                        infinite: true,
                        speed: 300,
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        arrows: true,
                        prevArrow: '<img src="prev-arrow.png" class="prev-btn" style="width: 40px; height: auto;" />',
                        nextArrow: '<img src="next-arrow.png" class="next-btn" style="width: 40px; height: auto;" />',
                        responsive: [{
                            breakpoint: 480,
                            settings: {
                                dots: false,
                                infinite: true,
                                slidesToShow: 1,
                                slidesToScroll: 1
                            }
                        }]
                    });

                    $('.mfp-content').css('margin-top', '-100px');
                },
                // close: function() {
                //     $( 'body' ).removeClass( 'noscroll' );
                // }
            }
        });

        $('.photo-gallery-popup-nav > div').on('click', function() {
            var navItem = $('.photo-gallery-popup-nav > div');

            navItem.removeClass('current-photo');
            $(this).addClass('current-photo');
            $('.photo-gallery-popup').slick('slickGoTo', $(this).index());
        });

        $('.photo-gallery-popup').on('afterChange', function() {
            var index = $('.slick-current').attr("data-slick-index");
            var navItem = $('.photo-gallery-popup-nav > div');
            console.log(index);

            navItem.removeClass('current-photo');
            navItem.eq(index).addClass('current-photo');
        });
    });
</script>