		<!-- Sidebar -->
		<div class="sidebar sidebar-style-2">
			<div class="sidebar-wrapper scrollbar scrollbar-inner">
				<div class="sidebar-content">
					<div class="user">
						<div class="avatar-sm float-left mr-2">
							<img src="viewer.php?src=profile_picture&location=<?= base64_encode($_SESSION['profile_picture']) ?>" alt="..." class="avatar-img rounded-circle">
						</div>
						<div class="info">
							<a data-toggle="collapse" href="#collapseExample" aria-expanded="true">
								<span>

									<span class="wrap2"><?= $_SESSION['user_fullname'] ?></span>
									<span class="user-level"><?= $_SESSION['user_type'] ?></span>
								</span>
							</a>
							<div class="clearfix"></div>

						</div>
					</div>
					<ul class="nav nav-primary">
						<li class="nav-item <?php if ($route == 'dashboard') {
												echo "active";
											} ?>">
							<a href="index.php" class="collapsed" aria-expanded="false">
								<i class="fas fa-home"></i>
								<p>Dashboard</p>
							</a>
						</li>


						<li class="nav-item <?php if ($route == 'my_company') {
												echo "active";
											} ?>">
							<a href="index.php?page=my_company" class="collapsed" aria-expanded="false">
								<i class="fas fa-building"></i>
								<p>My Company</p>
							</a>
						</li>

						<li class="nav-item <?php if ($route == 'industry_offer') {
												echo "active";
											} ?>">
							<a href="index.php?page=industry_offer" class="collapsed" aria-expanded="false">
								<i class="fas fa-briefcase"></i>
								<p>Job Offers</p>
							</a>
						</li>

						<?php if ($_SESSION['user_type'] == 'HRD') : include './view/industri/template/sidebar-hrd.php';
						endif; ?>
						<?php if ($_SESSION['user_type'] == 'supervisor' || $_SESSION['user_type'] == 'Supervisor') : include './view/industri/template/sidebar-supervisor.php';
						endif; ?>


						<li class="nav-item submenu <?php if (in_array($routes, ['intern_logbook', 'intern_attendance', 'intern_administration'])) {
														echo "active";
													} ?>">

							<a data-toggle="collapse" href="#internship_data" class="collapsed" aria-expanded="true">
								<i class="fab fa-wpforms"></i>
								<p>Internship Data</p>
								<span class="caret"></span>
							</a>
							<div class="collapse show" id="internship_data">
								<ul class="nav nav-collapse open">
									<li class="nav-item <?php echo in_array($routes, ['intern_logbook']) ? 'active' : ''; ?>">
										<a aria-label="Go to Logbook" href="index.php?page=intern_logbook" class="collapsed" aria-expanded="false">
											<i class="fas fa-book"></i>
											<p class="wrap">Student Logbook</p>
										</a>
									</li>

									<li class="nav-item <?php echo in_array($routes, ['intern_attendance']) ? 'active' : ''; ?>">
										<a aria-label="Go to Attendance" href="index.php?page=intern_attendance" class="collapsed" aria-expanded="false">
											<i class="fas fa-calendar"></i>
											<p class="wrap">Student Attendance</p>
										</a>
									</li>

									<li class="nav-item <?php echo in_array($routes, ['intern_administration']) ? 'active' : ''; ?>">
										<a aria-label="Go to Administration" href="index.php?page=intern_administration" class="collapsed" aria-expanded="false">
											<i class="fas fa-file"></i>
											<p class="wrap">Student Administration</p>
										</a>
									</li>


								</ul>
							</div>
						</li>



						<li class="nav-section">
							<span class="sidebar-mini-icon">
								<i class="fa fa-ellipsis-h"></i>
							</span>
							<h4 class="text-section">Account</h4>
						</li>

						<li class="nav-item <?php if ($route == 'industry_profile') {
												echo "active";
											} ?>">
							<a href="index.php?page=industry_profile" class="collapsed" aria-expanded="false">
								<i class="fas fa-user"></i>
								<p>Profile</p>
							</a>
						</li>

						<li class="nav-item">
							<a href="#" onclick="logout_confirm()" class="collapsed" aria-expanded="false">
								<i class="fas fa-sign-out-alt"></i>
								<p>Logout</p>
							</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<!-- End Sidebar -->