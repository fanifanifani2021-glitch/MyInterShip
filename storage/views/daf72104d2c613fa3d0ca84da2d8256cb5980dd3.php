

<?php $__env->startPush('head-scripts'); ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="container">
    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Announcement Management</h2>
                    <h5 class="text-white op-7 mb-2">Managing My Internship Announcement</h5>
                </div>
                <div class="ml-md-auto py-2 py-md-0">
                    <a href="<?php echo e(router('cdc.announcement.content_manager')); ?>" class="btn btn-primary btn-round">
                        <i class="fa fa-plus mr-1"></i> Add Announcement
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-inner mt--5">
        <div class="row mt--2">
            <div class="col-md-12">
                <div class="card full-height">
                    <div class="card-header">
                        <div class="card-title">
                            <span class="text-sm">Announcement List</span>
                            <button data-toggle="modal" data-target="#ModalFilter" class="btn btn-primary btn-round btn-sm float-right">
                                <i class="fas fa-filter mr-1"></i>Filter
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="ekonomi-kepri" class="display dataTable table table-striped table-hover w-100">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">No.</th>
                                        <th style="width: 10%">Status</th>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Visitor Count</th>
                                        <th>Created By</th>
                                        <th style="width: 10%" class="text-center">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="ModalFilter" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Filter Announcement</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body form-row">
                <div class="form-group col-md-12">
                    <label>Status</label>
                    <select class="form-control" id="filter_status">
                        <option value="All">All</option>
                        <option value="Draft">Draft</option>
                        <option value="Publish">Publish</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Close</button>
                <button type="button" onclick="apply_filter()" class="btn btn-primary"><i class="fas fa-check mr-1"></i>Apply</button>
            </div>
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
    let table;
    let _token = $('meta[name="csrf-token"]').attr('content');

    // Matikan popup alert alert bawaan DataTables
    $.fn.dataTable.ext.errMode = 'none';

    $(document).ready(function() {
        table = $('#ekonomi-kepri').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?php echo e(router('datatable.cdc.announcement.data')); ?>",
                type: "POST",
                data: function(d) {
                    d._token = _token;
                    d.status = $('#filter_status').val();
                },
                // VERBOSE ERROR HANDLER: Tangkap & tampilkan detail error dari server
                error: function (xhr, error, thrown) {
                    console.error("DataTables AJAX Error Response:", xhr.responseText);
                    
                    let errorDetails = xhr.responseText;
                    
                    // Coba parse jika JSON
                    try {
                        let json = JSON.parse(xhr.responseText);
                        errorDetails = `<b>Message:</b> ${json.message || thrown}<br>`;
                        if (json.file) errorDetails += `<b>File:</b> ${json.file}<br>`;
                        if (json.line) errorDetails += `<b>Line:</b> ${json.line}<br>`;
                    } catch (e) {
                        // Jika respon berupa HTML Error (Fatal Error PHP / Stack Trace)
                        errorDetails = `<div style="max-height: 250px; overflow-y: auto; text-align: left; font-family: monospace; font-size: 12px; background: #222; color: #ff5555; padding: 10px; border-radius: 5px;">${xhr.responseText}</div>`;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'DataTables Server Error (500)',
                        html: `<div style="text-align: left; font-size: 13px;">${errorDetails}</div>`,
                        width: '700px',
                        confirmButtonText: 'Close & Check Console'
                    });
                }
            },
            columns: [
                { data: 'no', width: '5%' },
                { data: 'status', width: '10%' },
                { data: 'title' },
                { data: 'category' },
                { data: 'visitor_count' },
                { data: 'created_by' },
                { data: 'aksi', orderable: false, searchable: false, className: 'text-center' }
            ]
        });

        window.apply_filter = function() {
            table.ajax.reload();
            $('#ModalFilter').modal('hide');
        };

        window.delete_data = function(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This announcement will be permanently deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?php echo e(router('ajax.cdc.announcement.destroy')); ?>",
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
                        error: function(xhr) {
                            console.error("Delete Error:", xhr.responseText);
                            Swal.fire('Server Error!', xhr.responseText, 'error');
                        }
                    });
                }
            });
        };
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('templates.cdc', ['title' => 'Announcement Management'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/cdc/announcement_management/index.blade.php ENDPATH**/ ?>