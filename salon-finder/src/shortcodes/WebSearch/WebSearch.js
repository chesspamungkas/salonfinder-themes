
jQuery(document).ready(function($) { 

  if(navigator.geolocation) {
    navigator.geolocation.getCurrentPosition((position) => {
      console.log(position)
    },(err) => {
      console.log(err)
    });
  }



  $('#location').multiselect({
    enableHTML: true,
    buttonTextAlignment: 'left',
    nonSelectedText: 'Select a location',
    onChange: function(option, checked, select) {
    }
  });

  $('#location-mobile').multiselect({
    buttonContainer: '<div id="mobile-checkbox-list-container"></div>',
    buttonClass: '',
    nonSelectedText: 'Select a location',
    templates: {
      button: '',
      popupContainer: '<div class="multiselect-container checkbox-list"></div>',
      // option: '<a class="multiselect-option text-dark text-decoration-none"></a>'
    },
    onChange: function(option, checked, select) {
    }
  }); 

  if (window.webresults) {
    $('#searchText').autocomplete({
      minLength: 3,
      source: function(req, responseFn) {
        var re = $.ui.autocomplete.escapeRegex(req.term);
        var matcher = new RegExp( re, "i" );
        var productCategories = []
        var merchants = []
        window.webresults.productCategories
          .filter(c=>matcher.test(c.name))
          .map(c=> {
            c.child.map(child=> {
              productCategories.push({
                main: c,
                child: child
              })
            })
          })
        window.webresults.productCategories
          .filter(c=>!matcher.test(c.name))
          .map(c=> {
            c.child
              .filter(child=>matcher.test(child.name))
              .map(child=> {
                productCategories.push({
                  main: c,
                  child: child
                })
              })
          })


        for(const merchant of window.webresults.merchant) {
          if(matcher.test(merchant.name)) {
            merchant.child.map(c=> {
              productCategories.push({
                main: merchant,
                child: c
              })
            })
          } else {
            merchant.child.filter((c) => matcher.test(c.name)).map(c=> {
              productCategories.push({
                main: merchant, 
                child: c
              })
            })
          }
        }
        responseFn(productCategories);
      },
      // html: true,      
      select: function(event, ui) {        
        var item = ui.item;
        var path = '/' + item.child.slug;        
        $('#searchText').val(item.child.name);
        $('#search_form').attr('data-searchtype', item.taxonomy=='product_cat'?'services':'salons');
        $('#search_form').attr('data-searchvalue', item.child.slug);
        var location = $('#location').val().join(',');
        $('#mainSearchWrapper').addClass('loadingPage')    
        var mobileCheck = $('#mobile-check').is(':visible');
        if(mobileCheck) {
          location = $('#location-mobile').val().join(',');
        }
        if(!location) {
          location = 'all'
        }
        setTimeout(() => {
          window.location.href = item.child.url + '/' +location;
        }, 100)
        return false;
      }
    })
    .autocomplete( "instance" )._renderItem = function( ul, item) {
      var cat;
      if (item.main.taxonomy=='product_cat')
        cat = "<span class='poppins-medium service-text'>SERVICE</span>";
      else if (item.main.taxonomy=='merchant')
        cat = "<span class='poppins-medium salon-text'>SALON</span>";
      var inner_html = '<p><a>' + cat+ '</span><span class="poppins-semibold">' + item.main.name + '</span> - <span class="poppins-light">' + item.child.name + '</span></a></p>';
      return $( "<li></li>" )
        // .data( "item.autocomplete", item )
      .append( inner_html )
      .appendTo( ul )
    };
  }

  $('#search_form').on('submit', function(e){    
    if($('#mainSearchWrapper').hasClass('loadingPage')) {
      e.preventDefault();
      return false;
    }
    var location = $('#location').val().join(',');
    var mobileCheck = $('#mobile-check').is(':visible');
    if(mobileCheck) {
      location = $('#location-mobile').val().join(',');
    }
    var referText = encodeURIComponent($('#searchText').val());
    if($(this).attr('data-searchtype') != 'search') {
      referText = $(this).attr('data-searchvalue')
    }
    if(!location) {
      location = 'all'
    }
    if(referText.length>=3) {
      window.location.href = $(this).attr('data-searchurl') + '/' + $(this).attr('data-searchtype') +'/'+ referText + '/' + location;
    } else {
      window.alert('Please enter at least 3 characters to start searching')
    }
    return false;
  })
  
  $("#searchText").on('change', function() {
    $('#search_form').attr('data-searchtype', 'search')
  })
  $("#searchText").on('click', function() {
    var mobileCheck = $('#mobile-check').is(':visible');
    if(mobileCheck)
      $('#body').addClass('showSearch')
  })
  $('#search-close').on('click', function() {
    $('#body').removeClass('showSearch')
  })
});

