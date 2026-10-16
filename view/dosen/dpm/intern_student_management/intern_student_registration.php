<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = 'Registration Data';
$route = 'intern_student_management';
include "./view/dosen/template/head.php";

// Pengecekan Login Dosen Pembimbing
include './view/dosen/template/cek_login_dpm.php';

?>


<?php



$id_internship = base64_decode($_GET['id_internship']);

$sql = "SELECT internship.* FROM internship WHERE internship.id_internship = '$id_internship'";
$exec2 = $_SERVER['KONEKSI_DB']->query($sql);
$internship_data = $exec2->fetch_assoc();

if ($exec2->num_rows != 1) {
	make_swal2('warning', 'Not Allowed', 'You don`t have access to this page !');
	redirect_back(1);
	exit;
}


$nim = $internship_data['nim'];

$sql = "SELECT * FROM student_internship WHERE nim = '$nim'";
$exec = $_SERVER['KONEKSI_DB']->query($sql);
$data = $exec->fetch_assoc();

if ($exec->num_rows != 1) {
	make_swal2('warning', 'Not Allowed', 'You don`t have access to this page !');
	redirect_back(1);
	exit;
}


$id_company = $internship_data['id_company'];

$sql = "SELECT company.* FROM company WHERE company.id_company = $id_company";
$exec = $_SERVER['KONEKSI_DB']->query($sql);
$data_company = $exec->fetch_assoc();


// Cek Applicant
$sql = "SELECT * FROM applicant WHERE id_internship = '$id_internship'";

$exec_applicant = $_SERVER['KONEKSI_DB']->query($sql);

if ($exec_applicant->num_rows == 1) {
	$applicant_data = $exec_applicant->fetch_assoc();

	$id_offer = $applicant_data['id_offer'];
	$sql = "SELECT * FROM offer WHERE id_offer = '$id_offer'";

	$exec_offer = $_SERVER['KONEKSI_DB']->query($sql);
	$data_offer = $exec_offer->fetch_assoc();
}


?>

<body>
	<div class="wrapper">

		<?php include "./view/dosen/template/main-header.php"; ?>
		<?php include "./view/dosen/template/sidebar.php"; ?>


		<div class="main-panel">

			<div class="container">
				<div class="page-inner">

					<div class="page-header">
						<h4 class="page-title">Registration Data</h4>
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
								<a href="index.php?page=intern_student_internship_detail&id_internship=<?= base64_encode($id_internship) ?>">Internship Detail</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="#">Registration Data</a>
							</li>
						</ul>
					</div>


					<!-- Form -->
					<div class="row">

						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<h4 class="card-title">Registration Data</h4>
								</div>

								<div class="card-body form-row">


									<div class="form-group col-md-12">
										<label>Registration Status </label>
										<input type="text" class="form-control" value="<?= $internship_data['status']; ?>" />
									</div>



									<div class="form-group col-md-6">
										<label>Expected Start Date </label>
										<input type="date" class="form-control" value="<?= $internship_data['start_date']; ?>" />
									</div>

									<div class="form-group col-md-6">
										<label>Expected End Date </label>
										<input type="date" class="form-control" value="<?= $internship_data['end_date']; ?>" />
									</div>


									<?php if ( !in_array( $internship_data['file_1'], [NULL, '','-'] ) ) : ?>

									<div class="form-group col-md-6">
										<label>Curriculum Vitae </label>

										<div class="input-group mb-3">
											<input type="text" class="form-control" value="<?= $internship_data['file_1']; ?>" />

											<div class="input-group-prepend">
												<a target="_blank" href='<?= $internship_data['file_1'] ?>' class="input-group-text"><i class="fas fa-globe mr-2"></i>Visit</a>
											</div>
										</div>

									</div>


									<!-- Modal -->
									<div class="modal fade" id="ModalCV" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
										<div class="modal-dialog" role="document">
											<div class="modal-content">
												<div class="modal-header">
													<h5 class="modal-title" id="exampleModalLabel">Attach title</h5>
													<button type="button" class="close" data-dismiss="modal" aria-label="Close">
														<span aria-hidden="true">&times;</span>
													</button>
												</div>
												<div class="modal-body">
													...
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
													<button type="button" class="btn btn-primary">Save changes</button>
												</div>
											</div>
										</div>
									</div>

									<?php endif; ?>


									<?php if ( !in_array( $internship_data['file_2'], [NULL, '','-'] ) ) : ?>

									<div class="form-group col-md-6">
										<label>Transcript of Grades </label>

										<?php if ($internship_data['file_2'] != '-') : ?>
											<div class="input-group mb-3">
												<input type="text" class="form-control" value="<?= $internship_data['file_2']; ?>" />
												<div class="input-group-append">
													<!-- <a class="btn btn-outline-secondary"><i class="fas fa-eye mr-1"></i>Show</a> -->
													<a class="btn btn-outline-secondary" href="./upload/lampiran_internship/<?= $id_internship ?>/<?= $internship_data['file_2'] ?>" target="_blank"><i class="fas fa-eye mr-1"></i>Show</a>

												</div>
											</div>

										<?php else : ?>
											<input type="text" class="form-control" value="<?= $internship_data['file_2']; ?>" />


										<?php endif; ?>

									</div>

									<?php endif; ?>





									<?php if ($internship_data['file_3'] != '') : ?>


										<div class="form-group col-md-12">
											<label>Optional Attachment</label>
											<div class="input-group mb-3">
												<input type="text" class="form-control" value="<?= $internship_data['file_3']; ?>" />
												<div class="input-group-append">
													<!-- <button class="btn btn-outline-secondary"><i class="fas fa-eye mr-1"></i>Show</button> -->
													<a class="btn btn-outline-secondary" href="./upload/lampiran_internship/<?= $id_internship ?>/<?= $internship_data['file_3'] ?>" target="_blank"><i class="fas fa-eye mr-1"></i>Show</a>

												</div>
											</div>
										</div>

									<?php endif; ?>


								</div>

							</div>

							<div class="card">
								<div class="card-header">
									<h4 class="card-title">Company Profile</h4>
								</div>

								<!-- Company Data -->
								<div class="card-body form-row">
									<button class="btn btn-md btn-secondary btn-block mt-1 mr-1" onclick="window.location.href = '<?= base_url('index.php?page=company_list_detail&id_company=' .base64_encode( $id_company ) ) ?>' "><i class="fas fa-building mr-2"></i>Company Profile</button>

								</div>


							</div>




							<?php if ($exec_applicant->num_rows == 1) : ?>

								<div class="card">
									<div class="card-header">
										<h4 class="card-title">Offer Data</h4>
									</div>

									<div class="card-body form-row">


										<div class="form-group col-md-12">
											<label>Position </label>
											<input type="text" class="form-control" name="company_name" value="<?= $data_offer['position']; ?>" placeholder="Enter Company Name" />
										</div>



										<div class="form-group col-md-6">
											<label>Job Type </label>
											<input type="text" class="form-control" name="company_name" value="<?= $data_offer['job_type']; ?>" placeholder="Enter Company Name" />
										</div>

										<div class="form-group col-md-6">
											<label>Offer Type </label>
											<input type="text" class="form-control" name="company_name" value="<?= $data_offer['offer_type']; ?>" placeholder="Enter Company Name" />
										</div>

										<div class="form-group col-md-6">
											<label>Employer Type </label>
											<input type="text" class="form-control" name="company_name" value="<?= $data_offer['employer_type']; ?>" placeholder="Enter Company Name" />
										</div>


										<div class="form-group col-md-6">
											<label>City </label>
											<input type="text" class="form-control" name="company_name" value="<?= $data_offer['city']; ?>" placeholder="Enter Company Name" />
										</div>

										<!-- <div class="form-group col-md-6">
										<label>Salary Range </label>
										<input type="text" class="form-control" 
										name="company_name" value="<?= $data_offer['salary_range']; ?>" 
										placeholder="Enter Company Name"/>
										</div> -->



									</div>

									<div class="separator-solid"></div>

									<div class="card-body form-row">
										<div class="form-group col-md-12">
											<label>Description </label>

											<div class="card card-body no-shadow"><?= $data_offer['description']; ?>
												<!-- <textarea type="text" class="form-control" rows="4"
											placeholder="Enter Company Name"></textarea> -->

											</div>
										</div>


										<div class="form-group col-md-12">
											<label>Requirement </label>

											<div class="card card-body no-shadow"><?= $data_offer['requirement']; ?>

												<!-- <input type="text" class="form-control" 
											name="company_name" value="<?= $data_offer['requirement']; ?>" 
											placeholder="Enter Company Name"/> -->
											</div>
										</div>




									</div>
								</div>
								<?php endif; ?>
							


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