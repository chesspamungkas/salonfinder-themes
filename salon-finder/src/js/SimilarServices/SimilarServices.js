jQuery( document ).ready( function( $ ) {
    $( '.similar-services-load-more' ).on( 'click', function( e ) {
        e.preventDefault(); 

        var str = '&page=' + (page+1) + '&product_id=' + productID + '&action=ajaxSimilarServices&nonce=' + nonce;

        $( '.similar-services-load-more' ).html( '<span class="spinner-grow spinner-grow-sm me-2 mr-2" role="status" aria-hidden="true"></span>Loading...' );

        console.log( str );
        console.log( totalPages );

        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: str,
            success: function(data){
                var $data = $( data );

                console.log( $data );

                if( $data.length ){
                    $( '.services-list' ).append( $data );

                    if( page+1 >= totalPages ) {
                        $( '.similar-services-load-more' ).fadeOut( 'slow' );
                    } else {
                        $( '.similar-services-load-more' ).html( 'Load More <i class="fa fa-arrow-down" aria-hidden="true"></i>' );
                        page++;
                    }
                } else{
                    $( '.similar-services-load-more' ).fadeOut( 'slow' );
                }
            },
            error : function(jqXHR, textStatus, errorThrown) {
                // console.log(jqXHR + " :: " + textStatus + " :: " + errorThrown);
            }
    
        });
    } );
} );