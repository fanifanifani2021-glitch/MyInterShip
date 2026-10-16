		<!-- Sidebar -->
		<div class="sidebar sidebar-style-2">
		  <div class="sidebar-wrapper scrollbar scrollbar-inner">
		    <div class="sidebar-content">
		      <div class="user">
		        <div class="avatar-sm float-left mr-2">
		          <img src="viewer.php?src=profile_picture" alt="..." class="avatar-img rounded-circle">
		        </div>
		        <div class="info">
		          <a data-toggle="collapse" href="#collapseExample" aria-expanded="true">
		            <span>
		              <span class="wrap2"><?= $_SESSION['name'] ?></span>
		              <span class="user-level"><?= $_SESSION['role'] ?></span>
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

		        <li class="nav-item <?php if ($route == 'student_identity') {
                                  echo "active";
                                } ?>">
		          <a href="index.php?page=student_identity" class="collapsed" aria-expanded="false">
		            <i class="fas fa-id-card"></i>
		            <p>Student Identity</p>
		          </a>
		        </li>


		        <li class="nav-section">
		          <span class="sidebar-mini-icon">
		            <i class="fa fa-ellipsis-h"></i>
		          </span>
		          <h4 class="text-section">Internship Menu</h4>
		        </li>


		        <li class="nav-item <?php checker($route, ['student_offer', 'register_offer'], 'active') ?>">
		          <a href="index.php?page=student_offer" class="collapsed" aria-expanded="false">
		            <i class="fas fa-clipboard-list"></i>
		            <p>Internship Offer</p>
		          </a>
		        </li>


		        <li class="nav-item <?php checker($route, ['register_independent', 'register_cooperation'], 'active') ?>">
		          <a data-toggle="collapse" href="#register_internship" class="collapsed" aria-expanded="false">
		            <i class="fab fa-wpforms"></i>
		            <p>Register Internship</p>
		            <span class="caret"></span>
		          </a>
		          <div class="collapse" id="register_internship">
		            <ul class="nav nav-collapse open">
		              <li class="<?php checker($route, ['register_cooperation'], 'active') ?>">
		                <a href="index.php?page=register_cooperation">
		                  <span class="sub-item">Register Cooperation Internship</span>
		                </a>
		              </li>
		              <!-- <li class="<?php checker($route, ['register_independent'], 'active') ?>">
										<a href="index.php?page=register_independent">
											<span class="sub-item">Register Independent Internship</span>
										</a>
									</li> -->

		            </ul>
		          </div>
		        </li>

		        <li class="nav-item <?php if ($route == 'my_internship') {
                                  echo "active";
                                } ?>">
		          <a href="index.php?page=my_internship" class="collapsed" aria-expanded="false">
		            <i class="fas fa-briefcase"></i>
		            <p>My Internship</p>
		          </a>
		        </li>


				<li class="nav-item <?php if ($route == 'my_internship_letter') {
                                  echo "active";
                                } ?>">
                	<a href="index.php?page=my_internship_letter" class="collapsed" aria-expanded="false">
                    	<i class="fas fa-file-alt"></i>
                    	<p>Internship Letter</p>
                    </a>
                </li>


		        <li class="nav-section">
		          <span class="sidebar-mini-icon">
		            <i class="fa fa-ellipsis-h"></i>
		          </span>
		          <h4 class="text-section">Account</h4>
		        </li>

		        <li class="nav-item">
		          <a href="https://wa.me/6281364440803" target="_blank"  class="collapsed" aria-expanded="false">
		            <i class="fas fa-question"></i>
		            <p>Helpdesk</p>
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



		<!-- Modal Logout -->
		<div class="modal fade" id="ModalLogout2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title" id="exampleModalLabel">Logout Confirmation</hh5>
		      </div>
		      <div class="modal-body">
		        Are you sure you want to end current session?
		      </div>
		      <form action="index.php?form=logout" method="POST"><?php csrf(); ?>
		        <div class="modal-footer">
		          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
		          <button type="submit" class="btn btn-primary">Yes, take me out!</button>
		        </div>
		      </form>
		    </div>
		  </div>
		</div>



		<link rel='stylesheet' href='./assets/sweetalert2.css'>
		<script src='./assets/sweetalert2.all.js'></script>