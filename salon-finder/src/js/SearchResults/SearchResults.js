var PushState = require( "properjs-pushstate" ),
    pushstate = new PushState({

    });
(function() {
  jQuery(document).ready(function($) {
    var path = window.location.origin + window.location.pathname;
    var params = window.location.search.substring(1).split( '&' );
    var page = 1;

    if( params ) {
      params.forEach( function( value, index ) {
        if( value.includes( 'page' ) ) {
          page = parseInt( value.split( '=' )[1] );
        }
      } );
    }

    // if( page ) {
    //   let queryLink = path + '?page=';
    //   for( $i = 2; $i <= page; $i++ ) {
    //     $.ajax( queryLink+$i, {
    //       xhr: function() {
    //         return xhr;
    //       },
    //       success: function( data, status, jqXHR ) {
    //         let newHtml = $(data).find('#search-results').html();
    //         $( "#search-results" ).append( newHtml );
    //       }
    //     });
    //   }
    // }

    var checkLoadMoreBtn = (formObj) => {
      var totalPages = $(formObj).data('total-pages')
      var loadedPage = $(formObj).data('paged')
      var hide = totalPages<=loadedPage
      if(hide)
        $(formObj).find('.searchNextPage').hide();
      else 
        $(formObj).find('.searchNextPage').show();
    }

    checkLoadMoreBtn($('#search-result-body'))

    $( 'a.searchNextPage' ).html( 'Load More <i class="fa fa-arrow-down" aria-hidden="true"></i>' );
    $('a.searchNextPage').click(function(e) {
      var currentLink = this;
      var link = $(currentLink).attr('href');
      var xhr = new XMLHttpRequest();

      $( 'a.searchNextPage' ).html( '<span class="spinner-grow spinner-grow-sm me-2 mr-2" role="status" aria-hidden="true"></span>Loading...' );

      $.ajax( link, {
        xhr: function() {
          return xhr;
        },
        success: function( data, status, jqXHR ) {
          var href = $(data).find('.searchNextPage').attr('href');
          var newHtml = $(data).find('#search-results').html();
          var loadedPage = $(data).find('#search-result-body').data('paged')
          
          $( "#search-results" ).append( newHtml );
          
          pushstate.push( xhr.responseURL );

          if( $(data).find('.searchNextPage').length ) {
            $( currentLink ).attr('href', href);
            $( 'a.searchNextPage' ).html( 'Load More <i class="fa fa-arrow-down" aria-hidden="true"></i>' );
          }
          checkLoadMoreBtn($('#search-result-body'))
        }
      });
      e.preventDefault();
    })
    
  })
  
})()
