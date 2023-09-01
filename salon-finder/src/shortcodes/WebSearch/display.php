<?php 
  use DV\shortcodes\WebSearch\WebSearch;
?>
<?php echo do_action(WebSearch::$_BEFORE_CONTENT_ACTION_HOOK, ''); ?>
<div class="container display-autocomplete" id="mainSearchWrapper" data-selected="category">
  <form class="advertiser-services-form desktop-view" id="search_form" role="search" action="<?= site_url('search') ?>" data-searchtype="<?= $searchType ?>" data-searchvalue="<?= $searchValue; ?>" data-searchurl="<?= site_url(); ?>">                  
    <input type="hidden" name="paged" id="paged" value="1" />                  
    <div id="locationField" class="tabcontent row position-relative">      
      <div class="col-sm-6 pl-sm-4 pr-sm-0">
        <div class="input-group">
          <a class="btn btn-close" id="search-close" href="#"><i class="fa fa-angle-left"></i></a>
          <input type="search" name="s" placeholder="Find a service or salon" id="searchText" value="<?= $searchText ?>" class="form-control" autocomplete="off">
          <button type="submit" name="searchsubmit" class="btn btn-close d-block d-sm-none form-submit-button" id="search-button"> <i class="fa fa-search"></i></button>
        </div>
      </div>
      <div class="col-sm-4 pl-sm-3 pr-sm-0 d-none d-sm-block locationSelectWrapper">
        <select id="location" name="location[]" class="poppins-light" multiple="multiple" style="display:none;">
          <option value="North" <?php if($location): if(in_array('North',$location)): ?>selected="selected"<?php endif; endif; ?>>North</option>
          <option value="South" <?php if($location): if(in_array('South',$location)): ?>selected="selected"<?php endif; endif; ?>>South</option>
          <option value="East" <?php if($location): if(in_array('East',$location)): ?>selected="selected"<?php endif; endif; ?>>East</option>
          <option value="West" <?php if($location): if(in_array('West',$location)): ?>selected="selected"<?php endif; endif; ?>>West</option>
          <option value="Central" <?php if($location): if(in_array('Central',$location)): ?>selected="selected"<?php endif; endif; ?>>Central</option>
          <option value="Northeast" <?php if($location): if(in_array('Northeast',$location)): ?>selected="selected"<?php endif; endif; ?>>Northeast</option>
          <option value="Northwest" <?php if($location): if(in_array('Northwest',$location)): ?>selected="selected"<?php endif; endif; ?>>Northwest</option>
          <option value="Southeast" <?php if($location): if(in_array('Southeast',$location)): ?>selected="selected"<?php endif; endif; ?>>Southeast</option>
        </select>
      </div>
      <div class="col-sm-1 col-md-2 ml-sm-4 ml-md-0 pl-sm-0 pl-md-2 pl-lg-4 pr-sm-0 d-none d-sm-block">
        <button type="submit" name="searchsubmit" class="form-control inter-bold form-submit-button" id="searchsubmit">
          <i class="fa fa-search d-block d-md-none"></i>
          <span class="d-none d-md-block">SEARCH</span>
        </button>
      </div>          
    </div>
    <div id="searchLocationWrapper">
      <div class="container-fluid no-padding" id="menu-wrapper">
        <!-- <div class="row no-gutters" style="flex-grow:1;"> -->
          <hr class="mobile-searchbar" style="display: block;">
          <p class="browse-location poppins-medium">BROWSE BY LOCATION</p>
          <div class="btn-group">
            <select id="location-mobile" name="location[]" multiple="multiple" style="display:none;">
              <option value="North" <?php if($location): if(in_array('North',$location)): ?>selected="selected"<?php endif; endif; ?>>North</option>
              <option value="South" <?php if($location): if(in_array('South',$location)): ?>selected="selected"<?php endif; endif; ?>>South</option>
              <option value="East" <?php if($location): if(in_array('East',$location)): ?>selected="selected"<?php endif; endif; ?>>East</option>
              <option value="West" <?php if($location): if(in_array('West',$location)): ?>selected="selected"<?php endif; endif; ?>>West</option>
              <option value="Central" <?php if($location): if(in_array('Central',$location)): ?>selected="selected"<?php endif; endif; ?>>Central</option>
              <option value="Northeast" <?php if($location): if(in_array('Northeast',$location)): ?>selected="selected"<?php endif; endif; ?>>Northeast</option>
              <option value="Northwest" <?php if($location): if(in_array('Northwest',$location)): ?>selected="selected"<?php endif; endif; ?>>Northwest</option>
              <option value="Southeast" <?php if($location): if(in_array('Southeast',$location)): ?>selected="selected"<?php endif; endif; ?>>Southeast</option>
            </select>
          </div> 
        <!-- </div> -->
      </div>
    </div>
    
  </form>
  <div id="mobile-check"></div>
</div>
<?php echo do_action(WebSearch::$_AFTER_CONTENT_ACTION_HOOK, ''); ?>