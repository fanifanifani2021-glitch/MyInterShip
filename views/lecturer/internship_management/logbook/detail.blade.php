@extends('templates.lecturer')


@push('head-scripts')

<!-- CSS Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
<style>
    #check-all.indeterminate~.custom-control-label::before {
        content: "-";
    }

</style>

@php

// Ambil id_internship dari URL dan decode
$id_logbook = base64_decode($_GET['id_logbook']);
$id_internship = base64_decode($_GET['id_internship']);
$nim = base64_decode($_GET['nim']);

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


$sql = "SELECT * FROM logbook WHERE id_internship = :id_internship AND id_logbook = :id_logbook LIMIT 1";

$stmt = $_SERVER['PDO']->prepare($sql);
$stmt->execute([':id_internship' => $id_internship, ':id_logbook' => $id_logbook]);

$logbook_data = $stmt->fetch(PDO::FETCH_ASSOC);

@endphp
@endpush

@section('main-content')


<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title">Logbook Detail</h4>
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
                    <a href="index.php?page=intern_student_logbook&nim={{ base64_encode($nim) }}&id_internship=<?= base64_encode($id_internship) ?>">Logbook</a>
                </li>

                <li class="separator">
                    <i class="flaticon-right-arrow"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Logbook Detail</a>
                </li>





            </ul>
        </div>

        <div class="row">

            <!-- Internship Under Supervisor -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Logbook Detail
                            <div class="float-right justify-content-beetwen">
                                <button onclick="window.location.href='index.php?page=print_logbook_internship&id_logbook=<?= base64_encode($id_logbook) ?>'" class="btn btn-info btn-sm btn-round mr-1"><i class="fas fa-download mr-2"></i>Download</button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body form-row">

                        <div class="form-group col-md-4">
                            <label>Week Num <b class="text-danger">*</b></label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1">M</span>
                                </div>
                                <input type="number" min="0" name="week_num" class="form-control" value="<?= $logbook_data['week_num'] ?>" />
                            </div>
                        </div>


                        <div class="form-group col-md-4">
                            <label>Start Date <b class="text-danger">*</b></label>
                            <input type="date" name="start_date" class="form-control" value="<?= $logbook_data['start_date'] ?>" />
                        </div>

                        <div class="form-group col-md-4">
                            <label>End Date <b class="text-danger">*</b></label>
                            <input type="date" name="end_date" class="form-control" value="<?= $logbook_data['end_date'] ?>" />
                        </div>


                        <div class="form-group col-md-12">
                            <label class="label-control">Description <b class="text-danger">*</b></label>
                            <textarea class="form-control" name="description" rows="10" placeholder="Describe all job you`ve done this week"><?= $logbook_data['description'] ?></textarea>

                        </div>

                        <div class="form-group col-md-12">
                            <label class="label-control">Documentation <b class="text-danger">*</b></label>
                            <textarea class="form-control" name="documentation" rows="10" placeholder="Provide some photos to prove your job"><?= $logbook_data['documentation'] ?></textarea>

                        </div>







                    </div>



                </div>
            </div>
        </div>



    </div>
</div>

@endsection

@push('footer-scripts')

@endpush
