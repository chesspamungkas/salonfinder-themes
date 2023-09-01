<?php 
/* Template Name: Thank You for purchase*/ 
get_header();
?>
<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
  <div id="main-content">
    <div class="container" id="purchaseConfirmation">
      <div class="row">
        <div class="col-12">
          <?= the_content(); ?>
        </div>
      </div>
    </div>
  </div>
</article>
<?php 
get_footer();
?>