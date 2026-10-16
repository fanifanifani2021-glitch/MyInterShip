<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = "Import Study Program";
$route = 'study_program_management';
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
                        <h4 class="page-title">Import Study Program</h4>
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
                                <a href="index.php?page=study_program_management">Study Program Management</a>
                            </li>
                            <li class="separator">
                                <i class="flaticon-right-arrow"></i>
                            </li>
                            <li class="nav-item">
                                <a href="#">Import Study Program</a>
                            </li>
                        </ul>
                    </div>


                    <div class="row mt-2">
                        <div class="col-md-12">

                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Import Study Program

                                        <a data-toggle="tooltip" data-placement="right" data-original-title="Excel Template" target="_blank" href="<?= base_url('excel/template/import_study_program.xlsx') ?>" class="btn btn-primary btn-sm pull-right text-white"><i class="fas fa-file-excel mr-2"></i>Template</a>

                                    </h4>

                                </div>

                                <form id="preview" method="POST" enctype="multipart/form-data"> <?= csrf() ?>
                                    <div class="card-body form-row">
                                        <div class="form-group col-md-12">
                                            <input type="file" id="file" class="form-control" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" name="file" required />
                                        </div>

                                        <div class="form-group col-md-12">
                                            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-file-import mr-2"></i>Import Data</button>
                                        </div>
                                    </div>
                                </form>

                            </div>

                            <div id="preview_data2"></div>


                            <form method='GET' id="import" > <?= csrf() ?>

                                <div id="preview_data"></div>

                            </form>

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


    <script>
        $(document).ready(function() {
            toggle_company();

            var description = CKEDITOR.replace('description', {
                language: 'en',
                extraPlugins: 'editorplaceholder',
                editorplaceholder: 'Enter company description',
                removeButtons: 'PasteFromWord,Styles,Format,Font,FontSize',
            });
        });

        $(document).ready(function() {

            setTimeout(function() {

                $('#cke_description').addClass('<?= show_error('description') ?>');

            }, 500);
        })
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