<?php
/**
 * Template Name: Home Page with 1 Column
 */
get_header();

?>

<?php if( is_front_page() ): ?>
  <style>
    @media (min-width: 481px) {
      .custom-select {
        padding-top: .2rem !important;
      }

      .ui-autocomplete {
        top: 300px !important;
      }
    }
  </style>
<?php endif; ?>

<div id="main-content">  
	<div id="content-area">
    <?php if(have_posts()): ?>		
      <?php while ( have_posts() ) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
          <?php
            echo do_shortcode( '[cat-navbar multiple_rows="yes"]' );
            echo do_shortcode( '[collections featured="1"]' );
            echo do_shortcode( '[latest-deals]' );
            echo do_shortcode( '[collections]' );
          ?>
          <?php //the_content(); ?>
        </article> <!-- .et_pb_post -->
      <?php endwhile; ?>
    <?php endif; ?>  
	</div> <!-- #content-area -->	
</div> <!-- #main-content -->

<?php

// do_action( 'after_setup_theme' );

get_footer(); 

?>
