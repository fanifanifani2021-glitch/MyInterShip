<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting( false ); session_start();

$title = 'Intern Jobdesk'; $route = 'intern_list';
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
$_GET['nim'] = $nim;

// Prodi
$sql = "SELECT * FROM student_internship WHERE nim = '$nim'";
$mhs_data = $_SERVER['KONEKSI_DB']->query($sql)->fetch_assoc();
$kode_prodi = $mhs_data['program_study'];

// var_dump( $kode_prodi ); die;
$_GET['kode_prodi'] = $kode_prodi;

$sql 	= "SELECT * FROM jobdesc WHERE kode_prodi = '$kode_prodi'";
$exec	= $_SERVER['KONEKSI_DB']->query($sql);

if ( $exec->num_rows != 1 ) {
	make_swal2('error', 'Not Allowed', 'Jobdesc Master data not been prepared yet !');
	redirect_referrer(1); exit();
}

$num = $exec->num_rows;
$row = $exec->fetch_assoc(); $id_job = $row['id_jobdesc'];

$sql 	= "SELECT * FROM jobdesc_intern WHERE id_internship = '$id_internship'";
$exec2	= $_SERVER['KONEKSI_DB']->query($sql);

$num = $exec2->num_rows;

if ($num == 1){

	$data_job_intern = mysqli_fetch_assoc($exec2);

	$num_jobdesc = $exec2->num_rows;

	$_GET['id_jobdesc_intern'] = $data_job_intern['id_jobdesc_intern'];
	$id_jobdesc_intern = $data_job_intern['id_jobdesc_intern'];
	$id_jobdesc = $data_job_intern['id_jobdesc'];

    $act = "index.php?form=jobdesk_spv&aksi=edit";
} else {
    $act = "index.php?form=jobdesk_spv&aksi=add";
}


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
								<a href="index.php?page=intern_list">Intern List</a>
							</li>
							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							
							<li class="nav-item">
								<a href="index.php?page=intern_list_detail&id_internship=<?= base64_encode( $id_internship ) ?>">Intern Detail</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							
							<li class="nav-item">
								<a href="#">Intern Jobdesk</a>
							</li>
						</ul>
					</div>


					<!-- Form -->
					<div class="row" >

						<!-- Jobdesc -->
                        <div class="col-md-12">
							<div class="card full-height">
								<div class="card-header">
									<h4 class="card-title">Intern Jobdesk</h4>
								</div>

                                <form action="<?= $act; ?>" method="POST" id="form_jobdesc"><?= csrf(); ?>
									<input type="hidden" name="id_jobdesc_intern" id="id_jobdesc_intern" value="<?=$id_jobdesc_intern?>">
									<input type="hidden" name="id_jobdesc" id="id_jobdesc" value="<?=$row['id_jobdesc']?>">
									<input type="hidden" name="nim" id="nim" value="<?=$nim?>">
									<input type="hidden" name="id_internship" id="id_internship" value="<?=$id_internship?>">


								<div class="card-body form-row">          

									<div class="form-group col-md-12">
										<label>Question 1 <b class="text-danger">*</b></label> <br>
										<label><?= $row['pertanyaan_1'] ?></label>
										
										<textarea class="form-control <?= show_error('jawaban_1')?>" name="jawaban_1" id="jawaban_1" 
										placeholder="Fill Answer for Question 1" rows="3"><?= $data_job_intern['jawaban_1']?></textarea>
										
										<?= form_error('jawaban_1')?>
									</div>


									<div class="form-group col-md-12">
										<label>Question 2 <b class="text-danger">*</b></label> <br>
										<label><?= $row['pertanyaan_2'] ?></label>
										
										<textarea class="form-control <?= show_error('jawaban_2')?>" name="jawaban_2" id="jawaban_2" 
										placeholder="Fill Answer for Question 2" rows="3"><?= $data_job_intern['jawaban_2']?></textarea>
										
										<?= form_error('jawaban_2')?>
									</div>


								</div>
								</form>


                                <div class="card-footer">

									<!-- <button id="cancel_btn" onclick="confirmation();" type="button" class="btn btn-danger"><i class="fas fa-times mr-1"></i>Cancel</button> -->
									<!-- <button id="reset_btn" onclick="reset_confirmation()" type="reset" class="btn btn-warning"><i class="fas fa-sync mr-1"></i>Reset</button> -->
                                    <button onclick="submit_form()"  class="btn btn-primary"><i class="fas fa-save mr-2"></i>Save</button>

                                </div>
							</div>
						</div>

							
						<?php if ($num_jobdesc == 1){
							$id_internship = $id_internship;
							$id_jobdesc_intern = $id_jobdesc_intern;
							$prodi = $prodi;
							
				// 			var_dump( $id_internship, $id_job ); die;

							if ( in_array($row['need_monitoring'], ['YES', 'Yes', 'yes'] ) ) {
							    view('industri/intern_list/intern_list_jobdesk/intern_list_jobtype/intern_list_monitoring_jobtype', ['nim' => $nim, 'id_job' => $id_job, 'id_internship' => $id_internship, 'id_jobdesc_intern' => $id_jobdesc_intern , 'prodi' => $prodi  ]);
							} else {
								view('industri/intern_list/intern_list_jobdesk/intern_list_jobtype/intern_list_no_monitoring', ['nim' => $nim,'id_job' => $id_job, 'id_internship' => $id_internship, 'id_jobdesc_intern' => $id_jobdesc_intern , 'prodi' => $prodi  ]);
							}
						} 
						?>
							
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
					onOk : ()=>{ disabled_form(); }
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

		function disabled_form () {

			$("#edit_btn").prop("disabled", false);
			$("#edit_btn").prop("hidden", false);


			$("#cancel_btn").prop("hidden", true);
			$("#cancel_btn").prop("disabled", true);

			$("#reset_btn").prop("hidden", true);
			$("#reset_btn").prop("disabled", true);

			$("#save_btn").prop("disabled", true);

			$("#form_jobdesc :input").prop("disabled", true);
			$("#form_jobdesc").trigger("reset");

			$("#form_jobdesc :input").removeClass('is-invalid is-valid border-danger border-success');



			$("small").empty();
		};


		function enable_form () {


			$("#edit_btn").prop("disabled", true);
			$("#edit_btn").prop("hidden", true);


			$("#cancel_btn").prop("hidden", false);
			$("#cancel_btn").prop("disabled", false);

			$("#reset_btn").prop("hidden", false);
			$("#reset_btn").prop("disabled", false);


			$("#save_btn").prop("disabled", false);

			$("#form_jobdesc :input").prop("disabled", false);

		}


		function confirm_delete( id, week_num ) {
			swal.fire({
				title               : 'Delete Confirmation',
				text                :  'Are you sure you want to delete ' +week_num + ' Job Type ?',
				icon                : 'question',
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Yes, delete it!",
			}).then((result) => {
				/* Read more about isConfirmed, isDenied below */
				if (result.isConfirmed) {


					// AJAX
					$.ajax({
						url: "./ajax/delJobdescCeklis.php",
						type: "POST",
						data: { id_ceklis: id },
						dataType: 'text',
					success: function ( response ) {
						window.location.reload(1);
					},
					error: function ( response ) {
						Swal.fire("Deleting Data Failed !", "Please try again later !", "error");
					}
					});
					
				}
			})

		}



			$(document).ready(function() {

				// CKEDITOR
				var jawaban_1 = CKEDITOR.replace('jawaban_1', {
					language: 'en',
					extraPlugins: 'editorplaceholder',
					editorplaceholder: 'Fill your Answer for Question 1',
					removeButtons: 'PasteFromWord',
				});

				var jawaban_2 = CKEDITOR.replace('jawaban_2', {
					language: 'en',
					extraPlugins: 'editorplaceholder',
					editorplaceholder: 'Fill your Answer for Question 2',
					removeButtons: 'PasteFromWord',
				});

				setTimeout( function() {
					$('#cke_jawaban_1').addClass('<?= show_error('jawaban_1') ?>');
					$('#cke_jawaban_2').addClass('<?= show_error('jawaban_2') ?>');
				}, 500);


				$('#basic-datatables').DataTable({
				});

				$('#table').DataTable({
				});

				



				<?php
				if ( old('FocusAdd') === TRUE ){
					echo '
					var elmnt = document.getElementById("FocusAdd");
					elmnt.scrollIntoView();
					';
				}
				?>

				<?php
				if ( old('FocusTable') === TRUE ){
					echo "var elmnt = document.getElementById('table'); elmnt.scrollIntoView(); ";
				}
				?>

				<?php
				if(old('ModalEdit') === TRUE ){
					echo "$( '#ModalEdit".old('LoopEdit')."' ).modal('show')";
				}
				?>

			});
	</script>

</body>
</html>