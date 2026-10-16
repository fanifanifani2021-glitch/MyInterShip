<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = 'Company Detail';
$route = 'company_management';
include "./view/upkpk/template/head.php";

$id = base64_decode($_GET['id']);
$id_company = $id;


$sql = "SELECT * FROM company WHERE id_company = :id LIMIT 1";
$stmt = $_SERVER['PDO']->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

// $data = $sql->fetch(PDO::FETCH_ASSOC);

if ($stmt->rowCount() != 1) {
    make_swal2('error', 'Not Allowed', 'Company Data not found !');
    redirect_back(1);
    exit();
}


$data = $stmt->fetch(PDO::FETCH_ASSOC);

$sql2 = "SELECT * FROM user_company WHERE id_company = :id_company ORDER BY CASE WHEN user_type = 'HRD' THEN 0 ELSE 1 END, id_user_company ASC";
$stmt2 = $_SERVER['PDO']->prepare($sql2);
$stmt2->bindParam(':id_company', $id_company, PDO::PARAM_INT);
$stmt2->execute();

$sql2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);

$cekhrd = mysqli_query($koneksi_db, "SELECT * FROM user_company WHERE id_company = '$id' AND user_type = 'HRD'");
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

?>


<body>
    <div class="wrapper">

        <?php include "./view/upkpk/template/main-header.php"; ?>
        <?php include "./view/upkpk/template/sidebar.php"; ?>

        <div class="main-panel">
            <div class="container">
                <div class="page-inner mt-2">


                    <div class="page-header">
                        <h4 class="page-title">Company Detail</h4>
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
                        </ul>
                    </div>


                    <div class="row mt-2">


                        <!-- Company Identity -->
                        <div class="col-md-5">

                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center float-left">
                                        <h4 class="card-title">Company Identity</h4>
                                    </div>
                                </div>
                                <div class="card-body ">

                                    <table class="table table-hover table-bordered table-responsive-md">
                                        <tr>
                                            <th>Name </th>
                                            <td><?= $data['name']; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Type </th>
                                            <td><?= $data['type']; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Phone </th>
                                            <td><?= $data['phone']; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Email </th>
                                            <td><?= $data['email']; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Website </th>
                                            <td><?= $data['website']; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Facebook </th>
                                            <td><?= $data['facebook']; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Twitter </th>
                                            <td><?= $data['twitter']; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Instagram </th>
                                            <td><?= $data['instagram']; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Logo </th>
                                            <td><?= $data['logo']; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Address </th>
                                            <td><?= $data['address']; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Province </th>
                                            <td><?= $data['province']; ?></td>
                                        </tr>
                                        <tr>
                                            <th>City </th>
                                            <td><?= $data['city']; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Status </th>
                                            <td><span><?= $status; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Access Type </th>
                                            <td><?= $data['access_type']; ?> <i class="ml-2 fas fa-info-circle text-info" data-toggle="tooltip" data-placement="top" title="1 - Can Make Offer; 2 - Cannot Make Offer"></i></span></td>
                                        </tr>
                                    </table>


                                    <?php if ($data['id_kampus'] == $_SESSION['CDC_Data']['id_kampus']) : ?>
                                        <div class="table-responsive">
                                            <div class="mt-1 mb-1 d-sm-inline-flex d-md-inline-flex d-lg-inline-flex justify-content-arround ">
                                                <button class="btn btn-info btn-block mr-1 mt-1" onclick="window.location.href='index.php?page=edit_company&id_company=<?= base64_encode($id) ?>'"><i class="fa fa-edit mr-1"></i> Edit</button>
                                                <!-- <button class="btn btn-secondary btn-md" data-toggle="modal" data-target="#ModalEdit"><i class="fa fa-edit mr-1"></i> Edit Company</button> -->
                                                <button class="btn btn-info btn-block mr-1 mt-1" data-toggle="modal" data-target="#ModalEdit2"><i class="fas fa-check-double mr-1"></i> Update Validation</button>

                                                <button class="btn btn-danger btn-block mr-1 mt-1" data-toggle="modal" data-target="#ModalHapus"><i class="fas fa-trash-alt mr-1"></i> Delete</button>


                                            </div>
                                        </div>
                                    <?php endif; ?>


                                </div>

                            </div>


                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center float-left">
                                        <h4 class="card-title">Company Description</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <?= html_entity_decode($data['description']); ?>
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
                        <div class="col-md-7">

                            <!-- User Company Data -->
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center float-left">
                                        <h4 class="card-title">User Company Data</h4>
                                    </div>
                                    <?php //if ($data['id_kampus'] == $_SESSION['CDC_Data']['id_kampus']) : ?>



                                        <button class="btn btn-sm btn-secondary float-right mr-1" data-toggle="modal" data-target="#ModalTambahUserCompany"><i class="fas fa-plus mr-2"></i>Add Data</button>

                                        <button class="btn btn-sm btn-secondary float-right mr-1" data-toggle="modal" data-target="#ModalPasswordDefault"><i class="fas fa-info-circle mr-2"></i>Password Default</button>

                                    <?php //endif; ?>

                                </div>
                                <div class="card-body">
                                    <div class="table-responsive" id="tabel-ini">
                                        <table id="ekonomi-kepri" class="display dataTable table table-striped table-hover">
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

                                                foreach ($sql2 as $row) :
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
                                                                    <?= csrf() ?> <?= input_hidden('id_company', $id) ?>
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
                                                                    <?= csrf() ?> <?= input_hidden('id_company', $id) ?>
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
                            </div>


                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center float-left">
                                        <h4 class="card-title">Student Intern</h4>
                                    </div>

                                    <!-- <a href="index.php?page=add_internship&id_company=<?= base64_encode($id_company) ?>" class="btn btn-sm btn-secondary float-right mr-1"><i class="fas fa-plus mr-2"></i>Add Data</a> -->

                                </div>
                                <div class="card-body">
                                    <div class="table-responsive" id="tabel-ini">
                                        <table id="student-internship" class="display dataTable table table-striped table-hover">
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
                                                    FROM internship, student_internship, tb_kampus 
                                                    WHERE internship.id_company = :id_company 
                                                    AND student_internship.nim = internship.nim 
                                                    AND tb_kampus.id_kampus = student_internship.id_kampus 
                                                    AND internship.nim IN (
                                                        SELECT DISTINCT(nim) 
                                                        FROM internship 
                                                        WHERE id_company = :id_company
                                                        AND status IN ('YES', 'Yes', 'FINISH', 'Finish')
                                                    )";

                                                $stmt = $_SERVER['PDO']->prepare($sql_student);
                                                $stmt->bindParam(':id_company', $id_company, PDO::PARAM_INT);

                                                $stmt->execute();

                                                $student_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                $no = 1;

                                                foreach ($student_data as $row) :


                                                    $internship = $row;

                                                    if (in_array($internship['status'], [NULL])) {
                                                        $internship_status = 'None';
                                                        $internship_period = '-';
                                                        $internship_date = '-';
                                                    } else 
                                                    if (in_array($internship['status'], ['YES', 'Yes', 'yes'])) {
                                                        $internship_status = 'Internship Accepted';
                                                        $internship_period = range_time_elapsed_string($internship['start_date'], $internship['end_date']);
                                                        $internship_date = date('d-M-Y', strtotime($internship['start_date'])) . " until " . date('d-M-Y', strtotime($internship['end_date']));

                                                        $internship_date .= ' (' . $internship_period . ')';
                                                    } else
                                                
                                                    if (in_array($internship['status'], ['NO', 'No', 'no'])) {

                                                        $internship_status = 'Rejected by Company';
                                                        $internship_date = '-';
                                                        $internship_period = '-';
                                                    } else
                                                
                                                    if (in_array($internship['status'], ['WAITING', 'Waiting', 'waiting'])) {
                                                        $internship_status = 'Currently Submitting';

                                                        $internship_date = '-';
                                                        $internship_period = '-';
                                                    }


                                                ?>
                                                    <tr>
                                                        <td><?= $no++; ?>.</td>
                                                        <td><?= $row['nim'] . ' - ' . $row['name'] ?></td>
                                                        <td><?= $row['nama_kampus'] ?></td>
                                                        <td><?= $internship_status ?></td>
                                                        <td><?= $internship_date ?></td>
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


                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center float-left">
                                        <h4 class="card-title">Raw Internship Data</h4>
                                    </div>

                                    <!-- <a href="index.php?page=add_internship&id_company=<?= base64_encode($id_company) ?>" class="btn btn-sm btn-secondary float-right mr-1"><i class="fas fa-plus mr-2"></i>Add Data</a> -->

                                </div>
                                <div class="card-body">
                                    <div class="table-responsive" id="tabel-ini">
                                        <table id="raw-internship" class="display dataTable table table-striped table-hover">
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
                                                    FROM internship, student_internship, tb_kampus 
                                                    WHERE internship.id_company = :id_company 
                                                    AND student_internship.nim = internship.nim 
                                                    AND tb_kampus.id_kampus = student_internship.id_kampus 
                                                    ";

                                                $stmt = $_SERVER['PDO']->prepare($sql_student);
                                                $stmt->bindParam(':id_company', $id_company, PDO::PARAM_INT);

                                                $stmt->execute();

                                                $student_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                $no = 1;

                                                foreach ($student_data as $row) :


                                                    $internship = $row;

                                                    if (in_array($internship['status'], [NULL])) {
                                                        $internship_status = 'None';
                                                        $internship_period = '-';
                                                        $internship_date = '-';
                                                    } else 
                                                    if (in_array($internship['status'], ['YES', 'Yes', 'yes'])) {
                                                        $internship_status = 'Internship Accepted';
                                                        $internship_period = range_time_elapsed_string($internship['start_date'], $internship['end_date']);
                                                        $internship_date = date('d-M-Y', strtotime($internship['start_date'])) . " until " . date('d-M-Y', strtotime($internship['end_date']));

                                                        $internship_date .= ' (' . $internship_period . ')';
                                                    } else
                                                
                                                    if (in_array($internship['status'], ['NO', 'No', 'no'])) {

                                                        $internship_status = 'Rejected by Company';
                                                        $internship_date = '-';
                                                        $internship_period = '-';
                                                    } else
                                                
                                                    if (in_array($internship['status'], ['WAITING', 'Waiting', 'waiting'])) {
                                                        $internship_status = 'Currently Submitting';

                                                        $internship_date = '-';
                                                        $internship_period = '-';
                                                    }


                                                ?>
                                                    <tr>
                                                        <td><?= $no++; ?>.</td>
                                                        <td><?= $row['nim'] . ' - ' . $row['name'] ?></td>
                                                        <td><?= $row['nama_kampus'] ?></td>
                                                        <td><?= $internship_status ?></td>
                                                        <td><?= $internship_date ?></td>
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



            </div>

            <?php include "./view/upkpk/template/footer.php"; ?>


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
                            <?= csrf() ?> <?= input_hidden('id_company', $id) ?> <?= input_hidden('redirect', $_GET['redirect']) ?>
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


        </div>

    </div>
    <?php include "./view/upkpk/template/script.php"; ?>

    <!-- Datatables -->
    <script src="./assets/js/plugin/datatables/datatables.min.js"></script>

    <script>

        let _token = $('meta[name="csrf-token"]').attr('content');

        $(document).ready(function() {


            if (window.location.hash === "#add_user") {
                // Display the modal
                $("#ModalTambahUserCompany").modal('show');

                // You can add more code here to customize the modal behavior
            }

            <?php if (old('ModalTambahUserCompany') !== NULL) : echo "$('#ModalTambahUserCompany').modal('show')";
            endif; ?>
            <?php if (old('ModalEditUserCompany') !== NULL) : echo "$('#ModalEditUserCompany" . old('LoopEdit') . "').modal('show')";
            endif; ?>


            $('#ekonomi-kepri').DataTable({
                "pageLength": 10,
            });

            $('#student-internship').DataTable({
                "pageLength": 20,
            });


            $('#raw-internship').DataTable({
                "pageLength": 20,
            });


            


        });

        function confirmation_delete(id) {
                swal.fire({
                    title: 'Delete Confirmation',
                    text: 'Are you sure you want to delete current User Company ?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {


                        // AJAX
                        $.ajax({
                            url: 'index.php?request=delete_user_company',
                            type: 'POST',
                            data: {
                                id: id,
                                'id_company' : "<?= base64_encode($id_company) ?>" ,
                                '_method': 'DELETE',
                                'token': _token,
                            },
                            dataType: 'json',
                            success: function(response) {
                                window.location.reload(1);
                            },
                            error: function(response) {

                                
                                // window.location.reload(1);

                                Swal.fire("Deleting Data Failed !", "Please try again later !", "error");
                            }
                        });

                    }
                })

            }
    </script>

</body>

</html>