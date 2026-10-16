
<?php $__env->startSection('main-content'); ?>

<?php
// Deklarasi variabel
$id = base64_decode($_GET['id_offer']);

try {
    // Ambil instance PDO dari $_SERVER
    $pdo = $_SERVER['PDO'];

    // Siapkan query SQL dengan placeholder
    $sql = "SELECT * FROM company, offer WHERE offer.id_company = company.id_company AND offer.id_offer = :id_offer";

    // Siapkan statement
    $stmt = $pdo->prepare($sql);

    // Bind parameter
    $stmt->bindParam(':id_offer', $id, PDO::PARAM_INT);

    // Eksekusi statement
    $stmt->execute();

    // Ambil hasil
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    // Cek jika data ditemukan
    if ($stmt->rowCount() != 1) {
        make_swal2('error', 'Error', 'Internship Offer is not found!');
        redirect_back(1);
        exit;
    }

    // Tentukan status berdasarkan data
    if ($data['status'] == 'Open') {
        $status = "<button type='button' class='btn btn-success btn-sm'>Open</button>";
    } else {
        $status = "<button type='button' class='btn btn-danger btn-sm'>Closed</button>";
    }

} catch (PDOException $e) {
    // Tangani kesalahan
    make_swal2('error', 'Error', 'Internship Offer is not found!');

    // make_swal2('error', 'Database Error', 'An error occurred: ' . $e->getMessage());
    redirect_back(1);
    exit;
}

?>


<div class="container">
    <div class="page-inner">

        <div class="page-header">
            <h4 class="page-title">Internship Offer Detail</h4>
            <ul class="breadcrumbs">
                <li class="nav-home">
                    <a href="index.php">
                        <i class="flaticon-home"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="flaticon-right-arrow"></i>
                </li>
                <li class="nav-item">
                    <a href="index.php?page=student_offer">Internship Offer</a>
                </li>
                <li class="separator">
                    <i class="flaticon-right-arrow"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Internship Offer Detail</a>
                </li>
            </ul>
        </div>


        <!-- Form -->
        <div class="row">



            <!-- Table Detail -->
            <div class="col-md-12">

                <div class="card full-height">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">Offer Detail</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <tbody>
                                    <tr>
                                        <th width="30%">Company Name <span class="float-right">:</span></th>
                                        <td width="70%"><span><?= $data['name']; ?></span></td>
                                    </tr>
                                    <tr>
                                        <th width="30%">Position <span class="float-right">:</span></th>
                                        <td width="70%"><span><?= $data['position']; ?></span></td>
                                    </tr>

                                    <tr>
                                        <th width="30%">Employer Type <span class="float-right">:</span></th>
                                        <td width="70%"><span><?= $data['employer_type']; ?></span></td>
                                    </tr>
                                    <tr>
                                        <th width="30%">City <span class="float-right">:</span></th>
                                        <td width="70%"><span><?= $data['city']; ?></span></td>
                                    </tr>
                                    <tr>
                                        <th width="30%">Job Type <span class="float-right">:</span></th>
                                        <td width="70%"><span><?= $data['job_type']; ?></span></td>
                                    </tr>

                                    <tr>
                                        <th width="30%">Start Date <span class="float-right">:</span></th>
                                        <td width="70%"><span><?= date('d-m-Y', strtotime($data['start_date'])); ?></span></td>
                                    </tr>
                                    <tr>
                                        <th width="30%">End Date <span class="float-right">:</span></th>
                                        <td width="70%"><span><?= date('d-m-Y', strtotime($data['end_date'])); ?></span></td>
                                    </tr>
                                    <tr>
                                        <th width="30%">Offer Type <span class="float-right">:</span></th>
                                        <td width="70%"><span><?= $data['offer_type']; ?></span></td>
                                    </tr>

                                    <?php if ( !in_array( $data['offer_type'], ['Part Time']) ) : 
                                        if ( $data['offer_type'] == 'Internship') : $label_salary = "Allowence"; else : $label_salary = 'Salary Range'; endif;
                                        ?>

                                    <tr>
                                        <th width="30%"><?= $label_salary ?> <span class="float-right">:</span></th>
                                        <td width="70%"><span><?=$data['salary_range']; ?></span></td>
                                    </tr>

                                    <?php endif; ?>

                                    <tr>
                                        <th width="30%">Offer Status <span class="float-right">:</span></th>
                                        <td width="70%"><span><?= $status; ?></span></td>
                                    </tr>

                                    <tr>
                                        <th width="30%">Date Added <span class="float-right">:</span></th>
                                        <td width="70%"><span><?= date( 'D, d M Y H:i:s' ,strtotime( $data['timestamp_add'] ) ); ?> ( <?= time_elapsed_string( $data['timestamp_add'] ) ?> )</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>



            <!-- Description -->
            <div class="col-md-12">
                <div class="card full-height">
                    <div class="card-header">
                        <div class="d-flex align-items-center float-left">
                            <h4 class="card-title">Description</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php echo htmlspecialchars_decode($data['description']); ?>

                    </div>

                </div>
            </div>


            <!-- Requirement -->
            <div class="col-md-12">
                <div class="card full-height">
                    <div class="card-header">
                        <div class="d-flex align-items-center float-left">
                            <h4 class="card-title">Requirement</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php echo htmlspecialchars_decode($data['requirement']); ?>

                    </div>

                </div>
            </div>





            <!-- Applicant List -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Applicant List
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="basic-datatables" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>NIM</th>
                                        <th>Applicant Name</th>
                                        <th>Status</th>
                                        <th>Apply Date</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php

                                    $sql 	= "SELECT * FROM student_internship, internship, applicant WHERE student_internship.nim = internship.nim AND internship.id_internship = applicant.id_internship AND applicant.id_offer = '$id' ";
                                    $exec = $_SERVER['KONEKSI_DB']->query($sql);
                                    ?>

                                    <?php foreach ($exec as $i => $intern_data) : ?>

                                    <tr>
                                        <td><?= $i + 1 ?>.</td>
                                        <td><?= $intern_data['nim'] ?></td>
                                        <td><?= $intern_data['name'] ?></td>
                                        <td><?= $intern_data['status'] ?></td>
                                        <td><?= date('D, M d Y', strtotime($intern_data['date'])) ?></td>



                                    </tr>

                                    <?php endforeach; ?>

                                </tbody>


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
<!-- Datatables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

<!-- Datatables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

<!-- Soloalert -->
<script src='./core/component/soloalert.js'></script>

<!-- User Custom Script -->
<script>
    $(document).ready(function() {
        // Inisialisasi DataTable
        var table = $('#basic-datatables').DataTable({
            "pageLength": 15
            , "responsive": true

        });

    });

</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('templates.student', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/student/offer_detail.blade.php ENDPATH**/ ?>