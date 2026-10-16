

<?php $__env->startPush('head-scripts'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet">
<style>
    /* Custom Styling for Modern Registration Form */
    .step-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }
    .step-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 16px 24px;
    }
    .step-badge {
        background: #005b9f;
        color: #ffffff;
        font-size: 12px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .readiness-box {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 10px;
        padding: 16px;
    }
    .ongoing-banner {
        background: #e0f2fe;
        border: 1px solid #bae6fd;
        border-radius: 10px;
        padding: 18px;
    }
    .waiting-banner {
        background: #fef3c7;
        border: 1px solid #fde68a;
        border-radius: 10px;
        padding: 18px;
    }
    .doc-preview-card {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 16px;
        background: #ffffff;
        transition: all 0.2s ease;
    }
    .doc-preview-card:hover {
        border-color: #005b9f;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }
    .doc-icon-box {
        width: 42px;
        height: 42px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    .select2-container--default .select2-selection--single {
        height: 45px !important;
        border: 1px solid #ced4da !important;
        border-radius: 6px !important;
        padding: 8px 12px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px !important;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>
<?php
    $talenthubCv   = is_object($student_profile) ? ($student_profile->talenthub_cv ?? '') : ($student_profile['talenthub_cv'] ?? '');
    $transcriptDoc = is_object($student_profile) ? ($student_profile->academic_transcript ?? '') : ($student_profile['academic_transcript'] ?? '');
    $studentNim    = is_object($student_data) ? ($student_data->nim ?? '') : ($student_data['nim'] ?? '');
    $studentName   = is_object($student_data) ? ($student_data->name ?? '') : ($student_data['name'] ?? '');
    
    // 🔒 DISABLE FORM JIKA MAGANG BERJALAN ATAU MASIH WAITING
    $isFormDisabled = (!empty($ongoing_internship) || !empty($waiting_internship));

    // AMBIL NAMA PERUSAHAAN UNTUK BANNER ONGOING
    $ongoingCompanyName = '-';
    if (!empty($ongoing_internship)) {
        $ongoingCompanyName = is_object($ongoing_internship->company) ? ($ongoing_internship->company->name ?? '-') : '-';
    }

    // AMBIL NAMA PERUSAHAAN UNTUK BANNER WAITING
    $waitingCompanyName = '-';
    if (!empty($waiting_internship)) {
        $waitingCompanyName = is_object($waiting_internship->company) ? ($waiting_internship->company->name ?? '-') : '-';
    }
?>

<div class="container">
    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row justify-content-between">
                <div>
                    <h2 class="text-white pb-2 fw-bold"><i class="fab fa-wpforms mr-2"></i>Register Internship (Cooperation)</h2>
                    <h5 class="text-white op-7 mb-2">Apply for a registered partner company internship position</h5>
                </div>
                <div class="mt-3 mt-md-0">
                    <a href="<?php echo e(router('student.internship')); ?>" class="btn btn-light btn-round btn-sm fw-bold">
                        <i class="fas fa-arrow-left mr-1"></i> Back to My Internship
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-inner mt--5">
        <div class="row mt--2">
            <div class="col-md-12">

                
                <?php if(!empty($ongoing_internship)): ?>
                    <div class="ongoing-banner mb-4 shadow-sm">
                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                            <div class="d-flex align-items-center mb-2 mb-md-0">
                                <div class="badge badge-info rounded-circle p-2 mr-3">
                                    <i class="fas fa-briefcase fa-2x"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-primary mb-1">
                                        Active / Ongoing Internship Detected!
                                    </h6>
                                    <p class="text-dark small mb-0">
                                        You are currently completing an active internship at 
                                        <strong><?php echo e($ongoingCompanyName); ?></strong> 
                                        until <strong><?php echo e(date('d M Y', strtotime($ongoing_internship->end_date))); ?></strong> 
                                        (<?php echo e($ongoing_internship->getDateRange('en')); ?>). Registration is locked.
                                    </p>
                                </div>
                            </div>
                            <span class="badge badge-info px-3 py-2 fw-bold"><i class="fas fa-lock mr-1"></i> Form Locked</span>
                        </div>
                    </div>
                <?php endif; ?>

                
                <?php if(!empty($waiting_internship)): ?>
                    <div class="waiting-banner mb-4 shadow-sm">
                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                            <div class="d-flex align-items-center mb-2 mb-md-0">
                                <div class="badge badge-warning text-white rounded-circle p-2 mr-3">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-warning-d dark mb-1" style="color: #b45309;">
                                        Pending Application In Progress!
                                    </h6>
                                    <p class="text-dark small mb-0">
                                        You have a pending application at 
                                        <strong><?php echo e($waitingCompanyName); ?></strong>. 
                                        You can only apply to <strong>one company at a time</strong>. Form is currently locked.
                                    </p>
                                </div>
                            </div>
                            <span class="badge badge-warning text-white px-3 py-2 fw-bold"><i class="fas fa-hourglass-half mr-1"></i> Form Locked</span>
                        </div>
                    </div>
                <?php endif; ?>

                
                <div class="readiness-box mb-4 shadow-sm d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="badge badge-success rounded-circle p-2 mr-3">
                            <i class="fas fa-user-check fa-2x"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-success mb-1">Student Profile & Documents Verified</h6>
                            <p class="text-muted small mb-0">Registered Student: <strong><?php echo e($studentName); ?> (<?php echo e($studentNim); ?>)</strong>. All required profile documents are attached automatically.</p>
                        </div>
                    </div>
                    <span class="badge badge-success px-3 py-2 fw-bold"><i class="fas fa-check-circle mr-1"></i> Profile Ready</span>
                </div>

                
                <form action="<?php echo e(router('student.register_internship.cooperation_store')); ?>" method="POST" enctype="multipart/form-data" id="form_register">
                    <?= csrf(); ?>
                    <input type="hidden" name="nim" value="<?php echo e($studentNim); ?>" />

                    <!-- STEP 01. COMPANY SELECTION -->
                    <div class="card step-card mb-4">
                        <div class="step-header d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <span class="step-badge mr-2">Step 01</span>
                                <h5 class="fw-bold text-dark mb-0">Select Target Company</h5>
                            </div>
                            <small class="text-muted">Choose from registered partner companies</small>
                        </div>
                        <div class="card-body p-4">
                            <div class="form-group p-0">
                                <label class="fw-bold text-dark mb-2">Partner Company Name <span class="text-danger">*</span></label>
                                <select class="form-select select2 <?= show_error('id_company'); ?>" name="id_company" id="company_name" required <?php echo e($isFormDisabled ? 'disabled' : ''); ?>>
                                    <option value="">Search and select partner company...</option>
                                    <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $compId   = is_object($row) ? $row->id_company : $row['id_company'];
                                            $compName = is_object($row) ? $row->name : $row['name'];
                                        ?>
                                        <option value="<?php echo e($compId); ?>" <?php echo e(($compId == old('id_company')) ? 'selected' : ''); ?>>
                                            <?php echo e(htmlspecialchars($compName)); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?= form_error('id_company'); ?>
                                <small class="text-muted d-block mt-2">Can't find your target company? Please contact your Internship Coordinator.</small>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 02. ADDITIONAL INFORMATION & DOCUMENTS -->
                    <div class="card step-card mb-4">
                        <div class="step-header d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <span class="step-badge mr-2">Step 02</span>
                                <h5 class="fw-bold text-dark mb-0">Internship Period & Attached Documents</h5>
                            </div>
                            <small class="text-muted">Expected dates & profile documents</small>
                        </div>
                        <div class="card-body p-4">
                            
                            
                            <div class="row mb-4">
                                <div class="form-group col-md-6 mb-3 mb-md-0">
                                    <label class="fw-bold text-dark">Expected Start Date <span class="text-danger">*</span></label>
                                    <input type="date" name="expected_start" value="<?= old('expected_start'); ?>" class="form-control <?= show_error('expected_start'); ?>" required <?php echo e($isFormDisabled ? 'disabled' : ''); ?> />
                                    <?= form_error('expected_start'); ?>
                                </div>

                                <div class="form-group col-md-6">
                                    <label class="fw-bold text-dark">Expected End Date <span class="text-danger">*</span></label>
                                    <input type="date" name="expected_end" value="<?= old('expected_end'); ?>" class="form-control <?= show_error('expected_end'); ?>" required <?php echo e($isFormDisabled ? 'disabled' : ''); ?> />
                                    <?= form_error('expected_end'); ?>
                                </div>
                            </div>

                            <hr class="my-4">

                            <h6 class="fw-bold text-primary mb-3"><i class="fas fa-folder-open mr-1"></i> System Auto-Attached Documents</h6>

                            <div class="row">
                                
                                <div class="col-md-6 mb-3">
                                    <div class="doc-preview-card d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="doc-icon-box bg-light-primary text-primary mr-3">
                                                <i class="fas fa-globe"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold text-dark mb-0">Talenthub CV</h6>
                                                <span class="badge badge-success px-2 py-1 mt-1" style="font-size: 10px;">Ready from Profile</span>
                                            </div>
                                        </div>
                                        <a target="_blank" href="<?php echo e(!empty($talenthubCv) ? $talenthubCv : '#'); ?>" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-external-link-alt mr-1"></i> Visit
                                        </a>
                                    </div>
                                    <input type="hidden" name="attachment_cv" value="<?php echo e($talenthubCv); ?>" />
                                    <?= form_error('attachment_cv'); ?>
                                </div>

                                
                                <div class="col-md-6 mb-3">
                                    <div class="doc-preview-card d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="doc-icon-box bg-light-danger text-danger mr-3">
                                                <i class="fas fa-file-pdf"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold text-dark mb-0">Academic Transcript</h6>
                                                <span class="badge badge-success px-2 py-1 mt-1" style="font-size: 10px;">Ready from Profile</span>
                                            </div>
                                        </div>
                                        <a target="_blank" href="<?php echo e(!empty($transcriptDoc) ? $transcriptDoc : '#'); ?>" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye mr-1"></i> View
                                        </a>
                                    </div>
                                    <input type="hidden" name="attachment_transkrip" value="<?php echo e($transcriptDoc); ?>" />
                                    <?= form_error('attachment_transkrip'); ?>
                                </div>
                            </div>

                        </div>

                        <div class="card-footer bg-light d-flex justify-content-between align-items-center p-3">
                            <a href="<?php echo e(router('student.internship')); ?>" class="btn btn-outline-danger fw-bold px-4">
                                <i class="fas fa-times mr-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success btn-round fw-bold px-4" <?php echo e($isFormDisabled ? 'disabled' : ''); ?>>
                                <i class="fab fa-wpforms mr-1"></i> Submit Registration
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('footer-scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: 'Search and select partner company...',
            width: '100%'
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('templates.student', ['title' => 'Register Internship'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/student/register_internship/cooperation.blade.php ENDPATH**/ ?>