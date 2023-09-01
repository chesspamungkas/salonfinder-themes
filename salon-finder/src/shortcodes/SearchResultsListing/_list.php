<section id="search-result-body" class="container searchResultContainer" data-total-pages="<?php echo $query->max_num_pages;?>" data-paged="<?= $query->paged?$query->paged:1 ?>">
  <?=$this->render('SearchResultsListing/_title', ['total_search_results'=>$query->found_posts, 'title'=>$title, 'location'=>$location, 'notfound'=>$notfound]) ?>
  <?=do_shortcode( '[cat-navbar multiple_rows="no"]' ); ?>
  <div class="row">
    <div class="col-12">
      <div id="search-results" class="mt-3">
        <div class="row">
          <?php if( $query->have_posts()): $query->rewind_posts(); ?>
            <?php while( $query->have_posts()): $query->the_post(); ?>
              <?php wc_get_template_part( 'content', 'product' ); ?>
            <?php endwhile; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12 col-12 col-sm-12 load-more-div">
      <a href="<?php echo $this->getNextPageLink(); ?>" class="load-more load-more-btn searchNextPage">Load More <i class="fa fa-arrow-down" aria-hidden="true"></i></a>
    </div>
  </div>
</section>