@extends('templates.internship_data')
@section('main-content')
<div class="main-panel">

<div class="bg-primary2 pt-4 pb-5">
    <div class="container text-white py-2">
        <div class="d-flex align-items-center">
            <div class="mr-3">
                <h1 class="mb-3">Attendance Approval</h1>
                <h5 class="op-7 mb-3">Tools for checking approval made for Student Attendance</h5>
            </div>
        </div>
    </div>
</div>

<div class="container mt--5">
    <div class="page-inner mt--3">

        <!-- Approval Detail -->

        <div class="row row-card-no-pd">
            <div class="col-md-12">

                <div class="card">
                    <div class="card-header">
                        <div class="card-head-row">
                            <h4 class="card-title">Approval Detail</h4>
                            <!-- <div class="card-tools">
                                <button class="btn btn-icon btn-link btn-primary btn-xs"><span class="fa fa-angle-down"></span></button>
                                <button class="btn btn-icon btn-link btn-primary btn-xs btn-refresh-card"><span class="fa fa-sync-alt"></span></button>
                                <button class="btn btn-icon btn-link btn-primary btn-xs"><span class="fa fa-times"></span></button>
                            </div> -->
                        </div>
                        <!-- <p class="card-category">
                            Map of the distribution of users around the world</p> -->
                    </div>
                    <div class="card-body">

                        <div class="form-group">
                            <label>Approval Subject</label>
                            <div class="forrm-group">
                                Attendance Approval
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Approval Date</label>
                            <div class="forrm-group">
                                <?= $approver['approval_date'] ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Approver</label>
                            <div class="forrm-group">
                                <?= $approver['approved_by']['company_name'] . " | " . $approver['approved_by']['supervisor_name'] ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Student Name</label>
                            <div class="forrm-group">
                                <?= $internship_data['name'] ?>
                            </div>
                        </div>




                    </div>
                </div>
            </div>
        </div>


        <!-- Attendance Detail -->

        <div class="row row-card-no-pd">

            <div class="col-md-12">

                <div class="card">
                    <div class="card-header">
                        <div class="card-head-row">
                            <h4 class="card-title">Attendance Detail</h4>
                            <!-- <div class="card-tools">
                                <button class="btn btn-icon btn-link btn-primary btn-xs"><span class="fa fa-angle-down"></span></button>
                                <button class="btn btn-icon btn-link btn-primary btn-xs btn-refresh-card"><span class="fa fa-sync-alt"></span></button>
                                <button class="btn btn-icon btn-link btn-primary btn-xs"><span class="fa fa-times"></span></button>
                            </div> -->
                        </div>
                        <!-- <p class="card-category">
                            Map of the distribution of users around the world</p> -->
                    </div>
                    <div class="card-body">

                        <div class="form-group">
                            <label>Student Name</label>
                            <div class="forrm-group">
                                <?= $internship_data['name'] ?>
                            </div>
                        </div>



                        <div class="form-group">
                            <label>Attendance Date</label>
                            <div class="forrm-group">
                                <?= $attendance_data['date'] ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Attendance Status</label>
                            <div class="forrm-group">
                                <?= $attendance_data['attendance_type'] ?>
                            </div>
                        </div>

                        <?php if ($attendance_data['attendance_type'] == 'Present') : ?>

                        <div class="form-group">
                            <label>Check In</label>
                            <div class="forrm-group">
                                <?= $attendance_data['check_in'] ?>
                            </div>
                        </div>


                        <div class="form-group">
                            <label>Check Out</label>
                            <div class="forrm-group">
                                <?= $attendance_data['check_out'] ?>
                            </div>
                        </div>
                        <?php endif; ?>



                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@endsection
