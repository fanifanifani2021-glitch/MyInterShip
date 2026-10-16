<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = 'Student Feedback';
$route = 'my_internship';
include "./view/mahasiswa/template/head.php";


$id_internship = base64_decode($_GET['id_internship']);
$nim = $_SESSION['nim'];

$sql = mysqli_query($koneksi_db, "SELECT * FROM student_feedback WHERE id_internship = '$id_internship' ");
$row = mysqli_fetch_assoc($sql);

if ( mysqli_num_rows($row) == 0 ) {
    // Insert ke Student Feedback
    $sql = mysqli_query($koneksi_db, "INSERT INTO `student_feedback`(`id_student_feedback`, `id_internship`, `nim`, `date`, `posisi`, `ilmu_dari_poltek`, `ilmu_baru`, `data_magang`, `kesan`, `kendala`, `masukan`) VALUES 
        (NULL, '$id_internship', '$nim', CURRENT_TIMESTAMP(), '-', '-', '-', '-','-','-','-') ");
        
    $sql = mysqli_query($koneksi_db, "SELECT * FROM student_feedback WHERE id_internship = '$id_internship' ");
	$row = mysqli_fetch_assoc($sql);
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
						<h4 class="page-title">Student Feedback</h4>
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
								<a href="index.php?page=my_internship">My Internship</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="index.php?page=internship_detail&id_internship=<?= base64_encode($id_internship) ?>">Internship Detail</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="#">Student Feedback</a>
							</li>
						</ul>
					</div>


					<!-- Form -->
					<div class="row" id="feedback_form">

						<div class="col-md-12">
							<div class="card full-height">
								<div class="card-header">
									<h4 class="card-title">Student Feedback

										<button onclick="enable_form()" type="button" class="btn btn-warning btn-sm float-right mr-1 edit_btn"><i class="fas fa-edit mr-1"></i>Edit</button>

										<button onclick="submit_form()" type="button" class="btn btn-success btn-sm float-right mr-1 save_btn" hidden disabled><i class="fas fa-save mr-1"></i>Save</button>
										<button onclick="disable_form()" type="button" class="btn btn-danger btn-sm float-right mr-1 cancel_btn" hidden><i class="fas fa-times mr-1"></i>Cancel</button>
										
										<button onclick="window.location.href='index.php?page=print_student_feedback&id_intrenship=<?= base64_encode( $id_internship ) ?>&id_student_feedback=<?= base64_encode($row['id_student_feedback']) ?>'"  type="button" class="text-white btn btn-info btn-sm float-right mr-1 edit_btn"><i class="fas fa-download mr-2"></i>Download</button>


									</h4>


								</div>

								<form action="index.php?form=student_feedback" method="POST" id="form_feedback"><?= csrf(); ?>
									<input type="hidden" name="id_feedback" id="id_feedback" value="<?= $row['id_student_feedback'] ?>">
									<input type="hidden" name="id_internship" id="id_internship" value="<?= $id_internship ?>">

									<div class="card-body form-row justify-content-center">

										<div class="form-group col-md-12">
											<div class="table-responsive">
												<table class="table table-bordered">
													<thead>
														<tr>
															<th class="text-center bg-info" width="10%" rowspan="2">No.</th>
															<th class="text-center bg-info" rowspan="2">Criteria</th>
															<th class="text-center bg-info" colspan="4">Mark</th>
														</tr>

														<!-- <tr>
												<th class="text-center bg-info">1 - Strongly Disagree</th>
												<th class="text-center bg-info">2 - Disagree</th>
												<th class="text-center bg-info">3 - Agree</th>
												<th class="text-center bg-info">4 - Strongly Agree</th>
											</tr> -->

														<tr>
															<th class="text-center bg-info">4 - Strongly Agree</th>
															<th class="text-center bg-info">3 - Agree</th>

															<th class="text-center bg-info">2 - Disagree</th>
															<th class="text-center bg-info">1 - Strongly Disagree</th>

														</tr>
													</thead>
													<tbody>

														<tr>
															<th>1.</th>
															<td>Position </td>

															<td>
															    
															    																<input type="hidden" name="posisi" value="" />
															    																
																<div class="form-check form-check-inline">
																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="posisi4" name="posisi" value="4" <?php if ($row['posisi'] == '4') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="posisi4">4</label>
																	</div>
																</div>
															</td>

															<td>
																<div class="form-check form-check-inline">
																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="posisi3" name="posisi" value="3" <?php if ($row['posisi'] == '3') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="posisi3">3</label>
																	</div>
																</div>
															</td>

															<td>
																<div class="form-check form-check-inline">

																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="posisi2" name="posisi" value="2" <?php if ($row['posisi'] == '2') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="posisi2">2</label>
																	</div>
																</div>
															</td>



															<td>

																<div class="form-check form-check-inline">
																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="posisi1" name="posisi" value="1" <?php if ($row['posisi'] == '1') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="posisi1">1</label>
																	</div>
																</div>
															</td>




														</tr>


														<tr>
															<th>2.</th>
															<td>Knowledge From Polibatam</td>

															<td>
															    																<input type="hidden" name="ilmu_dari_poltek" value="" />
															    																
																<div class="form-check form-check-inline">
																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="ilmu_dari_poltek4" name="ilmu_dari_poltek" value="4" <?php if ($row['ilmu_dari_poltek'] == '4') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="ilmu_dari_poltek4">4</label>
																	</div>
																</div>
															</td>


															<td>
																<div class="form-check form-check-inline">
																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="ilmu_dari_poltek3" name="ilmu_dari_poltek" value="3" <?php if ($row['ilmu_dari_poltek'] == '3') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="ilmu_dari_poltek3">3</label>
																	</div>
																</div>
															</td>



															<td>
																<div class="form-check form-check-inline">
																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="ilmu_dari_poltek2" name="ilmu_dari_poltek" value="2" <?php if ($row['ilmu_dari_poltek'] == '2') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="ilmu_dari_poltek2">2</label>
																	</div>
																</div>
															</td>


															<td>
																<div class="form-check form-check-inline">
																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="ilmu_dari_poltek1" name="ilmu_dari_poltek" value="1" <?php if ($row['ilmu_dari_poltek'] == '1') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="ilmu_dari_poltek1">1</label>
																	</div>
																</div>
															</td>


														</tr>

														<tr>
															<th>3.</th>
															<td>New Knowledge</td>

															<td>
															    
																<input type="hidden" name="ilmu_baru" value="" />
																<div class="form-check form-check-inline">
																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="ilmu_baru4" name="ilmu_baru" value="4" <?php if ($row['ilmu_baru'] == '4') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="ilmu_baru4">4</label>
																	</div>
																</div>
															</td>


															<td>
																<div class="form-check form-check-inline">
																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="ilmu_baru3" name="ilmu_baru" value="3" <?php if ($row['ilmu_baru'] == '3') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="ilmu_baru3">3</label>
																	</div>
																</div>
															</td>
															<td>
																<div class="form-check form-check-inline">

																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="ilmu_baru2" name="ilmu_baru" value="2" <?php if ($row['ilmu_baru'] == '2') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="ilmu_baru2">2</label>
																	</div>

																</div>
															</td>


															<td>
																<div class="form-check form-check-inline">
																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="ilmu_baru1" name="ilmu_baru" value="1" <?php if ($row['ilmu_baru'] == '1') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="ilmu_baru1">1</label>
																	</div>
																</div>
															</td>




														</tr>



														<tr>
															<th>4.</th>
															<td>Internship Data</td>

															<td>
															    <input type="hidden" name="data_magang" value="" />
																<div class="form-check form-check-inline">
																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="data_magang4" name="data_magang" value="4" <?php if ($row['data_magang'] == '4') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="data_magang4">4</label>
																	</div>
																</div>
															</td>

															
															<td>
																<div class="form-check form-check-inline">
																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="data_magang3" name="data_magang" value="3" <?php if ($row['data_magang'] == '3') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="data_magang3">3</label>
																	</div>
																</div>
															</td>

															


															<td>
																<div class="form-check form-check-inline">
																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="data_magang2" name="data_magang" value="2" <?php if ($row['data_magang'] == '2') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="data_magang2">2</label>
																	</div>
																</div>
															</td>

															<td>
																
																<div class="form-check form-check-inline">
																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="data_magang1" name="data_magang" value="1" <?php if ($row['data_magang'] == '1') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="data_magang1">1</label>
																	</div>
																</div>
															</td>
															

															
														</tr>

													</tbody>
												</table>

												<?= form_error('posisi') ?? form_error('ilmu_dari_poltek') ?? form_error('ilmu_baru') ?? form_error('data_magang') ?>
												
											</div>
										</div>

									</div>

									<div class="separator-solid"></div>

									<div class="card-body form-row">

										<div class="form-group col-md-12">
											<label>Impression <b class="text-danger">*</b></label>
											<textarea class="form-control <?= show_error('kesan') ?>" name="kesan" id="kesan" placeholder="Type Your Impression after working in this company" rows="6" disabled><?php if ($row['kesan'] == "-") {
																																																						echo "";
																																																					} else {
																																																						echo $row['kesan'];
																																																					} ?></textarea>
											<?= form_error('kesan') ?>
										</div>

										<div class="form-group col-md-12">
											<label>Problem <b class="text-danger">*</b></label>
											<textarea class="form-control <?= show_error('kendala') ?>" name="kendala" id="kendala" placeholder="Type all of your problem before or while or after working in this company" rows="6" 
											disabled><?= old('kendala', ($row['kendala'] == '-') ? '' : $row['kendala'] ) ?></textarea>
											<?= form_error('kendala') ?>
										</div>

										<div class="form-group col-md-12">
											<label>Feedback <b class="text-danger">*</b></label>
											<textarea class="form-control <?= show_error('masukan') ?>" name="masukan" id="masukan" placeholder="Type Your Feedback regarding this company" 
											rows="6" disabled><?= old('masukan', ($row['masukan'] == '-') ? '' : $row['masukan'] ) ?></textarea>
											<?= form_error('masukan') ?>

										</div>

									</div>

									<div class="card-footer">
										<button onclick="disable_form()" type="button" id="cancel_btn" class="btn btn-danger cancel_btn" hidden><i class="fas fa-times mr-1"></i>Cancel</button>
										<button onclick="enable_form()" type="button" id="" class="btn btn-warning edit_btn"><i class="fas fa-edit mr-1"></i>Edit</button>
										<button onclick="submit_form()" type="button" hidden disabled class="btn btn-success save_btn" id="save_btn"><i class="fas fa-save mr-1"></i>Save</button>
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


		function enable_form() {
			$("#form_feedback :input").prop("disabled", false);
			$(".edit_btn").prop("disabled", true);
			$(".edit_btn").prop("hidden", true);
			$(".cancel_btn").prop("hidden", false);
			$(".cancel_btn").prop("disabled", false);
			$(".save_btn").prop("disabled", false);
			$(".save_btn").prop("hidden", false);

			$('html, body').animate({
				scrollTop: $("#feedback_form").offset().top
			}, 500);
		}

		function disable_form() {
			$("#form_feedback :input").prop("disabled", true);
			$(".edit_btn").prop("disabled", false);
			$(".edit_btn").prop("hidden", false);
			$(".cancel_btn").prop("hidden", true);
			$(".cancel_btn").prop("disabled", false);
			$(".save_btn").prop("disabled", true);
			$(".save_btn").prop("hidden", true);

			$('html, body').animate({
				scrollTop: $("#feedback_form").offset().top
			}, 500);
		}

		function submit_form() {
			console.log('helo there');
			$('form#form_feedback').submit();
		}
	</script>

	<script type="text/javascript">
		$(document).ready(function() {
			<?php if (old('open-form') !== NULL) echo 'enable_form()'; ?>
		});
	</script>

</body>

</html>