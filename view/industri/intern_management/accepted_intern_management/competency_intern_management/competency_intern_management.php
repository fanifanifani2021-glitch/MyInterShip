<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = 'Student Competency';
$route = 'accepted_intern_management';
include "./view/industri/template/head.php";


$id_internship = base64_decode($_GET['id_internship']);

// Cek Internship
$sql = "SELECT * FROM internship WHERE id_internship = '$id_internship'";
$exec = $_SERVER['KONEKSI_DB']->query($sql);

if ($exec->num_rows != 1) {
	make_swal2('error', 'Not Allowed', 'Internship Data is not found !');
	redirect_referrer(1);
	exit();
}

$data_internship = $exec->fetch_assoc();
$nim = $data_internship['nim'];
$_GET['nim'] = $nim;

// Prodi
$sql = "SELECT * FROM student_internship WHERE nim = '$nim'";
$mhs_data = $_SERVER['KONEKSI_DB']->query($sql)->fetch_assoc();
$kode_prodi = $mhs_data['program_study'];
$_GET['kode_prodi'] = $kode_prodi;

$sql 	= "SELECT * FROM profile_jobdesc WHERE kode_prodi = '$kode_prodi'";
$exec	= $_SERVER['KONEKSI_DB']->query($sql);

if ($exec->num_rows < 1 ) {
	make_swal2('error', 'Not Allowed', 'Jobdesc Master data is not ready yet !');
	redirect_referrer(1);exit();
}

$num = $exec->num_rows;
$row = $exec->fetch_assoc();
$id_job = $row['id_jobdesc'];

$sql 	= "SELECT * FROM jobdesc_intern WHERE id_internship = '$id_internship'";
$exec2	= $_SERVER['KONEKSI_DB']->query($sql);

$num = $exec2->num_rows;

if ($num == 1) {

	$data_job_intern = mysqli_fetch_assoc($exec2);

	$num_jobdesc = $exec2->num_rows;

	$_GET['id_jobdesc_intern'] = $data_job_intern['id_jobdesc_intern'];
	$id_jobdesc_intern = $data_job_intern['id_jobdesc_intern'];
	$id_jobdesc = $data_job_intern['id_jobdesc'];

	$act = "index.php?form=jobdesk_spv&aksi=edit";
} else {
	$act = "index.php?form=jobdesk_spv&aksi=add";
}


?>

<body>
	<div class="wrapper">

		<?php include "./view/industri/template/main-header.php"; ?>
		<?php include "./view/industri/template/sidebar.php"; ?>


		<div class="main-panel">

			<div class="container">
				<div class="page-inner">

					<div class="page-header">
						<h4 class="page-title">Student Competency</h4>
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
								<a href="index.php?page=accepted_intern_management">Accepted Intern Management</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="index.php?page=detail_intern_management&id_internship=<?= base64_encode($id_internship) ?>">Intern Detail</a>
							</li>

							
							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="#">Student Competency</a>
							</li>
						</ul>
					</div>


					<!-- Form -->
					<div class="row">

						<!-- Jobdesc -->
						<div class="col-md-12">
							<div class="card full-height">
								<div class="card-header">
									<div class="card-title">Intern Competency
									</div>
								</div>



								<div class="card-body">

									<div class="table-responsive">
										<table id="basic-datatables" class="display table table-striped table-hover">

											<thead>
												<tr>
													<th>No.</th>
													<th>Profile</th>
													<th>Start Date</th>
													<th>End Date</th>
													<th>Average Score</th>
													<th>Action</th>
												</tr>
											</thead>

											<tbody>
												<?php

												$sql = "SELECT * FROM profile_student_jobdesc WHERE id_internship = '$id_internship' ORDER BY id_profile_student_jobdesc ASC";
												$exec = $_SERVER['KONEKSI_DB']->query($sql);

												foreach ($exec as $i => $data) :

													$id_profile_jobdesc = $data['id_profile_jobdesc'];
													$sql2 = "SELECT * FROM profile_jobdesc WHERE id_profile_jobdesc = '$id_profile_jobdesc'";
													$data_profile = $_SERVER['KONEKSI_DB']->query($sql2)->fetch_assoc();

													$id_profile_student_jobdesc  = $data['id_profile_student_jobdesc'];
													$sql3 = "SELECT SUM(`nilai`) / COUNT(`nilai`) AS average_score FROM profile_student_detail_comp WHERE id_profile_student_jobdesc = '$id_profile_student_jobdesc'";
													$data['average_score'] = $_SERVER['KONEKSI_DB']->query($sql3)->fetch_assoc()['average_score'];




													// 


												?>

													<tr>
														<td><?= $i + 1 ?>.</td>
														<td><?= $data_profile['nama_profile'] ?></td>
														<td><?= date('D, d-M-Y', strtotime($data['start_date'])) ?></td>
														<td><?= date('D, d-M-Y', strtotime($data['end_date'])) ?></td>
														<td><?= number_format($data['average_score'],2) ?> out of 4</td>

														<td>
															<div class="btn-group btn-round">
																<button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																	Action
																</button>
																<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">

																	<button class="dropdown-item" onclick="window.location.href = 'index.php?page=competency_detail_intern_management&id_internship=<?= base64_encode($data['id_internship']) ?>&id_profile_student=<?= base64_encode($data['id_profile_student_jobdesc']) ?>'">Detail Competency</button>


																</div>
															</div>
														</td>
													</tr>




												<?php
												endforeach;
												?>
											</tbody>



										</table>
									</div>


								</div>


							</div>
						</div>


					</div>


				</div>



			</div>




			<?php include "./view/industri/template/footer.php"; ?>

		</div>

	</div>

	<?php include "./view/industri/template/script.php"; ?>

	<!-- Datatables -->
	<script src="./assets/js/plugin/datatables/datatables.min.js"></script>

	<script>
		$(document).ready(function() {

			$('#basic-datatables').DataTable({});

		});
	</script>

	<script>
		function delete_confirm( id ) {
			swal.fire({
				title               : 'Delete Confirmation',
				text                :  'Are you sure you want to delete current Student Competency ?',
				icon                : 'question',
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Yes, delete it!",
			}).then((result) => {
				/* Read more about isConfirmed, isDenied below */
				if (result.isConfirmed) {


					// AJAX
					$.ajax({
						url: "./ajax/delStudentCompetency.php",
						type: "POST",
						data: { id_profile_student_jobdesc : id },
						dataType: 'text',
					success: function ( response ) {
						window.location.reload(1);
					},
					error: function ( response ) {
						Swal.fire("Deleting Data Failed !", "Please try again later !", "error");
					}
					});
					
				}
			})

		}
	</script>

</body>

</html>