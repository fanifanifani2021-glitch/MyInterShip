

<?php $__env->startPush('header-scripts'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    .card-title { font-weight: 600; }
    .info-label { font-weight: 600; color: #666; }
    .section-title { font-weight: 700; margin-bottom: 8px; }
</style>
<?php $__env->stopPush(); ?>



<?php $__env->startSection('main-content'); ?>
<div class="container">

    <!-- PAGE HEADER -->
    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex justify-content-between align-items-center">

                <div>
                    <h2 class="text-white fw-bold">Internship Acceptance Confirmation</h2>
                    <h5 class="text-white op-7">Please confirm the company's response to your internship letter.</h5>
                </div>

                <div>
                    <a href="<?php echo e(base_url('index.php?page=student/internship_letter/detail&id='. $letter['id_internship_letter'])); ?>" 
                       class="btn btn-light">
                        <i class="fas fa-arrow-left"></i> Go Back to Letter
                    </a>
                </div>

            </div>
        </div>
    </div>



    <!-- PAGE CONTENT -->
    <div class="page-inner mt--5">

        <!-- ========== STUDENT & LETTER SUMMARY ========== -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h4 class="card-title">Internship Letter Summary</h4>
            </div>

            <div class="card-body">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><span class="info-label">Student Name:</span> <?php echo e($student['name']); ?></p>
                        <p><span class="info-label">NIM:</span> <?php echo e($student['nim']); ?></p>
                        <p><span class="info-label">Program:</span>
                            <?php echo e($student['jenjang']); ?> <?php echo e($student['study_program']); ?>

                        </p>
                    </div>

                    <div class="col-md-6">
                        <p><span class="info-label">Letter Number:</span> <?php echo e($letter['letter_number'] ?? '-'); ?></p>
                        <p><span class="info-label">Letter Release Date:</span>
                            <?php echo e(date('F d, Y', strtotime($letter['letter_date']))); ?>

                        </p>
                        <p><span class="info-label">Internship Period:</span>
                            <?php echo e(date('F d, Y', strtotime($letter['expected_start']))); ?>

                            –
                            <?php echo e(date('F d, Y', strtotime($letter['expected_end']))); ?>

                        </p>
                    </div>
                </div>

                <hr>

                <p><span class="info-label">Company:</span> <?php echo e($company['name']); ?></p>
                <p><span class="info-label">Address:</span> <?php echo e($company['address']); ?></p>

                <?php if($company['city']): ?>
                <p><span class="info-label">City:</span> <?php echo e($company['city']); ?></p>
                <?php endif; ?>

                <?php if($company['province']): ?>
                <p><span class="info-label">Province:</span> <?php echo e($company['province']); ?></p>
                <?php endif; ?>

                <p>
                    <span class="info-label">New Company?</span>
                    <?php if($letter['is_new_company'] == 1): ?>
                        <span class="badge badge-warning">Yes — HR Data Required</span>
                    <?php else: ?>
                        <span class="badge badge-success">No</span>
                    <?php endif; ?>
                </p>

            </div>
        </div>




        <!-- ========== COMPANY RESPONSE CONFIRMATION ========== -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h4 class="card-title">Company Response Confirmation</h4>
            </div>

            <form action="<?php echo e(base_url('index.php?form=student/internship_letter/acceptance_form')); ?>"
                  method="POST" enctype="multipart/form-data">
            <?php echo e(csrf()); ?>


            <?php echo e(input_hidden('id_internship_letter', $letter['id_internship_letter'])); ?>


            <div class="card-body">

                <!-- Q1 — REPLY OR NOT -->
                <label class="d-block mb-2">Have you received a reply from the company?</label>

                <div class="custom-control custom-radio mb-2">
                    <input type="radio" name="has_reply" value="yes" id="replyYes" class="custom-control-input">
                    <label for="replyYes" class="custom-control-label">Yes, I have received a reply</label>
                </div>

                <div class="custom-control custom-radio mb-3">
                    <input type="radio" name="has_reply" value="no" id="replyNo" class="custom-control-input">
                    <label for="replyNo" class="custom-control-label">No, I have not received a reply</label>
                </div>



                <!-- Warning <10 business days -->
                <div id="replyWarningDays" class="alert alert-warning mt-3" style="display:none;">
                    You cannot continue because it has not been 10 business days since the letter was issued.
                </div>



                <!-- Step A: Upload PDF FIRST -->
                <div id="replyUploadSection" class="mt-3" style="display:none;">
                    <label>Upload Company's Reply Letter (PDF) <span class="text-danger">*</span></label>
                    <input type="file" name="response_pdf" class="form-control" accept="application/pdf">
                </div>



                <!-- Step B: Acceptance Status -->
                <div id="acceptStatusSection" class="mt-3" style="display:none;">
                    <label class="d-block mb-2">Did the company accept your internship application?</label>

                    <div class="custom-control custom-radio mb-2">
                        <input type="radio" name="is_accepted" value="yes" id="acceptedYes" class="custom-control-input">
                        <label for="acceptedYes" class="custom-control-label">Yes, I am accepted</label>
                    </div>

                    <div class="custom-control custom-radio mb-3">
                        <input type="radio" name="is_accepted" value="no" id="acceptedNo" class="custom-control-input">
                        <label for="acceptedNo" class="custom-control-label">No, I was not accepted</label>
                    </div>

                    <!-- Encouragement -->
                    <div id="rejectEncourage" class="alert alert-info" style="display:none;">
                        It's okay! Please try applying to another company. You can still submit this form.
                    </div>
                </div>




                <!-- Step C: Internship Date Confirmation -->
                <div id="dateConfirmSection" class="mt-4" style="display:none;">

                    <h5 class="section-title">Internship Period Confirmation</h5>

                    <label>Did the company adjust the internship dates?</label>

                    <div class="custom-control custom-radio mb-2">
                        <input type="radio" name="changed_date" value="no" id="noChange" class="custom-control-input">
                        <label for="noChange" class="custom-control-label">No, dates remain the same</label>
                    </div>

                    <div class="custom-control custom-radio mb-3">
                        <input type="radio" name="changed_date" value="yes" id="yesChange" class="custom-control-input">
                        <label for="yesChange" class="custom-control-label">Yes, the dates were adjusted</label>
                    </div>


                    <!-- NEW DATES -->
                    <div id="dateChangeSection" class="row mt-2" style="display:none;">
                        <div class="col-md-6 mb-3">
                            <label>New Start Date</label>
                            <input type="date" name="new_start_date" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>New End Date</label>
                            <input type="date" name="new_end_date" class="form-control">
                        </div>
                    </div>
                </div>




                <!-- Step D: HR Info (For NEW COMPANY ONLY) -->
                <?php if($letter['is_new_company'] == 1): ?>
                <div id="hrSection" class="mt-4" style="display:none;">

                    <h5 class="section-title">HR Representative Information</h5>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>HR Name <span class="text-danger">*</span></label>
                            <input type="text" name="hr_name" class="form-control" placeholder="e.g., Budi Santoso">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>HR Email <span class="text-danger">*</span></label>
                            <input type="email" name="hr_email" class="form-control" placeholder="e.g., hr@company.com">
                        </div>
                    </div>

                </div>
                <?php endif; ?>




                <!-- Step E: Internship Position -->
                <div id="positionSection" class="mt-4" style="display:none;">
                    <h5 class="section-title">Internship Position</h5>

                    <label>Position offered by the company</label>
                    <input type="text" name="internship_position" class="form-control"
                           placeholder="e.g., Web Developer, IT Support, Data Analyst">
                </div>



                <!-- SUBMIT -->
                <div id="submitArea" class="text-right mt-4" style="display:none;">
                    <button class="btn btn-primary px-4 py-2">Submit Confirmation</button>
                </div>

            </div>

            </form>
        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>




<?php $__env->startPush('footer-scripts'); ?>
<script>
document.addEventListener("DOMContentLoaded", function(){

    const replyYes = document.getElementById("replyYes");
    const replyNo = document.getElementById("replyNo");

    const acceptedYes = document.getElementById("acceptedYes");
    const acceptedNo = document.getElementById("acceptedNo");

    const uploadSection = document.getElementById("replyUploadSection");
    const acceptSection = document.getElementById("acceptStatusSection");

    const hrSection = document.getElementById("hrSection");
    const dateConfirm = document.getElementById("dateConfirmSection");
    const dateChangeSection = document.getElementById("dateChangeSection");
    const positionSection = document.getElementById("positionSection");
    const rejectEncourage = document.getElementById("rejectEncourage");

    const yesChange = document.getElementById("yesChange");
    const noChange = document.getElementById("noChange");

    const warningDays = document.getElementById("replyWarningDays");
    const submitArea = document.getElementById("submitArea");

    const canSubmit = <?php echo e($canSubmitWithoutReply ? 'true' : 'false'); ?>;


    /** UPDATE VIEW */
    function updateView() {

        uploadSection.style.display = "none";
        acceptSection.style.display = "none";
        hrSection && (hrSection.style.display = "none");
        dateConfirm.style.display = "none";
        dateChangeSection.style.display = "none";
        positionSection.style.display = "none";
        rejectEncourage.style.display = "none";
        warningDays.style.display = "none";
        submitArea.style.display = "none";



        /** === IF NO REPLY === */
        if (replyNo.checked) {

            if (!canSubmit) {
                warningDays.style.display = "block";
            } else {
                submitArea.style.display = "block";
            }

            return;
        }



        /** === IF YES REPLY === */
        if (replyYes.checked) {

            uploadSection.style.display = "block"; // Step A
            acceptSection.style.display = "block"; // Step B


            // STEP B: REJECTED
            if (acceptedNo.checked) {
                rejectEncourage.style.display = "block";
                submitArea.style.display = "block";
                return;
            }



            // STEP C: ACCEPTED
            if (acceptedYes.checked) {

                dateConfirm.style.display = "block";
                positionSection.style.display = "block";

                if (yesChange.checked) {
                    dateChangeSection.style.display = "block";
                }

                hrSection && (hrSection.style.display = "block");

                submitArea.style.display = "block";
            }
        }
    }


    replyYes.addEventListener("change", updateView);
    replyNo.addEventListener("change", updateView);
    acceptedYes.addEventListener("change", updateView);
    acceptedNo.addEventListener("change", updateView);

    yesChange.addEventListener("change", function(){
        dateChangeSection.style.display = "block";
    });

    noChange.addEventListener("change", function(){
        dateChangeSection.style.display = "none";
    });

    // =============================
    // RUN ON PAGE LOAD ✔️
    // =============================
    updateView();

});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('templates.student', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/student/my_internship_letter/acceptance_form.blade.php ENDPATH**/ ?>