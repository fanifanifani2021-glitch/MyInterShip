

<?php $__env->startPush('head-scripts'); ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<style>
    /* Styling Flatpickr agar background putih */
    .flatpickr-input { background-color: #fff !important; cursor: pointer; }
    
    /* Tweaks margin form filter agar lebih rapi */
    #form_filter .form-group { margin-top: 5px !important; margin-bottom: 5px !important; }
    #form_filter .mb-2 { margin-bottom: 5px !important; }
</style>

<?php
    // --- LOGIC PHP (Sama seperti sebelumnya) ---
    $id_kampus = $_SESSION['Lecturer-Data']['id_kampus'] ?? 1;
    $pdo = $_SERVER['PDO'];

    // Ambil daftar jurusan (major)
    $sql = "SELECT DISTINCT(major) FROM program_study WHERE id_kampus = :id_kampus ORDER BY major";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_kampus', $id_kampus, PDO::PARAM_STR);
    $stmt->execute();

    $daftar_major = [];
    while ($data_jurusan = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $jurusan = $data_jurusan['major'];
        $sql2 = "SELECT * FROM program_study WHERE major = :jurusan AND id_kampus = :id_kampus ORDER BY jenjang";
        $stmt2 = $pdo->prepare($sql2);
        $stmt2->bindParam(':jurusan', $jurusan, PDO::PARAM_STR);
        $stmt2->bindParam(':id_kampus', $id_kampus, PDO::PARAM_STR);
        $stmt2->execute();
        $daftar_prodi = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        $daftar_major[] = [ 'major' => $jurusan, 'programs' => $daftar_prodi ];
    }
?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>

<div class="container">
    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex flex-column flex-md-row align-items-left justify-content-between">
                <div>
                    <h2 class="text-white fw-bold mb-1">Student Internship Administration</h2>
                    <h5 class="text-white op-7 mb-2">Monitor internship progress, reports, and feedback for all students.</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="page-inner mt--5">
        <div class="row mt--2">
            <div class="col-md-12">

                <div class="card filter-card">
                    <div class="card-header">
                        <div class="card-title">Filter Data</div>
                    </div>

                    <form id="form_filter">
                        <div class="card-body">
                            <h6 class="text-primary mb-2 border-bottom pb-1">Academic Filters</h6>
                            <div class="row row-cols-1 row-cols-md-3 g-3">
                                <div class="col">
                                    <label>Major</label>
                                    <select class="form-control" id="major" name="major">
                                        <option value="All">All Major</option>
                                        <?php $__currentLoopData = $daftar_major; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($group['major']); ?>"><?php echo e($group['major']); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="col">
                                    <label>Study Program</label>
                                    <select class="form-control" id="program_study" name="program_study">
                                        <option value="">- Select Study Program -</option>
                                        <option value="All">All Study Program</option>
                                        <?php $__currentLoopData = $daftar_major; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <optgroup label="<?php echo e($group['major']); ?>">
                                            <option value="Major:<?php echo e($group['major']); ?>">All <?php echo e($group['major']); ?></option>
                                            <?php $__currentLoopData = $group['programs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prodi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="PS:<?php echo e($prodi['kode_prodi']); ?>"><?php echo e($prodi['jenjang']); ?> <?php echo e($prodi['prodi']); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </optgroup>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="col">
                                    <label>Generation</label>
                                    <select class="form-control" id="generation" name="generation">
                                        <option value="All">All Generation</option>
                                        <?php for($year = 2017; $year <= date('Y'); $year++): ?>
                                            <option value="<?php echo e(substr($year, 2)); ?>">Generation <?php echo e($year); ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>

                            <h6 class="text-primary mt-3 mb-2 border-bottom pb-1">Internship Filters</h6>
                            <div class="row row-cols-1 row-cols-md-4 g-3">
                                <div class="col">
                                    <label>Internship Status</label>
                                    <select class="form-control" id="internship_status" name="internship_status">
                                        <option value="All">All Status</option>
                                        <option value="Waiting for Validation">Waiting for Validation</option>
                                        <option value="Internship in Progress">Internship in Progress</option>
                                        <option value="Internship Completed">Internship Completed</option>
                                        <option value="Internship Rejected">Rejected</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label>Student Feedback</label>
                                    <select class="form-control" id="student_feedback_status" name="student_feedback_status">
                                        <option value="All">All</option>
                                        <option value="filled">Filled</option>
                                        <option value="empty">Not Filled</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label>Industry Feedback</label>
                                    <select class="form-control" id="industry_feedback_status" name="industry_feedback_status">
                                        <option value="All">All</option>
                                        <option value="filled">Filled</option>
                                        <option value="empty">Not Filled</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label>Final Report</label>
                                    <select class="form-control" id="final_report_status" name="final_report_status">
                                        <option value="All">All</option>
                                        <option value="uploaded">Uploaded</option>
                                        <option value="empty">Not Uploaded</option>
                                    </select>
                                </div>
                            </div>

                            <h6 class="text-primary mt-3 mb-2 border-bottom pb-1">Date Range (Start Date)</h6>
                            <div class="row row-cols-1 row-cols-md-2 g-3">
                                <div class="col">
                                    <input type="text" class="form-control datepicker" id="from_date" placeholder="From Date">
                                </div>
                                <div class="col">
                                    <div class="input-group">
                                        <input type="text" class="form-control datepicker" id="to_date" placeholder="Until Date">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="reset_dates">Reset</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button class="btn btn-primary" id="filterButton">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <button type="button" class="btn btn-secondary ml-2" id="resetButton">
                                <i class="fas fa-undo"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            Data List
                            <div class="float-right">
                                <button id="btnExportFullExcel" class="btn btn-success btn-sm d-none">
                                    <i class="fas fa-file-excel mr-1"></i> Export Excel
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="internshipTable" class="table table-striped table-hover" width="100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Student</th>
                                        <th>Company</th>
                                        <th>Status</th>
                                        <th>Period</th>
                                        <th>Final Report</th>
                                        <th>Std. Feedback</th>
                                        <th>Ind. Feedback</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('footer-scripts'); ?>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const _token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // --- 1. Init Flatpickr ---
    let fromPicker = flatpickr("#from_date", {
        altInput: true, altFormat: "d M Y", dateFormat: "Y-m-d",
        onChange: function(selectedDates, dateStr) { toPicker.set("minDate", dateStr || null); }
    });
    let toPicker = flatpickr("#to_date", {
        altInput: true, altFormat: "d M Y", dateFormat: "Y-m-d",
        onChange: function(selectedDates, dateStr) { fromPicker.set("maxDate", dateStr || null); }
    });
    $('#reset_dates').on('click', function() { fromPicker.clear(); toPicker.clear(); });

    // --- 2. Init Select2 ---
    $('.form-control').each(function() {
        if($(this).is('select')) { $(this).select2({ theme: 'bootstrap4', width: '100%' }); }
    });

    // --- 3. Dynamic Prodi Select ---
    const allProgramStudyOptions = $('#program_study').html();
    $('#major').on('change', function () {
        const selectedMajor = $(this).val();
        const $programStudy = $('#program_study');
        if (selectedMajor === 'All') {
            $programStudy.html(allProgramStudyOptions);
        } else {
            const temp = $('<select>' + allProgramStudyOptions + '</select>');
            const matchedGroup = temp.find(`optgroup[label="${selectedMajor}"]`).clone();
            $programStudy.html('<option value="">- Select Study Program -</option><option value="Major:' + selectedMajor + '">All Study Program in ' + selectedMajor + '</option>');
            $programStudy.append(matchedGroup);
        }
        $programStudy.select2({ theme: 'bootstrap4', width: '100%' });
    });

    // --- 4. Init DataTable (PLAIN STYLING) ---
    const table = $('#internshipTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        order: [[0, 'desc']],
        ajax: {
            url: "<?php echo e(router('datatable.coordinator.internship_administration')); ?>", 
            type: "POST",
            data: function (d) {
                d.token             = _token;
                d.major             = $('#major').val();
                d.program_study     = $('#program_study').val();
                d.generation        = $('#generation').val();
                d.internship_status = $('#internship_status').val();
                d.std_fb_status     = $('#student_feedback_status').val();
                d.ind_fb_status     = $('#industry_feedback_status').val();
                d.final_rpt_status  = $('#final_report_status').val();
                d.from_date         = $('#from_date').val();
                d.to_date           = $('#to_date').val();
            },
            beforeSend: function() { $('#filterButton').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Loading...'); },
            complete: function() { $('#filterButton').prop('disabled', false).html('<i class="fas fa-filter"></i> Filter'); }
        },
        columns: [
            { data: 'id_internship', name: 'id_internship', className: 'text-center' },
            { 
                data: 'student_name', 
                name: 'student_name',
                render: function(data, type, row) {
                    return `<strong>${data}</strong><br><small class="text-muted">${row.nim || '-'} | ${row.prodi || ''}</small>`;
                }
            },
            { data: 'company_name', name: 'company_name' },
            { 
                data: 'status', 
                name: 'status',
                className: 'text-center',
                render: function(data, type, row) {
                    let badgeClass = 'badge badge-secondary'; 
                    let label = data;
                    if(data.toLowerCase() === 'yes' || data.toLowerCase() === 'active') { badgeClass = 'badge badge-success'; label = 'In Progress'; }
                    else if(data.toLowerCase() === 'finish') { badgeClass = 'badge badge-primary'; label = 'Completed'; }
                    else if(data.toLowerCase() === 'no') { badgeClass = 'badge badge-danger'; label = 'Rejected'; }
                    else if(data.toLowerCase() === 'waiting') { badgeClass = 'badge badge-warning'; label = 'Waiting'; }
                    return `<span class="${badgeClass}">${label}</span>`;
                }
            },
            { 
                data: 'period', 
                name: 'period',
                render: function(data) { return `<small>${data}</small>`; }
            },

            // --- BAGIAN YANG DIUPDATE (MENAMPILKAN URL/LINK) ---

            { 
                data: 'final_report', 
                name: 'final_report',
                className: 'text-center',
                orderable: false,
                render: function(data, type, row) {
                    // Jika data ada (URL), buat tombol Link
                    if(data && data !== '-') {
                        return `<a href="${data}" target="_blank" class="btn btn-xs btn-outline-primary shadow-sm" title="View PDF">
                                    <i class="fas fa-file-pdf mr-1"></i> View File
                                </a>`;
                    }
                    return '<span class="text-muted small font-italic">-</span>';
                }
            },
            { 
                data: 'student_feedback', 
                name: 'student_feedback',
                className: 'text-center',
                orderable: false,
                render: function(data, type, row) {
                    // Jika data ada (URL)
                    if(data) {
                        return `<a href="${data}" target="_blank" class="btn btn-xs btn-outline-info shadow-sm">
                                    <i class="fas fa-external-link-alt mr-1"></i> Open Link
                                </a>`;
                    }
                    return '<span class="text-muted small font-italic">Not Filled</span>';
                }
            },
            { 
                data: 'industry_feedback', 
                name: 'industry_feedback',
                className: 'text-center',
                orderable: false,
                render: function(data, type, row) {
                    // Jika data ada (URL)
                    if(data) {
                        return `<a href="${data}" target="_blank" class="btn btn-xs btn-outline-info shadow-sm">
                                    <i class="fas fa-external-link-alt mr-1"></i> Open Link
                                </a>`;
                    }
                    return '<span class="text-muted small font-italic">Not Filled</span>';
                }
            },
            { 
                data: 'action', 
                name: 'action',
                orderable: false,
                className: 'text-center',
                render: function(data, type, row) {
                    
                    // row.nim_encoded => Diambil dari PHP (sudah base64)
                    // data            => Diambil dari action (id_internship base64)
                    
                    let url = `index.php?page=student_internship_detail&nim=${row.nim_encoded}&id_internship=${data}`; 
                    
                    return `<a href="${url}" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></a>`;
                }
            }
        ]
    });

    // --- 5. Event Listeners ---
    $('#form_filter').on('submit', function(e) { e.preventDefault(); table.ajax.reload(); });
    
    $('#resetButton').on('click', function() {
        $('#major, #program_study, #generation, #internship_status, #student_feedback_status, #industry_feedback_status, #final_report_status').val(function() {
            return $(this).find('option:first').val(); // Select first option
        }).trigger('change');
        fromPicker.clear(); toPicker.clear();
        table.ajax.reload();
    });

    table.on('xhr', function () {
        let json = table.ajax.json();
        if (json && json.recordsFiltered > 0) $('#btnExportFullExcel').removeClass('d-none');
        else $('#btnExportFullExcel').addClass('d-none');
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('templates.lecturer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/coordinator/internship_administration/index.blade.php ENDPATH**/ ?>