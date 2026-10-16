

<?php $__env->startPush('head-scripts'); ?>
<!-- DataTables CSS & JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

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
.timeline-content { margin-left: 15px; }
.timeline-date { font-size: 0.9rem; color: #6c757d; display: block; margin-bottom: 3px; }
.timeline-text { font-size: 0.95rem; color: #212529; margin: 0; line-height: 1.5; }
.timeline-text strong { font-weight: 600; color: #1e1e2d; }
.timeline-item:hover .timeline-marker { background-color: #0d6efd; }
</style>


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
.timeline-content { margin-left: 15px; }
.timeline-date { font-size: 0.9rem; color: #6c757d; display: block; margin-bottom: 3px; }
.timeline-text { font-size: 0.95rem; color: #212529; margin: 0; line-height: 1.5; }
.timeline-text strong { font-weight: 600; color: #1e1e2d; }
.timeline-item:hover .timeline-marker { background-color: #0d6efd; }
</style>
<style>
.badge-breathing {
    animation: breathing 1.8s infinite ease-in-out;
}

@keyframes  breathing {
    0%   { transform: scale(1); opacity: 1; }
    50%  { transform: scale(1.12); opacity: 0.85; }
    100% { transform: scale(1); opacity: 1; }
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
                    <h2 class="text-white pb-2 fw-bold">Internship Letter Review</h2>
                    <h5 class="text-white op-7 mb-2">Detailed information of student's internship letter request.</h5>
                </div>
                <div class="mt-3 mt-md-0">
                    <a href="<?php echo e(base_url('index.php?page=internship-coordinator/internship_letter')); ?>" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-2"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- === CONTENT === -->
    <div class="page-inner mt--5">
        <div class="row mt--2">

            <!-- === LETTER INFORMATION === -->
            <div class="col-md-6">

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
                                <p class="fw-bold"><?php echo e(($letter['jenjang'] ?? '') . ' ' . ($letter['study_program'] ?? '-')); ?></p>
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
            
            <!-- === COMPANY INFORMATION === -->
            <div class="col-md-6">

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

                <!-- === APPROVAL FORM (KOORDINATOR) === -->
                <?php if(isset($letter['coor_approval']) && strtolower($letter['coor_approval']) == 'waiting'): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h4 class="mb-0 fw-bold text-primary">
                            <i class="fas fa-clipboard-check me-2"></i> Coordinator Approval
                        </h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?php echo e(base_url('index.php?form=internship-coordinator/letter_approval')); ?>">
                            <?php echo e(csrf()); ?>

                            <input type="hidden" name="id_internship_letter" value="<?php echo e($letter['id_internship_letter']); ?>">
                            <div class="mb-3">
                                <label for="remark" class="form-label">Remark</label>
                                <textarea name="remark" id="remark" class="form-control" rows="3" placeholder="Add a remark (optional)"></textarea>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" name="action" value="approved" class="btn btn-success mr-2">
                                    <i class="fas fa-check me-1"></i> Accept
                                </button>
                                <button type="submit" name="action" value="rejected" class="btn btn-danger">
                                    <i class="fas fa-times me-1"></i> Reject
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endif; ?>

                <!-- === APPROVAL HISTORY === -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h4 class="mb-0 fw-bold text-primary"><i class="fas fa-user-check me-2"></i> Approval History</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped align-middle" id="approval-history">
                            <thead class="table-light">
                                <tr>
                                    <th>Role</th>
                                    <th>Username</th>
                                    <th>Status</th>
                                    <th>Remark</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($approvals)): ?>
                                    <?php foreach ($approvals as $a): ?>
                                        <?php
                                            $roleColor = ($a['role'] == 'cdc') ? 'info' : 'primary';
                                            $roleName = ($a['role'] == 'coor') ? 'Internship Coordinator' : 'CDC';
                                            $statusColor = ($a['status_approval'] == 'approved') ? 'success' : (($a['status_approval'] == 'rejected') ? 'danger' : 'secondary');
                                        ?>
                                        <tr>
                                            <td><span class="badge bg-<?php echo $roleColor; ?> text-white"><?php echo strtoupper($roleName); ?></span></td>
                                            <td><?php echo htmlspecialchars($a['role_username']); ?> - <?php echo e($a['role_name']); ?></td>
                                            <td><span class="badge bg-<?php echo $statusColor; ?> text-white"><?php echo ucfirst($a['status_approval']); ?></span></td>
                                            <td><?php echo !empty($a['remark']) ? htmlspecialchars($a['remark']) : '—'; ?></td>
                                            <td><?php echo !empty($a['created_at']) ? date('d M Y, H:i', strtotime($a['created_at'])) : '—'; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- === LETTER STATUS HISTORY === -->
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h4 class="mb-0 fw-bold text-primary"><i class="fas fa-history me-2"></i> Letter Status History</h4>
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

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#approval-history').DataTable({
        responsive: true,
        paging: true,
        searching: true,
        ordering: true,
        info: false,
        lengthChange: false,
        language: {
            search: "Search:",
            emptyTable: "No approval records available."
        },
        columnDefs: [
            { orderable: false, targets: [0, 2, 3] } // kolom Role, Status, Remark non-sortable
        ]
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('templates.lecturer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/coordinator/internship_letter/detail.blade.php ENDPATH**/ ?>