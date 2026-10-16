<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = 'Competence Unit Manager'; $route = 'profile_jobdesc';
include "./view/dosen/template/head.php";

$id_profile = base64_decode( $_GET['id_profile'] );
$kode_prodi = $_SESSION['program_study'];

$sql = $_SERVER['KONEKSI_DB']->query("SELECT profile_jobdesc.* FROM profile_jobdesc WHERE profile_jobdesc.id_profile_jobdesc = '$id_profile' AND profile_jobdesc.kode_prodi = '$kode_prodi'");

if ( $sql->num_rows != 1 ) {
    make_swal2('error', 'Not Allowed', 'Graduation Profile data is not found !' );
    redirect_referrer(1); exit();
}

$job_profile = $sql->fetch_assoc();

$sql2 = $_SERVER['KONEKSI_DB']->query("SELECT profile_detail_comp.* FROM profile_detail_comp WHERE profile_detail_comp.id_profile_jobdesc = '$id_profile'");


?>

<body>
    <div class="wrapper">

        <?php include "./view/dosen/template/main-header.php"; ?>
        <?php include "./view/dosen/template/sidebar.php"; ?>


        <div class="main-panel">

            <div class="container">
                <div class="page-inner">

                    <div class="page-header">
                        <h4 class="page-title">Competence Unit Manager</h4>
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
                                <a href="index.php?page=profile_jobdesc">Job Profile</a>
                            </li>

                            <li class="separator">
                                <i class="flaticon-right-arrow"></i>
                            </li>
                            <li class="nav-item">
                                <a href="#">Competence Unit Manager</a>
                            </li>
                        </ul>
                    </div>


                    <!-- Form -->
                    <div class="row">

                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td width="30%">Job Profile</td>
                                            <td width="10%">:</td>
                                            <td width="60%"><?= $job_profile['nama_profile'] ?></td>
                                        </tr>

                                        <!-- <tr>
                                            <td>Study Program</td>
                                            <td>:</td>
                                            <td><?= $kode_prodi ?></td>
                                        </tr> -->
                                    </table>
                                </div>
                            </div>
                        </div>

                    

                        <!-- Jobdesc -->
                        <div class="col-md-12" id="focus_add">
                            <div class="card full-height">
                                <div class="card-header">
                                    <div class="card-title">Add Competence Unit
                                    </div>
                                </div>


                                <form action='index.php?form=competence_unit_dkm&action=add' method='POST' id="form_add"> <?= csrf() ?>
                                    <input type="hidden" name="id_profile_jobdesc" value="<?= $id_profile ?>">
                                    <div class="card-body form-row">

                                        <div class="form-group col-md-12">
                                            <label>Competence Unit <b class="text-danger">*</b></label>
                                            <input type="text" class="form-control <?= show_error('unit_kompetensi') ?>" name='unit_kompetensi' 
                                                placeholder='Input Competence Unit'></input>
                                            <?= form_error('unit_kompetensi') ?>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label>Competence Description <b class="text-danger">*</b></label>
                                            <textarea rows="5" type="text" class="form-control <?= show_error('deskripsi_unit') ?>" name='deskripsi_unit' 
                                                placeholder='Input Competence Unit' id="deskripsi_unit"></textarea>
                                            <?= form_error('deskripsi_unit') ?>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label>Subject Code <b class="text-danger">*</b></label>
                                            <input type="text" class="form-control <?= show_error('kode_matkul') ?>" name='kode_matkul' 
                                                placeholder='Input Subject Code'>

                                            <?= form_error('kode_matkul') ?>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label>Subject Name <b class="text-danger">*</b></label>
                                            <input type="text" class="form-control <?= show_error('nama_matkul') ?>" name='nama_matkul' 
                                                placeholder='Input Subject Name'>

                                            <?= form_error('nama_matkul') ?>
                                        </div>
                                    </div>

                                    <div class="card-footer justify-content-start">
                                        <button type="button" onclick="cancel_confirmation('index.php?page=profile_jobdesc')" class="btn btn-danger"><i class="fas fa-times mr-1"></i>Cancel</button>
                                        <button type="button" onclick="reset_confirmation('form_add')" class="btn btn-warning"><i class="fas fa-sync mr-1"></i>Reset</button>
                                        <button type="submit" class="btn btn-success"><i class="fas fa-save mr-1"></i>Save</button>
                                    </div>
                                </form>

                            </div>
                        </div>



                    </div>


                    <!-- Form -->
                    <div class="row">

                        <!-- Jobdesc -->
                        <div class="col-md-12">
                            <div class="card full-height">
                                <div class="card-header">
                                    <div class="card-title">Competence Unit
                                        <!-- <button data-toggle="modal" data-target="#ModalAdd" class="float-right btn btn-info btn-sm btn-round"><i class="fas fa-plus mr-1"></i>Add Data</button> -->
                                    </div>
                                </div>


                                <div class="card-body table-responsive">

                                    <table id="basic-datatables" class="table table-bordered table-striped table-hover">
                                        <thead>
                                            <th width='10%'>No.</th>
                                            <th width='20%'>Competence Unit</th>
                                            <th width='20%'>Competence Description</th>
                                            <th width='20%'>Subject Code</th>
                                            <th width='20%'>Subject Name</th>
                                            <th>Action</th>
                                        </thead>

                                        <tbody>
                                            <?php

                                            foreach ($sql2 as $i => $data) {



                                            ?>

                                                <tr>
                                                    <td><?= $i + 1 ?>.</td>
                                                    <td><?= $data['unit_kompetensi'] ?></td>
                                                    <td class="text-center"><button type="button" onclick="modal_deskripsi('<?= base64_encode($data['unit_kompetensi']) ?>', '<?= base64_encode($data['deskripsi_unit']) ?>' )" data-toggle="modal" data-target="#ModalDeskripri" class="btn btn-sm btn-primary"><i class="fas fa-eye mr-1"></i>Show</button></td>
                                                    <td><?= $data['kode_matkul'] ?></td>
                                                    <td><?= $data['matkul'] ?></td>
                                                    <td>
                                                        <div class="btn-group btn-round">
                                                            <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                Action
                                                            </button>
                                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                                <button class="dropdown-item" data-toggle="modal" data-target="#ModalEdit" onclick="modal_edit( '<?= base64_encode( $data['id_detail_profile'] ) ?>', '<?= base64_encode( $data['unit_kompetensi'] ) ?>', '<?= base64_encode( $data['deskripsi_unit'] ) ?>', '<?= base64_encode( $data['kode_matkul'] ) ?>', '<?= base64_encode( $data['matkul'] ) ?>' ) " ><i class="fa fa-edit mr-1"></i>Edit Data</button>
                                                                <button class="dropdown-item" onclick="swal_confirm('<?= $data['id_detail_profile'] ?>')"><i class="fa fa-trash mr-1"></i>Delete Data</button>


                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>

                                            <?php    }
                                            ?>
                                        </tbody>
                                    </table>

                                </div>

                            </div>
                        </div>



                    </div>


                    <!-- Modal Edit -->
                    <div class="modal fade" id="ModalDeskripri" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Competence Description</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

    
                                <div class="modal-body form-row">

                                        <div class="form-group col-md-12">
                                            <label>Competence Unit</label>
                                            <input type="text" class="form-control" id='unit_kompetensi_show'></input>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label>Competence Description</label>
                                            <textarea rows="5" type="text" class="form-control" id='deskripsi_unit_show'></textarea>
                                        </div>

                                    </div>

                                    <div class="modal-footer justify-content-start">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Close</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>



                    <!-- Modal Edit -->
                    <div class="modal fade" id="ModalEdit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Edit Graduation Profile</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <form action='index.php?form=competence_unit_dkm&action=edit' method='POST' id="form_edit"> <?= csrf() ?>
                                    <input type="hidden" id="id_edit" name="id_edit" value="<?= old('id_edit') ?>">
                                    <div class="modal-body form-row">

                                        <div class="form-group col-md-12">
                                            <label>Competence Unit <b class="text-danger">*</b></label>
                                            <input type="text" class="form-control <?= show_error('unit_kompetensi_edit') ?>" name='unit_kompetensi_edit'
                                            id='unit_kompetensi_edit' 
                                                placeholder='Input Competence Unit'></input>
                                            <?= form_error('unit_kompetensi_edit') ?>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label>Competence Description <b class="text-danger">*</b></label>
                                            <textarea rows="5" type="text" class="form-control <?= show_error('deskripsi_unit_edit') ?>" 
                                            name='deskripsi_unit_edit' id='deskripsi_unit_edit' 
                                                placeholder='Input Competence Unit' id="deskripsi_unit_edit"></textarea>
                                            <?= form_error('deskripsi_unit_edit') ?>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label>Subject Code <b class="text-danger">*</b></label>
                                            <input type="text" class="form-control <?= show_error('kode_matkul_edit') ?>" name='kode_matkul_edit' 
                                                id="kode_matkul_edit" placeholder='Input Subject Code'>

                                            <?= form_error('kode_matkul_edit') ?>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label>Subject Name <b class="text-danger">*</b></label>
                                            <input type="text" class="form-control <?= show_error('nama_matkul_edit') ?>" name='nama_matkul_edit'
                                                id='nama_matkul_edit' 
                                                placeholder='Input Subject Name'>

                                            <?= form_error('nama_matkul_edit') ?>
                                        </div>
                                    </div>

                                    <div class="modal-footer justify-content-start">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Cancel</button>
                                        <button type="button" onclick="reset_confirmation('form_edit')" class="btn btn-warning"><i class="fas fa-sync mr-1"></i>Reset</button>
                                        <button type="submit" class="btn btn-success"><i class="fas fa-edit mr-1"></i>Edit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>


                </div>



            </div>




            <?php include "./view/dosen/template/footer.php"; ?>

        </div>

    </div>

	<?php include "./view/dosen/template/script.php"; ?>

	<!-- Datatables -->
	<script src="./assets/js/plugin/datatables/datatables.min.js"></script>

    <!-- Soloalert -->
    <script src='./core/component/soloalert.js'></script>

    <!-- SWAL2 -->
    <link rel='stylesheet' href='./assets/sweetalert2.css'>
    <script src='./assets/sweetalert2.all.js'></script>

    <script>




        function modal_deskripsi(competence_unit, unit_description) {

            $('#unit_kompetensi_show').val( Base64.decode(competence_unit) );

            CKEDITOR.instances['deskripsi_unit_show'].setData( Base64.decode(unit_description) ); 

        }


        function modal_edit(id, competence_unit, unit_description, kode_matkul, matkul) {
            

            var isiku = 'CKE_deskripsi_unit_edit';

            $("#form_edit small").empty();

            $('#id_edit').val( Base64.decode(id) );


            $('#unit_kompetensi_edit').attr('class', 'form-control');
            $('#unit_kompetensi_edit').val( Base64.decode(competence_unit) );

            $('#kode_matkul_edit').attr('class', 'form-control');
            $('#kode_matkul_edit').val( Base64.decode(kode_matkul) );
            
            $('#nama_matkul_edit').attr('class', 'form-control');
            $('#nama_matkul_edit').val( Base64.decode(matkul) );

            // $('#deskripsi_unit_edit').attr('class', 'form-control');
            // $('#deskripsi_unit_edit').text( Base64.decode(unit_description) );
            // $('#deskripsi_unit_edit').val( Base64.decode(unit_description) );
            // $('#cke_deskripsi_unit_edit').val( Base64.decode(unit_description) );
            // $('#deskripsi_unit_edit').attr('class', 'form-control');


            CKEDITOR.instances['deskripsi_unit_edit'].setData( Base64.decode(unit_description) ); 
            // CKEDITOR.instances['deskripsi_unit_edit'].attr( 'class', 'form-control' ); 


            

        }


        function swal_confirm(id) {

            var kode_prodi_dkm = '<?= $_SESSION['program_study'] ?>';
            swal.fire({
                title: 'Delete Confirmation',
                text: 'Are you sure you want to delete this data?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {


                    // AJAX
                    $.ajax({
                        url: "./ajax/delProfileDetailComp.php",
                        type: "POST",
                        data: {
                            id_detail_profile: id,
                        },
                        dataType: 'text',
                        success: function(response) {
                            window.location.reload(1);
                        },
                        error: function(response) {
                            Swal.fire("Deleting Data Failed !", "Please try again later !", "error");
                        }
                    });

                }
            })

        }
    </script>

    <!-- User Custom Script -->
    <script>


        // Untuk Inisiasi Konfirmasi Cancel
        function cancel_confirmation( location ) {
            // alert('Are you sure you want to cancel ? (Your change will not be saved)');
            SoloAlert.confirm({
                title: "Are you sure you want to cancel ?",
                body: "Changes you made will not be saved !",
                useTransparency: true,
                onOk: () => {
                    window.location.href = location;
                }
            });

        }


        // Untuk Inisiasi Konfirmasi Reset
        function reset_confirmation( form ) {

            SoloAlert.confirm({
                title: "Are you sure you want to reset ?",
                body: "Changes you made will be reset to default !",
                useTransparency: true,
                onOk: () => {
                    $("#"+form).trigger("reset");
                    
                    CKEDITOR.instances['deskripsi_unit'].setData( '' ); 
                    CKEDITOR.instances['deskripsi_unit_edit'].setData( '' ); 

                }
            });

        }


    </script>

    <script>
        function disabled_form() {

            $("#edit_btn").prop("disabled", false);
            $("#edit_btn").prop("hidden", false);


            $("#cancel_btn").prop("hidden", true);
            $("#cancel_btn").prop("disabled", true);

            $("#reset_btn").prop("hidden", true);
            $("#reset_btn").prop("disabled", true);

            $("#save_btn").prop("disabled", true);

            $("#form_jobdesc :input").prop("disabled", true);
            $("#form_jobdesc").trigger("reset");

            $("#form_jobdesc :input").removeClass('is-invalid is-valid border-danger border-success');



            $("small").empty();
        };


        function enable_form() {


            $("#edit_btn").prop("disabled", true);
            $("#edit_btn").prop("hidden", true);


            $("#cancel_btn").prop("hidden", false);
            $("#cancel_btn").prop("disabled", false);

            $("#reset_btn").prop("hidden", false);
            $("#reset_btn").prop("disabled", false);


            $("#save_btn").prop("disabled", false);

            $("#form_jobdesc :input").prop("disabled", false);

        }

        $(document).ready(function() {

            var deskripsi_unit = CKEDITOR.replace('deskripsi_unit', {
			    language: 'en',
				extraPlugins: 'editorplaceholder',
				editorplaceholder: 'Enter Competence Description',
				removeButtons: 'PasteFromWord',
			});

            var deskripsi_unit_edit = CKEDITOR.replace('deskripsi_unit_edit', {
			    language: 'en',
				extraPlugins: 'editorplaceholder',
				editorplaceholder: 'Enter Competence Description',
				removeButtons: 'PasteFromWord',
			});

            var deskripsi_unit_show = CKEDITOR.replace('deskripsi_unit_show', {
			    language: 'en',
				extraPlugins: 'editorplaceholder',
				editorplaceholder: 'Enter Competence Description',
				removeButtons: 'PasteFromWord',
			});





            $('#basic-datatables').DataTable({});

            <?php
            if (old('focus-add') != NULL) {
                echo '
					var elmnt = document.getElementById("focus_add");
					elmnt.scrollIntoView();
					';
            }
            ?>

            <?php
            if (old('ModalEdit') != NULL) {
                echo "$('#ModalEdit').modal('show');";
            }
            ?>

                    setTimeout(function() {
                        $('#cke_deskripsi_unit').addClass('<?= show_error('deskripsi_unit') ?>');
                    },500)

        });
    </script>

</body>

</html>