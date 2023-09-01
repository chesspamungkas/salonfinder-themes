<?php
/********
 * Copy all the code in this file and put it in your functions.php file located in
 * wp-content/themes/your-theme-name/
 ********/

add_action( 'template_redirect', 'wc_custom_redirect_after_purchase' );
function wc_custom_redirect_after_purchase() {
    global $wp;
	
	$current_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	
	if( is_page() && $current_url != site_url('/profile/login/') ) {
		setcookie("redirect_url", $current_url, time() + (86400 * 30), "/");
	}
	
    if( is_checkout() && !is_user_logged_in()){
		$redirect  = site_url('/profile/login/');
		wp_redirect( $redirect );
        exit;
	}
	if ( is_checkout() && ! empty( $wp->query_vars['order-received'] ) ) {
        $order_id  = absint( $wp->query_vars['order-received'] );
        $order_key = wc_clean( $_GET['key'] );

        
        $redirect  = site_url('/confirmation/');
        $redirect .= get_option( 'permalink_structure' ) === '' ? '&' : '?';
        $redirect .= 'order=' . $order_id . '&key=' . $order_key;

        wp_redirect( $redirect );
        exit;
    }
}

add_filter( 'the_content', 'wc_custom_thankyou' );
function wc_custom_thankyou( $content ) {
    // Check if is the correct page
    if ( ! is_page( 'confirmation' ) ) {
        return $content;
    }

    // check if the order ID exists
    if ( ! isset( $_GET['key'] ) || ! isset( $_GET['order'] ) ) {
        return $content;
	}
	
	$order_id  = apply_filters( 'woocommerce_thankyou_order_id', absint( $_GET['order'] ) );
    $order_key = apply_filters( 'woocommerce_thankyou_order_key', empty( $_GET['key'] ) ? '' : wc_clean( $_GET['key'] ) );
	$order     = wc_get_order( $order_id );
	
    if ( $order->id != $order_id || $order->order_key != $order_key ) {
        return $content;
    }

    ob_start();

    // Check that the order is valid
    if ( ! $order ) {
        // The order can't be returned by WooCommerce - Just say thank you
        ?><p><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. Your order has been received.', 'woocommerce' ), null ); ?></p><?php
    } else {
        if ( $order->has_status( 'failed' ) ) {
            // Order failed - Print error messages and ask to pay again

            /**
             * @hooked wc_custom_thankyou_failed - 10
             */
            do_action( 'wc_custom_thankyou_failed', $order );
        } else {
            // The order is successfull - print the complete order review

            /**
             * @hooked wc_custom_thankyou_header - 10
             * @hooked wc_custom_thankyou_table - 20
             * @hooked wc_custom_thankyou_customer_details - 30
             */
            do_action( 'wc_custom_thankyou_successful', $order );
        }
    }

    $content .= ob_get_contents();
    ob_end_clean();

    return $content;
}

add_action( 'wc_custom_thankyou_failed', 'wc_custom_thankyou_failed', 10 );
function wc_custom_thankyou_failed( $order ) {
    wc_get_template( 'customization/custom-thankyou/failed.php', array( 'order' => $order ) );
}

add_action( 'wc_custom_thankyou_successful', 'wc_custom_thankyou_header', 10 );
function wc_custom_thankyou_header( $order ) {
    wc_get_template( 'customization/custom-thankyou/header.php', array( 'order' => $order ) );
}

// add_action( 'wc_custom_thankyou_successful', 'wc_custom_thankyou_table', 20 );
// function wc_custom_thankyou_table( $order ) {
//     wc_get_template( 'customization/custom-thankyou/table.php', array( 'order' => $order ) );
// }

// add_action( 'wc_custom_thankyou_successful', 'wc_custom_thankyou_customer_details', 30 );
// function wc_custom_thankyou_customer_details( $order ) {
//     wc_get_template( 'customization/custom-thankyou/customer-details.php', array( 'order' => $order ) );
// }

function more_outlet_ajax(){
	global $wpdb;
	 $search_term = true;
	 $search_key = $_REQUEST['catName'];
	 $keyword = $_REQUEST['keyword'];
	 $data_service= $_REQUEST['dataService'];
	 $paged = absint($_REQUEST['nextPage']) - 1;
	 $repeatdiv = $_REQUEST['repeatDiv'];
	 $result = $_REQUEST['result'];
	 $search_postal_code = false;
	 
	 /*
	 if($data_service =="advertise"){
		$postal_match_ids = array();
			if($search_key!=""){
					$post_meta_ids=$wpdb->get_col("SELECT ID FROM f5ATe_posts INNER JOIN f5ATe_postmeta   ON ID = post_id WHERE meta_value LIKE '%{$search_key}%' AND (meta_key = 'attribute_pa_branch' OR meta_key = 'attribute_pa_advertiser') AND post_status = 'publish' AND post_type='product'");
					 $term_tax_ids = $wpdb->get_col("SELECT object_id FROM  f5ATe_term_relationships wr 
						  INNER JOIN f5ATe_term_taxonomy wtx ON wr.term_taxonomy_id = wtx.term_taxonomy_id
						  INNER JOIN f5ATe_terms wt ON wtx.term_id = wt.term_id
						  WHERE  wt.name LIKE '%{$search_key}%' AND wtx.taxonomy='product_cat'"
					);
						$term_meta_ids = $wpdb->get_col("SELECT object_id FROM  f5ATe_term_relationships wr 
						  INNER JOIN f5ATe_term_taxonomy wtx ON wr.term_taxonomy_id = wtx.term_taxonomy_id
						  INNER JOIN f5ATe_termmeta wtm ON wtx.term_id = wtm.term_id
						  WHERE  wtm.meta_value LIKE '%{$search_key}%' AND wtx.taxonomy='product_cat'"
					);
					$tax_prd_ids_in = array_unique(array_merge ($term_tax_ids,$term_meta_ids,$post_meta_ids));
					if(!empty($tax_prd_ids_in)){
						$search_prd_ids_in = $wpdb->get_col("SELECT ID FROM  f5ATe_posts WHERE meta_value LIKE '%".$post_id_result."%' AND  post_parent IN (".$tax_prd_ids_in.") AND post_status='publish' AND post_type='product_variation'");
					}
					$post_ids = $wpdb->get_col("SELECT DISTINCT slug FROM `f5ATe_terms` WHERE term_id IN (SELECT DISTINCT REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(option_name,'merchant_',''),'_outlet_postalcode',''),'_outlet_description',''),'_outlet_address',''),'_outlet_brandname',''),'_advertiser_description','') FROM `f5ATe_options` WHERE (option_name LIKE 'merchant%_outlet_postalcode' OR option_name LIKE 'merchant%_advertiser_description' OR option_name LIKE 'merchant%_outlet_description' OR option_name LIKE 'merchant%_outlet_address' OR option_name LIKE 'merchant%_outlet_brandname' ) AND option_value LIKE '%{$search_key}%')");
				$outlet_match_ids = array();
				foreach($post_ids as $post_id_result){
					$outlet_match_id =$wpdb->get_col("SELECT DISTINCT ID FROM  f5ATe_posts INNER JOIN `f5ATe_postmeta` on ID = post_id   WHERE meta_value LIKE '%".$post_id_result."%' AND  meta_key = 'attribute_pa_branch' AND post_status='publish' AND post_type='product_variation'");
					if(empty($outlet_match_ids)){
						$outlet_match_ids=$outlet_match_id;
					} else {
						if($outlet_match_id)$outlet_match_ids = array_merge($outlet_match_ids,$outlet_match_id);
					}
								
				}
				$search_salon_ids =  array_merge($outlet_match_ids,$tax_prd_ids_in);
			}
			
			if(isset($_REQUEST["postcode"]) && $_REQUEST["postcode"]!=""){
				$search_postal_code = true;
				 $postcode = $_REQUEST["postcode"];
				 $trim_postcode =substr($postcode,0,2);
				 $postal_code_results = $wpdb->get_col("SELECT slug FROM `f5ATe_terms` WHERE term_id IN (SELECT REPLACE(REPLACE(option_name,'merchant_',''),'_outlet_postalcode','') FROM `f5ATe_options` WHERE option_name LIKE 'merchant%_outlet_postalcode' AND option_value LIKE '".$trim_postcode."%')");
					foreach($postal_code_results as $postal_code_result){
						$postal_match_id =$wpdb->get_col("SELECT DISTINCT ID FROM  f5ATe_posts INNER JOIN `f5ATe_postmeta`  on ID = post_id   WHERE meta_value LIKE '%".$postal_code_result."%' AND  meta_key = 'attribute_pa_branch' AND post_status='publish' AND post_type='product_variation'");
						if(empty($postal_match_ids)){
							$postal_match_ids=$postal_match_id;
						} else {
							if($postal_match_id)$postal_match_ids = array_merge($postal_match_ids,$postal_match_id);
						}
						
					}
				 
			}
			if( !empty($postal_match_ids) && $search_key!="" && !empty($search_salon_ids) ) {
				$postal_match_ids =array_unique($postal_match_ids);
				$prd_ids_in =array_intersect($postal_match_ids,$search_salon_ids);
			} elseif(!empty($postal_match_ids) && $search_key=="" ) {
				$prd_ids_in =$postal_match_ids;
			} else {
				$prd_ids_in =$search_salon_ids;
			}
			$merchant_prd_in = implode(",",$prd_ids_in);
			$select_query = "SELECT * ";
			$from_query =" FROM `f5ATe_posts` fp";
			$search_query_where = " WHERE post_type = 'product_variation' AND post_status = 'publish' AND ID IN (".$merchant_prd_in.") AND fpm1.meta_key = 'attribute_pa_branch' ";
			$inner_join = " INNER JOIN f5ATe_postmeta fpm1 ON fp.ID = fpm1.post_id";
			$order_by = " ORDER BY fpm1.meta_value, post_title ASC";
			
			if( isset($_REQUEST["orderby"]) && $_REQUEST["orderby"]!="" ) {
				switch ($_REQUEST["orderby"]) {
				 case 'price':
					$inner_join .=" INNER JOIN f5ATe_postmeta fpm2 ON fp.ID = fpm2.post_id INNER JOIN f5ATe_postmeta fpm3 ON fp.ID = fpm3.post_id";
					$search_query_where .=" AND fpm2.meta_key = '_price' AND fpm3.meta_key = '_sale_price'";
					$order_by = " ORDER BY product_price ASC, fpm1.meta_value, post_title ASC";
					$select_query .=",if(fpm3.meta_value+0>0, fpm3.meta_value+0 , fpm2.meta_value+0) AS product_price";
					break;
				  case 'price-desc':
					$inner_join .=" INNER JOIN f5ATe_postmeta fpm2 ON fp.ID = fpm2.post_id INNER JOIN f5ATe_postmeta fpm3 ON fp.ID = fpm3.post_id";
					$search_query_where .=" AND fpm2.meta_key = '_price' AND fpm3.meta_key = '_sale_price'";
					$order_by = " ORDER BY product_price DESC, fpm1.meta_value, post_title ASC";
					$select_query .=",if(fpm3.meta_value+0>0, fpm3.meta_value+0 , fpm2.meta_value+0) AS product_price";
					break;
				case 'promotional':
				  $inner_join .=" INNER JOIN f5ATe_postmeta fpm2 ON fp.ID = fpm2.post_id";
				  $search_query_where .=" AND fpm2.meta_key = '_sale_price'";
				  $order_by = " ORDER BY fpm2.meta_value DESC, fpm1.meta_value, post_title ASC";
				  break;
				}
			}
	 } else {
		 // $paged = get_query_var('page') ? get_query_var('page') : 1;
		$term_id = $keyword;
		$terms = $wpdb->get_col("SELECT * FROM `f5ATe_terms` WHERE `name` LIKE '%$term_id%'");
		  if( $terms ) {
			  $terms = implode(",", $terms);
			  $search_term = "AND ftr.term_taxonomy_id IN ($terms)";
		  }
		  else {
			  $search_term = "AND ftr.term_taxonomy_id IN (9999999999999)";
		}
		$select_query = "SELECT * ";
		$from_query =" FROM `f5ATe_posts` fp";
		$search_query_where = " WHERE post_type = 'product_variation' AND post_status = 'publish' $search_term AND fpm1.meta_key = 'attribute_pa_branch'";
		$inner_join = " INNER JOIN  `f5ATe_term_relationships` ftr on fp.post_parent = ftr.object_id 
		INNER JOIN f5ATe_postmeta fpm1 ON fp.ID = fpm1.post_id";
		$order_by = " ORDER BY fpm1.meta_value, post_title ASC";
		if( isset($_REQUEST["orderby"]) && $_REQUEST["orderby"]!="" ) {
					switch ($_REQUEST["orderby"]) {
					 case 'price':
						$inner_join .=" INNER JOIN f5ATe_postmeta fpm2 ON fp.ID = fpm2.post_id INNER JOIN f5ATe_postmeta fpm3 ON fp.ID = fpm3.post_id";
					$search_query_where .=" AND fpm2.meta_key = '_price' AND fpm3.meta_key = '_sale_price'";
					$order_by = " ORDER BY product_price ASC, fpm1.meta_value, post_title ASC";
					$select_query .=",if(fpm3.meta_value+0>0, fpm3.meta_value+0 , fpm2.meta_value+0) AS product_price";
						break;
					  case 'price-desc':
						$inner_join .=" INNER JOIN f5ATe_postmeta fpm2 ON fp.ID = fpm2.post_id INNER JOIN f5ATe_postmeta fpm3 ON fp.ID = fpm3.post_id";
					$search_query_where .=" AND fpm2.meta_key = '_price' AND fpm3.meta_key = '_sale_price'";
					$order_by = " ORDER BY product_price DESC, fpm1.meta_value, post_title ASC";
					$select_query .=",if(fpm3.meta_value+0>0, fpm3.meta_value+0 , fpm2.meta_value+0) AS product_price";
						break;
					case 'promotional':
					  $inner_join .=" INNER JOIN f5ATe_postmeta fpm2 ON fp.ID = fpm2.post_id";
					  $search_query_where .=" AND fpm2.meta_key = '_sale_price'";
					  $order_by = " ORDER BY fpm2.meta_value DESC, fpm1.meta_value, post_title ASC";
					  break;
					}
		}
	 }
#$search_results = $wpdb->get_results($search_query.$inner_join.$search_query_where.$order_by);
// var_dump($select_query.$from_query.$inner_join.$search_query_where.$order_by);
$search_results = $wpdb->get_results($select_query.$from_query.$inner_join.$search_query_where.$order_by);

$matched_promotion_results = array();
foreach($search_results as $search_result):
	$product_id = $search_result->post_parent;
	if(get_post_status( $product_id )!="publish")continue;
	$variation_ID = $search_result->ID;
	$attribute_pa_branch =  get_post_meta($variation_ID, 'attribute_pa_branch', true);
	$attribute_pa_advertiser = get_post_meta($variation_ID, 'attribute_pa_advertiser', true);
	$merchant_slug = trim( str_replace_first( $attribute_pa_advertiser, "", $attribute_pa_branch), "-");
	$from = get_post_meta( $variation_ID, '_sale_price_dates_from', true );
	$to = get_post_meta( $variation_ID, '_sale_price_dates_to', true );
	$current = time();
	
		if( has_term( $merchant_slug, 'merchant',$product_id ) || has_term( $attribute_pa_advertiser, 'merchant',$product_id )  ) {
    		
			if($search_postal_code){
				 
				 $term = get_term_by( 'slug', $merchant_slug, 'merchant' );
				 $term_id = $term->term_id;
				 $term_meta = $term->taxonomy . '_' . $term_id;
				 $output_postal_code  =substr(get_field('outlet_postalcode', $term_meta),0,2);
				 if( $trim_postcode ==$output_postal_code ) {
						$all_matched_outlet_results[$merchant_slug][]=$variation_ID;
						if ( get_post_meta($variation_ID, '_sale_price', true) && $current >= $from && $current <= $to ){
							$matched_promotion_results[$merchant_slug]["promotion"] = true;
						} 
				 }
			 } else {
				$all_matched_outlet_results[$merchant_slug][]=$variation_ID;
				if ( get_post_meta($variation_ID, '_sale_price', true) && $current >= $from && $current <= $to ){
					$matched_promotion_results[$merchant_slug]["promotion"] = true;
				}
			 }
		}
endforeach;

foreach( $all_matched_outlet_results as $key=>$matched_outlet_result ){
	$term = get_term_by( 'slug', $key, 'merchant' );

	if( !isset($term->term_id) ) {
		unset( $all_matched_outlet_results[$key] );
	}
}


*/

$all_matched_outlet_results = $result;
$matched_promotion_results = $_REQUEST['promotion'];

$total_search_results = count($all_matched_outlet_results);
#3,8,13
$initial_show_limit = 8 + ($paged-2)*5;
$final_show_limit = 8 + ($paged-1)*5;

$start = 8 + 5*($paged-1) + 1;
$to = 8 + 5*($paged-1) + 5;

$staring_array = ($paged-1)*5 - ($initial_show_limit-1) ;
$limit_array = ($initial_show_limit-1) + ($paged-1)*5;
#$matched_outlet_results = array_slice($all_matched_outlet_results, $staring_array, $limit_array);
ob_start();

			if( !empty($all_matched_outlet_results)):
							$x = $repeatdiv;
							$k=0;
							$outlet_array = array();
								foreach( $all_matched_outlet_results as $key=>$matched_outlet_result ):
								$x++;
								$k++;
									if( $k < $start ) continue;
									$term = get_term_by( 'slug', $key, 'merchant' );
									$parent = get_term_by( 'id', $term->parent, 'merchant' );
									$term_id = $term->term_id;
									$term_meta = $term->taxonomy . '_' . $term_id;
									$parent_meta = $parent->taxonomy . '_' . $parent->term_id;
									$ad_alt = get_field('outlet_image_alt', $term_meta);
									$i=0 ?>
									  <div class="repeatdiv repeat<?php echo $x; ?>" number="<?php echo $x ;?>"<?php if ( $x == 1 ) { ?> cuspage="0"<?php } ?>>										   <div class="repeatleft">
												<?php $ad_logo = get_field('outlet_featured_image', $term_meta);  ?>
												<a href="<?php echo get_term_link($term_id, 'merchant'); ?>" class="" title=""><img src="<?php echo isset($ad_logo["url"]) ? $ad_logo["url"] : $ad_logo; ?>" alt="<?php echo $ad_alt; ?>" /></a>
											  </div>
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
																	<?php  if ( isset($matched_promotion_results[$key]["promotion"]) && $matched_promotion_results[$key]["promotion"]==true) { ?>
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
								foreach( $matched_outlet_result as $search_result ){
									$x++;
									$i++;
									$variation_ID = $search_result;
									$product_id = wp_get_post_parent_id( $variation_ID );
									$attribute_pa_branch = get_post_meta($variation_ID, 'attribute_pa_branch', true);
									$attribute_pa_advertiser = get_post_meta($variation_ID, 'attribute_pa_advertiser', true);
									$merchant_slug = $key;
									
									if ( $i == 4 ) {
										?>
										<p class="show_more"><a href="<?php echo get_term_link($term_id, 'merchant'); ?>">Show more services</a></p>
										<?php                            
										break;
									 }
									 if( !in_array( $search_result, $check_args ) ) {
										$check_args[] = $search_result;
										?>
										<div class="row repeatrightprice rightprice_<?php echo $i; ?> <?php if ( $i >= 4 ) { echo 'rightprice_none'; } ?>">	   <div class="col-md-4 col-xs-12">	
												<a href="<?php echo get_the_permalink($product_id); if (!empty($variation_ID)) { echo '?varid='.$variation_ID; } ?>" id="product_<?php echo $product_id; ?>">
													<?php if (!empty($variation_ID))  {echo change_promotion_title( get_the_title($product_id), $variation_ID); } else { echo  get_the_title($product_id);} ?>
												</a>
											</div> <!-- end col-md-4 col-xs-12 -->
											<div class="col-md-3 col-xs-4">
												  <span class="duration <?php if ( get_post_meta( $product_id, 'duration', true ) ) { echo 'dur_active'; } ?>"><?php if ( get_post_meta( $product_id, 'duration', true ) ) { echo get_post_meta( $product_id, 'duration', true ); } ?></span>
											</div> <!-- end col-md-2 col-xs-3 -->
											<div class="col-md-3 col-xs-5">
												<?php
													// if ( get_post_meta( $variation_ID, '_sale_price', true ) ) {
														// echo '<strong><span class="regular_price">S$' . number_format( get_post_meta( $variation_ID, '_regular_price', true ), 2 ) . '</span></strong>';
														// echo '&nbsp;&nbsp;<strong><span class="sale_price">S$' . number_format( get_post_meta( $variation_ID, '_sale_price', true ), 2 ) . '</strong>';
													// } else {
														// if ( get_post_meta( $variation_ID, '_regular_price', true ) ) {
																// echo '<strong><span style="color:#000">S$'. number_format(get_post_meta( $variation_ID, '_regular_price', true ), 2 ) . '</span></strong>';
														// } else {
																// echo '<strong><span style="color:#000">S$' . number_format( get_post_meta( $variation_ID, '_price', true ), 2 ) . '</span></strong>';
														// }
													// }
													echo admin_get_variation_price( $variation_ID );
												?>
											</div> <!-- end col-md-4 col-xs-6 -->
											<div class="col-md-2 col-xs-3">
												<div class="buy-details">
													<form class="cart ng-pristine ng-valid allpro-duct" action="<?php echo get_term_link($term_id, 'merchant'); ?>" method="post" enctype="multipart/form-data">
														<button type="submit" class="single_add_to_cart_button button alt">BUY</button>
														<input type="hidden" name="add-to-cart" value="<?php echo absint( $product_id ); ?>" />
														<input type="hidden" name="product_id" value="<?php echo absint( $product_id ); ?>" />
														<input type="hidden" name="variation_id" class="variation_id" value="<?php echo $variation_ID ?>" />
													</form>
												</div> <!-- end buy-details -->
											</div> <!-- end col-md-2 col-xs-3 -->
										</div> <!-- end row repeatrightprice -->
										<div style="clear:both"></div>
							<?php 	
								} } ?>
									</div> <!-- end container -->
								</div> <!-- end repeatright -->
								<div style="clear:both"></div>
							</div> <!-- end repeatdiv -->
								<?php if( $k == $to ) break; ?>
						<?php endforeach;
						endif;

				 $output = ob_get_contents();

				 ob_end_clean();
				 if( $total_search_results > $k ) $load_more = true;
				 else $load_more = false;
				 if($output) {
        			echo json_encode(array('success' => true, 'result' => $output,'load_more'=>$load_more));
    			} else {
        			echo json_encode(array('success' => false, 'message' => 'Not found!','load_more'=>$load_more));
    			}
			     exit;
 }
add_action( 'wp_ajax_more_outlet_ajax', 'more_outlet_ajax' );
add_action( 'wp_ajax_nopriv_more_outlet_ajax', 'more_outlet_ajax' );

add_action( 'init', 'woocommerce_clear_cart_url' );
function woocommerce_clear_cart_url() {
  global $woocommerce;

    if ( isset( $_GET['empty-cart'] ) ) { 
        $woocommerce->cart->empty_cart(); 
    }
	remove_filter('authenticate', 'wp_authenticate_username_password', 20,3);
}

//update user meta after checkout
add_action('woocommerce_checkout_update_user_meta', 'salon_custom_checkout_field_update_user_meta',10,2);

function salon_custom_checkout_field_update_user_meta( $user_id, $fields ) {
	if ($fields["wc_billing_field_7149"]!="") update_user_meta( $user_id, "gender", esc_attr($fields["wc_billing_field_7149"]));
	if ($fields["wc_billing_field_2495"]!="") update_user_meta( $user_id, "dob", esc_attr($fields["wc_billing_field_2495"]));
	$user_info = get_userdata($user_id);
	if( $user_info->user_email != esc_attr($fields["billing_email"]) ){
		$args = array(
				'ID'         => $user_id,
				'user_email' => esc_attr($fields["billing_email"])
			);
		wp_update_user( $args );
	}
}

add_action('woocommerce_checkout_process', 'validate_user_email_phone');
function validate_user_email_phone(){
	global $current_user,$wpdb;
	get_currentuserinfo();
	$biling_email = $_REQUEST['billing_email'];
	$biling_phone= $_REQUEST['billing_phone'];
	$curr_user_phone = get_user_meta( $current_user->ID, 'billing_phone', true );
	
	if($curr_user_phone!=$biling_phone){
		$existing_phone = $wpdb->get_var("SELECT count(meta_value) from f5ATe_usermeta WHERE meta_key ='biling_phone' AND meta_value='".$biling_phone."' AND user_id !=".$current_user->ID);
		if($existing_phone>0){
			wc_add_notice(__('The phone number exist. Please choose another one.'), 'error');
		}
	}
	#file_put_contents('woocommerce_checkout_process.txt', "\woocommerce_checkout_process /n/r<br />" . print_r(array($_REQUEST), true));
	#wc_add_notice(__("SELECT user_email from f5ATe_users WHERE user_email ='".$biling_email."' AND ID !=".$current_user->ID), 'error');
	if( $current_user->user_email != $biling_email ){
		$existing_email = $wpdb->get_var("SELECT count(user_email) from f5ATe_users WHERE user_email ='".$biling_email."' AND ID !=".$current_user->ID);
		if($existing_email>0){
			wc_add_notice(__('The email already exists. Please choose another one.'), 'error');
		}
	}
	 

}

#add_filter("fbl/js_auth_data",'get_fb_user_extra_details');
function get_fb_user_extra_details($fb_query_args){
	$fb_query_args['fields']='id,first_name,last_name,email,link,gender, birthday';
	return $fb_query_args;
}

function salon_email_login_authenticate( $user, $username, $password ) {
	if ( is_a( $user, 'WP_User' ) )
		return $user;

	if ( !empty( $username ) ) {
		$username = str_replace( '&', '&amp;', stripslashes( $username ) );
		$user = get_user_by( 'email', $username );
		if ( isset( $user, $user->user_login, $user->user_status ) && 0 == (int) $user->user_status )
			$username = $user->user_login;
	}

	return wp_authenticate_username_password( null, $username, $password );
}

#add_filter( 'authenticate', 'dr_email_login_authenticate', 20, 3 );
add_filter('authenticate', function($user, $email, $password){

    //Check for empty fields
    if(empty($email) || empty ($password)){        
        //create new error object and add errors to it.
        $error = new WP_Error();

        if(empty($email)){ //No email
            $error->add('empty_username', __('<strong>ERROR</strong>: Email field is empty.'));
        }
        else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){ //Invalid Email
            $error->add('invalid_username', __('<strong>ERROR</strong>: Email is invalid.'));
        }

        if(empty($password)){ //No password
            $error->add('empty_password', __('<strong>ERROR</strong>: Password field is empty.'));
        }

        return $error;
    }

    //Check if user exists in WordPress database
    $user = get_user_by('email', $email);

    //bad email
    if(!$user){
        $error = new WP_Error();
        $error->add('invalid', __('<strong>ERROR</strong>: Either the email or password you entered is invalid.'));
        return $error;
    }
    else{ //check password
        if(!wp_check_password($password, $user->user_pass, $user->ID)){ //bad password
            $error = new WP_Error();
            $error->add('invalid', __('<strong>ERROR</strong>: Either the email or password you entered is invalid.'));
            return $error;
        }else{
            return $user; //passed
        }
    }
}, 20, 3);  

add_filter('gettext', function($text){
    if(in_array($GLOBALS['pagenow'], array('wp-login.php'))){
        if('Username' == $text){
            return 'Email';
        }
    }
    return $text;
}, 20);

 
// Edit term page
function salon_taxonomy_edit_meta_field($term) {
 
	// put the term ID into a variable
	$t_id = $term->term_id;
 
	// retrieve the existing value(s) for this meta field. This returns an array
	$term_meta = get_option( "taxonomy_$t_id" ); 
	$outlet_unique_code = get_term_meta($term->term_id,'outlet_unique_code',true);
	?>
	<tr class="form-field">
	<?php if($term->parent!=0){ ?>
	<th scope="row" valign="top"><label for="outlet_unique_code"><?php _e( 'Outlet Code', 'pippin' ); ?></label></th>
		<td>
			<input disabled="disabled" type="text" name="outlet_unique_code" id="outlet_unique_code" value="<?php echo $outlet_unique_code; ?>">
		</td>
	<?php } else { $outlet_unique_code = get_term_meta($term->term_id,'merchant_unique_code',true);?>
		<th scope="row" valign="top"><label for="merchant_unique_code"><?php _e( 'Advertiser Code', 'pippin' ); ?></label></th>
		<td>
			<input disabled="disabled" type="text" name="merchant_unique_code" id="merchant_unique_code" value="<?php echo $outlet_unique_code; ?>">
		</td>
	<?php } ?>
	</tr>
<?php
}
add_action( 'merchant_edit_form_fields', 'salon_taxonomy_edit_meta_field', 10, 2 );