<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

?>

<?php
$coor = $_GET['coor'];
$nim = base64_decode($_GET['nim']);

$sql = "SELECT student_internship.*, program_study.* FROM student_internship LEFT JOIN program_study ON program_study.kode_prodi = student_internship.program_study WHERE student_internship.nim = '$nim' ";
$sql = $_SERVER['KONEKSI_DB']->query($sql);

if ($sql->num_rows != 1) {
	make_soloalert('error', 'Edit Data Failed', 'Student data is not found !');
	redirect_back(1);
	exit;
}

$data_student = $sql->fetch_assoc();

// Get data dospem


if ( in_array($data_student['nik_dospem'], [NULL, '', '-']) ) {
	$dospem = 'Not set';
} else {
	$sql_dospem = "SELECT * FROM lecturer WHERE nim_nik_unit = :nik_dospem LIMIT 1";

	$stmt = $_SERVER['PDO']->prepare( $sql_dospem );

	$stmt->execute( [':nik_dospem' => $data_student['nik_dospem'] ] );


	if ( $stmt->rowCount() != 1 ) {
		$dospem = 'Lecturer not found';
	} else {
		$data_lecturer = $stmt->fetch( PDO::FETCH_ASSOC );

		$dospem = $data_lecturer['nim_nik_unit']. ' - ' .$data_lecturer['name'];
	}

}



if ($coor == 1) :
	$prodi = $data_student['program_study'];

	$sql_lecturer = "SELECT * FROM lecturer WHERE is_koor = 1 AND prodi_koor = '$prodi'";
	$sql_lecturer = $_SERVER['KONEKSI_DB']->query($sql_lecturer);
	$data_lecturer = $sql_lecturer->fetch_assoc();

endif;

$id_kampus = $data_student['id_kampus'];
$sql_kampus = "SELECT * FROM tb_kampus WHERE id_kampus = '$id_kampus'";
$university = $_SERVER['KONEKSI_DB']->query( $sql_kampus )->fetch_assoc();

?>

<?php


$title = "Student Detail";

if ($coor == 1) {
	$route = 'intern_co_management';
} else {
	$route = 'intern_student_list';
}
include "./view/upkpk/template/head.php";


?>

<body>
	<div class="wrapper">

		<?php include "./view/upkpk/template/main-header.php"; ?>
		<?php include "./view/upkpk/template/sidebar.php"; ?>

		<div class="main-panel">
			<div class="container">
				<div class="page-inner">


					<div class="page-header">
						<h4 class="page-title">Student Detail</h4>
						<ul class="breadcrumbs">
							<li class="nav-home">
								<a href="index.php">
									<i class="flaticon-home"></i>
								</a>
							</li>


							<?php if ($coor == 1) : ?>

								<li class="separator">
									<i class="flaticon-right-arrow"></i>
								</li>
								<li class="nav-item">
									<a href="index.php?page=intern_co_management">Coordinator Management</a>
								</li>


								<li class="separator">
									<i class="flaticon-right-arrow"></i>
								</li>

								<li class="nav-item">
									<a href="<?= base_url('index.php?page=detail_intern_co&nik=' . base64_encode($data_lecturer['nim_nik_unit'])) ?>">Coordinator Detail</a>
								</li>

							<?php else : ?>
							
								<li class="separator">
									<i class="flaticon-right-arrow"></i>
								</li>
								<li class="nav-item">
									<a href="index.php?page=intern_student_list">Student Management</a>
								</li>

							<?php endif; ?>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="#">Student Detail</a>
							</li>
						</ul>
					</div>

					<div class="row">


						<!-- Lecturer's Profile -->
						<div class="col-md-12">
							<div class="card card-profile">
								<div class="card-header" style="background-image: url('viewer.php?src=profile_background'); height: 300px; background-size: cover;background-repeat: no-repeat;">

									<div class="profile-picture">
										<div class="avatar avatar-xxl">
											<img src="viewer.php?src=get_profile&location=<?= base64_encode($data_student['profile_picture']) ?>" alt="..." class="avatar-img rounded-circle">
										</div>
									</div>
								</div>

								<div class="card-body">
									<div class="user-profile text-center">
										<div class="name"><?= $data_student['name'] ?></div>
										<div class="job">Student at <?= $university['nama_kampus'] ?? 'Politeknik Negeri Batam' ?></div>
										<div class="job"><?= $data_student['jenjang'] ?> <?= $data_student['study_program'] ?> </div>
										<!-- <div class="job"><?= $data_student['jenjang'] ?> <?= $data_student['study_program'] ?> (<?= $data_student['kode_prodi'] ?? 'IF' ?>)</div> -->

									</div>

									<!-- GET DATA LECTURER DAN LECTURER PROFILE -->

									<div class="row mt-3">
										<div class="col-md-6">
											<div class="form-group form-group-default">
												<label>Name</label>
												<input type="text" class="form-control" name="name" value="<?= $data_student['name'] ?>">
												<?= form_error('name') ?>

											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group form-group-default">
												<label>E-Mail</label>
												<input type="email" class="form-control" name="polibatam_email" value="<?= $data_student['email'] ?? '-' ?>">
												<?= form_error('polibatam_email') ?>

											</div>
										</div>

									</div>

									<div class="row mt-3">

										<div class="col-md-6">
											<div class="form-group form-group-default">
												<label>Other E-Mail </label>
												<input type="email" class="form-control" name="other_email" value="<?= $data_student['other_email'] ?? '-' ?>">
												<?= form_error('other_email') ?>

											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group form-group-default">
												<label>Phone Number </label>
												<input type="text" class="form-control" name="phone" value="<?= $data_student['phone'] ?? '-' ?>">
												<?= form_error('phone') ?>

											</div>
										</div>
									</div>


									<div class="row mt-3">

										<div class="col-md-12">
											<div class="form-group form-group-default">
												<label>University Supervisor </label>
												<input type="text" class="form-control" name="other_email" value="<?= $dospem ?? 'Not Set' ?>">

											</div>
										</div>

									</div>


								</div>

							</div>
						</div>

						<!-- List Dosen Pembimbing -->
						<div class="col-md-12">
							<div id="filter_result"></div>
							<div id="filter_result2"></div>
						</div>






					</div>
				</div>
			</div>
			<?php include './view/upkpk/template/footer.php'; ?>
		</div>

	</div>
	<?php include './view/upkpk/template/script.php'; ?>

	<script src="./assets/js/plugin/datatables/datatables.min.js"></script>

	
	<script>
		$(document).ready(function() {
			show_data();
		});
	</script>


	<script type='text/javascript'>
		function show_data() {

			let spinner = "<i class='fas fa-spinner fa-spin mr-1 fa-2x'></i> <span>Searching . . .</span>";
			$('#filter_result2').html(spinner);



			$.ajax({
				type: 'POST',
				url: 'index.php?form=filter_internship_student_list',
				data: {
					'coor': '<?= $coor ?? 0 ?>',
					'major': 'All',
					'nim': '<?= $nim ?>',
					'token': '<?= $_SESSION['token'] ?>',
				},
				success: function(data, textStatus, XHR) {
					console.log("berhasil");

					$('#filter_result2').html('');
					$('#filter_result').html(data);

					$('#basic-datatables').DataTable({});
				},
				error: function(data) {
					console.log(data);
					$('#filter_result2').html('');
					$('#filter_result').html('Failed to fetch student data !');
				},
			});


		}
	</script>




</body>

</html>