<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting( false ); session_start();

$title = 'Add Job Type'; $route = 'intern_guidance';
include "./view/industri/template/head.php";


$id_internship = base64_decode($_GET['id_internship']);

// Cek Internship
$sql = "SELECT * FROM internship WHERE id_internship = '$id_internship'";
$exec = $_SERVER['KONEKSI_DB']->query($sql);

if ( $exec->num_rows != 1 ){
	make_swal2('error', 'Not Allowed', 'Internship is not found !');
	redirect_referrer(1); exit();
}

$data_internship = $exec->fetch_assoc();
$nim = $data_internship['nim'];


// Cek Jobdesk, Apakah sudah di isi oleh SPV
$id_jobdesc_intern = base64_decode($_GET['id_jobdesc_intern']);
$sql = "SELECT * FROM jobdesc_intern WHERE id_jobdesc_intern = '$id_jobdesc_intern'";
$exec = $_SERVER['KONEKSI_DB']->query($sql);

// Kalau belum, kembali ke halaman sebelumnya
if ( $exec->num_rows != 1 ){
	make_swal2('error', 'Not Allowed', 'Jobdesk Data is not found !');
	redirect_referrer(1); exit();
}

$data_jobdec_intern = $exec->fetch_assoc();


// Ambil data jobdesc
$id_jobdesc = $data_jobdec_intern['id_jobdesc'];
$sql = "SELECT * FROM jobdesc WHERE id_jobdesc = '$id_jobdesc'";
$data_jobdesc = $_SERVER['KONEKSI_DB']->query($sql)->fetch_assoc();




?>

<body>
	<div class="wrapper">

		<?php include "./view/industri/template/main-header.php"; ?>
		<?php include "./view/industri/template/sidebar.php"; ?>


		<div class="main-panel">

			<div class="container">
				<div class="page-inner">

					<div class="page-header">
						<h4 class="page-title">Intern Jobdesk</h4>
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
								<a href="index.php?page=intern_guidance">Intern Guidance</a>
							</li>
							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							
							<li class="nav-item">
								<a href="index.php?page=intern_guidance_detail&id_internship=<?= base64_encode( $id_internship ) ?>">Intern Detail</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							
							<li class="nav-item">
								<a href="index.php?page=intern_guidance_jobdesk&id_internship=<?= base64_encode($id_internship) ?>">Intern Jobdesk</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							
							<li class="nav-item">
								<a href="#">Add Job Type</a>
							</li>
						</ul>
					</div>


					<!-- Form -->
					<div class="row" >

						<div class="col-md-12">
							<div class="card full-height">
								<div class="card-header">
									<h4 class="card-title">Instruction</h4>
								</div>

								<div class="card-body form-row">   
										<div class="form-group col-md-12">
											Check for job type that student intern do !
										</div>



								<!-- Ketentuan -->
								<?php 
									if ( in_array($data_jobdesc['need_monitoring'], ['YES', 'Yes', 'yes', '1'] ) ) { ?>

										<div class="form-group col-md-12">
											Polibatam Internship Coordinator require you to monitor our intern student through their internship period. For more information please refer to <b>Additional Information</b> section provided by Polibatam Internship Coordinator.
										</div>

										<div class="form-group col-md-12">
											<label>Additional Information</label>
											<textarea class="form-control" rows="5" id="additional_information"><?= $data_jobdesc['additional_information']?></textarea>
										</div>

								<?php
								}
								?>

								</div>

							</div>
						</div>

						<!-- Jobdesc -->
                        <div class="col-md-12">
							<div class="card full-height">
								<div class="card-header">
									<h4 class="card-title">Add Job Type</h4>
								</div>

                                <form action="index.php?form=jobdesk_spv&aksi=add_job_type" method="POST" id="form_jobdesc"><?= csrf(); ?>
									<input type="hidden" name="id_jobdesc_intern" id="id_jobdesc_intern" value="<?=$id_jobdesc_intern?>">
									<input type="hidden" name="nim" id="nim" value="<?=$nim?>">
									<input type="hidden" name="id_internship" id="id_internship" value="<?=$id_internship?>">
									<input type="hidden" name="need_monitoring" id="need_monitoring" value="<?=$data_jobdesc['need_monitoring']?>">


								<div class="card-body form-row">


									<?php if ( in_array($data_jobdesc['need_monitoring'], ['YES', 'Yes', 'yes', '1'] ) ) { ?>

									<div class="form-group col-md-6">
										<label class="control-label">Monitoring Start Date <b class="text-danger">*</b></label>
										<input type="date" class="form-control <?= show_error('start_date') ?>" name="start_date" required />
										<?= form_error('start_date') ?>
									</div> 

									<div class="form-group col-md-6">
										<label class="control-label">Monitoring End Date <b class="text-danger">*</b></label>
										<input type="date" class="form-control <?= show_error('end_date') ?>" name="end_date" required />
										<?= form_error('end_date') ?>
									</div> 

									<?php } ?>


									<div class="table-responsive">
									<table class="table table-bordered">
										<thead>
											<tr>
												<th>No.</th>
												<th>Job Type</th>
												<th>Description</th>
												<th>Checklist</th>
												
											</tr>
										</thead>

										<tbody>
											<?php

											$sql = "SELECT * FROM detail_jobdesc WHERE id_jobdesc = '$id_jobdesc'";

											foreach( $_SERVER['KONEKSI_DB']->query( $sql ) as $i => $data_job ) : ?>
											<tr>
												<td><?= $i+1 ?>.</td>
												<td><?= $data_job['jenis_pekerjaan'] ?></td>
												<td><?= $data_job['job_description'] ?></td>
												<td>
													<div class="form-check">
														<label class="form-check-label">
															<input type="hidden" name="id_detail[<?= $i ?>]" value="<?= $data_job['id_detail'] ?>">
															<input type="hidden" name="ceklis_jobtype[<?= $i ?>]" value="0">
															<input class="form-check-input toggle" onclick="toggle_date()" type="checkbox" name="ceklis_jobtype[<?= $i ?>]" id="ceklis_jobtype_<?= $i ?>" value="1">
															<span class="form-check-sign">in accordance</span>
														</label>
													</div>
												</td>

											</tr>

											<?php $i++; endforeach; ?>
										</tbody>
									</table>
									</div>




								</div>
								</form>


                                <div class="card-footer">

									<button id="cancel_btn" onclick="confirmation();" type="button" class="btn btn-danger"><i class="fas fa-times mr-1"></i>Cancel</button>
									<button id="reset_btn" onclick="reset_confirmation()" type="reset" class="btn btn-warning"><i class="fas fa-sync mr-1"></i>Reset</button>
                                    <button onclick="submit_form()"  class="btn btn-success"><i class="fas fa-save mr-1"></i>Save</button>

                                </div>
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

		function submit_form() {
			console.log('helo there');
			$('form#form_jobdesc').submit();
		}

		function confirmation() {
				// alert('Are you sure you want to cancel ? (Your change will not be saved)');
				SoloAlert.confirm({
					title:"Are you sure you want to cancel ?",
					body: "Changes you made will not be saved !",
					useTransparency: true,
					onOk : ()=>{ 
						window.location.href = 'index.php?page=intern_guidance_jobdesk&id_internship=<?= base64_encode($id_internship) ?>';
					 }
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

			$("#form_jobdesc").trigger("reset");
		};

		function modal_hapus(id){
			var id_hapus = document.getElementById('id_hapus');
			id_hapus.value = id;
		}

		function modal_edit(id, jenis, job){
			$('#id_detail_edit').val(id);
          	$('#jenis_pekerjaan_edit').val(jenis);	
			$('#job_description_edit').text(job);	
			$('#job_description_edit').val(job);	

			  
		}

	</script>

	<script>


			$(document).ready(function() {

				
				// CKEDITOR

				var additional_information = CKEDITOR.replace('additional_information', {
					language: 'en',
					extraPlugins: 'editorplaceholder',
					editorplaceholder: 'Fill your Answer for Question 1',
					removeButtons: 'PasteFromWord',
				});



			});
	</script>

</body>
</html>