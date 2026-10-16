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



@endpush

@section('main-content')
@php

$id_kampus = $_SESSION['Lecturer_Data']['id_kampus'] ?? NULL;

$sql_spv = "SELECT * FROM lecturer WHERE id_kampus = :id_kampus";

$stmt_spv = $_SERVER['PDO']->prepare($sql_spv);
$stmt_spv->execute([':id_kampus' => $id_kampus]);


$sql_prodi = "SELECT * FROM program_study WHERE id_kampus = :id_kampus ORDER BY major, jenjang, study_program";
$stmt_prodi = $_SERVER['PDO']->prepare($sql_prodi);
$stmt_prodi->execute([':id_kampus' => $id_kampus ]);

$prodi_data = $stmt_prodi->fetchAll(PDO::FETCH_ASSOC);

$grouped_prodi = [];

foreach ($prodi_data as $prodi) {
$major = $prodi['major'];
$study_program = $prodi['jenjang'] . " " . $prodi['study_program'];
$kode_prodi = $prodi['kode_prodi'];

// Grouping berdasarkan major
if (!isset($grouped_prodi[$major])) {
$grouped_prodi[$major] = [];
}
$grouped_prodi[$major][] = [
'kode_prodi' => $kode_prodi,
'study_program' => $prodi['jenjang'] . " " . $prodi['study_program'],
'prodi' => $prodi['jenjang'] . " " . $prodi['prodi']
];
}


@endphp


<div class="container">

    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Lecturer Management</h2>
                    <h5 class="text-white op-7 mb-2">List of Polibatam Lecturer</h5>
                </div>

                <div class="ml-md-auto py-2 py-md-0">
                    <button onclick="window.location.href = 'index.php?page=add_supervisor'" class="btn btn-primary btn-round"><i class="fas fa-plus mr-1"></i>Add Data</button>
                </div>
            </div>
        </div>
    </div>

    <div class="page-inner mt--5">
        <div class="row">


            <!-- Internship History -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <span class="text-sm">Lecturer List</span>

                        </div>

                    </div>

                    <div class="card-body">
                        <div class="form-row">

                            <div class="form-group col-md-12">

                                <label for="study-program-filter">Filter by Supervising in Study Program:</label>
                                <select id="study-program-filter" class="form-control" disabled>
                                    <option value="">All</option>
                                    @foreach ($grouped_prodi as $major => $prodi_list)
                                    <optgroup label="Major : {{ $major }}">
                                        @foreach ($prodi_list as $prodi)
                                        <option value="{{ trim($prodi['jenjang'] . ' ' . $prodi['study_program']) }}">{{ $prodi['jenjang'] . ' ' . $prodi['study_program'] }}</option>
                                        @endforeach
                                    </optgroup>
                                    @endforeach
                                </select>
                            </div>


                        </div>


                        <table id="basic-datatables" class="table table-striped table-hover">

                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>NIK</th>
                                    <th>Name</th>
                                    <th>Total Student</th>
                                    <th>Supervising in Study Program</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>


                            <tbody>

                                @php

                                // Fetch supervisor data
                                $sql_supervisor = $stmt_spv->fetchAll(PDO::FETCH_ASSOC);

                                @endphp

                                @foreach ($sql_supervisor as $index => $data)

                                @php
                                $nik = $data['nim_nik_unit'];

                                // Prepare and execute query to get total students supervised
                                $sql_lecturer_stats = "SELECT SUBSTRING(si.nim, 4, 2) AS angkatan, COUNT(DISTINCT(si.nim)) AS total_mhs,
                                SUM(CASE WHEN i.status = 'Yes' THEN 1 ELSE 0 END) AS internship_in_progress,
                                SUM(CASE WHEN i.status = 'Finish' THEN 1 ELSE 0 END) AS internship_finished,
                                SUM(CASE WHEN i.status = 'Waiting' THEN 1 ELSE 0 END) AS waiting_for_approval,
                                SUM(CASE WHEN i.status NOT IN ('Yes', 'Finish', 'Waiting') THEN 1 ELSE 0 END) AS other

                                FROM student_internship si
                                LEFT JOIN internship i ON si.nim = i.nim
                                WHERE si.nik_dospem = :nik AND si.id_kampus = :id_kampus
                                GROUP BY angkatan
                                ORDER BY angkatan;";

                                $stmt_lecturer_stats = $_SERVER['PDO']->prepare($sql_lecturer_stats);
                                $stmt_lecturer_stats->execute([':nik' => $nik,':id_kampus' => $id_kampus]);

                                $lecturer_stats = $stmt_lecturer_stats->fetchAll(PDO::FETCH_ASSOC);

                                $total_mhs = 0;

                                foreach ($lecturer_stats as $key => $value) {
                                // $total_mhs = $total_mhs + $value['internship_in_progress'] + $value['waiting_for_approval'] + $value['other'] ;
                                $total_mhs = $total_mhs + $value['total_mhs'];
                                }



                                $sql_prodi_bimbingan = "SELECT DISTINCT ps.jenjang, ps.study_program
                                FROM student_internship si
                                JOIN program_study ps ON si.program_study = ps.kode_prodi
                                WHERE si.nik_dospem = :nik AND si.id_kampus = :id_kampus";
                                $stmt_prodi_bimbingan = $_SERVER['PDO']->prepare($sql_prodi_bimbingan);
                                $stmt_prodi_bimbingan->execute([
                                ':nik' => $nik,
                                ':id_kampus' => $id_kampus
                                ]);

                                $prodi_bimbingan = $stmt_prodi_bimbingan->fetchAll(PDO::FETCH_ASSOC);

                                // Create buttons for each Prodi and Jenjang
                                $prodi_text = ''; $prodi_text2 = '';
                                $prodi_bimbingan_buttons = '';
                                foreach ($prodi_bimbingan as $prodi) {
                                $prodi_text .= trim(htmlspecialchars($prodi['jenjang'] . ' ' . $prodi['study_program']. ', '));
                                $prodi_text2 = trim(htmlspecialchars($prodi['jenjang'] . ' ' . $prodi['study_program']));
                                $prodi_bimbingan_buttons .= '<button type="button" class="btn btn-primary btn-sm" style="margin-right: 5px;">' . $prodi_text2 . '</button>';
                                }
                                @endphp


                                <tr>
                                    <td><?= $index + 1 ?>.</td>
                                    <td><?= $data['nim_nik_unit'] ?></td>
                                    <td><?= $data['name'] ?></td>
                                    <td><button class="btn btn-secondary btn-sm" data-toggle="modal" onclick="showStatisticsModal(this)" data-statistics='<?= htmlspecialchars( json_encode( $lecturer_stats ) , ENT_QUOTES, 'UTF-8') ?>'><i class="fa fa-info-circle mr-1"></i><?= $total_mhs ?> Student</button></td>

                                    <td data-bimbingan="<?= trim(htmlspecialchars($prodi_text)) ?>">{!! $prodi_bimbingan_buttons !!}</td>

                                    <td><?= $data['status'] ?></td>
                                    <td>
                                        <button class="btn btn-secondary btn-sm" onclick="window.location.href = 'index.php?page=supervisor_detail&nik=<?= base64_encode($data['nim_nik_unit']) ?>'"><i class="fa fa-info-circle mr-1"></i>Detail</button>
                                    </td>
                                </tr>


                                @endforeach

                            </tbody>


                        </table>
                    </div>
                </div>



                <!-- Modal -->
                <div class="modal fade" id="ModalStatistik" tabindex="-1" role="dialog" aria-labelledby="ModalStatistikLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="ModalStatistikLabel">Lecturer Statistics</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="statisticsTable">
                                        <thead>
                                            <tr>
                                                <th>Generation</th>
                                                <th>Total Student</th>
                                                <th>Internship in Progress</th>
                                                <th>Internship Finished</th>
                                                <th>Waiting for Approval</th>
                                                <th>Other Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Data akan diisi oleh JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
        $('#study-program-filter').select2({
            theme: 'bootstrap4'
        });


        let _token = $('meta[name="csrf-token"]').attr('content');

        setTimeout(function() {
            $('#study-program-filter').prop('disabled', false);
        }, 1000);


        let table = $('#basic-datatables').DataTable({
            "responsive": true
            , "pageLength": 25
            , "stateSave": false
            , "lengthMenu": [10, 25, 50, 100]
            , "columnDefs": [{
                targets: 4, // Kolom ke-5 (indeks 4) yang berisi data-bimbingan
                createdCell: function(td, cellData, rowData, row, col) {
                    $(td).attr('data-bimbingan', cellData.trim()); // Tambahkan atribut data-bimbingan
                }
            }]
            , "order": [
                [1, 'asc'] // Urutkan berdasarkan kolom kedua (indeks 1)
            ]
        });

        // Event listener untuk dropdown filter
        $('#study-program-filter').on('change', function() {
            let selectedValue = $(this).val().trim(); // Mendapatkan nilai yang dipilih dari dropdown

            // Cek apakah selectedValue tidak kosong
            let regexSearch = selectedValue ? selectedValue : '';

            // Cari di kolom ke-5 (indeks 4) menggunakan substring pencarian
            table.column(4).search(regexSearch, true, false).draw();
        });

    });


    function showStatisticsModal(button) {
        // Ambil data JSON dari atribut data-statistics pada tombol yang diklik
        let statisticsData = JSON.parse(button.getAttribute('data-statistics'));

        // Log untuk memastikan data JSON yang benar diterima
        console.log(statisticsData);

        // Pilih elemen tabel dan tbody dalam tabel di modal
        let statisticsTable = $('#statisticsTable');
        const tableBody = statisticsTable.find('tbody'); // Gunakan jQuery untuk memilih tbody

        // Hancurkan instance DataTable jika sudah ada
        if ($.fn.DataTable.isDataTable(statisticsTable)) {
            statisticsTable.DataTable().clear().destroy();
        }

        // Kosongkan isi tabel sebelum menambahkan data baru
        tableBody.empty();

        // Loop melalui data dan tambahkan baris ke tabel
        statisticsData.forEach(stat => {
            let row = `
            <tr>
                <td>20${stat.angkatan}</td>
                <td>${stat.total_mhs}</td>
                <td>${stat.internship_in_progress}</td>
                <td>${stat.internship_finished}</td>
                <td>${stat.waiting_for_approval}</td>
                <td>${stat.other}</td>
            </tr>
        `;
            tableBody.append(row); // Tambahkan baris baru ke tbody
        });

        // Inisialisasi DataTable
        statisticsTable.DataTable({
            responsive: true
            , pageLength: 25
        });

        // Tampilkan modal setelah data diisi
        $('#ModalStatistik').modal('show');
    }

</script>
@endpush