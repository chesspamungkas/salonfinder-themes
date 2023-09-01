jQuery( document ).ready( function( $ ) {
    

    $("#searchTextm").focus(function() {
      $('.search-full-view').addClass("search-normal-screen");
      $('.search-full-view #searchTextm').focus();
      $( '#main-header-container' ).css( 'z-index', '9999' );
    });

    $("#search-close").click(function() {
      $( '#main-header-container' ).css( 'z-index', '1030' );
      $('.search-full-view').removeClass("search-normal-screen");
      if(document.referrer.indexOf(window.location.hostname) != -1){
        var referrer =  document.referrer;
      }
    });

    $('.slider').slick( {
        dots: false,
        infinite: false,
        speed: 300,
        slidesToShow: 4,
        slidesToScroll: 4,
        arrows: true,
        prevArrow: '<img src="prev-arrow.png" class="prev-btn" style="width: 40px; height: auto;" />',
        nextArrow: '<img src="next-arrow.png" class="next-btn" style="width: 40px; height: auto;" />',
        responsive: [
        //   {
        //     breakpoint: 1024,
        //     settings: {
        //       slidesToShow: 3,
        //       slidesToScroll: 3,
        //       infinite: true,
        //       dots: true
        //     }
        //   },
          {
            breakpoint: 600,
            settings: {
              arrows: false,
              slidesToShow: 2,
              slidesToScroll: 2
            }
          },
          {
            breakpoint: 481,
            settings: {
              arrows: false,
              slidesToShow: 1,
              slidesToScroll: 1,
              variableWidth: true,
            }
          }
        ]
    } );

    $( '.view-qr-code' ).magnificPopup( { type:'image' } );
} );