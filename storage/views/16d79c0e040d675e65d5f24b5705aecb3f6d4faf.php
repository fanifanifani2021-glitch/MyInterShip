

<?php $__env->startSection('main-content'); ?>
<style>
    /* Custom Styling Core */
    .info-table th { width: 30%; background: #f9f9f9; font-size: 13px; color: #555; }
    .info-table td { font-weight: 600; color: #2a2b3d; }
    .btn-action-custom { border-radius: 8px !important; font-weight: 600; font-size: 12px; margin-bottom: 10px; text-align: left; display: flex; align-items: center; }
    .btn-action-custom i { width: 20px; }
    
        /* Student Info Header Card */
    .student-header-card {
        background: #fff;
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        margin-bottom: 25px;
    }
    .student-avatar {
        width: 55px; height: 55px;
        background: #1572e8;
        color: #fff;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px; font-weight: bold;
    }

    /* Stepper Styling */
    .stepper-wrapper { display: flex; justify-content: space-between; margin-top: 20px; margin-bottom: 20px; }
    .stepper-item { position: relative; display: flex; flex-direction: column; align-items: center; flex: 1; }
    .stepper-item::before { position: absolute; content: ""; border-bottom: 2px solid #ebedf2; width: 100%; top: 20px; left: -50%; z-index: 2; }
    .stepper-item::after { position: absolute; content: ""; border-bottom: 2px solid #ebedf2; width: 100%; top: 20px; left: 50%; z-index: 2; }
    .stepper-item .step-counter { position: relative; z-index: 5; display: flex; justify-content: center; align-items: center; width: 40px; height: 40px; border-radius: 50%; background: #ebedf2; margin-bottom: 6px; font-weight: bold; color: #8d9498; transition: 0.3s; }
    .stepper-item.completed .step-counter { background-color: #31ce36; color: white; }
    .stepper-item.completed::after { border-bottom-color: #31ce36; }
    .stepper-item.active .step-counter { background-color: #1572e8; color: white; box-shadow: 0 0 0 4px rgba(21, 114, 232, 0.2); }
    .stepper-item.rejected .step-counter { background-color: #f25961; color: white; }
    .stepper-item:first-child::before, .stepper-item:last-child::after { content: none; }
    .step-name { color: #8d9498; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
    .stepper-item.active .step-name { color: #1572e8; }

    /* Letter Detail Card Styling */
    .letter-card { border-radius: 12px; overflow: hidden; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    .letter-header { background: #f8f9fa; border-bottom: 1px solid #eee; padding: 15px 20px; }
    .history-timeline { position: relative; padding-left: 20px; border-left: 2px solid #e9ecef; margin-left: 10px; }
    .history-item { position: relative; margin-bottom: 15px; }
    .history-item::before { content: ""; position: absolute; left: -26px; top: 4px; width: 10px; height: 10px; border-radius: 50%; background: #1572e8; border: 2px solid white; }
    .doc-viewer-container { border: 1px solid #eee; border-radius: 8px; overflow: hidden; background: #525659; }
</style>

<div class="container">
    <div class="page-inner">
        
        <div class="page-header mb-4">
            <h4 class="page-title">Internship Detail</h4>
            <ul class="breadcrumbs">
                <li class="nav-home"><a href="<?php echo e(router('dashboard')); ?>"><i class="flaticon-home"></i></a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="<?php echo e(router('lecturer/my/student.index')); ?>">My Supervised Student</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="<?php echo e(router('lecturer/my/student.detail', ['nim' => $internship['nim']])); ?>">Detail</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><b>Internship Detail</b></li>
            </ul>
        </div>


                
        <div class="student-header-card">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-auto">
                        
                        <div class="student-avatar shadow-sm"><?php echo e(substr($student->name, 0, 1)); ?></div>
                    </div>
                    <div class="col-md-2 border-right">
                        <div class="info-label">Student Name</div>
                        <div class="info-value"><?php echo e($student['name']); ?></div>
                        <div class="text-muted small fw-bold"><?php echo e($student['nim']); ?></div>
                    </div>
                    <div class="col-md-2 border-right">
                        <div class="info-label">Official Email</div>
                        <div class="info-value text-primary"><?php echo e($student->email ?? 'N/A'); ?></div>
                    </div>

                    <div class="col-md-2 border-right">
                        <div class="info-label">Whatsapp Number</div>
                        <div class="info-value text-primary"><?php echo e($student->no_whatsapp ?? 'N/A'); ?></div>
                    </div>
                    <div class="col-md-3 border-right">
                        <div class="info-label">Study Program</div>
                        <div class="info-value"><?php echo e($student->programStudy->getFullName('en') ?? 'N/A'); ?></div>
                    </div>
                </div>
            </div>
        </div>

        
        <?php if(!empty($internship['id_internship_letter'])): ?>
        <div class="alert alert-info d-flex align-items-center mb-4 border-0 shadow-sm" style="border-radius: 8px; background: linear-gradient(to right, #e3f2fd, #ffffff);">
            <i class="fas fa-info-circle mr-3 text-info" style="font-size: 24px;"></i>
            <div>
                <span class="d-block fw-bold text-info">Application Source: Formal Letter</span>
                <small class="text-muted">This internship was initiated through an official request letter #<?php echo e($letter['full_letter_number'] ?: $internship['id_internship_letter']); ?>.</small>
            </div>
        </div>
        <?php endif; ?>

        
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-body py-4">
                <div class="stepper-wrapper">
                    <div class="stepper-item completed">
                        <div class="step-counter"><i class="fas fa-paper-plane"></i></div>
                        <div class="step-name">Applying</div>
                    </div>
                    <div class="stepper-item <?php echo e(in_array($internship['status'], ['Yes', 'Finish', 'No']) ? 'completed' : 'active'); ?>">
                        <div class="step-counter">
                            <?php if(in_array($internship['status'], ['Yes', 'Finish', 'No'])): ?> <i class="fas fa-check"></i> <?php else: ?> <i class="fas fa-hourglass-half"></i> <?php endif; ?>
                        </div>
                        <div class="step-name">Validation</div>
                    </div>
                    <?php if($internship['status'] == 'No'): ?>
                    <div class="stepper-item rejected">
                        <div class="step-counter"><i class="fas fa-times"></i></div>
                        <div class="step-name">Rejected</div>
                    </div>
                    <?php else: ?>
                    <div class="stepper-item <?php echo e(($internship['status'] == 'Yes') ? 'active' : ($internship['status'] == 'Finish' ? 'completed' : '')); ?>">
                        <div class="step-counter">
                            <?php if($internship['status'] == 'Finish'): ?> <i class="fas fa-flag-checkered"></i> <?php else: ?> <i class="fas fa-check-double"></i> <?php endif; ?>
                        </div>
                        <div class="step-name"><?php echo e(($internship['status'] == 'Finish') ? 'Finished' : 'Ongoing'); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="row">
            
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-white border-bottom-0 py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-list-alt mr-2 text-primary"></i> Internship Summary</h6>
                        
                        <a href="<?php echo e(router('lecturer/my/student.internship.edit', ['nim' => $nim, 'id_internship' => $internship['id_internship']])); ?>" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-edit mr-1"></i> Manage Record
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table info-table table-bordered">
                                <tr>
                                    <th>Registered Date</th>
                                    <td><?php echo e(date('D, d M Y', strtotime($internship['timestamp_register']))); ?></td>
                                </tr>
                                <tr>
                                    <th>Target Company</th>
                                    <td>
                                        <a target="_blank" href="<?php echo e(router('lecturer.company.detail', ['id' => Encrypter::encrypt($internship->company->id_company)])); ?>" class="fw-bold">
                                            <?php echo e($internship->company->name); ?> <i class="fas fa-link ml-1 small"></i>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Current Status</th>
                                    <td><span class="badge badge-<?php echo e($internship->getStatusColor()); ?> px-3 py-2"><?php echo e($internship->getStatusLabel('en')); ?></span></td>
                                </tr>
                                <?php if($status['accepted'] === true): ?>
                                <tr>
                                    <th>Internship Position</th>
                                    <td>
                                        <?php echo e($internship['internship_position'] ?? 'Not specified'); ?>

                                    </td>
                                </tr>
                                <tr>
                                    <th>Internship Period</th>
                                    <td>
                                        <?php echo e($internship->getDateRange()); ?> (<?php echo e($internship->getDuration()); ?>)
                                        <span class="badge badge-<?php echo e($internship->getStatusColor()); ?> ml-2" style="font-size: 9px; vertical-align: middle;"><?php echo e($internship->getStatusLabel()); ?></span>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            
            <?php if($letter): ?>
            <div class="col-lg-12">
                <div class="card letter-card shadow-sm mb-4">
                    <div class="letter-header d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="m-0 fw-bold text-primary"><i class="fas fa-envelope-open mr-2"></i> Application Letter Details</h6>
                            <small class="text-muted"><?php echo e($letter['full_letter_number'] ?: 'Processing...'); ?></small>
                        </div>
                        <div class="text-right">
                            <button onclick="document.getElementById('letter-frame').contentWindow.print()" class="btn btn-sm btn-primary btn-round px-3">
                                <i class="fas fa-print mr-1"></i> Print PDF
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-7">
                                <div class="doc-viewer-container">
                                    <iframe id="letter-frame" src="<?php echo e(base_url('index.php?page=print_internship_letter&id=' . $letter['id_internship_letter'] . '&token=' . $_SESSION['PRINT_TOKEN'])); ?>" width="100%" height="450px" style="border:none;"></iframe>
                                </div>
                                <p class="small text-muted mt-2 text-center"><i class="fas fa-info-circle mr-1"></i> Document preview as seen by company</p>
                            </div>
                            <div class="col-md-5">
                                
                                <div class="bg-light p-3 mb-4" style="border-radius: 8px;">
                                    <h6 class="fw-bold mb-3" style="font-size: 11px; letter-spacing: 1px;">APPROVAL STATUS</h6>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="small">Coordinator:</span>
                                        <span class="badge <?php echo e($letter['coor_approval'] == 'approved' ? 'badge-success' : 'badge-warning'); ?>"><?php echo e(ucfirst($letter['coor_approval'])); ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="small">CDC/Unit Magang:</span>
                                        <span class="badge <?php echo e($letter['cdc_approval'] == 'approved' ? 'badge-success' : 'badge-warning'); ?>"><?php echo e(ucfirst($letter['cdc_approval'])); ?></span>
                                    </div>
                                </div>

                                
                                <h6 class="fw-bold mb-3 px-2" style="font-size: 11px; letter-spacing: 1px;">APPROVAL HISTORY</h6>
                                <div class="history-timeline">
                                    <?php $__empty_1 = true; $__currentLoopData = $letter_history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <div class="history-item">
                                        <div class="fw-bold text-dark" style="font-size: 12px;"><?php echo e($h['status']); ?></div>
                                        <div class="text-muted" style="font-size: 11px;"><?php echo e(date('d M Y, H:i', strtotime($h['created_at']))); ?></div>
                                    </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <p class="text-muted small italic">No tracking records available.</p>
                                    <?php endif; ?>
                                </div>

                                <?php if($letter['has_reply'] == 'yes' && !empty($letter['company_reply_letter'])): ?>
                                <div class="mt-4 pt-3 border-top">
                                    <a href="viewer.php?src=get_letter_reply&location=<?php echo e(base64_encode($letter['company_reply_letter'])); ?>" target="_blank" class="btn btn-outline-info btn-block btn-sm btn-round">
                                        <i class="fas fa-download mr-2"></i> Download Company Reply
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            
            <div class="col-lg-12 mt-2">
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-header bg-white border-bottom-0 py-3">
                        <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-cogs mr-2 text-primary"></i> Internship Management</h6>
                    </div>
                    <div class="card-body">
                        <div class="action-section">
                            <h6 class="text-uppercase fw-bold text-muted mb-3" style="font-size: 10px; letter-spacing: 1.5px;">Group 1: Administration</h6>
                            <div class="row mb-4">
                                <div class="col-md-3 col-6">
                                    <a href="index.php?page=intern_student_registration&nim=<?= base64_encode($internship['nim']) ?>&id_internship=<?= base64_encode($internship['id_internship']) ?>" class="btn btn-secondary btn-block btn-action-custom">
                                        <i class="fab fa-wpforms mr-2"></i> Registration Data
                                    </a>
                                </div>
                                <div class="col-md-3 col-6">
                                    <a href="<?php echo e(router('lecturer.company.detail', ['id' => Encrypter::encrypt($internship['id_company'])])); ?>" class="btn btn-secondary btn-block btn-action-custom">
                                        <i class="fas fa-building mr-2"></i> Company Profile
                                    </a>
                                </div>
                            </div>

                            <?php if($status['accepted'] === true): ?>
                            <h6 class="text-uppercase fw-bold text-muted mb-3" style="font-size: 10px; letter-spacing: 1.5px;">Group 2: Execution & Tracking</h6>
                            <div class="row mb-4">
                                <div class="col-md-3 col-6">
                                    <a href="<?php echo e(router('lecturer/my/student.internship.discussion', ['nim' => $nim, 'id_internship' => $internship['id_internship']])); ?>" class="btn btn-primary btn-block btn-action-custom shadow-sm">
                                        <i class="fas fa-comments mr-2"></i> Discussion
                                    </a>
                                </div>
                                <div class="col-md-3 col-6">
                                    <a href="index.php?page=intern_student_attendance&nim=<?= base64_encode($internship['nim']) ?>&id_internship=<?= base64_encode($internship['id_internship']) ?>" class="btn btn-primary btn-block btn-action-custom shadow-sm">
                                        <i class="fas fa-calendar-check mr-2"></i> Attendance
                                    </a>
                                </div>
                                <div class="col-md-3 col-6">
                                    <a href="index.php?page=intern_student_logbook&nim=<?= base64_encode($internship['nim']) ?>&id_internship=<?= base64_encode($internship['id_internship']) ?>" class="btn btn-primary btn-block btn-action-custom shadow-sm">
                                        <i class="fas fa-book mr-2"></i> Logbook
                                    </a>
                                </div>
                            </div>

                            <h6 class="text-uppercase fw-bold text-muted mb-3" style="font-size: 10px; letter-spacing: 1.5px;">Group 3: Closing (Final Phase) 
                                <?php if(!$is_end_phase): ?> <i class="fas fa-lock ml-2 text-warning"></i> <small class="text-sm text-warning text-muted" style="font-size: 10px; letter-spacing: 1.5px;">Locked until the final phase</small> <?php endif; ?>
                            </h6>
                            <div class="row <?php echo e(!$is_end_phase ? 'opacity-50' : ''); ?>">
                                <div class="col-md-3 col-6">
                                    <a href="index.php?page=intern_student_final_report&nim=<?= base64_encode($internship['nim']) ?>&id_internship=<?= base64_encode($internship['id_internship']) ?>" class="btn <?php echo e($is_end_phase ? 'btn-info' : 'btn-light disabled'); ?> btn-block btn-action-custom">
                                        <i class="fas fa-file-pdf mr-2"></i> Final Report
                                    </a>
                                </div>
                                <div class="col-md-3 col-6">
                                    <a href="index.php?page=intern_student_student_feedback&nim=<?= base64_encode($internship['nim']) ?>&id_internship=<?= base64_encode($internship['id_internship']) ?>" class="btn <?php echo e($is_end_phase ? 'btn-info' : 'btn-light disabled'); ?> btn-block btn-action-custom">
                                        <i class="fas fa-user-edit mr-2"></i> Student Feedback
                                    </a>
                                </div>
                                <div class="col-md-3 col-6">
                                    <a href="index.php?page=intern_student_industry_feedback&nim=<?= base64_encode($internship['nim']) ?>&id_internship=<?= base64_encode($internship['id_internship']) ?>" class="btn <?php echo e($is_end_phase ? 'btn-info' : 'btn-light disabled'); ?> btn-block btn-action-custom">
                                        <i class="fas fa-medal mr-2"></i> Industry Feedback
                                    </a>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Internship -->
<div class="modal fade" id="modalEditInternship" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content border-0">
            <form action="" method="POST">
                <?php echo csrf(); ?>

                <input type="hidden" name="id_internship" value="<?php echo e(Encrypter::encrypt($internship['id_internship'])); ?>">
                
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold"><i class="fas fa-cog mr-2"></i> Adjust Internship Details</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-group form-group-default">
                        <label>Internship Position</label>
                        <input type="text" name="internship_position" class="form-control" value="<?php echo e($internship['internship_position']); ?>">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group form-group-default">
                                <label>Start Date</label>
                                <input type="date" name="start_date" class="form-control" value="<?php echo e($internship['start_date']); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-group-default">
                                <label>End Date (Extension)</label>
                                <input type="date" name="end_date" class="form-control" value="<?php echo e($internship['end_date']); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group form-group-default">
                                <label>Duration (Month)</label>
                                <input type="number" name="internship_period" class="form-control" value="<?php echo e($internship['internship_period']); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-group-default">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="Waiting" <?php echo e($internship['status'] == 'Waiting' ? 'selected' : ''); ?>>Waiting</option>
                                    <option value="Yes" <?php echo e($internship['status'] == 'Yes' ? 'selected' : ''); ?>>On Going (Yes)</option>
                                    <option value="Finish" <?php echo e($internship['status'] == 'Finish' ? 'selected' : ''); ?>>Finished</option>
                                    <option value="No" <?php echo e($internship['status'] == 'No' ? 'selected' : ''); ?>>Rejected (No)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning mt-3 border-0 small" style="border-radius: 8px;">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Changing the <strong>End Date</strong> will automatically update the internship duration status in the system.
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light btn-round px-4" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-round px-4 shadow-sm">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('templates.lecturer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp_new\htdocs\pblKita\views/lecturer/internship_management/internship_detail.blade.php ENDPATH**/ ?>