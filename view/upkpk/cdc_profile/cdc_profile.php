<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">
<?php
error_reporting(false);
session_start();

$title = 'CDC Profile';
$route = 'cdc_profile';
include "./view/upkpk/template/head.php";


if (in_array($_GET['tab'], [NULL, ''])) {
	$_GET['tab'] = 'identity';
} else {
	$_GET['tab'] = $_GET['tab'];
}


?>


<body id="body">
	<div class="wrapper">

		<?php include "./view/upkpk/template/main-header.php"; ?>
		<?php include "./view/upkpk/template/sidebar.php"; ?>

		<div class="main-panel">
			<div class="container">

				<div class="panel-header bg-primary-gradient">
					<div class="page-inner py-5">
						<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
							<div>
								<h2 class="text-white pb-2 fw-bold">Profile</h2>
								<h5 class="text-white op-7 mb-2">Update Identity or Profile Picture or Change Password</h5>
							</div>
						</div>
					</div>
				</div>



				<div class="page-inner mt--5">
					<div class="row mt--2">
						<div class="col-md-12">
							<div class="card card-with-nav">
								<div class="card-header">
									<div class="row row-nav-line">
										<ul class="nav nav-tabs nav-line nav-color-secondary w-100 pl-4" role="tablist">
											<li class="nav-item"> <a class="nav-link <?php checker($_GET['tab'], ['identity', NULL], 'active') ?>" href="index.php?page=cdc_profile" role="tab" aria-selected="true">Identity</a> </li>
											<li class="nav-item"> <a class="nav-link <?php checker($_GET['tab'], ['profile'], 'active') ?>" href="index.php?page=cdc_profile&tab=profile" role="tab" aria-selected="false">Profile Picture</a> </li>
											<li class="nav-item"> <a class="nav-link <?php checker($_GET['tab'], ['password'], 'active') ?>" href="index.php?page=cdc_profile&tab=password" role="tab" aria-selected="false">Password</a> </li>
											<!-- <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#settings" role="tab" aria-selected="false">Settings</a> </li> -->
										</ul>
									</div>
								</div>


								<?php

								switch ($_GET['tab']):
									default:
										view('upkpk/cdc_profile/profile_404');
										break;

									case 'password':
										view('upkpk/cdc_profile/profile_password');
										break;

									case 'identity':
										view('upkpk/cdc_profile/profile_identity');

										break;

									case 'profile':
										view('upkpk/cdc_profile/profile_picture');
										break;
								endswitch;
								?>

							</div>
						</div>



					</div>
				</div>
			</div>

			<?php include "./view/upkpk/template/footer.php"; ?>

		</div>

	</div>

	<?php include "./view/upkpk/template/script.php"; ?>

	<!-- jQuery Validation -->
	<script src="./assets/js/plugin/jquery.validate/jquery.validate.min.js"></script>


	<!-- Soloalert -->
	<script src='./core/component/soloalert.js'></script>



	<script>
		$(document).ready(function() {
			<?= (old('open-profile') == 'open') ? 'enable_form()' : 'disabled_form()'; ?>
		});
	</script>

	<script>
		function submit_form() {
			$('form#form').submit();
		}

		function confirmation() {
			// alert('Are you sure you want to cancel ? (Your change will not be saved)');
			SoloAlert.confirm({
				title: "Are you sure you want to cancel ?",
				body: "Changes you made will not be saved !",
				useTransparency: true,
				onOk: () => {
					disabled_form()
				}
			});

		}

		function disabled_form() {

			$("#edit_btn").prop("disabled", false);
			$("#edit_btn").prop("hidden", false);


			$("#cancel_btn").prop("hidden", true);
			$("#cancel_btn").prop("disabled", true);

			$("#save_btn").prop("disabled", true);

			$("#form :input").prop("disabled", true);
			$("#form").trigger("reset");

			$("small").empty();

			$("#form :input").removeClass("is-invalid border-danger is-valid border-success");

		}

		function enable_form() {


			$("#edit_btn").prop("disabled", true);
			$("#edit_btn").prop("hidden", true);


			$("#cancel_btn").prop("hidden", false);
			$("#cancel_btn").prop("disabled", false);


			$("#save_btn").prop("disabled", false);

			$("#form :input").prop("disabled", false);

			$("#form :input").removeClass("is-invalid border-danger is-valid border-success");


		}
	</script>



</body>

</html>