@extends('templates.cdc')

@push('head-scripts')
<!-- DataTables Core CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

<!-- Responsive Extension -->
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

<!-- Buttons Extension (Excel, PDF, Print) -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">

<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<style>
    .filter-label {
        font-weight: 600;
        margin-bottom: 5px;
        color: #555;
    }
</style>

<style>
    /* Perkecil jarak antar form-group hanya di dalam card filter */
    #form_filter .form-group {
        margin-top: 6px !important;
        margin-bottom: 10px !important;
    }

    /* Perkecil jarak hr */
    #form_filter hr {
        margin: 12px 0 !important;
    }

    /* Perkecil jarak judul section */
    #form_filter .mb-2 {
        margin-bottom: 6px !important;
    }

    /* Perkecil jarak antar column dalam satu row */
    #form_filter .form-row {
        margin-left: -5px;
        margin-right: -5px;
    }
    #form_filter .form-row > .form-group {
        padding-left: 5px;
        padding-right: 5px;
    }

    /* Wrapper utama card filter */
</style>
@endpush


@php
$id_kampus = $_SESSION['CDC-Data']['id_kampus'] ?? 1;

// Menyiapkan koneksi PDO (pastikan PDO sudah diinisialisasi sebelumnya)
$pdo = $_SERVER['PDO'];

// Menyiapkan query dengan PDO prepared statement, dengan limit 1
$sql = "SELECT * FROM tb_kampus WHERE id_kampus = :id_kampus LIMIT 1";
$stmt = $pdo->prepare($sql);

// Mengikat parameter :id_kampus ke nilai $id_kampus
$stmt->bindParam(':id_kampus', $id_kampus, PDO::PARAM_STR);

// Eksekusi statement
$stmt->execute();

// Mendapatkan hasil sebagai array asosiatif
$exec = $stmt->fetch(PDO::FETCH_ASSOC);

// Mengambil nama kampus dari hasil query
$nama_kampus = $exec['nama_kampus'];

// Menyiapkan query untuk mendapatkan distinct major dari program_study berdasarkan id_kampus
$sql_major = "SELECT DISTINCT major FROM program_study WHERE id_kampus = :id_kampus";
$stmt_major = $pdo->prepare($sql_major);

// Mengikat parameter :id_kampus ke nilai $id_kampus (gunakan kembali variabel yang sudah ada)
$stmt_major->bindParam(':id_kampus', $id_kampus, PDO::PARAM_STR);

// Eksekusi statement untuk mendapatkan hasil
$stmt_major->execute();

// Mengumpulkan hasil major sebagai array
$majors = $stmt_major->fetchAll(PDO::FETCH_COLUMN);

// Ambil daftar jurusan (major)
$sql = "SELECT DISTINCT(major) FROM program_study WHERE id_kampus = :id_kampus ORDER BY major";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id_kampus', $id_kampus, PDO::PARAM_STR);
$stmt->execute();

$daftar_major = [];

while ($data_jurusan = $stmt->fetch(PDO::FETCH_ASSOC)) {
$jurusan = $data_jurusan['major'];

// Ambil daftar program studi untuk setiap jurusan
$sql2 = "SELECT * FROM program_study WHERE major = :jurusan AND id_kampus = :id_kampus ORDER BY jenjang";
$stmt2 = $pdo->prepare($sql2);
$stmt2->bindParam(':jurusan', $jurusan, PDO::PARAM_STR);
$stmt2->bindParam(':id_kampus', $id_kampus, PDO::PARAM_STR);
$stmt2->execute();

$daftar_prodi = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// Simpan ke array besar
$daftar_major[] = [
'major' => $jurusan,
'programs' => $daftar_prodi
];
}

$sql3 = "SELECT * FROM tb_kampus";
$stmt3 = $pdo->prepare($sql3);
$stmt3->execute();

$daftar_kampus = $stmt3->fetchAll(PDO::FETCH_ASSOC);

@endphp


@section('main-content')
<div class="container">

    <!-- HEADER -->
    <div class="panel-header bg-primary-gradient shadow-sm">
        <div class="page-inner py-5">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Internship Letter Management</h2>
                    <h5 class="text-white op-7 mb-2">Manage, review, and approve internship letter submissions.</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="page-inner mt--5">
        <div class="row mt--2">
            <div class="col-md-12">


                <!-- Filter -->
                <div class="card filter-card">
                    <div class="card-header fw-bold">
                        <i class="fas fa-filter mr-1"></i> Filter Internship Letters
                    </div>

                    <form id="form_filter">
                        <div class="card-body">

                            <!-- Section: Academic -->
                            <h6 class="text-primary mb-2">Academic Filters</h6>
                            <div class="row row-cols-1 row-cols-md-3 g-3">

                                <div class="col">
                                    <label class="form-label">Major</label>
                                    <select class="form-control" id="major" name="major">
                                        <option value="All">All Major</option>
                                        @foreach ($daftar_major as $group)
                                            <option value="{{ $group['major'] }}">{{ $group['major'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col">
                                    <label class="form-label">Study Program</label>
                                    <select class="form-control" id="program_study" name="program_study">
                                        <option value="">- Select Study Program -</option>
                                        <option value="All">All Study Program</option>

                                        @foreach ($daftar_major as $group)
                                        <optgroup label="{{ $group['major'] }}">
                                            <option value="Major:{{ $group['major'] }}">
                                                All Study Program in {{ $group['major'] }}
                                            </option>

                                            @foreach ($group['programs'] as $prodi)
                                            <option value="PS:{{ $prodi['kode_prodi'] }}">
                                                {{ $prodi['jenjang'] }} {{ $prodi['prodi'] }}
                                            </option>
                                            @endforeach
                                        </optgroup>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col">
                                    <label class="form-label">Generation</label>
                                    <select class="form-control" id="generation" name="generation">
                                        <option value="All">All Generation</option>
                                        @for ($year = 2017; $year <= date('Y') + 1; $year++)
                                            <option value="{{ substr($year, 2) }}">Generation {{ $year }}</option>
                                        @endfor
                                    </select>
                                </div>

                            </div>

                            <!-- Section: Approval -->
                            <h6 class="text-primary mt-4 mb-2">Approval Filters</h6>
                            <div class="row row-cols-1 row-cols-md-3 g-3">

                                <div class="col">
                                    <label class="form-label">CDC Approval</label>
                                    <select class="form-control" id="cdc_status" name="cdc_status">
                                        <option value="All">All Status</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Waiting">Waiting</option>
                                        <option value="Rejected">Rejected</option>
                                        <option value="Approved">Approved</option>
                                    </select>
                                </div>

                                <div class="col">
                                    <label class="form-label">Coordinator Approval</label>
                                    <select class="form-control" id="coor_status" name="coor_status">
                                        <option value="All">All Status</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Waiting">Waiting</option>
                                        <option value="Rejected">Rejected</option>
                                        <option value="Approved">Approved</option>
                                    </select>
                                </div>

                                <div class="col">
                                    <label class="form-label">Internship Status</label>
                                    <select class="form-control" id="internship_status" name="internship_status">
                                        <option value="All">All Status</option>
                                        <option value="Waiting">Waiting</option>
                                        <option value="Rejected">Rejected</option>
                                        <option value="Accepted">Accepted</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Section: Dates -->
                            <h6 class="text-primary mt-4 mb-2 d-flex justify-content-between align-items-center">
                                Letter Created Date
                                <button type="button" id="reset_dates" class="btn btn-sm btn-outline-secondary">
                                    Reset Dates
                                </button>
                            </h6>

                            <div class="row row-cols-1 row-cols-md-2 g-3">
                                <div class="col">
                                    <label class="form-label">From Date</label>
                                    <input type="text" class="form-control datepicker" id="from_date" placeholder="Select start date">
                                </div>
                                <div class="col">
                                    <label class="form-label">Until</label>
                                    <input type="text" class="form-control datepicker" id="to_date" placeholder="Select end date">
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

                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title mb-0 fw-bold">
                            <i class="fas fa-envelope-open mr-1"></i> Internship Letter Submissions
                        </div>

                        
                        {{-- <button id="btnExportFullExcel" class="btn btn-info btn-sm d-none" data-toggle="modal" data-target="#exportFormatModal">
                            <i class="fa fa-file-export"></i> Export Data
                        </button> --}}
                    </div>

                    <div class="card-body">

                        <table id="internshipLetterTable" class="display table table-striped table-hover" width="100%">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th style="width: 100px;">Created Date</th>
                                    <th>Student</th>
                                    <th>Company</th>
                                    <th>Letter Status</th>
                                    <th>Coordinator Approval</th>
                                    <th>CDC Approval</th>
                                    <th>Acceptance Status</th>
                                    <th>Language</th>
                                    <th>Period</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>

                        <!-- === MODAL DETAIL === -->
                        <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title" id="detailModalLabel">
                                            <i class="fas fa-info-circle mr-2"></i> Internship Letter Detail
                                        </h5>
                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div id="letterDetailContent" class="px-2">
                                            <p class="text-muted text-center mb-0">Loading details...</p>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button id="approveBtn" class="btn btn-success">
                                            <i class="fas fa-check mr-1"></i> Approve
                                        </button>
                                        <button id="rejectBtn" class="btn btn-danger">
                                            <i class="fas fa-times mr-1"></i> Reject
                                        </button>
                                        <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- === END MODAL === -->

                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection


@push('footer-scripts')
<!-- DataTables Core JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>


<!-- Responsive Extension -->
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

<!-- Buttons Extension -->
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>

<!-- Export to Excel -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>

<!-- (Optional) Export to PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.68/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.68/vfs_fonts.js"></script>

<!-- JS Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    let fromPicker, toPicker;

    fromPicker = flatpickr("#from_date", {
        altInput: true,
        altFormat: "l, d F Y", // hanya untuk tampilan
        dateFormat: "Y-m-d",   // value asli yang dikirim ke PHP
        maxDate: false,
        onChange: function(selectedDates, dateStr) {
            // dateStr = "YYYY-MM-DD", bisa langsung dipakai filter server
            if (dateStr) {
                toPicker.set("minDate", dateStr);
            } else {
                toPicker.set("minDate", null);
            }
        }
    });

    toPicker = flatpickr("#to_date", {
        altInput: true,
        altFormat: "l, d F Y", 
        dateFormat: "Y-m-d",
        maxDate: false,
        onChange: function(selectedDates, dateStr) {
            if (dateStr) {
                fromPicker.set("maxDate", dateStr);
            } else {
                fromPicker.set("maxDate", null);
            }
        }
    });

    document.getElementById('reset_dates').addEventListener('click', function () {
        fromPicker.clear();
        toPicker.clear();

        fromPicker.set('maxDate', null);
        toPicker.set('minDate', null);

        // reload datatable
        if (typeof $('#internshipLetterTable').DataTable === 'function') {
            $('#internshipLetterTable').DataTable().ajax.reload();
        }
    });

});

</script>

<script>
let _token = $('meta[name="csrf-token"]').attr('content');

$.ajaxPrefilter(function(options, originalOptions, jqXHR) {
    if (options.dataType === 'binary') {
        options.responseType = 'blob';
        options.processData = false;
    }
});

function triggerExport() {

    let filters = {
        major: $('#major').val(),
        program_study: $('#program_study').val(),
        cdc_status: $('#cdc_status').val(),
        coor_status: $('#coor_status').val(),
        letter_status: $('#letter_status').val(),
        internship_status: $('#internship_status').val(),
        _token: $('meta[name="csrf-token"]').attr('content')
    };

    $.ajax({
    url: "index.php?request=datatable&data=internship_letter_cdc&action=export",
    type: "POST",
    data: filters,
    dataType: "binary",        // <--- penting
    processData: false,
    xhrFields: { responseType: 'blob' },

    beforeSend: function () {
        console.log("Export started");
        $('#exportLoadingSpinner').show();
    },
    complete: function () {
        $('#exportLoadingSpinner').hide();
        console.log("Export completed");
    },

    success: function (blob, status, xhr) {

        console.log("SUCCESS → Blob OK:", blob);

        let filename = "internship_letter_export.xlsx";
        let disposition = xhr.getResponseHeader("Content-Disposition");

        if (disposition && disposition.indexOf("filename=") !== -1) {
            filename = disposition.split("filename=")[1].replace(/["']/g, "");
        }

        const link = document.createElement("a");
        link.href = window.URL.createObjectURL(blob);
        link.download = filename;
        link.click();

        console.log("Downloaded:", filename);
    },

    error: function (xhr, status, errorThrown) {
        console.error("EXPORT FAILED");
        console.error("Status:", status);
        console.error("Error:", errorThrown);
        console.error("Response text:", xhr.responseText);

        alert("Export failed. Check console.");
    }
});


}


document.addEventListener('DOMContentLoaded', function () {

    const _token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const FILTER_KEYS = [
        'major',
        'program_study',
        'generation',
        'cdc_status',
        'coor_status',
        'letter_status',
        'internship_status'
    ];

    const defaultValues = {
        major: 'All',
        program_study: 'All',
        generation: 'All',
        cdc_status: 'All',
        coor_status: 'All',
        letter_status: 'All',
        internship_status: 'All'
    };

    /** ==============================
     *  LOAD FILTERS FROM LOCALSTORAGE
     *  ============================== */
    loadFilters();

    /** ==============================
     *  DATATABLE INIT
     *  ============================== */
    const table = $('#internshipLetterTable').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        order: [[1, 'desc']],
        ajax: {
            url: "index.php?request=datatable&data=internship_letter_cdc",
            type: "POST",
            data: function (d) {
                d.token               = _token;
                d.major               = $('#major').val();
                d.program_study       = $('#program_study').val();
                d.generation          = $('#generation').val();
                d.cdc_status          = $('#cdc_status').val();
                d.coor_status         = $('#coor_status').val();
                d.letter_status       = $('#letter_status').val();
                d.internship_status   = $('#internship_status').val();

                d.from_date   = $('#from_date').val();
                d.to_date   = $('#to_date').val();

            },
            beforeSend: function () {
                $('#spinner').show();
                $('#filterButton').attr('disabled', true);
            },
            complete: function () {
                $('#spinner').hide();
                $('#filterButton').attr('disabled', false);
            }
        },
        columns: [
            { data: "no", className: "text-center" },
            { data: "created_at", className: "text-center" },
            { data: "student_name" },
            { data: "company_name" },
            { data: "status" },
            { data: "coor_approval" },
            { data: "cdc_approval" },
            { data: "acceptance_status", orderable: false },
            { data: "language" },
            { data: "periode", className: "text-center" },
            {
                data: "id_internship_letter",
                orderable: false,
                render: function (id) {
                    return `
                        <a href="{{ base_url('cdc/internship_letter/detail/') }}${id}"
                            class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye mr-1"></i> Detail
                        </a>`;
                }
            }
        ]
    });

    table.on('xhr', function () {
        let json = table.ajax.json();
        if (json.recordsFiltered > 0) {
            $('#btnExportFullExcel').removeClass('d-none').show();
        } else {
            $('#btnExportFullExcel').addClass('d-none').hide();
        }
    });

    /** ==============================
     *  SAVE FILTER WHEN FILTER BUTTON CLICKED
     *  ============================== */
    $('#form_filter').on('submit', function (e) {
        e.preventDefault();
        saveFilters();
        table.ajax.reload();
    });

    /** ==============================
     *  RESET FILTER (CLEAR LOCALSTORAGE)
     *  ============================== */
    $('#resetButton').on('click', function () {

        FILTER_KEYS.forEach(key => {
            $('#' + key).val(defaultValues[key]).trigger('change');
        });

        localStorage.removeItem('internship_letter_filters');

        table.ajax.reload();
    });

    /** ==============================
     *  FUNCTIONS
     *  ============================== */
    function saveFilters() {
        let filters = {};
        FILTER_KEYS.forEach(key => {
            filters[key] = $('#' + key).val();
        });
        localStorage.setItem('internship_letter_filters', JSON.stringify(filters));
    }

    function loadFilters() {
        let saved = localStorage.getItem('internship_letter_filters');
        if (!saved) return;

        let filters = JSON.parse(saved);

        FILTER_KEYS.forEach(key => {
            if (filters[key] !== undefined) {
                $('#' + key).val(filters[key]).trigger('change');
            }
        });
    }

});

$('#btnExportFullExcel').on('click', function() {
    triggerExport();
});

</script>


<script>
$(document).ready(function() {

    // Activate select2
    $('#major, #program_study, #generation, #cdc_status, #coor_status, #internship_status').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // Save original full program study list
    const allProgramStudyOptions = $('#program_study').html();

    // When major changed → filter prodi list
    $('#major').on('change', function () {

        const selectedMajor = $(this).val();
        const $programStudy = $('#program_study');

        if (selectedMajor === 'All') {
            $programStudy.html(allProgramStudyOptions);
        } else {

            const temp = $('<select>' + allProgramStudyOptions + '</select>');
            const matchedGroup = temp.find(`optgroup[label="${selectedMajor}"]`).clone();

            $programStudy.html(`
                <option value="">- Select Study Program -</option>
                <option value="Major:${selectedMajor}">All Study Program in ${selectedMajor}</option>
            `);

            $programStudy.append(matchedGroup);
        }

        // re-init select2
        $programStudy.select2({ theme: 'bootstrap4', width: '100%' });
    });

    // Reset filter
    $('#resetButton').on('click', function() {
        $('#major').val('All').trigger('change');
        $('#program_study').val('').trigger('change');
        $('#generation').val('All').trigger('change');

        $('#cdc_status').val('All').trigger('change');
        $('#coor_status').val('All').trigger('change');
        $('#internship_status').val('All').trigger('change');

        $('#internshipLetterTable').DataTable().ajax.reload();
    });

    // On Filter Submit
    $('#form_filter').on('submit', function(e) {
        e.preventDefault();
        $('#internshipLetterTable').DataTable().ajax.reload();
    });

});
</script>


@endpush
