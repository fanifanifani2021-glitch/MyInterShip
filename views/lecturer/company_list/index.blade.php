@extends('templates.lecturer')
@section('main-content')
<div class="container">

    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Company List</h2>
                    <h5 class="text-white op-7 mb-2">List of Company</h5>
                </div>
            </div>
        </div>
    </div>



    <div class="page-inner mt--5">
        <div class="row mt--2">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Company List
                        </div>
                    </div>
                    <div class="card-body">
                            <table aria-label="All Company Data Available in My Internship" id="datatables" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>City</th>
                                        <!-- <th>Access Type</th> -->
                                        <th>Status</th>
                                        <th style="width: 5%">Action</th>
                                    </tr>
                                </thead>
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
        $('#basic-datatables').DataTable({});

        let _token = $('meta[name="csrf-token"]').attr('content');

        let dtable = $('#datatables').DataTable({
            "responsive": true,
            "pageLength": 15,
            "lengthMenu": [10, 15, 25, 50],
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "{{ router('datatable.lecturer.company_list') }}",
                "type": "POST",
                "data": function(d) {
                    d._token = "{{ csrf_token() }}";
                    return d;
                },
                "error": function(jqXHR, textStatus, errorThrown) {
                    swal.fire({
                        title: 'Error ' + jqXHR.status,
                        text: 'Gagal mengambil data perusahaan. Silakan coba lagi.',
                        icon: 'error'
                    });
                }
            },
            "columns": [
                { 
                    "data": null, 
                    "render": function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    } 
                },
                { "data": "name" },
                { "data": "type" },
                { "data": "city" },
                { "data": "status_label" },
                { "data": "aksi" }
            ]
        });
    
    });

</script>

@endpush
