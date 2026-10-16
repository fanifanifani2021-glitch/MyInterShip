

<?php $__env->startPush('head-scripts'); ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
<style>
    .modal-content { border-radius: 8px; border: none; }
    .form-group label { font-weight: 600; color: #575962; font-size: 13px; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="container">
    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Helpdesk & Support Center</h2>
                    <h5 class="text-white op-7 mb-2">Submit your issues or inquiries regarding internship procedures</h5>
                </div>
                <div class="ml-md-auto py-2 py-md-0">
                    <a href="<?php echo e(router('student.helpdesk.create')); ?>" class="btn btn-secondary btn-round">
                        <i class="fa fa-plus mr-1"></i> Create New Ticket
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
                        <div class="card-title">My Support Tickets</div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="helpdesk-table" class="table table-striped table-hover w-100">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">No.</th>
                                        <th>Ticket Code</th>
                                        <th>Subject / Issue</th>
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

<?php $__env->stopSection(); ?>

<?php $__env->startPush('footer-scripts'); ?>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let table;
    let _token = $('meta[name="csrf-token"]').attr('content');

    $(document).ready(function() {
        table = $('#helpdesk-table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?php echo e(router('student.helpdesk.datatable')); ?>",
                type: "POST",
                data: { _token: _token }
            },
            columns: [
                { data: 'no', width: '5%' },
                { data: 'ticket_code' },
                { data: 'subject' },
                { data: 'category' },
                { data: 'priority' },
                { data: 'status' },
                { data: 'created_at' },
                { data: 'aksi', orderable: false, searchable: false, className: 'text-center' }
            ],
            language: {
                searchPlaceholder: "Search ticket...",
                processing: '<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i>'
            }
        });

        window.open_modal_ticket = function() {
            $('#form-ticket')[0].reset();
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            $('#modal-ticket').modal('show');
        };

        $('#form-ticket').on('submit', function(e) {
            e.preventDefault();
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');

            Swal.fire({
                title: 'Submitting ticket...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            let formData = new FormData(this);

            $.ajax({
                url: "<?php echo e(router('student.helpdesk.store')); ?>",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire('Success!', res.message, 'success');
                        $('#modal-ticket').modal('hide');
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
                    Swal.fire('Error', 'An error occurred on the server.', 'error');
                }
            });
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('templates.student', ['title' => 'Helpdesk Support'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/student/helpdesk/index.blade.php ENDPATH**/ ?>