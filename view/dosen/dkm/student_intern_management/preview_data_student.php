<?php defined('BASE_PATH' or header('Location: /'));  ?>

<?php $id_kampus = $_SESSION['CDC-Data']['id_kampus'] ?? $_SESSION['Lecturer_Data']['id_kampus'] ?>



<div class="card">
    <div class="card-header">
        <h4 class="card-title">Import Student Data


            <a data-toggle="tooltip" data-placement="right" data-original-title="Close Preview" href="#" onclick="tutup_preview()" class="btn btn-danger btn-sm pull-right text-white mr-1"><i class="fas fa-times mr-2"></i>Close</a>

            <!-- <button data-toggle="tooltip" data-placement="right" data-original-title="Import Data" type="submit" class="btn btn-primary btn-sm pull-right text-white mr-1"><i class="fas fa-file-import mr-2"></i>Import</button> -->

            <?php if ($_SESSION['IMPORT']['STUDENT_OVERALL_IMPORT'] !== TRUE && count($_SESSION['IMPORT']['STUDENT']) > 0) : ?>
                <button data-toggle="tooltip" data-placement="right" data-original-title="Import Data" type="submit" class="btn btn-primary btn-sm pull-right text-white mr-1"><i class="fas fa-file-import mr-2"></i>Import</button>
            <?php endif; ?>

        </h4>
    </div>
    <div class="card-body ">

        <?php if ($data_bermasalah) : ?>

            <div class="alert alert-danger">
                <b><?= $total_kosong ?></b> data is empty or invalid ! Those data will be <b><em>skip</em></b> when importing !
            </div>

        <?php endif; ?>

    </div>





    <div class="card-body ">


        <?php if ($_SESSION['role'] != 'Dosen') : ?>
            <div class="form-group col-md-12 mb-2">
                <label>Study Program <b class="text-danger">*</b></label>


                <select id="program_study" name="program_study" class="form-control">
                    <?php

                    $sql = "SELECT DISTINCT(program_study.major) FROM program_study WHERE id_kampus = '$id_kampus' ORDER BY program_study.major";
                    $exec = $_SERVER['KONEKSI_DB']->query($sql);

                    foreach ($exec as $data_jurusan) :
                        $jurusan = $data_jurusan['major'];
                    ?>

                        <optgroup label="<?= $jurusan ?>">
                            <?php

                            $sql2 = "SELECT program_study.* FROM program_study WHERE program_study.major = '$jurusan' AND program_study.id_kampus = '$id_kampus' ORDER BY program_study.jenjang";
                            $exec2 = $_SERVER['KONEKSI_DB']->query($sql2);

                            foreach ($exec2 as $data_prodi) :
                            ?>

                                <option value="<?= $data_prodi['kode_prodi'] ?>" <?= ($_POST['program_study'] == $data_prodi['kode_prodi']) ? 'selected' : ''; ?>><?= $data_prodi['jenjang'] ?> <?= $data_prodi['prodi'] ?> - (<?= $data_prodi['jenjang'] ?> <?= $data_prodi['study_program'] ?>)</option>

                            <?php endforeach; ?>



                        </optgroup>

                    <?php endforeach; ?>
                </select>

                <?= form_error('program_study') ?>

            </div>
        <?php endif; ?>





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
                                <input class="form-check-input" type="checkbox" id="check_all" name="check_all" value="1" <?= ($_POST['check_all'] === NULL) ? 'checked' : '' ?> <?= ($_POST['check_all'] == 1) ? 'checked' : '' ?>>
                                <span class="form-check-sign">Check All</span>
                            </label>

                        </div>
                    </th>

                    <!-- <th width="5%">No</th> -->
                    <th>Import Status</th>
                    <th>NIM</th>
                    <th>Student Name</th>
                    <!-- <th>E-Mail</th> -->
                    <th>Lecturer Supervisor</th>
                    <th>Password</th>


                </tr>
            </thead>

            <tbody>

                <?php if (count($_SESSION['IMPORT']['STUDENT']) < 1) : ?>
                    <tr>
                        <td colspan='100%' class='text-center'>No data to import !</td>
                    </tr>
                <?php endif; ?>

                <?php //var_dump( $_SESSION['IMPORT']['STUDENT'] ); exit; ?>

                <?php foreach ($_SESSION['IMPORT']['STUDENT'] as $i => $data) : ?>

                    <tr>
                        <!-- <td><?= $i ?>.</td> -->
                        <td>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input type="hidden" name="import_status[<?= $i ?>]" value="0">
                                    <input class="form-check-input import_status" type="checkbox" value="1" name="import_status[<?= $i ?>]" <?= ($_POST['import_status'][$i] === NULL) ? 'checked' : ''; ?> <?= ($_POST['import_status'][$i] == 1) ? 'checked' : '' ?>>
                                    <span class="form-check-sign">Import</span>

                                </label>

                            </div>
                        </td>

                        <td class="<?php
                                    if ($data['import_status'] === false) {
                                        echo 'bg-danger text-white';
                                    } else if ($data['import_status'] === null) {
                                        echo 'bg-warning text-white';
                                    } else if ($data['import_status'] === true) {
                                        echo 'bg-success text-white';
                                    } ?>"><?= $data['import_review'] ?>
                        </td>


                        <td class="<?= (($data['student_nim']) == '') ? 'bg-danger' : ''; ?>"><?= $data['student_nim'] ?></td>
                        <td class="<?= (($data['student_name']) == '') ? 'bg-danger' : ''; ?>"><?= $data['student_name'] ?></td>
                        <!-- <td class="<?= (($data['student_email']) == '') ? 'bg-danger' : ''; ?>"><?= $data['student_email'] ?></td> -->
                        <td class="<?= (($data['lecturer_nik']) == '' && $data['lecturer_name'] == '') ? 'bg-danger text-white' : ''; ?>">
                            <?= ($data['lecturer_nik'] == '' && $data['lecturer_name'] == '') ? 'No Lecturer Supervisor found !' : $data['lecturer_nik'] . ' - ' . $data['lecturer_name']; ?>

                            <?= ($data['lecturer_status'] == 1) ? "<br><label class='btn btn-sm btn-success text-white'>" . $data['lecturer_status_message'] . "</label>" : "<br><label class='btn btn-sm btn-danger text-white'>" . $data['lecturer_status_message'] . "</label>"
                            ?>

                        </td>



                        <td class="<?= (($_SERVER['STUDENT_DEFAULT_PASSWORD']) == '') ? 'bg-danger' : ''; ?>">
                            <?php

                            if ($id_kampus == 1) {
                                echo "Login using <b>Learning Account</b>";
                            } else {
                                echo $_SERVER['STUDENT_DEFAULT_PASSWORD'];
                            }
                            ?>
                        </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>


    </div>





    <div id="tes"></div>

</div>