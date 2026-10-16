<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

include "./view/upkpk/template/head.php";

?>

<body>
    <div class="wrapper">

        <?php include "./view/upkpk/template/main-header.php"; ?>
        <?php include "./view/upkpk/template/sidebar.php"; ?>

        <div class="main-panel">
            <div class="container">

                <div class="page-inner mt-2">

                    <div class="page-header">
                        <h4 class="page-title">Add MoU</h4>
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
                                <a href="index.php?page=company_mou_management">Company MoU Management</a>
                            </li>
                            <li class="separator">
                                <i class="flaticon-right-arrow"></i>
                            </li>
                            <li class="nav-item">
                                <a href="#">Add MoU</a>
                            </li>
                        </ul>
                    </div>


                    <div class="row mt-2">
                        <div class="col-md-12">

                            <div class="card full-height">
                                <div class="card-header">
                                    <div class="d-flex align-items-center">
                                        <h4 class="card-title">Add New Company MoU</h4>
                                    </div>
                                </div>

                                <form action="index.php?form=company_mou&action=add" method="POST" enctype="multipart/form-data" id="form_edit">
                                    <?= csrf(); ?>
                                    <?= input_hidden('redirect', $_GET['redirect']) ?>
                                    <div class="card-body form-row">



                                        <div class="form-group col-md-12">
                                            <label>Company <b class="text-danger">*</b></label>


                                            <select id="company" name="company" required style="width:inherit;" class="<?= show_error('company') ?>">
                                                <option value="" hidden>Choose one . . .</option>

                                                <?php

                                                $sql = "SELECT * FROM company";
                                                $exec = $_SERVER['KONEKSI_DB']->query($sql);

                                                foreach ($company as $key => $value) :
                                                ?>

                                                    <option value="<?= $value['id_company'] ?>"><?= $value['name'] ?></option>

                                                <?php endforeach; ?>
                                            </select>

                                            <?= form_error('company') ?>

                                            <?php if (in_array($_SESSION['role'], ['CDC', 'UPKPK'])) : ?>
                                                <label class="text-sm">Company does not exist ? <a href="<?= base_url('index.php?page=add_company&redirect=' . base64_encode('add_mou')) ?>">Add Company</a></label>
                                            <?php endif; ?>

                                        </div>


                                        <div class="form-group col-md-6">
                                            <label for="name">MoU Type <label class="text-danger">*</label></label>
                                            <select name="mou_type" class="form-control <?= show_error('mou_type') ?>">
                                                <!-- <option value="">- Choose One -</option> -->
                                                <option>Internship</option>
                                            </select>
                                            <?= form_error('mou_type'); ?>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="name">Date of MoU <label class="text-danger">*</label></label>
											<a class="float-right text-sm" href="#" data-toggle="tooltip" data-placement="top" title="Please enter the date when the Memorandum of Understanding (MoU) is signed"><i class="fas fa-question-circle mr-1"></i>What is this ?</a>

                                            <input type="date" name="mou_date" required class="form-control <?= show_error('mou_date') ?>" value="<?= old('mou_date') ?>" />
                                            <?= form_error('mou_date'); ?>
                                        </div>



                                        <div class="form-group col-md-12">
                                            <label for="name">MoU File <label class="text-danger">*</label></label>
                                            <input type="file" class="form-control <?= show_error('mou_file') ?>" accept="application/pdf" name="mou_file" id="mou_file" onchange="validateFile(this, '5MB')">
                                            <?= form_error('mou_file'); ?>

												<span class="text-sm float-right">Allowed file : pdf | Max Size : 5MB</span>

                                        </div>

                                        <div class="form-group col-md-12">
                                            <label for="name">MoU Description <label class="text-danger">*</label></label>

                                            <textarea row="5" placeholder="Enter MoU Description" type="text" class="form-control <?= show_error('mou_description') ?>" name="mou_description" id="mou_description"><?= old('mou_description') ?></textarea>
                                            <?= form_error('mou_description'); ?>

                                        </div>


                                    </div>
                                    <div class="modal-footer justify-content-start">
                                        <button onclick="cancel_confirmation('index.php?page=company_mou_management')" type="button" class="btn btn-danger"><i class="fas fa-times mr-1"></i>Cancel</button>
                                        <button onclick="reset_confirmation('form_edit')" type="button" class="btn btn-warning"><i class="fas fa-sync mr-1"></i>Reset</button>
                                        <button type="submit" class="btn btn-success"><i class="fas fa-save mr-1"></i>Save</button>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include "./view/upkpk/template/footer.php"; ?>

        </div>

    </div>

    <?php include "./view/upkpk/template/script.php"; ?>

    <!-- Soloalert -->
    <script src='./core/component/soloalert.js'></script>

    <!-- CKEDITOR -->
    <script src="./library/ckeditor/ckeditor.js"></script>

    <!-- Select2 -->
    <script src="./assets/js/plugin/select2/select2.full.min.js"></script>

    <script>
        $(document).ready(function() {

            $('#company').select2({
                theme: "bootstrap"
            });

            <?php if (old('company') != '') : ?>
                $('#company').val('<?= old('company') ?>').select2({
                    theme: "bootstrap"
                }).trigger('change');
            <?php endif; ?>

        });
    </script>


    <!-- User Custom Script -->
    <script>
        // Untuk Inisiasi Konfirmasi Cancel
        function cancel_confirmation(location) {
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
        function reset_confirmation(form) {

            SoloAlert.confirm({
                title: "Are you sure you want to reset ?",
                body: "Changes you made will be reset to default !",
                useTransparency: true,
                onOk: () => {
                    $("#" + form).trigger("reset");
                }
            });

        }
    </script>


</body>

</html>