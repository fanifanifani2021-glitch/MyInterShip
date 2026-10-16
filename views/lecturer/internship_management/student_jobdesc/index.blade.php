@extends('templates.lecturer')


@push('head-scripts')

@php

// Ambil id_internship dari URL dan decode
$id_internship = base64_decode($_GET['id_internship']);

// Siapkan statement untuk mendapatkan data internship
$sql = "SELECT * FROM internship WHERE id_internship = :id_internship LIMIT 1";
$stmt = $_SERVER['PDO']->prepare($sql);

// Bind parameter dan eksekusi
$stmt->bindParam(':id_internship', $id_internship, PDO::PARAM_STR);
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

// Ambil data student_internship
$student_data = $stmt->fetch(PDO::FETCH_ASSOC);

// Cek Profile Jobdesk untuk prodi
$kode_prodi = $student_data['program_study'];
$sql = "SELECT * FROM jobdesc WHERE kode_prodi = :kode_prodi";
$stmt = $_SERVER['PDO']->prepare($sql);
$stmt->execute( ['kode_prodi' => $kode_prodi] );

if ( $stmt->rowCount() < 1 ) { make_swal2('error', 'Jobdesc Master data is not ready' , 'Ask internship coordinator to input it first' ); redirect_referrer(1); exit(); } $row=$stmt->fetch( PDO::FETCH_ASSOC );

    // Cek Jobdesk yang sudah diinput Supervisor
    $sql = "SELECT * FROM jobdesc_intern WHERE id_internship = :id_internship LIMIT 1";
    $stmt = $_SERVER['PDO']->prepare($sql);
    $stmt->execute( ['id_internship' => $id_internship] );

    $num = 0;

    if ( $stmt->rowCount() == 1 ) {
    $data_job_intern = $stmt->fetch(PDO::FETCH_ASSOC);
    $id_jobdesc_intern = $data_job_intern['id_jobdesc_intern'];
    $id_jobdesc = $data_job_intern['id_jobdesc'];

    $num = 1;

    $act = "index.php?form=jobdesk_spv&aksi=edit";
    } else {

    $id_jobdesc_intern = null;
    $id_jobdesc = null;
    $act = "index.php?form=jobdesk_spv&aksi=add";
    }
    @endphp
    @endpush

    @section('main-content')


    <div class="container">
        <div class="page-inner">
            <div class="page-header">
                <h4 class="page-title">Jobdesc</h4>
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
                        <a href="#">Jobdesc</a>
                    </li>




                </ul>
            </div>

            <div class="row">

                <!-- Jobdesc -->
                <div class="col-md-12">
                    <div class="card full-height">
                        <div class="card-header">
                            <h4 class="card-title">Internship Jobdesk


                            </h4>
                        </div>


                        @if ( $num != 1 )
                        <div class="card-body form-row">

                            <button class="btn btn-warning">Industry Supervisor for current Student has not fill out Jobdesk Intrenship yet.</button>

                        </div>

                        @else

                        <div class="card-body form-row">

                            <div class="form-group col-md-12">
                                <label>Question 1 <b class="text-danger">*</b></label> <br>
                                <label><?= $row['pertanyaan_1'] ?></label>

                                <textarea class="form-control <?= show_error('jawaban_1')?>" name="jawaban_1" id="jawaban_1" placeholder="Fill Answer for Question 1" rows="3"><?= $data_job_intern['jawaban_1']?></textarea>

                                <?= form_error('jawaban_1')?>
                            </div>


                            <div class="form-group col-md-12">
                                <label>Question 2 <b class="text-danger">*</b></label> <br>
                                <label><?= $row['pertanyaan_2'] ?></label>

                                <textarea class="form-control <?= show_error('jawaban_2')?>" name="jawaban_2" id="jawaban_2" placeholder="Fill Answer for Question 2" rows="3"><?= $data_job_intern['jawaban_2']?></textarea>

                                <?= form_error('jawaban_2')?>
                            </div>


                        </div>

                        @endif

                    </div>
                </div>

                @if ( in_array($row['need_monitoring'], ['YES', 'Yes', 'yes'] ) )

                @php
                $sql 	= "SELECT * FROM jobdesc WHERE kode_prodi = '$kode_prodi'";
                $row	= $_SERVER['KONEKSI_DB']->query($sql)->fetch_assoc();


                $sql = "SELECT * FROM jobdesc_intern_ceklis WHERE id_jobdesc_intern = '$id_jobdesc_intern'";
                $exec = $_SERVER['KONEKSI_DB']->query( $sql );
                @endphp

                <div class="col-md-12">
                    <div class="card full-height">
                        <div class="card-header">
                            <div class="card-title">Monitoring Tasks performed by students
                                <div class="float-right justify-content-beetwen">
                                    <select class="form-control">

                                        <?php
                                        $sql2 = "SELECT DISTINCT(`date_start`) AS tgl FROM jobdesc_intern_ceklis WHERE id_jobdesc_intern = '$id_jobdesc_intern'";
                                        $exec2 = $_SERVER['KONEKSI_DB']->query($sql2);
                
                                        if ($exec2->num_rows == 0) {
                                            echo "<option value=''>None</option>";
                                        } else {
                                            // echo "<option value='' hidden>- Select Filter Data -</option>";
                                            echo "<option value='all'>All Data</option>";
                                        }
                
                                        foreach ($exec2 as $tgl) : ?>
                                        <option><?= date('M Y', strtotime($tgl['tgl'])) ?></option>

                                        <?php endforeach; ?>

                                    </select>
                                </div>

                            </div>
                        </div>


                        <div class="card-body">

                            <table class="table table-bordered table-hover" id="basic-datatables">
                                <thead>
                                    <tr>
                                        <th width="5%">No.</th>
                                        <th>Job Type</th>
                                        <th>Description</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th width="5%">Action</th>
                                        <!-- <th>Action</th> -->
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                
                                        foreach ($exec as $i => $data_ceklis) :
                                            $id_ceklis = $data_ceklis['id_ceklis'];
                                            $id_detail = $data_ceklis['id_detail'];
                                            $ceklis = $data_ceklis['ceklis'];
                
                                            $sql = "SELECT * FROM detail_jobdesc WHERE id_detail = '$id_detail'";
                                            $data_jobdesc = $_SERVER['KONEKSI_DB']->query($sql)->fetch_assoc();
                
                                            if ($ceklis == 1) {
                                                $ceklis = "checked";
                                            } else {
                                                $ceklis = '';
                                            }
                                        ?>
                                    <tr>
                                        <td><?= $i + 1 ?>.</td>
                                        <td><?= $data_jobdesc['jenis_pekerjaan'] ?></td>
                                        <td><?= $data_jobdesc['job_description'] ?></td>
                                        <td><?= $data_ceklis['date_start'] ?></td>
                                        <td><?= $data_ceklis['date_end'] ?></td>

                                        <td>
                                            <div class="btn-group">
                                                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#ModalEdit<?= $i ?>"><i class="fas fa-trash mr-1"></i>Edit</button>
                                                <button class="btn btn-danger btn-sm" onclick="confirm_delete('<?= $id_ceklis ?>', '<?= $data_jobdesc['jenis_pekerjaan'] ?>')"><i class="fas fa-trash mr-1"></i>Delete</button>
                                            </div>
                                        </td>


                                    </tr>


                                    <?php
                                            $i++;
                                        endforeach; ?>
                                </tbody>
                            </table>

                        </div>



                    </div>
                </div>



                @else


                <div class="col-md-12">
                    <div class="card full-height">
                        <div class="card-header">
                            <h4 class="card-title">Tasks performed by students</h4>
                        </div>


                        <div class="card-body">

                            <table class="table table-bordered table-hover" id="basic-datatables">
                                <thead>
                                    <tr>
                                        <th width="5%">No.</th>
                                        <th>Job Type</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php


                                    $sql = "SELECT * FROM jobdesc_intern_ceklis WHERE id_jobdesc_intern = :id_jobdesc_intern";
                                    $stmt = $_SERVER['PDO']->prepare( $sql );
                                    $stmt->execute( ['id_jobdesc_intern' => $id_jobdesc_intern] );

                                    $data_task = $stmt->fetchAll( PDO::FETCH_ASSOC );
                                                
                                    foreach( $data_task as $i => $data_job ) : 
                                        $id_ceklis = $data_job['id_ceklis'];
                                        $id_detail = $data_job['id_detail'];
                                        $ceklis = $data_job['ceklis'];
            
                                        $sql = "SELECT * FROM detail_jobdesc WHERE id_detail = '$id_detail'";
                                        $data_jobdesc = $_SERVER['KONEKSI_DB']->query($sql)->fetch_assoc();
            
                                        if ( $ceklis == 1 ) {
                                            $ceklis = "checked";
                                        } else {
                                            $ceklis = '';
                                        }
                                    ?>
                                    <tr>
                                        <td><?= $i+1 ?>.</td>
                                        <td><?= $data_jobdesc['jenis_pekerjaan'] ?></td>
                                        <td><?= $data_jobdesc['job_description'] ?></td>
                                        <!-- <td>
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input disabled class="form-check-input toggle" type="checkbox" <?= $ceklis ?> >
                                                    <span class="form-check-sign">in accordance</span>
                                                </label>
                                            </div>
                                        </td> -->



                                    </tr>

                                    <?php 
                                        $i++; endforeach; ?>
                                </tbody>
                            </table>

                        </div>



                    </div>
                </div>

                @endif


            </div>



        </div>
    </div>

    @endsection


    @push('footer-scripts')
    <!-- Datatables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

    <!-- Datatables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>


    <script>
        $(document).ready(function() {

            let _token = $('meta[name="csrf-token"]').attr('content');

            // CKEDITOR
            let jawaban_1 = CKEDITOR.replace('jawaban_1', {
                language: 'en'
                , extraPlugins: 'editorplaceholder'
                , editorplaceholder: 'Fill your Answer for Question 1'
                , removeButtons: 'PasteFromWord'
            , });

            let jawaban_2 = CKEDITOR.replace('jawaban_2', {
                language: 'en'
                , extraPlugins: 'editorplaceholder'
                , editorplaceholder: 'Fill your Answer for Question 2'
                , removeButtons: 'PasteFromWord'
            , });


            $('#basic-datatables').DataTable({});

            $('#table').DataTable({});





        });

    </script>

    <script>
        'use strict';

        function submit_form() {
            $('form#form_jobdesc').submit();
        }

        function confirmation() {
            // alert('Are you sure you want to cancel ? (Your change will not be saved)');
            SoloAlert.confirm({
                title: "Are you sure you want to cancel ?"
                , body: "Changes you made will not be saved !"
                , useTransparency: true
                , onOk: () => {
                    disabled_form();
                }
            });

        }

        function reset_confirmation() {

            SoloAlert.confirm({
                title: "Are you sure you want to reset ?"
                , body: "Changes you made will be reset to default !"
                , useTransparency: true
                , onOk: () => {
                    form_reset();
                }
            });

        }




        function form_reset() {

            $("#form_jobdesc").trigger("reset");
        };

        function modal_hapus(id) {
            var id_hapus = document.getElementById('id_hapus');
            id_hapus.value = id;
        }

        function modal_edit(id, jenis, job) {
            $('#id_detail_edit').val(id);
            $('#jenis_pekerjaan_edit').val(jenis);
            $('#job_description_edit').text(job);
            $('#job_description_edit').val(job);

        }

    </script>

    @endpush
