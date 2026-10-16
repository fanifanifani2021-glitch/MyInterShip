@extends('templates.industry')


@section('main-content')
@php
$sql = "SELECT internship.*, student_internship.*, applicant.id_applicant
FROM internship
INNER JOIN student_internship ON student_internship.nim = internship.nim
LEFT JOIN applicant ON applicant.id_internship = internship.id_internship
WHERE internship.id_company = :id_company
AND internship.status IN ('Yes', 'No', 'Finish') 
ORDER BY CASE 
    WHEN internship.status = 'Yes' THEN 1
    WHEN internship.status = 'No' THEN 2
    WHEN internship.status = 'Finish' THEN 3
    ELSE 4
END, internship.id_internship ASC";


$stmt = $_SERVER['PDO']->prepare($sql); // Menggunakan $pdo yang sudah diinisialisasi sebelumnya
$stmt->execute(['id_company' => $_SESSION['id_company']]);

$intern_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
@endphp


<div class="container">

    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Internship Management</h2>
                    <h5 class="text-white op-7 mb-2">Managing Interns in the Company</h5>

                </div>

            </div>
        </div>
    </div>

    <div class="page-inner mt--5">
        <div class="row mt--2">
            <div class="col-md-12">

                <div class="card full-height">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">Internship Data</h4>
                        </div>
                    </div>
                    <div class="card-body">


                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="internship-filter">Filter by Internship Status:</label>
                                <select id="internship-filter" class="form-control" disabled>
                                    <option value="">All</option>
                                    <option value="Yes">Only Accepted Internship are shown</option>
                                    <option value="No">Only Rejected Internship are shown</option>
                                    <option value="Finish">Only Finished Internship are shown</option>

                                </select>
                            </div>

                        </div>


                        <table id="basic-datatables" class=" table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>NIM</th>
                                    <th>Student Name</th>
                                    <th>Status</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Apply Via</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-staff">
                                @foreach($intern_data as $i => $data)

                                @php
                                // Cek Applicant
                                if ( $data['id_applicant'] !== NULL ) {
                                $registered_via = 'Offer';
                                } else {
                                $registered_via = 'Cooperation';
                                }


                                if ( $data['status'] == 'Yes' ) {
                                $status = '<button type="button" class="btn btn-sm btn-success">Accepted</button>';
                                } else if ( $data['status'] == 'Finish' ) {
                                $status = '<button type="button" class="btn btn-sm btn-info">Finished</button>';
                                } else {
                                $status = '<button type="button" class="btn btn-sm btn-danger">Rejected</button>';

                                }
                                @endphp

                                <tr>
                                    <td><?=$i+1 ?>.</td>
                                    <td><?=$data['nim']?></td>
                                    <td><?=$data['name']?></td>
                                    <td data-internship_status="{{ $data['status'] }}">
                                        <span class="d-none">{{ $data['status'] }}</span> {!! $status !!}</td>
                                    <td><?= date('D, d - M - Y', strtotime($data['start_date']) )?></td>
                                    <td><?= date('D, d - M - Y', strtotime($data['end_date']) )?></td>
                                    <td><?= $registered_via ?></td>

                                    <td>

                                        @if ( in_array(strtolower($data['status']), ['yes', 'finish'] ) )
                                        <button class="btn btn-info btn-sm" onclick="window.location.href = 'index.php?page=detail_intern_management&id_internship=<?= base64_encode($data['id_internship']) ?>'"><i class="fas fa-info-circle mr-1"></i>Internship Details</button>
                                        @else
                                        <button class="btn btn-info btn-sm" onclick="window.location.href = 'index.php?page=validate_intern&id_internship=<?= base64_encode($data['id_internship']) ?>'"><i class="fas fa-check mr-1"></i>Validate Internship</button>
                                        
                                        @endif

                                </tr>

                                @endforeach
                            </tbody>
                        </table>
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

<script>
    $(document).ready(function() {


        let table = $('#basic-datatables').DataTable({
            "responsive": true
            , "pageLength": 15
            , "lengthMenu": [10, 15, 25, 50]

        });


        setTimeout(function() {

            setTimeout(function() {
                $('#internship-filter').prop('disabled', false);
            }, 1000);
        }, 500);

        $('#internship-filter').on('change', function() {
            var selected = $(this).val(); // Ambil nilai yang dipilih

            // Lakukan pencarian berdasarkan atribut data-internship_status
            table.column(3).search(selected).draw(); // Ganti 3 dengan index kolom yang sesuai
        });


    });

</script>
@endpush
