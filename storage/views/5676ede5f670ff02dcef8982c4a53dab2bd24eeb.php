


<?php $__env->startPush('head-scripts'); ?>
<?php
$access = $_GET['access'];

if ($access == '') {
$access = 'All';
}

$status = $_GET['status'];

if ($status == '') {
$status = 'All';
}



$id_kampus = $_SESSION['CDC_Data']['id_kampus'];

$query = "SELECT * FROM company ";



if ($access == '' || $access === NULL || $access == 'All') {
$access_text = 'All';
$access_value = 'All';
} else {

$access_value = $access;
$access_text = $access;

$query .= " AND access_type = '$access_value'";
}


if ($status == '' || $status === NULL || $status == 'All') {
$status_text = 'All';
$status_value = 'All';
} else {
$status_value = $status;
$status_text = $status;

if ($access == '' || $access === NULL || $access == 'All') {
$query .= " AND `status` = '$status_value'";
} else {
$query .= " AND `status` = '$status_value'";
}
}

$query .= " ORDER BY id_company DESC";


$stmt = $_SERVER['PDO']->query($query);
$stmt->execute();

$sql = $stmt->fetchAll( PDO::FETCH_ASSOC );

?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="container">

    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Company Management</h2>
                    <h5 class="text-white op-7 mb-2">Managing and Validating Company</h5>
                </div>
                <div class="ml-md-auto py-2 py-md-0">
                    <a href="index.php?page=export_company" class="btn btn-primary btn-round"><i class="fa fa-file-excel mr-2"></i> Export</a>
                    <a href="index.php?page=import_company" class="btn btn-primary btn-round"><i class="fa fa-file-excel mr-2"></i> Import</a>
                    <a href="index.php?page=add_company" class="btn btn-primary btn-round"><i class="fa fa-plus mr-2"></i> Add Company</a>
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
                            <span class="text-sm">Company List</span>

                            <button data-toggle="modal" data-target="#ModalFilter" class="btn btn-primary btn-round btn-sm float-right"><i class="fas fa-filter mr-1"></i>Filter</button>

                            <div class="modal fade" id="ModalFilter" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Filter Company</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body form-row">
                                            <div class="form-group col-md-6">
                                                <label>Access Type</label>
                                                <select class="form-control" id="access_type">
                                                    <option value="<?= $access_value ?>" hidden><?= $access_text ?></option>
                                                    <option value="All">All</option>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>

                                                </select>
                                                <small class="invalid-feedback text-sm text-danger">Choose one</small>
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label>Status</label>
                                                <select class="form-control" id="status">
                                                    <option value="<?= $status_value ?>" hidden><?= $status_text ?></option>
                                                    <option value="All">All</option>
                                                    <option value="Verified">Verified</option>
                                                    <option value="Not Verified">Not Verified</option>
                                                    <option value="Blacklist">Blacklist</option>
                                                </select>
                                                <small class="invalid-feedback text-sm text-danger">Choose one</small>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Close</button>
                                            <button type="button" id="btn_filter" onclick="cek_filter()" class="btn btn-primary"><i class="fas fa-check mr-1"></i>Apply</button>
                                        </div>

                                        <script type="text/javascript">
                                            function cek_filter() {
                                                let access = $('#access_type');
                                                let status = $('#status');

                                                if (access.val() == '') {
                                                    access.addClass('is-invalid');
                                                }

                                                if (status.val() == '') {
                                                    status.addClass('is-invalid');
                                                }


                                                if (access.val() != '' && status.val() != '') {
                                                    window.location.href = 'index.php?page=company_management&access=' + access.val() + '&status=' + status.val();
                                                }
                                            }

                                        </script>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                    <div class="card-body">
                        <table id="basic-datatables" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>City</th>
                                    <th>Company Stats</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $no = 1;

                                    foreach ($sql as $row) :

                                        if (in_array($row['status'], [1, 'verified', 'Verified'])) {
                                            $status = '<button type="button" class="btn btn-sm btn-success">Verified</button>';
                                        } else
                                                if (in_array($row['status'], [0, 'not verified', 'Not Verified'])) {
                                            $status = '<button type="button" class="btn btn-sm btn-danger">Not Verified</button>';
                                        } else
                                                if (in_array($row['status'], [2, 'blacklist', 'Blacklist'])) {
                                            $status = '<button type="button" class="btn btn-sm btn-dark">Blacklist</button>';
                                        }

                                    ?>
                                <tr>
                                    <td><?= $no ?>.</td>
                                    <td><?= $row['name'] ?></td>
                                    <td><?= $row['type'] ?></td>
                                    <td><?= $row['city'] ?></td>

                                    <td><button class="btn btn-info btn-sm" onclick="company_stats('<?= base64_encode($row['id_company']) ?>')"><i class="fas fa-info-circle mr-2"></i>Company Stats</button></td>
                                    <!-- <td><?= $row['access_type'] ?> <i class="ml-2 fas fa-info-circle" data-toggle="tooltip" data-placement="top" title="1 - Can Make Offer; 2 - Cannot Make Offer"></i></td> -->
                                    <td><?= $status ?></td>
                                    <td>
                                        <div class="btn-group btn-round">
                                            <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Action
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">

                                                <button onclick="window.open('index.php?page=company_detail&id=<?= base64_encode($row['id_company']); ?>', '_blank');" type="button" class="dropdown-item text-info">
                                                    <i class="fas fa-info-circle mr-2"></i>Company Detail
                                                </button>

                                                <?php if ($row['id_kampus'] == $_SESSION['CDC_Data']['id_kampus']) : ?>
                                                <div class="dropdown-divider"></div>
                                                <button class="dropdown-item text-danger" data-toggle="modal" data-target="#ModalHapus" onclick="modal_hapus('<?php echo $row['id_company']; ?>')"><i class="fas fa-trash-alt mr-2"></i>Delete Company</button>
                                                <?php endif; ?>

                                            </div>
                                        </div>

                                    </td>
                                </tr>
                                <?php
                                        $no++;
                                    endforeach; ?>


                            </tbody>
                        </table>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


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
            <div class="modal-body">
                <form action="index.php?form=company&aksi=hapus" method="POST">
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?>">
                    <input type="hidden" id="id_hapus" name="id_hapus" value="">
                    Are You Sure to Delete Current Company Data ?
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fas fa-times mr-2"></i>Cancel</button>
                <button type="submit" class="btn btn-primary pull-right"><i class="fas fa-trash-alt mr-2"></i>Delete</button>
            </div>
            </form>

        </div>
    </div>
</div>
<!-- tutup modal -->


<!-- Modal Stats -->
<div id="CompanyStats"></div>
<!-- End Modal Stats -->

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

<!-- Chart JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    $(document).ready(function() {
        let table = $('#basic-datatables').DataTable({
            "responsive": true
            , "pageLength": 15
            , "lengthMenu": [10, 15, 25, 50]

        });
    });

    let _token = $('meta[name="csrf-token"]').attr('content');

    function modal_hapus(id) {
        var id_hapus = document.getElementById('id_hapus');
        id_hapus.value = id;
    }

    function company_stats(id) {

        $.ajax({
            url: "index.php?request=company_stats"
            , method: "POST"
            , data: {
                id: id
                , token: _token
            }
            , success: function(data) {
                $('#CompanyStats').html(data);
                $('#ModalStats').modal('show');
            }
            , error: function(data) {
                // console.log('hagal');

                swal.fire({

                    title: 'Error'
                    , text: 'Company Statistics is not available',
                    // footer              :  '300',
                    icon: 'error'
                    , timer: 2000
                    , showConfirmButton: true
                });
            }
        });



    }

</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('templates.cdc', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon2\www\pblKita\views/cdc/company_management/index.blade.php ENDPATH**/ ?>