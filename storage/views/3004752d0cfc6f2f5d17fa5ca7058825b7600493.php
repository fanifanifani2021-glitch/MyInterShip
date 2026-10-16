

<?php $__env->startSection('main-content'); ?>
<style>
    /* Custom Styling untuk meningkatkan estetika KaiAdmin */
    .card-profile .card-header {
        border-radius: 12px 12px 0 0 !important;
        background-position: center !important;
    }
    .profile-picture {
        margin-bottom: -60px;
    }
    /* Avatar tetap lingkaran agar ikonik */
    .profile-avatar-custom {
        width: 120px;
        height: 120px;
        border: 6px solid #ffffff;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        background: #eee;
    }
    .info-label {
        font-size: 10px !important;
        font-weight: 800 !important;
        letter-spacing: 0.8px;
        color: #1572e8 !important;
        margin-bottom: 5px !important;
    }
    .info-value {
        font-weight: 600 !important;
        color: #2a2b3d !important;
        background: transparent !important;
        padding: 0 !important;
        height: auto !important;
    }
    .custom-form-group {
        border: 1px solid #ebedf2 !important;
        border-radius: 8px !important; /* Senada dengan tombol */
        transition: all 0.3s;
        background: #fdfdfd;
    }
    .custom-form-group:hover {
        border-color: #1572e8 !important;
        box-shadow: 0 4px 12px rgba(21, 114, 232, 0.08);
    }

    /* Tab Navigation - Ubah dari Pill ke Rounded Square */
    .nav-pills.nav-custom .nav-link {
        background: #f4f4f4;
        color: #8d9498;
        border-radius: 8px !important; /* Perubahan di sini */
        padding: 8px 20px;
        font-weight: 600;
        font-size: 12px;
        margin-left: 5px;
    }
    .nav-pills.nav-custom .nav-link.active {
        background: #5c55bf !important;
        color: white;
    }

    .table-history thead th {
        font-size: 12px;
        text-transform: uppercase;
        font-weight: 700;
        color: #495057;
        background: #fafafa;
        border-top: 0 !important;
    }

    /* Tombol Dokumen - Ubah dari Pill ke Rounded */
    .btn-doc-custom {
        border: 1.5px solid #ebedf2;
        border-radius: 8px !important; /* Perubahan di sini */
        padding: 6px 15px;
        font-size: 12px;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-doc-custom:hover {
        background: #1572e8;
        color: white;
        border-color: #1572e8;
        transform: translateY(-2px);
    }

    /* Tombol Management - Ubah dari Pill ke Rounded */
    .btn-management {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.5px;
        border-radius: 8px !important; /* Perubahan di sini */
        padding: 5px 15px;
    }
</style>

<div class="container">
    <div class="page-inner">
        <div class="page-header mb-4">
            <h4 class="page-title">Student Detail</h4>
            <ul class="breadcrumbs">
                <li class="nav-home"><a href="<?php echo e(router('dashboard')); ?>"><i class="flaticon-home"></i></a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="<?php echo e(router('lecturer/my/student.index')); ?>">My Supervised Student</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><b>Detail</b></li>
            </ul>
        </div>

        <div class="row">
            
            <div class="col-md-12">
                <div class="card card-profile border-0 shadow-sm">
                    <div class="card-header" style="background-image: url('assets/img/blogpost.jpg'); height: 200px; background-size: cover;">
                        <div class="profile-picture text-center">
                            <div class="avatar mx-auto">
                                <img src="<?php echo e($student->getAvatarUrl()); ?>" alt="Profile" class="avatar-img rounded-circle profile-avatar-custom">
                            </div>
                        </div>
                    </div>
                    <div class="card-body mt-5 pt-5">
                        <div class="user-profile text-center mb-4">
                            <h2 class="fw-bold mb-1"><?php echo e($student->name); ?></h2>
                            <p class="text-muted">Student at <?php echo e($student->kode_prodi); ?> - <?php echo e($student->program_study_name); ?></p>
                            
                            
                            <div class="d-flex justify-content-center gap-2 mt-2">
                                <form action="<?php echo e(router('lecturer/my/student.reset_password')); ?>" method="POST" class="form-confirm" data-msg="Reset password for this student to their NIM?">
                                    <?php echo csrf(); ?>

                                    <input type="hidden" name="nim" value="<?php echo e(Encrypter::encrypt($student->nim)); ?>">
                                    <button type="submit" class="btn btn-warning btn-management btn-sm shadow-sm mr-2">
                                        <i class="fas fa-key mr-1"></i> RESET PASSWORD
                                    </button>
                                </form>

                                <form action="<?php echo e(router('lecturer/my/student.cancel_supervision')); ?>" method="POST" class="form-confirm" data-msg="Are you sure you want to stop supervising this student?">
                                    <?php echo csrf(); ?>

                                    <input type="hidden" name="nim" value="<?php echo e(Encrypter::encrypt($student->nim)); ?>">
                                    <button type="submit" class="btn btn-danger btn-management btn-sm shadow-sm">
                                        <i class="fas fa-user-slash mr-1"></i> CANCEL SUPERVISION
                                    </button>
                                </form>
                            </div>
                        </div>

                        
                        <div class="row px-lg-5 mb-4">
                            <div class="col-md-4 mb-3">
                                <div class="form-group form-group-default custom-form-group">
                                    <label class="info-label">NIM (Student ID)</label>
                                    <input type="text" class="form-control info-value" value="<?php echo e($student->nim); ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-group form-group-default custom-form-group">
                                    <label class="info-label">Official Email</label>
                                    <input type="text" class="form-control info-value" value="<?php echo e($student->email ?: '-'); ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-group form-group-default custom-form-group">
                                    <label class="info-label">Study Program</label>
                                    <input type="text" class="form-control info-value" value="<?php echo e($student->program_study_name); ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group form-group-default custom-form-group">
                                    <label class="info-label">WhatsApp Number</label>
                                    <input type="text" class="form-control info-value" value="<?php echo e($student->no_whatsapp ?: '-'); ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group form-group-default custom-form-group">
                                    <label class="info-label">Alternative Email</label>
                                    <input type="text" class="form-control info-value" value="<?php echo e($student->other_email ?: '-'); ?>" readonly>
                                </div>
                            </div>
                        </div>

                        
                        <div class="px-lg-5 border-top pt-4">
                            <h6 class="text-uppercase fw-extrabold text-muted mb-3" style="font-size: 11px;">Documents & Portfolio</h6>
                            <div class="d-flex flex-wrap gap-2">
                                <?php if(!empty($profile->talenthub_cv)): ?>
                                    <a href="<?php echo e($profile->talenthub_cv); ?>" target="_blank" class="btn btn-doc-custom text-info mr-2 mb-2">
                                        <i class="fas fa-link mr-2"></i> TalentHub CV
                                    </a>
                                <?php endif; ?>
                                <?php if(!empty($profile->academic_transcript)): ?>
                                    <a href="<?php echo e(Url::asset('/upload' . $profile->academic_transcript)); ?>" target="_blank" class="btn btn-doc-custom text-danger mr-2 mb-2">
                                        <i class="fas fa-file-pdf mr-2"></i> Academic Transcript
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
<div class="col-md-12 mt-4">
    <div class="card border-0 shadow-sm">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-history mr-2"></i> Internship History
            </h6>
            
            <a href="<?php echo e(router('lecturer/my/student.internship.add', ['nim' => $student['nim']])); ?>" 
               class="btn btn-primary btn-sm shadow-sm px-3">
                <i class="fas fa-plus mr-1"></i> Add Record
            </a>
        </div>

                    <div class="card-body">
                        <div class="d-flex justify-content-end mb-4">
                <ul class="nav nav-pills nav-custom" id="pills-tab" role="tablist">
                    <li class="nav-item"><a class="nav-link active" data-toggle="pill" href="#pills-all">All</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#pills-ongoing">On Going / Finish</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#pills-waiting">Waiting</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#pills-rejected">Rejected</a></li>
                </ul>
            </div>
                        <div class="tab-content">
                            <?php
                                $statusMap = [
                                    'Yes' => 'badge-success', 'Finish' => 'badge-primary',
                                    'Waiting' => 'badge-warning', 'No' => 'badge-danger',
                                ];

                                function renderHistoryTable($data, $studentNim, $statusMap) {
                                    if (empty($data)) {
                                        echo '<div class="text-center py-5">
                                            <!-- SVG Icon Empty State -->
                                            <svg width="120" height="120" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="opacity: 0.2; margin-bottom: 15px; color: #8d9498;">
                                                <path d="M13 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V9L13 2Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M13 2V9H20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                <circle cx="11.5" cy="14.5" r="2.5" stroke="currentColor" stroke-width="1.5"/>
                                                <path d="M13.5 16.5L15.5 18.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                            </svg>
                                            <p class="text-muted fw-bold" style="font-size: 13px;">No internship records found in this category.</p>
                                            <small class="text-muted opacity-75">Try adjusting your filters or add a new record.</small>
                                        </div>';
                                        return;
                                    }
                                    echo '<div class="table-responsive">
                                            <table class="table table-history">
                                                <thead>
                                                    <tr>
                                                        <th width="15%">Register Date</th>
                                                        <th width="35%">Company Name</th>
                                                        <th class="text-center">Status</th>
                                                        <th width="20%">Duration</th>
                                                        <th class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>';
                                    foreach ($data as $row) {
                                        $cls = $statusMap[$row['status']] ?? 'badge-secondary';
                                        $txt = ($row['status'] == 'Yes') ? 'Active' : $row['status'];
                                        $start = date('d M Y', strtotime($row['start_date']));
                                        $end = date('d M Y', strtotime($row['end_date']));
                                        $reg = date('d M Y', strtotime($row['timestamp_register']));
                                        // $url = base_url('index.php?page=intern_student_internship_detail&id_internship=' . base64_encode($row['id_internship']));
                                        $url = router('lecturer/my/student.internship.detail', ['nim' => $row['nim'], 'id_internship' => $row['id_internship']]);
                                        
                                        echo "<tr>
                                                <td class='align-middle text-muted'>{$reg}</td>
                                                <td class='align-middle'><span class='fw-bold text-primary'>{$row['company_name']}</span></td>
                                                <td class='align-middle text-center'><span class='badge {$cls} px-3 py-2'>{$txt}</span></td>
                                                <td class='align-middle small text-muted'>{$start}<br>{$end}</td>
                                                <td class='align-middle text-center'><a href='{$url}' class='btn btn-link btn-primary btn-sm px-3 border rounded-pill'>Detail</a></td>
                                              </tr>";
                                    }
                                    echo '</tbody></table></div>';
                                }
                            ?>

                            <div class="tab-pane fade show active" id="pills-all">
                                <?php renderHistoryTable($history, $student->nim, $statusMap); ?>
                            </div>
                            <div class="tab-pane fade" id="pills-ongoing">
                                <?php renderHistoryTable(array_filter($history, function($i){ return in_array($i['status'], ['Yes','Finish']); }), $student->nim, $statusMap); ?>
                            </div>
                            <div class="tab-pane fade" id="pills-waiting">
                                <?php renderHistoryTable(array_filter($history, function($i){ return $i['status'] == 'Waiting'; }), $student->nim, $statusMap); ?>
                            </div>
                            <div class="tab-pane fade" id="pills-rejected">
                                <?php renderHistoryTable(array_filter($history, function($i){ return $i['status'] == 'No'; }), $student->nim, $statusMap); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('footer-scripts'); ?>
<script>
    $(document).ready(function() {
        // Konfirmasi sebelum Reset/Cancel
        $('.form-confirm').on('submit', function(e) {
            e.preventDefault();
            const form = this;
            const message = $(this).data('msg');

            Swal.fire({
                title: 'Are you sure?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1572e8',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('templates.lecturer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/lecturer/internship_management/detail.blade.php ENDPATH**/ ?>