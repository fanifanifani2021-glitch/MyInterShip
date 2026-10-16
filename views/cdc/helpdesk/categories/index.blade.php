@extends('templates.cdc')

@push('head-scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">

<style>
    /* Status Badge Minimalis */
    .badge-status { font-size: 10px; padding: 4px 10px; border-radius: 4px; font-weight: 600; text-transform: uppercase; }
    .badge-active { background: #e1fcef; color: #27ae60; }
    .badge-expired { background: #fef1f2; color: #eb5757; }
    
    /* Tabel Simpel */
    #categories-datatables { border: none !important; }
    #categories-datatables thead th { border-bottom: 2px solid #ebedf2 !important; color: #495057; font-weight: 600; }
    
    /* Modal Clean */
    .modal-content { border-radius: 8px; border: none; }
    .modal-header { padding: 1.5rem; border-bottom: 1px solid #f2f2f2; }
    .modal-body { padding: 1.5rem; }
    .form-group label { font-weight: 600; color: #575962; font-size: 13px; }
</style>
@endpush

@section('main-content')
<div class="container">
    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Ticket Categories Management</h2>
                    <h5 class="text-white op-7 mb-2">Manage categories for support helpdesk tickets</h5>
                </div>
                <div class="ml-md-auto py-2 py-md-0">
                    <button class="btn btn-primary btn-round" onclick="add_category()">
                        <i class="fa fa-plus mr-1"></i> Add Category
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="page-inner mt--5">
        <div class="row mt--2">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">All Categories</div>
                    </div>
                    <div class="card-body">
                        <table id="categories-datatables" class="table table-striped table-bordered w-100">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">No.</th>
                                    <th style="width: 25%;">Category Name</th>
                                    <th>Description</th>
                                    <th style="width: 15%;" class="text-center">Status</th>
                                    <th style="width: 15%;" class="text-center">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL FORM --}}
<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modal-title">Category Details</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="main-form">
                <?= csrf() ?>
                <input type="hidden" name="id" id="field-id">
                <div class="modal-body">
                    <div class="form-group p-0 mb-3">
                        <label>Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="category_name" id="field-category_name" class="form-control" placeholder="e.g., Logbook & Attendance" required autocomplete="off">
                        <div class="invalid-feedback" id="error-category_name"></div>
                    </div>
                    <div class="form-group p-0 mb-3">
                        <label>Description</label>
                        <textarea name="category_description" id="field-category_description" class="form-control" rows="3" placeholder="Short description of this category..."></textarea>
                        <div class="invalid-feedback" id="error-category_description"></div>
                    </div>
                    <div class="form-check p-0 mt-2">
                        <label class="form-check-label">
                            <input class="form-check-input" type="checkbox" name="is_active" id="field-is_active" value="1">
                            <span class="form-check-sign">Set active status for this category</span>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold" id="btn-save">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('footer-scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        let _token = $('meta[name="csrf-token"]').attr('content');

        // 1. INISIALISASI DATATABLE
        let table = $('#categories-datatables').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ router('datatable.cdc.helpdesk.categories') }}",
                type: "POST",
                data: { _token: _token }
            },
            columns: [
                { data: 'no', width: '5%' },
                { data: 'category_name' },
                { data: 'category_description' },
                { data: 'status', className: 'text-center' },
                { data: 'aksi', orderable: false, searchable: false, className: 'text-center' }
            ],
            language: {
                searchPlaceholder: "Search category...",
                processing: '<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i>'
            }
        });

        // 2. FUNGSI ADD
        window.add_category = function() {
            $('#main-form')[0].reset();
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            $('#field-id').val('');
            $('#field-is_active').prop('checked', true);
            $('#modal-title').text('Create Ticket Category');
            $('#modal-form').modal('show');
        };

        // 3. FUNGSI EDIT
        window.edit_data = function(id) {
            $('#main-form')[0].reset();
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');

            Swal.fire({
                title: 'Fetching data...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: "{{ router('ajax.cdc.helpdesk.categories.detail') }}",
                type: "POST",
                data: { id: id, _token: _token },
                dataType: "json",
                success: function(res) {
                    Swal.close();
                    $('#field-id').val(res.id_category);
                    $('#field-category_name').val(res.category_name);
                    $('#field-category_description').val(res.category_description);
                    $('#field-is_active').prop('checked', res.is_active == 1);

                    $('#modal-title').text('Update Ticket Category');
                    $('#modal-form').modal('show');
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Failed to fetch record details.', 'error');
                }
            });
        };

        // 4. SAVE DATA (Create / Update)
        $('#main-form').on('submit', function(e) {
            e.preventDefault();
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');

            Swal.fire({
                title: 'Saving changes...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            let formData = new FormData(this);
            formData.set('is_active', $('#field-is_active').is(':checked') ? 1 : 0);

            $.ajax({
                url: "{{ router('ajax.cdc.helpdesk.categories.save') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire('Success!', res.message, 'success');
                        $('#modal-form').modal('hide');
                        table.ajax.reload(null, false);
                    } else if (res.status === 'validation_error') {
                        Swal.close();
                        $.each(res.errors, function(field, message) {
                            $('#field-' + field).addClass('is-invalid');
                            $('#error-' + field).text(message);
                        });
                    } else {
                        Swal.fire('Failed', res.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Internal Server Error occurred.', 'error');
                }
            });
        });

        // 5. TOGGLE STATUS
        window.toggle_status = function(id) {
            $.ajax({
                url: "{{ router('ajax.cdc.helpdesk.categories.toggle_status') }}",
                type: "POST",
                dataType: "json",
                data: { id: id, _token: _token },
                success: function(res) {
                    if (res.status === 'success') {
                        table.ajax.reload(null, false);
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to change status.', 'error');
                }
            });
        };

        // 6. DELETE DATA
        window.delete_data = function(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This category will be permanently deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ router('ajax.cdc.helpdesk.categories.destroy') }}",
                        type: "POST",
                        dataType: 'json',
                        data: {
                            id: id,
                            _method: 'DELETE',
                            _token: _token
                        },
                        success: function(res) {
                            if (res.status === 'success') {
                                Swal.fire('Deleted!', res.message, 'success');
                                table.ajax.reload(null, false);
                            } else {
                                Swal.fire('Error!', res.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Server returned an invalid response.', 'error');
                        }
                    });
                }
            });
        };
    });
</script>
@endpush