<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = 'Register Cooperation Internship';
$route = 'register_cooperation';
include "./view/mahasiswa/template/head.php";

$sql = mysqli_query($koneksi_db, "SELECT * FROM company WHERE access_type IN ('1','2') ORDER BY id_company DESC");
?>

<body>
	<div class="wrapper">

		<?php include "./view/mahasiswa/template/main-header.php"; ?>
		<?php include "./view/mahasiswa/template/sidebar.php"; ?>


		<div class="main-panel">

			<div class="container">
				<div class="page-inner">

					<div class="page-header">
						<h4 class="page-title">Register Cooperation Internship</h4>
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
								<a href="#">Register Cooperation Internship</a>
							</li>
						</ul>
					</div>


					<!-- Form -->
					<div class="row">

						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<h4 class="card-title">Form Register Cooperation Internship</h4>
								</div>

								<form action="index.php?form=register_cooperation&aksi=register" method="POST" enctype="multipart/form-data" id="form_register"><?= csrf(); ?>
								
																		<input type="hidden" name="nim" class="form-control" value="<?= $_SESSION['nim'] ?>" readonly />


									<!-- Company Data -->
									<div class="card-body form-row">

										<input type="hidden" name="id_company" id="id_company" value="<?= old('id_company') ?>">
										<div class="form-group col-md-12">
											<label>Company Name <b class="text-danger">*</b></label>
											<input type="text" class="form-control <?= show_error('id_company') ?>" name="company_name" id="company_name" value="<?= old('company_name'); ?>" placeholder="Enter Company Name" />

											<?= form_error('id_company') ?>

										</div>

										<div class="form-group col-md-12">
											<span class="input-group-btn float-right">
												<button type="button" class="btn btn-success btn-md" data-toggle="modal" data-target="#ModalPT"><i class="fa fa-search"></i> Search Company</button>
											</span>
										</div>

										<div class="form-group col-md-12">
											<label>Company Type <b class="text-danger">*</b></label>
											<input type="text" class="form-control <?= show_error('company_type') ?>" name="company_type" id="company_type" placeholder="Enter Company Type" readonly />

										</div>

										<div class="form-group col-md-12">
											<label>Description <b class="text-danger">*</b></label>

											<textarea class="form-control <?= show_error('company_description') ?>" name="company_description" id="company_description" placeholder="Enter Company Description" rows="3" readonly></textarea>

										</div>


										<div class="form-group col-md-6">
											<label>Province <b class="text-danger">*</b></label>

											<input type="text" class="form-control <?= show_error('company_province') ?>" name="company_province" id="company_province" placeholder="Enter Company Province Location" readonly />

										</div>

										<div class="form-group col-md-6">
											<label>City <b class="text-danger">*</b></label>

											<input type="text" class="form-control <?= show_error('company_city') ?>" name="company_city" id="company_city" placeholder="Enter Company City Location" readonly />

										</div>


										<div class="form-group col-md-12">
											<label>Address <b class="text-danger">*</b></label>

											<textarea class="form-control <?= show_error('company_address') ?>" name="company_address" id="company_address" placeholder="Enter Company Address" rows="3" readonly></textarea>

											<?= form_error('company_address'); ?>
										</div>

										<div class="form-group col-md-6">
											<label>Phone Number <b class="text-danger">*</b></label>
											<input type="tel" class="form-control <?= show_error('company_phone') ?>" name="company_phone" id="company_phone" value="<?= old('company_phone'); ?>" placeholder="Enter Company Phone Number" readonly />
											<?= form_error('company_phone') ?>
										</div>

										<div class="form-group col-md-6">
											<label>E-Mail <b class="text-danger">*</b></label>
											<input type="email" class="form-control <?= show_error('company_email') ?>" name="company_email" id="company_email" value="<?= old('company_email'); ?>" placeholder="Enter Company E-Mail" readonly />
											<?= form_error('company_email') ?>
										</div>


										<div class="form-group col-md-6">
											<label>Website <b class="text-danger">*</b></label>
											<input type="text" class="form-control <?= show_error('company_website') ?>" name="company_website" id="company_website" value="<?= old('company_website'); ?>" placeholder="Enter Company Website" readonly />
											<?= form_error('company_website') ?>
										</div>

									</div>

									<div class="separator-solid"></div>

									<!-- Student's Data -->
									<div class="card-body form-row">

										<!-- <div class="form-group col-md-12">-->
										<!--	<label>Student's NIM <b class="text-danger">*</b></label>-->
										<!--	<input type="text" name="nim" class="form-control" value="<?= $_SESSION['nim'] ?>" readonly />-->

										<!--</div>-->


										<div class="form-group col-md-6">
											<label>Expected Start Date <b class="text-danger">*</b></label>
											<input type="date" name="expected_start" class="form-control <?= show_error('expected_start'); ?>" />
											<?= form_error('expected_start'); ?>
										</div>

										<div class="form-group col-md-6">
											<label>Expected End Date <b class="text-danger">*</b></label>
											<input type="date" class="form-control <?= show_error('expected_end'); ?>" name="expected_end" />
											<?= form_error('expected_end'); ?>
										</div>


										<div class="form-group col-md-12">
											<label class="">Curriculum Vitae</label>
											<!-- <small class="float-right"><b class="text-danger">*</b> file must be .pdf, max size is 2MB</small> -->
											<small class="float-right"><b class="text-danger">*</b> Your CV is in Talent Hub</small>

											<!-- <input type="file" class="form-control <?= show_error('attachment_cv'); ?>" name="attachment_cv" /> -->
											<div class="input-group">
												<div class="input-group-append">
													<span class="input-group-text">talent-hub.id/</span>
												</div>
												<input type="text" class="form-control" name="" value="index.php?page=mhs_profile&nim=<?= base64_encode($_SESSION['nim']) ?>" disabled />
												<input type="hidden" name="attachment_cv" value="https://talent-hub.id/index.php?page=mhsprofile&nim=<?= base64_encode($_SESSION['nim']) ?>" />


												<div class="input-group-prepend">
													<a target="_blank" href='https://talent-hub.id/index.php?page=mhsprofile&nim=<?= base64_encode($_SESSION['nim']) ?>' class="input-group-text"><i class="fas fa-globe mr-2"></i>Visit</a>
												</div>
											</div>
											<?= form_error('attachment_cv'); ?>


										</div>


										<div class="form-group col-md-12">
											<label>Transcript of Grades </label>
											<small class="float-right"><b class="text-danger">*</b> not required, file must be .pdf, max size is 500KB</small>

											<input type="file" class="form-control <?= show_error('attachment_transkrip'); ?>" name="attachment_transkrip" />
											<?= form_error('attachment_transkrip'); ?>

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

			<!-- modal edit -->
			<div class="modal fade" id="ModalPT">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">
								<span class="fw-mediumbold">
									Company Open for Internship</span>
							</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">

							<div class="table-responsive">
								<table id="ekonomi-kepri" class="table table-hover">
									<thead>
										<tr>
											<th>No.</th>
											<th>Name</th>
											<th>Type</th>
											<th>City</th>
											<th>E-Mail</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody id="tbody-staff">
										<?php
										$no = 1;

										foreach ($sql as $row) :
										?>
											<tr>
												<td><?= $no++ ?>.</td>
												<td><?= $row['name'] ?></td>
												<td><?= $row['type'] ?></td>
												<td><?= $row['city'] ?></td>
												<td><?= $row['email'] ?></td>
												<td>
													<div class="form-button-action">
														<button type="button" class="btn btn-primary btn-sm" id="select" data-id_company="<?= $row['id_company'] ?>" data-name="<?= $row['name'] ?>" data-type="<?= $row['type'] ?>" data-phone="<?= $row['phone'] ?>" data-email="<?= $row['email'] ?>" data-website="<?= $row['website'] ?>" data-address="<?= $row['address'] ?>" data-province="<?= $row['province'] ?>" data-city="<?= $row['city'] ?>" data-description="<?= $row['description'] ?>"><i class="fas fa-mouse-pointer mr-1"></i>Choose
														</button>
													</div>
												</td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>



						</div>


						<div class="modal-footer justify-content-between">
							<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-1"></i>Close</button>
						</div>
					</div>
					<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.tutupmodal -->

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

			$('#ekonomi-kepri').DataTable({
				"pageLength": 10,
			});

			$(document).on('click', '#select', function() {
				var id_company = $(this).data('id_company');
				var name = $(this).data('name');
				var type = $(this).data('type');
				var phone = $(this).data('phone');
				var email = $(this).data('email');
				var website = $(this).data('website');
				var address = $(this).data('address');
				var province = $(this).data('province');
				var city = $(this).data('city');
				var description = $(this).data('description');
				$('#id_company').val(id_company);
				$('#company_name').val(name);
				$('#company_type').val(type);
				$('#company_phone').val(phone);
				$('#company_email').val(email);
				$('#company_website').val(website);
				$('#company_address').val(address);
				$('#company_province').val(province);
				$('#company_city').val(city);
				$('#company_description').val(description);
				$('#ModalPT').modal('hide');
			});


		});

		function confirmation() {
			// alert('Are you sure you want to cancel ? (Your change will not be saved)');
			SoloAlert.confirm({
				title: "Are you sure you want to cancel ?",
				body: "Changes you made will not be saved !",
				useTransparency: true,
				onOk: () => {
					window.location.href = "index.php";
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