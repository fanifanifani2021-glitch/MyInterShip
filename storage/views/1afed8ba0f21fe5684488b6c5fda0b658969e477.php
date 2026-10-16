
<?php $__env->startSection('main-content'); ?>


<?php
$nim = $_SESSION['nim'];


// Get Student Data
$query = $_SERVER['PDO']->prepare("SELECT * FROM student_internship WHERE nim = :nim LIMIT 1");
$query->execute(array('nim' => $nim));

if ($query->rowCount() != 1) {
make_swal2('warning', 'Not Allowed', 'You do not have access to this page !');
redirect_back(1);
exit;
}

$student_data = $query->fetch(PDO::FETCH_ASSOC);


// Get Student Profile
$query = $_SERVER['PDO']->prepare("SELECT * FROM student_profile WHERE nim = :nim LIMIT 1");
$query->execute(array('nim' => $nim));

$student_profile = $query->fetch(PDO::FETCH_ASSOC);
$student_profile['talenthub_cv'] = $student_profile['talenthub_cv'] ?? "https://talenthub.polibatam.ac.id/index.php?page=mhsprofile&nim=" .base64_encode($student_data['nim']);



if (in_array($_GET['tab'], [NULL, ''])) {
$_GET['tab'] = 'identity';
} else {
$_GET['tab'] = $_GET['tab'];
}

// Get Data Kampus
$query_kampus = $_SERVER['PDO']->prepare("SELECT * FROM tb_kampus WHERE id_kampus = :id_kampus LIMIT 1");
$query_kampus->execute(array('id_kampus' => $student_data['id_kampus']));

$university_data = $query_kampus->fetch(PDO::FETCH_ASSOC);

// Get All Prodi
$query_prodi = $_SERVER['PDO']->prepare("SELECT * FROM program_study WHERE id_kampus = :id_kampus");
$query_prodi->execute(array('id_kampus' => $student_data['id_kampus']));

$student_data_prodi = $query_prodi->fetchAll(PDO::FETCH_ASSOC);
?>


<div class="container">
    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Student Identity</h2>
                    <h5 class="text-white op-7 mb-2">Update Internship Profile, Profile Picture, and more . . .</h5>
                </div>
            </div>
        </div>
    </div>



    <div class="page-inner mt--5">
        <div class="row mt--2">
            <div class="col-md-12">

                <div class="card card-with-nav">

                    <div class="card-header">
                        <div class="row row-nav-line">
                            <ul class="nav nav-tabs nav-line nav-color-secondary w-100 pl-4" role="tablist">
                                <li class="nav-item"> <a class="nav-link <?php checker($_GET['tab'], ['identity', NULL], 'active') ?>" href="<?php echo e(router('student.identity')); ?>" role="tab" aria-selected="true">Identity</a> </li>
                                <li class="nav-item"> <a class="nav-link <?php checker($_GET['tab'], ['profile'], 'active') ?>" href="<?php echo e(router('student.identity')); ?>&tab=profile" role="tab" aria-selected="false">Profile Picture</a></li>

                                <?php if (!$_SESSION['POLIBATAM_STUDENT']) { ?>
                                <li class="nav-item"> <a class="nav-link <?php checker($_GET['tab'], ['password'], 'active') ?>" href="<?php echo e(router('student.identity')); ?>&tab=password" role="tab" aria-selected="false">Password</a> </li>
                                <!-- <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#settings" role="tab" aria-selected="false">Password</a> </li> -->
                                <?php } ?>


                                <?php if ($_SESSION['POLIBATAM_STUDENT']) { ?>

                                <li class="nav-item"> <a class="nav-link <?php checker($_GET['tab'], ['account'], 'active') ?>" href="<?php echo e(router('student.identity')); ?>&tab=account" role="tab" aria-selected="false">Account</a> </li>
                                <?php } ?>

                            </ul>
                        </div>
                    </div>

                    <div class="card-body row">


                        

                        <?php if(in_array($_GET['tab'], [NULL, '', 'identity'])): ?>
                        <div class="col-md-12">

                            <h3 class="text-center my-1">My Identity</h3>

                            <form action="<?php echo e(router('student.identity.post')); ?>&action=identity" method="POST"> <?= csrf() ?>


                                <div class="form-group">
                                    <label>Name <b class="text-danger">*</b></label>
                                    <input type="text" class="form-control" name="name" placeholder="Enter Your Full Name" value="<?= $student_data['name'] ?>">
                                    <?= form_error('name') ?>
                                </div>

                                <div class="form-group">
                                    <label>NIM</label>
                                    <input type="text" class="form-control" name="nim" placeholder="Enter Your Full Name" value="<?= old('nim', $student_data['nim']) ?>" disabled readonly>
                                    <?= form_error('nim') ?>
                                </div>

                                <div class="form-group">
                                    <label>Program Study</label>
                                    <select class="form-control" disabled readonly>
                                        <?php $__currentLoopData = $student_data_prodi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $prodi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?= $prodi['kode_prodi'] ?>" <?= ($student_data['program_study'] == $prodi['kode_prodi']) ? 'selected' : ''; ?>>
                                            <?= $prodi['jenjang'] . ' ' . $prodi['prodi'] ?>
                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?= form_error('program_study') ?>
                                </div>


                                <div class="form-group">
                                    <label>E-Mail <b class="text-danger">*</b></label>
                                    <input type="email" class="form-control" name="email" placeholder="Enter Your E-Mail" value="<?= $student_data['email'] ?>">
                                    <?= form_error('email') ?>

                                </div>

                                <div class="form-group">
                                    <label>Other E-Mail <b class="text-danger">*</b></label>
                                    <input type="email" class="form-control" name="other_email" placeholder="Enter Your Other E-Mail" value="<?= $student_data['other_email'] ?>">
                                    <?= form_error('other_email') ?>

                                </div>

                                <div class="form-group">
                                    <label>WhatsApp Number <b class="text-danger">*</b></label>
                                    <input type="tel" class="form-control" name="no_whatsapp" placeholder="Whatsapp Number" value="<?= $student_data['no_whatsapp'] ?>">
                                    <?= form_error('no_whatsapp') ?>

                                </div>

                                <div class="form-group">
                                    <label>Phone Number <b class="text-danger">*</b></label>
                                    <input type="text" class="form-control" name="phone" placeholder="Phone" value="<?= $student_data['phone'] ?>">
                                    <?= form_error('phone') ?>

                                </div>

                                <div class="text-left mt-3 mb-3">
                                    <button onclick="enable_form()" type="reset" class="btn btn-warning"><i class="fas fa-sync mr-1"></i>Reset</button>
                                    <button onclick="submit_form()" type="submit" class="btn btn-success"><i class="fas fa-save mr-1"></i>Save</button>
                                </div>
                            </form>

                        </div>



                        <div class="col-md-12">

                            <h3 class="text-center my-1">Internship Document</h3>

                            <form action="<?php echo e(router('student.identity.post')); ?>&action=internship_document" method="POST" enctype="multipart/form-data"> <?= csrf() ?>


                                <div class="form-group">
                                    <div class="d-flex justify-content-between">
                                        <!-- Label di kiri -->
                                        <label class="float-left">Talenthub CV <b class="text-danger">*</b></label>

                                        <!-- Link di kanan -->
                                        <a class="float-right" href="https://talenthub.polibatam.ac.id">Visit Talenthub</a>
                                    </div>
                                    <input type="text" class="form-control" name="talenthub_cv" placeholder="Enter Your Talenthub CV" value="<?= old('talenthub_cv', $student_profile['talenthub_cv']) ?>">

                                    <div class="d-flex justify-content-between">
                                        <a class="float-left" href="<?= $student_profile['talenthub_cv'] ?>">Visit My Talenthub CV</a>
                                        <b class="float-left">Please make sure your Talenthub CV is Active and Up to-date</b>

                                    </div>
                                    <!-- Link untuk CV yang sudah diisi -->


                                    <?= form_error('talenthub_cv') ?>
                                </div>


                                <div class="form-group">
                                    <label>Academic Transcript <b class="text-danger">*</b></label>


                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" placeholder="Your Academic Transcript will be shown here" value="<?= $student_profile['academic_transcript'] ?>" disabled readonly>
                                        <div class="input-group-append">
                                            <a href="<?php echo e(base_url('index.php?viewer=academic_transcript&nim=' .base64_encode($student_data['nim']) )); ?>" class="btn btn-outline-secondary" type="button"> <i class="fas fa-eye mr-1"></i> View</a>
                                        </div>
                                    </div>

                                    <input type="file" accept="application/pdf" class="my-1 form-control" name="academic_transcript" placeholder="Enter Your Academic Transcript" value="<?= old('academic_transcript') ?>">



                                    <?= form_error('academic_transcript') ?>
                                </div>


                                <div class="text-left mt-3 mb-3">
                                    <button type="reset" class="btn btn-warning"><i class="fas fa-sync mr-1"></i>Reset</button>
                                    <button type="submit" class="btn btn-success"><i class="fas fa-save mr-1"></i>Save</button>
                                </div>

                            </form>


                        </div>


                        <?php elseif(in_array($_GET['tab'], ['account'])): ?>





                        <div class="modal fade" id="HelperAccount" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">How it works? MyInternship Local Account</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body form-row">
                                        <div class="form-group col-md-12">

                                            To activate your MyInternship Local Account, you need to follow these steps:
                                            <ol>
                                                <li>Change the <b>MyInternship Local Account Status</b> to <b>Active</b>.</li>
                                                <li>Click <b>Save</b>.</li>
                                                <li>Create a <b>Password for your local account</b>. Please note that <b>this password</b> is <b>separate from your Learning Account password</b>, and you cannot use your Learning Account credentials to log in with the MyInternship Local Account.</li>
                                            </ol>


                                            <br>
                                            To de-activate or Reset Password for your MyInternship Local Account, you need to follow these steps:
                                            <ol>
                                                <li>Change the <b>MyInternship Local Account Status</b> to <b>Not Active</b>.</li>
                                                <li>Click <b>Save</b>.</li>

                                            </ol>


                                            <br>
                                            To login using MyInternship Local Account, you need to follow these steps:
                                            <ol>
                                                <li>Go to <b>Student Login</b>.</li>
                                                <li>Enter your <b>Student ID (NIM)</b> and your <b>MyInternship Local Account Password</b>.</li>
                                                <li>Click <b>Login</b>.</li>
                                            </ol>
                                        </div>
                                    </div>
                                    <div class="modal-footer text-center">
                                        <button data-dismiss="modal" class="btn btn-primary w-100">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="modal fade" id="HelperAccount2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">What is this? MyInternship Login Method</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body form-row">
                                        <div class="form-group col-md-12">
                                            As a student of Polibatam, you have the option to log in to MyInternship using your Learning Account.
                                            However, it is also recommended to activate your MyInternship Local Account in case you encounter any login issues with the Learning Account.

                                        </div>
                                    </div>
                                    <div class="modal-footer text-center">
                                        <button data-dismiss="modal" class="btn btn-primary w-100">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <form method="POST" action="<?php echo e(router('student.identity.post')); ?>&action=local_account"> <?= csrf(); ?> <?= input_hidden('nim', $_SESSION['nim']); ?>
                            <div class="col-md-12">


                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">

                                            <h3>MyInternship Login Method</h3>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="">Current MyInternship login method</label>
                                    <a class="float-right text-sm" href="#" data-toggle="modal" data-target="#HelperAccount2"><i class="fas fa-question-circle mr-1"></i>What is this ?</a>

                                    <div type="text" class="form-control">
                                        <?php if ($_SESSION['POLIBATAM_STUDENT']) {
                                                    echo "<li>Polibatam Learning Account</li>";
                                                } ?>

                                        <?php if ($student_data['local_account']) {
                                                    echo "<li>MyInternship Local Account</li>";
                                                } ?>
                                    </div>

                                </div>

                                <div class="form-group">
                                    <label class="">MyInternship Local Account Status <b class="text-danger">*</b></label>
                                    <a class="float-right text-sm" href="#" data-toggle="modal" data-target="#HelperAccount"><i class="fas fa-question-circle mr-1"></i>How it work ?</a>

                                    <select class="form-control" name="local_account" id="local_account" onchange="password_maker( this.value )">
                                        <option value="0" <?= (old('local_account', $student_data['local_account']) == 0) ? 'selected' : ''; ?>>Not Active</option>
                                        <option value="1" <?= (old('local_account', $student_data['local_account']) == 1) ? 'selected' : ''; ?>>Active</option>
                                    </select>
                                </div>

                                <div class="form-group">

                                    <button type="submit" class="btn btn-success"><i class="fas fa-save mr-1"></i>Save</button>

                                </div>
                            </div>
                        </form>



                        <?php if($student_data['local_account']): ?>

                        <hr>

                        <div class="col-md-12">

                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="form-group">

                                        <h3>MyInternship Local Account Password Manager</h3>
                                    </div>
                                </div>
                            </div>

                            <?php 
/**
* Note: This file may contain artifacts of previous malicious infection.
* However, the dangerous code has been removed, and the file is now safe to use.
*/
 ?>
                        </div>

                        <?php endif; ?>


                        <?php elseif($_GET['tab'] == 'profile'): ?>


                        <div class="col-md-12">


                            <div class="form-group">
                                <label class="">Current Profile Picture</label>
                                <div class="form-group">

                                    <!-- <div class="col-lg-4 col-md-9 col-sm-8"> -->
                                    <div class="avatar avatar-xxl">
                                        <img src="viewer.php?src=profile_picture" alt="..." class="avatar-img rounded">
                                    </div>
                                    <!-- </div> -->

                                    <a target="_blank" download href="viewer.php?src=profile_picture&download=true" class="btn btn-info btn-sm"><i class="fas fa-download mr-2"></i>Unduh</a>

                                    <?php if (!in_array($student_data['profile_picture'], ['profile.jpg', 'profile.png', null])) : ?>
                                    <button type="button" href="#" onclick="delete_profile()" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt mr-2"></i>Remove</button>
                                    <?php endif ?>

                                </div>

                            </div>

                            <form id="form" action="<?php echo e(router('student.identity.post')); ?>&action=profile" method="POST" enctype="multipart/form-data" onload="disabled_form()"> <?= csrf() ?> <?= input_hidden('nim', $_SESSION['nim']) ?>

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
                        <?php elseif(!$_SESSION['POLIBATAM_STUDENT'] && $_GET['tab'] == 'password'): ?>


                        <div class="card-body">
                            <form id="form" action="<?php echo e(router('student.identity.post')); ?>&action=password" method="POST" onload="disabled_form()"> <?= csrf() ?> <?= input_hidden('nim', $_SESSION['nim']) ?>
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div class="form-group form-group-default">
                                            <label>Current Password <b class="text-danger">*</b></label>
                                            <input type="password" class="form-control" name="current_password" id="current_password" placeholder="Enter Your Current Password" value="<?= old('current_password') ?>">
                                            <?= form_error('current_password') ?>

                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group form-group-default">
                                            <label>New Password <b class="text-danger">*</b></label>
                                            <input type="password" class="form-control" name="new_password" id="new_password" placeholder="Enter Your New Password" value="<?= old('new_password') ?>">
                                            <?= form_error('new_password') ?>

                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group form-group-default">
                                            <label>New Password Confirmation <b class="text-danger">*</b></label>
                                            <input type="password" class="form-control" name="new_password2" id="new_password2" placeholder="Confirm Your New Password" value="<?= old('new_password2') ?>">
                                            <?= form_error('new_password2') ?>

                                        </div>
                                    </div>



                                </div>
                            </form>

                            <div class="text-left mt-3 mb-3">
                                <?php if ($_SESSION['old']['open-form'] == 'open') : ?>
                                <button onclick="confirmation()" id="cancel_btn" class="btn btn-danger"><i class="fas fa-times mr-1"></i>Cancel</button>
                                <button onclick="enable_form()" id="edit_btn" class="btn btn-warning" hidden><i class="fas fa-edit mr-1"></i>Edit</button>
                                <button onclick="submit_form()" class="btn btn-success" id="save_btn"><i class="fas fa-key mr-1"></i>Change Password</button>
                                <?php else : ?>
                                <button onclick="confirmation()" id="cancel_btn" class="btn btn-danger" hidden><i class="fas fa-times mr-1"></i>Cancel</button>
                                <button onclick="enable_form()" id="edit_btn" class="btn btn-warning"><i class="fas fa-edit mr-1"></i>Edit</button>
                                <button onclick="submit_form()" class="btn btn-success" disabled id="save_btn"><i class="fas fa-key mr-1"></i>Change Password</button>
                                <?php endif; ?>

                            </div>
                        </div>
                        <?php else: ?>
                        <div class="col-md-12">
                            <!-- 404 Page Not Found -->
                            <div id="container">
                                <h1>404 Tab Not Found</h1>
                                <p>Your requsted tab is not found in this server.</p>

                            </div>
                        </div>
                        <?php endif; ?>




                    </div>

                </div>
            </div>


        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php $__env->startPush('footer-scripts'); ?>
<!-- Soloalert -->
<script src='./core/component/soloalert.js'></script>
<?php if( $_SESSION['old']['open-form'] != 'open' || $_SESSION['old']['open-form'] === NULL): ?> :

<script type="text/javascript">
    $(function() {
        disabled_form();
    });

</script>
<?php endif; ?>


<script>
    function submit_form() {
        console.log('helo there');
        $('form#form').submit();
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

        $("#form :input").prop("disabled", true);
        $("#form").trigger("reset");

        $("small").empty();

        $("#form :input").removeClass("is-invalid border-danger is-valid border-success");


    };

    function enable_form() {


        $("#edit_btn").prop("disabled", true);
        $("#edit_btn").prop("hidden", true);


        $("#cancel_btn").prop("hidden", false);
        $("#cancel_btn").prop("disabled", false);


        $("#save_btn").prop("disabled", false);

        $("#form :input").prop("disabled", false);

        $("#form :input").removeClass("is-invalid border-danger is-valid border-success");


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
                    url: "index.php?request=profile_remove&action=student",
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

    function togglePasswordVisibility() {
        var button_show = $('#show_password').is(":checked");
        var inputType = button_show ? 'text' : 'password';

        $('#current_password, #new_password, #new_password2').attr('type', inputType);
    }

</script>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('templates.student', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/student/student_identity.blade.php ENDPATH**/ ?>