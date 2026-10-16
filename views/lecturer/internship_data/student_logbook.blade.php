@extends('templates.lecturer')


@push('head-scripts')

<!-- CSS Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">

<link rel='stylesheet' href='./core/component/snackbar/dist/snackbar.min.css' media='screen' title='no title' charset='utf-8'>

<style>
    #check-all.indeterminate~.custom-control-label::before {
        content: "-";
    }

</style>


<script src='./core/component/snackbar/dist/snackbar.min.js' charset='utf-8'></script>

<!-- Script untuk Snackbar -->
<script>
    // Fungsi untuk mendeteksi apakah layar adalah layar mobile
    function getSnackbarPosition() {
        // Jika lebar layar lebih kecil dari 768px (misalnya untuk layar mobile atau tablet kecil), gunakan posisi bottom-center
        return window.innerWidth <= 768 ? 'bottom-center' : 'top-right';
    }

    // Tampilkan Snackbar dengan spinner saat halaman mulai loading
    document.addEventListener("DOMContentLoaded", function() {
        Snackbar.show({
            text: '<i class="fas fa-spin fa-spinner" class="spinner"></i> Loading in progress, this may take a moment...'
            , pos: getSnackbarPosition(), // Panggil fungsi untuk menentukan posisi
            showAction: false
            , duration: 0 // Tampilkan terus sampai ditutup secara manual
        });
    });

    // Saat halaman selesai loading, ganti notifikasi dengan "Loading selesai"
    window.addEventListener("load", function() {
        Snackbar.show({
            text: 'Data loaded successfully.'
            , pos: getSnackbarPosition(), // Panggil fungsi untuk menentukan posisi
            showAction: true
            , duration: 3000 // Tampilkan selama 3 detik
        });

        // Tampilkan Snackbar baru dengan pesan "Loading selesai"
        setTimeout(function() {
            Snackbar.close(); // Tutup Snackbar loading
        }, 3000); // Tambahkan jeda sebelum menutup
    });

</script>




@endpush

@section('main-content')
@php
$nik_dosen = $_SESSION['nim_nik_unit'] ?? $_SESSION['Lecturer-Data']['nim_nik_unit'];
$result = array();

// Query untuk mendapatkan data dosen
$query = $_SERVER['PDO']->prepare("SELECT * FROM lecturer WHERE nim_nik_unit = :nim_nik_unit LIMIT 1");
$query->execute(['nim_nik_unit' => $nik_dosen]);
$lecturer = $query->fetch(PDO::FETCH_OBJ);

if (!$lecturer) {
// Redirect atau berikan notifikasi jika data dosen tidak ditemukan
// Contoh: header('Location: previous_page.php'); exit;
} else {
unset($lecturer->password);
}


# Query untuk mendapatkan data logbook mahasiswa yang dibimbing oleh dosen

// Tangkap nilai filter dari URL (GET parameter)
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'Yes'; // Default 'Yes' jika tidak ada filter

// Pastikan filter hanya memiliki nilai yang valid
$validFilters = ['Yes', 'Finish', 'All'];
if (!in_array($filter, $validFilters)) {
$filter = 'Yes'; // Jika filter tidak valid, gunakan default 'Yes'
}

// Jika filter 'All', hapus kondisi filter internship status
$statusCondition = '';
if ($filter !== 'All') {
$statusCondition = ' AND internship.status = :internship_status';
}

$orderBy = " ORDER BY CASE WHEN logbook.approval_dpm = 'Yes' THEN 1 WHEN logbook.approval_dpm = 'No' THEN 2 WHEN logbook.approval_dpm = 'Waiting' THEN 3 ELSE 4 END";


// Query dengan kondisi dinamis berdasarkan status
$sql = "SELECT student_internship.nim, student_internship.name, program_study.jurusan, program_study.prodi, logbook.*
FROM logbook INNER JOIN internship ON logbook.id_internship = internship.id_internship
INNER JOIN student_internship ON internship.nim = student_internship.nim
LEFT JOIN program_study ON program_study.kode_prodi = student_internship.program_study
WHERE student_internship.nik_dospem = :nik_dospem" . $statusCondition . $orderBy;


$query = $_SERVER['PDO']->prepare($sql);
// Eksekusi query dengan kondisi dinamis
$params = ['nik_dospem' => $nik_dosen];
if ($filter !== 'All') {
$params['internship_status'] = $filter;
}

$query->execute($params);

$logbook = $query->fetchAll(PDO::FETCH_OBJ);

// Memasukkan hasil query ke dalam array result
$result['lecturer'] = $lecturer;
$result['logbook'] = $logbook;

$response = $result;

@endphp

<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title">Student Logbook</h4>
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
                    <a href="#">Student Logbook</a>
                </li>





            </ul>
        </div>

        <div class="row">


            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Student Logbook
                            <div class="float-right justify-content-between">
                                <div class="btn-group btn-round">
                                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" disabled aria-expanded="false">
                                        Bulk Action
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                        <button class="dropdown-item text-info" id="bulk-action-btn">
                                            <i class="fas fa-check-circle mr-2"></i>Approval Logbook</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Logbook -->
                    <div class="card-body">

                        <div class="form-row">

                            <div class="form-group col-md-12">
                                <label for="internship-filter">Filter by Internship Status:</label>
                                <select id="internship-filter" class="form-control" disabled>
                                    <option value="Yes" <?= (!isset($_GET['filter']) || $_GET['filter'] === 'Yes') ? 'selected' : '' ?>>Only Ongoing Internship are Shown</option>
                                    <option value="Finish" <?= isset($_GET['filter']) && $_GET['filter'] === 'Finish' ? 'selected' : '' ?>>Only Finished Internship are Shown</option>
                                    <option value="All" <?= isset($_GET['filter']) && $_GET['filter'] === 'All' ? 'selected' : '' ?>>All Internship are Shown</option>
                                </select>
                            </div>



                            <div class="form-group col-md-12">
                                <label for="student-name-filter">Filter by Student Name:</label>
                                <select id="student-name-filter" class="form-control" disabled>
                                    <option value="">All</option>
                                </select>
                            </div>


                            <div class="form-group col-md-6">
                                <label for="approval-lecturer-filter">Filter by Approval Lecturer:</label>
                                <select id="approval-lecturer-filter" class="form-control" disabled>
                                    <option value="">All</option>
                                    <option>Waiting for Approval</option=>
                                    <option>Approved</option=>
                                    <option>Not Approved</option=>
                                </select>
                            </div>


                            <div class="form-group col-md-6">
                                <label for="approval-supervisor-filter">Filter by Approval Supervisor:</label>
                                <select id="approval-supervisor-filter" class="form-control" disabled>
                                    <option value="">All</option>
                                    <option>Waiting for Approval</option=>
                                    <option>Approved</option>
                                    <option>Not Approved</option=>


                                </select>
                            </div>
                        </div>

                        <table id="basic-datatables" class="table table-striped table-hover display">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="check-all" class="form-control"></th>
                                    <th>Student</th>
                                    <th>Week Num</th>
                                    <th>Logbook Period</th>
                                    <th>Approval Lecturer</th>
                                    <th>Approval Supervisor</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    foreach ($response['logbook'] as $i => $data) :
                                        $approval_spv = $data->approval_spv == 'Waiting' ? '<button class="btn btn-sm btn-warning">Waiting for Approval</button>' : ($data->approval_spv == 'Yes' ? '<button class="btn btn-sm btn-success">Approved</button>' : '<button class="btn btn-sm btn-danger">Not Approved</button>');
                                        $text_spv = $data->approval_spv == 'Waiting' ? 'Waiting for Approval' : ($data->approval_spv == 'Yes' ? 'Approved' : 'Not Approved');

                                        $approval_dpm = $data->approval_dpm == 'Waiting' ? '<button class="btn btn-sm btn-warning">Waiting for Approval</button>' : ($data->approval_dpm == 'Yes' ? '<button class="btn btn-sm btn-success">Approved</button>' : '<button class="btn btn-sm btn-danger">Not Approved</button>');
                                        $text_dpm = $data->approval_dpm == 'Waiting' ? 'Waiting for Approval' : ($data->approval_dpm == 'Yes' ? 'Approved' : 'Not Approved');

                                        $warna_btn = $data->approval_dpm == 'Waiting' ? "text-warning" : ($data->approval_dpm == 'Yes' ? "text-success" : "text-danger");
                                    ?>
                                <tr>
                                    <td><input type="checkbox" class="form-control" value="<?= $data->id_logbook ?>"></td>
                                    <td data-nim="<?= $data->nim ?>" data-jurusan="<?= $data->jurusan ?>" data-prodi="<?= $data->prodi ?>"><?= $data->name ?></td>

                                    <td>M<?= $data->week_num ?></td>
                                    <td><?= date('d M Y', strtotime($data->start_date)) . ' - ' . date('d M Y', strtotime($data->end_date)) ?></td>
                                    <td data-approval_dpm="<?= $text_dpm ?>" class="text-center"><?= $approval_dpm ?></td>
                                    <td data-approval_spv="<?= $text_spv ?>" class="text-center"><?= $approval_spv ?></td>
                                    <td>
                                        <div class="btn-group btn-round">
                                            <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Action
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                <button class="dropdown-item text-info" onclick="window.location.href = 'index.php?page=intern_student_logbook_detail&nim=<?= base64_encode($data->nim) ?>&id_internship=<?= base64_encode($data->id_internship) ?>&id_logbook=<?= base64_encode($data->id_logbook) ?>'">
                                                    <i class="fas fa-info-circle mr-2"></i>Detail Logbook</button>
                                                <div class="dropdown-divider"></div>
                                                {{-- <button class="dropdown-item <?= $warna_btn ?>" data-toggle="modal" data-target="#ModalApproval" data-id_logbook="<?= $data->id_logbook ?>" data-nim="<?= $data->nim ?>">
                                                    <i class="fas fa-check-circle mr-2"></i>Approval Logbook
                                                </button> --}}
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            <div class="modal fade" id="ModalApprovalBulk">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Bulk Approval
                                <span class="fw-mediumbold">
                                    Logbook</span>
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <div class="form-group">
                                <label for="approval_dpm">Approval Lecturer<label class="text-danger">*</label></label>
                                <select class="form-control" name="approval_dropdown" id="approval-dropdown">
                                    <option hidden value=''>- Choose One -</option>

                                    <option>Yes</option>
                                    <option>No</option>
                                </select>
                                <?= form_error('approval_dpm'); ?>
                            </div>


                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Close</button>
                            <button type="button" id="save-approval-btn" class="btn btn-primary"><i class="fas fa-check-double mr-1"></i>Bulk Approval</button>
                        </div>

                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>

            <div class="modal fade" id="ModalApproval">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Approval Logbook</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="index.php?form=logbook_approval&action=lecturer" method="POST">
                                <?= csrf() ?>
                                <input type="hidden" name="id_logbook" id="id_logbook">
                                <input type="hidden" name="nim" id="nim">
                                <input type="hidden" name="nik" value="<?= $_SESSION['nim_nik_unit'] ?>">
                                <div class="form-group">
                                    <label for="approval">Approval Status</label>
                                    <select class="form-control" name="approval" id="approval" required>
                                        <option value="Yes">Approved</option>
                                        <option value="No">Not Approved</option>
                                        <option value="Waiting">Waiting for Approval</option>
                                    </select>
                                </div>
                                <div class="modal-footer justify-content-between">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-1"></i>Close</button>
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-check mr-1"></i>Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>





        </div>
    </div>
</div>
@endsection

@push('footer-scripts')
<!-- Datatables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

<!-- Datatables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

<!-- Soloalert -->
<script src='./core/component/soloalert.js'></script>

<!-- JS Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>


<script>
    $(document).ready(function() {

        $('#student-name-filter').select2({
            theme: 'bootstrap4'
        });

        let _token = $('meta[name="csrf-token"]').attr('content');

        let table = $('#basic-datatables').DataTable({
            "responsive": true
            , "pageLength": 25
            , "stateSave": false // Aktifkan state saving
            , "lengthMenu": [10, 25, 50, 100]
            , "columnDefs": [{
                    targets: 1, // Kolom kedua (nama)
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).attr('data-nim', rowData.nim); // Tambahkan atribut data-nim
                    }
                },

                {
                    targets: 4, // Kolom kedua (nama)
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).attr('data-approval_dpm', rowData.approval_dpm); // Tambahkan atribut data-nim
                    }
                }
                , {
                    targets: 5, // Kolom kedua (nama)
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).attr('data-approval_spv', rowData.approval_spv); // Tambahkan atribut data-nim
                    }
                }
            , ]
            , "order": [
                [1, 'asc']
            ], // Urutkan berdasarkan kolom kedua
        });


        function disableAllElements() {
            $('select, input[type="checkbox"], button').prop('disabled', true);
        }

        function enableAllElements() {
            $('select, input[type="checkbox"], button').prop('disabled', false);
        }



        $('#internship-filter').on('change', function() {
            // Dapatkan nilai filter yang dipilih
            let filterValue = $(this).val();

            // Jika memilih 'All', tampilkan peringatan
            if (filterValue === 'All') {
                Swal.fire({
                    icon: 'warning'
                    , title: 'Warning'
                    , text: 'You have selected "All". Displaying all internship data may result in slow performance due to the large amount of data.'
                    , showCancelButton: true
                    , confirmButtonText: 'Proceed'
                    , cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Jika pengguna menekan "Proceed", arahkan ke halaman dengan filter 'All'
                        window.location.href = 'index.php?page=logbook_student&filter=All';
                    } else {
                        // Jika pengguna menekan "Cancel", reset dropdown ke pilihan sebelumnya
                        $(this).val('Yes');
                    }
                });
            } else {
                // Jika memilih filter lain selain 'All', langsung arahkan ke halaman dengan filter
                window.location.href = 'index.php?page=logbook_student&filter=' + filterValue;
            }
        });


        setTimeout(function() {

            var groupedStudents = {};
            table.rows().every(function(rowIdx, tableLoop, rowLoop) {
                var data = this.data();
                var $row = $(this.node());
                var prodi = $row.find('td[data-prodi]').data('prodi');
                var nim = $row.find('td[data-nim]').data('nim');
                var name = $row.find('td[data-nim]').text();

                // Jika belum ada kelompok untuk prodi ini, buat objek baru
                if (!groupedStudents[prodi]) {
                    groupedStudents[prodi] = {};
                }

                // Tambahkan mahasiswa ke dalam objek berdasarkan nim (untuk menghindari duplikasi)
                groupedStudents[prodi][nim] = name;
            });

            // Buat opsi dalam dropdown berdasarkan kelompok prodi
            var $dropdown = $('#student-name-filter');
            $.each(groupedStudents, function(prodi, students) {
                var $optgroup = $('<optgroup>').attr('label', prodi);
                $.each(students, function(nim, name) {
                    var $option = $('<option>').val(name).text(nim + ' | ' + name);
                    $optgroup.append($option);
                });
                $dropdown.append($optgroup);
            });

            enableAllElements();

        }, 2000);


        $('#approval-lecturer-filter').on('change', function() {

            var selectedValue = $(this).val();

            // Filter kolom Company (kolom index 1)
            if (selectedValue) {
                table.column(4).search('^' + selectedValue + '$', true, false).draw();
            } else {
                table.column(4).search('').draw();
            }

            updateCheckAllStatus();

        });

        $('#approval-supervisor-filter').on('change', function() {

            var selectedValue = $(this).val();

            // Filter kolom Company (kolom index 1)
            if (selectedValue) {
                table.column(5).search('^' + selectedValue + '$', true, false).draw();
            } else {
                table.column(5).search('').draw();
            }

            updateCheckAllStatus();

        });


        $('#ModalApproval').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Tombol yang diklik
            var id_logbook = button.data('id_logbook');
            var nim = button.data('nim');

            // Masukkan data ke dalam modal
            var modal = $(this);
            modal.find('#id_logbook').val(id_logbook);
            modal.find('#nim').val(nim);
        });



        // Filter berdasarkan NIM menggunakan dropdown
        $('#student-name-filter').on('change', function() {

            var selectedValue = $(this).val();

            // Filter kolom Company (kolom index 1)
            if (selectedValue) {
                table.column(1).search('^' + selectedValue + '$', true, false).draw();
            } else {
                table.column(1).search('').draw();
            }

            updateCheckAllStatus();

        });


        // Menangani checkbox individual
        $('#basic-datatables').on('change', 'tbody input[type="checkbox"]', function() {
            updateCheckAllStatus();
        });


        // Untuk bulk-action-btn
        $('#bulk-action-btn').on('click', function() {
            var selectedIds = [];

            // Ambil semua baris dari semua halaman dan cek apakah checkbox tercentang
            table.rows().every(function() {
                var checkbox = $(this.node()).find('input[type="checkbox"]');
                if (checkbox.prop('checked')) {
                    selectedIds.push(checkbox.val());
                }
            });

            if (selectedIds.length === 0) {
                Swal.fire({
                    icon: 'warning'
                    , title: 'No Selection'
                    , text: 'Please select at least one item.'
                    , confirmButtonText: 'OK'
                });
            } else {
                $('#ModalApprovalBulk').modal('show');
            }
        });

        // Untuk save-approval-btn
        $('#save-approval-btn').on('click', function() {
            var selectedIds = [];
            var approvalValue = $('#approval-dropdown').val();

            // Disable tombol dan tambahkan loading indicator
            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Processing...');

            // Ambil semua baris dari semua halaman dan cek apakah checkbox tercentang
            table.rows().every(function() {
                var checkbox = $(this.node()).find('input[type="checkbox"]');
                if (checkbox.prop('checked')) {
                    selectedIds.push(checkbox.val());
                }
            });

            $.ajax({
                url: 'index.php?request=bulk_approval_logbook&action=lecturer'
                , method: 'POST'
                , data: {
                    token: _token
                    , _method: 'PUT'
                    , ids: selectedIds
                    , approval: approvalValue
                }
                , success: function(response) {
                    $('#ModalApprovalBulk').modal('hide');
                    window.location.reload();
                }
                , error: function(xhr, status, error) {
                    window.location.reload();
                }
            });
        });

        // Untuk toggle select all
        $('#check-all').on('change', function() {
            let checked = $(this).prop('checked');
            let checkboxes = table.rows({
                search: 'applied'
            }).nodes().to$().find('input[type="checkbox"]');

            // Set all checkboxes to the same state as 'check-all'
            checkboxes.prop('checked', checked);

            // Cek apakah aksi sebelumnya adalah centang atau uncentang
            // Jika 'check-all' diset ke checked (true), baru cek apakah ada yang dipilih
            if (checked) {
                // Jika tidak ada checkbox yang tercentang setelah check-all di centang
                if (!checkboxes.is(':checked')) {
                    Swal.fire({
                        icon: 'warning'
                        , title: 'No Data Selected'
                        , text: 'There are no selected data to process!'
                        , confirmButtonText: 'OK'
                    });

                    // Pastikan check-all kembali unchecked jika tidak ada data yang dipilih
                    $(this).prop('checked', false);
                }
            }
        });



        function updateCheckAllStatus() {
            let checkboxes = table.rows({
                search: 'applied'
            }).nodes().to$().find('input[type="checkbox"]');

            let totalCheckboxes = checkboxes.length;
            let checkedCheckboxes = checkboxes.filter(':checked').length;

            // Tentukan status dari checkbox "check-all"
            if (checkedCheckboxes === totalCheckboxes && totalCheckboxes > 0) {
                $('#check-all').prop('checked', true).prop('indeterminate', false); // Semua tercentang
            } else if (checkedCheckboxes === 0) {
                $('#check-all').prop('checked', false).prop('indeterminate', false); // Tidak ada yang tercentang
            } else {
                $('#check-all').prop('checked', false).prop('indeterminate', true); // Sebagian tercentang
            }
        }


    });

</script>



@if (old('ModalApproval') !== NULL):
<script>
    var counter = 1;
    $('#ModalApproval' + counter).modal('show');

</script>
@endif

@endpush
