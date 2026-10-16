<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">
<?php
error_reporting(false);
session_start();

$title = 'Student Identity';
$route = 'student_identity';
include "./view/mahasiswa/template/head.php";


?>

<?php

$nim = $_SESSION['nim'];


// Get Student Data
$query = $_SERVER['PDO']->prepare("SELECT * FROM student_internship WHERE nim = :nim LIMIT 1");
$query->execute(array('nim' => $nim));

if ($query->rowCount() != 1) {
	make_swal2('warning', 'Not Allowed', 'You don`t have access to this page !');
	redirect_back(1);
	exit;
}

$data = $query->fetch(PDO::FETCH_ASSOC);


if (in_array($_GET['tab'], [NULL, ''])) {
	$_GET['tab'] = 'identity';
} else {
	$_GET['tab'] = $_GET['tab'];
}

// Get Data Kampus
$query_kampus = $_SERVER['PDO']->prepare("SELECT * FROM tb_kampus WHERE id_kampus = :id_kampus");
$query_kampus->execute(array('id_kampus' => $data['id_kampus']));

$university_data = $query_kampus->fetch(PDO::FETCH_ASSOC);

// Get All Prodi
$query_prodi = $_SERVER['PDO']->prepare("SELECT * FROM program_study WHERE id_kampus = :id_kampus");
$query_prodi->execute(array('id_kampus' => $data['id_kampus']));

$data_prodi = $query_prodi->fetchAll(PDO::FETCH_ASSOC);


?>


<body id="body">
	<div class="wrapper">

		<?php include "./view/mahasiswa/template/main-header.php"; ?>
		<?php include "./view/mahasiswa/template/sidebar.php"; ?>

		<div class="main-panel">
			<div class="container">
				<div class="panel-header bg-primary-gradient">
					<div class="page-inner py-5">
						<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
							<div>
								<h2 class="text-white pb-2 fw-bold">Profile</h2>
								<h5 class="text-white op-7 mb-2">Update Identity or Profile Picture</h5>
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
										<div class="name"><?= $data['name'] ?></div>
										<div class="job">Student at <?= ($_SESSION['POLIBATAM_STUDENT']) ? 'Politeknik Negeri Batam' : $university_data['nama_kampus'] ?? 'Politeknik Negeri Batam'; ?></div>

									</div>


								</div>


							</div>

						</div>

						<div class="col-md-8">

							<div class="card card-with-nav">
								<div class="card-header">
									<div class="row row-nav-line">
										<ul class="nav nav-tabs nav-line nav-color-secondary w-100 pl-4" role="tablist">
											<li class="nav-item"> <a class="nav-link <?php checker($_GET['tab'], ['identity', NULL], 'active') ?>" href="index.php?page=student_identity" role="tab" aria-selected="true">Identity</a> </li>
											<li class="nav-item"> <a class="nav-link <?php checker($_GET['tab'], ['profile'], 'active') ?>" href="index.php?page=student_identity&tab=profile" role="tab" aria-selected="false">Profile Picture</a></li>

											<?php if (!$_SESSION['POLIBATAM_STUDENT']) { ?>
												<li class="nav-item"> <a class="nav-link <?php checker($_GET['tab'], ['password'], 'active') ?>" href="index.php?page=student_identity&tab=password" role="tab" aria-selected="false">Password</a> </li>
												<!-- <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#settings" role="tab" aria-selected="false">Password</a> </li> -->
											<?php } ?>
										</ul>
									</div>
								</div>



								<?php
								if (in_array($_GET['tab'], [NULL, '', 'identity'])) { ?>

									<!-- <div class="card-header" style="background-color:antiquewhite; height: 200px; background-size: cover;background-repeat: no-repeat;"> -->
									<!-- <div class="card-header" style="background-image: url('viewer.php?src=profile_background'); height: 200px; background-size: cover;background-repeat: no-repeat;"> -->

									<!-- <div class="profile-picture">
											<div class="avatar avatar-xxl">
												<img src="viewer.php?src=get_university_logo&location=<?= base64_encode($university_data['logo']) ?>" alt="..." class="avatar-img rounded-circle">
											</div>
										</div> -->
									<!-- </div> -->


									<div class="card-body">
										<div class="form-group text-center">

											<div class="avatar avatar-xxl">
												<img src="viewer.php?src=get_university_logo&location=<?= base64_encode($university_data['logo']) ?>" alt="..." class="avatar-img rounded-circle">
											</div>

										</div>

										<div class="user-profile text-center mb-2">
											<div class="name">
												<?= ($_SESSION['POLIBATAM_STUDENT']) ? 'Politeknik Negeri Batam' : $university_data['nama_kampus'] ?? 'Politeknik Negeri Batam'; ?>
												<sup> <a class="text-sm" target="_blank" href="<?= $university_data['website'] ?>"><i class="fas fa-external-link-alt"></i></a></sup>
											</div>
										</div>

										<form id="form" action="index.php?form=student_identity&action=identity" method="POST" onload="disabled_form()"> <?= csrf() ?>
											<div class="row mt-3">



												<div class="col-md-12">
													<div class="form-group form-group-default">
														<label>Name <b class="text-danger">*</b></label>
														<input type="text" class="form-control" name="name" placeholder="Enter Your Full Name" value="<?= $data['name'] ?>">
														<?= form_error('name') ?>

													</div>
												</div>

												<div class="col-md-6">
													<div class="form-group form-group-default">
														<label>NIM</label>
														<input type="text" class="form-control" name="nim" placeholder="Enter Your Full Name" value="<?= old('nim', $data['nim']) ?>" disabled readonly>
														<?= form_error('nim') ?>

													</div>
												</div>


												<div class="col-md-6">
													<div class="form-group form-group-default">
														<label>Program Study</label>

														<select class="form-control" disabled readonly>
															<?php foreach ($data_prodi as $key => $prodi) : ?>
																<option value="<?= $prodi['kode_prodi'] ?>" <?= ($data['program_study'] == $prodi['kode_prodi']) ? 'selected' : ''; ?>><?= $prodi['jenjang'] . ' ' . $prodi['prodi'] ?></option>
															<?php endforeach; ?>
														</select>

														<!-- <input type="text" class="form-control"  placeholder="Enter Your Full Name" value="<?= $data['program_study'] ?>"> -->
														<?= form_error('program_study') ?>

													</div>
												</div>




												<br>


											</div>

											<div class="separator-solid"></div>


											<div class="row mt-3">



												<div class="col-md-6">
													<div class="form-group form-group-default">
														<label>E-Mail <b class="text-danger">*</b></label>
														<input type="email" class="form-control" name="email" placeholder="Enter Your E-Mail" value="<?= $data['email'] ?>">
														<?= form_error('email') ?>

													</div>
												</div>


												<div class="col-md-6">
													<div class="form-group form-group-default">
														<label>Other E-Mail <b class="text-danger">*</b></label>
														<input type="email" class="form-control" name="other_email" placeholder="Enter Your Other E-Mail" value="<?= $data['other_email'] ?>">
														<?= form_error('other_email') ?>

													</div>
												</div>

												<div class="col-md-6">
													<div class="form-group form-group-default">
														<label>No WhatsApp <b class="text-danger">*</b></label>
														<input type="tel" class="form-control" name="no_whatsapp" placeholder="Whatsapp Number" value="<?= $data['no_whatsapp'] ?>">
														<?= form_error('no_whatsapp') ?>

													</div>
												</div>

												<div class="col-md-6">
													<div class="form-group form-group-default ">
														<label>Phone Number <b class="text-danger">*</b></label>
														<input type="text" class="form-control" name="phone" placeholder="Phone" value="<?= $data['phone'] ?>">
														<?= form_error('phone') ?>

													</div>
												</div>
											</div>


										</form>

										<div class="text-left mt-3 mb-3">
											<?php if ($_SESSION['old']['open-form'] == 'open') : ?>
												<button onclick="confirmation()" id="cancel_btn" class="btn btn-danger"><i class="fas fa-times mr-1"></i>Cancel</button>
												<button onclick="enable_form()" id="edit_btn" class="btn btn-warning" hidden><i class="fas fa-edit mr-1"></i>Edit</button>
												<button onclick="submit_form()" class="btn btn-success" id="save_btn"><i class="fas fa-save mr-1"></i>Save</button>
											<?php else : ?>
												<button onclick="confirmation()" id="cancel_btn" class="btn btn-danger" hidden><i class="fas fa-times mr-1"></i>Cancel</button>
												<button onclick="enable_form()" id="edit_btn" class="btn btn-warning"><i class="fas fa-edit mr-1"></i>Edit</button>
												<button onclick="submit_form()" class="btn btn-success" disabled id="save_btn"><i class="fas fa-save mr-1"></i>Save</button>
											<?php endif; ?>

										</div>
									</div>


								<?php } else if ($_GET['tab'] == 'profile') { ?>


									<div class="card-body">


										<div class="form-group">
											<label class="">Current Profile Picture</label>
											<div class="form-group">

												<!-- <div class="col-lg-4 col-md-9 col-sm-8"> -->
												<div class="avatar avatar-xxl">
													<img src="viewer.php?src=profile_picture" alt="..." class="avatar-img rounded">
												</div>
												<!-- </div> -->

												<a target="_blank" download href="viewer.php?src=profile_picture&download=true" class="btn btn-info btn-sm"><i class="fas fa-download mr-2"></i>Unduh</a>

												<?php if (!in_array($data['profile_picture'], ['profile.jpg', 'profile.png', null])) : ?>
													<button type="button" href="#" onclick="delete_profile()" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt mr-2"></i>Remove</button>
												<?php endif ?>

											</div>

										</div>

										<form id="form" action="index.php?form=student_identity&action=profile" method="POST" enctype="multipart/form-data" onload="disabled_form()"> <?= csrf() ?> <?= input_hidden('nim', $_SESSION['nim']) ?>

											<div class="form-group">
												<label class="">New Profile Picture <span class="required-label">*</span></label>
												<input type="file" name="new_profile" accept="image/png, image/jpg, image/jpeg" id="new_profile" onchange="validateFile(this, '2MB')" class="form-control <?= show_error('new_profile') ?>" />
												<?= form_error('new_profile') ?>
												<span class="text-sm float-right">Allowed file : png, jpeg and jpg | Max Size : 2MB</span>


											</div>


										</form>

										<div class="form-group">
											<?php if ($_SESSION['old']['open-form'] == 'open') : ?>
												<button onclick="confirmation()" id="cancel_btn" class="btn btn-danger"><i class="fas fa-times mr-1"></i>Cancel</button>
												<button onclick="enable_form()" id="edit_btn" class="btn btn-warning" hidden><i class="fas fa-edit mr-1"></i>Edit</button>
												<button onclick="submit_form()" class="btn btn-success" id="save_btn"><i class="fas fa-save mr-1"></i>Save</button>
											<?php else : ?>
												<button onclick="confirmation()" id="cancel_btn" class="btn btn-danger" hidden><i class="fas fa-times mr-1"></i>Cancel</button>
												<button onclick="enable_form()" id="edit_btn" class="btn btn-warning"><i class="fas fa-edit mr-1"></i>Edit</button>
												<button onclick="submit_form()" class="btn btn-success" disabled id="save_btn"><i class="fas fa-save mr-1"></i>Save</button>
											<?php endif; ?>

										</div>
									</div>
								<?php	} else if (!$_SESSION['POLIBATAM_STUDENT'] && $_GET['tab'] == 'password') { ?>


									<div class="card-body">
										<form id="form" action="index.php?form=student_identity&action=password" method="POST" onload="disabled_form()"> <?= csrf() ?> <?= input_hidden('nim', $_SESSION['nim']) ?>
											<div class="row mt-3">
												<div class="col-md-12">
													<div class="form-group form-group-default">
														<label>Current Password <b class="text-danger">*</b></label>
														<input type="password" class="form-control" name="current_password" placeholder="Enter Your Current Password" value="<?= old('current_password') ?>">
														<?= form_error('current_password') ?>

													</div>
												</div>

												<div class="col-md-12">
													<div class="form-group form-group-default">
														<label>New Password <b class="text-danger">*</b></label>
														<input type="password" class="form-control" name="new_password" placeholder="Enter Your New Password" value="<?= old('new_password') ?>">
														<?= form_error('new_password') ?>

													</div>
												</div>

												<div class="col-md-12">
													<div class="form-group form-group-default">
														<label>New Password Confirmation <b class="text-danger">*</b></label>
														<input type="password" class="form-control" name="new_password2" placeholder="Confirm Your New Password" value="<?= old('new_password2') ?>">
														<?= form_error('new_password2') ?>

													</div>
												</div>



											</div>
										</form>

										<div class="text-left mt-3 mb-3">
											<?php if ($_SESSION['old']['open-form'] == 'open') : ?>
												<button onclick="confirmation()" id="cancel_btn" class="btn btn-danger"><i class="fas fa-times mr-1"></i>Cancel</button>
												<button onclick="enable_form()" id="edit_btn" class="btn btn-warning" hidden><i class="fas fa-edit mr-1"></i>Edit</button>
												<button onclick="submit_form()" class="btn btn-success" id="save_btn"><i class="fas fa-key mr-1"></i>Change Password</button>
											<?php else : ?>
												<button onclick="confirmation()" id="cancel_btn" class="btn btn-danger" hidden><i class="fas fa-times mr-1"></i>Cancel</button>
												<button onclick="enable_form()" id="edit_btn" class="btn btn-warning"><i class="fas fa-edit mr-1"></i>Edit</button>
												<button onclick="submit_form()" class="btn btn-success" disabled id="save_btn"><i class="fas fa-key mr-1"></i>Change Password</button>
											<?php endif; ?>

										</div>
									</div>


								<?php
								} else { ?>

									<div class="card-body">
										<!-- 404 Page Not Found -->
										<div id="container">
											<h1>404 Tab Not Found</h1>
											<p>Your requsted tab is not found in this server.</p>

										</div>
									</div>

								<?php
								}
								?>






							</div>
						</div>


					</div>
				</div>
			</div>

			<?php include "./view/mahasiswa/template/footer.php"; ?>

		</div>

	</div>


	<!--   Core JS Files   -->
	<script src="./assets/js/core/jquery.3.2.1.min.js"></script>
	<script src="./assets/js/core/popper.min.js"></script>
	<script src="./assets/js/core/bootstrap.min.js"></script>

	<!-- jQuery UI -->
	<script src="./assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
	<script src="./assets/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js"></script>

	<!-- jQuery Scrollbar -->
	<script src="./assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

	<!-- Moment JS -->
	<script src="./assets/js/plugin/moment/moment.min.js"></script>


	<!-- Datatables -->
	<script src="./assets/js/plugin/datatables/datatables.min.js"></script>


	<!-- Bootstrap Toggle -->
	<script src="./assets/js/plugin/bootstrap-toggle/bootstrap-toggle.min.js"></script>


	<!-- Dropzone -->
	<script src="./assets/js/plugin/dropzone/dropzone.min.js"></script>

	<!-- Fullcalendar -->
	<script src="./assets/js/plugin/fullcalendar/fullcalendar.min.js"></script>

	<!-- DateTimePicker -->
	<script src="./assets/js/plugin/datepicker/bootstrap-datetimepicker.min.js"></script>

	<!-- Bootstrap Tagsinput -->
	<script src="./assets/js/plugin/bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>

	<!-- Bootstrap Wizard -->
	<script src="./assets/js/plugin/bootstrap-wizard/bootstrapwizard.js"></script>

	<!-- jQuery Validation -->
	<script src="./assets/js/plugin/jquery.validate/jquery.validate.min.js"></script>

	<!-- Summernote -->
	<script src="./assets/js/plugin/summernote/summernote-bs4.min.js"></script>

	<!-- Select2 -->
	<script src="./assets/js/plugin/select2/select2.full.min.js"></script>


	<!-- Owl Carousel -->
	<script src="./assets/js/plugin/owl-carousel/owl.carousel.min.js"></script>

	<!-- Atlantis JS -->
	<script src="./assets/js/atlantis.min.js"></script>

	<!-- Soloalert -->
	<script src='./core/component/soloalert.js'></script>


	<script type="text/javascript">
		$(function() {
			<?php if ($_SESSION['old']['open-form'] != 'open' || $_SESSION['old']['open-form'] === NULL) : echo 'disabled_form();';
			endif; ?>
		});
	</script>


	<script>
		function submit_form() {
			console.log('helo there');
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


		};

		function enable_form() {


			$("#edit_btn").prop("disabled", true);
			$("#edit_btn").prop("hidden", true);


			$("#cancel_btn").prop("hidden", false);
			$("#cancel_btn").prop("disabled", false);


			$("#save_btn").prop("disabled", false);

			$("#form :input").prop("disabled", false);

			$("#form :input").removeClass("is-invalid border-danger is-valid border-success");


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
						url: "index.php?request=profile_remove&action=student",
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

						error: function() {
							console.log('gagalssss');
						}
					});

				}
			})

		}
	</script>


</body>

</html>