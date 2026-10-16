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

<link rel='stylesheet' href='./core/component/snackbar/dist/snackbar.min.css' media='screen' title='no title' charset='utf-8'>
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

$query = $_SERVER['PDO']->prepare("SELECT * FROM lecturer WHERE nim_nik_unit = :nim_nik_unit LIMIT 1");
$query->execute(['nim_nik_unit' => $nik_dosen]);
$lecturer = $query->fetch(PDO::FETCH_OBJ);

if (!$lecturer) {
// redirect_back
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
$parameters = ['nik_dospem' => $nik_dosen]; // Pastikan variabel $nik_dosen diatur

if ($filter !== 'All') {
    $statusCondition = ' AND internship.status = :internship_status';
    $parameters['internship_status'] = $filter; // Tambahkan parameter status jika filter bukan 'All'
}

$orderBy = "
ORDER BY CASE 
    WHEN discussion.discussion_status = 'Open' THEN 1 
    WHEN discussion.discussion_status = 'Closed' THEN 2 
    ELSE 3 
END";

// Menyusun query SQL
$sql = "
SELECT
    student_internship.nim,
    student_internship.name,
    internship.id_internship,
    discussion.*,
    program_study.jurusan,
    program_study.prodi
FROM
    discussion
INNER JOIN internship ON discussion.id_internship = internship.id_internship
INNER JOIN student_internship ON internship.nim = student_internship.nim
LEFT JOIN program_study ON program_study.kode_prodi = student_internship.program_study
WHERE
    student_internship.nik_dospem = :nik_dospem
" . $statusCondition . $orderBy;

$query = $_SERVER['PDO']->prepare($sql);

// Eksekusi query dengan kondisi dinamis
$query->execute($parameters);

// Ambil hasilnya
$discussion = $query->fetchAll(PDO::FETCH_ASSOC);

$result['lecturer'] = $lecturer;
$result['discussion'] = $discussion;

$response = $result;

@endphp

<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title">Student Discussion</h4>
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
                    <a href="#">Student Discussion</a>
                </li>





            </ul>
        </div>

        <div class="row">


            <!-- Internship History -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Student Discussion
                        </div>

                    </div>

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


                            <div class="form-group col-md-6">
                                <label for="discussion-status-filter">Filter by Discussion Status:</label>
                                <select id="discussion-status-filter" class="form-control" disabled>
                                    <option value="">All</option>
                                    <option>Open</option>
                                    <option>Closed</option>
                                </select>
                            </div>

                        </div>


                        <table id="basic-datatables" class="table table-striped table-hover">

                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Created By</th>
                                    <th>Topic</th>
                                    <th>Status</th>
                                    <th>Total Comment</th>

                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($response['discussion'] as $i => $data)

                                @php
                                $sql = "SELECT COUNT(id_discussion) AS count_comment FROM discussion_comment WHERE id_discussion = :id_discussion";
                                $stmt = $_SERVER['PDO']->prepare($sql);
                                $stmt->execute([':id_discussion' => $data['id_discussion']]);

                                if ($stmt->rowCount() === 1) {
                                $count_comment = $stmt->fetch(PDO::FETCH_ASSOC)['count_comment'];
                                } else {
                                $count_comment = 0;
                                }


                                if ($data['user_type'] == 'spv') {
                                $user_type = $_SERVER['DISCUSSION_ACTOR'] [ $data['user_type'] ];
                                } else if ($data['user_type'] == 'hrd') {
                                $user_type = $_SERVER['DISCUSSION_ACTOR'] [ $data['user_type'] ];
                                } else if ($data['user_type'] == 'dpm') {
                                $user_type = $_SERVER['DISCUSSION_ACTOR'] [ $data['user_type'] ];
                                } else if ($data['user_type'] == 'dkm') {
                                $user_type = $_SERVER['DISCUSSION_ACTOR'] [ $data['user_type'] ];
                                } else if ($data['user_type'] == 'mhs') {
                                $user_type = $_SERVER['DISCUSSION_ACTOR'] [ $data['user_type'] ];
                                } else if ($data['user_type'] == 'cdc') {
                                $user_type = $_SERVER['DISCUSSION_ACTOR'] [ $data['user_type'] ];
                                } else {
                                $user_type = $_SERVER['DISCUSSION_ACTOR'] [ 'anon' ];
                                }



                                if ( $data['discussion_status'] == 'Open' ) {
                                $discussion_status = "<button class='btn btn-success btn-sm'>Open</button>";
                                } else if ( $data['discussion_status'] == 'Pending' ) {
                                $discussion_status = "<button class='btn btn-warning btn-sm'>Pending</button>";
                                } else if ( $data['discussion_status'] == 'Closed' ) {
                                $discussion_status = "<button class='btn btn-danger btn-sm'>Closed</button>";
                                } else {
                                $discussion_status = "<button class='btn btn-dark btn-sm'>" .$data['discussion_status'] . "</button>";
                                }

                                $nim = $data['nim'];
                                $id_internship = $data['id_internship'];

                                $konfirmasi = base64_encode($data['discussion_topic']);
                                @endphp

                                <tr>
                                    <td><?= $i + 1 ?>.</td>
                                    <td><?= $data['user_name'] ?> (<?= $user_type ?>)</td>
                                    <td>{{ mb_strimwidth( html_entity_decode($data['discussion_topic']) , 0, 30, "...") }}</td>
                                    <td><?= $discussion_status ?></td>

                                    <td><?= $count_comment ?> Comment</td>
                                    <td>

                                        @if ( $data['user_id'] == $_SESSION['nim_nik_unit'])

                                        <div class="btn-group btn-round">
                                            <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Action
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">

                                                <button class="dropdown-item" onclick="window.location.href = 'index.php?page=intern_student_discussion_join&nim=<?= base64_encode( $nim )?>&id_internship=<?= base64_encode($data['id_internship']) ?>&id_discussion=<?= base64_encode($data['id_discussion']) ?>'"><i class="fas fa-comments mr-2"></i>Join Discussion</button>

                                                <div class="dropdown-divider"></div>

                                                <button class="dropdown-item" data-toggle="modal" data-target="#ModalEdit<?= $i ?>"><i class="fas fa-edit mr-2"></i>Edit Data</button>

                                                <button class="dropdown-item text-danger delete-discussion" data-id="<?= $data['id_discussion'] ?>">
                                                    <i class="fas fa-trash-alt mr-2"></i>Delete Data
                                                </button>
                                            </div>
                                        </div>

                                        @else
                                        <button class="btn btn-primary btn-sm" onclick="window.location.href = 'index.php?page=intern_student_discussion_join&nim=<?= base64_encode( $nim )?>&id_internship=<?= base64_encode($data['id_internship']) ?>&id_discussion=<?= base64_encode($data['id_discussion']) ?>'"><i class="fas fa-comments mr-2"></i>Join Discussion</button>

                                        @endif

                                    </td>
                                </tr>



                                @endforeach
                            </tbody>



                        </table>
                    </div>
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

                                <input type="hidden" name="id_attendance" id="id_attendance">
                                <input type="hidden" name="id_internship" id="id_internship">
                                <input type="hidden" name="nim" id="nim">
                                <input type="hidden" name="nik" value="<?= $_SESSION['nim_nik_unit'] ?>">

                                <div class="form-group">
                                    <label for="approval_dpm">Approval Lecturer<label class="text-danger">*</label></label>
                                    <select class="form-control" name="approval_dpm" id="approval_dpm">
                                        <option hidden><?= $data->approval_dpm ?></option>
                                        <option>Yes</option>
                                        <option>No</option>
                                        <option>Waiting</option>
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
            , "order": [
                [3, 'desc']
            ], // Urutkan berdasarkan kolom kedua
        });


        function disableAllElements() {
            $('select, input[type="checkbox"], button').prop('disabled', true);
        }

        function enableAllElements() {
            $('select, input[type="checkbox"], button').prop('disabled', false);
        }


        $('#ModalApproval').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Tombol yang diklik
            var id_attendance = button.data('id_attendance');
            var nim = button.data('nim');

            // Masukkan data ke dalam modal
            var modal = $(this);
            modal.find('#id_attendance').val(id_attendance);
            modal.find('#nim').val(nim);
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

        }, 1000);

        $('#discussion-status-filter').on('change', function() {

            var selectedValue = $(this).val();

            // Filter kolom Company (kolom index 1)
            if (selectedValue) {
                table.column(3).search('^' + selectedValue + '$', true, false).draw();
            } else {
                table.column(3).search('').draw();
            }

        });



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
                        window.location.href = 'index.php?page=discussion_student&filter=All';
                    } else {
                        // Jika pengguna menekan "Cancel", reset dropdown ke pilihan sebelumnya
                        $(this).val('Yes');
                    }
                });
            } else {
                // Jika memilih filter lain selain 'All', langsung arahkan ke halaman dengan filter
                window.location.href = 'index.php?page=discussion_student&filter=' + filterValue;
            }
        });


    });

</script>



@if (old('ModalApproval') !== NULL):
<script>
    var counter = 1;
    $('#ModalApproval' + counter).modal('show');

</script>
@endif

@endpush
