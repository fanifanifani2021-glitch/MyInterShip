

<?php $__env->startPush('head-scripts'); ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">

<style>
    /* Styling untuk Status Badge di Table */
    .badge-status {
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .badge-active { background: #e1fcef; color: #27ae60; border: 1px solid #27ae60; }
    .badge-expired { background: #fef1f2; color: #eb5757; border: 1px solid #eb5757; }
    .badge-pending { background: #fff8e6; color: #f2994a; border: 1px solid #f2994a; }

    /* Custom styling untuk modal agar lebih clean */
    .modal-content { border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
    .modal-header { background: #f8f9fa; border-bottom: 1px solid #eee; border-radius: 15px 15px 0 0; }
    
    /* Toggle Switch Style */
    .custom-switch .custom-control-label::before { height: 1.5rem; width: 2.75rem; border-radius: 1rem; }
    .custom-switch .custom-control-label::after { width: calc(1.5rem - 4px); height: calc(1.5rem - 4px); border-radius: 1rem; }
    .custom-switch .custom-control-input:checked ~ .custom-control-label::after { transform: translateX(1.25rem); }
</style>

<style>
    /* Status Badge Minimalis */
    .badge-status { font-size: 10px; padding: 4px 10px; border-radius: 4px; font-weight: 600; text-transform: uppercase; }
    .badge-active { background: #e1fcef; color: #27ae60; }
    .badge-expired { background: #fef1f2; color: #eb5757; }
    .badge-pending { background: #fff8e6; color: #f2994a; }
    
    /* Tabel Simpel */
    #maintenance-datatables { border: none !important; }
    #maintenance-datatables thead th { border-bottom: 2px solid #ebedf2 !important; color: #495057; font-weight: 600; }
    
    /* Modal Clean */
    .modal-content { border-radius: 8px; border: none; }
    .modal-header { padding: 1.5rem; border-bottom: 1px solid #f2f2f2; }
    .modal-body { padding: 1.5rem; }
    .form-group label { font-weight: 600; color: #575962; font-size: 13px; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="container">
    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Maintenance Schedule Management</h2>
                    <h5 class="text-white op-7 mb-2">Schedule and manage system maintenance windows</h5>
                </div>
                <div class="ml-md-auto py-2 py-md-0">
                    <button class="btn btn-primary btn-round" onclick="add_schedule()">
                        <i class="fa fa-plus"></i> Add Schedule
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
                        <div class="card-title">All Scheduled Maintenance</div>
                    </div>
                    <div class="card-body">
                        <table id="maintenance-datatables" class="table table-striped table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Title</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
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

<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modal-title">Schedule Details</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="main-form">
                <?= csrf() ?>
                <input type="hidden" name="id" id="field-id">
                <div class="modal-body">
                    <div class="form-group p-0 mb-3">
                        <label>Maintenance Title</label>
                        <input type="text" name="title" id="field-title" class="form-control" placeholder="e.g., Core Server Update" required>
                        <div class="invalid-feedback" id="error-title"></div>
                    </div>
                    <div class="form-group p-0 mb-3">
                        <label>Short Description</label>
                        <textarea name="description" id="field-description" class="form-control" rows="2" placeholder="Describe the maintenance activities..." required></textarea>
                        <div class="invalid-feedback" id="error-description"></div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group p-0 mb-3">
                                <label>Start Date</label>
                                <input type="datetime-local" name="start_date" id="field-start_date" class="form-control" required>
                                <div class="invalid-feedback" id="error-start_date"></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group p-0 mb-3">
                                <label>End Date</label>
                                <input type="datetime-local" name="end_date" id="field-end_date" class="form-control" required>
                                <div class="invalid-feedback" id="error-end_date"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group p-0 mb-3">
                        <label>Allowed IPs (Optional)</label>
                        <input type="text" name="allowed_ips" id="field-allowed_ips" class="form-control" placeholder="127.0.0.1, 192.168.1.1">
                        <div class="invalid-feedback" id="error-allowed_ips"></div>
                    </div>
                    <div class="form-check p-0 mt-2">
                        <label class="form-check-label">
                            <input class="form-check-input" type="checkbox" name="is_active" id="field-is_active" value="1">
                            <span class="form-check-sign">Activate this schedule immediately</span>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold" id="btn-save">Save Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('footer-scripts'); ?>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        let _token = $('meta[name="csrf-token"]').attr('content');

        // 1. INISIALISASI DATATABLE
        let table = $('#maintenance-datatables').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?php echo e(router('datatable.cdc.maintenance')); ?>",
                type: "POST",
                data: { token: _token }
            },
            columns: [
                { data: 'no', width: '5%' },
                { data: 'title' },
                { data: 'start_date' },
                { data: 'end_date' },
                { data: 'status', className: 'text-center' },
                { data: 'aksi', orderable: false, searchable: false, className: 'text-center' }
            ],
            language: {
                searchPlaceholder: "Search schedule...",
                processing: '<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i>'
            }
        });

        // 2. FUNGSI ADD
        window.add_schedule = function() {
            $('#main-form')[0].reset();
            $('#field-id').val('');
            $('#modal-title').text('Create Maintenance Schedule');
            $('#modal-form').modal('show');
        };

        // 3. FUNGSI EDIT (Fetch Data via AJAX)
        window.edit_data = function(id) {
            // 1. Reset state form dan validasi sebelum mengisi data baru
            $('#main-form')[0].reset();
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            
            // 2. Tampilkan loading state menggunakan SweetAlert2
            Swal.fire({
                title: 'Fetching data...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // 3. Request data ke server
            $.ajax({
                url: "<?php echo e(router('ajax.cdc.maintenance.detail')); ?>",
                type: "post",
                data: { id: id, token: _token },
                dataType: "json",
                success: function(res) {
                    Swal.close(); // Tutup loading

                    // 4. Mapping data ke field form
                    $('#field-id').val(res.id);
                    $('#field-title').val(res.title);
                    $('#field-description').val(res.description);
                    
                    // Format datetime-local (YYYY-MM-DDTHH:MM)
                    if(res.start_date) {
                        let start = res.start_date.replace(" ", "T").substring(0, 16);
                        $('#field-start_date').val(start);
                    }
                    
                    if(res.end_date) {
                        let end = res.end_date.replace(" ", "T").substring(0, 16);
                        $('#field-end_date').val(end);
                    }
                    
                    $('#field-allowed_ips').val(res.allowed_ips);
                    $('#field-is_active').prop('checked', res.is_active == 1);

                    // 5. Update UI Modal
                    $('#modal-title').text('Update Maintenance Schedule');
                    $('#modal-form').modal('show');
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Failed to fetch record details. Please try again.'
                    });
                    console.error(xhr.responseText);
                }
            });
        };

        // 4. SAVE DATA (C-U)
        $('#main-form').on('submit', function(e) {
            e.preventDefault();
            
            // Reset state: hapus class error dan pesan error lama
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
                url: "<?php echo e(router('ajax.cdc.maintenance.save')); ?>",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    let res = JSON.parse(response);
                    
                    if(res.status === 'success') {
                        Swal.fire('Success!', res.message, 'success');
                        $('#modal-form').modal('hide');
                        table.ajax.reload(null, false); 
                    } 
                    else if(res.status === 'validation_error') {
                        Swal.close();
                        // Looping error dari Validator
                        $.each(res.errors, function(field, message) {
                            $('#field-' + field).addClass('is-invalid'); // Tambah border merah
                            $('#error-' + field).text(message); // Tambah teks error
                        });
                    } 
                    else {
                        Swal.fire('Failed', res.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Internal Server Error occurred.', 'error');
                }
            });
        });

        window.delete_data = function(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This schedule will be permanently deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?php echo e(router('ajax.cdc.maintenance.destroy')); ?>",
                        type: "POST", // Tetap POST karena _method dikirim di data
                        dataType: 'json', // Tambahkan ini agar otomatis parsing
                        data: { 
                            id: id, 
                            _method: 'DELETE', 
                            token: _token 
                        },
                        success: function(response) {
                            // Tidak perlu JSON.parse lagi
                            if(response.status === 'success') {
                                Swal.fire('Deleted!', response.message, 'success');
                                table.ajax.reload(null, false);
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            // Jika error, cek di console Network apa isinya
                            console.log(xhr.responseText); 
                            Swal.fire('Error', 'Server returned an invalid response.', 'error');
                        }
                    });
                }
            });
        };
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('templates.cdc', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon2\www\pblKita\views/cdc/maintenance_schedule_management/index.blade.php ENDPATH**/ ?>