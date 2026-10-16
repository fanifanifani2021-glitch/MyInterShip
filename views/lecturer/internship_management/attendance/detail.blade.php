@extends('templates.lecturer')


@push('head-scripts')
@php

try {
// Ambil id_internship dari URL dan decode
$id_internship = base64_decode($_GET['id_internship']);
$nim = base64_decode($_GET['nim']);
$id_attendance = base64_decode($_GET['id_attendance']);

// Siapkan statement untuk mendapatkan data internship
$sql = "SELECT * FROM internship WHERE id_internship = :id_internship AND nim = :nim LIMIT 1";
$stmt = $_SERVER['PDO']->prepare($sql);

// Bind parameter dan eksekusi
$stmt->bindParam(':id_internship', $id_internship, PDO::PARAM_STR);
$stmt->bindParam(':nim', $nim, PDO::PARAM_STR);
$stmt->execute();

// Cek apakah data ditemukan
if ($stmt->rowCount() != 1) {
make_swal2('error', 'Not Allowed', 'Internship Data is not found!');
redirect_back(1);
exit;
}

// Ambil data internship
$internship_data = $stmt->fetch(PDO::FETCH_ASSOC);
$nim = $internship_data['nim'];

// Siapkan statement untuk mendapatkan data student_internship
$sql = "SELECT * FROM student_internship WHERE nim = :nim AND nik_dospem = :nik_dospem LIMIT 1";
$stmt = $_SERVER['PDO']->prepare($sql);
$stmt->execute(['nim' => $nim, 'nik_dospem' => $_SESSION['nim_nik_unit']]);

if ($stmt->rowCount() != 1) {
make_swal2('error', 'Not Allowed', 'Only Supervsior can view student data !');
redirect_back(1);
exit;
}

$student_data = $stmt->fetch(PDO::FETCH_ASSOC);


$sql = "SELECT `attendance`.* , `attendance-signature`.`signature` FROM attendance
LEFT JOIN `attendance-signature` ON `attendance-signature`.`id_attendance` = `attendance`.`id_attendance`
WHERE attendance.id_internship = :id_internship AND attendance.id_attendance = :id_attendance";

$stmt = $_SERVER['PDO']->prepare($sql);
$stmt->execute([':id_internship' => $id_internship, 'id_attendance' => $id_attendance]);

if ( $stmt->rowCount() != 1 ) {
make_swal2('warning', 'Not Found', 'Attendance data is not found !');
redirect_back(1); exit;
}

$attendance_data = $stmt->fetch( PDO::FETCH_ASSOC );
} catch (PDOException $e ) {
make_swal2('warning', 'Not Found', 'Attendance data is not found !');
redirect_back(1); exit;
}

@endphp
@endpush

@section('main-content')


<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title">Attendance Detail</h4>
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
                    <a href="index.php?page=intern_student_management">Students Management</a>
                </li>

                <li class="separator">
                    <i class="flaticon-right-arrow"></i>
                </li>
                <li class="nav-item">
                    <a href="index.php?page=intern_student_detail&nim=<?= base64_encode($nim) ?>">Student Detail</a>
                </li>

                <li class="separator">
                    <i class="flaticon-right-arrow"></i>
                </li>
                <li class="nav-item">
                    <a href="index.php?page=intern_student_internship_detail&id_internship=<?= base64_encode($id_internship) ?>">Internship Detail</a>
                </li>

                <li class="separator">
                    <i class="flaticon-right-arrow"></i>
                </li>
                <li class="nav-item">
                    <a href="index.php?page=intern_student_attendance&nim={{ base64_encode($nim) }}&id_internship={{ base64_encode($id_internship) }}">Attendance</a>
                </li>

                <li class="separator">
                    <i class="flaticon-right-arrow"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Attendance Detail</a>
                </li>




            </ul>
        </div>

        <div class="row">

            <!-- Jobdesc -->
            <div class="col-md-12">
                <div class="card full-height">
                    <div class="card-header">
                        <h4 class="card-title">Attendance Detail
                        </h4>
                    </div>


                    <!-- Student's Data -->
                    <div class="card-body form-row">


                        <div class="form-group col-md-4">
                            <label>Week Num <b class="text-danger">*</b></label>

                            <select class="form-control <?= show_error('week_num') ?>" name="week_num">
                                <!-- <option value="" hidden>- Select one -</option> -->
                                <option hidden><?= $attendance_data['week']?></option>
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                                <option>5</option>
                            </select>

                            <!-- <div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text" id="basic-addon1">M</span>
											</div>
											<input type="number" min="0" name="week_num" class="form-control <?= show_error('week_num'); ?>" />
										</div> -->
                            <?= form_error('week_num'); ?>
                        </div>


                        <div class="form-group col-md-4 ">
                            <label>Date <b class="text-danger">*</b></label>
                            <input type="date" name="attendance_date" value="<?= $attendance_data['date'] ?>" class="form-control <?= show_error('attendance_date'); ?>" readonly />
                            <?= form_error('attendance_date'); ?>
                        </div>

                        <div class="form-group col-md-4 ">
                            <label>Attendance Type <b class="text-danger">*</b></label>

                            <select class="form-control <?= show_error('attendance_type') ?>" name="attendance_type" id="attendance_type" onchange="cek_absen()">
                                <!-- <option value="" hidden>- Select one -</option> -->
                                <option hidden><?= $attendance_data['attendance_type']?></option>

                                <!-- <option>Present</option> -->
                                <option>National Holiday</option>
                                <option>Abstaint</option>
                                <option>Permit</option>
                                <option>Sick Leave</option>
                            </select>
                            <?= form_error('attendance_type'); ?>

                        </div>

                        <script>
                            function cek_absen() {

                                var value = document.getElementById('attendance_type').value;
                                var jam_absen = document.getElementsByClassName('jam_absen');


                                if (value == 'Present') {
                                    for (var x = 0; x <= jam_absen.length; x++) {
                                        jam_absen[x].removeAttribute('hidden');
                                    }
                                } else {
                                    for (var x = 0; x <= jam_absen.length; x++) {
                                        jam_absen[x].setAttribute('hidden', 'hidden');

                                    }
                                }
                            }

                        </script>



                        <div class="form-group col-md-6 jam_absen" hidden>
                            <label>Check In <b class="text-danger">*</b></label>
                            <input type="time" name="check_in" value="<?= date('H:i', strtotime($attendance_data['check_in'])) ?>" class="form-control <?= show_error('check_in'); ?>" readonly />
                            <?= form_error('check_in'); ?>
                        </div>

                        <div class="form-group col-md-6 jam_absen" hidden>
                            <label>Check Out <b class="text-danger">*</b></label>
                            <input type="time" name="check_out" value="<?= date('H:i', strtotime($attendance_data['check_out'])) ?>" class="form-control <?= show_error('check_out'); ?>" readonly />
                            <?= form_error('check_out'); ?>
                        </div>


                        <div class="form-group col-md-12">
                            <label class="label-control">Description <b class="text-danger">*</b></label>
                            <textarea class="form-control <?= show_error('description')?>" id="description" name="description" rows="5" placeholder="Describe how was your day going or reason why you're not present on that day"><?= $attendance_data['description']?></textarea>
                            <?= form_error('description'); ?>

                        </div>


                        @if ( $attendance_data['signature'] !== NULL )
                        <div class="form-group col-md-12">

                            <label class="label-control">Student Signature <b class="text-danger">*</b></label>

                            <div class="form-control">
                                <img src="{{ base_url('index.php?viewer=internship_signature&id_internship=' .base64_encode( $id_internship ). '&id_attendance=' .base64_encode( $id_attendance ) ) }}" class="img-fluid" />
                            </div>

                        </div>
                        @endif


                    </div>


                </div>


            </div>



        </div>



    </div>
</div>

@endsection

@push('footer-scripts')
<!-- CKEDITOR -->
<script src="./library/ckeditor/ckeditor.js"></script>

<script>
    // CKEDITOR Initialize
    $(function() {
        // CKEDITOR
        var description = CKEDITOR.replace('description', {
            language: 'en'
            , extraPlugins: 'editorplaceholder'
            , editorplaceholder: 'Describe how was your day going or reason why you`re not present on that day'
            , removeButtons: 'PasteFromWord'
        , });

    });

</script>
@endpush
