<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting( false ); session_start();

$title = 'Student Profile'; $route = 'intern_list';
include "./view/industri/template/head.php";


?>


<?php



$id_internship = base64_decode($_GET['id_internship']);

$sql = "SELECT internship.* FROM internship WHERE internship.id_internship = '$id_internship'";
$exec2 = $_SERVER['KONEKSI_DB']->query($sql);
$internship_data = $exec2->fetch_assoc();

if ( $exec2->num_rows != 1 ) {
	make_swal2('warning', 'Not Allowed', 'You don`t have access to this page !');
	redirect_back(1); exit;
}


$nim = $internship_data['nim'];

$sql = "SELECT * FROM student_internship WHERE nim = '$nim'";
$exec = $_SERVER['KONEKSI_DB']->query($sql);
$data = $exec->fetch_assoc();

if ( $exec->num_rows != 1 ) {
	make_swal2('warning', 'Not Allowed', 'You don`t have access to this page !');
	redirect_back(1); exit;
}



?>

<body>
	<div class="wrapper">

		<?php include "./view/industri/template/main-header.php"; ?>
		<?php include "./view/industri/template/sidebar.php"; ?>


		<div class="main-panel">

			<div class="container">
				<div class="page-inner">

					<div class="page-header">
						<h4 class="page-title">Student Profile</h4>
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
								<a href="#">Student Profile</a>
							</li>
						</ul>
					</div>


					<!-- Form -->
					<div class="row">

                        <div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<h4 class="card-title">Student Profile</h4>
								</div>

								<!-- Student's Data -->
								<div class="card-body form-row">

									<div class="form-group col-md-12 text-center">
									
										<div class="profile-picture">
											<div class="avatar avatar-xxl">
												<img src="./upload/profile_picture/<?= $data['profile_picture'] ?>" alt="..." class="avatar-img rounded-circle">
											</div>
										</div>
								
									</div>

									<div class="form-group col-md-6">
										<label>NIM </label>
										<input type="text" class="form-control" value="<?= $data['nim']?>" readonly />

									</div>

									<div class="form-group col-md-6">
											
												<label>Name </label>
												<input type="text" class="form-control" placeholder="Enter Your Full Name" value="<?= $data['name']?>" readonly>
												<?= form_error('name') ?>
											
											
										</div>

										<div class="form-group col-md-6">
											
												<label>E-Mail </label>
												<input type="email" class="form-control" placeholder="Enter Your E-Mail" value="<?= $data['email']?>" readonly>

											
										</div>


										<div class="form-group col-md-6">
											
												<label>Other E-Mail </label>
												<input type="email" class="form-control" placeholder="Enter Your Other E-Mail" value="<?= $data['other_email']?>" readonly>
										</div>


										<div class="form-group col-md-6">
											
												<label>No WhatsApp </label>
												<input type="tel" class="form-control" name="no_whatsapp" placeholder="Whatsapp Number" value="<?= $data['no_whatsapp']?>" readonly>
												<?= form_error('no_whatsapp') ?>
											
										</div>

										<div class="form-group col-md-6">
											
												<label>Phone Number </label>
												<input type="text" class="form-control" name="phone" placeholder="Phone" value="<?= $data['phone']?>" readonly>
											
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