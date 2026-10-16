<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = 'My Company';
$route = 'my_company';
include "./view/industri/template/head.php";

$id = $_SESSION['id_company'];
$sql = mysqli_query($koneksi_db, "SELECT * FROM company WHERE id_company = $id");
$data = mysqli_fetch_assoc($sql);

$sql2 = mysqli_query($koneksi_db, "SELECT * FROM user_company WHERE id_company = $id ORDER BY id_user_company ASC");

$cekhrd = mysqli_query($koneksi_db, "SELECT * FROM user_company WHERE id_company = $id AND user_type = 'HRD'");
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


$status .= '<i class="fas fa-info-circle text-info ml-2" data-toggle="tooltip" data-placement="top" title="1. Verified : Company is verified and registered, can receive student internship from Polibatam; 2. Not Verified : Company is not yet verified and not registered; 3. Blacklist : Company is blacklist from receiving Polibatam`s student internship;"></i>';

if ($data['access_type'] == 1) {
    $data['access_type'] = '1 - Can Make Job Offer';
} else if ($data['status'] == 2) {
    $data['access_type'] = '2 - Cannot Make Job Offer';
}

?>

<body>
    <div class="wrapper">

        <?php include "./view/industri/template/main-header.php"; ?>
        <?php include "./view/industri/template/sidebar.php"; ?>

        <div class="main-panel">
            <div class="container">



                <div class="panel-header bg-primary-gradient">
                    <div class="page-inner py-5">
                        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                            <div>
                                <h2 class="text-white pb-2 fw-bold">My Company</h2>
                                <h5 class="text-white op-7 mb-2">Company Identity Detail</h5>
                            </div>
                            <!-- <div class="ml-md-auto py-2 py-md-0">
								<button class="btn btn-secondary btn-round" data-toggle="modal" data-target="#ModalTambah"><i class="fas fa-plus mr-2"></i> Add Supervisor</button>
							</div> -->
                        </div>
                    </div>
                </div>

                <div class="page-inner mt--5">
                    <div class="row mt--2">


                        <div class="col-md-12">
                            <div class="card card-profile">
                                <div class="card-header" style="background-image: url('viewer.php?src=profile_background'); height: 300px; background-size: cover;background-repeat: no-repeat;">

                                    <?php if ($_SESSION['user_type'] == 'HRD') : ?>

                                        <a href="#" data-toggle="modal" data-target="#ModalEdit" type="button" role="button " class="btn btn-primary btn-sm pull-right"> <i class="fas fa-edit mr-2"></i>Edit</a>
                                    <?php endif; ?>


                                    <div class="profile-picture">
                                        <div class="avatar avatar-xxl">
                                            <img src="viewer.php?src=get_company_logo&location=<?= base64_encode($data['logo']) ?>" alt="..." class="avatar-img rounded-circle">
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="user-profile text-center mb-4">
                                        <div class="name"><?= $data['name'] ?></div>
                                        <div class="job">Company engaged in <?= $data['type'] ?? 'NaN' ?></div>
                                    </div>




                                    <table class="table table-bordered table-hover table-responsive-md">
                                        <tbody>
                                            <tr>
                                                <th width="30%">Name <span class="float-right">:</span></th>
                                                <td width="70%"><span><?= $data['name']; ?></span></td>
                                            </tr>

                                            <!-- <tr>
                                                <th width="30%">Alias <span class="float-right">:</span></th>
                                                <td width="70%"><span><?= $data['alias'] ?? 'NaN'; ?></span></td>
                                            </tr> -->


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
                                                <td width="70%"><span><?= $data['access_type']; ?></span></td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <?php if ($_SESSION['user_type'] == 'HRD') : ?>

                                        <button class="btn btn-primary btn-sm float-left" data-toggle="modal" data-target="#ModalEdit"><i class="fas fa-edit mr-1"></i>Edit</button>
                                    <?php endif; ?>


                                </div>


                            </div>
                        </div>


                        <!-- <div class="col-md-12">

                            <div class="card full-height">
                                <div class="card-header">
                                    <div class="d-flex align-items-center float-left">
                                        <h4 class="card-title">Company Identity</h4>
                                    </div>
                                </div>
                                <div class="card-body">

                                    <table class="table table-bordered table-hover table-responsive-md">
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
                                                <td width="70%"><span><?= $data['access_type']; ?></span></td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <?php if ($_SESSION['user_type'] == 'HRD') : ?>

                                        <button class="btn btn-secondary btn-md float-left" data-toggle="modal" data-target="#ModalEdit"><i class="fas fa-edit mr-1"></i>Edit Company</button>
                                    <?php endif; ?>
                                </div>

                            </div>

                        </div> -->




                        <div class="col-md-12">

                            <div class="card full-height">
                                <div class="card-header">
                                    <div class="d-flex align-items-center float-left">
                                        <h4 class="card-title">Company Description</h4>
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

            <?php include "./view/industri/template/footer.php"; ?>

            <!-- modal edit -->
            <div class="modal fade" id="ModalEdit">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <span class="fw-mediumbold">
                                    Edit Company</span>
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <form action="index.php?form=my_company" method="POST"> <?= csrf(); ?>
                            <?= input_hidden('id_company', $_SESSION['id_company']) ?>

                            <div class="modal-body form-row">


                                <div class="form-group col-md-12">
                                    <label for="name">Name <label class="text-danger">*</label></label>
                                    <input type="text" placeholder="Fill Company Name" class="form-control <?= show_error('name') ?>" name="name" id="name2" value="<?= $data['name']; ?>">
                                    <?= form_error('name') ?>
                                </div>

                                <script type="text/javascript">
                                    function toggle_company() {
                                        var company_type = document.getElementById('company_type');
                                        var div_company_other = document.getElementById('div_company_other');
                                        var type_other = document.getElementById('type_other');


                                        if (company_type.value == 'Other') {
                                            div_company_other.hidden = false;

                                        } else {
                                            div_company_other.hidden = true;
                                            type_other.value = '';
                                        }
                                    }
                                </script>

                                <div class="form-group col-md-12">
                                    <label for="type">Type <label class="text-danger">*</label></label>
                                    <select class="form-control <?= show_error('company_type') ?>" name="company_type" id="company_type" onchange="toggle_company()">
                                        <option hidden><?= $data['type']; ?></option>
                                        <option>Agency</option>
                                        <option>Design</option>
                                        <option>Education</option>
                                        <option>Engineering</option>
                                        <option>Finance</option>
                                        <option>Government</option>
                                        <option>Health</option>
                                        <option>IT and Telecomunication</option>
                                        <option>Logistic</option>
                                        <option>Marketing</option>
                                        <option>Media</option>
                                        <option>Other</option>
                                    </select>
                                    <?= form_error('company_type'); ?>
                                </div>

                                <div class="form-group col-md-12" hidden id='div_company_other'>
                                    <label for="type_other">Type (Other) <label class="text-danger">*</label></label>
                                    <input type="text" placeholder="Fill Company Type" class="form-control <?= show_error('company_type_other') ?>" name="company_type_other" id="company_type_other">
                                    <?= form_error('company_type_other'); ?>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="phone">Phone <label class="text-danger">*</label></label>
                                    <input type="text" placeholder="Fill Company Phone" class="form-control <?= show_error('phone') ?>" name="phone" id="phone2" value="<?= $data['phone']; ?>">
                                    <?= form_error('phone'); ?>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="email">E-Mail <label class="text-danger">*</label></label>
                                    <input type="text" placeholder="Fill Company Email" class="form-control <?= show_error('email') ?>" name="email" id="email2" value="<?= $data['email']; ?>">
                                    <?= form_error('email'); ?>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="website">LinkedIn</label>
                                    <input type="text" placeholder="Fill Company LinkedIn" class="form-control <?= show_error('linkedin') ?>" name="linkedin" id="linkedin2" value="<?= $data['linkedin']; ?>">
                                    <?= form_error('linkedin') ?>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="website">Website</label>
                                    <input type="text" placeholder="Fill Company Website" class="form-control <?= show_error('website') ?>" name="website" id="website2" value="<?= $data['website']; ?>">
                                    <?= form_error('website') ?>

                                </div>

                                <div class="form-group col-md-6">
                                    <label for="facebook">Facebook</label>
                                    <input type="text" placeholder="Fill Company Facebook" class="form-control <?= show_error('facebook') ?>" name="facebook" id="facebook2" value="<?= $data['facebook']; ?>">
                                    <?= form_error('facebook') ?>

                                </div>

                                <div class="form-group col-md-6">
                                    <label for="twitter">Twitter</label>
                                    <input type="text" placeholder="Fill Company Twitter" class="form-control <?= show_error('twitter') ?>" name="twitter" id="twitter2" value="<?= $data['twitter']; ?>">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="instagram">Instagram</label>
                                    <input type="text" placeholder="Fill Company Instagram" class="form-control <?= show_error('instagram') ?>" name="instagram" id="instagram2" value="<?= $data['instagram']; ?>">
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="header">Logo <label class="text-danger">*</label></label>
                                    <input type="text" placeholder="Provile Link to Company Logo" class="form-control <?= show_error('logo') ?>" name="logo" id="logo2" value="<?= $data['logo']; ?>">
                                    <?= form_error('logo'); ?>
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="address">Address <label class="text-danger">*</label></label>
                                    <input type="text" placeholder="Fill Company Address" class="form-control <?= show_error('address') ?>" name="address" id="address2" value="<?= $data['address']; ?>">
                                    <?= form_error('address'); ?>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="province">Province <label class="text-danger">*</label></label>
                                    <input type="text" placeholder="Fill Company Province" class="form-control <?= show_error('province') ?>" name="province" id="province2" value="<?= $data['province']; ?>">
                                    <?= form_error('province'); ?>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="city">City <label class="text-danger">*</label></label>
                                    <input type="text" placeholder="Fill Company City" class="form-control <?= show_error('city') ?>" name="city" id="city2" value="<?= $data['city']; ?>">
                                    <?= form_error('city'); ?>
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="description">Description <label class="text-danger">*</label></label>
                                    <textarea class="form-control <?= show_error('description') ?>" placeholder="Fill Company Description" id="description2" name="description" rows="4"><?= $data['description']; ?></textarea>
                                    <?= form_error('description'); ?>
                                </div>

                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Close</button>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-edit mr-1"></i>Edit</button>
                        </form>

                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
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


            CKEDITOR.replace('description2', {
                removeButtons: 'PasteFromWord,Styles,Format,Font,FontSize',
            });

            toggle_company();


            $('#ekonomi-kepri').DataTable({
                "pageLength": 10,
            });

        });
    </script>

</body>

</html>