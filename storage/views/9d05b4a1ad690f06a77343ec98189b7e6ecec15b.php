

<?php $__env->startPush('head-scripts'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<style>
    /* === TIMELINE STYLE === */
    .timeline-wrapper {
        position: relative;
        margin-left: 20px;
        padding-left: 20px;
        border-left: 3px solid #0d6efd;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 20px;
    }

    .timeline-marker {
        position: absolute;
        left: -11px;
        top: 3px;
        width: 14px;
        height: 14px;
        background-color: #fff;
        border: 3px solid #0d6efd;
        border-radius: 50%;
    }

    .timeline-content {
        margin-left: 15px;
    }

    .timeline-date {
        font-size: 0.9rem;
        color: #6c757d;
        display: block;
        margin-bottom: 3px;
    }

    .timeline-text {
        font-size: 0.95rem;
        color: #212529;
        margin: 0;
        line-height: 1.5;
    }

    .timeline-text strong {
        font-weight: 600;
        color: #1e1e2d;
    }

    .timeline-item:hover .timeline-marker {
        background-color: #0d6efd;
    }
</style>

<style>
    .timeline-wrapper {
        position: relative;
        margin-left: 20px;
        padding-left: 20px;
        border-left: 3px solid #0d6efd;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 20px;
    }

    .timeline-marker {
        position: absolute;
        left: -11px;
        top: 3px;
        width: 14px;
        height: 14px;
        background-color: #fff;
        border: 3px solid #0d6efd;
        border-radius: 50%;
    }

    .timeline-content {
        margin-left: 15px;
    }

    .timeline-date {
        font-size: 0.9rem;
        color: #6c757d;
        display: block;
        margin-bottom: 3px;
    }

    .timeline-text {
        font-size: 0.95rem;
        color: #212529;
        margin: 0;
        line-height: 1.5;
    }

    .timeline-text strong {
        font-weight: 600;
        color: #1e1e2d;
    }

    .timeline-item:hover .timeline-marker {
        background-color: #0d6efd;
    }
</style>
<style>
    .badge-breathing {
        animation: breathing 1.8s infinite ease-in-out;
    }

    @keyframes  breathing {
        0% {
            transform: scale(1);
            opacity: 1;
        }

        50% {
            transform: scale(1.12);
            opacity: 0.85;
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }
</style>

<?php $__env->stopPush(); ?>


<?php $__env->startSection('main-content'); ?>
<div class="container">

    <!-- === HEADER === -->
    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left flex-column flex-md-row justify-content-between">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Internship Letter Detail</h2>
                    <h5 class="text-white op-7 mb-2">Detailed information of your internship letter submission.</h5>
                </div>
                <div class="mt-3 mt-md-0">
                    <a href="<?php echo e(base_url('index.php?page=my_internship_letter')); ?>" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-2"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- === CONTENT === -->
    <div class="page-inner mt--5">
        <div class="row mt--2">


            <div class="col-md-6">

                <!-- === LETTER INFORMATION === -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 fw-bold text-primary">
                            <i class="fas fa-file-alt me-2"></i> Letter Information
                        </h4>

                        
                        <?php
                            $statusColor = 'secondary';
                            if (isset($letter['status'])) {
                                if ($letter['status'] == 'WAITING') $statusColor = 'warning';
                                elseif ($letter['status'] == 'APPROVED') $statusColor = 'success';
                                elseif ($letter['status'] == 'REJECTED') $statusColor = 'danger';
                            }
                        ?>
                        <span class="badge bg-<?php echo e($statusColor); ?> text-white px-3 py-2">
                            <?php echo e(ucfirst(strtolower($letter['status'] ?? 'Unknown'))); ?>

                        </span>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="text-muted mb-0">Student NIM</label>
                                <p class="fw-bold"><?php echo e($letter['student_nim'] ?? '-'); ?></p>
                            </div>

                            <div class="col-md-6">
                                <label class="text-muted mb-0">Student Name</label>
                                <p class="fw-bold"><?php echo e($letter['student_name'] ?? '-'); ?></p>
                            </div>

                            <div class="col-md-6">
                                <label class="text-muted mb-0">Study Program</label>
                                <p class="fw-bold">
                                    <?php echo e(($letter['jenjang'] ?? '') . ' ' . ($letter['study_program'] ?? '-')); ?></p>
                            </div>

                            <div class="col-md-6">
                                <label class="text-muted mb-0">Department</label>
                                <p class="fw-bold"><?php echo e($letter['jurusan'] ?? '-'); ?></p>
                            </div>

                            <div class="col-md-6">
                                <label class="text-muted mb-0">Letter Language</label>
                                <p class="fw-bold">
                                    <span class="badge bg-info text-white">
                                        <?php echo e((isset($letter['letter_languange']) && $letter['letter_languange'] == 'EN') ? 'English' : 'Bahasa Indonesia'); ?>

                                    </span>
                                </p>
                            </div>

                            <div class="col-md-6">
                                <label class="text-muted mb-0">Internship Period</label>
                                <p class="fw-bold">
                                    <?php echo e(isset($letter['expected_start']) ? date('M d, Y', strtotime($letter['expected_start'])) : '-'); ?>

                                    -
                                    <?php echo e(isset($letter['expected_end']) ? date('M d, Y', strtotime($letter['expected_end'])) : '-'); ?>

                                </p>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            <div class="col-md-6">

                <!-- === COMPANY INFORMATION === -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 fw-bold text-primary">
                            <i class="fas fa-building me-2"></i> Company Information
                        </h4>

                        <?php if(isset($letter['is_new_company']) && $letter['is_new_company'] == 1): ?>
                        <span class="badge bg-warning text-white badge-breathing">
                            <i class="fas fa-star me-1"></i> New Company
                        </span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="text-muted mb-0">Company Name</label>
                                <p class="fw-bold"><?php echo e($letter['company_name'] ?? '-'); ?></p>
                            </div>

                            <div class="col-md-6">
                                <label class="text-muted mb-0">Address</label>
                                <p class="fw-bold"><?php echo e($letter['company_address'] ?? '-'); ?></p>
                            </div>

                            <div class="col-md-6">
                                <label class="text-muted mb-0">Email</label>
                                <p class="fw-bold"><?php echo e($letter['company_email'] ?? '-'); ?></p>
                            </div>

                            <div class="col-md-6">
                                <label class="text-muted mb-0">Phone</label>
                                <p class="fw-bold"><?php echo e($letter['company_phone'] ?? '-'); ?></p>
                            </div>

                            <div class="col-md-6">
                                <label class="text-muted mb-0">City</label>
                                <p class="fw-bold"><?php echo e($letter['company_city'] ?? '-'); ?></p>
                            </div>

                            <div class="col-md-6">
                                <label class="text-muted mb-0">Province</label>
                                <p class="fw-bold"><?php echo e($letter['company_province'] ?? '-'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <div class="col-md-12">


                <?php if($letter['status'] == 'APPROVED'): ?>

                <!-- === INTERNSHIP ACCEPTANCE STATUS === -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h4 class="mb-0 fw-bold text-primary">
                            <i class="fas fa-envelope-open me-2"></i> Internship Acceptance Status
                        </h4>
                    </div>

                    <div class="card-body">

                        <?php
                        $acceptanceStatus = $letter['acceptance_status'] ?? null;
                        $letterDate = !empty($letter['created_at'])
                        ? date('F d, Y', strtotime($letter['created_at']))
                        : '-';
                        ?>

                        
                        <?php if(empty($acceptanceStatus)): ?>

                        <div class="alert alert-info mb-3">
                            Your internship letter was issued on <strong><?php echo e($letterDate); ?></strong>.<br>
                            Please wait for the company's response.<br>
                            <strong>Maximum waiting period: 10 days.</strong>
                        </div>

                        <a href="<?php echo e(base_url('index.php?page=student/internship_letter/acceptance_form&id=' . $letter['id_internship_letter'])); ?>"
                            class="btn btn-primary">
                            <i class="fas fa-edit"></i> Fill Acceptance Status
                        </a>

                        <?php else: ?>
                        
                        <div class="alert alert-<?php echo e($acceptanceStatus == 'accepted' ? 'success' : 'danger'); ?>">
                            <strong>Company Response:</strong>
                            <?php if($acceptanceStatus == 'accepted'): ?>
                            <span class="badge bg-success text-white">Accepted</span> — You have been accepted for the internship.
                            <?php else: ?>
                            <span class="badge bg-danger text-white">Rejected</span> — Your internship request was rejected by the
                            company.
                            <?php endif; ?>
                        </div>

                        <?php endif; ?>

                    </div>
                </div>




                <?php

                $token = $_SESSION['PRINT_TOKEN'];

                ?>
                <!-- === INTERNSHIP LETTER DOCUMENT === -->
                <div class="card shadow-sm mb-4">

                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 fw-bold text-primary">
                            <i class="fas fa-user-check me-2"></i> Internship Letter Document
                        </h4>

                        <button onclick="document.querySelector('iframe').contentWindow.print()"
                            class="btn btn-primary">
                            Print / Save as PDF
                        </button>
                    </div>

                    <div class="card-body">

                        <?php
                        $releaseDate = !empty($letter['created_at'])
                        ? date('F d, Y', strtotime($letter['created_at']))
                        : '-';
                        ?>

                        <p class="text-muted mb-2">
                            <i class="far fa-calendar-check"></i>
                            <strong>Issued on:</strong> <?php echo e($releaseDate); ?>

                        </p>

                        <iframe
                            src="<?php echo e(base_url('index.php?page=print_internship_letter&id=' . $letter['id_internship_letter'] . '&token=' . $token)); ?>"
                            width="100%" height="300px" style="border:none;">
                        </iframe>
                    </div>

                </div>




                <?php endif; ?>



                <!-- === APPROVAL HISTORY === -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h4 class="mb-0 fw-bold text-primary">
                            <i class="fas fa-user-check me-2"></i> Approval History
                        </h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Role</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Remark</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($approvals)): ?>
                                <?php $__currentLoopData = $approvals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                            $roleColor = ($a['role'] == 'cdc') ? 'info' : 'primary';
                                            $roleName = ($a['role'] == 'coor') ? 'Internship Coordinator' : 'CDC';
                                            $statusColor = 'secondary';
                                            if ($a['status_approval'] == 'approved') $statusColor = 'success';
                                            elseif ($a['status_approval'] == 'rejected') $statusColor = 'danger';
                                        ?>
                                <tr>
                                    <td><span
                                            class="badge bg-<?php echo $roleColor; ?> text-white"><?php echo strtoupper($roleName); ?></span>
                                    </td>
                                    <td><?php echo htmlspecialchars($a['role_username']); ?> - <?php echo e($a['role_name']); ?>

                                    </td>
                                    <td><span
                                            class="badge bg-<?php echo $statusColor; ?> text-white"><?php echo ucfirst($a['status_approval']); ?></span>
                                    </td>
                                    <td><?php echo !empty($a['remark']) ? htmlspecialchars($a['remark']) : '—'; ?></td>
                                    <td><?php echo !empty($a['created_at']) ? date('d M Y, H:i', strtotime($a['created_at'])) : '—'; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-muted text-center">No approval record found.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>

                        </table>
                    </div>
                </div>

                <!-- === LETTER STATUS HISTORY === -->
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h4 class="mb-0 fw-bold text-primary"><i class="fas fa-history me-2"></i> Letter Status History
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="timeline-wrapper">
                            <?php if (!empty($histories)): ?>
                            <?php foreach ($histories as $h): ?>
                            <div class="timeline-item">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <span class="timeline-date">
                                        <?php echo htmlspecialchars(date('j F Y, H:i', strtotime($h['created_at'] ?? 'now'))); ?>
                                    </span>
                                    <p class="timeline-text">
                                        <?php echo nl2br(htmlspecialchars($h['status'] ?? '-')); ?>
                                    </p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <p class="text-muted mb-0">No history found.</p>
                            <?php endif; ?>
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
    function submitAcceptance(status) {

        Swal.fire({
            title: "Are you sure?",
            text: "You cannot change this later.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, submit",
            reverseButtons: true
        }).then((result) => {
            if (!result.isConfirmed) return;

            fetch("<?php echo e(base_url('index.php?page=save_acceptance_status')); ?>", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        id: "<?php echo e($letter['id_internship_letter']); ?>",
                        status: status
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire("Saved!", "Acceptance status updated.", "success")
                            .then(() => location.reload());
                    } else {
                        Swal.fire("Failed", data.message, "error");
                    }
                })
                .catch(() => Swal.fire("Error", "Something went wrong!", "error"));
        });
    }
</script>

<?php $__env->stopPush(); ?>
<?php echo $__env->make('templates.student', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/student/my_internship_letter/detail.blade.php ENDPATH**/ ?>