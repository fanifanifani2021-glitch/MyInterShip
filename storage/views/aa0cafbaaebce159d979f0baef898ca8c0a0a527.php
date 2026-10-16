
<?php $__env->startSection('main-content'); ?>


<?php

if (in_array($_GET['tab'], [NULL, ''])) {
	$_GET['tab'] = 'identity';
} else {
	$_GET['tab'] = $_GET['tab'];
}

$nim_nik_unit = $_SESSION['nim_nik_unit'];
$query = $_SERVER['PDO']->prepare("SELECT lecturer.*, lecturer_profile.* FROM lecturer LEFT JOIN lecturer_profile ON lecturer.nim_nik_unit = lecturer_profile.nim_nik_unit WHERE lecturer.nim_nik_unit = :nim_nik_unit LIMIT 1");
$query->execute(['nim_nik_unit' => $nim_nik_unit]);

$data_lecturer = $query->fetch(PDO::FETCH_ASSOC);


$query_kampus = $_SERVER['PDO']->prepare("SELECT * FROM tb_kampus WHERE id_kampus = :id_kampus LIMIT 1");
$query_kampus->execute(array('id_kampus' => $data_lecturer['id_kampus']));

$university_data = $query_kampus->fetch(PDO::FETCH_ASSOC);

$data = $data_lecturer;


?>


<div class="container">

    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Profile</h2>
                    <h5 class="text-white op-7 mb-2">Update Identity or Profile Picture</h5>
                </div>
            </div>
        </div>
    </div>



    <div class="page-inner mt--5">
        <div class="row mt--2">



            <div class="col-md-4">


                <div class="card card-profile">
                    <div class="card-header" style="background-image: url('viewer.php?src=profile_background'); height: 150px; background-size: cover;background-repeat: no-repeat;">

                        <div class="profile-picture">
                            <div class="avatar avatar-xxl">
                                <img src="viewer.php?src=profile_picture" alt="..." class="avatar-img rounded-circle">
                            </div>
                        </div>
                    </div>

                    <div class="card-body">


                        <div class="user-profile text-center mb-2">
                            <div class="name"><?= $data_lecturer['name'] ?></div>
                            <div class="job">Lecturer at <?= ($_SESSION['POLIBATAM_STUDENT']) ? 'Politeknik Negeri Batam' : $university_data['nama_kampus'] ?? 'Politeknik Negeri Batam'; ?></div>

                        </div>


                    </div>


                </div>

            </div>




            <div class="col-md-8">
                <div class="card card-with-nav">
                    <div class="card-header">
                        <div class="row row-nav-line">
                            <ul class="nav nav-tabs nav-line nav-color-secondary w-100 pl-4" role="tablist">
                                <li class="nav-item"> <a class="nav-link <?php checker($_GET['tab'], ['identity', NULL], 'active') ?>" href="index.php?page=lecturer_profile" role="tab" aria-selected="true">Identity</a> </li>
                                <li class="nav-item"> <a class="nav-link <?php checker($_GET['tab'], ['profile'], 'active') ?>" href="index.php?page=lecturer_profile&tab=profile" role="tab" aria-selected="false">Profile</a> </li>

                                <?php if (!$_SESSION['POLIBATAM_LECTURER'] && $_GET['tab'] == 'password') { ?>
                                <li class="nav-item"> <a class="nav-link <?php checker($_GET['tab'], ['password'], 'active') ?>" href="index.php?page=lecturer_profile&tab=password" role="tab" aria-selected="false">Password</a> </li>
                                <?php } ?>
                                <!-- <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#settings" role="tab" aria-selected="false">Settings</a> </li> -->
                            </ul>
                        </div>
                    </div>


                    <?php
                    if (in_array($_GET['tab'], [NULL, '', 'identity'])) { ?>


                    <div class="card-body">


                        <div class="form-group text-center">

                            <div class="avatar avatar-xxl">
                                <img src="viewer.php?src=get_university_logo&location=<?= base64_encode($university_data['logo']) ?>" alt="..." class="avatar-img rounded-circle">
                            </div>

                        </div>

                        <div class="user-profile text-center mb-2">
                            <div class="name">
                                <?= ($_SESSION['POLIBATAM_STUDENT']) ? 'Politeknik Negeri Batam' : $university_data['nama_kampus'] ?? 'Politeknik Negeri Batam'; ?>
                                <sup> <a class="text-sm" target="_blank" href="<?= $university_data['website'] ?>"><i class="fas fa-external-link-alt"></i></a></sup>
                            </div>
                        </div>

                        <form id="form_lecturer_profile" action="index.php?form=lecturer_profile&action=identity" method="POST" onload="disabled_form()"> <?= csrf() ?>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group form-group-default">
                                        <label>Name <b class="text-danger">*</b></label>
                                        <input type="text" class="form-control" name="name" placeholder="Enter Your Full Name" value="<?= $data['name'] ?>">
                                        <?= form_error('name') ?>

                                    </div>
                                </div>



                                <div class="col-md-6">
                                    <div class="form-group form-group-default">
                                        <label>E-Mail <b class="text-danger">*</b></label>
                                        <input type="email" class="form-control" name="polibatam_email" placeholder="Enter Your Polibatam E-Mail" value="<?= $data['email_polibatam'] ?>">
                                        <?= form_error('polibatam_email') ?>

                                    </div>
                                </div>

                            </div>

                            <div class="row mt-3">

                                <div class="col-md-6">
                                    <div class="form-group form-group-default">
                                        <label>Other E-Mail <b class="text-danger">*</b></label>
                                        <input type="email" class="form-control" name="other_email" placeholder="Enter Your Other E-Mail" value="<?= $data['other_email'] ?>">
                                        <?= form_error('other_email') ?>

                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group form-group-default ">
                                        <label>Phone Number <b class="text-danger">*</b></label>
                                        <input type="text" class="form-control" name="phone" placeholder="Phone" value="<?= $data['phone'] ?>">
                                        <?= form_error('phone') ?>

                                    </div>
                                </div>
                            </div>


                        </form>

                        <div class="text-left mt-3 mb-3">
                            <?php if ($_SESSION['old']['open-form'] == 'open') : ?>
                            <button onclick="confirmation()" id="cancel_btn" class="btn btn-danger"><i class="fas fa-times mr-1"></i>Cancel</button>
                            <button onclick="enable_form()" id="edit_btn" class="btn btn-warning" hidden><i class="fas fa-edit mr-1"></i>Edit</button>
                            <button onclick="submit_form()" class="btn btn-success" id="save_btn"><i class="fas fa-save mr-1"></i>Save</button>
                            <?php else : ?>
                            <button onclick="confirmation()" id="cancel_btn" class="btn btn-danger" hidden><i class="fas fa-times mr-1"></i>Cancel</button>
                            <button onclick="enable_form()" id="edit_btn" class="btn btn-warning"><i class="fas fa-edit mr-1"></i>Edit</button>
                            <button onclick="submit_form()" class="btn btn-success" disabled id="save_btn"><i class="fas fa-save mr-1"></i>Save</button>
                            <?php endif; ?>

                        </div>
                    </div>

                    <?php } else if ($_GET['tab'] == 'profile') { ?>


                    <div class="card-body">

                        <div class="form-group">
                            <label class="">Current Profile Picture</label>
                            <div class="form-group">

                                <!-- <div class="col-lg-4 col-md-9 col-sm-8"> -->
                                <div class="avatar avatar-xxl">
                                    <img src="viewer.php?src=profile_picture" alt="..." class="avatar-img rounded">
                                </div>
                                <!-- </div> -->

                                <a target="_blank" download href="viewer.php?src=profile_picture&download=true" class="btn btn-info btn-sm"><i class="fas fa-download mr-2"></i>Unduh</a>


                                <?php if (!in_array($data['profile_picture'], ['profile.jpg', 'profile.png', null])) : ?>
                                <button type="button" href="#" onclick="delete_profile()" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt mr-2"></i>Remove</button>
                                <?php endif ?>

                            </div>

                        </div>


                        <form id="form_lecturer_profile" action="index.php?form=lecturer_profile&action=profile" method="POST" enctype="multipart/form-data" onload="disabled_form()"> <?= csrf() ?> <?= input_hidden('nim', $_SESSION['nim']) ?>


                            <div class="form-group">
                                <label class="">New Profile Picture <span class="required-label">*</span></label>
                                <input type="file" name="new_profile" accept="image/png, image/jpg, image/jpeg" id="new_profile" onchange="validateFile(this, '2MB')" class="form-control <?= show_error('new_profile') ?>" />
                                <?= form_error('new_profile') ?>
                                <span class="text-sm float-right">Allowed file : png, jpeg and jpg | Max Size : 2MB</span>


                            </div>


                        </form>

                        <div class="form-group">
                            <?php if ($_SESSION['old']['open-form'] == 'open') : ?>
                            <button onclick="confirmation()" id="cancel_btn" class="btn btn-danger"><i class="fas fa-times mr-1"></i>Cancel</button>
                            <button onclick="enable_form()" id="edit_btn" class="btn btn-warning" hidden><i class="fas fa-edit mr-1"></i>Edit</button>
                            <button onclick="submit_form()" class="btn btn-success" id="save_btn"><i class="fas fa-save mr-1"></i>Save</button>
                            <?php else : ?>
                            <button onclick="confirmation()" id="cancel_btn" class="btn btn-danger" hidden><i class="fas fa-times mr-1"></i>Cancel</button>
                            <button onclick="enable_form()" id="edit_btn" class="btn btn-warning"><i class="fas fa-edit mr-1"></i>Edit</button>
                            <button onclick="submit_form()" class="btn btn-success" disabled id="save_btn"><i class="fas fa-save mr-1"></i>Save</button>
                            <?php endif; ?>

                        </div>
                    </div>
                    <?php	} else if (!$_SESSION['POLIBATAM_LECTURER'] && $_GET['tab'] == 'password') { ?>


                    <div class="card-body">
                        <form id="form_lecturer_profile" action="index.php?form=lecturer_profile&action=password" method="POST" onload="disabled_form()"> <?= csrf() ?> <?= input_hidden('nim_nik_unit', $_SESSION['nim_nik_unit']) ?>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="form-group form-group-default">
                                        <label>Current Password <b class="text-danger">*</b></label>
                                        <input type="password" class="form-control" name="current_password" placeholder="Enter Your Current Password" value="<?= old('current_password') ?>">
                                        <?= form_error('current_password') ?>

                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group form-group-default">
                                        <label>New Password <b class="text-danger">*</b></label>
                                        <input type="password" class="form-control" name="new_password" placeholder="Enter Your New Password" value="<?= old('new_password') ?>">
                                        <?= form_error('new_password') ?>

                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group form-group-default">
                                        <label>New Password Confirmation <b class="text-danger">*</b></label>
                                        <input type="password" class="form-control" name="new_password2" placeholder="Confirm Your New Password" value="<?= old('new_password2') ?>">
                                        <?= form_error('new_password2') ?>

                                    </div>
                                </div>



                            </div>
                        </form>

                        <div class="form-group">
                            <?php if ($_SESSION['old']['open-form'] == 'open') : ?>
                            <button onclick="confirmation()" id="cancel_btn" class="btn btn-danger"><i class="fas fa-times mr-1"></i>Cancel</button>
                            <button onclick="enable_form()" id="edit_btn" class="btn btn-warning" hidden><i class="fas fa-edit mr-1"></i>Edit</button>
                            <button onclick="submit_form()" class="btn btn-success" id="save_btn"><i class="fas fa-save mr-1"></i>Save</button>
                            <?php else : ?>
                            <button onclick="confirmation()" id="cancel_btn" class="btn btn-danger" hidden><i class="fas fa-times mr-1"></i>Cancel</button>
                            <button onclick="enable_form()" id="edit_btn" class="btn btn-warning"><i class="fas fa-edit mr-1"></i>Edit</button>
                            <button onclick="submit_form()" class="btn btn-success" disabled id="save_btn"><i class="fas fa-save mr-1"></i>Save</button>
                            <?php endif; ?>

                        </div>
                    </div>


                    <?php
                    } else { ?>
                    <div class="card-body">
                        <!-- 404 Page Not Found -->
                        <div id="container">
                            <h1>404 Tab Not Found</h1>
                            <p>Your requsted tab is not found in this server.</p>

                        </div>
                    </div>

                    <?php }
                    ?>




                </div>
            </div>



        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('footer-scripts'); ?>
<!-- Soloalert -->
<script src='./core/component/soloalert.js'></script>

<?php if($_SESSION['old']['open-form'] != 'open' || $_SESSION['old']['open-form'] === NULL): ?> :

<script type="text/javascript">
    $(function() {
        disabled_form();
    });

</script>
<?php endif; ?>


<script>
    function submit_form() {
        console.log('helo there');
        $('form#form_lecturer_profile').submit();
    }

    function confirmation() {
        // alert('Are you sure you want to cancel ? (Your change will not be saved)');
        SoloAlert.confirm({
            title: "Are you sure you want to cancel ?"
            , body: "Changes you made will not be saved !"
            , useTransparency: true
            , onOk: () => {
                disabled_form()
            }
        });

    }

    function disabled_form() {

        $("#edit_btn").prop("disabled", false);
        $("#edit_btn").prop("hidden", false);


        $("#cancel_btn").prop("hidden", true);
        $("#cancel_btn").prop("disabled", true);

        $("#save_btn").prop("disabled", true);

        $("#form_lecturer_profile :input").prop("disabled", true);
        $("#form_lecturer_profile").trigger("reset");

        $("small").empty();

        $("#form_lecturer_profile :input").removeClass("is-invalid border-danger is-valid border-success");

    }

    function enable_form() {


        $("#edit_btn").prop("disabled", true);
        $("#edit_btn").prop("hidden", true);


        $("#cancel_btn").prop("hidden", false);
        $("#cancel_btn").prop("disabled", false);


        $("#save_btn").prop("disabled", false);

        $("#form_lecturer_profile :input").prop("disabled", false);

        $("#form_lecturer_profile :input").removeClass("is-invalid border-danger is-valid border-success");


    }




    function delete_profile() {

        let _token = $('meta[name="csrf-token"]').attr('content');

        swal.fire({
            title: 'Profile Remove Confirmation'
            , text: 'Are you sure you want to remove current profile picture ?'
            , icon: 'question'
            , showCancelButton: true
            , confirmButtonColor: "#DD6B55"
            , confirmButtonText: "Yes, I'm sure !"
        , }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                // AJAX
                $.ajax({
                    url: "index.php?request=profile_remove&action=lecturer",
                    // type: "GET",
                    type: "POST"
                    , data: {
                        'token': _token
                        , '_method': 'DELETE'
                    , },

                    success: function() {
                        setTimeout(function() {
                            window.location.reload(1);
                        }, 200);
                    },

                    error: function() {
                        console.log('gagalssss');
                    }
                });

            }
        })

    }

</script>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('templates.lecturer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/lecturer/lecturer_profile.blade.php ENDPATH**/ ?>