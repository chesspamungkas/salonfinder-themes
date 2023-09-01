<?php /* Template Name: Change Password Page */
$user_login_details = $email.'_pass_'.$password;
if (is_user_logged_in()) {
	$errors = array();
    $success_message = "";

	if ($_POST['up_pass'] == 'Update Password') {

		// $user = get_user_by('login', $current_user->user_login);
		$user = wp_get_current_user();
		// print_r($user);die();
		if ($user && wp_check_password($_POST['old_pass'], $user->data->user_pass, $user->ID)) {
		} else {
			$errors['old_password_confirmation'] = "Old Password doesn't match";
		}

		if (0 === preg_match("/.{6,}/", $_POST['user_pass'])) {
			$errors['password'] = "Password must be at least six characters";
		}
		if (0 !== strcmp($_POST['user_pass'], $_POST['cn_user_pass'])) {
			$errors['password_confirmation'] = "Passwords do not match";
		}

		if (0 === count($errors)) {
			wp_set_password($_POST['user_pass'], $user->ID );
			wp_set_current_user($user->user_login);
			wp_set_auth_cookie($user->ID);
			do_action('wp_login', $user->user_login, $user);
			$redirect_url222 = "/password-changed-successfully";
			// wp_redirect( $redirect_url222 );exit;
			?>
			<script type="text/javascript">
				window.location.href = "<?php echo $redirect_url222; ?>";
			</script>
			<?php
		}
	}
	get_header();
?>
<div id="main-content" class="changepass-main text-center">
	<div class="container">
		<h1 class="changepass-text poppins-bold mb-4">Change Password</h1>
		<div class="cm_pro_notification">
			<?php
			if ($errors) {
				foreach ($errors as $error) {
					echo '<p class="error_msg poppins-medium">' . $error . '</p>';
				}
			}
			if ($success_message != "") {
				echo '<p class="error_msg poppins-medium">' . $success_message . '</p>';
			}
			?>
		</div>
		<form method="post" action="" class="change_password" id="register_form">
			<div class="inputcontainer mb-3">
				<input type="password" id="old_pass" name="old_pass" value="" class="form-control input-form poppins-medium" placeholder="Old Password">
			</div>
			<div class="inputcontainer mb-3">
				<input type="password" id="user_pass" name="user_pass" value="" class="form-control input-form poppins-medium" placeholder="New Password">
			</div>
			<div class="inputcontainer mb-5">
				<input type="password" id="cn_user_pass" name="cn_user_pass" value="" class="form-control input-form poppins-medium" placeholder="Confirm New Password">
			</div>
			<div class="inputcontainer mb-3">
				<input type="submit" name="up_pass" id="for-submit" class="woocommerce-Button msbutton" value="Update Password" />
			</div>
			<div class="clear"></div>
		</form>
	</div>
</div>
<style type="text/css">
.change_password.load {
	position: relative;
	display: block !important;
}

.change_password.load:after {
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
<script>
    jQuery(document).ready(function ($) {
        jQuery('#for-submit').click(function(){
			jQuery(".change_password").addClass('load');
		});
    });
</script>
<?php } else {
    ?>
	<script type="text/javascript">
		window.location.href = "<?php echo site_url(); ?>";
	</script>
	
	<?php
}
get_footer();?>
