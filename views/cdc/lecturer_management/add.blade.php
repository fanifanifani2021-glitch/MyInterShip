@extends('templates.cdc')

@push('head-scripts')
@endpush

@section('main-content')


<div class="container">

    <div class="page-inner">


        <div class="page-header">
            <h4 class="page-title">Add New Lecturer</h4>
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
                    <a href="javascript:void(0)" onclick="cancel_confirmation('{{ router('dashboard') }}')">Dashboard</a>
                </li>

                <li class="separator">
                    <i class="flaticon-right-arrow"></i>
                </li>
                <li class="nav-item">
                    <a href="javascript:void(0)" onclick="cancel_confirmation('{{ router('lecturer.management') }}')">Lecturer Management</a>
                </li>

                <li class="separator">
                    <i class="flaticon-right-arrow"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Add</a>
                </li>
            </ul>
        </div>


        <div class="row">

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Add Lecturer Form</h4>
                    </div>

                    <form action="{{ router('lecturer.store') }}" method="post" id="form_add_lecturer">
                        <?= csrf(); ?> <?= input_hidden('lecturer_status', 'Active'); ?>
                        <div class="card-body form-row">

                            <div class="form-group col-md-12">
                                <label>Lecturer NIDN <b class="text-danger">*</b></label>

                                <input type="text" class="form-control <?= show_error('lecturer_nik') ?>"
                                    name="lecturer_nik" placeholder="Input Lecturer NID, Example: 02240786xx"
                                    value="<?= old('lecturer_nik') ?>" />



                                <?= form_error('lecturer_nik') ?>

                            </div>

                            <div class="form-group col-md-6">
                                <label>Lecturer Name <b class="text-danger">*</b></label>


                                <input type="text" class="form-control <?= show_error('lecturer_name') ?>"
                                    name="lecturer_name" placeholder="Input Lecturer Name"
                                    value="<?= old('lecturer_name') ?>" />


                                <?= form_error('lecturer_name') ?>

                            </div>

                            <div class="form-group col-md-6">
                                <label>Lecturer E-Mail <b class="text-danger">*</b></label>


                                <input type="text" class="form-control <?= show_error('lecturer_email') ?>"
                                    name="lecturer_email" placeholder="Input Lecturer E-Mail"
                                    value="<?= old('lecturer_email') ?>" />
                                <?= form_error('lecturer_email') ?>

                            </div>

                        </div>

                        <div class="card-footer form-row">

                            <div class="form-group col-md-12">
                                <button type="button" class="btn btn-danger" onclick="cancel_confirmation('{{ router('lecturer.management') }}')"><i
                                        class="fas fa-times mr-1"></i>Cancel</button>
                                <button type="button" class="btn btn-warning" onclick="reset_confirmation()"><i
                                        class="fas fa-sync mr-1"></i>Reset</button>
                                <button type="submit" class="btn btn-success" id="submit_btn"><i
                                        class="fas fa-plus mr-1"></i>Add</button>

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

<!-- JS Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>


<!-- Soloalert -->
<script src='./core/component/soloalert.js'></script>


<!-- AJAX Script -->
    <script>
        $(document).ready(function() {

            $("#program_study").val("<?= old('program_study') ?>");
            $("#program_study").trigger('change');


            
            $("#lecturer_nik").val("<?= old('lecturer_nik') ?>");
            $("#lecturer_nik").trigger('change');

            $("#user_type").val("<?= old('user_type') ?>");
            $("#user_type").trigger('change');


            $("[aria-labelledby='select2-program_study-container']").addClass('<?= show_error('program_study') ?>');
            $("[aria-labelledby='select2-user_type-container']").addClass('<?= show_error('user_type') ?>');
            $("[aria-labelledby='select2-lecturer_status-container']").addClass('<?= show_error('lecturer_status') ?>');




        });

    </script>


    <!-- User Custom Script -->
    <script>

        // Untuk Inisiasi Konfirmasi Cancel
        function cancel_confirmation(location) {
            // alert('Are you sure you want to cancel ? (Your change will not be saved)');
            SoloAlert.confirm({
                title: "Are you sure you want to cancel ?"
                , body: "Changes you made will not be saved !"
                , useTransparency: true
                , onOk: () => {
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
                    form_reset();
                }
            });

        }

        function form_reset() {

            $("#form_add_lecturer").trigger("reset");

            $("small").empty();
        };
    </script>



    <!-- Page Sript Dependencies -->
    <script>
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


@endpush