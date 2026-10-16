@php
$extends_template = (in_array($_SESSION['role'], ['UPKPK', 'CDC'])) ? 'templates.cdc' : 'templates.lecturer';
$routes = (in_array($_SESSION['role'], ['UPKPK', 'CDC'])) ? 'intern_student_list' : 'student_intern_management';
$redirectPage = (in_array($_SESSION['role'], ['UPKPK', 'CDC'])) ? 'intern_student_list' : 'student_intern_management';
@endphp
@extends( $extends_template , ['title' => 'Import Student', 'routes' => $routes, 'route' => $routes ])

@push('head-scripts')
<!-- CSS Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">

<!-- Datatables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

<!-- DataTables Buttons CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
<style>
    #check-all.indeterminate~.custom-control-label::before {
        content: "-";
    }

    .dt-buttons {
        display: none;
    }

</style>



@php
$id_kampus = (in_array($_SESSION['role'], ['UPKPK', 'CDC'])) ? $_SESSION['CDC-Data'] ['id_kampus'] : $_SESSION['Lecturer-Data'] ['id_kampus'];

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


$sql4 = "SELECT * FROM lecturer WHERE id_kampus = :id_kampus";
$stmt4 = $pdo->prepare($sql4);
$stmt4->execute(['id_kampus' => $id_kampus]);

$daftar_dosen = $stmt4->fetchAll(PDO::FETCH_ASSOC);


$prodi_koor = $_SESSION['Lecturer-Data'] ['prodi_koor'];
@endphp
@endpush

@section('main-content')
<div class="container">

    <div class="page-inner mt-2">

        <div class="page-header">
            <h4 class="page-title">Import Student</h4>
            <ul class="breadcrumbs">
                <li class="nav-home">
                    <a href="index.php">
                        <i class="flaticon-home"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="flaticon-right-arrow"></i>
                </li>
                <li class="nav-item">
                    <a href="index.php?page=<?= ($_SESSION['role'] == 'Dosen') ? 'student_intern_management' : 'intern_student_list'; ?>">Students Management</a>
                </li>
                <li class="separator">
                    <i class="flaticon-right-arrow"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Import Student</a>
                </li>
            </ul>
        </div>

        <div class="row mt-2">

            <div class="col-md-12">


                <!-- Import Student Card -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Import Student
                            <a data-toggle="tooltip" data-placement="right" data-original-title="Download Excel Template" target="_blank" href="<?= base_url('viewer.php?src=excel_template&location=' . base64_encode('import_student.xlsx')) ?>" class="btn btn-primary btn-sm pull-right text-white">
                                <i class="fas fa-file-excel mr-2"></i>Template
                            </a>
                            <a data-toggle="modal" data-target="#ModalHelper" class="btn btn-info btn-sm pull-right text-white mr-2">
                                <i class="fas fa-question-circle mr-2"></i>Help
                            </a>
                        </h4>
                    </div>

                    <form id="preview" method="POST" enctype="multipart/form-data">
                        <?= csrf() ?> <?= input_hidden('id_kampus', $id_kampus) ?>
                        <div class="card-body form-row">
                            <div class="form-group col-md-12">
                                <label>File Excel Import <b class="text-danger">*</b></label>
                                <input type="file" id="file" class="form-control" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" name="file" required />
                            </div>
                        </div>

                        <div class="card-footer d-flex flex-wrap">
                            <button type="button" id="btnCancelForm" class="btn btn-danger btn-sm mr-2" data-redirect="{{ base_url('index.php?page=' . $redirectPage) }}">
                                <i class="fas fa-times mr-1"></i>Cancel
                            </button>

                            <button type="button" id="btnResetForm" class="btn btn-warning btn-sm mr-2">
                                <i class="fas fa-undo mr-1"></i>Reset
                            </button>

                            <button type="submit" id="btnPreviewStudent" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye mr-2"></i>Preview Data
                            </button>
                        </div>


                    </form>
                </div>



                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            Preview Student Data before Importing
                        </h4>

                        <div class="btn-group">
                            <button type="button" id="btnClosePreview" class="btn btn-danger btn-sm mr-2 d-none">
                                <i class="fas fa-times mr-1"></i> Close Preview
                            </button>

                            <button type="button" id="btnResetPreview" class="btn btn-warning btn-sm mr-2 d-none">
                                <i class="fas fa-undo mr-1"></i> Reset Preview
                            </button>

                            <button type="submit" id="btnImportStudent" class="btn btn-success btn-sm d-none">
                                <i class="fas fa-file-import mr-1"></i> Import Data
                            </button>
                        </div>
                    </div>


                    <div class="card-body ">


                        <div class="form-group col-md-12">
                            <label>Program Study <b class="text-danger">*</b></label>


                            <select id="kode_program_study" name="kode_program_study" class="form-control <?= show_error('kode_program_study') ?>">
                                <option value="">- Select Study Program -</option>
                                @foreach ($daftar_major as $group)
                                <optgroup label="{{ htmlspecialchars($group['major']) }}" id="{{ 'group-' . htmlspecialchars($group['major']) }}">
                                    @foreach ($group['programs'] as $prodi)
                                    <option value="{{ htmlspecialchars($prodi['kode_prodi']) }}" {{ ( old('kode_program_study', $prodi_koor ) == $prodi['kode_prodi'] ) ? 'selected' : ''; }}>
                                        {{ htmlspecialchars($prodi['jenjang']) }} {{ htmlspecialchars($prodi['prodi']) }} -
                                        ({{ htmlspecialchars($prodi['jenjang']) }} {{ htmlspecialchars($prodi['study_program']) }})
                                    </option>
                                    @endforeach
                                </optgroup>
                                @endforeach
                            </select>



                            <?= form_error('kode_program_study') ?>
                        </div>
                        <div class="form-group col-md-12">
                            <table class="table table-bordered table-responsive" id="tablePreviewStudent">
                                <thead>
                                    <tr>
                                        <th width="5%">Action</th>
                                        <th>Import Status</th>
                                        <th>NIM</th>
                                        <th>Student Name</th>
                                        <th>Lecturer</th>
                                        <th>Password</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td colspan="100%" class="text-center">No data to preview</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>


                    </div>

                </div>

                <!-- Modal Helper -->
                <div class="modal fade" id="ModalHelper" tabindex="-1" role="dialog" aria-labelledby="ModalHelperLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="ModalHelperLabel">
                                    <i class="fas fa-info-circle mr-2"></i>Help with Student Import
                                </h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">

                                <p class="mb-2"><strong>📘 Student Import Guidelines:</strong></p>
                                <ul class="pl-3">
                                    <li>Use the provided <strong>Excel template</strong> (<code>.xlsx</code> format).</li>
                                    <li>Required columns: <strong>Student ID (NIM)</strong>, <strong>Student Name</strong>.</li>
                                    <li>To assign a supervisor, fill in <strong>Lecturer ID</strong> and <strong>Name</strong>.</li>
                                    <li>Existing students won't be imported again — only <strong>name</strong> and <strong>supervisor</strong> will be updated.</li>
                                    <li>If the lecturer isn't registered, the system will auto-create based on the given ID & Name.</li>
                                </ul>

                                <hr>

                                <p class="mb-2"><strong>📝 Notes:</strong></p>
                                <ul class="pl-3">
                                    <li>Avoid blank rows in the middle of the Excel file.</li>
                                    <li>Student's default password is their Student ID.</li>
                                    <li>Be sure to select a <strong>Study Program</strong> before importing.</li>
                                    <li>The <strong>Reset</strong> button restores the original preview data.</li>
                                </ul>

                                <hr>
                                <p class="mb-0">
                                    ❓For any issues, please contact the internship or CDC administrator.
                                </p>

                                <hr class="mt-4 mb-4">

                                <h5><i class="fas fa-language mr-2"></i>Bantuan Impor Mahasiswa</h5>

                                <p class="mb-2"><strong>📘 Panduan:</strong></p>
                                <ul class="pl-3">
                                    <li>Gunakan <strong>template Excel</strong> yang disediakan (format <code>.xlsx</code>).</li>
                                    <li>Kolom yang wajib diisi: <strong>NIM</strong>, <strong>Nama Mahasiswa</strong>.</li>
                                    <li>Jika menetapkan dosen pembimbing, isi <strong>NIK Dosen</strong> dan <strong>Nama Dosen</strong>.</li>
                                    <li>Data yang sudah terdaftar tidak akan diimpor ulang, hanya akan diperbarui (nama & dospem).</li>
                                    <li>Jika dosen belum terdaftar, sistem akan menambahkannya otomatis berdasarkan NIK & Nama.</li>
                                </ul>

                                <hr>

                                <p class="mb-2"><strong>📝 Catatan:</strong></p>
                                <ul class="pl-3">
                                    <li>Hindari baris kosong di tengah file Excel.</li>
                                    <li>Password default mahasiswa adalah sesuai dengan NIM.</li>
                                    <li>Pastikan memilih <strong>Program Studi</strong> sebelum melakukan import.</li>
                                    <li>Tombol <strong>Reset</strong> akan mengembalikan data preview ke kondisi awal.</li>
                                </ul>

                                <hr>
                                <p class="mb-0">
                                    ❓Jika mengalami kendala, silakan hubungi pengelola magang atau CDC.
                                </p>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                                    <i class="fas fa-times mr-1"></i> Close / Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>




            </div>
        </div>
    </div>
</div>
@endsection

@push('footer-scripts')

<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script src='{{ base_url('assets/sweetalert2.all.js') }}'></script>
<script src='{{ base_url('core/component/soloalert.js') }}'></script>

<script>
    let _token = $('meta[name="csrf-token"]').attr('content');
    window.studentPreviewData = [];
    window.originalStudentPreviewData = [];




    $(document).ready(function() {

        $('#btnCancelForm').on('click', function() {
            const redirectUrl = $(this).data('redirect') || 'index.php';

            Swal.fire({
                title: 'Cancel Import?'
                , text: 'Are you sure you want to cancel the import process?'
                , icon: 'warning'
                , showCancelButton: true
                , confirmButtonText: 'Yes, Cancel'
                , cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = redirectUrl;
                }
            });
        });


        $('#btnResetForm').on('click', function() {
            if (!window.originalStudentPreviewData || window.originalStudentPreviewData.length === 0) {
                Swal.fire('No Data', 'There is no original preview data to reset.', 'info');
                return;
            }

            Swal.fire({
                title: 'Reset Preview?'
                , text: 'This will undo all row deletions or changes and restore the original preview.'
                , icon: 'question'
                , showCancelButton: true
                , confirmButtonText: 'Yes, Reset'
                , cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.studentPreviewData = [...window.originalStudentPreviewData];
                    renderStudentPreviewTable(window.originalStudentPreviewData);
                }
            });
        });


        $('#kode_program_study').select2({
            theme: 'bootstrap4'
            , width: '100%'
        });

        // Submit Preview
        $('#preview').on('submit', function(e) {
            e.preventDefault();
            loadStudentPreview(this);
        });

        // Close Preview
        $('#btnClosePreview').on('click', function() {
            Swal.fire('Preview Closed', 'You have closed the preview. Your data is still safe and can be imported.', 'info');
            resetPreviewUI();
        });

        // Reset Preview
        $('#btnResetPreview').on('click', function() {
            if (!window.originalStudentPreviewData.length) {
                Swal.fire('No Data', 'There is no original preview data to reset.', 'info');
                return;
            }

            Swal.fire({
                title: 'Reset Preview?'
                , text: 'This will undo all row deletions and restore the original preview.'
                , icon: 'warning'
                , showCancelButton: true
                , confirmButtonText: 'Yes, Reset'
                , cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.studentPreviewData = [...window.originalStudentPreviewData];
                    renderStudentPreviewTable(window.originalStudentPreviewData);

                    $('#btnImportStudent, #btnResetPreview, #btnClosePreview').removeClass('d-none');

                }
            });
        });

        // Import Data
        $('#btnImportStudent').on('click', function() {
            if (!window.studentPreviewData.length) {
                Swal.fire('No Data', 'There is no data to import.', 'info');
                return;
            }

            const kode_prodi = $('#kode_program_study').val();
            if (!kode_prodi) {
                Swal.fire('Warning', 'Please select a Study Program first.', 'warning');
                return;
            }

            // Ambil data yang masih ditampilkan di tabel
            const filteredData = [];
            $('#tablePreviewStudent tbody tr').each(function() {
                // const index = $(this).attr('id') ? .replace('row-', '');
                const rowId = $(this).attr('id');
                const index = rowId ? rowId.replace('row-', '') : null;


                const student = window.studentPreviewData[index];
                if (student) filteredData.push(student);
            });

            if (!filteredData.length) {
                Swal.fire('No Data', 'All rows have been removed.', 'info');
                return;
            }

            Swal.fire({
                title: 'Are you sure?'
                , html: `
                    <div style="text-align: left;">
                        <p><strong>Total Students:</strong> ${filteredData.length}</p>
                        <p><strong>Note:</strong> Only <span style="color:green;">visible data</span> in the table will be imported.</p>
                        <p>Please confirm that all the data is <b>final and correct</b>.</p>
                    </div>`
                , icon: 'warning'
                , showCancelButton: true
                , confirmButtonText: 'Yes, Import Data'
                , cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('token', _token);
                    formData.append('kode_program_study', kode_prodi);

                    filteredData.forEach((student, i) => {
                        for (const key in student) {
                            formData.append(`data[${i}][${key}]`, student[key]);
                        }
                    });

                    $('#btnImportStudent').prop('disabled', true).html(`<i class='fas fa-spinner fa-spin mr-1'></i> Importing...`);

                    // console.log( formData );

                    for (let pair of formData.entries()) {
                        console.log(pair[0] + ': ' + pair[1]);
                    }

                    $.ajax({
                        url: 'index.php?form=import_student'
                        , method: 'POST'
                        , data: formData
                        , contentType: false
                        , processData: false
                        , success: function(res) {
                            if (res.success) {
                                Swal.fire('Imported!', res.message, 'success');
                                resetPreviewUI();
                                window.studentPreviewData = [];
                                window.originalStudentPreviewData = [];
                            } else {
                                Swal.fire('Error', res.message || 'Import failed.', 'error');
                            }
                        }
                        , error: function(xhr) {
                            console.error(xhr.responseText);
                            Swal.fire('Error', 'Failed to import data. Please try again later.', 'error');
                        }
                        , complete: function() {
                            $('#btnImportStudent').prop('disabled', false).html(`<i class='fas fa-file-import mr-1'></i> Import Data`);
                        }
                    });
                }
            });
        });
    });

    function loadStudentPreview(formElement) {
        const $btn = $('#btnPreviewStudent');
        const originalHTML = $btn.html();

        $btn.html(`<i class='fas fa-spinner fa-spin mr-2'></i>Loading...`).prop('disabled', true);
        $('#tablePreviewStudent tbody').html(`<tr><td colspan='100%' class='text-center'><i class='fas fa-spinner fa-spin'></i> Loading preview...</td></tr>`);

        $.ajax({
            url: 'index.php?form=preview_import_student'
            , method: 'POST'
            , data: new FormData(formElement)
            , contentType: false
            , cache: false
            , processData: false
            , success: function(res) {
                const data = res.data || [];

                if (!data.length) {
                    $('#tablePreviewStudent tbody').html("<tr><td colspan='100%' class='text-center'>No data to preview</td></tr>");
                    $('#btnImportStudent, #btnResetPreview, #btnClosePreview').addClass('d-none');
                    return;
                }

                window.studentPreviewData = [...data];
                window.originalStudentPreviewData = [...data];

                renderStudentPreviewTable(data);
                $('#btnImportStudent, #btnResetPreview, #btnClosePreview').removeClass('d-none');
            }
            , error: function() {
                $('#tablePreviewStudent tbody').html("<tr><td colspan='100%' class='text-danger text-center'>Failed to load data</td></tr>");
            }
            , complete: function() {
                $btn.html(originalHTML).prop('disabled', false);
            }
        });
    }

    function renderStudentPreviewTable(data) {
        const rows = data.map((item, i) => {
            const rowClass = item.import_status === true ? 'bg-success text-white' : item.import_status === false ? 'bg-danger text-white' : 'bg-warning text-white';

            const lecturer = item.lecturer_nik && item.lecturer_name ?
                `${item.lecturer_nik} - ${item.lecturer_name}<br><span class='badge badge-${item.lecturer_status ? 'success' : 'danger'}'>${item.lecturer_status_message}</span>` :
                '<span class="text-danger">No Lecturer</span>';

            return `
                <tr id='row-${i}'>
                    <td><button type='button' class='btn btn-sm btn-danger' onclick='removeStudentPreviewRow(${i})'><i class='fas fa-trash'></i></button></td>
                    <td class='${rowClass}'>${item.import_review}</td>
                    <td>${item.student_nim}</td>
                    <td>${item.student_name}</td>
                    <td>${lecturer}</td>
                    <td>${item.student_password}</td>
                </tr>`;
        }).join('');

        $('#tablePreviewStudent tbody').html(rows);
    }

    function removeStudentPreviewRow(index) {
        Swal.fire({
            title: 'Remove Row?'
            , text: 'Are you sure you want to remove this student from the preview list?'
            , icon: 'warning'
            , showCancelButton: true
            , confirmButtonColor: '#d33'
            , confirmButtonText: 'Yes, remove it'
            , cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $(`#row-${index}`).remove();

                if ($('#tablePreviewStudent tbody tr').length === 0) {
                    $('#tablePreviewStudent tbody').html("<tr><td colspan='100%' class='text-center'>No data to preview</td></tr>");
                    $('#btnImportStudent').addClass('d-none');
                    // $('#btnResetPreview, #btnClosePreview').addClass('d-none');
                }
            }
        });
    }

    function resetPreviewUI() {
        $('#tablePreviewStudent tbody').html("<tr><td colspan='100%' class='text-center'>No data to preview</td></tr>");
        $('#btnImportStudent, #btnResetPreview, #btnClosePreview').addClass('d-none');
        window.studentPreviewData = [];
    }

</script>


{{-- <script>
    let _token = $('meta[name="csrf-token"]').attr('content');

    window.addEventListener('DOMContentLoaded', () => {
        $('#kode_program_study').select2({
            theme: 'bootstrap4'
            , width: '100%'
        });

        $('#preview').on('submit', function(e) {
            e.preventDefault();
            loadStudentPreview(this);
        });

        $('#btnClosePreview').on('click', function() {
            Swal.fire('Preview Closed', 'You have closed the preview. Your data is still safe and can be imported.', 'info');
            $('#tablePreviewStudent tbody').html("<tr><td colspan='100%' class='text-center'>No data to preview</td></tr>");
            $('#btnImportStudent').addClass('d-none');
        });
    });

    function loadStudentPreview(formElement) {
        const $btn = $('#btnPreviewStudent');
        const originalHTML = $btn.html();

        $btn.html(`<i class='fas fa-spinner fa-spin mr-2'></i>Loading...`).prop('disabled', true);

        const spinnerRow = `<tr><td colspan='100%' class='text-center'><i class='fas fa-spinner fa-spin'></i> Loading preview...</td></tr>`;
        $('#tablePreviewStudent tbody').html(spinnerRow);

        $.ajax({
            url: 'index.php?form=preview_import_student'
            , method: 'POST'
            , data: new FormData(formElement)
            , contentType: false
            , cache: false
            , processData: false
            , success: function(res) {
                const data = res.data || [];

                if (data.length === 0) {
                    $('#tablePreviewStudent tbody').html("<tr><td colspan='100%' class='text-center'>No data to preview</td></tr>");
                    $('#btnImportStudent').addClass('d-none');
                } else {
                    let rows = data.map((item, i) => {
                        const rowClass = item.import_status === true ? 'bg-success text-white' :
                            item.import_status === false ? 'bg-danger text-white' : 'bg-warning text-white';

                        const lecturer = item.lecturer_nik && item.lecturer_name ?
                            `${item.lecturer_nik} - ${item.lecturer_name}<br><span class='badge badge-${item.lecturer_status ? 'success' : 'danger'}'>${item.lecturer_status_message}</span>` :
                            '<span class="text-danger">No Lecturer</span>';

                        return `<tr id='row-${i}'>
                        <td><button type='button' class='btn btn-sm btn-danger' onclick='removeStudentPreviewRow(${i})'><i class='fas fa-trash'></i></button></td>
                        <td class='${rowClass}'>${item.import_review}</td>
                        <td>${item.student_nim}</td>
                        <td>${item.student_name}</td>
                        <td>${lecturer}</td>
                        <td>${item.student_password}</td>
                    </tr>`;
                    }).join('');

                    $('#tablePreviewStudent tbody').html(rows);
                    $('#btnImportStudent').removeClass('d-none');
                    $('#btnResetPreview').removeClass('d-none');
                    $('#btnClosePreview').removeClass('d-none');

                    window.studentPreviewData = data;
                }
            }
            , error: function() {
                $('#tablePreviewStudent tbody').html("<tr><td colspan='100%' class='text-danger text-center'>Failed to load data</td></tr>");
            }
            , complete: function() {
                $btn.html(originalHTML).prop('disabled', false);
            }
        });
    }

    function renderStudentPreviewTable(data) {
        let rows = data.map((item, i) => {
            const rowClass = item.import_status === true ? 'bg-success text-white' :
                item.import_status === false ? 'bg-danger text-white' : 'bg-warning text-white';

            const lecturer = item.lecturer_nik && item.lecturer_name ?
                `${item.lecturer_nik} - ${item.lecturer_name}<br><span class='badge badge-${item.lecturer_status ? 'success' : 'danger'}'>${item.lecturer_status_message}</span>` :
                '<span class="text-danger">No Lecturer</span>';

            return `<tr id='row-${i}'>
            <td><button type='button' class='btn btn-sm btn-danger' onclick='removeStudentPreviewRow(${i})'><i class='fas fa-trash'></i></button></td>
            <td class='${rowClass}'>${item.import_review}</td>
            <td>${item.student_nim}</td>
            <td>${item.student_name}</td>
            <td>${lecturer}</td>
            <td>${item.student_password}</td>
        </tr>`;
        }).join('');

        $('#tablePreviewStudent tbody').html(rows);
    }

    $('#btnResetPreview').on('click', function() {
        if (!window.originalStudentPreviewData || window.originalStudentPreviewData.length === 0) {
            Swal.fire('No Data', 'There is no original preview data to reset.', 'info');
            return;
        }

        Swal.fire({
            title: 'Reset Preview?'
            , text: 'This will undo all row deletions or changes and restore the original preview.'
            , icon: 'warning'
            , showCancelButton: true
            , confirmButtonText: 'Yes, Reset'
            , cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.studentPreviewData = [...window.originalStudentPreviewData];
                renderStudentPreviewTable(window.originalStudentPreviewData);
            }
        });
    });




    function removeStudentPreviewRow(index) {
        Swal.fire({
            title: 'Remove Row?'
            , text: 'Are you sure you want to remove this student from the preview list?'
            , icon: 'warning'
            , showCancelButton: true
            , confirmButtonColor: '#d33'
            , confirmButtonText: 'Yes, remove it'
            , cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $(`#row-${index}`).remove();
                if ($('#tablePreviewStudent tbody tr').length === 0) {
                    $('#tablePreviewStudent tbody').html("<tr><td colspan='100%' class='text-center'>No data to preview</td></tr>");
                    $('#btnImportStudent').addClass('d-none');
                }
            }
        });
    }


    $('#btnImportStudent').on('click', function() {
        if (!window.studentPreviewData || window.studentPreviewData.length === 0) {
            Swal.fire('No Data', 'There is no data to import.', 'info');
            return;
        }

        const kode_prodi = $('#kode_program_study').val();
        if (!kode_prodi) {
            Swal.fire('Warning', 'Please select a Study Program first.', 'warning');
            return;
        }

        const filteredData = [];
        $('#tablePreviewStudent tbody tr').each(function() {
            const index = $(this).attr('id').replace('row-', '');
            const student = window.studentPreviewData[index];
            if (student) {
                filteredData.push(student);
            }
        });

        if (filteredData.length === 0) {
            Swal.fire('No Data', 'All rows have been removed.', 'info');
            return;
        }

        Swal.fire({
            title: 'Are you sure?'
            , html: `
            <div style="text-align: left;">
                <p><strong>Total Students:</strong> ${filteredData.length}</p>
                <p><strong>Note:</strong> Only <span style="color:green;">visible data</span> in the table will be imported.</p>
                <p>Please confirm that all the data is <b>final and correct</b>.</p>
            </div>`
            , icon: 'warning'
            , showCancelButton: true
            , confirmButtonText: 'Yes, Import Data'
            , cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('token', _token);
                formData.append('kode_program_study', kode_prodi);

                // Tambahkan data dengan struktur data[][key]
                filteredData.forEach(student => {
                    for (const key in student) {
                        formData.append(`data[][${key}]`, student[key]);
                    }
                });

                $.ajax({
                    url: 'index.php?form=import_student'
                    , method: 'POST'
                    , data: formData
                    , contentType: false
                    , processData: false
                    , beforeSend: function() {
                        $('#btnImportStudent').prop('disabled', true);
                    }
                    , success: function(res) {
                        if (res.success) {
                            Swal.fire('Imported!', res.message, 'success');
                            $('#tablePreviewStudent tbody').html("<tr><td colspan='100%' class='text-center'>No data to preview</td></tr>");
                            $('#btnImportStudent').addClass('d-none');
                            window.studentPreviewData = [];
                        } else {
                            Swal.fire('Error', res.message || 'Import failed.', 'error');
                        }
                    }
                    , error: function(xhr, status, err) {
                        console.error(xhr.responseText || err);
                        Swal.fire('Error', 'Failed to import data. Please try again later.', 'error');
                    }
                    , complete: function() {
                        $('#btnImportStudent').prop('disabled', false);
                    }
                });
            }
        });
    });

</script> --}}

@endpush
