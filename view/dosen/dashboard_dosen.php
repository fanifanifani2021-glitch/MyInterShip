<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = 'Dashboard Dosen';
$route = 'dashboard';
include "./view/dosen/template/head.php";

?>





<?php

$id_kampus = $_SESSION['Lecturer_Data'] ['id_kampus'];

// Mengganti penggunaan $_SERVER['KONEKSI_DB']->query() dengan PDO
$sql_kampus = "SELECT * FROM tb_kampus WHERE id_kampus = :id_kampus";
$exec_kampus = $_SERVER['PDO']->prepare($sql_kampus);
$exec_kampus->bindParam(':id_kampus', $id_kampus);
$exec_kampus->execute();
$data_kampus = $exec_kampus->fetch(PDO::FETCH_ASSOC);


// Menghitung jumlah baris menggunakan PDO
$count_university = $_SERVER['PDO']->query("SELECT COUNT(id_kampus) AS Count_Kampus FROM tb_kampus")->fetch(PDO::FETCH_ASSOC)['Count_Kampus'];


$count_cdc = $_SERVER['PDO']->query("SELECT COUNT(id_upkpk) AS Count_upkpk FROM upkpk")->fetch(PDO::FETCH_ASSOC)['Count_upkpk'];

$count_student = $_SERVER['PDO']->prepare("SELECT COUNT(*) AS Count_Student FROM student_internship WHERE id_kampus = :id_kampus");
$count_student->bindParam(':id_kampus', $id_kampus);

$count_student->execute();
$count_student = $count_student->fetch(PDO::FETCH_ASSOC)['Count_Student'];



$count_lecturer = $_SERVER['PDO']->prepare("SELECT COUNT(*) AS Count_Lecturer FROM lecturer WHERE id_kampus = :id_kampus");
$count_lecturer->bindParam(':id_kampus', $id_kampus);
$count_lecturer->execute();
$count_lecturer = $count_lecturer->fetch(PDO::FETCH_ASSOC)['Count_Lecturer'];

$count_prodi = $_SERVER['PDO']->prepare("SELECT COUNT(*) AS Count_Prodi FROM program_study WHERE id_kampus = :id_kampus");
$count_prodi->bindParam(':id_kampus', $id_kampus);
$count_prodi->execute();
$count_prodi = $count_prodi->fetch(PDO::FETCH_ASSOC)['Count_Prodi'];

$count_total_company = $_SERVER['PDO']->query("SELECT COUNT(*) AS Count_Total_Company FROM company")->fetch(PDO::FETCH_ASSOC)['Count_Total_Company'];

$count_company = $_SERVER['PDO']->query("SELECT COUNT(*) AS Count_Company FROM company WHERE status NOT IN ('Not Verified', 'Blacklist')")->fetch(PDO::FETCH_ASSOC)['Count_Company'];

$count_blacklist_company = $_SERVER['PDO']->query("SELECT COUNT(*) AS Count_Blacklist_Company FROM company WHERE status IN ('Blacklist')")->fetch(PDO::FETCH_ASSOC)['Count_Blacklist_Company'];

$count_new_company = $_SERVER['PDO']->query("SELECT COUNT(*) AS Count_New_Company FROM company WHERE status IN ('Not Verified')")->fetch(PDO::FETCH_ASSOC)['Count_New_Company'];
?>

<body>
	<div class="wrapper">

		<?php include "./view/dosen/template/main-header.php"; ?>
		<?php include "./view/dosen/template/sidebar.php"; ?>


		<div class="main-panel">
			<div class="container">

				<div class="panel-header bg-primary-gradient">
					<div class="page-inner py-5">
						<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
							<div>
								<h1 class="text-white pb-2 fw-bold">Welcome To My Internship</h1>
								<h4 class="text-white op-7 mb-2">Managing Industrial Learning through Structured Internship</h4>
							</div>
						</div>
					</div>
				</div>

				


				<div class="page-inner mt--5">
					<div class="row mt--2">

					<div class="col-md-6">
							<div class="card full-height">
								<div class="card-body">
									<div class="card-title">My Internship Statistics</div>
									<div class="card-category">Daily information about statistics in system</div>
									<div class="d-flex flex-wrap justify-content-around pb-2 pt-4">
										<div class="px-2 pb-2 pb-md-0 text-center">
											<div id="circles-0"></div>
											<h6 class="fw-bold mt-3 mb-0">University</h6>
										</div>
										<div class="px-2 pb-2 pb-md-0 text-center">
											<div id="circles-1"></div>
											<h6 class="fw-bold mt-3 mb-0">Company</h6>
										</div>

										<div class="px-2 pb-2 pb-md-0 text-center">
											<div id="circles-5"></div>
											<h6 class="fw-bold mt-3 mb-0">CDC User</h6>
										</div>
										
									</div>

								</div>
							</div>
						</div>

						<!-- Politeknik Statistik -->
						<div class="col-md-6">
							<div class="card full-height">
								<div class="card-body">
									<div class="card-title"><?= $data_kampus['nama_kampus'] ?> statistics</div>
									<div class="card-category">Daily information about your university statistics in system</div>
									<div class="d-flex flex-wrap justify-content-around pb-2 pt-4">
										
										<div class="px-2 pb-2 pb-md-0 text-center">
											<div id="circles-2"></div>
											<h6 class="fw-bold mt-3 mb-0">Lecturer</h6>
										</div>
										<div class="px-2 pb-2 pb-md-0 text-center">
											<div id="circles-3"></div>
											<h6 class="fw-bold mt-3 mb-0">Student</h6>
										</div>

										<div class="px-2 pb-2 pb-md-0 text-center">
											<div id="circles-4"></div>
											<h6 class="fw-bold mt-3 mb-0">Study Program</h6>
										</div>
									</div>

								</div>
							</div>
						</div>


						<div class="col-md-12">
							<div class="card full-height">
								<div class="card-body">
									<div class="card-title">Manual Guide Application</div>
									<div class="card-category">Download Manual Guide</div>
									<div class="d-flex flex-wrap justify-content-around pb-2 pt-4">
										<div class="px-2 pb-2 pb-md-0 text-center">
											<h4 class="fw-bold mt-3 mb-0 text-primary">
												<a style="text-decoration: none;" title="Download Manual Guide for UPKPK" aria-label="Download Manual Guide for UPKPK" 
												href="viewer.php?src=manual_guide" download="" target="_blank"> <i class="fas fa-download"></i> Download Manual Guide </a>
											</h4>
										</div>
									</div>
								</div>
							</div>
						</div>


						<!-- <div class="col-md-6">
							<div class="card full-height">
								<div class="card-body">
									<div class="card-title">Manual Guide Application</div>
									<div class="card-category">Download Manual Guide</div>
									<div class="d-flex flex-wrap justify-content-around pb-2 pt-4">
										<div class="px-2 pb-2 pb-md-0 text-center">
											<h4 class="fw-bold mt-3 mb-0 text-primary">
												<a style="text-decoration: none;" title="Download Manual Guide for UPKPK" aria-label="Download Manual Guide for UPKPK" href="./system_requirement/manual_guide_upkpk.pdf" download="" target="_blank"> <i class="fas fa-download"></i> Download Manual Guide </a>
											</h4>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-6">
							<div class="card full-height">
								<div class="card-body">
									<div class="card-title">Tutorial Video Application</div>
									<div class="card-category">Watch Tutorial Video</div>
									<div class="d-flex flex-wrap justify-content-around pb-2 pt-4">
										<div class="px-2 pb-2 pb-md-0 text-center">
											<h4 class="fw-bold mt-3 mb-0 text-primary">
												<a style="text-decoration: none;" title="Watch Video Tutorial in Youtube" aria-label="Watch Video Tutorial in Youtube" href="https://youtube.com/" download="" target="_blank"> <i class="fab fa-youtube"></i> Watch Video Tutorial</a>
											</h4>
										</div>
									</div>
								</div>
							</div>
						</div> -->


					</div>
				</div>
			</div>

			<?php include "./view/dosen/template/footer.php"; ?>

		</div>

	</div>

	<?php include "./view/dosen/template/script.php"; ?>

	<!-- Chart Circle -->
	<script src="./assets/js/plugin/chart-circle/circles.min.js"></script>

	<script>
		Circles.create({
			id:'circles-0',
			radius:45,
			value: '<?= $count_university ?>' ,
			maxValue: '<?= $count_university ?>',
			width:7,
			text: '<?= $count_university ?>',
			colors:['#f1f1f1', '#FE5D26'],
			duration:400,
			wrpClass:'circles-wrp',
			textClass:'circles-text',
			styleWrapper:true,
			styleText:true
		})

		Circles.create({
			id:'circles-1',
			radius:45,
			value: '<?= $count_company ?>' ,
			maxValue: '<?= $count_company ?>',
			width:7,
			text: '<?= $count_company ?>',
			colors:['#f1f1f1', '#F2C078'],
			duration:400,
			wrpClass:'circles-wrp',
			textClass:'circles-text',
			styleWrapper:true,
			styleText:true
		})

		Circles.create({
			id:'circles-2',
			radius:45,
			value: '<?= $count_lecturer ?? 0 ?>',
			maxValue: '<?= $count_lecturer ?? 0 ?>',
			width:7,
			text: '<?= $count_lecturer ?? 0?>',
			colors:['#f1f1f1', '#FAEDCA'],
			duration:400,
			wrpClass:'circles-wrp',
			textClass:'circles-text',
			styleWrapper:true,
			styleText:true
		})

		Circles.create({
			id:'circles-3',
			radius:45,
			value: '<?= $count_student ?? 0?>',
			maxValue: '<?= $count_student ?? 0 ?>',
			width:7,
			text: '<?= $count_student ?? 0 ?>',
			colors:['#f1f1f1', '#c1dbb3'],
			duration:400,
			wrpClass:'circles-wrp',
			textClass:'circles-text',
			styleWrapper:true,
			styleText:true
		})


		Circles.create({
			id:'circles-4',
			radius:45,
			value: '<?= $count_prodi ?>',
			maxValue: '<?= $count_prodi ?>',
			width:7,
			text: '<?= $count_prodi ?>',
			colors:['#f1f1f1', '#6861CE'],
			duration:400,
			wrpClass:'circles-wrp',
			textClass:'circles-text',
			styleWrapper:true,
			styleText:true
		})

		Circles.create({
			id:'circles-5',
			radius:45,
			value: '<?= $count_cdc ?>',
			maxValue: '<?= $count_cdc ?>',
			width:7,
			text: '<?= $count_cdc ?>',
			colors:['#f1f1f1', '#7ebc89'],
			duration:400,
			wrpClass:'circles-wrp',
			textClass:'circles-text',
			styleWrapper:true,
			styleText:true
		})

	</script>


</body>

</html>