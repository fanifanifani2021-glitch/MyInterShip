<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = "Internship Detail";
$route = 'intern_student_management';
include "./view/dosen/template/head.php";


// Pengecekan Login Dosen Pembimbing
include './view/dosen/template/cek_login_dpm.php';


?>

<?php
$id_internship = base64_decode($_GET['id_internship']);

$sql = "SELECT * FROM internship WHERE id_internship = '$id_internship'";
$exec = $_SERVER['KONEKSI_DB']->query($sql);

if ($exec->num_rows != 1) {
	make_swal2('error', 'Not Allowed', 'Internship Data is not found !');
	redirect_back(1);
	exit;
}

$internship_data = $exec->fetch_assoc();
$nim = $internship_data['nim'];


$sql = "SELECT student_internship.* FROM student_internship WHERE student_internship.nim = '$nim' ";
$student_data = $_SERVER['KONEKSI_DB']->query($sql)->fetch_assoc();


?>

<body>
	<div class="wrapper">

		<?php include "./view/dosen/template/main-header.php"; ?>
		<?php include "./view/dosen/template/sidebar.php"; ?>

		<div class="main-panel">
			<div class="container">
				<div class="page-inner">
					<div class="page-header">
						<h4 class="page-title">Internship Detail</h4>
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
								<a href="index.php?page=intern_student_management">Students Management</a>
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
								<a href="#">Internship Detail</a>
							</li>



						</ul>
					</div>

					<div class="row">


						<!-- Lecturer's Profile -->
						<div class="col-md-12">
							<div class="card">
								<div class="card-body">

									<?php
									$sql = "SELECT internship.* FROM internship WHERE nim = '$nim' AND internship.id_internship = '$id_internship'";
									$exec = $_SERVER['KONEKSI_DB']->query($sql);


								
									foreach ($exec as $row) :

										$registered_date = $row['timestamp_register'];
										$id_internship = $row['id_internship'];
										$id_company = $row['id_company'];

										$sql2 = "SELECT company.name AS company_name FROM company WHERE company.id_company = '$id_company' ";
										$company_name = $_SERVER['KONEKSI_DB']->query($sql2)->fetch_assoc()['company_name'];

										if ( in_array($student_data['nik_dospem'], [NULL,'','-'] ) ) {
											$nik_dospem = 0;
											$dospem = 'Not Set';
										} else {
											$nik_dospem = $student_data['nik_dospem'];

											$sql2 = "SELECT lecturer.name AS lecturer_name FROM lecturer WHERE nim_nik_unit = '$nik_dospem' ";
											$exec2 = $_SERVER['KONEKSI_DB']->query($sql2);
											$dospem = mysqli_fetch_assoc($exec2)['lecturer_name'];
										}


										if ($row['id_user_company'] == '') {
											$user_company = 0;
											$supervisor_industri = '<button class="btn btn-danger btn-sm btn-round">Not Set</button>';

										} else {
											$user_company = $row['id_user_company'];


											$sql2 = "SELECT company.name AS company_name FROM company, user_company WHERE company.id_company = user_company.id_company AND user_company.id_user_company = '$user_company' ";
											$exec2 = $_SERVER['KONEKSI_DB']->query($sql2);

											$sql2 = "SELECT user_company.user_fullname AS supervisor_name FROM user_company WHERE user_company.id_user_company = '$user_company' ";
											$exec2 = $_SERVER['KONEKSI_DB']->query($sql2);
											$supervisor_industri = mysqli_fetch_assoc($exec2)['supervisor_name'];
										}


										if (in_array($row['status'], ['YES', 'Yes', 'yes']) === true) {
											$accepted = 1;
											$status = 'Intern Accepted';
											$internship_period = $row['internship_period'] . " Month";
											$start_date = $row['start_date'];
											$end_date = $row['end_date'];
										} else 

											if (in_array($row['status'], ['FINISH', 'Finish', 'finish']) === true) {
											$accepted = 1;
											$status = 'Intern Accepted and Finished';
											$internship_period = $row['internship_period'] . " Month";
											$start_date = $row['start_date'];
											$end_date = $row['end_date'];
										} else
											if (in_array($row['status'], ['NO', 'No', 'no']) === true) {
											$accepted = 0;
											$status = 'Intern Not Accepted';
										} else 
											if (in_array($row['status'], ['WAITING', 'Waiting', 'waiting']) === true) {
											$accepted = 0;
											$status = 'Waiting for validation';
										} else {
											$accepted = NULL;
											$status = 'Not Registered';
										}


									endforeach;
									?>


									<table class="table table-striped">
										<tr>
											<th>Registered Date</th>
											<td><?= date('D, d M Y', strtotime($row['timestamp_register'])) ?></td>
										</tr>

										<tr>
											<th>Company Name</th>
											<td><a target="_blank" href="<?= base_url('index.php?page=company_list_detail&id_company=' . base64_encode($row['id_company'])) ?>"><?= $company_name ?></a></td>
										</tr>

										<tr>
											<th>Internship Status</th>
											<td><?= $status ?></td>
										</tr>

										<?php if ($accepted == 1) : ?>


											<tr>
												<th>Internship Period</th>
												<td><?= date('D, d M Y', strtotime($start_date)) ?> until <?= date('D, d M Y', strtotime($end_date)) ?> (<?= range_time_elapsed_string($start_date, $end_date) ?>)</td>
											</tr>

											<tr>
												<th>Supervisor</th>
												<td><a href="<?= base_url('index.php?page=intern_student_supervisor&id_internship=' . base64_encode($id_internship)) ?>"><?= $supervisor_industri ?></a></td>
											</tr>

											<tr>
												<th>Lecturer</th>
												<td><a href="#"><?= $dospem ?></a></td>
											</tr>


										<?php endif; ?>
									</table>


									<div class="table-responsive">
										<div class="mt-1 mb-1 d-sm-inline-flex d-md-inline-flex d-lg-inline-flex justify-content-arround">

											<?php if ($accepted !== NULL) : ?>
												<a href="index.php?page=intern_student_registration&nim=<?= base64_encode($nim) ?>&id_internship=<?= base64_encode($id_internship) ?>" class="btn btn-md btn-secondary btn-block mr-1 mt-1"><i class="fab fa-wpforms mr-1"></i>Registration Data</a>
												<a href="<?= base_url('index.php?page=company_list_detail&id_company=' . base64_encode($row['id_company'])) ?>" class="btn btn-md btn-secondary btn-block mr-1 mt-1"><i class="fas fa-building mr-1"></i>Company Profile</a>
											<?php endif; ?>


											<?php if ($accepted != 0) : ?>
												<button class="btn btn-md btn-secondary btn-block mt-1 mr-1" onclick="window.location.href = 'index.php?page=intern_student_supervisor&nim=<?= base64_encode($nim) ?>&id_internship=<?= base64_encode($id_internship) ?>'"><i class="fas fa-user mr-1"></i>Supervisor Profile</button>
												<button class="btn btn-md btn-secondary btn-block mt-1 mr-1" onclick="window.location.href = 'index.php?page=intern_student_jobdesk&nim=<?= base64_encode($nim) ?>&id_internship=<?= base64_encode($id_internship) ?>'"><i class="fas fa-list mr-1"></i>Jobdesk</button>

												<?php if ($student_data['program_study'] == 'LPI') : ?>
													<button class="btn btn-md btn-secondary btn-block mt-1 mr-1" onclick="window.location.href = 'index.php?page=intern_student_competency&nim=<?= base64_encode($nim) ?>&id_internship=<?= base64_encode($id_internship) ?>'"><i class="fas fa-star mr-1"></i>Student Competency</button>
												<?php endif; ?>

											<?php endif; ?>

										</div>
									</div>

									<?php if ($accepted != 0) : ?>


										<div class="table-responsive">

											<div class="mt-1 mb-1 d-sm-inline-flex d-md-inline-flex d-lg-inline-flex justify-content-arround">
												<button class="btn btn-md btn-secondary btn-block mt-1 mr-1" onclick="window.location.href = 'index.php?page=intern_student_discussion&nim=<?= base64_encode($nim) ?>&id_internship=<?= base64_encode($id_internship) ?>'"><i class="fas fa-comments mr-1"></i>Discussion</button>

												<button class="btn btn-md btn-secondary btn-block mt-1 mr-1" onclick="window.location.href = 'index.php?page=intern_student_attendance&nim=<?= base64_encode($nim) ?>&id_internship=<?= base64_encode($id_internship) ?>'"><i class="fas fa-calendar mr-1"></i>Attendance</button>
												<button class="btn btn-md btn-secondary btn-block mt-1 mr-1" onclick="window.location.href = 'index.php?page=intern_student_logbook&nim=<?= base64_encode($nim) ?>&id_internship=<?= base64_encode($id_internship) ?>'"><i class="fas fa-book mr-1"></i>Logbook</button>
												<button class="btn btn-md btn-secondary btn-block mt-1 mr-1" onclick="window.location.href = 'index.php?page=intern_student_final_report&nim=<?= base64_encode($nim) ?>&id_internship=<?= base64_encode($id_internship) ?>'"><i class="fas fa-book mr-1"></i>Final Report</button>
												<button class="btn btn-md btn-secondary btn-block mt-1 mr-1" onclick="window.location.href = 'index.php?page=intern_student_student_feedback&nim=<?= base64_encode($nim) ?>&id_internship=<?= base64_encode($id_internship) ?>'"><i class="fas fa-book mr-1"></i>Student Feedback</abutton>
													<button class="btn btn-md btn-secondary btn-block mt-1 mr-1" onclick="window.location.href = 'index.php?page=intern_student_industry_feedback&nim=<?= base64_encode($nim) ?>&id_internship=<?= base64_encode($id_internship) ?>'"><i class="fas fa-book mr-1"></i>Industry Feedback</abutton>
											</div>


										</div>
									<?php endif; ?>

								</div>
							</div>
						</div>







					</div>



				</div>
			</div>
			<?php include './view/dosen/template/footer.php'; ?>








		</div>

	</div>

	<?php include "./view/dosen/template/script.php"; ?>




</body>

</html>