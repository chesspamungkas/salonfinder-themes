<?php

// require get_template_directory() . '/src/core/ThemeBase.php';
// include the Composer autoload file
require_once __DIR__ . '/vendor/autoload.php';
function remove_dashboard_widgets()
{
	// remove WooCommerce Dashboard Status
	remove_meta_box('woocommerce_dashboard_status', 'dashboard', 'normal');
}
add_action('wp_user_dashboard_setup', 'remove_dashboard_widgets', 20);
add_action('wp_dashboard_setup', 'remove_dashboard_widgets', 20);

date_default_timezone_set('Asia/Singapore');

DV\DailyVanity::init();
add_action('widgets_init', 'init_sidebar');

function init_sidebar()
{
	register_sidebar(DV\core\sidebars\FooterOne::init());
	register_sidebar(DV\core\sidebars\FooterTwo::init());
	register_sidebar(DV\core\sidebars\FooterThree::init());
	register_sidebar(DV\core\sidebars\FooterFour::init());
}

DV\core\Constants::Define('BASE_PATH', home_url());
DV\core\Constants::Define('S3_PATH', 'https://yourdomain.com');
DV\core\Constants::Define('DV_S3_PATH', 'https://yourdomain.com');
DV\core\Constants::Define('FB_APP_ID', '');
DV\core\Constants::Define('FB_PAGE_NAME', 'dailyvanity');
DV\core\Constants::Define('IG_USERNAME', 'dailyvanity');
DV\core\Constants::Define('TG_LINK', '');
DV\core\Constants::Define('TW_LINK', '');
DV\core\Constants::Define('YTB_LINK', '');

DV\core\Constants::Define('SALON_BASE_PATH', '/salons');
DV\core\Constants::Define('SERVICES_BASE_PATH', '/services');
define('reCAPTCHA_SITE_KEY', '');
define('reCAPTCHA_SECRET_KEY', '');

$useragent = $_SERVER['HTTP_USER_AGENT'];
$isMobile = false;
$isIOS = false;

if (preg_match('/(Mobile|Android|Tablet|GoBrowser|[0-9]x[0-9]*|uZardWeb\/|Mini|Doris\/|Skyfire\/|iPhone|Fennec\/|Maemo|Iris\/|CLDC\-|Mobi\/)/uis', $useragent)) {
	$isMobile = true;
	$device = 'mobile';
	$isIOS = false;

	if (stripos($useragent, 'iphone') !== false || stripos($useragent, 'ipad') !== false) {
		// $isIOS = true;
		$isIOS = true;
	}
}

if ($isMobile) {
	if ($isIOS) {
		$fb = 'fb://profile/' . FB_APP_ID;
	} else {
		$fb = 'fb://page/' . FB_APP_ID;
	}
	$ig = 'instagram://user?username=' . IG_USERNAME;
} else {
	$fb = 'https://facebook.com/' . FB_PAGE_NAME;
	$ig = 'https://instagram.com/' . IG_USERNAME;
}

DV\core\Constants::Define('FB_LINK', $fb);
DV\core\Constants::Define('IG_LINK', $ig);
DV\core\Constants::Define('BASE_URL', home_url());

remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

function customtheme_add_woocommerce_support()
{
	add_theme_support('woocommerce');
}
add_action('after_setup_theme', 'customtheme_add_woocommerce_support');

DV\core\ThemeBase::addSupport('post-thumbnails');

DV\core\ThemeBase::addImageSize('full-width', 1600);

DV\core\ThemeBase::addImageSize('dvsf-promotion', 288, 160, true);
DV\core\ThemeBase::addImageSize('dvsf-card', 450, 300, true);
DV\core\ThemeBase::addImageSize('dvsf-stardard', 900, 600, true);
DV\core\ThemeBase::addImageSize('cat-navbar-image', 262, 82, true);
DV\core\ThemeBase::addImageSize('full-cat-navbar-image', 400, 152, true);
DV\core\ThemeBase::addImageSize('woocommerce_cart_item_thumbnail', 169, 88, true);
DV\core\ThemeBase::addImageSize('woocommerce_random_services', 324, 168, true);

// DV\core\ThemeBase::AddStyle( 'index-style', get_template_directory_uri() . '/src/.dist/index.ts.css' );
// DV\core\ThemeBase::AddStyle('child-index-style', get_template_directory_uri() . '/src/.dist/index.ts.css');
// DV\core\ThemeBase::AddScript('child-script', get_template_directory_uri() . '/src/.dist/index.ts.js');
DV\core\ThemeBase::AddStyle('slick-css', get_template_directory_uri() . '/src/css/slick.css');
DV\core\ThemeBase::AddStyle('font-css', get_template_directory_uri() . '/src/css/font.css');
DV\core\ThemeBase::AddScript('slick-script', get_template_directory_uri() . '/src/js/slick.min.js');
DV\core\ThemeBase::AddScript('custom-script', get_template_directory_uri() . '/src/js/custom.js');

function wpdocs_main_scripts_styles()
{
	wp_enqueue_style('fontawesome-css', get_template_directory_uri() . '/src/css/fontawesome/all.min.css', array(), DEPLOY_VERSION);
	wp_enqueue_style('multiselect-style', get_template_directory_uri() . '/src/css/bootstrap-multiselect.min.css?v=' . DEPLOY_VERSION);
	wp_enqueue_script('jqueryui', 'https://code.jquery.com/ui/1.12.1/jquery-ui.js', 'jquery', true);
	wp_enqueue_script('multiselect', get_template_directory_uri() . '/src/js/bootstrap-multiselect.min.js', array(), DEPLOY_VERSION);
	wp_enqueue_script('autocompletehtml', get_template_directory_uri() . '/src/js/jquery.ui.autocomplete.html.js', array('jquery'), DEPLOY_VERSION);
}
add_action('wp_enqueue_scripts', 'wpdocs_main_scripts_styles');

// Add to your init function
add_filter('get_search_form', 'my_search_form');

function my_search_form($text)
{
	$text = str_replace('value="Search"', 'value=""', $text);
	return $text;
}

/******** register menu location start ********/

function wpb_top_main_menu()
{
	register_nav_menu('top-main-menu', __('Top Main Menu'));
}
add_action('init', 'wpb_top_main_menu');

function wpb_top_main_menu_dv_link()
{
	register_nav_menu('top-main-menu-dv-link', __('Top Main Menu DV Link'));
}
add_action('init', 'wpb_top_main_menu_dv_link');

function wpb_top_header_bk_bar_menu()
{
	register_nav_menu('top-header-bk-bar-menu', __('Top Header Black Bar Menu'));
}
add_action('init', 'wpb_top_header_bk_bar_menu');

function wpb_mobile_docking_menu()
{
	register_nav_menu('mobile-docking-menu', __('Mobile Docking Menu'));
}
add_action('init', 'wpb_mobile_docking_menu');

function wpb_category_menu()
{
	register_nav_menu('category-navbar-menu', __('Category NavBar Menu'));
}
add_action('init', 'wpb_category_menu');

/******** register menu location end ********/

function top_header_bar_before_callback()
{
	$content = '';

	$locations = get_nav_menu_locations();
	$menu = wp_get_nav_menu_object($locations['top-header-bk-bar-menu']);
	$menu_items = wp_get_nav_menu_items($menu->term_id);

	// print_r( wp_get_nav_menu_object( $locations[ 'top-header-bk-bar-menu' ] ) );

	$content .= "<div class='container-fluid top-header-bk-bar'>\n";
	$content .= "\t<div class='row no-gutters'>\n";
	$content .= "\t\t<div class='col'>\n";
	$content .= "\t\t\t<div class='container'>\n";
	$content .= "\t\t\t\t<div class='row no-gutters'>\n";
	$content .= "\t\t\t\t\t<div class='col'>\n";

	if ($menu_items) {
		$content .= "\t\t\t\t\t\t<ul class='nav justify-content-end align-items-center'>\n";

		foreach ((array)$menu_items as $key => $menu_item) {
			$title = $menu_item->title;
			$url = $menu_item->url;

			if ($title == 'My Account') {
				if (is_user_logged_in()) {
					$content .= "\t\t\t\t\t\t\t" . '<li class="nav-item dropdown">' . "\n";
					$content .= "\t\t\t\t\t\t\t\t" . '<a class="btn dropdown-toggle" href="#" role="button" id="my-account-dropdown"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' . $title . '</a>' . "\n";
					$content .= "\t\t\t\t\t\t\t\t" . '<ul class="dropdown-menu my-account-dropdown-menu" aria-labelledby="my-account-dropdown">' . "\n";
					$content .= "\t\t\t\t\t\t\t\t\t" . '<li><a href="' . site_url('profile') . '">Manage Account</a></li>' . "\n";
					$content .= "\t\t\t\t\t\t\t\t\t" . '<li><a href="' . wp_logout_url(home_url()) . '">Sign Out</a></li>' . "\n";
					$content .= "\t\t\t\t\t\t\t\t" . '</ul>' . "\n";
				} else {
					$content .= "\t\t\t\t\t\t\t" . '<li class="nav-item">' . "\n";
					$content .= "\t\t\t\t\t\t\t\t" . '<a href="' . site_url('login') . '" class="nav-link ' . strtolower(str_replace(' ', '-', $title)) . '" rel="noopener noreferrer">' . $title . '</a>' . "\n";
				}
			} else {
				$content .= "\t\t\t\t\t\t\t" . '<li class="nav-item">' . "\n";
				$content .= "\t\t\t\t\t\t\t\t" . '<a href="' . $url . '" class="nav-link ' . strtolower(str_replace(' ', '-', $title)) . '" target="_blank" rel="noopener noreferrer">' . $title . '</a>' . "\n";
			}
			$content .= "\t\t\t\t\t\t\t" . '</li>' . "\n";
		}

		$content .= "\t\t\t\t\t\t</ul>\n";
	}

	$content .= "\t\t\t\t\t</div>\n";
	$content .= "\t\t\t\t</div>\n";
	$content .= "\t\t\t</div>\n";
	$content .= "\t\t</div>\n";
	$content .= "\t</div>\n";
	$content .= "</div>\n";

	echo $content;
}

add_action('top_header_bar_before', 'top_header_bar_before_callback', 20);

// top_header_social_section
function top_header_social_section($html)
{
	$html = '<div class="col-xs-12 col-sm-12 col-md-2 pt-3">';
	if (is_user_logged_in()) :
		$html .= '<p id="myaccount-title" class="poppins-semibold"><a href="' . home_url() . '/profile" target="_blank" rel="noopener noreferrer"><i class="fas fa-user mr-3"></i> MY ACCOUNT</a></p>';
	else :
		$html .= '<p id="myaccount-title" class="poppins-semibold"><a href="' . home_url() . '/login" target="_blank" rel="noopener noreferrer"><i class="fas fa-user mr-3"></i> MY ACCOUNT</a></p>';
	endif;

	$html .= '<p id="follow-us-title" class="poppins-semibold">FOLLOW US</p>'
		. '<ul class="justify-content-center" id="header-social-icon">'
		. '<li class="social-icon"><a href="' . FB_LINK . '" target="_blank" rel="noopener noreferrer"><i class="fab fa-facebook-f"></i></a></li>'
		. '<li class="social-icon"><a href="' . IG_LINK . '" target="_blank" rel="noopener noreferrer"><i class="fab fa-instagram"></i></a></li>'
		. '<li class="social-icon"><a href="' . TG_LINK . '" target="_blank" rel="noopener noreferrer"><i class="fab fa-telegram-plane"></i></a></li>'
		. '<li class="social-icon"><a href="' . TW_LINK . '" target="_blank" rel="noopener noreferrer"><i class="fab fa-twitter"></i></a></li>'
		. '<li class="social-icon"><a href="' . YTB_LINK . '" target="_blank" rel="noopener noreferrer"><i class="fab fa-youtube"></i></a></li>'
		. '</ul>'
		. '</div>';

	echo $html;
}
add_action('top_header_social_section', 'top_header_social_section');

function remove_admin_bar()
{
	// if (!current_user_can('administrator') && !is_admin()) {
	show_admin_bar(false);
	// }
}
add_action('after_setup_theme', 'remove_admin_bar');

global $woocommerce;
if (version_compare($woocommerce->version, '2.3', '<')) {
	// WC 2.3 -
	add_filter('add_to_cart_fragments', 'woocommerceframework_header_add_to_cart_fragment');
} else {
	// WC 2.3 +
	add_filter('woocommerce_add_to_cart_fragments', 'woocommerceframework_header_add_to_cart_fragment');
}

function woocommerceframework_header_add_to_cart_fragment($fragments)
{
	global $woocommerce;
?>
	<div class="header-cart-info">
		<a href="<?php echo get_permalink(wc_get_page_id('cart')); ?>" title="<?php _e('View your shopping cart', 'woothemes'); ?>" class="et-cart-info header-total-car-count">
			<span class="cart-count"><span class="count cart_totals"><?php echo WC()->cart->get_cart_contents_count(); ?> </span></span>
		</a>
	</div>
	<?php

	$fragments['.header-cart-info'] = ob_get_clean();

	return $fragments;
}

//add docking menu
function footer_docking_menu()
{
	if (is_product()) {
		global $product;
		$menu_name = 'product-mobile-docking-menu';
		$menu_list = '<div id="docking-menu">' . "\n";
		$menu_list .= '<ul class="nav justify-content-center">' . "\n";
		// $menu_list .= '<li>Empty</li>';

		if ($menu_items = wp_get_nav_menu_items($menu_name)) {
			$count = 0;
			$submenu = false;
			$parent_id = 0;
			$previous_item_has_submenu = false;

			foreach ((array)$menu_items as $key => $menu_item) {
				$title = $menu_item->title;
				$url = $menu_item->url;
				$icon = get_field('icon', $menu_item->ID);

				// check if it's a top-level item
				if ($menu_item->menu_item_parent == 0) {
					$parent_id = $menu_item->ID;
					// write the item but DON'T close the A or LI until we know if it has children!

					// if ($title=='Discover') {
					// 	$alt = "Discover more beauty articles";
					// } elseif ($title=='Salon Finder') {
					// 	$alt = "Salon Finder - Beauty Services";
					// } else {
					$alt = $title;
					// }

					if ($title == 'Deals') {
						$menu_list .= "\t" . '<li class="nav-item"><a href="' . $url . '" class="nav-link parent-nav-link ' . strtolower(str_replace(' ', '-', $title)) . '-btn" id="menu-' . $parent_id . '" target="_blank" title="' . $title . '" rel="noopener noreferrer"><i class="' . $icon . '" id="deals-icon"></i><br/>' . $title . '</a><br/>' . "\n";
					} elseif ($title == 'How To Redeem?') {
						$menu_list .= "\t" . '<li class="nav-item"><a href="' . $url . '" class="nav-link parent-nav-link ' . strtolower(str_replace(' ', '-', str_replace('?', '', $title))) . '-btn" id="menu-' . $parent_id . '" title="' . $title . '">' . str_replace('To Redeem', 'To<br/>Redeem', $title) . '</a>' . "\n";
					} elseif ($title == 'Buy Voucher') {
						if ($product->is_on_sale()) {
							$price = 'S$ ' . number_format($product->getCheapestSalesPrice(), 2);
						} else {
							$price = 'S$ ' . number_format($product->getCheapestRegularPrice(), 2);
						}

						// $menu_list .= "\t". '<li class="nav-item"><a href="'. $url .'" class="nav-link parent-nav-link ' . strtolower( str_replace( ' ', '-', $title ) ) . '-btn" id="menu-' . $parent_id . '" title="' . $title . '">' . $title . '<br/><span class="docking-menu-price">' . $price . '</span></a>' . "\n";

						$menu_list .= "\t" . '<li class="nav-item">' . "\n";

						$menu_list .= "\t\t" . '<form class="cart ng-pristine ng-valid variations_form" action="" method="post" enctype="multipart/form-data">' . "\n";
						$menu_list .= "\t\t\t" . '<button class="single_add_to_cart_button alt ' . strtolower(str_replace(' ', '-', $title)) . '-btn">Buy Voucher<br/><span class="docking-menu-price">' . $price . '</span></button>' . "\n";
						$menu_list .= "\t\t\t" . '<input type="hidden" name="add-to-cart" value="' . absint($product->get_id()) . '" />' . "\n";
						$menu_list .= "\t\t\t" . '<input type="hidden" name="product_id" value="' . absint($product->get_id()) . '" />' . "\n";
						$menu_list .= "\t\t\t" . '<input type="hidden" name="variation_id" class="variation_id" value="' . $product->getCheapestVariantID() . '" />' . "\n";
						$menu_list .= "\t\t" . '</form>' . "\n";
					} elseif ($title == 'Account') {
						if (is_user_logged_in()) {
							$menu_list .= "\t" . '<li class="nav-item dropup"><a href="' . $url . '" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle parent-nav-link ' . strtolower(str_replace(' ', '-', $title)) . '-btn" id="menu-' . $parent_id . '" title="' . $title . '"><img alt="' . $alt . '" src="' . $icon . '?v=' . DEPLOY_VERSION . '" class="parent-icon" id="' . strtolower(str_replace(' ', '-', $title)) . '-icon" /><br/>' . $title . '</a><br/>' . "\n";
							$menu_list .= "\t" . '<div class="dropdown-menu" aria-labelledby="menu-' . $parent_id . '">' . "\n";
							$menu_list .= "\t" . '<a class="dropdown-item" href="' . site_url('profile') . '" target="_blank">Manage Account</a>' . "\n";
							$menu_list .= "\t" . '<a class="dropdown-item" href="' . wp_logout_url(home_url()) . '">Sign Out</a>' . "\n";
						} else {
							$menu_list .= "\t" . '<li class="nav-item"><a href="' . site_url('login') . '" class="nav-link parent-nav-link ' . strtolower(str_replace(' ', '-', $title)) . '-btn" id="menu-' . $parent_id . '" title="' . $title . '"><img alt="' . $alt . '" src="' . $icon . '?v=' . DEPLOY_VERSION . '" class="parent-icon" id="' . strtolower(str_replace(' ', '-', $title)) . '-icon" /><br/>' . $title . '</a><br/>' . "\n";
						}

						$menu_list .= "\t" . '</div>' . "\n";
					} else {
						$menu_list .= "\t" . '<li class="nav-item dropdown"><a href="' . $url . '" class="nav-link parent-nav-link ' . strtolower(str_replace(' ', '-', $title)) . '-btn" id="menu-' . $parent_id . '" target="_blank" title="' . $title . '" rel="noopener noreferrer"><img alt="' . $alt . '" src="' . $icon . '?v=' . DEPLOY_VERSION . '" class="parent-icon" id="' . strtolower(str_replace(' ', '-', $title)) . '-icon" /><br/>' . $title . "\n";

						// if( isset( $_COOKIE['woocomemrce_total_cart_item'] ) ) {
						// 	$cartTotal = $_COOKIE['woocomemrce_total_cart_item'];
						// } else {
						// 	$cartTotal = 0;
						// }

						$cartTotal = WC()->cart->get_cart_contents_count();

						if (strtolower($menu_item->title) == 'cart') {
							$menu_list .= '<span class="cart-totals count">' . $cartTotal . '</span></a>';
						} else {
							$menu_list .= '</a>';
						}
					}
				}

				// if this item has a (nonzero) parent ID, it's a second-level (child) item
				else {
					if (!$submenu) { // first item
						// add the dropdown arrow to the parent
						// $menu_list .= '<span class="arrow-down"></span></a>' . "\n";
						// start the child list
						$submenu = true;
						$previous_item_has_submenu = true;
						$menu_list .= "\t\t" . '<ul id="submenu-' . $menu_item->menu_item_parent . '" class="submenu">' . "\n";
					}

					$menu_list .= "\t\t\t" . '<li>';

					$menu_list .= '<a href="' . $url . '" target="_blank" title="' . $title . '" rel="noopener noreferrer"><i class="' . $icon . '"></i></a>';

					$menu_list .= '</li>' . "\n";

					// if it's the last child, close the submenu code
					if ($menu_items[$count + 1]->menu_item_parent != $parent_id && $submenu) {
						$menu_list .= "\t\t" . '</ul></li>' . "\n";
						$submenu = false;
					}
				}

				// close the parent (top-level) item
				if (empty($menu_items[$count + 1]) || $menu_items[$count + 1]->menu_item_parent != $parent_id) {
					if ($previous_item_has_submenu) {
						// the a link and list item were already closed
						$previous_item_has_submenu = false; //reset
					} else {
						// close a link and list item
						$menu_list .= "\t" . '</a></li>' . "\n";
					}
				}

				$count++;
			}
		} else {
			$menu_list .= '<!-- no list defined -->';
		}
		$menu_list .= "\t" . '</ul>' . "\n";
		$menu_list .= "\t" . '</div>' . "\n";
	} elseif (is_cart()) {
		$menu_name = 'mobile-docking-menu'; // specify custom menu slug
		// $menu_list = '<nav class="navbar navbar-expand-lg navbar-dark bg-dark" id="docking-menu">' ."\n";
		// $menu_list .= '<div class="container-fluid justify-content-center">' ."\n";
		$menu_list = '<div id="docking-menu">' . "\n";
		$menu_list .= '<ul class="nav justify-content-center">' . "\n";

		if ($menu_items = wp_get_nav_menu_items($menu_name)) {
			$count = 0;
			$submenu = false;
			$parent_id = 0;
			$previous_item_has_submenu = false;

			foreach ((array)$menu_items as $key => $menu_item) {
				$title = $menu_item->title;
				$url = $menu_item->url;
				$icon = get_field('icon', $menu_item->ID);

				switch (strtolower($menu_item->title)) {
					case 'facebook':
						$url = FB_LINK;
						break;
					case 'instagram':
						$url = IG_LINK;
						break;
				}

				// check if it's a top-level item
				if ($menu_item->menu_item_parent == 0) {
					$parent_id = $menu_item->ID;
					// write the item but DON'T close the A or LI until we know if it has children!

					if ($title == 'Discover') {
						$alt = "Discover more beauty articles";
					} elseif ($title == 'Salon Finder') {
						$alt = "Salon Finder - Beauty Services";
					} else {
						$alt = $title;
					}

					// if($title=='Nearby') {
					// 	$menu_list .= "\t". '<li class="nav-item"><a href="'. $url .'" class="nav-link parent-nav-link ' . strtolower( str_replace( ' ', '-', $title ) ) . '-btn" id="menu-' . $parent_id . '" target="_blank" title="' . $title . '" rel="noopener noreferrer"><i class="'.$icon.'" id="nearby-icon"></i><br/>' . $title . '</a><br/>' . "\n";
					if ($title == 'Deals') {
						$menu_list .= "\t" . '<li class="nav-item"><a href="' . $url . '" class="nav-link parent-nav-link ' . strtolower(str_replace(' ', '-', $title)) . '-btn" id="menu-' . $parent_id . '" target="_blank" title="' . $title . '" rel="noopener noreferrer"><i class="' . $icon . '" id="deals-icon"></i><br/>' . $title . '</a><br/>' . "\n";
					} elseif ($title == 'Account') {
						if (is_user_logged_in()) {
							$menu_list .= "\t" . '<li class="nav-item dropup"><a href="' . $url . '" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle parent-nav-link ' . strtolower(str_replace(' ', '-', $title)) . '-btn" id="menu-' . $parent_id . '" title="' . $title . '"><img alt="' . $alt . '" src="' . $icon . '?v=' . DEPLOY_VERSION . '" class="parent-icon" id="' . strtolower(str_replace(' ', '-', $title)) . '-icon" /><br/>' . $title . '</a><br/>' . "\n";
							$menu_list .= "\t" . '<div class="dropdown-menu" aria-labelledby="menu-' . $parent_id . '">' . "\n";
							$menu_list .= "\t" . '<a class="dropdown-item" href="' . site_url('profile') . '" target="_blank">Manage Account</a>' . "\n";
							$menu_list .= "\t" . '<a class="dropdown-item" href="' . wp_logout_url(home_url()) . '">Sign Out</a>' . "\n";
						} else {
							$menu_list .= "\t" . '<li class="nav-item"><a href="' . site_url('login') . '" class="nav-link parent-nav-link ' . strtolower(str_replace(' ', '-', $title)) . '-btn" id="menu-' . $parent_id . '" title="' . $title . '"><img alt="' . $alt . '" src="' . $icon . '?v=' . DEPLOY_VERSION . '" class="parent-icon" id="' . strtolower(str_replace(' ', '-', $title)) . '-icon" /><br/>' . $title . '</a><br/>' . "\n";
						}

						$menu_list .= "\t" . '</div>' . "\n";
					} elseif (strtolower($menu_item->title) == 'cart') {
						$menu_list .= "\t" . '<li class="nav-item dropdown"><a href="' . $url . '" class="nav-link parent-nav-link ' . strtolower(str_replace(' ', '-', $title)) . '-btn" id="menu-' . $parent_id . '" target="_blank" title="' . $title . '" rel="noopener noreferrer" style="background-color: #22201B;"><img alt="' . $alt . '" src="' . $icon . '?v=' . DEPLOY_VERSION . '" class="parent-icon" id="' . strtolower(str_replace(' ', '-', $title)) . '-icon" /><br/>' . $title . "\n";

						// if( isset( $_COOKIE['woocomemrce_total_cart_item'] ) ) {
						// 	$cartTotal = $_COOKIE['woocomemrce_total_cart_item'];
						// } else {
						// 	$cartTotal = 0;
						// }

						$cartTotal = WC()->cart->get_cart_contents_count();

						$menu_list .= '<span class="cart-totals count">' . $cartTotal . '</span></a>';
					} else {
						$menu_list .= "\t" . '<li class="nav-item dropdown"><a href="' . $url . '" class="nav-link parent-nav-link ' . strtolower(str_replace(' ', '-', $title)) . '-btn" id="menu-' . $parent_id . '" target="_blank" title="' . $title . '" rel="noopener noreferrer" style="background-color: unset;"><img alt="' . $alt . '" src="' . $icon . '?v=' . DEPLOY_VERSION . '" class="parent-icon" id="' . strtolower(str_replace(' ', '-', $title)) . '-icon" /><br/>' . $title . "\n";
						$menu_list .= '</a>';
					}
				}

				// if this item has a (nonzero) parent ID, it's a second-level (child) item
				else {
					if (!$submenu) { // first item
						// add the dropdown arrow to the parent
						// $menu_list .= '<span class="arrow-down"></span></a>' . "\n";
						// start the child list
						$submenu = true;
						$previous_item_has_submenu = true;
						$menu_list .= "\t\t" . '<ul id="submenu-' . $menu_item->menu_item_parent . '" class="submenu">' . "\n";
					}

					$menu_list .= "\t\t\t" . '<li>';

					$menu_list .= '<a href="' . $url . '" target="_blank" title="' . $title . '" rel="noopener noreferrer"><i class="' . $icon . '"></i></a>';

					$menu_list .= '</li>' . "\n";

					// if it's the last child, close the submenu code
					if ($menu_items[$count + 1]->menu_item_parent != $parent_id && $submenu) {
						$menu_list .= "\t\t" . '</ul></li>' . "\n";
						$submenu = false;
					}
				}

				// close the parent (top-level) item
				if (empty($menu_items[$count + 1]) || $menu_items[$count + 1]->menu_item_parent != $parent_id) {
					if ($previous_item_has_submenu) {
						// the a link and list item were already closed
						$previous_item_has_submenu = false; //reset
					} else {
						// close a link and list item
						$menu_list .= "\t" . '</a></li>' . "\n";
					}
				}

				$count++;
			}
		} else {
			$menu_list .= '<!-- no list defined -->';
		}
		$menu_list .= "\t" . '</ul>' . "\n";
		$menu_list .= "\t" . '</div>' . "\n";
		// $menu_list .= "\t". '</nav>' ."\n";

		$menu_list .= <<<SD
			<script>
				jQuery( document ).ready( function( $ ) {
					$( ".follow-btn" ).on( "click", function( e ) {
						e.preventDefault();
						var thisID = this.id.split( '-' );
						var parentID = thisID[1];
						var childMenuID = 'submenu-' + parentID;
						
						if( $( "#" + childMenuID ).length ) {
							if( $( "#" + childMenuID ).is( ":hidden" ) ) {
								$( "#" + childMenuID ).fadeIn( "slow" );
								$( "#" + this.id ).css( 'background-color', '#333333' );
							} else {
								$( "#" + childMenuID ).fadeOut( "fast" );
								$( "#" + this.id ).css( 'background-color', '#707070' );
							}
						}
					} );
				} );
			</script>
		SD;
	} else {
		global $template;

		$a = $_SERVER['REQUEST_URI'];
		$search_deals = 'beauty-deals';
		$search_profiles = array('profile', 'change-password');

		$default = '';
		$deals = '';
		$profiles = '';
		$sf = '';

		if (is_front_page()) {
			// $default = ' style="background-color: unset;"';
			$sf = ' style="background-color: #22201B;"';
		}

		if (preg_match("/{$search_deals}/i", $a)) {
			// $default = ' style="background-color: unset;"';
			$deals = ' style="background-color: #22201B;"';
		}

		// converts the array into a regex friendly or list
		$search = implode('|', $search_profiles);
		if (preg_match("/{$search}/i", $a)) {
			$default = ' style="background-color: unset;"';
			$profiles = ' style="background-color: #22201B;"';
		}
		$menu_name = 'mobile-docking-menu'; // specify custom menu slug
		// $menu_list = '<nav class="navbar navbar-expand-lg navbar-dark bg-dark" id="docking-menu">' ."\n";
		// $menu_list .= '<div class="container-fluid justify-content-center">' ."\n";
		$menu_list = '<div id="docking-menu">' . "\n";
		$menu_list .= '<ul class="nav justify-content-center">' . "\n";

		if ($menu_items = wp_get_nav_menu_items($menu_name)) {
			$count = 0;
			$submenu = false;
			$parent_id = 0;
			$previous_item_has_submenu = false;

			foreach ((array)$menu_items as $key => $menu_item) {
				$title = $menu_item->title;
				// print_r($title);
				$url = $menu_item->url;
				$icon = get_field('icon', $menu_item->ID);

				switch (strtolower($menu_item->title)) {
					case 'facebook':
						$url = FB_LINK;
						break;
					case 'instagram':
						$url = IG_LINK;
						break;
				}

				// check if it's a top-level item
				if ($menu_item->menu_item_parent == 0) {
					$parent_id = $menu_item->ID;
					// write the item but DON'T close the A or LI until we know if it has children!

					if ($title == 'Discover') {
						$alt = "Discover more beauty articles";
					} elseif ($title == 'Salon Finder') {
						$alt = "Salon Finder - Beauty Services";
					} else {
						$alt = $title;
					}
					if (basename($template) === 'template-changepass.php') {
						$title == 'Account';
					}
					// if($title=='Nearby') {
					// 	$menu_list .= "\t". '<li class="nav-item"><a href="'. $url .'" class="nav-link parent-nav-link ' . strtolower( str_replace( ' ', '-', $title ) ) . '-btn" id="menu-' . $parent_id . '" target="_blank" title="' . $title . '" rel="noopener noreferrer"><i class="'.$icon.'" id="nearby-icon"></i><br/>' . $title . '</a><br/>' . "\n";
					if ($title == 'Deals') {
						$menu_list .= "\t" . '<li class="nav-item"><a href="' . $url . '" class="nav-link parent-nav-link ' . strtolower(str_replace(' ', '-', $title)) . '-btn" id="menu-' . $parent_id . '" target="_blank" title="' . $title . '" rel="noopener noreferrer"' . $deals . '><i class="' . $icon . '" id="deals-icon"></i><br/>' . $title . '</a><br/>' . "\n";
					} elseif ($title == 'Account') {
						if (is_user_logged_in()) {
							$menu_list .= "\t" . '<li class="nav-item dropup"><a href="' . $url . '" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle parent-nav-link ' . strtolower(str_replace(' ', '-', $title)) . '-btn" id="menu-' . $parent_id . '" title="' . $title . '" rel="noopener noreferrer"' .  $profiles . '"><img alt="' . $alt . '" src="' . $icon . '?v=' . DEPLOY_VERSION . '" class="parent-icon" id="' . strtolower(str_replace(' ', '-', $title)) . '-icon" /><br/>' . $title . '</a><br/>' . "\n";
							$menu_list .= "\t" . '<div class="dropdown-menu" aria-labelledby="menu-' . $parent_id . '">' . "\n";
							$menu_list .= "\t" . '<a class="dropdown-item" href="' . site_url('profile') . '" target="_blank">Manage Account</a>' . "\n";
							$menu_list .= "\t" . '<a class="dropdown-item" href="' . wp_logout_url(home_url()) . '">Sign Out</a>' . "\n";
						} else {
							$menu_list .= "\t" . '<li class="nav-item"><a href="' . site_url('login') . '" class="nav-link parent-nav-link ' . strtolower(str_replace(' ', '-', $title)) . '-btn" id="menu-' . $parent_id . '" title="' . $title . '" rel="noopener noreferrer"' .  $profiles . '><img alt="' . $alt . '" src="' . $icon . '?v=' . DEPLOY_VERSION . '" class="parent-icon" id="' . strtolower(str_replace(' ', '-', $title)) . '-icon" /><br/>' . $title . '</a><br/>' . "\n";
						}

						$menu_list .= "\t" . '</div>' . "\n";
					} elseif ($title == 'Salon Finder') {
						$menu_list .= "\t" . '<li class="nav-item"><a href="' . $url . '" class="nav-link parent-nav-link ' . strtolower(str_replace(' ', '-', $title)) . '-btn" id="menu-' . $parent_id . '" title="' . $title . '" rel="noopener noreferrer"' .  $sf . '><img alt="' . $alt . '" src="' . $icon . '?v=' . DEPLOY_VERSION . '" class="parent-icon" id="' . strtolower(str_replace(' ', '-', $title)) . '-icon" /><br/>' . $title . '</a><br/>' . "\n";
					} else {
						$menu_list .= "\t" . '<li class="nav-item dropdown"><a href="' . $url . '" class="nav-link parent-nav-link ' . strtolower(str_replace(' ', '-', $title)) . '-btn" id="menu-' . $parent_id . '" target="_blank" title="' . $title . '" rel="noopener noreferrer"><img alt="' . $alt . '" src="' . $icon . '?v=' . DEPLOY_VERSION . '" class="parent-icon" id="' . strtolower(str_replace(' ', '-', $title)) . '-icon" /><br/>' . $title . "\n";

						// if( isset( $_COOKIE['woocomemrce_total_cart_item'] ) ) {
						// 	$cartTotal = $_COOKIE['woocomemrce_total_cart_item'];
						// } else {
						// 	$cartTotal = 0;
						// }

						$cartTotal = WC()->cart->get_cart_contents_count();

						if (strtolower($menu_item->title) == 'cart') {
							$menu_list .= '<span class="cart-totals count">' . $cartTotal . '</span></a>';
						} else {
							$menu_list .= '</a>';
						}
					}
				}

				// if this item has a (nonzero) parent ID, it's a second-level (child) item
				else {
					if (!$submenu) { // first item
						// add the dropdown arrow to the parent
						// $menu_list .= '<span class="arrow-down"></span></a>' . "\n";
						// start the child list
						$submenu = true;
						$previous_item_has_submenu = true;
						$menu_list .= "\t\t" . '<ul id="submenu-' . $menu_item->menu_item_parent . '" class="submenu">' . "\n";
					}

					$menu_list .= "\t\t\t" . '<li>';

					$menu_list .= '<a href="' . $url . '" target="_blank" title="' . $title . '" rel="noopener noreferrer"><i class="' . $icon . '"></i></a>';

					$menu_list .= '</li>' . "\n";

					// if it's the last child, close the submenu code
					if ($menu_items[$count + 1]->menu_item_parent != $parent_id && $submenu) {
						$menu_list .= "\t\t" . '</ul></li>' . "\n";
						$submenu = false;
					}
				}

				// close the parent (top-level) item
				if (empty($menu_items[$count + 1]) || $menu_items[$count + 1]->menu_item_parent != $parent_id) {
					if ($previous_item_has_submenu) {
						// the a link and list item were already closed
						$previous_item_has_submenu = false; //reset
					} else {
						// close a link and list item
						$menu_list .= "\t" . '</a></li>' . "\n";
					}
				}

				$count++;
			}
		} else {
			$menu_list .= '<!-- no list defined -->';
		}
		$menu_list .= "\t" . '</ul>' . "\n";
		$menu_list .= "\t" . '</div>' . "\n";
		// $menu_list .= "\t". '</nav>' ."\n";

		$menu_list .= <<<SD
			<script>
				jQuery( document ).ready( function( $ ) {
					$( ".follow-btn" ).on( "click", function( e ) {
						e.preventDefault();
						var thisID = this.id.split( '-' );
						var parentID = thisID[1];
						var childMenuID = 'submenu-' + parentID;
						
						if( $( "#" + childMenuID ).length ) {
							if( $( "#" + childMenuID ).is( ":hidden" ) ) {
								$( "#" + childMenuID ).fadeIn( "slow" );
								$( "#" + this.id ).css( 'background-color', '#333333' );
							} else {
								$( "#" + childMenuID ).fadeOut( "fast" );
								$( "#" + this.id ).css( 'background-color', '#707070' );
							}
						}
					} );
				} );
			</script>
		SD;
	}

	echo $menu_list;
}
add_action('body_div_after', 'footer_docking_menu', 20);

function display_slide_container_start($array)
{
	if (!empty($array['background-image'])) {
		$slidertitle = 'slider-title-collection';
		$sliderexploreall = 'slider-explore-all-collection';
	} else {
		$slidertitle = 'slider-title';
		$sliderexploreall = 'slider-explore-all';
	}

	$content = '';
	$content .= '<div class="container slider-container">' . "\n";
	$content .= "\t" . '<div class="row">' . "\n";
	$content .= "\t\t" . '<div class="col-12 col-md-6 text-left ' . $slidertitle . '">' . $array['title'] . '</div>' . "\n";
	$content .= "\t\t" . '<div class="col-12 col-md-6 text-md-right"><a href="' . $array['view-all-link'] . '" class="' . $sliderexploreall . '">Explore All</a></div>' . "\n";
	$content .= "\t" . '</div>' . "\n";
	$content .= "\t" . '<div class="row">' . "\n";
	$content .= "\t\t" . '<div class="col p-0">' . "\n";
	$content .= "\t\t\t" . '<div class="slider">' . "\n";

	echo $content;
}
add_action('slider-container-start', 'display_slide_container_start', 20);

function display_slide_container_end()
{
	$content = '';
	$content .= "\t\t\t" . '</div>' . "\n";
	$content .= "\t\t" . '</div>' . "\n";
	$content .= "\t" . '</div>' . "\n";
	$content .= '</div>' . "\n";

	echo $content;
}
add_action('slider-container-end', 'display_slide_container_end', 20);

function get_postal_codes($location)
{
	$args = array(
		'North' => array('72', '73', '77', '78', '75', '76', '79', '80'),
		'South' => array('09', '10'),
		'East' => array('38', '39', '40', '41', '42', '43', '44', '45', '46', '47', '48', '49', '50', '81', '51', '52'),
		'West' => array('11', '12', '13', '58', '59', '60', '61', '62', '63', '64', '59'),
		'Central' => array('01', '02', '03', '04', '05', '06', '07', '08', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '57', '59'),
		'Northeast' => array('31', '32', '33', '34', '35', '36', '37', '53', '54', '55', '82', '56', '57', '65', '66', '67', '68', '69', '70', '71'),
		'Northwest' => array('65', '66', '67', '68', '69', '70', '71'),
		'Southeast' => array('38', '39', '40', '41', '42', '43', '44', '45', '46', '47', '48')
	);

	foreach ($location as $loc) {
		$return[] = isset($args[$loc]) && !empty($args[$loc]) ? $args[$loc] : array();
	}

	return $return;
}

add_action('init', 'checkout_user_registration');
function checkout_user_registration()
{
	if ($_SERVER['REQUEST_METHOD'] == 'POST' &&  isset($_REQUEST['user_email'])) {
		global $wpdb, $errors;
		$errors = array();

		require_once('library/ReCaptcha/autoload.php');


		$reCaptcha = new \ReCaptcha\ReCaptcha(reCAPTCHA_SECRET_KEY, new \ReCaptcha\RequestMethod\CurlPost());


		if ($_REQUEST['g-recaptcha-response']) {
			$resp = $reCaptcha->verify($_REQUEST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
		} else {
			$errors['recatpcha'] = "Oops, Please fill ReCaptcha";
			return;
		}

		if (!($resp != null && $resp->isSuccess())) {
			$errors['recatpcha'] = "Oops, Please fill ReCaptcha";
			return;
		}


		$first_name = $wpdb->escape($_REQUEST['first_name']);
		if (empty($first_name)) {
			$errors['first_name'] = "Please enter a First name";

			return;
		}
		$last_name = $wpdb->escape($_REQUEST['last_name']);
		if (empty($last_name)) {
			$errors['last_name'] = "Please enter a Last Name";
			return;
		}

		$username = $wpdb->escape($_REQUEST['username']);
		$email = $wpdb->escape($_REQUEST['user_email']);
		if (!is_email($email)) {
			$errors['email'] = "Please enter a valid email";
			return;
		} elseif (email_exists($email)) {
			$errors['email'] = "This email address is already in use";
			return;
		}
		if (0 === preg_match("/.{6,}/", $_POST['user_pass'])) {
			$errors['password'] = "Password must be at least six characters";
			return;
		}
		if (0 !== strcmp($_POST['user_pass'], $_POST['cn_user_pass'])) {
			$errors['password_confirmation'] = "Passwords do not match";
			return;
		}

		if (0 === count($errors)) {

			$username = substr($email, 0, strpos($email, '@'));

			$password = $_POST['user_pass'];
			$new_user_id = wc_create_new_customer($email, $email, $password, [
				'first_name' => $first_name,
				'last_name' => $last_name,

			]);
			update_field('register_type', 'website', 'user_' . $new_user_id);
			$creds = array();
			$creds['user_login'] = $email; #$username;
			$creds['user_password'] = $password;
			$creds['remember'] = true;
			$user = wp_signon($creds, false);
			$redirect_url = $_POST['redirect_to'];
			##for auto login
			$secure_cookie = is_ssl();
			$secure_cookie = apply_filters('secure_signon_cookie', $secure_cookie, array());
			global $auth_secure_cookie; // XXX ugly hack to pass this to wp_authenticate_cookie
			$auth_secure_cookie = $secure_cookie;
			wp_set_auth_cookie($user->ID, true, $secure_cookie);
			$user_info = get_userdata($userid);
			wp_redirect($redirect_url);
			exit;
		}
	}
}

add_action('wp_ajax_nopriv_check_login_user', 'check_login_user');
add_action('wp_ajax_check_login_user', 'check_login_user');
function check_login_user()
{
	$user = NULL;
	$username = $_REQUEST['email_user'];
	$password = $_REQUEST['password'];
	$remember = $_REQUEST['remember-me'];

	if ($user instanceof WP_User) {
		echo json_encode($user);
		die;
	}

	if (empty($username) || empty($password)) {
		if (is_wp_error($user)) {
			echo json_encode($user);
			die;
		}

		$error = new WP_Error();
		if (empty($username))
			$error->add('empty_username', __('<strong>ERROR</strong>: The Email field is empty.'));

		if (empty($password))
			$error->add('empty_password', __('<strong>ERROR</strong>: The password field is empty.'));

		echo json_encode($error);
		die;
	}

	$user = get_user_by('email', $username);
	if (!$user) {
		echo json_encode(new WP_Error(
			'invalid_username',
			__('<strong>ERROR</strong>: Invalid Email.') .
				' <a href="' . wp_lostpassword_url() . '">' .
				__('Lost your password?') .
				'</a>'
		));
		die;
	}

	if (is_wp_error($user)) {
		echo json_encode($user);
		die;
	}

	if (!wp_check_password($password, $user->user_pass, $user->ID)) {
		do_action('wp_login_failed');
		echo json_encode(new WP_Error(
			'incorrect_password',
			sprintf(
				__('<strong>ERROR</strong>: The password you entered is incorrect.')
			) .
				' <a href="' . wp_lostpassword_url() . '">' .
				__('Lost your password?') .
				'</a>'
		));
		die;
	}

	$creds = array();
	$creds['user_login'] = $username;
	$creds['user_password'] = $password;
	$creds['remember'] = $remember;
	$user = wp_signon($creds, false);
	$secure_cookie = is_ssl();
	$secure_cookie = apply_filters('secure_signon_cookie', $secure_cookie, array());
	global $auth_secure_cookie; // XXX ugly hack to pass this to wp_authenticate_cookie
	$auth_secure_cookie = $secure_cookie;
	wp_set_auth_cookie($user->ID, true, $secure_cookie);
	#wp_redirect($redirect_url);
	#exit();
	#echo json_encode($user);
	echo json_encode($user);
	die;
}

add_filter('login_redirect', function ($url, $query, $user) {
	return site_url('profile');
}, 10, 3);

add_action('wp_ajax_nopriv_ajaxforgotpassword', 'ajaxforgotpassword');
add_action('wp_ajax_ajaxforgotpassword', 'ajaxforgotpassword');
function ajaxforgotpassword()
{
	global $wpdb;
	$account = $_POST['user_login'];
	if (empty($account)) {
		$error = 'Enter an email address';
	} else {
		if (is_email($account)) {
			if (email_exists($account))
				$get_by = 'email';
			else
				$error = 'There is no user registered with that email address.';
		} else
			$error = 'Invalid email address.';
	}

	if (empty($error)) {
		$random_password = wp_generate_password();
		$user = get_user_by($get_by, $account);
		$update_user = wp_update_user(array('ID' => $user->ID, 'user_pass' => $random_password));
		if ($update_user) {
			$from = 'mail@yourdomain.com'; // Set whatever you want like mail@yourdomain.com			
			if (!(isset($from) && is_email($from))) {
				$sitename = strtolower($_SERVER['SERVER_NAME']);
				if (substr($sitename, 0, 4) == 'www.') {
					$sitename = substr($sitename, 4);
				}
				$from = 'admin@' . $sitename;
			}

			$to = $user->user_email;
			$subject = 'Your new password';
			$sender = 'From: ' . get_option('name') . ' <' . $from . '>' . "\r\n";

			$message = '
			<!DOCTYPE html>
			<html>
			<head>
				<title>Your new password</title>
			</head>
			<body>
				<h3>Hi</h3>

				<h4>Your password has been changed</h4>

				<h4>Your new password is: ' . $random_password . ' </h4>

				<h4>Please <a href="' . home_url('/profile/login/') . '">login</a> and change it to a new password for security reason</h4>
				<h4>Stay beautiful!</h4>
				<p>This auto-generated email was sent by Daily Vanity Pte Ltd.</p>	
				<p>201 Henderson Road #06-13/14 Singapore 159545</p>	
			</body>
			</html>
			';

			$headers[] = 'MIME-Version: 1.0' . "\r\n";
			$headers[] = 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers[] = "X-Mailer: PHP \r\n";
			$headers[] = $sender;

			$mail = wp_mail($to, $subject, $message, $headers);
			if ($mail)
				$success = 'Please check your email address for your new password.';
			else
				$error = 'System is unable to send you mail containing your new password.';
		} else {
			$error = 'Oops! Something went wrong while updaing your account.';
		}
	}

	if (!empty($error))
		echo json_encode(array('loggedin' => false, 'message' => __($error)));

	if (!empty($success))
		echo json_encode(array('loggedin' => true, 'message' => __($success)));

	die();
}

function wpb_sender_email($original_email_address)
{
	return 'mail@yourdomain.com';
}

function wpb_sender_name($original_email_from)
{
	return 'Daily Vanity Singapore';
}

// Hooking up our functions to WordPress filters 
add_filter('wp_mail_from', 'wpb_sender_email');
add_filter('wp_mail_from_name', 'wpb_sender_name');

add_filter('wp_mail', 'admin_email_subject_remove_sitename', 99);
function admin_email_subject_remove_sitename($email)
{
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
	$email['subject'] = str_replace("[" . $blogname . "] - ", "", $email['subject']);
	$email['subject'] = str_replace("[" . $blogname . "]", "", $email['subject']);
	return $email;
}


function login_checked_remember_me()
{
	add_filter('login_footer', 'rememberme_checked');
}
add_action('init', 'login_checked_remember_me');

function rememberme_checked()
{
	echo "<script type='text/javascript'>document.getElementById('rememberme').checked = true;</script>";
}

function login_checked_remember_mex()
{
	add_filter('login_footer', 'rememberme_checkedx');
}
add_action('init', 'login_checked_remember_mex');

function rememberme_checkedx()
{
	echo "<script type='text/javascript'>document.getElementById('remember-me').checked = true;</script>";
}

add_filter('woocommerce_form_field', 'my_woocommerce_form_field_form_row');
function my_woocommerce_form_field_form_row($field)
{
	return preg_replace(
		'#<p class="form-row (.*?)"(.*?)>(.*?)</p>#',
		'<div class="inputcontainer pb-2 $1"$2>$3</div>',
		$field
	);
}

add_filter('woocommerce_form_field', 'my_woocommerce_form_field_flabel_for');
function my_woocommerce_form_field_flabel_for($field)
{
	return preg_replace(
		'#label for="(.*?)" class>(.*?)</label>#',
		'<div class="profile-label poppins-semibold pb-2">(.*?)</div>',
		$field
	);
}

add_filter('password_change_email', 'change_password_mail_message', 10, 3);
function change_password_mail_message($pass_change_mail, $user, $userdata)
{
	if (isset($_POST['user_login'])) {
		$user_login = $_POST['user_login'];
	}

	$user = get_user_by('email', $user_login);
	if ($user->first_name) {
		$firstname = $user->first_name;
	}

	if (isset($firstname) & !empty($firstname))
		$name = $firstname;
	else
		$name = "there";

	$new_message_txt = __('Hi ' . $name . ',
	<br><br>
	This notice confirms that your password was changed on <a href="https://salonfinder.dailyvanity.sg">Daily Vanity\'s Salon Finder</a>.
	<br><br>
	If you did not change your password, please contact the Site Administrator at <a href="mailto:email@yourdomain.com">email@yourdomain.com</a>.
	<br><br>
	Regards,
	<br>
	Daily Vanity
	');
	$pass_change_mail['message'] = $new_message_txt;
	return $pass_change_mail;
}

// Similar Services ajax call
add_action("wp_ajax_ajaxSimilarServices", "get_similar_services");
add_action("wp_ajax_nopriv_ajaxSimilarServices", "get_similar_services");

function get_similar_services()
{
	if (!wp_verify_nonce($_REQUEST['nonce'], "get_similar_services_nonce")) {
		exit("Security Error");
	}
	echo do_shortcode('[similar-services page="' . $_POST['page'] . '" product_id="' . $_POST['product_id'] . '"]');
}

// create menu location for product page docking menu

function wpb_product_mobile_docking_menu()
{
	register_nav_menu('product-mobile-docking-menu', __('Product Mobile Docking Menu'));
}
add_action('init', 'wpb_product_mobile_docking_menu');

// remove inline styling 
add_filter('the_content', function ($content) {
	return str_replace(' style="', ' data-style="', $content);
});

remove_action('wpua_before_avatar', 'wpua_do_before_avatar');
remove_action('wpua_after_avatar', 'wpua_do_after_avatar');

add_action('woocommerce_cart_totals_before_order_total', 'show_total_discount_cart_checkout', 9999);
add_action('woocommerce_review_order_after_order_total', 'show_total_discount_cart_checkout', 9999);
function show_total_discount_cart_checkout()
{
	$discount_total = 0;
	// WC()->cart->calculate_totals();
	foreach (WC()->cart->get_cart() as $cart_item_key => $values) {
		$product = $values['data'];
		//   print_r($product);die();
		if ($product->is_on_sale()) {
			$regular_price = is_numeric($product->get_regular_price());
			$sale_price = is_numeric($product->get_sale_price());
			$discount = ($regular_price - $sale_price) * $values['quantity'];
			$discount_total += $discount;
		}
	}
	// print_r($discount_total);
	if ($discount_total > 0) {
		echo '<tr class="yousaved poppins-medium"><td colspan="4" data-title="You Saved" style="border:none;padding-top:10px;padding-right:0px;">You saved S' . wc_price($discount_total + WC()->cart->get_discount_total()) . '!</td></tr>';
	}
}

add_action('template_redirect', 'check_if_logged_in');
function check_if_logged_in()
{
	$pageid = get_option('woocommerce_checkout_page_id'); // your checkout page id
	if (!is_user_logged_in() && is_page($pageid)) {
		$url = add_query_arg(
			'redirect_to',
			get_permalink($pageid),
			site_url('/profile/register/') // your my acount url
		);
		wp_redirect($url);
		exit;
	}
}

// Removes Order Notes Title - Additional Information & Notes Field
add_filter('woocommerce_enable_order_notes_field', '__return_false', 9999);

// Remove Order Notes Field
add_filter('woocommerce_checkout_fields', 'remove_order_notes');
function remove_order_notes($fields)
{
	unset($fields['order']['order_comments']);
	return $fields;
}

include_once('customization/customization.php');

// change continue shopping button link
add_filter('woocommerce_continue_shopping_redirect', 'st_change_continue_shopping');
/**
 * WooCommerce
 * Change continue shopping URL
 */
function st_change_continue_shopping()
{
	return home_url(); // Change link
}

add_filter('woocommerce_thankyou_order_received_text', 'woo_change_order_received_text');
function woo_change_order_received_text($str)
{
	$new_str = '<div class="col-md-12 col-12 col-sm-12 pb-4 thankyou-title">';
	$new_str .= '<span class="thankyou">Thank you!</span><br>';
	$new_str .= '<span class="successfull poppins-medium">Your purchase was successful!</span>';
	$new_str .= '</div>';
	return $new_str;
}

// Start Disable Continue Shopping Message after Add to Cart
add_filter('wc_add_to_cart_message', function ($string, $product_id = 0) {
	$start = strpos($string, '<a href=') ?: 0;
	$end = strpos($string, '</a>', $start) ?: 0;
	return substr($string, $end) ?: $string;
});

add_action('wp_footer', 'trigger_update_cart_add_script_to_footer');
function trigger_update_cart_add_script_to_footer()
{
	if (is_cart()) :
	?>
		<style>
			.woocommerce button[name="update_cart"],
			.woocommerce input[name="update_cart"] {
				display: none !important;
			}
		</style>

		<script type="text/javascript">
			jQuery(document).ready(function($) {
				$(document).on('click', '.btn-plus', function(e) {
					$input = $(this).prev('input.qty');
					var val = parseInt($input.val());
					var step = $input.attr('step');
					step = 'undefined' !== typeof(step) ? parseInt(step) : 1;
					$input.val(val + step).change();
					$("[name='update_cart']").trigger('click');
				});
				$(document).on('click', '.btn-minus',
					function(e) {
						$input = $(this).next('input.qty');
						var val = parseInt($input.val());
						var step = $input.attr('step');
						step = 'undefined' !== typeof(step) ? parseInt(step) : 1;
						if (val > 0) {
							$input.val(val - step).change();
						}
						$("[name='update_cart']").trigger('click');
					});
			});
		</script>
	<?php
	endif;
}

function cw_change_product_price_display($price)
{
	$text_format = 'S';
	return $text_format . $price;
}
// add_filter( 'woocommerce_get_price_html', 'cw_change_product_price_display' );
add_filter('woocommerce_cart_item_subtotal', 'cw_change_product_price_display');

add_filter('woocommerce_checkout_fields', 'addBootstrapToCheckoutFields');
function addBootstrapToCheckoutFields($fields)
{
	foreach ($fields as &$fieldset) {
		foreach ($fieldset as &$field) {
			// if you want to add the form-group class around the label and the input
			$field['class'][] = 'form-group';

			// add form-control to the actual input
			$field['input_class'][] = 'form-control input-form poppins-medium no-shadow';
		}
	}
	return $fields;
}

add_filter('woocommerce_checkout_fields', 'rename_woo_checkout_fields');
function rename_woo_checkout_fields($fields)
{
	$fields['billing']['billing_first_name']['label_class'] = 'woo-label poppins-semibold';
	$fields['billing']['billing_last_name']['label_class'] = 'woo-label poppins-semibold';
	$fields['billing']['billing_phone']['label_class'] = 'woo-label poppins-semibold';
	$fields['billing']['billing_email']['label_class'] = 'woo-label poppins-semibold';
	return $fields;
}

add_action('woocommerce_checkout_order_review', 'reordering_checkout_order_review', 1);
function reordering_checkout_order_review()
{
	remove_action('woocommerce_checkout_order_review', 'woocommerce_order_review', 10);
	remove_action('woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20);

	add_action('woocommerce_checkout_order_review', 'woocommerce_order_review', 20);
	add_action('woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 10);
	// add_action( 'woocommerce_checkout_order_review', 'after_custom_checkout_payment', 9 );
	// add_action( 'woocommerce_checkout_order_review', 'custom_checkout_place_order', 20 );
}

add_filter('woocommerce_gateway_icon', 'sort_stripe_payment_icons', 10, 2);
function sort_stripe_payment_icons($icons_str)
{
	if (is_checkout()) {
		$available_gateways = WC()->payment_gateways->get_available_payment_gateways();
		$stripe = $available_gateways['stripe'];
		$icons = $stripe->payment_icons();
		$icons_str = '';

		$icons_str .= '<img src="' . WC_STRIPE_PLUGIN_URL . '/assets/images/amex.svg" class="stripe-amex-icon stripe-icon" alt="American Express" />';
		$icons_str .= '<img src="' . WC_STRIPE_PLUGIN_URL . '/assets/images/visa.svg" class="stripe-visa-icon stripe-icon" alt="Visa" />';
		$icons_str .= '<img src="' . WC_STRIPE_PLUGIN_URL . '/assets/images/mastercard.svg" class="stripe-mastercard-icon stripe-icon" alt="Mastercard" />';
	}
	return $icons_str;
}

function custom_cart_totals_order_total_html($value)
{
	$value = WC()->cart->get_total();

	// If prices are tax inclusive, show taxes here.
	if (wc_tax_enabled() && WC()->cart->display_prices_including_tax()) {
		$tax_string_array = array();
		$cart_tax_totals  = WC()->cart->get_tax_totals();
		if (get_option('woocommerce_tax_total_display') === 'itemized') {
			foreach ($cart_tax_totals as $code => $tax) {
				$tax_string_array[] = sprintf('%s %s', $tax->formatted_amount, $tax->label);
			}
		} elseif (!empty($cart_tax_totals)) {
			$tax_string_array[] = sprintf('%s %s', wc_price(WC()->cart->get_taxes_total(true, true)), WC()->countries->tax_or_vat());
		}

		if (!empty($tax_string_array)) {
			$taxable_address = WC()->customer->get_taxable_address();
			$estimated_text  = '';
			$value .= '<small class="includes_tax">' . sprintf(__('(incl. VAT & delivery)', 'woocommerce'), implode(', ', $tax_string_array) . $estimated_text) . '</small>';
		}
	}
	return $value;
}
add_filter('woocommerce_cart_totals_order_total_html', 'custom_cart_totals_order_total_html', 20, 1);

add_filter('woocommerce_order_button_html', '');

// function woocommerce_change_coupon_label($coupon)
// {
// 	if ( is_string( $coupon ) ) {
// 		$coupon = new WC_Coupon( $coupon );
// 	}

// 	// $label = apply_filters( 'woocommerce_cart_totals_coupon_label', sprintf( esc_html__( 'Coupon - %s', 'woocommerce' ), $coupon->get_code() ), $coupon );

// 	// if ( $echo ) {
// 	// 	echo $label; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
// 	// } else {
// 		// return $label;
// 	// }
//     // $coupon_code = substr($coupon->get_code(), strpos($coupon->get_code(), ': ') + 1);
//     return 'Coupon - '. $coupon_code . ' <a href="' . wc_get_checkout_url() . '?remove_coupon='. esc_attr( $coupon->get_code() ) . '" class="woocommerce-remove-coupon poppins-medium" data-coupon="' . esc_attr( $coupon->get_code() ) .'">REMOVE</a>';
// }
// add_filter('woocommerce_cart_totals_coupon_label', 'woocommerce_change_coupon_label');

function filter_woocommerce_cart_totals_coupon_html($coupon_html, $coupon, $discount_amount_html)
{
	if (is_string($coupon)) {
		$coupon = new WC_Coupon($coupon);
	}
	$discount_amount_html = '';
	$discount_amount_html .= '<a href="' . wc_get_checkout_url() . '?remove_coupon=' . esc_attr($coupon->get_code()) . '" class="woocommerce-remove-coupon" data-coupon="' . esc_attr($coupon->get_code()) . '">' . __('REMOVE', 'woocommerce') . '</a>';
	$amount               = WC()->cart->get_coupon_discount_amount($coupon->get_code(), WC()->cart->display_cart_ex_tax);
	$discount_amount_html .= '-' . wc_price($amount);

	$coupon_html = $discount_amount_html;

	return $coupon_html;
}
add_filter('woocommerce_cart_totals_coupon_html', 'filter_woocommerce_cart_totals_coupon_html', 10, 3);

add_filter('wc_add_to_cart_message_html', 'add_continue_shopping_button', 10, 2);
function add_continue_shopping_button($message, $products)
{

	$content = '';

	// $message .= sprintf( '<a href="%s" class="button wc-forward" style="clear:both;margin-top:5px;">%s</a>', esc_url( wc_get_page_permalink( 'shop' ) ), esc_html__( 'Continue Shopping', 'woocommerce' ) );

	$content .= '<div class="add-to-cart-message">' . $message . '</div><div class="continue-shopping-btn">';

	$content .= sprintf('<a href="%s" class="button wc-forward" style="clear:both;margin-top:5px;">%s</a>', esc_url(wc_get_page_permalink('shop')), esc_html__('Continue Shopping', 'woocommerce'));

	$content .= '</div>';

	return $content;
}

// cancel voucher ajax
add_action('wp_ajax_nopriv_ajaxCancelVoucher', 'cancel_voucher');
add_action('wp_ajax_ajaxCancelVoucher', 'cancel_voucher');

function cancel_voucher()
{
	if (!wp_verify_nonce($_REQUEST['nonce'], $_POST['voucher'] . "-nonce")) {
		exit("Security Error");
	}

	$current_user = wp_get_current_user();

	$voucher = $_POST['voucher'];
	$to = 'chesspamungkas@gmail.com';
	$from = $current_user->user_email;
	$fromName = $current_user->display_name;
	$subj = 'Voucher Cancellation - ' . $voucher;
	$radioVal = $_POST['radio'];
	$other = '';

	if ($_POST['other']) {
		$other = ' - ' . $_POST['other'];
	}

	$body = '';

	$body .= <<<EMAIL
		<p>Voucher Code: {$voucher}</p>
		<p>Email: {$from}</p>
		<p>Reason: {$radioVal} {$other}</p>
	EMAIL;

	echo sendEmail($fromName, $from, $to, $subj, $body);
	die();
}

// function to send email
function sendEmail($fromName, $from, $to, $subj, $body)
{
	add_filter('wp_mail_content_type', 'set_html_content_type');
	$headers[] = 'From: ' . $fromName . ' <' . $from . '>';
	// $headers[]= 'Cc: ReceiverName <second email>';
	// $headers[]= 'Bcc: Receiver2Name <third email>';
	// if( wp_mail( $to, $subj, $body, $headers ) ) {
	// 	return true;
	// } else {
	// 	return false;
	// }
	return wp_mail($to, $subj, $body, $headers);
	remove_filter('wp_mail_content_type', 'set_html_content_type');
}

function set_html_content_type()
{
	return 'text/html';
}

function custom_continue_shopping_redirect_url($url)
{
	$url = site_url();
	return $url;
}
add_filter('woocommerce_continue_shopping_redirect', 'custom_continue_shopping_redirect_url');

function populate_specific_checkout_field($input, $key)
{
	global $current_user;

	$user_phone = get_user_meta($current_user->ID, 'billing_phone', true);
	// print_r($user_phone);die();
	switch ($key) {
		case 'billing_first_name':
			return $current_user->first_name;
			break;

		case 'billing_last_name':
			return $current_user->last_name;
			break;

		case 'billing_phone':
			return $user_phone;
			break;
	}
}
add_filter('woocommerce_checkout_get_value', 'populate_specific_checkout_field', 15, 2);

function get_domain($host)
{
	$myhost = strtolower(trim($host));
	$domainArr = explode('.', $myhost);

	$domain = '';
	foreach ($domainArr as $k => $v) {
		if ($k !== 0) {
			if ($k == 1) {
				$domain .= $v;
			} else {
				$domain .= '.' . $v;
			}
		}
	}
	return $domain;
}

add_action('wp', 'woocommerce_set_custom_cookie', 100);
add_action('woocommerce_add_to_cart_handler', 'woocommerce_set_custom_cookie');
function woocommerce_set_custom_cookie()
{
	$domain = get_domain($_SERVER['HTTP_HOST']);
	setcookie('CARTCOUNT', WC()->cart->cart_contents_count, time() + 31556926, '/', $domain);
}

function change_promotion_title($title, $id)
{
	return $title;
	$product_id = wp_get_post_parent_id($id);
	$from = get_post_meta($id, '_sale_price_dates_from', true);
	$to = get_post_meta($id, '_sale_price_dates_to', true);
	$current = time();
	$default_title = get_the_title($product_id);

	if (get_post_meta($id, '_sale_price', true) && ($from > 0 || $from == '') && $to > 0 && $current >= $from && $current <= $to) {
		// $product_variation = new WC_Product_Variation($id); 
		// $name = $product_variation->get_sku();

		$group = get_post_meta($id, '_weight', true);
		if ($group) {
			$key = 'promotion_title_' . $group;
			delete_transient($key);
			if (false === ($name = get_transient($key))) {
				$url = VOUCHERS_API . 'promo/' . $group;

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_TIMEOUT, 20);
				$response = curl_exec($ch);
				curl_close($ch);

				$response = json_decode($response, true);
				if (isset($response['promo']['name'])) {
					$response = $response['promo']['name'];
					set_transient($key, $response, 0.02 * HOUR_IN_SECONDS);
					$name = $response;
				}
			}
		}
	}
	if (!empty($name)) {
		$title = $name;
	} else {
		$title = $default_title;
	}

	//Return the normal Title if conditions aren't met
	return str_replace("&#8217;", "'", $title);
}

add_action('woocommerce_after_checkout_billing_form', 'custom_woocommerce_billing_fields');
function custom_woocommerce_billing_fields()
{
	$current_user = wp_get_current_user();
	$content = '<div class="inputcontainer pb-2 form-group form-row-first validate-required validate-email" id="billing_email_field_text">';
	$content .= '<label for="billing_email" class="woo-label poppins-semibold">Email address&nbsp;<abbr class="required" title="required">*</abbr></label>';
	$content .= '<p class="email-form poppins-medium">' . $current_user->user_email . '</p>';
	$content .= '</div>';

	echo $content;
}


add_action('admin_head-edit-tags.php', 'remove_parent_category');
function remove_parent_category()
{
	// don't run in the Tags screen
	if ('collection' != $_GET['taxonomy'])
		return;

	$parent = 'parent()';

	if (isset($_GET['action']))
		$parent = 'parent().parent()';

	?>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('label[for=parent]').<?php echo $parent; ?>.remove();
			$('label[for=tag-description]').<?php echo $parent; ?>.remove();
		});
	</script>
<?php
}

// define the woocommerce_product_title callback 
function filter_woocommerce_product_title($this_get_name)
{
	global $product;
	if ($product) {
		if ($product->is_on_sale() && $product->get_meta('promoTerms')) {
			$this_get_name = $product->get_meta('promoName');
		}
	}
	return $this_get_name;
};
// add the filter 
add_filter('woocommerce_product_title', 'filter_woocommerce_product_title', 10, 2);

add_action('woocommerce_payment_complete', 'order_received_empty_cart_action', 10, 1);
function order_received_empty_cart_action($order_id)
{
	WC()->cart->empty_cart();
}
