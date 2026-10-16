<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = 'Add Competency';
$route = 'intern_list';
include "./view/industri/template/head.php";


$id_internship = base64_decode($_GET['id_internship']);

// Cek Internship
$sql = "SELECT * FROM internship WHERE id_internship = '$id_internship'";
$exec = $_SERVER['KONEKSI_DB']->query($sql);

if ($exec->num_rows != 1) {
	make_swal2('error', 'Not Allowed', 'Internship is not found !');
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



$profile_competence = base64_decode($_GET['profile_competence']);
$sql2 = "SELECT * FROM profile_jobdesc WHERE id_profile_jobdesc = '$profile_competence'";
$exec2 = $_SERVER['KONEKSI_DB']->query($sql2);
$data_profile_jobdesc = $exec2->fetch_assoc();

if ($exec2->num_rows != 1) {
	make_swal2('error', 'Not Allowed', 'Profile Competence data is not found ! !');
	redirect_referrer(1);
	exit();
}

$sql3 = "SELECT * FROM profile_detail_comp WHERE id_profile_jobdesc = '$profile_competence'";
$exec3 = $_SERVER['KONEKSI_DB']->query($sql3);



?>

<body>
	<div class="wrapper">

		<?php include "./view/industri/template/main-header.php"; ?>
		<?php include "./view/industri/template/sidebar.php"; ?>


		<div class="main-panel">

			<div class="container">
				<div class="page-inner">

					<div class="page-header">
						<h4 class="page-title">Add Competency</h4>
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
								<a href="index.php?page=intern_list">Intern List</a>
							</li>
							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>

							<li class="nav-item">
								<a href="index.php?page=intern_list_detail&id_internship=<?= base64_encode($id_internship) ?>">Intern Detail</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>

							<li class="nav-item">
								<a href="index.php?page=intern_list_competency&id_internship=<?= base64_encode($id_internship) ?>">Intern Competency</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>

							<li class="nav-item">
								<a href="#">Add Competency</a>
							</li>
						</ul>
					</div>


					<!-- Form -->
					<div class="row">

						<!-- Jobdesc -->
						<div class="col-md-12">
							<div class="card full-height">
								<div class="card-header">
									<div class="card-title">Add Competency
									</div>
								</div>

								<form action="index.php?form=competence_spv&action=add" method="post" enctype="multipart/form" id="form_add"> <?= csrf() ?>

									<input type="hidden" name="id_internship" value="<?= $id_internship ?>" />
									<input type="hidden" name="id_profile_jobdesc" value="<?= $profile_competence ?>" />
									<input type="hidden" name="nim" value="<?= $nim ?>" />

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
											<label class="">Start Date <b class="text-danger">*</b></label>
											<input type="date" name="start_date" class="form-control <?= show_error('start_date') ?>" />

											<?= form_error('start_date') ?>
										</div>

										<div class="form-group col-md-6">
											<label class="">End Date <b class="text-danger">*</b></label>
											<input type="date" name="end_date" class="form-control <?= show_error('end_date') ?>" />

											<?= form_error('end_date') ?>
										</div>

										<!-- <div class="form-group col-md-12">
											<label>Profile Competence <b class="text-danger">*</b></label>
											<select class="form-control <?= show_error('profile_competence') ?>" name="profile_competence">
												<option value="" hidden>- Choose One -</option>

												<?php
												$sql2 = "SELECT * FROM profile_jobdesc WHERE kode_prodi = '$kode_prodi'";
												$exec2 = $_SERVER['KONEKSI_DB']->query($sql2);

												foreach ($exec2 as $i => $data) :
												?>
													<option value="<?= $data['id_profile_jobdesc'] ?>"><?= $data['kode_prodi'] ?> | <?= $data['nama_profile'] ?></option>
												<?php endforeach; ?>

											</select>

											<?= form_error('profile_competence') ?>
										</div> -->


										<div class="form-group col-md-12">
											<div class="table-responsive">
												<table class="table table-bordered">
													<thead class="text-center">
														<tr>
															<td width="5%" rowspan='2'>No.</td>
															<td width="60%" colspan='1'>Competence Unit</td>
															<td width="35%" colspan='4'>Fulfillment and Scoring</td>
														</tr>
														<tr>
															<td width="65%">Competence Unit Description</td>
															<td width="5%">1 (Moderate)</td>
															<td width="5%">2 (Good)</td>
															<td width="8%">3 (Very Well)</td>
															<td width="9%">4 (Extremely Well)</td>
														</tr>

													</thead>

													<tbody>
														<?php

														foreach ($exec3 as $ii => $rrow) {

															$id_detail_profile = $rrow['id_detail_profile'];

														?>


															<tr>
																<td width="5%" rowspan='2'><?= $ii + 1 ?>.</td>
																<td width="60%" colspan='1'><?= $rrow['unit_kompetensi'] ?></td>
																<td rowspan='2'>
																	<div class="form-check">
																		<input type="hidden" name="nilai[<?= $id_detail_profile ?>]" value="">
																		<input class="form-check-input" type="radio" name="nilai[<?= $id_detail_profile ?>]" id="flexRadioDefault2" value="1">
																		<label class="form-check-label" for="flexRadioDefault2">
																			1
																		</label>
																	</div>
																</td>
																<td rowspan='2'>
																	<div class="form-check">
																		<input class="form-check-input" type="radio" name="nilai[<?= $id_detail_profile ?>]" id="flexRadioDefault2" value="2">
																		<label class="form-check-label" for="flexRadioDefault2">
																			2
																		</label>
																	</div>
																</td>
																<td rowspan='2'>
																	<div class="form-check">
																		<input class="form-check-input" type="radio" name="nilai[<?= $id_detail_profile ?>]" id="flexRadioDefault2" value="3">
																		<label class="form-check-label" for="flexRadioDefault2">
																			3
																		</label>
																	</div>
																</td>
																<td rowspan='2'>
																	<div class="form-check">
																		<input class="form-check-input" type="radio" name="nilai[<?= $id_detail_profile ?>]" id="flexRadioDefault2" value="4">
																		<label class="form-check-label" for="flexRadioDefault2">
																			4
																		</label>
																	</div>
																</td>
															</tr>
															<tr>
																<td><?= $rrow['deskripsi_unit'] ?></td>
															</tr>

														<?php } ?>
													</tbody>
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

									<div class="card-footer">
										<button type="button" onclick="cancel_confirmation('index.php?page=intern_list_competency&id_internship=<?= base64_encode($id_internship) ?>')" class="btn btn-danger"><i class="fas fa-times mr-1"></i>Cancel</button>
										<button type="button" onclick="reset_confirmation('form_add')" class="btn btn-warning"><i class="fas fa-sync mr-1"></i>Reset</button>
										<button type="submit" class="btn btn-success"><i class="fas fa-save mr-1"></i>Save</button>

									</div>
								</form>


							</div>
						</div>


					</div>


				</div>



			</div>




			<?php include "./view/industri/template/footer.php"; ?>

		</div>

	</div>

	<?php include "./view/industri/template/script.php"; ?>

	<!-- Soloalert -->
	<script src='./core/component/soloalert.js'></script>



	<!-- User Custom Script -->
	<script>
		// Untuk Inisiasi Konfirmasi Cancel
		function cancel_confirmation(location) {
			// alert('Are you sure you want to cancel ? (Your change will not be saved)');
			SoloAlert.confirm({
				title: "Are you sure you want to cancel ?",
				body: "Changes you made will not be saved !",
				useTransparency: true,
				onOk: () => {
					window.location.href = location;
				}
			});

		}


		// Untuk Inisiasi Konfirmasi Reset
		function reset_confirmation(form) {

			SoloAlert.confirm({
				title: "Are you sure you want to reset ?",
				body: "Changes you made will be reset to default !",
				useTransparency: true,
				onOk: () => {
					$("#" + form).trigger("reset");
				}
			});

		}
	</script>

</body>

</html>