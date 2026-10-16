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
                        <h4 class="page-title">MoU Detail</h4>
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
                                <a href="#">MoU Detail</a>
                            </li>
                        </ul>
                    </div>


                    <div class="row mt-2">


                        <div class="col-md-12">


                            <div class="card card-profile">
                                <div class="card-header" style="background-image: url('viewer.php?src=profile_background'); height: 300px; background-size: cover;background-repeat: no-repeat;">

                                    <div class="profile-picture">
                                        <div class="avatar avatar-xxl">
                                            <img src="viewer.php?src=get_company_logo&location=<?= base64_encode($company['logo']) ?>" alt="..." class="avatar-img rounded-circle">
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="user-profile text-center">
                                        <div class="name"><?= $company['name'] ?></div>
                                        <div class="job">Company engaged in <?= $company['type'] ?? 'NaN' ?></div>
                                    </div>

                                    <div class="card-body form-row">



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

                                            <input type="date" name="mou_date" required class="form-control <?= show_error('mou_date') ?>" value="<?= old('mou_date', $mou['mou_date']) ?>" />
                                            <?= form_error('mou_date'); ?>
                                        </div>


                                        <div class="form-group col-md-12">
                                            <label for="name">MoU Description <label class="text-danger">*</label></label>

                                            <textarea row="5" placeholder="Enter MoU Description" type="text" class="form-control <?= show_error('mou_description') ?>" name="mou_description" id="mou_description"><?= old('mou_description', $mou['mou_description']) ?></textarea>
                                            <?= form_error('mou_description'); ?>

                                        </div>


                                    </div>

                                </div>

                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center">
                                        <h4 class="card-title">MoU File</h4>
                                    </div>
                                </div>


                                <div class="card-body">
                                    <div class="card-body form-row">
                                        <div class="form-group col-md-12">
                                            <label for="name">MoU File <label class="text-danger">*</label></label>
                                            <div class="embed-responsive embed-responsive-16by9">
  <iframe class="embed-responsive-item" src="index.php?viewer=mou&id_mou=<?= base64_encode( $mou['id_mou'] ) ?>&file=<?= base64_encode($mou['mou_file'] )?>" allowfullscreen></iframe>
</div>
                                        </div>
                                    </div>
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