<?php error_reporting(false);
session_start(); ?>



<div class="main-header">

	<!-- Logo Header -->
	<div class="logo-header" data-background-color="blue2">

		<a href="index.php" class="logo" aria-label="My Internship Logo">
			<!-- <img src="./assets/img/my_internship_logo.png" alt="navbar brand" class="navbar-brand" style="width: 180px; height: auto;"> -->
			<!-- <img src="./assets/img/my_internship_logo.png" alt="navbar brand" class="navbar-brand" style="width: 180px; height: auto; filter: grayscale(1) invert(1);"> -->
			<img src="./assets/img/my_internship_logo_grey5.png" alt="navbar brand" class="navbar-brand" style="width: 180px; height: auto; ">
		</a>
		<button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse" data-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon">
				<i class="icon-menu"></i>
			</span>
		</button>
		<button aria-label="Toggle Sidebar" class="topbar-toggler more"><i class="icon-options-vertical"></i></button>
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


			<ul class="navbar-nav navbar-left topbar-nav nav-search mr-md-3 align-items-center">


				<!-- Tanggal -->
				<li class="nav-item dropdown hidden-caret">
					<a aria-label="Current Date and Calendar" class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">
						<span id="date"><?= date('D, d M Y') ?></span>
					</a>
					<ul class="float-right dropdown-menu dropdown-calendar dropdown-user animated fadeIn">
						<div class="dropdown-user-scroll scrollbar-outer">
							<div class="card-body">
								<div id="calendar">
								</div>
							</div>
						</div>
					</ul>
				</li>


				<!-- Jam -->
				<li class="nav-item dropdown hidden-caret">
					<a aria-label="Current Time" class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">
						<span id="clock"><?= date('H : i : s') ?></span>


					</a>
					<ul class="float-right dropdown-menu dropdown-calendar dropdown-user animated fadeIn">
						<div class="dropdown-user-scroll scrollbar-outer">
							<div class="card-body text-center text-accent-1 ">
								<h4>(UTC+07:00) Bangkok, Hanoi, Jakarta</h4>
								<h1>
									<span id="clock2"><?= date('H : i : s') ?></span>
								</h1>

								<h4>(UTC+08:00) Kuala Lumpur, Singapore</h3>
								<h1>
									<span id="clock3"><?= date('H : i : s') ?></span>
								</h1>

							</div>
						</div>
					</ul>
				</li>


			</ul>


			<ul class="navbar-nav topbar-nav ml-md-auto align-items-center">



				<!-- Notification -->
				<li class="nav-item dropdown hidden-caret" id="notification">


					<a aria-label="Notification Toolbar" class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fa fa-bell"></i>

						<span id="count_notification"></span>

					</a>


					<ul class='dropdown-menu messages-notif-box animated fadeIn' aria-labelledby='notifDropdown' id=''>

						<li>
							<div class='dropdown-title'>New Notification</div>
						</li>
						<li>
							<div class='dropdown-title'>You don't have new notification</div>
						</li>
					</ul>

				</li>

				<!-- Quick Action -->
				<li class="nav-item dropdown hidden-caret">
					<a aria-label="Quick Action Toolbar" class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
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
									<a class="col-6 col-md-4 p-0" href="/index.php">
										<div class="quick-actions-item">
											<div class="avatar-item bg-danger rounded-circle">
												<i class="fas fa-home"></i>
											</div>
											<span class="text">Dashboard</span>
										</div>
									</a>
									<a class="col-6 col-md-4 p-0" href="/index.php?page=intern_co_management">
										<div class="quick-actions-item">
											<div class="avatar-item bg-warning rounded-circle">
												<i class="fas fa-users"></i>
											</div>
											<span class="text">Internship Coordinator</span>
										</div>
									</a>
									<a class="col-6 col-md-4 p-0" href="/index.php?page=company_management">
										<div class="quick-actions-item">
											<div class="avatar-item bg-info rounded-circle">
												<i class="fas fa-building"></i>
											</div>
											<span class="text">Company Management</span>
										</div>
									</a>
									<a class="col-6 col-md-4 p-0" href="//index.php?page=announcement_management">
										<div class="quick-actions-item">
											<div class="avatar-item bg-success rounded-circle">
												<i class="fas fa-newspaper"></i>
											</div>
											<span class="text">Announcement Management</span>
										</div>
									</a>
									<a class="col-6 col-md-4 p-0" href="#" onclick="logout_confirm()">
										<div class="quick-actions-item">
											<div class="avatar-item bg-primary rounded-circle">
												<i class="fas fa-sign-out-alt"></i>
											</div>
											<span class="text">Logout</span>
										</div>
									</a>

								</div>
							</div>
						</div>
					</div>
				</li>

				<!-- NaN -->
				<li class="nav-item">
					<a aria-label="More Action Toolbar" href="#" class="nav-link quick-sidebar-toggler">
						<i class="fa fa-th"></i>
					</a>
				</li>

				<!-- Profile -->
				<li aria-label="Profile Toolbar" class="nav-item dropdown hidden-caret">
					<a class="dropdown-toggle profile-pic form-inline" data-toggle="dropdown" href="#" aria-expanded="false">
						<div class="avatar-sm">
							<!-- <img src="./upload/profile_picture/<?= $_SESSION['profile_picture'] ?>" alt="..." class="avatar-img rounded-circle"> -->
							<img src="viewer.php?src=profile_picture" alt="..." class="avatar-img rounded-circle">

							<!-- <img src="./assets/img/profile.jpg" alt="..." class="avatar-img rounded-circle"> -->
						</div>

						<!-- <label class="float-right ml-2 text-white"><?= $_SESSION['name'] ?></label> -->
					</a>
					<ul class="dropdown-menu dropdown-user animated fadeIn">
						<div class="dropdown-user-scroll scrollbar-outer">
							<li>
								<div class="user-box">
									<div class="avatar-sm"><img src="./assets/img/profile.jpg" alt="image profile" class="avatar-img rounded"></div>
									<div class="u-text">
										<h5><?= $_SESSION['name'] ?></h5>
										<p class="text-muted">CDC</p><a href="index.php?page=profile" class="btn btn-xs btn-secondary btn-sm">View Profile</a>
									</div>
								</div>
							</li>
							<li>
								<a class="dropdown-item" href="index.php">Dashboard</a>
								<div class="dropdown-divider"></div>

								<a class="dropdown-item" href="index.php?page=intern_student_list">Student Management</a>
								<a class="dropdown-item" href="index.php?page=intern_co_management">Coordinator Management</a>
								<a class="dropdown-item" href="index.php?page=company_management">Company Management</a>
								<a class="dropdown-item" href="index.php?page=announcement_management">Announcement Management</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="index.php?page=cdc_profile">Profile</a>
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


	function show_calendar() {
		var date = new Date();
		var d = date.getDate();
		var m = date.getMonth();
		var y = date.getFullYear();
		// var className = Array('fc-primary', 'fc-danger', 'fc-black', 'fc-success', 'fc-info', 'fc-warning', 'fc-danger-solid', 'fc-warning-solid', 'fc-success-solid', 'fc-black-solid', 'fc-success-solid', 'fc-primary-solid');

		$calendar = $('#calendar');
		$calendar.fullCalendar({
			aspectRatio: 2,
		});
	}
</script>