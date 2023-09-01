<?php 
use DV\shortcodes\SearchResultsListing\SearchResultsListing;
?>
<div class="search_title row">
  <div class="search_title_left col-md-12">
    <?php if($title && !$notfound): ?>
      <h1 class='poppins-bold' style='text-transform: none;'>
        <span class='pink-title'><?php echo apply_filters(SearchResultsListing::$_FILTERS_SEARCH_RESULT_TITLE, $title); ?></span> 
        <?php if($location && count($location)>0 && $location[0] && $location[0] != "all"): ?>
          in <?php echo implode(', ', array_map('ucfirst', $location)); ?> part of Singapore
        <?php else: ?>
          in Singapore
        <?php endif; ?>
      </h1>
    <?php endif; ?>
    <div class="container-fluid">
      <div class="row">
        <?php if($notfound):?>
          <?php if($location): ?>
            <div class="column col-6 p-0">
              <p class="search_number poppins-medium">How about these other services in <?php echo implode(', ', array_map('ucfirst', $location)); ?> part of Singapore?</p>
            </div>
          <?php else:?>
            <div class="column col-6 p-0">
              <p class="search_number poppins-medium">How about these other services?</p>
            </div>              
          <?php endif;?>
        <?php else:?>
          <div class="column col-6 p-0">
            <p class="search_number poppins-medium"><?= $total_search_results; ?> services found </p>
          </div>
          <!-- <p class="col-6 search_number poppins-medium"><?= $total_search_results; ?> services found <span class="col-6 hide-categories ml-5">Hide categories <i class="fas fa-angle-double-up"></i></span> <span class="show-categories ml-5">Show categories <i class="fas fa-angle-double-down"></i></span></p> -->
        <?php endif;?>
        <div class="column">
            <span class="hide-categories poppins-medium">Hide categories <i class="fas fa-angle-double-up"></i></span> <span class="show-categories poppins-medium">Show categories <i class="fas fa-angle-double-down"></i></span>
        </div>
      </div>
    </div>
   </div>
</div>
<script>
  jQuery(document).ready(function ($) {
    $('.hide-categories').click(function() {
      $("#category-navbar").slideToggle();
      $(this).hide();
      $('.show-categories').show();
    });

    $('.show-categories').click(function() {
      $("#category-navbar").slideToggle();
      $(this).hide();
      $('.hide-categories').show();
    });
  });
</script>