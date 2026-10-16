<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php 
error_reporting( false ); session_start();

$title = "Attendance Detail"; $route = 'intern_student_management'; 
include "./view/dosen/template/head.php";

// Pengecekan Login Dosen Pembimbing
include './view/dosen/template/cek_login_dpm.php';

?>

<?php

$nim = base64_decode($_GET['nim']);
$id_internship = base64_decode($_GET['id_internship']);
$id_attendance = base64_decode($_GET['id_attendance']);


$sql = "SELECT * FROM attendance WHERE id_attendance = '$id_attendance'";
$exec = $_SERVER['KONEKSI_DB']->query($sql);

if ( $exec->num_rows != 1 ) {
	make_swal2('warning', 'Not Found', 'Attendance data is not found !');
	redirect_back(1); exit;
}

$attendance_data = $exec->fetch_assoc(); 



?>

<body>
	<div class="wrapper">

        <?php include "./view/dosen/template/main-header.php";?>
        <?php include "./view/dosen/template/sidebar.php";?>
		
		<div class="main-panel">
			<div class="container">
				<div class="page-inner">
					<div class="page-header">
						<h4 class="page-title">Attendance Detail</h4>
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
								<a href="index.php?page=intern_student_management">Intern Students Management</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="index.php?page=intern_student_detail&nim=<?= base64_encode($nim) ?>">Student Detail</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="index.php?page=intern_student_internship_detail&id_internship=<?= base64_encode($id_internship) ?>">Internship Detail</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="index.php?page=intern_student_attendance&nim=<?= base64_encode($nim) ?>&id_internship=<?= base64_encode($id_internship) ?>">Attendance</a>
							</li>
							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="#">Attendance Detail</a>
							</li>
						</ul>
					</div>



                    <div class="row">


						<!-- Internship Under Supervisor -->
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<div class="card-title">Attendance Detail
                                    </div>
								</div>

								<!-- Student's Data -->
								<div class="card-body form-row">


									<div class="form-group col-md-4">
										<label>Week Num <b class="text-danger">*</b></label>

										<select class="form-control <?= show_error('week_num') ?>" name="week_num">
											<!-- <option value="" hidden>- Select one -</option> -->
											<option hidden><?= $attendance_data['week']?></option>
											<option>1</option>
											<option>2</option>
											<option>3</option>
											<option>4</option>
											<option>5</option>
										</select>

										<!-- <div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text" id="basic-addon1">M</span>
											</div>
											<input type="number" min="0" name="week_num" class="form-control <?= show_error('week_num'); ?>" />
										</div> -->
										<?= form_error('week_num'); ?>
									</div>


									<div class="form-group col-md-4 ">
										<label>Date <b class="text-danger">*</b></label>
										<input type="date" name="attendance_date" value="<?= $attendance_data['date'] ?>" class="form-control <?= show_error('attendance_date'); ?>" readonly />
										<?= form_error('attendance_date'); ?>
									</div>

									<div class="form-group col-md-4 ">
										<label>Attendance Type <b class="text-danger">*</b></label>

										<select class="form-control <?= show_error('attendance_type') ?>" name="attendance_type" id="attendance_type" onchange="cek_absen()">
											<!-- <option value="" hidden>- Select one -</option> -->
											<option hidden><?= $attendance_data['attendance_type']?></option>

											<!-- <option>Present</option> -->
											<option>National Holiday</option>
											<option>Abstaint</option>
											<option>Permit</option>
											<option>Sick Leave</option>
										</select>
										<?= form_error('attendance_type'); ?>

									</div>

									<script>
										function cek_absen( ) {

											var value = document.getElementById('attendance_type').value;
											var jam_absen = document.getElementsByClassName( 'jam_absen' );
											

											if ( value == 'Present' ) {
												for( var x = 0; x <= jam_absen.length; x++ ) {
													jam_absen[x].removeAttribute('hidden');
												}
											} else {
												for( var x = 0; x <= jam_absen.length; x++){
													jam_absen[x].setAttribute('hidden', 'hidden');

												}
											}
										}
									</script>



									<div class="form-group col-md-6 jam_absen" hidden>
										<label>Check In <b class="text-danger">*</b></label>
										<input type="time" name="check_in" value="<?= date('H:i', strtotime($attendance_data['check_in'])) ?>" class="form-control <?= show_error('check_in'); ?>" readonly />
										<?= form_error('check_in'); ?>
									</div>

									<div class="form-group col-md-6 jam_absen" hidden>
										<label>Check Out <b class="text-danger">*</b></label>
										<input type="time" name="check_out" value="<?= date('H:i', strtotime($attendance_data['check_out'])) ?>" class="form-control <?= show_error('check_out'); ?>" readonly />
										<?= form_error('check_out'); ?>
									</div>


									<div class="form-group col-md-12">
										<label class="label-control">Description <b class="text-danger">*</b></label>
										<textarea class="form-control <?= show_error('description')?>" id="description" name="description" 
										rows="5" placeholder="Describe how was your day going or reason why you're not present on that day"><?= $attendance_data['description']?></textarea>
										<?= form_error('description'); ?>

									</div>







								</div>


							</div>
						</div>



					</div>


				</div>
			</div>
			<?php include './view/dosen/template/footer.php';?>
		</div>
		
	</div>

	<?php include "./view/dosen/template/script.php"; ?>
	
	<!-- Datatables -->
	<script src="./assets/js/plugin/datatables/datatables.min.js"></script>

	<!-- SWAL2 -->
	<link rel='stylesheet' href='./assets/sweetalert2.css'>
    <script src='./assets/sweetalert2.all.js'></script>

    <!-- Soloalert -->
	<script src='./core/component/soloalert.js'></script>

	<!-- CKEDITOR -->
	<script src="./library/ckeditor/ckeditor.js"></script>


	<script> 

		// CKEDITOR Initialize
		$(function() {



			// CKEDITOR
			var description = CKEDITOR.replace('description', {
				language: 'en',
				extraPlugins: 'editorplaceholder',
				editorplaceholder: 'Describe how was your day going or reason why you`re not present on that day',
				removeButtons: 'PasteFromWord',
			});






		} );

		$(document).ready( function(){


			setTimeout( function() {
			
			
			$('#cke_description').addClass('<?= show_error('description')?>');
			$('#cke_documentation').addClass('<?= show_error('documentation')?>');

			}, 1000);

			cek_absen();

		})

	</script>


    <!-- User Custom Script -->
    <script>

        function confirmation() {
                // alert('Are you sure you want to cancel ? (Your change will not be saved)');
                SoloAlert.confirm({
                    title:"Are you sure you want to cancel ?",
                    body: "Changes you made will not be saved !",
                    useTransparency: true,
                    onOk : ()=>{ window.location.href = "index.php?page=Attendance_internship&id_internship=<?= base64_encode($id_internship)?>"; }
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

            $("#form").trigger("reset");

            $("small").empty();

			$(".form-control").removeClass('is-invalid border-success border-danger is-valid');
			$('#cke_description').removeClass('<?= show_error('description')?>');
			$('#cke_documentation').removeClass('<?= show_error('documentation')?>');

        };



    </script>




</body>
</html>