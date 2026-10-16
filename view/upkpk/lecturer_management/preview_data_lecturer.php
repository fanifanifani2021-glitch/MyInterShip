<?php defined('BASE_PATH' or header('Location: /'));  ?>


<div class="card">
    <div class="card-header">
        <h4 class="card-title">Import Lecturer Data


            <a data-toggle="tooltip" data-placement="right" data-original-title="Close Preview" href="#" onclick="tutup_preview()" class="btn btn-danger btn-sm pull-right text-white mr-1"><i class="fas fa-times mr-2"></i>Close</a>

            <!-- <button data-toggle="tooltip" data-placement="right" data-original-title="Import Data" type="submit" class="btn btn-primary btn-sm pull-right text-white mr-1"><i class="fas fa-file-import mr-2"></i>Import</button> -->

            <?php if ($_SESSION['IMPORT']['LECTURER_OVERALL_IMPORT'] !== TRUE && count($_SESSION['IMPORT']['LECTURER']) > 0 ) : ?>
                <button data-toggle="tooltip" data-placement="right" data-original-title="Import Data" type="submit" class="btn btn-primary btn-sm pull-right text-white mr-1"><i class="fas fa-file-import mr-2"></i>Import</button>
            <?php endif; ?>

        </h4>
    </div>
    <div class="card-body ">

        <?php if ($data_bermasalah) : ?>

            <div class="alert alert-danger">
                Some <b><?= $total_kosong ?></b> data is empty or invalid ! Those data will be <b><em>skip</em></b> when importing !
            </div>

        <?php endif; ?>

    </div>





    <div class="card-body ">





        <script>
            $('#check_all').change(function() {

                if ($(this).prop('checked')) {
                    $('.import_status').prop('checked', 'checked');
                } else {
                    $('.import_status').prop('checked', '');
                }
            });

            $('.import_status').change(function() {
                let numberOfChecked = $('.import_status:checkbox:checked').length
                let totalCheckboxes = $('.import_status').length;
                var numberNotChecked = totalCheckboxes - numberOfChecked;

                if (numberNotChecked == 0) {
                    $('#check_all').prop('checked', 'checked');
                } else {
                    $('#check_all').prop('checked', '');
                }
            });
        </script>


        <table class="table table-bordered table-responsive">
            <thead>
                <tr>
                    <th width="5%">
                        <div class="form-check">
                            <label class="form-check-label">
                                <input type="hidden" name="check_all" value="0">
                                <input class="form-check-input" type="checkbox" id="check_all" name="check_all" value="1" <?= ( $_POST['check_all'] === NULL ) ? 'checked' : ''?> <?= ( $_POST['check_all'] == 1  ) ? 'checked' : '' ?> >
                                <span class="form-check-sign">Check All</span>
                            </label>

                        </div>
                    </th>

                    <!-- <th width="5%">No</th> -->
                    <th>Import Status</th>
                    <th>Lecturer NIK</th>
                    <th>Lecturer Name</th>
                    <th>Password</th>


                </tr>
            </thead>

            <tbody>

                <?php if (count($_SESSION['IMPORT']['LECTURER']) < 1) : ?>
                    <tr>
                        <td colspan='100%' class='text-center'>No data to import !</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($_SESSION['IMPORT']['LECTURER'] as $i => $data) : ?>

                    <tr>
                        <!-- <td><?= $i ?>.</td> -->
                        <td>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input type="hidden" name="import_status[<?= $i ?>]" value="0">
                                    <input class="form-check-input import_status" type="checkbox" value="1" name="import_status[<?= $i ?>]" <?= ( $_POST['import_status'] [$i] === NULL ) ? 'checked' : ''; ?> <?= ( $_POST['import_status'] [$i] == 1 ) ? 'checked' : ''?>>
                                    <span class="form-check-sign">Import</span>

                                </label>

                            </div>
                        </td>

                        <td class="<?php
                                    if ($data['import_status'] === false) {
                                        echo 'bg-danger';
                                    } else if ($data['import_status'] === null) {
                                        echo 'bg-warning';
                                    } else if ($data['import_status'] === true) {
                                        echo 'bg-success';
                                    } ?>"><?= $data['import_review'] ?>
                        </td>


                        <td class="<?= (($data['lecturer_nik']) == '') ? 'bg-danger' : ''; ?>"><?= $data['lecturer_nik'] ?></td>
                        <td class="<?= (($data['lecturer_name']) == '') ? 'bg-danger' : ''; ?>"><?= $data['lecturer_name'] ?></td>
                        
                        <td class="<?= (($_SERVER['LECTURER_DEFAULT_PASSWORD']) == '') ? 'bg-danger' : ''; ?>"><?= $_SERVER['LECTURER_DEFAULT_PASSWORD'] ?></td>
                        
                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>


    </div>





    <div id="tes"></div>

</div>