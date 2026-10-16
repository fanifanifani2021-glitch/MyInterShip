@extends('templates.industry')

@push('head-scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

@endpush
@section('main-content')

<div class="container">

    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Attendance Location (Recycle)</h2>
                    <h5 class="text-white op-7 mb-2">
                        Configure allowed attendance locations for active internships (Deleted Data)
                    </h5>

                </div>
            </div>
        </div>
    </div>

    <div class="page-inner mt--5">
        <div class="row mt--2">
            <div class="col-md-12">

                <div class="card full-height">

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Attendance Location (Recycle)</h4>

                        <a class="btn btn-primary btn-sm" href="{{ router('industry/attendance_location.index') }}">
                            <i class="fas fa-arrow-left mr-1"></i> Go Back
                        </a>
                    </div>


                    <div class="card-body">
                        {{-- TABLE --}}
                        <table id="basic-datatables" class="table table-striped table-hover w-100">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Location</th>
                                    <th>Latitude</th>

                                    <th>Longitude</th>
                                    <th>Attendance Radius</th>
                                    <th>Deleted By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- kosong, diisi via AJAX --}}
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

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
    let _token = $('meta[name="csrf-token"]').attr('content');

    // =========================================================================
    // HELPER: GLOBAL ERROR HANDLER
    // =========================================================================
    function handleAjaxError(xhr, defaultMessage = "Something went wrong") {
        Swal.close(); 

        if (xhr.status === 429) {
            Swal.fire({
                title: 'Too Many Requests',
                text: 'You are performing actions too quickly. Please wait a moment.',
                icon: 'warning',
                timer: 4000,
                timerProgressBar: true
            });
        } else {
            let msg = defaultMessage;
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }
            Swal.fire({
                title: 'Error',
                text: msg,
                icon: 'error'
            });
        }
    }

    $(document).ready(function () {

        // =========================================================================
        // 1. DATATABLE CONFIGURATION (RECYCLE BIN)
        // =========================================================================
        let table = $('#basic-datatables').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            searching: true,
            pageLength: 15,
            lengthMenu: [10, 15, 25, 50],
            ajax: {
                url: "{{ router('datatable.industry.attendance_location.recycle') }}",
                type: 'POST',
                // [NEW] Header Token
                headers: { 'X-CSRF-TOKEN': _token },
                data: function (d) {
                    d._token = _token;
                },
                // [NEW] Error Handler
                error: function (xhr, error, code) {
                    handleAjaxError(xhr, "Failed to load recycle bin data");
                    $('#basic-datatables_processing').hide();
                }
            },
            columns: [
                { data: 'no', orderable: false, searchable: false },
                { data: 'location' },
                { data: 'latitude' },
                { data: 'longitude' },
                { data: 'radius_meters' },
                { data: 'deleted_properties' }, // Info kapan dihapus
                { data: 'action', orderable: false, searchable: false }
            ]
        });

        // =========================================================================
        // 2. RESTORE ACTION
        // =========================================================================
        $(document).on('click', '.btn-restore', function () {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Restore location?',
                text: 'This location will be restored and active again.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745', // Green for restore
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, restore'
            }).then((result) => {
                if (!result.isConfirmed) return;

                $.ajax({
                    url: "{{ router('ajax.industry.attendance_location.restore') }}",
                    type: "POST",
                    dataType: "json",
                    // [NEW] Header Token
                    headers: { 'X-CSRF-TOKEN': _token },
                    data: {
                        id_location: id,
                        _token: _token
                    },
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Restoring...',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });
                    },
                    success: function (res) {
                        if(res.status === true) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Restored!',
                                text: res.message ?? 'Location restored successfully',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            table.ajax.reload(null, false);
                        } else {
                            Swal.fire('Failed', res.message, 'warning');
                        }
                    },
                    error: function (xhr) {
                        handleAjaxError(xhr, 'Failed to restore location');
                    }
                });
            });
        });

        // =========================================================================
        // 3. FORCE DELETE ACTION
        // =========================================================================
        $(document).on('click', '.btn-force-delete', function () {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Delete permanently?',
                text: 'This action cannot be undone!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Delete Permanently'
            }).then((result) => {
                if (!result.isConfirmed) return;

                $.ajax({
                    url: "{{ router('ajax.industry.attendance_location.force_delete') }}",
                    type: "POST", // Menggunakan POST dengan _method DELETE
                    dataType: "json",
                    // [NEW] Header Token
                    headers: { 'X-CSRF-TOKEN': _token },
                    data: {
                        id_location: id,
                        _token: _token,
                        _method: 'DELETE'
                    },
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Deleting...',
                            text: 'Permanently removing data',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });
                    },
                    success: function (res) {
                        if(res.status === true) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: res.message ?? 'Location permanently deleted',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            table.ajax.reload(null, false);
                        } else {
                            Swal.fire('Failed', res.message, 'warning');
                        }
                    },
                    error: function (xhr) {
                        handleAjaxError(xhr, 'Failed to delete location');
                    }
                });
            });
        });

    });
</script>
@endpush