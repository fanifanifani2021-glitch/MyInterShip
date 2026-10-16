<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

if ($_SESSION['role'] == 'UPKPK') {

    $title = "Add Student";
    $route = 'intern_student_list';
    include "./view/upkpk/template/head.php";
} else {
    $title = "Add Student";
    $route = 'student_intern_management';
    include "./view/dosen/template/head.php";
}

$id_kampus = $_SESSION['Lecturer-Data']['id_kampus'] ?? $_SESSION['CDC-Data']['id_kampus'];

?>


<body>
    <div class="wrapper">


        <?php


        if ($_SESSION['role'] == 'UPKPK') {

            include "./view/upkpk/template/main-header.php";


            include "./view/upkpk/template/sidebar.php";
        } else {

            include "./view/dosen/template/main-header.php";


            include "./view/dosen/template/sidebar.php";
        }


        ?>


        <div class="main-panel">
            <div class="container">
                <div class="page-inner">


                

                    <div class="page-header">
                        <h4 class="page-title">Add Student</h4>
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
                                <a href="index.php?page=<?= ( $_SESSION['role'] == 'UPKPK' ) ? 'intern_student_list' : 'student_intern_management'; ?>">Students Management</a>
                            </li>

                            <li class="separator">
                                <i class="flaticon-right-arrow"></i>
                            </li>
                            <li class="nav-item">
                                <a href="#">Add Student</a>
                            </li>
                        </ul>
                    </div>


                    <div class="row">

                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Add Student Form</h4>
                                </div>

                                <form action="index.php?form=dkm&aksi=add_student_intern" method="post" id="form_add_lecturer"><?= csrf(); ?>
                                    <div class="card-body form-row">

                                        <div class="form-group col-md-6">
                                            <label>Student NIM <b class="text-danger">*</b></label>
                                            <input type="text" class="form-control <?= show_error('student_nim') ?>" name="student_nim" id="student_nim" value="<?= old('student_nim') ?>" onkeyup="search_student( this.value )" placeholder="Example : 33118010xx" />

                                            <?= form_error('student_nim') ?>

                                        </div>


                                        <div class="form-group col-md-6">
                                            <label>Student Name <b class="text-danger">*</b></label>
                                            <input type="text" class="form-control <?= show_error('student_name') ?>" name="student_name" id="student_name" placeholder="Example : Fulan Ramadhan" />

                                            <?= form_error('student_name') ?>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label>Student E-Mail <small class="text-sm">Opsional</small></label>
                                            <input type="text" class="form-control <?= show_error('student_email') ?>" name="student_email" id="student_email" placeholder="Example : fulan@gmail.com" />

                                            <?= form_error('student_email') ?>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label>Program Study <b class="text-danger">*</b></label>


                                            <select class="form-control <?= show_error('kode_program_study') ?>" id="kode_program_study" name="kode_program_study">
                                            <option value="" >- Choose Program Study -</option>
                                                
                                                <?php
                                                $sql2 = "SELECT DISTINCT major, jurusan FROM program_study WHERE id_kampus = '" . $id_kampus . "'";

                                                foreach ($exec2 = $_SERVER['KONEKSI_DB']->query($sql2) as $row2) :

                                                    $jurusan = $row2['jurusan'];
                                                ?>


                                                    <optgroup label="<?= $row2['major'] ?> Major">

                                                        <?php
                                                        $sql3 = "SELECT * FROM program_study WHERE jurusan = '$jurusan' AND id_kampus = '" . $id_kampus . "' ORDER BY jenjang ASC ";

                                                        foreach ($exec3 = $_SERVER['KONEKSI_DB']->query($sql3) as $row3) :
                                                        ?>

                                                            <option value="<?php echo $row3['kode_prodi'] ?>" <?= ( old('kode_program_study', $_SESSION['Lecturer-Data'] ['prodi_koor'] ) == $row3['kode_prodi'] ) ? 'selected' : ''; ?>><?= $row3['jenjang'] ?> <?= $row3['prodi'] ?> ( <?= $row3['study_program'] ?> )</option>

                                                        <?php endforeach; ?>

                                                    </optgroup>


                                                <?php endforeach; ?>
                                            </select>

                                            <?= form_error('kode_program_study') ?>
                                        </div>


                                        <div class="form-group col-md-12" hidden>
                                            <label>Search Status <b class="text-danger">*</b></label>
                                            <input type="text" class="form-control" name="search_status" id="search_status" value="Success" />

                                            <?= form_error('search_status') ?>

                                        </div>



                                    </div>
                                    <div class="card-footer">
                                        <button type="button" class="btn btn-danger" onclick="confirmation()"><i class="fas fa-times mr-1"></i>Cancel</button>
                                        <button type="button" class="btn btn-warning" onclick="reset_confirmation()"><i class="fas fa-sync mr-1"></i>Reset</button>
                                        <button type="submit" class="btn btn-success"><i class="fas fa-plus mr-1"></i>Add</button>

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

    <!-- Soloalert -->
    <script src='./core/component/soloalert.js'></script>


    <!-- AJAX Script -->
    <script>
        $(document).ready(function() {
            search_student($('#student_nim').val());
        });

        function search_student(nim) {

            var save_btn = document.getElementById('save_btn');

            var student_waiting = document.getElementById('student_waiting');
            var student_notification = document.getElementById('student_notification');
            var search_result = document.getElementsByClassName('search-result');
            var search_status = document.getElementById('search_status');

            search_status.value = 'Failed';
            save_btn.disabled = true;

            if (nim.length >= 10) {
                $('#student_nim').val($('#student_nim').val().substr(0, 10));
            }

            if (nim.length != 0 && nim.length != 10) {

                for (z = 0; z <= search_result.length - 1; z++) {
                    search_result[z].setAttribute('hidden', true);
                    search_result[z].removeAttribute('value');
                }

                student_waiting.hidden = true;
                student_notification.hidden = false;
                search_status.value = 'Failed';
            }

            if (nim.length != 0 && nim.length == 10) {

                student_waiting.hidden = false;
                student_notification.hidden = true;

                $.ajax({

                    url: './ajax/getDataAPIPolbat.php',
                    type: 'post',
                    data: {
                        'nim_nik_unit': nim
                    },
                    dataType: 'json',


                    success: function(response) {

                        console.log(response);


                        var len = response.length;
                        if (response[0]['hasil_pencarian'] == "Berhasil") {

                            save_btn.disabled = false;


                            student_waiting.hidden = true;
                            student_notification.hidden = true;


                            search_status.value = 'Success';

                            document.getElementById('student_name').value = response[0]['name'];
                            document.getElementById('student_email').value = response[0]['email'];
                            document.getElementById('program_study').value = response[0]['prodi'];
                            document.getElementById('kode_program_study').value = response[0]['kode_prodi'];
                            document.getElementById('generation').value = response[0]['angkatan'];

                            for (z = 0; z <= search_result.length - 1; z++) {
                                console.log(search_result);
                                search_result[z].removeAttribute('hidden');
                            }

                        } else {

                            student_waiting.hidden = true;
                            student_notification.hidden = false;


                            search_status.value = 'Failed';

                            for (z = 0; z <= search_result.length - 1; z++) {
                                search_result[z].setAttribute('hidden', true);
                                search_result[z].removeAttribute('value');
                            }
                        }
                    },

                    error: function(response) {
                        console.log(response);

                        student_waiting.hidden = true;
                        student_notification.hidden = false;

                        search_status.value = 'Failed';

                        for (z = 0; z <= search_result.length - 1; z++) {
                            search_result[z].setAttribute('hidden', true);
                            search_result[z].removeAttribute('value');
                        }
                    }

                });





            }
        }
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
                    window.location.href = "index.php?page=<?= ( $_SESSION['role'] == 'UPKPK' ) ? 'intern_student_list' : 'student_intern_management'; ?>";
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