<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = 'Dashboard UPKPK';
$route = 'dashboard';
include "./view/industri/template/head.php";


// GET MID
$mid = base64_decode(base64_decode($_GET['mid']));

// CEK MESSAGES
if ( in_array($_SESSION['role'], ['INDUSTRI', 'Industri', 'industri'] ) ) {
	$from_username = $_SESSION['username'];
} else if ( $_SESSION['role'] == 'Dosen' ) {
	$from_username = $_SESSION['nim_nik_unit'];
} else if ( $_SESSION['role'] == 'Mahasiswa') {
	$from_username = $_SESSION['nik'];
}




$sql0 = "SELECT DISTINCT(from_username) AS person, from_name FROM `message` WHERE id_message = '$mid' ";
$exec0 = $_SERVER['KONEKSI_DB']->query($sql0);

if ( $exec0->num_rows != 1 ) {
	make_snackbar('You do not have permission to access this page !');
	redirect_referrer(1); exit();
}

$dat0 = $exec0->fetch_assoc();

var_dump($dat0); exit;

?>

<body>
	<div class="wrapper">

		<?php include "./view/industri/template/main-header.php"; ?>
		<?php include "./view/industri/template/sidebar.php"; ?>


		<div class="main-panel">

			<div class="container container-full mb-4">
				<div class="page-wrapper has-sidebar">
					<div class="page-inner page-inner-fill">
						<div class="conversations">
							<div class="message-header">
								<div class="message-title">
									<a class="btn btn-light" href="index.php?page=message">
										<i class="fa fa-flip-horizontal fa-share"></i>
									</a>
									<div class="user ml-2">
										<div class="avatar avatar-offline">
											<img src="../assets/img/chadengle.jpg" alt="..." class="avatar-img rounded-circle border border-white">
										</div>
										<div class="info-user ml-2">
											<span class="name"><?= $dat0['from_name'] ?></span>
											<span class="last-active">Active 2h ago</span>
										</div>
									</div>
									<div class="ml-auto">
										<button class="btn btn-light">
											<i class="fa fa-user-plus"></i>
										</button>
										<button class="btn btn-light page-sidebar-toggler d-xl-none">
											<i class="fa fa-angle-double-left"></i>
										</button>
									</div>
								</div>
							</div>
							<div class="conversations-body">
								<div class="conversations-content bg-white">
									aaa

									<?php 
									
										$sq1 = "SELECT * FROM `message` WHERE from_username = '" .$dat0['person']. "' OR to_username = '" .$dat0['person']."' ORDER BY id_message";
										$exec1 = $_SERVER['KONEKSI_DB']->query($sq1);

										var_dump('aaa'); exit;




									//foreach ($exec1 as $i => $dat1 ) :
										?>

									<div class="message-content-wrapper">
										<div class="message message-in">
											<div class="avatar avatar-sm">
												<img src="../assets/img/chadengle.jpg" alt="..." class="avatar-img rounded-circle border border-white">
											</div>
											<div class="message-body">
												<div class="message-content">
													<div class="name"><?= $dat0['from_name'] ?></div>
													<div class="content"><?= $dat1['message'] ?></div>
												</div>
												<div class="date"><?= time_elapsed_string( $dat1['timestamp_send'] )?></div>
											</div>
										</div>
									</div>

									
									
									<?php //endforeach; ?>
								</div>
							</div>
							<div class="messages-form">
								<div class="messages-form-control">
									<input type="text" placeholder="Type here" class="form-control input-pill input-solid message-input">
								</div>
								<div class="messages-form-tool">
									<a href="#" class="attachment">
										<i class="flaticon-file"></i>
									</a>
								</div>
							</div>
						</div>
					</div>
					<div class="page-sidebar">
						<header class="sidebar-header d-xl-none">
							<a class="back">
								<i class="breadcrumb-icon fa fa-angle-left mr-2"></i>Back
							</a>
						</header>
						<div class="page-sidebar-section pb-3">
							<div class="page-navs bg-white mb-4">
								<div class="nav-scroller">
									<div class="nav nav-tabs nav-line nav-color-secondary">
										<a class="nav-link active show" data-toggle="tab" href="#tab1">Contact Info
										</a>
										<a class="nav-link" data-toggle="tab" href="#tab2">Media</a>
										<a class="nav-link" data-toggle="tab" href="#tab3">Action</a>
									</div>
								</div>
							</div>
							<div class="text-center">
								<div class="avatar avatar-xxl mb-3">
									<img src="../assets/img/chadengle.jpg" alt="..." class="avatar-img rounded-circle border border-white">
								</div>
								<h4 class="fw-bold">Chad</h4>
								<h6 class="text-muted mb-3">Banda Aceh . Since Jun 12, 2018</h6>
								<div class="row m-0">
									<div class="col-4">
										<div class="metric">
											<h4 class="metric-value fw-bold">125</h4>
											<p class="metric-label fw-bold">Post</p>
										</div>
									</div>
									<div class="col-4">
										<div class="metric">
											<h4 class="metric-value fw-bold">25K</h4>
											<p class="metric-label fw-bold">Followers</p>
										</div>
									</div>
									<div class="col-4">
										<div class="metric">
											<h4 class="metric-value fw-bold">134</h4>
											<p class="metric-label fw-bold">Following</p>
										</div>
									</div>
								</div>
							</div>
							<div class="list-group list-group-file-item list-group-reflow list-group-flush list-group-divider mt-3">
								<!-- .list-group-header -->
								<div class="list-group-header"> Today </div>
								<!-- /.list-group-header -->
								<!-- .list-group-item -->
								<div class="list-group-item align-items-start">
									<div class="list-group-item-figure">
										<a href="#" class="tile tile-circle bg-primary">
											<span class="fa fa-file-pdf"></span>
										</a>
									</div>
									<div class="list-group-item-body">
										<h4 class="list-group-item-title text-truncate">
											<a href="#">pengertian-agregat.pdf</a>
										</h4>
										<p class="list-group-item-text"> 1.08 MB by Emma Stone </p>
										<p class="list-group-item-text small"> 11:40pm </p>
									</div>
									<div class="list-group-item-figure">
										<button class="btn btn-sm btn-light">
											<i class="fa fa-download"></i>
										</button>
									</div>
								</div>
								<!-- /.list-group-item -->
								<!-- .list-group-item -->
								<div class="list-group-item align-items-start">
									<div class="list-group-item-figure">
										<a href="#" class="tile tile-circle bg-primary">
											<span class="fa fa-file-pdf"></span>
										</a>
									</div>
									<div class="list-group-item-body">
										<h4 class="list-group-item-title text-truncate">
											<a href="#">Baked-Chicken-and-Spinach-Flautas.pdf</a>
										</h4>
										<p class="list-group-item-text"> 0.39 MB by Peter Willis </p>
										<p class="list-group-item-text small"> 5:22pm </p>
									</div>
									<div class="list-group-item-figure">
										<button class="btn btn-sm btn-light">
											<i class="fa fa-download"></i>
										</button>
									</div>
								</div>
								<!-- /.list-group-item -->
								<!-- .list-group-item -->
								<div class="list-group-item align-items-start">
									<div class="list-group-item-figure">
										<a href="#" class="tile tile-circle bg-danger">
											<span class="fa fa-file-image"></span>
										</a>
									</div>
									<div class="list-group-item-body">
										<h4 class="list-group-item-title text-truncate">
											<a href="#">Cajun Chicken Egg Pasta.jpg</a>
										</h4>
										<p class="list-group-item-text"> 0.93 MB by Danielle Garza </p>
										<p class="list-group-item-text small"> 9:32pm </p>
									</div>
									<div class="list-group-item-figure">
										<button class="btn btn-sm btn-light">
											<i class="fa fa-download"></i>
										</button>
									</div>
								</div>
								<!-- /.list-group-item -->
								<!-- .list-group-header -->
								<div class="list-group-header"> Jan 28, 2018 </div>
								<!-- /.list-group-header -->
								<!-- .list-group-item -->
								<div class="list-group-item align-items-start">
									<div class="list-group-item-figure">
										<a href="#" class="tile tile-circle bg-secondary">
											<span class="fa fa-file-archive"></span>
										</a>
									</div>
									<div class="list-group-item-body">
										<h4 class="list-group-item-title text-truncate">
											<a href="#">Atlantis_regular.zip</a>
										</h4>
										<p class="list-group-item-text"> 0.53 MB by Jimmy Chad </p>
										<p class="list-group-item-text small"> 3:13am </p>
									</div>
									<div class="list-group-item-figure">
										<button class="btn btn-sm btn-light">
											<i class="fa fa-download"></i>
										</button>
									</div>
								</div>
								<!-- /.list-group-item -->
								<!-- .list-group-item -->
								<div class="list-group-item align-items-start">
									<div class="list-group-item-figure">
										<a href="#" class="tile tile-circle bg-secondary">
											<span class="fa fa-file-archive"></span>
										</a>
									</div>
									<div class="list-group-item-body">
										<h4 class="list-group-item-title text-truncate">
											<a href="#">Atlantis_extended.zip</a>
										</h4>
										<p class="list-group-item-text"> 2.04 MB by John Doe </p>
										<p class="list-group-item-text small"> 11:27pm </p>
									</div>
									<div class="list-group-item-figure">
										<button class="btn btn-sm btn-light">
											<i class="fa fa-download"></i>
										</button>
									</div>
								</div>
								<!-- /.list-group-item -->
								<!-- .list-group-header -->
								<div class="list-group-header"> Jan 21, 2018 </div>
								<!-- /.list-group-header -->
								<!-- .list-group-item -->
								<div class="list-group-item align-items-start">
									<div class="list-group-item-figure">
										<a href="#" class="tile tile-circle bg-primary">
											<span class="fa fa-file-pdf"></span>
										</a>
									</div>
									<div class="list-group-item-body">
										<h4 class="list-group-item-title text-truncate">
											<a href="#">Usulan Beasiswa Tahun 2018.pdf</a>
										</h4>
										<p class="list-group-item-text"> 1.1 MB by Peter Willis </p>
										<p class="list-group-item-text small"> 6:20pm </p>
									</div>
									<div class="list-group-item-figure">
										<button class="btn btn-sm btn-light">
											<i class="fa fa-download"></i>
										</button>
									</div>
								</div>
								<!-- /.list-group-item -->
								<!-- .list-group-item -->
								<div class="list-group-item align-items-start">
									<div class="list-group-item-figure">
										<a href="#" class="tile tile-circle bg-success">
											<span class="fa fa-file-excel"></span>
										</a>
									</div>
									<div class="list-group-item-body">
										<h4 class="list-group-item-title text-truncate">
											<a href="#">Daftar Peserta Ujian.xlsx</a>
										</h4>
										<p class="list-group-item-text"> 0.37 MB by Peter Willis </p>
										<p class="list-group-item-text small"> 1:32pm </p>
									</div>
									<div class="list-group-item-figure">
										<button class="btn btn-sm btn-light">
											<i class="fa fa-download"></i>
										</button>
									</div>
								</div>
								<!-- /.list-group-item -->
								<!-- .list-group-item -->
								<div class="list-group-item align-items-start">
									<div class="list-group-item-figure">
										<a href="#" class="tile tile-circle bg-info">
											<span class="fa fa-file-word"></span>
										</a>
									</div>
									<div class="list-group-item-body">
										<h4 class="list-group-item-title text-truncate">
											<a href="#">data_not_verified.docx</a>
										</h4>
										<p class="list-group-item-text"> 0.94 MB by Kathryn Black </p>
										<p class="list-group-item-text small"> 5:11am </p>
									</div>
									<div class="list-group-item-figure">
										<button class="btn btn-sm btn-light">
											<i class="fa fa-download"></i>
										</button>
									</div>
								</div>
								<!-- /.list-group-item -->
								<!-- .list-group-header -->
								<div class="list-group-header"> Jan 19, 2018 </div>
								<!-- /.list-group-header -->
								<!-- .list-group-item -->
								<div class="list-group-item align-items-start">
									<div class="list-group-item-figure">
										<a href="#" class="tile tile-circle bg-warning">
											<span class="fa fa-file-powerpoint"></span>
										</a>
									</div>
									<div class="list-group-item-body">
										<h4 class="list-group-item-title text-truncate">
											<a href="#">mockup-presentation.pptx</a>
										</h4>
										<p class="list-group-item-text"> 0.59 MB by Kathryn Black </p>
										<p class="list-group-item-text small"> 6:50am </p>
									</div>
									<div class="list-group-item-figure">
										<button class="btn btn-sm btn-light">
											<i class="fa fa-download"></i>
										</button>
									</div>
								</div>
								<!-- /.list-group-item -->
							</div>
						</div>
					</div>
				</div>
			</div>
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


</body>

</html>