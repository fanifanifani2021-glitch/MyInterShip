


<?php $__env->startPush('head-scripts'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">


<?php

$ps = $_GET['prodi'] ?? $_SESSION['prodi_koor'] ?? 'IF';

$sql = "SELECT * FROM jobdesc WHERE kode_prodi = :kode_prodi ORDER BY id_jobdesc DESC LIMIT 1";
$stmt = $_SERVER['PDO']->prepare($sql);
$stmt->execute( ['kode_prodi' => $ps ] );


if ($stmt->rowCount() == 0) {

$pertanyaan1 = "Do the Students do work in other fields of work, please provide more information";
$pertanyaan2 = "During the internship period, what contributions do you expect from the student?";


$data = [
'id_jobdesc' => NULL, 'kode_prodi' => $ps,
'pertanyaan_1' => $pertanyaan1, 'pertanyaan_2' => $pertanyaan2,
'need_monitoring' => 'No', 'additional_information' => '-'
];


// SQL query untuk insert data ke dalam tabel 'jobdesc'
$sql_insert = "INSERT INTO jobdesc (kode_prodi, pertanyaan_1, pertanyaan_2, need_monitoring, additional_information)
VALUES (:kode_prodi, :pertanyaan_1, :pertanyaan_2, :need_monitoring, :additional_information)";

// Persiapkan statement
$stmt_insert = $_SERVER['PDO']->prepare($sql_insert);

// Data binding
$stmt_insert->execute([
'kode_prodi' => $data['kode_prodi'],
'pertanyaan_1' => $data['pertanyaan_1'],
'pertanyaan_2' => $data['pertanyaan_2'],
'need_monitoring' => $data['need_monitoring'],
'additional_information' => $data['additional_information']
]);

// Mendapatkan ID dari record yang baru saja diinsert
$id_jobdesc = $_SERVER['PDO']->lastInsertId();


$sql = "SELECT * FROM jobdesc WHERE id_jobdesc = :id_jobdesc ORDER BY id_jobdesc DESC LIMIT 1";
$stmt = $_SERVER['PDO']->prepare($sql);
$stmt->execute([':id_jobdesc' => $id_jobdesc]);
$jobdesc_data = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
$jobdesc_data = $stmt->fetch(PDO::FETCH_ASSOC);
}

$id_jobdesc = $jobdesc_data['id_jobdesc'];

$sql = "SELECT * FROM detail_jobdesc WHERE id_jobdesc = :id_jobdesc ORDER BY id_detail DESC";
$stmt = $_SERVER['PDO']->prepare( $sql ); $stmt->execute([':id_jobdesc' => $id_jobdesc]);
$detail_jobdesc = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Get Study Program

// Query untuk mengambil data program_study dengan grouping berdasarkan jurusan/major
$sql = "SELECT * FROM program_study WHERE id_kampus = :id_kampus ORDER BY jurusan, jenjang ASC";
$stmt = $_SERVER['PDO']->prepare($sql);
$stmt->execute([':id_kampus' => $_SESSION['Lecturer_Data']['id_kampus']]);

// Ambil semua hasil
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Array untuk menyimpan program studi yang dikelompokkan berdasarkan jurusan
$program_studies = [];

foreach ($results as $row) {
// Kelompokkan berdasarkan jurusan
$program_studies[$row['major']][] = [
'kode_prodi' => $row['kode_prodi'],
'name' => $row['jenjang']. " " .$row['study_program']. " (" .$row['jenjang']. " " .$row['prodi']. ")",
];
}

?>

<?php $__env->stopPush(); ?>


<?php $__env->startSection('main-content'); ?>
<div class="container">
    <div class="page-inner">

        <div class="page-header">
            <h4 class="page-title">Jobdesk Management</h4>
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
                    <a href="#">Jobdesk Management</a>
                </li>
            </ul>
        </div>


        <!-- Form -->
        <div class="row">
            <div class="col-md-12">


                <div class="card full-height">
                    <div class="card-header">
                        <h4 class="card-title">Jobtype List

                            <?php if( $_SESSION['prodi_koor'] == $ps ): ?>
                            <button type="button" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#ModalAdd"><i class="fas fa-plus mr-2"></i>Add Data</button>
                            <?php endif; ?>
                        </h4>
                    </div>



                    <div class="card-body">

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="study-program-filter">Filter by Study Program:</label>
                                <select id="study-program-filter" class="form-control" disabled>

                                    <?php $__currentLoopData = $program_studies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $major => $programs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                    <optgroup label="<?php echo e(htmlspecialchars($major)); ?>">

                                        <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e(htmlspecialchars($program['kode_prodi'])); ?>" <?php if( $ps==$program['kode_prodi'] ): ?> selected <?php endif; ?>><?php echo e(htmlspecialchars($program['name'])); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    </optgroup>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>

                                <span id="study-program-notification"></span>
                            </div>

                        </div>

                        <table id="basic-datatables" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No</th>
                                    <th>Jobtype</th>
                                    <th>Job Description</th>
                                    <th style="width: 10%">Action</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-staff">
                                <?php
                                    $no = 1;
                                    foreach ($detail_jobdesc as $data) :
                                    ?>
                                <tr>
                                    <td><?= $no++ ?>.</td>
                                    <td><?= $data['jenis_pekerjaan'] ?></td>
                                    <td><?= $data['job_description'] ?></td>
                                    <td>
                                        <div class="btn-group btn-round">
                                            <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Action
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">

                                                <?php if( $_SESSION['prodi_koor'] == $ps ): ?>

                                                <button class="dropdown-item" data-toggle="modal" data-target="#ModalEdit" onclick="modal_edit('<?php echo $data['id_detail']; ?>', '<?php echo $data['jenis_pekerjaan']; ?>', '<?php echo $data['job_description'] ?>')"><i class="fa fa-edit mr-1"></i>Edit Data</button>
                                                <button class="dropdown-item" data-toggle="modal" data-target="#ModalHapus" onclick="modal_hapus('<?php echo $data['id_detail']; ?>')"><i class="fas fa-trash-alt mr-1"></i>Delete Data</button>
                                            
                                                <?php else: ?>
                                                <button class="dropdown-item">No Action</button>

                                                <?php endif; ?>
                                            
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>


                        <div class="modal fade" id="ModalAdd" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header no-bd">
                                        <h5 class="modal-title">
                                            <span class="fw-mediumbold">
                                                Add Jobtype</span>
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="index.php?form=jobdesk_dkm&aksi=addjob" method="POST"><?= csrf(); ?>


                                        <input type="hidden" name="id_jobdesc" id="id_jobdesc" value="<?= $row['id_jobdesc'] ?>">

                                        <div class="modal-body">

                                            <div class="form-group col-md-12">
                                                <label>Jobtype <b class="text-danger">*</b></label>

                                                <input required type="text" class="form-control" name="jenis_pekerjaan" id="jenis_pekerjaan" placeholder="Example : Designer" aria-label="Input Jobtype" aria-describedby="basic-addon2">


                                            </div>


                                            <div class="form-group col-md-12">
                                                <label>Job Description <b class="text-danger">*</b></label>
                                                <textarea required class="form-control" name="job_description" rows="5" placeholder="Example : Icons, Posters, Book Design"></textarea>
                                            </div>
                                        </div>

                                        <div class="modal-footer justify-content-between">
                                            <button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fas fa-times mr-2"></i>Cancel</button>
                                            <button class="btn btn-primary" type="submit"><i class="fas fa-save mr-1"></i>Save</button>

                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>

                        <!-- modal hapus -->
                        <div class="modal fade" id="ModalHapus" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header no-bd">
                                        <h5 class="modal-title">
                                            <span class="fw-mediumbold">
                                                Delete Data</span>
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="index.php?form=jobdesk_dkm&aksi=deljob" method="POST">
                                            <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?>">
                                            <input type="hidden" id="id_hapus" name="id_hapus" value="">
                                            Are You Sure to Delete Current Jobtype Data ?
                                    </div>
                                    <div class="modal-footer justify-content-between">
                                        <button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fas fa-times mr-2"></i>Cancel</button>
                                        <button type="submit" class="btn btn-primary pull-right"><i class="fas fa-trash-alt mr-2"></i>Delete</button>
                                    </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                        <!-- tutup modal -->


                        <!-- modal edit -->
                        <div class="modal fade" id="ModalEdit">
                            <div class="modal-dialog modal-md">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            <span class="fw-mediumbold">
                                                Edit Jobtype</span>
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>

                                    <form action="index.php?form=jobdesk_dkm&aksi=editjob" method="POST">
                                        <?= csrf() ?>
                                        <input type="hidden" value="" name="id_detail_edit" id="id_detail_edit">
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="jenis_pekerjaan_edit">Jobtype <label class="text-danger">*</label></label>
                                                <input type="text" placeholder="Input Jobtype" class="form-control <?= show_error('jenis_pekerjaan_edit') ?>" name="jenis_pekerjaan_edit" id="jenis_pekerjaan_edit" required>
                                                <?= form_error('jenis_pekerjaan_edit'); ?>
                                            </div>

                                            <div class="form-group">
                                                <label for="job_description_edit">Job Description <label class="text-danger">*</label></label>
                                                <textarea rows="5" type="text" placeholder="Input Jobtype" class="form-control <?= show_error('job_description_edit') ?>" name="job_description_edit" id="job_description_edit" required></textarea>
                                                <?= form_error('job_description_edit'); ?>
                                            </div>
                                        </div>

                                        <div class="modal-footer justify-content-between">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times mr-2"></i>Close</button>
                                            <button type="submit" class="btn btn-primary"><i class="fas fa-edit mr-2"></i>Edit</button>
                                        </div>
                                    </form>

                                </div>
                                <!-- /.modal-content -->

                            </div>
                            <!-- /.modal-dialog -->
                        </div>
                        <!-- /.tutupmodal -->

                    </div>
                </div>
            </div>

            <!-- Jobdesc -->
            <div class="col-md-12">
                <div class="card full-height">
                    <div class="card-header">
                        <h4 class="card-title">Additional Question</h4>
                    </div>

                    <form class="" action="<?= $act; ?>" method="POST" id="form_jobdesc"><?= csrf(); ?>
                        <input type="hidden" name="id_jobdesc" id="id_jobdesc" value="<?= $jobdesc_data['id_jobdesc'] ?>">
                        <input type="hidden" name="prodi" id="prodi" value="<?= $ps ?>">
                        <input type="hidden" name="need_monitoring" id="need_monitoring" value="No">
                        <input type="hidden" name="additional_information" id="additional_information" value="-">


                        <div class="card-body form-row">

                            <div class="form-group col-md-12">
                                <label>Question 1 <b class="text-danger">*</b></label>
                                <textarea class="form-control <?= show_error('pertanyaan_1') ?>" name="pertanyaan_1" id="pertanyaan_1" placeholder="Fill Question 1" rows="3"><?php echo e($jobdesc_data['pertanyaan_1']); ?></textarea>

                                <?= form_error('pertanyaan_1') ?>
                            </div>

                            <div class="form-group col-md-12">
                                <label>Question 2 <b class="text-danger">*</b></label>
                                <textarea class="form-control <?= show_error('pertanyaan_2') ?>" name="pertanyaan_2" id="pertanyaan_2" placeholder="Fill Question 2" rows="3"><?php echo e($jobdesc_data['pertanyaan_2']); ?></textarea>
                                <?= form_error('pertanyaan_2') ?>
                            </div>


                        </div>
                    </form>


                </div>
            </div>


        </div>


    </div>



</div>
<?php $__env->stopSection(); ?>



<?php $__env->startPush('footer-scripts'); ?>
<!-- Datatables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

<!-- Datatables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

<!-- JS Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>


<script>
    $(document).ready(function() {
        let _token = $('meta[name="csrf-token"]').attr('content');

        let table =
            $('#basic-datatables').DataTable({
                "responsive": true
                , "pageLength": 15
                , "lengthMenu": [10, 15, 25, 50]
            });


        function enableAllElements() {
            $('select, input[type="checkbox"], button').prop('disabled', false);
        }

        $('#study-program-filter').select2({
            theme: 'bootstrap4'
        });


        setTimeout(() => {
            enableAllElements();
        }, 1000);


        $('#study-program-filter').change(function() {

            $('#study-program-notification').html("<i class='fa fa-spinner fa-spin'></i> Redirecting . . .");
            $(this).prop('disabled', true);
            var selectedValue = $(this).val();
            var baseUrl = 'index.php?page=jobdesk_management';
            var url = selectedValue ? baseUrl + '&prodi=' + encodeURIComponent(selectedValue) : baseUrl;

            window.location.href = url;
        });

    });

</script>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('templates.lecturer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp_new\htdocs\pblKita\views/lecturer/jobdesk_management/index.blade.php ENDPATH**/ ?>