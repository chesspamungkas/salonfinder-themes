<div class="search-result">
<?php 
/**
 * Changes by Navin, in the flow of the query as follows: Searching for the  name and slug for the salon name and searching for the branch name based on the parent ID of the merchant then combining the merchant-slugs and branch slug.
 */
             global $wpdb;
			 $all_matched_outlet_results = array();
			 $search_postal_code = false;
			 $search_key = $catname;
				 $branch_ids = '';
			 $salon_name = str_replace( "\'", "'", $search_key[0] );
			 $salon_name = str_replace( "&", "&amp;", $salon_name );
			 $salon_branch = str_replace( "\'", "'", $search_key[1] );
			 $salon_branch = str_replace( "&", "&amp;", $salon_branch );
			 $postal_match_ids = array();
			 $post_ids = array();
			$search_salon_term_name_args = array();
			
			 if( sizeof( $search_key ) ){
				if(isset($search_key[1])){
				   $sql = 'SELECT `slug`,`term_id`,`name` FROM `f5ATe_terms` WHERE `name` = %s';
				   $search_salon_term_name = $wpdb->get_results( $wpdb->prepare( $sql, $salon_name ) );
				}else {
					$sql = 'SELECT `slug`,`term_id`,`name` FROM `f5ATe_terms` WHERE `name` LIKE %s';
				   $search_salon_term_name = $wpdb->get_results( $wpdb->prepare( $sql, '%' . $salon_name . '%' ) );
				   $search_salon_term_name = get_terms( 'merchant', array(
					   'hide_empty' => true,
					   'parent' => 0,
					   'name__like'	=> $salon_name
				   ) );
				   foreach( $search_salon_term_name as $term ) {
					   $search_salon_term_name_args[] = $term->term_id;
				   }
				}
				
				if( sizeof( $search_salon_term_name_args ) == 0 ) 		
					$search_salon_term_name_args = array( 0 );
				// $search_branch_term_name = $wpdb->get_col("SELECT `term_id` FROM `f5ATe_term_taxonomy` WHERE `parent` = '" . $search_salon_term_name[0]->term_id . "'");
				if( isset( $search_key[1] ) ){
					if( isset( $search_salon_term_name[0]->term_id ) ) {
						$sql = "SELECT `tr`.`slug`, `tr`.`name`	 FROM `f5ATe_terms` `tr` INNER JOIN `f5ATe_term_taxonomy` `tt` ON `tt`.`term_taxonomy_id` = `tr`.`term_id` WHERE `tt`.`parent` = %s AND `tr`.`name` = %s ORDER BY RAND()";
						$get_branch_term_name = $wpdb->get_results( $wpdb->prepare( $sql, $search_salon_term_name[0]->term_id, esc_sql( $salon_branch ) ) );
					}
				} else {
					$placeholders = implode( ', ', array_fill( 0, count( $search_salon_term_name_args ), '%d' ) );
					// $multiple = implode( ", ", $search_salon_term_name_args );
					$sql = "SELECT `tr`.`slug`, `tr`.`name`	 FROM `f5ATe_terms` `tr` INNER JOIN `f5ATe_term_taxonomy` `tt` ON `tt`.`term_taxonomy_id` = `tr`.`term_id` WHERE `tt`.`parent` IN ( $placeholders ) ORDER BY RAND()";
					$get_branch_term_name = $wpdb->get_results( $wpdb->prepare( $sql, $search_salon_term_name_args ) );
					
				}
				// print_r($search_salon_term_name);
				// print_r($get_branch_term_name);
				$post_meta_ids = array();
					
				if( isset( $search_salon_term_name[0]->slug ) ) {
					$sql = "SELECT ID FROM f5ATe_posts  INNER JOIN f5ATe_postmeta ON ID = post_id WHERE meta_value LIKE %s AND (meta_key = 'attribute_pa_branch' OR meta_key = 'attribute_pa_advertiser') AND post_status = 'publish' ORDER BY RAND()";
					$post_meta_ids = $wpdb->get_col( $wpdb->prepare( $sql, '%'. $search_salon_term_name[0]->slug .'%' ) ); // AND post_type='product'
				}
				 
				$sql = "SELECT object_id FROM f5ATe_term_relationships wr INNER JOIN f5ATe_term_taxonomy wtx ON wr.term_taxonomy_id = wtx.term_taxonomy_id INNER JOIN f5ATe_terms wt ON wtx.term_id = wt.term_id WHERE wt.name LIKE %s AND wtx.taxonomy = 'product_cat' ORDER BY RAND()";

				$term_tax_ids = array();
				$term_tax_ids = $wpdb->get_col( $wpdb->prepare( $sql, "%". $salon_name ."%" ) );

				$term_meta_ids = array();

				$tax_prd_ids_in = array_unique( array_merge( $term_tax_ids, $term_meta_ids, $post_meta_ids ) );
					
				if( sizeof( $tax_prd_ids_in ) ){
					$placeholders = implode( ', ', array_fill( 0, count( $tax_prd_ids_in ), '%d' ) );

					if( $post_id_result ) {
						$sql = "SELECT ID FROM f5ATe_posts WHERE ID LIKE %s AND post_parent IN ( $placeholders ) AND post_status = 'publish' AND post_type = 'product_variation' ORDER BY RAND()";

						$search_prd_ids_in = $wpdb->get_col( $wpdb->prepare( $sql, $post_id_result, $tax_prd_ids_in ) );
					} else {
						$sql = "SELECT ID FROM f5ATe_posts WHERE post_parent IN ( $placeholders ) AND post_status = 'publish' AND post_type = 'product_variation' ORDER BY RAND()";

						$search_prd_ids_in = $wpdb->get_col( $wpdb->prepare( $sql, $tax_prd_ids_in ) );
					}
				}
				
				$slug = '';
				if( isset( $search_key[1] ) ) {
					$slug = $search_salon_term_name[0]->slug."-".$get_branch_term_name[0]->slug;

					$sql = "SELECT DISTINCT slug FROM `f5ATe_terms` `tt` WHERE tt.term_id IN ( SELECT DISTINCT REPLACE( REPLACE( REPLACE( REPLACE( REPLACE( REPLACE( option_name,'merchant_', '' ), '_outlet_postalcode', '' ), '_outlet_description', '' ), '_outlet_address', '' ), '_outlet_brandname', '' ), '_advertiser_description', '' ) FROM `f5ATe_options` WHERE ( option_name LIKE 'merchant%_outlet_postalcode' OR option_name LIKE 'merchant%_advertiser_description' OR option_name LIKE 'merchant%_outlet_description' OR option_name LIKE 'merchant%_outlet_address' OR option_name LIKE 'merchant%_outlet_brandname' ) AND tt.slug = %s ) ORDER BY RAND()";

					$post_ids = $wpdb->get_col( $wpdb->prepare( $sql, $slug ) );
				} else {
					$slug = $get_branch_term_name;
					foreach($get_branch_term_name as $branch_slug){
						foreach( $search_salon_term_name as $salon ) {
							$post_ids[] = $branch_slug->slug;
							// $post_ids[] = $salon->slug."-".$branch_slug->slug;
						}
					}
				}
					
				$outlet_match_ids = array();
				if( is_array( $post_ids ) ) foreach( $post_ids as $post_id_result ){
					$sql = "SELECT DISTINCT ID FROM f5ATe_posts INNER JOIN `f5ATe_postmeta` on ID = post_id WHERE meta_value LIKE %s AND meta_key = 'attribute_pa_branch' AND post_status = 'publish' AND post_type = 'product_variation' ORDER BY RAND()";

					$outlet_match_id = $wpdb->get_col( $wpdb->prepare( $sql, '%'. $post_id_result .'%' ) );
					
					if( empty( $outlet_match_ids ) ){
						$outlet_match_ids = $outlet_match_id;
					} else {
						if( $outlet_match_id ) 
							$outlet_match_ids = array_merge( $outlet_match_ids,$outlet_match_id );
					}
				}

				$search_salon_ids =  array_merge( $outlet_match_ids, $tax_prd_ids_in );
			}
		
			if( isset($_REQUEST["postcode"]) && $_REQUEST["postcode"]!=""){
				
				$search_postal_code = true;
				$postcode = $_REQUEST["postcode"];

				if( !is_numeric( $postcode ) ){
					preg_match_all( '!\d+!', $postcode, $matches );
					if( !empty( $matches ) ) $postcode = $matches[0][0];
				}
				$trim_postcode =substr( $postcode,0,2 );
				
				$sql = "SELECT slug FROM `f5ATe_terms` WHERE term_id IN ( SELECT REPLACE( REPLACE( option_name, 'merchant_', '' ), '_outlet_postalcode', '' ) FROM `f5ATe_options` WHERE option_name LIKE 'merchant%_outlet_postalcode' AND option_value LIKE %s) ORDER BY RAND()";

				$postal_code_results = $wpdb->get_col( $wpdb->prepare( $sql, $trim_postcode.'%' ) );
				
				foreach( $postal_code_results as $postal_code_result ) {
					$sql = "SELECT DISTINCT ID FROM f5ATe_posts INNER JOIN `f5ATe_postmeta` on ID = post_id WHERE meta_value LIKE %s AND meta_key = 'attribute_pa_branch' AND post_status = 'publish' AND post_type = 'product_variation' ORDER BY RAND()";

					$postal_match_id = $wpdb->get_col( $wpdb->prepare( $sql, '%'. $postal_code_result .'%' ) );

					if( empty( $postal_match_ids ) ){
						$postal_match_ids = $postal_match_id;
					} else {
						if( $postal_match_id ) 
							$postal_match_ids = array_merge( $postal_match_ids,$postal_match_id );
					}
				}
			}
			
			$ = isset($_GET['locationempty( $_GET['location'] ) && $_GET['location'] != 'null' ? $_GET['location'] : '';
			
			if( $location ) {
				$search_postal_code = true;
				$location_codes = get_postal_codes($location);
				$postal_code_results = array();
				if( $location_codes ) {
					$terms = get_terms( 'merchant', array(
						'hide_empty' => true
					));
					foreach( $terms as $term ) {
						if( $term->parent != 0 ) {
							$o_address = get_field('outlet_postalcode', 'merchant_'. $term->term_id ); 
							$o_address_sub = substr($o_address ,0,2);
							
							if ( in_array( $o_address_sub, $location_codes ) ) {
								$postal_code_results[] = $term->slug;
							}
						}
					}
					
	
					if( $postal_code_results ) {
						foreach( $postal_code_results as $postal_code_result ) {
							$sql = "SELECT DISTINCT ID FROM f5ATe_posts INNER JOIN `f5ATe_postmeta` on ID = post_id WHERE meta_value LIKE %s AND meta_key = 'attribute_pa_branch' AND post_status = 'publish' AND post_type = 'product_variation' ORDER BY RAND()";

							$postal_match_id =$wpdb->get_col( $wpdb->prepare( $sql, '%'. $postal_code_result .'%' ) );

							if( empty( $postal_match_ids ) ) {
								$postal_match_ids = $postal_match_id;
							} else {
								if( $postal_match_id ) 
									$postal_match_ids = array_merge( $postal_match_ids,$postal_match_id );
							}
						}
					}
					
				}
			}
	
			// var_dump($search_salon_ids);
			
			if( !empty($postal_match_ids) && ( sizeof( $search_key ) && isset( $search_key[0] ) && !empty($search_key[0]) ) && !empty($search_salon_ids) ) {
				$postal_match_ids = array_unique($postal_match_ids);
				$prd_ids_in = array_intersect($postal_match_ids, $search_salon_ids);
			} elseif( !empty($postal_match_ids) && ( sizeof( $search_key ) && isset( $search_key[0] ) && !empty($search_key[0]) ) ) {
				$prd_ids_in = $postal_match_ids;
			} else if( $location ) {
				$prd_ids_in = $postal_match_ids;
			} else {
				$prd_ids_in = $search_salon_ids;
			}
			
			// var_dump($prd_ids_in);
			
			 if( empty($prd_ids_in) ) {
				  //echo '<div class="no_data">No Results Found</div>';
			 } else {
				 	
				$merchant_prd_in = implode( ",", $prd_ids_in );
				$placeholders = implode( ", ", array_fill( 0, count( explode(',',  $merchant_prd_in ) ), '%d' ) );
				$select_query = "SELECT * ";
				$from_query = " FROM `f5ATe_posts` fp";
				$do_not_search = false;
				$search_query_where = " WHERE post_type = 'product_variation' AND post_status = 'publish' AND ID IN ( $placeholders ) AND fpm1.meta_key = 'attribute_pa_branch' ";
				$inner_join = " INNER JOIN f5ATe_postmeta fpm1 ON fp.ID = fpm1.post_id";
				$order_by = " ORDER BY Rand()";
				
				$orderbymore="";
				if( isset( $_REQUEST["orderby"] ) && $_REQUEST["orderby"] != "" ) {
					switch ( $_REQUEST["orderby"] ) {
						case 'price':
							$inner_join .=" INNER JOIN f5ATe_postmeta fpm2 ON fp.ID = fpm2.post_id INNER JOIN f5ATe_postmeta fpm3 ON fp.ID = fpm3.post_id";
							$search_query_where .=" AND fpm2.meta_key = '_price' AND fpm3.meta_key = '_sale_price'";
							$order_by = " ORDER BY Rand()";
							$select_query .=",if(fpm3.meta_value+0>0, fpm3.meta_value+0 , fpm2.meta_value+0) AS product_price";
							$orderbymore="price";
							break;
						case 'price-desc':
							$inner_join .=" INNER JOIN f5ATe_postmeta fpm2 ON fp.ID = fpm2.post_id INNER JOIN f5ATe_postmeta fpm3 ON fp.ID = fpm3.post_id";
							$search_query_where .=" AND fpm2.meta_key = '_price' AND fpm3.meta_key = '_sale_price'";
							$order_by = " ORDER BY Rand()";
							$select_query .=",if(fpm3.meta_value+0>0, fpm3.meta_value+0 , fpm2.meta_value+0) AS product_price";
							$orderbymore="price-desc";
							break;
						case 'promotional':
							$inner_join .=" INNER JOIN f5ATe_postmeta fpm2 ON fp.ID = fpm2.post_id";
							$search_query_where .=" AND fpm2.meta_key = '_sale_price'";
							$order_by = " ORDER BY Rand()";
							$orderbymore = "promotional";
							break;
					}
				} else {
					$inner_join .=" INNER JOIN f5ATe_postmeta fpm2 ON fp.ID = fpm2.post_id";
						$search_query_where .= " AND fpm2.meta_key = '_sale_price'";
						$order_by = " ORDER BY Rand()";
						$orderbymore = "promotional";
				}

				$sql = $select_query . $from_query.$inner_join . $search_query_where . $order_by;
				
				$search_results = $wpdb->get_results( $wpdb->prepare( $sql, explode(',',  $merchant_prd_in ) ) );
						
			}
//var_dump( $search_results );
				
$matched_promotion_results = array();
#$search_postal_code =false;
if ($search_results && (is_array($search_results) || is_object($search_results))){
	foreach($get_branch_term_name as $branch_slug){

foreach($search_results as $search_result):
	$product_id = $search_result->post_parent;
	
	if( get_post_status( $product_id ) !="publish" )continue;
	
	$variation_ID = $search_result->ID;
	$attribute_pa_branch =  get_post_meta($variation_ID, 'attribute_pa_branch', true);
	$attribute_pa_advertiser = get_post_meta($variation_ID, 'attribute_pa_advertiser', true);
	$merchant_slug = $branch_slug->slug;
	$from = get_post_meta( $variation_ID, '_sale_price_dates_from', true );
	$to = get_post_meta( $variation_ID, '_sale_price_dates_to', true );
	
	$current = time();
	// var_dump( $variation_ID );
		////trim(str_replace( $attribute_pa_advertiser,"",$attribute_pa_branch),"-");
	
// var_dump( $attribute_pa_branch );
		#echo $merchant_slug."--".$variation_ID."--".$product_id;
		#echo "<br>";
		//Addde this if loop because, now we are looping based on the merchant name checking whether the this branch exist with variation only then we add it to the results.
	
		if( $merchant_slug == $attribute_pa_branch ){
		// if($attribute_pa_advertiser."-".$merchant_slug === $search_result->meta_value){
			if( has_term( $merchant_slug, 'merchant',$product_id ) && has_term( $attribute_pa_advertiser, 'merchant',$product_id )  ) {
			
			if($search_postal_code){
				 $term = get_term_by( 'slug', $merchant_slug, 'merchant' );
				 $term_id = $term->term_id;
				 $term_meta = $term->taxonomy . '_' . $term_id;
				 $output_postal_code  = substr(get_field('outlet_postalcode', $term_meta), 0, 2);
				
				 if( in_array( $output_postal_code, $location_codes ) ) {
						if( empty($all_matched_outlet_results[$merchant_slug]) )
								$all_matched_outlet_results[$merchant_slug][]=$variation_ID;
						else if( !in_array($variation_ID,$all_matched_outlet_results[$merchant_slug]) ) 
								$all_matched_outlet_results[$merchant_slug][]=$variation_ID;
						if ( get_post_meta($variation_ID, '_sale_price', true) && $current >= $from && $current <= $to ){
							$matched_promotion_results[$merchant_slug]["promotion"] = true;
						} 
				 }
			 } else {
	
				if( empty($all_matched_outlet_results[$merchant_slug]) )
					$all_matched_outlet_results[$merchant_slug][]=$variation_ID;
				else if( !in_array($variation_ID,$all_matched_outlet_results[$merchant_slug]) ) 
					$all_matched_outlet_results[$merchant_slug][]=$variation_ID;
				
				if ( get_post_meta($variation_ID, '_sale_price', true) && $current >= $from && $current <= $to ){
					$matched_promotion_results[$merchant_slug]["promotion"] = true;
				}
			}
		}
	}
endforeach;
	}
}



$total_search_results = count($all_matched_outlet_results);
$paged = get_query_var('page') ? get_query_var('page') : 1;
$initial_show_limit = 8;
$limit_array = ($initial_show_limit-1) + ($paged-1)*5;
#if($total_search_results>0)$matched_outlet_results = array_slice($all_matched_outlet_results, 0, $initial_show_limit);
 ?>
	<?php if($total_search_results != 0){ ?>
	<div class="search_title">
		<div class="search_title_left">
		<?php #if ( function_exists('yoast_breadcrumb') ) {yoast_breadcrumb('<p id="breadcrumbs">','</p>');}?>
		<h2>
			<?php 
			
			if( is_array($catname) && !empty($catname[0]) ) {
				
				
				
				if(isset($catname[1])):
					echo $get_branch_term_name[0]->name;
				else :
					echo str_replace( "\'", "'", $catname[0] );
				endif;
				
				if( $location ) {
					echo ' in ' .$location;
				}
			}
			else {
				echo  $location;
			}
			?>
		</h2>
			<p class="search_number"><?php echo $total_search_results; ?> services found</p>
		</div>
		<?php if($total_search_results>0): ?> <div class="search_title_right"><?php woocommerce_catalog_ordering(); ?></div><?php endif; ?>
		<div class="floatfix"></div>
	</div>
	<?php } ?>



	<?php 
		// print_r($all_matched_outlet_results);
		if( !empty($all_matched_outlet_results)):
			$x = 0;
			$outlet_array = array();
			
				foreach( $all_matched_outlet_results as $key=>$matched_outlet_result ):
				$x++;
					$term = get_term_by( 'slug', $key, 'merchant' );
					if( !isset($term->term_id) ) continue;
					$parent = get_term_by( 'id', $term->parent, 'merchant' );
					
					$term_id = $term->term_id;
					$term_meta = $term->taxonomy . '_' . $term_id;
					$parent_meta = $parent->taxonomy . '_' . $parent->term_id;
					$i=0 ?>
					<div class="repeatdiv repeat<?php echo $x; ?>" number="<?php echo $x ;?>"<?php if ( $x == 1 ) { ?> cuspage="0"<?php } ?>>				
						<div class="repeatleft">
							<?php $ad_logo = get_field('outlet_featured_image', $term_meta); ?>
							<?php $ad_alt = get_field('outlet_image_alt', $term_meta); ?>
										<a href="<?php echo get_term_link($term_id, 'merchant'); ?>" class="" title=""><img src="<?php echo $ad_logo ?>" alt="<?php echo $ad_alt ?>" /></a>
							</div>
					<div class="repeatright">
						<div class="container">
							<div class="row">
								<div class="repeatright_title col-md-12 col-sm-12">
									<div class="row">
										<div class="col-md-9 col-xs-12">
											<h3>
												<a href="<?php echo get_term_link($term_id, 'merchant'); ?>"><?php echo get_field('outlet_brandname', $term_meta); ?></a>
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
				foreach( $matched_outlet_result as $search_result ){
					$i++;
					$variation_ID = $search_result;

					$product_id = wp_get_post_parent_id( $variation_ID );
					$attribute_pa_branch = get_post_meta($variation_ID, 'attribute_pa_branch', true);
					$attribute_pa_advertiser = get_post_meta($variation_ID, 'attribute_pa_advertiser', true);
					$merchant_slug = $key;

					if ( $i == 4 ) {
						echo '<p class="show_more"><a href="'. get_term_link($term_id, 'merchant') .'">Show more services</a></p>';
						break;
					}
				?>
					<div class="row repeatrightprice rightprice_<?php echo $i; ?> <?php if ( $i >= 4 ) { echo 'rightprice_none'; } ?>">
						<div class="col-md-4 col-xs-12">	
							<a href="<?php echo get_the_permalink($product_id); ?>?varid=<?php echo $variation_ID; ?>" id="product_<?php echo $product_id; ?>"><?php $title= get_the_title($product_id);echo change_promotion_title($title, $variation_ID); ?></a>
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
				<?php } ?>
				</div> <!-- end container -->
			</div> <!-- end repeatright -->
			<div style="clear:both"></div>
		</div>  <!-- end repeatdiv -->
				<?php if($initial_show_limit==$x)break; ?>
		<?php endforeach;
		else:
		
			if( $salon_name != '' ) {
				if( $location == 'null' || $location == '' ) {
					$keysearch = "We don't seem to have <b>$salon_name</b> on our platform! We are in the midst of bringing more merchants to the Salon Finder and hopefully we can bring in the salon you are searching for soon!";
				}
				else {
					$keysearch = "We don't seem to have <b>$salon_name</b> in <b>$location</b>";
				}
				
			}
			else {
				$keysearch = "We don't seem to have any salon in <b>$location</b>. We are in the midst of bringing more merchants to the Salon Finder and hopefully we can bring in the salon you are searching for soon!";
			}
			echo '<div class="no_data">'. $keysearch .'</div>';


		$to = get_option('admin_email');
		$location = ( $location != 'null' && $location != '' ) ? $location : 'All'; 
		$title = 'Salon '. $_SESSION["catname"][0] .' at Location: '.$location.' has returned null';

		$email = '';
		if( is_user_logged_in() ) {
			$current_user = wp_get_current_user();
			$email = $current_user->user_email;
		}
		$actual_link = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		
		$body = '
    	<!DOCTYPE html>
    	<html>
      	<head>
      		<title>'.$title.'</title>
      	</head>
      	<body>
      		<p>IP address: '. get_client_ip() .'</p>
      		<p>Keyword: '. $_SESSION["catname"][0] .'</p>
      		<p>Timestamp: '. date( 'd M Y', time()+28800 ) .'</p>
      		<p>Email: '. $email .'</p>
      		<p>Location: '. $location .'</p>
      		<p>Type: Salon</p>
      		<p>Page url: <a href="'. $_SERVER['HTTP_REFERER'] .'">'. $_SERVER['HTTP_REFERER'] .'</a></p>
      	</body>
    	</html>
  	';

		$headers = array('Content-Type: text/html; charset=UTF-8;');
		$headers[] = 'Bcc: mail@yourdomain.com';
		// Function to change sender name
		function search_sender_name( $original_email_from ) {
			return 'Daily Vanity Bot';
		}
			
		add_filter( 'wp_mail_from_name', 'search_sender_name' );


		wp_mail( $to, $title, $body, $headers );

		$url = VOUCHERS_API . 'nullsearchresults';
		$json = array(
			'ip_address' => get_client_ip(),
			'keyword'        => $_SESSION["catname"][0],
			'timestamp'        => date( 'c', time()+28800 ),
			'wp_user_email'        => $email,
			'postal_location'        => $location,
			'page_url'        => $_SERVER['HTTP_REFERER'],
			'search_type'        => 'Salon',
		);

		$ch = curl_init($url);

		// curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		// curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);             
		curl_setopt($ch, CURLOPT_POST, 1 );
		
		$headers    = array();
		$headers[]  = "Content-Type: application/x-www-form-urlencoded";
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		endif;
	?>
					
</div><!-- .search-result -->      
<?php if( $total_search_results>$initial_show_limit): ?>
	<div class="load_outlets">
		<img src="ajax-loading-gif-4-1.gif" alt="ajax" class="loadmomre"/>
	</div>
	<a class="search-result-page load-more-outlet" 
	data-result='<?php echo json_encode( $all_matched_outlet_results ); ?>' 
	data-promotion='<?php echo json_encode( $matched_promotion_results ); ?>' 
	data-orderby="<?php echo $orderbymore; ?>" 
	data-repeat="<?php echo $x; ?>" 
	data-service="advertise" 
	data-search="<?php echo $search_key[0]; ?>" 
	<?php if(isset($_REQUEST["postcode"])){ ?>data-postal="<?php echo $_REQUEST["postcode"]; ?>" <?php } ?> 
	data-paged="<?php echo $paged; ?>" href="#">load more</a> 
<?php endif; ?>