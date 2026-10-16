<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = 'Job Profile';
$route = 'profile_jobdesc';
include "./view/dosen/template/head.php";

$ps = $_SESSION['program_study'];

$sql = mysqli_query($_SERVER['KONEKSI_DB'], "SELECT profile_jobdesc.* FROM profile_jobdesc WHERE profile_jobdesc.kode_prodi = '$ps'");



?>

<body>
    <div class="wrapper">

        <?php include "./view/dosen/template/main-header.php"; ?>
        <?php include "./view/dosen/template/sidebar.php"; ?>


        <div class="main-panel">

            <div class="container">
                <div class="page-inner">

                    <div class="page-header">
                        <h4 class="page-title">Job Profile</h4>
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
                                <a href="#">Job Profile</a>
                            </li>
                        </ul>
                    </div>


                    <!-- Form -->
                    <div class="row">

                        <!-- Jobdesc -->
                        <div class="col-md-12" id="focus_add">
                            <div class="card full-height">
                                <div class="card-header">
                                    <div class="card-title">Add Job Profile
                                    </div>
                                </div>


                                <form action='index.php?form=graduation_profile_dkm&action=add' method='POST' id="form_add"> <?= csrf() ?>
                                    <input type="hidden" name="kode_prodi" value="<?= $_SESSION['program_study'] ?>">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>Graduation Profile <b class="text-danger">*</b></label>
                                            <input type="text" class="form-control <?= show_error('graduation_profile') ?>" name='graduation_profile' 
                                            placeholder='Input Graduation Profile' required>

                                            <?= form_error('graduation_profile') ?>
                                        </div>
                                    </div>

                                    <div class="card-footer justify-content-start">
                                        <button type="button" onclick="cancel_confirmation('index.php') " class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Cancel</button>
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
                                    <div class="card-title">Job Profile</div>
                                </div>


                                <div class="card-body table-responsive">

                                    <table id="basic-datatables" class="table table-bordered table-striped table-hover">
                                        <thead>
                                            <th width='10%'>No.</th>
                                            <th width='50%'>Job Profile</th>
                                            <th width='20%'>Competence Unit</th>
                                            <th>Action</th>
                                        </thead>

                                        <tbody>
                                            <?php

                                            foreach ($sql as $i => $data) {

                                                $id_profile_jobdesc = $data['id_profile_jobdesc'];

                                                $sql2 = $_SERVER['KONEKSI_DB']->query("SELECT * FROM profile_detail_comp WHERE id_profile_jobdesc = '$id_profile_jobdesc'");
                                                $data['total_competences'] = $sql2->num_rows;


                                            ?>

                                                <tr>
                                                    <td><?= $i + 1 ?>.</td>
                                                    <td><?= $data['nama_profile'] ?></td>
                                                    <td><?= $data['total_competences'] ?> Unit</td>
                                                    <td>
                                                        <div class="btn-group btn-round">
                                                            <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                Action
                                                            </button>
                                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">

                                                                <button class="dropdown-item" onclick="window.location.href = 'index.php?page=competence_unit_manager&id_profile=<?= base64_encode($data['id_profile_jobdesc']) ?>'"><i class="fa fa-puzzle-piece mr-1"></i>Competence Unit Manager</button>

                                                                <?php if ($_SESSION['program_study'] == $data['kode_prodi']) : ?>
                                                                    <div class="dropdown-divider"></div>

                                                                    <button class="dropdown-item" data-toggle="modal" data-target="#ModalEdit" onclick="modal_edit( '<?= base64_encode( $data['id_profile_jobdesc'] ) ?>', '<?= base64_encode( $data['nama_profile'] ) ?>', '<?= base64_encode( $data['kode_prodi'] ) ?>' ) " ><i class="fa fa-edit mr-1"></i>Edit Data</button>
                                                                    <button class="dropdown-item" onclick="swal_confirm('<?= $data['id_profile_jobdesc'] ?>')"><i class="fa fa-trash mr-1"></i>Delete Data</button>

                                                                <?php endif; ?>

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
                    <div class="modal fade" id="ModalEdit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Edit Graduation Profile</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <form id="form_edit" action='index.php?form=graduation_profile_dkm&action=edit' method="POST" id="form_edit"> <?= csrf() ?>
                                    <input type="hidden" id="kode_prodi_edit" name="kode_prodi_edit">
                                    <input type="hidden" id="id_edit" name="id_edit">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>Graduation Profile<b class="text-danger">*</b></label>
                                            <input type="text" class="form-control <?= show_error('graduation_profile_edit') ?>" 
                                            id="graduation_profile_edit" name='graduation_profile_edit' 
                                            placeholder='Input Graduation Profile' required>

                                            <?= form_error('graduation_profile_edit') ?>


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


    <!-- User Custom Script -->
    <script>

        // Untuk Inisiasi Modal Edit
        function modal_edit(id, graduation_profile, prodi) {

            $("#form_edit small").empty();

            $('#id_edit').attr('class', 'form-control');
            $('#id_edit').val( Base64.decode(id) );
            
            $('#graduation_profile_edit').attr('class', 'form-control');
            $('#graduation_profile_edit').val( Base64.decode(graduation_profile) );
            
            $('#kode_prodi_edit').attr('class', 'form-control');
            $('#kode_prodi_edit').val( Base64.decode(prodi) );
        }


        // Untuk Inisiasi Konfirmasi Hapus
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
                        url: "./ajax/delProfileJobdesc.php",
                        type: "POST",
                        data: {
                            id_profile_jobdesc: id,
                            kode_prodi: kode_prodi_dkm
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
                }
            });

        }


    </script>


    <!-- On Document Ready, User Custom Script -->
    <script>

        $(document).ready(function() {

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

        });
    </script>

</body>

</html>