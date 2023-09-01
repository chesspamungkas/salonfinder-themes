<div class="search_title row">
  <div class="search_title_left col-md-12">
    <?= isset($title)?$title:'' ; ?>
    <div class="container-fluid">
      <div class="row">
        <?php if($total_search_results<=0 || $notfound=='yes'):?>
          <?php if($location): 
              $returnTitle = '';
              foreach($location as $key => $loc):
                $locationTitle .= $loc;
                if ($key !== array_key_last($location))
                    $locationTitle .= ', ';
              endforeach;
              ?>
              <div class="column col-6 p-0">
                <p class="search_number poppins-medium">How about these other services in the <?php echo $locationTitle; ?> part of Singapore?</p>
              </div>
              <!-- <p class="col-6 search_number poppins-medium">How about these other services in the <?php echo $locationTitle; ?> part of Singapore? <span class="col-6 hide-categories ml-5">Hide categories <i class="fas fa-angle-double-up"></i></span> <span class="show-categories ml-5">Show categories <i class="fas fa-angle-double-down"></i></span></p> -->
          <?php else:?>
              <div class="column col-6 p-0">
                <p class="search_number poppins-medium">How about these other services?</p>
              </div>
              <!-- <p class="col-6 search_number poppins-medium">How about these other services? <span class="col-6 hide-categories ml-5">Hide categories <i class="fas fa-angle-double-up"></i></span> <span class="show-categories ml-5">Show categories <i class="fas fa-angle-double-down"></i></span></p> -->
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