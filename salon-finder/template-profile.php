<?php /* Template Name: Profile Page */

/**
 * changes in query by Navin: Changed pageination query due the inner loop form order to order item id, Now change to single query when it passes the order_item_id to get the order details
 */
global $wp_version;
global $wpdb;

use \DV\core\helpers\Constants;

define("POST_PER_PAGE", 10);
$paged1 = isset($_GET['paged1']) ? (int) $_GET['paged1'] : 1;
$paged2 = isset($_GET['paged2']) ? (int) $_GET['paged2'] : 1;

function joinOrderItem($join, $query)
{
	global $wpdb;
	$join .= " 
      left join {$wpdb->prefix}woocommerce_order_items orderItem on orderItem.order_id = {$wpdb->posts}.ID
      inner join {$wpdb->prefix}woocommerce_order_itemmeta woim on woim.order_item_id = orderItem.order_item_id and woim.meta_key ='voucherRedeemed' and woim.meta_value = 'NO'
    ";
	return $join;
}

if (is_user_logged_in()) {
	$current_user = wp_get_current_user();

	$upload_dir = wp_upload_dir();
	$select_arrs = array('test' => 'Test', 'test1' => 'Test1', 'test2' => 'Test2');
	$errors = array();
	$success_message = "";

	if ($_POST['f_name_save'] == 'Save') {
		$user_firstname = $wpdb->escape($_REQUEST['first_name']);
		if (empty($user_firstname)) {
			$errors['first_name'] = "Please enter a First name";
		}
		if (0 === count($errors)) {
			update_user_meta($current_user->ID, 'first_name', $user_firstname);
			$success_message = "Your profile has been successfully updated.";
		}
	}

	if ($_POST['l_name_save'] == 'Save') {
		$user_lastname = $wpdb->escape($_REQUEST['last_name']);
		if (empty($user_lastname)) {
			$errors['last_name'] = "Please enter a Last name";
		}
		if (0 === count($errors)) {
			update_user_meta($current_user->ID, 'last_name', $user_lastname);
			$success_message = "Your profile has been successfully updated.";
		}
	}

	if ($_POST['contact_num_save'] == 'Save') {
		$user_phone = $wpdb->escape($_REQUEST['contact_num']);
		if (empty($user_phone)) {
			$errors['contact_num'] = "Please enter a Contact Number";
		}
		if (0 === count($errors)) {
			update_user_meta($current_user->ID, 'billing_phone', $user_phone);
			$success_message = "Your profile has been successfully updated.";
		}
	}

	if ($_POST['dob_save'] == 'Save') {
		$user_dob = $wpdb->escape($_REQUEST['dob']);
		if (empty($user_dob)) {
			$errors['dob'] = "Please select Birthday";
		}
		if (0 === count($errors)) {
			$user_dob = get_user_meta($current_user->ID, 'dob', true);
			$success_message = "Your profile has been successfully updated.";
		}
	}

	if ($_POST['skin_type_save'] == 'Save') {
		$user_skin_type = $wpdb->escape($_REQUEST['skin_type']);
		if (empty($user_skin_type)) {
			$errors['skin_type'] = "Select Skin Type";
		}
		if (0 === count($errors)) {
			update_user_meta($current_user->ID, 'skin_type', $user_skin_type);
			$success_message = "Your profile has been successfully updated.";
		}
	}

	if ($_POST['skin_tone_save'] == 'Save') {
		$user_skin_tone = $wpdb->escape($_REQUEST['skin_tone']);
		if (empty($user_skin_tone)) {
			$errors['skin_tone'] = "Select Skin Tone";
		}
		if (0 === count($errors)) {
			update_user_meta($current_user->ID, 'skin_tone', $user_skin_tone);
			$success_message = "Your profile has been successfully updated.";
		}
	}

	if ($_POST['skin_undertone_save'] == 'Save') {
		$user_skin_under_tone = $wpdb->escape($_REQUEST['skin_under_tone']);
		if (empty($user_skin_under_tone)) {
			$errors['skin_under_tone'] = "Select Skin Undertone";
		}
		if (0 === count($errors)) {
			update_user_meta($current_user->ID, 'skin_under_tone', $user_skin_under_tone);
			$success_message = "Your profile has been successfully updated.";
		}
	}

	if ($_POST['ann_salary_save'] == 'Save') {
		$user_ann_salary = $wpdb->escape($_REQUEST['ann_salary']);
		if (empty($user_ann_salary)) {
			$errors['ann_salary'] = "Please enter a Annual Salary";
		}
		if (0 === count($errors)) {
			update_user_meta($current_user->ID, 'ann_salary', $user_ann_salary);
			$success_message = "Your profile has been successfully updated.";
		}
	}

	if ($_POST['update_submit'] == 'Update Profile') {
		$percentage = 0;

		$profile_photo = get_avatar(get_current_user_id());
		if (strpos($profile_photo, 'wpua-96x96.png')) {
			$profile_photo = '';
		} else
			$profile_photo = $profile_photo;

		$user_firstname = $wpdb->escape($_REQUEST['first_name']);
		if (empty($user_firstname)) {
			$errors['first_name'] = "Please enter a First name";
		}
		$user_lastname = $wpdb->escape($_REQUEST['last_name']);
		if (empty($user_lastname)) {
			$errors['last_name'] = "Please enter a Last Name";
		}
		$user_phone = $wpdb->escape($_REQUEST['contact_num']);
		if (empty($user_phone)) {
			$errors['contact_num'] = "Please enter a Contact Number";
		}
		$user_gender = $wpdb->escape($_REQUEST['gender']);
		if (empty($user_gender)) {
			$errors['gender'] = "Please select gender";
		}
		$user_dob = $wpdb->escape($_REQUEST['dob']);
		if (empty($user_dob)) {
			$errors['dob'] = "Please select Birthday";
		}
		// print_r($_REQUEST['skin_type']);die();
		$user_skin_type = $wpdb->escape($_REQUEST['skin_type']);

		if (empty($user_skin_type)) {
			$errors['skin_type'] = "Select Skin Type";
		}
		$user_skin_tone = $wpdb->escape($_REQUEST['skin_tone']);
		if (empty($user_skin_tone)) {
			$errors['skin_tone'] = "Select Skin Tone";
		}
		// print_r($_REQUEST['skin_under_tone']);die();
		$user_skin_under_tone = $wpdb->escape($_REQUEST['skin_under_tone']);
		if (empty($user_skin_under_tone)) {
			$errors['skin_under_tone'] = "Select Skin Undertone";
		}
		$user_ann_salary = $wpdb->escape($_REQUEST['ann_salary']);
		if (empty($user_ann_salary)) {
			$errors['ann_salary'] = "Please enter a Annual Salary";
		}

		$completion = array($profile_photo, $user_firstname, $user_lastname, $user_phone, $user_gender, $user_dob, $user_skin_type, $user_skin_tone, $user_skin_under_tone, $user_ann_salary);
		// echo '<pre>'; print_r($completion); echo '</pre>';
		$divider = count($completion);
		$add = 100 / $divider;
		foreach ($completion as $field) {
			// if it is set and it is not NULL, FALSE, '' or 0
			if (isset($field) && !empty($field)) {
				// Increment our counter
				$percentage = $percentage + $add;
			}
		}
		// print_r($errors);die();
		if (0 === count($errors)) {

			update_user_meta($current_user->ID, 'first_name', $user_firstname);
			update_user_meta($current_user->ID, 'last_name', $user_lastname);
			update_user_meta($current_user->ID, 'billing_phone', $user_phone);
			update_user_meta($current_user->ID, 'gender', $user_gender);
			update_user_meta($current_user->ID, 'dob', $user_dob);
			update_user_meta($current_user->ID, 'skin_type', $user_skin_type);
			update_user_meta($current_user->ID, 'skin_tone', $user_skin_tone);
			update_user_meta($current_user->ID, 'skin_under_tone', $user_skin_under_tone);
			update_user_meta($current_user->ID, 'ann_salary', $user_ann_salary);

			$type = get_user_meta($current_user->ID, 'register_type', true);
			$is_facebook = $type == 'facebook' ? 'yes' : 'no';
			$is_website = $type == 'website' ? 'yes' : 'no';

			$dob = explode("/", $_REQUEST['dob']);

			if ($_FILES['profile_foto']) {
				$file = $_FILES['profile_foto'];
				$filename = $file['name'];

				$ext = pathinfo($filename, PATHINFO_EXTENSION);
				$name = admin_create_slug($first_name) . '-' . admin_create_slug($last_name) . '-' . date("Y-m-d-G-i-s") . '.' . $ext;
				$path = $upload_dir['basedir'] . '/' . $name;
				$pa_url = $upload_dir['baseurl'] . '/' . $name;
				if (move_uploaded_file($file['tmp_name'], $path)) {
					update_user_meta($current_user->ID, 'profile_foto', $pa_url);
				}
			}
			$success_message = "Your profile has been successfully updated.";
		}
?>
		<script>
			jQuery(document).ready(function($) {
				$(".completed-progress").load(location.href + " .completed-progress>*");
				$(".completed-title").load(location.href + " .completed-title", "");
				$(".progress").load(location.href + " .progress>*", "");
			});
		</script>
	<?php
	} else {
		$percentage = 0;
		$prefix = $wpdb->prefix;
		$profile_photo = get_avatar(get_current_user_id());
		if (strpos($profile_photo, 'wpua-96x96.png')) {
			$profile_photo = '';
		} else {
			$profile_photo = $profile_photo;
		}
		$user_firstname = $current_user->user_firstname;
		$user_lastname = $current_user->user_lastname;
		$user_phone = get_user_meta($current_user->ID, 'billing_phone', true);
		$user_gender = get_user_meta($current_user->ID, 'gender', true);
		$user_dob = get_user_meta($current_user->ID, 'dob', true);
		$user_skin_type = get_user_meta($current_user->ID, 'skin_type', true);
		// print_r($user_skin_type);die();
		$user_skin_tone = get_user_meta($current_user->ID, 'skin_tone', true);
		$user_skin_under_tone = get_user_meta($current_user->ID, 'skin_under_tone', true);
		// print_r($user_skin_under_tone."xxx");die();
		$user_ann_salary = get_user_meta($current_user->ID, 'ann_salary', true);

		$fb_user_id = get_user_meta($current_user->ID, '_fb_user_id', true);
		$list_1 = get_user_meta($current_user->ID, 'list_1', true);
		$list_2 = get_user_meta($current_user->ID, 'list_2', true);

		$completion = array($profile_photo, $user_firstname, $user_lastname, $user_phone, $user_gender, $user_dob, $user_skin_type, $user_skin_tone, $user_skin_under_tone, $user_ann_salary);
		$divider = count($completion);
		$add = 100 / $divider;
		foreach ($completion as $field) {
			// if it is set and it is not NULL, FALSE, '' or 0
			if (isset($field) && !empty($field)) {
				// Increment our counter
				$percentage = $percentage + $add;
			}
		}
	}

	get_header();
	?>

	<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/node_modules/magnific-popup/dist/magnific-popup.css">
	<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/node_modules/magnific-popup/dist/jquery.magnific-popup.min.js"></script>
	<style>
		td.voucher-info>a {
			display: block;
			width: 100%;
			padding: 15px 0;
		}

		@media (max-width: 480px) {
			.mfp-wrap {
				position: fixed !important;
				top: 0 !important;
			}
		}
	</style>
	<div id="main-content">
		<div class="profile-header-container">
			<div class="container">
				<div class="row no-gutters">
					<h1 class="profile-text poppins-bold">My Account</h1>
				</div>
				<div class="row no-gutters name-photo">
					<div class="col-xs-1 col-lg-1 w-25 pr-3 profile-photo-wrapper">
						<!--i class="fas fa-user-circle user-color" aria-hidden="true"></i-->
						<?php
						// print_r( get_user_meta( $current_user->ID, 'profile_photo', false ) );
						// print_r( get_user_meta( $current_user->ID ) );
						$profile_photo = get_avatar(get_current_user_id());
						if (strpos($profile_photo, 'wpua-96x96.png')) :
							echo '<div class="profile-photo-initial">';
							if ($current_user->user_firstname && $current_user->user_lastname) :
								echo '<span class="initial-avatar">' . strtoupper($current_user->user_firstname[0]) . strtoupper($current_user->user_lastname[0]) . '</span>';
							else :
								list($id, $domain) = explode('@', $current_user->user_login);
								if (strpos($current_user->user_login, ".") !== false) :
									list($fname, $lname) = explode('.', $id);
									echo '<span class="initial-avatar">' . strtoupper($fname[0]) . strtoupper($lname[0]) . '</span>';
								endif;
							endif;
							echo '</div>';
						else :
							echo '<div class="profile-photo">' . get_avatar(get_current_user_id()) . '</div>';
						endif;
						?>
					</div>
					<script>
						jQuery(document).ready(function($) {
							$(".profile-photo-wrapper").load(location.href + " .profile-photo-wrapper>*", "");
							$(".completed-progress").load(location.href + " .completed-progress>*");
							$(".completed-title").load(location.href + " .completed-title", "");
							$(".progress").load(location.href + " .progress>*", "");
						});
					</script>
					<div class="col-xs-11 col-lg-11 w-75 welcome-photo align-self-center">
						<div class="row">
							<div class="ml-5">
								<span class="current-user poppins-semibold">Welcome,
									<span class="current-name poppins-semibold"><?php
																				$current_user = wp_get_current_user();

																				if ($current_user->user_firstname != "") :
																					echo stripslashes($current_user->user_firstname);
																				else :
																					echo stripslashes($current_user->user_login);
																				endif;
																				?>!
									</span>
								</span>
							</div>
						</div>
						<div class="row">
							<div class="ml-5">
								<span class="edit-photo poppins-medium"><a href="#upload-photo" class="edit-photo-btn">Edit Photo</a></span>
								<div id="upload-photo" class="mfp-hide">
									<h4>Edit Profile Photo</h4>
									<?php
									// do_action( 'edit_user_avatar', $current_user ); 
									echo do_shortcode('[avatar_upload]');
									?>
									<script>
										jQuery(document).ready(function($) {
											// wp.media.wpUserAvatar.frame().state('library').on('select', function() {
											// 	$('.wpua-edit input[type="submit"]').click();
											// });
											$('.edit-photo-btn').magnificPopup({
												type: 'inline',
												midClick: true
											});
										});
									</script>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="profile-body-container">
			<div class="container pb-5">
				<?php
				$queryArgs = [
					'status' => ['pending', 'processing', 'completed'],
					'customer_id' => $current_user->ID,
					'paginate' => true,
					'paged' => 1,
					'limit' => -1
				];
				add_filter('posts_join', 'joinOrderItem', 10, 2);
				// add_filter( 'posts_request', 'dump_request');
				$query = wc_get_orders($queryArgs);
				// echo '<pre>'; print_r($query); echo '</pre>';
				remove_filter('posts_join', 'joinOrderItem', 10, 2);
				if ($query->total) {
					$orderItems = [];
					foreach ($query->orders as $order) {
						foreach ($order->get_items('line_item') as $orderItem) {
							$orderItems[] = [
								'order' => $order,
								'item' => \DV\core\models\OrderItemProduct::init($orderItem)
							];
						}
					}
				}
				$countActiveVoucher = 0;
				if ($query->total) {
					foreach ($orderItems as $orderItem) {
						$item = $orderItem['item'];
						if ($item->getExpiryDate() !== '-') {
							$expiryDate = new \DateTime($item->getExpiryDate());
							$today = new \DateTime();
							if ($expiryDate > $today)
								$countActiveVoucher++;
						}
					}
				}
				?>
				<div id="" class="et_pb_module et_pb_tabs et_pb_tabs_0 et_slide_transition_to_1 et_slide_transition_to_0 my_account_page">
					<ul id="myTab" role="tablist" class="nav nav-tabs et_pb_tabs_controls poppins-semibold justify-content-center align-items-center pt-4" style="min-height: 31px;">
						<li class="nav-item profile-menu"><a class="nav-link active" href="#profile" data-toggle="tab" role="tab" aria-controls="profile" aria-selected="true">Profile</a></li>
						<li class="nav-item profile-menu"><a class="nav-link" href="#purchases" data-toggle="tab" role="tab" aria-controls="purchases" aria-selected="true">Purchases<span class="count purchase_totals"><?php echo $countActiveVoucher; ?></span></a></li>
						<!-- <li class="nav-item profile-menu"><a class="nav-link" href="#reviews">Reviews</a></li>
					<li class="nav-item profile-menu"><a class="nav-link" href="#comments">Comments</a></li> -->
					</ul>

					<!-- <a class="back_shop_mobile" href="<?php echo home_url("/"); ?>"><i class="fa fa-angle-left" aria-hidden="true"></i> Back To Salon Finder</a> -->

					<div class="tab-content profile-tab mt-4" id="myTabContent">
						<div id="profile" class="tab-pane active">
							<?php if (round($percentage) < 100) : ?>
								<div id="profile-content" class="completed-progress">
									<div class="et_pb_tab_content">
										<h1 class="completed-title poppins-medium"><?php echo round($percentage); ?>% Completed</h1>
										<div class="progress">
											<div class="progress-bar" style="width:<?php echo round($percentage); ?>%"></div>
										</div>
										<div class="row">
											<div class="col-12 fillin-profile poppins-medium">
												<span>Fill in the rest below to get 100%!</span>
											</div>
										</div>
									</div>
								</div>
							<?php endif; ?>
							<div id="profile-content">
								<div class="et_pb_tab_content">
									<h2 class="your-details poppins-medium">Your Details</h2>
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
									<form name="registerform" id="register_form" class="update_profile" action="" method="post" style="padding:0;" enctype="multipart/form-data">
										<?php /*if( 0 ) : ?>
										<div class="inputcontainer">
											<?php if(!empty($profile_foto)){ ?>
											<img src="<?php echo $profile_foto; ?>" alt="Profile Picture" width="96px;" height="96px;" />
											<label for="profile_foto">Profile Image</label>
											<input type="file" id="profile_foto" name="profile_foto" class="" accept="image/*">
										</div>
									<?php endif;*/ ?>
										<div class="inputcontainer pb-2">
											<div class="col-12 p-0">
												<div class="row">
													<div class="col-6">
														<label for="first_name" class="profile-label poppins-semibold pb-2" id="#f_name">First Name</label>
													</div>
													<div class="col-6">
														<a href='#f_name' class='edit-link poppins-semibold float-right pb-2'>Edit</a>
														<a href='#f_name' class='cancel-link poppins-semibold float-right pb-2'>Cancel</a>
													</div>
												</div>
											</div>
											<span class="span-profile poppins-medium"><?php echo isset($_POST['first_name']) ? stripslashes($_POST['first_name']) : stripslashes($current_user->user_firstname); ?></span>
											<div class="col-12 p-0">
												<div class="row">
													<div class="col-6">
														<input type="text" id="first_name" name="first_name" value="<?php echo isset($_POST['first_name']) ? stripslashes($_POST['first_name']) : stripslashes($current_user->user_firstname); ?>" class="form-control input-form-profile poppins-medium" />
													</div>
													<div class="col-6" style="padding-left: 78px;">
														<input type="submit" name="f_name_save" class="woocommerce-Button save f_name_save button msbutton" value="Save" style="display:none;" />
														<!-- <a href='?save=f_name_save' class="woocommerce-Button save f_name_save button msbutton" style="display:none;">Save</a> -->
													</div>
												</div>
											</div>
										</div>
										<div class="row no-gutters input-border mt-2"></div>
										<div class="inputcontainer pt-4 pb-2">
											<div class="col-12 p-0">
												<div class="row">
													<div class="col-6">
														<label for="last_name" class="profile-label poppins-semibold pb-2" id="#f_name">Last Name</label>
													</div>
													<div class="col-6">
														<a href='#l_name' class='edit-link poppins-semibold float-right pb-2'>Edit</a>
														<a href='#l_name' class='cancel-link poppins-semibold float-right pb-2'>Cancel</a>
													</div>
												</div>
											</div>
											<span class="span-profile poppins-medium"><?php echo isset($_POST['last_name']) ? stripslashes($_POST['last_name']) : stripslashes($current_user->user_lastname); ?></span>
											<div class="col-12 p-0">
												<div class="row">
													<div class="col-6">
														<input type="text" id="last_name" name="last_name" value="<?php echo isset($_POST['last_name']) ? stripslashes($_POST['last_name']) : stripslashes($current_user->user_lastname); ?>" class="form-control input-form-profile poppins-medium" />
													</div>
													<div class="col-6" style="padding-left: 78px;">
														<input type="submit" name="l_name_save" class="woocommerce-Button save l_name_save button msbutton" value="Save" style="display:none;" />
														<!-- <a href='#l_name_save' class="woocommerce-Button save l_name_save button msbutton" style="display:none;">Save</a> -->
													</div>
												</div>
											</div>
										</div>
										<div class="row no-gutters input-border mt-2"></div>
										<div class="inputcontainer pt-4 pb-2">
											<div class="col-12 p-0">
												<div class="row">
													<div class="col-6">
														<label for="user_email" class="profile-label poppins-semibold pb-2" id="#email">Email Address</label>
													</div>
													<!-- <div class="col-6">
													<a href='#email' class='edit-link poppins-semibold float-right pb-2'>Edit</a>
													<a href='#email' class='cancel-link poppins-semibold float-right pb-2'>Cancel</a>
												</div> -->
												</div>
											</div>
											<span class="span-profile poppins-medium"><?php echo isset($_POST['user_email']) ? $_POST['user_email'] : $current_user->user_email; ?></span>
											<input type="text" disabled="" name="user_email" id="user_email" value="<?php echo isset($_POST['user_email']) ? $_POST['user_email'] : $current_user->user_email; ?>" class="form-control input-form-profile poppins-medium" />
										</div>
										<div class="row no-gutters input-border mt-2"></div>
										<div class="inputcontainer pt-4 pb-2">
											<div class="col-12 p-0">
												<div class="row">
													<div class="col-6">
														<label for="contact_num" class="profile-label poppins-semibold pb-2" id="#contact">Contact Number</label>
													</div>
													<div class="col-6">
														<a href='#contact' class='edit-link poppins-semibold float-right pb-2'>Edit</a>
														<a href='#contact' class='cancel-link poppins-semibold float-right pb-2'>Cancel</a>
													</div>
												</div>
											</div>
											<span class="span-profile poppins-medium"><?php echo isset($_POST['contact_num']) ? $_POST['contact_num'] : $user_phone; ?></span>
											<div class="col-12 p-0">
												<div class="row">
													<div class="col-6">
														<input type="number" id="contact_num" name="contact_num" value="<?php echo isset($_POST['contact_num']) ? $_POST['contact_num'] : $user_phone; ?>" class="form-control input-form-profile poppins-medium" />
													</div>
													<div class="col-6" style="padding-left: 78px;">
														<input type="submit" name="contact_num_save" class="woocommerce-Button save contact_num_save button msbutton" value="Save" style="display:none;" />
														<!-- <a href='#contact_num_save' class="woocommerce-Button save contact_num_save button msbutton" style="display:none;">Save</a> -->
													</div>
												</div>
											</div>
										</div>
										<div class="row no-gutters input-border mt-2"></div>
										<div class="inputcontainer pt-4 pb-2 gander_maill">
											<div class="col-12 p-0">
												<div class="row">
													<div class="col-md-6 col-xs-12">
														<label for="gender" class="mmal profile-label poppins-semibold pb-2" id="#gender">Gender</label>
													</div>
												</div>
												<?php
												$u_gender = !empty($_POST['gender']) ? $_POST['gender'] : $user_gender;
												$male_ch = ($u_gender == 'male') ? 'checked' : '';
												$female_ch = ($u_gender == 'female') ? 'checked' : '';
												?>
												<div class="col-md-6 col-xs-12 p-0">
													<div class="row">
														<div class="col-md-6 col-xs-12">
															<input type="radio" name="gender" value="male" class="form-check-gender mmal" <?php echo $male_ch; ?>><span class="male_ntm poppins-medium">Male</span>&nbsp;&nbsp;
															<input type="radio" name="gender" value="female" class="form-check-gender mmal" <?php echo $female_ch; ?>><span class="male_ntm poppins-medium">Female</span>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="row no-gutters input-border mt-2"></div>
										<div class="inputcontainer pt-4 pb-2 dg">
											<div class="col-12 p-0">
												<div class="row">
													<div class="col-6">
														<label for="dob" class="profile-label poppins-semibold pb-2" id="#birthday">Birthday</label>
													</div>
													<div class="col-6">
														<a href='#birthday' class='edit-link poppins-semibold float-right pb-2'>Edit</a>
														<a href='#birthday' class='cancel-link poppins-semibold float-right pb-2'>Cancel</a>
													</div>
												</div>
											</div>
											<span class="span-profile poppins-medium"><?php echo isset($_POST['dob']) ? $_POST['dob'] : $user_dob; ?></span>
											<div class="col-12 p-0">
												<div class="row">
													<div class="col-6">
														<input type="text" name="dob" value="<?php echo isset($_POST['dob']) ? $_POST['dob'] : $user_dob; ?>" readonly="readonly" class="date_picker input-form-profile form-control">
													</div>
													<div class="col-6" style="padding-left: 78px;">
														<input type="submit" name="dob_save" class="woocommerce-Button save dob_save button msbutton" value="Save" style="display:none;" />
														<!-- <a href='#dob_save' class="woocommerce-Button save dob_save button msbutton" style="display:none;">Save</a> -->
													</div>
												</div>
											</div>
										</div>
										<div class="row no-gutters input-border mt-2"></div>
										<div class="inputcontainer pt-4 pb-2">
											<div class="col-12 p-0">
												<div class="row">
													<div class="col-6">
														<label for="skin_type_text" class="profile-label poppins-semibold pb-2" id="#skin_type_text">Skin Type</label>
													</div>
													<div class="col-6">
														<a href='#skin_type_text' class='edit-link poppins-semibold float-right pb-2'>Edit</a>
														<a href='#skin_type_text' class='cancel-link poppins-semibold float-right pb-2'>Cancel</a>
													</div>
												</div>
											</div>
											<span class="span-profile poppins-medium"><?php echo isset($_POST['skin_type']) ? stripslashes($_POST['skin_type']) : stripslashes($user_skin_type); ?></span>
											<div class="col-12 p-0">
												<div class="row">
													<div class="col-8">
														<select id="skin_type" name="skin_type" class="form-control input-form-profile poppins-medium">
															<option value="" disabled selected value>Select your skin type</option>
															<?php
															if ($_POST['skin_type'])
																$user_skin_type = $_POST['skin_type'];
															else
																$user_skin_type = $user_skin_type;

															$skin_type_db = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}skin_type", OBJECT);
															foreach ($skin_type_db as $select_type_arr) {
																$select_ty = ($select_type_arr->skin_type == $user_skin_type) ? 'selected' : '';
																echo '<option ' . $select_ty . ' value="' . $select_type_arr->skin_type . '">' . $select_type_arr->skin_type . '</option>';
															}
															?>
														</select>
													</div>
													<div class="col-4" style="padding-left: 22px;">
														<input type="submit" name="skin_type_save" class="woocommerce-Button save skin_type_save button msbutton" value="Save" style="display:none;" />
														<!-- <a href='#skin_type_save' class="woocommerce-Button save skin_type_save button msbutton" style="display:none;">Save</a> -->
													</div>
												</div>
											</div>
										</div>
										<div class="row no-gutters input-border mt-2"></div>
										<div class="inputcontainer pt-4 pb-2">
											<div class="col-12 p-0">
												<div class="row">
													<div class="col-6">
														<label for="skin_tone_text" class="profile-label poppins-semibold pb-2" id="#skin_tone_text">Skin Tone</label>
													</div>
													<div class="col-6">
														<a href='#skin_tone_text' class='edit-link poppins-semibold float-right pb-2'>Edit</a>
														<a href='#skin_tone_text' class='cancel-link poppins-semibold float-right pb-2'>Cancel</a>
													</div>
												</div>
											</div>
											<span class="span-profile poppins-medium"><?php echo isset($_POST['skin_tone']) ? stripslashes($_POST['skin_tone']) : stripslashes($user_skin_tone); ?></span>
											<div class="col-12 p-0">
												<div class="row">
													<div class="col-8">
														<select id="skin_tone" name="skin_tone" class="form-control input-form-profile poppins-medium">
															<option value="" disabled selected value>Select your skin tone</option>
															<?php
															if ($_POST['skin_tone'])
																$user_skin_tone = $_POST['skin_tone'];
															else
																$user_skin_tone = $user_skin_tone;

															$skin_tone_db = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}skin_tone", OBJECT);
															foreach ($skin_tone_db as $select_tone_arr) {
																$select_to = ($select_tone_arr->skin_tone == $user_skin_tone) ? 'selected' : '';
																echo '<option ' . $select_to . ' value="' . $select_tone_arr->skin_tone . '">' . $select_tone_arr->skin_tone . '</option>';
															}
															?>
														</select>
													</div>
													<div class="col-4" style="padding-left: 22px;">
														<input type="submit" name="skin_tone_save" class="woocommerce-Button save skin_tone_save button msbutton" value="Save" style="display:none;" />
														<!-- <a href='#skin_tone_save' class="woocommerce-Button save skin_tone_save button msbutton" style="display:none;">Save</a> -->
													</div>
												</div>
											</div>
										</div>
										<!-- <div class="row no-gutters input-border mt-2"></div> -->
										<div class="inputcontainer pb-2">
											<img class="tone" src="Salonfinder-shades.png" />
										</div>
										<div class="row no-gutters input-border mt-2"></div>
										<div class="inputcontainer pt-4 pb-2">
											<div class="col-12 p-0">
												<div class="row">
													<div class="col-6">
														<label for="skin_under_tone" class="profile-label poppins-semibold pb-2" id="#skin_undertone">Skin Undertone</label>
													</div>
													<div class="col-6">
														<a href='#skin_undertone' class='edit-link poppins-semibold float-right pb-2'>Edit</a>
														<a href='#skin_undertone' class='cancel-link poppins-semibold float-right pb-2'>Cancel</a>
													</div>
												</div>
											</div>
											<span class="span-profile poppins-medium"><?php echo isset($_POST['skin_under_tone']) ? stripslashes($_POST['skin_under_tone']) : stripslashes($user_skin_under_tone); ?></span>
											<div class="col-12 p-0">
												<div class="row">
													<div class="col-8">
														<select id="skin_under_tone" name="skin_under_tone" class="form-control input-form-profile poppins-medium">
															<option value="" disabled selected value>Select your skin undertone</option>
															<?php
															if ($_POST['skin_under_tone'])
																$user_skin_under_tone = $_POST['skin_under_tone'];
															else
																$user_skin_under_tone = $user_skin_under_tone;

															$skin_under_tone_db = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}skin_undertone", OBJECT);
															foreach ($skin_under_tone_db as $select_undertone_arr) {
																$select_under_to = ($select_undertone_arr->skin_undertone == $user_skin_under_tone) ? 'selected' : '';
																echo '<option ' . $select_under_to . ' value="' . $select_undertone_arr->skin_undertone . '">' . $select_undertone_arr->skin_undertone . '</option>';
															}
															?>
														</select>
													</div>
													<div class="col-4" style="padding-left: 22px;">
														<input type="submit" name="skin_undertone_save" class="woocommerce-Button save skin_undertone_save button msbutton" value="Save" style="display:none;" />
														<!-- <a href='#skin_undertone_save' class="woocommerce-Button save skin_undertone_save button msbutton" style="display:none;">Save</a> -->
													</div>
												</div>
											</div>
										</div>
										<div class="row no-gutters input-border mt-2"></div>
										<div class="inputcontainer pt-4 pb-2">
											<div class="col-12 p-0">
												<div class="row">
													<div class="col-6">
														<label for="ann_salary" class="profile-label poppins-semibold pb-2" id="salary">Annual Salary (SGD)</label>
													</div>
													<div class="col-6">
														<a href='#salary' class='edit-link poppins-semibold float-right pb-2'>Edit</a>
														<a href='#salary' class='cancel-link poppins-semibold float-right pb-2'>Cancel</a>
													</div>
												</div>
											</div>
											<span class="span-profile poppins-medium"><?php echo isset($_POST['ann_salary']) ? stripslashes($_POST['ann_salary']) : stripslashes($user_ann_salary); ?></span>
											<div class="col-12 p-0">
												<div class="row">
													<div class="col-9">
														<select type="number" id="ann_salary" name="ann_salary" class="form-control input-form-profile poppins-medium">
															<option value="" disabled selected value>Select your salary</option>
															<option <?php echo $user_ann_salary == 'less than $30,000' ? 'selected' : ''; ?> value="less than $30,000">Less than $30,000</option>
															<option <?php echo $user_ann_salary == '$30,000 - $41,000' ? 'selected' : ''; ?> value="$30,000 - $41,000">$30,000 - $41,000</option>
															<option <?php echo $user_ann_salary == '$41,001 - $53,000' ? 'selected' : ''; ?> value="$41,001 - $53,000">$41,001 - $53,000</option>
															<option <?php echo $user_ann_salary == '$53,001 - $65,000' ? 'selected' : ''; ?> value="$53,001 - $65,000">$53,001 - $65,000</option>
															<option <?php echo $user_ann_salary == '$65,001 - $77,000' ? 'selected' : ''; ?> value="$65,001 - $77,000">$65,001 - $77,000</option>
															<option <?php echo $user_ann_salary == '$77,001 - $89,000' ? 'selected' : ''; ?> value="$77,001 - $89,000">$77,001 - $89,000</option>
															<option <?php echo $user_ann_salary == '$89,001 - $111,000' ? 'selected' : ''; ?> value="$89,001 - $111,000">$89,001 - $111,000</option>
															<option <?php echo $user_ann_salary == 'more than $111,000' ? 'selected' : ''; ?> value="more than $111,000">More than $111,000</option>
															<option <?php echo $user_ann_salary == 'student/homemaker/unemployed' ? 'selected' : ''; ?> value="student/homemaker/unemployed">Student/homemaker/unemployed</option>
														</select>
													</div>
													<div class="col-3" style="padding-left: 0px;">
														<input type="submit" name="ann_salary_save" class="woocommerce-Button save ann_salary_save button msbutton" value="Save" style="display:none;" />
														<!-- <a href='#ann_salary_save' class="woocommerce-Button save ann_salary_save button msbutton" style="display:none;">Save</a> -->
													</div>
												</div>
											</div>
										</div>
										<?php echo do_shortcode('[acf field="skin_type"]'); ?>
										<div class="row no-gutters input-border mt-2"></div>
										<p class="pt-4"><a href="<?php echo site_url('change-password'); ?>" class="change-password poppins-medium">Change My Password</a></p>
								</div>
							</div>
							<br><br>
							<!-- <div class="updatebtn"><input type="submit" name="update_submit" id="wp-submit" class="msbutton updatebttn poppins-semibold" value="Update Profile" /></div> -->
							<input type="submit" name="update_submit" id="wp-submit" class="msbutton updatebttn poppins-semibold" value="Update Profile" />
							</form>
						</div>
						<div id="purchases" class="tab-pane" role="tabpanel">
							<p class="purchase-note poppins-semibold">It may take about 5 to 10 minutes before your voucher order is reflected in the Purchase page.</p>
							<?php echo do_shortcode('[valid-voucher]'); ?>
							<?php echo do_shortcode('[redeemed-voucher]'); ?>
							<?php echo do_shortcode('[expired-voucher]'); ?>
							<?php echo do_shortcode('[inactive-voucher]'); ?>
						</div>
						<!-- <div id="reviews" class="tab-pane" role="tabpanel" >
						<div class="et_pb_tab_content">
							Reviews
						</div>
					</div>
					<div id="comments" class="tab-pane" role="tabpanel" >
						<div class="et_pb_tab_content">
							Comments
						</div>
					</div> -->
					</div>
				</div>
			</div>
		</div>
	</div>
	<style type="text/css">
		.update_profile.load,
		.change_password.load {
			position: relative;
			display: block !important;
		}

		.update_profile.load:after,
		.change_password.load:after {
			content: "";
			position: absolute;
			width: 100%;
			height: 100%;
			top: 0;
			left: 0;
			background: rgba(255, 255, 255, 0.5);
			z-index: 11;
			cursor: wait;
		}
	</style>

	<script>
		jQuery(document).ready(function($) {

			var pairs = location.search.slice(1).split('&');
			var result = {};
			pairs.forEach(function(pair) {
				pair = pair.split('=');
				result[pair[0]] = decodeURIComponent(pair[1] || '');
			});

			if (result.page != '' || result.page != undefined) {
				console.log('1');
				if (result.page == 'purchases') {
					console.log('2');
					activaTab('purchases');
				}
			}

			//Edit
			$('.edit-link').click(function() {
				$(this).hide();
				$(this).parent().find('a.cancel-link').show();

				var prnt = $(this).parent().parent().parent().parent();
				prnt.find('.span-profile').hide();
				prnt.find('.input-form-profile').show();
				prnt.find('.save').show();
			});

			//Cancel
			$('.cancel-link').click(function() {
				$(this).hide();
				$(this).parent().find('a.edit-link').show();

				var prnt = $(this).parent().parent().parent().parent();
				prnt.find('.span-profile').show();
				prnt.find('.input-form-profile').hide();
				prnt.find('.save').hide();
			});

			//Save
			$('.save.f_name_save').submit(function() {
				$(this).hide();
				$(this).parent().parent().parent().parent().find('a.cancel-link').hide();
				$(this).parent().parent().parent().parent().find('a.edit-link').show();

				var prnt = $(this).parent().parent().parent().parent();
				prnt.find('.span-profile').show();
				prnt.find('.input-form-profile').hide();
				txtval = prnt.find('.input-form-profile').val();
				selval = prnt.find('.input-form-profile option:selected').val();
				if (txtval) {
					prnt.find('.span-profile').html(txtval);
				} else {
					prnt.find('.span-profile').html(selval);
				}
				prnt.find('.save.f_name_save').hide();

				// var data = {
				// 	'action' : 'f_name_save',
				// 	'txtval': txtval
				// };

				// $.ajax({
				// 	// url: '/profile',
				// 	type: 'post',
				// 	data: data,
				// 	success: function(data){
				// 		console.log(data);
				// 	}
				// });

				// $( ".firstx" ).load( location.href + " .firstx>*", "" );
			});

			// $('.save.f_last_save').click(function()
			// { 
			// 	$(this).hide();
			// 	$(this).parent().parent().parent().parent().find('a.cancel-link').hide();
			// 	$(this).parent().parent().parent().parent().find('a.edit-link').show();

			//     var prnt=$(this).parent().parent().parent().parent();
			// 	prnt.find('.span-profile').show();
			// 	prnt.find('.input-form-profile').hide();
			// 	// txtval = prnt.find('.input-form-profile').val();
			// 	// selval = prnt.find('.input-form-profile option:selected').val();
			// 	// if(txtval) {
			// 	// 	prnt.find('.span-profile').html(txtval);
			// 	// }
			// 	// else{
			// 	// 	prnt.find('.span-profile').html(selval);
			// 	// }
			// 	prnt.find('.save.f_last_save').hide();
			// $("[name='f_last_save']").trigger('click');
			// });

			var elem = jQuery(".update_profile").formProgressBar({
				transitionTime: 500
			});

			jQuery('#wp-submit').click(function() {
				jQuery(".update_profile").addClass('load');
			});
			jQuery('#for-submit').click(function() {
				jQuery(".change_password").addClass('load');
			});

			$(".thumb").click(
				function() {
					if ($(this).children('.blurb')[0].style.display == '' || $(this).children('.blurb')[0].style.display == 'none') {
						$(this).children('.blurb')[0].style.display = 'block';
						console.log($(this).children('.blurb')[0].style);
					} else {
						$(this).children('.blurb')[0].style.display = 'none';
					}
				}
			);

			$('.email-btn').on('click', function(e) {


				// e.stopImmediatePropagation();
				e.preventDefault();

				var _this = $(this);


				// if( _this.hasClass('checked') ) {
				/*$(this).attr('disable', true);
				$(this).addClass('disable');
				*/
				var current_user_firstname = $('#current_user_firstname').val();
				var current_user_lastname = $('#current_user_lastname').val();
				var current_user_email = $('#current_user_email').val();
				var current_user_phone = $('#current_user_phone').val();

				var tr_parent_obj = $(this).parent().parent();

				var customer_order_id = $(tr_parent_obj).find('.customer-order-id').val();
				var product_id = $(tr_parent_obj).find('.product-id').val();
				var product_name = $(tr_parent_obj).find('.product-name').val();

				var item_id = $(tr_parent_obj).find('.item-id').val();
				var item_name = $(tr_parent_obj).find('.item-name').val();
				var variation_id = $(tr_parent_obj).find('.variation-id').val();
				var quantity = $(tr_parent_obj).find('.quantity').val();
				var total = $(tr_parent_obj).find('.total').val();
				var expiredate = $(tr_parent_obj).find('.expiredate').val();

				var postData = {
					'firstName': current_user_firstname,
					'lastName': current_user_lastname,
					'email': current_user_email,
					'phone': current_user_phone,
					'id': customer_order_id,
					'type': 'email_me',
					'itemId': item_id,
					'itemName': item_name,
					'productId': product_id,
					'variationId': variation_id,
					'quantity': quantity,
					'total': total,
					'expiredate': expiredate
				};

				jQuery.ajax({
					url: '<?php echo VOUCHERS_API ?>qr/send',
					type: 'POST',
					dataType: 'json',
					data: postData,
					success: function(data) {
						alert('Email has been sent!');
						/*$(this).removeAttr('disable');
						$(this).removeClass('disable');*/

					}
				});

				// if( ! _this.hasClass('checked') ) {
				_this.attr('disabled', 'disabled');
				// }

				setTimeout(function() {
					_this.removeAttr('disabled');
					// _this.addClass('checked');
				}, 120000);

				// }
			});
		});
	</script>

	<style type="text/css">
		.profile-table ul {
			list-style: disc;
			padding-left: 26px;
		}
	</style>
<?php } else {
?>
	<script type="text/javascript">
		window.location.href = "<?php echo site_url(); ?>";
	</script>

<?php } ?>

<script>
	jQuery(document).ready(function($) {
		$('.profile-menu a').on('click', function() {
			var hash = $(this).attr('href');
			// console.log( hash );
			if (history.pushState) {
				var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + hash;
				window.history.pushState({
					path: newurl
				}, '', newurl);
			}
		});
	});
</script>

<?php get_footer(); ?>