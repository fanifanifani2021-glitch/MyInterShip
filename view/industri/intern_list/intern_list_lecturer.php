<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting( false ); session_start();

$title = 'Lecturer Profile'; $route = 'intern_list';
include "./view/industri/template/head.php";


?>


<?php



$id_internship = base64_decode($_GET['id_internship']);

$sql = "SELECT internship.* FROM internship WHERE internship.id_internship = '$id_internship'";
$exec2 = $_SERVER['KONEKSI_DB']->query($sql);

if ( $exec2->num_rows != 1 ) {
	make_swal2('warning', 'Not Allowed', 'You don`t have access to this page !');
	redirect_back(1); exit;
}

$internship_data = $exec2->fetch_assoc();
$nim = $internship_data['nim'];

$sql4 = "SELECT * FROM student_internship WHERE nim = '$nim'";
$exec4 = $_SERVER['KONEKSI_DB']->query($sql4);

if ( $exec2->num_rows != 1 ) {
	make_swal2('warning', 'Not Allowed', 'You don`t have access to this page !');
	redirect_back(1); exit;
}

$student_data = $exec4->fetch_assoc();
$nik = $student_data['nik_dospem'];

$sql = "SELECT lecturer.* FROM lecturer WHERE lecturer.nim_nik_unit = '$nik'";
$exec = $_SERVER['KONEKSI_DB']->query($sql);
$data = $exec->fetch_assoc();


$sql2 = "SELECT lecturer_profile.* FROM lecturer_profile WHERE lecturer_profile.nim_nik_unit = '$nik'";
$exec2 = $_SERVER['KONEKSI_DB']->query($sql2);
$data2 = $exec2->fetch_assoc();
if ( $exec->num_rows != 1 ) {
	make_swal2('warning', 'Not Allowed', 'You don`t have access to this page !');
	redirect_back(1); exit;
}

$id_kampus = $data['id_kampus'];

$sql6 = "SELECT * FROM tb_kampus WHERE id_kampus = '$id_kampus'";
$exec6 = $_SERVER['KONEKSI_DB']->query($sql6);
$university = $exec6->fetch_assoc();



?>

<body>
	<div class="wrapper">

		<?php include "./view/industri/template/main-header.php"; ?>
		<?php include "./view/industri/template/sidebar.php"; ?>


		<div class="main-panel">

			<div class="container">
				<div class="page-inner">

					<div class="page-header">
						<h4 class="page-title">Lecturer Profile</h4>
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
								<a href="index.php?page=intern_list">Intern List</a>
							</li>
							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="index.php?page=intern_list_detail&id_internship=<?= base64_encode( $id_internship)?>">Intern Detail</a>
							</li>
							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="#">Lecturer Profile</a>
							</li>
						</ul>
					</div>


					<!-- Form -->
					<div class="row">

						<div class="col-md-12">


							<div class="card card-profile">
								<div class="card-header" style="background-image: url(viewer.php?src=profile_background); height: 300px; background-size: cover;background-repeat: no-repeat;">

									<div class="profile-picture">
										<div class="avatar avatar-xxl">
											<img src="viewer.php?src=get_profile&location=<?= base64_encode( $data['profile_picture'] ) ?>" alt="..." class="avatar-img rounded-circle">
										</div>
									</div>
								</div>

								<div class="card-body">
									<div class="user-profile text-center">
										<div class="name"><?= $data['name'] ?></div>
										<div class="job">Lecturer at <?= $university['nama_kampus'] ?? 'Politeknik Negeri Batam' ?></div>
									</div>

									<!-- GET DATA LECTURER DAN LECTURER PROFILE -->

									<div class="row mt-3">
										

										<div class="col-md-6">
											<div class="form-group form-group-default">
												<label>NIK</label>
												<input type="text" class="form-control" name="name" value="<?= $data['nim_nik_unit'] ?>">
												<?= form_error('name') ?>

											</div>
										</div>



										<div class="col-md-6">
											<div class="form-group form-group-default">
												<label>E-Mail</label>
												<input type="text" class="form-control" name="program_study" value="<?= $data['email_polibatam'] ?>">
												<?= form_error('program_study') ?>

											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group form-group-default">
												<label>Other E-Mail</label>
												<input type="text" class="form-control" name="program_study" value="<?= $data2['other_email'] ?>">
												<?= form_error('program_study') ?>

											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group form-group-default">
												<label>Phone Number</label>
												<input type="email" class="form-control" name="polibatam_email" value="<?= $data2['phone'] ?>">
												<?= form_error('polibatam_email') ?>

											</div>
										</div>



									</div>


								</div>


							</div>
						</div>


					</div>



				</div>
			</div>

			<?php include "./view/industri/template/footer.php"; ?>

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


</body>
</html>