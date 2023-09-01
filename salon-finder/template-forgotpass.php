<?php /* Template Name: Forgot Password Page */
get_header();
global $wpdb;
if ( !is_user_logged_in() ) {
?>
<div id="main-content" class="forgotpass-main text-center">
	<div class="container">
		<h1 class="forgotpass-text poppins-bold mb-4">Lost Password</h1>
		<p class="error_msg poppins-medium"></p>
		<p id="time"></p>
		<form name="lostpasswordform" id="lostpasswordform" action="#" method="post">
			<div class="inputcontainer mb-3">
				<input type="text" name="user_login" id="user_login" class="form-control input-form poppins-medium" placeholder="Email Address">
			</div>
				<input type="hidden" name="redirect_to" value="<?php echo $redirect ?>">
			<div class="inputcontainer mb-3">
				<input type="submit" name="wp-submit" id="wp-submit" class="woocommerce-Button msbutton" value="Get New Password">
			</div>
		</form>
	</div>
</div>
<script>
	var seconds = 5;
	var url="<?php echo site_url('login'); ?>";
	function redirect(){
		if (seconds <=0){
			// redirect to new url after counter  down.
			window.location = url;
		} else {
			seconds--;
			document.getElementById("time").innerHTML = "You will be redirected to login page in "+seconds+" seconds."
			setTimeout("redirect()", 1000)
		}
	}

	jQuery('form#lostpasswordform').on('submit', function(e){
		e.preventDefault();
		jQuery('form#lostpasswordform').addClass('loading');
		jQuery.ajax({
			type: 'POST',
			dataType: 'json',
			url: "<?php echo admin_url('admin-ajax.php'); ?>",
			data: { 
				'action': 'ajaxforgotpassword', 
				'user_login': jQuery('#user_login').val(),
			},
			success: function(data){
				console.log(data);	
				// jQuery('form#lostpasswordform').hide();	
				jQuery('form#lostpasswordform').removeClass('loading');			
				jQuery('.error_msg').html(data.message);
				if(data.loggedin){
					jQuery('form#lostpasswordform').hide();	
					redirect();
				}	
			}
		});
		e.preventDefault();
		return false;
	});
</script>
<style type="text/css">
	#lostpasswordform.loading {
		position: relative;
		display: block !important;
	}
	#lostpasswordform.loading:after {
		content: "";
		position: absolute;
		width: 100%;
		height: 100%;
		top: 0;
		left: 0;
		background: rgba(255,255,255,0.5);
		z-index: 11;
		cursor: wait;
	}
</style>
<?php }else{
	?>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			setTimeout(function() {
			window.location.href = "<?php echo site_url(); ?>";
			}, 5000);		
		});
	</script>
	<?php
} ?>

 <?php get_footer(); ?>