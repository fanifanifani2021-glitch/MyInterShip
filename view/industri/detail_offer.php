<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting( false ); session_start();

$title = 'Offer Detail'; $route = 'industry_offer';
include "./view/industri/template/head.php";

$id = base64_decode($_GET['id']); $id_company = $_SESSION['id_company'];
$sql = "SELECT * FROM offer WHERE id_offer = '$id' LIMIT 1";
$exec = $_SERVER['KONEKSI_DB']->query($sql);

if ( $exec->num_rows != 1 ) {
	make_swal2('error', 'Not Allowed', 'Offer Data not found !');
	redirect_back(1); exit;
}

$data = $exec->fetch_assoc();

if ( $data['status'] == 'Open' ) {
	$status = "<button type='button' class='btn btn-success btn-sm'>Open</button>";
} else {
	$status = "<button type='button' class='btn btn-danger btn-sm'>Closed</button>";
}



?>

<body>
	<div class="wrapper">

		<?php include "./view/industri/template/main-header.php"; ?>
		<?php include "./view/industri/template/sidebar.php"; ?>	

		<div class="main-panel">
			<div class="container">

				<div class="page-inner mt-2">

					<div class="page-header">
						<h4 class="page-title">Offer Detail</h4>
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
								<a href="index.php?page=industry_offer">Internship Offer</a>
							</li>
							
							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="#">Offer Detail</a>
							</li>
						</ul>
					</div>



					<div class="row">


						<!-- Table -->
						<div class="col-md-12">
							<div class="card full-height">
								<div class="card-header">
									<div class="d-flex align-items-center float-left">
										<h4 class="card-title">Offer Data</h4>
									</div>    
								</div>
								<div class="card-body">
                                    <table class="table table-bordered table-hover">
                                            <tbody>
                                                <tr>
                                                    <th width="30%">Position <span class="float-right">:</span></th>
                                                    <td width="70%"><span><?=$data['position']; ?></span></td>
                                                </tr>
                                                <tr>
                                                    <th width="30%">Employer Type <span class="float-right">:</span></th>
                                                    <td width="70%"><span><?=$data['employer_type']; ?></span></td>
                                                </tr>
                                                <tr>
                                                    <th width="30%">City <span class="float-right">:</span></th>
                                                    <td width="70%"><span><?=$data['city']; ?></span></td>
                                                </tr>
                                                <tr>
                                                    <th width="30%">Job Type <span class="float-right">:</span></th>
                                                    <td width="70%"><span><?=$data['job_type']; ?></span></td>
                                                </tr>
                                                
                                                <tr>
                                                    <th width="30%">Start Date <span class="float-right">:</span></th>
                                                    <td width="70%"><span><?=date('d-m-Y', strtotime($data['start_date']));?></span></td>
                                                </tr>
                                                <tr>
                                                    <th width="30%">End Date <span class="float-right">:</span></th>
                                                    <td width="70%"><span><?=date('d-m-Y', strtotime($data['end_date']));?></span></td>
                                                </tr>
                                                <tr>
                                                    <th width="30%">Offer Type <span class="float-right">:</span></th>
                                                    <td width="70%"><span><?=$data['offer_type']; ?></span></td>
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
                                    
									<?php if ( $_SESSION['user_type'] == 'HRD' ) : ?>
                                    <button class="btn btn-secondary" data-toggle="modal" data-target="#ModalEdit"><i class="fa fa-edit"></i> Edit Offer</button>             
                                    <button class="btn btn-secondary" data-toggle="modal" data-target="#ModalChange"><i class="fa fa-sync"></i> Change Status</button>             
									<?php endif; ?>

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
									<?=$data['description']; ?>
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
									<?=$data['requirement']; ?>
                                </div>
                            
							</div>
						</div>


						<div class="col-md-12">

							<div class="card full-height">
								<div class="card-header">
									<div class="d-flex align-items-center float-left">
										<h4 class="card-title">Offer Applicant</h4>
									</div>

								</div>
								<div class="card-body">
								<div class="table-responsive" id="tabel-ini">
									<table id="ekonomi-kepri" 
										class="display dataTable table table-striped table-hover" >
										<thead>
											<tr>
												<th>No.</th>
												<th>NIM</th>
												<th>Name</th>
												<th>Status</th>
												<th>Apply Date</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody id="tbody-staff">
											<?php

											$sql2 	= "SELECT * FROM student_internship, internship, applicant WHERE student_internship.nim = internship.nim AND internship.id_internship = applicant.id_internship AND applicant.id_offer = '$id' ";
											$exec2	= $_SERVER['KONEKSI_DB']->query($sql2);
											$no = 1;

                                            foreach($exec2 as $row):
                                            ?>
											<tr>
												<td><?=$no++?>.</td>
												<td><?=$row['nim']?></td>
												<td><?=$row['name']?></td>
												<td><?=$row['status']?></td>
												<td><?= date('D, M d Y', strtotime($row['date'] ) )?></td>
												<td>
													<?php if ( $_SESSION['user_type'] == 'HRD' && in_array($row['status'], ['Waiting', 'waiting', 'Apply', 'apply'] ) ) :?>
													<button type="button" class="btn btn-success btn-sm" onclick="window.location.href='index.php?page=validate_intern&id_internship=<?= base64_encode( $row['id_internship'] )?>' "><i class="fas fa-check mr-1"></i>Validate Data</button></td>
													<?php else : ?>
													<button type="button" disabled class="btn btn-info btn-sm" onclick="window.location.href='index.php?page=registration_data&id_internship=<?= base64_encode( $row['id_internship'] )?>' "><i class="fas fa-info-circle mr-1"></i>Registration Data</button></td>
													<?php endif; ?>
											</tr>
                                            <?php endforeach; ?>
										</tbody>
									</table>
									</div>
								</div>
							</div>
						</div>


					</div>
				</div>


			</div>

			<?php include "./view/industri/template/footer.php"; ?>

            <!-- modal edit -->
            <div class="modal fade" id="ModalEdit">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">
							<span class="fw-mediumbold">
							Edit Offer</span> 
							</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
							</button>
						</div>
						
						<form action="index.php?form=offer&aksi=edit" method="POST"> <?= csrf() ?> <?= input_hidden('id_offer', $data['id_offer'])?>
						
						<div class="modal-body">

							<div class="form-group">
                            	<label for="position">Position <label class="text-danger">*</label></label>
                                
								<input type="text" placeholder="Fill Position" 
									class="form-control <?= show_error('position'); ?>" 
									name="position" id="position" value="<?=$data['position']?>">  
                                <?= form_error('position'); ?>  
                            </div>

                            <div class="form-group">
                            	<label for="description">Description <label class="text-danger">*</label></label>
                                <textarea class="form-control <?= show_error('description'); ?>" 
								placeholder="Fill Description" id="description" name="description" 
								rows="4"><?=$data['description']?></textarea>
                                <?= form_error('description'); ?>  
                            </div>

                            <div class="form-group">
                            	<label for="requirement">Requirement <label class="text-danger">*</label></label>
                                <textarea type="text" placeholder="Fill Requirement" 
								class="form-control <?= show_error('requirement'); ?>" 
								name="requirement" id="requirement"><?=$data['requirement']?></textarea>
                                <?= form_error('requirement'); ?>  
                            </div>

                            <div class="form-group">
                            	<label for="employer_type">Employer Type <label class="text-danger">*</label></label>
                                <input type="text" placeholder="Fill Employer type" 
								class="form-control <?= show_error('employer_type'); ?>" 
								name="employer_type" id="employer_type" value="<?=$data['employer_type']?>">  
                                <?= form_error('employer_type'); ?>  
                            </div>

                            <div class="form-group">
                            	<label for="city">City <label class="text-danger">*</label></label>
                                <input type="text" placeholder="Fill City" 
								class="form-control <?= show_error('city'); ?>" 
								name="city" id="city" value="<?=$data['city']?>">  
                                <?= form_error('city'); ?>  
                            </div>

                            <div class="form-group">
                            	<label for="job_type">Job Type <label class="text-danger">*</label></label>
                                <input type="text" placeholder="Fill Job Type" 
								class="form-control <?= show_error('job_type'); ?>" 
								name="job_type" id="job_type" value="<?=$data['job_type']?>">  
                                <?= form_error('job_type'); ?>  
                            </div>

                            <div class="form-group">
                            	<label for="salary_range">Salary Range <label class="text-danger">*</label></label>
                                <input type="text" placeholder="Fill Salary Range" 
								class="form-control <?= show_error('salary_range'); ?> " 
								name="salary_range" id="salary_range" value="<?=$data['salary_range']?>">  
                                <?= form_error('salary_range'); ?>  
                            </div>

                            <div class="form-group">
                            	<label for="start_date">Start Date <label class="text-danger">*</label></label>
                                <input type="date" placeholder="Fill Start Date" 
								class="form-control <?= show_error('start_date'); ?>  " 
								name="start_date" id="start_date" value="<?=$data['start_date']?>">  
                            	<?= form_error('start_date'); ?>  
                            </div>

                            <div class="form-group">
                            	<label for="end_date">End Date <label class="text-danger">*</label></label>
                                <input type="date" placeholder="Fill End Date" class="form-control <?= show_error('end_date'); ?> " name="end_date" id="end_date" value="<?=$data['end_date']?>">  
                                <?= form_error('end_date'); ?>  
                            </div>

                            <div class="form-group">
                            	<label for="offer_type">Offer Type <label class="text-danger">*</label></label>
                                <select class="form-control <?= show_error('offer_type'); ?>" name="offer_type" id="offer_type">
                                	<option value="<?=$data['offer_type']?>" hidden><?=$data['offer_type']?></option>
                                    <option>Full Employement</option>
                                    <option>Part Time</option>
                                    <option>Internship</option>
                                </select>
                            	<?= form_error('offer_type'); ?>
                            </div>

                        </div>
                        
						<div class="modal-footer justify-content-between">
                        	<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Close</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-edit mr-1"></i>Edit</button>
						</div>
					
						</form>

					</div>
					<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.tutupmodal -->


			<!-- modal hapus -->
			<div class="modal fade" id="ModalChange" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header no-bd">
							<h5 class="modal-title">
							<span class="fw-mediumbold">
							Change Status</span> 
							</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
							</button>
						</div>

						<form action="index.php?form=offer&aksi=change_status" method="POST"> <?= csrf(); ?>
							<input type="hidden" id="id_change" name="id_change" value="<?= $id ?>">

						<div class="modal-body">
							<div class="form-group">
								<label>Offer Status <b class="text-danger">*</b></label>
								<select class="form-control <?= show_error('offer_status')?>" name="offer_status">
									<!-- <option value="" hidden>- Select Offer Status -</option> -->
									<option hidden><?= $data['status'] ?></option>
									<option>Open</option>
									<option>Closed</option>
								</select>

								<?= form_error('offer_status')?>
							</div>
						</div>
						<div class="modal-footer justify-content-between">
							<button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Cancel</button>
							<button type="submit" class="btn btn-primary pull-right"><i class="fas fa-sync mr-1"></i>Change Status</button>
						</div>
						</form>

					</div>
				</div>
            </div>
            <!-- tutup modal -->


			<!-- modal edit validasi -->
                                                <div class="modal fade" id="ModalValidasi">
													<div class="modal-dialog modal-md">
														<div class="modal-content">
															<div class="modal-header">
															<h5 class="modal-title">
																	<span class="fw-mediumbold">
																	Change Validation Internship</span> 
																</h5>
															<button type="button" class="close" data-dismiss="modal" aria-label="Close">
																<span aria-hidden="true">&times;</span>
															</button>
															</div>
															<div class="modal-body">

															<form action="index.php?form=register_offer&aksi=validasi_offer" method="POST">

                                                            <input type="hidden" name="token" value="<?php echo $_SESSION['token']?>" />
															<input type="hidden" name="id_internship_validasi" id="id_internship_validasi" value="">
															<input type="hidden" name="id_offer" id="id_offer" value="<?=$id?>">
															<input type="hidden" name="start_date" value="<?=$data['start_date']?>">
															<input type="hidden" name="end_date" value="<?=$data['end_date']?>">

															
                                                            <div class="form-group">
                                                            <label for="status2">Apply Status <label class="text-danger">*</label></label>
                                                            <select class="form-control" name="status2" id="status2">
                                                                <option hidden><?=$row['status']; ?></option>
                                                                <option>diterima</option>
                                                                <option>ditolak</option>
                                                            </select>
                                                            <?= form_error('status2'); ?>
                                                            </div>

															<div class="form-group">
                                                            <label for="status">Acceptance Status <label class="text-danger">*</label></label>
                                                            <select class="form-control" name="status" id="status">
                                                                <option value="" hidden>Yes or No From Industry...</option>
                                                                <option>Yes</option>
                                                                <option>No</option>
                                                            </select>
                                                            <?= form_error('status'); ?>
                                                            </div>

                                                            <div class="form-group">
                                                            <label for="supervisor">Supervisor <label class="text-danger">*</label></label>
                                                            <select class="form-control" name="supervisor" id="supervisor">
                                                                <option value="" hidden>Choose Supervisor...</option>
																<?php
																$id = $data['id_company'];
																$spv = mysqli_query($koneksi_db, "SELECT * FROM user_company WHERE id_company = $id AND user_type = 'supervisor'");
																foreach($spv as $ol):
																?>
                                                                <option value="<?=$ol['id_user_company']?>"><?=$ol['user_fullname']?></option>
																<?php endforeach; ?>
                                                            </select>
                                                            <?= form_error('supervisor'); ?>
                                                            </div>

                                                            </div>
                                                            <div class="modal-footer justify-content-between">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary">Update</button>
                                                            </form>

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


	<script>
        CKEDITOR.replace( 'description', {
			removeButtons : 'PasteFromWord,Styles,Format,Font,FontSize',
		} );
		CKEDITOR.replace( 'requirement', {
			removeButtons : 'PasteFromWord,Styles,Format,Font,FontSize',
		} );
	</script>


    <script>
        $(document).ready(function(){
            <?php if ( old ('old_edit') !== NULL ) : echo "$('#ModalEdit').modal('show')"; endif; ?>
            <?php if ( old ('ModalChange') !== NULL ) : echo "$('#ModalChange').modal('show')"; endif; ?>

			$('#ekonomi-kepri').DataTable({
				"pageLength": 10,
			});

        });

		function changevalidasi(id){
			$("#id_internship_validasi").val(id);
		}
    </script>

</body>
</html>