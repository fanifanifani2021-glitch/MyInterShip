@extends('templates.industry')

@push('head-scripts')
<!-- CSS Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
<style>
    #check-all.indeterminate~.custom-control-label::before {
        content: "-";
    }

</style>
@endpush

@section('main-content')
@php
// Decode ID dari parameter GET
$id_internship = base64_decode($_GET['id_internship']);

// Cek apakah decoding berhasil
if (!$id_internship) {
make_soloalert('error', 'Invalid ID', 'The internship ID is not valid!');
redirect_back(1);
exit;
}

// Siapkan query untuk memeriksa internship berdasarkan id_internship
$sql = "SELECT * FROM internship WHERE internship.id_internship = :id_internship LIMIT 1";

// Mengambil PDO dari server
$pdo = $_SERVER['PDO'];

try {
// Siapkan dan eksekusi statement dengan parameter
$stmt = $pdo->prepare($sql);
$stmt->execute(['id_internship' => $id_internship]);

// Cek apakah data internship ditemukan
if ($stmt->rowCount() != 1) {
make_soloalert('error', 'Failed to Validate', 'Internship data is not found!');
redirect_back(1);
exit;
}

// Ambil data internship
$internship_data = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
// Tangani jika terjadi error pada query
make_soloalert('error', 'Invalid ID', 'The internship ID is not valid!');
redirect_back(1);
exit;
}
@endphp

<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title">Validate Candidate Data</h4>
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
                    <a href="index.php?page=intern_candidate">Intern Candidate</a>
                </li>

                <li class="separator">
                    <i class="flaticon-right-arrow"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Validate Candidate Data</a>
                </li>



            </ul>
        </div>


        <?php
        $sql = "SELECT internship.*, applicant.*, student_internship.*, program_study.* FROM internship
        JOIN student_internship ON student_internship.nim = internship.nim
        LEFT JOIN applicant ON applicant.id_internship = internship.id_internship
        LEFT JOIN program_study ON student_internship.program_study = program_study.kode_prodi
        WHERE internship.id_internship = :id_internship LIMIT 1;";
        $stmt = $_SERVER['PDO']->prepare($sql);
        $stmt->execute(['id_internship' => $id_internship]);

        $internship_data = $stmt->fetchAll( PDO::FETCH_ASSOC );


        foreach ($internship_data as $i => $candidate_data) :

            if ( $candidate_data['id_applicant'] !== NULL ) {
                $registered_via = 'Offer';

                $sql = "SELECT * FROM offer WHERE id_offer = :id_offer LIMIT 1";

                $stmt_offer = $_SERVER['PDO']->prepare($sql);
                $stmt_offer->execute( ['id_offer' => $candidate_data['id_offer'] ] );
                $data_offer = $stmt_offer->fetch( PDO::FETCH_ASSOC );
            } else {
                $registered_via = 'Cooperation';
            }

        ?>

        <form action="index.php?form=validate_intern" method="POST">

            <?= csrf() ?> <?= input_hidden('registered_via', $registered_via) ?>

            <?= input_hidden('id_internship', $id_internship) ?> <?= input_hidden('student_nim', $candidate_data['nim']) ?>
            <div class="row">



                <!-- Internship Under Supervisor -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3>Candidate Identity</h3>
                        </div>
                        <div class="card-body form-row">
                            <div class="form-group col-md-12 text-center">

                                <div class="profile-picture">
                                    <div class="avatar avatar-xxl">
                                        <img src="index.php?viewer=get_profile&location=<?= base64_encode($candidate_data['profile_picture']) ?>" alt="..." class="avatar-img rounded-circle">
                                    </div>
                                </div>

                                <a type="button" role="button" download target="_blank" href="./upload/profile_picture/<?= $candidate_data['profile_picture'] ?>" class="mt-1 btn btn-success btn-sm"><i class="fas fa-download mr-1"></i>Save</a>

                            </div>
                            <div class="form-group col-md-6">
                                <label class="label-control">Student ID (NIM)</label>
                                <input class="form-control" value="<?= $candidate_data['nim'] ?>" placeholder="Student's NIM" />
                            </div>

                            <div class="form-group col-md-6">
                                <label class="label-control">Study Program</label>
                                <input class="form-control" value="{{ $candidate_data['jenjang']. " " .$candidate_data['study_program'] }}" placeholder="Student's Program Study" />
                            </div>

                            <div class="form-group col-md-12">
                                <label class="label-control">Student Name</label>
                                <input class="form-control" value="<?= $candidate_data['name'] ?>" placeholder="Student's Name" />
                            </div>

                            <div class="form-group col-md-6">
                                <label class="label-control">E-Mail</label>

                                <div class="input-group">
                                    <input class="form-control" value="<?= $candidate_data['email'] ?>" placeholder="Student's E-Mail" />

                                    <div class="input-group-append">
                                        <a class="btn btn-outline-secondary" href="mailto:<?= $candidate_data['email'] ?>" target="_blank"><i class="fas fa-envelope mr-1"></i>E-Mail</a>

                                    </div>
                                </div>
                            </div>

                            <?php if (!in_array($candidate_data['other_email'], [NULL, '-', ''])) : ?>
                            <div class="form-group col-md-6">
                                <label class="label-control">Other E-Mail</label>

                                <div class="input-group">
                                    <input class="form-control" value="<?= $candidate_data['other_email'] ?>" placeholder="Student's E-Mail" />

                                    <div class="input-group-append">
                                        <a class="btn btn-outline-secondary" href="mailto:<?= $candidate_data['other_email'] ?>" target="_blank"><i class="fas fa-envelope mr-1"></i>E-Mail</a>
                                    </div>
                                </div>
                            </div>

                            <?php endif; ?>

                            <div class="form-group col-md-6">
                                <label class="label-control">Phone</label>

                                <div class="input-group">
                                    <input class="form-control" value="<?= $candidate_data['phone'] ?>" placeholder="Student's Phone Number" />

                                    <div class="input-group-append">
                                        <a class="btn btn-outline-secondary" href="tel:<?= $candidate_data['phone'] ?>" target="_blank"><i class="fas fa-phone mr-1"></i>Call</a>

                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="label-control">Whatsapp Number</label>


                                <div class="input-group">
                                    <input class="form-control" value="<?= $candidate_data['no_whatsapp'] ?>" placeholder="Student's Whatsapp Number" />

                                    <div class="input-group-append">
                                        <a class="btn btn-outline-secondary" href="https://wa.me/<?= $candidate_data['no_whatsapp'] ?>" target="_blank"><i class="fab fa-whatsapp mr-1"></i>Send Message</a>
                                    </div>
                                </div>

                            </div>


                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3>Candidate Registration</h3>
                        </div>

                        <div class="card-body form-row">

                            <div class="form-group col-md-12">
                                <label class="label-control">Registered Via</label>

                                <input type="text" class="form-control" value="<?= $registered_via ?>" readonly placeholder="Candidate Registered" />

                            </div>


                            <?php if (stripos($registered_via, 'Offer') !== false) : ?>
                            <?= input_hidden('id_offer', $data_offer['id_offer']) ?>


                            <div class="form-group col-md-12">
                                <label>Position</label>
                                <input type="text" class="form-control" value="<?= $data_offer['position']; ?>" placeholder="Offer Position" />
                            </div>

                            <div class="form-group col-md-12">
                                <label>Description</label>

                                <div class="card card-body">
                                    <?= $data_offer['description']; ?>
                                </div>
                                <!-- <textarea type="text" class="form-control" rows="4" placeholder="Offer Description"><?= $data_offer['description']; ?></textarea> -->
                            </div>


                            <div class="form-group col-md-12">
                                <label>Requirement</label>

                                <div class="card card-body">
                                    <?= $data_offer['requirement']; ?>
                                </div>
                                <!-- <input type="text" class="form-control" value="<?= $data_offer['requirement']; ?>" placeholder="Offer Requirement" /> -->
                            </div>

                            <div class="form-group col-md-6">
                                <label>Job Type</label>
                                <input type="text" class="form-control" value="<?= $data_offer['job_type']; ?>" placeholder="Offer Job Type" />
                            </div>

                            <div class="form-group col-md-6">
                                <label>Offer Type</label>
                                <input type="text" class="form-control" value="<?= $data_offer['offer_type']; ?>" placeholder="Offer Type" />
                            </div>

                            <div class="form-group col-md-6">
                                <label>Employer Type</label>
                                <input type="text" class="form-control" value="<?= $data_offer['employer_type']; ?>" placeholder="Offer Employer Type" />
                            </div>


                            <div class="form-group col-md-6">
                                <label>City</label>
                                <input type="text" class="form-control" value="<?= $data_offer['city']; ?>" placeholder="Offer Working City" />
                            </div>

                            <?php if (!in_array($data_offer['offer_type'], ['Part Time'])) :
                                        if ($data_offer['offer_type'] == 'Internship') : $label_salary = "Allowence";
                                        else : $label_salary = 'Salary Range';
                                        endif;
                                    ?>

                            <div class="form-group col-md-12">
                                <label><?= $label_salary ?></label>
                                <input type="text" class="form-control" value="<?= $data_offer['salary_range']; ?>" placeholder="Offer Slaary Range" />
                            </div>

                            <?php endif; ?>



                            <div class="form-group col-md-12 separator-solid"></div>

                            <?php endif; ?>

                            <div class="form-group col-md-6">
                                <label class="label-control">Excpected Start Date</label>

                                <input class="form-control" name="expected_start_date" value="<?= date('D, d-M-Y', strtotime($candidate_data['start_date'])) ?>" readonly />
                                <input hidden name="expected_start_date" value="<?= $candidate_data['start_date'] ?>" />

                            </div>

                            <div class="form-group col-md-6">
                                <label class="label-control">Excpected End Date</label>

                                <input class="form-control" value="<?= date('D, d-M-Y', strtotime($candidate_data['end_date'])) ?>" readonly />
                                <input hidden name="expected_end_date" value="<?= $candidate_data['end_date'] ?>" />

                            </div>


                            <div class="form-group col-md-6">
                                <label class="label-control">CV</label>

                                <div class="input-group mb-3">
                                    <input class="form-control" value="<?= $candidate_data['file_1'] ?>" readonly />

                                    <div class="input-group-append">
                                        <a target="_blank" href="index.php?viewer=url&location=<?= base64_encode($candidate_data['file_1']) ?>" class="btn btn-outline-secondary"><i class="fas fa-globe mr-2"></i>Visit</a>

                                        <!-- <a class="btn btn-outline-secondary" type="button" href="./upload/lampiran_internship/<?= $candidate_data['file_1'] ?>" target="_blank"  ><i class="fas fa-eye"></i></a> -->
                                    </div>
                                </div>

                            </div>


                            <?php if (!in_array($candidate_data['file_2'], [NULL, '-', ''])) :  ?>
                            <div class="form-group col-md-6">
                                <label class="label-control">Transcript of Grade</label>
                                <div class="input-group mb-3">
                                    <input class="form-control" value="<?= $candidate_data['file_2'] ?>" readonly />

                                    <div class="input-group-append">
                                        <a class="btn btn-outline-secondary" href="index.php?viewer=internship_attachment&location=<?= base64_encode('./upload/lampiran_internship/' . $candidate_data['file_2']) ?>" target="_blank"><i class="fas fa-eye mr-1"></i>Show</a>

                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>



                            <?php if (!in_array($candidate_data['file_3'], [NULL, '-', ''])) :  ?>
                            <div class="form-group col-md-12">
                                <label class="label-control">Additional Attachment</label>
                                <div class="input-group mb-3">
                                    <input class="form-control" value="<?= $candidate_data['file_3'] ?>" readonly />

                                    <div class="input-group-append">

                                        <a class="btn btn-outline-secondary" href="index.php?viewer=internship_attachment&location=<?= base64_encode('./upload/lampiran_internship/' .$candidate_data['file_3']) ?>" target="_blank"><i class="fas fa-eye mr-1"></i>Show</a>

                                    </div>
                                </div>
                            </div>

                            <?php endif; ?>


                            <div class="form-group col-md-12 separator-solid"></div>


                            <div class="form-group col-md-12">
                                <label class="label-control">Status Acceptance <b class="text-danger">*</b></label>
                                <select class="form-control <?= show_error('status_acceptance') ?>" name="status_acceptance" id="status_acceptance" onchange="show_supervisor()" aria-label="Status Acceptance">
                                    <option value="" hidden>Choose one . . .</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>

                                <?= form_error('status_acceptance') ?>
                            </div>


                            <div class="form-group col-md-12 acceptance-yes">
                                <label class="label-control">Industry Supervisor <b class="text-danger">*</b></label>
                                <select class="form-control <?= show_error('industry_supervisor') ?>" name="industry_supervisor" id="industry_supervisor" aria-label="Industry Supervisor">
                                    <option value="" hidden>Choose one . . .</option>
                                    <?php
                                        $id_company = $_SESSION['id_company'];
                                        $exec = $_SERVER['KONEKSI_DB']->query("SELECT * FROM user_company WHERE id_company = '$id_company' AND user_type NOT IN ('HRD')");

                                        if ( $exec->num_rows < 1 ) :
                                            echo "<option value=''>No Supervisor found !</option>";
                                        endif;
                                        foreach ($exec as $row) :
                                        ?>

                                    <option value="<?= $row['id_user_company'] ?>"><?= $row['user_fullname'] ?></option>

                                    <?php endforeach; ?>
                                </select>

                                <?= form_error('industry_supervisor') ?>

                                <?php 
                                    if ( $exec->num_rows < 1 ) :
                                        echo "<small class='text-danger'>Add Supervisor on <b><a href='index.php?page=industry_supervisor_management'>Supervisor Management</a></b></small>";
                                    endif;
                                    ?>

                            </div>




                            <div class="form-group col-md-6 acceptance-yes">
                                <label class="label-control">Start Date <b class="text-danger">*</b></label>

                                <input type="date" class="form-control <?= show_error('start_date') ?>" name="start_date" value="<?php if (old('start_date') !== NULL) {
                                                                                                                                            echo old('start_date');
                                                                                                                                        } else {
                                                                                                                                            echo date('Y-m-d', strtotime($candidate_data['start_date']));
                                                                                                                                        } ?>" />
                                <?= form_error('start_date') ?>

                            </div>

                            <div class="form-group col-md-6 acceptance-yes">
                                <label class="label-control">End Date <b class="text-danger">*</b></label>

                                <input type="date" class="form-control <?= show_error('end_date') ?>" name="end_date" value="<?php if (old('end_date') !== NULL) {
                                                                                                                                        echo old('end_date');
                                                                                                                                    } else {
                                                                                                                                        echo date('Y-m-d', strtotime($candidate_data['end_date']));
                                                                                                                                    } ?>" />

                                <?= form_error('end_date') ?>
                            </div>


                        </div>

                        <div class="card-footer">
                            <button type="button" class="btn btn-danger" onclick="confirmation()"><i class="fas fa-times mr-1"></i>Cancel</button>
                            <button type="button" class="btn btn-warning" onclick="reset_confirmation()"><i class="fas fa-sync mr-1"></i>Reset</button>
                            <button type="submit" class="btn btn-success" id="submit_btn"><i class="fas fa-save mr-1"></i>Save</button>

                        </div>
                    </div>
                </div>

            </div>
        </form>
        <?php endforeach; ?>

    </div>
</div>


@endsection
@push('footer-scripts')
<!-- Soloalert -->
<script src='./core/component/soloalert.js'></script>

<!-- JS Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>



<!-- User Custom Script -->
<script>
    $(document).ready(function() {
        show_supervisor();

        $('#industry_supervisor').select2({
            theme: 'bootstrap4'
        });
    });

    function confirmation() {

        SoloAlert.confirm({
            title: "Are you sure you want to cancel ?"
            , body: "Changes you made will not be saved !"
            , useTransparency: true
            , onOk: () => {
                window.location.href = "index.php?page=intern_candidate";
            }
        });

    }

    function reset_confirmation() {

        SoloAlert.confirm({
            title: "Are you sure you want to reset ?"
            , body: "Changes you made will be reset to default !"
            , useTransparency: true
            , onOk: () => {
                form_reset();
            }
        });

    }

    function form_reset() {

        $("#form_add_lecturer").trigger("reset");

        $("small").empty();
    };


    function show_supervisor() {
        var acceptance_yes = document.getElementsByClassName('acceptance-yes');
        var status_acceptance = document.getElementById('status_acceptance');

        // Loop through all elements with class 'acceptance-yes'
        for (var i = 0; i < acceptance_yes.length; i++) {
            if (status_acceptance.value === 'Yes') {
                acceptance_yes[i].hidden = false;
            } else {
                acceptance_yes[i].hidden = true;
            }
        }
    }

</script>

@endpush
