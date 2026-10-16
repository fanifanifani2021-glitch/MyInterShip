

<?php $__env->startPush('head-scripts'); ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">

<style>
    .flatpickr-input { background-color: #fff !important; cursor: pointer; }
    .filter-label { font-size: 12px; font-weight: 600; text-transform: uppercase; color: #555; margin-bottom: 4px; }
</style>

<?php
    $pdo = $_SERVER['PDO'];
    $id_kampus = $_SESSION['Lecturer-Data']['id_kampus'] ?? 1;
    // NIK Dosen Pembimbing (untuk filter My Student Prodi)
    $nik_dosen = $_SESSION['nim_nik_unit'] ?? $_SESSION['Lecturer-Data']['nim_nik_unit'];

    // 1. Ambil Prodi Mahasiswa Bimbingan SAAT INI (Untuk Optgroup Prioritas)
    $sql_my_prodi = "SELECT DISTINCT ps.kode_prodi, ps.jenjang, ps.study_program
                     FROM student_internship si
                     JOIN program_study ps ON si.program_study = ps.kode_prodi
                     WHERE si.nik_dospem = :nik
                     ORDER BY ps.jenjang, ps.study_program";
    $stmt_my = $pdo->prepare($sql_my_prodi);
    $stmt_my->execute([':nik' => $nik_dosen]);
    $my_prodi_list = $stmt_my->fetchAll(PDO::FETCH_ASSOC);
    
    // Ambil ID Prodi 'My Student' untuk exclude nanti (agar tidak double di list bawah)
    $my_prodi_ids = array_column($my_prodi_list, 'kode_prodi');


    // 2. Ambil SEMUA Prodi (Group by Major) seperti sebelumnya
    // (Logic lama tetap dipakai untuk list 'All Study Program')
    $sql = "SELECT DISTINCT(major) FROM program_study WHERE id_kampus = :id_kampus ORDER BY major";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id_kampus' => $id_kampus]);
    
    $daftar_major = [];
    while ($data_jurusan = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $jurusan = $data_jurusan['major'];
        $sql2 = "SELECT * FROM program_study WHERE major = :jurusan AND id_kampus = :id_kampus ORDER BY jenjang";
        $stmt2 = $pdo->prepare($sql2);
        $stmt2->execute([':jurusan' => $jurusan, ':id_kampus' => $id_kampus]);
        $daftar_prodi = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        
        $daftar_major[] = [ 'major' => $jurusan, 'programs' => $daftar_prodi ];
    }
?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="container">

    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">My Supervised Students</h2>
                    <h5 class="text-white op-7 mb-2">Monitor internship progress of your supervised students.</h5>
                </div>
                <div class="ml-md-auto py-2 py-md-0">
                    <button onclick="window.location.href = '<?php echo e(router('lecturer/my/student.add')); ?>'" class="btn btn-secondary btn-round">
                        <i class="fas fa-plus mr-1"></i> Add Student Manually
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="page-inner mt--5">
        <div class="row mt--2">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center cursor-pointer" data-toggle="collapse" data-target="#filterPanel">
                        <div class="card-title"><i class="fas fa-users mr-2"></i> Students List</div>
                        <button class="btn btn-sm btn-outline-primary"><i class="fas fa-filter mr-1"></i> Filter Data</button>
                    </div>

                    <div id="filterPanel" class="collapse show">
                        <div class="card-body bg-light border-bottom">
                            <form id="filter-form">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="filter-label">Study Program</label>
                                        <select id="study-program-filter" name="study_program" class="form-control select2">
                                            <option value="">All Study Programs</option>
                                            
                                            <?php if(count($my_prodi_list) > 0): ?>
                                            <optgroup label="-- My Current Students --">
                                                <?php $__currentLoopData = $my_prodi_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($mp['kode_prodi']); ?>">
                                                        <?php echo e($mp['jenjang']); ?> <?php echo e($mp['study_program']); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </optgroup>
                                            <?php endif; ?>

                                            <?php $__currentLoopData = $daftar_major; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <optgroup label="<?php echo e($group['major']); ?>">
                                                    <?php $__currentLoopData = $group['programs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prodi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        
                                                        <option value="<?php echo e($prodi['kode_prodi']); ?>">
                                                            <?php echo e($prodi['jenjang']); ?> <?php echo e($prodi['prodi']); ?>

                                                        </option>
                                                        
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </optgroup>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>

                                    <div class="col-md-2 mb-3">
                                        <label class="filter-label">Generation</label>
                                        <select id="generation-filter" class="form-control select2">
                                            <option value="All">All</option>
                                            <?php for($year = 2017; $year <= date('Y'); $year++): ?>
                                                <option value="<?php echo e(substr($year, 2)); ?>"><?php echo e($year); ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label class="filter-label">Internship Status</label>
                                        <select id="internship-status-filter" class="form-control select2">
                                            <option value="All">All Status</option>
                                            <option value="Waiting for Validation">Waiting</option>
                                            <option value="Internship in Progress">In Progress</option>
                                            <option value="Internship Completed">Completed</option>
                                            <option value="Internship Rejected">Rejected</option>
                                            <option value="Not Applied Yet">Not Applied</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label class="filter-label">Start Date Range</label>
                                        <div class="input-group">
                                            <input type="text" id="from_date" class="form-control" placeholder="From">
                                            <input type="text" id="to_date" class="form-control" placeholder="To">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <button type="button" id="resetButton" class="btn btn-sm btn-outline-secondary mr-2">Reset</button>
                                        <button type="submit" class="btn btn-sm btn-primary">Apply Filter</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card-body">
                        <table id="myStudentTable" class="table table-striped table-hover" width="100%">
                            <thead>
                                <tr>
                                    <th>NIM</th>
                                    <th>Student Name</th>
                                    <th>Study Program</th>
                                    <th>Internship Info</th>
                                    <th>Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    let _token = $('meta[name="csrf-token"]').attr('content');

    $(document).ready(function() {
        
        // 1. Init Select2
        $('.select2').select2({ theme: 'bootstrap4', width: '100%' });

        // 2. Init Flatpickr (Date Range)
        let fromPicker = flatpickr("#from_date", {
            altInput: true, altFormat: "d M Y", dateFormat: "Y-m-d",
            onChange: function(selectedDates, dateStr) { toPicker.set("minDate", dateStr || null); }
        });
        let toPicker = flatpickr("#to_date", {
            altInput: true, altFormat: "d M Y", dateFormat: "Y-m-d",
            onChange: function(selectedDates, dateStr) { fromPicker.set("maxDate", dateStr || null); }
        });

        // 3. Init DataTable (Server-Side)
        let table = $('#myStudentTable').DataTable({
            responsive: true,
            pageLength: 10,
            processing: true,
            serverSide: true, // AKTIFKAN SERVER SIDE
            order: [[1, 'asc']], // Default sort berdasarkan Nama Mahasiswa
            ajax: {
                url: "<?php echo e(router('datatable.lecturer.my.student')); ?>", 
                type: "POST",
                data: function(d) {
                    // Kirim Parameter Filter ke Server
                    d._token            = _token; // Pastikan menggunakan key yang sesuai di backend
                    d.study_program     = $('#study-program-filter').val();
                    d.generation        = $('#generation-filter').val();
                    d.internship_status = $('#internship-status-filter').val();
                    d.from_date         = $('#from_date').val();
                    d.to_date           = $('#to_date').val();
                }
            },
            columns: [
                { data: "nim", name: "si.nim" },
                { 
                    data: "student_name", 
                    name: "si.name",
                    render: function(data, type, row) {
                        return `<span class="fw-bold text-dark">${data}</span>`;
                    }
                },
                { 
                    data: "prodi", 
                    name: "ps.study_program",
                    render: function(data, type, row) {
                        return `<small class="text-muted">${row.jenjang} ${row.prodi}</small>`;
                    }
                },
                { 
                    data: "company_name", 
                    name: "co.name",
                    render: function(data, type, row) {
                        if(row.id_internship && row.company_name) {
                            let period = row.period ? `<br><small class="text-muted"><i class="far fa-calendar-alt mr-1"></i>${row.period}</small>` : '';
                            return `<span class="text-primary fw-bold">${row.company_name}</span>${period}`;
                        }
                        return '<span class="text-muted small">- Not Applied -</span>';
                    }
                },
                { 
                    data: "status", 
                    name: "i.status",
                    className: "text-center",
                    render: function(data, type, row) {
                        let badgeClass = 'badge-secondary';
                        let label = data || 'Not Applied';

                        // Mapping Status ke UI Badge
                        const statusMap = {
                            'Yes': { class: 'badge-success', label: 'In Progress' },
                            'Internship in Progress': { class: 'badge-success', label: 'In Progress' },
                            'Finish': { class: 'badge-primary', label: 'Completed' },
                            'Internship Completed': { class: 'badge-primary', label: 'Completed' },
                            'Waiting': { class: 'badge-warning', label: 'Waiting' },
                            'No': { class: 'badge-danger', label: 'Rejected' }
                        };

                        if (statusMap[data]) {
                            badgeClass = statusMap[data].class;
                            label = statusMap[data].label;
                        }

                        return `<span class="badge ${badgeClass}">${label}</span>`;
                    }
                },
                { 
                    data: "action", 
                    orderable: false, 
                    searchable: false,
                    className: "text-center",
                    render: function(data, type, row) {
                        // Render Dropdown Action (Detail & Reset Password)
                        return `
                            <div class="dropdown">
                                <button class="btn btn-link text-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right shadow-sm border-0">
                                    <a class="dropdown-item py-2" href="${row.detail_url}">
                                        <i class="fas fa-user-graduate mr-2 text-info"></i> View Detail
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item py-2 btn-reset-pw" href="javascript:void(0)" 
                                    data-nim="${row.enc_nim}" 
                                    data-name="${row.student_name}">
                                        <i class="fas fa-key mr-2 text-warning"></i> Reset Password
                                    </a>
                                </div>
                            </div>
                        `;
                    }
                }
            ]
        });

        // 4. Filter Event Listeners
        $('#filter-form').on('submit', function(e) {
            e.preventDefault();
            table.ajax.reload();
        });

        $('#resetButton').on('click', function() {
            $('#study-program-filter').val('').trigger('change');
            $('#generation-filter').val('All').trigger('change');
            $('#internship-status-filter').val('All').trigger('change');
            fromPicker.clear(); 
            toPicker.clear();
            table.ajax.reload();
        });
    });


    // Handler untuk tombol Reset Password di dalam DataTable
    $(document).on('click', '.btn-reset-pw', function() {
        const nim = $(this).data('nim');
        const name = $(this).data('name');

        Swal.fire({
            title: 'Reset Password?',
            text: `Are you sure you want to reset password for ${name}? It will be set back to their NIM.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ffad46',
            confirmButtonText: 'Yes, Reset it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                $.ajax({
                    url: "<?php echo e(router('lecturer/my/student.reset_password')); ?>",
                    type: "POST",
                    data: {
                        nim: nim,
                        _token: _token // Pastikan nama variabel token ini benar (cek di atas script)
                    },
                    success: function(res) {
                        Swal.close();
                        
                        // Jika sistem PHP kamu mengembalikan notifikasi SweetAlert otomatis,
                        // biasanya halaman akan reload atau muncul notif sendiri. 
                        // Jika tidak, kita munculkan manual:
                        Swal.fire('Success', 'Password has been reset successfully.', 'success').then(() => {
                            // Opsi: reload table jika perlu
                            // table.ajax.reload();
                        });
                    },
                    error: function(xhr, status, error) {
                        Swal.close();
                        
                        // LOGGING ERROR KE CONSOLE
                        Swal.fire({
                            title: 'Server Error ' + xhr.status,
                            html: `
                                <p>Something went wrong on the server.</p>`,
                            icon: 'error'
                        });
                    }
                });
            }
        });
    });

</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('templates.lecturer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp_new\htdocs\pblKita\views/lecturer/internship_management/index.blade.php ENDPATH**/ ?>