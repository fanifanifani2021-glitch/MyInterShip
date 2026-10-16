<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = 'Student Competency Detail';
$route = 'intern_student_management';
include "./view/dosen/template/head.php";

// Pengecekan Login Dosen Pembimbing
include './view/dosen/template/cek_login_dpm.php';

?>

<?php

$id_internship = base64_decode($_GET['id_internship']);

// Cek Internship
$sql = "SELECT * FROM internship WHERE id_internship = '$id_internship'";
$exec = $_SERVER['KONEKSI_DB']->query($sql);

if ($exec->num_rows != 1) {
	make_swal2('error', 'Not Allowed', 'Internship is not found !');
	redirect_referrer(1);
	exit();
}

// Get Data Internship
$data_internship = $exec->fetch_assoc();
$nim = $data_internship['nim'];
$_GET['nim'] = $nim;

// Prodi
$sql = "SELECT * FROM student_internship WHERE nim = '$nim'";
$mhs_data = $_SERVER['KONEKSI_DB']->query($sql)->fetch_assoc();
$kode_prodi = $mhs_data['program_study'];
$_GET['kode_prodi'] = $kode_prodi;


// Cek Data Profile Student
$id_profile_student = base64_decode($_GET['id_profile_student']);
$sql2 = "SELECT * FROM profile_student_jobdesc WHERE id_profile_student_jobdesc = '$id_profile_student'";
$exec2 = $_SERVER['KONEKSI_DB']->query($sql2);

if ($exec2->num_rows != 1) {
	make_swal2('error', 'Not Allowed', 'Student Competence data is not found ! !');
	redirect_referrer(1);
	exit();
}

$data_profile_student = $exec2->fetch_assoc();

$sql4 = "SELECT profile_student_detail_comp.*, profile_detail_comp.* FROM profile_student_detail_comp, profile_detail_comp WHERE profile_detail_comp.id_detail_profile = profile_student_detail_comp.id_detail_profile AND profile_student_detail_comp.id_profile_student_jobdesc = '$id_profile_student' ";
$exec4 = $_SERVER['KONEKSI_DB']->query($sql4);


// Get Data Profile Detail Competence
$sql3 = "SELECT * FROM profile_jobdesc WHERE id_profile_jobdesc = '" . $data_profile_student['id_profile_jobdesc'] . "'";
$exec3 = $_SERVER['KONEKSI_DB']->query($sql3);
$data_profile_jobdesc = $exec3->fetch_assoc();






?>

<body>
	<div class="wrapper">

		<?php include "./view/dosen/template/main-header.php"; ?>
		<?php include "./view/dosen/template/sidebar.php"; ?>


		<div class="main-panel">

			<div class="container">
				<div class="page-inner">

					<div class="page-header">
						<h4 class="page-title">Detail Competency</h4>

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
								<a href="index.php?page=intern_student_management">Intern Students Management</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="index.php?page=intern_student_detail&nim=<?= base64_encode($nim) ?>">Student Detail</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="index.php?page=intern_student_internship_detail&id_internship=<?= base64_encode($id_internship) ?>">Internship Detail</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="index.php?page=intern_student_competency&nim=<?= base64_encode($nim) ?>&id_internship=<?= base64_encode($id_internship) ?>">Student Competency</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="#">Competency Detail</a>
							</li>



						</ul>
					</div>


					<!-- Form -->
					<div class="row">

						<!-- Jobdesc -->
						<div class="col-md-12">
							<div class="card full-height">
								<div class="card-header">
									<div class="card-title">Detail Competency
									</div>
								</div>

								<div class="card-body form-row">

									<div class="form-group col-md-12">
										<table class="table table-hover table-bordered">
											<tr>
												<td>Profile Competence</td>
												<td><?= $data_profile_jobdesc['nama_profile'] ?></td>
											</tr>

											<tr>
												<td>Program Study</td>
												<td><?= $data_profile_jobdesc['kode_prodi'] ?></td>
											</tr>
										</table>
									</div>


									<div class="form-group col-md-6">
										<label class="">Start Date</label>
										<input type="date" name="start_date" class="form-control" value="<?= $data_profile_student['start_date'] ?>" />
									</div>

									<div class="form-group col-md-6">
										<label class="">End Date</label>
										<input type="date" name="end_date" class="form-control" value="<?= $data_profile_student['end_date'] ?>" />
									</div>


									<div class="form-group col-md-12">
										<div class="table-responsive">
											<table class="table table-bordered">
												<thead class="text-center">
													<tr>
														<td width="5%" rowspan='2'>No.</td>
														<td width="20%">Competence Unit</td>
														<td width="40%">Detail Competence</td>
														<td width="10%">Scoring</td>
													</tr>
												</thead>


												<tbody>
													<?php

													// Average Student
													$sql5 = "SELECT SUM(`nilai`) / COUNT(`nilai`) AS average_score FROM profile_student_detail_comp WHERE id_profile_student_jobdesc = '$id_profile_student'";
													$average_score = $_SERVER['KONEKSI_DB']->query($sql5)->fetch_assoc()['average_score'];


													foreach ($exec4 as $i => $data_job) :

														$rubrik = array(
															1 => 'Moderate',
															2 => 'Good',
															3 => 'Very Well',
															4 => 'Extremely Well'
														);

														$data_job['nilai'] = $data_job['nilai'] . " - " . $rubrik[$data_job['nilai']];


													?>


														<tr>
															<td><?= $i + 1 ?>.</td>
															<td><?= $data_job['unit_kompetensi'] ?></td>
															<td><?= $data_job['deskripsi_unit'] ?></td>
															<td><?= $data_job['nilai'] ?></td>
														</tr>

													<?php
													endforeach;
													?>
												</tbody>
												<tfoot>
													<tr>
														<td class="text-right text-bold" colspan="3">Average Score</td>
														<td><?= number_format($average_score, 2) ?> out of 4</td>
													</tr>
												</tfoot>


											</table>
										</div>
									</div>


									<div class="form-group col-md-12">
										<label>Notes for Scoring:</label>
										<ul>
											<li>1 = Moderate</li>
											<li>2 = Good</li>
											<li>3 = Very Well</li>
											<li>4 = Extremely Well</li>
										</ul>
									</div>


								</div>



							</div>
						</div>


					</div>


				</div>



			</div>




			<?php include "./view/dosen/template/footer.php"; ?>

		</div>

	</div>


	<?php include "./view/dosen/template/script.php"; ?>

</body>

</html>