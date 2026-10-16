

<?php $__env->startPush('head-scripts'); ?>

<?php if($record['attendance_type'] === 'Present' && $has_location_rule): ?>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<?php endif; ?>

<style>
    .page-inner { background: #f8f9fa; min-height: 100vh; padding: 30px 20px !important; }
    .card { border-radius: 8px !important; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
    
    /* Time Card Styling */
    .time-banner { background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; margin-bottom: 25px; }
    .time-card { padding: 20px; transition: 0.3s; }
    .time-card.server-side { border-left: 5px solid #1572e8; background: linear-gradient(90deg, #f0f7ff 0%, #ffffff 100%); }
    .time-card.device-side { border-left: 5px solid #6861ce; background: linear-gradient(90deg, #f5f3ff 0%, #ffffff 100%); }
    
    .label-caps { font-size: 0.65rem; font-weight: 800; text-transform: uppercase; color: #718096; margin-bottom: 4px; display: block; }
    .time-main { font-size: 2.2rem; font-weight: 800; line-height: 1; color: #1a202c; font-family: monospace; }
    
    /* Map Container */
    #attendanceMap { height: 400px; width: 100%; border-radius: 8px; border: 1px solid #e2e8f0; background: #f1f5f9; }
    
    /* Status Badge */
    .status-badge { padding: 12px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; display: flex; align-items: center; gap: 10px; margin-top: 15px; }
    .status-searching { background: #ebf8ff; color: #3182ce; border: 1px solid #bee3f8; }
    .status-success { background: #f0fff4; color: #38a169; border: 1px solid #c6f6d5; }
    .status-failed { background: #fff5f5; color: #e53e3e; border: 1px solid #fed7d7; }

    .location-item { padding: 12px; border-radius: 6px; border: 1px solid #edf2f7; margin-bottom: 8px; background: white; transition: 0.2s; }
    .location-item.active { border-color: #1572e8; background: #f0f7ff; box-shadow: 0 2px 8px rgba(21, 114, 232, 0.1); }
    
    .border-left-success { border-left: 0.25rem solid #28a745 !important; }
    .icon-circle { height: 35px; width: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title fw-bold">Daily Attendance</h4>
            <ul class="breadcrumbs">
                <li class="nav-home"><a href="<?php echo e(base_url()); ?>"><i class="flaticon-home text-primary"></i></a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="<?php echo e(router('student.internship')); ?>">My Internship</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="<?php echo e(router('student.attendance.index', ['id' => $id_enc])); ?>">Attendance</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><b>Check Out</b></li>
            </ul>
        </div>

        
        <div class="time-banner shadow-sm">
            <div class="row no-gutters">
                <div class="col-md-6 border-right">
                    <div class="time-card server-side">
                        <span class="label-caps"><i class="fas fa-server mr-1"></i> System Server Time</span>
                        <div class="d-flex align-items-baseline">
                            <span class="time-main text-primary" id="live-server-clock">--:--:--</span>
                            <span class="text-muted ml-2 small">(WIB)</span>
                        </div>
                        <div class="text-muted small mt-1"><i class="far fa-calendar-alt mr-1"></i> <?php echo e(date('D, d M Y')); ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="time-card device-side">
                        <span class="label-caps"><i class="fas fa-mobile-alt mr-1"></i> Your Device Time</span>
                        <div class="d-flex align-items-baseline">
                            <span class="time-main text-secondary" id="live-device-clock">--:--:--</span>
                            <span class="text-muted ml-2 small" id="device-timezone">...</span>
                        </div>
                        <div class="text-muted small mt-1"><i class="far fa-clock mr-1"></i> <span id="device-date">--</span></div>
                    </div>
                </div>
            </div>
        </div>

        
        <form action="<?php echo e(router('student.attendance.checkOut-store', ['id' => $id_enc, 'attendance_id' => $att_id_enc])); ?>" id="form_attendance" method="POST">
            <?php echo '<input type="hidden" name="token" value="' . Session::get('token') . '" />'; ?>
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
            <input type="hidden" name="accuracy" id="accuracy">

            <div class="row">
                
                <div class="<?php echo e($record['attendance_type'] === 'Present' && $has_location_rule ? 'col-lg-5' : 'col-lg-12'); ?>">
                    
                    
                    <div class="card shadow-sm mb-4 border-left-success">
                        <div class="card-header bg-white py-3">
                            <h6 class="m-0 font-weight-bold text-success"><i class="fas fa-sign-in-alt mr-2"></i>Check-In Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-6">
                                    <span class="label-caps">Time Recorded</span>
                                    <span class="h5 font-weight-bold text-dark"><?php echo e(date('H:i', strtotime($record['check_in']))); ?> WIB</span>
                                </div>
                                <div class="col-6 text-right">
                                    <span class="label-caps">Week Reference</span>
                                    <span class="badge badge-info px-3">Week <?php echo e($record['week']); ?></span>
                                </div>
                            </div>
                            
                            <?php if($check_in_gps): ?>
                            <div class="d-flex align-items-start pt-3 border-top">
                                <div class="icon-circle bg-light mr-3 text-primary"><i class="fas fa-map-marker-alt"></i></div>
                                <div>
                                    <span class="label-caps">Location Captured</span>
                                    <p class="mb-0 text-dark font-weight-bold small"><?php echo e($check_in_gps['master_location']); ?></p>
                                    <small class="text-muted">Verification Accuracy: <?php echo e(round($check_in_gps['accuracy'])); ?>m</small>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <div class="form-group p-0 mb-4">
                                <label class="fw-bold text-dark mb-2">Job Description / Activity Report <span class="text-danger">*</span></label>
                                <textarea name="description" id="description" class="form-control" rows="5" placeholder="Summary of your completed tasks today..."></textarea>
                                <small class="form-text text-muted">Mandatory field for daily progress tracking.</small>
                            </div>

                            <?php if($record['attendance_type'] === 'Present' && $has_location_rule): ?>
                            <div id="location-list-panel" class="mb-4">
                                <label class="fw-bold text-dark mb-2 small">AUTHORIZED OFFICE ZONES</label>
                                <?php $__currentLoopData = $attendance_locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="location-item" id="loc-card-<?php echo e($loop->index); ?>">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="fw-bold mb-0" style="font-size: 0.85rem;"><?php echo e($loc['location']); ?></h6>
                                            <small class="text-muted">Allowed Radius: <?php echo e($loc['radius_meters']); ?>m</small>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-muted d-block" style="font-size: 0.6rem; font-weight: 800;">DISTANCE</span>
                                            <span class="fw-bold text-dark" id="dist-val-<?php echo e($loop->index); ?>">-- m</span>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-6">
                                    <button type="button" onclick="confirmation()" class="btn btn-link btn-block text-muted fw-bold">CANCEL</button>
                                </div>
                                <div class="col-6">
                                    <button type="submit" id="submit_btn" class="btn btn-primary btn-block fw-bold shadow">
                                        SUBMIT CHECK OUT
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <?php if($record['attendance_type'] === 'Present' && $has_location_rule): ?>
                <div class="col-lg-7">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div id="attendanceMap"></div>
                            
                            <div id="loc-status" class="status-badge status-searching">
                                <div class="spinner-border spinner-border-sm"></div>
                                <span>Establishing GPS connection for checkout...</span>
                            </div>

                            <div id="loc-success" class="status-badge status-success d-none">
                                <i class="fas fa-check-circle fa-lg"></i>
                                <span>Area Verified. You are within the office perimeter.</span>
                            </div>

                            <div id="loc-error" class="status-badge status-failed d-none">
                                <i class="fas fa-times-circle fa-lg"></i>
                                <span id="loc-error-msg">Outside authorized zone. Verification failed.</span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('footer-scripts'); ?>
<script src='./core/component/soloalert.js'></script>
<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>

<script>
    // 1. Clock Logic
    let serverMillis = <?php echo e(time() * 1000); ?>;
    setInterval(() => {
        serverMillis += 1000;
        $('#live-server-clock').text(new Date(serverMillis).toLocaleTimeString('en-GB', { hour12: false }));
        
        const now = new Date();
        $('#live-device-clock').text(now.toLocaleTimeString('en-GB', { hour12: false }));
        $('#device-date').text(now.toLocaleDateString('en-GB', { weekday: 'short', day: '2-digit', month: 'short', year: 'numeric' }));
        $('#device-timezone').text(`(${Intl.DateTimeFormat().resolvedOptions().timeZone})`);
    }, 1000);

    // 2. Text Editor
    ClassicEditor.create(document.querySelector('#description'), {
        toolbar: ['bold', 'italic', 'bulletedList', 'numberedList', 'undo', 'redo']
    }).catch(error => { console.error(error); });

    // 3. Location Verification
    <?php if($record['attendance_type'] === 'Present' && $has_location_rule): ?>
    const ATT_LOCATIONS = <?= json_encode($attendance_locations) ?>;
    let map = null, userMarker = null;

    function startDetection() {
        $('#submit_btn').prop('disabled', true);
        
        navigator.geolocation.getCurrentPosition(pos => {
            const { latitude, longitude, accuracy } = pos.coords;
            $('#latitude').val(latitude); $('#longitude').val(longitude); $('#accuracy').val(accuracy);

            if (!map) {
                map = L.map('attendanceMap').setView([latitude, longitude], 17);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                ATT_LOCATIONS.forEach(loc => {
                    L.circle([loc.latitude, loc.longitude], { 
                        radius: parseInt(loc.radius_meters), 
                        color: '#1572e8', 
                        fillOpacity: 0.1 
                    }).addTo(map);
                });
            }
            
            if (userMarker) userMarker.setLatLng([latitude, longitude]);
            else userMarker = L.marker([latitude, longitude]).addTo(map).bindPopup("Current Position").openPopup();
            
            validateArea(latitude, longitude, accuracy);
        }, err => {
            $('#loc-status').addClass('d-none'); 
            $('#loc-error').removeClass('d-none').find('span').text("Geolocation access denied. Please enable GPS.");
        }, { enableHighAccuracy: true });
    }

    function validateArea(uLat, uLng, accuracy) {
        let isInside = false;
        $('.location-item').removeClass('active');

        ATT_LOCATIONS.forEach((loc, index) => {
            const dist = getDistance(uLat, uLng, loc.latitude, loc.longitude);
            $(`#dist-val-${index}`).text(Math.round(dist) + " m");
            if (dist <= parseInt(loc.radius_meters)) { 
                isInside = true; 
                $(`#loc-card-${index}`).addClass('active'); 
            }
        });

        $('#loc-status').addClass('d-none');
        if (isInside && accuracy <= 100) {
            $('#loc-success').removeClass('d-none'); 
            $('#loc-error').addClass('d-none');
            $('#submit_btn').prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
        } else {
            $('#loc-error').removeClass('d-none');
            $('#loc-success').addClass('d-none');
        }
    }

    function getDistance(lat1, lon1, lat2, lon2) {
        const R = 6371000;
        const dLat = (lat2 - lat1) * Math.PI / 180, dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(lat1 * Math.PI/180) * Math.cos(lat2 * Math.PI/180) * Math.sin(dLon/2) * Math.sin(dLon/2);
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    }

    $(document).ready(startDetection);
    <?php endif; ?>

    function confirmation() {
        SoloAlert.confirm({
            title: "Cancel Check-out?", 
            body: "Your daily description will not be saved.",
            onOk: () => { window.location.href = "<?php echo e(router('student.attendance.index', ['id' => $id_enc])); ?>"; }
        });
    }
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('templates.student', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/student/my_internship/attendance/check_out.blade.php ENDPATH**/ ?>