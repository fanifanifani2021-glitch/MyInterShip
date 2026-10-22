@extends('templates.cdc')

@push('head-scripts')
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">

<style>
    .badge-status {
        font-size: 0.82rem;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 20px;
    }
    .form-label-custom {
        font-size: 0.85rem;
        font-weight: 600;
        color: #495057;
        margin-bottom: 6px;
    }
    .table-custom th {
        background-color: #f8fafc !important;
        color: #334155 !important;
        font-weight: 700 !important;
        font-size: 0.85rem;
        text-transform: uppercase;
        border-top: none !important;
        vertical-align: middle !important;
    }
    .table-custom td {
        vertical-align: middle !important;
        font-size: 0.9rem;
    }
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 6px;
        border: 1px solid #ebedf2;
        padding: 6px 12px;
        margin-left: 8px;
    }
    .dataTables_wrapper .dataTables_length select {
        border-radius: 6px;
        border: 1px solid #ebedf2;
        padding: 4px 8px;
    }
</style>
@endpush

@section('main-content')
<div class="container">

    <!-- Header Panel Biru Khas Atlantis Dashboard -->
    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">
                        <i class="fas fa-user-graduate mr-2"></i> Data Mahasiswa Magang
                    </h2>
                    <h5 class="text-white op-7 mb-2">
                        Pusat Karir & Kerjasama (CDC) Polibatam
                    </h5>
                </div>
                <div class="ml-md-auto py-2 py-md-0">
                    <!-- Tombol Kembali ke Dashboard -->
                    <a href="index.php" class="btn btn-white btn-round font-weight-bold text-primary shadow-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-inner mt--5">
        <div class="row mt--2">
            <div class="col-md-12">

                <!-- CARD FILTER -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title text-primary font-weight-bold">
                            <i class="fas fa-filter mr-2"></i> Filter Data Mahasiswa Magang
                        </div>
                        <small class="text-muted">Filter dapat digunakan bersamaan</small>
                    </div>

                    <div class="card-body">
                        <form id="filterForm" method="GET" action="index.php">
                            <input type="hidden" name="page" value="show_data">
                            <input type="hidden" name="action" value="companies_ranking_received_student">

                            <div class="row">
                                <!-- 1. Start Date -->
                                <div class="col-md-3 form-group">
                                    <label class="form-label-custom">
                                        <i class="far fa-calendar-alt text-muted mr-1"></i> Start Date
                                    </label>
                                    <input type="date" id="filter_start_date" name="start_date" class="form-control"
                                        value="<?= htmlspecialchars($start_date) ?>">
                                </div>

                                <!-- 2. End Date -->
                                <div class="col-md-3 form-group">
                                    <label class="form-label-custom">
                                        <i class="far fa-calendar-alt text-muted mr-1"></i> End Date
                                    </label>
                                    <input type="date" id="filter_end_date" name="end_date" class="form-control"
                                        value="<?= htmlspecialchars($end_date) ?>">
                                </div>

                                <!-- 3. Major (Jurusan) -->
                                <div class="col-md-3 form-group">
                                    <label class="form-label-custom">
                                        <i class="fas fa-graduation-cap text-muted mr-1"></i> Major (Jurusan)
                                    </label>
                                    <select id="filter_major" name="major" class="form-control" data-selected="<?= htmlspecialchars($major) ?>">
                                        <option value="">— Semua Jurusan —</option>
                                    </select>
                                </div>

                                <!-- 4. Study Program (Prodi) -->
                                <div class="col-md-3 form-group">
                                    <label class="form-label-custom">
                                        <i class="fas fa-book-open text-muted mr-1"></i> Study Program (Prodi)
                                    </label>
                                    <select id="filter_prodi" name="prodi" class="form-control" data-selected="<?= htmlspecialchars($prodi) ?>">
                                        <option value="">— Semua Program Studi —</option>
                                    </select>
                                </div>

                                <!-- 5. Company (Perusahaan) -->
                                <div class="col-md-12 form-group">
                                    <label class="form-label-custom">
                                        <i class="far fa-building text-muted mr-1"></i> Company (Perusahaan)
                                    </label>
                                    <select id="filter_company" name="company" class="form-control">
                                        <option value="">— Semua Perusahaan —</option>
                                        <?php foreach ($companies as $comp): ?>
                                            <option value="<?= htmlspecialchars($comp['name']) ?>" <?= ($company === $comp['name'] || $company == $comp['id_company']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($comp['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Tombol Aksi Filter -->
                            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                <div>
                                    <span class="badge badge-primary p-2">
                                        Total Data Ditemukan: <strong><?= count($students) ?></strong> Mahasiswa
                                    </span>
                                </div>
                                <div>
                                    <a href="index.php?page=show_data&action=companies_ranking_received_student" class="btn btn-outline-secondary btn-sm mr-2">
                                        <i class="fas fa-undo mr-1"></i> Reset Filter
                                    </a>
                                    <button type="submit" class="btn btn-primary btn-sm px-3 font-weight-bold">
                                        <i class="fas fa-search mr-1"></i> Terapkan Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- CARD TABEL HASIL -->
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <div class="card-title text-dark font-weight-bold">
                                <i class="fas fa-table mr-2 text-primary"></i> Tabel Data Magang Mahasiswa
                            </div>
                            <small class="text-muted">Pencarian dapat melalui Nama Mahasiswa, NIM, atau Perusahaan</small>
                        </div>
                        <!-- Tombol Unduh Excel, CSV & Cetak -->
                        <div class="d-flex gap-2 mt-2 mt-md-0">
                            <a href="index.php?page=show_data&action=companies_ranking_received_student&export=excel&start_date=<?= urlencode($start_date) ?>&end_date=<?= urlencode($end_date) ?>&major=<?= urlencode($major) ?>&prodi=<?= urlencode($prodi) ?>&company=<?= urlencode($company) ?>" 
                               class="btn btn-success btn-sm font-weight-bold mr-2 shadow-sm" title="Unduh hasil filter ke Excel (.xlsx)">
                                <i class="fas fa-file-excel mr-1"></i> Unduh Excel
                            </a>
                            <a href="index.php?page=show_data&action=companies_ranking_received_student&export=csv&start_date=<?= urlencode($start_date) ?>&end_date=<?= urlencode($end_date) ?>&major=<?= urlencode($major) ?>&prodi=<?= urlencode($prodi) ?>&company=<?= urlencode($company) ?>" 
                               class="btn btn-primary btn-sm font-weight-bold mr-2 shadow-sm" title="Unduh hasil filter ke CSV (.csv)">
                                <i class="fas fa-file-csv mr-1"></i> Unduh CSV
                            </a>
                            <button type="button" id="btn-print-table" class="btn btn-secondary btn-sm font-weight-bold shadow-sm" title="Cetak data magang mahasiswa">
                                <i class="fas fa-print mr-1"></i> Cetak
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="internshipTable" class="table table-bordered table-hover table-custom w-100">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;" class="text-center">No</th>
                                        <th>Nama Mahasiswa</th>
                                        <th style="width: 140px;" class="text-center">NIM</th>
                                        <th>Jurusan</th>
                                        <th>Program Studi</th>
                                        <th>Perusahaan</th>
                                        <th style="width: 150px;" class="text-center">Status Magang</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($students)): ?>
                                        <?php $no = 1; foreach ($students as $row): ?>
                                            <tr>
                                                <td class="text-center font-weight-bold text-muted"><?= $no++ ?></td>
                                                <td>
                                                    <div class="font-weight-bold text-dark"><?= htmlspecialchars($row['student_name']) ?></div>
                                                    <small class="text-muted">
                                                        <i class="far fa-calendar-check mr-1"></i>
                                                        <?= htmlspecialchars($row['start_date']) ?> s/d <?= htmlspecialchars($row['end_date']) ?>
                                                    </small>
                                                </td>
                                                <td class="text-center font-weight-bold text-primary font-monospace">
                                                    <?= htmlspecialchars($row['nim']) ?>
                                                </td>
                                                <td><?= htmlspecialchars($row['jurusan']) ?></td>
                                                <td>
                                                    <?= htmlspecialchars(trim(($row['jenjang'] ? $row['jenjang'] . ' ' : '') . $row['prodi'])) ?>
                                                </td>
                                                <td>
                                                    <i class="far fa-building text-muted mr-1"></i>
                                                    <strong><?= htmlspecialchars($row['company_name']) ?></strong>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-status <?= $row['badge_class'] ?>">
                                                        <?= htmlspecialchars($row['status_label']) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">
                                                <i class="far fa-folder-open fa-2x mb-2 d-block"></i>
                                                Tidak ada data magang yang sesuai dengan filter.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection

@push('footer-scripts')
<!-- DataTables Script & Print Extension -->
<script src="assets/js/plugin/datatables/datatables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
    // ---- DATA PROGRAM STUDI DARI PHP ----
    const programStudyData = <?= json_encode($program_study) ?>;

    // Grouping Prodi berdasarkan Major / Jurusan
    const groupedProdi = {};
    programStudyData.forEach(p => {
        const majorKey = p.major || p.jurusan || 'Lainnya';
        if (!groupedProdi[majorKey]) {
            groupedProdi[majorKey] = [];
        }
        groupedProdi[majorKey].push(p);
    });

    // 1. Populate Dropdown Major
    const majorSelect = document.getElementById('filter_major');
    const selectedMajor = majorSelect.getAttribute('data-selected') || '';

    Object.keys(groupedProdi).sort().forEach(major => {
        const isSelected = (major === selectedMajor) ? 'selected' : '';
        majorSelect.innerHTML += `<option value="${major}" ${isSelected}>${major}</option>`;
    });

    // 2. Generate Dropdown Prodi berdasarkan Major
    const prodiSelect = document.getElementById('filter_prodi');
    const selectedProdi = prodiSelect.getAttribute('data-selected') || '';

    function renderProdiDropdown(targetMajor = "") {
        prodiSelect.innerHTML = `<option value="">— Semua Program Studi —</option>`;

        if (targetMajor === "") {
            // Tampilkan semua prodi dikelompokkan berdasarkan optgroup
            Object.keys(groupedProdi).sort().forEach(major => {
                let optGroup = `<optgroup label="${major}">`;
                optGroup += `<option value="MAJOR:${major}">Semua Prodi di ${major}</option>`;

                groupedProdi[major].forEach(p => {
                    const val = `PS:${p.kode_prodi}`;
                    const isSel = (val === selectedProdi || p.kode_prodi === selectedProdi) ? 'selected' : '';
                    optGroup += `<option value="${val}" ${isSel}>${p.jenjang ? p.jenjang + ' - ' : ''}${p.study_program || p.prodi}</option>`;
                });

                optGroup += `</optgroup>`;
                prodiSelect.innerHTML += optGroup;
            });
        } else {
            // Tampilkan prodi dari major yang dipilih
            if (groupedProdi[targetMajor]) {
                prodiSelect.innerHTML += `<option value="MAJOR:${targetMajor}">Semua Prodi di ${targetMajor}</option>`;
                groupedProdi[targetMajor].forEach(p => {
                    const val = `PS:${p.kode_prodi}`;
                    const isSel = (val === selectedProdi || p.kode_prodi === selectedProdi) ? 'selected' : '';
                    prodiSelect.innerHTML += `<option value="${val}" ${isSel}>${p.jenjang ? p.jenjang + ' - ' : ''}${p.study_program || p.prodi}</option>`;
                });
            }
        }
    }

    // Jalankan render prodi pertama kali
    renderProdiDropdown(selectedMajor);

    // Event change ketika major dipilih/diganti
    $('#filter_major').on('change', function () {
        const chosenMajor = $(this).val();
        renderProdiDropdown(chosenMajor);
        $('#filter_prodi').val('');
    });

    // Inisialisasi DataTable
    $(document).ready(function () {
        const table = $('#internshipTable').DataTable({
            responsive: true,
            paging: true,
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, 100, -1],
                ['10 Data', '25 Data', '50 Data', '100 Data', 'Semua Data']
            ],
            order: [[0, 'asc']],
            dom: "<'row mb-3'<'col-md-6 d-flex align-items-center'l><'col-md-6 d-flex justify-content-end'f>>" +
                 "<'row'<'col-12'tr>>" +
                 "<'row mt-3'<'col-md-5'i><'col-md-7 d-flex justify-content-end'p>>",
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Cari nama, NIM, atau perusahaan...",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data mahasiswa",
                infoEmpty: "Menampilkan 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                zeroRecords: "Tidak ada data yang sesuai",
                paginate: {
                    first: '<i class="fas fa-angle-double-left"></i>',
                    last: '<i class="fas fa-angle-double-right"></i>',
                    next: '<i class="fas fa-angle-right"></i>',
                    previous: '<i class="fas fa-angle-left"></i>'
                }
            }
        });

        // Inisialisasi tombol Cetak DataTables (dihubungkan ke tombol Cetak di header tabel)
        new $.fn.dataTable.Buttons(table, {
            buttons: [
                {
                    extend: 'print',
                    title: 'Data Mahasiswa Magang - CDC Polibatam',
                    messageTop: 'Periode: <?= htmlspecialchars($start_date) ?> s/d <?= htmlspecialchars($end_date) ?> | Jurusan: <?= htmlspecialchars($major ?: "Semua Jurusan") ?>',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                }
            ]
        });

        // Saat tombol #btn-print-table diklik, jalankan fungsi cetak
        $('#btn-print-table').on('click', function () {
            table.button(0).trigger();
        });
    });
</script>
@endpush