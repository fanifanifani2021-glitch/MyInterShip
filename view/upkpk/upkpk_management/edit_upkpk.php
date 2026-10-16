<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = "Edit CDC";
$route = 'upkpk_management';
include "./view/upkpk/template/head.php";

?>

<?php

$id_upkpk = base64_decode($_GET['id']);

$sql = "SELECT * FROM upkpk WHERE id_upkpk = '$id_upkpk'";
$exec = $_SERVER['KONEKSI_DB']->query($sql);
if ($exec->num_rows != 1) {
    make_swal2('error', 'Not Allowed', 'UPKPK data not found !');
    redirect_referrer(1);
    exit();
}

$data_upkpk = $exec->fetch_assoc();

?>


<body>
    <div class="wrapper">


        <?php include "./view/upkpk/template/main-header.php"; ?>
        <?php include "./view/upkpk/template/sidebar.php"; ?>


        <div class="main-panel">
            <div class="container">
                <div class="page-inner">


                    <div class="page-header">
                        <h4 class="page-title">Edit CDC</h4>
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
                                <a href="#">Edit CDC</a>
                            </li>
                        </ul>
                    </div>


                    <div class="row">

                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Edit CDC Form</h4>
                                </div>

                                <form action="index.php?form=upkpk_management&action=edit" method="POST" id="form_add_lecturer"><?= csrf(); ?>
                                    <div class="card-body form-row">

                                        <input type="hidden" name="id_upkpk" value="<?= $id_upkpk ?>">
                                        <input type="hidden" name="old_username" value="<?= $data_upkpk['username'] ?>">

                                        <div class="form-group col-md-12">
                                            <label>Username <b class="text-danger">*</b></label>
                                            <input type="text" placeholder="Enter CDC Username" class="form-control <?= show_error('username') ?>" required name="username" id="username" value="<?= old('username', $data_upkpk['username']) ?>" />

                                            <?= form_error('username') ?>

                                        </div>


                                        <div class="form-group col-md-6">
                                            <label>Name <b class="text-danger">*</b></label>
                                            <input type="text" placeholder="Enter CDC Name" class="form-control <?= show_error('name') ?>" required name="name" id="name" value="<?= old('name', $data_upkpk['name']) ?>" />

                                        </div>

                                        <div class="form-group col-md-6">
                                            <label>Status <b class="text-danger">*</b></label>

                                            <select name="status" class="form-control <?= show_error('status') ?>">
                                                <option <?= (old('status', $data_upkpk['status']) == 'Active') ? 'selected' : ''; ?>>Active</option>
                                                <option <?= (old('status', $data_upkpk['status']) == 'Not Active') ? 'selected' : ''; ?>>Not Active</option>
                                            </select>


                                            <?= form_error('status') ?>

                                        </div>


                                        <div class="form-group col-md-12">
                                            <label>University <b class="text-danger">*</b></label>
                                            
                                            <select class="form-control" name="id_kampus">
                                                <?php
                                                $sql = "SELECT * FROM tb_kampus";
                                                $sql = $_SERVER['KONEKSI_DB']->query($sql);

                                                foreach ($sql as $data) : ?>

                                                    <option value="<?= $data['id_kampus'] ?>" <?= ( old('id_kampus', $data_upkpk['id_kampus'] ) == $data['id_kampus'] ) ? 'selected' : ''; ?>><?= $data['nama_kampus'] ?></option>

                                                <?php endforeach; ?>
                                            </select>
                                            <?= form_error('id_kampus') ?>

                                        </div>


                                    </div>
                                    <div class="card-footer form-row">

                                        <div class="form-group col-md-12">

                                            <button type="button" class="btn btn-danger" onclick="confirmation()"><i class="fas fa-times mr-1"></i>Cancel</button>
                                            <button type="button" class="btn btn-warning" onclick="reset_confirmation()"><i class="fas fa-sync mr-1"></i>Reset</button>
                                            <button type="submit" class="btn btn-success"><i class="fas fa-edit mr-1"></i>Edit</button>

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

            $("#user_type").val("<?= old('user_type') ?>");
            $("#user_type").trigger('change');

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