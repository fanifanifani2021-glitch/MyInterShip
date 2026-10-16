

<?php
    $companyObj   = $internship->company;
    $companyName  = is_object($companyObj) ? $companyObj->name : '-';
    $companyLogo  = is_object($companyObj) ? $companyObj->getLogo() : null;
    $durationText = $internship->getDuration('en');
    $dateRange    = $internship->getDateRange('en');
    $appliedDate  = $internship->getRegisteredDate('en');
    $position     = !empty($internship->internship_position) ? $internship->internship_position : 'No Position Set';

    // File Dokumen Pendaftaran (Fallback dari file_1 / file_2 atau profil)
    $cvLink         = !empty($internship->file_1) ? $internship->file_1 : ($student_profile->talenthub_cv ?? '#');
    $transcriptLink = !empty($internship->file_2) ? $internship->file_2 : ($student_profile->academic_transcript ?? '#');
?>

<?php $__env->startPush('head-scripts'); ?>
<style>
    .page-inner { background: #f8fafc; min-height: 100vh; padding: 30px 20px !important; }

    /* Sharp & Clean Card Styling */
    .card, .company-banner, .report-card, .side-card, .summary-card, .progress-tracker-card { 
        border-radius: 12px !important; 
        border: 1px solid #e2e8f0; 
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        background: #ffffff;
    }

    /* Banner Perusahaan */
    .company-banner { 
        padding: 24px; 
        display: flex; 
        align-items: center; 
        gap: 20px; 
    }
    .company-logo-box { 
        width: 70px; 
        height: 70px; 
        background: #f1f5f9; 
        border-radius: 10px !important; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        overflow: hidden; 
        border: 1px solid #e2e8f0;
    }
    .company-logo-box img { width: 100%; height: 100%; object-fit: cover; }

    /* Summary Stats */
    .summary-card { padding: 18px; text-align: center; }
    .summary-label { 
        font-size: 0.72rem; font-weight: 800; color: #64748b; 
        text-transform: uppercase; margin-bottom: 6px; display: block; letter-spacing: 0.5px;
    }
    .summary-value { font-weight: 700; color: #1e293b; font-size: 0.95rem; }

    /* Reporting Grid 2x2 */
    .reporting-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
    .report-card { 
        padding: 20px 20px 0 20px; transition: all 0.25s ease; 
        min-height: 180px; display: flex; flex-direction: column; text-decoration: none !important; 
    }
    .report-card:hover { transform: translateY(-4px); border-color: #005b9f; box-shadow: 0 8px 20px rgba(0,0,0,0.06); }
    
    .icon-wrapper { 
        width: 42px; height: 42px; border-radius: 8px !important; display: flex; 
        align-items: center; justify-content: center; margin-bottom: 14px; font-size: 1.1rem; 
    }

    /* Soft Color Palette */
    .bg-soft-blue { background: #e0f2fe; color: #0284c7; }
    .bg-soft-green { background: #dcfce7; color: #16a34a; }
    .bg-soft-purple { background: #f3e8ff; color: #9333ea; }
    .bg-soft-orange { background: #ffedd5; color: #ea580c; }
    .bg-soft-teal { background: #ccfbf1; color: #0d9488; }

    .card-footer-info { 
        margin-top: auto; padding: 12px 0; display: flex; 
        justify-content: space-between; align-items: center; 
        border-top: 1px dashed #e2e8f0;
    }

    /* Sidebar Supervision & Identity */
    .side-card { padding: 22px; margin-bottom: 20px; }
    .side-title {
        font-size: 0.72rem; font-weight: 800; text-transform: uppercase; 
        color: #64748b; margin-bottom: 18px; letter-spacing: 0.8px;
    }
    .profile-item { 
        display: flex; align-items: center; gap: 14px; margin-bottom: 18px; 
        padding-bottom: 14px; border-bottom: 1px solid #f1f5f9; 
    }
    .profile-item:last-child { margin-bottom: 0; padding-bottom: 0; border-bottom: none; }

    /* Shortcut Identity Box */
    .identity-shortcut-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px;
        transition: all 0.2s ease;
    }
    .identity-shortcut-box:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }

    @media (max-width: 991px) {
        .col-lg-8 { margin-bottom: 24px; }
        .reporting-grid { grid-template-columns: 1fr; gap: 16px; }
        .company-banner { flex-direction: column; text-align: center; }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="container">
    <div class="page-inner">
        
        
        <div class="page-header mb-4">
            <h4 class="page-title fw-bold text-dark">Internship Detail</h4>
            <ul class="breadcrumbs">
                <li class="nav-home"><a href="<?php echo e(base_url()); ?>"><i class="flaticon-home text-primary"></i></a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="<?php echo e(router('student.internship')); ?>">My Internship</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a class="text-muted">Detail Application</a></li>
            </ul>
        </div>

        <div class="row">
            
            <div class="col-lg-8">
                
                
                <div class="company-banner mb-4">
                    <div class="company-logo-box">
                        <?php if($companyLogo): ?>
                            <img src="<?php echo e($companyLogo); ?>" alt="<?php echo e($companyName); ?>">
                        <?php else: ?>
                            <i class="fas fa-building text-muted fa-2x"></i>
                        <?php endif; ?>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="fw-bold text-dark mb-1"><?php echo e($companyName); ?></h4>
                        <div class="d-flex align-items-center flex-wrap">
                            <span class="text-muted small mr-3"><i class="fas fa-briefcase mr-1 text-primary"></i> Position: <strong><?php echo e($position); ?></strong></span>
                        </div>
                    </div>
                    <span class="badge <?php echo e($status['class']); ?> px-3 py-2 mt-2 mt-md-0 fw-bold" style="border-radius: 6px; font-size: 12px;">
                        <?php echo e($status['label']); ?>

                    </span>
                </div>

                
                <div class="row mb-4">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="summary-card">
                            <span class="summary-label"><i class="far fa-calendar-alt mr-1"></i> Applied Date</span>
                            <span class="summary-value"><?php echo e($appliedDate); ?></span>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="summary-card">
                            <span class="summary-label"><i class="far fa-clock mr-1"></i> Internship Period</span>
                            <span class="summary-value"><?php echo e($dateRange); ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="summary-card">
                            <span class="summary-label"><i class="fas fa-hourglass-half mr-1"></i> Total Duration</span>
                            <span class="summary-value text-primary"><?php echo e($durationText); ?></span>
                        </div>
                    </div>
                </div>

                
                <?php if(($status['accepted'] ?? 0) == 1): ?>
                    <h5 class="fw-bold text-dark mb-3 mt-2"><i class="fas fa-tasks mr-2 text-primary"></i> Daily Progress & Reporting Modules</h5>
                    
                    <div class="reporting-grid">
                        
                        <a href="<?php echo e(router('student.logbook.index', ['id' => $id_enc])); ?>" class="report-card">
                            <div class="icon-wrapper bg-soft-blue"><i class="fas fa-book"></i></div>
                            <div class="card-title fw-bold text-dark" style="font-size: 1.05rem;">Logbook</div>
                            <p class="card-text text-muted small">Document your daily technical tasks and activities.</p>
                            <div class="card-footer-info"><span class="fw-bold text-primary small">Open Module</span><i class="fas fa-chevron-right text-muted small"></i></div>
                        </a>

                        
                        <a href="<?php echo e(router('student.attendance.index', ['id' => $id_enc])); ?>" class="report-card">
                            <div class="icon-wrapper bg-soft-green"><i class="fas fa-calendar-check"></i></div>
                            <div class="card-title fw-bold text-dark" style="font-size: 1.05rem;">Attendance</div>
                            <p class="card-text text-muted small">Record your check-in, presence, and daily hours.</p>
                            <div class="card-footer-info"><span class="fw-bold text-success small">Daily Tracking</span><i class="fas fa-chevron-right text-muted small"></i></div>
                        </a>

                        
                        <a href="<?php echo e(router('student.discussion.index', ['id' => $id_enc])); ?>" class="report-card">
                            <div class="icon-wrapper bg-soft-purple"><i class="fas fa-comments"></i></div>
                            <div class="card-title fw-bold text-dark" style="font-size: 1.05rem;">Discussion</div>
                            <p class="card-text text-muted small">Coordinate with your supervisors and lecturers.</p>
                            <div class="card-footer-info"><span class="fw-bold text-purple small">Consultation</span><i class="fas fa-chevron-right text-muted small"></i></div>
                        </a>

                        
                        <a href="<?php echo e(router('student.final_report.index', ['id' => $id_enc])); ?>" class="report-card">
                            <div class="icon-wrapper bg-soft-orange"><i class="fas fa-file-contract"></i></div>
                            <div class="card-title fw-bold text-dark" style="font-size: 1.05rem;">Final Report</div>
                            <p class="card-text text-muted small">Submit your final internship documentation.</p>
                            <div class="card-footer-info"><span class="fw-bold text-warning small">Final Stage</span><i class="fas fa-chevron-right text-muted small"></i></div>
                        </a>

                        
                        <a href="<?php echo e(router('student.feedback.index', ['id' => $id_enc])); ?>" class="report-card">
                            <div class="icon-wrapper bg-soft-teal"><i class="fas fa-comment-dots"></i></div>
                            <div class="card-title fw-bold text-dark" style="font-size: 1.05rem;">Student Feedback</div>
                            <p class="card-text text-muted small">Share your experience and evaluate the program.</p>
                            <div class="card-footer-info">
                                <span class="fw-bold text-teal small">Self Reflection</span>
                                <i class="fas fa-chevron-right text-muted small"></i>
                            </div>
                        </a>

                        
                        <a href="<?php echo e(router('student.industry-feedback.index', ['id' => $id_enc])); ?>" class="report-card">
                            <div class="icon-wrapper bg-soft-purple"><i class="fas fa-user-check"></i></div>
                            <div class="card-title fw-bold text-dark" style="font-size: 1.05rem;">Industry Feedback</div>
                            <p class="card-text text-muted small">View assessment from your company supervisor.</p>
                            <div class="card-footer-info">
                                <span class="fw-bold text-purple small">Company Assessment</span>
                                <i class="fas fa-chevron-right text-muted small"></i>
                            </div>
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            
            <div class="col-lg-4">
                
                
                <div class="side-card">
                    <div class="side-title"><i class="fas fa-users-cog mr-1"></i> Supervision Team</div>
                    
                    
                    <div class="profile-item">
                        <div class="avatar avatar-sm">
                            <img src="https://ui-avatars.com/api/?name=<?php echo e(urlencode($supervisor ?? 'Supervisor')); ?>&background=0284c7&color=fff" class="avatar-img rounded-circle">
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="fw-bold text-dark mb-0"><?php echo e($supervisor ?? 'Waiting Assignment'); ?></h6>
                            <small class="text-muted d-block">Industry Supervisor</small>
                        </div>
                        <span class="badge <?php echo e($supervisor ? 'badge-success' : 'badge-light text-muted border'); ?>" style="font-size: 10px;">
                            <?php echo e($supervisor ? 'Assigned' : 'Pending'); ?>

                        </span>
                    </div>

                    
                    <div class="profile-item">
                        <div class="avatar avatar-sm">
                            <span class="avatar-title rounded-circle bg-light text-primary border"><i class="fas fa-user-tie"></i></span>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="fw-bold text-dark mb-0"><?php echo e($dospem ?? 'Not assigned'); ?></h6>
                            <small class="text-muted d-block">University Lecturer</small>
                        </div>
                        <span class="badge <?php echo e($dospem ? 'badge-success' : 'badge-light text-muted border'); ?>" style="font-size: 10px;">
                            <?php echo e($dospem ? 'Assigned' : 'Pending'); ?>

                        </span>
                    </div>
                </div>

                
                <div class="side-card">
                    <div class="side-title"><i class="fas fa-id-card mr-1"></i> Submitted Student Data</div>
                    <p class="text-muted small mb-3">Review your profile identity, Talenthub CV, and academic transcript used for this registration.</p>
                    
                    <a href="<?php echo e(router('student.identity.index')); ?>" class="identity-shortcut-box d-flex align-items-center justify-content-between text-decoration-none">
                        <div class="d-flex align-items-center">
                            <div class="badge badge-primary rounded-circle p-2 mr-3">
                                <i class="fas fa-user-edit"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0" style="font-size: 13px;">Student Identity & CV</h6>
                                <small class="text-muted" style="font-size: 11px;">View Registered Documents</small>
                            </div>
                        </div>
                        <i class="fas fa-arrow-right text-primary"></i>
                    </a>
                </div>

                
                <div class="side-card">
                    <div class="side-title"><i class="fas fa-folder mr-1"></i> Administration & Resources</div>
                    <div class="list-group list-group-flush">
                        <a href="<?php echo e(router('student.identity.index')); ?>" class="list-group-item list-group-item-action border-0 px-0 py-2 d-flex justify-content-between align-items-center">
                            <span class="small text-dark fw-bold"><i class="fas fa-file-invoice mr-2 text-primary"></i> My Profile Documents</span>
                            <span class="badge badge-light border text-primary" style="font-size: 10px;">Review</span>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action border-0 px-0 py-2 d-flex justify-content-between align-items-center">
                            <span class="small text-dark"><i class="fas fa-info-circle mr-2 text-muted"></i> Internship Guidelines</span>
                            <i class="fas fa-external-link-alt text-muted" style="font-size: 10px;"></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('templates.student', ['title' => 'Internship Detail'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp_new\htdocs\pblKita\views/student/my_internship/detail.blade.php ENDPATH**/ ?>