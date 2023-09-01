<div class="container mb-5" <?php if (!empty($backgroundimage)) :?>style="background-image: url( '<?php echo $backgroundimage; ?>' );border-radius: 25px; padding: 25px 10px; background-size: contain"<?php endif;?>>
  <?php echo \DV\core\Views::render( 'ListServices/listServicesWithoutMobile', [ 'products' => $products, 'title'=>$title, 'viewAll'=>$viewAll ] ); ?> 
</div>
