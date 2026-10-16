<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = 'Registration Data';
$route = 'student_intern_management';
include "./view/dosen/template/head.php";


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
								<a href="index.php?page=student_intern_management">Students Management</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="index.php?page=student_intern_detail&nim=<?= base64_encode($internship_data['nim']) ?>">Student Detail</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="index.php?page=student_internship_detail&id_internship=<?= base64_encode($id_internship) ?>">Internship Detail</a>
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

								<!-- Student's Data -->
								<!-- <div class="card-body form-row">

									<div class="form-group col-md-6">
										<label>NIM </label>
										<input type="text" class="form-control" value="<?= $data['nim'] ?>" readonly />

									</div>

									<div class="form-group col-md-6">
											
												<label>Name </label>
												<input type="text" class="form-control" placeholder="Enter Your Full Name" value="<?= $data['name'] ?>" readonly>
												<?= form_error('name') ?>
											
											
										</div>

										<div class="form-group col-md-6">
											
												<label>E-Mail </label>
												<input type="email" class="form-control" placeholder="Enter Your E-Mail" value="<?= $data['email'] ?>" readonly>

											
										</div>


										<div class="form-group col-md-6">
											
												<label>Other E-Mail </label>
												<input type="email" class="form-control" placeholder="Enter Your Other E-Mail" value="<?= $data['other_email'] ?>" readonly>
										</div>


										<div class="form-group col-md-6">
											
												<label>No WhatsApp </label>
												<input type="tel" class="form-control" name="no_whatsapp" placeholder="Whatsapp Number" value="<?= $data['no_whatsapp'] ?>" readonly>
												<?= form_error('no_whatsapp') ?>
											
										</div>

										<div class="form-group col-md-6">
											
												<label>Phone Number </label>
												<input type="text" class="form-control" name="phone" placeholder="Phone" value="<?= $data['phone'] ?>" readonly>
											
									</div>

								</div>

								<div class="separator-solid"></div> -->

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
									<h4 class="card-title">Company Data</h4>
								</div>

								<div class="card-body">
								<a target="_blank" href="<?= base_url('index.php?page=company_list_detail&id_company=' .base64_encode( $data_company['id_company'] ) ) ?>" class="btn btn-block btn-primary"><i class="fas fa-building mr-2"></i>Company Profile</a>
									<!-- <a href="" class="btn btn-block btn-primary"><i class="fas fa-building mr-2"></i>Company Profile</a> -->
								</div>
							</div>

							<!-- <div class="card">
								<div class="card-header">
									<h4 class="card-title">Company Data</h4>
								</div>

								
								<div class="card-body form-row">


									<div class="form-group col-md-12">
										<label>Company Name </label>
										<input type="text" class="form-control" name="company_name" value="<?= $data_company['name']; ?>" placeholder="Enter Company Name" />

									</div>

									<div class="form-group col-md-12">
										<label>Company Type </label>
										<select class="form-control" name="company_type" id="company_type">
											<option><?= $data_company['type']; ?></option>
										</select>
									</div>

									<div class="form-group col-md-6">
										<label>Province </label>

										<input type="text" class="form-control" value="<?= $data_company['province']; ?>" name="company_province" placeholder="Enter Company Province Location" />
									</div>

									<div class="form-group col-md-6">
										<label>City </label>

										<input type="text" class="form-control" value="<?= $data_company['city']; ?>" name="company_city" placeholder="Enter Company City Location" />
									</div>


									<div class="form-group col-md-12">
										<label>Address </label>

										<textarea class="form-control <?= show_error('company_address') ?>" name="company_address" placeholder="Enter Company Address" rows="3"><?= $data_company['address']; ?></textarea>
									</div>


									<div class="form-group col-md-12">
										<label>Description </label>

										<div class="card card-body no-shadow"><?= $data_company['description']; ?>
										</div>
									</div>

								</div>

								<?php if (!in_array($data_company['phone'], [NULL, '-', ''])) : ?>

									<div class="separator-solid"></div>


									<div class="card-body form-row">


										<div class="form-group col-md-6">
											<label>Phone Number </label>

											<div class="input-group">
												<input type="tel" class="form-control <?= show_error('company_phone') ?>" name="company_phone" value="<?= $data_company['phone']; ?>" placeholder="Enter Company Phone Number" />
												<div class="input-group-append">
													<a class="btn btn-outline-secondary" href="tel:<?= $data_company['phone']; ?>" target="_blank"><i class="fas fa-phone mr-1"></i>Call</a>

												</div>
											</div>
										</div>

										<div class="form-group col-md-6">
											<label>E-Mail </label>

											<div class="input-group">
												<input type="email" class="form-control <?= show_error('company_email') ?>" name="company_email" value="<?= $data_company['email']; ?>" placeholder="Enter Company E-Mail" />
												<div class="input-group-append">
													<a class="btn btn-outline-secondary" href="mailto:<?= $data_company['email']; ?>" target="_blank"><i class="fas fa-envelope mr-1"></i>E-Mail</a>

												</div>
											</div>



										</div>


										<?php if (!in_array($data_company['twitter'], [NULL, '-', ''])) : ?>
											<div class="form-group col-md-6">
												<label>Twitter </label>

												<div class="input-group ">
													<input type="text" class="form-control <?= show_error('company_email') ?>" name="company_email" value="<?= $data_company['twitter']; ?>" placeholder="Enter Company E-Mail" />
													<div class="input-group-append">
														<a class="btn btn-outline-secondary" href="https://twitter.com/<?= $data_company['twitter']; ?>" target="_blank"><i class="fab fa-twitter mr-1"></i>Follow</a>

													</div>
												</div>
											</div>
										<?php endif; ?>



										<?php if (!in_array($data_company['instagram'], [NULL, '-', ''])) : ?>
											<div class="form-group col-md-6">
												<label>Instagram </label>
												<div class="input-group">
													<input type="text" class="form-control <?= show_error('company_email') ?>" name="company_email" value="<?= $data_company['instagram']; ?>" placeholder="Enter Company E-Mail" />
													<div class="input-group-append">
														<a class="btn btn-outline-secondary" href="https://instagram.com/<?= $data_company['instagram']; ?>" target="_blank"><i class="fab fa-instagram mr-1"></i>Follow</a>

													</div>
												</div>
											</div>

										<?php endif; ?>


										<?php if (!in_array($data_company['facebook'], [NULL, '-', ''])) : ?>
											<div class="form-group col-md-6">
												<label>Facebook </label>

												<div class="input-group">
													<input type="text" class="form-control <?= show_error('company_email') ?>" name="company_email" value="<?= $data_company['facebook']; ?>" placeholder="Enter Company E-Mail" />
													<div class="input-group-append">
														<a class="btn btn-outline-secondary" href="https://facebook.com/<?= $data_company['facebook']; ?>" target="_blank"><i class="fab fa-facebook mr-1"></i>Follow</a>

													</div>
												</div>


											</div>
										<?php endif; ?>



										<div class="form-group col-md-6">
											<label>LinkedIn </label>

											<div class="input-group">
												<input type="text" class="form-control <?= show_error('company_email') ?>" name="company_email" value="<?= $data_company['facebook']; ?>" placeholder="Enter Company E-Mail" />
												<div class="input-group-append">
													<a class="btn btn-outline-secondary" href="https://linkedin.com/company/<?= $data_company['linkedin']; ?>" target="_blank"><i class="fab fa-linkedin mr-1"></i>Follow</a>

												</div>
											</div>


										</div>


										<?php if (!in_array($data_company['website'], [NULL, '-', ''])) : ?>
											<div class="form-group col-md-12">
												<label>Website </label>


												<div class="input-group mb-3">
													<input type="text" class="form-control <?= show_error('company_website') ?>" name="company_website" value="<?= $data_company['website']; ?>" placeholder="Enter Company Website" />
													<div class="input-group-append">
														<a class="btn btn-outline-secondary" href="https://<?= $data_company['website']; ?>" target="_blank"><i class="fas fa-globe mr-1"></i>Visit</a>

													</div>
												</div>
											</div>
										<?php endif; ?>


									</div>

								<?php endif; ?>

							</div> -->




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

	<!-- Datatables -->
	<script src="./assets/js/plugin/datatables/datatables.min.js"></script>


</body>

</html>