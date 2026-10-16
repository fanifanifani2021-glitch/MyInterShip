<?php

$_SESSION['EXPORT']['LECTURER']['QUERY'] = '';

$id_kampus = $_POST['id_kampus'] ?? $_SESSION['CDC_Data']['id_kampus'];
$prodi_koor = $_POST['program_study'];

$sql = "SELECT * FROM lecturer WHERE id_kampus = '$id_kampus' AND nim_nik_unit IN ( SELECT  DISTINCT(nik_dospem) FROM student_internship WHERE program_study = '" . $prodi_koor . "' )";



$_SESSION['EXPORT']['LECTURER']['QUERY'] = $sql2;


?>

<div class="card">
	<div class="card-header">
		<div class="card-title">Lecturers List

			<?php if ($_POST['coor'] != 1) : ?>
				<button class="btn btn-danger btn-round btn-sm float-right mr-1 " type="button" onclick="close_filter()"><i class="fas fa-times mr-2"></i>Close</button>
			<?php endif; ?>
			<!-- <a type="button" role="button" class="btn btn-primary btn-round btn-sm float-right mr-1 text-white" href='index.php?page=export_lecturer' target='_blank'><i class="fas fa-file-excel mr-2"></i>Export</a> -->


		</div>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table id="basic-datatables2" class="display table table-striped table-hover">
				<thead>
					<tr>
						<th>No.</th>
						<th>NIK</th>
						<th>Name</th>
						<th>Status</th>
						<th>Total Student</th>
					</tr>
				</thead>

				<tbody>
					<?php
					$exec = $_SERVER['KONEKSI_DB']->query($sql);
					foreach ($exec as $i => $data) :

						$nik = $data['nim_nik_unit'];

						$total_mhs = $_SERVER['KONEKSI_DB']->query("SELECT COUNT( DISTINCT(nim) ) AS total_mhs FROM student_internship WHERE nik_dospem = '$nik' ")->fetch_assoc()['total_mhs'];


					?>

						<tr>
							<td><?= $i + 1 ?>.</td>
							<td><?= $data['nim_nik_unit'] ?></td>
							<td><?= $data['name'] ?></td>
							<td><?= $data['status'] ?></td>

							<!-- Hitung person nya -->
							<td><?= $total_mhs ?? '0'; ?> Person</td>
						</tr>

					<?php endforeach; ?>

				</tbody>

			</table>
		</div>
	</div>
</div>