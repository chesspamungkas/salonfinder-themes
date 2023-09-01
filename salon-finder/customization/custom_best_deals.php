<?php
function custom_styles_method(){
	?>
		<style>
			.page-template-beauty-deals .search-result .buy-details{
		  float: none !important;
		}
		.page-template-beauty-deals .repeatdiv {
		  border: 1px solid #d8d8d8;
		  width: 31%;
		  /* width: 920px; */
		  /* margin: 28px auto; */
		  margin: 0.5%;
		  padding: 22px;
		  float: left;
		  _min-height: 600px;
		}
		.page-template-beauty-deals .repeatdiv .repeatright{
		  width: 100%;
		  float: none;
		}
		.page-template-beauty-deals .price-section strong{
		  display: block;
		}
		.page-template-beauty-deals span.discount{
		  background: #f04084;
		  display: inline-block;
		  padding: 5px 10px;
		  border-radius: 30px;
		  color: #fff;
		  margin: 5px 0;
		}
		.page-template-beauty-deals .text-right strong{
		  display: block;
		  float: right;
		  clear: both;
		}
		.page-template-beauty-deals .price-section{
		  clear: both;
		  overflow: auto;
		  margin: 0 -15px;
		  padding-top: 10px;
		}
		.page-template-beauty-deals .price-section .col-sm-6{
		  padding: 0 !important; 
		}
		.page-template-beauty-deals .repeatrightprice > .col-xs-12{
		  padding: 10px 0 !important;
		}
		.page-template-beauty-deals .grid_services{
		  display: flex;
		  flex-wrap: wrap;
		}
		.page-template-beauty-deals .repeatdiv:nth-child(3n-1) {
		  clear: both;
		}

		.page-template-beauty-deals .repeatdiv .repeatleft {
		  /* width: 290px; */
		  width: 100%;
		  padding: 0px;
		  overflow: hidden;
		  float: none;
		}
		</style>
	<?php
}

add_action( 'wp_head', 'custom_styles_method', 10, 10 );

function custom_javascript_method(){
	?>
		<script>
			jQuery(document).ready( function(){
				jQuery('.page-template-beauty-deals #main-content').on('click', 'a.load-more-outlet', function(e) {
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
					jQuery.ajax({
						url : '<?php echo admin_url('admin-ajax.php'); ?>',
						type : 'post',
						dataType: 'JSON',
						data : {
							action : 'new_more_outlet_ajax',
							nextPage : nextPage,
							repeatDiv : repeatDiv,
							orderby : orderby,
							dataService : dataService,
							result : result,
							promotion : promotion,
							postcode : postcode,
							keyword : keyword,
							catName : catName
						},
						success : function( response ) {

							if( response.success==true ) {
								elem.data("paged", nextPage);
								console.log(response.load_more);
								jQuery(".grid_services").append(response.result);
								jQuery('.load_outlets').hide();
								if( response.load_more==false )jQuery('.load-more-outlet').hide();
							} else {
								console.log("failure");
							}
						}
					});
				});
			});
		</script>
	<?php
}

add_action( 'wp_footer', 'custom_javascript_method', 10, 10 );

function new_more_outlet_ajax(){
	$paged = $_REQUEST['nextPage'];
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
    'post__in' => array_merge( array( 0 ), wc_get_product_ids_on_sale() )
	);	
	$product_query = new WP_Query( $query_args );
	$prev_sale_price = 0;
	$prev_product_id = 0;
	ob_start();
	
	if($product_query->found_posts) :
		foreach ($product_query->posts as $post) : 
			$product_id = $post->post_parent;
			$variation_id = $post->ID;
			$duration = get_post_meta( $product_id, 'duration', true );
			$price = get_post_meta( $variation_id, '_price', true );
			$regular_price = get_post_meta( $variation_id, '_regular_price', true );
			$price = $regular_price ? $regular_price : $price;
			$sale_price = get_post_meta( $variation_id, '_sale_price', true );
			$discount = $sale_price ? $sale_price / $price : 0;	
			$percentage = -(round($discount, 2) * 100) . '%';
			if(get_post_status($product_id) !== 'publish') continue; 
			if($prev_product_id == $product_id) {
				if($sale_price == $prev_sale_price){
					continue;
				}
			}else{
				$prev_product_id = $product_id;					
			}
			$prev_sale_price = $sale_price;
			$merchant = get_term_by('slug', get_post_meta($variation_id, 'attribute_pa_advertiser', true), 'merchant');
			?>
			<div class="repeatdiv repeat" number="<?php echo $variation_id; ?>" cuspage="0">
				<!-- Left Side -->
				<div class="repeatleft">
					<a href="<?php the_permalink($product_id); ?>?varid=<?php echo $variation_id; ?>" title="">
						<img src="<?php echo get_the_post_thumbnail_url( $product_id, 'medium' ) ?>" alt="<?php echo get_the_title($variation_id) ?>" />
					</a>
				</div>

				<!-- Right Side -->
				<div class="repeatright">
					<div class="container">  						
						<div class="row repeatrightprice rightprice_<?php echo $variation_id; ?>">
							<div class="col-md-12 col-xs-12">
								<h5 class="author"><strong><?php echo $merchant->name ?></strong></h5>
								<a href="<?php the_permalink($product_id); ?>?varid=<?php echo $variation_id; ?>" id="product_<?php echo $variation_id; ?>">
									<?php echo get_the_title($product_id); ?>
								</a>
								<!-- <p><?php echo get_the_title($variation_id); ?></p> -->
							</div> <!-- end col-md-4 col-xs-12 -->
							<!-- <div class="col-md-12 col-xs-12">
								<span class="duration <?php echo $duration ? 'dur_active' : ''; ?>">
									<?php echo $duration ? $duration : ''; ?>
								</span>
							</div> --> <!-- end col-md-2 col-xs-3 -->
							<div class="price-section">
								<div class="col-md-6 col-xs-6">  											
									<?php if($discount): ?>
										<span class="discount"><?php echo $percentage; ?></span>
									<?php endif; ?>
								</div>
								<div class="col-md-6 col-xs-6 text-right">
									<?php echo admin_get_variation_price( $variation_id ); ?>
								</div> 
							</div>
							<div class="col-md-12 col-xs-12">
								<div class="buy-details">
									<form class="cart ng-pristine ng-valid allpro-duct" action="<?php // echo get_term_link($term_id, 'merchant'); ?>" method="post" enctype="multipart/form-data">
										<button type="submit" class="single_add_to_cart_button button alt">BUY</button>
										<input type="hidden" name="add-to-cart" value="<?php echo $product_id; ?>" />
										<input type="hidden" name="product_id" value="<?php echo $product_id; ?>" />
										<input type="hidden" name="variation_id" class="variation_id" value="<?php echo $variation_id ?>" />
									</form>
								</div> <!-- end buy-details -->
							</div> <!-- end col-md-2 col-xs-3 -->
						</div> <!-- end row repeatrightprice -->  							
					</div> <!-- end container -->
				</div> <!-- end repeatright -->
				<div style="clear:both"></div>
			</div> <!-- end repeatdiv -->
		<?php endforeach;
	endif;

	$output = ob_get_contents();

	ob_end_clean();

	if( $product_query->max_num_pages > $paged ) $load_more = true; else $load_more = false;
	if($output) {
  		echo json_encode(array('success' => true, 'result' => $output,'load_more'=>$load_more));
	} else {
  		echo json_encode(array('success' => false, 'message' => 'Not found!','load_more'=>$load_more));
	}
  exit;
}

add_action( 'wp_ajax_new_more_outlet_ajax', 'new_more_outlet_ajax' );
add_action( 'wp_ajax_nopriv_new_more_outlet_ajax', 'new_more_outlet_ajax' );