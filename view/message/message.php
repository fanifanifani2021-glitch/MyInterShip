<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = 'Dashboard UPKPK';
$route = 'dashboard';
include "./view/industri/template/head.php";


?>

<body>
	<div class="wrapper">

		<?php include "./view/industri/template/main-header.php"; ?>
		<?php include "./view/industri/template/sidebar.php"; ?>


		<div class="main-panel">

			<div class="container container-full">
				
				<div class="page-inner">
					<div class="row">
						<div class="col-md-12">
							

							<!-- Search Message and Create New Message -->
							<div class="d-flex justify-content-between">
								<div class="d-md-inline-block">
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text bg-white">
												<i class="fa fa-search search-icon"></i>
											</span>
										</div>
										<input type="text" class="form-control" aria-label="Text input with dropdown button">
										<div class="input-group-append">
											<button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Filter</button>
											<div class="dropdown-menu">
												<a class="dropdown-item" href="#">Action</a>
												<a class="dropdown-item" href="#">Another action</a>
												<a class="dropdown-item" href="#">Something else here</a>
												<div role="separator" class="dropdown-divider"></div>
												<a class="dropdown-item" href="#">Separated link</a>
											</div>
										</div>
									</div>
								</div>
								<button type="button" class="btn btn-success d-none d-sm-inline-block">New Message</button>
							</div>


							<!-- Message screen -->
							<section class="card mt-4">
								<div class="list-group list-group-messages list-group-flush">


								<?php

									$sqql = "SELECT DISTINCT(from_username) AS person, from_name FROM `message` WHERE to_username = '" .$_SESSION['username']. "'";
									$exec = $_SERVER['KONEKSI_DB']->query($sqql);

									foreach ( $exec as $i => $dat ) : 

										if ( $dat['person'] == $_SESSION['username'] ) continue;

										$sq = "SELECT * FROM `message` WHERE from_username = '" .$dat['person']. "' OR to_username = '" .$dat['person']."' ORDER BY id_message DESC LIMIT 1;";
										$dadat = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc();

									?>


									<!-- Bagian Conversation -->
									<div class="list-group-item <?php if ( $dadat['read_status'] == 0 ) { echo 'unread'; }{ echo 'read';} ?>">
										
										<!-- User Profile Pict -->
										<div class="list-group-item-figure">
											<span class="rating rating-sm mr-3">
												<input type="checkbox" id="star1" value="1">
												<label for="star1">
													<span class="fa fa-star"></span>
												</label>
											</span>
											<a href="index.php?page=conversations&mid=<?= base64_encode(base64_encode($dadat['id_message'])) ?>" class="user-avatar">
												<div class="avatar">
													<img src="./assets/img/jm_denis.jpg" alt="..." class="avatar-img rounded-circle">
												</div>
											</a>
										</div>

										<!-- Name and Message -->
										<div class="list-group-item-body pl-3 pl-md-4">
											<div class="row">
												<div class="col-12 col-lg-10">
													<h4 class="list-group-item-title">
														<a href="index.php?page=conversation&mid=<?= base64_encode(base64_encode($dadat['id_message'])) ?>"><?= $dat['from_name'] ?></a>
													</h4>
													<p class="list-group-item-text text-truncate"><?= $dadat['message'] ?></p>
												</div>
												<div class="col-12 col-lg-2 text-lg-right">
													<p class="list-group-item-text"><?= time_elapsed_string( $dadat['timestamp_send'] ) ?></p>
												</div>
											</div>
										</div>

										<!-- Action -->
										<div class="list-group-item-figure">
											<div class="dropdown">
												<button class="btn-dropdown" data-toggle="dropdown">
													<i class="icon-options-vertical"></i>
												</button>
												<div class="dropdown-arrow"></div>
												<div class="dropdown-menu dropdown-menu-right">
													<!-- <a href="#" class="dropdown-item">Read</a> -->
													<a href="#" class="dropdown-item">Mark as read</a>
													<a href="#" class="dropdown-item">Mark as unread</a>
													<!-- <a href="#" class="dropdown-item">Toggle star</a>
													<a href="#" class="dropdown-item">Trash</a> -->
												</div>
											</div>
										</div>
									</div>

									<?php endforeach; ?>
									
									
									
									
								</div>
							</section>


							<!-- Pagination -->
							<div class="mt-1 mb-2">
								<p class="text-muted"> Showing 1 to 10 of 1,000 entries </p>
								<ul class="pagination justify-content-center mb-5 mb-sm-0">
									<li class="page-item disabled">
										<a class="page-link" href="#" tabindex="-1">
											<i class="fa fa-angle-left"></i>
										</a>
									</li>
									<li class="page-item">
										<a class="page-link" href="#">1</a>
									</li>
									<li class="page-item disabled">
										<a class="page-link" href="#" tabindex="-1">...</a>
									</li>
									<li class="page-item">
										<a class="page-link" href="#">13</a>
									</li>
									<li class="page-item active">
										<a class="page-link" href="#">14</a>
									</li>
									<li class="page-item">
										<a class="page-link" href="#">15</a>
									</li>
									<li class="page-item disabled">
										<a class="page-link" href="#" tabindex="-1">...</a>
									</li>
									<li class="page-item">
										<a class="page-link" href="#">24</a>
									</li>
									<li class="page-item">
										<a class="page-link" href="#">
											<i class="fa fa-angle-right"></i>
										</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>

			<?php include "./view/mahasiswa/template/footer.php"; ?>

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