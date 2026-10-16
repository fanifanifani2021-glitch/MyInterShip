<?php defined('BASE_PATH' or header('Location: /'));  ?>

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

<script>
    $('#export-account').on('click', function(e) {
        window.open(
            '<?= base_url('?page=export_company&type=account_only&from=import_company') ?>',
            '_blank' // <- This is what makes it open in a new window.
        );
    });
</script>


<div class="card">
    <div class="card-header">
        <h4 class="card-title">Import Company Data

            <a data-toggle="tooltip" data-placement="right" data-original-title="Tutup Preview" href="#" onclick="tutup_preview()" class="btn btn-danger btn-sm pull-right text-white mr-1"><i class="fas fa-times mr-2"></i>Tutup</a>

            <?php if ($_SESSION['IMPORT']['COMPANY_OVERALL_IMPORT'] !== TRUE && count($_SESSION['IMPORT']['COMPANY']) > 0) : ?>
                <button data-toggle="tooltip" data-placement="right" data-original-title="Import Data" type="submit" class="btn btn-primary btn-sm pull-right text-white mr-1"><i class="fas fa-file-import mr-2"></i>Import</button>
            <?php else : ?>
                <button data-toggle="tooltip" id="export-account" data-placement="right" data-original-title="Export Data" type="button" class="btn btn-primary btn-sm pull-right text-white mr-1"><i class="fas fa-file-export mr-2"></i>Export Account</button>

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

        <?php if ($_SESSION['IMPORT']['COMPANY_OVERALL_IMPORT'] != true) : ?>
            <!-- <div class="form-group mb-2 pull-left">
                <button data-toggle="tooltip" data-placement="right" data-original-title="Import Data" type="submit" class="btn btn-primary btn-sm pull-right text-white mr-1"><i class="fas fa-file-import mr-2"></i>Import</button>
            </div> -->
        <?php endif; ?>





        <table class="table table-bordered table-responsive">
            <thead>
                <tr>
                    <!-- <th width="5%">
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="checkbox" id="check_all" checked="checked">
                                    <span class="form-check-sign">Check All</span>
                                </label>

                            </div>
                        </th> -->

                    <th width="5%">
                        <div class="form-check">
                            <label class="form-check-label">
                                <input type="hidden" name="check_all" value="0">
                                <input class="form-check-input" type="checkbox" id="check_all" name="check_all" value="1" <?= ($_POST['check_all'] === NULL) ? 'checked' : '' ?> <?= ($_POST['check_all'] == 1) ? 'checked' : '' ?>>
                                <span class="form-check-sign">Check All</span>
                            </label>

                        </div>
                    </th>

                    <!-- <th width="5%">No</th> -->
                    <th>Import Status</th>
                    <th>Company Name</th>
                    <th>Company E-Mail</th>
                    <th>Company Telp</th>
                    <th>Address</th>
                    <th>City</th>
                    <th>Province</th>
                    <th>Access Type</th>
                    <th>Company Status</th>
                    <th>User HRD</th>
                    <th>Password</th>



                </tr>
            </thead>

            <tbody>

                <?php if (count($_SESSION['IMPORT']['COMPANY']) < 1) : ?>
                    <tr>
                        <td colspan='100%' class='text-center'>No data !</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($_SESSION['IMPORT']['COMPANY'] as $i => $data) : ?>

                    <tr>
                        <!-- <td><?= $i - 1 ?>.</td> -->
                        <td>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input type="hidden" name="import_status[<?= $i ?>]" value="0">
                                    <input class="form-check-input import_status" type="checkbox" value="1" name="import_status[<?= $i ?>]" <?= ($_POST['import_status'][$i] === NULL) ? 'checked' : ''; ?> <?= ($_POST['import_status'][$i] == 1) ? 'checked' : '' ?>>
                                    <span class="form-check-sign">Import</span>

                                </label>

                            </div>
                        </td>

                        <td class="text-white <?php
                                                if ($data['import_status'] === false) {
                                                    echo 'bg-danger';
                                                } else if ($data['import_status'] === null) {
                                                    echo 'bg-warning';
                                                } else if ($data['import_status'] === true) {
                                                    echo 'bg-success';
                                                } ?>">
                            <?php
                            if ($data['import_status'] === false) {
                                echo 'Failed to Import';
                            } else if ($data['import_status'] === null) {
                                echo 'Not Yet Imported';
                            } else if ($data['import_status'] === true) {
                                echo 'Success';
                            } ?>
                        </td>


                        <td class="<?= (($data['nama_perusahaan']) == '') ? 'bg-danger' : ''; ?>"><?= $data['nama_perusahaan'] ?></td>
                        <td class="<?= (($data['email']) == '') ? 'bg-danger' : ''; ?>"><?= $data['email'] ?></td>
                        <td class="<?= (($data['no_telp']) == '') ? 'bg-danger' : ''; ?>"><?= $data['no_telp'] ?></td>
                        <td class="<?= (($data['alamat']) == '') ? 'bg-danger' : ''; ?>"><?= $data['alamat'] ?></td>
                        <td class="<?= (($data['city']) == '') ? 'bg-danger' : ''; ?>"><?= $data['city'] ?></td>
                        <td class="<?= (($data['province']) == '') ? 'bg-danger' : ''; ?>"><?= $data['province'] ?></td>
                        <td class="<?= (($data['access_type']) == '') ? 'bg-danger' : ''; ?>"><?= $data['access_type'] ?></td>
                        <td class="<?= (($data['company_status']) == '') ? 'bg-danger' : ''; ?>"><?= $data['company_status'] ?></td>

                        <td class="<?= (($data['username']) == '') ? 'bg-danger' : ''; ?>"><?= $data['username'] ?></td>
                        <td class="<?= (($data['password']) == '') ? 'bg-danger' : ''; ?>"><?= $data['password'] ?></td>

                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>


    </div>





    <div id="tes"></div>

</div>