		<!-- Sidebar -->
		<div class="sidebar sidebar-style-2">
			<div class="sidebar-wrapper scrollbar scrollbar-inner">
				<div class="sidebar-content">
					<div class="user">
						<div class="avatar-sm float-left mr-2">
							<img aria-label="User's Profile Picture" src="viewer.php?src=profile_picture&location=<?= base64_encode($_SESSION['profile_picture']) ?>" alt="User's Profile Picture" class="avatar-img rounded-circle">
						</div>
						<div class="info">
							<a data-toggle="collapse" href="#collapseExample" aria-expanded="true">
								<span>
									<span class="wrap2"><?= $_SESSION['name'] ?></span>
									<span class="user-level">CDC</span>
								</span>
							</a>
							<div class="clearfix"></div>

						</div>
					</div>
                    <ul class="nav nav-primary">

                        <!-- ================= DASHBOARD ================= -->
                        <li class="nav-item <?php checker($route, ['', 'dashboard'], 'active') ?>">
                            <a href="<?= router('landing') ?>">
                                <i class="fas fa-tachometer-alt"></i>
                                <p class="wrap">Dashboard</p>
                            </a>
                        </li>

                        <li class="nav-item @if ($route == 'home') active @endif">
                            <a href="<?= router('home') ?>">
                                <i class="fas fa-home"></i>
                                <p class="wrap">Home</p>
                            </a>
                        </li>


                        <!-- ================= UNIVERSITY ================= -->
                        <li class="nav-section">
                            <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
                            <h4 class="text-section">University</h4>
                        </li>

                        <li class="nav-item <?php checker($route, ['my_university'], 'active') ?>">
                            <a href="<?= router('cdc/my/university.index') ?>">
                                <i class="fas fa-building"></i>
                                <p class="wrap">My University</p>
                            </a>
                        </li>


                        <!-- ================= CDC MANAGEMENT ================= -->
                        <li class="nav-section">
                            <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
                            <h4 class="text-section">CDC Management</h4>
                        </li>

                        <?php if ($_SERVER['UNIVERSITY'] != 'POLIBATAM') : ?>
                            <?php if ($_SESSION['CDC_Data']['id_kampus'] == 1) : ?>

                                <li class="nav-item <?php checker($route, ['university_management'], 'active') ?>">
                                    <a href="<?= router('cdc/university.index') ?>">
                                        <i class="fas fa-building"></i>
                                        <p class="wrap">University Management</p>
                                    </a>
                                </li>

                                <li class="nav-item <?php checker($route, ['cdc_management'], 'active') ?>">
                                    <a href="index.php?page=cdc_management">
                                        <i class="fas fa-user-secret"></i>
                                        <p class="wrap">CDC Account Management</p>
                                    </a>
                                </li>

                            <?php endif; ?>
                        <?php endif; ?>


                        <!-- ================= ACADEMIC ================= -->
                        <li class="nav-section">
                            <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
                            <h4 class="text-section">Academic</h4>
                        </li>

                        <li class="nav-item <?php checker($route, ['study_program_management'], 'active') ?>">
                            <a href="<?= router('study_program.management') ?>">
                                <i class="fas fa-graduation-cap"></i>
                                <p class="wrap">Study Program Management</p>
                            </a>
                        </li>

                        <li class="nav-item <?php checker($route, ['lecturer_management'], 'active') ?>">
                            <a href="index.php?page=lecturer_management">
                                <i class="fas fa-user"></i>
                                <p class="wrap">Lecturer Management</p>
                            </a>
                        </li>


                        <!-- ================= STUDENT & INTERNSHIP ================= -->
                        <li class="nav-section">
                            <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
                            <h4 class="text-section">Student & Internship</h4>
                        </li>

                        <li class="nav-item <?php checker($route, ['intern_student_list'], 'active') ?>">
                            <a href="index.php?page=intern_student_list">
                                <i class="fas fa-users"></i>
                                <p class="wrap">Student Management</p>
                            </a>
                        </li>

                        <li class="nav-item <?php checker($route, ['intern_co_management'], 'active') ?>">
                            <a href="index.php?page=intern_co_management">
                                <i class="fas fa-users"></i>
                                <p class="wrap">Coordinator Management</p>
                            </a>
                        </li>

                        <li class="nav-item <?php checker($route, ['cdc/internship_letter'], 'active') ?>">
                            <a href="index.php?page=cdc/internship_letter">
                                <i class="fas fa-envelope"></i>
                                <p class="wrap">Internship Letter</p>
                            </a>
                        </li>


                        <!-- ================= COMPANY ================= -->
                        <li class="nav-section">
                            <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
                            <h4 class="text-section">Company</h4>
                        </li>

                        <li class="nav-item <?php checker($route, ['company_management'], 'active') ?>">
                            <a href="index.php?page=company_management">
                                <i class="fas fa-building"></i>
                                <p class="wrap">Company Management</p>
                            </a>
                        </li>

                        <li class="nav-item <?php checker($route, ['company_mou_management'], 'active') ?>">
                            <a href="index.php?page=company_mou_management">
                                <i class="fas fa-file-contract"></i>
                                <p class="wrap">Company MoU Management</p>
                            </a>
                        </li>

                        <li class="nav-item <?php checker($route, ['job_offer'], 'active') ?>">
                            <a href="index.php?page=job_offer">
                                <i class="fas fa-briefcase"></i>
                                <p class="wrap">Job Offers</p>
                            </a>
                        </li>


                        <!-- ================= INFORMATION ================= -->
                        <li class="nav-section">
                            <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
                            <h4 class="text-section">Information</h4>
                        </li>

                        <li class="nav-item <?php checker($route, ['announcement_management'], 'active') ?>">
                            <a href="index.php?page=announcement_management">
                                <i class="fas fa-newspaper"></i>
                                <p class="wrap">Announcement Management</p>
                            </a>
                        </li>


                        <!-- ================= ACCOUNT ================= -->
                        <li class="nav-section">
                            <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
                            <h4 class="text-section">Account</h4>
                        </li>

                        <li class="nav-item <?php checker($route, ['cdc_profile'], 'active') ?>">
                            <a href="index.php?page=cdc_profile">
                                <i class="fas fa-user"></i>
                                <p class="wrap">CDC Profile</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="javascript:void(0)" onclick="logout_confirm()">
                                <i class="fas fa-sign-out-alt"></i>
                                <p class="wrap">Logout</p>
                            </a>
                        </li>

                    </ul>

				</div>
			</div>
		</div>
		<!-- End Sidebar -->