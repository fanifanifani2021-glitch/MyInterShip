<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php 
error_reporting( false ); session_start();

$title = "Add Attendance Internship"; $route = 'my_internship'; 
include "./view/mahasiswa/template/head.php";

?>

<?php

$id_internship = base64_decode($_GET['id_internship']);


// Pengecekan Internship
$nim = $_SESSION['nim'];

$sql = "SELECT * FROM student_internship WHERE nim = '$nim'";
$exec = $_SERVER['KONEKSI_DB']->query($sql);

if ( $exec->num_rows != 1 ) {
	make_swal2('warning', 'Not Allowed', 'You don`t have access to this page !');
	redirect_back(1); exit;
}


?>

<body>
	<div class="wrapper">

        <?php include "./view/mahasiswa/template/main-header.php";?>
        <?php include "./view/mahasiswa/template/sidebar.php";?>
		
		<div class="main-panel">
			<div class="container">
				<div class="page-inner">
					<div class="page-header">
						<h4 class="page-title">Attendance Internship</h4>
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
								<a href="index.php?page=attendance_internship&id_internship=<?= base64_encode( $id_internship ) ?>">Attendance Internship</a>
							</li>
							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="#">Add Attendance Internship</a>
							</li>
						</ul>
					</div>



                    <div class="row">


						<!-- Internship Under Supervisor -->
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<div class="card-title">Add Attendance Internship Data
                                    </div>
								</div>
								<form action="index.php?form=attendance&action=add" id="form" method="POST" enctype="multipart/form-data" id="form_register">
									<?= csrf(); ?> <?= input_hidden('id_internship', $id_internship )?> <?= input_hidden('nim', $_SESSION['nim'] )?>
								
								<!-- Student's Data -->
								<div class="card-body form-row">


									<div class="form-group col-md-4">
										<label>Week Num <b class="text-danger">*</b></label>

										<select class="form-control <?= show_error('week_num') ?>" name="week_num">
											<option value="" hidden>- Select one -</option>
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
										<input type="date" name="attendance_date" class="form-control <?= show_error('attendance_date'); ?>" />
										<?= form_error('attendance_date'); ?>
									</div>

									<div class="form-group col-md-4 ">
										<label>Attendance Type <b class="text-danger">*</b></label>

										<select class="form-control <?= show_error('attendance_type') ?>" name="attendance_type" id="attendance_type" onchange="cek_absen()">
											<option value="" hidden>- Select one -</option>
											<option>Present</option>
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
										<input type="time" name="check_in" class="form-control <?= show_error('check_in'); ?>" />
										<?= form_error('check_in'); ?>
									</div>

									<div class="form-group col-md-6 jam_absen" hidden>
										<label>Check Out <b class="text-danger">*</b></label>
										<input type="time" name="check_out" class="form-control <?= show_error('check_out'); ?>" />
										<?= form_error('check_out'); ?>
									</div>


									<div class="form-group col-md-12">
										<label class="label-control">Description <b class="text-danger">*</b></label>
										<textarea class="form-control <?= show_error('description')?>" id="description" name="description" 
										rows="5" placeholder="Describe how was your day going or reason why you're not present on that day"></textarea>
										<?= form_error('description'); ?>

									</div>







								</div>

                                <div class="card-footer">
                                    <button type="button" class="btn btn-danger" onclick="confirmation()"><i class="fas fa-times mr-1"></i>Cancel</button>
                                    <button type="button" class="btn btn-warning" onclick="reset_confirmation()" ><i class="fas fa-sync mr-1"></i>Reset</button>
                                    <button type="submit" class="btn btn-success" id="submit_btn"><i class="fas fa-save mr-1"></i>Save</button>

                                </div>
                                </form>
							</div>
						</div>



					</div>


				</div>
			</div>
			<?php include './view/mahasiswa/template/footer.php';?>
		</div>
		
	</div>
	<!--   Core JS Files   -->
	<script src="./assets/js/core/jquery.3.2.1.min.js"></script>
	<script src="./assets/js/core/popper.min.js"></script>
	<script src="./assets/js/core/bootstrap.min.js"></script>
	<!-- jQuery UI -->
	<script src="./assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
	<script src="./assets/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js"></script>
	<!-- Bootstrap Toggle -->
	<script src="./assets/js/plugin/bootstrap-toggle/bootstrap-toggle.min.js"></script>
	<!-- jQuery Scrollbar -->
	<script src="./assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
	<!-- Datatables -->
	<script src="./assets/js/plugin/datatables/datatables.min.js"></script>
	<!-- Atlantis JS -->
	<script src="./assets/js/atlantis.min.js"></script>

	<link rel='stylesheet' href='./assets/sweetalert2.css'>
    <script src='./assets/sweetalert2.all.js'></script>

	
    <!-- Soloalert -->
	<script src='./core/component/soloalert.js'></script>


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
			

			// console.log(document.getElementById('cke_description'));
			// .addClass = 'is-invalid border-danger';
			
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
                    onOk : ()=>{ window.location.href = "index.php?page=attendance_internship&id_internship=<?= base64_encode($id_internship)?>"; }
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