<?php /* Template Name: Login Page */
if (isset($_SERVER["HTTP_REFERER"]))
	$referer_url = $_SERVER["HTTP_REFERER"];
else
$referer_url = '';

if( strpos($referer_url,"cart") !== FALSE ){
	$redirect_url = site_url('/checkout/');
	$_COOKIE["redirect_url"] = $redirect_url;
}else if( isset($_COOKIE["redirect_url"])) {
	$redirect_url= $_COOKIE["redirect_url"] != home_url('/') ? $_COOKIE["redirect_url"] : site_url('/profile/');
} else {
	$redirect_url= site_url('/profile/');
}
if( isset($_POST['redirect_url']) ) {
	$redirect_url= $_POST['redirect_url'];
}
if( strrpos($redirect_url, "lostpassword") !== false ) {
	$redirect_url= site_url('/profile/');
}


if (isset($_POST['email'])){
	$email = $_POST['email'];
}
else
{
	$email='';
}

if (isset($_POST['password'])){
	$password = $_POST['password'];
}
else
{
	$password='';
}

$user_login_details = $email.'_pass_'.$password;
if(!empty($_POST["remember"])) {
	setcookie ("user_login_details",$user_login_details,time()+ (10 * 365 * 24 * 60 * 60)); //set cookie time as per you need
	} else {  //remove login details from cookie
	if(isset($_COOKIE["user_login_details"])) {
		setcookie ("user_login_details","");
	}
}

get_header();
if ( !is_user_logged_in() ) {
	if(isset($_COOKIE["user_login_details"])) {
		$login_details = $_COOKIE["user_login_details"];
		$login_details = explode('_pass_', $login_details);
		$email = $login_details[0];
		$password = $login_details[1];
	}
?>
<style type="text/css">
	#login_form.loading {
		position: relative;
		display: block !important;
	}
	#login_form.loading:after {
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
<div id="main-content" class="login-main text-center">
	<div class="container">
		<h1 class="signin-text poppins-bold mt-4 mb-5">Sign In</h1>
		<p class="fill-in poppins-medium mb-4">Fill in your email and password below.<br> If you don't have an account, you can <a href="<?php echo home_url();?>/profile/register/" class="signupupper-link">sign up here</a></p>
		<form id="login_form" style="margin-top: 0px; margin-bottom: 0px;" class="woocommerce-form woocommerce-form-login login register_form" action="#" method="post">
			<div class="inputcontainer mb-3">
				<input type="text" id="email" name="email" required="" class="form-control input-form poppins-medium" placeholder="Email Address" value="<?php if(isset($email)){ echo $email; } ?>">
			</div>
			<div class="inputcontainer mb-4">
				<input type="password" id="password" name="password" required="" class="form-control input-form poppins-medium" placeholder="Password" value="<?php if(isset($pass_set)){ echo $pass_set; } ?>">
			</div>
			<div class="inputcontainer mb-3 remember-forgot">
				<div class="row">
					<div class="col-6 pl-0">
						<label for="remember-me" class="remember-me">
							<input name="remember-me" id="remember-me"  type="checkbox" class="remember-me" value="forever" checked/>&nbsp;&nbsp;<span>Remember me</span>
						</label>
					</div>
					<div class="col-6">
						<p class="forgot_pass"><a href="<?php echo site_url('lostpassword'); ?>">Forgot Password?</a></p>
					</div>
				</div>
			</div>
			<div class="inputcontainer mb-3">
				<input type="hidden" name="redirect_to" value="<?php echo $redirect_url;?>" />
				<div>
					<p class="error_msg poppins-medium"></p>
				</div>
				<span class="facebook_login cm_facebooklog fbllogin">
					<button type="submit" name="submit" value="Login" class="woocommerce-Button login button msbutton">Sign In</button>
				</span>
			</div>
		</form>
		<p class="dont-have poppins-semibold text-center">Don’t have an account yet? <a href="<?php echo home_url();?>/profile/register/" class="signupbottom-link">Sign up now</a></p>
	</div>
</div>
<script>
    jQuery(document).ready(function ($) {
        jQuery('.msbutton').on('click', function (e) {
			e.preventDefault();
			jQuery('#login_form').addClass('loading');
            var email_user = jQuery('#email').val();
            var password = jQuery('#password').val();
			var rememberme = jQuery('#rememberme').val();
			var redirect_url = "<?php echo $redirect_url; ?>";
			
			setTimeout(function(){
				jQuery.ajax({
					type: "post",
					dataType: "json",
					async: false,
					url: "<?php echo admin_url('admin-ajax.php'); ?>",
					data: {action: "check_login_user", email_user: email_user, password: password, rememberme: rememberme},
					success: function (msg) {
						response = "<b>ERROR: </b>  Your email/password combo is incorrect, please try again.";
						if (msg.errors) {
							if (msg.errors.incorrect_password) {
								var removedSpanString = removeElements(msg.errors.incorrect_password, "a");
								jQuery('.error_msg').html(response);
							} else if (msg.errors.invalid_username) {
								var removedSpanString = removeElements(msg.errors.invalid_username, "a");
								jQuery('.error_msg').html(response);
							}else if(msg.errors.empty_username){
								jQuery('.error_msg').html(msg.errors.empty_username);
							}else if(msg.errors.empty_password){
								jQuery('.error_msg').html(msg.errors.empty_password);
							}
							jQuery('#login_form').removeClass('loading');
							return false;
						} else {
							<?php 
								$user_login_details = $email.'_pass_'.$password;
							?>
							<?php if( isset($_POST['redirect_url']) ) { ?>
							// if( jQuery.inArray( "administrator", msg.roles ) != -1 ) {
								jQuery.get( "<?php echo home_url("/"); ?>", function( data ) {
									window.location.href = redirect_url;
								});
							// }
							// else {
								// jQuery.get( "<?php echo home_url("/"); ?>", function( data ) {
									// window.location.href = "<?php echo home_url('/profile'); ?>";
								// });
							// }
							<?php } else { ?>
							jQuery.get( "<?php echo home_url("/"); ?>", function( data ) {
								// jQuery('#login_form').removeClass('loading');
								window.location.href = redirect_url;
							});
							<?php } ?>
							//return true;
						}
					}
				});
			}, 1000);
            //return testing;
        });

		function removeElements(text, selector) {
			var wrapped = jQuery("<div>" + text + "</div>");
			wrapped.find(selector).remove();
			return wrapped.html();
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
