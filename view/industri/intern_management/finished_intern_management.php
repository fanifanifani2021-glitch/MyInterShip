<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting( false ); session_start();

$title = 'Finished Intern Management'; $route = 'finished_intern_management';
include "./view/industri/template/head.php";
?>

<body>
	<div class="wrapper">

		<?php include "./view/industri/template/main-header.php"; ?>
		<?php include "./view/industri/template/sidebar.php"; ?>	

		<div class="main-panel">
			<div class="container">

				<div class="panel-header bg-primary-gradient">
					<div class="page-inner py-5">
						<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
							<div>
								<h2 class="text-white pb-2 fw-bold">Finished Intern Management</h2>
								<h5 class="text-white op-7 mb-2">Managing Finished Intern Student</h5>
							</div>

						</div>
					</div>
				</div>

				<div class="page-inner mt--5">
					<div class="row mt--2">
						<div class="col-md-12">

							<div class="card full-height">
								<div class="card-header">
									<div class="d-flex align-items-center">
										<h4 class="card-title">Finished Intern Data</h4>
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
												<th>Student Name</th>
												<th>Start Date</th>
												<th>End Date</th>
												<th>Internship Period</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody id="tbody-staff">
											<?php 

											$id_company = $_SESSION['id_company'];
											$sql = "SELECT internship.* , student_internship.name  FROM internship, student_internship
												WHERE  student_internship.nim = internship.nim AND internship.id_company = '$id_company'
												AND internship.status NOT IN ('Yes', 'No') ";
											$exec = $_SERVER['KONEKSI_DB']->query($sql); 
											foreach($exec as $i => $data): 
											$no = 1;
											?>
											<tr>
												<td><?=$i+1 ?>.</td>
												<td><?=$data['nim']?></td>
												<td><?=$data['name']?></td>
												<td><?= date('D, d - M - Y', strtotime($data['start_date']) )?></td>
												<td><?= date('D, d - M - Y', strtotime($data['end_date']) )?></td>
												<td><?= date('n', strtotime($data['end_date']) - strtotime($data['start_date']) )?> Month(s)</td>

												<td>

													<button class="btn btn-secondary btn-sm" onclick="window.location.href = 'index.php?page=finished_intern_management_detail&id_internship=<?= base64_encode($data['id_internship']) ?>'"><i class="fas fa-info-circle mr-1"></i>Detail</button>
												</td>
											</tr>

											<?php 
                                            endforeach; 
                                            ?>
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

                                                    <!-- modal tambah hrd -->
                                                <div class="modal fade" id="ModalTambah">
													<div class="modal-dialog modal-lg">
														<div class="modal-content">
															<div class="modal-header">
															<h5 class="modal-title">
																	<span class="fw-mediumbold">
																	Add Offer</span> 
																</h5>
															<button type="button" class="close" data-dismiss="modal" aria-label="Close">
																<span aria-hidden="true">&times;</span>
															</button>
															</div>
															<div class="modal-body">

															<form action="index.php?form=offer&aksi=tambah" method="POST">

                                                            <input type="hidden" name="token" value="<?php echo $_SESSION['token']?>" />
                                                            <input type="hidden" name="id_company" value="<?php echo $_SESSION['id_company'] ?>" />

                                                            <div class="form-group">
                                                            <label for="position">Position <label class="text-danger">*</label></label>
                                                            <input type="text" placeholder="Fill Position" class="form-control" name="position" id="position">  
                                                            <?= form_error('position'); ?>  
                                                            </div>

                                                            <div class="form-group">
                                                            <label for="description">Description <label class="text-danger">*</label></label>
                                                            <textarea class="form-control" placeholder="Fill Description" id="description" name="description" rows="4"></textarea>
                                                            <?= form_error('description'); ?>  
                                                            </div>

                                                            <div class="form-group">
                                                            <label for="requirement">Requirement <label class="text-danger">*</label></label>
                                                            <input type="text" placeholder="Fill Requirement" class="form-control" name="requirement" id="requirement">  
                                                            <?= form_error('requirement'); ?>  
                                                            </div>

                                                            <div class="form-group">
                                                            <label for="employer_type">Emplyer Type <label class="text-danger">*</label></label>
                                                            <input type="text" placeholder="Fill Employer type" class="form-control" name="employer_type" id="employer_type">  
                                                            <?= form_error('employer_type'); ?>  
                                                            </div>

                                                            <div class="form-group">
                                                            <label for="city">City <label class="text-danger">*</label></label>
                                                            <input type="text" placeholder="Fill City" class="form-control" name="city" id="city">  
                                                            <?= form_error('city'); ?>  
                                                            </div>

                                                            <div class="form-group">
                                                            <label for="job_type">Job Type <label class="text-danger">*</label></label>
                                                            <input type="text" placeholder="Fill Job Type" class="form-control" name="job_type" id="job_type">  
                                                            <?= form_error('job_type'); ?>  
                                                            </div>

                                                            <div class="form-group">
                                                            <label for="salary_range">Salary Range <label class="text-danger">*</label></label>
                                                            <input type="text" placeholder="Fill Salary Range" class="form-control" name="salary_range" id="salary_range">  
                                                            <?= form_error('salary_range'); ?>  
                                                            </div>

                                                            <div class="form-group">
                                                            <label for="start_date">Start Date <label class="text-danger">*</label></label>
                                                            <input type="text" placeholder="Fill Start Date" class="form-control" name="start_date" id="start_date">  
                                                            <?= form_error('start_date'); ?>  
                                                            </div>

                                                            <div class="form-group">
                                                            <label for="end_date">End Date <label class="text-danger">*</label></label>
                                                            <input type="text" placeholder="Fill End Date" class="form-control" name="end_date" id="end_date">  
                                                            <?= form_error('end_date'); ?>  
                                                            </div>

                                                            <div class="form-group">
                                                            <label for="offer_type">Offer Type <label class="text-danger">*</label></label>
                                                            <select class="form-control" name="offer_type" id="offer_type">
                                                                <option value="">Choose . . .</option>
                                                                <option>Full Employement</option>
                                                                <option>Part Time</option>
                                                                <option>Internship</option>
                                                            </select>
                                                            <?= form_error('offer_type'); ?>
                                                            </div>

                                                            </div>
                                                            <div class="modal-footer justify-content-between">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary">Save</button>
                                                            </form>

															</div>
														</div>
														<!-- /.modal-content -->
													</div>
														<!-- /.modal-dialog -->
												</div>
												<!-- /.tutupmodal -->

													<!-- modal hapus -->
													<div class="modal fade" id="ModalHapus" tabindex="-1" role="dialog" aria-hidden="true">
													<div class="modal-dialog" role="document">
														<div class="modal-content">
															<div class="modal-header no-bd">
																<h5 class="modal-title">
																	<span class="fw-mediumbold">
																	Delete Data</span> 
																</h5>
																<button type="button" class="close" data-dismiss="modal" aria-label="Close">
																	<span aria-hidden="true">&times;</span>
																</button>
															</div>
															<div class="modal-body">
																<form action="index.php?form=offer&aksi=hapus" method="POST">
																	<input type="hidden" name="token" value="<?php echo $_SESSION['token']?>">
																	<input type="hidden" id="id_hapus" name="id_hapus" value="">
																	Are You Sure to Delete This Data?
															</div>
															<div class="modal-footer justify-content-between">
																<button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Batal</button>
																<button type="submit" class="btn btn-primary pull-right">Hapus</button>
															</div>
															</form>

														</div>
													</div>
                                                	</div>
                                                	<!-- tutup modal -->

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

	<script >
		$(document).ready(function() {
            <?php if ( old('old_tambah') !== NULL ) : echo "$('#ModalTambah').modal('show');"; endif; ?>


			$('#ekonomi-kepri').DataTable({
				"pageLength": 10,
			});
		});

		function modal_hapus(id){
			var id_hapus = document.getElementById('id_hapus');
			id_hapus.value = id;
		}
    </script>

</body>
</html>