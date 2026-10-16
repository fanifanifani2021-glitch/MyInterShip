


<?php $__env->startPush('head-scripts'); ?>

<!-- CSS Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>



<div class="container">
    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Internship Offer</h2>
                    <h5 class="text-white op-7 mb-2">Internship Offer Available at My Internship</h5>
                </div>
            </div>
        </div>
    </div>



    <div class="page-inner mt--5">
        <div class="row mt--2">

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Internship Offer List</h4>
                    </div>

                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label>Company</label>
                                <select id="filterCompany" class="form-control select2">
                                    <option value="">All Companies</option>

                                    <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($c['id_company']); ?>"><?php echo e($c['name']); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Offer Type</label>
                                <select id="filterOfferType" class="form-control select2">
                                    <option value="Internship" selected>Internship / Magang</option>
                                    <option value="Job">Full-time Job</option>
                                    <option value="Freelance">Freelance</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Status</label>
                                <select id="filterStatus" class="form-control select2">
                                    <option value="Open" selected>Open</option>
                                    <option value="Closed">Closed</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>City</label>
                                <select id="filterCity" class="form-control select2">
                                    <option value="">All Cities</option>

                                    <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($c['city']); ?>"><?php echo e($c['city']); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button id="btnFilter" class="btn btn-primary btn-block"><i class="fas fa-filter"></i> Filter</button>
                            </div>
                        </div>

                        <table id="offer-datatables" class="table table-striped table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Company</th>
                                    <th>Position</th>
                                    <th>Work Period</th> 
                                    <th>City</th>
                                    <th>Job Type</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
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

<!-- Soloalert -->
<script src='./core/component/soloalert.js'></script>

<!-- JS Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- User Custom Script -->
<script>
$(document).ready(function() {
    // Inisialisasi Select2 untuk semua element dengan class .select2
    $('.select2').select2({
        theme: 'bootstrap4',
        placeholder: "Select an option",
        allowClear: true,
        width: '100%'
    });

    let table = $('#offer-datatables').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "ajax": {
            "url": "<?= router('datatable.student.internship_offer') ?>",
            "type": "POST",
            "data": function (d) {
                d.filter_company = $('#filterCompany').val();
                d.filter_city = $('#filterCity').val();
                d.filter_status = $('#filterStatus').val();
                d.filter_offer_type = $('#filterOfferType').val();
                d._token = "<?= Session::get('_token') ?>";
            }
        },
        // Pastikan urutan kolom sesuai dengan <thead>
        "columnDefs": [
            { "targets": [0, 3, 7], "orderable": false },
            { "targets": 0, "width": "5%" }
        ]
    });

    $('#btnFilter').on('click', function() {
        let type = $('#filterOfferType').val();
        
        if (type !== 'Internship' && type !== 'Magang') {
            Swal.fire({
                title: 'Notice!',
                text: 'This is not an internship offer. You can only view details but cannot apply.',
                icon: 'warning',
                confirmButtonColor: '#005b9f',
                confirmButtonText: 'I Understand'
            });
        }
        
        table.draw();
    });
});


// Fungsi Action (Karena Server-side, kita gunakan function global)
function viewDetail(id) {
    window.location.href = 'index.php?page=internship_offer_detail&id_offer=' + id;
}

function applyOffer(id) {
    window.location.href = 'index.php?page=register_offer&id=' + id;
}

function viewReq(company, id) {
    // Implementasikan AJAX Modal di sini untuk mengambil requirement berdasarkan ID
    // Lebih efisien daripada merender 1000 modal di HTML
    alert("Fetching requirement for " + company + " ID: " + atob(id));
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('templates.student', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/student/internship_offer/index.blade.php ENDPATH**/ ?>