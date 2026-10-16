<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = "Add Coordinator";
$route = 'intern_co_management';
include "./view/upkpk/template/head.php";

$id_kampus = $_SESSION['CDC_Data'] ['id_kampus'];
?>


<body>
    <div class="wrapper">


        <?php include "./view/upkpk/template/main-header.php"; ?>
        <?php include "./view/upkpk/template/sidebar.php"; ?>


        <div class="main-panel">
            <div class="container">
                <div class="page-inner">


                    <div class="page-header">
                        <h4 class="page-title">Add Coordinator</h4>
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
                                <a href="index.php?page=intern_co_management">Coordinator Management</a>
                            </li>

                            <li class="separator">
                                <i class="flaticon-right-arrow"></i>
                            </li>
                            <li class="nav-item">
                                <a href="#">Add Coordinator</a>
                            </li>
                        </ul>
                    </div>


                    <div class="row">

                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Add Coordinator Form</h4>
                                </div>

                                <form action="index.php?form=intern_co&action=add" method="post" id="form_add_lecturer"><?= csrf(); ?>
                                    <div class="card-body form-row">

                                        <div class="form-group col-md-12">
                                            <label>Lecturer NIK <b class="text-danger">*</b></label>


                                            <select id="lecturer_nik" name="lecturer_nik" class="form-control col-md-12 <?= show_error('lecturer_nik') ?>">
                                                <option value="" hidden>Choose one . . .</option>

                                                <?php

                                                $sql = "SELECT * FROM lecturer WHERE lecturer.is_koor NOT IN (1) AND lecturer.id_kampus = '$id_kampus'";
                                                $exec = $_SERVER['KONEKSI_DB']->query($sql);

                                                foreach ($exec as $data_lecturer) :
                                                ?>

                                                        <option value="<?= $data_lecturer['nim_nik_unit'] ?>"><?= $data_lecturer['nim_nik_unit'] ?> - <?= $data_lecturer['name'] ?></option>

                                                <?php endforeach; ?>
                                            </select>

                                            <?= form_error('lecturer_nik') ?>

                                        </div>

                                        <div class="form-group col-md-12">
                                            <label>Study Program <b class="text-danger">*</b></label>


                                            <select id="program_study" name="program_study" class="form-control col-md-12 <?= show_error('lecturer_nik') ?>">
                                                <option value="" hidden>Choose one . . .</option>

                                                <?php

                                                // $sql = "SELECT DISTINCT(program_study.major) FROM program_study WHERE program_study.id_kampus = '$id_kampus' AND program_study.kode_prodi NOT IN (SELECT lecturer.prodi_koor FROM lecturer WHERE lecturer.is_koor NOT IN (0) ) ORDER BY program_study.major;";
                                                $sql = "SELECT DISTINCT(program_study.major) FROM program_study WHERE program_study.id_kampus = '$id_kampus'";
                                                $exec = $_SERVER['KONEKSI_DB']->query($sql);

                                                foreach ($exec as $data_jurusan) :
                                                    $jurusan = $data_jurusan['major'];
                                                ?>

                                                    <optgroup label="<?= $jurusan ?>">


                                                        <?php

                                                        $sql2 = "SELECT program_study.* FROM program_study WHERE program_study.id_kampus = '$id_kampus' AND program_study.major = '$jurusan' ORDER BY program_study.jenjang";
                                                        $exec2 = $_SERVER['KONEKSI_DB']->query($sql2);

                                                        foreach ($exec2 as $data_prodi) :
                                                        ?>

                                                            <option value="<?= $data_prodi['kode_prodi'] ?>"><?= $data_prodi['jenjang'] ?> <?= $data_prodi['study_program'] ?></option>

                                                        <?php endforeach; ?>



                                                    </optgroup>

                                                <?php endforeach; ?>
                                            </select>

                                            <?= form_error('program_study') ?>

                                        </div>

                                    </div>

                                    <div class="card-footer form-row">

                                        <div class="form-group col-md-12">
                                            <button type="button" class="btn btn-danger" onclick="confirmation()"><i class="fas fa-times mr-1"></i>Cancel</button>
                                            <button type="button" class="btn btn-warning" onclick="reset_confirmation()"><i class="fas fa-sync mr-1"></i>Reset</button>
                                            <button type="submit" class="btn btn-success" id="submit_btn" ><i class="fas fa-plus mr-1"></i>Add</button>

                                        </div>
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

    <?php include './view/upkpk/template/script.php'; ?>

    <!-- Select2 -->
    <script src="./assets/js/plugin/select2/select2.full.min.js"></script>

    <!-- Soloalert -->
    <script src='./core/component/soloalert.js'></script>


    <!-- AJAX Script -->
    <script>
        $(document).ready(function() {

            $("#program_study").val("<?= old('program_study') ?>");
            $("#program_study").trigger('change');


            
            $("#lecturer_nik").val("<?= old('lecturer_nik') ?>");
            $("#lecturer_nik").trigger('change');

            $("#user_type").val("<?= old('user_type') ?>");
            $("#user_type").trigger('change');


            $("[aria-labelledby='select2-program_study-container']").addClass('<?= show_error('program_study') ?>');
            $("[aria-labelledby='select2-user_type-container']").addClass('<?= show_error('user_type') ?>');
            $("[aria-labelledby='select2-lecturer_status-container']").addClass('<?= show_error('lecturer_status') ?>');




        });

    </script>


    <!-- User Custom Script -->
    <script>
        function confirmation() {
            // alert('Are you sure you want to cancel ? (Your change will not be saved)');
            SoloAlert.confirm({
                title: "Are you sure you want to cancel ?",
                body: "Changes you made will not be saved !",
                useTransparency: true,
                onOk: () => {
                    window.location.href = "index.php?page=intern_co_management";
                }
            });

        }

        function reset_confirmation() {

            SoloAlert.confirm({
                title: "Are you sure you want to reset ?",
                body: "Changes you made will be reset to default !",
                useTransparency: true,
                onOk: () => {
                    form_reset();
                }
            });

        }


        function form_reset() {

            $("#form_add_lecturer").trigger("reset");

            $("small").empty();
        };
    </script>



    <!-- Page Sript Dependencies -->
    <script>
        $('#lecturer_nik').select2({
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