<?php 
global $wp;
?>
<script>
  var totalPages = <?php echo $query->max_num_pages; ?>;
  console.log( window.location.origin + window.location.pathname );
</script>
<section id="search-result-body" class="container searchResultContainer" data-page="<?= $paged ?>">
  <?=$this->render('SearchResults/_title', ['total_search_results'=>$query->total, 'title'=>$title, 'location'=>$location, 'notfound'=>$notfound]) ?>
  <?=do_shortcode( '[cat-navbar multiple_rows="no"]' ); ?>
  <div class="row">
    <div class="col-12">
      <?= $this->render('ListServices/display', ['products'=>$query->products]) ?>
    </div>
  </div>
    <div class="row">
        <div class="col-md-12 col-12 col-sm-12 load-more-div">
          <?php if( $paged < $query->max_num_pages ) : ?>
            <a class="load-more load-more-btn searchNextPage" href="<?= home_url(add_query_arg($getRequest, $wp->request))?>">Load More <i class="fa fa-arrow-down" aria-hidden="true"></i></a>
          <?php endif; ?>
        </div>
    </div>
</section>