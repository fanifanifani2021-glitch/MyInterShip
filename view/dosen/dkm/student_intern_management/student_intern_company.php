<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = 'Company Profile';
$route = 'student_intern_management';
include "./view/dosen/template/head.php";

$id_internship = base64_decode($_GET['id_internship']);


// Cek Internship
$sql = "SELECT * FROM internship WHERE id_internship = '$id_internship'";
$exec = $_SERVER['KONEKSI_DB']->query($sql);

if ($exec->num_rows != 1) {
    make_swal2('error', 'Not Allowed', 'Internship Data is not found !');
    redirect_back(1);
    exit();
}

$intrenship_data = $exec->fetch_assoc();
$id_company = $intrenship_data['id_company'];

$sql2 = "SELECT * FROM company WHERE id_company = $id_company";
$data = $_SERVER['KONEKSI_DB']->query($sql2)->fetch_assoc();


if ( in_array($data['status'], [1, 'verified', 'Verified'] ) ) {
    $status = '<button type="button" class="btn btn-sm btn-success">' . $data['status'] . '</button>';
} else
    if ( in_array($data['status'], [0, 'not verified', 'Not Verified'] ) ) {
    $status = '<button type="button" class="btn btn-sm btn-danger">' . $data['status'] . '</button>';
} else
if ( in_array($data['status'], [2, 'blacklist', 'Blacklist'] ) ) {
    $status = '<button type="button" class="btn btn-sm btn-dark">' . $data['status'] . '</button>';
}

$status .= '<i class="fas fa-info-circle text-info ml-2" data-toggle="tooltip" data-placement="top" title="1. Verified : Company is verified and registered, can receive student internship from Polibatam; 2. Not Verified : Company is not yet verified and not registered; 3. Blacklist : Company is blacklist from receiving Polibatam`s student internship;"></i>';

?>

<body>
    <div class="wrapper">

        <?php include "./view/dosen/template/main-header.php"; ?>
        <?php include "./view/dosen/template/sidebar.php"; ?>

        <div class="main-panel">
            <div class="container">
                <div class="page-inner mt-2">


                    <div class="page-header">
                        <h4 class="page-title">Company Profile</h4>
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
                                <a href="index.php?page=student_intern_management">Students Management</a>
                            </li>

                            <li class="separator">
                                <i class="flaticon-right-arrow"></i>
                            </li>
                            <li class="nav-item">
                                <a href="index.php?page=student_intern_detail&nim=<?= base64_encode($internship_data['nim']) ?>">Student Detail</a>
                            </li>

                            <li class="separator">
                                <i class="flaticon-right-arrow"></i>
                            </li>
                            <li class="nav-item">
                                <a href="index.php?page=student_internship_detail&id_internship=<?= base64_encode($id_internship) ?>">Internship Detail</a>
                            </li>

                            <li class="separator">
                                <i class="flaticon-right-arrow"></i>
                            </li>
                            <li class="nav-item">
                                <a href="#">Company Profile</a>
                            </li>
                        </ul>
                    </div>


                    <div class="row mt-2">


                        <div class="col-md-12">

                            <div class="card full-height">
                                <div class="card-header">
                                    <div class="d-flex align-items-center float-left">
                                        <h4 class="card-title">Company Identity</h4>
                                    </div>
                                </div>
                                <div class="card-body">

                                    <table class="table table-bordered table-hover">
                                        <tbody>
                                            <tr>
                                                <th width="30%">Name <span class="float-right">:</span></th>
                                                <td width="70%"><span><?= $data['name']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <th width="30%">Type <span class="float-right">:</span></th>
                                                <td width="70%"><span><?= $data['type']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <th width="30%">Phone <span class="float-right">:</span></th>
                                                <td width="70%"><span><?= $data['phone']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <th width="30%">Email <span class="float-right">:</span></th>
                                                <td width="70%"><span><?= $data['email']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <th width="30%">Website <span class="float-right">:</span></th>
                                                <td width="70%"><span><?= $data['website']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <th width="30%">Facebook <span class="float-right">:</span></th>
                                                <td width="70%"><span><?= $data['facebook']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <th width="30%">Twitter <span class="float-right">:</span></th>
                                                <td width="70%"><span><?= $data['twitter']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <th width="30%">Instagram <span class="float-right">:</span></th>
                                                <td width="70%"><span><?= $data['instagram']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <th width="30%">LinkedIn <span class="float-right">:</span></th>
                                                <td width="70%"><span><?= $data['linkedin']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <th width="30%">Logo <span class="float-right">:</span></th>
                                                <td width="70%"><span><?= $data['logo']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <th width="30%">Address <span class="float-right">:</span></th>
                                                <td width="70%"><span><?= $data['address']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <th width="30%">Province <span class="float-right">:</span></th>
                                                <td width="70%"><span><?= $data['province']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <th width="30%">City <span class="float-right">:</span></th>
                                                <td width="70%"><span><?= $data['city']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <th width="30%">Status <span class="float-right">:</span></th>
                                                <td width="70%"><span><?= $status; ?></span></td>
                                            </tr>
                                            <tr>
                                                <th width="30%">Access Type <span class="float-right">:</span></th>
                                                <td width="70%"><span><?= $data['access_type']; ?> <i class="ml-2 fas fa-info-circle text-info" data-toggle="tooltip" data-placement="top" title="1 - Make Offer for Internship; 2 - Can Make Offer and Always Open for Internship;"></i></span></td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <a role="button" type="button" href="index.php?page=company_list_detail&id_company=<?= base64_encode($data['id_company']) ?>" type="button" class="btn btn-secondary btn-md float-left">
                                        <i class="fa fa-info-circle mr-1"></i> More Detail
                                    </a>
                                </div>

                            </div>
                        </div>

                        <!-- Description Detail -->
                        <div class="col-md-12">

                            <div class="card full-height">
                                <div class="card-header">
                                    <div class="d-flex align-items-center float-left">
                                        <h4 class="card-title">Description</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <?= $data['description']; ?>
                                </div>

                            </div>
                        </div>
                    </div>


                </div>
            </div>


            <?php include "./view/dosen/template/footer.php"; ?>

        </div>



    </div>




    </div>

    </div>
	<?php include "./view/dosen/template/script.php"; ?>



</body>

</html>