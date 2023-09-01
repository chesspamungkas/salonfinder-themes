<div class="search-result container">
	<?php
	$paged = get_query_var('page') ? get_query_var('page') : 1;
	$order = $order == 'asc' ? 'asc' : 'desc';
	$orderby = 'modified';
	$meta_query = array(
		'relation' => 'OR',
		array( // Simple products type
			'key'           => '_sale_price',
			'value'         => 0,
			'compare'       => '>',
			'type'          => 'numeric'
		),
	);

	$query_args = array(
		'posts_per_page'    => 30,
		'post_status'       => 'publish',
		'post_type'         => 'product_variation',
		'orderby' => $orderby,
		'order' => $order,
		'paged' => $paged,
		// 'meta_query' => $meta_query,
		'post__in' => array_merge(array(0), wc_get_product_ids_on_sale())
	);
	$width = 288;
	$height = 160;
	$crop = true;
	$product_query = new WP_Query($query_args);
	global $wpdb;
	if (isset($_GET['debug'])) {
		// echo '<pre>';
		// print_r($product_query->request);
		// echo '</pre>';
		// SELECT SQL_CALC_FOUND_ROWS  f5ATe_posts.ID FROM f5ATe_posts  WHERE 1=1  AND f5ATe_posts.ID IN (0,3855,3859,3864,3875,3878,3882,3885,4795,4798,5901,5907,5915,5922,5924,5926,5930,6503,6506,6507,6512,6537,6742,6743,7164,7169,7171,7175,7176,7178,7179,7181,7182,7183,7185,7186,7187,7188,7404,12966,12969,12972,13086,13087,13088,13089,13090,13223,13226,13229,13232,13235,13238,13590,13591,13592,13595,3854,3858,3863,3874,3877,3881,3884,4794,4797,5895,5897,5902,5909,5913,5912,5919,6488,6497,6495,6498,6530,6722,6728,7149,7152,7158,7159,7160,7167,7173,7398,12965,12968,12971,13085,13222,13225,13228,13231,13234,13237,13589,13594) AND f5ATe_posts.post_type = 'product_variation' AND ((f5ATe_posts.post_status = 'publish'))  ORDER BY f5ATe_posts.post_modified DESC LIMIT 0, 12
		// exit;

	}

	$prev_sale_price = 0;
	$prev_product_id = 0;
	?>

	<form method="post" action="<?php echo home_url($wp->request); ?>" id="product-filter">
		<select name="filter" id="filter" class="postform">
			<option selected="selected">Latest</option>
			<option value="_sale_price">Lowest price to highest</option>
			<option value="_sale_price-highest">Highest price to lowest</option>
			<option value="perc_discount_to_lowest">Highest % discount to lowest</option>
			<option value="price_discount_to_lowest">Highest $ discount to lowest</option>
			<option value="most_popular">Most popular</option>
		</select>
		<input name="slug" id="slug" type="hidden" value="<?php echo get_queried_object()->post_name; ?>">
	</form>

	<div class="grid_services" id="results-wrap">
		<?php if ($product_query->found_posts) : ?>
			<?php foreach ($product_query->posts as $post) : ?>
				<?php
				$product_id = $post->post_parent;
				$variation_id = $post->ID;
				$duration = get_post_meta($product_id, 'duration', true);
				$price = get_post_meta($variation_id, '_price', true);
				$regular_price = get_post_meta($variation_id, '_regular_price', true);
				$price = $regular_price ? $regular_price : $price;
				$sale_price = get_post_meta($variation_id, '_sale_price', true);
				$discount = $sale_price ? $sale_price / $price : 0;
				$percentage = - (100 - (round($discount, 2) * 100)) . '%';
				//				if(get_post_status($product_id) !== 'publish') continue;
				//				if($prev_product_id == $product_id) {
				//					if($sale_price == $prev_sale_price){
				//						continue;
				//					}
				//				}else{
				//					$prev_product_id = $product_id;
				//				}

				if (has_post_thumbnail($product_id)) {
					$large_image_url = wp_get_attachment_image_src(get_post_thumbnail_id($product_id), 'custom-featured-image');
					$image_alt = get_the_title(get_post_thumbnail_id($product_id));
					$large_image_url = custom_image_resize($large_image_url[0], $width, $height, $crop, $single = true);
				}
				$prev_sale_price = $sale_price;
				$merchant = get_term_by('slug', get_post_meta($variation_id, 'attribute_pa_advertiser', true), 'merchant');
				?>
				<div class="repeatdiv repeat" number="<?php echo $variation_id; ?>" cuspage="0">
					<!-- Left Side -->
					<div class="repeatleft">
						<a href="<?php the_permalink($product_id); ?>?varid=<?php echo $variation_id; ?>" title="">
							<img class="blog-image" alt="<?php echo $image_alt; ?>" src="<?php echo $large_image_url; ?>">
						</a>
					</div>

					<!-- Right Side -->
					<div class="repeatright">
						<div class="container">
							<div class="row repeatrightprice rightprice_<?php echo $variation_id; ?>">
								<div class="col-md-12 col-xs-12 descr">
									<div class="descr-in">
										<h5 class="author"><strong><?php echo $merchant->name ?></strong></h5>
										<a href="<?php the_permalink($product_id); ?>?varid=<?php echo $variation_id; ?>" id="product_<?php echo $product_id; ?>">
											<?php echo get_the_title($product_id); ?>
										</a>
										<!-- <p><?php echo get_the_title($variation_id); ?></p> -->
									</div>
								</div> <!-- end col-md-4 col-xs-12 -->
								<!-- <div class="col-md-12 col-xs-12">
									<span class="duration <?php echo $duration ? 'dur_active' : ''; ?>">
										<?php echo $duration ? $duration : ''; ?>
									</span>
								</div> --> <!-- end col-md-2 col-xs-3 -->
								<div class="price-section">
									<div class="col-md-6 col-xs-6">
										<?php if ($discount) : ?>
											<span class="discount"><?php echo $percentage; ?></span>
										<?php endif; ?>
									</div>
									<div class="col-md-6 col-xs-6 text-right">
										<?php echo admin_get_variation_price($variation_id); ?>
									</div>
								</div>
								<div class="col-md-12 col-xs-12">
									<div class="buy-details">
										<form class="cart ng-pristine ng-valid allpro-duct" target="_blank" action="<?php // echo get_term_link($term_id, 'merchant'); 
																													?>" method="post" enctype="multipart/form-data">
											<a href="<?php the_permalink($product_id); ?>?varid=<?php echo $variation_id; ?>" target="_blank" style="text-align: center !important;" class="single_add_to_cart_button button alt" id="product_<?php echo $product_id; ?>">VIEW DETAILS</a>
										</form>
									</div> <!-- end buy-details -->
								</div> <!-- end col-md-2 col-xs-3 -->
							</div> <!-- end row repeatrightprice -->
						</div> <!-- end container -->
					</div> <!-- end repeatright -->
					<div style="clear:both"></div>
				</div> <!-- end repeatdiv -->
			<?php endforeach; ?>
		<?php endif; ?>
		<?php
		if ($product_query->found_posts <= 0) {
			$cat_name = '';
			$location = '';
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

			$headers = array('Content-Type: text/html; charset=UTF-8; Bcc: mail@yourdomain.com');
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
				'keyword' => $cat_name,
				'timestamp' => date('c', time() + 28800),
				'wp_user_email' => $email,
				'postal_location' => $location,
				'page_url' => $_SERVER['HTTP_REFERER'],
				'search_type' => 'Service',
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
		}
		?>
	</div><!-- end grid service -->
</div>

<!-- .search-result -->
<?php if ($product_query->found_posts > 8) : ?>

	<div class="load_outlets">
		<img src="loading-gif-4-1.gif" alt="ajax" class="loadmore" />
	</div>
	<a class="search-result-page load-more-outlet" data-result='<?php echo isset($all_matched_outlet_results) ? json_encode($all_matched_outlet_results) : ''; ?>' data-promotion='<?php echo isset($matched_promotion_results) ? json_encode($matched_promotion_results) : ''; ?>' data-orderby="<?php echo isset($orderbymore) ? $orderbymore : ''; ?>" data-repeat="<?php echo isset($x) ? $x : 0; ?>" data-service="advertise-services" data-search="<?php echo $term_id; ?>" data-keyword="<?php echo $cat_name; ?>" data-paged="<?php echo $paged; ?>" href="#">load more</a>

<?php endif; ?>