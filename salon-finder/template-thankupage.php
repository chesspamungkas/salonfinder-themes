<?php /* Template Name: Thank You*/ 
get_header();?>
<div class="col-md-12 thanks_page text-center mt-5 mb-5">
	<h2 class="poppins-bold">Registration Success!</h2>
	<p id="time" class="poppins-medium"></p>
</div>
<?php
if (isset($_GET['cmname'])) {
	$url = site_url($_GET['cmname']);
	$cmname = 'checkout';
}else{
	$url = bloginfo('url').'/profile';
	$cmname = 'Profile';
}

?>
<script language="JavaScript" type="text/javascript">
	var seconds = 3;
	var url="<?php echo $url; ?>";
	var cmname="<?php echo $cmname; ?>";

	function redirect(){
		if (seconds <=0){
		// redirect to new url after counter down.
			window.location = url;
		}else{
			seconds--;
			//You will be redirected checkout page to complete your order in 3 seconds
			if (cmname == 'checkout') {
			document.getElementById("time").innerHTML = "You will be redirected "+cmname+" page to complete your order in "+seconds+" seconds."
			}else if(cmname == 'profile'){
			document.getElementById("time").innerHTML = "You will be redirected "+cmname+" page to complete your profile in "+seconds+" seconds."
			}else{
			document.getElementById("time").innerHTML = "You will be redirected "+cmname+" page to complete your profile in "+seconds+" seconds."
			}

			setTimeout("redirect()", 1000)
		}
	}
	redirect();
</script>

<?php get_footer();?>