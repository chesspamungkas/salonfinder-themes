<?php get_header(); ?>
<div id="main-content">
	<!-- <div class="search-header srchpage">
  		<?php echo do_shortcode('[salonsearching]');?>
  	</div> -->
	<?php $cat_name = get_query_var('advertiser_slug'); 
	// print_r($cat_name);die();
	
	?>
  	<?php do_shortcode("[search-results merchantslug='$cat_name' preview_product=1]" ); ?>
</div>
<?php get_footer(); ?>