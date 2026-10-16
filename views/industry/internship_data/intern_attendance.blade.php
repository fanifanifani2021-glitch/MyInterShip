@extends('templates.industry')


@push('head-scripts')

<!-- CSS Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
<style>
    #check-all.indeterminate~.custom-control-label::before {
        content: "-";
    }

</style>


@endpush

@section('main-content')
@php

$id_user_company = $_SESSION['id_user_company'];
$result = array();


try {
    // Query untuk mendapatkan data user_company
    $query = $_SERVER['PDO']->prepare("SELECT * FROM user_company WHERE id_user_company = :id_user_company LIMIT 1");
    $query->execute(['id_user_company' => $id_user_company]);
    $user_company = $query->fetch(PDO::FETCH_OBJ);

    if (!$user_company) {
        make_swal2('error', 'Forbidden', 'You do not have permission to access this page.');
        redirect('', 1000);
        die;
    } else {
        unset($user_company->password); // Menghapus password dari object untuk keamanan
    }

    if (strtolower($_SESSION['user_type']) == 'supervisor') {
        // Jika user type adalah supervisor
        $query = $_SERVER['PDO']->prepare("SELECT student_internship.nim, student_internship.name, program_study.jurusan, program_study.prodi, attendance.*
            FROM attendance
            INNER JOIN internship ON attendance.id_internship = internship.id_internship
            INNER JOIN student_internship ON internship.nim = student_internship.nim
            LEFT JOIN program_study ON program_study.kode_prodi = student_internship.program_study
            WHERE internship.id_user_company = :id_user_company AND internship.status = :internship_status
            ORDER BY attendance.date DESC");
        $query->execute(['id_user_company' => $id_user_company, 'internship_status' => 'Yes']);
    } else {
        // Jika user type bukan supervisor
        $query = $_SERVER['PDO']->prepare("SELECT student_internship.nim, student_internship.name, internship.id_user_company, program_study.jurusan, program_study.prodi, attendance.*
            FROM attendance
            INNER JOIN internship ON attendance.id_internship = internship.id_internship
            INNER JOIN student_internship ON internship.nim = student_internship.nim
            LEFT JOIN program_study ON program_study.kode_prodi = student_internship.program_study
            WHERE internship.id_company = :id_company AND internship.status = :internship_status
            ORDER BY attendance.date DESC");
        // Gunakan ->id_company karena $user_company adalah object
        $query->execute(['id_company' => $user_company->id_company, 'internship_status' => 'Yes']);
    }

    // Fetch hasilnya sebagai array object
    $attendance = $query->fetchAll(PDO::FETCH_OBJ);

} catch (PDOException $e) {
    // Jika terjadi kesalahan, tampilkan errornya dan hentikan proses
    var_dump($e->getMessage());
    die;
    $attendance = [];
}


// Memasukkan hasil query ke dalam array result
$result['user_company'] = $user_company;
$result['attendance'] = $attendance;

$response = $result;

@endphp

<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title">Student Attendance</h4>
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
                    <a href="#">Student Attendance</a>
                </li>





            </ul>
        </div>

        <div class="row">


            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Student Attendance
                            <div class="float-right justify-content-between">
                                <div class="btn-group btn-round">
                                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Bulk Action
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                        <button class="dropdown-item text-info" id="bulk-action-btn">
                                            <i class="fas fa-check-circle mr-2"></i>Approval Attendance</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Tabel Logbook -->
                    <div class="card-body">

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="student-name-filter">Filter by Student Name:</label>
                                <select id="student-name-filter" class="form-control" disabled>
                                    <option value="">All</option>
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


                            <div class="form-group col-md-6">
                                <label for="approval-lecturer-filter">Filter by Approval Lecturer:</label>
                                <select id="approval-lecturer-filter" class="form-control" disabled>
                                    <option value="">All</option>
                                    <option>Waiting for Approval</option=>
                                    <option>Approved</option=>
                                    <option>Not Approved</option=>
                                </select>
                            </div>



                        </div>

                        <table id="basic-datatables" class="table table-striped table-hover display">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="check-all" class="form-control"></th>
                                    <th>Student</th>
                                    <th>Date</th>
                                    <th>Attendance Status</th>
                                    <th>Approval Supervisor</th>

                                    <th>Approval Lecturer</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    foreach ($response['attendance'] as $i => $data) :
                                        $approval_spv = $data->approval_spv == 'Waiting' ? '<button class="btn btn-sm btn-warning">Waiting for Approval</button>' : ($data->approval_spv == 'Yes' ? '<button class="btn btn-sm btn-success">Approved</button>' : '<button class="btn btn-sm btn-danger">Not Approved</button>');
                                        $text_spv = $data->approval_spv == 'Waiting' ? 'Waiting for Approval' : ($data->approval_spv == 'Yes' ? 'Approved' : 'Not Approved');

                                        $approval_dpm = $data->approval_dpm == 'Waiting' ? '<button class="btn btn-sm btn-warning">Waiting for Approval</button>' : ($data->approval_dpm == 'Yes' ? '<button class="btn btn-sm btn-success">Approved</button>' : '<button class="btn btn-sm btn-danger">Not Approved</button>');
                                        $text_dpm = $data->approval_dpm == 'Waiting' ? 'Waiting for Approval' : ($data->approval_dpm == 'Yes' ? 'Approved' : 'Not Approved');

                                        $warna_btn = $data->approval_dpm == 'Waiting' ? "text-warning" : ($data->approval_dpm == 'Yes' ? "text-success" : "text-danger");
                                    ?>
                                <tr>
                                    <td><input type="checkbox" class="form-control" value="<?= $data->id_attendance ?>">
                                    </td>
                                    <td data-nim="<?= $data->nim ?>" data-jurusan="<?= $data->jurusan ?>" data-prodi="<?= $data->prodi ?>">
                                        <?= $data->name ?>
                                    </td>

                                    <td><?= date('d M Y', strtotime($data->date)) ?></td>
                                    <td><?= $data->attendance_type ?></td>


                                    <td data-approval_spv="<?= $text_spv ?>" class="text-center">
                                        <?= $approval_spv ?>
                                    </td>
                                    <td data-approval_dpm="<?= $text_dpm ?>" class="text-center">
                                        <?= $approval_dpm ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-round">
                                            <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Action
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                <button class="dropdown-item text-info" onclick="window.location.href = 'index.php?page=intern_list_attendance_detail&id_internship=<?= base64_encode($data->id_internship) ?>&id_attendance=<?= base64_encode($data->id_attendance) ?>'">
                                                    <i class="fas fa-info-circle mr-2"></i>Detail Attendance</button>
                                                <div class="dropdown-divider"></div>
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
                                    Attendance</span>
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <div class="form-group">
                                <label for="approval-dropdown">Approval Supervisor<label class="text-danger">*</label></label>
                                <select class="form-control" name="approval_dropdown" id="approval-dropdown">
                                    <option hidden value=''>- Choose One -</option>

                                    <option>Yes</option>
                                    <option>No</option>
                                </select>
                                <?= form_error('approval_dropdown'); ?>
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
                            <h5 class="modal-title">Approval Attendance</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="index.php?form=logbook_approval&action=supervisor" method="POST">
                                <?= csrf() ?>
                                <input type="hidden" name="id_attendance" id="id_attendance">
                                <input type="hidden" name="nim" id="nim">
                                <input type="hidden" name="id_user_company" value="<?= $_SESSION['id_user_company'] ?>">

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

<!-- AJAX Script -->
<script>
    document.getElementById('approval-dropdown').addEventListener('change', function() {
        var userType = "{{ $_SESSION['user_type'] }}"; // Asumsi user type dari backend atau session
        if (userType === 'HRD') {
            // Tampilkan alert jika HRD dan memilih "Yes"
            Swal.fire({
                title: 'You are the company\'s HRD, not the student\'s supervisor',
                text: 'Are you sure you want to give approval?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, approve it!',
                cancelButtonText: 'No, cancel',
                reverseButtons: true
            }).then((result) => {
                if (!result.isConfirmed) {
                    // Reset dropdown jika user membatalkan
                    this.value = '';
                }
            });
        }
    });
</script>

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

            $('#student-name-filter').prop('disabled', false);
            $('#approval-lecturer-filter').prop('disabled', false);
            $('#approval-supervisor-filter').prop('disabled', false);

        }, 1000);


        $('#approval-lecturer-filter').on('change', function() {

            var selectedValue = $(this).val();

            // Filter kolom Company (kolom index 1)
            if (selectedValue) {
                table.column(5).search('^' + selectedValue + '$', true, false).draw();
            } else {
                table.column(5).search('').draw();
            }

            updateCheckAllStatus();

        });

        $('#approval-supervisor-filter').on('change', function() {

            var selectedValue = $(this).val();

            // Filter kolom Company (kolom index 1)
            if (selectedValue) {
                table.column(4).search('^' + selectedValue + '$', true, false).draw();
            } else {
                table.column(4).search('').draw();
            }

            updateCheckAllStatus();

        });


        $('#ModalApproval').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Tombol yang diklik
            var id_attendance = button.data('id_attendance');
            var nim = button.data('nim');

            // Masukkan data ke dalam modal
            var modal = $(this);
            modal.find('#id_attendance').val(id_attendance);
            modal.find('#nim').val(nim);
        });



        // Filter berdasarkan NIM menggunakan dropdown
        $('#student-name-filter').on('change', function() {

            var selectedValue = $.trim($(this).val()); // Trim value yang dipilih
            
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
            
            var selectedIdsString = selectedIds.join(',');


            $.ajax({
                url: 'index.php?request=bulk_approval_attendance&action=supervisor'
                , method: 'POST'
                , data: {
                    token: _token
                    , _method: 'PUT'
                    , ids: selectedIdsString
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
