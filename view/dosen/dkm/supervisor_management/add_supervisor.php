<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = "Add Lecturer";
$route = 'supervisor_management';
include "./view/dosen/template/head.php";

// Pengecekan Login Dosen Pembimbing
include './view/dosen/template/cek_login_dkm.php';
?>


<body>
    <div class="wrapper">


        <?php include "./view/dosen/template/main-header.php"; ?>
        <?php include "./view/dosen/template/sidebar.php"; ?>


        <div class="main-panel">
            <div class="container">
                <div class="page-inner">


                    <div class="page-header">
                        <h4 class="page-title">Add Lecturer</h4>
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
                                <a href="index.php?page=supervisor_management">Lecturer Management</a>
                            </li>

                            <li class="separator">
                                <i class="flaticon-right-arrow"></i>
                            </li>
                            <li class="nav-item">
                                <a href="#">Add Lecturer</a>
                            </li>
                        </ul>
                    </div>


                    <div class="row">

                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Form Add Lecturer</h4>
                                </div>

                                <form action="index.php?form=dkm&aksi=add_supervisor" method="post" id="form_add_lecturer"><?= csrf(); ?>
                                    <div class="card-body form-row">

                                        <div class="form-group col-md-12">
                                            <label>Lecturer's NIK <b class="text-danger">*</b></label>
                                            <input type="text" class="form-control" name="lecturer_nik" id="lecturer_nik" value="<?= old('lecturer_nik') ?>" onkeyup="search_lecturer( this.value )" placeholder="Enter Lecturer's NIK" />

                                            <?= form_error('lecturer_nik') ?>

                                        </div>

                                        <div class="form-group col-md-6 search-result">
                                            <label>Lecturer's Name <b class="text-danger">*</b></label>
                                            <input type="text" class="form-control " name="lecturer_name" id="lecturer_name" placeholder="Enter Lecturer Name" value="<?= old('lecturer_name') ?>" />

                                            <?= form_error('lecturer_name') ?>
                                        </div>

                                        <div class="form-group col-md-6 search-result">
                                            <label>Lecturer E-Mail <b class="text-danger">*</b></label>
                                            <input type="text" class="form-control" name="lecturer_email" id="lecturer_email" placeholder="Enter Lecturer Email" value="<?= old('lecturer_nik') ?>"/>

                                            <?= form_error('lecturer_email') ?>
                                        </div>


                                    </div>
                                    <div class="card-footer">
                                        <button type="button" class="btn btn-danger" onclick="confirmation()"><i class="fas fa-times mr-1"></i>Cancel</button>
                                        <button type="button" class="btn btn-warning" onclick="reset_confirmation()"><i class="fas fa-sync mr-1"></i>Reset</button>
                                        <button type="submit" class="btn btn-success" id="submit_btn"><i class="fas fa-plus mr-1"></i>Add</button>

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

    <!-- Select2 -->
    <script src="./assets/js/plugin/select2/select2.full.min.js"></script>

    <!-- Soloalert -->
    <script src='./core/component/soloalert.js'></script>


    <!-- AJAX Script -->
    <script>
        $(document).ready(function() {
            search_lecturer($('#lecturer_nik').val());
            $("#program_study").val("<?= old('program_study') ?>");
            $("#program_study").trigger('change');

            $("#user_type").val("<?= old('user_type') ?>");
            $("#user_type").trigger('change');

        });

        function search_lecturer(nik) {
            var submit_btn = document.getElementById('submit_btn');
            var lecturer_waiting = document.getElementById('lecturer_waiting');
            var lecturer_notification = document.getElementById('lecturer_notification');
            var search_result = document.getElementsByClassName('search-result');
            var search_status = document.getElementById('search_status');
            search_status.value = 'Failed';
            submit_btn.disabled = true;

            if (nik.length >= 6) {
                $('#lecturer_nik').val($('#lecturer_nik').val().substr(0, 6));
            }

            if (nik.length != 0 && nik.length != 6) {

                for (z = 0; z <= search_result.length - 1; z++) {
                    search_result[z].setAttribute('hidden', true);
                    search_result[z].removeAttribute('value');
                }

                lecturer_waiting.hidden = true;
                lecturer_notification.hidden = false;
                search_status.value = 'Failed';
            }

            if (nik.length != 0 && nik.length == 6) {

                lecturer_waiting.hidden = false;
                lecturer_notification.hidden = true;

                $.ajax({

                    url: './ajax/getDataAPIPolbat.php',
                    type: 'post',
                    data: {
                        'nim_nik_unit': nik
                    },
                    dataType: 'json',


                    success: function(response) {


                        var len = response.length;
                        if (response[0]['hasil_pencarian'] == "Berhasil") {

                            submit_btn.disabled = false;
                            lecturer_waiting.hidden = true;
                            lecturer_notification.hidden = true;


                            search_status.value = 'Success';

                            document.getElementById('lecturer_name').value = response[0]['name'];
                            document.getElementById('lecturer_email').value = response[0]['email'];

                            for (z = 0; z <= search_result.length - 1; z++) {
                                console.log(search_result);
                                search_result[z].removeAttribute('hidden');
                            }

                        } else {

                            lecturer_waiting.hidden = true;
                            lecturer_notification.hidden = false;


                            search_status.value = 'Failed';

                            for (z = 0; z <= search_result.length - 1; z++) {
                                search_result[z].setAttribute('hidden', true);
                                search_result[z].removeAttribute('value');
                            }
                        }
                    },

                    error: function(response) {
                        lecturer_waiting.hidden = true;
                        lecturer_notification.hidden = false;

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
                    window.location.href = "index.php?page=supervisor_management";
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

        $('#program_study').select2({
            theme: "bootstrap"
        });
    </script>
</body>

</html>