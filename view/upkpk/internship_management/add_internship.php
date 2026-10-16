<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$coor = $_GET['coor'];
$nim = base64_decode($_GET['nim']);

$sql = "SELECT student_internship.*, program_study.* FROM student_internship LEFT JOIN program_study ON program_study.kode_prodi = student_internship.program_study WHERE student_internship.nim = '$nim' ";
$sql = $_SERVER['KONEKSI_DB']->query($sql);

if ($sql->num_rows != 1) {
    make_soloalert('error', 'Add Data Failed', 'Student data is not found !');
    redirect_back(1);
    exit;
}

$data_student = $sql->fetch_assoc();


if ($coor == 1) :
    $prodi = $data_student['program_study'];

    $sql_lecturer = "SELECT * FROM lecturer WHERE is_koor = 1 AND prodi_koor = '$prodi'";
    $sql_lecturer = $_SERVER['KONEKSI_DB']->query($sql_lecturer);
    $data_lecturer = $sql_lecturer->fetch_assoc();

endif;

$id_kampus = $data_student['id_kampus'];
$sql_kampus = "SELECT * FROM tb_kampus WHERE id_kampus = '$id_kampus'";
$university = $_SERVER['KONEKSI_DB']->query($sql_kampus)->fetch_assoc();

?>

<?php


$title = "Add Internship";

if ($_SESSION['role'] == 'Dosen') {

    $route = 'student_intern_management';
} else {

    if ($coor == 1) {
        $route = 'intern_co_management';
    } else {
        $route = 'intern_student_list';
    }
}


if ($_SESSION['role'] == 'Dosen') {
    include "./view/dosen/template/head.php";
} else {
    include "./view/upkpk/template/head.php";
}



?>



<body>
    <div class="wrapper">


        <?php

        if ($_SESSION['role'] == 'Dosen') {
            include "./view/dosen/template/main-header.php";
            include "./view/dosen/template/sidebar.php";
        } else {
            include "./view/upkpk/template/main-header.php";
            include "./view/upkpk/template/sidebar.php";
        }

        ?>


        <div class="main-panel">
            <div class="container">
                <div class="page-inner">



                    <div class="page-header">
                        <h4 class="page-title">Add Internship</h4>
                        <ul class="breadcrumbs">
                            <li class="nav-home">
                                <a href="index.php">
                                    <i class="flaticon-home"></i>
                                </a>
                            </li>

                            <?php

                            if ($_SESSION['role'] == 'Dosen') { ?>

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
                                    <a href="<?= base_url('index.php?page=student_intern_detail&nim=' . base64_encode($nim)) ?>">Student Detail</a>
                                </li>

                                <?php
                            } else {
                                if ($coor == 1) : ?>

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
                                    <a href="<?= base_url('index.php?page=student_detail_intern_co&nim=' . base64_encode($nim)) ?>">Student Detail</a>
                                </li>

                                <!-- <li class="separator">
                                <i class="flaticon-right-arrow"></i>
                            </li>
                            <li class="nav-item">
                                <a href="#">Internship Management</a>
                            </li> -->


                            <?php } ?>

                            <li class="separator">
                                    <i class="flaticon-right-arrow"></i>
                                </li>
                                <li class="nav-item">
                                    <a href="#">Add Internship</a>
                                </li>

                        </ul>
                    </div>


                    <div class="row">

                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Add Internship</h4>
                                </div>

                                <form action="index.php?form=internship&action=add" method="post" id="form_add_lecturer"><?= csrf(); ?>
                                    <div class="card-body form-row">

                                        <div class="form-group col-md-12">
                                            <label>Student NIM <b class="text-danger">*</b></label>



                                            <select id="student_nim" name="student_nim" required style="width:inherit;" class="<?= show_error('student_nim') ?>">
                                                <option value="" hidden>Choose one . . .</option>

                                                <?php

                                                $id_kampus = $_SESSION['CDC-Data']['id_kampus'] ?? $_SESSION['Lecturer-Data']['id_kampus'];

                                                $sql = "SELECT * FROM student_internship WHERE id_kampus = '" . $id_kampus . "'";

                                                $exec = $_SERVER['KONEKSI_DB']->query($sql);

                                                foreach ($exec as $student) :
                                                ?>

                                                    <option value="<?= $student['nim'] ?>"><?= $student['nim'] ?> - <?= $student['name'] ?></option>

                                                <?php endforeach; ?>
                                            </select>

                                            <?= form_error('student_nim') ?>


                                        </div>

                                        <div class="form-group col-md-12">
                                            <label>Company <b class="text-danger">*</b></label>


                                            <select id="company" name="company" required style="width:inherit;" class="<?= show_error('company') ?>">
                                                <option value="" hidden>Choose one . . .</option>

                                                <?php

                                                $sql = "SELECT * FROM company";
                                                $exec = $_SERVER['KONEKSI_DB']->query($sql);

                                                foreach ($exec as $company) :
                                                ?>

                                                    <option value="<?= $company['id_company'] ?>"><?= $company['name'] ?></option>

                                                <?php endforeach; ?>
                                            </select>

                                            <?= form_error('company') ?>

                                            <?php if (in_array($_SESSION['role'], ['CDC', 'UPKPK'])) : ?>
                                                <label class="text-sm">Company does not exist ? <a href="<?= base_url('index.php?page=add_company&redirect=' .base64_encode( 'add_internship&coor=' .$coor. '&nim=' .base64_encode( $nim ) )) ?>">Add Company</a></label>
                                            <?php endif; ?>

                                        </div>

                                        <div class="form-group col-md-6">
                                            <label>Start Date <b class="text-danger">*</b></label>


                                            <input type="date" name="start_date" required class="form-control <?= show_error('start_date') ?>" value="<?= old('start_date') ?>" />

                                            <?= form_error('start_date') ?>

                                        </div>

                                        <div class="form-group col-md-6">
                                            <label>End Date <b class="text-danger">*</b></label>


                                            <input type="date" name="end_date" required class="form-control <?= show_error('end_date') ?>" value="<?= old('end_date') ?>" />

                                            <?= form_error('end_date') ?>

                                        </div>

                                    </div>

                                    <div class="card-footer form-row">

                                        <div class="form-group col-md-12">
                                            <button type="button" class="btn btn-danger" onclick="confirmation()"><i class="fas fa-times mr-1"></i>Cancel</button>
                                            <button type="button" class="btn btn-warning" onclick="reset_confirmation()"><i class="fas fa-sync mr-1"></i>Reset</button>
                                            <button type="submit" class="btn btn-success" id="submit_btn"><i class="fas fa-plus mr-1"></i>Add</button>

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



    <?php

    if ($_SESSION['role'] == 'Dosen') {
        include "./view/dosen/template/script.php";
    } else {
        include "./view/upkpk/template/script.php";
    }

    ?>

    <!-- Select2 -->
    <script src="./assets/js/plugin/select2/select2.full.min.js"></script>

    <!-- Soloalert -->
    <script src='./core/component/soloalert.js'></script>


    <!-- AJAX Script -->
    <script>
        $(document).ready(function() {

            $('#student_nim').select2({
                theme: "bootstrap"
            });

            $('#company').select2({
                theme: "bootstrap"
            });


            <?php if (old('student_nim', $nim) != '') : ?>
                $('#student_nim').val('<?= old('student_nim', $nim) ?>').select2({
                    theme: "bootstrap"
                }).trigger('change');
            <?php endif; ?>

            <?php if (old('company') != '') : ?>
                $('#company').val('<?= old('company') ?>').select2({
                    theme: "bootstrap"
                }).trigger('change');
            <?php endif; ?>




            $("[aria-labelledby='select2-student_nim-container']").addClass('<?= show_error('student_nim') ?>');
            $("[aria-labelledby='select2-company-container']").addClass('<?= show_error('company') ?>');

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
                    window.location.href = "<?= (in_array($_SESSION['role'], ['CDC', 'UPKPK'])) ? base_url('index.php?page=student_detail_intern_co&nim=' . base64_encode($nim)) : base_url('index.php?page=student_intern_detail&nim=' . base64_encode($nim)) ?>";
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




</body>

</html>