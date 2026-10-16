<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">
<?php
error_reporting(false);
session_start();

$title = 'Industry Profil';
$route = 'industry_profile';
include "./view/industri/template/head.php";

if ($_GET['tab'] == '') {
	$_GET['tab'] = 'identity';
} else {
	$tab = $_GET['tab'];
}


$id_user_company = $_SESSION['id_user_company'];
$sql_user = "SELECT user_company.* , company.* FROM user_company, company WHERE company.id_company = user_company.id_company AND user_company.id_user_company = :id_user_company";
$query = $_SERVER['PDO']->prepare($sql_user);
$query->execute(['id_user_company' => $id_user_company]);
$data = $query->fetch(PDO::FETCH_ASSOC);

?>




<body id="body">
	<div class="wrapper">

		<?php include "./view/industri/template/main-header.php"; ?>
		<?php include "./view/industri/template/sidebar.php"; ?>

		<div class="main-panel">
			<div class="container">
				<div class="panel-header bg-primary-gradient">
					<div class="page-inner py-5">
						<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
							<div>
								<h2 class="text-white pb-2 fw-bold">Profile</h2>
								<h5 class="text-white op-7 mb-2">Update Identity, Password or Profile Picture</h5>
							</div>

						</div>
					</div>
				</div>

				<div class="page-inner mt--5">
					<div class="row mt--2">

						<div class="col-md-4">


							<div class="card card-profile">
								<div class="card-header" style="background-image: url('viewer.php?src=profile_background'); height: 150px; background-size: cover;background-repeat: no-repeat;">

									<div class="profile-picture">
										<div class="avatar avatar-xxl">
											<img src="viewer.php?src=profile_picture" alt="..." class="avatar-img rounded-circle">
										</div>
									</div>
								</div>

								<div class="card-body">


									<div class="user-profile text-center mb-2">
										<div class="name"><?= $data['user_fullname'] ?></div>
										<div class="job"><?= $data['user_type'] ?? 'HRD' ?> at <?= $data['name'] ?? 'Politeknik Negeri Batam'; ?></div>

									</div>

								</div>


							</div>

						</div>

						<div class="col-md-8">
							<div class="card card-with-nav">
								<div class="card-header">
									<div class="row row-nav-line">
										<ul class="nav nav-tabs nav-line nav-color-secondary w-100 pl-4" role="tablist">
											<li class="nav-item"> <a class="nav-link show <?= checker($_GET['tab'], ['identity'], 'active') ?>" href="index.php?page=industry_profile&tab=identity" role="tab" aria-selected="true">Identity</a> </li>
											<li class="nav-item"> <a class="nav-link show <?= checker($_GET['tab'], ['password'], 'active') ?>" href="index.php?page=industry_profile&tab=password" role="tab" aria-selected="false">Password</a> </li>
											<li class="nav-item"> <a class="nav-link show <?= checker($_GET['tab'], ['profile'], 'active') ?>" href="index.php?page=industry_profile&tab=profile" role="tab" aria-selected="false">Profile</a> </li>
											<!-- <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#profile" role="tab" aria-selected="false">Profile</a> </li>
											<li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#settings" role="tab" aria-selected="false">Settings</a> </li> -->
										</ul>
									</div>
								</div>


								<?php
								if ($_GET['tab'] == 'identity') {
									view('industri/industry_profile/identity', ['data' => $data]);
								} else
								if ($_GET['tab'] == 'password') {
									view('industri/industry_profile/password', ['data' => $data]);
								} else
								if ($_GET['tab'] == 'profile') {
									view('industri/industry_profile/profile', ['data' => $data]);
								} else { ?>
									<div class="card-body">
										<!-- 404 Page Not Found -->
										<div id="container">
											<h1>404 Tab Not Found</h1>
											<p>Your requsted tab is not found in this server.</p>

										</div>
									</div>
								<?php }
								?>



							</div>
						</div>

					</div>
				</div>
			</div>

			<?php include "./view/industri/template/footer.php"; ?>

		</div>

	</div>

	<?php include "./view/industri/template/script.php"; ?>

	<!-- jQuery Validation -->
	<script src="./assets/js/plugin/jquery.validate/jquery.validate.min.js"></script>

	<!-- Soloalert -->
	<script src='./core/component/soloalert.js'></script>


	<!-- Hello -->
	<script type="text/javascript">
		$(document).ready(function() {
			<?php
			if (old('open-profile') != 'open') : echo 'disabled_form();';
			endif;
			?>
		});
	</script>


	<script>
		function submit_form() {
			console.log('helo there');
			$('form#form_profile').submit();
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

			$("#form_profile :input").prop("disabled", true);
			$("#form_profile").trigger("reset");

			$("small").empty();

			$("#form_profile :input").removeClass("is-invalid border-danger is-valid border-success");
		};

		function enable_form() {


			$("#edit_btn").prop("disabled", true);
			$("#edit_btn").prop("hidden", true);


			$("#cancel_btn").prop("hidden", false);
			$("#cancel_btn").prop("disabled", false);


			$("#save_btn").prop("disabled", false);

			$("#form_profile :input").prop("disabled", false);

			$("#form_profile :input").removeClass("is-invalid border-danger is-valid border-success");


		}

		function delete_profile() {

			let _token = $('meta[name="csrf-token"]').attr('content');

			swal.fire({
				title: 'Profile Remove Confirmation',
				text: 'Are you sure you want to remove current profile picture ?',
				icon: 'question',
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Yes, I'm sure !",
			}).then((result) => {
				/* Read more about isConfirmed, isDenied below */
				if (result.isConfirmed) {
					// AJAX
					$.ajax({
						url: "index.php?request=profile_remove&action=user_company",
						// type: "GET",
						type: "POST",
						data: {
							'token': _token,
							'_method': 'DELETE',
						},

						success: function() {
							setTimeout(function() {
								window.location.reload(1);
							}, 200);
						},
						
						error : function() {
							console.log('gagalssss');
						}
					});

				}
			})

		}
	</script>


</body>

</html>