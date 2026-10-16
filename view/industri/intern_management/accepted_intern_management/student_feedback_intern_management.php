<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = 'Student Feedback';
$route = 'accepted_intern_management';
include "./view/industri/template/head.php";


$id_internship = base64_decode($_GET['id_internship']);

$sql = mysqli_query($koneksi_db, "SELECT * FROM student_feedback WHERE id_internship = $id_internship");
$row = mysqli_fetch_assoc($sql);



?>

<body>
	<div class="wrapper">

		<?php include "./view/industri/template/main-header.php"; ?>
		<?php include "./view/industri/template/sidebar.php"; ?>


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
								<a href="index.php?page=accepted_intern_management">Accepted Intern</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="index.php?page=detail_intern_management&id_internship=<?= base64_encode($id_internship) ?>">Student Detail</a>
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
									<h4 class="card-title">Student Feedback</h4>
								</div>

								<form method="POST" id="form_feedback">

									<div class="card-body form-row justify-content-center">

										<div class="form-group col-md-11">
											<div class="table-responsive">
												<table class="table table-bordered">
													<thead>
														<tr>
															<th class="text-center bg-info" width="5%" rowspan="2">No.</th>
															<th class="text-center bg-info" rowspan="2">Criteria</th>
															<th class="text-center bg-info" colspan="4">Mark</th>
														</tr>

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
															<td>Position</td>

															<!-- 4 -->
															<td>
																<div class="form-check form-check-inline">
																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="posisi4" name="posisi" value="4" <?php if ($row['posisi'] == '4') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="posisi4">4</label>
																	</div>
																</div>
															</td>

															<!-- 3 -->
															<td>
																<div class="form-check form-check-inline">
																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="posisi3" name="posisi" value="3" <?php if ($row['posisi'] == '3') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="posisi3">3</label>
																	</div>
																</div>
															</td>

															<!-- 2 -->
															<td>
																<div class="form-check form-check-inline">

																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="posisi2" name="posisi" value="2" <?php if ($row['posisi'] == '2') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="posisi2">2</label>
																	</div>
																</div>
															</td>


															<!-- 1 -->
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

															<!-- 4 -->
															<td>
																<div class="form-check form-check-inline">
																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="ilmu_dari_poltek4" name="ilmu_dari_poltek" value="4" <?php if ($row['ilmu_dari_poltek'] == '4') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="ilmu_dari_poltek4">4</label>
																	</div>
																</div>
															</td>


															<!-- 3 -->
															<td>
																<div class="form-check form-check-inline">
																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="ilmu_dari_poltek3" name="ilmu_dari_poltek" value="3" <?php if ($row['ilmu_dari_poltek'] == '3') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="ilmu_dari_poltek3">3</label>
																	</div>
																</div>
															</td>

															<!-- 2 -->
															<td>
																<div class="form-check form-check-inline">
																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="ilmu_dari_poltek2" name="ilmu_dari_poltek" value="2" <?php if ($row['ilmu_dari_poltek'] == '2') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="ilmu_dari_poltek2">2</label>
																	</div>
																</div>
															</td>

															<!-- 1 -->
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


															<!-- 4 -->
															<td>
																<div class="form-check form-check-inline">
																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="ilmu_baru4" name="ilmu_baru" value="4" <?php if ($row['ilmu_baru'] == '4') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="ilmu_baru4">4</label>
																	</div>
																</div>
															</td>

															<!-- 3 -->
															<td>
																<div class="form-check form-check-inline">
																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="ilmu_baru3" name="ilmu_baru" value="3" <?php if ($row['ilmu_baru'] == '3') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="ilmu_baru3">3</label>
																	</div>
																</div>
															</td>


															<!-- 2 -->
															<td>
																<div class="form-check form-check-inline">

																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="ilmu_baru2" name="ilmu_baru" value="2" <?php if ($row['ilmu_baru'] == '2') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="ilmu_baru2">2</label>
																	</div>

																</div>
															</td>

															<!-- 1 -->
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


															<!-- 4 -->
															<td>
																<div class="form-check form-check-inline">
																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="data_magang4" name="data_magang" value="4" <?php if ($row['data_magang'] == '4') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="data_magang4">4</label>
																	</div>
																</div>
															</td>


															<!-- 3 -->
															<td>
																<div class="form-check form-check-inline">
																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="data_magang3" name="data_magang" value="3" <?php if ($row['data_magang'] == '3') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="data_magang3">3</label>
																	</div>
																</div>
															</td>

															<!-- 2 -->
															<td>
																<div class="form-check form-check-inline">
																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="data_magang2" name="data_magang" value="2" <?php if ($row['data_magang'] == '2') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="data_magang2">2</label>
																	</div>
																</div>
															</td>

															<!-- 1 -->
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
											</div>
										</div>

									</div>

									<div class="separator-solid"></div>

									<div class="card-body form-row">

										<div class="form-group col-md-12">
											<label>Impression <b class="text-danger">*</b></label>
											<textarea class="form-control <?= show_error('kesan') ?>" name="kesan" id="kesan" placeholder="Enter Company Description" rows="3" disabled><?php if ($row['kesan'] == "-") {
																																															echo "";
																																														} else {
																																															echo $row['kesan'];
																																														} ?></textarea>
										</div>

										<div class="form-group col-md-12">
											<label>Problem <b class="text-danger">*</b></label>
											<textarea class="form-control <?= show_error('kendala') ?>" name="kendala" id="kendala" placeholder="Enter Company Description" rows="3" disabled><?php if ($row['kendala'] == "-") {
																																																	echo "";
																																																} else {
																																																	echo $row['kendala'];
																																																} ?></textarea>
										</div>

										<div class="form-group col-md-12">
											<label>Masukan <b class="text-danger">*</b></label>
											<textarea class="form-control <?= show_error('masukan') ?>" name="masukan" id="masukan" placeholder="Enter Company Description" rows="3" disabled><?php if ($row['masukan'] == "-") {
																																																	echo "";
																																																} else {
																																																	echo $row['masukan'];
																																																} ?></textarea>
										</div>

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