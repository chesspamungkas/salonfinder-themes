<?php
/*
Template Name: Search Testing-bvm
*/
get_header();
$is_page_builder_used = et_pb_is_pagebuilder_used( get_the_ID() ); ?>

<div id="main-content">
<?php
 global $woocommerce, $product, $wpdb;
if(isset($_POST['searchsubmit'])){
	$catname = $_POST['catname']; 
	$posthiddenvalue = $_POST['posthiddenvalue'];  
	$hidetermid = $_POST['hidetermid']; 
	$postalcode = $_POST['postalcode'];
	if(!empty($catname) && $posthiddenvalue == 'advertiser_services'){
	 $searchingfor = 1;
	 $search = $_POST['catname'];
	}
	if(!empty($catname) && $posthiddenvalue == 'advertiser'){
	 $searchingfor = 2;
	 $search = $_POST['catname'];
	}	
/*	echo $catname.'-cat<br/>';
	echo $posthiddenvalue.'-jid<br/>';
	echo $hidetermid.'-catid<br/>';
	echo $searchingfor.'-val<br/>';
	echo $search.'-sea<br/>';*/
}
?>
<div class="search-header srchpage">
</div>

<div class="search-result">
	<div class="search_title">
	<div class="search_title_left">
		       <?php
if ( function_exists('yoast_breadcrumb') ) {
yoast_breadcrumb('
<p id="breadcrumbs">','</p>
');
}
?>
	<h2>
<?php 
/*	session_start(); 
	if ( $catname != "" ) { 
		$_SESSION['catname'] = $catname;
	}
	if ( $hidetermid != "" ) { 
		$_SESSION['catid'] = $hidetermid;
	}
	echo $_SESSION['catname'];*/
?>
        </h2>
<?php 
	$paged = get_query_var('page') ? get_query_var('page') : 1;
	$args = array(
		'post_type' 	  => 'product',
		'post_status'     => 'publish',
		'posts_per_page'  => -1,
	//	'paged'			  => $paged, 
		'product_cat'	  => 142
	);
	
	 $argsPro = array(
	'post_type' 	  => 'product',
    'post_status' => 'publish',
    'tax_query' => array(
         'taxonomy' => 'product_cat',
        'operator' => 'IN',
         'terms'     =>  195,
         )
    );
$the_query_public = new WP_Query( $args );
$clearedPrducts = array();

while ( $the_query_public->have_posts() ) { $the_query_public->the_post();
	echo 'id are-'.$post->ID.'<br/>';
}




get_footer(); ?>