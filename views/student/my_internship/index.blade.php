@extends('templates.student')

@push('head-scripts')

<!-- CSS Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('main-content')
<div class="container">
    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">My Internship</h2>
                    <h5 class="text-white op-7 mb-2">Internship History</h5>
                </div>
            </div>
        </div>
    </div>


    <div class="page-inner mt--5">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><h4 class="card-title">Internship History List</h4></div>
                    <div class="card-body">
                        {{-- Filter Row --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label>Filter by Company</label>
                                <select id="filterCompany" class="form-control select2">
                                    <option value="">All Companies</option>
                                    @php
                                        $companies = DB::table('company')
                                            ->join('internship', 'company.id_company', '=', 'internship.id_company')
                                            ->where('internship.nim', '=', $_SESSION['nim'])
                                            ->distinct()->select('company.id_company', 'company.name')->get();
                                        foreach($companies as $c) echo "<option value='{$c['id_company']}'>{$c['name']}</option>";
                                    @endphp
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Status</label>
                                <select id="filterStatus" class="form-control select2">
                                    <option value="">All Status</option>
                                    <option value="Yes">Ongoing</option>
                                    <option value="Finish">Finished</option>
                                    <option value="No">Rejected</option>
                                    <option value="Waiting">Waiting for Approval</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button id="btnFilter" class="btn btn-primary btn-block">Filter</button>
                            </div>
                        </div>

                        <table id="internship-table" class="display table table-striped table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Company Name</th>
                                    <th>Status</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
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

<!-- Soloalert -->
<script src='./core/component/soloalert.js'></script>

<!-- JS Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
$(document).ready(function() {
    $('.select2').select2({ theme: 'bootstrap4', width: '100%' });

    let table = $('#internship-table').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "ajax": {
            "url": "<?= router('datatable.student.my_internship') ?>",
            "type": "POST",
            "data": function (d) {
                d.filter_company = $('#filterCompany').val();
                d.filter_status = $('#filterStatus').val();
                d._token = "<?= Session::get('_token') ?>";
            }
        },
        "columnDefs": [
            { "targets": [0, 5], "orderable": false },
            { "targets": 0, "width": "5%" }
        ]
    });

    $('#btnFilter').on('click', function() {
        table.draw();
    });
});

function viewDetail(encryptedId) {
    window.location.href = "<?= base_url('student/internship/') ?>" + encryptedId;
}
</script>
@endpush
