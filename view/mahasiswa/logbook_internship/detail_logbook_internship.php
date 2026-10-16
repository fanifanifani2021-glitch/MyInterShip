<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php 
error_reporting( false ); session_start();

$title = "Detail Logbook Internship"; $route = 'my_internship'; 
include "./view/mahasiswa/template/head.php";

?>

<body>
	<div class="wrapper">

        <?php include "./view/mahasiswa/template/main-header.php";?>
        <?php include "./view/mahasiswa/template/sidebar.php";?>
		
		<div class="main-panel">
			<div class="container">
				<div class="page-inner">
					<div class="page-header">
						<h4 class="page-title">Detail Logbook</h4>
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
								<a href="index.php?page=my_internship">My Internship</a>
							</li>


							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="index.php?page=internship_detail&id_internship=<?= base64_encode( $id_internship ) ?>">Internship Detail</a>
							</li>


							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="index.php?page=logbook_internship&id_internship=<?= base64_encode( $id_internship ) ?>">Logbook Internship</a>
							</li>


							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="#">Detail Logbook</a>
							</li>


						</ul>
					</div>



                    <div class="row">


						<!-- Internship Under Supervisor -->
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<div class="card-title">Detail Logbook

									<div class="float-right justify-content-beetwen">

											<button onclick="window.location.href='index.php?page=print_logbook_internship&id_logbook=<?= base64_encode($id_logbook) ?>'" class="btn btn-info btn-sm mr-2"><i class="fas fa-download mr-2"></i>Download</button>
										</div>
                                    </div>
								</div>
								
							
								<div class="card-body form-row">


									<!-- <div class="form-group col-md-6">
										<label>NIM </label>
										<input type="text" class="form-control" value="<?= $data_mhs['nim']?>" readonly />
									</div>

									<div class="form-group col-md-6">
										<label>Name </label>
										<input type="text" class="form-control" placeholder="Enter Your Full Name" value="<?= $data_mhs['name']?>" readonly>											
									</div>

										<div class="form-group col-md-6">
											
												<label>E-Mail </label>
												<input type="email" class="form-control" placeholder="Enter Your E-Mail" value="<?= $data_mhs['email']?>" readonly>

											
										</div>


										<div class="form-group col-md-6">
											
												<label>Other E-Mail </label>
												<input type="email" class="form-control" placeholder="Enter Your Other E-Mail" value="<?= $data_mhs['other_email']?>" readonly>
										</div>


										<div class="form-group col-md-6">
											
												<label>No WhatsApp </label>
												<input type="tel" class="form-control" name="no_whatsapp" placeholder="Whatsapp Number" value="<?= $data_mhs['no_whatsapp']?>" readonly>
												<?= form_error('no_whatsapp') ?>
											
										</div>

										<div class="form-group col-md-6">
											
												<label>Phone Number </label>
												<input type="text" class="form-control" name="phone" placeholder="Phone" value="<?= $data_mhs['phone']?>" readonly>
											
										</div>


									<div class="form-group col-md-12 separator-solid"></div> -->


									<div class="form-group col-md-4">
										<label>Week Num </label>

										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text" id="basic-addon1">M</span>
											</div>
											<input type="number" min="0" name="week_num" class="form-control" value="<?= $data['week_num']?>" />
										</div>
									</div>


									<div class="form-group col-md-4">
										<label>Start Date </label>
										<input type="date" name="start_date" class="form-control" value="<?= $data['start_date']?>" />
									</div>

									<div class="form-group col-md-4">
										<label>End Date </label>
										<input type="date" name="end_date" class="form-control" value="<?= $data['end_date']?>" />
									</div>


									<div class="form-group col-md-12">
										<label class="label-control">Description </label>
										<div class="form-control"><?= $data['description']?></div>

									</div>

									<div class="form-group col-md-12">
										<label class="label-control">Documentation </label>
										<div class="form-control"><?= $data['documentation']?></div>

									</div>


									




								</div>


					
							</div>
						</div>



					</div>


				</div>
			</div>
			<?php include './view/mahasiswa/template/footer.php';?>
		</div>
		
	</div>

	<?php include './view/mahasiswa/template/min-script.php';?>

	<link rel='stylesheet' href='./assets/sweetalert2.css'>
    <script src='./assets/sweetalert2.all.js'></script>
	<!-- Soloalert -->
	<script src='./core/component/soloalert.js'></script>

	<script> 

		// CKEDITOR Initialize
		$(function() {



			// CKEDITOR
			var description = CKEDITOR.replace('description', {
				language: 'en',
				extraPlugins: 'editorplaceholder',
				editorplaceholder: 'Describe all job you`ve done this week',
				removeButtons: 'PasteFromWord',
			});

			

			var documentation = CKEDITOR.replace( 'documentation', {
				language: 'en',
				extraPlugins: 'editorplaceholder',
				editorplaceholder: 'Provide some photos to prove your job',
				removeButtons: 'PasteFromWord'
			});






		} );


	</script>






</body>
</html>