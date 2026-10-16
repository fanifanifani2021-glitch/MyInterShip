<!DOCTYPE html>
<html lang="en">

<head>

	<!-- Meta for Compatibility -->
	<meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html;">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
	<meta name="theme-color" content="#FFAD46" />

	<!-- Meta for Security -->
	<!-- <meta http-equiv="Content-Security-Policy" content="default-src https: 'unsafe-eval' 'unsafe-inline'; object-src 'none'"> -->

	<!-- Meta for SEO Friendly -->
	<meta name="description" content="<?= $_SERVER['APP_DESCRIPTION'] ?>">
	<meta name="keywords" content="<?= $_SERVER['APP_KEYWORDS'] ?>">
	<meta name="author" content="<?= $_SERVER['APP_AUTHOR'] ?>">
	<meta name="robots" content="index, follow">
	<link rel="canonical" href="https://myinternship.polibatam.ac.id/" />


	<meta name="csrf-token" content="<?= csrf_token() ?>" />


	<!-- Canonical -->
	<link rel="canonical" href="https://myinternship.polibatam.ac.id/" />


	<title><?= $title ?> | <?= $_SERVER['APP_NAME'] ?></title>


	<!-- Favicons -->
	<!-- <link href="assets/jobcy/img/myinternship-logo.png" rel="icon">
  <link href="assets/jobcy/img/apple-touch-icon.png" rel="apple-touch-icon"> -->

	<!-- Icon -->
	<link rel="icon" href="<?= base_url('assets/img/icon.png') ?>" type="image/x-icon" />
	<link href="<?= base_url('assets/img/icon.png') ?>" rel="apple-touch-icon">


	<!-- Google Fonts -->
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">


	<!-- =======================================================
  * Template Name: Vesperr - v4.9.1
  * Template URL: https://bootstrapmade.com/vesperr-free-bootstrap-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->

	<!-- PWA Sorta -->
	<link rel="manifest" href="<?= base_url('manifest.webmanifest') ?>" />
	<script src="<?= base_url('sw.js') ?>"></script>

	<script src="<?= base_url('upup.sw.min.js') ?>"></script>
	<script src="<?= base_url('upup.min.js') ?>"></script>
	<script>
		UpUp.start({
			'content-url': "<?= base_url('offline.html') ?>" // show this page to offline users
		});
	</script>


	<!-- Fonts and icons -->
	<script src="<?= base_url('assets/js/plugin/webfont/webfont.min.js') ?>"></script>
	<script>
		WebFont.load({
			google: {
				"families": ["Lato:300,400,700,900"]
			},
			custom: {
				"families": ["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"],
				urls: ["<?= base_url('assets/css/fonts.min.css') ?>"]
			},
			active: function() {
				sessionStorage.fonts = true;
			}
		});
	</script>


	<!-- CSS Files -->
	<link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
	<link rel="stylesheet" href="<?= base_url('assets/css/atlantis2.css') ?>">

</head>

<body>
	<div class="wrapper horizontal-layout-3">

		<div class="main-header no-box-shadow" data-background-color="blue2">
			<div class="nav-top">
				<div class="container d-flex flex-row">


					<button class="navbar-toggler sidenav-toggler2 ml-auto" type="button" data-toggle="collapse" data-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
						<span class="navbar-toggler-icon">
							<i class="icon-menu"></i>
						</span>
					</button>
					<button class="topbar-toggler more"><i class="icon-options-vertical"></i></button>


					<!-- Logo Header -->
					<a href="<?= base_url() ?>" class="logo d-flex align-items-center">
						<img src="<?= base_url('viewer.php?src=main_logo') ?>" alt="navbar brand" class="navbar-brand" style="width: 140px; height: auto;">

						<!-- <img src="" alt="navbar brand" class="navbar-brand"> -->
					</a>
					<!-- End Logo Header -->

					<!-- Navbar Header -->
					<nav class="navbar navbar-header-left navbar-expand-lg p-0">
						<ul class="navbar-nav page-navigation pl-md-3">
							<h3 class="title-menu d-flex d-lg-none">
								Menu
								<div class="close-menu"> <i class="flaticon-cross"></i></div>
							</h3>
							<li class="nav-item">
								<a class="nav-link" href="<?= base_url('home') ?>" role="button">
									Home
								</a>

							</li>

							<?php if (isset($_SESSION['role'])) : ?>
								<li class="nav-item">
									<a class="nav-link" href="<?= base_url() ?>" role="button">
										Dashboard
									</a>

								</li>

							<?php endif; ?>


						</ul>
					</nav>

					<nav class="navbar navbar-header navbar-expand-lg p-0">
						<div class="container-fluid p-0">
							<ul class="navbar-nav topbar-nav ml-md-auto align-items-center">

								<!-- Tanggal -->
								<li class="nav-item dropdown hidden-caret">
									<a aria-label="Current Date and Calendar" class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">
										<span id="date"><?= date('D, d M Y') ?></span>
										<!-- <label class="float-right ml-2 text-white"><?= $_SESSION['name'] ?></label> -->
									</a>
									<ul class="float-right dropdown-menu dropdown-calendar dropdown-user animated fadeIn">
										<div class="dropdown-user-scroll scrollbar-outer">
											<div class="card-body text-center text-accent-1">

												<h3><?= date('D, d M Y') . 'M' ?></h3>
												<h3><?= getHijriDateWithDayName() ?></h3>


												<!-- <div id="calendar"></div> -->
											</div>
										</div>
									</ul>
								</li>


								<!-- Jam -->
								<li class="nav-item dropdown hidden-caret">
									<a aria-label="Current Time" class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">
										<span id="clock"><?= date('H : i : s') ?></span>

										<!-- <label class="float-right ml-2 text-white"><?= $_SESSION['name'] ?></label> -->
									</a>
									<ul class="float-right dropdown-menu dropdown-calendar dropdown-user animated fadeIn">
										<div class="dropdown-user-scroll scrollbar-outer">
											<div class="card-body text-center text-accent-1 ">
												<h3>Jakarta, Indonesia</h3>
												<h1>
													<span id="clock2"><?= date('H : i : s') ?></span>
												</h1>

											</div>
										</div>
									</ul>
								</li>



							</ul>
						</div>
					</nav>

					<!-- End Navbar -->
				</div>
			</div>
		</div>

		<div class="main-panel">

			<div class="bg-primary2 pt-4 pb-5">
				<div class="container text-white py-2">
					<div class="d-flex align-items-center">
						<div class="mr-3">
							<h1 class="mb-3">Attendance Approval</h1>
							<h5 class="op-7 mb-3">Tools for checking approval made for Student Attendance</h5>
						</div>
					</div>
				</div>
			</div>

			<div class="container mt--5">
				<div class="page-inner mt--3">

					<!-- Approval Detail -->

					<div class="row row-card-no-pd">
						<div class="col-md-12">

							<div class="card">
								<div class="card-header">
									<div class="card-head-row">
										<h4 class="card-title">Approval Detail</h4>
										<!-- <div class="card-tools">
											<button class="btn btn-icon btn-link btn-primary btn-xs"><span class="fa fa-angle-down"></span></button>
											<button class="btn btn-icon btn-link btn-primary btn-xs btn-refresh-card"><span class="fa fa-sync-alt"></span></button>
											<button class="btn btn-icon btn-link btn-primary btn-xs"><span class="fa fa-times"></span></button>
										</div> -->
									</div>
									<!-- <p class="card-category">
										Map of the distribution of users around the world</p> -->
								</div>
								<div class="card-body">

									<div class="form-group">
										<label>Approval Subject</label>
										<div class="forrm-group">
											Attendance Approval
										</div>
									</div>

									<div class="form-group">
										<label>Approval Date</label>
										<div class="forrm-group">
											<?= $approver['approval_date'] ?>
										</div>
									</div>

									<div class="form-group">
										<label>Approver</label>
										<div class="forrm-group">
											<?= $approver['approved_by']['company_name'] . " | " . $approver['approved_by']['supervisor_name'] ?>
										</div>
									</div>

									<div class="form-group">
										<label>Student Name</label>
										<div class="forrm-group">
											<?= $internship_data['name'] ?>
										</div>
									</div>




								</div>
							</div>
						</div>
					</div>


					<!-- Attendance Detail -->

					<div class="row row-card-no-pd">

						<div class="col-md-12">

							<div class="card">
								<div class="card-header">
									<div class="card-head-row">
										<h4 class="card-title">Attendance Detail</h4>
										<!-- <div class="card-tools">
											<button class="btn btn-icon btn-link btn-primary btn-xs"><span class="fa fa-angle-down"></span></button>
											<button class="btn btn-icon btn-link btn-primary btn-xs btn-refresh-card"><span class="fa fa-sync-alt"></span></button>
											<button class="btn btn-icon btn-link btn-primary btn-xs"><span class="fa fa-times"></span></button>
										</div> -->
									</div>
									<!-- <p class="card-category">
										Map of the distribution of users around the world</p> -->
								</div>
								<div class="card-body">

									<div class="form-group">
										<label>Student Name</label>
										<div class="forrm-group">
											<?= $internship_data['name'] ?>
										</div>
									</div>



									<div class="form-group">
										<label>Attendance Date</label>
										<div class="forrm-group">
											<?= $attendance_data['date'] ?>
										</div>
									</div>

									<div class="form-group">
										<label>Attendance Status</label>
										<div class="forrm-group">
											<?= $attendance_data['attendance_type'] ?>
										</div>
									</div>

									<?php if ($attendance_data['attendance_type'] == 'Present') : ?>

										<div class="form-group">
											<label>Check In</label>
											<div class="forrm-group">
												<?= $attendance_data['check_in'] ?>
											</div>
										</div>


										<div class="form-group">
											<label>Check Out</label>
											<div class="forrm-group">
												<?= $attendance_data['check_out'] ?>
											</div>
										</div>
									<?php endif; ?>



								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>


		<?= view('template/footer'); ?>


	</div>

	<!--   Core JS Files   -->
	<script src="<?= base_url('assets/js/core/jquery.3.2.1.min.js') ?>"></script>
	<script src="<?= base_url('assets/js/core/popper.min.js') ?>"></script>
	<script src="<?= base_url('assets/js/core/bootstrap.min.js') ?>"></script>

	<!-- jQuery UI -->
	<script src="<?= base_url('assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js') ?>"></script>
	<script src="<?= base_url('assets/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js') ?>"></script>

	<!-- jQuery Scrollbar -->
	<script src="<?= base_url('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') ?>"></script>

	<!-- Moment JS -->
	<script src="<?= base_url('assets/js/plugin/moment/moment.min.js') ?>"></script>

	<!-- Fullcalendar -->
	<script src="<?= base_url('assets/js/plugin/fullcalendar/fullcalendar.min.js') ?>"></script>

	<!-- Sweet Alert -->
	<script src="<?= base_url('assets/js/plugin/sweetalert/sweetalert.min.js') ?>"></script>

	<!-- Atlantis JS -->
	<script src="<?= base_url('assets/js/atlantis2.min.js') ?>"></script>

	<script>
		$(document).ready(function() {

			clock_run();

		});

		function clock_run() {

			'use strict';

			let d = new Date();

			let en_day = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
			let en_month = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

			let day = en_day[d.getDay()];
			let date = d.getDate();
			let month = en_month[d.getMonth()];
			let year = (d.getYear() + 1900);

			let curr_date = day + ', ' + date + ' ' + month + ' ' + year;

			localStorage.setItem('curr_date', curr_date);
			let old_date = localStorage.getItem('curr_date');

			if ($("#date").text() != curr_date) {
				localStorage.setItem('curr_date', curr_date);
				$("#date").text(curr_date);
			}


			setInterval(function() {

				let d = new Date();

				let day = en_day[d.getDay()];
				let date = d.getDate();
				let month = en_month[d.getMonth()];
				let year = (d.getYear() + 1900);

				let date_day = day + ', ' + date + ' ' + month + ' ' + year;

				if (date_day != old_date) {
					localStorage.setItem('curr_date', date_day);
					$("#date").text(date_day);
				}


				let hours = d.getHours();
				let minutes = d.getMinutes();
				let seconds = d.getSeconds();


				let time = ((hours < 10 ? "0" : "") + hours) + ' : ' + ((minutes < 10 ? "0" : "") + minutes) + ' : ' + ((seconds < 10 ? "0" : "") + seconds);

				$("#clock").text(time);
				$("#clock2").text(time);

			}, 1000);


		}
	</script>

</body>

</html>