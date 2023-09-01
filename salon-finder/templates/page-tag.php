<?php
/**
* Template Name: Tag Page
*/
get_header();

?>    
    
<?php  ?> 
<div id="main-content">
	<div class="bg-white">
	</div>
	<div class="bg-light-grey">
		<div class="container p-t-15 p-b-15">
			<div class="author-block posts-grid" style="padding-top:58px;">
				<div class="author-posts">
					<div class="section-title bkg-gray cat-title">
						<div class="et_pb_text_inner">
							<h2><?php echo single_cat_title() ?></h2>
						</div>
					</div>
					<!-- <div class="fieldset">
						<div class="border ls"></div>

						<strong class="fieldset-title "><w?php echo single_cat_title() ?></strong>
						<div class="border rs"></div>
					</div> -->
					<?php
					/*$length = 12;
					$args = get_posts( array(
						'orderby'       => 'post_date',
						'order'         => 'ASC',
						'cat'           => $categories[0]->cat_ID,
						'posts_per_page'=> $length
					) );

					$the_query = new WP_Query( $args );*/

					?>

					<div class="flex-grid">
						<?php while ( have_posts() ) : the_post();
							?>
							<div class="flex-grid-item">
								<div class="img">
									<a href="<?php echo get_the_permalink(); ?>"><img src="<?php echo get_the_post_thumbnail_url( get_the_ID() ) ?>"   alt="<?php echo get_the_title() ?>"></a>
								</div>
								<div class="flex-grid-meta">
									<h5 class="base-color"><?php echo $tagID; ?></h5>
									<div class="p-l-25 p-r-25">
										<a href="<?php echo get_the_permalink(); ?>" class="cat-link-post"><?php echo get_the_title(); ?></a>
									</div>
									<div class="clearfix m-t-25">
										<div class="pull-left p-l-15 p-b-25 text-left text-small text-light-grey"><?php echo get_the_author() ?></div>
										<div class="pull-right p-r-15 p-b-25 text-right text-small text-light-grey"><?php echo get_the_date(); ?></div>
									</div>
								</div>
							</div>
							<?php
							endwhile;
							wp_reset_postdata();
						?>
					</div>
					<?php
					wp_pagenavi();
					?>
				</div>
			</div>
		</div>
	</div>
</div> <!-- #main-content -->
<?php get_footer(); ?>                    