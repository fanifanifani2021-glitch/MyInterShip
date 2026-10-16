<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = 'My University';
$route = 'my_university';
include "./view/upkpk/template/head.php";

?>

<body>
    <div class="wrapper">

        <?php include "./view/upkpk/template/main-header.php"; ?>
        <?php include "./view/upkpk/template/sidebar.php"; ?>

        <div class="main-panel">
            <div class="container">



                <div class="panel-header bg-primary-gradient">
                    <div class="page-inner py-5">
                        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                            <div>
                                <h2 class="text-white pb-2 fw-bold">My University</h2>
                                <h5 class="text-white op-7 mb-2">University Identity Detail</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-inner mt--5">
                    <div class="row mt--2">


                        <div class="col-md-12">
                            <div class="card card-profile">
                                <div class="card-header" style="background-image: url('viewer.php?src=profile_background'); height: 300px; background-size: cover;background-repeat: no-repeat;">


                                    <!-- <a href="#" data-toggle="modal" data-target="#ModalBackground" type="button" role="button " class="btn btn-primary btn-sm pull-right"> <i class="fas fa-edit mr-2"></i>Change Background</a> -->

                                    <div class="profile-picture">
                                        <div class="avatar avatar-xxl">
                                            <img src="viewer.php?src=get_university_logo&location=<?= base64_encode($data['logo'] ?? '--') ?>" alt="..." class="avatar-img rounded-circle">
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="user-profile text-center mb-4">

                                        <div class="name"><?= $data['nama_kampus'] ?></div>
                                        <div class="job">Joined My Internship since <b><?= ($data['id_kampus'] == 1) ? 'Beginning' : date( 'd M Y',  strtotime($data['created_at']) ) ?></b></div>

                                    </div>




                                    <table class="table table-bordered table-hover table-responsive-md">
                                        <tbody>
                                            <tr>
                                                <th width="30%">Name <span class="float-right">:</span></th>
                                                <td width="70%"><span><?= $data['nama_kampus']; ?></span></td>
                                            </tr>

                                            <tr>
                                                <th width="30%">Logo <span class="float-right">:</span></th>
                                                <td width="70%"><span><?= $data['logo']; ?></span></td>
                                            </tr>

                                            <tr>
                                                <th width="30%">Website <span class="float-right">:</span></th>
                                                <td width="70%"><span><?= $data['website']; ?></span></td>
                                            </tr>

                                        </tbody>
                                    </table>


                                    <button class="btn btn-primary btn-sm float-left" data-toggle="modal" data-target="#ModalEdit"><i class="fas fa-edit mr-1"></i>Edit</button>


                                </div>


                            </div>
                        </div>





                    </div>



                </div>


            </div>




            <?php include "./view/upkpk/template/footer.php"; ?>


            <div class="modal fade" id="ModalBackground">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <span class="fw-mediumbold">
                                    Change Background</span>
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>


                    </div>
                </div>
                <!-- /.modal-content -->
            </div>


            <!-- modal edit -->
            <div class="modal fade" id="ModalEdit">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <span class="fw-mediumbold">
                                    Edit University Data</span>
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <form action="index.php?form=my_university" method="POST"> <?= csrf(); ?>

                            <div class="modal-body form-row">


                                <div class="form-group col-md-12">
                                    <label for="name">Name <label class="text-danger">*</label></label>
                                    <input type="text" placeholder="Fill University Name" class="form-control <?= show_error('name') ?>" name="name" id="name2" value="<?= old('name', $data['nama_kampus']) ?>">
                                    <?= form_error('name') ?>
                                </div>


                                <div class="form-group col-md-12">
                                    <label for="header">Logo <label class="text-danger">*</label></label>
                                    <input type="text" placeholder="Provile Link to Company Logo" class="form-control <?= show_error('logo') ?>" name="logo" id="logo2" value="<?= $data['logo']; ?>">
                                    <?= form_error('logo'); ?>
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="website">Website</label>
                                    <input type="text" placeholder="Fill Company Website" class="form-control <?= show_error('website') ?>" name="website" id="website2" value="<?= $data['website']; ?>">
                                    <?= form_error('website') ?>

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
        });
    </script>

</body>

</html>