<?php /* Template Name: Registration Page */ 
if( isset($_GET['redirect_to']) ) {
	$redirect_url= $_GET['redirect_to'];
}
else
{
	$redirect_url='';
}
get_header();
if ( !is_user_logged_in() ) {
?>
<style type="text/css">
	#register_form.loading {
		position: relative;
		display: block !important;
	}

	#register_form.loading:after
 	{
		content: "";
		position: absolute;
		width: 100%;
		height: 100%;
		top: 0;
		left: 0;
		background: url("<?php echo get_template_directory_uri(); ?>/images/loading.gif") no-repeat rgba(255,255,255,0.5);
		z-index: 11;
		cursor: wait;
		background-position: center;
		background-size: 100px;
	}
</style>
<script>
    jQuery(document).ready(function ($) {
		jQuery('#register_form').submit(function(e){
			jQuery('#register_form').addClass('loading');
			var honeypot = $( '#honeypot' );
			if( honeypot.val() != '' )
			{
				return false;
			}
			else
			{
				return true; 
			}
		});
    })
</script>
<div id="main-content" class="registration-main text-center">
	<div class="container">
		<h1 class="signup-text poppins-bold mb-4">Sign Up</h1>
		<?php 
			global $errors;
			global $woocommerce;
			$cart_url = get_permalink( wc_get_page_id( 'cart' ) );
			$checkout_url = get_permalink( wc_get_page_id( 'checkout' ) );
			$thankpage = site_url('/thank-you-for-register-user');
			if($redirect_url == $cart_url || $redirect_url == $checkout_url){
				$register_redirect_url = $thankpage.'?cmname=checkout';
			}else{
				$register_redirect_url = $thankpage.'?cmname=profile';
			}
			if($errors){
				foreach($errors as $error){
					echo '<p class="error_msg poppins-medium">'.$error.'</p>';
				}
			}
		?>
		<p class="complete poppins-medium mb-4">Complete your profile to create your personal Daily Vanity account! If you already have an account, you can <a href="<?php echo home_url();?>/profile/login/" class="signinupper-link">sign in here</a></p>
		<form name="registerform" id="register_form" class="woocommerce-form woocommerce-form-login login register register_form" action="#" method="post" style="padding:0;" enctype="multipart/form-data">
			<div class="inputcontainer mb-3">
				<input type="text" id="first_name" name="first_name" value="<?php echo isset($_POST['first_name'])?$_POST['first_name']:"" ?>" required class="form-control input-form poppins-medium" placeholder="First Name">
			</div>
			<div class="inputcontainer mb-3">
				<input type="text" id="last_name" name="last_name" value="<?php echo isset($_POST['last_name'])?$_POST['last_name']:"" ?>" required class="form-control input-form poppins-medium" placeholder="Last Name">
			</div>
			<div class="inputcontainer mb-3">
				<input type="email" name="user_email" id="user_email" value="<?php echo isset($_POST['user_email'])?$_POST['user_email']:"" ?>" required class="form-control input-form poppins-medium" placeholder="Email Address">
			</div>
			<div class="inputcontainer mb-3">
				<input type="password" id="user_pass" name="user_pass" value="" class="form-control input-form poppins-medium" required placeholder="Password">
			</div>
			<div class="inputcontainer mb-3">
				<input type="password" id="cn_user_pass" name="cn_user_pass" value="" class="form-control input-form poppins-medium" required placeholder="Confirm Password">
			</div>
			
			<div class="inputcontainer mb-4">
				<label class="agree mailchimp-label"><input id="mailchimp-request" type="checkbox" required="" class="gdprInput textField"> <span class="poppins-regular">I agree to allow Daily Vanity Pte Ltd to include me in their mailing list for marketing and communications matters. I am able to request for my data to be removed from the site if required</span></label>
				<div style="clear:both"></div>
				<script src='https://www.google.com/recaptcha/api.js'></script>
				<div class="g-recaptcha mb-4" data-sitekey="<?= reCAPTCHA_SITE_KEY ?>"></div>
				<br />
				<input type="hidden" id="honeypot" name="honeypot" value="" />
				<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
				<input type="hidden" name="redirect_to" value="<?php echo $register_redirect_url;?>" />
				<span class="register_facebook cm_facebooklog">
					<input type="submit" name="wp-submit" id="wp-submit" class="woocommerce-Button" value="Create New Account" />
				</span>
			</div>
		</form>
		<p class="already-have poppins-semibold text-center">Already have an account? <a href="<?php echo home_url();?>/profile/login/" class="signinbottom-link">Sign in here</a></p>
	</div>
</div>
<script type="text/javascript">
	jQuery('#mailchimp-request').change(function(){
		if( ! jQuery('#mailchimp-request').is(':checked') ) {
			jQuery('#register_form .cm_facebooklog .fbl-button').removeClass('active');
		}
		else {
			jQuery('#register_form .cm_facebooklog .fbl-button').addClass('active');
		}
	});
	
	jQuery('#login_form .cm_facebooklog .fbl-button').addClass('active');
	
	jQuery('#register_form .cm_facebooklog .fbl-button').click(function(e){
		if( ! jQuery('#mailchimp-request').is(':checked') ) {
			e.preventDefault();
			jQuery('.mailchimp-label').addClass('error');
		}
		else{
			jQuery('.mailchimp-label').removeClass('error');
		}
	});
</script>
<?php }else{ ?>
	<script type="text/javascript">
		window.location.href = "<?php echo site_url(); ?>";	
	</script>
	<?php
} ?>

 <?php get_footer(); ?>
