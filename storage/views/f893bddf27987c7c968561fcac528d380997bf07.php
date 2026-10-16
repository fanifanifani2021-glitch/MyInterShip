

<?php $__env->startPush('head-scripts'); ?>
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="container">
    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Internship Letter</h2>
                    <h5 class="text-white op-7 mb-2">
                        View your internship letter requests and download them once approved.
                    </h5>
                </div>
            </div>
        </div>
    </div>

    <div class="page-inner mt--5">
        <div class="row mt--2">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title">Internship Letter List</div>
                        <a href="<?php echo e(router('student.internship_letter.create')); ?>" class="btn btn-primary btn-round btn-sm">
                            <i class="fas fa-plus mr-1"></i> Request New Letter
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="internshipLetterTable" class="display table table-striped table-hover w-100">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th style="width: 100px;">Created Date</th>
                                        <th>Company</th>
                                        <th>Letter Status</th>
                                        <th>Coordinator Approval</th>
                                        <th>CDC Approval</th>
                                        <th>Acceptance Status</th>
                                        <th>Language</th>
                                        <th>Period</th>
                                        <th style="width: 80px;" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div> <!-- end card-body -->
                </div> <!-- end card -->
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('footer-scripts'); ?>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

<script>
let _token = $('meta[name="csrf-token"]').attr('content');

$(document).ready(function () {
    const table = $('#internshipLetterTable').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        order: [[1, "desc"]], // urut berdasarkan tanggal terbaru
        ajax: {
            url: "<?php echo e(router('datatable.student.my_internship_letter')); ?>",
            type: "POST",
            data: function(d) {
                d._token = _token;
            }
        },
        columns: [
            { data: "no", width: "5%" },
            { data: "created_at" },
            { data: "company_name" },
            { data: "status", orderable: false },
            { data: "coor_approval", orderable: false },
            { data: "cdc_approval", orderable: false },
            { data: "acceptance_status", orderable: false },
            { data: "language" },
            { data: "periode" },
            { 
                data: "actions",
                orderable: false,
                searchable: false,
                className: "text-center"
            }
        ]
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('templates.student', ['title' => 'Internship Letter'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/student/my_internship_letter/index.blade.php ENDPATH**/ ?>