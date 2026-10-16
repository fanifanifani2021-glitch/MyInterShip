@extends('templates.cdc')

@push('head-scripts')
@endpush

@section('main-content')


<div class="container">

    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">CDC Account Management</h2>
                    <h5 class="text-white op-7 mb-2">Managing All CDC Account for All University</h5>
                </div>
                <div class="ml-md-auto py-2 py-md-0">
                    <!-- <a aria-label="Add New UPKPK User" href="#" onclick="window.location.href='index.php?page=import_university';" class="btn btn-primary btn-round"><i class="fa fa-file-import mr-2"></i> Import Data</a> -->
                    <a aria-label="Add New CDC User" href="{{ router('cdc/management.add') }}" class="btn btn-primary btn-round"><i class="fa fa-plus"></i> Add Data</a>

                </div>
            </div>
        </div>
    </div>



    <div class="page-inner mt--5">


        <div class="row mt--2">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">CDC User List</div>
                    </div>
                    <div class="card-body">
                        <table id="basic-datatables" class="table table-striped table-bordered">

                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Status</th>
                                    <th>University</th>
                                    <th>Action</th>
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

<!-- JS Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>


<script>
    let _token = $('meta[name="csrf-token"]').attr('content');
    // Inisialisasi DataTable dan simpan dalam variabel
    let table = $('#basic-datatables').DataTable({
        responsive: true
        , processing: true
        , serverSide: true
        , ajax: {
            url: "index.php?request=datatable&data=cdc_account_data_cdc"
            , type: "POST"
            , data: {
                token: _token
            }
        }
        , columns: [{
                data: 'no'
            }
            , {
                data: 'name'
            }
            , {
                data: 'username'
            }
            , {
                data: 'status'
            }
            , {
                data: 'nama_kampus'
            }
            , {
                data: 'aksi'
                , orderable: false
                , searchable: false
            }
        ]
    });

    function delete_data(id) {
        swal.fire({
            title: 'Delete Confirmation'
            , text: 'Are you sure you want to delete current CDC Account ?'
            , icon: 'question'
            , showCancelButton: true
            , confirmButtonColor: "#DD6B55"
            , confirmButtonText: "Yes, delete it!"
        , }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {

                // Tampilkan indikator loading sebelum AJAX
                Swal.fire({
                    title: 'Processing...'
                    , text: 'Please wait while the data is being processed.'
                    , icon: 'info'
                    , allowOutsideClick: false
                    , showConfirmButton: false
                    , didOpen: () => {
                        Swal.showLoading();
                    }
                });


                // AJAX
                $.ajax({
                    url: "index.php?form=upkpk_management&action=delete"
                    , type: "POST"
                    , data: {
                        token: _token
                        , id: id
                    }
                    , dataType: 'text'
                    , success: function(response) {

                        swal.fire({
                            title: 'Delete Success'
                            , text: 'CDC Account Successfully Deleted !'
                            , icon: 'success'
                            , timer: 2000
                            , showConfirmButton: true
                        });

                        const currentPage = table.page();
                        const pageSize = table.page.info().length;

                        // Reload data dulu
                        table.ajax.reload(function() {
                            const totalRecords = table.page.info().recordsDisplay; // total setelah reload
                            const totalPages = Math.ceil(totalRecords / pageSize);
                            let goToPage = currentPage;

                            if (currentPage >= totalPages) {
                                goToPage = totalPages - 1;
                            }

                            table.page(goToPage).draw('page');
                        }, false);
                    }
                    , error: function(response) {
                        Swal.fire({
                            title: 'Error'
                            , text: 'Failed to delete CDC Account'
                            , icon: 'error'
                            , timer: 3000
                            , showConfirmButton: true
                        });

                    }
                });

            }
        })

    }

    function reset_password(id) {
        swal.fire({
            title: 'Reset Password Confirmation'
            , text: 'Are you sure you want to Reset Password for current user ?'
            , icon: 'question'
            , showCancelButton: true
            , confirmButtonColor: "#DD6B55"
            , confirmButtonText: "Yes"
        , }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {

                // Tampilkan indikator loading sebelum AJAX
                Swal.fire({
                    title: 'Processing...'
                    , text: 'Please wait while the data is being processed.'
                    , icon: 'info'
                    , allowOutsideClick: false
                    , showConfirmButton: false
                    , didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // AJAX
                $.ajax({
                    url: "index.php?form=upkpk_management&action=reset_password"
                    , type: "POST"
                    , data: {
                        token: _token
                        , id: id
                    }
                    , dataType: 'text'
                    , success: function(response) {

                        swal.fire({
                            title: 'Reset Success'
                            , text: 'CDC Account Password Successfully resetted !'
                            , icon: 'success'
                            , timer: 2000
                            , showConfirmButton: true
                        });

                        const currentPage = table.page();
                        const pageSize = table.page.info().length;

                        // Reload data dulu
                        table.ajax.reload(function() {
                            const totalRecords = table.page.info().recordsDisplay; // total setelah reload
                            const totalPages = Math.ceil(totalRecords / pageSize);
                            let goToPage = currentPage;

                            if (currentPage >= totalPages) {
                                goToPage = totalPages - 1;
                            }

                            table.page(goToPage).draw('page');
                        }, false);
                    }
                    , error: function(response) {
                        Swal.fire({
                            title: 'Error'
                            , text: 'Failed to reset CDC Account Password'
                            , icon: 'error'
                            , timer: 3000
                            , showConfirmButton: true
                        });

                    }
                });

            }
        })

    }

</script>


@endpush
