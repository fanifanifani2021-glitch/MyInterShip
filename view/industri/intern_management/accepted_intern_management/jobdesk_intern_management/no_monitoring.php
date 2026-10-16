<?php 
$id_internship = base64_decode($_GET['id_internship']);
$id_jobdesc_intern = $_GET['id_jobdesc_intern'];
$nim = $_GET['nim'];


$sql = "SELECT jobdesc_intern_ceklis.* FROM jobdesc_intern_ceklis INNER JOIN jobdesc_intern ON jobdesc_intern.id_jobdesc_intern = jobdesc_intern_ceklis.id_jobdesc_intern WHERE jobdesc_intern.id_internship = '$id_internship'";
$exec = $_SERVER['KONEKSI_DB']->query( $sql );

?>

<div class="col-md-12">
	<div class="card full-height">
		<div class="card-header">
		    <h4 class="card-title">Jobtype</h4>
		</div>
        
        
		<div class="card-body form-row">

			<div class="table-responsive">
				<table class="table table-bordered" id="table">
					<thead>
						<tr>
							<th width="5%">No.</th>
							<th>Job Type</th>
							<th>Description</th>
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



						</tr>

						<?php 
							$i++; endforeach; ?>
					</tbody>
				</table>
			</div>

		</div>



	</div>
</div>


