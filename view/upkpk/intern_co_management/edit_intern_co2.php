<!DOCTYPE html>
<html lang="en">

<?php 
error_reporting( false ); session_start();

$title = "Edit Supervisor"; $route = 'intern_co_management'; 
include "./view/upkpk/template/head.php";

?>


<?php

$nik_edit = base64_decode($_GET['nik']);

// Data NIK
$sql = $_SERVER['KONEKSI_DB']->query("SELECT * FROM lecturer WHERE nim_nik_unit = '$nik_edit'");
$data_lecturer = $sql->fetch_object();

if ( $sql->num_rows != 1 ) {
    make_soloalert('error', 'Edit Data Failed', 'Lecturer data is not found !');
    redirect_back(1); exit;
}

?>


<body>
	<div class="wrapper">


        <?php include "./view/upkpk/template/main-header.php";?>
        <?php include "./view/upkpk/template/sidebar.php";?>


		<div class="main-panel">
			<div class="container">
				<div class="page-inner">


                    <div class="page-header">
						<h4 class="page-title">Edit Internship Coordinator</h4>
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
								<a href="index.php?page=intern_co_management">Internship Coordinator Management</a>
							</li>

                            <li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="#">Edit Internship Coordinator</a>
							</li>
						</ul>
					</div>


					<div class="row">

                        <div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<h4 class="card-title">Form Edit Intern Coordinator</h4>
								</div>

                                <form action="index.php?form=intern_co&action=edit" method="post" id="form_add_lecturer"><?= csrf(); ?> <?= input_hidden( 'old_nik', $data_lecturer->nim_nik_unit ) ?>
								<div class="card-body form-row">

                                    <div class="form-group col-md-12">
                                        <label>Lecturer's NIK <b class="text-danger">*</b></label>
                                        <div class="input-group">
											<input type="text" class="form-control <?= show_error('lecturer_nik') ?>" name="lecturer_nik" id="lecturer_nik" 
                                                value="<?= old('lecturer_nik', $data_lecturer->nim_nik_unit ) ?>" 
                                                onkeyup="search_lecturer( this.value )" placeholder="Enter Lecturer's NIK"/>
                                            <div class="input-group-append">
											    <button class="btn btn-black btn-border" type="button" onclick="search_lecturer( document.getElementById('lecturer_nik').value )"><i class="fas fa-search mr-1"></i>Click Me !</button>
											</div>
                                        </div>
                                        <?= form_error('lecturer_nik') ?>

                                    </div>

                                    <div class="form-group col-md-12" hidden id="lecturer_notification">
                                        <label class="alert alert-warning">Lecturer is not found !</label>
                                    </div>

                                    <div class="form-group col-md-12" id="lecturer_waiting" hidden>
                                        <label class="alert alert-info"><i class="fas fa-spin fa-spinner mr-1"></i>Please wait while system is searching for the lecturer !</label>
                                    </div>

                                    <div class="form-group col-md-12" hidden>
                                        <label>Search Status <b class="text-danger">*</b></label>
                                        <input type="text" class="form-control" name="search_status" id="search_status" value="Failed" />

                                        <?= form_error('search_status') ?>

                                    </div>

             


                                    <div class="form-group col-md-6 search-result" hidden>
                                        <label>Lecturer's Name <b class="text-danger">*</b></label>
                                        <input type="text" class="form-control" name="lecturer_name" id="lecturer_name" readonly/>


                                    </div>

                                    <div class="form-group col-md-6 search-result" hidden>
                                        <label>Lecturer E-Mail <b class="text-danger">*</b></label>
                                        <input type="text" class="form-control" name="lecturer_email" id="lecturer_email" readonly/>


                                    </div>



                                    <div class="form-group col-md-12">
                                        <label>Program Study <b class="text-danger">*</b></label>
                                        
                                        
										<select id="program_study" name="program_study" class="form-control">
                                            <option value="" hidden>Choose one . . .</option>

                                            
                                            <?php

                                                $sql = "SELECT DISTINCT(program_study.major) FROM program_study ORDER BY program_study.major";
                                                $exec = $_SERVER['KONEKSI_DB']->query($sql);

                                                foreach ($exec as $data_jurusan) :
                                                    $jurusan = $data_jurusan['major'];
                                                ?>

                                                    <optgroup label="<?= $jurusan ?>">


                                                        <?php

                                                        $sql2 = "SELECT program_study.* FROM program_study WHERE program_study.major = '$jurusan' ORDER BY program_study.jenjang";
                                                        $exec2 = $_SERVER['KONEKSI_DB']->query($sql2);

                                                        foreach ($exec2 as $data_prodi) :
                                                        ?>

                                                            <option value="<?= $data_prodi['kode_prodi'] ?>"><?= $data_prodi['study_program'] ?></option>

                                                        <?php endforeach; ?>



                                                    </optgroup>
                                                    <optgroup></optgroup>


                                            <?php endforeach; ?>
                                        </select>

                                        <?= form_error('program_study') ?>

                                    </div>


                                    <div class="form-group col-md-6">
                                        <label>User Type <b class="text-danger">*</b></label>
										<select id="user_type" name="user_type" class="form-control">
                                            <option value="" hidden>Choose one . . .</option>
                                            <option>Koor</option>
                                            <!-- <option>Pembimbing</option> -->
                                        </select>
                                        <?= form_error('user_type') ?>
                                    </div>
                                    
                                    <div class="form-group col-md-6">
                                        <label>Status <b class="text-danger">*</b></label>
                                        
                                        <div class="select2-input">
											<select id="lecturer_status" name="lecturer_status" class="form-control">
                                                <option>Active</option>
												<option>Not Active</option>
                                            </select>
										</div>

                                        <?= form_error('lecturer_status') ?>

                                    </div>


								</div>
                                <div class="card-footer">
                                    <button type="button" class="btn btn-danger" onclick="confirmation()"><i class="fas fa-times mr-1"></i>Cancel</button>
                                    <button type="button" class="btn btn-warning" onclick="reset_confirmation()" ><i class="fas fa-sync mr-1"></i>Reset</button>
                                    <button type="submit" class="btn btn-success" id="submit_btn"><i class="fas fa-edit mr-1"></i>Edit</button>

                                </div>
                                </form>


							</div>
						</div>


					</div>
				</div>
			</div>
            
            <?php include "./view/upkpk/template/footer.php";?>

		</div>

	</div>
	
	<?php include './view/upkpk/template/script.php';?>

	<!-- Select2 -->
	<script src="./assets/js/plugin/select2/select2.full.min.js"></script>

    <!-- Soloalert -->
	<script src='./core/component/soloalert.js'></script>

    <!-- AJAX Script -->
    <script>

        $(document).ready(function() { 
            search_lecturer( $('#lecturer_nik').val() );
            $("#program_study").val("<?= old('program_study', $data_lecturer->program_study )?>");
            $("#program_study").trigger('change');


            $("#lecturer_status").val("<?php if ( old('lecturer_status') !== NULL ) : echo old('lecturer_status'); else : echo $data_lecturer->status; endif; ?>");
            $("#lecturer_status").trigger('change');
            
            $("#user_type").val("<?php if ( old('user_type') !== NULL ) : echo old('user_type'); else : echo $data_lecturer->user_type; endif; ?>");
            $("#user_type").trigger('change');

            
            $("[aria-labelledby='select2-program_study-container']").addClass('<?= show_error('program_study') ?>'); 
            $("[aria-labelledby='select2-user_type-container']").addClass('<?= show_error('user_type') ?>'); 
            $("[aria-labelledby='select2-lecturer_status-container']").addClass('<?= show_error('lecturer_status') ?>'); 

        
            
        });

        function search_lecturer( nik ) {

            var submit_btn = document.getElementById('submit_btn');
            var lecturer_waiting = document.getElementById('lecturer_waiting');
            var lecturer_notification = document.getElementById('lecturer_notification');
            var search_result = document.getElementsByClassName('search-result'); 
            var search_status = document.getElementById('search_status');
            
            search_status.value = 'Failed';
            submit_btn.disabled = true;

            if ( nik.length >= 6 ) {
                $('#lecturer_nik').val($('#lecturer_nik').val().substr(0, 6));
            }

            if ( nik == "" || nik.length < 6 ) {

                for( z = 0; z <= search_result.length-1; z++ ) {
                    search_result[z].setAttribute('hidden' , '');
                    search_result[z].removeAttribute('value');
                }

                lecturer_waiting.hidden = true;
                lecturer_notification.hidden = false;
                search_status.value = 'Failed';
            }

            if ( nik.length == 6 ) {

                lecturer_waiting.hidden = false;
                lecturer_notification.hidden = true;

                $.ajax({

                    url : './ajax/getDataAPIPolbat.php',
                    type: 'post',
                    data: {'nim_nik_unit' : nik},
                    dataType: 'json',


                    success: function(response){


                        var len = response.length;
                        if( response[0]['hasil_pencarian'] == "Berhasil" ){

                            submit_btn.disabled = false;

                            lecturer_waiting.hidden = true;
                            lecturer_notification.hidden = true;


                            search_status.value = 'Success';

                            document.getElementById('lecturer_name').value = response[0]['name'];
                            document.getElementById('lecturer_email').value = response[0]['email'];

                            for( z = 0; z <= search_result.length-1; z++ ) {
                                console.log(search_result);
                                search_result[z].removeAttribute('hidden');
                            }
                        
                        } else {

                            submit_btn.disabled = true;

                            lecturer_waiting.hidden = true;
                            lecturer_notification.hidden = false;


                            search_status.value = 'Failed';

                            for( z = 0; z <= search_result.length-1; z++ ) {
                                search_result[z].setAttribute('hidden' , true);
                                search_result[z].removeAttribute('value');
                            }
                        }
                    },

                    error: function(response){
                        lecturer_waiting.hidden = true;
                        lecturer_notification.hidden = false;

                        search_status.value = 'Failed';

                        for( z = 0; z <= search_result.length-1; z++ ) {
                            search_result[z].setAttribute('hidden' , true);
                            search_result[z].removeAttribute('value');
                        }
                    }
                
                });





        }}
    </script>


    <!-- User Custom Script -->
    <script>

        function confirmation() {
                // alert('Are you sure you want to cancel ? (Your change will not be saved)');
                SoloAlert.confirm({
                    title:"Are you sure you want to cancel ?",
                    body: "Changes you made will not be saved !",
                    useTransparency: true,
                    onOk : ()=>{ window.location.href = "index.php?page=intern_co_management"; }
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

            $("#form_add_lecturer").trigger("reset");

            $("small").empty();
        };



    </script>



    <!-- Page Sript Dependencies -->
	<script>

        $('#lecturer_status').select2({
			theme: "bootstrap"
		});

        $('#user_type').select2({
			theme: "bootstrap"
		});

        $('#program_study').select2({
			theme: "bootstrap"
		});


	</script>
</body>
</html>