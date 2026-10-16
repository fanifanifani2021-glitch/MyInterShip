@extends('templates.internship_data')
@section('main-content')
<div class="main-panel">

<div class="bg-primary2 pt-4 pb-5">
    <div class="container text-white py-2">
        <div class="d-flex align-items-center">
            <div class="mr-3">
                <h1 class="mb-3">Logbook Approval</h1>
                <h5 class="op-7 mb-3">Tools for checking approval made for Student Logbook</h5>
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
                                Logbook Approval
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
                            <h4 class="card-title">Logbook Detail</h4>
                            <!-- <div class="card-tools">
                                <button class="btn btn-icon btn-link btn-primary btn-xs"><span class="fa fa-angle-down"></span></button>
                                <button class="btn btn-icon btn-link btn-primary btn-xs btn-refresh-card"><span class="fa fa-sync-alt"></span></button>
                                <button class="btn btn-icon btn-link btn-primary btn-xs"><span class="fa fa-times"></span></button>
                            </div> -->
                        </div>
                        <!-- <p class="card-category">
                            Map of the distribution of users around the world</p> -->
                    </div>
                    <div class="card-body form-row">

                        <div class="form-group col-md-6">
                            <label>Student Name</label>
                            <div class="forrm-group">
                                <?= $internship_data['name'] ?>
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Student NIM</label>
                            <div class="forrm-group">
                                <?= $internship_data['nim'] ?>
                            </div>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Week Num </label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1">M</span>
                                </div>
                                <input type="number" min="0" name="week_num" class="form-control" value="<?= $logbook_data['week_num'] ?>" />
                            </div>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Start Date </label>
                            <input type="date" name="start_date" class="form-control" value="<?= $logbook_data['start_date'] ?>" />
                        </div>

                        <div class="form-group col-md-4">
                            <label>End Date </label>
                            <input type="date" name="end_date" class="form-control" value="<?= $logbook_data['end_date'] ?>" />
                        </div>


                        <div class="form-group col-md-12">
                            <label class="label-control">Description </label>
                            <div class="form-control" name="description" rows="10" placeholder="Describe all job you`ve done this week"><?= $logbook_data['description'] ?></div>

                        </div>

                        <div class="form-group col-md-12">
                            <label class="label-control">Documentation </label>
                            <div class="form-control" name="documentation" rows="10" placeholder="Provide some photos to prove your job"><?= $logbook_data['documentation'] ?></div>

                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@endsection
