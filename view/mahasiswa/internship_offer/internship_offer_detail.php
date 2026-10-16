<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = 'Internship Offer Detail';
$route = 'register_offer';
include "./view/mahasiswa/template/head.php";


$id = base64_decode($_GET['id_offer']);

$sql = mysqli_query($koneksi_db, "SELECT * FROM company,offer where offer.id_company = company.id_company AND offer.id_offer = $id");
$data = mysqli_fetch_assoc($sql);

if ($sql->num_rows != 1) {
	make_swal2('error', 'Not Allowed', 'Intern Offer is not found !');
	redirect_back(1);
	exit;
}

if ( $data['status'] == 'Open' ) {
	$status = "<button type='button' class='btn btn-success btn-sm'>Open</button>";
} else {
	$status = "<button type='button' class='btn btn-danger btn-sm'>Closed</button>";
}



$nim = $_SESSION['nim'];

$sql = "SELECT * FROM student_internship WHERE nim = '$nim'";
$exec = $_SERVER['KONEKSI_DB']->query($sql);

if ($exec->num_rows != 1) {
	make_swal2('warning', 'Not Allowed', 'You don`t have access to this page !');
	redirect_back(1);
	exit;
}


?>

<body>
	<div class="wrapper">

		<?php include "./view/mahasiswa/template/main-header.php"; ?>
		<?php include "./view/mahasiswa/template/sidebar.php"; ?>


		<div class="main-panel">

			<div class="container">
				<div class="page-inner">

					<div class="page-header">
						<h4 class="page-title">Internship Offer Detail</h4>
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
								<a href="index.php?page=student_offer">Internship Offer</a>
							</li>
							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="#">Internship Offer Detail</a>
							</li>
						</ul>
					</div>


					<!-- Form -->
					<div class="row">



						<!-- Table Detail -->
						<div class="col-md-12">

							<div class="card full-height">
								<div class="card-header">
									<div class="d-flex align-items-center">
										<h4 class="card-title">Offer Detail</h4>
									</div>
								</div>
								<div class="card-body">
									<div class="table-responsive">
										<table class="table table-bordered table-hover">
											<tbody>
												<tr>
													<th width="30%">Company Name <span class="float-right">:</span></th>
													<td width="70%"><span><?= $data['name']; ?></span></td>
												</tr>
												<tr>
													<th width="30%">Position <span class="float-right">:</span></th>
													<td width="70%"><span><?= $data['position']; ?></span></td>
												</tr>

												<tr>
													<th width="30%">Employer Type <span class="float-right">:</span></th>
													<td width="70%"><span><?= $data['employer_type']; ?></span></td>
												</tr>
												<tr>
													<th width="30%">City <span class="float-right">:</span></th>
													<td width="70%"><span><?= $data['city']; ?></span></td>
												</tr>
												<tr>
													<th width="30%">Job Type <span class="float-right">:</span></th>
													<td width="70%"><span><?= $data['job_type']; ?></span></td>
												</tr>
												
												<tr>
													<th width="30%">Start Date <span class="float-right">:</span></th>
													<td width="70%"><span><?= date('d-m-Y', strtotime($data['start_date'])); ?></span></td>
												</tr>
												<tr>
													<th width="30%">End Date <span class="float-right">:</span></th>
													<td width="70%"><span><?= date('d-m-Y', strtotime($data['end_date'])); ?></span></td>
												</tr>
												<tr>
													<th width="30%">Offer Type <span class="float-right">:</span></th>
													<td width="70%"><span><?= $data['offer_type']; ?></span></td>
												</tr>
												
												<?php if ( !in_array( $data['offer_type'], ['Part Time']) ) : 
													if ( $data['offer_type'] == 'Internship') : $label_salary = "Allowence"; else : $label_salary = 'Salary Range'; endif;
													?>

												<tr>
                                                    <th width="30%"><?= $label_salary ?> <span class="float-right">:</span></th>
                                                    <td width="70%"><span><?=$data['salary_range']; ?></span></td>
                                                </tr>

												<?php endif; ?>

												<tr>
                                                    <th width="30%">Offer Status <span class="float-right">:</span></th>
                                                    <td width="70%"><span><?= $status; ?></span></td>
                                                </tr>

												<tr>
                                                    <th width="30%">Date Added <span class="float-right">:</span></th>
                                                    <td width="70%"><span><?= date( 'D, d M Y H:i:s' ,strtotime( $data['timestamp_add'] ) ); ?> ( <?= time_elapsed_string( $data['timestamp_add'] ) ?> )</span></td>
                                                </tr>
											</tbody>
										</table>
									</div>
								</div>

							</div>
						</div>



						<!-- Description -->
						<div class="col-md-12">
							<div class="card full-height">
								<div class="card-header">
									<div class="d-flex align-items-center float-left">
										<h4 class="card-title">Description</h4>
									</div>
								</div>
								<div class="card-body">
									<?= $data['description']; ?>
								</div>

							</div>
						</div>


						<!-- Requirement -->
						<div class="col-md-12">
							<div class="card full-height">
								<div class="card-header">
									<div class="d-flex align-items-center float-left">
										<h4 class="card-title">Requirement</h4>
									</div>
								</div>
								<div class="card-body">
									<?= $data['requirement']; ?>
								</div>

							</div>
						</div>





						<!-- Applicant List -->
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<div class="card-title">Applicant List
									</div>
								</div>
								<div class="card-body">
									<div class="table-responsive">
										<table id="basic-datatables" class="display table table-striped table-hover">
											<thead>
												<tr>
													<th>No.</th>
													<th>NIM</th>
													<th>Applicant Name</th>
													<th>Status</th>
													<th>Apply Date</th>
												</tr>
											</thead>

											<tbody>

												<?php

												$sql 	= "SELECT * FROM student_internship, internship, applicant WHERE student_internship.nim = internship.nim AND internship.id_internship = applicant.id_internship AND applicant.id_offer = '$id' ";
												$exec = $_SERVER['KONEKSI_DB']->query($sql);
												?>

												<?php foreach ($exec as $i => $intern_data) : ?>

													<tr>
														<td><?= $i + 1 ?>.</td>
														<td><?= $intern_data['nim'] ?></td>
														<td><?= $intern_data['name'] ?></td>
														<td><?= $intern_data['status'] ?></td>
														<td><?= date('D, M d Y', strtotime($intern_data['date'])) ?></td>



													</tr>

												<?php endforeach; ?>

											</tbody>


										</table>
									</div>
								</div>
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

	<!-- User Custom Script -->
	<script>
		$(document).ready(function() {
			$('#basic-datatables').DataTable({});
		});
	</script>

</body>

</html>