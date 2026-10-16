


<?php $__env->startSection('main-content'); ?>

<?php

$id_company = $_SESSION['id_company'];

// Cek Company
$stmt = $_SERVER['PDO']->prepare("SELECT * FROM company WHERE id_company = :id_company LIMIT 1");
$stmt->execute( ['id_company' => $id_company] );
$company_data = $stmt->fetch( PDO::FETCH_ASSOC );

if (!in_array($company_data['status'], ['VERIFIED', 'Verified', 'verified', 1])) {
make_swal2('error', 'Not Allowed', 'Your company not Verified yet !');
redirect_referrer(1);
exit();
}


if (!in_array($company_data['access_type'], [1])) {
make_swal2('error', 'Not Allowed', 'Your company do not have access to this page !');
redirect_referrer(1);
exit();
}



$stmt2 = $_SERVER['PDO']->prepare("SELECT * FROM offer WHERE id_company = :id_company ORDER BY id_offer DESC");
$stmt2->execute( ['id_company' => $id_company] );

$offer_data = $stmt2->fetchAll( PDO::FETCH_ASSOC );


?>


<div class="container">

    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Job Offers</h2>
                    <h5 class="text-white op-7 mb-2">Managing Industry Job Offers</h5>
                </div>


                <?php if ($_SESSION['user_type'] == 'HRD') : ?>
                <div class="ml-md-auto py-2 py-md-0">
                    <button class="btn btn-secondary btn-round" data-toggle="modal" data-target="#ModalTambah"><i class="fa fa-plus mr-1"></i> Add Offer</button>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <div class="page-inner mt--5">
        <div class="row mt--2">
            <div class="col-md-12">

                <div class="card full-height">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">Offer List</h4>
                        </div>
                    </div>
                    <div class="card-body">



                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="offer-filter">Filter by Offer Type:</label>
                                <select id="offer-filter" class="form-control" disabled>
                                    <option value="">All Type</option>
                                    <option>Internship</option>
                                    <option>Part Time</option>
                                    <option>Full Employement</option>
                                </select>
                            </div>

                        </div>


                        <table id="basic-datatables" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Position</th>
                                    <th>Offer Type</th>
                                    <th>City</th>
                                    <th>Job Type</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-staff">
                                <?php
                                    $no = 1;

                                    foreach ($offer_data as $row) :

                                        if ($row['status'] == 'Open') {
                                            $status = "<button type='button' class='btn btn-success btn-sm'>Open</button>";
                                        } else {
                                            $status = "<button type='button' class='btn btn-danger btn-sm'>Closed</button>";
                                        }
                                    ?>
                                <tr>
                                    <td><?= $no++; ?>.</td>
                                    <td><?= $row['position'] ?></td>
                                    <td><?= $row['offer_type'] ?></td>
                                    <td><?= $row['city'] ?></td>
                                    <td><?= $row['job_type'] ?></td>
                                    <td><?= $status ?></td>
                                    <td>
                                        <div class="btn-group btn-round">
                                            <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Action
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">

                                                <button onclick="window.location.href='index.php?page=offer_detail&id=<?= base64_encode($row['id_offer']) ?>'" type="button" class="dropdown-item">
                                                    <i class="fas fa-info-circle mr-2"></i>Offer Detail
                                                </button>

                                                <?php if ($_SESSION['user_type'] == 'HRD') : ?>
                                                <div class="dropdown-divider"></div>
                                                <button class="dropdown-item" data-toggle="modal" data-target="#ModalChange" onclick="modal_change('<?= $row['id_offer']; ?>', '<?= $row['status']; ?>')"><i class="fas fa-sync mr-2"></i>Change Status</button>
                                                <button type="button" data-toggle="modal" data-target="#ModalHapus" onclick="modal_hapus('<?php echo $row['id_offer']; ?>')" class="dropdown-item" data-original-title="Hapus"><i class="fa fa-trash mr-2"></i> Delete
                                                    <?php endif; ?>

                                                </button>

                                            </div>
                                        </div>

                                    </td>
                                </tr>

                                <?php
                                    endforeach;
                                    ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>




            <!-- modal tambah hrd -->
            <div class="modal fade" id="ModalTambah">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <span class="fw-mediumbold">
                                    Add Offer</span>
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <form action="index.php?form=offer&aksi=tambah" method="POST"> <?= csrf() ?> <?= input_hidden('id_company', $_SESSION['id_company']) ?>

                            <div class="modal-body">
                                <?php if ( $_SESSION['old'] ['notifikasi-error'] ) : ?>

                                <div class="form-group">
                                    <label class="btn btn-danger text-white"><?=  old('notifikasi-error') ?></label>
                                </div>

                                <?php endif; ?>

                                <div class="form-group">
                                    <label for="position">Position <label class="text-danger">*</label></label>
                                    <input type="text" placeholder="Fill Position" class="form-control <?= show_error('position'); ?>  " name="position" id="position">
                                    <?= form_error('position'); ?>
                                </div>

                                <div class="form-group">
                                    <label for="description">Description <label class="text-danger">*</label></label>
                                    <textarea class="form-control <?= show_error('description'); ?>" placeholder="Fill Description" id="description" name="description" rows="4"></textarea>
                                    <?= form_error('description'); ?>
                                </div>

                                <div class="form-group">
                                    <label for="requirement">Requirement <label class="text-danger">*</label></label>
                                    <textarea type="text" placeholder="Fill Requirement" class="form-control <?= show_error('requirement'); ?>  " name="requirement" id="requirement"></textarea>
                                    <?= form_error('requirement'); ?>
                                </div>

                                <!--<div class="form-group">-->
                                <!--    <label for="employer_type">Employer Type <label class="text-danger">*</label></label>-->
                                    <input type="hidden" value="-" placeholder="Fill Employer type" class="form-control <?= show_error('employer_type'); ?>  " name="employer_type" id="employer_type">
                                    <!-- <select class="form-control <?= show_error('employer_type'); ?>" name="employer_type" id="employer_type">
									<option value="" hidden>- Choose Employer Type -</option>
									<option>Internship</option>
									<option>Part Time</option>
									<option>Employee</option>
								</select>   -->

                                    <?= form_error('employer_type'); ?>
                                <!--</div>-->

                                <div class="form-group">
                                    <label for="city">City <label class="text-danger">*</label></label>
                                    <input type="text" placeholder="Fill City" class="form-control <?= show_error('city'); ?>  " name="city" id="city">
                                    <?= form_error('city'); ?>
                                </div>

                                <div class="form-group">
                                    <label for="job_type">Job Type <label class="text-danger">*</label></label>
                                    <input type="text" placeholder="Fill Job Type" class="form-control <?= show_error('job_type'); ?>  " name="job_type" id="job_type">
                                    <?= form_error('job_type'); ?>
                                    
                                    <small class="">Work from Office or Work from Anywhere, etc</small>
                                </div>

                                <div class="form-group">
                                    <label for="start_date">Start Date <label class="text-danger">*</label></label>
                                    <input type="date" placeholder="Fill Start Date" class="form-control <?= show_error('start_date'); ?>  " name="start_date" id="start_date">
                                    <?= form_error('start_date'); ?>
                                </div>

                                <div class="form-group">
                                    <label for="end_date">End Date <label class="text-danger">*</label></label>
                                    <input type="date" placeholder="Fill End Date" class="form-control <?= show_error('end_date'); ?>  " name="end_date" id="end_date">
                                    <?= form_error('end_date'); ?>
                                </div>

                                <div class="form-group">
                                    <label for="offer_type">Offer Type <label class="text-danger">*</label></label>
                                    <select class="form-control <?= show_error('offer_type'); ?>" name="offer_type" id="offer_type" onchange="change_allowence()">
                                        <option value="" hidden>- Choose Offer Type -</option>
                                        <option>Full Employement</option>
                                        <option>Part Time</option>
                                        <option>Internship</option>
                                    </select>
                                    <?= form_error('offer_type'); ?>
                                </div>

                                <script>
                                    function change_allowence() {
                                        console.log('hello');
                                        let offer = $('#offer_type').val();
                                        let div = document.getElementById('allowence');
                                        let label = document.getElementById('label_allowence');
                                        let input = document.getElementById('salary_range');

                                        if (offer == 'Internship') {

                                            div.hidden = false;
                                            label.innerHTML = 'Allowence';
                                            input.val = '';
                                            input.placeholder = 'Fill Allowence';

                                        } else if (offer == 'Full Employement') {
                                            div.hidden = false;
                                            label.innerHTML = 'Salary Range';
                                            input.val = '';
                                            input.placeholder = 'Fill Salary Range';

                                        } else {
                                            div.hidden = true;
                                            label.innerHTML = '';
                                            input.val = 0;

                                        }
                                    }

                                </script>


                                <div class="form-group" id="allowence" hidden>
                                    <label for="salary_range" id="label_allowence">Allowence</label>
                                    <input type="text" placeholder="Fill Allowence" id="salary_range" class="form-control <?= show_error('salary_range'); ?>  " name="salary_range" id="salary_range">
                                    <?= form_error('salary_range'); ?>
                                </div>

                            </div>

                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Cancel</button>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Save</button>
                            </div>

                        </form>

                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.tutupmodal -->

            <!-- modal hapus -->
            <div class="modal fade" id="ModalHapus" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header no-bd">
                            <h5 class="modal-title">
                                <span class="fw-mediumbold">
                                    Delete Data</span>
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <form action="index.php?form=offer&aksi=hapus" method="POST"> <?= csrf(); ?>
                            <input type="hidden" id="id_hapus" name="id_hapus" value="">

                            <div class="modal-body">
                                Are You Sure to Delete This Data?
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Cancel</button>
                                <button type="submit" class="btn btn-primary pull-right"><i class="fas fa-trash mr-1"></i>Delete</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
            <!-- tutup modal -->


            <!-- modal hapus -->
            <div class="modal fade" id="ModalChange" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header no-bd">
                            <h5 class="modal-title">
                                <span class="fw-mediumbold">
                                    Change Status</span>
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <form action="index.php?form=offer&aksi=change_status" method="POST"> <?= csrf(); ?>
                            <input type="hidden" id="id_change" name="id_change" value="<?= old('id_change') ?>">

                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Offer Status <b class="text-danger">*</b></label>
                                    <select class="form-control <?= show_error('offer_status') ?>" name="offer_status">
                                        <!-- <option value="" hidden>- Select Offer Status -</option> -->
                                        <option hidden id="offer_status"></option>
                                        <option>Open</option>
                                        <option>Closed</option>
                                    </select>

                                    <?= form_error('offer_status') ?>
                                </div>
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Cancel</button>
                                <button type="submit" class="btn btn-primary pull-right"><i class="fas fa-sync mr-1"></i>Change Status</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
            <!-- tutup modal -->





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
    CKEDITOR.replace('description', {
        removeButtons: 'PasteFromWord,Styles,Format,Font,FontSize'
    , });
    CKEDITOR.replace('requirement', {
        removeButtons: 'PasteFromWord,Styles,Format,Font,FontSize'
    , });

</script>



<script>
    $(document).ready(function() {


        let table = $('#basic-datatables').DataTable({
            "responsive": true
            , "pageLength": 15
            , "lengthMenu": [10, 15, 25, 50]
        });


        setTimeout(function() {

            setTimeout(function() {
                $('#offer-filter').prop('disabled', false);
            }, 1000);
        }, 500);

        $('#offer-filter').on('change', function() {
            var selected = $(this).val(); // Ambil nilai yang dipilih

            // Filter DataTable berdasarkan nilai yang dipilih
            table.column(2).search(selected).draw(); // Ganti 4 dengan index kolom yang sesuai
        });


    });

    function modal_hapus(id) {
        var id_hapus = document.getElementById('id_hapus');
        id_hapus.value = id;
    }

    function modal_change(id, offer) {
        var id_change = document.getElementById('id_change');
        id_change.value = id;

        var offer_status = document.getElementById('offer_status');
        offer_status.value = offer;
        offer_status.innerText = offer;

    }

</script>

<?php if(old('old_tambah') !== NULL): ?>

<script>
    $(document).ready(function() {
        $('#ModalTambah').modal('show');
    });

</script>
<?php endif; ?>


<?php if(old('ModalChange') !== NULL): ?>

<script>
    $(document).ready(function() {
        $('#ModalChange').modal('show');
    });

</script>

<?php endif; ?>


<?php $__env->stopPush(); ?>

<?php echo $__env->make('templates.industry', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp_new\htdocs\pblKita\views/industry/job_offer/index.blade.php ENDPATH**/ ?>