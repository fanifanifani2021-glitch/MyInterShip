		<!-- Sidebar -->
		<div class="sidebar sidebar-style-2">
			<div class="sidebar-wrapper scrollbar scrollbar-inner">
				<div class="sidebar-content">
					<div class="user">
						<div class="avatar-sm float-left mr-2">
							<img aria-label="User's Profile Picture" src="viewer.php?src=profile_picture" alt="User's Profile Picture" class="avatar-img rounded-circle">
						</div>
						<div class="info">
							<a data-toggle="collapse" href="#collapseExample" aria-expanded="true">
								<span>
									<span class="wrap2"><?= $_SESSION['name'] ?></span>
									<span class="user-level">Lecturer</span>
								</span>
							</a>
							<div class="clearfix"></div>

						</div>
					</div>
					<ul class="nav nav-primary">
						<li class="nav-item <?php checker( $route , ['dashboard', ''], 'active' ) ?>">
							<a aria-label="Go to Dashboard" href="index.php" class="collapsed" aria-expanded="false">
								<i class="fas fa-home"></i>
								<p>Dashboard</p>
							</a>
						</li>

						<li class="nav-item <?php checker( $route, ['company_list'], 'active' ) ?>">
							<a aria-label="Go to Company List" href="index.php?page=company_list" class="collapsed" aria-expanded="false">
								<i class="fas fa-building"></i>
								<p class="wrap">Company List</p>
							</a>
						</li>

						<?php include './view/dosen/template/sidebar-dospem.php'; ?>

						
						<li class="nav-item <?php checker($route, ['logbook_student'], 'active') ?>">
							<a aria-label="Go to Logbook" href="index.php?page=logbook_student" class="collapsed" aria-expanded="false">
								<i class="fas fa-book"></i>
								<p class="wrap">Student Logbook</p>
							</a>
						</li>


						<li class="nav-item <?php checker($route, ['attendance_student'], 'active') ?>">
							<a aria-label="Go to Attendance" href="index.php?page=attendance_student" class="collapsed" aria-expanded="false">
								<i class="fas fa-calendar"></i>
								<p class="wrap">Student Attendance</p>
							</a>
						</li>

						<?php if ( in_array($_SESSION['is_koor'] , [true, 1] ) ) : include './view/dosen/template/sidebar-doskoor.php';
						endif; ?>

						



						<li class="nav-section">
							<span class="sidebar-mini-icon">
								<i class="fa fa-ellipsis-h"></i>
							</span>
							<h4 class="text-section">Account</h4>
						</li>


						<li class="nav-item <?php checker( $route, ['lecturer_profile'], 'active' ) ?>">
							<a aria-label="Go to Lecturer Profile" href="index.php?page=lecturer_profile">
								<i class="fas fa-user"></i>
								<p>Lecturer Profile</p>
							</a>
						</li>



						<li class="nav-item">
							<a aria-label="Logout Button" href="#" onclick="logout_confirm()" >
								<i class="fas fa-sign-out-alt"></i>
								<p>Logout</p>
							</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<!-- End Sidebar -->

		<link rel='stylesheet' href='./assets/sweetalert2.css'>
		<script src='./assets/sweetalert2.all.js'></script>