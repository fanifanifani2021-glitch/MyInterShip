<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting( false ); session_start();

$title = 'Final Report'; $route = 'student_intern_management';
include "./view/dosen/template/head.php";


$id_internship = base64_decode($_GET['id_internship']);

$sql = mysqli_query($koneksi_db, "SELECT * FROM internship WHERE id_internship = $id_internship");
$row = mysqli_fetch_assoc($sql);

$nim = $row['nim'];



?>

<body>
	<div class="wrapper">

		<?php include "./view/dosen/template/main-header.php"; ?>
		<?php include "./view/dosen/template/sidebar.php"; ?>


		<div class="main-panel">

			<div class="container">
				<div class="page-inner">

					<div class="page-header">
						<h4 class="page-title">Final Report</h4>
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
								<a href="index.php?page=student_intern_management">Students Management</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="index.php?page=student_intern_detail&nim=<?= base64_encode($nim) ?>">Student Detail</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="index.php?page=student_internship_detail&id_internship=<?= base64_encode($id_internship) ?>">Internship Detail</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="#">Final Report</a>
							</li>
						</ul>
					</div>


					<!-- Form -->
					<div class="row" id="feedback_form">

                        <div class="col-md-12">
							<div class="card full-height">
								<div class="card-header">
									<h4 class="card-title">Final Report</h4>
								</div>

								<div class="card-body form-row"> 
									
									<?php if ( in_array($row['final_report'], [NULL, '', '-']) ) {?>
									
									<button class="btn btn-warning">The student has not uploaded the final report file yet.</button>
										
									<?php } else {?>

                                    <div class="form-group col-md-12">
                                    <label class="label-control">Student Final Report</label>
                                    <div class="input-group mb-1">
                                        <input class="form-control" value="<?php if ($row['final_report'] == "-"){echo 'No Files';}else{ echo $row['final_report'];} ?>" readonly/>

                                        <div class="input-group-append">
                                            <a class="btn btn-outline-secondary" type="button" href="index.php?page=view_pdf&id=<?=$row['id_internship']?>&file=<?=$row['final_report']?>" target="_blank"><i class="fas fa-eye"></i></a>
                                        </div>
                                    </div>

									<small class="text-sm">Uploaded at <?= date( 'D, d M Y', strtotime($row['date_finalreport']))?></small>
									</div>

									<?php }?>

								</div>



							</div>
						</div>


					</div>


				</div>
			</div>

			<?php include "./view/dosen/template/footer.php"; ?>

		</div>
		
	</div>
	<?php include "./view/dosen/template/script.php"; ?>


	<!-- Soloalert -->
	<script src='./core/component/soloalert.js'></script>

	<!-- User Custom Script -->
	<script>

		function confirmation() {
				// alert('Are you sure you want to cancel ? (Your change will not be saved)');
				SoloAlert.confirm({
					title:"Are you sure you want to cancel ?",
					body: "Changes you made will not be saved !",
					useTransparency: true,
					onOk : ()=>{ window.location.href = "index.php"; }
				});
				
		}

		function reset_confirmation() {

				SoloAlert.confirm({
					title:"Are you sure you want to reset ?",
					body: "Changes you made will be reset to default !",
					useTransparency: true,
					onOk : ()=>{ form_reset(); }
				});
				
		}


		function form_reset () {

			$("#form_register").trigger("reset");
		};


</script>

</body>
</html>