jQuery( document ).ready( function( $ ) {
    if ( $( '#wpadminbar' )[0] ) {
        console.log( 'fix wpadminbar' );
        // jQuery( 'div#page-container' ).css( { 'margin-top': '32px' } );
        if( $( window ).width() > 480 ) {
            $( '.top-header-bk-bar' ).css( { 'top': '32px' } );
            $( '#main-header-container' ).css( { 'top': '72px' } );

            if( !$( 'body' ).hasClass( 'home' ) ) {
                $( '#content-area' ).css( { 'margin-top': '50px' } );
            }
        } else {
            $( '#wpadminbar' ).css( 'position', 'fixed' );
            $( '.fixed-top' ).css( { 'top': '45px' } );
            $( '#main-menu-container, #top-search-container' ).css( { 'margin-top': '45px' } );

            if( $( 'body' ).hasClass( 'home' ) ) {
                // jQuery( '#content-area' ).css( { 'margin-top': '32px' } );
            } else {
                $( '#content-area' ).css( { 'margin-top': '50px' } );
            }
        }
        // console.log( jQuery( '#main-header-container' ).css( 'top' ) );
    } else {
        if( $( 'body' ).hasClass( 'home' ) ) {
            // jQuery( '#content-area' ).css( { 'margin-top': '32px' } );
        } else {
            $( '#content-area' ).css( { 'margin-top': '50px' } );
        }
    }

    jQuery( 'body' ).on( 'click', '.main-menu-btn', function() {
        jQuery('body').toggleClass('showMenu')
    } );
} );