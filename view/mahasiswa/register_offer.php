<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = 'Register Offer Internship';
$route = 'register_offer';
include "./view/mahasiswa/template/head.php";
$id = base64_decode($_GET['id']);

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

$data_mhs = $exec->fetch_assoc();
?>

<body>
	<div class="wrapper">

		<?php include "./view/mahasiswa/template/main-header.php"; ?>
		<?php include "./view/mahasiswa/template/sidebar.php"; ?>


		<div class="main-panel">

			<div class="container">
				<div class="page-inner">

					<div class="page-header">
						<h4 class="page-title">Register Internship Offer</h4>
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
								<a href="#">Register Internship Offer</a>
							</li>
						</ul>
					</div>


					<!-- Form -->
					<div class="row">

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

						<div class="col-md-12">
							<div class="card full-height">
								<div class="card-header">
									<h4 class="card-title">Form Register Offer</h4>
								</div>

								<form action="index.php?form=register_offer&aksi=register_offer" method="POST" enctype="multipart/form-data" id="form_register"><?= csrf(); ?>

									<!-- Student's Data -->
									<!-- <div class="card-body form-row">



										<div class="form-group col-md-6">
											<label>NIM </label>
											<input type="text" class="form-control" value="<?= $data_mhs['nim'] ?>" readonly />

										</div>

										<div class="form-group col-md-6">
											<label>Program Study </label>
											<input type="text" class="form-control" value="<?= $data_mhs['program_study'] ?>" readonly />

										</div>

										<div class="form-group col-md-12">

											<label>Name </label>
											<input type="text" class="form-control" placeholder="Enter Your Full Name" value="<?= $data_mhs['name'] ?>" readonly>
											<?= form_error('name') ?>


										</div>

										<div class="form-group col-md-6">

											<label>E-Mail </label>
											<input type="email" class="form-control" placeholder="Enter Your E-Mail" value="<?= $data_mhs['email'] ?>" readonly>


										</div>


										<div class="form-group col-md-6">

											<label>Other E-Mail </label>
											<input type="email" class="form-control" placeholder="Enter Your Other E-Mail" value="<?= $data_mhs['other_email'] ?>" readonly>
										</div>


										<div class="form-group col-md-6">

											<label>No WhatsApp </label>
											<input type="tel" class="form-control" name="no_whatsapp" placeholder="Whatsapp Number" value="<?= $data_mhs['no_whatsapp'] ?>" readonly>
											<?= form_error('no_whatsapp') ?>

										</div>

										<div class="form-group col-md-6">

											<label>Phone Number </label>
											<input type="text" class="form-control" name="phone" placeholder="Phone" value="<?= $data_mhs['phone'] ?>" readonly>

										</div>

									</div>

									<div class="separator-solid"></div> -->

									<div class="card-body form-row">
										<input type="hidden" name="id_company" value="<?= $data['id_company'] ?>">
										<input type="hidden" name="id_offer" value="<?= $data['id_offer'] ?>">
										<input type="hidden" name="start_date" value="<?= $data['start_date'] ?>">
										<input type="hidden" name="end_date" value="<?= $data['end_date'] ?>">
										<input type="hidden" name="nim" value="<?= $data_mhs['nim'] ?>">


										<div class="form-group col-md-12">
											<label class="">Curriculum Vitae <b class="text-danger">*</b> </label> 
											<!-- <small class="float-right"><b class="text-danger">*</b> file must be .pdf, max size is 2MB</small> -->
											<small class="float-right"><b class="text-danger">*</b> Your CV is in Talent Hub</small>
											
											<!-- <input type="file" class="form-control <?= show_error('attachment_cv'); ?>" name="attachment_cv" /> -->
											<div class="input-group">
												<div class="input-group-append">
													<span class="input-group-text">talent-hub.id/</span>
												</div>
												<input type="text" class="form-control" name="" value="index.php?page=mhs_profile&nim=<?= base64_encode( $data_mhs['nim'] ) ?>" disabled />
												<input type="hidden" name="attachment_cv" value="https://talenthub.polibatam.ac.id/index.php?page=mhsprofile&nim=<?= base64_encode( $data_mhs['nim'] ) ?>" />

												
												<div class="input-group-prepend">
													<a target="_blank" href='https://talenthub.polibatam.ac.id/index.php?page=mhsprofile&nim=<?= base64_encode( $data_mhs['nim'] ) ?>' class="input-group-text"><i class="fas fa-globe mr-2"></i>Visit</a>
												</div>
											</div>
											<?= form_error('attachment_cv'); ?>

											
										</div>

										<div class="form-group col-md-12">
											<label>Transcript of Grades </label>
											<small class="float-right"><b class="text-danger">*</b> not required; If uploaded, file must be .pdf, max size is 500KB</small>

											<input type="file" class="form-control <?= show_error('attachment_transkrip'); ?>" name="attachment_transkrip" />
											<?= form_error('attachment_transkrip'); ?>

										</div>

										<div class="form-group col-md-12">
											Do you want to add another Attachment ?
										</div>


										<div class="form-check">
											<label class="form-check-label">
												<input type="hidden" id="have_another_value" name="have_another" value="0" />

												<input class="form-check-input" type="checkbox" onclick="open_div( )" id="have_another" name="have_another" value="1">
												<span class="form-check-sign">Add another attachment</span>
											</label>
										</div>

										<script type="text/javascript">
											function open_div() {

												var have_another = document.getElementById('have_another');
												var add_optional = document.getElementById('add_optional');

												var have_another_value = document.getElementById('have_another_value');

												if (have_another.checked === true) {
													have_another_value.value = 1;
													add_optional.hidden = false;
												} else {
													have_another_value.value = 0;

													add_optional.hidden = true;
												}

											}
										</script>


										<div class="form-group col-md-12" id="add_optional" hidden>
											<label>Optional Attachment</label>
											<small class="float-right"><b class="text-danger">*</b> file must be .pdf, max size is 500KB</small>

											<input type="file" class="form-control <?= show_error('another_attachment'); ?>" name="another_attachment" accept="application/pdf" />
											<?= form_error('another_attachment'); ?>
										
										</div>



									</div>
									<div class="card-footer">
										<button type="button" class="btn btn-danger" onclick="confirmation()"><i class="fas fa-times mr-1"></i>Cancel</button>
										<button type="button" class="btn btn-warning" onclick="reset_confirmation()"><i class="fas fa-sync mr-1"></i>Reset</button>
										<button type="submit" class="btn btn-success" id="submit_btn"><i class="fab fa-wpforms mr-1"></i>Register</button>

									</div>
								</form>


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
			open_div();
		});

		function confirmation() {
			// alert('Are you sure you want to cancel ? (Your change will not be saved)');
			SoloAlert.confirm({
				title: "Are you sure you want to cancel ?",
				body: "Changes you made will not be saved !",
				useTransparency: true,
				onOk: () => {
					window.location.href = "index.php?page=student_offer";
				}
			});

		}

		function reset_confirmation() {

			SoloAlert.confirm({
				title: "Are you sure you want to reset ?",
				body: "Changes you made will be reset to default !",
				useTransparency: true,
				onOk: () => {
					form_reset();
				}
			});

		}


		function form_reset() {

			$("#form_register").trigger("reset");
		};
	</script>

</body>

</html>