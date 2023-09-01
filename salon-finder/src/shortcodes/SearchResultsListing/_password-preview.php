<div class="container-fluid px-0 single-product">
  <div class="col text-center poppins-medium" style="min-height: 300px; margin-top:8rem !important;">
    <?php
      echo get_the_password_form($query->posts[0]->ID);
    ?>
  </div>
</div>