<div class="col-md-12">


	<div class="card full-height">
		<div class="card-header">
			<h4 class="card-title">Jobdesk Job Type

				<button class="btn btn-primary btn-md float-right" data-toggle="modal" data-target="#ModalTambah"><i class="fas fa-plus mr-1"></i>Add Data</button>

			</h4>
		</div>


		<!-- Kalau Perlu Monitoring -->
		<?php if (in_array($row['need_monitoring'], ['YES', 'Yes', 'yes'])) { ?>
			<div class="card-body">

				<div class="table-responsive">
					<table id="basic-datatables" class="display dataTable table table-striped table-hover">
						<thead>
							<tr>
								<th style="width: 5%">No</th>
								<th>Job Type</th>
								<th>Checklist</th>
								<th style="width: 10%">Action</th>
							</tr>
						</thead>
						<tbody id="tbody-staff">
							<?php

							$sql = "SELECT * FROM jobdesc_intern_ceklis WHERE id_jobdesc_intern = '$id_jobdesc_intern'";
							$exec = $_SERVER['KONEKSI_DB']->query($sql);


							$id_jobdesc = $data_job_intern['id_jobdesc_intern'];

							$sql2 = mysqli_query($koneksi_db, "SELECT * FROM jobdesc_intern_ceklis WHERE id_jobdesc = $id_jobdesc AND id_internship = '$id_internship' ORDER BY id_ceklis DESC");

							$no = 1;
							foreach ($sql2 as $data) :
							?>
								<tr>
									<td><?= $no++ ?>.</td>
									<td><?= $data['jenis_pekerjaan'] ?></td>
									<td><?= $data['job_description'] ?></td>
									<td>
										<div class="btn-group btn-round">
											<button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												Action
											</button>
											<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
												<button class="dropdown-item" onclick="window.location.href='index.php?page=intern_guidance_edit_job&id_internship=<?= base64_encode($id_internship) ?>&id_ceklis=<?= base64_encode($data['id_ceklis']) ?>'"><i class="fa fa-edit mr-1"></i>Edit Data</button>
												<button class="dropdown-item" data-toggle="modal" data-target="#ModalHapus" onclick="modal_hapus('<?php echo $data['id_ceklis']; ?>')"><i class="fas fa-trash-alt mr-1"></i>Delete Data</button>
											</div>
										</div>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>

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
								<form action="index.php?form=jobdesk_dkm&aksi=deljob" method="POST">
									<input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?>">
									<input type="hidden" id="id_hapus" name="id_hapus" value="">
									Are You Sure to Delete Current Jobdesk Data ?
							</div>
							<div class="modal-footer justify-content-between">
								<button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fas fa-times mr-2"></i>Cancel</button>
								<button type="submit" class="btn btn-primary pull-right"><i class="fas fa-trash-alt mr-2"></i>Delete</button>
							</div>
							</form>

						</div>
					</div>
				</div>
				<!-- tutup modal -->


				<!-- modal tambah hrd -->
				<div class="modal fade" id="ModalEdit">
					<div class="modal-dialog modal-md">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title">
									<span class="fw-mediumbold">
										Edit Job Type</span>
								</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>

							<form action="index.php?form=jobdesk_dkm&aksi=editjob" method="POST">
								<?= csrf() ?>
								<input type="hidden" value="" name="id_detail_edit" id="id_detail_edit">
								<div class="modal-body">
									<div class="form-group">
										<label for="jenis_pekerjaan_edit">Job Type <label class="text-danger">*</label></label>
										<input type="text" placeholder="Input Job Type" class="form-control <?= show_error('jenis_pekerjaan_edit') ?>" name="jenis_pekerjaan_edit" id="jenis_pekerjaan_edit" required>
										<?= form_error('jenis_pekerjaan_edit'); ?>
									</div>

									<div class="form-group">
										<label for="job_description_edit">Job Description <label class="text-danger">*</label></label>
										<textarea rows="5" type="text" placeholder="Input Job Type" class="form-control <?= show_error('job_description_edit') ?>" name="job_description_edit" id="job_description_edit" required></textarea>
										<?= form_error('job_description_edit'); ?>
									</div>
								</div>

								<div class="modal-footer justify-content-between">
									<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times mr-2"></i>Close</button>
									<button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i>Update</button>
								</div>
							</form>

						</div>
						<!-- /.modal-content -->

					</div>
					<!-- /.modal-dialog -->
				</div>
				<!-- /.tutupmodal -->

			</div>
		<?php } ?>

		<?php if (in_array($row['need_monitoring'], ['NO', 'No', 'no'])) { ?>
			<div class="card-body form-row">

				<?php


				if ($exec->num_rows < 1) {
					echo "<div class='form-group col-md-12'>Wow such an empty !</div";
				} else {
				?>


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



								foreach ($exec as $i => $data_job) :
									$id_detail = $data_job['id_detail'];
									$ceklis = $data_job['ceklis'];

									$sql = "SELECT * FROM detail_jobdesc WHERE id_detail = '$id_detail'";
									$data_jobdesc = $_SERVER['KONEKSI_DB']->query($sql)->fetch_assoc();

									if ($ceklis == 1) {
										$ceklis = "checked";
									} else {
										$ceklis = '';
									}
								?>
									<tr>
										<td><?= $i + 1 ?>.</td>
										<td><?= $data_jobdesc['jenis_pekerjaan'] ?></td>
										<td><?= $data_jobdesc['job_description'] ?></td>
										<td>
											<div class="form-check">
												<label class="form-check-label">
													<input disabled class="form-check-input toggle" onclick="toggle_date()" type="checkbox" <?= $ceklis ?>>
													<span class="form-check-sign">in accordance</span>
												</label>
											</div>
										</td>

									</tr>

								<?php $i++;
								endforeach; ?>
							</tbody>
						</table>
					</div>

				<?php } ?>



			</div>
		<?php } ?>



	</div>
</div>