@extends('templates.cdc')

@push('head-scripts')

@php
    $id_upkpk = base64_decode($_GET['id']);

$sql = "SELECT * FROM upkpk WHERE id_upkpk = :id_upkpk LIMIT 1";
$stmt = $_SERVER['PDO']->prepare($sql);
$stmt->execute(['id_upkpk' => $id_upkpk]);
if ($stmt->rowCount() != 1) {
    make_swal2('error', 'Not Allowed', 'UPKPK data not found !');
    redirect_referrer(1);
    exit();
}

$data_upkpk = $stmt->fetch( PDO::FETCH_ASSOC );
@endphp
@endpush

@section('main-content')


<div class="container">

    <div class="page-inner">


        <div class="page-header">
            <h4 class="page-title">Edit Existing CDC Account</h4>
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
                    <a href="index.php?page=cdc_management">CDC Account Management</a>
                </li>

                <li class="separator">
                    <i class="flaticon-right-arrow"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Edit CDC</a>
                </li>
            </ul>
        </div>


        <div class="row">

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit CDC Form</h4>
                    </div>

                    <form action="index.php?form=upkpk_management&action=edit" method="POST" id="form_add_lecturer"><?= csrf(); ?>
                        <div class="card-body form-row">


                            <input type="hidden" name="id_upkpk" value="<?= $id_upkpk ?>">
                            <input type="hidden" name="old_username" value="<?= $data_upkpk['username'] ?>">


                            <div class="form-group col-md-12">
                                <label>Username <b class="text-danger">*</b></label>
                                <input type="text" placeholder="Enter CDC Username" class="form-control <?= show_error('username') ?>" required name="username" id="username" value="<?= old('username', $data_upkpk['username']) ?>" />

                                <?= form_error('username') ?>

                            </div>


                            <div class="form-group col-md-6">
                                <label>Name <b class="text-danger">*</b></label>
                                <input type="text" placeholder="Enter CDC Name" class="form-control <?= show_error('name') ?>" required name="name" id="name" value="<?= old('name', $data_upkpk['name']) ?>" />

                            </div>

                            <div class="form-group col-md-6">
                                <label>Status <b class="text-danger">*</b></label>

                                <select name="status" class="form-control <?= show_error('status') ?>">
                                    <option <?= (old('status', $data_upkpk['status']) == 'Active') ? 'selected' : ''; ?>>Active</option>
                                    <option <?= (old('status', $data_upkpk['status']) == 'Not Active') ? 'selected' : ''; ?>>Not Active</option>
                                </select>


                                <?= form_error('status') ?>

                            </div>


                            <div class="form-group col-md-12">
                                <label>University <b class="text-danger">*</b></label>
                                
                                <select class="form-control" name="id_kampus" id="id_kampus">
                                    <?php
                                    $sql = "SELECT * FROM tb_kampus";
                                    $sql = $_SERVER['KONEKSI_DB']->query($sql);

                                    foreach ($sql as $data) : ?>

                                        <option value="<?= $data['id_kampus'] ?>" <?= ( old('id_kampus', $data_upkpk['id_kampus'] ) == $data['id_kampus'] ) ? 'selected' : ''; ?>><?= $data['nama_kampus'] ?></option>

                                    <?php endforeach; ?>
                                </select>
                                <?= form_error('id_kampus') ?>

                            </div>

                        </div>
                        <div class="card-footer form-row">

                            <div class="form-group col-md-12">

                                <button type="button" class="btn btn-danger" onclick="cancel_confirmation('{{ base_url('index.php?page=cdc_management') }}')"><i class="fas fa-times mr-1"></i>Cancel</button>
                                <button type="button" class="btn btn-warning" onclick="reset_confirmation()"><i class="fas fa-sync mr-1"></i>Reset</button>
                                <button type="submit" class="btn btn-success"><i class="fas fa-edit mr-1"></i>Edit</button>

                            </div>
                        </div>
                    </form>


                </div>
            </div>


        </div>


    </div>
</div>


@endsection

@push('footer-scripts')

<!-- Datatables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

<!-- Datatables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

<!-- JS Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>


<!-- Soloalert -->
<script src='./core/component/soloalert.js'></script>


<!-- AJAX Script -->
<script>
    $(document).ready(function() {
        $("#program_study").val("{{ old('program_study') }}");
        $("#program_study").trigger('change');

    });

    function cancel_confirmation(location) {
        // alert('Are you sure you want to cancel ? (Your change will not be saved)');
        SoloAlert.confirm({
            title: "Are you sure you want to cancel ?",
            body: "Changes you made will not be saved !",
            useTransparency: true,
            onOk: () => {
                window.location.href = location;
            }
        });

    }

    function reset_confirmation() {

        SoloAlert.confirm({
            title: "Are you sure you want to reset ?",
            body: "Changes you made will be reset to default !",
            useTransparency: true,
            onOk: () => {
                $("#program_study").trigger('change');
                form_reset();
            }
        });

    }


    function form_reset() {
        $("#form_add_lecturer").trigger("reset");
        $("small").empty();
    };


    $('#id_kampus').select2({
        theme: "bootstrap"
    });
</script>


@endpush
