

<?php $__env->startPush('head-scripts'); ?><?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="container">

    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">University Management</h2>
                    <h5 class="text-white op-7 mb-2">University Management Exclusively for Politeknik Negeri Batam CDC</h5>
                </div>
                <div class="ml-md-auto py-2 py-md-0">
                    <button aria-label="Add New University" onclick="add_data()" class="btn btn-primary btn-round"><i class="fa fa-plus mr-2"></i> Add Data</button>
                </div>
            </div>
        </div>
    </div>



    <div class="page-inner mt--5">


        <div class="row mt--2">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">University List</div>
                    </div>
                    <div class="card-body">

                        <table id="basic-datatables" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Name</th>
                                    <th>Total Student(s)</th>
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


<?php $__env->stopSection(); ?>

<?php $__env->startPush('footer-scripts'); ?>

<!-- Datatables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

<!-- Datatables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

<!-- JS Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

<script>
    // Ambil token CSRF
    let _token = $('meta[name="csrf-token"]').attr('content');

    // =========================================================================
    // HELPER: GLOBAL ERROR HANDLER
    // =========================================================================
    // Fungsi ini menangani semua error AJAX di satu tempat agar kodenya rapi
    function handleAjaxError(xhr, defaultMessage = "Something went wrong") {
        Swal.close(); // Tutup loading spinner jika masih berputar

        if (xhr.status === 429) {
            // [KHUSUS] Penanganan Error 429 (Rate Limit)
            Swal.fire({
                title: 'Too Many Requests',
                text: 'You are performing actions too quickly. Please wait a moment before trying again.',
                icon: 'warning',
                timer: 5000,
                timerProgressBar: true,
                showConfirmButton: true
            });
        } else {
            // Penanganan Error Standar (500, 404, 403, dll)
            let msg = defaultMessage;
            
            // Coba ambil pesan spesifik dari server jika ada
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }

            Swal.fire({
                title: 'Error',
                text: msg,
                icon: 'error',
                timer: 3000,
                showConfirmButton: true
            });
        }
    }

    // =========================================================================
    // 1. DATATABLE CONFIGURATION
    // =========================================================================
    let table = $('#basic-datatables').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: "<?php echo e(router('datatable.cdc.university')); ?>",
            type: "POST",
            // Mengirim token via Header (Best Practice) atau Body
            headers: { 'X-CSRF-TOKEN': _token }, 
            data: { _token: _token }, // Fallback untuk body
            
            // [UPDATE] Error handling khusus Datatable
            error: function (xhr, error, code) {
                handleAjaxError(xhr, "Failed to load data table");
                // Matikan indikator loading datatable manual karena error
                $('#basic-datatables_processing').hide();
            }
        },
        columns: [
            { data: 'no' },
            { data: 'nama_kampus' },
            { data: 'jumlah_mahasiswa' },
            { data: 'aksi', orderable: false, searchable: false }
        ]
    });

    // =========================================================================
    // 2. ADD DATA FUNCTION
    // =========================================================================
    function add_data() {
        Swal.fire({
            title: 'Add New University',
            html: `<label class="swal2-label">University Name</label>
                   <input type="text" id="university" class="swal2-input" placeholder="Enter University Name">`,
            confirmButtonText: 'Add Data',
            showCancelButton: true,
            focusConfirm: false,
            preConfirm: () => {
                const university = Swal.getPopup().querySelector('#university').value;
                if (!university) {
                    Swal.showValidationMessage(`Please enter University Name !`);
                }
                return { university };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();

                $.ajax({
                    url: "<?php echo e(router('ajax.cdc.university.submit')); ?>",
                    type: "POST",
                    data: {
                        _token: _token, // Gunakan _token agar konsisten
                        university: result.value.university,
                    },
                    dataType: 'json',
                    success: function(response) {
                        reloadTable();
                        Swal.fire({
                            title: 'Success',
                            text: 'New University Successfully Added!',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, 'Failed to add new university');
                    }
                });
            }
        });
    }

    // =========================================================================
    // 3. EDIT DATA FUNCTION
    // =========================================================================
    function edit_data(id_kampus, nama_kampus) {
        Swal.fire({
            title: 'Edit University Data',
            html: `<label class="swal2-label">University Name</label>
                   <input type="text" id="university" class="swal2-input" placeholder="Enter University Name"
                   value="` + Base64.decode(nama_kampus) + `">`,
            confirmButtonText: 'Edit Data',
            showCancelButton: true,
            focusConfirm: false,
            preConfirm: () => {
                const university = Swal.getPopup().querySelector('#university').value
                if (!university) {
                    Swal.showValidationMessage(`Please enter University Name !`);
                }
                return { university: university }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();

                $.ajax({
                    url: "<?php echo e(router('ajax.cdc.university.update')); ?>",
                    type: "POST",
                    data: {
                        _token: _token,
                        _method: "PUT",
                        id_kampus: Base64.decode(id_kampus),
                        university: result.value.university
                    },
                    dataType: 'json',
                    success: function(data) {
                        reloadTable();
                        Swal.fire({
                            title: 'Success',
                            text: 'University Successfully Edited!',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: true
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, 'Failed to edit university');
                    }
                });
            }
        })
    }

    // =========================================================================
    // 4. DELETE DATA FUNCTION
    // =========================================================================
    function delete_data(id) {
        Swal.fire({
            title: 'Delete Confirmation',
            text: 'Are you sure you want to delete current University?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();

                $.ajax({
                    url: "<?php echo e(router('ajax.cdc.university.destroy')); ?>",
                    type: "POST",
                    data: {
                        _token: _token,
                        _method: "DELETE",
                        id_kampus: Base64.decode(id)
                    },
                    dataType: 'json',
                    success: function(response) {
                        reloadTable();
                        Swal.fire({
                            title: 'Delete Success',
                            text: 'University Successfully Deleted!',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: true
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, 'Failed to delete university');
                    }
                });
            }
        })
    }

    // =========================================================================
    // HELPER: RELOAD TABLE & LOADING UI
    // =========================================================================
    
    function showLoading() {
        Swal.fire({
            title: 'Processing...',
            text: 'Please wait while the data is being processed.',
            icon: 'info',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });
    }

    function reloadTable() {
        const currentPage = table.page();
        
        table.ajax.reload(function () {
            // Logic agar tidak mental ke halaman kosong jika data di page terakhir dihapus semua
            const pageSize = table.page.info().length;
            const totalRecords = table.page.info().recordsDisplay;
            const totalPages = Math.ceil(totalRecords / pageSize);
            
            let goToPage = currentPage;
            if (totalRecords > 0 && currentPage >= totalPages) {
                goToPage = totalPages - 1;
            }
            
            // Draw ulang di halaman yang benar
            table.page(goToPage).draw('page');
        }, false);
    }

</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('templates.cdc', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon2\www\pblKita\views/cdc/university_management/index.blade.php ENDPATH**/ ?>