<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = 'Import Company';
$route = 'company_management';
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
                        <h4 class="page-title">Import Company</h4>
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
                                <a href="index.php?page=company_management">Company Management</a>
                            </li>
                            <li class="separator">
                                <i class="flaticon-right-arrow"></i>
                            </li>
                            <li class="nav-item">
                                <a href="#">Import Company</a>
                            </li>
                        </ul>
                    </div>


                    <div class="row mt-2">
                        <div class="col-md-12">

                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Import Company

                                        <a data-toggle="tooltip" data-placement="right" data-original-title="Unduh Template Excel" target="_blank" href="<?= base_url('excel/template/import_perusahaan.xlsx') ?>" class="btn btn-primary btn-sm pull-right text-white"><i class="fas fa-file-excel mr-2"></i>Template</a>

                                    </h4>

                                </div>

                                <form id="preview" method="POST" enctype="multipart/form-data"> <?= csrf() ?>
                                    <div class="card-body form-row">
                                        <div class="form-group col-md-12">
                                            <input type="file" id="file" class="form-control" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" name="file" required />
                                        </div>

                                        <div class="form-group col-md-12">
                                            <!-- <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-file-import mr-2"></i>Import Data</button> -->
                                            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-eye mr-2"></i>Preview Data</button>
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
    <script src='./core/component/snackbar/dist/snackbar.min.js' charset='utf-8'></script>



    <script>
        $(document).ready(function(e) {


            $("#preview").on('submit', function(e) {

                e.preventDefault();


                let spinner = "<i class='fas fa-spinner fa-spin mr-1 fa-3x'></i>";
                $('#default_show').hide();
                $('#preview_data').show();
                $('#preview_data').html(spinner);


                // $('#status_import').html(data);

                $.ajax({
                    type: 'POST',
                    url: 'index.php?form=preview_import_perusahaan',
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,

                    success: function(data) {

                        $('#preview_data').html(data);
                    },
                    error: function() {
                        soloalert('error', 'Failed', 'Failed to fetch preview data !');

                        $('#default_show').show();
                        $('#preview_data').hide();
                        $('#preview_data').html();

                    },
                });


            });

            //file type validation
            $("#file").change(function() {
                var file = this.files[0];
                var imagefile = file.type;
                var match = ["application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"];
                if (!((imagefile == match[0]))) {
                    alert('Please select a valid excel file (.xlsx).');
                    $("#file").val('');
                    return false;
                }
            });
        });
    </script>


    <script type='text/javascript'>
        $("#import").on('submit', function(e) {

            let spinner = "<i class='fas fa-spinner fa-spin mr-1 fa-2x'></i> <span>Importing</span>";
            $('#preview_data2').html(spinner);


            e.preventDefault();

            $.ajax({
                type: 'POST',
                url: 'index.php?form=import_perusahaan',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,


                success: function(data, textStatus, XHR) {
                    $('#preview_data2').html('');
                    $('#preview_data').html(data);
                },
                error: function(data) {
                    $('#preview_data2').html('');

                    console.log('failed');
                    console.log(data);

                    // $('#upload_status').html('');
                    Snackbar.show({
                        text: 'Gagal import data, silahkan coba lagi nanti !',
                        pos: 'bottom-center',
                    });

                },
            });


        });


        function tutup_preview() {

            swal.fire({
                title: 'Close Confirmation',
                text: 'Are you sure want close current preview ?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {

                    $('#default_show').show();
                    $('#preview_data').hide();
                    $('#preview_data').html();;

                }
            })


        }
    </script>
</body>

</html>