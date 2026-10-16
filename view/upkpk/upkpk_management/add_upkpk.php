<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = "Add CDC";
$route = 'upkpk_management';
include "./view/upkpk/template/head.php";

?>


<body>
    <div class="wrapper">


        <?php include "./view/upkpk/template/main-header.php"; ?>
        <?php include "./view/upkpk/template/sidebar.php"; ?>


        <div class="main-panel">
            <div class="container">
                <div class="page-inner">


                    <div class="page-header">
                        <h4 class="page-title">Add CDC</h4>
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
                                <a href="index.php?page=upkpk_management">CDC Management</a>
                            </li>

                            <li class="separator">
                                <i class="flaticon-right-arrow"></i>
                            </li>
                            <li class="nav-item">
                                <a href="#">Add CDC</a>
                            </li>
                        </ul>
                    </div>


                    <div class="row">

                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Add CDC Form</h4>
                                </div>

                                <form action="index.php?form=upkpk_management&action=add" method="POST" id="form_add_lecturer"><?= csrf(); ?>
                                    <div class="card-body form-row">

                                        <div class="form-group col-md-12">
                                            <label>Username <b class="text-danger">*</b></label>
                                            <input type="text" placeholder="Enter CDC Username" class="form-control <?= show_error('username') ?>" required name="username" id="username" value="<?= old('username') ?>" />

                                            <?= form_error('username') ?>

                                        </div>


                                        <div class="form-group col-md-12">
                                            <label>Name <b class="text-danger">*</b></label>
                                            <input type="text" placeholder="Enter CDC Name" class="form-control <?= show_error('name') ?>" required 
                                            name="name" id="name" value="<?= old('name') ?>"/>
                                            <?= form_error('name') ?>

                                        </div>

                                        <div class="form-group col-md-12">
                                            <label>University <b class="text-danger">*</b></label>
                                            <!-- <input type="text" placeholder="Enter CDC Name" class="form-control <?= show_error('name') ?>" required 
                                            name="name" id="name" value="<?= old('name') ?>"/> -->
                                            <select class="form-control" name="id_kampus" >
                                                <?php 
                                                $sql = "SELECT * FROM tb_kampus";
                                                $sql = $_SERVER['KONEKSI_DB']->query( $sql );

                                                foreach ($sql as $data) : ?>

                                                <option value="<?= $data['id_kampus'] ?>"><?= $data['nama_kampus'] ?></option>

                                                <?php endforeach; ?>
                                            </select>
                                            <?= form_error('id_kampus') ?>

                                        </div>

                                    </div>
                                    <div class="card-footer form-row">

                                        <div class="form-group col-md-12">

                                            <button type="button" class="btn btn-danger" onclick="confirmation()"><i class="fas fa-times mr-1"></i>Cancel</button>
                                            <button type="button" class="btn btn-warning" onclick="reset_confirmation()"><i class="fas fa-sync mr-1"></i>Reset</button>
                                            <button type="submit" class="btn btn-success"><i class="fas fa-plus mr-1"></i>Add</button>

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
    
	<?php include "./view/upkpk/template/script.php"; ?>

    <!-- Soloalert -->
    <script src='./core/component/soloalert.js'></script>


    <!-- AJAX Script -->
    <script>
        $(document).ready(function() {
            search_lecturer($('#lecturer_nik').val());
            $("#program_study").val("<?= old('program_study') ?>");
            $("#program_study").trigger('change');

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
                    window.location.href = "index.php?page=upkpk_management";
                }
            });

        }

        function reset_confirmation() {

            SoloAlert.confirm({
                title: "Are you sure you want to reset ?",
                body: "Changes you made will be reset to default !",
                useTransparency: true,
                onOk: () => {
                    $("#program_study").trigger('change');

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