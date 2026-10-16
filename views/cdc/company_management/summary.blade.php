@extends('templates.cdc')


@push('head-scripts')
<!-- Datatables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

@php



$id_company = base64_decode( $_GET['id'] );


$sql = "SELECT * FROM company WHERE id_company = :id_company LIMIT 1";
$stmt = $_SERVER['PDO']->prepare($sql);
$stmt->bindParam(':id_company', $id_company, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() != 1) {
swal2('error', 'Not Allowed', 'Company Data not found !');
close(3000);
exit();
}


$data = $stmt->fetch(PDO::FETCH_ASSOC);

// Mengambil semua user_company berdasarkan id_company dan urutkan
$sql2 = "SELECT * FROM user_company WHERE id_company = :id_company ORDER BY CASE WHEN user_type = 'HRD' THEN 0 ELSE 1 END, id_user_company ASC";
$stmt2 = $_SERVER['PDO']->prepare($sql2);
$stmt2->bindParam(':id_company', $id_company, PDO::PARAM_INT);
$stmt2->execute();

// Menyimpan hasil ke variabel terpisah
$userCompanies = $stmt2->fetchAll(PDO::FETCH_ASSOC);

$countSupervisor = $stmt2->rowCount();

// Mengambil HRD user_company berdasarkan id_company
$sql3 = "SELECT * FROM user_company WHERE id_company = :id_company AND user_type = :user_type LIMIT 1";
$stmt3 = $_SERVER['PDO']->prepare($sql3);
$stmt3->bindParam(':id_company', $id_company, PDO::PARAM_INT);
$userType = 'HRD'; // Mendefinisikan user_type
$stmt3->bindParam(':user_type', $userType, PDO::PARAM_STR);
$stmt3->execute();

// Mengambil data contact_person jika ada
$contact_person = $stmt3->fetch(PDO::FETCH_ASSOC);

$cekhrd = mysqli_query($_SERVER['DB'], "SELECT * FROM user_company WHERE id_company = '$id_company' AND user_type = 'HRD'");
$counthrd = mysqli_num_rows($cekhrd);
$data2 = mysqli_fetch_assoc($cekhrd);

if (in_array($data['status'], [1, 'verified', 'Verified'])) {
$status = '<button type="button" class="btn btn-sm btn-success">Verified</button>';
} else
if (in_array($data['status'], [0, 'not verified', 'Not Verified'])) {
$status = '<button type="button" class="btn btn-sm btn-danger">Not Verified</button>';
} else
if (in_array($data['status'], [2, 'blacklist', 'Blacklist'])) {
$status = '<button type="button" class="btn btn-sm btn-dark">Blacklist</button>';
}

$status .= '<i class="fas fa-info-circle text-info ml-2" data-toggle="tooltip" data-placement="top" title="1. Verified : Company is verified and registered, can receive student internship from Polibatam; 2. Not Verified : Company is not yet verified and not registered; 3. Blacklist : Company is blacklist from receiving Polibatam`s student internship"></i>';


if ($data['access_type'] == 1) {
$data['access_type'] = '1 - Can Make Offer';
} else {
$data['access_type'] = '2 - Cannot Make Offer';
}


$sql_company_stats = "SELECT COUNT(id_internship) as total_internship,
SUM(CASE WHEN `status` IN ('YES', 'Yes') THEN 1 ELSE 0 END) as accepted_internship,
SUM(CASE WHEN `status` IN ('FINISH', 'Finish') THEN 1 ELSE 0 END) as finished_internship,
SUM(CASE WHEN `status` IN ('WAITING', 'Waiting') THEN 1 ELSE 0 END) as waiting_internship
FROM internship
WHERE id_company = :id_company
";

$stmt_stats = $_SERVER['PDO']->prepare($sql_company_stats);
$stmt_stats->bindParam(':id_company', $id_company, PDO::PARAM_INT);
$stmt_stats->execute();
$company_stats = $stmt_stats->fetch(PDO::FETCH_ASSOC);

$company_stats['total_internship'] = $company_stats['total_internship'] ?? 0;
$company_stats['accepted_internship'] = $company_stats['accepted_internship'] ?? 0;
$company_stats['finished_internship'] = $company_stats['finished_internship'] ?? 0;
$company_stats['waiting_internship'] = $company_stats['waiting_internship'] ?? 0;


@endphp
@endpush

@section('main-content')

<div class="container">
    <div class="page-inner mt-2">


        <div class="page-header">
            <h4 class="page-title">Company Summary</h4>
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
                    <a href="#">Company Detail</a>
                </li>

                <li class="separator">
                    <i class="flaticon-right-arrow"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Company Summary</a>
                </li>
            </ul>
        </div>


        <div class="row mt-2">


            <!-- Company Identity -->
            <div class="col-md-12">



                <div class="card card-profile">

                    <div class="card-header" style="background-image: url('index.php?viewer=profile_background'); height: 300px; background-size: cover;background-repeat: no-repeat;">


                        <!-- <a href="#" data-toggle="modal" data-target="#ModalBackground" type="button" role="button " class="btn btn-primary btn-sm pull-right"> <i class="fas fa-edit mr-2"></i>Change Background</a> -->

                        <div class="profile-picture">
                            <div class="avatar avatar-xxl">
                                <img src="index.php?viewer=get_company_logo&location=<?= base64_encode($data['logo'] ?? '--') ?>" alt="..." class="avatar-img rounded-circle">
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="user-profile text-center mb-4">

                            <div class="name"><?= $data['name'] ?></div>
                            <div class="job">Joined My Internship since <b><?= $company['created_at'] ?></b></div>
                            <div class="job">Company Status <span><?= $status; ?></span> <span><?= $data['access_type']; ?> <i class="ml-2 fas fa-info-circle text-info" data-toggle="tooltip" data-placement="top" title="1 - Can Make Offer; 2 - Cannot Make Offer"></i></span></div>


                        </div>



                        <table class="table table-hover table-bordered table-responsive-md">
                            <tr rowspan="2">
                                <th colspan="2" width="10%">Name </th>
                                <td colspan="2" width="90%"><?= $data['name']; ?></td>
                            </tr>
                            <tr rowspan="2">
                                <th colspan="2" width="10%">Type </th>
                                <td colspan="2" width="90%"><?= $data['type']; ?></td>
                            </tr>
                        </table>
                        <table class="table table-hover table-bordered table-responsive-md">

                            <tr colspan="1">
                                <th width="10%">Phone </th>
                                <td width="40%"><?= $data['phone']; ?></td>

                                <th width="10%">Email </th>
                                <td width="40%"><?= $data['email']; ?></td>
                            </tr>
                            <tr>
                                <th width="10%">Address </th>
                                <td colspan="3"><?= $data['address']; ?></td>
                            </tr>

                            <tr>
                                <th width="10%">City </th>
                                <td width="40%"><?= $data['city']; ?></td>


                                <th width="10%">Province </th>
                                <td width="40%"><?= $data['province']; ?></td>

                            </tr>
                        </table>

                        <table class="table table-hover table-bordered">
                            <tr>
                                <th width="10%">Website</th>
                                <td width="40%"><a href="{{ base_url('index.php?viewer=url&location=' .base64_encode($data['website'])) }}" target="_blank"><?= $data['website']; ?></a></td>
                                <th width="10%">Logo</th>
                                <td width="40%">
                                    <a href="{{ base_url('index.php?viewer=url&location=' .base64_encode($data['logo'])) }}" target="_blank">Link to Companies Logo</a>
                                </td>
                            </tr>
                            <tr>
                                <th width="10%">Instagram</th>
                                <td width="40%">
                                    <a href="{{ base_url('index.php?viewer=url&location=' .base64_encode($data['instagram'])) }}" target="_blank">Link to Companies Instagram</a>
                                </td>
                                <th width="10%">LinkedIn</th>
                                <td width="40%">
                                    <a href="{{ base_url('index.php?viewer=url&location=' .base64_encode($data['linkedin'])) }}" target="_blank">Link to Companies LinkedIn</a>
                                </td>
                            </tr>
                            <tr>
                                <th width="10%">Facebook</th>
                                <td width="40%">
                                    <a href="{{ base_url('index.php?viewer=url&location=' .base64_encode($data['facebook'])) }}" target="_blank">Link to Companies Facebook</a>
                                </td>
                                <th width="10%">Twitter</th>
                                <td width="40%">
                                    <a href="{{ base_url('index.php?viewer=url&location=' .base64_encode($data['twitter'])) }}" target="_blank">Link to Companies Twitter</a>
                                </td>
                            </tr>
                        </table>


                        <hr>

                        <h4 class="text-center text-bold"><strong>Contact Person</strong></h4>
                        <table class="table table-hover table-bordered">
                            <tr>
                                <th width="10%">HRD</th>
                                <td width="40%">{{ $contact_person['user_fullname'] ?? 'Company does not have HRD' }}</td>
                                <td width="40%">

                                    @if ( $contact_person['user_fullname'] != '' )
                                    @if ( $contact_person['user_email'] != '' )
                                    Email to : <a href="mailto:{{ $contact_person['user_email'] }}">{{ $contact_person['user_email'] }}</a>
                                    @endif

                                    @if ( $contact_person['user_phone'] != '' )
                                    or Phone to : <a href="tel:{{ $contact_person['user_phone'] }}">{{ $contact_person['user_phone'] }}</a>
                                    @endif


                                    @if ( $contact_person['user_email'] == '' && $contact_person['user_phone'] == '' )
                                    HRD does not provide contact information
                                    @endif


                                    @else {{ 'Company does not have HRD' }}@endif

                                </td>
                            </tr>
                            <tr>
                                <th width="10%">Supervisor</th>
                                <td width="90%" colspan="2">
                                    <a class="scroll-btn" href="#user-company">{{ $countSupervisor ?? 0 }} Supervisor</a>
                                </td>
                            </tr>
                        </table>


                        <h4 class="text-center text-bold"><strong>Statistics</strong></h4>
                        <table class="table table-hover table-bordered">

                            <tr>
                                <th width="10%">Total Internship</th>
                                <td width="90%" colspan="5">{{ $company_stats['total_internship'] }} Student</td>
                            </tr>
                            <tr>
                                <th width="10%">Internship Waiting for Validation</th>
                                <td width="20%">{{ $company_stats['accepted_internship'] }} Student</td>
                                <th width="10%">Internship Finished</th>
                                <td width="20%">{{ $company_stats['finished_internship'] }} Student</td>

                                <th width="10%">Internship Finished</th>
                                <td width="20%">{{ $company_stats['finished_internship'] }} Student</td>

                            </tr>

                        </table>





                        <div class="table-responsive">


                            <div class="mt-1 mb-1 d-sm-inline-flex d-md-inline-flex d-lg-inline-flex justify-content-arround ">

                                <button class="btn btn-primary btn-block mr-1 mt-1 scroll-btn" href="#user-company">
                                    <i class="fas fa-building mr-1"></i> Go to User Company
                                </button>
                                <button class="btn btn-primary btn-block mr-1 mt-1 scroll-btn" href="#student-internship">
                                    <i class="fas fa-user-graduate mr-1"></i> Go to Student Internship
                                </button>

                            </div>
                        </div>



                    </div>


                </div>


                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center float-left">
                            <h4 class="card-title">Company Description</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        {!! html_entity_decode($data['description']) !!}
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
                                <form action="index.php?form=company&aksi=hapus" method="POST">
                                    <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?>">
                                    <input type="hidden" id="id_hapus" name="id_hapus" value="<?= $id_company ?>">
                                    Are You Sure to Delete Current Company Data ?
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


            </div>




            <!-- User Company and Student Intern -->
            <div class="col-md-12">

                <!-- User Company Data -->
                <div class="card" id="user-company">
                    <div class="card-header">
                        <div class="d-flex align-items-center float-left">
                            <h4 class="card-title">User Company</h4>
                        </div>
                        <button class="btn btn-sm btn-secondary float-right mr-1" data-toggle="modal" data-target="#ModalTambahUserCompany"><i class="fas fa-plus mr-2"></i>Add Data</button>

                        <button class="btn btn-sm btn-secondary float-right mr-1" data-toggle="modal" data-target="#ModalPasswordDefault"><i class="fas fa-info-circle mr-2"></i>Password Default</button>

                    </div>
                    <div class="card-body">
                        <table id="basic-datatables" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>FullName</th>
                                    <th>Phone</th>
                                    <th>E-Mail</th>
                                    <th>Username</th>
                                    <th>User Type</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-staff">
                                <?php
                                    $no = 1;

                                    foreach ($userCompanies as $row) :
                                    ?>
                                <tr>
                                    <td><?= $no++; ?>.</td>
                                    <td><?= $row['user_fullname'] ?></td>
                                    <td><?= $row['user_phone'] ?></td>
                                    <td><?= $row['user_email'] ?></td>
                                    <td><?= $row['username'] ?></td>
                                    <td><?= $row['user_type'] ?></td>
                                    <td>

                                        <div class="btn-group btn-round">
                                            <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Action
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                <?php //if ($data['id_kampus'] == $_SESSION['CDC_Data']['id_kampus']) : ?>
                                                <button class="dropdown-item text-info" data-toggle="modal" data-target="#ModalResetUserCompany<?= $no ?>"><i class="fas fa-sync mr-2"></i>Reset Password</button>
                                                <div class="dropdown-divider"></div>
                                                <button class="dropdown-item text-warning" data-toggle="modal" data-target="#ModalEditUserCompany<?= $no ?>"><i class="fas fa-edit mr-2"></i>Edit Data</button>
                                                <button class="dropdown-item text-danger" onclick="confirmation_delete( '<?= base64_encode($row['id_user_company']) ?>' )"><i class="fas fa-trash-alt mr-2"></i>Delete Data</button>

                                                <?php //endif; ?>
                                            </div>
                                        </div>

                                    </td>
                                </tr>


                                <!-- Modal Edit User Company -->
                                <div class="modal fade" id="ModalEditUserCompany<?= $no ?>">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    <span class="fw-mediumbold">
                                                        Edit User Company</span>
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>

                                            <form action="index.php?form=company&aksi=edit_usercompany" method="POST">
                                                <?= csrf() ?> <?= input_hidden('id_company', $id_company) ?>
                                                <?= input_hidden('id_user_company', $row['id_user_company']) ?> <?= input_hidden('loop', $no) ?>

                                                <?= input_hidden('old_user_phone', $row['user_phone']) ?> <?= input_hidden('old_user_email', $row['user_email']) ?>
                                                <?= input_hidden('old_user_type', $row['user_type']) ?> <?= input_hidden('old_username', $row['username']) ?>
                                                <div class="modal-body">

                                                    <div class="form-group">
                                                        <label for="user_fullname">Fullname <label class="text-danger">*</label></label>
                                                        <input type="text" placeholder="Fill User Company Fullname" class="form-control <?= show_error('edit_user_fullname') ?>" name="edit_user_fullname" value="<?= $row['user_fullname'] ?>">
                                                        <?= form_error('edit_user_fullname'); ?>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="user_phone">Phone Number <label class="text-danger">*</label></label>
                                                        <input type="text" placeholder="Fill User Company Phone Number" class="form-control <?= show_error('edit_user_phone') ?>" name="edit_user_phone" value="<?= $row['user_phone'] ?>">
                                                        <?= form_error('edit_user_phone'); ?>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="user_email">E-Mail <label class="text-danger">*</label></label>
                                                        <input type="text" placeholder="Fill User Company Email" class="form-control <?= show_error('edit_user_email') ?>" name="edit_user_email" value="<?= $row['user_email'] ?>">
                                                        <?= form_error('edit_user_email'); ?>
                                                    </div>



                                                    <div class="form-group">
                                                        <label for="user_type">User Type <label class="text-danger">*</label></label>
                                                        <select class="form-control <?= show_error('edit_user_type') ?>" name="edit_user_type">
                                                            <option hidden><?= $row['user_type'] ?></option>
                                                            <?php //if ($counthrd < 1) { 
                                                                    ?>
                                                            <option value="HRD">HRD</option>
                                                            <?php //} 
                                                                    ?>
                                                            <option value="Supervisor">Supervisor</option>
                                                        </select>
                                                        <?= form_error('edit_user_type'); ?>
                                                    </div>


                                                    <div class="form-group">
                                                        <label for="username">Username <label class="text-danger">*</label></label>
                                                        <input type="text" placeholder="Fill User Company Username" class="form-control <?= show_error('edit_username') ?>" name="edit_username" value="<?= $row['username'] ?>">
                                                        <?= form_error('edit_username'); ?>
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

                                <!-- Modal Reset -->
                                <div class="modal fade" id="ModalResetUserCompany<?= $no ?>">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    <span class="fw-mediumbold">
                                                        Reset Password User Company</span>
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>

                                            <form action="index.php?form=company&aksi=reset_usercompany" method="POST">
                                                <?= csrf() ?> <?= input_hidden('id_company', $id_company) ?>
                                                <?= input_hidden('id_user_company', $row['id_user_company']) ?> <?= input_hidden('loop', $no) ?>
                                                <div class="modal-body">

                                                    Are you sure you want to reset current user company password ?

                                                </div>

                                                <div class="modal-footer justify-content-between">
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times mr-2"></i>Close</button>
                                                    <button type="submit" class="btn btn-primary"><i class="fas fa-sync mr-2"></i>Reset Password</button>
                                                </div>
                                            </form>

                                        </div>
                                        <!-- /.modal-content -->

                                    </div>
                                    <!-- /.modal-dialog -->
                                </div>
                                <!-- /.tutupmodal -->


                                <?php
                                    endforeach;
                                    ?>
                            </tbody>
                        </table>
                    </div>
                </div>


                <div class="card" id="student-internship">
                    <div class="card-header">
                        <div class="d-flex align-items-center float-left">
                            <h4 class="card-title">Student Internship</h4>
                        </div>

                        <!-- <a href="index.php?page=add_internship&id_company=<?= base64_encode($id_company) ?>" class="btn btn-sm btn-secondary float-right mr-1"><i class="fas fa-plus mr-2"></i>Add Data</a> -->

                    </div>
                    <div class="card-body">

                        <div class="form-row">



                            <div class="form-group col-md-12">
                                <label for="internship-filter">Filter by Internship Status:</label>
                                <select id="internship-filter" class="form-control" disabled>
                                    <option value="">All</option>
                                    <option value="Yes">Only Accepted Internship are shown</option>
                                    <option value="No">Only Rejected Internship are shown</option>
                                    <option value="Finish">Only Finished Internship are shown</option>

                                </select>
                            </div>



                            <div class="form-group col-md-6">
                                <label for="university-filter">Filter by University:</label>
                                <select id="university-filter" class="form-control" disabled>
                                    <option value="">All University</option>
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="study_program-filter">Filter by Study Program:</label>
                                <select id="study_program-filter" class="form-control" disabled>
                                    <option value="">All Study Program</option>
                                </select>
                            </div>



                        </div>


                        <table id="basic-datatables2" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Student</th>
                                    <th>University</th>
                                    <th>Internship Status</th>
                                    <th>Internship Period</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-staff">
                                <?php

                                    $sql_student = "SELECT student_internship.id_kampus, student_internship.nim, student_internship.name, tb_kampus.nama_kampus as nama_kampus, internship.* 
                                        FROM internship, student_internship, tb_kampus WHERE internship.id_company = :id_company 
                                        AND student_internship.nim = internship.nim AND tb_kampus.id_kampus = student_internship.id_kampus";

                                    $stmt = $_SERVER['PDO']->prepare($sql_student);
                                    $stmt->bindParam(':id_company', $id_company, PDO::PARAM_INT);
                                    $stmt->execute();

                                    $student_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    $no = 1;

                                    foreach ($student_data as $index => $internship) :

                                        if (in_array($internship['status'], [NULL])) {
                                            $internship_status = 'None';
                                            $internship_period = '-';
                                            $internship_date = '-';
                                        } else 
                                        if (in_array( strtolower($internship['status']), ['yes'])) {
                                            $internship_status = 'Internship Accepted';
                                            // $internship_period = range_time_elapsed_string($internship['start_date'], $internship['end_date']);
                                            $internship_date = date('d-M-Y', strtotime($internship['start_date'])) . " until " . date('d-M-Y', strtotime($internship['end_date']));

                                            // $internship_date .= ' (' . $internship_period . ')';
                                        } else
                                    
                                        if (in_array( strtolower($internship['status']), ['no'])) {

                                            $internship_status = 'Rejected by Company';
                                            $internship_date = '-';
                                            $internship_period = '-';
                                        } else
                                    
                                        if (in_array( strtolower($internship['status']), ['waiting'])) {
                                            $internship_status = 'Currently Submitting';

                                            $internship_date = '-';
                                            $internship_period = '-';
                                        } 
                                        else
                                    
                                    if (in_array( strtolower($internship['status']), ['finish'])) {
                                        $internship_status = 'Internship Finished';
                                            // $internship_period = range_time_elapsed_string($internship['start_date'], $internship['end_date']);
                                        $internship_date = date('d-M-Y', strtotime($internship['start_date'])) . " until " . date('d-M-Y', strtotime($internship['end_date']));

                                    }
                                        
                                        else {
                                            $internship_status = 'None';
                                            $internship_period = '-';
                                            $internship_date = '-';
                                        }


                                    ?>
                                <tr>
                                    <td><?= $no++; ?>.</td>
                                    <td><?= $internship['nim'] . ' - ' . $internship['name'] ?></td>
                                    <td><?= $internship['nama_kampus'] ?></td>
                                    <td>{!! $internship_status !!}</td>
                                    <td>{!! $internship_date !!}</td>
                                    <td>

                                        <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="false" aria-expanded="false">
                                            Action
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">

                                            <a class="dropdown-item" href='index.php?page=internship_detail_intern_co&nim=<?= base64_encode($row['nim']) ?>&id_internship=<?= base64_encode($row['id_internship']) ?>'><i class="fas fa-info-circle mr-1"></i>Internship Detail</a>
                                            <?php if ($row['id_kampus'] == $_SESSION['CDC_Data']['id_kampus']) : ?>

                                            <div class="dropdown-divider"></div>

                                            <button class="dropdown-item" onclick="window.location.href='index.php?page=edit_internship&nim=<?= base64_encode($row['nim']) ?>&internship=<?= base64_encode($row['id_internship']) ?>'"><i class="fas fa-edit mr-1"></i>Edit Data</button>
                                            <?php endif; ?>

                                        </div>



                                    </td>
                                </tr>



                                <?php
                                    endforeach;
                                    ?>
                            </tbody>
                        </table>
                    </div>
                </div>


            </div>
        </div>
    </div>



</div>



<!-- modal tambah hrd -->
<div class="modal fade" id="ModalTambahUserCompany">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <span class="fw-mediumbold">
                        Add User Company</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="index.php?form=company&aksi=add_usercompany" method="POST">
                <?= csrf() ?> <?= input_hidden('id_company', $id_company) ?> <?= input_hidden('redirect', $_GET['redirect']) ?>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="user_fullname">Fullname <label class="text-danger">*</label></label>
                        <input required type="text" placeholder="Fill User Company Fullname" class="form-control <?= show_error('user_fullname') ?>" name="user_fullname" id="user_fullname">
                        <?= form_error('user_fullname'); ?>
                    </div>

                    <div class="form-group">
                        <label for="user_phone">Phone Number <small class="text-sm">Opsional</small></label>
                        <input type="text" placeholder="Fill User Company Phone Number" class="form-control <?= show_error('user_phone') ?>" name="user_phone" id="user_phone">
                        <?= form_error('user_phone'); ?>
                    </div>

                    <div class="form-group">
                        <label for="user_email">E-Mail <small class="text-sm">Opsional</small></label>
                        <input type="text" placeholder="Fill User Company Email" class="form-control <?= show_error('user_email') ?>" name="user_email" id="user_email">
                        <?= form_error('user_email'); ?>
                    </div>



                    <div class="form-group">
                        <label for="user_type">User Type <label class="text-danger">*</label></label>
                        <select required class="form-control <?= show_error('user_type') ?>" name="user_type" id="user_type">
                            <!-- <option value="" hidden>- Select User Type -</option> -->
                            <?php if ($counthrd < 1) { ?>
                            <!--<option value="HRD">HRD</option>-->
                            <?php } ?>

                            <option value="HRD">HRD</option>




                            <option value="Supervisor">Supervisor</option>
                        </select>
                        <?= form_error('user_type'); ?>
                    </div>


                    <div class="form-group">
                        <label for="username">Username <label class="text-danger">*</label></label>
                        <input type="text" placeholder="Fill User Company Username" class="form-control <?= show_error('username') ?>" name="username" id="username">
                        <?= form_error('username'); ?>
                    </div>

                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times mr-2"></i>Close</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i>Save</button>
                </div>
            </form>

        </div>
        <!-- /.modal-content -->

    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.tutupmodal -->



<!-- modal password default -->
<div class="modal fade" id="ModalPasswordDefault">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <span class="fw-mediumbold">
                        Password Default</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

                Password Default untuk Akun HRD dan Supervisor adalah <b><?= $_SERVER['INDUSTRY_DEFAULT_PASSWORD'] ?></b>

            </div>

            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times mr-2"></i>Close</button>
            </div>

        </div>
        <!-- /.modal-content -->

    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.tutupmodal -->




<!-- modal edit validasi -->
<div class="modal fade" id="ModalEdit2">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <span class="fw-mediumbold">
                        Update Validation Company</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <form action="index.php?form=company&aksi=editvalidasi" method="POST">

                    <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?>" />
                    <input type="hidden" name="id_editcompany2" value="<?php echo $data['id_company'] ?>" />

                    <div class="form-group">
                        <label for="status">Status <label class="text-danger">*</label></label>
                        <select class="form-control" name="status" id="status2">
                            <option value="<?= $data['status']; ?>" hidden><?= ucwords($data['status']) ?></option>
                            <option value="verified">Verified</option>
                            <option value="not verified">Not Verified</option>
                            <option value="blacklist">Blacklist</option>
                        </select>
                        <?= form_error('status'); ?>
                    </div>

                    <div class="form-group">
                        <label for="access_type">Access Type <label class="text-danger">*</label></label>
                        <select class="form-control" name="access_type" id="access_type2">
                            <option value="1" <?= checker($data['access_type'], [1], 'selected') ?>>1 - Make Offer for Internship</option>
                            <option value="2" <?= checker($data['access_type'], [2], 'selected') ?>>2 - Can Make Offer and Always Open for Internship</option>
                        </select>
                        <?= form_error('access_type'); ?>
                    </div>

            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Close</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-check-double mr-1"></i>Update Validation</button>
                </form>

            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.tutupmodal -->

@endsection

@push('footer-scripts')
<!-- Datatables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

<!-- JS Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

<!-- Chart JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    let _token = $('meta[name="csrf-token"]').attr('content');

    $(document).ready(function() {


        if (window.location.hash === "#add_user") {
            // Display the modal
            $("#ModalTambahUserCompany").modal('show');
        }


        $('.scroll-btn').click(function(e) {
            e.preventDefault(); // Mencegah aksi default tombol
            var target = $(this).attr('href'); // Mendapatkan target ID dari href
            var targetElement = $(target); // Seleksi elemen target berdasarkan ID

            // Animasi scroll
            $('html, body').animate({
                scrollTop: targetElement.offset().top // Scroll ke posisi elemen target
            }, 800); // 800ms durasi animasi
        });



        let user_table = $('#basic-datatables').DataTable({
            "responsive": true
            , "pageLength": 15
            , "lengthMenu": [10, 15, 25, 50]
        });

        let internship_table = $('#basic-datatables2').DataTable({
            "responsive": true
            , "pageLength": 15
            , "lengthMenu": [10, 15, 25, 50]
        });


        $('#show_more').click(function() {
            if ($('#company_detail').css('display') == 'none') {
                $('#company_detail').css('display', 'table'); // gunakan 'table' agar struktur tampil normal
                $('#show_more').html('<i class="fa fa-chevron-up mr-1"></i> Show less details');
            } else {
                $('#company_detail').css('display', 'none');
                $('#show_more').html('<i class="fa fa-chevron-down mr-1"></i> Show more details');
            }
        });



    });

    function confirmation_delete(id) {
        swal.fire({
            title: 'Delete Confirmation'
            , text: 'Are you sure you want to delete current User Company ?'
            , icon: 'question'
            , showCancelButton: true
            , confirmButtonColor: "#DD6B55"
            , confirmButtonText: "Yes, delete it!"
        , }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {


                // AJAX
                $.ajax({
                    url: 'index.php?request=delete_user_company'
                    , type: 'POST'
                    , data: {
                        id: id
                        , 'id_company': "<?= base64_encode($id_company) ?>"
                        , '_method': 'DELETE'
                        , 'token': _token
                    , }
                    , dataType: 'json'
                    , success: function(response) {
                        window.location.reload(1);
                    }
                    , error: function(response) {


                        // window.location.reload(1);

                        Swal.fire("Deleting Data Failed !", "Please try again later !", "error");
                    }
                });

            }
        })

    }

</script>


@if (old('ModalTambahUserCompany') !== NULL)
<script>
    $('#ModalTambahUserCompany').modal('show');

</script>
@endif

@if (old('ModalEditUserCompany') !== NULL)
<script>
    $("#ModalEditUserCompany{{ old('LoopEdit') }}").modal('show');

</script>

@endif
@endpush
