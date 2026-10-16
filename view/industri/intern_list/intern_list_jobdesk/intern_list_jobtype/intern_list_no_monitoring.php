<?php 
// $id_internship = base64_decode($_GET['id_internship']);
// $id_jobdesc_intern = $_GET['id_jobdesc_intern'];
// $nim = $_GET['nim'];


$sql = "SELECT * FROM jobdesc_intern_ceklis WHERE id_jobdesc_intern = '$id_jobdesc_intern'";
$exec = $_SERVER['KONEKSI_DB']->query( $sql );

// var_dump( $id_job ); die;

// $sql = "SELECT detail_jobdesc.* FROM detail_jobdesc WHERE detail_jobdesc.id_detail NOT IN (SELECT `id_detail` FROM jobdesc_intern_ceklis WHERE id_jobdesc_intern = '$id_jobdesc_intern' ) AND detail_jobdesc.id_jobdesc = '$id_job';";
// var_dump( $sql ); die;

?>


<div class="col-md-12">

	<div class="card full-height">
		<div class="card-header">
		    <h4 class="card-title" id="FocusAdd">Add Jobdesk</h4>
		</div>


		<form action="index.php?form=jobdesk_spv&aksi=add_job_type" method="POST"> <?= csrf() ?>
				<input type="hidden" name="id_jobdesc_intern" id="id_jobdesc_intern" value="<?=$id_jobdesc_intern?>">
				<input type="hidden" name="nim" id="nim" value="<?=$nim?>">
				<input type="hidden" name="id_internship" id="id_internship" value="<?=$id_internship?>">
				<input type="hidden" name="need_monitoring" id="need_monitoring" value="No">

			<div class="card-body form-row" >

				<div class="form-group col-md-12">
					<label>Job Type <b class="text-danger">*</b></label>

					<select type="text" class="form-control <?= show_error('id_detail') ?>" name="id_detail" required>
						<option value="" hidden>- Select One -</option>
						
						<?php
				// 		$sql = "SELECT detail_jobdesc.* FROM detail_jobdesc, jobdesc_intern WHERE detail_jobdesc.id_detail NOT IN (SELECT `id_detail` FROM jobdesc_intern_ceklis WHERE id_internship = $id_internship ) AND jobdesc_intern.id_jobdesc = detail_jobdesc.id_jobdesc ANd jobdesc_intern.id_jobdesc_intern = $id_jobdesc_intern ";

$sql = "SELECT detail_jobdesc.* FROM detail_jobdesc WHERE detail_jobdesc.id_detail NOT IN (SELECT `id_detail` FROM jobdesc_intern_ceklis WHERE id_jobdesc_intern = '$id_jobdesc_intern' ) AND detail_jobdesc.id_jobdesc = '$id_job';";
// var_dump( $sql ); die;

						foreach( $_SERVER['KONEKSI_DB']->query($sql) as $row ) {
							echo "<option value='" .$row['id_detail']. "'>" .$row['jenis_pekerjaan']. "</option>";
						}

						?>
					</select>

					<?= form_error('id_detail') ?>

				</div>


			</div>

			<div class="modal-footer justify-content-between">
				<button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Save</button>
			</div>
			</form>


	</div>

</div>



<div class="col-md-12">
	<div class="card full-height">
		<div class="card-header">
		    <h4 class="card-title">Jobdesk Job Type</h4>
		</div>
        
        
		<div class="card-body form-row">

			<div class="table-responsive">
				<table class="table table-bordered" id="table">
					<thead>
						<tr>
							<th width="5%">No.</th>
							<th>Job Type</th>
							<th>Description</th>
							<th width="5%">Action</th>
							<!-- <th>Action</th> -->
						</tr>
					</thead>

					<tbody>
						<?php

						foreach( $exec as $i => $data_job ) : 
							$id_ceklis = $data_job['id_ceklis'];
							$id_detail = $data_job['id_detail'];
							$ceklis = $data_job['ceklis'];

							$sql = "SELECT * FROM detail_jobdesc WHERE id_detail = '$id_detail'";
							$data_jobdesc = $_SERVER['KONEKSI_DB']->query($sql)->fetch_assoc();

							if ( $ceklis == 1 ) {
								$ceklis = "checked";
							} else {
								$ceklis = '';
							}
						?>
						<tr>
							<td><?= $i+1 ?>.</td>
							<td><?= $data_jobdesc['jenis_pekerjaan'] ?></td>
							<td><?= $data_jobdesc['job_description'] ?></td>
							<!-- <td>
								<div class="form-check">
									<label class="form-check-label">
										<input disabled class="form-check-input toggle" type="checkbox" <?= $ceklis ?> >
										<span class="form-check-sign">in accordance</span>
									</label>
								</div>
							</td> -->

							<td>
								<button class="btn btn-danger btn-sm" onclick="confirm_delete('<?= $id_ceklis ?>', '<?= $data_jobdesc['jenis_pekerjaan'] ?>')"><i class="fas fa-trash mr-1"></i>Delete</button>

							</td>


						</tr>

						<?php 
							$i++; endforeach; ?>
					</tbody>
				</table>
			</div>

		</div>



	</div>
</div>


