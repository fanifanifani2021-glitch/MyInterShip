<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = 'Company Profile';
$route = 'my_internship';
include "./view/mahasiswa/template/head.php";


// Pastikan $_GET['id_internship'] dan $_SESSION['nim'] telah ada dan aman untuk digunakan
$id_internship = isset($_GET['id_internship']) ? base64_decode($_GET['id_internship']) : null;
$nim = isset($_SESSION['nim']) ? $_SESSION['nim'] : null;

if (!$id_internship || !$nim) {
    make_swal2('warning', 'Not Allowed', 'You do not have access to this page !');
    redirect_back(1);
    exit;
}

// Cek Internship
$sql = "SELECT * FROM internship WHERE id_internship = :id_internship AND nim = :nim LIMIT 1";
$exec2 = $_SERVER['PDO']->prepare($sql);
$exec2->execute([':id_internship' => $id_internship, ':nim' => $nim]);

if ($exec2->rowCount() != 1) {
    make_swal2('warning', 'Not Allowed', 'You do not have access to this page !');
    redirect_back(1);
    exit;
}

$intrenship_data = $exec2->fetch(PDO::FETCH_ASSOC);

$id_company = $intrenship_data['id_company'];
$sql = "SELECT * FROM company WHERE id_company = :id_company";

$exec = $_SERVER['PDO']->prepare($sql);
$exec->execute(['id_company' => $intrenship_data['id_company']]);

$data = $exec->fetch(PDO::FETCH_ASSOC);

$status = '';
if (isset($data['status'])) {
    if (in_array($data['status'], [1, 'verified', 'Verified'])) {
        $status = '<button type="button" class="btn btn-sm btn-success">Verified</button>';
    } elseif (in_array($data['status'], [0, 'not verified', 'Not Verified'])) {
        $status = '<button type="button" class="btn btn-sm btn-danger">Not Verified</button>';
    } elseif (in_array($data['status'], [2, 'blacklist', 'Blacklist'])) {
        $status = '<button type="button" class="btn btn-sm btn-dark">Blacklist</button>';
    }
}

$status .= '<i class="fas fa-info-circle text-info ml-2" data-toggle="tooltip" data-placement="top" title="1. Verified : Company is verified and registered, can receive student internship from Polibatam; 2. Not Verified : Company is not yet verified and not registered; 3. Blacklist : Company is blacklist from receiving Polibatam`s student internship;"></i>';
?>

<body>
    <div class="wrapper">

        <?php include "./view/mahasiswa/template/main-header.php"; ?>
        <?php include "./view/mahasiswa/template/sidebar.php"; ?>

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
                                <a href="index.php?page=my_internship">My Internship</a>
                            </li>

                            <li class="separator">
                                <i class="flaticon-right-arrow"></i>
                            </li>
                            <li class="nav-item">
                                <a href="index.php?page=internship_detail&id_internship=<?= base64_encode($id_internship) ?>">Internship Detail</a>
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

                            <div class="card card-profile">
                                <div class="card-header" style="background-image: url('viewer.php?src=profile_background'); height: 300px; background-size: cover;background-repeat: no-repeat;">

                                    <div class="profile-picture">
                                        <div class="avatar avatar-xxl">
                                            <img src="viewer.php?src=get_company_logo&location=<?= base64_encode($data['logo']); ?>" alt="..." class="avatar-img rounded-circle">
                                        </div>
                                    </div>
                                </div>

                                <!-- Company Data -->
                                <div class="card-body ">

                                    <div class="user-profile text-center mb-2">
                                        <div class="name"><?= $data['name']; ?> <a class="text-sm" onclick="copyToClipboard('<?= $data['name'] ?>')"><i class="fas fa-copy"></i></a></div>
                                        <div class="job">Company engaged in <?= $data['type']; ?></div>
                                    </div>
                                </div>


                                <div class="card-body form-row">

                                <div class="form-group col-md-12">
                                        <label>Company Name </label>
                                        <input type="text" class="form-control" value="<?= $data['name']; ?>" />

                                    </div>

                                    <div class="form-group col-md-12">
                                        <label>Company Type </label>
                                        <input type="text" class="form-control" value="<?= $data['type']; ?>" />

                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Province </label>

                                        <input type="text" class="form-control" value="<?= $data['province']; ?>" name="company_province" placeholder="Enter Company Province Location" />
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>City </label>

                                        <input type="text" class="form-control" value="<?= $data['city']; ?>" name="company_city" placeholder="Enter Company City Location" />
                                    </div>


                                    <div class="form-group col-md-12">
                                        <label>Address </label>

                                        <textarea class="form-control <?= show_error('company_address') ?>" name="company_address" placeholder="Enter Company Address" rows="3"><?= $data['address']; ?></textarea>
                                    </div>

                                </div>


                                <div class="separator-solid"></div>


                                <div class="card-body form-row">

                                    <?php if (!in_array($data['phone'], [NULL, '-', ''])) : ?>

                                        <div class="form-group col-md-6">
                                            <label>Phone Number </label>

                                            <div class="input-group">
                                                <input type="tel" class="form-control <?= show_error('company_phone') ?>" name="company_phone" value="<?= $data['phone']; ?>" placeholder="Enter Company Phone Number" />
                                                <div class="input-group-append">
                                                    <!-- <a class="btn btn-outline-secondary"><i class="fas fa-eye mr-1"></i>Show</a> -->
                                                    <a class="btn btn-outline-secondary" href="tel:<?= $data['phone']; ?>" target="_blank"><i class="fas fa-phone mr-1"></i>Call</a>

                                                </div>
                                            </div>
                                        </div>

                                    <?php endif; ?>

                                    <?php if (!in_array($data['email'], [NULL, '-', ''])) : ?>


                                        <div class="form-group col-md-6">
                                            <label>E-Mail </label>

                                            <div class="input-group">
                                                <input type="email" class="form-control <?= show_error('company_email') ?>" name="company_email" value="<?= $data['email']; ?>" placeholder="Enter Company E-Mail" />
                                                <div class="input-group-append">
                                                    <!-- <a class="btn btn-outline-secondary"><i class="fas fa-eye mr-1"></i>Show</a> -->
                                                    <a class="btn btn-outline-secondary" href="mailto:<?= $data['email']; ?>" target="_blank"><i class="fas fa-envelope mr-1"></i>E-Mail</a>

                                                </div>
                                            </div>



                                        </div>

                                    <?php endif; ?>
                                    
                                    <?php if (!in_array($data_company['twitter'], [NULL, '-', ''])) : ?>
										<div class="form-group col-md-6">
											<label>Twitter </label>

											<div class="input-group ">
												<input type="text" class="form-control <?= show_error('company_email') ?>" name="company_email" value="<?= $data_company['twitter']; ?>" placeholder="Enter Company E-Mail" />
												<div class="input-group-append">
													<!-- <a class="btn btn-outline-secondary"><i class="fas fa-eye mr-1"></i>Show</a> -->
													<a class="btn btn-outline-secondary" href="index.php?viewer=url&location=<?= base64_encode('https://twitter.com/' . $data_company['twitter']) ?>" target="_blank"><i class="fab fa-twitter mr-1"></i>Follow</a>

												</div>
											</div>
										</div>
									<?php endif; ?>



									<?php if (!in_array($data_company['instagram'], [NULL, '-', ''])) : ?>
										<div class="form-group col-md-6">
											<label>Instagram </label>
											<div class="input-group">
												<input type="text" class="form-control <?= show_error('company_email') ?>" name="company_email" value="<?= $data_company['instagram']; ?>" placeholder="Enter Company E-Mail" />
												<div class="input-group-append">
													<!-- <a class="btn btn-outline-secondary"><i class="fas fa-eye mr-1"></i>Show</a> -->
													<a class="btn btn-outline-secondary" href="index.php?viewer=url&location=<?= base64_encode('https://instagram.com/' . $data_company['instagram']) ?>" target="_blank"><i class="fab fa-instagram mr-1"></i>Follow</a>

												</div>
											</div>
										</div>

									<?php endif; ?>


									<?php if (!in_array($data_company['facebook'], [NULL, '-', ''])) : ?>
										<div class="form-group col-md-6">
											<label>Facebook </label>

											<div class="input-group">
												<input type="text" class="form-control <?= show_error('company_email') ?>" name="company_email" value="<?= $data_company['facebook']; ?>" placeholder="Enter Company E-Mail" />
												<div class="input-group-append">
													<!-- <a class="btn btn-outline-secondary"><i class="fas fa-eye mr-1"></i>Show</a> -->
													<a class="btn btn-outline-secondary" href="index.php?viewer=url&location=<?= base64_encode('https://facebook.com/' .$data_company['facebook']) ?>" target="_blank"><i class="fab fa-facebook mr-1"></i>Follow</a>

												</div>
											</div>


										</div>
									<?php endif; ?>


									<?php if (!in_array($data_company['linkedin'], [NULL, '-', ''])) : ?>

										<div class="form-group col-md-6">
											<label>LinkedIn </label>

											<div class="input-group">
												<input type="text" class="form-control <?= show_error('company_email') ?>" name="company_email" value="<?= $data_company['linkedin']; ?>" placeholder="Enter Company E-Mail" />
												<div class="input-group-append">
													<!-- <a class="btn btn-outline-secondary"><i class="fas fa-eye mr-1"></i>Show</a> -->
													<a class="btn btn-outline-secondary" href="index.php?viewer=url&location=<?= base64_encode('https://linkedin.com/company/' .$data_company['linkedin'] ) ?>" target="_blank"><i class="fab fa-linkedin mr-1"></i>Follow</a>

												</div>
											</div>


										</div>

									<?php endif; ?>


									<?php if (!in_array($data_company['website'], [NULL, '-', ''])) : ?>
										<div class="form-group col-md-12">
											<label>Website </label>


											<div class="input-group mb-3">
												<input type="text" class="form-control <?= show_error('company_website') ?>" name="company_website" value="<?= $data_company['website']; ?>" placeholder="Enter Company Website" />
												<div class="input-group-append">
													<!-- <a class="btn btn-outline-secondary"><i class="fas fa-eye mr-1"></i>Show</a> -->
													<a class="btn btn-outline-secondary" href="index.php?viewer=url&location=<?= base64_encode( 'https://' .$data_company['website'] ) ?>" target="_blank"><i class="fas fa-globe mr-1"></i>Visit</a>

												</div>
											</div>
										</div>
									<?php endif; ?>


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

            <?php include "./view/mahasiswa/template/footer.php"; ?>


        </div>




    </div>

    </div>

    <!--   Core JS Files   -->
    <script src="./assets/js/core/jquery.3.2.1.min.js"></script>
    <script src="./assets/js/core/popper.min.js"></script>
    <script src="./assets/js/core/bootstrap.min.js"></script>

    <!-- jQuery UI -->
    <script src="./assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
    <script src="./assets/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js"></script>

    <!-- jQuery Scrollbar -->
    <script src="./assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

    <!-- Moment JS -->
    <script src="./assets/js/plugin/moment/moment.min.js"></script>


    <!-- Datatables -->
    <script src="./assets/js/plugin/datatables/datatables.min.js"></script>


    <!-- Bootstrap Toggle -->
    <script src="./assets/js/plugin/bootstrap-toggle/bootstrap-toggle.min.js"></script>


    <!-- Dropzone -->
    <script src="./assets/js/plugin/dropzone/dropzone.min.js"></script>

    <!-- Fullcalendar -->
    <script src="./assets/js/plugin/fullcalendar/fullcalendar.min.js"></script>

    <!-- DateTimePicker -->
    <script src="./assets/js/plugin/datepicker/bootstrap-datetimepicker.min.js"></script>

    <!-- Bootstrap Tagsinput -->
    <script src="./assets/js/plugin/bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>

    <!-- Bootstrap Wizard -->
    <script src="./assets/js/plugin/bootstrap-wizard/bootstrapwizard.js"></script>

    <!-- jQuery Validation -->
    <script src="./assets/js/plugin/jquery.validate/jquery.validate.min.js"></script>

    <!-- Summernote -->
    <script src="./assets/js/plugin/summernote/summernote-bs4.min.js"></script>

    <!-- Select2 -->
    <script src="./assets/js/plugin/select2/select2.full.min.js"></script>


    <!-- Owl Carousel -->
    <script src="./assets/js/plugin/owl-carousel/owl.carousel.min.js"></script>

    <!-- Atlantis JS -->
    <script src="./assets/js/atlantis.min.js"></script>

    <script>
        $(document).ready(function() {


            <?php if (old('ModalEdit') !== NULL) : echo "$('#ModalEdit').modal('show')";
            endif; ?>

            $('#ekonomi-kepri').DataTable({
                "pageLength": 10,
            });

        });
    </script>

</body>

</html>