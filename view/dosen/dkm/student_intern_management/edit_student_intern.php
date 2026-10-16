<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = "Edit Student";
$route = 'student_intern_management';
include "./view/dosen/template/head.php";

?>

<?php

$nim = base64_decode($_GET['nim']);

$sql = "SELECT * FROM student_internship WHERE nim = '$nim'";
$exec = $_SERVER['KONEKSI_DB']->query($sql);
$student_data = $exec->fetch_object();

if ($exec->num_rows != 1) {

    make_soloalert('error', 'Edit Data Failed', 'Student data is not found !');
    redirect_back(1);
    exit;
}

?>


<body>
    <div class="wrapper">


        <?php include "./view/dosen/template/main-header.php"; ?>
        <?php include "./view/dosen/template/sidebar.php"; ?>


        <div class="main-panel">
            <div class="container">
                <div class="page-inner">


                    <div class="page-header">
                        <h4 class="page-title">Edit Student</h4>
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
                                <a href="index.php?page=student_intern_management">Student Management</a>
                            </li>

                            <li class="separator">
                                <i class="flaticon-right-arrow"></i>
                            </li>
                            <li class="nav-item">
                                <a href="#">Edit Student</a>
                            </li>
                        </ul>
                    </div>


                    <div class="row">

                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Edit Student Form</h4>
                                </div>

                                <form action="index.php?form=dkm&aksi=edit_student_intern" method="post" id="form_add_lecturer"><?= csrf(); ?> <?= input_hidden('old_student_nim', $student_data->nim) ?>
                                    <div class="card-body form-row">

                                        <div class="form-group col-md-6">
                                            <label>Student NIM <b class="text-danger">*</b></label>
                                            <input type="text" class="form-control" name="student_nim" id="student_nim" value="<?= old('student_nim', $student_data->nim) ?>" onkeyup="search_student( this.value )" placeholder="Enter Student's NIM" />

                                            <?= form_error('student_nim') ?>

                                        </div>

                                        <div class="form-group col-md-6">
                                            <label>Student Name <b class="text-danger">*</b></label>
                                            <input type="text" class="form-control" name="student_name" id="student_name" value="<?= old('student_name', $student_data->name) ?>" />

                                            <?= form_error('student_name') ?>
                                        </div>

                                        <div class="form-group col-md-6" >
                                            <label>Student's E-Mail <b class="text-danger">*</b></label>
                                            <input type="text" class="form-control" name="student_email" id="student_email" value="<?= old('student_email', $student_data->email) ?>" />

                                            <?= form_error('student_email') ?>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label>Program Study <b class="text-danger">*</b></label>


                                            <select class="form-control" id="kode_program_study" name="kode_program_study">
                                                <?php
                                                $sql2 = "SELECT DISTINCT major, jurusan FROM program_study WHERE id_kampus = '" . $_SESSION['Lecturer-Data']['id_kampus'] . "'";

                                                foreach ($exec2 = $_SERVER['KONEKSI_DB']->query($sql2) as $row2) :

                                                    $jurusan = $row2['jurusan'];
                                                ?>

                                                    <!-- <optgroup label="<?= $row2['jurusan'] ?>" > -->
                                                    <optgroup label="<?= $row2['major'] ?>">

                                                        <?php
                                                        $sql3 = "SELECT * FROM program_study WHERE jurusan = '$jurusan' AND id_kampus = '" . $_SESSION['Lecturer-Data']['id_kampus'] . "' ORDER BY jenjang ASC ";

                                                        foreach ($exec3 = $_SERVER['KONEKSI_DB']->query($sql3) as $row3) :
                                                        ?>

                                                            <!-- <option value="<?php echo $row3['kode_prodi'] ?>"><?= $row3['jenjang'] ?> <?= $row3['prodi'] ?></option> -->
                                                            <option value="<?php echo $row3['kode_prodi'] ?>" <?= ( old('kode_program_study', $student_data->program_study) == $row3['kode_prodi'] ) ? 'selected': ''; ?>><?= $row3['jenjang'] ?> <?= $row3['study_program'] ?></option>

                                                        <?php endforeach; ?>

                                                    </optgroup>


                                                <?php endforeach; ?>
                                            </select>

                                            <?= form_error('program_study') ?>
                                        </div>



                                    </div>
                                    <div class="card-footer">
                                        <button type="button" class="btn btn-danger" onclick="confirmation()"><i class="fas fa-times mr-1"></i>Cancel</button>
                                        <button type="button" class="btn btn-warning" onclick="reset_confirmation()"><i class="fas fa-sync mr-1"></i>Reset</button>
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


    <!-- AJAX Script -->

    <!-- User Custom Script -->
    <script>
        function confirmation() {
            // alert('Are you sure you want to cancel ? (Your change will not be saved)');
            SoloAlert.confirm({
                title: "Are you sure you want to cancel ?",
                body: "Changes you made will not be saved !",
                useTransparency: true,
                onOk: () => {
                    window.location.href = "index.php?page=student_intern_management";
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
        $('#datetime').datetimepicker({
            format: 'MM/DD/YYYY H:mm',
        });
        $('#datepicker').datetimepicker({
            format: 'MM/DD/YYYY',
        });
        $('#timepicker').datetimepicker({
            format: 'h:mm A',
        });


        $('#lecturer_status').select2({
            theme: "bootstrap"
        });

        $('#user_type').select2({
            theme: "bootstrap"
        });
    </script>
</body>

</html>