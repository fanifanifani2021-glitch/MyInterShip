
<?php $__env->startPush('head-scripts'); ?>
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.3.2/dist/select2-bootstrap4.min.css"
    rel="stylesheet" />

<style>
    /* 1. SAMAKAN TINGGI & STYLE DASAR */
    .form-control,
    .select2-container--bootstrap4 .select2-selection--single {
        height: 45px !important; 
        min-height: 45px !important;
        font-size: 14px !important;
        border-radius: 4px !important;
        color: #495057 !important;
        border: 1px solid #ebedf2 !important; 
    }

    /* 2. [FIX] ERROR STATE */
    .form-control.is-invalid,
    .select2-container--bootstrap4 .select2-selection--single.is-invalid,
    .is-invalid .select2-selection--single { 
        border-color: #dc3545 !important;
        padding-right: 2.25rem !important; 
    }

    /* 3. FIX FLATPICKR */
    input.form-control.datepicker,
    input.form-control.datepicker:read-only {
        background-color: #ffffff !important; 
        cursor: pointer !important;
        width: 100% !important;
    }

    /* 4. FIX SELECT2 ALIGNMENT */
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        line-height: 43px !important; 
        padding-left: 12px !important;
        color: #495057 !important;
        display: block !important;
        height: 100% !important;
        margin-top: 0 !important;
    }
    .select2-container--bootstrap4 .select2-selection--single {
        padding: 0 !important; 
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
        position: absolute !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        right: 10px !important;
    }
</style>

<style>
    /* === Base Select2 Bootstrap-like styling === */
    .select2-container .select2-selection--single {
        height: calc(2.25rem + 2px);
        padding: 0.375rem 0.75rem;
        font-size: 0.95rem;
        line-height: 1.5;
        color: #495057;
        background-color: #fff;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
    }

    /* Hover & Focus */
    .select2-container--default .select2-selection--single:focus,
    .select2-container--default .select2-selection--single:hover {
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.15rem rgba(0, 123, 255, 0.25);
    }

    /* Text alignment inside select */
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #212529;
        line-height: 2.1rem;
        padding-left: 2px;
    }

    /* Remove small arrow overlap */
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 100%;
        right: 10px;
    }

    /* Dropdown style */
    .select2-dropdown {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    /* Highlighted (hovered) option */
    .select2-results__option--highlighted {
        background-color: #007bff !important;
        color: white !important;
    }

    /* Selected item */
    .select2-results__option[aria-selected=true] {
        background-color: #e9ecef;
        color: #212529;
    }

    /* Placeholder text */
    .select2-selection__placeholder {
        color: #6c757d;
        font-style: italic;
    }
</style>

<script>
    // FUNGSI DI SCOPE GLOBAL (PENTING!)
    function cancel_confirmation(loc) {
        SoloAlert.confirm({
            title: "Cancel Process?",
            body: "Any unsaved changes will be lost.",
            icon: "warning",
            theme: "light",
            useTransparency: true,
            onOk: () => {
                window.location.href = loc;
            }
        });
    }

    function reset_confirmation() {
        SoloAlert.confirm({
            title: "Reset Form?",
            body: "This will clear all input fields.",
            icon: "warning",
            theme: "light",
            useTransparency: true,
            onOk: () => {
                $("#form_add_internship")[0].reset();
                $('.select2-theme').val(null).trigger('change');
            }
        });
    }
</script>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('main-content'); ?>
<div class="container">
    <div class="page-inner">
        
        <div class="page-header">
            <h4 class="page-title">Add New Internship</h4>
            <ul class="breadcrumbs">
                <li class="nav-home"><a href="javascript:void(0)" onclick="cancel_confirmation('<?php echo e(router('home')); ?>')"><i class="flaticon-home"></i></a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="#" onclick="cancel_confirmation('<?php echo e(router('cdc.internship.management')); ?>')">Internship Management</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="#">Add Data</a></li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Internship Registration Form</div>
                    </div>

                    
                    
                    
                    <?php if(isset($_SESSION['old'] ['existingInternship'])): ?>
                        <div class="card-body pb-0 border-bottom bg-light">
                            <?php 
                                $existingInternship = $_SESSION['old'] ['existingInternship'];
                            ?>

                            <div class="alert alert-warning shadow-sm mb-3" role="alert" style="border-left: 5px solid #ffc107;">
                                <div class="d-flex align-items-start">
                                    <div class="icon-big text-warning mr-3"><i class="fas fa-exclamation-triangle fa-2x"></i></div>
                                    <div class="w-100">
                                        <h5 class="alert-heading font-weight-bold mb-1">Duplicate Data Found!</h5>
                                        <p class="mb-2 text-muted small">Student has <b><?php echo e(count($existingInternship)); ?></b> active internship(s). Details:</p>
                                        
                                        
                                        <div class="table-responsive bg-white rounded border p-0 mb-3">
                                            <table class="table table-sm mb-0">
                                                <thead>
                                                    <tr class="bg-light text-muted">
                                                        <th class="px-3">ID</th>
                                                        <th>Company</th>
                                                        <th>Position</th>
                                                        <th>Status</th>
                                                        <th>Duration</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $__currentLoopData = $existingInternship; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td class="px-3 font-weight-bold">#<?php echo e($dup['id_internship']); ?></td>
                                                        <td><?php echo e($dup['company_name'] ?? '-'); ?></td>
                                                        <td><?php echo e($dup['internship_position'] ?? '-'); ?></td>
                                                        <td><span class="badge badge-success"><?php echo e($dup['status']); ?></span></td>
                                                        <td><?php echo e(Date::format($dup['start_date']). " - " .Date::format($dup['end_date']). " (". Date::duration( $dup['start_date'], $dup['end_date'], 'EN' ). ")"); ?></td>

                                                    </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>
                                            </table>
                                        </div>

                                        
                                        <form action="<?php echo e(router('cdc.internship.store')); ?>" method="POST">
                                            <?= csrf() ?>
                                            
                                            
                                            <input type="hidden" name="nim" value="<?php echo e(old('nim') ?? ''); ?>">
                                            <input type="hidden" name="id_company" value="<?php echo e(old('id_company') ?? ''); ?>">
                                            <input type="hidden" name="internship_position" value="<?php echo e(old('internship_position') ?? ''); ?>">
                                            <input type="hidden" name="id_lecturer" value="<?php echo e(old('id_lecturer') ?? ''); ?>">
                                            <input type="hidden" name="start_date" value="<?php echo e(old('start_date') ?? ''); ?>">
                                            <input type="hidden" name="end_date" value="<?php echo e(old('end_date') ?? ''); ?>">
                                            <input type="hidden" name="status" value="Active">
                                            
                                            
                                            <input type="hidden" name="resolve_action" value="Finish">

                                            <div class="p-3 rounded" style="background: rgba(255, 193, 7, 0.2);">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="checkResolveAll" required>
                                                    <label class="custom-control-label font-weight-bold text-dark" for="checkResolveAll">
                                                        Set ALL existing active internships above to <span class="badge badge-success">Finish</span> & Create New
                                                    </label>
                                                </div>
                                                <button type="submit" class="btn btn-warning btn-sm btn-block mt-3 font-weight-bold">
                                                    <i class="fas fa-check-circle mr-1"></i> Confirm & Save New Data
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    
                    
                    
                    <form action="<?php echo e(router('cdc.internship.store')); ?>" method="post" id="form_add_internship">
                        <?= csrf(); ?>
                        <?= input_hidden('status', 'Active'); ?> 

                        <div class="card-body">
                            
                            <?php if(!empty($studentData)): ?>
                                <div class="alert alert-info border-0 shadow-sm mb-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm mr-3">
                                            <span class="avatar-title rounded-circle border border-white bg-white text-info"><i class="fas fa-user"></i></span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 font-weight-bold"><?php echo e($studentData['name']); ?></h6>
                                            <small class="d-block"><?php echo e($studentData['nim']); ?></small>
                                        </div>
                                    </div>
                                    <input type="hidden" name="nim" value="<?php echo e($studentData['nim']); ?>">
                                </div>
                            <?php endif; ?>

                            <div class="form-row">
                                
                                <div class="form-group col-md-12">
                                    <label>Student Name <span class="text-danger">*</span></label>
                                    <?php if(!empty($studentData)): ?>
                                        <input type="text" class="form-control" value="<?php echo e($studentData['nim']); ?> - <?php echo e($studentData['name']); ?>" disabled>
                                    <?php else: ?>
                                        <select class="form-control <?= show_error('nim') ?>" name="nim" id="nim" style="width: 100%;">
                                        <option value="">-- Select Student --</option>

                                            <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($s['nim']); ?>" <?php echo e(old('nim') == $s['nim'] ? 'selected' : ''); ?>>
                                                    <?php echo e($s['nim']); ?> - <?php echo e($s['name']); ?> (<?php echo e($s['program_study']); ?>)
                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <?= form_error('nim') ?>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Company <span class="text-danger">*</span></label>
                                    <select class="form-control select2-theme <?= show_error('id_company') ?>" name="id_company" id="id_company" style="width: 100%;">
                                        <option value="">-- Select Company --</option>

                                        <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($c['id_company']); ?>" <?php echo e(old('id_company') == $c['id_company'] ? 'selected' : ''); ?>>
                                                <?php echo e($c['name']); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?= form_error('id_company') ?>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Internship Position <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?= show_error('internship_position') ?>" 
                                           name="internship_position" value="<?= old('internship_position') ?>" placeholder="e.g. Frontend Developer" />
                                    <?= form_error('internship_position') ?>
                                </div>

                                <div class="form-group col-md-12">
                                    <label>Lecturer for Student</label>
                                    <select class="form-control <?= show_error('id_lecturer') ?>" name="id_lecturer" id="id_lecturer" style="width: 100%;">
                                        <option value="">-- Select Lecturer (Optional) --</option>

                                        <?php $__currentLoopData = $lecturers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($l['nim_nik_unit']); ?>" <?php echo e(old('id_lecturer') == $l['nim_nik_unit'] ? 'selected' : ''); ?>>
                                                <?php echo e($l['name']); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?= form_error('id_lecturer') ?>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Start Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control datepicker <?= show_error('start_date') ?>" name="start_date" value="<?= old('start_date') ?>" placeholder="Select Start Date" />
                                    <?= form_error('start_date') ?>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>End Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control datepicker <?= show_error('end_date') ?>" name="end_date" value="<?= old('end_date') ?>" placeholder="Select End Date" />
                                    <?= form_error('end_date') ?>
                                </div>
                            </div>
                        </div>

                        
                        <?php if(!isset($_SESSION['old'] ['existingInternship'])): ?>

                        <div class="card-footer">
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-danger mr-2" onclick="cancel_confirmation('<?php echo e(router('cdc.internship.management')); ?>')">
                                    <i class="fas fa-times mr-1"></i> Cancel
                                </button>
                                <button type="button" class="btn btn-warning mr-2" onclick="reset_confirmation()">
                                    <i class="fas fa-sync mr-1"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-success" id="submit_btn">
                                    <i class="fas fa-save mr-1"></i> Save Data
                                </button>
                            </div>
                        </div>
                        <?php endif; ?>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('footer-scripts'); ?>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="./core/component/soloalert.js"></script>

<script>
/* =====================================================
 * GLOBAL FUNCTIONS
 * ===================================================== */

function cancel_confirmation(loc) {
    SoloAlert.confirm({
        title: "Cancel Process?",
        body: "Any unsaved changes will be lost.",
        icon: "warning",
        theme: "light",
        useTransparency: true,
        onOk: () => window.location.href = loc
    });
}

function reset_confirmation() {
    SoloAlert.confirm({
        title: "Reset Form?",
        body: "This will clear all input fields.",
        icon: "warning",
        theme: "light",
        useTransparency: true,
        onOk: () => {
            $("#form_add_internship")[0].reset();

            // Reset Select2 UI
            $('#nim').val(null).trigger('change');
            $('#id_company').val(null).trigger('change');
            $('#id_lecturer').val(null).trigger('change');
        }
    });
}

/* =====================================================
 * DOCUMENT READY
 * ===================================================== */
$(document).ready(function () {

    /* ================== SELECT2 INIT ================== */
    let student_nim = $('#nim').select2({
        theme: "bootstrap4",
        placeholder: "-- Select Student --",
        allowClear: true,
        width: '100%'
    });

    let company = $('#id_company').select2({
        theme: "bootstrap4",
        placeholder: "-- Select Company --",
        allowClear: true,
        width: '100%'
    });

    let supervisor = $('#id_lecturer').select2({
        theme: "bootstrap4",
        placeholder: "-- Select Supervisor --",
        allowClear: true,
        width: '100%'
    });

    student_nim.trigger("change");
    supervisor.trigger("change");
    company.trigger("change");

});
</script>

<?php $__env->stopPush(); ?>
<?php echo $__env->make('templates.cdc', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/cdc/internship_management/add.blade.php ENDPATH**/ ?>