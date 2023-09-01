<!DOCTYPE html>
<!--[if lt IE 9]><html <?php language_attributes(); ?> class="oldie"><![endif]-->
<!--[if (gte IE 9) | !(IE)]><!-->
<html <?php language_attributes(); ?> class="modern">
<!--<![endif]-->

<head>
  <title><?php wp_title(''); ?></title>
  <meta charset="<?php bloginfo('charset'); ?>" />
  <meta name="google-site-verification" content="fiSWk21d7qwA3ASsMSd6P64m2-m1uKg80B-QE8dfrb8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="verification" content="84d2a2a3d059c72be9d0e1b932719d6d" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <!-- Favicon -->
  <link rel="apple-touch-icon-precomposed" sizes="57x57" href="https://yourdomain.com/assets/apple-touch-icon-57x57.png" />
  <link rel="apple-touch-icon-precomposed" sizes="114x114" href="https://yourdomain.com/assets/apple-touch-icon-114x114.png" />
  <link rel="apple-touch-icon-precomposed" sizes="72x72" href="https://yourdomain.com/assets/apple-touch-icon-72x72.png" />
  <link rel="apple-touch-icon-precomposed" sizes="144x144" href="https://yourdomain.com/assets/apple-touch-icon-144x144.png" />
  <link rel="apple-touch-icon-precomposed" sizes="60x60" href="https://yourdomain.com/assets/apple-touch-icon-60x60.png" />
  <link rel="apple-touch-icon-precomposed" sizes="120x120" href="https://yourdomain.com/assets/apple-touch-icon-120x120.png" />
  <link rel="apple-touch-icon-precomposed" sizes="76x76" href="https://yourdomain.com/assets/apple-touch-icon-76x76.png" />
  <link rel="apple-touch-icon-precomposed" sizes="152x152" href="https://yourdomain.com/assets/apple-touch-icon-152x152.png" />
  <link rel="icon" type="image/png" href="https://yourdomain.com/assets/favicon-196x196.png" sizes="196x196" />
  <link rel="icon" type="image/png" href="https://yourdomain.com/assets/favicon-96x96.png" sizes="96x96" />
  <link rel="icon" type="image/png" href="https://yourdomain.com/assets/favicon-32x32.png" sizes="32x32" />
  <link rel="icon" type="image/png" href="https://yourdomain.com/assets/favicon-16x16.png" sizes="16x16" />
  <link rel="icon" type="image/png" href="https://yourdomain.com/assets/favicon-128.png" sizes="128x128" />
  <meta name="application-name" content="Daily Vanity Salon Finder" />
  <meta name="msapplication-TileColor" content="#ef497f" />
  <meta name="msapplication-TileImage" content="https://yourdomain.com/assets/mstile-144x144.png" />
  <meta name="msapplication-square70x70logo" content="https://yourdomain.com/assets/mstile-70x70.png" />
  <meta name="msapplication-square150x150logo" content="https://yourdomain.com/assets/mstile-150x150.png" />
  <meta name="msapplication-wide310x150logo" content="https://yourdomain.com/assets/mstile-310x150.png" />
  <meta name="msapplication-square310x310logo" content="https://yourdomain.com/assets/mstile-310x310.png" />

  <!-- Chrome, Firefox OS and Opera -->
  <meta name="theme-color" content="#ef497f">
  <!-- Windows Phone -->
  <meta name="msapplication-navbutton-color" content="#ef497f">
  <!-- iOS Safari -->

  <!-- endinject -->
  <?php wp_head(); ?>

  <!-- head:js -->
  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-scrollTo/2.1.2/jquery.scrollTo.min.js"></script>

  <!-- endinject -->
  <script type="text/javascript">
    var ajaxurl = "<?= admin_url('admin-ajax.php') ?>";
    var siteurl = "<?= site_url(); ?>"
  </script>

  <!-- Google Tag Manager -->
  <script>
    (function(w, d, s, l, i) {
      w[l] = w[l] || [];
      w[l].push({
        'gtm.start': new Date().getTime(),
        event: 'gtm.js'
      });
      var f = d.getElementsByTagName(s)[0],
        j = d.createElement(s),
        dl = l != 'dataLayer' ? '&l=' + l : '';
      j.async = true;
      j.src =
        'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
      f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', '');
  </script>
  <!-- End Google Tag Manager -->
</head>

<body <?php body_class(); ?>>
  <!-- Google Tag Manager (noscript) -->
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
  <!-- End Google Tag Manager (noscript) -->
  <?php do_action('after-body-tag'); ?>
  <div id="page-container">
    <?php echo do_shortcode('[top-header-bar]'); ?>
    <?php do_action('top-header-bar-after'); ?>
    <div id="body" class="container-fluid no-padding">
      <?php if (is_home() || is_front_page()) : ?>
        <div class="home-firstsec fixed-top">
          <div class="row no-gutters">
            <div class="container">
              <div class="row no-gutters">
                <p class="poppins-medium title-search">Discover beauty treats near you!</p>
              </div>
            </div>
          </div>
        </div>
        <div class="search-header srchpage">
          <?php echo do_shortcode('[web-search]'); ?>
        </div>
      <?php else : ?>
        <div class="search-header srchpage">
          <?php echo do_shortcode('[web-search]'); ?>
        </div>
      <?php endif; ?>