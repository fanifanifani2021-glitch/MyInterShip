

<?php $__env->startPush('head-scripts'); ?>
<style>

    .page-inner { background: #f8f9fa; min-height: 100vh; padding: 30px 20px !important; }


    /* 1. Timeline Layout - Responsive Adjustments */
    .classic-timeline {
        position: relative;
        padding: 10px 0;
        margin-left: 15px;
    }

    .classic-timeline::before {
        content: '';
        position: absolute;
        left: 20px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e8e8e8;
    }

    .logbook-entry {
        position: relative;
        margin-bottom: 30px;
        padding-left: 55px;
    }

    .timeline-node {
        position: absolute;
        left: 0px;
        top: 15px;
        width: 42px;
        height: 42px;
        background: #1572e8;
        border: 4px solid #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 0.85rem;
        z-index: 2;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }

    /* Media Query untuk HP Layar Kecil */
    @media (max-width: 576px) {
        .classic-timeline {
            margin-left: 5px;
        }

        .classic-timeline::before {
            left: 15px;
        }

        .logbook-entry {
            padding-left: 45px;
        }

        .timeline-node {
            left: -4px;
            width: 38px;
            height: 38px;
            font-size: 0.75rem;
        }

        .page-header {
            flex-direction: column !important;
            align-items: flex-start !important;
        }

        .page-header .ml-auto {
            width: 100%;
            margin-top: 15px;
            margin-left: 0 !important;
        }

        .btn-modern-mobile {
            width: 100%;
            display: flex;
            justify-content: center;
        }
    }

    /* 2. Styling Elements */
    .btn-modern {
        border-radius: 8px !important;
        padding: 10px 20px;
        font-weight: 600;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    .content-limiter {
        max-height: 180px;
        overflow: hidden;
        position: relative;
        transition: 0.4s;
    }

    .content-limiter.expanded {
        max-height: none;
    }

    .read-more-fade {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 50px;
        background: linear-gradient(transparent, white);
    }

    .logbook-content img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 10px 0;
    }

    /* 1. Reset & Fix Header Layout */
    .page-header {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        /* Izinkan elemen turun jika tidak muat */
    }

    .page-header .page-title {
        margin-bottom: 0;
        margin-right: 15px;
    }

    /* Buat breadcrumb sejajar dengan judul seperti di image_5d59d6.png */

    /* 2. Responsive Mobile Fix */
    @media (max-width: 768px) {
        .page-header {
            display: block !important;
        }

        /* Stack vertikal */

        .page-header .breadcrumbs {
            border-left: none;
            padding-left: 0;
            margin-top: 8px;
            margin-bottom: 15px;
            overflow-x: auto;
            /* Agar breadcrumb panjang bisa di-geser/scroll */
            white-space: nowrap;
        }

        .page-header .ml-auto {
            width: 100%;
            margin-left: 0 !important;
        }

        .btn-modern-mobile {
            width: 100%;
            /* Tombol penuh agar mudah ditekan di HP */
            justify-content: center;
        }
    }

    .page-num {
        font-size: 1.1rem !important;
        /* Ukuran teks sedikit lebih besar dari standar */
        font-weight: 700 !important;
        /* Tebalkan agar simbol « ‹ terlihat jelas */
        min-width: 40px;
        /* Pastikan tombol tetap kotak sempurna walau angka cuma 1 digit */
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 !important;
        /* Hapus padding agar teks benar-benar di tengah */
    }

    /* 2. Styling Khusus untuk Simbol agar Lebih Tegas */
    .page-num[data-page] {
        line-height: 1;
    }

    /* 3. Hover Effect agar Lebih Modern */
    .page-num:hover:not([disabled]) {
        background-color: #1572e8 !important;
        color: white !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(21, 114, 232, 0.2);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title">Internship Logbook</h4>
            <ul class="breadcrumbs">
                <li class="nav-home"><a href="index.php"><i class="flaticon-home text-primary"></i></a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="<?php echo e(router('student.internship')); ?>">My Internship</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="<?php echo e(router('student.internship.detail', ['id' => $id_enc])); ?>">Detail</a>
                </li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><b>Logbook</b></li>
            </ul>

            <div class="ml-auto">
                <a href="<?php echo e(router('student.logbook.add', ['id' => $id_enc])); ?>"
                    class="btn btn-primary btn-modern shadow btn-modern-mobile">
                    <i class="fa fa-plus mr-2"></i> New Entry
                </a>
            </div>
        </div>

        
        <div class="card bg-primary-gradient text-white shadow mb-4" style="border-radius: 6px;">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8 mb-3 mb-md-0">
                        <div class="d-flex align-items-center mb-1">
                            <h5 class="mb-0 font-weight-bold">Logbook Completion Status</h5>
                            <button class="btn btn-link text-white btn-sm p-0 ml-2 op-8" data-toggle="modal"
                                data-target="#weekInfoModal">
                                <i class="fas fa-info-circle"></i>
                            </button>
                        </div>
                        <div class="progress mb-2"
                            style="height: 12px; background: rgba(255,255,255,0.2); border-radius: 6px;">
                            <div class="progress-bar bg-white shadow-sm" style="width: <?php echo e($stats['percentage']); ?>%">
                            </div>
                        </div>
                        <small class="op-8"><?php echo e($stats['percentage']); ?>% documented. You have
                            <?php echo e(max(0, $stats['total'] - $stats['filled'])); ?> weeks left.</small>
                    </div>
                    <div class="col-md-4 text-center border-left border-white op-8 d-none d-md-block">
                        <h1 class="font-weight-bold mb-0"><?php echo e($stats['filled']); ?> / <?php echo e($stats['total']); ?></h1>
                        <p class="mb-0 small">Weeks Completed</p>
                        <a href="javascript:void(0)" data-toggle="modal" data-target="#weekInfoModal"
                            class="text-white small font-weight-bold" style="text-decoration: underline;">
                            View Schedule
                        </a>
                    </div>
                    
                    <div class="col-12 d-md-none text-center mt-2 border-top border-white pt-2 op-9">
                        <span class="font-weight-bold h4"><?php echo e($stats['filled']); ?> / <?php echo e($stats['total']); ?> Weeks</span>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="form-group p-0">
                    <label class="small font-weight-bold text-uppercase">Filter by Month:</label>
                    <select id="filterMonthFrontend" class="form-control select2" style="width: 100%;">
                        <option value="">All Months</option>
                        <?php
                        $availableMonths = [];
                        foreach($exec as $row) {
                        $m = date('Y-m', strtotime($row['start_date']));
                        if(!in_array($m, $availableMonths)) $availableMonths[] = $m;
                        }
                        ?>
                        <?php $__currentLoopData = $availableMonths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($month); ?>"><?php echo e(date('F Y', strtotime($month))); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
        </div>

        <?php if(count($exec) < 1 ): ?> <div class="card p-5 text-center shadow-sm border-0" style="border-radius: 12px;">
            <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">No logbook entries found.</h5>
    </div>
    <?php else: ?>
    
    <div class="classic-timeline" id="logbook-wrapper">
        <?php $__currentLoopData = $exec; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
        // Enkripsi ID untuk keamanan rute
        $enc_log_id = Encrypter::encrypt($data['id_logbook']);
        $enc_int_id = Encrypter::encrypt($id_plain);
        ?>

        <div class="logbook-entry" data-month="<?php echo e(date('Y-m', strtotime($data['start_date']))); ?>">
            <div class="timeline-node">W<?php echo e($data['week_num']); ?></div>
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            
                            <h5 class="text-primary font-weight-bold mb-0">
                                <a href="<?php echo e(router('student.logbook.detail', ['id' => $enc_int_id, 'logbook_id' => $enc_log_id])); ?>"
                                    class="text-primary decoration-none">
                                    Week <?php echo e($data['week_num']); ?> Report <i class="fas fa-external-link-alt small ml-1"
                                        style="font-size: 0.65rem;"></i>
                                </a>
                            </h5>
                            <small class="text-muted"><i class="far fa-calendar-alt mr-1"></i>
                                <?php echo e(date('d M Y', strtotime($data['start_date']))); ?> -
                                <?php echo e(date('d M Y', strtotime($data['end_date']))); ?></small>
                        </div>

                        <div class="dropdown">
                            <button class="btn btn-icon btn-light btn-round btn-sm" data-toggle="dropdown"><i
                                    class="fa fa-ellipsis-v"></i></button>
                            <div class="dropdown-menu dropdown-menu-right shadow border-0">
                                
                                <a class="dropdown-item"
                                    href="<?php echo e(router('student.logbook.detail', ['id' => $enc_int_id, 'logbook_id' => $enc_log_id])); ?>">
                                    <i class="fas fa-info-circle mr-2 text-primary"></i> View Detail
                                </a>
                                <div class="dropdown-divider"></div>

                                <a class="dropdown-item"
                                    href="<?php echo e(router('student.logbook.edit', ['id' => $enc_int_id, 'logbook_id' => $enc_log_id])); ?>">
                                    <i class="fas fa-edit mr-2 text-warning"></i> Edit Report
                                </a>

                                <button class="dropdown-item text-danger"
                                    onclick="confirm_delete('<?php echo e($enc_log_id); ?>', '<?php echo e($data['week_num']); ?>', '<?php echo e(date('d M Y', strtotime($data['start_date']))); ?>')">
                                    <i class="fas fa-trash-alt mr-2"></i> Delete entry
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="content-limiter mt-3" id="content-<?php echo e($data['id_logbook']); ?>">
                        <div class="logbook-content text-dark">
                            <?php echo htmlspecialchars_decode($data['description']); ?>

                        </div>
                        <div class="read-more-fade"></div>
                    </div>

                    <button class="btn btn-link p-0 mt-2 font-weight-bold text-primary btn-read-more"
                        onclick="toggleLogbook('<?php echo e($data['id_logbook']); ?>')">
                        Read More <i class="fas fa-chevron-down ml-1"></i>
                    </button>

                    <div class="separator-dashed mt-3 mb-3"></div>

                    <div class="d-flex flex-column flex-md-row justify-content-between small align-items-md-center">
                        <div class="mb-2 mb-md-0">
                            <span class="mr-3">Lecturer: <b
                                    class="<?php echo e(strtolower($data['approval_dpm']) == 'yes' ? 'text-success' : 'text-warning'); ?>"><?php echo e($data['approval_dpm']); ?></b></span>
                            <span>Supervisor: <b
                                    class="<?php echo e(strtolower($data['approval_spv']) == 'yes' ? 'text-success' : 'text-warning'); ?>"><?php echo e($data['approval_spv']); ?></b></span>
                        </div>
                        <span class="text-muted font-italic">Added on:
                            <?php echo e(date('d/m/Y H:i', strtotime($data['timestamp_add']))); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div id="frontend-pagination" class="mt-4 d-flex justify-content-center flex-wrap"></div>

    
    <div id="go-to-container" class="mt-4 d-flex justify-content-center align-items-center" style="display:none;">
        <div class="input-group shadow-sm" style="width: 220px;">
            <div class="input-group-prepend">
                <span class="input-group-text bg-white small font-weight-bold text-muted">Jump to:</span>
            </div>
            <select id="jump-page-select" class="form-control form-control-sm border-left-0" style="cursor: pointer;">
                
            </select>
        </div>
        <small class="text-muted ml-3 font-italic"><span id="total-results-info"></span> entries found</small>
    </div>

    <?php endif; ?>
</div>
</div>


<div class="modal fade" id="weekInfoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document"> 
        <div class="modal-content" style="border-radius: 12px;">
            <div class="modal-header border-0">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-calculator mr-2 text-primary"></i>How we count your weeks?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card border-0 shadow-none bg-light p-3" style="border-radius: 10px;">
                            <h6 class="font-weight-bold text-primary mb-2">Calculation Logic:</h6>
                            <p class="small text-muted mb-2">
                                System calculates your internship weeks based on the <b>fixed 7-day calendar cycle</b>:
                            </p>
                            <ul class="small text-muted pl-3">
                                <li><b>Start Date:</b> The first day of your internship marks the beginning of <b>Week 1</b>.</li>
                                <li><b>Duration:</b> Each week consists of exactly <b>7 consecutive days</b>.</li>
                                <li><b>Interval:</b> Week 2 starts exactly 7 days after the Start Date, and so on.</li>
                                <li><b>Rounding:</b> The total number of weeks is calculated by dividing total days by 7 and <b>rounding up (ceil)</b>.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                
                <div class="alert alert-primary shadow-sm mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-uppercase op-8 d-block font-weight-bold">Internship Period</small>
                            <span class="h6 font-weight-bold">
                                <?php echo e(date('d M Y', strtotime($internship['start_date']))); ?> - <?php echo e(date('d M Y', strtotime($internship['end_date']))); ?>

                            </span>
                        </div>
                        <div class="text-right border-left pl-3 ml-3">
                            <small class="text-uppercase op-8 d-block font-weight-bold">Total Duration</small>
                            <span class="h6 font-weight-bold text-primary"><?php echo e($stats['total']); ?> Weeks</span>
                        </div>
                    </div>
                </div>

                
                <h6 class="font-weight-bold mb-3 mt-4">Weekly Reference Table:</h6>
                <div class="table-responsive" style="max-height: 350px; border-radius: 8px;">
                    <table class="table table-sm table-hover border">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th class="py-2 pl-3">Week</th>
                                <th class="py-2">Date Range</th>
                                <th class="py-2 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $weekSchedule; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sched): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="py-2 pl-3"><b>W<?php echo e($sched['week']); ?></b></td>
                                <td class="py-2 small">
                                    <span class="text-dark font-weight-bold"><?php echo e($sched['start']); ?></span> 
                                    <i class="fas fa-long-arrow-alt-right mx-1 text-muted"></i> 
                                    <span class="text-dark font-weight-bold"><?php echo e($sched['end']); ?></span>
                                </td>
                                <td class="py-2 text-center">
                                    <?php if($sched['filled']): ?> 
                                        <span class="badge badge-success px-3 py-1" style="border-radius: 20px;">
                                            <i class="fas fa-check-circle mr-1"></i> Completed
                                        </span>
                                    <?php else: ?> 
                                        <span class="badge badge-warning px-3 py-1" style="border-radius: 20px; color: #fff;">
                                            <i class="fas fa-clock mr-1"></i> Pending
                                        </span> 
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-2 text-center">
                    <small class="text-muted font-italic">* Please ensure you fill your logbook according to these date ranges.</small>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-primary btn-modern w-100" data-dismiss="modal">I Understand</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('footer-scripts'); ?>
<script>
    $(document).ready(function () {
        const itemsPerPage = 20;
        let $allEntries = $('.logbook-entry');
        let $visibleEntries = $allEntries;

        function renderPage(page) {
            const start = (page - 1) * itemsPerPage;
            const end = start + itemsPerPage;

            $allEntries.hide();
            $visibleEntries.slice(start, end).fadeIn(400);

            updatePaginationUI(parseInt(page));
        }

        function updatePaginationUI(currentPage) {
            const totalPages = Math.ceil($visibleEntries.length / itemsPerPage);
            let html = '';

            if (totalPages <= 1) {
                $('#frontend-pagination').html('');
                $('#go-to-container').hide();
                return;
            }

            // 1. Build Tombol Simbol (<< < > >>)
            html +=
                `<button class="btn btn-sm btn-outline-primary mx-1 page-num btn-modern" data-page="1" ${currentPage === 1 ? 'disabled' : ''}>«</button>`;
            html +=
                `<button class="btn btn-sm btn-outline-primary mx-1 page-num btn-modern" data-page="${currentPage - 1}" ${currentPage === 1 ? 'disabled' : ''}>‹</button>`;

            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, startPage + 4);
            if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);

            for (let i = startPage; i <= endPage; i++) {
                html +=
                    `<button class="btn btn-sm ${i === currentPage ? 'btn-primary shadow-sm' : 'btn-outline-primary'} mx-1 page-num btn-modern" data-page="${i}">${i}</button>`;
            }

            html +=
                `<button class="btn btn-sm btn-outline-primary mx-1 page-num btn-modern" data-page="${currentPage + 1}" ${currentPage === totalPages ? 'disabled' : ''}>›</button>`;
            html +=
                `<button class="btn btn-sm btn-outline-primary mx-1 page-num btn-modern" data-page="${totalPages}" ${currentPage === totalPages ? 'disabled' : ''}>»</button>`;

            $('#frontend-pagination').html(html);

            // 2. Isi Dropdown Jump to Page
            let selectOptions = '';
            for (let j = 1; j <= totalPages; j++) {
                selectOptions +=
                    `<option value="${j}" ${j === currentPage ? 'selected' : ''}>Page ${j}</option>`;
            }
            $('#jump-page-select').html(selectOptions);

            // 3. Tampilkan Container & Info
            $('#go-to-container').show();
            $('#total-results-info').text($visibleEntries.length);
        }

        // LISTENER: Ketika dropdown dipilih
        $('#jump-page-select').on('change', function () {
            const target = $(this).val();
            renderPage(target);
            $('html, body').animate({
                scrollTop: $(".page-header").offset().top - 20
            }, 500);
        });

        // LISTENER: Klik nomor halaman
        $(document).on('click', '.page-num', function () {
            if ($(this).prop('disabled')) return;
            renderPage($(this).data('page'));
            $('html, body').animate({
                scrollTop: $(".page-header").offset().top - 20
            }, 500);
        });

        // Filter Logic
        $('#filterMonthFrontend').on('change', function () {
            const selected = $(this).val();
            $visibleEntries = selected === "" ? $allEntries : $allEntries.filter(
                `[data-month="${selected}"]`);
            renderPage(1);
        });

        renderPage(1);
    });

    function toggleLogbook(id) {
        const container = $('#content-' + id);
        container.toggleClass('expanded');
        const isExpanded = container.hasClass('expanded');
        $(event.currentTarget).html(isExpanded ? 'Show Less <i class="fas fa-chevron-up"></i>' :
            'Read More <i class="fas fa-chevron-down"></i>');
        container.find('.read-more-fade').toggle(!isExpanded);
    }

    function confirm_delete(encryptedLogbookId, weekNum, dateRange) {
        Swal.fire({
            title: 'Delete this report?',
            html: `<div class="text-center"><p>You are about to delete entry for:</p><h5 class="font-weight-bold text-primary mb-1">Week ${weekNum}</h5><p class="text-muted small">${dateRange}</p><hr><p class="text-danger small font-weight-bold">This action cannot be undone!</p></div>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            customClass: {
                confirmButton: 'btn btn-danger btn-modern mx-2',
                cancelButton: 'btn btn-secondary btn-modern mx-2'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.showLoading();
                const form = document.createElement('form');
                form.method = 'POST';
                form.action =
                    "<?= router('student.logbook.delete', ['id' => $id_enc, 'logbook_id' => 'REPLACE_ID']) ?>"
                    .replace('REPLACE_ID', encryptedLogbookId);
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = "<?= csrf_token() ?>";
                form.appendChild(csrfInput);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('templates.student', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/student/my_internship/logbook/index.blade.php ENDPATH**/ ?>