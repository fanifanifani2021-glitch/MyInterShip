<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php 
error_reporting( false ); session_start();

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
						<h4 class="page-title">Logbook Internship</h4>
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
								<a href="#">Add Logbook Internship</a>
							</li>


						</ul>
					</div>



                    <div class="row">


						<!-- Internship Under Supervisor -->
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<div class="card-title">Add Logbook Internship Data
                                    </div>
								</div>
								<form action="index.php?form=logbook&action=add" id="form" method="POST" enctype="multipart/form-data" id="form_register">
									<?= csrf(); ?> <?= input_hidden('id_internship', $id_internship )?> <?= input_hidden('nim', $_SESSION['nim'] )?>
								
								<!-- Student's Data -->
								<div class="card-body form-row">


									<div class="form-group col-md-4">
										<label>Week Num <b class="text-danger">*</b></label>

										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text" id="basic-addon1">M</span>
											</div>
											<input type="number" min="0" name="week_num" class="form-control <?= show_error('week_num'); ?>" value="<?= old('week_num', $last_week  ) ?>" />
										</div>
										<?= form_error('week_num'); ?>
									</div>


									<div class="form-group col-md-4">
										<label>Start Date <b class="text-danger">*</b></label>
										<input type="date" name="start_date" class="form-control <?= show_error('start_date'); ?>" />
										<?= form_error('start_date'); ?>
									</div>

									<div class="form-group col-md-4">
										<label>End Date <b class="text-danger">*</b></label>
										<input type="date" name="end_date" class="form-control <?= show_error('end_date'); ?>" />
										<?= form_error('end_date'); ?>
									</div>


									<div class="form-group col-md-12">
										<label class="label-control">Description <b class="text-danger">*</b></label>
										<textarea class="form-control <?= show_error('description')?>" id="description" name="description" 
										rows="5" placeholder="Describe all job you`ve done this week"></textarea>
										<?= form_error('description'); ?>

									</div>

									<div class="form-group col-md-12">
										<label class="label-control">Documentation <b class="text-danger">*</b></label>
										<textarea class="form-control <?= show_error('documentation')?>" id="documentation" name="documentation" 
										rows="5" placeholder="Provide some photos to prove your job"></textarea>
										<?= form_error('documentation'); ?>
									</div>






								</div>

                                <div class="card-footer">
                                    <button type="button" class="btn btn-danger" onclick="confirmation()"><i class="fas fa-times mr-1"></i>Cancel</button>
                                    <button type="button" class="btn btn-warning" onclick="reset_confirmation()" ><i class="fas fa-sync mr-1"></i>Reset</button>
                                    <button type="submit" class="btn btn-success" id="submit_btn"><i class="fas fa-save mr-1"></i>Save</button>

                                </div>
                                </form>
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
			var description = CKEDITOR.replace('1description', {
				language: 'en',
				extraPlugins: 'editorplaceholder',
				editorplaceholder: 'Describe all job you`ve done this week',
				removeButtons : 'PasteFromWord,Styles,Format,Font,FontSize',
			});

			

			var documentation = CKEDITOR.replace( '1documentation', {
				language: 'en',
				extraPlugins: 'editorplaceholder',
				editorplaceholder: 'Provide some photos to prove your job',
				removeButtons : 'PasteFromWord,Styles,Format,Font,FontSize',
			});






		} );

// 		$(document).ready( function(){

// 			setTimeout( function() {
			
// 			$('#cke_description').addClass('<?= show_error('description')?>');
// 			$('#cke_documentation').addClass('<?= show_error('documentation')?>');

// 			}, 500);
// 		})

	</script>


    <!-- User Custom Script -->
    <script>

        function confirmation() {
                // alert('Are you sure you want to cancel ? (Your change will not be saved)');
                SoloAlert.confirm({
                    title:"Are you sure you want to cancel ?",
                    body: "Changes you made will not be saved !",
                    useTransparency: true,
                    onOk : ()=>{ window.location.href = "index.php?page=logbook_internship&id_internship=<?= base64_encode($id_internship)?>"; }
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

            $("#form").trigger("reset");

            $("small").empty();

			$(".form-control").removeClass('is-invalid border-success border-danger is-valid');
			$('#cke_description').removeClass('<?= show_error('description')?>');
			$('#cke_documentation').removeClass('<?= show_error('documentation')?>');

        };



    </script>




</body>
</html>