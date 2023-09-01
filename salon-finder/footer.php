        </div><!--  end body -->
        <?php do_action('body_div_after'); ?>
        <div id="footer">
          <?php get_sidebar('footer-cols'); ?>
        </div>
        </div>
        <?php
        wp_footer();
        ?>
        <script>
          var wpcf7Elm = document.querySelector('.wpcf7');

          if (wpcf7Elm != null) {
            wpcf7Elm.addEventListener('wpcf7mailsent', function(event) {
              document.getElementById("myModal").style.display = "block";
            });
          }
        </script>
        <script>
          jQuery(document).ready(function() {

            //fix for admin bar
            if (jQuery('#wpadminbar')[0])
              jQuery('body.et_fixed_nav.et_secondary_nav_enabled #main-header').css('top', '32px !important');
          });

          function close_Function() {
            document.getElementById("myModal").style.display = "none";
          };
        </script>
        <div class="modal fade" id="myModal" role="dialog">
          <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                <button id="#modalwindow" type="button" class="close" data-dismiss="modal " onclick="close_Function();">&times;</button>
                <h4 class="modal-title"></h4>
              </div>
              <div class="modal-body">
                <p>Thank you for subscribing!</p>
              </div>
              <div class="modal-footer"> </div>
            </div>
          </div>
        </div>
        <script>
        </script>
        <script>
          jQuery(document).ready(function($) {

            $(document).on("click", ".show-hide-btn", function() {
              if ($(".ser_block_detail").hasClass("in")) {
                $(".ser_block_detail").removeClass("in")
              }
              if ($(this).hasClass("active")) {
                $(this).removeClass("active");
                $(this).text("Show Details");
              } else {
                $(this).addClass("active");
                $(this).text("Hide Details");
              }
            });
          });
        </script>
        <script>
          jQuery(document).ready(function() {
            var now = new Date();
            var year_ago = new Date(now.getFullYear() - 90, now.getMonth(), now.getDay());


            jQuery('.date_picker,#registration_field_5').datepicker({
              dateFormat: 'd M yy',
              changeMonth: true,
              changeYear: true,
              setDate: '01 Jan 1995',
              yearRange: "-90:-13",
              defaultDate: new Date(1985, 00, 01)
            });
            jQuery('#main-content').on('click', 'a.load-more-outlet', function(e) {
              e.preventDefault();

              var postCount = jQuery(this).data("paged");
              var dataService = jQuery(this).data("service");
              var postcode = jQuery(this).data("data-postal");
              var catName = jQuery(this).data("search");
              var orderby = jQuery(this).data("orderby");
              var result = jQuery(this).data("result");
              var promotion = jQuery(this).data("promotion");
              var repeatDiv = jQuery(this).data("repeat");
              var keyword = jQuery(this).data("keyword");
              var elem = jQuery(this);
              var nextPage = postCount + 1;

              jQuery('.load_outlets').show();


              jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'post',
                dataType: 'JSON',
                data: {
                  action: 'more_outlet_ajax',
                  nextPage: nextPage,
                  repeatDiv: repeatDiv,
                  orderby: orderby,
                  dataService: dataService,
                  result: result,
                  promotion: promotion,
                  postcode: postcode,
                  keyword: keyword,
                  catName: catName
                },
                success: function(response) {

                  if (response.success == true) {
                    elem.data("paged", nextPage);
                    console.log(response.load_more);
                    jQuery(".search-result").append(response.result);
                    jQuery(".grid_services").append(response.result);
                    jQuery('.load_outlets').hide();
                    if (response.load_more == false) jQuery('.load-more-outlet').hide();
                  } else {
                    console.log("failure");
                  }
                }
              });
            });
          });
        </script>
        <script>
          jQuery(document).ready(function($) {

            var hash = window.location.hash;
            var check_url = "<?= site_url(); ?>/profile/page/"
            var or_check_url = "<?= site_url(); ?>/profile/page/#"
            var pairs = location.search.slice(1).split('&');
            if (pairs == "") {
              if (hash == "") {
                if (window.location.href.indexOf(check_url) > -1) {
                  $('a[href="#purchases"]').tab('show');
                } else {
                  $('a[href="#profile"]').tab('show');
                }
              } else {
                if (hash == "#purchases") {
                  $('a[href="#purchases"]').tab('show');
                }
              }
            }

            $('.et_pb_tabs_controls a').click(function(e) {
              var hash = window.location.hash;
              $('a[href="' + this.hash + '"]').tab('show');
            });
          });

          jQuery(document).ready(function($) {

            $('#wc_billing_field_2495').datepicker({
              dateFormat: 'dd/mm/yy',
              changeMonth: true,
              changeYear: true,
              setDate: '01/01/1995',
              yearRange: "-90:-13",
              defaultDate: new Date(1985, 00, 01)
            });


            $('#wc_billing_field_2495').prop('readonly', true);



            $('#validate_order').click(function(e) {
              e.preventDefault();

              var f_name = $('#billing_first_name').val();
              var l_name = $('#billing_last_name_field').val();
              var email = $('#billing_email_field').val();
              var phone = $('#billing_phone_field').val();
              // var gender = $('#wc_billing_field_7149').val();
              // var dob = $('#wc_billing_field_2495').val();

              if (f_name == '' || l_name == '' || email == '' || phone == '') {
                swal("Opps!", "All fields are required!", "error");
                return false;
              }
              if (!validateEmail(email)) {
                swal("Opps!", "Please type valid email!", "error");
                return false;
              }
              $("#place_order").click();

            });

            function validateEmail($email) {
              var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
              return emailReg.test($email);
            }


          });



          jQuery(document).ready(function($) {
            $('.yes-i-want-btn').click(function() {
              $('#main-header').css('z-index', '0');
            });

            $('.closeLightBtn').click(function() {
              $('#main-header').css('z-index', '99999999999999');
            });


          });
        </script>
        <style type="text/css">
          #search_form.loading {
            position: relative;
            display: block !important;
          }

          #search_form.loading:after {
            content: "";
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            display: block;
            background: url("<?php echo get_stylesheet_directory_uri(); ?>/images/loading.gif") no-repeat;
            z-index: 11;
            cursor: wait;
            background-position: center;
            background-size: 100px;
          }
        </style>
        <script>
          (function(w, d, t, u, n, a, m) {
            w['MauticTrackingObject'] = n;
            w[n] = w[n] || function() {
                (w[n].q = w[n].q || []).push(arguments)
              }, a = d.createElement(t),
              m = d.getElementsByTagName(t)[0];
            a.async = 1;
            a.src = u;
            m.parentNode.insertBefore(a, m)
          })(window, document, 'script', 'https://yourdomain.com/mtc.js', 'mt');

          mt('send', 'pageview');
        </script>
        </body>

        </html>