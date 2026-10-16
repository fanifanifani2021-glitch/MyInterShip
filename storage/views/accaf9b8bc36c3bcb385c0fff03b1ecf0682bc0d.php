

<?php $__env->startPush('head-scripts'); ?>
<?php if(count($logs) > 0): ?>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<?php endif; ?>
<style>
    .page-inner { background: #f8f9fa; min-height: 100vh; padding: 30px 20px !important; }
    .card { border-radius: 12px !important; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .status-pill { padding: 6px 16px; border-radius: 20px; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; }
    
    /* Log Style */
    .log-card { border-left: 4px solid #1572e8; background: #fff; padding: 15px; margin-bottom: 15px; border-radius: 8px; }
    .log-card.out { border-left-color: #ff9100; }
    
    #detailMap { height: 400px; border-radius: 12px; border: 1px solid #e2e8f0; }
    .signature-display { max-width: 400px; border: 2px dashed #cbd5e0; border-radius: 12px; background: #fff; padding: 10px; }
    
    @media (max-width: 768px) {
        .page-header { display: block !important; }
        .breadcrumbs { padding-left: 0; margin-top: 10px; overflow-x: auto; white-space: nowrap; }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title">Attendance Detail</h4>
            <ul class="breadcrumbs">

                <li class="nav-home"><a href="index.php"><i class="flaticon-home text-primary"></i></a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="<?php echo e(router('student.internship')); ?>">My Internship</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="<?php echo e(router('student.internship.detail', ['id' => $id_enc ])); ?>">Detail</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="<?php echo e(router('student.attendance.index', ['id' => $id_enc])); ?>">Attendance</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><b>Detail</b></li>
            </ul>
        </div>

        <div class="row">
            
            <div class="col-lg-5">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h3 class="fw-bold mb-0"><?php echo e(strtoupper($attendance['attendance_type'])); ?></h3>
                                <p class="text-muted mb-0"><?php echo e(date('l, d F Y', strtotime($attendance['date']))); ?></p>
                            </div>
                            <?php if($attendance['is_history']): ?>
                                <span class="status-pill bg-danger text-white">Manual History</span>
                            <?php else: ?>
                                <span class="status-pill bg-primary text-white">GPS Verified</span>
                            <?php endif; ?>
                        </div>

                        <div class="row text-center border-top pt-4">
                            <div class="col-6 border-right">
                                <label class="text-muted small fw-bold text-uppercase">Check In</label>
                                <h4 class="fw-bold text-primary"><?php echo e($attendance['check_in']); ?></h4>
                            </div>
                            <div class="col-6">
                                <label class="text-muted small fw-bold text-uppercase">Check Out</label>
                                <h4 class="fw-bold text-warning"><?php echo e($attendance['check_out'] === '-' ? '--:--' : $attendance['check_out']); ?></h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-white"><h6 class="fw-bold mb-0">Daily Report</h6></div>
                    <div class="card-body">
                        <div class="report-content">
                            <?php echo $attendance['description']; ?>

                        </div>
                    </div>
                </div>
            </div>

            
            <div class="col-lg-7">
                <?php if(!$attendance['is_history'] && count($logs) > 0): ?>
                    <div class="card mb-4">
                        <div class="card-header bg-white d-flex justify-content-between">
                            <h6 class="fw-bold mb-0">Location Tracking</h6>
                            <span class="badge badge-info"><?php echo e(count($logs)); ?> Point(s)</span>
                        </div>
                        <div class="card-body">
                            <div id="detailMap" class="mb-4"></div>
                            
                            <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="log-card <?php echo e($log['attendance_type'] == 'OUT' ? 'out' : ''); ?>">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold text-primary mb-1">
                                        <i class="fas fa-map-marker-alt mr-2"></i><?php echo e($log['attendance_type'] == 'IN' ? 'Entry Point' : 'Exit Point'); ?>

                                    </h6>
                                    <small class="text-muted fw-bold"><?php echo e($log['master_location']); ?></small>
                                </div>
                                <p class="text-muted small mb-0">
                                    Coords: <?php echo e($log['latitude']); ?>, <?php echo e($log['longitude']); ?> | Accuracy: <?php echo e($log['accuracy']); ?>m
                                </p>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php elseif($attendance['is_history'] && $signature): ?>
                    <div class="card mb-4">
                        <div class="card-header bg-white"><h6 class="fw-bold mb-0">Digital Signature Validation</h6></div>
                        <div class="card-body text-center">
                            <div class="signature-display mx-auto mb-3">
                                <img src="<?php echo e($signature['signature']); ?>" class="img-fluid" alt="Student Signature">
                            </div>
                            <p class="text-muted small italic">This attendance was submitted manually and validated by digital signature on <?php echo e(date('d/m/Y H:i', strtotime($signature['created_at'] ?? $attendance['date']))); ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card bg-light border-0">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                            <h5 class="fw-bold">No Verification Data</h5>
                            <p class="text-muted">This record doesn't have GPS logs or signature data.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('footer-scripts'); ?>
<?php if(count($logs) > 0): ?>
<script>
    var map = L.map('detailMap').setView([<?php echo e($logs[0]['latitude']); ?>, <?php echo e($logs[0]['longitude']); ?>], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    var points = [];
    <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        var markerColor = "<?php echo e($log['attendance_type'] == 'IN' ? '#1572e8' : '#ff9100'); ?>";
        var m = L.marker([<?php echo e($log['latitude']); ?>, <?php echo e($log['longitude']); ?>]).addTo(map)
            .bindPopup("<b><?php echo e($log['attendance_type']); ?> Point</b><br><?php echo e($log['master_location']); ?>");
        points.push([<?php echo e($log['latitude']); ?>, <?php echo e($log['longitude']); ?>]);
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    if(points.length > 1) {
        var group = new L.featureGroup(points.map(p => L.marker(p)));
        map.fitBounds(group.getBounds().pad(0.1));
    }
</script>
<?php endif; ?>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('templates.student', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/student/my_internship/attendance/detail.blade.php ENDPATH**/ ?>