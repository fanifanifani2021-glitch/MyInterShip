

<?php $__env->startPush('head-scripts'); ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
<style>
    .card-stats { border-radius: 10px; border: none; }
    .stat-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="container">
    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Helpdesk Support Management</h2>
                    <h5 class="text-white op-7 mb-2">Monitor, assign, and respond to student inquiries and support tickets</h5>
                </div>
                <div class="ml-md-auto py-2 py-md-0">
                    <a href="<?php echo e(router('cdc.helpdesk.categories.index')); ?>" class="btn btn-secondary btn-round">
                        <i class="fas fa-tags mr-1"></i> Manage Categories
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-inner mt--5">
        
        <div class="row mt--2 mb-3">
            <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round card-warning shadow-sm text-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <i class="fas fa-folder-open fa-3x"></i>
                            </div>
                            <div class="col-7 text-right">
                                <div class="stat-title">Open Tickets</div>
                                <h2 class="fw-bold mb-0 text-white"><?php echo e($stats['open']); ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round card-info shadow-sm text-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <i class="fas fa-spinner fa-3x"></i>
                            </div>
                            <div class="col-7 text-right">
                                <div class="stat-title">In Progress</div>
                                <h2 class="fw-bold mb-0 text-white"><?php echo e($stats['in_progress']); ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round card-success shadow-sm text-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <i class="fas fa-check-circle fa-3x"></i>
                            </div>
                            <div class="col-7 text-right">
                                <div class="stat-title">Resolved</div>
                                <h2 class="fw-bold mb-0 text-white"><?php echo e($stats['resolved']); ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round card-secondary shadow-sm text-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <i class="fas fa-lock fa-3x"></i>
                            </div>
                            <div class="col-7 text-right">
                                <div class="stat-title">Closed</div>
                                <h2 class="fw-bold mb-0 text-white"><?php echo e($stats['closed']); ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="row">
            <div class="col-md-12">
                <div class="card full-height">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title">Incoming Support Tickets</div>
                        <div>
                            <button data-toggle="modal" data-target="#ModalFilter" class="btn btn-primary btn-round btn-sm">
                                <i class="fas fa-filter mr-1"></i> Filter Data
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="cdc-helpdesk-table" class="table table-striped table-hover w-100">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">No.</th>
                                        <th>Ticket Code</th>
                                        <th>Submitted By</th>
                                        <th>Subject</th>
                                        <th>Category</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th style="width: 12%" class="text-center">Action</th>
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
                <h5 class="modal-title fw-bold"><i class="fas fa-filter mr-2 text-primary"></i>Filter Tickets</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body form-row">
                <div class="form-group col-md-12 mb-2">
                    <label>Status</label>
                    <select class="form-control" id="filter_status">
                        <option value="All">All Statuses</option>
                        <option value="Open">Open</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Resolved">Resolved</option>
                        <option value="Closed">Closed</option>
                    </select>
                </div>
                <div class="form-group col-md-12 mb-2">
                    <label>Category</label>
                    <select class="form-control" id="filter_category">
                        <option value="All">All Categories</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cat->id_category); ?>"><?php echo e($cat->category_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="form-group col-md-12 mb-2">
                    <label>Priority</label>
                    <select class="form-control" id="filter_priority">
                        <option value="All">All Priorities</option>
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                        <option value="Urgent">Urgent</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="apply_filter()" class="btn btn-primary fw-bold"><i class="fas fa-check mr-1"></i> Apply Filter</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('footer-scripts'); ?>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let table;
    let _token = $('meta[name="csrf-token"]').attr('content');

    $.fn.dataTable.ext.errMode = 'none';

    $(document).ready(function() {
        table = $('#cdc-helpdesk-table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?php echo e(router('cdc.helpdesk.datatable')); ?>",
                type: "POST",
                data: function(d) {
                    d._token   = _token;
                    d.status   = $('#filter_status').val();
                    d.category = $('#filter_category').val();
                    d.priority = $('#filter_priority').val();
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    Swal.fire('DataTables Error', 'Failed to fetch tickets data.', 'error');
                }
            },
            columns: [
                { data: 'no', width: '5%' },
                { data: 'ticket_code' },
                { data: 'user_info' },
                { data: 'subject' },
                { data: 'category' },
                { data: 'priority' },
                { data: 'status' },
                { data: 'created_at' },
                { data: 'aksi', orderable: false, searchable: false, className: 'text-center' }
            ]
        });

        window.apply_filter = function() {
            table.ajax.reload();
            $('#ModalFilter').modal('hide');
        };
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('templates.cdc', ['title' => 'Helpdesk Ticket Management'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/cdc/helpdesk/index.blade.php ENDPATH**/ ?>