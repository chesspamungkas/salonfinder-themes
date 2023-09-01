<?php
/*
Template Name: Beauty Deals
*/

get_header();

$is_page_builder_used = et_pb_is_pagebuilder_used( get_the_ID() ); ?>
<div id="main-content">
  <?php //print_r($_POST);
      $posthiddenvalue = ($_REQUEST['posthiddenvalue'] != null)?$_REQUEST['posthiddenvalue']:"advertiser_services";   
	  if( isset($posthiddenvalue) ) {
          // if (strpos($_REQUEST['catname'], '-') == true) {
          //   $str = strstr($_REQUEST['catname'], '-'); 
          //   $catname = ltrim(substr($str, 1));
          // }else{
          //   $catname = $_REQUEST['catname'];
          // }


          $posthiddenvalue = ($_REQUEST['posthiddenvalue'] != null)?$_REQUEST['posthiddenvalue']:"advertiser_services";   
          $hidetermid = $_REQUEST['hidetermid'];
          $outtype = $_REQUEST['outtype'];
          $postalcode = $_REQUEST['postcode'];
		  
          if ($posthiddenvalue == "advertiser_services" || ($outtype == "category" || $posthiddenvalue == "advertiser")) {
              if ($posthiddenvalue == "advertiser_services") {
                $catname = $_REQUEST['catname'];
              } else if ($posthiddenvalue == "advertiser") {
                  // $catname = (explode("-", $_REQUEST['catname']));
                  $catname = array();
				  if( strrpos($_REQUEST['catname'], " - ") !== false ) {
					  $args = explode("-", $_REQUEST['catname']);
					  $catname[0] = trim( $args[0] );
					  $catname[1] = trim( $_REQUEST['catname'] );
				  }
				  else {
					  $catname[0] = trim( $_REQUEST['catname'] );
				  }
              }
          }
          if ( $catname != "" ) { 
                $_SESSION['catname'] = $catname;
          }
          if ( $posthiddenvalue != "" ) { 
            $_SESSION['posthiddenvalue'] = $posthiddenvalue;
          }
          if ( $hidetermid != "" ) { 
            $_SESSION['catid'] = $hidetermid;
          }
          if ( $outtype != "" ) { 
                $_SESSION['outtype'] = $outtype;
          }
          if ( $postalcode != "" ) { 
                $_SESSION['postalcode'] = $postalcode;
          }
          
      } else if( isset($_SESSION['catname'] )) { 
          $catname = $_SESSION['catname']; 
          $posthiddenvalue = $_SESSION['posthiddenvalue'];   
          $hidetermid = $_SESSION['hidetermid'];
          $outtype = $_SESSION['outtype'];
          $postalcode = $_SESSION['postalcode']; 

      } else {
        $catname = ''; 
        $posthiddenvalue = '';   $hidetermid = ''; $postalcode = '';
      }
			?>
  <div class="search-header srchpage">
  	<?php echo do_shortcode('[salonsearching]');?>

  </div>
   
    <?php
		$outlet_list = array();
        $parent_list = array();
		$show_outlet_list = TRUE;
  		if ( $posthiddenvalue == "advertiser_services" || ( $outtype == "category" || $posthiddenvalue == "advertiser" ) ) {
         if ( $posthiddenvalue == "advertiser_services"){	
			     include_once("get-beauty-deals.php");

        } else if ( $posthiddenvalue == "advertiser" ) {
			include_once("search-salon.php");
		}
  }
        ?>
  
</div>

<?php get_footer(); ?>