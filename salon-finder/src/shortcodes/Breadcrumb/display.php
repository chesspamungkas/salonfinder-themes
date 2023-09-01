<?php 
use DV\core\models\Merchant;
use DV\core\models\Product;

?>
<p id="breadcrumbs">
    <span xmlns:v="http://rdf.data-vocabulary.org/#">
        <?php if(is_search()): ?>
            <span class="breadcrumb_last">SEARCH RESULT</span> <i class="fas fa-chevron-right"></i>
        <?php else: ?>
            <a href="<?php echo site_url(); ?>">HOME</a> <i class="fas fa-chevron-right"></i>    
        <?php endif; ?>

        <?php if(is_tax('merchant')): ?>
            <strong class="breadcrumb_last"><a href="<?php echo Merchant::makeURL($term); ?>"><?= $term->name; ?></a></strong>
        <?php endif; ?>
        <?php if(is_tax('product_cat')): ?>
            <strong class="breadcrumb_last"><a href="<?php echo Product::makeCategoryURL($term); ?>"><?= $term->name; ?></a></strong>
        <?php endif; ?>
        <?php if(is_product()): ?>
            <a href="<?php echo Merchant::makeURL($product->getMerchant()); ?>"><?= $product->getMerchant()->name; ?></a> <i class="fas fa-chevron-right"></i>
            <strong class="breadcrumb_last"><a href="<?= get_permalink($product->get_id()) ?>"><?= $product->get_title(); ?></a></strong>
        <?php endif; ?>
        </span>
    </span>
</p>