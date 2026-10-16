<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = 'Register Independent Internship';
$route = 'register_independent';
include "./view/mahasiswa/template/head.php";


?>


<?php

$nim = $_SESSION['nim'];

$sql = "SELECT * FROM student_internship WHERE nim = '$nim'";
$exec = $_SERVER['KONEKSI_DB']->query($sql);

if ($exec->num_rows != 1) {
	make_swal2('warning', 'Not Allowed', 'You don`t have access to this page !');
	redirect_back(1);
	exit;
}

$data = $exec->fetch_assoc();


?>

<body>
	<div class="wrapper">

		<?php include "./view/mahasiswa/template/main-header.php"; ?>
		<?php include "./view/mahasiswa/template/sidebar.php"; ?>


		<div class="main-panel">

			<div class="container">
				<div class="page-inner">

					<div class="page-header">
						<h4 class="page-title">Register Independent Internship</h4>
						<ul class="breadcrumbs">
							<li class="nav-home">
								<a href="index.php">
									<i class="flaticon-home"></i>
								</a>
							</li>
							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="#">Register Independent Internship</a>
							</li>
						</ul>
					</div>


					<!-- Form -->
					<div class="row">

						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<h4 class="card-title">Form Register Independent Internship</h4>
								</div>



								<!-- Student's Data -->
								<!-- <div class="card-body form-row">



									<div class="form-group col-md-6">
										<label>NIM </label>
										<input type="text" class="form-control" value="<?= $data['nim'] ?>" readonly />

									</div>

									<div class="form-group col-md-6">
											
												<label>Name </label>
												<input type="text" class="form-control" placeholder="Enter Your Full Name" value="<?= $data['name'] ?>" readonly>
												<?= form_error('name') ?>
											
											
										</div>

										<div class="form-group col-md-6">
											
												<label>E-Mail </label>
												<input type="email" class="form-control" placeholder="Enter Your E-Mail" value="<?= $data['email'] ?>" readonly>

											
										</div>


										<div class="form-group col-md-6">
											
												<label>Other E-Mail </label>
												<input type="email" class="form-control" placeholder="Enter Your Other E-Mail" value="<?= $data['other_email'] ?>" readonly>
										</div>


										<div class="form-group col-md-6">
											
												<label>No WhatsApp </label>
												<input type="tel" class="form-control" name="no_whatsapp" placeholder="Whatsapp Number" value="<?= $data['no_whatsapp'] ?>" readonly>
												<?= form_error('no_whatsapp') ?>
											
										</div>

										<div class="form-group col-md-6">
											
												<label>Phone Number </label>
												<input type="text" class="form-control" name="phone" placeholder="Phone" value="<?= $data['phone'] ?>" readonly>
											
									</div>

								</div>

								<div class="separator-solid"></div> -->


								<form action="index.php?form=register_independent" method="POST" enctype="multipart/form-data" id="form_register">
									<?= csrf(); ?> <?= input_hidden('nim', $data['nim']) ?>
									
									
									<div class="card-body form-row">



										<div class="form-group col-md-6">
											<label>Expected Start Date <b class="text-danger">*</b></label>
											<input type="date" name="expected_start" class="form-control <?= show_error('expected_start'); ?>" />
											<?= form_error('expected_start'); ?>
										</div>

										<div class="form-group col-md-6">
											<label>Expected End Date <b class="text-danger">*</b></label>
											<input type="date" class="form-control <?= show_error('expected_end'); ?>" name="expected_end" />
											<?= form_error('expected_start'); ?>
										</div>


										<div class="form-group col-md-12">
											<label class="">Curriculum Vitae</label>
											<!-- <small class="float-right"><b class="text-danger">*</b> file must be .pdf, max size is 2MB</small> -->
											<small class="float-right"><b class="text-danger">*</b> Your CV is in Talent Hub</small>

											<!-- <input type="file" class="form-control <?= show_error('attachment_cv'); ?>" name="attachment_cv" /> -->
											<div class="input-group">
												<div class="input-group-append">
													<span class="input-group-text">talent-hub.id/</span>
												</div>
												<input type="text" class="form-control" name="" value="index.php?page=mhs_profile&nim=<?= base64_encode($data['nim']) ?>" disabled />
												<input type="hidden" name="attachment_cv" value="https://talent-hub.idid/index.php?page=mhsprofile&nim=<?= base64_encode($data['nim']) ?>" />


												<div class="input-group-prepend">
													<a target="_blank" href='https://talent-hub.id/index.php?page=mhsprofile&nim=<?= base64_encode($data['nim']) ?>' class="input-group-text"><i class="fas fa-globe mr-2"></i>Visit</a>
												</div>
											</div>
											<?= form_error('attachment_cv'); ?>


										</div>


										<div class="form-group col-md-12">
											<label>Transcript of Grades </label>
											<small class="float-right"><b class="text-danger">*</b> not required, file must be .pdf, max size is 500KB</small>

											<input type="file" class="form-control <?= show_error('attachment_transkrip'); ?>" name="attachment_transkrip" />
											<?= form_error('attachment_transkrip'); ?>

										</div>

										<div class="form-check">
											<label class="form-check-label">
												<input type="hidden" id="have_another_value" name="have_another" value="0" />

												<input class="form-check-input" type="checkbox" onclick="open_div( )" id="have_another" name="have_another" value="1">
												<span class="form-check-sign">Add another attachment</span>
											</label>
										</div>

										<script type="text/javascript">
											function open_div() {

												var have_another = document.getElementById('have_another');
												var add_optional = document.getElementById('add_optional');

												var have_another_value = document.getElementById('have_another_value');

												if (have_another.checked === true) {
													have_another_value.value = 1;
													add_optional.hidden = false;
												} else {
													have_another_value.value = 0;

													add_optional.hidden = true;
												}

											}
										</script>




										<div class="form-group col-md-12" id="add_optional" hidden>
											<label>Optional Attachment</label>
											<small class="float-right"><b class="text-danger">*</b> file must be .pdf, max size is 500KB</small>

											<input type="file" class="form-control <?= show_error('another_attachment'); ?>" name="another_attachment" accept="application/pdf" />
											<?= form_error('another_attachment'); ?>



										</div>


									</div>

									<div class="separator-solid"></div>


									<!-- Company Data -->
									<div class="card-body form-row">


										<div class="form-group col-md-12">
											<label>Company Name <b class="text-danger">*</b></label>
											<input type="text" class="form-control <?= show_error('company_name') ?>" name="company_name" value="<?= old('company_name'); ?>" placeholder="Enter Company Name" />
											<?= form_error('company_name') ?>
										</div>

										<div class="form-group col-md-12">
											<label>Company Type <b class="text-danger">*</b></label>
											<select class="form-control <?= show_error('company_type') ?>" name="company_type" id="company_type">
												<option value="">Choose one . . .</option>
												<option>Agency</option>
												<option>Design</option>
												<option>Education</option>
												<option>Engineering</option>
												<option>Finance</option>
												<option>Government</option>
												<option>Health</option>
												<option>IT and Telco</option>
												<option>Logistic</option>
												<option>Marketing</option>
												<option>Media</option>
												<option>Others</option>
											</select>

											<?= form_error('company_type') ?>

										</div>

										<div class="form-group col-md-12">
											<label>Description <b class="text-danger">*</b></label>

											<textarea class="form-control <?= show_error('company_description') ?>" name="company_description" placeholder="Enter Company Description" rows="3"></textarea>

											<?= form_error('company_description'); ?>
										</div>


										<div class="form-group col-md-6">
											<label>Province <b class="text-danger">*</b></label>

											<input type="text" class="form-control <?= show_error('company_province') ?>" name="company_province" placeholder="Enter Company Province Location" />

											<?= form_error('company_province'); ?>
										</div>

										<div class="form-group col-md-6">
											<label>City <b class="text-danger">*</b></label>

											<input type="text" class="form-control <?= show_error('company_city') ?>" name="company_city" placeholder="Enter Company City Location" />

											<?= form_error('company_city'); ?>
										</div>


										<div class="form-group col-md-12">
											<label>Address <b class="text-danger">*</b></label>

											<textarea class="form-control <?= show_error('company_address') ?>" name="company_address" placeholder="Enter Company Address" rows="3"></textarea>

											<?= form_error('company_address'); ?>
										</div>

										<div class="form-group col-md-6">
											<label>Phone Number <b class="text-danger">*</b></label>
											<input type="tel" class="form-control <?= show_error('company_phone') ?>" name="company_phone" value="<?= old('company_phone'); ?>" placeholder="Enter Company Phone Number" />
											<?= form_error('company_phone') ?>
										</div>

										<div class="form-group col-md-6">
											<label>E-Mail <b class="text-danger">*</b></label>
											<input type="email" class="form-control <?= show_error('company_email') ?>" name="company_email" value="<?= old('company_email'); ?>" placeholder="Enter Company E-Mail" />
											<?= form_error('company_email') ?>
										</div>


										<div class="form-group col-md-6">
											<label>Website <b class="text-danger">*</b></label>
											<input type="text" class="form-control <?= show_error('company_website') ?>" name="company_website" value="<?= old('company_website'); ?>" placeholder="Enter Company Website" />
											<?= form_error('company_website') ?>
										</div>


									</div>


									<div class="card-footer">
										<button type="button" class="btn btn-danger" onclick="confirmation()"><i class="fas fa-times mr-1"></i>Cancel</button>
										<button type="button" class="btn btn-warning" onclick="reset_confirmation()"><i class="fas fa-sync mr-1"></i>Reset</button>
										<button type="submit" class="btn btn-success" id="submit_btn"><i class="fab fa-wpforms mr-1"></i>Register</button>

									</div>
								</form>


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

	<!-- User Custom Script -->
	<script>
		$(document).ready(function() {

			open_div();

		});

		function confirmation() {
			// alert('Are you sure you want to cancel ? (Your change will not be saved)');
			SoloAlert.confirm({
				title: "Are you sure you want to cancel ?",
				body: "Changes you made will not be saved !",
				useTransparency: true,
				onOk: () => {
					window.location.href = "index.php";
				}
			});

		}

		function reset_confirmation() {

			SoloAlert.confirm({
				title: "Are you sure you want to reset ?",
				body: "Changes you made will be reset to default !",
				useTransparency: true,
				onOk: () => {
					form_reset();
				}
			});

		}


		function form_reset() {

			$("#form_register").trigger("reset");
		};
	</script>

</body>

</html>