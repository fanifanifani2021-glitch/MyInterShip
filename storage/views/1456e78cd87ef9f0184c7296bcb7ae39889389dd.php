

<?php $__env->startPush('head-scripts'); ?>
<style>
    /* Modern Card Styling */
    .card-modern {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
        background: #ffffff;
        margin-bottom: 24px;
        overflow: hidden;
    }
    .card-modern .card-header {
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        padding: 16px 20px;
    }
    .card-modern .card-body {
        padding: 20px;
    }
    
    /* Quick Menu Custom Box */
    .quick-menu-item {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 16px 10px;
        text-align: center;
        background: #ffffff;
        transition: all 0.2s ease;
        text-decoration: none !important;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .quick-menu-item:hover {
        border-color: #1572e8;
        background: #f8fafc;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(21, 114, 232, 0.1);
    }
    .quick-menu-item i {
        font-size: 26px;
        color: #1572e8;
        margin-bottom: 8px;
    }
    .quick-menu-item span {
        font-size: 13px;
        font-weight: 600;
        color: #2c3e50;
    }

    /* Alert Item Styling */
    .alert-card-item {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 16px;
        background: #ffffff;
        margin-bottom: 12px;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        text-decoration: none !important;
    }
    .alert-card-item:hover {
        border-color: #1572e8;
        background: #f8fafc;
    }
    .alert-card-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        color: #ffffff;
        margin-right: 14px;
        flex-shrink: 0;
    }

    /* Select2 Styling Adjustment */
    .select2-container--default .select2-selection--single {
        height: 42px !important;
        border: 1px solid #ced4da !important;
        border-radius: 6px !important;
        padding: 6px 12px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px !important;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="container">
    
    
    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row justify-content-between">
                <div>
                    <h2 class="text-white pb-2 fw-bold"><i class="fas fa-chart-line mr-2"></i>Welcome Back, <?= $_SESSION['name'] ?? 'Student' ?>!</h2>
                    <h5 class="text-white op-7 mb-2">MyInternship - Industrial Learning Portal</h5>
                </div>
                <div class="mt-2 mt-md-0">
                    <span class="badge badge-light px-3 py-2 fw-bold text-primary" style="border-radius: 6px; font-size: 12px;">
                        <i class="fas fa-id-badge mr-1"></i> NIM : <?= $_SESSION['nim'] ?? '-' ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    
    <div class="page-inner mt--5">
        
        
        <?php if($upcoming): ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-warning shadow-sm border-left-warning mb-4" role="alert" style="background: #fff8e6; border-left: 5px solid #f2994a; border-radius: 8px;">
                        <div class="d-flex align-items-center">
                            <div class="mr-3">
                                <i class="fas fa-tools fa-2x text-warning"></i>
                            </div>
                            <div class="flex-1">
                                <h5 class="mb-1 fw-bold text-dark">Upcoming System Maintenance</h5>
                                <p class="mb-0 text-dark small">
                                    Please be informed that <strong><?php echo e(htmlspecialchars(is_object($upcoming) ? $upcoming->title : $upcoming['title'])); ?></strong> is scheduled on 
                                    <span class="badge badge-warning text-dark fw-bold">
                                        <?php echo e(date('l, d M Y | H:i', strtotime(is_object($upcoming) ? $upcoming->start_date : $upcoming['start_date']))); ?> WIB
                                    </span>.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row mt--2">
            
            <div class="col-lg-8 col-md-12">
                
                <!-- 01. INTERNSHIP QUICK ACCESS -->
                <div class="card card-modern">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title fw-bold text-primary mb-0">
                            <i class="fas fa-bolt mr-2"></i>Internship Quick Access
                        </h5>
                        <small class="text-muted fw-bold">Today: <?php echo e(date('l, d M Y')); ?></small>
                    </div>

                    <div class="card-body">
                        <div class="form-group p-0 mb-4">
                            <label for="internship-select" class="fw-bold text-dark mb-2">Select Internship Application:</label>
                            <select id="internship-select" class="form-control select2">
                                <option value="">Choose an Internship Record...</option>
                                <?php $selectedSet = false; ?>
                                <?php $__currentLoopData = $internships; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $internship): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $encId    = is_object($internship) ? $internship->encoded_id : $internship['encoded_id'];
                                        $st       = is_object($internship) ? $internship->status : $internship['status'];
                                        $compObj  = is_object($internship) ? $internship->company : null;
                                        $compName = $compObj ? $compObj->name : (is_array($internship) ? ($internship['company_name'] ?? 'Company') : 'Company');
                                    ?>
                                    <option value="<?php echo e($encId); ?>"
                                        <?php if(!$selectedSet && strtolower($st) === 'yes'): ?>
                                            selected
                                            <?php $selectedSet = true; ?>
                                        <?php endif; ?>
                                    >
                                        Status: <?php echo e(strtoupper($st)); ?> | <?php echo e(htmlspecialchars($compName)); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        
                        <?php $__empty_1 = true; $__currentLoopData = $internships; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $internship): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $encId     = is_object($internship) ? $internship->encoded_id : $internship['encoded_id'];
                                $rawId     = is_object($internship) ? $internship->id_internship : $internship['id_internship'];
                                $st        = strtolower(is_object($internship) ? $internship->status : $internship['status']);
                                $att       = $attendanceToday[$rawId] ?? null;

                                $hasIn     = !empty($att['check_in']) && strtotime($att['check_in']);
                                $hasOut    = !empty($att['check_out']) && $att['check_out'] !== '-' && strtotime($att['check_out']);
                                $canIn     = !$hasIn;
                                $canOut    = $hasIn && !$hasOut;

                                $checkInFormatted  = $hasIn ? date('H:i \W\I\B', strtotime($att['check_in'])) : '-';
                                $checkOutFormatted = $hasOut ? date('H:i \W\I\B', strtotime($att['check_out'])) : '-';
                            ?>

                            <div class="internship-container" id="<?php echo e($encId); ?>" style="display:none;">
                                <?php if(in_array($st, ['yes', 'finish'])): ?>
                                    <div class="row align-items-center mb-3">
                                        <div class="col-md-6 mb-2 mb-md-0">
                                            <?php if($canIn): ?>
                                                <button class="btn btn-primary btn-block fw-bold" onclick="location.href='index.php?page=check_in_attendance_internship&id_internship=<?php echo e($encId); ?>'">
                                                    <i class="fas fa-sign-in-alt mr-2"></i> Check In Today
                                                </button>
                                            <?php else: ?>
                                                <div class="badge badge-success px-3 py-2 w-100 text-left" style="font-size: 12px; border-radius: 6px;">
                                                    <i class="fas fa-check-circle mr-1"></i> Checked In: <strong><?php echo e($checkInFormatted); ?></strong>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="col-md-6">
                                            <?php if($canOut): ?>
                                                <button class="btn btn-warning text-white btn-block fw-bold" onclick="location.href='index.php?page=check_out_attendance_internship&id_internship=<?php echo e($encId); ?>&id_attendance=<?php echo e(base64_encode($att['id_attendance'] ?? '')); ?>'">
                                                    <i class="fas fa-sign-out-alt mr-2"></i> Check Out Today
                                                </button>
                                            <?php elseif($hasOut): ?>
                                                <div class="badge badge-info px-3 py-2 w-100 text-left" style="font-size: 12px; border-radius: 6px;">
                                                    <i class="fas fa-check-circle mr-1"></i> Checked Out: <strong><?php echo e($checkOutFormatted); ?></strong>
                                                </div>
                                            <?php else: ?>
                                                <button class="btn btn-secondary btn-block fw-bold" disabled style="border-radius: 6px;">
                                                    <i class="fas fa-lock mr-1"></i> Check Out Pending
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <hr class="my-4">

                                    
                                    <div class="row">
                                        <div class="col-6 col-md-3 mb-3 mb-md-0">
                                            <a href="<?php echo e(router('student.internship.detail', ['id' => $encId])); ?>" class="quick-menu-item">
                                                <i class="fas fa-info-circle"></i>
                                                <span>Detail</span>
                                            </a>
                                        </div>

                                        <div class="col-6 col-md-3 mb-3 mb-md-0">
                                            <a href="index.php?page=logbook_internship&id_internship=<?php echo e($encId); ?>" class="quick-menu-item">
                                                <i class="fas fa-book"></i>
                                                <span>Logbook</span>
                                            </a>
                                        </div>

                                        <div class="col-6 col-md-3">
                                            <a href="index.php?page=attendance_internship&id_internship=<?php echo e($encId); ?>" class="quick-menu-item">
                                                <i class="fas fa-calendar-check"></i>
                                                <span>Attendance</span>
                                            </a>
                                        </div>

                                        <div class="col-6 col-md-3">
                                            <a href="index.php?page=discussion_internship&id_internship=<?php echo e($encId); ?>" class="quick-menu-item">
                                                <i class="fas fa-comments"></i>
                                                <span>Discussion</span>
                                            </a>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning text-center mb-0" style="border-radius: 8px;">
                                        <i class="fas fa-exclamation-circle mr-1"></i> Quick access modules are only available for <strong>Accepted / Active (Ongoing)</strong> internships.
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-briefcase fa-2x mb-2"></i>
                                <p class="mb-0 fw-bold">No Internship Record Found</p>
                                <small>Register for an internship position to get started.</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 02. USER GUIDE & RESOURCES -->
                <div class="card card-modern">
                    <div class="card-header">
                        <h5 class="card-title fw-bold text-primary mb-0">
                            <i class="fas fa-book-reader mr-2"></i>User Guide & Resources
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert-card-item justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <div class="alert-card-icon bg-danger">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0" style="font-size: 14px;">Manual Guide (PDF)</h6>
                                    <small class="text-muted">PDF Document Resource</small>
                                </div>
                            </div>
                            <a href="viewer.php?src=manual_guide" target="_blank" class="btn btn-sm btn-outline-primary fw-bold px-3">
                                <i class="fas fa-external-link-alt mr-1"></i> Open
                            </a>
                        </div>

                        <div class="alert-card-item justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="alert-card-icon bg-info">
                                    <i class="fas fa-play"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0" style="font-size: 14px;">Tutorial Video (MP4)</h6>
                                    <small class="text-muted">Video Walkthrough Resource</small>
                                </div>
                            </div>
                            <a href="https://drive.google.com/drive/folders/1uqbFngXQHvtgfW0sbaMHuCth6Qr4ibjE" target="_blank" class="btn btn-sm btn-outline-primary fw-bold px-3">
                                <i class="fas fa-external-link-alt mr-1"></i> Watch
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="col-lg-4 col-md-12">
                
                <!-- TODAY ALERTS -->
                <div class="card card-modern">
                    <div class="card-header">
                        <h5 class="card-title fw-bold text-primary mb-0">
                            <i class="fas fa-bell text-danger mr-2"></i>Today Alert
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php $__empty_1 = true; $__currentLoopData = $alerts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <a href="<?php echo e($alert['url']); ?>" class="alert-card-item justify-content-between mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="alert-card-icon bg-<?php echo e($alert['type'] ?? 'primary'); ?>">
                                        <i class="fas <?php echo e($alert['icon'] ?? 'fa-info-circle'); ?>"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0" style="font-size: 13px;"><?php echo e($alert['title']); ?></h6>
                                        
                                        
                                        <small class="fw-bold <?php echo e($alert['text_color'] ?? 'text-muted'); ?>" style="font-size: 11px;">
                                            <?php echo e($alert['action_text'] ?? 'Information'); ?>

                                        </small>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-right text-muted small"></i>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                                <h6 class="fw-bold mb-0">All Clear</h6>
                                <small>No pending attendance or action items today.</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- LATEST ANNOUNCEMENTS -->
                <div class="card card-modern">
                    <div class="card-header">
                        <h5 class="card-title fw-bold text-primary mb-0">
                            <i class="fas fa-bullhorn text-info mr-2"></i>Latest Announcement
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php $__empty_1 = true; $__currentLoopData = $announcements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $cat   = is_object($info) ? $info->category : $info['category'];
                                $t     = is_object($info) ? $info->title : $info['title'];
                                $dt    = is_object($info) ? $info->created_at : $info['created_at'];
                                $annId = is_object($info) ? $info->id_announcement : $info['id_announcement'];
                            ?>
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge badge-info px-2 py-1" style="font-size: 10px;"><?php echo e($cat); ?></span>
                                    <small class="text-muted" style="font-size: 11px;"><?php echo e(date('d M Y', strtotime($dt))); ?></small>
                                </div>
                                <h6 class="fw-bold text-dark mb-2" style="font-size: 13px;"><?php echo e($t); ?></h6>
                                <a href="announcement_detail.php?id=<?php echo e(base64_encode($annId)); ?>" class="btn btn-xs btn-outline-info fw-bold">
                                    <i class="fas fa-eye mr-1"></i> Read Detail
                                </a>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-bullhorn fa-2x mb-2"></i>
                                <h6 class="fw-bold mb-0">No Announcement</h6>
                                <small>There is no published announcement yet.</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- MODAL INCOMPLETE PROFILE -->
<div class="modal fade" id="ModalIncomplete" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 10px;">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-exclamation-triangle text-warning mr-2"></i>Profile Identity Incomplete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-dark">Before registering for an internship, please prepare and complete your identity documents:</p>
                <ul class="text-dark small">
                    <li>Updated Talenthub Curriculum Vitae (CV)</li>
                    <li>Official Academic Transcript (PDF)</li>
                </ul>
                <p class="text-muted small mb-0">Completing these details ensures a smooth internship application process.</p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Later</button>
                <button type="button" onclick="window.location.href='<?php echo e(router('student.identity.index')); ?>'" class="btn btn-primary fw-bold">
                    <i class="fas fa-user-edit mr-1"></i> Complete Profile Now
                </button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('footer-scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const select = document.getElementById('internship-select');
    const containers = document.querySelectorAll('.internship-container');

    const showContainer = (id) => {
        containers.forEach(el => {
            el.style.display = (el.id === id) ? 'block' : 'none';
        });
    };

    if (select) {
        select.addEventListener('change', () => {
            if (select.value) {
                showContainer(select.value);
            }
        });

        if (select.value) {
            showContainer(select.value);
        }
    }
});

<?php if(!$student_profile || empty($student_profile->academic_transcript)): ?>
$(document).ready(function() {
    setTimeout(function() {
        $('#ModalIncomplete').modal({
            backdrop: 'static',
            keyboard: false
        }).modal('show');
    }, 1500);
});
<?php endif; ?>
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('templates.student', ['title' => 'Student Dashboard'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/student/dashboard.blade.php ENDPATH**/ ?>