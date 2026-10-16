<?php
$_SESSION['EXPORT']['INTERNSHIP_LIST']['QUERY'] = '';

$coor = $_POST['coor'];

$nim = $_POST['nim'];
$sql = "SELECT internship.*, company.name FROM internship, company WHERE company.id_company = internship.id_company AND internship.nim = '$nim' ORDER BY id_internship DESC";
$_SESSION['EXPORT']['INTERNSHIP_LIST']['QUERY'] = $sql;

?>


<div class="card">
	<div class="card-header">
		<div class="card-title">Internship List
			<!-- <a type="button" role="button" class="btn btn-primary btn-round btn-sm float-right mr-1 text-white" href='index.php?page=export_internship_student' target='_blank'><i class="fas fa-file-excel mr-2"></i>Export</a> -->
			<a type="button" role="button" class="btn btn-primary btn-round btn-sm float-right mr-1 text-white" href="<?= base_url('index.php?page=add_internship&coor=' . $coor . '&nim=' . base64_encode($nim)) ?>"><i class="fas fa-plus mr-2"></i>Add Data</a>
		</div>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table id="basic-datatables" class="display table table-striped table-hover">
				<thead>
					<tr>
						<th>No.</th>
						<th>Register Date</th>
						<th>Company Name</th>
						<th>Status</th>
						<th>Start Date</th>
						<th>End Date</th>
						<th>Action</th>
					</tr>
				</thead>

				<tbody>


					<?php
					$exec = $_SERVER['KONEKSI_DB']->query($sql);

					foreach ($exec as $i => $intern_data) : 
					
						$internship = $intern_data;

						if (in_array($internship['status'], [NULL])) {
							$internship_status = 'None';
							$internship_period = '-';
							$internship_date = '-';
						} else 
						if (in_array($internship['status'], ['YES', 'Yes', 'yes'])) {
							$internship_status = 'Internship Accepted';
							$internship_period = range_time_elapsed_string($internship['start_date'], $internship['end_date']);
							$internship_date = date('d-M-Y', strtotime($internship['start_date'])) . " until " . date('d-M-Y', strtotime($internship['end_date']));
						
							$internship_date .= ' (' .$internship_period. ')';
						
						} else
					
						if (in_array($internship['status'], ['NO', 'No', 'no'])) {
							$internship_status = 'Rejected by Company';
							$internship_date = '-';
							$internship_period = '-';
						} else
					
						if (in_array($internship['status'], ['WAITING', 'Waiting', 'waiting'])) {
							$internship_status = 'Currently Submitting';
					
							$internship_date = '-';
							$internship_period = '-';
						}

						else
					
						if (in_array($internship['status'], ['FINISH', 'Finish', 'finish'])) {
							$internship_status = 'Finish';
							$internship_status = 'Internship Finished';
							$internship_period = range_time_elapsed_string($internship['start_date'], $internship['end_date']);
							$internship_date = date('d-M-Y', strtotime($internship['start_date'])) . " until " . date('d-M-Y', strtotime($internship['end_date']));
						
							$internship_date .= ' (' .$internship_period. ')';
						} else {
							$internship_status = 'Unknown';
							// $internship_period = range_time_elapsed_string($internship['start_date'], $internship['end_date']);
							// $internship_date = date('d-M-Y', strtotime($internship['start_date'])) . " until " . date('d-M-Y', strtotime($internship['end_date']));
						
							// $internship_date .= ' (' .$internship_period. ')';

							$internship_date = 'Unknown';
							$internship_period = 'Unknown';

						}
					

					?>

						<tr>
							<td><?= $i + 1 ?>.</td>
							<td><?= date('d-M-Y', strtotime($intern_data['timestamp_register'])) ?></td>
							<td><?= $intern_data['name'] ?></td>
							<td><?= $internship_status ?></td>
							<!-- <td><?= $intern_data['status'] ?></td> -->

							
							<td><?= date('d-M-Y', strtotime($intern_data['start_date'])) ?></td>
							<td><?= date('d-M-Y', strtotime($intern_data['end_date'])) ?></td>

							<td>

								<button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									Action
								</button>
								<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">

									<button class="dropdown-item" onclick="window.location.href = 'index.php?page=internship_detail_intern_co&nim=<?= base64_encode($nim) ?>&id_internship=<?= base64_encode($intern_data['id_internship']) ?> <?= ($coor == 1) ? '&coor=1' : ''; ?>'"><i class="fas fa-info-circle mr-1"></i>Internship Detail</button>
									<div class="dropdown-divider"></div>
									
									<button class="dropdown-item" onclick="window.location.href='index.php?page=edit_internship&nim=<?= base64_encode($intern_data['nim']) ?>&internship=<?= base64_encode($intern_data['id_internship']) ?>'"><i class="fas fa-edit mr-1"></i>Edit Data</button>
								</div>
							</td>
						</tr>


					<?php endforeach; ?>

				</tbody>


			</table>


		</div>
	</div>
</div>