<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting( false ); session_start();

$title = 'Validate Candidate Data'; $route = 'rejected_intern_management';
include "./view/industri/template/head.php";
?>

<?php

$id_internship = base64_decode($_GET['id_internship']);


// Cek Internship
$cek = $_SERVER['KONEKSI_DB']->query("SELECT * FROM internship WHERE internship.id_internship = '$id_internship' ");

if ( $cek->num_rows != 1 ) {
	make_soloalert('error', 'Failed to Validate', 'Internship data is not found !');
	redirect_back(1); exit;
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
						<h4 class="page-title">Validate Candidate Data</h4>
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
								<a href="index.php?page=intern_candidate">Intern Candidate</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="#">Validate Candidate Data</a>
							</li>


							
						</ul>
					</div>


					<?php
						$sql = "SELECT internship.* , student_internship.*  FROM internship, student_internship
							WHERE  student_internship.nim = internship.nim AND internship.id_internship = '$id_internship' ";
						$exec = $_SERVER['KONEKSI_DB']->query($sql); 

						foreach($exec as $i => $candidate_data) :

							$id_internship = $candidate_data['id_internship'];

							$sql = "SELECT applicant.* FROM applicant WHERE id_internship = '$id_internship' ";
							$exec2 = $_SERVER['KONEKSI_DB']->query($sql);

							if ( $exec2->num_rows == 1 ) {
								$applicant_data = $exec2->fetch_assoc();

								$registered_via = 'Offer';
								$id_offer = $applicant_data['id_offer'];
								$sql = "SELECT * FROM offer WHERE id_offer = '$id_offer'";
							
								$exec_offer = $_SERVER['KONEKSI_DB']->query($sql);
								$data_offer = $exec_offer->fetch_assoc();

							} else {
								$registered_via = 'Cooperation';
							}
					?>

					<form action="index.php?form=validate_intern" method="POST"> 

						<?= csrf() ?> <?= input_hidden('registered_via' , $registered_via )?>

						<?= input_hidden('id_internship', $id_internship) ?> <?= input_hidden('student_nim', $candidate_data['nim']) ?>
					<div class="row">



						<!-- Internship Under Supervisor -->
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">Candidate Identity</div>
								<div class="card-body form-row">
									<div class="form-group col-md-12 text-center">
									
										<div class="profile-picture">
											<div class="avatar avatar-xxl">
												<img src="./upload/profile_picture/<?= $candidate_data['profile_picture'] ?>" alt="..." class="avatar-img rounded-circle">
											</div>
										</div>
								
									</div>
									<div class="form-group col-md-6">
										<label class="label-control">Student's NIM</label>
										<input class="form-control" value="<?= $candidate_data['nim'] ?>" />
									</div>

									<div class="form-group col-md-6">
										<label class="label-control">Student's Program Study</label>
										<input class="form-control" value="<?= $candidate_data['program_study'] ?>" />
									</div>

									<div class="form-group col-md-12">
										<label class="label-control">Student's Name</label>
										<input class="form-control" value="<?= $candidate_data['name'] ?>" />
									</div>

									<div class="form-group col-md-6">
										<label class="label-control">E-Mail</label>
										<input class="form-control" value="<?= $candidate_data['email'] ?>" />
									</div>

									<div class="form-group col-md-6">
										<label class="label-control">Other E-Mail</label>
										<input class="form-control" value="<?= $candidate_data['other_email'] ?>" />
									</div>

									<div class="form-group col-md-6">
										<label class="label-control">Phone</label>
										<input class="form-control" value="<?= $candidate_data['phone'] ?>" />
									</div>

									<div class="form-group col-md-6">
										<label class="label-control">Whatsapp Number</label>
										<input class="form-control" value="<?= $candidate_data['no_whatsapp'] ?>" />
									</div>


								</div>
							</div>

							<div class="card">
								<div class="card-header">Candidate Registration</div>
								<div class="card-body form-row">

									<div class="form-group col-md-12">
										<label class="label-control">Registered Via</label>

										<input type="text" class="form-control" value="<?= $registered_via ?>" readonly/>

									</div>


									<?php if ( stripos($registered_via, 'Offer') !== false  ) : ?>
									<?= input_hidden('id_offer' , $data_offer['id_offer'] )?>


									<div class="form-group col-md-12">
										<label>Position <b class="text-danger">*</b></label>
										<input type="text" class="form-control" value="<?= $data_offer['position']; ?>" 
										placeholder="Enter Company Name"/>
									</div>

									<div class="form-group col-md-12">
										<label>Description <b class="text-danger">*</b></label>
										<textarea type="text" class="form-control" rows="4"
										placeholder="Enter Company Name"><?= $data_offer['description']; ?></textarea>
									</div>


									<div class="form-group col-md-6">
										<label>Requirement <b class="text-danger">*</b></label>
										<input type="text" class="form-control" value="<?= $data_offer['requirement']; ?>" 
										placeholder="Enter Company Name"/>
									</div>

									<div class="form-group col-md-6">
										<label>Job Type <b class="text-danger">*</b></label>
										<input type="text" class="form-control" value="<?= $data_offer['job_type']; ?>" 
										placeholder="Enter Company Name"/>
									</div>

									<div class="form-group col-md-6">
										<label>Offer Type <b class="text-danger">*</b></label>
										<input type="text" class="form-control" value="<?= $data_offer['offer_type']; ?>" 
										placeholder="Enter Company Name"/>
									</div>

									<div class="form-group col-md-6">
										<label>Employer Type <b class="text-danger">*</b></label>
										<input type="text" class="form-control" value="<?= $data_offer['employer_type']; ?>" 
										placeholder="Enter Company Name"/>
									</div>


									<div class="form-group col-md-6">
										<label>City <b class="text-danger">*</b></label>
										<input type="text" class="form-control" value="<?= $data_offer['city']; ?>" 
										placeholder="Enter Company Name"/>
									</div>

									<div class="form-group col-md-6">
										<label>Salary Range <b class="text-danger">*</b></label>
										<input type="text" class="form-control" value="<?= $data_offer['salary_range']; ?>" 
										placeholder="Enter Company Name"/>
									</div>
									
									<div class="form-group col-md-12 separator-solid"></div>

									<?php endif; ?>

									<div class="form-group col-md-12" >
										<label class="label-control">Excpected Duration</label>

										<div class="input-group mb-3">
											<input class="form-control" value="<?= date( 'n' , ( strtotime($candidate_data['end_date']) - strtotime($candidate_data['start_date']) )) ?>" readonly/>
											<div class="input-group-append">
												<span class="input-group-text" id="basic-addon2">Month</span>
											</div>
										</div>

									</div>

									<div class="form-group col-md-6">
										<label class="label-control">Excpected Start Date</label>

										<input class="form-control" name="expected_start_date" value="<?= date( 'D, d-M-Y' ,strtotime($candidate_data['start_date'])) ?>" readonly/>
										<input hidden name="expected_start_date" value="<?= $candidate_data['start_date'] ?>"/>
									
									</div>

									<div class="form-group col-md-6">
										<label class="label-control">Excpected End Date</label>

										<input class="form-control" value="<?= date( 'D, d-M-Y' ,strtotime($candidate_data['end_date'])) ?>" readonly/>
										<input hidden name="expected_end_date" value="<?= $candidate_data['end_date'] ?>"/>
									
									</div>




									<div class="form-group col-md-6">
										<label class="label-control">CV</label>

										<div class="input-group mb-3">
											<input class="form-control" value="<?= $candidate_data['file_1'] ?>" readonly/>

											<div class="input-group-append">
												<a class="btn btn-outline-secondary" type="button" href="./upload/lampiran_internship/<?= $candidate_data['file_1'] ?>" target="_blank" download ><i class="fas fa-eye"></i></a>
											</div>
										</div>

									</div>

									<div class="form-group col-md-6">
										<label class="label-control">Transcript of Grade</label>
										<div class="input-group mb-3">
											<input class="form-control" value="<?= $candidate_data['file_2'] ?>" readonly/>

											<div class="input-group-append">
												<a class="btn btn-outline-secondary" type="button" href="./upload/lampiran_internship/<?= $candidate_data['file_2'] ?>" target="_blank" download ><i class="fas fa-eye"></i></a>
											</div>
										</div>
									</div>


									<?php if ( $candidate_data['file_3'] != '' ) :  ?>
									<div class="form-group col-md-12">
										<label class="label-control">Additional Attachment</label>
										<div class="input-group mb-3">
											<input class="form-control" value="<?= $candidate_data['file_3'] ?>" readonly/>

											<div class="input-group-append">
												<a class="btn btn-outline-secondary" type="button" href="./upload/lampiran_internship/<?= $candidate_data['file_3'] ?>" target="_blank" download ><i class="fas fa-eye"></i></a>
											</div>
										</div>
									</div>

									<?php endif; ?>


									<div class="form-group col-md-12 separator-solid"></div>


									<div class="form-group col-md-12">
										<label class="label-control">Status Acceptance <b class="text-danger">*</b></label>
										<select class="form-control <?= show_error('status_acceptance') ?>" name="status_acceptance" id="status_acceptance" onchange="show_supervisor()">
											<!-- <option value="" hidden>Choose one . . .</option> -->
											<?php if ( old('status_acceptance') === NULL ) { echo "<option hidden value='" .$candidate_data['status']."' >" .$candidate_data['status']. "</option>"; } ?>
											<option value="Yes">Yes</option>
											<option value="No">No</option>
										</select>

										<?= form_error('status_acceptance') ?>
									</div>

									<script type="text/javascript">

										function show_supervisor( ) {

											var acceptance_yes = document.getElementsByClassName('acceptance-yes');
											var status_acceptance = document.getElementById('status_acceptance');
											var assign_supervisor = document.getElementById('assign_supervisor');
											var assign_period = document.getElementById('assign_period');

											if ( status_acceptance.value == 'Yes') {

												for ( i=0; i <= acceptance_yes.length; i++){
													acceptance_yes[i].hidden = false;
												}
												// assign_supervisor.hidden = false;
											} else {
												for ( i=0; i <= acceptance_yes.length; i++){
													acceptance_yes[i].hidden = true;
												}
												// assign_supervisor.hidden = true;
											}
										}

									</script>


									<div class="form-group col-md-12 acceptance-yes" hidden>
										<label class="label-control">Industry Supervisor <b class="text-danger">*</b></label>
										<select class="form-control <?= show_error('industry_supervisor') ?>" 
											name="industry_supervisor" id="industry_supervisor">
											<option value="" hidden>Choose one . . .</option>
											<?php 
											$id_company = $_SESSION['id_company'];
											$exec = $_SERVER['KONEKSI_DB']->query("SELECT * FROM user_company WHERE id_company = '$id_company' AND user_type NOT IN ('HRD')");

											foreach ( $exec as $row ) :
											?>

											<option value="<?= $row['id_user_company'] ?>"><?= $row['user_fullname'] ?></option>

											<?php endforeach; ?>
										</select>

										<?= form_error('industry_supervisor') ?>

									</div>




									<div class="form-group col-md-6 acceptance-yes">
										<label class="label-control">Start Date <b class="text-danger">*</b></label>

										<input type="date" class="form-control <?= show_error('start_date') ?>" name="start_date" value="<?php if ( old('start_date') !== NULL ) { echo old('start_date'); } else { echo date( 'Y-m-d' ,strtotime($candidate_data['start_date']) ); } ?>"/>
										<?= form_error('start_date') ?>
									
									</div>

									<div class="form-group col-md-6 acceptance-yes">
										<label class="label-control">End Date <b class="text-danger">*</b></label>

										<input type="date" class="form-control <?= show_error('end_date') ?>" name="end_date" value="<?php if ( old('end_date') !== NULL ) { echo old('end_date'); } else { echo date( 'Y-m-d' ,strtotime($candidate_data['end_date']) ); } ?>"/>
										
										<?= form_error('end_date') ?>
									</div>


								</div>

								<div class="card-footer">
                                    <button type="button" class="btn btn-danger" onclick="confirmation()"><i class="fas fa-times mr-1"></i>Cancel</button>
                                    <button type="button" class="btn btn-warning" onclick="reset_confirmation()" ><i class="fas fa-sync mr-1"></i>Reset</button>
                                    <button type="submit" class="btn btn-success" id="submit_btn"><i class="fas fa-save mr-1"></i>Save</button>

                                </div>
							</div>
						</div>

					</div>
					</form>
					<?php endforeach; ?>

				</div>
			</div>
			<?php include './view/dosen/template/footer.php';?>
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


	<!-- Dropzone -->
	<script src="./assets/js/plugin/dropzone/dropzone.min.js"></script>

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
			show_supervisor();
		});

        function confirmation() {
                
			SoloAlert.confirm({
                title:"Are you sure you want to cancel ?",
            	body: "Changes you made will not be saved !",
                useTransparency: true,
                onOk : ()=>{ window.location.href = "index.php?page=intern_candidate_independent"; }
            });

			// swal.fire({
			// 	title               : 'Cancel Confirmation',
			// 	text                :  'Are you sure you want to cancel ?',
			// 	footer                :  'All changes you made will not be saved !',
			// 	icon                : 'question',
			// 	showCancelButton: true,
			// 	confirmButtonColor: "#DD6B55",
			// 	confirmButtonText: "Yes, I'm sure !",
			// }).then((result) => {
			// 	/* Read more about isConfirmed, isDenied below */
			// 	if (result.isConfirmed) {

			// 		window.location.href = 'index.php?page=intern_candidate_independent';
			// 	}
			// })
                
        }

        function reset_confirmation() {

            SoloAlert.confirm({
            	title:"Are you sure you want to reset ?",
                body: "Changes you made will be reset to default !",
                useTransparency: true,
                onOk : ()=>{ form_reset(); }
            });
                

			// swal.fire({
			// 	title               : 'Reset Confirmation',
			// 	text                :  'Are you sure you want to reset ?',
			// 	footer                :  'All changes you made will be reset to default !',
			// 	icon                : 'question',
			// 	showCancelButton: true,
			// 	confirmButtonColor: "#DD6B55",
			// 	confirmButtonText: "Yes, I'm sure !",
			// }).then((result) => {
			// 	/* Read more about isConfirmed, isDenied below */
			// 	if (result.isConfirmed) {
			// 		form_reset();
			// 	}
			// })
        }


        function form_reset () {

            $("#form_add_lecturer").trigger("reset");

            $("small").empty();
        };



    </script>



</body>
</html>