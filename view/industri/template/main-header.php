<?php error_reporting(false);
session_start(); ?>

<style>
    .fc-sun {
        color: red; /* Mengubah warna font menjadi merah pada hari Minggu */
    }
</style>

<div class="modal fade" id="Modalkalender" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header no-bd">
				<h5 class="modal-title">
					<span class="fw-mediumbold">
						Calendar</span>
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div id="calendar"></div>
			</div>
			<div class="modal-footer justify-content-between">
				<button type="button" class="btn btn-primary btn-block" data-dismiss="modal">Close</button>
			</div>

		</div>
	</div>
</div>


<div class="main-header">
	<!-- Logo Header -->
	<div class="logo-header" data-background-color="blue2">

		<a href="index.php" class="logo">
			<!-- <img src="./assets/img/logo_semoga_white.png" alt="navbar brand" class="navbar-brand" style="width: 120px; height: auto;"> -->
			<!-- <img src="./assets/img/my_internship_logo_grey5.png" alt="navbar brand" class="navbar-brand" style="width: 180px; height: auto;" > -->
			<img src="viewer.php?src=main_logo" alt="navbar brand" class="navbar-brand" style="width: 180px; height: auto;">

		</a>
		<button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse" data-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon">
				<i class="icon-menu"></i>
			</span>
		</button>
		<button class="topbar-toggler more"><i class="icon-options-vertical"></i></button>
		<div class="nav-toggle">
			<button class="btn btn-toggle toggle-sidebar">
				<i class="icon-menu"></i>
			</button>
		</div>
	</div>
	<!-- End Logo Header -->

	<!-- Navbar Header -->
	<nav class="navbar navbar-header navbar-expand-lg" data-background-color="blue">

		<div class="container-fluid">



			<div class="collapse" id="search-nav">
				<ul class="navbar-nav navbar-left topbar-nav nav-search mr-md-3 align-items-center">


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


			<ul class="navbar-nav topbar-nav ml-md-auto align-items-center">




				<!-- Icon Jam -->
				<li class="nav-item toggle-nav-search hidden-caret">
					<a class="nav-link" data-toggle="collapse" href="#search-nav" role="button" aria-expanded="false" aria-controls="search-nav">
						<i class="fa fa-clock"></i>
					</a>
				</li>

				<!-- Icon Calendar -->
				<li class="nav-item dropdown hidden-caret">


					<!-- <a style="text-decoration: none;" href="#" data-toggle="modal" data-target="#Modalkalender"> <i class="fas fa-download"></i> Click Here </a> -->

					<a class="nav-link" href="#" role="button" data-toggle="modal" data-target="#Modalkalender" aria-haspopup="true" aria-expanded="false">
						<i class="fa fa-calendar"></i>
					</a>
					<!-- 
					<ul class="float-right dropdown-menu dropdown-calendar dropdown-user animated fadeIn">
						<div class="dropdown-user-scroll scrollbar-outer">
							<div class="card-body text-center text-accent-1">
							</div>
						</div>
					</ul> -->

				</li>



				<!-- Notification -->
				<li class="nav-item dropdown hidden-caret" id="notification">



					<a class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fa fa-bell"></i>

						<span id="count_notification"></span>

					</a>


					<div id="notification_alert">
						<ul class='dropdown-menu messages-notif-box animated fadeIn' aria-labelledby='notifDropdown' id=''>

							<li>
								<div class='dropdown-title'>New Notification</div>
							</li>
							<li>
								<div class='dropdown-title'>You don't have new notification</div>
							</li>
						</ul>
					</div>




				</li>



				<!-- Quick Access -->
				<!-- <li class="nav-item dropdown hidden-caret">
					<a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
						<i class="fas fa-layer-group"></i>
					</a>
					<div class="dropdown-menu quick-actions quick-actions-info animated fadeIn">
						<div class="quick-actions-header">
							<span class="title mb-1">Quick Actions</span>
							<span class="subtitle op-8">Shortcuts</span>
						</div>
						<div class="quick-actions-scroll scrollbar-outer">
							<div class="quick-actions-items">
								<div class="row m-0">
									<a class="col-6 col-md-4 p-0" href="index.php?page=my_company">
										<div class="quick-actions-item">
											<div class="avatar-item bg-info rounded-circle">
												<i class="far fa-building"></i>
											</div>
											<span class="text">My Company</span>
										</div>
									</a>

									<?php if ($_SESSION['user_type'] == 'HRD') : ?>
										<a class="col-6 col-md-4 p-0" href="index.php?page=industry_offer">
											<div class="quick-actions-item">
												<div class="avatar-item bg-warning rounded-circle">
													<i class="fas fa-briefcase"></i>
												</div>
												<span class="text">Job Offer</span>
											</div>
										</a>
										<a class="col-6 col-md-4 p-0" href="index.php?page=intern_candidate">
											<div class="quick-actions-item">
												<div class="avatar-item bg-danger rounded-circle">
													<i class="fab fa-wpforms"></i>
												</div>
												<span class="text">Intern Candidate</span>
											</div>
										</a>

									<?php else : ?>
										<a class="col-6 col-md-4 p-0" href="index.php?page=intern_list">
											<div class="quick-actions-item">
												<div class="avatar-item bg-warning rounded-circle">
													<i class="fab fa-wpforms"></i>
												</div>
												<span class="text">Intern List</span>
											</div>
										</a>
									<?php
									endif;

									?>

									<a class="col-6 col-md-4 p-0" href="index.php?page=industry_profile">
										<div class="quick-actions-item">
											<div class="avatar-item bg-aqua rounded-circle">
												<i class="fas fa-user"></i>
											</div>
											<span class="text">Profile</span>
										</div>
									</a>

									<a class="col-6 col-md-4 p-0" href="index.php?page=industry_profile&tab=profile">
										<div class="quick-actions-item">
											<div class="avatar-item bg-secondary rounded-circle">
												<i class="fas fa-image"></i>
											</div>
											<span class="text">Change Profile Pict</span>
										</div>
									</a>

									<a class="col-6 col-md-4 p-0" href="index.php?page=industry_profile&tab=password">
										<div class="quick-actions-item">
											<div class="avatar-item bg-warning rounded-circle">
												<i class="fas fa-key"></i>
											</div>
											<span class="text">Change Password</span>
										</div>
									</a>

								</div>
							</div>
						</div>
					</div>
				</li> -->



				<!-- NaN -->
				<!-- <li class="nav-item">
					<a href="#" class="nav-link quick-sidebar-toggler">
						<i class="fa fa-th"></i>
					</a>
				</li> -->


				<li class="nav-item dropdown hidden-caret">
					<a aria-label="Current Date and Calendar" class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">
						<span>

							Weather Info :


							<?php if ($_SESSION['WEATHER']['WEATHER_TEXT']) { ?>
								<?= $_SESSION['WEATHER']['WEATHER_TEXT'] ?? '<i class="fas fa-ban text-danger"></i> Not Available' ?>
							<?php

							} else { ?>

								<i class="fas fa-ban text-danger"></i> Not Available

							<?php

							} ?>

						</span>
					</a>
					<ul class="float-right dropdown-menu dropdown-calendar dropdown-user animated fadeIn">
						<div class="dropdown-user-scroll scrollbar-outer">

							<div class="card-body text-center text-accent-1 ">
								<h3> <i class="fas fa-map-pin"></i> Batam, Indonesia</h3>
								<h2>

									<?php if ($_SESSION['WEATHER']['WEATHER_TEXT']) { ?>

										<img width='35' height='25' src='viewer.php?src=get_weather&location=<?= base64_encode($_SESSION['WEATHER']['WEATHERICON_URL']) ?>' alt='Weather Icon'></img>

										<?= $_SESSION['WEATHER']['WEATHER_TEXT'] ?>

									<?php

									} else { ?>

										<i class="fas fa-ban text-danger"></i>
										No Weather data Available

									<?php

									} ?>

									</h1>

							</div>
						</div>
					</ul>
				</li>



				<!-- Profile -->
				<li class="nav-item dropdown hidden-caret">
					<a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#" aria-expanded="false">
						<div class="avatar-sm">
							<img src="viewer.php?src=profile_picture&location=<?= base64_encode($_SESSION['profile_picture']) ?>" alt="..." class="avatar-img rounded-circle">

						</div>
					</a>
					<ul class="dropdown-menu dropdown-user animated fadeIn">
						<div class="dropdown-user-scroll scrollbar-outer">
							<li>
								<div class="user-box">
									<div class="avatar-lg">
										<img src="viewer.php?src=profile_picture&location=<?= base64_encode($_SESSION['profile_picture']) ?>" alt="..." class="avatar-img rounded">

									</div>
									<div class="u-text">
										<h5><?= $_SESSION['user_fullname'] ?></h5>
										<p class="text-muted"><?= $_SESSION['user_type'] ?></p><a href="index.php?page=industry_profile" class="btn btn-xs btn-secondary btn-sm">View Profile</a>
									</div>
								</div>
							</li>
							<li>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="index.php">My Dashboard</a>
								<a class="dropdown-item" href="index.php?page=industry_profile">My Profile</a>
								<a class="dropdown-item" href="index.php?page=my_company">My Company</a>
								<!-- <a class="dropdown-item" href="#">Inbox</a> -->
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="index.php?page=home">Home</a>
								<a class="dropdown-item" href="index.php?page=announcements">Announcements</a>
								<div class="dropdown-divider"></div>


								<a class="dropdown-item" href="#" onclick="logout_confirm()">Logout</a>
							</li>
						</div>
					</ul>
				</li>
			</ul>


		</div>
	</nav>
	<!-- End Navbar -->
</div>


<script>
	$(document).ready(function() {

		clock_run();
		
		show_calendar();

		// localStorage.setItem('first', 0);
		// localStorage.setItem('next_chime', 0);

		// count_notification();
		// notification();

	});


	function count_notification() {

		var id_company = '<?= base64_encode(base64_encode($_SESSION['id_company'])) ?>';

		$.ajax({

			url: './ajax/industri_count_notification.php',
			type: 'post',
			data: {
				'id_company': id_company,
			},
			dataType: 'json',


			success: function(response) {


				var len = response.length;
				if (response[0]['total_notif'] > 0) {
					var add_class = 'notification';
					var count_notif = response[0]['total_notif'];
					var myAudio = new Audio('./assets/sound/chime.mp3');

				} else {
					var add_class = 'new'
					var count_notif = '0';
				}




				if (localStorage.getItem("first") == 0) {



					document.getElementById('count_notification').classList.add(add_class);

					if (count_notif > 0) {
						$('#count_notification').text(count_notif);
					}


					localStorage.setItem('initialise_count', count_notif);
					localStorage.setItem('current_count', count_notif);




					if (localStorage.getItem('first_chime') != 1) {

						setTimeout(function() {
							myAudio.play();
						}, 1000);

						myAudio.pause();


					}

				}




			},
			error: function() {
				console.log('gagal');

			}


		});
	}

	function notification() {


		var id_company = '<?= base64_encode(base64_encode($_SESSION['id_company'])) ?>';

		$.ajax({

			url: './ajax/industri_notification.php',
			type: 'post',
			data: {
				'id_company': id_company,
			},


			success: function(response) {

				if (localStorage.getItem("first") == 0) {

					$('#notification_alert').html(response);

					localStorage.setItem('first', 1);
					localStorage.setItem('first_chime', 1);

				}


			},
			error: function() {
				console.log('gagal');

			}


		});

	}


	function check_count_notifications() {
		var id_company = '<?= base64_encode(base64_encode($_SESSION['id_company'])) ?>';

		$.ajax({

			url: './ajax/industri_count_notification.php',
			type: 'post',
			data: {
				'id_company': id_company,
			},
			dataType: 'json',


			success: function(response) {


				var len = response.length;
				if (response[0]['total_notif'] > 0) {
					var add_class = 'notification';
					var count_notif = response[0]['total_notif'];
					var myAudio = new Audio('./assets/sound/chime.mp3');

				} else {
					var add_class = 'old'
					var count_notif = '0';
				}

				if (localStorage.getItem('initialise_count') != count_notif) {
					document.getElementById('count_notification').classList.add(add_class);
					$('#count_notification').text(count_notif);

					localStorage.setItem('current_count', count_notif);
					var myAudio = new Audio('./assets/sound/chime.mp3');

					if (localStorage.getItem('next_chime') != 1) {
						setTimeout(function() {
							myAudio.play();
						}, 1000);

						myAudio.pause();
					}

					localStorage.setItem('next_chime', 1);
					localStorage.setItem('get_new_notif', 0);
					localStorage.setItem('initialise_count', count_notif);



				}

			},

			error: function() {
				console.log('gagal');

			}


		});
	}

	function check_notifications() {


		var id_company = '<?= base64_encode(base64_encode($_SESSION['id_company'])) ?>';

		$.ajax({

			url: './ajax/industri_notification.php',
			type: 'post',
			data: {
				'id_company': id_company,
			},


			success: function(new_data) {

				// console.log(new_data);
				// console.log('success cek notif');


				if (localStorage.getItem("get_new_notif") == 0) {

					// console.log('new notif');

					$('#notification_alert').html(new_data);


					localStorage.setItem('get_new_notif', 1);
					localStorage.setItem('next_chime', 0);
				}


			},
			error: function() {
				console.log('gagal');

			}


		});

	}




	// setInterval(function() {
	// 	check_count_notifications();
	// 	check_notifications();
	// }, 2000);


	function show_calendar() {
		var date = new Date();
		var d = date.getDate();
		var m = date.getMonth();
		var y = date.getFullYear();
		var className = Array('fc-primary', 'fc-danger', 'fc-black', 'fc-success', 'fc-info', 'fc-warning', 'fc-danger-solid', 'fc-warning-solid', 'fc-success-solid', 'fc-black-solid', 'fc-success-solid', 'fc-primary-solid');


		$calendar = $('#calendar');
		$calendar.fullCalendar({
			fixedWeekCount: false, // Set false agar jumlah minggu yang ditampilkan menyesuaikan dengan bulan aktif
		});
	}

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
			let hours2 = hours + 1;
			let minutes = d.getMinutes();
			let seconds = d.getSeconds();


			let time = ((hours < 10 ? "0" : "") + hours) + ' : ' + ((minutes < 10 ? "0" : "") + minutes) + ' : ' + ((seconds < 10 ? "0" : "") + seconds);
			let time2 = ((hours2 < 10 ? "0" : "") + hours2) + ' : ' + ((minutes < 10 ? "0" : "") + minutes) + ' : ' + ((seconds < 10 ? "0" : "") + seconds);

			$("#clock").text(time);
			$("#clock2").text(time);
			$("#clock3").text(time2);

		}, 1000);


	}

</script>