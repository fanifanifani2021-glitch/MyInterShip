<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting( false ); session_start();

$title = 'Add Industry Feedback'; $route = 'accepted_intern_management';
include "./view/industri/template/head.php";
$id = base64_decode($_GET['id']);
$nim = base64_decode($_GET['nim']);
$name = base64_decode($_GET['name']);

$sql = mysqli_query($koneksi_db, "SELECT * FROM industry_feedback WHERE id_internship = $id");
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
						<h4 class="page-title">Add Industry Feedback</h4>
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
								<a href="#">Add Industry Feedback</a>
							</li>
						</ul>
					</div>


					<!-- Form -->
					<div class="row" id="feedback_form">

                        <div class="col-md-12">
							<div class="card full-height">
								<div class="card-header">
									<h4 class="card-title">Form Feedback for <?=$name?></h4>
								</div>

                                <form action="index.php?form=industry_feedback" method="POST" id="form_feedback"><?= csrf(); ?>
								<input type="hidden" name="id_feedback" id="id_feedback" value="<?=$row['id_industry_feedback']?>">
								<input type="hidden" name="nim" id="nim" value="<?=$nim?>">
								<input type="hidden" name="id_internship" id="id_internship" value="<?=$id?>">
								<input type="hidden" name="name" id="name" value="<?=$name?>">

								<div class="card-body form-row">     

                                <div class="form-group col-md-6">
                                    <label>Notes For Student <b class="text-danger">*</b></label>
                                        <select class="form-control <?= show_error('catatan_utk_mahasiswa') ?>" name="catatan_utk_mahasiswa" id="catatan_utk_mahasiswa" disabled>
											<option value="<?=$row['catatan_utk_mahasiswa']?>"><?php if($row['catatan_utk_mahasiswa'] == '-'){echo 'Choose . . .';}else{echo $row['catatan_utk_mahasiswa'];} ?></option>
											<option>Yes</option>
											<option>No</option>
										</select>
									<?= form_error('catatan_utk_mahasiswa') ?>			
                                </div>
																
                                <div class="form-group col-md-6">
                                    <label>Notes For Politechnic <b class="text-danger">*</b></label>
                                        <select class="form-control <?= show_error('catatan_utk_poltek') ?>" name="catatan_utk_poltek" id="catatan_utk_poltek" disabled>
											<option value="<?=$row['catatan_utk_poltek']?>"><?php if($row['catatan_utk_poltek'] == '-'){echo 'Choose . . .';}else{echo $row['catatan_utk_poltek'];} ?></option>
											<option>Yes</option>
											<option>No</option>
										</select>
									<?= form_error('catatan_utk_poltek') ?>   
                                </div>

								</div>

								<div class="separator-solid"></div>

								<div class="card-body form-row justify-content-center">

								<div class="form-group col-md-11">
								<div class="table-responsive">
                                    <table class="table table-bordered">
										<thead>
											<tr>
												<th class="text-center bg-info">Criteria</th>
												<th class="text-center bg-info">Mark</th>
											</tr>
										</thead>
                                        <tbody>

                                            <tr>
                                                <th width="30%">Ethics <span class="float-right">:</span></th>
                                                <td width="70%" class="text-center">
													<div class="form-check form-check-inline">
														<div class="custom-control custom-radio col-3">
															<input type="radio" id="etika1" name="etika" value="1" <?php if($row['etika']=='1') echo 'checked'?> class="custom-control-input" disabled>
															<label class="custom-control-label" for="etika1">1</label>
														</div>
														<div class="custom-control custom-radio col-3">
															<input type="radio" id="etika2" name="etika" value="2" <?php if($row['etika']=='2') echo 'checked'?> class="custom-control-input" disabled>
															<label class="custom-control-label" for="etika2">2</label>
														</div>
														<div class="custom-control custom-radio col-3">
															<input type="radio" id="etika3" name="etika" value="3" <?php if($row['etika']=='3') echo 'checked'?> class="custom-control-input" disabled>
															<label class="custom-control-label" for="etika3">3</label>
														</div>
														<div class="custom-control custom-radio col-3">
															<input type="radio" id="etika4" name="etika" value="4" <?php if($row['etika']=='4') echo 'checked'?> class="custom-control-input" disabled>
															<label class="custom-control-label" for="etika4">4</label>
														</div>
													</div>
												</td>
                                            </tr>

											<tr>
                                                <th width="30%">Competency <span class="float-right">:</span></th>
                                                <td width="70%" class="text-center">
													<div class="form-check form-check-inline">
														<div class="custom-control custom-radio col-3">
															<input type="radio" id="kompetensi1" name="kompetensi" value="1" <?php if($row['keahlian_kompetensi']=='1') echo 'checked'?> class="custom-control-input" disabled>
															<label class="custom-control-label" for="kompetensi1">1</label>
														</div>
														<div class="custom-control custom-radio col-3">
															<input type="radio" id="kompetensi2" name="kompetensi" value="2" <?php if($row['keahlian_kompetensi']=='2') echo 'checked'?> class="custom-control-input" disabled>
															<label class="custom-control-label" for="kompetensi2">2</label>
														</div>
														<div class="custom-control custom-radio col-3">
															<input type="radio" id="kompetensi3" name="kompetensi" value="3" <?php if($row['keahlian_kompetensi']=='3') echo 'checked'?> class="custom-control-input" disabled>
															<label class="custom-control-label" for="kompetensi3">3</label>
														</div>
														<div class="custom-control custom-radio col-3">
															<input type="radio" id="kompetensi4" name="kompetensi" value="4" <?php if($row['keahlian_kompetensi']=='4') echo 'checked'?> class="custom-control-input" disabled>
															<label class="custom-control-label" for="kompetensi4">4</label>
														</div>
													</div>
												</td>
                                            </tr>

											<tr>
                                                <th width="30%">Language <span class="float-right">:</span></th>
                                                <td width="70%" class="text-center">
													<div class="form-check form-check-inline">
														<div class="custom-control custom-radio col-3">
															<input type="radio" id="bahasa1" name="bahasa" value="1" <?php if($row['keahlian_bahasa']=='1') echo 'checked'?> class="custom-control-input" disabled>
															<label class="custom-control-label" for="bahasa1">1</label>
														</div>
														<div class="custom-control custom-radio col-3">
															<input type="radio" id="bahasa2" name="bahasa" value="2" <?php if($row['keahlian_bahasa']=='2') echo 'checked'?> class="custom-control-input" disabled>
															<label class="custom-control-label" for="bahasa2">2</label>
														</div>
														<div class="custom-control custom-radio col-3">
															<input type="radio" id="bahasa3" name="bahasa" value="3" <?php if($row['keahlian_bahasa']=='3') echo 'checked'?> class="custom-control-input" disabled>
															<label class="custom-control-label" for="bahasa3">3</label>
														</div>
														<div class="custom-control custom-radio col-3">
															<input type="radio" id="bahasa4" name="bahasa" value="4" <?php if($row['keahlian_bahasa']=='4') echo 'checked'?> class="custom-control-input" disabled>
															<label class="custom-control-label" for="bahasa4">4</label>
														</div>
													</div>
												</td>
                                            </tr>

											<tr>
                                                <th width="30%">IT Usage <span class="float-right">:</span></th>
                                                <td width="70%" class="text-center">
													<div class="form-check form-check-inline">
														<div class="custom-control custom-radio col-3">
															<input type="radio" id="it1" name="it" value="1" <?php if($row['penggunaan_ti']=='1') echo 'checked'?> class="custom-control-input" disabled>
															<label class="custom-control-label" for="it1">1</label>
														</div>
														<div class="custom-control custom-radio col-3">
															<input type="radio" id="it2" name="it" value="2" <?php if($row['penggunaan_ti']=='2') echo 'checked'?> class="custom-control-input" disabled>
															<label class="custom-control-label" for="it2">2</label>
														</div>
														<div class="custom-control custom-radio col-3">
															<input type="radio" id="it3" name="it" value="3" <?php if($row['penggunaan_ti']=='3') echo 'checked'?> class="custom-control-input" disabled>
															<label class="custom-control-label" for="it3">3</label>
														</div>
														<div class="custom-control custom-radio col-3">
															<input type="radio" id="it4" name="it" value="4" <?php if($row['penggunaan_ti']=='4') echo 'checked'?> class="custom-control-input" disabled>
															<label class="custom-control-label" for="it4">4</label>
														</div>
													</div>
												</td>
                                            </tr>

											<tr>
                                                <th width="30%">Communication <span class="float-right">:</span></th>
                                                <td width="70%" class="text-center">
													<div class="form-check form-check-inline">
														<div class="custom-control custom-radio col-3">
															<input type="radio" id="komunikasi1" name="komunikasi" value="1" <?php if($row['komunikasi']=='1') echo 'checked'?> class="custom-control-input" disabled>
															<label class="custom-control-label" for="komunikasi1">1</label>
														</div>
														<div class="custom-control custom-radio col-3">
															<input type="radio" id="komunikasi2" name="komunikasi" value="2" <?php if($row['komunikasi']=='2') echo 'checked'?> class="custom-control-input" disabled>
															<label class="custom-control-label" for="komunikasi2">2</label>
														</div>
														<div class="custom-control custom-radio col-3">
															<input type="radio" id="komunikasi3" name="komunikasi" value="3" <?php if($row['komunikasi']=='3') echo 'checked'?> class="custom-control-input" disabled>
															<label class="custom-control-label" for="komunikasi3">3</label>
														</div>
														<div class="custom-control custom-radio col-3">
															<input type="radio" id="komunikasi4" name="komunikasi" value="4" <?php if($row['komunikasi']=='4') echo 'checked'?> class="custom-control-input" disabled>
															<label class="custom-control-label" for="komunikasi4">4</label>
														</div>
													</div>
												</td>
                                            </tr>

											<tr>
                                                <th width="30%">Team Work <span class="float-right">:</span></th>
                                                <td width="70%" class="text-center">
													<div class="form-check form-check-inline">
														<div class="custom-control custom-radio col-3">
															<input type="radio" id="kerjasama1" name="kerjasama" value="1" <?php if($row['kerjasama']=='1') echo 'checked'?> class="custom-control-input" disabled>
															<label class="custom-control-label" for="kerjasama1">1</label>
														</div>
														<div class="custom-control custom-radio col-3">
															<input type="radio" id="kerjasama2" name="kerjasama" value="2" <?php if($row['kerjasama']=='2') echo 'checked'?> class="custom-control-input" disabled>
															<label class="custom-control-label" for="kerjasama2">2</label>
														</div>
														<div class="custom-control custom-radio col-3">
															<input type="radio" id="kerjasama3" name="kerjasama" value="3" <?php if($row['kerjasama']=='3') echo 'checked'?> class="custom-control-input" disabled>
															<label class="custom-control-label" for="kerjasama3">3</label>
														</div>
														<div class="custom-control custom-radio col-3">
															<input type="radio" id="kerjasama4" name="kerjasama" value="4" <?php if($row['kerjasama']=='4') echo 'checked'?> class="custom-control-input" disabled>
															<label class="custom-control-label" for="kerjasama4">4</label>
														</div>
													</div>
												</td>
                                            </tr>

											<tr>
                                                <th width="30%">Improvement <span class="float-right">:</span></th>
                                                <td width="70%" class="text-center">
													<div class="form-check form-check-inline">
														<div class="custom-control custom-radio col-3">
															<input type="radio" id="pengembangan1" name="pengembangan" value="1" <?php if($row['pengembangan_diri']=='1') echo 'checked'?> class="custom-control-input" disabled>
															<label class="custom-control-label" for="pengembangan1">1</label>
														</div>
														<div class="custom-control custom-radio col-3">
															<input type="radio" id="pengembangan2" name="pengembangan" value="2" <?php if($row['pengembangan_diri']=='2') echo 'checked'?> class="custom-control-input" disabled>
															<label class="custom-control-label" for="pengembangan2">2</label>
														</div>
														<div class="custom-control custom-radio col-3">
															<input type="radio" id="pengembangan3" name="pengembangan" value="3" <?php if($row['pengembangan_diri']=='3') echo 'checked'?> class="custom-control-input" disabled>
															<label class="custom-control-label" for="pengembangan3">3</label>
														</div>
														<div class="custom-control custom-radio col-3">
															<input type="radio" id="pengembangan4" name="pengembangan" value="4" <?php if($row['pengembangan_diri']=='4') echo 'checked'?> class="custom-control-input" disabled>
															<label class="custom-control-label" for="pengembangan4">4</label>
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

                                <div class="form-group col-md-6">
                                    <label>Layak Diterima <b class="text-danger">*</b></label>
                                    <input type="text" class="form-control <?= show_error('layak_diterima') ?>" 
                                    name="layak_diterima" id="layak_diterima" value="<?php if($row['layak_diterima'] == '-'){echo '';}else{echo $row['layak_diterima'];} ?>" 
                                    placeholder="Input 1-100" disabled/>
									<?= form_error('layak_diterima') ?>			
                                </div>
																
                                <div class="form-group col-md-6">
                                    <label>Langsung Diterima <b class="text-danger">*</b></label>
                                    <input type="text" class="form-control <?= show_error('langsung_diterima') ?>" 
                                    name="langsung_diterima" id="langsung_diterima" value="<?php if($row['langsung_diterima'] == '-'){echo '';}else{echo $row['langsung_diterima'];} ?>" 
                                    placeholder="Enter text" disabled/>
									<?= form_error('langsung_diterima') ?>			
                                </div>

								<div class="form-group col-md-6">
                                    <label>Nilai Akhir <b class="text-danger">*</b></label>
                                    <input type="text" class="form-control <?= show_error('nilai_akhir') ?>" 
                                    name="nilai_akhir" id="nilai_akhir" value="<?php if($row['nilai_akhir'] == '-'){echo '';}else{echo $row['nilai_akhir'];} ?>" disabled
                                    placeholder="Input 1-100"/>
									<?= form_error('nilai_akhir') ?>			
                                </div>

								</div>

                                <div class="card-footer">
										<button onclick="disable_form()" type="button" id="cancel_btn" class="btn btn-danger" hidden><i class="fas fa-times mr-1"></i>Cancel</button>
										<button onclick="enable_form()" type="button" id="edit_btn" class="btn btn-warning"><i class="fas fa-edit mr-1"></i>Edit</button>
										<button onclick="submit_form()" type="button" hidden disabled class="btn btn-success" id="save_btn"><i class="fas fa-save mr-1"></i>Save</button>
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
					title:"Are you sure you want to cancel ?",
					body: "Changes you made will not be saved !",
					useTransparency: true,
					onOk : ()=>{ window.location.href = "index.php"; }
				});
				
		}

		function reset_confirmation() {

				SoloAlert.confirm({
					title:"Are you sure you want to reset ?",
					body: "Changes you made will be reset to default !",
					useTransparency: true,
					onOk : ()=>{ form_reset(); }
				});
				
		}


		function form_reset () {

			$("#form_register").trigger("reset");
		};


	function enable_form () {
		$("#form_feedback :input").prop("disabled", false);
		$("#edit_btn").prop("disabled", true);
		$("#edit_btn").prop("hidden", true);
		$("#cancel_btn").prop("hidden", false);
		$("#cancel_btn").prop("disabled", false);
		$("#save_btn").prop("disabled", false);
		$("#save_btn").prop("hidden", false);
		
		$('html, body').animate({
        scrollTop: $("#feedback_form").offset().top
    	}, 500);
	}

	function disable_form () {
		$("#form_feedback :input").prop("disabled", true);
		$("#edit_btn").prop("disabled", false);
		$("#edit_btn").prop("hidden", false);
		$("#cancel_btn").prop("hidden", true);
		$("#cancel_btn").prop("disabled", false);
		$("#save_btn").prop("disabled", true);
		$("#save_btn").prop("hidden", true);

		$('html, body').animate({
        scrollTop: $("#feedback_form").offset().top
    	}, 500);
	}

		function submit_form() {
			console.log('helo there');
			$('form#form_feedback').submit();
		}



	</script>

</body>
</html>