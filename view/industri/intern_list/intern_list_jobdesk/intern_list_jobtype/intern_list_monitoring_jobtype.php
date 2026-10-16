<?php
// $id_internship = base64_decode($_GET['id_internship']);
// $id_jobdesc_intern = $_GET['id_jobdesc_intern'];
// $nim = $_GET['nim'];
// $kode_prodi = $_GET['kode_prodi'];



$sql 	= "SELECT * FROM jobdesc WHERE kode_prodi = '$kode_prodi'";
$row	= $_SERVER['KONEKSI_DB']->query($sql)->fetch_assoc();


$sql = "SELECT * FROM jobdesc_intern_ceklis WHERE id_jobdesc_intern = '$id_jobdesc_intern'";
$exec = $_SERVER['KONEKSI_DB']->query($sql);

$sql = "SELECT detail_jobdesc.* FROM detail_jobdesc WHERE detail_jobdesc.id_detail NOT IN (SELECT `id_detail` FROM jobdesc_intern_ceklis WHERE id_jobdesc_intern = '$id_jobdesc_intern' ) AND detail_jobdesc.id_jobdesc = '$id_job';";
var_dump( $sql ); die;

?>


<div class="col-md-12">

	<div class="card full-height">
		<div class="card-header">
			<h4 class="card-title" id="FocusAdd">Add Jobdesk</h4>
		</div>


		<div class="card-body form-row">

			<div class="form-group col-md-12">
				Polibatam Internship Coordinator require you to monitor our intern student through their internship period. For more information please refer to <b>Additional Information</b> section provided by Polibatam Internship Coordinator.
			</div>

			<div class="form-group col-md-12">
				<label>Additional Information</label>
				<textarea class="form-control" rows="5" id="additional_information"><?= $row['additional_information'] ?></textarea>
			</div>

		</div>


		<form action="index.php?form=jobdesk_spv&aksi=add_monitoring_job" method="POST"> <?= csrf() ?>
			<input type="hidden" name="id_jobdesc_intern" id="id_jobdesc_intern" value="<?= $id_jobdesc_intern ?>">
			<input type="hidden" name="nim" id="nim" value="<?= $nim ?>">
			<input type="hidden" name="id_internship" id="id_internship" value="<?= $id_internship ?>">
			<input type="hidden" name="need_monitoring" id="need_monitoring" value="Yes">

			<div class="card-body form-row">

				<div class="form-group col-md-12">
					<label>Job Type <b class="text-danger">*</b></label>

					<select type="text" class="form-control <?= show_error('id_detail') ?>" name="id_detail" required>
						<option value="" hidden>- Select One -</option>

						<?php
				// 		$sql = "SELECT detail_jobdesc.* FROM detail_jobdesc, jobdesc_intern WHERE jobdesc_intern.id_jobdesc = detail_jobdesc.id_jobdesc ANd jobdesc_intern.id_jobdesc_intern = $id_jobdesc_intern ";
						
						$sql = "SELECT detail_jobdesc.* FROM detail_jobdesc WHERE detail_jobdesc.id_detail NOT IN (SELECT `id_detail` FROM jobdesc_intern_ceklis WHERE id_jobdesc_intern = '$id_jobdesc_intern' ) AND detail_jobdesc.id_jobdesc = '$id_job';"

						foreach ($_SERVER['KONEKSI_DB']->query($sql) as $row) {
							echo "<option value='" . $row['id_detail'] . "'>" . $row['jenis_pekerjaan'] . "</option>";
						}

						?>
					</select>

					<?= form_error('id_detail') ?>

				</div>

				<div class="form-group col-md-6">
					<label>Monitoring Start Date <b class="text-danger">*</b></label>

					<input type="date" name="start_date" class="form-control" />
					<?= form_error('start_date') ?>
				</div>

				<div class="form-group col-md-6">
					<label>Monitoring End Date <b class="text-danger">*</b></label>

					<input type="date" name="end_date" class="form-control" />
					<?= form_error('end_date') ?>
				</div>


			</div>

			<div class="card-footer justify-content-between">
				<button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Save</button>
			</div>
		</form>


	</div>

</div>



<div class="col-md-12">
	<div class="card full-height">
		<div class="card-header">
			<div class="card-title">Jobdesk Job Type
				<div class="float-right justify-content-beetwen">
					<select class="form-control">

						<?php
						$sql2 = "SELECT DISTINCT(`date_start`) AS tgl FROM jobdesc_intern_ceklis WHERE id_jobdesc_intern = '$id_jobdesc_intern'";
						$exec2 = $_SERVER['KONEKSI_DB']->query($sql2);

						if ($exec2->num_rows == 0) {
							echo "<option value=''>None</option>";
						} else {
							// echo "<option value='' hidden>- Select Filter Data -</option>";
							echo "<option value='all'>All Data</option>";
						}

						foreach ($exec2 as $tgl) : ?>
							<option><?= date('M Y', strtotime($tgl['tgl'])) ?></option>

						<?php endforeach; ?>

					</select>
				</div>

			</div>
		</div>


		<div class="card-body form-row">

			<div class="table-responsive">
				<table class="table table-bordered" id="table">
					<thead>
						<tr>
							<th width="5%">No.</th>
							<th>Job Type</th>
							<th>Description</th>
							<th>Start Date</th>
							<th>End Date</th>
							<th width="5%">Action</th>
							<!-- <th>Action</th> -->
						</tr>
					</thead>

					<tbody>
						<?php

						foreach ($exec as $i => $data_ceklis) :
							$id_ceklis = $data_ceklis['id_ceklis'];
							$id_detail = $data_ceklis['id_detail'];
							$ceklis = $data_ceklis['ceklis'];

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
								<td><?= $data_ceklis['date_start'] ?></td>
								<td><?= $data_ceklis['date_end'] ?></td>

								<td>
									<div class="btn-group">
										<button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#ModalEdit<?= $i ?>"><i class="fas fa-trash mr-1"></i>Edit</button>
										<button class="btn btn-danger btn-sm" onclick="confirm_delete('<?= $id_ceklis ?>', '<?= $data_jobdesc['jenis_pekerjaan'] ?>')"><i class="fas fa-trash mr-1"></i>Delete</button>
									</div>
								</td>


							</tr>

							<!-- modal edit -->
							<div class="modal fade" id="ModalEdit<?= $i ?>">
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

										<form action="index.php?form=jobdesk_spv&aksi=edit_monitoring_job" method="POST"> <?= csrf() ?>
											<input type="hidden" name="id_jobdesc_intern" id="id_jobdesc_intern" value="<?= $id_jobdesc_intern ?>">
											<input type="hidden" name="nim" id="nim" value="<?= $nim ?>">
											<input type="hidden" name="id_internship" id="id_internship" value="<?= $id_internship ?>">
											<input type="hidden" name="need_monitoring" id="need_monitoring" value="Yes">
											<input type="hidden" name="id_ceklis" value="<?= $id_ceklis ?>">
											<input type="hidden" name="loop" value="<?= $i  ?>">

											<div class="modal-body form-row">

												<div class="form-group col-md-12">
													<label>Job Type <b class="text-danger">*</b></label>

													<select type="text" class="form-control <?= show_error('edit_id_detail') ?>" name="edit_id_detail" required>
														<option value='<?= $data_jobdesc['id_detail'] ?>' hidden><?= $data_jobdesc['jenis_pekerjaan'] ?></option>

														<?php
														$sql = "SELECT detail_jobdesc.* FROM detail_jobdesc, jobdesc_intern WHERE jobdesc_intern.id_jobdesc = detail_jobdesc.id_jobdesc ANd jobdesc_intern.id_jobdesc_intern = $id_jobdesc_intern ";

														foreach ($_SERVER['KONEKSI_DB']->query($sql) as $row) {
															echo "<option value='" . $row['id_detail'] . "'>" . $row['jenis_pekerjaan'] . "</option>";
														}

														?>
													</select>

													<?= form_error('edit_id_detail') ?>

												</div>

												<div class="form-group col-md-6">
													<label>Monitoring Start Date <b class="text-danger">*</b></label>

													<input type="date" name="edit_start_date" class="form-control <?= show_error('edit_start_date') ?>" value="<?= $data_ceklis['date_start'] ?>" />
													<?= form_error('edit_start_date') ?>
												</div>

												<div class="form-group col-md-6">
													<label>Monitoring End Date <b class="text-danger">*</b></label>

													<input type="date" name="edit_end_date" class="form-control <?= show_error('edit_end_date') ?>" value="<?= $data_ceklis['date_end'] ?>" />
													<?= form_error('edit_end_date') ?>
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

						<?php
							$i++;
						endforeach; ?>
					</tbody>
				</table>
			</div>

		</div>



	</div>
</div>