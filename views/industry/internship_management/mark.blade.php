@extends('templates.industry')


@section('main-content')

@php

$pdo = $_SERVER['PDO'];

$id_internship = base64_decode($_GET['id_internship']);

// Query pertama dengan LIMIT 1
$sql = "SELECT * FROM internship WHERE id_internship = :id_internship LIMIT 1";
$stmt = $_SERVER['PDO']->prepare($sql);
$stmt->bindParam(':id_internship', $id_internship, PDO::PARAM_STR);
$stmt->execute();
$exec = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$exec) {
make_swal2('error', 'Not Allowed', 'Internship Data is not found!');
redirect_back(1);
exit;
}

$internship_data = $exec;
$nim = $internship_data['nim'];

// Query kedua dengan LEFT JOIN dan LIMIT 1
$sql2 = "SELECT * FROM student_internship
LEFT JOIN program_study ON student_internship.program_study = program_study.kode_prodi
WHERE student_internship.nim = :nim LIMIT 1";
$stmt2 = $_SERVER['PDO']->prepare($sql2);
$stmt2->bindParam(':nim', $nim, PDO::PARAM_STR);
$stmt2->execute();
$student_data = $stmt2->fetch(PDO::FETCH_ASSOC);

$id_kampus = $student_data['id_kampus'];

// Query ketiga untuk data kampus
$sql3 = "SELECT tb_kampus.* FROM tb_kampus WHERE id_kampus = :id_kampus";
$stmt3 = $_SERVER['PDO']->prepare($sql3);
$stmt3->bindParam(':id_kampus', $id_kampus, PDO::PARAM_INT);
$stmt3->execute();
$university = $stmt3->fetch(PDO::FETCH_ASSOC);


@endphp
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title">Student Detail</h4>
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
                    <a href="index.php?page=accepted_intern_management">Accepted Intern</a>
                </li>

                <li class="separator">
                    <i class="flaticon-right-arrow"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Student Detail</a>
                </li>



            </ul>
        </div>

        <div class="row">


            <!-- Lecturer's Profile -->
            <div class="col-md-12">
                <div class="card card-profile">
                    <div class="card-header" style="background-image: url('viewer.php?src=profile_background'); height: 250px; background-size:cover;">
                        <div class="profile-picture">
                            <div class="avatar avatar-xxl">
                                <img src="viewer.php?src=get_profile&location=<?= base64_encode($student_data['profile_picture']) ?>" alt="Student Profile Picture" class="avatar-img rounded-circle">
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="user-profile text-center">
                            <div class="name"><?= $student_data['name'] ?></div>
                            <div class="desc">Student at <?= $university['nama_kampus'] ?? 'Politeknik Negeri Batam' ?></div>
                        </div>


                        <?php
                        $sql = "SELECT internship.* FROM internship WHERE nim = :nim AND internship.id_internship = :id_internship LIMIT 1";
                        $exec = $_SERVER['PDO']->prepare($sql);

                        $exec->bindParam(':nim',$nim, PDO::PARAM_STR);
                        $exec->bindParam(':id_internship',$id_internship, PDO::PARAM_STR);
                        $exec->execute();

                        $current_internship = $exec->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($current_internship as $row) {
                            $registered_date = $row['timestamp_register'];
                            $id_internship = $row['id_internship'];
                            $id_company = $row['id_company'];

                            // Fetch company data
                            $sql_company = "SELECT *, company.name AS company_name FROM company WHERE company.id_company = :id_company LIMIT 1";
                            $exec_company = $pdo->prepare($sql_company);
                            $exec_company->bindParam(':id_company', $id_company, PDO::PARAM_INT);
                            $exec_company->execute();
                            $company_data = $exec_company->fetch(PDO::FETCH_ASSOC);
                            $company_name = $company_data['company_name'];

                            // Fetch lecturer data
                            if (in_array($student_data['nik_dospem'], [NULL, '', '-'])) {
                                $nik_dospem = 0;
                                $dospem = 'Not Set';
                                $dospem_html = '<button class="btn btn-round btn-danger">Not Set</button>';
                            } else {
                                $nik_dospem = $student_data['nik_dospem'];
                                $sql_dospem = "SELECT lecturer.name AS lecturer_name FROM lecturer WHERE nim_nik_unit = :nik_dospem LIMIT 1";
                                $exec_dospem = $pdo->prepare($sql_dospem);
                                $exec_dospem->bindParam(':nik_dospem', $nik_dospem, PDO::PARAM_STR);
                                $exec_dospem->execute();
                                $lecturer_data = $exec_dospem->fetch(PDO::FETCH_ASSOC);
                                $dospem = $lecturer_data ? $lecturer_data['lecturer_name'] : 'Not Set';
                                $dospem_html = htmlspecialchars($dospem);
                            }

                            // Fetch supervisor industri data
                            if (empty($row['id_user_company'])) {
                                $user_company = 0;
                                $supervisor_industri_html = '<button class="btn btn-round btn-danger">Not Set</button>';
                            } else {
                                $user_company = $row['id_user_company'];

                                $sql_supervisor = "SELECT user_company.user_fullname AS supervisor_name FROM user_company WHERE user_company.id_user_company = :user_company  LIMIT 1";
                                $exec_supervisor = $pdo->prepare($sql_supervisor);
                                $exec_supervisor->bindParam(':user_company', $user_company, PDO::PARAM_INT);
                                $exec_supervisor->execute();
                                $supervisor_data = $exec_supervisor->fetch(PDO::FETCH_ASSOC);
                                $supervisor_industri = $supervisor_data ? $supervisor_data['supervisor_name'] : 'Not Set';
                                $supervisor_industri_html = htmlspecialchars($supervisor_industri);
                            }

                            // Determine status
                            $status = 'Not Registered';
                            if (in_array(strtoupper($row['status']), ['YES', 'FINISH'])) {
                                $accepted = 1;
                                if (strtoupper($row['status']) === 'YES') {
                                    $status = 'Intern Accepted';
                                } elseif (strtoupper($row['status']) === 'FINISH') {
                                    $status = 'Intern Accepted and Finished';
                                }
                                $internship_period = date('d M Y', strtotime($row['start_date'])) . ' until ' . date('d M Y', strtotime($row['end_date'])) . ' (' . range_time_elapsed_string($row['start_date'], $row['end_date']) . ')';
                                $start_date = $row['start_date'];
                                $end_date = $row['end_date'];
                            } elseif (in_array(strtoupper($row['status']), ['NO', 'WAITING'])) {
                                $accepted = 0;
                                if (strtoupper($row['status']) === 'NO') {
                                    $status = 'Intern Not Accepted';
                                } elseif (strtoupper($row['status']) === 'WAITING') {
                                    $status = 'Waiting for validation';
                                }
                            } else {
                                $accepted = NULL;
                            }

                            }

                        ?>

                        <table class="table table-striped mt-2 mt-4">

                            <tbody>
                                <tr>
                                    <th width="30%">Registered Date</th>
                                    <td><?= date('D, d M Y', strtotime($registered_date)) ?></td>
                                </tr>

                                <tr>
                                    <th>Study Program</th>
                                    <td><?= $student_data['jenjang'] . ' ' . $student_data['study_program'] ?></td>
                                </tr>



                                <?php if ($accepted == 1) : ?>


                                <tr>
                                    <th>Internship Period</th>
                                    <td><?= $internship_period ?> </td>
                                </tr>

                                <tr>
                                    <th>Supervisor</th>
                                    <td><a href="<?= base_url('index.php?page=supervisor_intern_management&&id_internship=' . base64_encode($id_internship)) ?>"><?= $supervisor_industri_html ?></a></td>
                                </tr>

                                <tr>
                                    <th>Lecturer</th>
                                    <td><a href="<?= base_url('index.php?page=lecturer_intern_management&id_internship=' . base64_encode($id_internship)) ?>"><?= $dospem ?></a></td>
                                </tr>


                                <?php endif; ?>


                            </tbody>
                        </table>


                        <div class="table-responsive">
                            <div class="mt-1 mb-1 d-sm-inline-flex d-md-inline-flex d-lg-inline-flex justify-content-arround">
                                <?php if ($accepted != 0) : ?>

                                <a class="btn btn-md btn-secondary btn-block mr-1 mt-1 text-white" data-toggle="modal" data-target="#ModalAssignSupervisor"><i class="fas fa-user-plus mr-1"></i>Set Supervisor and Internship Period</a>

                                <?php endif; ?>

                            </div>
                        </div>

                        <div class="separator-solid"></div>


                        {{-- Logbook dan Attendance --}}
                        <table class="table table-striped mt-2 mt-4">
                            @php
                                // Internship start date and end date
                                $start_date = $row['start_date']; // Start date of the internship
                                $end_date = $row['end_date'];   // End date of the internship
                        
                                // Calculate the difference in days between the start and end dates
                                $total_days = (strtotime($end_date) - strtotime($start_date)) / (60 * 60 * 24);
                        
                                // Calculate the number of weeks passed
                                $total_weeks = floor($total_days / 7); // 7 days per week
                        
                                // Count completed logbooks (example: 10 out of 20 weeks)
                                $sql_logbook = "SELECT COUNT(id_logbook) as completed_logbook FROM logbook WHERE id_internship = :id_internship";
                                $stmt_logbook = $_SERVER['PDO']->prepare($sql_logbook);
                                $stmt_logbook->bindParam(':id_internship',$id_internship);
                                $stmt_logbook->execute();
                                $logbook = $stmt_logbook->fetch(PDO::FETCH_ASSOC);
                                $logbook_completed = $logbook['completed_logbook'];
                                
                                $logbook_total = $total_weeks;
                        
                                // Count attendance (excluding Saturdays and Sundays)
                                $attendance_completed = 0;

                                // Count completed logbooks (example: 10 out of 20 weeks)
                                $sql_attendance = "SELECT COUNT(id_attendance) as completed_attendance FROM attendance WHERE id_internship = :id_internship";
                                $stmt_attendance = $_SERVER['PDO']->prepare($sql_attendance);
                                $stmt_attendance->bindParam(':id_internship',$id_internship);
                                $stmt_attendance->execute();
                                $attendance = $stmt_attendance->fetch(PDO::FETCH_ASSOC);
                                $attendance_completed = $attendance['completed_attendance'];
                                

                                for ($i = 0; $i <= $total_days; $i++) {
                                    $current_day = strtotime("+$i day", strtotime($start_date));
                                    $day_of_week = date('N', $current_day); // N: 1 = Monday, 7 = Sunday
                        
                                    // Only count weekdays (1 = Monday to 5 = Friday)
                                    if ($day_of_week < 6) {
                                        $attendance_completed++;
                                    }
                                }
                        
                                $attendance_total = $attendance_completed;
                        
                            @endphp
                        
                            <tbody>
                                <tr>
                                    <th width="30%">Logbook</th>
                                    <td>{{ $logbook_completed }} out of {{ $logbook_total }} Weeks of Logbook. <a href="{{ base_url('index.php?page=intern_logbook') }}">Student Logbook</a></td>
                                </tr>
                        
                                <tr>
                                    <th>Attendance</th>
                                    <td>{{ $attendance_completed }} out of {{ $total_days }} Days (excluding Saturdays & Sundays). <a href="{{ base_url('index.php?page=intern_attendance') }}">Student Attendance</a></td>
                                </tr>

                            @php
                                
                                $sql_industry_feedback = "SELECT * FROM industry_feedback WHERE id_internship = :id_internship LIMIT 1";
                                $stmt_industry_feedback = $_SERVER['PDO']->prepare($sql_industry_feedback);
                                $stmt_industry_feedback->bindParam(':id_internship',$id_internship);
                                $stmt_industry_feedback->execute();

                                $industry_feedback = $stmt_industry_feedback->fetch( PDO::FETCH_ASSOC);

                                if ( $stmt_industry_feedback->rowCount() != 1 || $industry_feedback['terisi'] != 1 ) {
                                    $industry_feedback = '<button class="btn btn-round btn-danger">No Industry Feedback Found</button>';
                                } else {
                                    $industry_feedback = '<button class="btn btn-round btn-success">Industry Feedback Found</button>';
                                }
                                
                                $sql_student_feedback = "SELECT * FROM student_feedback WHERE id_internship = :id_internship LIMIT 1";
                                $stmt_student_feedback = $_SERVER['PDO']->prepare($sql_student_feedback);
                                $stmt_student_feedback->bindParam(':id_internship',$id_internship);
                                $stmt_student_feedback->execute();

                                $student_feedback = $stmt_student_feedback->fetch( PDO::FETCH_ASSOC);

                                if ( $stmt_student_feedback->rowCount() != 1 || $student_feedback['posisi'] == '-' ) {
                                    $student_feedback = '<button class="btn btn-round btn-danger">No Industry Feedback Found</button>';
                                } else {
                                    $student_feedback = '<button class="btn btn-round btn-success">Industry Feedback Found</button>';
                                }
                        
                            @endphp
                                <tr>
                                    <th width="30%">Feedback from Student</th>
                                    <td>{!! $student_feedback !!} <a href="{{ base_url('index.php?page=student_feedback_intern_management&id_internship=' .base64_encode( $id_internship ) ) }}">Student Feedback</a></td>
                                </tr>
                        
                                <tr>
                                    <th>Feedback from Industry</th>
                                    <td>{!! $industry_feedback !!} <a href="{{ base_url('index.php?page=industry_feedback_intern_management&id_internship=' .base64_encode( $id_internship ) ) }}">Industry Feedback</a></td>
                                </tr>
                            </tbody>
                        </table>

                        {{-- Approval Checkbox --}}
                        <div class="mt-4">
                            <form method="POST" action="{{ base_url('index.php?form=mark_internship') }}">
                                {{ csrf() }} 
                                <?= input_hidden('id_internship',$id_internship) ?>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input class="form-check-input" type="checkbox" value="" required>
                                        <span class="form-check-sign">
                                            I confirm that the student has completed the internship.
                                        </span>
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-success mt-2">Submit Approval</button>
                            </form>
                        </div>




                    </div>


                </div>
            </div>



            <!-- modal tambah hrd -->
            <div class="modal fade" id="ModalAssignSupervisor">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <span class="fw-mediumbold">
                                    Assign Supervisor</span>
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <form action="index.php?form=hrd&aksi=assign_supervisor" method="POST">
                            <?= csrf() ?> <?= input_hidden('id_internship',$id_internship) ?>
                            <div class="modal-body form-row">


                                <div class="form-group col-md-12">
                                    <label>Supervisor <b class="text-danger">*</b></label>

                                    <select class="form-control <?= show_error('id_supervisor') ?>" name="id_supervisor">
                                        <option value="" hidden>- Select One -</option>
                                        <?php

                                        $sql = "SELECT * FROM user_company WHERE user_type NOT IN ('HRD') AND id_company = '" . $_SESSION['id_company'] . "' ";

                                        foreach ($_SERVER['KONEKSI_DB']->query($sql) as $data) :
                                        ?>

                                        <option value="<?= $data['id_user_company'] ?>"><?= $data['user_fullname'] ?></option>

                                        <?php endforeach; ?>
                                    </select>


                                    <?= form_error('id_supervisor') ?>

                                </div>



                                <div class="form-group col-md-6">
                                    <label class="label-control">Start Date <b class="text-danger">*</b></label>

                                    <input type="date" class="form-control" name="start_date" value="<?= old('start_date', $start_date) ?>" />

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="label-control">End Date <b class="text-danger">*</b></label>

                                    <input type="date" class="form-control" name="end_date" value="<?= old('end_date', $end_date) ?>" />

                                </div>





                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Close</button>
                                <button type="submit" id="submit_btn" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Assign</button>
                        </form>

                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.tutupmodal -->






    </div>
</div>
@endsection
