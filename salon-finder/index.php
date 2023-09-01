<?php get_header(); ?>

<div id="main-content" >
	<div class="container">
		<div id="content-area" class="clearfix">
			<div id="left-area">
		<?php
			if ( have_posts() ) :
				while ( have_posts() ) : the_post();
					?>

					<article id="post-<?php the_ID(); ?>" <?php post_class( 'et_pb_post' ); ?>>
						<?php the_content(); ?>
					</article>
				<?php endwhile; endif; ?>
			</div> <!-- #left-area -->

			<?php get_sidebar(); ?>
		</div> <!-- #content-area -->
	</div> <!-- .container -->
		
	<div class="end-sticky"></div>
</div> <!-- #main-content -->

<?php get_footer(); ?>