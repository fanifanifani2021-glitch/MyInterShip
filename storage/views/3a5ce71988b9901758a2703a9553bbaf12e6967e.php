


<?php $__env->startPush('head-scripts'); ?>

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

<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>
<?php
$pdo = $_SERVER['PDO'];
$nik_dosen = $_SESSION['nim_nik_unit'] ?? $_SESSION['Lecturer-Data']['nim_nik_unit'];
$results = array();

$sql = "SELECT si.name AS student_name, si.nim, ps.major, ps.jurusan, ps.jenjang, ps.study_program, ps.prodi,
i.id_internship, i.status, i.start_date, i.end_date, i.final_report,
i.file_1 AS cv, i.file_2 AS transcript_grade,
co.name AS company_name,

ifb.id_industry_feedback, ifb.nilai_akhir AS industry_score, sfb.id_student_feedback

FROM
internship i

INNER JOIN
company co ON co.id_company = i.id_company

INNER JOIN
student_internship si ON i.nim = si.nim

LEFT JOIN
program_study ps ON ps.kode_prodi = si.program_study

LEFT JOIN
industry_feedback ifb ON i.id_internship = ifb.id_internship

LEFT JOIN
student_feedback sfb ON i.id_internship = sfb.id_internship

WHERE
si.nik_dospem = :nik";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':nik', $nik_dosen, PDO::PARAM_STR);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title">Student Administration</h4>
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
                    <a href="#">Student Administration</a>
                </li>





            </ul>
        </div>

        <div class="row">


            <!-- Internship History -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Student Administration

                            <div class="float-right justify-content-beetwen">
                                <!-- <button data-toggle="modal" data-target="#ModalFilter" class="btn btn-info btn-sm btn-round mr-1"><i class="fas fa-filter mr-1"></i>Filter Data</button> -->

                            </div>
                        </div>

                    </div>

                    <div class="card-body">

                        <div class="form-row">

                            <div class="form-group col-md-12">
                                <label for="internship-status-filter">Filter by Internship Status:</label>
                                <select id="internship-status-filter" class="form-control" disabled>
                                    <option value="">All</option>
                                    <option value="Internship in Progress">Internship in Progress</option>
                                    <option value="Internship Completed">Internship Completed</option>
                                    <option value="Internship Rejected">Internship Rejected</option>
                                    <option value="Not Applied Yet">Not Applied Yet</option>
                                    <option value="Waiting for Validation">Waiting for Validation</option>
                                </select>

                            </div>

                            <div class="form-group col-md-6">
                                <label for="student-name-filter">Filter by Student Name:</label>
                                <select id="student-name-filter" class="form-control" disabled>
                                    <option value="">All</option>
                                </select>
                            </div>


                            <div class="form-group col-md-6">
                                <label for="study-program-filter">Filter by Study Program:</label>
                                <select id="study-program-filter" class="form-control" disabled>
                                    <option value="">All</option>
                                </select>
                            </div>
                        </div>


                        <table id="basic-datatables" class="table table-striped table-hover">

                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Student</th>
                                    <th>Company</th>
                                    <th>Internship Status</th>
                                    <th>Period</th>
                                    <th>Final Report</th>
                                    <th>Student Feedback</th>
                                    <th>Industry Feedback</th>
                                    <th>#</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                <?php
                                $status = $row['status'];

                                $internship_detail = '-';
                                $internship_date = '-';

                                if ( strtolower($row['status']) == 'yes') {
                                $internship_detail = '<a href="index.php?page=intern_student_internship_detail&id_internship=' .base64_encode( $row['id_internship'] ). '" class="btn btn-info btn-sm"><i class="fas fa-info-circle mr-1"></i>Detail</a>';

                                $internship_status = 'Internship in Progress'; // Magang berjalan
                                $internship_btn = 'btn-success'; // Warna tombol hijau untuk status 'Yes'
                                } elseif ( strtolower($row['status']) == 'finish') {
                                $internship_detail = '<a href="index.php?page=intern_student_internship_detail&id_internship=' .base64_encode( $row['id_internship'] ). '" class="btn btn-info btn-sm"><i class="fas fa-info-circle mr-1"></i>Detail</a>';

                                $internship_status = 'Internship Completed'; // Magang selesai
                                $internship_btn = 'btn-info'; // Warna tombol biru untuk status 'Finish'
                                } elseif ( strtolower($row['status']) == 'no') {
                                $internship_detail = '<a href="index.php?page=intern_student_internship_detail&id_internship=' .base64_encode( $row['id_internship'] ). '" class="btn btn-info btn-sm"><i class="fas fa-info-circle mr-1"></i>Detail</a>';

                                $internship_status = 'Internship Rejected'; // Magang ditolak
                                $internship_btn = 'btn-danger'; // Warna tombol merah untuk status 'No'
                                } elseif (!$status) {
                                $internship_status = 'Not Applied Yet'; // Belum Apply Magang
                                $internship_btn = 'btn-secondary'; // Warna tombol abu-abu untuk status 'Belum Apply Magang'
                                } else {
                                $internship_detail = '<a href="index.php?page=intern_student_internship_detail&id_internship=' .base64_encode( $row['id_internship'] ). '" class="btn btn-info btn-sm"><i class="fas fa-info-circle mr-1"></i>Detail</a>';

                                $internship_status = 'Waiting for Validation'; // Menunggu validasi
                                $internship_btn = 'btn-warning'; // Warna tombol kuning untuk status 'Menunggu validasi'
                                }

                                if ( in_array( strtolower($status), ['finish', 'yes']) ) {

                                $internship_period = range_time_elapsed_string($row['start_date'], $row['end_date']);
                                $internship_date = date('d-M-Y', strtotime($row['start_date'])) . " until " . date('d-M-Y', strtotime($row['end_date'])) . " ($internship_period)";



                                $finalReport = $row['final_report'];
                                if (!empty($finalReport) && $finalReport !== '-' && $finalReport !== NULL) {
                                // Generate the link if final_report is valid
                                $encodedReport = base64_encode($finalReport);
                                $finalReport_link = '<a href="index.php?viewer=final_report&file=' . htmlspecialchars($encodedReport) . '" target="_blank">View Report</a>';
                                } else {
                                // Display a placeholder or empty text if not valid
                                $finalReport_link = 'No Final Report Uploaded';
                                }


                                if ( $row['id_student_feedback'] !== NULL ) {
                                $studentFeedback_link = base_url('index.php?page=print_student_feedback&id_student_feedback=' . base64_encode($row['id_student_feedback']));
                                $studentFeedback_link = "<a href='$studentFeedback_link'>View Student Feedback</a>";

                                } else {
                                $studentFeedback_link = 'Student feedback not filled in yet';
                                }


                                if ( $row['id_industry_feedback'] !== NULL ) {
                                $industryFeedback_link = base_url('index.php?page=print_industry_feedback&id=' . encrypt($row['id_internship'])) . '&exportLanguage=ID';
                                $industryFeedback_link = "<a href='$industryFeedback_link'>View Industry Feedback</a>";

                                } else {
                                $industryFeedback_link = 'Industry feedback not filled in yet';
                                }


                                // Cek
                                } else {
                                $finalReport_link = '-';
                                $industryFeedback_link = '-';
                                $studentFeedback_link = '-';
                                }




                                ?>

                                <tr>
                                    <td><?= htmlspecialchars($row['id_internship']) ?></td>
                                    <td data-nim="<?= $row['nim'] ?>" data-jurusan="<?= $row['major'] ?> | <?= $row['jurusan'] ?>" data-prodi="<?= $row['jenjang'] ?> <?= $row['study_program'] ?> | <?= $row['jenjang'] ?> <?= $row['prodi'] ?>"><?= htmlspecialchars($row['student_name']) ?></td>
                                    <td><?= htmlspecialchars($row['company_name']) ?></td>
                                    <td data-status="<?= $internship_status ?>">
                                        <button class="btn <?= $internship_btn ?> btn-sm" data-toggle="tooltip" title="Internship status : <?= htmlspecialchars($status) ?>"><?= $internship_status ?></button>
                                    </td>

                                    <td><?php echo e($internship_date); ?></td>
                                    <td><?php echo $finalReport_link; ?></td>
                                    <td><?php echo $studentFeedback_link; ?></td>
                                    <td><?php echo $industryFeedback_link; ?></td>

                                    <td><?php echo $internship_detail; ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>


                    </div>
                </div>
            </div>


        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('footer-scripts'); ?>
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

        // Set untuk menyimpan nilai unik
        const programMap = {};
        let _token = $('meta[name="csrf-token"]').attr('content');


        $('#student-name-filter').select2({
            theme: 'bootstrap4'
        });

        let table = $('#basic-datatables').DataTable({
            "responsive": true
            , "pageLength": 25
            , "lengthMenu": [10, 25, 50, 100]
            , "columnDefs": [{
                targets: 1, // Kolom kedua (nama)
                createdCell: function(td, cellData, rowData, row, col) {
                    $(td).attr('data-nim', rowData.nim); // Tambahkan atribut data-nim
                    $(td).attr('data-jurusan', rowData.jurusan); // Tambahkan atribut data-jurusan jika perlu
                    $(td).attr('data-prodi', rowData.prodi); // Tambahkan atribut data-prodi jika perlu
                }
            }]
            , "order": [
                [1, 'asc']
            ], // Urutkan berdasarkan kolom kedua
        });





        function enableAllElements() {
            $('select, input[type="checkbox"], button').prop('disabled', false);
        }


        setTimeout(function() {



            // Ambil data 'data-jurusan' dan 'data-prodi' dari kolom kedua
            table.rows().every(function(rowIdx, tableLoop, rowLoop) {
                const dataJurusan = $(this.node()).find('td').eq(1).data('jurusan');
                const dataProdi = $(this.node()).find('td').eq(1).data('prodi');

                if (dataJurusan && dataProdi) {
                    // Jika jurusan belum ada di programMap, inisialisasi sebagai array kosong
                    if (!programMap[dataJurusan]) {
                        programMap[dataJurusan] = new Set();
                    }
                    // Tambahkan prodi ke set sesuai jurusan
                    programMap[dataJurusan].add(dataProdi);
                }
            });

            // Populasi dropdown dengan optgroup
            const studyProgramFilter = $('#study-program-filter');
            Object.keys(programMap).forEach(jurusan => {
                const optGroup = $('<optgroup>').attr('label', "Major : " + jurusan);

                // programMap[jurusan].forEach(prodi => {
                //     optGroup.append(new Option(prodi, `${jurusan} - ${prodi}`));
                // });

                programMap[jurusan].forEach(prodi => {
                    optGroup.append(new Option(prodi, prodi)); // Menampilkan hanya prodi sebagai teks opsi
                });

                studyProgramFilter.append(optGroup);
            });



            // Set untuk menyimpan pasangan NIM dan nama secara unik
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


        // Filter berdasarkan NIM menggunakan dropdown
        $('#student-name-filter').on('change', function() {

            var selectedValue = $(this).val();

            // Filter kolom Company (kolom index 1)
            if (selectedValue) {
                table.column(1).search('^' + selectedValue + '$', true, false).draw();
            } else {
                table.column(1).search('').draw();
            }
        });


        $('#study-program-filter').on('change', function() {
            const selectedProgram = $(this).val();

            // Hapus filter sebelumnya
            $.fn.dataTable.ext.search = []; // Menghapus filter yang sebelumnya ada

            // Tambahkan filter baru
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                const row = table.row(dataIndex).node();
                const prodi = $(row).find('td').eq(1).data('prodi'); // Ambil data-prodi dari kolom yang sesuai

                // Jika tidak ada pilihan yang dipilih, tampilkan semua baris
                if (selectedProgram === '') {
                    return true;
                }

                // Kembalikan true jika prodi cocok
                return prodi === selectedProgram;
            });

            // Menarik ulang tabel untuk menerapkan pencarian
            table.draw();
        });





        $('#internship-status-filter').on('change', function() {
            var selectedStatus = $(this).val(); // Dapatkan nilai dari dropdown

            // Jika pilihan 'All', reset filter
            if (selectedStatus === 'All') {
                table.column(3).search('').draw(); // Kolom ke-4 diindeks sebagai 3 di DataTables (0-based index)
            } else {
                // Jika pilihan bukan 'All', filter berdasarkan status
                table.column(3).search(selectedStatus).draw();
            }
        });


    });

</script>



<?php if(old('ModalApproval') !== NULL): ?>:
<script>
    var counter = 1;
    $('#ModalApproval' + counter).modal('show');

</script>
<?php endif; ?>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('templates.lecturer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp_new\htdocs\pblKita\views/lecturer/internship_data/administration_student.blade.php ENDPATH**/ ?>