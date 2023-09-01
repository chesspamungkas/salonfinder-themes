<?php
$cat_name = $_GET['catname'] ?: null;
$location = isset($_GET['location']) && !empty($_GET['location']) && $_GET['location'] != 'null' ? $_GET['location'] : '';
do_shortcode("[list-branches merchantName='$cat_name' location='$location']");

?>

<div class="search-result">
	<?php
	global $wpdb;
	$all_matched_outlet_results = array();
	$paged = get_query_var('page') ? get_query_var('page') : 1;
	$term_id = $_SESSION['catid'];
	$cat_name = $_GET['catname'];
	$location = isset($_GET['location']) && !empty($_GET['location']) && $_GET['location'] != 'null' ? $_GET['location'] : '';

	$search_service_ids  = array();
	$postalCode = get_postal_codes($location);
	//   $search_query = "SELECT * FROM `f5ATe_posts` fp";
	$select_query = "SELECT * ";
	$from_query = " FROM `f5ATe_posts` fp";
	// if( $term_id ) {
	// $search_term = "AND ftr.term_taxonomy_id = ".$term_id;
	// }
	// else {
	// $terms = $wpdb->get_col("SELECT * FROM `f5ATe_terms` WHERE `name` LIKE '%$cat_name%'");
	$terms = get_terms('product_cat', array(
		'hide_empty' => true,
		'name'	=> $cat_name
	));

	if ($terms) {
		$term_ids = array();
		foreach ($terms as $t) {
			$term_ids[] = $t->term_id;
		}
		$term_ids = implode(",", $term_ids);

		$placeholders = implode(", ", array_fill(0, count($term_ids), '%d'));

		$search_term = "AND ftr.term_taxonomy_id IN ( $placeholders )";
	} else {
		$search_term = "AND ftr.term_taxonomy_id IN ( 9999999999999 )";
	}
	// }
	$search_query_where = " WHERE post_type = 'product_variation' AND post_status = 'publish' $search_term AND fpm1.meta_key = 'attribute_pa_branch'";
	$inner_join = " INNER JOIN  `f5ATe_term_relationships` ftr on fp.post_parent = ftr.object_id
  INNER JOIN f5ATe_postmeta fpm1 ON fp.ID = fpm1.post_id";
	$order_by = " ORDER BY fpm1.meta_value, post_title ASC";
	$orderbymore = "";
	$search_postal_code = false;
	$search_key = $catname;
	$postal_match_ids = array();
	if ($search_key != "") {
		$sql = "SELECT term_id FROM `f5ATe_terms` WHERE `name` = %s";
		$search_salon_term_name = $wpdb->get_col($wpdb->prepare($sql, $search_key));

		$sql = "SELECT ID FROM f5ATe_posts INNER JOIN f5ATe_postmeta   ON ID = post_id WHERE meta_value LIKE %s AND (meta_key = 'attribute_pa_branch' OR meta_key = 'attribute_pa_advertiser') AND post_status = 'publish' AND post_type='product'";
		$post_meta_ids = $wpdb->get_col($wpdb->prepare($sql, '%' . $search_key . '%'));

		$sql = "SELECT object_id FROM  f5ATe_term_relationships wr INNER JOIN f5ATe_term_taxonomy wtx ON wr.term_taxonomy_id = wtx.term_taxonomy_id INNER JOIN f5ATe_terms wt ON wtx.term_id = wt.term_id WHERE  wt.name LIKE %s AND wtx.taxonomy = 'product_cat'";
		$term_tax_ids = $wpdb->get_col($wpdb->prepare($sql, '%' . $search_key . '%'));

		$sql = "SELECT object_id FROM  f5ATe_term_relationships wr INNER JOIN f5ATe_term_taxonomy wtx ON wr.term_taxonomy_id = wtx.term_taxonomy_id INNER JOIN f5ATe_termmeta wtm ON wtx.term_id = wtm.term_id WHERE wtm.meta_value LIKE %s AND wtx.taxonomy = 'product_cat'";
		$term_meta_ids = $wpdb->get_col($wpdb->prepare($sql, '%' . $search_key . '%'));

		$tax_prd_ids_in = array_unique(array_merge($search_salon_term_name, $term_meta_ids, $post_meta_ids));

		// if(!empty($tax_prd_ids_in)){
		// $search_prd_ids_in = $wpdb->get_col("SELECT ID FROM  f5ATe_posts WHERE meta_value LIKE '%".$post_id_result."%' AND  post_parent IN (".$tax_prd_ids_in.") AND post_status='publish' AND post_type='product_variation'");
		// }

		$sql = "SELECT DISTINCT slug FROM `f5ATe_terms` WHERE term_id IN ( SELECT DISTINCT REPLACE( REPLACE( REPLACE( REPLACE( REPLACE( REPLACE( option_name, 'merchant_', '' ), '_outlet_postalcode', '' ), '_outlet_description', '' ), '_outlet_address', '' ), '_outlet_brandname', '' ), '_advertiser_description', '' ) FROM `f5ATe_options` WHERE ( option_name LIKE 'merchant%_outlet_postalcode' OR option_name LIKE 'merchant%_advertiser_description' OR option_name LIKE 'merchant%_outlet_description' OR option_name LIKE 'merchant%_outlet_address' OR option_name LIKE 'merchant%_outlet_brandname' ) AND option_value LIKE %s)";
		$post_ids = $wpdb->get_col($wpdb->prepare($sql, '%' . $search_key . '%'));

		$outlet_match_ids = array();

		foreach ($post_ids as $post_id_result) {
			$sql = "SELECT DISTINCT ID FROM  f5ATe_posts INNER JOIN `f5ATe_postmeta` on ID = post_id WHERE meta_value LIKE %s AND meta_key = 'attribute_pa_branch' AND post_status = 'publish' AND post_type = 'product_variation'";
			$outlet_match_id = $wpdb->get_col($wpdb->prepare($sql, '%' . $post_id_result . '%'));

			if (empty($outlet_match_ids)) {
				$outlet_match_ids = $outlet_match_id;
			} else {
				if ($outlet_match_id)
					$outlet_match_ids = array_merge($outlet_match_ids, $outlet_match_id);
			}
		}
		$search_service_ids =  array_merge($outlet_match_ids, $tax_prd_ids_in);
	}

	switch ($_REQUEST["orderby"]) {
		case 'price':
			$inner_join .= " INNER JOIN f5ATe_postmeta fpm2 ON fp.ID = fpm2.post_id INNER JOIN f5ATe_postmeta fpm3 ON fp.ID = fpm3.post_id";
			$search_query_where .= " AND fpm2.meta_key = '_price' AND fpm3.meta_key = '_sale_price'";
			$order_by = " ORDER BY Rand()";
			$select_query .= ",if(fpm3.meta_value+0>0, fpm3.meta_value+0 , fpm2.meta_value+0) AS product_price";
			$orderbymore = "price";
			break;
		case 'price-desc':
			$inner_join .= " INNER JOIN f5ATe_postmeta fpm2 ON fp.ID = fpm2.post_id INNER JOIN f5ATe_postmeta fpm3 ON fp.ID = fpm3.post_id";
			$search_query_where .= " AND fpm2.meta_key = '_price' AND fpm3.meta_key = '_sale_price'";
			$order_by = " ORDER BY Rand()";
			$select_query .= ",if(fpm3.meta_value+0>0, fpm3.meta_value+0 , fpm2.meta_value+0) AS product_price";
			$orderbymore = "price-desc";
			break;
		case 'promotional':
			$inner_join .= " INNER JOIN f5ATe_postmeta fpm2 ON fp.ID = fpm2.post_id";
			$search_query_where .= " AND fpm2.meta_key = '_sale_price'";
			$order_by = " ORDER BY Rand()";
			$orderbymore = "promotional";
			break;
		default:
			$inner_join .= " INNER JOIN f5ATe_postmeta fpm2 ON fp.ID = fpm2.post_id";
			$search_query_where .= " AND fpm2.meta_key = '_sale_price'";
			$order_by = " ORDER BY Rand()";
			$orderbymore = "promotional";
			break;
	}

	$sql = $select_query . $from_query . $inner_join . $search_query_where . $order_by;

	$search_results = $wpdb->get_results($wpdb->prepare($sql, $term_ids));

	//  echo $select_query.$from_query.$inner_join.$search_query_where.$order_by;
	// var_dump($select_query.$from_query.$inner_join.$search_query_where.$order_by);
	$search_results1 = array_merge($search_service_ids, $search_results);

	#$search_results = $wpdb->get_results($search_query.$inner_join.$search_query_where.$order_by);
	$matched_promotion_results = array();

	if (is_array($search_results1) || is_object($search_results1)) {
		foreach ($search_results1 as $search_result) {
			$variation_ID = isset($search_result->ID) ? $search_result->ID : $search_result;
			$variation = get_post($variation_ID);
			$parent = get_post($variation->post_parent);
			$attribute_pa_branch = get_post_meta($variation_ID, 'attribute_pa_branch', true);
			$product_id = isset($search_result->post_parent) ? $search_result->post_parent : 0;
			$attribute_pa_advertiser = get_post_meta($variation_ID, 'attribute_pa_advertiser', true);
			$merchant_slug = trim(str_replace_first($attribute_pa_advertiser, "", $attribute_pa_branch), "-");
			$from = get_post_meta($variation_ID, '_sale_price_dates_from', true);
			$to = get_post_meta($variation_ID, '_sale_price_dates_to', true);
			$current = time();

			if (
				$product_id &&
				has_term($attribute_pa_branch, 'merchant', $product_id) &&
				has_term($attribute_pa_advertiser, 'merchant', $product_id) &&
				$parent->post_status == 'publish'
			) {

				#$matched_search_resulst[]=$variation_ID;
				$all_matched_outlet_results[$attribute_pa_branch][] = $variation_ID;
				if (get_post_meta($variation_ID, '_sale_price', true) && $current >= $from && $current <= $to) {
					$matched_promotion_results[$attribute_pa_branch]["promotion"] = true;
				}
			}
		}
	}

	if ($all_matched_outlet_results) {
		foreach ($all_matched_outlet_results as $key => $matched_outlet_result) {
			$term = get_term_by('slug', $key, 'merchant');
			$parent = get_term_by('id', $term->parent, 'merchant');

			if (!isset($term->term_id)) {
				unset($all_matched_outlet_results[$key]);
			}
			if (!isset($parent->term_id)) {
				unset($all_matched_outlet_results[$key]);
			}
		}
	}

	if ($all_matched_outlet_results && $postalCode) {
		foreach ($all_matched_outlet_results as $key => $matched_outlet_result) {

			$term = get_term_by('slug', $key, 'merchant');

			$parent = get_term_by('id', $term->parent, 'merchant');
			$term_id = $term->term_id;
			$term_meta = $term->taxonomy . '_' . $term_id;
			$o_address = get_field('outlet_postalcode', $term_meta);
			$o_address_sub = substr($o_address, 0, 2);

			$term = get_term_by('slug', $key, 'merchant');

			if (!in_array($o_address_sub, $postalCode)) {
				unset($all_matched_outlet_results[$key]);
			}
		}
	}

	$total_search_results = count($all_matched_outlet_results);
	$paged = get_query_var('page') ? get_query_var('page') : 1;
	$initial_show_limit = 8;
	$limit_array = ($initial_show_limit - 1) + ($paged - 1) * 5;
	?>
	<?php if ($total_search_results != 0) { ?>
		<div class="search_title">
			<div class="search_title_left">
				<?php #if ( function_exists('yoast_breadcrumb') ) {yoast_breadcrumb('<p id="breadcrumbs">','</p>');}
				?>
				<h2>
					<?php
					$search_message = '';
					if (is_array($terms) && sizeof($terms)) {
						$service_term = get_term($terms[0], 'product_cat');
						if ($service_term->parent) {
							$parent_term = get_term($service_term->parent, 'product_cat');
							if ($parent_term) {
								if ($location) {
									$search_message = $parent_term->name . ": " . $service_term->name . " in " . $location;
								} else {
									$search_message = $parent_term->name . ": " . $service_term->name;
								}
							}
						}
					}
					if ($search_message == '') {
						if ($location) {
							$search_message = $location;
						} else {
							$search_message = $cat_name;
						}
					}
					echo $search_message;
					?>
				</h2>
				<p class="search_number"><?php echo $total_search_results; ?> services found</p>
			</div>
			<?php if ($total_search_results > 0) : ?> <div class="search_title_right"><?php woocommerce_catalog_ordering(); ?></div><?php endif; ?>
			<div class="floatfix"></div>
		</div>
	<?php } ?>
	<?php if (!empty($all_matched_outlet_results)) : ?>
		<?php
		$x = 0;
		$outlet_array = array();
		foreach ($all_matched_outlet_results as $key => $matched_outlet_result) :
			$x++;
			$term = get_term_by('slug', $key, 'merchant');
			$parent = get_term_by('id', $term->parent, 'merchant');
			$term_id = $term->term_id;
			$term_meta = $term->taxonomy . '_' . $term_id;
			$parent_meta = $parent->taxonomy . '_' . $parent->term_id;
			$i = 0;
			$o_address = get_field('outlet_postalcode', $term_meta);

			if ($postalCode) {
		?>
				<div class="repeatdiv repeat<?php echo $x; ?>" number="<?php echo $x; ?>" <?php if ($x == 1) { ?> cuspage="0" <?php } ?>>
					<!-- Left Side -->
					<div class="repeatleft">
						<?php $ad_logo = get_field('outlet_featured_image', $term_meta); ?>
						<?php $ad_alt = get_field('outlet_image_alt', $term_meta); ?>
						<a href="<?php echo get_term_link($term_id, 'merchant'); ?>" class="" title=""><img src="<?php echo $ad_logo ?>" alt="<?php echo $ad_alt ?>" /></a>
					</div>

					<!-- Right Side -->
					<div class="repeatright">
						<div class="container">
							<div class="row">
								<div class="repeatright_title col-md-12 col-sm-12">
									<div class="row">
										<div class="col-md-9 col-xs-12">
											<h3>
												<a href="<?php echo get_term_link($term_id, 'merchant'); ?>"><?php echo get_field('outlet_brandname', $term_meta); ?> </a>
											</h3>
										</div>
										<div class="col-md-3 col-xs-12">
											<?php if (isset($matched_promotion_results[$key]["promotion"]) && $matched_promotion_results[$key]["promotion"] == true) { ?>
												<span class="product_promotion">promotion</span>
											<?php } ?>
										</div>
										<div class="col-md-12 col-xs-12">
											<p class="outlet_address"><?php echo get_field('outlet_address', $term_meta); ?>, Singapore <?php echo get_field('outlet_postalcode', $term_meta); ?></p>
										</div>
									</div>
								</div> <!-- end repeatright_title -->
							</div> <!-- end row -->

							<?php
							$check_args = array();
							foreach ($matched_outlet_result as $search_result) {
								$i++;
								$variation_ID = $search_result;
								$product_id = wp_get_post_parent_id($variation_ID);
								$attribute_pa_branch = get_post_meta($variation_ID, 'attribute_pa_branch', true);
								$attribute_pa_advertiser = get_post_meta($variation_ID, 'attribute_pa_advertiser', true);
								$merchant_slug = $key;

								if ($i == 4) {
									echo '<p class="show_more"><a href="' . get_term_link($term_id, 'merchant') . '">Show more services</a></p>';
									break;
								}
								if (!in_array($search_result, $check_args)) {
									$check_args[] = $search_result;
							?>
									<div class="row repeatrightprice rightprice_<?php echo $i; ?> <?php if ($i >= 4) {
																										echo 'rightprice_none';
																									} ?>">
										<div class="col-md-4 col-xs-12">
											<a href="<?php echo get_the_permalink($product_id);
														if (!empty($variation_ID)) {
															echo '?varid=' . $variation_ID;
														} ?>" id="product_<?php echo $product_id; ?>">
												<?php
												if (!empty($variation_ID)) {
													echo change_promotion_title(get_the_title($product_id), $variation_ID);
												} else {
													echo get_the_title($product_id);
												}
												?>
											</a>
										</div> <!-- end col-md-4 col-xs-12 -->
										<div class="col-md-3 col-xs-4">
											<span class="duration <?php if (get_post_meta($product_id, 'duration', true)) {
																		echo 'dur_active';
																	} ?>">
												<?php if (get_post_meta($product_id, 'duration', true)) {
													echo get_post_meta($product_id, 'duration', true);
												} ?>
											</span>
										</div> <!-- end col-md-2 col-xs-3 -->
										<div class="col-md-3 col-xs-5">
											<?php
											echo admin_get_variation_price($variation_ID);
											?>
										</div> <!-- end col-md-4 col-xs-6 -->
										<div class="col-md-2 col-xs-3">
											<div class="buy-details">
												<form class="cart ng-pristine ng-valid allpro-duct" action="<?php echo get_term_link($term_id, 'merchant'); ?>" method="post" enctype="multipart/form-data">
													<button type="submit" class="single_add_to_cart_button button alt">BUY</button>
													<input type="hidden" name="add-to-cart" value="<?php echo absint($product_id); ?>" />
													<input type="hidden" name="product_id" value="<?php echo absint($product_id); ?>" />
													<input type="hidden" name="variation_id" class="variation_id" value="<?php echo $variation_ID ?>" />
												</form>
											</div> <!-- end buy-details -->
										</div> <!-- end col-md-2 col-xs-3 -->
									</div> <!-- end row repeatrightprice -->
							<?php }
							} ?>
						</div> <!-- end container -->
					</div> <!-- end repeatright -->
					<div style="clear:both"></div>
				</div> <!-- end repeatdiv -->
				<?php if ($initial_show_limit == $x) break; ?>



			<?php
			} else if (!$postalCode) {
			?>

				<div class="repeatdiv repeat<?php echo $x; ?>" number="<?php echo $x; ?>" <?php if ($x == 1) { ?> cuspage="0" <?php } ?>>
					<!-- Left Side -->
					<div class="repeatleft">
						<?php $ad_logo = get_field('outlet_featured_image', $term_meta); ?>
						<?php $ad_alt = get_field('outlet_image_alt', $term_meta); ?>
						<a href="<?php echo get_term_link($term_id, 'merchant'); ?>" class="" title=""><img src="<?php echo $ad_logo ?>" alt="<?php echo $ad_alt ?>" /></a>
					</div>

					<!-- Right Side -->
					<div class="repeatright">
						<div class="container">
							<div class="row">
								<div class="repeatright_title col-md-12 col-sm-12">
									<div class="row">
										<div class="col-md-9 col-xs-12">
											<h3>
												<a href="<?php echo get_term_link($term_id, 'merchant'); ?>"><?php echo get_field('outlet_brandname', $term_meta); ?> </a>
											</h3>
										</div>
										<div class="col-md-3 col-xs-12">
											<?php if (isset($matched_promotion_results[$key]["promotion"]) && $matched_promotion_results[$key]["promotion"] == true) { ?>
												<span class="product_promotion">promotion</span>
											<?php } ?>
										</div>
										<div class="col-md-12 col-xs-12">
											<p class="outlet_address"><?php echo get_field('outlet_address', $term_meta); ?> Singapore <?php echo get_field('outlet_postalcode', $term_meta); ?></p>
										</div>
									</div>
								</div> <!-- end repeatright_title -->
							</div> <!-- end row -->
							<?php
							$check_args = array();
							foreach ($matched_outlet_result as $search_result) {
								$i++;
								$variation_ID = $search_result;
								$product_id = wp_get_post_parent_id($variation_ID);
								$attribute_pa_branch = get_post_meta($variation_ID, 'attribute_pa_branch', true);
								$attribute_pa_advertiser = get_post_meta($variation_ID, 'attribute_pa_advertiser', true);
								$merchant_slug = $key;

								if ($i == 4) {
									echo '<p class="show_more"><a href="' . get_term_link($term_id, "merchant") . '">Show more services</a></p>';
									break;
								}
								if (!in_array($search_result, $check_args)) {
									$check_args[] = $search_result;
							?>
									<div class="row repeatrightprice rightprice_<?php echo $i; ?> <?php if ($i >= 4) {
																										echo 'rightprice_none';
																									} ?>">
										<div class="col-md-4 col-xs-12">
											<a href="<?php echo get_the_permalink($product_id);
														if (!empty($variation_ID)) {
															echo '?varid=' . $variation_ID;
														} ?>" id="product_<?php echo $product_id; ?>">
												<strong>
													<?php
													if (!empty($variation_ID)) {
														echo change_promotion_title(get_the_title($product_id), $variation_ID);
													} else {
														echo get_the_title($product_id);
													}
													?>
												</strong>
											</a>
										</div> <!-- end col-md-4 col-xs-12 -->
										<div class="col-md-3 col-xs-4">
											<span class="duration <?php if (get_post_meta($product_id, 'duration', true)) {
																		echo 'dur_active';
																	} ?>">
												<?php if (get_post_meta($product_id, 'duration', true)) {
													echo get_post_meta($product_id, 'duration', true);
												} ?>
											</span>
										</div> <!-- end col-md-2 col-xs-3 -->
										<div class="col-md-3 col-xs-5">
											<?php
											echo admin_get_variation_price($variation_ID);
											?>
										</div> <!-- end col-md-4 col-xs-6 -->
										<div class="col-md-2 col-xs-3">
											<div class="buy-details">
												<form class="cart ng-pristine ng-valid allpro-duct" action="<?php echo get_term_link($term_id, 'merchant'); ?>" method="post" enctype="multipart/form-data">
													<button type="submit" class="single_add_to_cart_button button alt">BUY</button>
													<input type="hidden" name="add-to-cart" value="<?php echo absint($product_id); ?>" />
													<input type="hidden" name="product_id" value="<?php echo absint($product_id); ?>" />
													<input type="hidden" name="variation_id" class="variation_id" value="<?php echo $variation_ID ?>" />
												</form>
											</div> <!-- end buy-details -->
										</div> <!-- end col-md-2 col-xs-3 -->
									</div> <!-- end row repeatrightprice -->
									<div style="clear:both"></div>
							<?php }
							} ?>
						</div> <!-- end container -->
					</div> <!-- end repeatright -->
					<div style="clear:both"></div>
				</div>
				<?php if ($initial_show_limit == $x) break; ?>




	<?php
			}
		endforeach;
	else :
		if ($cat_name != '') {
			if ($location == 'null' || $location == '') {
				$search_key = "We don't seem to have <b>$cat_name</b> on our platform! We are in the midst of bringing more merchants to the Salon Finder and hopefully we can bring in the salon you are searching for soon!";
			} else {
				$search_key = "We don't seem to have <b>$cat_name</b> in <b>$location</b>";
			}
		} else {
			$search_key = "We don't seem to have any services in <b>$location</b>. We are in the midst of bringing more merchants to the Salon Finder and hopefully we can bring in the salon you are searching for soon!";
		}

		$location = ($location != 'null' && $location != '') ? $location : 'All';

		echo '<div class="no_data">' . $search_key . '</div>';

		$to = get_option('admin_email');

		$title = 'Service ' . $cat_name . ' at Location: ' . $location . ' has returned null';

		$email = '';
		if (is_user_logged_in()) {
			$current_user = wp_get_current_user();
			$email = $current_user->user_email;
		}
		$actual_link = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

		$body = '
    	<!DOCTYPE html>
    	<html>
      	<head>
      		<title>' . $title . '</title>
      	</head>
      	<body>
      		<p>IP address: ' . get_client_ip() . '</p>
      		<p>Keyword: ' . $cat_name . '</p>
      		<p>Timestamp: ' . date('d M Y', time() + 28800) . '</p>
      		<p>Email: ' . $email . '</p>
      		<p>Location: ' . $location . '</p>
      		<p>Type: Service</p>
      		<p>Page url: <a href="' . $_SERVER['HTTP_REFERER'] . '">' . $_SERVER['HTTP_REFERER'] . '</a></p>
      	</body>
    	</html>
  	';

		$headers = array('Content-Type: text/html; charset=UTF-8;');
		// $headers[] = 'Bcc: chesspamungkas@gmail.com';
		// Function to change sender name
		function search_sender_name($original_email_from)
		{
			return 'Daily Vanity Bot';
		}
		add_filter('wp_mail_from_name', 'search_sender_name');
		wp_mail($to, $title, $body, $headers);

		$url = VOUCHERS_API . 'nullsearchresults';
		$json = array(
			'ip_address' => get_client_ip(),
			'keyword'        => $cat_name,
			'timestamp'        => date('c', time() + 28800),
			'wp_user_email'        => $email,
			'postal_location'        => $location,
			'page_url'        => $_SERVER['HTTP_REFERER'],
			'search_type'        => 'Service',
		);

		$ch = curl_init($url);

		$post = http_build_query($post);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		// curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_POST, 1);
		$headers    = array();
		$headers[]  = "Content-Type: application/x-www-form-urlencoded";
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$result = curl_exec($ch);
		curl_close($ch);
	endif;
	?>
</div>

<!-- .search-result -->
<?php if ($total_search_results > 8) : ?>

	<div class="load_outlets">
		<img src="ajax-loading-gif-4-1.gif" alt="ajax" class="loadmomre" />
	</div>
	<a class="search-result-page load-more-outlet" data-result='<?php echo json_encode($all_matched_outlet_results); ?>' data-promotion='<?php echo json_encode($matched_promotion_results); ?>' data-orderby="<?php echo $orderbymore; ?>" data-repeat="<?php echo $x; ?>" data-service="advertise-services" data-search="<?php echo $term_id; ?>" data-keyword="<?php echo $cat_name; ?>" data-paged="<?php echo $paged; ?>" href="#">load more</a>

<?php endif; ?>