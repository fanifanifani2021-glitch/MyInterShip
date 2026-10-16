


<?php $__env->startSection('main-content'); ?>
<?php
$sql = "SELECT internship.*, student_internship.*, applicant.id_applicant
FROM internship
INNER JOIN student_internship ON student_internship.nim = internship.nim
LEFT JOIN applicant ON applicant.id_internship = internship.id_internship
WHERE internship.id_company = :id_company
AND internship.status IN ('waiting', 'Waiting')";

$stmt = $_SERVER['PDO']->prepare($sql); // Menggunakan $pdo yang sudah diinisialisasi sebelumnya
$stmt->execute(['id_company' => $_SESSION['id_company']]);

$intern_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<div class="container">

    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Intern Candidate</h2>
                    <h5 class="text-white op-7 mb-2">Managing Intern Candidate</h5>
                </div>

            </div>
        </div>
    </div>

    <div class="page-inner mt--5">
        <div class="row mt--2">
            <div class="col-md-12">

                <div class="card full-height">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">Intern Candidate List</h4>
                        </div>
                    </div>
                    <div class="card-body">


                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="apply-filter">Filter by Apply:</label>
                                <select id="apply-filter" class="form-control" disabled>
                                    <option value="">All</option>
                                    <option>Offer</option>
                                    <option>Cooperation</option>
                                </select>
                            </div>

                        </div>


                        <table id="basic-datatables" class=" table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>NIM</th>
                                    <th>Student Name</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Apply Via</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-staff">
                                <?php $__currentLoopData = $intern_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                <?php
                                // Cek Applicant
                                if ( $data['id_applicant'] !== NULL ) {
                                $registered_via = 'Offer';
                                } else {
                                $registered_via = 'Cooperation';
                                }
                                ?>

                                <tr>
                                    <td><?=$i+1 ?>.</td>
                                    <td><?=$data['nim']?></td>
                                    <td><?=$data['name']?></td>
                                    <td><?= date('D, d - M - Y', strtotime($data['start_date']) )?></td>
                                    <td><?= date('D, d - M - Y', strtotime($data['end_date']) )?></td>
                                    <td><?= $registered_via ?></td>

                                    <td>
                                        <button class="btn btn-secondary btn-sm" onclick="window.location.href = 'index.php?page=validate_intern&id_internship=<?= base64_encode($data['id_internship']) ?>'"><i class="fas fa-check-double mr-1"></i>Validate Data</button>

                                </tr>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
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

<script>
    $(document).ready(function() {


        let table = $('#basic-datatables').DataTable({
            "responsive": true
            , "pageLength": 15
            , "lengthMenu": [10, 15, 25, 50]
        });


        setTimeout(function() {

            setTimeout(function() {
                $('#apply-filter').prop('disabled', false);
            }, 1000);
        }, 500);

        $('#apply-filter').on('change', function() {
            var selected = $(this).val(); // Ambil nilai yang dipilih

            // Filter DataTable berdasarkan nilai yang dipilih
            table.column(5).search(selected).draw(); // Ganti 4 dengan index kolom yang sesuai
        });


    });

</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('templates.industry', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/industry/intern_candidate/index.blade.php ENDPATH**/ ?>