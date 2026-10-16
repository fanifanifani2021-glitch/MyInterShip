

<?php
    // 1. Logika Navigasi Kalender (Tetap di atas)
    $m_input = (int) $month; 
    $y_input = (int) $year;

    $current_date = mktime(0, 0, 0, $m_input, 1, $y_input);
    $month_num = (int) date('m', $current_date);
    $year_num  = (int) date('Y', $current_date);

    $firstDayOfMonth = mktime(0, 0, 0, $month_num, 1, $year_num);
    $numberDays = date('t', $firstDayOfMonth);
    $dateComponents = getdate($firstDayOfMonth);
    $dayOfWeek = $dateComponents['wday']; 

    $prev_month = date('m', strtotime("-1 month", $firstDayOfMonth));
    $prev_year  = date('Y', strtotime("-1 month", $firstDayOfMonth));
    $next_month = date('m', strtotime("+1 month", $firstDayOfMonth));
    $next_year  = date('Y', strtotime("+1 month", $firstDayOfMonth));
    
    $attendanceMap = [];
    foreach($exec as $row) {
        $attendanceMap[date('Y-m-d', strtotime($row['date']))] = $row;
    }
?>

<?php $__env->startPush('head-scripts'); ?>
<style>
    /* CSS Responsif & Kalender Gilang */
    .page-inner { background: #f8f9fa; min-height: 100vh; padding: 30px 20px !important; }

    .calendar-wrapper { overflow-x: auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
    .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); min-width: 750px; background: #e2e8f0; gap: 1px; }
    .calendar-header-day { background: #f1f5f9; padding: 12px; text-align: center; font-weight: 800; font-size: 0.75rem; color: #64748b; }
    .calendar-day { background: white; min-height: 120px; padding: 10px; display: flex; flex-direction: column; cursor: pointer; transition: 0.2s; }
    .calendar-day.empty { background: #fafafa; cursor: default; }
    .day-today { background: #eff6ff !important; box-shadow: inset 0 0 0 2px #3b82f6; }
    .att-marker { font-size: 0.65rem; font-weight: 700; padding: 4px; border-radius: 4px; margin: 5px 0; text-align: center; }
    .status-approved { background: #dcfce7; color: #166534; }
    .status-waiting { background: #fef3c7; color: #92400e; }
    .status-rejected { background: #fee2e2; color: #991b1b; }
    .nav-control-group { background: white; border-radius: 8px; border: 1px solid #e2e8f0; display: flex; align-items: center; }
    .select-cal { border: none; font-weight: 700; color: #1572e8; background: transparent; padding: 5px; cursor: pointer; }
</style>

<style>
    .fw-extrabold { font-weight: 800 !important; }
    .bg-soft-primary { background-color: #e1effe; }
    .bg-soft-info { background-color: #e0f2fe; }
    .icon-circle { width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
    .fw-600 { font-weight: 600; }
    
    /* Animasi Modal agar lebih smooth */
    .modal.fade .modal-dialog { transform: scale(0.9); transition: transform 0.3s ease-out; }
    .modal.show .modal-dialog { transform: scale(1); }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title fw-bold">Internship Attendance</h4>
            <ul class="breadcrumbs">
                <li class="nav-home"><a href="index.php"><i class="flaticon-home text-primary"></i></a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="<?php echo e(router('student.internship')); ?>">My Internship</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="<?php echo e(router('student.internship.detail', ['id' => $id_enc ])); ?>">Detail</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a>Attendance</a></li>
            </ul>
        </div>

        
        <div class="card bg-primary text-white shadow-sm mb-4" style="border-radius: 10px; overflow: hidden;">
            <div class="card-body py-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h6 class="fw-bold mb-2">Attendance Completion Status <i class="fas fa-info-circle ml-1" style="font-size: 0.7rem;"></i></h6>
                        <div class="progress mb-2" style="height: 12px; border-radius: 6px; background: rgba(255,255,255,0.2);">
                            <div class="progress-bar bg-white" style="width: <?php echo e($attendance_percentage); ?>%;"></div>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span><?php echo e($attendance_percentage); ?>% documented. You have <?php echo e($total_period_days - $attendance_count); ?> days left to report.</span>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-right mt-3 mt-md-0 d-none d-md-block" style="border-left: 1px solid rgba(255,255,255,0.3);">
                        <div class="h3 fw-bold mb-0"><?php echo e($attendance_count); ?> / <?php echo e($total_period_days); ?></div>
                        <div class="small opacity-75">Days Logged</div>
                        <div class="mt-1 small fw-bold"><i class="far fa-calendar-alt mr-1"></i> Period: <?php echo e(date('d M Y', strtotime($internship_start))); ?> - <?php echo e(date('d M Y', strtotime($internship_end))); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-center py-3" style="gap:15px;">
                
                
                <div class="d-flex align-items-center gap-2">
                    <div class="nav-control-group px-2 shadow-sm">
                        <a href="<?php echo e(router('student.attendance.index', ['id' => $id_enc])); ?>&month=<?php echo e($prev_month); ?>&year=<?php echo e($prev_year); ?>" class="btn btn-link p-2"><i class="fas fa-chevron-left"></i></a>
                        <div class="d-flex align-items-center">
                            <select id="sel_month" class="select-cal" onchange="changeAttendanceDate()">
                                <?php for($m=1; $m<=12; $m++): ?>
                                    <option value="<?php echo e($m); ?>" <?php echo e($month == $m ? 'selected' : ''); ?>><?php echo e(date('M', mktime(0,0,0,$m,1))); ?></option>
                                <?php endfor; ?>
                            </select>
                            <select id="sel_year" class="select-cal" onchange="changeAttendanceDate()">
                                <?php for($y = $start_year; $y <= $end_year; $y++): ?>
                                    <option value="<?php echo e($y); ?>" <?php echo e($year == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <a href="<?php echo e(router('student.attendance.index', ['id' => $id_enc])); ?>&month=<?php echo e($next_month); ?>&year=<?php echo e($next_year); ?>" class="btn btn-link p-2"><i class="fas fa-chevron-right"></i></a>
                    </div>
                    
                </div>

                
                <div class="btn-group shadow-sm">
                    <?php if($can_check_in): ?>
                        <button class="btn btn-primary btn-sm px-4" onclick="location.href='<?php echo e(router('student.attendance.checkIn', ['id' => $id_enc])); ?>'"><i class="fas fa-sign-in-alt mr-1"></i> Check In</button>
                    <?php elseif($can_check_out): ?>
                        <button class="btn btn-warning btn-sm px-4 text-white" onclick="location.href='<?php echo e(router('student.attendance.checkOut', ['id' => $id_enc, 'attendance_id' => $today_att_id])); ?>'"><i class="fas fa-sign-out-alt mr-1"></i> Check Out</button>
                    <?php endif; ?>

                    <?php if($can_add_manual): ?>
                        <button class="btn btn-outline-primary btn-sm px-3" onclick="location.href='<?php echo e(router('student.attendance.addHistory', ['id' => $id_enc])); ?>'"><i class="fas fa-plus mr-1"></i> Manual</button>
                    <?php endif; ?>
                </div>
            </div>

            
            <?php if(!$is_internship_active): ?>
                <div class="px-4 pb-3">
                    <div class="alert alert-info border-0 shadow-sm m-0 py-2 d-flex align-items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span class="small fw-bold">Internship period has ended. You can only use the <b>Manual</b> button to fill missing reports within your contract period.</span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="calendar-wrapper">
            <div class="calendar-grid">
                <?php $__currentLoopData = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dayName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="calendar-header-day"><?php echo e($dayName); ?></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <?php for($i = 0; $i < $dayOfWeek; $i++): ?>
                    <div class="calendar-day empty"></div>
                <?php endfor; ?>

                <?php for($day = 1; $day <= $numberDays; $day++): ?>
                    <?php
                        $currentDate = sprintf('%04d-%02d-%02d', $year, $month, $day);
                        $att = $attendanceMap[$currentDate] ?? null;
                        $isToday = ($currentDate == date('Y-m-d'));
                        $statusClass = $att ? ($att['approval_spv'] == 'Yes' && $att['approval_dpm'] == 'Yes' ? 'status-approved' : ($att['approval_spv'] == 'No' || $att['approval_dpm'] == 'No' ? 'status-rejected' : 'status-waiting')) : '';
                    ?>

                    <div class="calendar-day <?php echo e($isToday ? 'day-today' : ''); ?>" 
                        <?php if($att): ?> 
                            onclick="showAttendanceDetail(this)"
                            data-type="<?php echo e($att['attendance_type']); ?>"
                            data-date="<?php echo e(date('l, d M Y', strtotime($att['date']))); ?>"
                            data-in="<?php echo e($att['check_in'] ?? '--:--'); ?>"
                            data-out="<?php echo e($att['check_out'] === '-' ? '--:--' : ($att['check_out'] ?? '--:--')); ?>"
                            data-spv="<?php echo e($att['approval_spv']); ?>"
                            data-dpm="<?php echo e($att['approval_dpm']); ?>"
                            data-id="<?php echo e(Encrypter::encrypt($att['id_attendance'])); ?>"
                            data-detail="<?php echo e(router('student.attendance.detail', ['id' => $id_enc, 'attendance_id' => Encrypter::encrypt($att['id_attendance']) ])); ?>"
                            <?php if($att['attendance_type'] === 'Present' && ($att['check_out'] === '-' || empty($att['check_out']))): ?>
                                data-checkout="<?php echo e(router('student.attendance.checkOut', ['id' => $id_enc, 'attendance_id' => Encrypter::encrypt($att['id_attendance'])])); ?>"
                            <?php endif; ?>
                        <?php endif; ?>>
                        
                        <span class="day-number"><?php echo e($day); ?></span>
                        <?php if($att): ?>
                            <div class="att-marker <?php echo e($statusClass); ?>"><?php echo e(strtoupper($att['attendance_type'])); ?></div>
                            <div class="mt-auto time-info">
                                <div class="text-primary"><i class="fas fa-sign-in-alt mr-1"></i><?php echo e($att['check_in'] ?? '--:--'); ?></div>
                                <div class="<?php echo e($att['check_out'] === '-' ? 'text-danger font-italic' : 'text-warning'); ?>">
                                    <i class="fas fa-sign-out-alt mr-1"></i><?php echo e($att['check_out'] === '-' ? 'Missed' : $att['check_out']); ?>

                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title fw-bold"><i class="fas fa-calendar-check mr-2"></i>Attendance Summary</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            
            <div class="modal-body p-0">
                
                <div class="text-center py-4 bg-light">
                    <div id="m-status-badge" class="badge badge-primary px-3 py-2 mb-2 text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;">PRESENT</div>
                    <h4 class="fw-bold text-dark mb-1" id="m-date">Monday, 02 Jan 2023</h4>
                    <span class="text-muted small"><i class="fas fa-map-marker-alt mr-1"></i> Recorded via Mobile System</span>
                </div>

                
                <div class="row no-gutters border-top border-bottom">
                    <div class="col-6 border-right py-3 text-center">
                        <span class="text-muted small d-block text-uppercase fw-bold">Check In</span>
                        <h3 class="fw-extrabold text-primary mb-0" id="m-in">08:00</h3>
                    </div>
                    <div class="col-6 py-3 text-center">
                        <span class="text-muted small d-block text-uppercase fw-bold">Check Out</span>
                        <h3 class="fw-extrabold text-warning mb-0" id="m-out">17:00</h3>
                    </div>
                </div>

                
                <div class="p-4">
                    <h6 class="fw-bold text-dark mb-3">Verification Status</h6>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle bg-soft-primary mr-3"><i class="fas fa-user-tie text-primary"></i></div>
                            <span class="text-dark fw-600">Company Supervisor</span>
                        </div>
                        <span id="m-spv" class="badge badge-warning px-3 py-1">Waiting</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle bg-soft-info mr-3"><i class="fas fa-user-grad text-info"></i></div>
                            <span class="text-dark fw-600">Lecturer</span>
                        </div>
                        <span id="m-dpm" class="badge badge-warning px-3 py-1">Waiting</span>
                    </div>
                </div>
            </div>

            
            <div class="modal-footer border-0 bg-light p-3" id="m-footer-actions">
                
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('footer-scripts'); ?>
<script>
    function showAttendanceDetail(element) {
        const d = element.dataset;
        
        // Sinkronisasi Data
        $('#m-date').text(d.date);
        $('#m-in').text(d.in);
        $('#m-out').text(d.out);
        $('#m-status-badge').text(d.type.toUpperCase());
        
        // Warna Badge dinamis
        if(d.type.toUpperCase() === 'PRESENT') {
            $('#m-status-badge').attr('class', 'badge badge-success px-3 py-2 mb-2 text-uppercase');
        } else {
            $('#m-status-badge').attr('class', 'badge badge-danger px-3 py-2 mb-2 text-uppercase');
        }

        updateBadge($('#m-spv'), d.spv);
        updateBadge($('#m-dpm'), d.dpm);

        // Tombol di Footer
        let footerHtml = `
            <div class="w-100 d-flex gap-2">
                <button class="btn btn-primary flex-grow-1 fw-bold shadow-sm py-2" onclick="location.href='${d.detail}'">
                    <i class="fas fa-external-link-alt mr-1"></i> FULL DETAILS
                </button>
        `;
        
        if (d.checkout) {
            footerHtml += `
                <button class="btn btn-warning text-white flex-grow-1 fw-bold shadow-sm py-2" onclick="location.href='${d.checkout}'">
                    <i class="fas fa-sign-out-alt mr-1"></i> CHECK OUT
                </button>
            `;
        }
        
        footerHtml += `</div>`;
        $('#m-footer-actions').html(footerHtml);
        $('#detailModal').modal('show');
    }

    function updateBadge(el, status) {
        el.removeClass('badge-success badge-warning badge-danger');
        if(status === 'Yes') el.addClass('badge-success').text('Approved');
        else if(status === 'No') el.addClass('badge-danger').text('Rejected');
        else el.addClass('badge-warning').text('Waiting');
    }

    function changeAttendanceDate() {
        const m = $('#sel_month').val();
        const y = $('#sel_year').val();
        location.href = "<?php echo e(router('student.attendance.index', ['id' => $id_enc])); ?>&month=" + m + "&year=" + y;
    }
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('templates.student', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/student/my_internship/attendance/index.blade.php ENDPATH**/ ?>