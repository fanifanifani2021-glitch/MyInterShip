<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = 'Company Detail';
$route = 'company_list';
include "./view/dosen/template/head.php";

$id_company = base64_decode($_GET['id_company']);
$sql = mysqli_query($_SERVER['KONEKSI_DB'], "SELECT * FROM company WHERE id_company = $id_company");
$data = mysqli_fetch_assoc($sql);

$sql2 = mysqli_query($_SERVER['KONEKSI_DB'], "SELECT * FROM user_company WHERE id_company = $id_company ORDER BY id_user_company ASC");

$cekhrd = mysqli_query($_SERVER['KONEKSI_DB'], "SELECT * FROM user_company WHERE id_company = $id_company AND user_type = 'HRD'");
$counthrd = mysqli_num_rows($cekhrd);
$data2 = mysqli_fetch_assoc($cekhrd);

if ( in_array( $data['status'], [1, 'verified', 'Verified'] )) {
	$status = '<button type="button" class="btn btn-sm btn-success">Verified</button>';
} else
if ( in_array($data['status'], [0,'Not Verified', 'not verified'] ) ) {
	$status = '<button type="button" class="btn btn-sm btn-danger">Not Verified</button>';
} else
if ( in_array($data['status'], [2, 'blacklist', 'Blacklist'] ) ) {
	$status = '<button type="button" class="btn btn-sm btn-dark">Blacklist</button>';
}

$status .= '<i class="fas fa-info-circle text-info ml-2" data-toggle="tooltip" data-placement="top" title="1. Verified : Company is verified and registered, can receive student internship from Polibatam; 2. Not Verified : Company is not yet verified and not registered; 3. Blacklist : Company is blacklist from receiving Polibatam`s student internship;"></i>';

// var_dump( $data ); exit;
?>

<body>
	<div class="wrapper">

		<?php include "./view/dosen/template/main-header.php"; ?>
		<?php include "./view/dosen/template/sidebar.php"; ?>

		<div class="main-panel">
			<div class="container">


				<div class="page-inner">

					<div class="page-header">
						<h4 class="page-title">Company Detail</h4>
						<ul class="breadcrumbs">
							<li class="nav-home">
								<a href="index.php" aria-label="Go to Dashboard">
									<i class="flaticon-home"></i>
								</a>
							</li>
							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="index.php?page=company_list" aria-label="Go to Company List">Company List</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="#" aria-label="Current Page - Company Detail">Company Detail</a>
							</li>
						</ul>
					</div>


					<div class="row mt-2">



						<!-- Lecturer's Profile -->
						<div class="col-md-12">
							<div class="card card-profile">
								<div class="card-header" style="background-image: url(viewer.php?src=profile_background); height: 300px; background-size: cover;background-repeat: no-repeat;">

									<div class="profile-picture">
										<div class="avatar avatar-xxl">
											<img src="viewer.php?src=get_company_logo&location=<?= base64_encode( $data['logo'] ) ?>" alt="Company Logo" class="avatar-img rounded-circle">
										</div>
									</div>
								</div>

								<div class="card-body">
									<div class="user-profile text-center">
										<div class="name"><?= $data['name'] ?></div>
										<div class="job">Company engaged in <?= $data['type'] ?? 'NaN Bussiness' ?></div>
									</div>

									<!-- GET DATA LECTURER DAN LECTURER PROFILE -->


									<div class="table-responsive">
										<table class="table table-bordered table-hover" aria-label="Company Identity">
											<tbody>
												<tr>
													<th width="30%">Name <span class="float-right">:</span></th>
													<td width="70%"><span><?= $data['name']; ?></span></td>
												</tr>
												<tr>
													<th width="30%">Type <span class="float-right">:</span></th>
													<td width="70%"><span><?= $data['type']; ?></span></td>
												</tr>
												<tr>
													<th width="30%">Phone <span class="float-right">:</span></th>
													<td width="70%"><span><?= $data['phone']; ?></span></td>
												</tr>
												<tr>
													<th width="30%">Email <span class="float-right">:</span></th>
													<td width="70%"><span><?= $data['email']; ?></span></td>
												</tr>
												<tr>
													<th width="30%">Website <span class="float-right">:</span></th>
													<td width="70%"><span><?= $data['website']; ?></span></td>
												</tr>
												<tr>
													<th width="30%">Facebook <span class="float-right">:</span></th>
													<td width="70%"><span><?= $data['facebook']; ?></span></td>
												</tr>
												<tr>
													<th width="30%">Twitter <span class="float-right">:</span></th>
													<td width="70%"><span><?= $data['twitter']; ?></span></td>
												</tr>
												<tr>
													<th width="30%">Instagram <span class="float-right">:</span></th>
													<td width="70%"><span><?= $data['instagram']; ?></span></td>
												</tr>

												<tr>
													<th width="30%">LinkedIn <span class="float-right">:</span></th>
													<td width="70%"><span><?= $data['linkedin']; ?></span></td>
												</tr>
												<tr>
													<th width="30%">Logo <span class="float-right">:</span></th>
													<td width="70%"><span><?= $data['logo']; ?></span></td>
												</tr>
												<tr>
													<th width="30%">Address <span class="float-right">:</span></th>
													<td width="70%"><span><?= $data['address']; ?></span></td>
												</tr>
												<tr>
													<th width="30%">Province <span class="float-right">:</span></th>
													<td width="70%"><span><?= $data['province']; ?></span></td>
												</tr>
												<tr>
													<th width="30%">City <span class="float-right">:</span></th>
													<td width="70%"><span><?= $data['city']; ?></span></td>
												</tr>

												<tr>
													<th width="30%">Status <span class="float-right">:</span></th>
													<td width="70%"><span><?= $status; ?></span></td>
												</tr>
												<tr>
													<th width="30%">Access Type <span class="float-right">:</span></th>
													<td width="70%"><span><?= $data['access_type'] ?> <i class="ml-2 fas fa-info-circle" data-toggle="tooltip" data-placement="top" title="1 - Can Make Offer; 2 - Cannot Make Offer"></i></span></td>

												</tr>
											</tbody>
										</table>
									</div>


								</div>


							</div>
						</div>






						<!-- Description Detail -->
						<div class="col-md-12">

							<div class="card full-height">
								<div class="card-header">
									<div class="d-flex align-items-center float-left">
										<h4 class="card-title">Company Description</h4>
									</div>
								</div>
								<div class="card-body">
									<?= $data['description']; ?>
								</div>

							</div>
						</div>

						<!-- User Company Data -->
						<div class="col-md-12">

							<div class="card full-height">
								<div class="card-header">
									<div class="d-flex align-items-center float-left">
										<h4 class="card-title">User Company List</h4>
									</div>
								</div>
								<div class="card-body">
									<div class="table-responsive" id="tabel-ini">
										<table aria-label="User Company Data in <?= $data['name'] ?>" id="ekonomi-kepri" class="display dataTable table table-striped table-hover">
											<thead>
												<tr>
													<th>No.</th>
													<th>Fullname</th>
													<th>Phone</th>
													<th>E-Mail</th>
													<th>User Type</th>
												</tr>
											</thead>
											<tbody id="tbody-staff">
												<?php
												$no = 1;

												foreach ($sql2 as $row) :
												?>
													<tr>
														<td><?= $no++; ?>.</td>
														<td><?= $row['user_fullname'] ?></td>
														<td><?= $row['user_phone'] ?></td>
														<td><?= $row['user_email'] ?></td>
														<td><?= $row['user_type'] ?></td>
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

						<!-- List Internship Offer -->
						<div class="col-md-12">

							<div class="card full-height">
								<div class="card-header">
									<div class="d-flex align-items-center float-left">
										<h4 class="card-title">Internship Offer List</h4>
									</div>
								</div>
								<div class="card-body">
									<div class="table-responsive" id="tabel-ini">
										<table aria-label="Offer Available in <?= $data['name'] ?>" id="intern-offer" class="display dataTable table table-striped table-hover">
											<thead>
												<tr>
													<th>No.</th>
													<th>Position</th>
													<th>Offer Type</th>
													<th>Start Date</th>
													<th>End Date</th>
													<th>Status</th>
													<th>Total Applicant</th>

													<th>Action</th>

												</tr>
											</thead>
											<tbody id="tbody-staff">
												<?php

												$sql = "SELECT * FROM offer WHERE id_company = '$id_company'";
												$exec = $_SERVER['KONEKSI_DB']->query($sql);
												$no = 1;

												foreach ($exec as $row) :

													$id_offer = $row['id_offer'];

													if ($row['status'] == 'Open') {
														$status = "<button type='button' class='btn btn-success btn-sm'>Open</button>";
													} else {
														$status = "<button type='button' class='btn btn-danger btn-sm'>Closed</button>";
													}

													$sql = "SELECT * FROM applicant WHERE id_offer = '$id_offer' ";
													$total_applicant = $_SERVER['KONEKSI_DB']->query($sql)->num_rows;

												?>
													<tr>
														<td><?= $no ?>.</td>
														<td><?= $row['position'] ?></td>
														<td><?= $row['offer_type'] ?></td>
														<td><?= $row['start_date'] ?></td>
														<td><?= $row['end_date'] ?></td>
														<td><?= $status ?></td>
														<td><?= $total_applicant ?> Applicant</td>
														<td>
															<div class="btn-group btn-round">
																<button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" 
																	aria-labelledby="Action Button"
																	aria-haspopup="true" aria-expanded="false">
																	Action
																</button>
																<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">

																	<button type="button" aria-label="Offer Detail" class="dropdown-item" data-toggle="modal" data-target="#ModalOfferDetail<?= $no ?>">
																		<i class="fas fa-info-circle mr-2"></i>Offer Detail
																	</button>
																	<!-- <div class="dropdown-divider"></div>
															<button class="dropdown-item" data-toggle="modal" data-target="#ModalTotalApplicant<?= $no ?>"><i class="fas fa-users mr-2"></i>Applicant List</button> -->

																</div>
															</div>
														</td>

													</tr>


													<!-- modal edit -->
													<div class="modal fade" id="ModalOfferDetail<?= $no ?>">
														<div class="modal-dialog modal-lg">
															<div class="modal-content">
																<div class="modal-header">
																	<h5 class="modal-title">
																		<span class="fw-mediumbold">
																			Offer Detail</span>
																	</h5>
																	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
																		<span aria-hidden="true">&times;</span>
																	</button>
																</div>

																<div class="modal-body">

																	<div class="form-group">
																		<label for="offer_type">Offer Type <label class="text-danger">*</label></label>
																		<select class="form-control" name="offer_type" aria-label="Offer Type">
																			<option value="<?= $row['offer_type'] ?>" hidden><?= $row['offer_type'] ?></option>
																		</select>
																		<?= form_error('offer_type'); ?>
																	</div>

																	<div class="form-group">
																		<label for="position">Position <label class="text-danger">*</label></label>
																		<input type="text" placeholder="Fill Position" class="form-control" name="position"
																			aria-label="Position" value="<?= $row['position'] ?>">
																		<?= form_error('position'); ?>
																	</div>

																	<div class="form-group">
																		<label for="description">Description <label class="text-danger">*</label></label>
																		<textarea class="form-control" placeholder="Fill Description" name="description" 
																			aria-label="Description" rows="4"><?= $row['description'] ?></textarea>
																		<?= form_error('description'); ?>
																	</div>

																	<div class="form-group">
																		<label for="requirement">Requirement <label class="text-danger">*</label></label>
																		<textarea type="text" placeholder="Fill Requirement" class="form-control" name="requirement" 
																			aria-label="Requirement" rows="4"><?= $row['requirement'] ?></textarea>
																		<?= form_error('requirement'); ?>
																	</div>

																	<div class="form-group">
																		<label for="employer_type">Employer Type <label class="text-danger">*</label></label>
																		<input type="text" placeholder="Fill Employer type" class="form-control" name="employer_type"
																			aria-label="Employer Type" value="<?= $row['employer_type'] ?>">
																		<?= form_error('employer_type'); ?>
																	</div>

																	<div class="form-group">
																		<label for="city">City <label class="text-danger">*</label></label>
																		<input type="text" placeholder="Fill City" class="form-control" name="city"
																			aria-label="City" value="<?= $row['city'] ?>">
																		<?= form_error('city'); ?>
																	</div>

																	<div class="form-group">
																		<label for="job_type">Job Type <label class="text-danger">*</label></label>
																		<input type="text" placeholder="Fill Job Type" class="form-control" name="job_type"
																			aria-label="Job Type" value="<?= $row['job_type'] ?>">
																		<?= form_error('job_type'); ?>
																	</div>

																	<!-- <div class="form-group">
																<label for="salary_range">Salary Range <label class="text-danger">*</label></label>
																<input type="text" placeholder="Fill Salary Range" class="form-control" name="salary_range" id="salary_range" value="<?= $row['salary_range'] ?>">  
																<?= form_error('salary_range'); ?>  
															</div> -->

																	<div class="form-group">
																		<label for="start_date">Start Date <label class="text-danger">*</label></label>
																		<input type="date" placeholder="Fill Start Date" class="form-control" name="start_date" 
																			aria-label="Start Date" value="<?= $row['start_date'] ?>">
																		<?= form_error('start_date'); ?>
																	</div>

																	<div class="form-group">
																		<label for="end_date">End Date <label class="text-danger">*</label></label>
																		<input type="date" placeholder="Fill End Date" class="form-control" name="end_date" 
																			aria-label="End Date" value="<?= $row['end_date'] ?>">
																		<?= form_error('end_date'); ?>
																	</div>

																	

																</div>

																<div class="modal-footer justify-content-between">
																	<button aria-label="Close Modal" type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Close</button>
																</div>



															</div>
															<!-- /.modal-content -->
														</div>
														<!-- /.modal-dialog -->
													</div>
													<!-- /.tutupmodal -->


												<?php $no++;
												endforeach;
												?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>

						<!-- List Internship Student -->
						<div class="col-md-12">

							<div class="card full-height">
								<div class="card-header">
									<div class="d-flex align-items-center float-left">
										<h4 class="card-title">Internship Student List</h4>
									</div>
								</div>
								<div class="card-body">
									<div class="table-responsive" id="tabel-ini">
										<table id="intern-student" class="display dataTable table table-striped table-hover">
											<thead>
												<tr>
													<th>No.</th>
													<th>NIM</th>
													<th>Name</th>
													<th>Study Program</th>
													<th>Internship Status</th>
													<th>E-Mail</th>
													<th>Whatsapp Number</th>
												</tr>
											</thead>
											<tbody id="tbody-staff">
												<?php

												$sql = "SELECT DISTINCT(`nim`) FROM internship WHERE id_company = '$id_company'";
												$exec = $_SERVER['KONEKSI_DB']->query($sql);
												$no = 1;

												foreach ($exec as $row) :
													$nim = $row['nim'];

													// Student Data
													$sql = "SELECT * FROM student_internship WHERE nim = '$nim'";
													$student_data = $_SERVER['KONEKSI_DB']->query($sql)->fetch_assoc();

													// Student Data
													$sql = "SELECT MAX(id_internship) AS internship_status FROM internship WHERE nim = '$nim'";
													$internship_status = $_SERVER['KONEKSI_DB']->query($sql)->fetch_assoc() ['internship_status'];

													$sql = "SELECT * FROM internship WHERE nim = '$nim' AND id_internship = '$internship_status' ";
													$internship_data = $_SERVER['KONEKSI_DB']->query($sql)->fetch_assoc();

												?>
													<tr>
														<td><?= $no++; ?>.</td>
														<td><?= $row['nim'] ?></td>
														<td><?= $student_data['name'] ?></td>
														<td><?= $student_data['program_study'] ?></td>
														<td><?= $internship_data['status'] ?></td>
														<td><?= $student_data['email'] ?></td>
														<td><?= $student_data['no_whatsapp'] ?></td>

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

			<?php include "./view/dosen/template/footer.php"; ?>


		</div>

	</div>
	<?php include "./view/dosen/template/script.php"; ?>

	<!-- Datatables -->
	<script src="./assets/js/plugin/datatables/datatables.min.js"></script>

	<script>
		$(document).ready(function() {

			$('#ekonomi-kepri').DataTable({
				"pageLength": 10,
			});

			$('#intern-offer').DataTable({
				"pageLength": 10,
			});

			$('#intern-student').DataTable({
				"pageLength": 10,
			});


		});
	</script>

</body>

</html>