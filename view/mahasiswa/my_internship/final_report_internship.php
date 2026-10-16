<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = 'Final Report';
$route = 'my_internship';
include "./view/mahasiswa/template/head.php";
$id_internship = base64_decode($_GET['id_internship']);
$nim = $_SESSION['nim'];

$sql = mysqli_query($koneksi_db, "SELECT * FROM internship WHERE id_internship = $id_internship");
$row = mysqli_fetch_assoc($sql);



?>

<body>
	<div class="wrapper">

		<?php include "./view/mahasiswa/template/main-header.php"; ?>
		<?php include "./view/mahasiswa/template/sidebar.php"; ?>


		<div class="main-panel">

			<div class="container">
				<div class="page-inner">

					<div class="page-header">
						<h4 class="page-title">Final Report</h4>
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
								<a href="index.php?page=my_internship">My Internship</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="index.php?page=internship_detail&id_internship=<?= base64_encode($id_internship) ?>">Internship Detail</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="#">Final Report</a>
							</li>
						</ul>
					</div>


					<!-- Form -->
					<div class="row" id="feedback_form">

						<div class="col-md-12">
							<div class="card full-height">
								<div class="card-header">
									<h4 class="card-title">Form Final Report</h4>
								</div>

								<div class="card-body form-row">
									<?php if (strtotime($row['end_date']) > strtotime(date('Y-m-d'))) : ?>

										<button class="btn btn-warning">You are advised to upload final report <b>after</b> your Internship finished.</button>

									<?php endif; ?>
								</div>


								<form action="index.php?form=final_report" method="POST" enctype="multipart/form-data" id="form_feedback"><?= csrf(); ?>
									<input type="hidden" name="id_internship" id="id_internship" value="<?= $id_internship ?>">

									<div class="card-body form-row">

										<div class="form-group col-md-12">
											<label class="label-control">Final Report File</label>
											<div class="input-group mb-3">
												<input class="form-control" value="<?php if ($row['final_report'] == "-") {
																						echo 'No Files Uploaded !';
																					} else {
																						echo $row['final_report'];
																					} ?>" readonly />

												<div class="input-group-append">
													<a class="btn btn-outline-secondary" type="button" href="index.php?viewer=final_report&id=<?= base64_encode($row['id_internship']) ?>&file=<?= base64_encode($row['final_report']) ?>" target="_blank"><i class="fas fa-eye"></i></a>
												</div>
											</div>
										</div>

										<div class="form-group col-md-12">
											<label>Upload or Edit Final Report <b class="text-danger">*</b></label>
											<input type="file" class="form-control <?= show_error('final_report'); ?>" name="final_report" />
											<?= form_error('final_report'); ?>

											<small><b class="text-danger">*</b> Allowed file : pdf | Max Size : 5MB</small>
										</div>



									</div>


									<div class="card-footer">
										<button type="submit" class="btn btn-success" id="save_btn"><i class="fas fa-upload mr-1"></i>Upload</button>
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