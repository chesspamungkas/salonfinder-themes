<?php
    if ($menu_item->title=='Award Winners')
        $url = $menu_item->url;
    else
        $url = site_url($menu_item->category_url);
?>
<a href="<?php echo $url; ?>" <?php if ($menu_item->title=='Award Winners') {?> target="_blank" <?php }?> class="col px-2 my-2 align-self-center cat-navbar-<?php echo $numOfItem; ?> slider-cat-navbar-item" title="<?php echo $menu_item->title; ?>">
    <div class="menu-item" style="background-image: url('<?php echo $menu_item->category_image; ?>');">
        <?php echo $menu_item->title; ?>
    </div>
</a>