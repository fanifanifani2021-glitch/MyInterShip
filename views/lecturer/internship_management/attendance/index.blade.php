@extends('templates.lecturer')


@push('head-scripts')

<!-- CSS Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
<style>
    #check-all.indeterminate~.custom-control-label::before {
        content: "-";
    }

</style>

@php

// Ambil id_internship dari URL dan decode
$id_internship = base64_decode($_GET['id_internship']);
$nim = base64_decode($_GET['nim']);

// Siapkan statement untuk mendapatkan data internship
$sql = "SELECT * FROM internship WHERE id_internship = :id_internship AND nim = :nim LIMIT 1";
$stmt = $_SERVER['PDO']->prepare($sql);

// Bind parameter dan eksekusi
$stmt->bindParam(':id_internship', $id_internship, PDO::PARAM_STR);
$stmt->bindParam(':nim', $nim, PDO::PARAM_STR);
$stmt->execute();

// Cek apakah data ditemukan
if ($stmt->rowCount() != 1) {
make_swal2('error', 'Not Allowed', 'Internship Data is not found!');
redirect_back(1);
exit;
}

// Ambil data internship
$internship_data = $stmt->fetch(PDO::FETCH_ASSOC);
$nim = $internship_data['nim'];

// Siapkan statement untuk mendapatkan data student_internship
$sql = "SELECT * FROM student_internship WHERE nim = :nim AND nik_dospem = :nik_dospem LIMIT 1";
$stmt = $_SERVER['PDO']->prepare($sql);
$stmt->execute(['nim' => $nim, 'nik_dospem' => $_SESSION['nim_nik_unit']]);

if ($stmt->rowCount() != 1) {
make_swal2('error', 'Not Allowed', 'Only Supervsior can view student data !');
redirect_back(1);
exit;
}

$student_data = $stmt->fetch(PDO::FETCH_ASSOC);


$sql = "SELECT * FROM attendance WHERE id_internship = :id_internship";

$stmt = $_SERVER['PDO']->prepare($sql);
$stmt->execute([':id_internship' => $id_internship]);

@endphp
@endpush

@section('main-content')


<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title">Attendance</h4>
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
                    <a href="index.php?page=intern_student_management">Students Management</a>
                </li>

                <li class="separator">
                    <i class="flaticon-right-arrow"></i>
                </li>
                <li class="nav-item">
                    <a href="index.php?page=intern_student_detail&nim=<?= base64_encode($nim) ?>">Student Detail</a>
                </li>

                <li class="separator">
                    <i class="flaticon-right-arrow"></i>
                </li>
                <li class="nav-item">
                    <a href="index.php?page=intern_student_internship_detail&id_internship=<?= base64_encode($id_internship) ?>">Internship Detail</a>
                </li>

                <li class="separator">
                    <i class="flaticon-right-arrow"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Attendance</a>
                </li>




            </ul>
        </div>

        <div class="row">

            <!-- Jobdesc -->
            <div class="col-md-12">
                <div class="card full-height">
                    <div class="card-header">
                        <h4 class="card-title">Attendance Data

                            <div class="float-right justify-content-beetwen">
                                <!-- <button data-toggle="modal" data-target="#ModalFilter" class="btn btn-info btn-sm btn-round mr-1"><i class="fas fa-filter mr-1"></i>Filter Data</button> -->

                                <div class="btn-group btn-round">
                                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" disabled>
                                        Bulk Action
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">

                                        <button class="dropdown-item text-info" id="bulk-action-btn">
                                            <i class="fas fa-check-circle mr-2"></i>
                                            Approval Attendance</button>


                                    </div>
                                </div>


                            </div>

                        </h4>
                    </div>


                    <div class="card-body">
                        <div class="form-row">


                            <div class="form-group col-md-12">
                                <label for="month-filter">Filter by Month:</label>
                                <select id="month-filter" class="form-control" disabled>
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


                        <table id="basic-datatables" class="table table-striped table-hover">

                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="check-all" class="form-control">
                                    </th>
                                    <th>Month</th>
                                    <th>Week Num</th>
                                    <th>Date</th>
                                    <th>Attendance Type</th>
                                    <th>Approval Lecturer</th>
                                    <th>Approval Supervisor</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ( $stmt as $i => $data )

                                @php
                                if ( $data['approval_spv'] == 'Waiting' ) {
                                $approval_spv = '<button class="btn btn-sm btn-warning">Waiting for Approval</button>';
                                } else if ( $data['approval_spv'] == 'Yes' ) {
                                $approval_spv = '<button class="btn btn-sm btn-success">Approved</button>';
                                } else {
                                $approval_spv = '<button class="btn btn-sm btn-danger">Not Approved</button>';
                                }


                                if ( $data['approval_dpm'] == 'Waiting' ) {
                                $approval_dpm = '<button class="btn btn-sm btn-warning">Waiting for Approval</button>';
                                } else if ( $data['approval_dpm'] == 'Yes' ) {
                                $approval_dpm = '<button class="btn btn-sm btn-success">Approved</button>';
                                } else {
                                $approval_dpm = '<button class="btn btn-sm btn-danger">Not Approved</button>';
                                }

                                $loop = $i;
                                @endphp

                                <tr>
                                    <td><input type="checkbox" class="form-control" value="<?= $data['id_attendance'] ?>"></td>
                                    <td data-month="<?= date('M', strtotime($data['date'] )) ?>" data-month_year="<?= date('M Y', strtotime($data['date'] )) ?>" data-year="<?= date('Y', strtotime($data['date'] )) ?>"><?= date('M Y', strtotime($data['date'] )) ?></td>
                                    <td>M<?= $data['week'] ?></td>
                                    <td><?= date('D, d-M-Y', strtotime($data['date'] )) ?></td>
                                    <td><?= $data['attendance_type'] ?></td>
                                    <td class="text-center"><?= $approval_dpm ?></td>
                                    <td class="text-center"><?= $approval_spv ?></td>
                                    <td>
                                        <div class="btn-group btn-round">
                                            <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Action
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                <!-- <button class="dropdown-item" data-toggle="modal" data-target="#ModalDetail<?= $loop ?>">Attendance Detail</button> -->
                                                <button class="dropdown-item" onclick="window.location.href='index.php?page=intern_student_attendance_detail&nim=<?= base64_encode($nim) ?>&id_internship=<?= base64_encode($id_internship) ?>&id_attendance=<?= base64_encode($data['id_attendance']) ?>' "><i class="fas fa-info-circle mr-2"></i>
                                                    Detail Attendance</button>

                                                @if ( $student_data['nik_dospem'] == $_SESSION['nim_nik_unit'] )
                                                <div class="dropdown-divider"></div>
                                                <button class="dropdown-item" data-toggle="modal" data-target="#ModalApproval" data-approval_dpm="{{ $data['approval_dpm'] }}" data-id_internship="<?= $data['id_internship'] ?>" data-id_attendance="<?= $data['id_attendance'] ?>" data-nim="<?= $data['nim'] ?>">
                                                    <i class="fas fa-check-circle mr-2"></i>Approval Attendance
                                                </button>
                                                @endif

                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                @endforeach
                            </tbody>



                        </table>
                    </div>


                </div>


                {{-- Modal Approval Bulk --}}
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
                                    <label for="approval_dpm">Approval Lecturer <label class="text-danger">*</label></label>
                                    <select class="form-control" name="approval_dropdown" id="approval-dropdown">
                                        <option hidden value=''>- Choose One -</option>
                                        <option value="Waiting" @if( old('approval_dropdown')=='Waiting' ) selected @endif>Waiting for Approval</option>
                                        
                                        <option value="Yes" @if( old('approval_dropdown')=='Yes' ) selected @endif>Approved</option>
                                        <option value="No" @if( old('approval_dropdown')=='No' ) selected @endif>Not Approved</option>

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


                {{-- Modal Approval --}}
                <div class="modal fade" id="ModalApproval">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Approval
                                    <span class="fw-mediumbold">
                                        Attendance</span>
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">

                                <form action="index.php?form=attendance_approval&action=lecturer" method="POST">
                                    <?= csrf() ?>

                                    <input type="hidden" name="id_attendance" id="id_attendance" value="{{ old('id_attendance') }}">
                                    <input type="hidden" name="id_internship" id="id_internship" value="{{ old('id_internship') }}">
                                    <input type="hidden" name="nim" id="nim" value="{{ old('nim') }}">
                                    <input type="hidden" name="nik" value="{{ old('nik', $_SESSION['nim_nik_unit']) }}">

                                    <div class="form-group">
                                        <label for="approval_dpm">Approval Lecturer<label class="text-danger">*</label></label>
                                        <select class="form-control" name="approval_dpm" id="approval_dpm">
                                            <option hidden>- Choose One -</option>
                                            <option value="Yes" @if( old('approval_dpm')=='Yes' ) selected @endif>Approved</option>
                                            <option value="No" @if( old('approval_dpm')=='No' ) selected @endif>Not Approved</option>
                                            <option value="Waiting" @if( old('approval_dpm')=='Waiting' ) selected @endif>Waiting for Approval</option>
                                        </select>
                                        <?= form_error('approval_dpm'); ?>
                                    </div>


                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Close</button>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-check-double mr-1"></i>Approval Attendance</button>
                            </div>
                            </form>

                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>
                <!-- /.tutupmodal -->

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

<!-- JS Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {

        let _token = $('meta[name="csrf-token"]').attr('content');

        let startTime = new Date().getTime();

        $('#month-filter').select2({
            theme: 'bootstrap4'
        });


        let table = $('#basic-datatables').DataTable({
            "responsive": true
            , "pageLength": 15
            , "lengthMenu": [10, 15, 25, 50]
            , "stateSave": false
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



        function disableAllElements() {
            $('select, input[type="checkbox"], button').prop('disabled', true);
        }

        function enableAllElements() {
            $('select, input[type="checkbox"], button').prop('disabled', false);
        }




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
                table.column(6).search('^' + selectedValue + '$', true, false).draw();
            } else {
                table.column(6).search('').draw();
            }

            updateCheckAllStatus();

        });


        $('#month-filter').on('change', function() {

            var selectedValue = $(this).val();

            // Filter kolom Company (kolom index 1)
            if (selectedValue) {
                table.column(1).search('^' + selectedValue + '$', true, false).draw();
            } else {
                table.column(1).search('').draw();
            }

            updateCheckAllStatus();

        });



        $('#basic-datatables').on('change', 'tbody input[type="checkbox"]', function() {
            updateCheckAllStatus();
        });

        // Menangani checkbox "Check All"
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


        setTimeout(function() {

            var groupedMonths = {};

            table.rows().every(function(rowIdx, tableLoop, rowLoop) {
                var data = this.data(); // Mengambil data baris saat ini
                var $row = $(this.node());

                // Mengambil data bulan, bulan-tahun, dan tahun dari atribut data pada elemen <td>
                var month = $row.find('td[data-month]').data('month');
                var monthYear = $row.find('td[data-month_year]').data('month_year');
                var year = $row.find('td[data-year]').data('year');

                // Jika belum ada kelompok untuk tahun ini, buat objek baru
                if (!groupedMonths[year]) {
                    groupedMonths[year] = {};
                }

                // Tambahkan bulan ke dalam objek berdasarkan monthYear (untuk menghindari duplikasi)
                if (!groupedMonths[year][monthYear]) {
                    groupedMonths[year][monthYear] = month;
                }
            });

            // Memasukkan nilai bulan ke dalam dropdown <select> dalam bentuk <optgroup>
            var selectOptions = $('#month-filter');
            $.each(groupedMonths, function(year, months) {
                var optgroup = $('<optgroup>', {
                    label: year
                });

                // Tambahkan bulan ke dalam optgroup berdasarkan tahun
                $.each(months, function(monthYear, month) {
                    // monthYear sebagai value, month sebagai teks yang ditampilkan
                    optgroup.append('<option value="' + monthYear + '">' + monthYear + '</option>');
                });

                selectOptions.append(optgroup);
            });

            enableAllElements();

        }, 1000);



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
                url: 'index.php?request=bulk_approval_attendance&action=lecturer'
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



        $('#ModalApproval').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Tombol yang diklik
            var id_attendance = button.data('id_attendance');
            var id_internship = button.data('id_internship');
            var approval_dpm = button.data('approval_dpm');
            var nim = button.data('nim');

            // Masukkan data ke dalam modal
            var modal = $(this);
            modal.find('#id_attendance').val(id_attendance);
            modal.find('#id_internship').val(id_internship);
            modal.find('#approval_dpm').val(approval_dpm);
            modal.find('#nim').val(nim);
        });



    });

</script>


@if (old('ModalApproval') == 'Open'):
<script>
    $('#ModalApproval').modal('show');

</script>
@endif

@endpush
