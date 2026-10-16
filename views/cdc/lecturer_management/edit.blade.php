@extends('templates.cdc')

@section('main-content')
<div class="container">
    <div class="page-inner">


        <div class="page-header">
            <h4 class="page-title">Edit Lecturer</h4>
            <ul class="breadcrumbs">
                <li class="nav-home">
                    <a href="javascript:void(0)" onclick="cancel_confirmation('{{ router('home') }}')">
                        <i class="flaticon-home"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="flaticon-right-arrow"></i>
                </li>
                <li class="nav-item">
                    <a href="javascript:void(0)"
                        onclick="cancel_confirmation('{{ router('dashboard') }}')">Dashboard</a>
                </li>

                <li class="separator">
                    <i class="flaticon-right-arrow"></i>
                </li>
                <li class="nav-item">
                    <a href="javascript:void(0)"
                        onclick="cancel_confirmation('{{ router('lecturer.management') }}')">Lecturer Management</a>
                </li>

                <li class="separator">
                    <i class="flaticon-right-arrow"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Edit</a>
                </li>
            </ul>
        </div>


        <div class="row">

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit Lecturer Form</h4>
                    </div>

                    <form action="{{ router('lecturer.update', ['id' => $id_encoded]) }}" method="post"
                        id="form_add_lecturer">
                        @csrf @method('PUT')
                        <?= csrf(); ?> <?= input_hidden('old_lecturer_nik', $data['nim_nik_unit']) ?>
                        <div class="card-body form-row">

                            <div class="form-group col-md-12">
                                <label>Lecturer NIDN <b class="text-danger">*</b></label>

                                <input type="text" class="form-control <?= show_error('lecturer_nik') ?>"
                                    name="lecturer_nik" placeholder="Input Lecturer NID, Example: 02240786xx"
                                    value="<?= old('lecturer_nik', $data['nim_nik_unit']) ?>" />


                                <?= form_error('lecturer_nik') ?>

                            </div>

                            <div class="form-group col-md-6">
                                <label>Lecturer Name <b class="text-danger">*</b></label>


                                <input type="text" class="form-control <?= show_error('lecturer_name') ?>"
                                    name="lecturer_name" placeholder="Input Lecturer Name, Example : Fulan Ramadhan"
                                    value="<?= old('lecturer_name', $data['name']) ?>" />


                                <?= form_error('lecturer_name') ?>

                            </div>

                            <div class="form-group col-md-6">
                                <label>Lecturer E-Mail <b class="text-danger">*</b></label>


                                <input type="text" class="form-control <?= show_error('lecturer_email') ?>"
                                    name="lecturer_email" placeholder="Input Lecturer E-Mail"
                                    value="<?= old('lecturer_email', $data['email_polibatam']) ?>" />
                                <?= form_error('lecturer_email') ?>

                            </div>

                            <div class="form-group col-md-12">
                                <label>Lecturer Status <b class="text-danger">*</b></label>

                                <select class="form-control <?= show_error('lecturer_status') ?>" name="lecturer_status"
                                    id="lecturer_status">
                                    <option
                                        <?= ( old('lecturer_status', $data['status'] ) == 'Active' ) ? 'selected' : ''; ?>>
                                        Active</option>
                                    <option
                                        <?= ( old('lecturer_status', $data['status'] ) == 'Not Active' ) ? 'selected' : ''; ?>>
                                        Not Active</option>
                                </select>


                                <?= form_error('lecturer_status') ?>

                            </div>

                        </div>

                        <div class="card-footer form-row">

                            <div class="form-group col-md-12">
                                <button type="button" class="btn btn-danger"
                                    onclick="cancel_confirmation('{{ router('lecturer.management') }}')"><i
                                        class="fas fa-times mr-1"></i>Cancel</button>
                                <button type="button" class="btn btn-warning" onclick="reset_confirmation()"><i
                                        class="fas fa-sync mr-1"></i>Reset</button>
                                <button type="submit" class="btn btn-success" id="submit_btn"><i
                                        class="fas fa-edit mr-1"></i>Edit</button>

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

<!-- User Custom Script -->
<script>
    // Untuk Inisiasi Konfirmasi Cancel
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

    $('#lecturer_nik').select2({
        theme: "bootstrap"
    });

    $('#user_type').select2({
        theme: "bootstrap"
    });

    $('#program_study').select2({
        theme: "bootstrap"
    });
</script>

<!-- AJAX Script -->
<script>
    $(document).ready(function () {

        $("#program_study").val("<?= old('program_study') ?>");
        $("#program_study").trigger('change');



        $("#lecturer_nik").val("<?= old('lecturer_nik') ?>");
        $("#lecturer_nik").trigger('change');

        $("#user_type").val("<?= old('user_type') ?>");
        $("#user_type").trigger('change');


        $("[aria-labelledby='select2-program_study-container']").addClass('<?= show_error('
            program_study ') ?>');
        $("[aria-labelledby='select2-user_type-container']").addClass('<?= show_error('
            user_type ') ?>');
        $("[aria-labelledby='select2-lecturer_status-container']").addClass('<?= show_error('
            lecturer_status ') ?>');




    });
</script>
@endpush