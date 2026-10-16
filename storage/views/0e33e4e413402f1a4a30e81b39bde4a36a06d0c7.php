

<?php $__env->startPush('head-scripts'); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>


<div class="container">

    <div class="page-inner">


        <div class="page-header">
            <h4 class="page-title">Add New CDC Account</h4>
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
                    <a href="index.php?page=cdc_management">CDC Account Management</a>
                </li>

                <li class="separator">
                    <i class="flaticon-right-arrow"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Add CDC</a>
                </li>
            </ul>
        </div>


        <div class="row">

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Add CDC Form</h4>
                    </div>

                    <form action="index.php?form=upkpk_management&action=add" method="POST" id="form_add_lecturer"><?= csrf(); ?>
                        <div class="card-body form-row">

                            <div class="form-group col-md-12">
                                <label>Username <b class="text-danger">*</b></label>
                                <input type="text" placeholder="Enter CDC Username" class="form-control <?= show_error('username') ?>" required name="username" id="username" value="<?= old('username') ?>" />

                                <?= form_error('username') ?>

                            </div>


                            <div class="form-group col-md-12">
                                <label>Name <b class="text-danger">*</b></label>
                                <input type="text" placeholder="Enter CDC Name" class="form-control <?= show_error('name') ?>" required 
                                name="name" id="name" value="<?= old('name') ?>"/>
                                <?= form_error('name') ?>

                            </div>

                            <div class="form-group col-md-12">
                                <label>University <b class="text-danger">*</b></label>
                                <!-- <input type="text" placeholder="Enter CDC Name" class="form-control <?= show_error('name') ?>" required 
                                name="name" id="name" value="<?= old('name') ?>"/> -->
                                <select class="form-control" name="id_kampus" id="id_kampus" >
                                    <?php 
                                    $sql = "SELECT * FROM tb_kampus";
                                    $sql = $_SERVER['KONEKSI_DB']->query( $sql );

                                    foreach ($sql as $data) : ?>

                                    <option value="<?= $data['id_kampus'] ?>"><?= $data['nama_kampus'] ?></option>

                                    <?php endforeach; ?>
                                </select>
                                <?= form_error('id_kampus') ?>

                            </div>

                        </div>
                        <div class="card-footer form-row">

                            <div class="form-group col-md-12">

                                <button type="button" class="btn btn-danger" onclick="cancel_confirmation('<?php echo e(base_url('index.php?page=cdc_management')); ?>')"><i class="fas fa-times mr-1"></i>Cancel</button>
                                <button type="button" class="btn btn-warning" onclick="reset_confirmation()"><i class="fas fa-sync mr-1"></i>Reset</button>
                                <button type="submit" class="btn btn-success"><i class="fas fa-plus mr-1"></i>Add</button>

                            </div>
                        </div>
                    </form>


                </div>
            </div>


        </div>


    </div>
</div>


<?php $__env->stopSection(); ?>

<?php $__env->startPush('footer-scripts'); ?>

<!-- Datatables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

<!-- Datatables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

<!-- JS Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>


<!-- Soloalert -->
<script src='./core/component/soloalert.js'></script>


<!-- AJAX Script -->
<script>
    $(document).ready(function() {
        $("#program_study").val("<?php echo e(old('program_study')); ?>");
        $("#program_study").trigger('change');

    });

    function cancel_confirmation(location) {
        // alert('Are you sure you want to cancel ? (Your change will not be saved)');
        SoloAlert.confirm({
            title: "Are you sure you want to cancel ?",
            body: "Changes you made will not be saved !",
            useTransparency: true,
            onOk: () => {
                window.location.href = location;
            }
        });

    }

    function reset_confirmation() {

        SoloAlert.confirm({
            title: "Are you sure you want to reset ?",
            body: "Changes you made will be reset to default !",
            useTransparency: true,
            onOk: () => {
                $("#program_study").trigger('change');
                form_reset();
            }
        });

    }


    function form_reset() {
        $("#form_add_lecturer").trigger("reset");
        $("small").empty();
    };


    $('#id_kampus').select2({
        theme: "bootstrap"
    });
</script>


<?php $__env->stopPush(); ?>

<?php echo $__env->make('templates.cdc', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/cdc/cdc_management/add.blade.php ENDPATH**/ ?>