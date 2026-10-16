

<?php $__env->startPush('header-scripts'); ?>
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.3.2/dist/select2-bootstrap4.min.css"
    rel="stylesheet" />


<style>
    /* === Base Select2 Bootstrap-like styling === */
    .select2-container .select2-selection--single {
        height: calc(2.25rem + 2px);
        padding: 0.375rem 0.75rem;
        font-size: 0.95rem;
        line-height: 1.5;
        color: #495057;
        background-color: #fff;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
    }

    /* Hover & Focus */
    .select2-container--default .select2-selection--single:focus,
    .select2-container--default .select2-selection--single:hover {
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.15rem rgba(0, 123, 255, 0.25);
    }

    /* Text alignment inside select */
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #212529;
        line-height: 2.1rem;
        padding-left: 2px;
    }

    /* Remove small arrow overlap */
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 100%;
        right: 10px;
    }

    /* Dropdown style */
    .select2-dropdown {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    /* Highlighted (hovered) option */
    .select2-results__option--highlighted {
        background-color: #007bff !important;
        color: white !important;
    }

    /* Selected item */
    .select2-results__option[aria-selected=true] {
        background-color: #e9ecef;
        color: #212529;
    }

    /* Placeholder text */
    .select2-selection__placeholder {
        color: #6c757d;
        font-style: italic;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="container">
    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
                <div class="d-flex align-items-left flex-column flex-md-row justify-content-between">

                <div>
                    <h2 class="text-white pb-2 fw-bold">Internship Letter Request</h2>
                    <h5 class="text-white op-7 mb-2">
                        Fill in the form below to request an official internship letter from your campus.
                    </h5>
                </div>

                <div class="mt-3 mt-md-0">
                    <a href="<?php echo e(base_url('index.php?page=my_internship_letter')); ?>" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-2"></i> Back to List
                    </a>
                </div>

            </div>
        </div>
    </div>


    <div class="page-inner mt--5">
        <div class="row mt--2">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title">Request New Internship Letter</div>
                    </div>

                    <div class="card-body">
                        <form id="submissionForm"
                            action="<?php echo e(base_url('index.php?form=submit_internship_letter_request')); ?>" method="POST">
                            <?php echo e(csrf()); ?>

                            <?php echo e(input_hidden('nim', $_SESSION['nim'])); ?>


                            <!-- ================= STUDENT INFORMATION ================= -->
                            <h5 class="font-weight-bold mb-3">Student Information</h5>
                            <hr class="mt-0 mb-4">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted mb-1">NIM</label>
                                    <div class="border rounded p-2 bg-light">
                                        <?php echo e($student_internship['nim'] ?? '-'); ?>

                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted mb-1">Full Name</label>
                                    <div class="border rounded p-2 bg-light">
                                        <?php echo e($student_internship['name'] ?? '-'); ?>

                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted mb-1">Study Program</label>
                                    <div class="border rounded p-2 bg-light">
                                        <?php echo e(($student_internship['jenjang'] ?? '') . ' ' . ($student_internship['prodi'] ?? '') . ' (' . ($student_internship['study_program'] ?? '') . ')'); ?>

                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted mb-1">Department</label>
                                    <div class="border rounded p-2 bg-light">
                                        <?php echo e($student_internship['jurusan'] ?? '-'); ?>

                                    </div>
                                </div>
                            </div>

                            <!-- ================= CONTACT INFORMATION ================= -->
                            <h5 class="font-weight-bold mt-4 mb-3">Contact Information</h5>
                            <hr class="mt-0 mb-4">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted mb-1">Email</label>
                                    <div class="border rounded p-2 bg-light">
                                        <?php echo e($student_internship['email'] ?? '-'); ?>

                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted mb-1">Active WhatsApp Number</label>
                                    <div class="border rounded p-2 bg-light">
                                        <?php echo e($student_internship['phone'] ?? '-'); ?>

                                    </div>
                                </div>
                            </div>

                            <!-- ================= CLASS & SEMESTER ================= -->
                            <h5 class="font-weight-bold mt-4 mb-3">Class & Semester</h5>
                            <hr class="mt-0 mb-4">

                            <div class="row align-items-center">
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted mb-2 d-block">Class <span
                                            class="text-danger">*</span></label>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" name="class" value="REGULAR CLASS"
                                            class="custom-control-input " id="classRegular" required
                                            <?php echo e(old('class', $student_internship['class'] ) === 'REGULAR CLASS' ? 'checked' : ''); ?>>
                                        <label class="custom-control-label" for="classRegular">Regular Class</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" name="class" value="EVENING CLASS"
                                            class="custom-control-input" id="classEvening"
                                            <?php echo e(old('class', $student_internship['class'] ) === 'EVENING CLASS' ? 'checked' : ''); ?>>
                                        <label class="custom-control-label" for="classEvening">Evening Class</label>
                                    </div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label>Semester <span class="text-danger">*</span></label>
                                    <select id="semesterField" name="semester" class="form-control text-center"
                                        required>
                                        <?php for($i = 3; $i <= 14; $i++): ?> <option value="<?php echo e($i); ?>"
                                            <?php echo e(old('semester', $student_internship['semester'] ) == $i ? 'selected' : ''); ?>>
                                            <?php echo e($i); ?>

                                            </option>
                                            <?php endfor; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- ================= COMPANY INFORMATION ================= -->
                            <h5 class="font-weight-bold mt-4 mb-3">Company Information</h5>
                            <hr class="mt-0 mb-4">


                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label>Company Name <span class="text-danger">*</span></label>
                                    <select id="companySelect" name="company_id" class="select2 form-control" required>
                                        <option value="">-- Select Company --</option>
                                        <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($company['id_company']); ?>"
                                            <?php echo e(old('company_id') == $company['id_company'] ? 'selected' : ''); ?>>
                                            <?php echo e($company['name']); ?>

                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <div class="custom-control custom-checkbox">
                                    <input type="text" name="company_not_listed" value="0" hidden />
                                        <input type="checkbox" class="custom-control-input" id="companyExist" value="1" name="company_not_listed" <?php echo e(old('company_not_listed') ? 'checked' : ''); ?> />
                                        <label class="custom-control-label" for="companyExist">Company not
                                            listed?</label>
                                    </div>
                                </div>

                                <div id="newCompanyFields" style="display:none;" class="col-md-12 mb-3">
                                    <div class="row">

                                        <div class="col-md-12 mb-3">
                                            <label for="newCompanyName">New Company Name <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                class="form-control" 
                                                id="newCompanyName" 
                                                name="newCompanyName"
                                                value="<?php echo e(old('newCompanyName')); ?>"
                                                placeholder="e.g., PT Maju Sejahtera Indonesia">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="newCompanyEmail">Company Email <span class="text-danger">*</span></label>
                                            <input type="email" 
                                                class="form-control" 
                                                id="newCompanyEmail" 
                                                name="newCompanyEmail"
                                                value="<?php echo e(old('newCompanyEmail')); ?>"
                                                placeholder="e.g., info@company.com">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="newCompanyPhone">Company Phone Number <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                class="form-control" 
                                                id="newCompanyPhone" 
                                                name="newCompanyPhone"
                                                value="<?php echo e(old('newCompanyPhone')); ?>"
                                                placeholder="e.g., +62 812-3456-7890">
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <hr />
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="newCompanyCity">City <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                class="form-control" 
                                                id="newCompanyCity" 
                                                name="newCompanyCity"
                                                value="<?php echo e(old('newCompanyCity')); ?>"
                                                placeholder="e.g., Batam">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="newCompanyProvince">Province <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                class="form-control" 
                                                id="newCompanyProvince" 
                                                name="newCompanyProvince"
                                                value="<?php echo e(old('newCompanyProvince')); ?>"
                                                placeholder="e.g., Kepulauan Riau">
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <label for="newCompanyAddress">Address <span class="text-danger">*</span></label>
                                            <textarea class="form-control" 
                                                    id="newCompanyAddress" 
                                                    name="newCompanyAddress"
                                                    rows="4"
                                                    placeholder="e.g., Jl. Gajah Mada No. 12, Sei Panas, Batam Kota, Batam 29444"><?php echo e(old('newCompanyAddress')); ?></textarea>
                                        </div>

                                    </div>

                                </div>
                            </div>

                            <!-- ================= INTERNSHIP DURATION ================= -->
                            <h5 class="font-weight-bold mt-4 mb-3">Internship Duration</h5>
                            <hr class="mt-0 mb-4">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Start Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="startDate" name="start_date" required
                                        value="<?php echo e(old('start_date')); ?>" min="<?php echo e($minimal_start_date); ?>">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>End Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="endDate" name="end_date" required
                                        value="<?php echo e(old('end_date')); ?>">
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="d-block mb-2 text-muted">Internship Duration</label>
                                    <input class="form-control border rounded p-2 bg-light" id="durationText"
                                        name="durationText" value="<?php echo e(old('durationText', "0 Month(s)")); ?>" readonly />
                                </div>
                            </div>


                            <!-- ================= LETTER LANGUAGE ================= -->
                            <h5 class="font-weight-bold mt-4 mb-3">Letter Language</h5>
                            <hr class="mt-0 mb-4">

                            <div class="form-group">
                            <label class="d-block mb-2 text-muted">
                                Please select the language in which the internship letter should be issued:
                            </label>

                            <div class="custom-control custom-radio custom-control-inline">
                                <input 
                                type="radio" 
                                name="language" 
                                value="ID" 
                                class="custom-control-input" 
                                id="langID"
                                <?php echo e((old('language') === 'ID') ? 'checked' : ''); ?>

                                required
                                >
                                <label class="custom-control-label" for="langID">
                                Bahasa Indonesia
                                </label>
                            </div>

                            <div class="custom-control custom-radio custom-control-inline">
                                <input 
                                type="radio" 
                                name="language" 
                                value="EN" 
                                class="custom-control-input" 
                                id="langENG"
                                <?php echo e((old('language') === 'EN') ? 'checked' : ''); ?>

                                >
                                <label class="custom-control-label" for="langENG">
                                English
                                </label>
                            </div>
                            </div>

                            


                            <!-- ================= SUBMIT BUTTON ================= -->
                            <div class="text-right mt-4">
                                <button type="submit" class="btn btn-primary px-4 py-2 font-weight-bold">
                                    Submit Request
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('footer-scripts'); ?>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        // Inisialisasi Select2
        $('#companySelect').select2({
            theme: 'bootstrap',
            placeholder: '-- Select Company --',
            allowClear: true,
            width: '100%'
        });

        // === FUNGSI UNTUK TOGGLE COMPANY BARU ===
        function toggleCompanyFields() {
            if ($('#companyExist').is(':checked')) {

                // Show fields for new company
                $('#newCompanyFields').slideDown();

                // Disable company select
                $('#companySelect').prop('disabled', true)
                                .removeAttr('required')
                                .val('')
                                .trigger('change');

                // Required fields untuk company baru
                $('#newCompanyName, #newCompanyEmail, #newCompanyPhone, #newCompanyAddress')
                    .attr('required', true);

            } else {

                // Hide the new company fields
                $('#newCompanyFields').slideUp();

                // Enable company select
                $('#companySelect').prop('disabled', false)
                                .attr('required', true);

                // Remove required fields
                $('#newCompanyName, #newCompanyEmail, #newCompanyPhone, #newCompanyAddress')
                    .removeAttr('required');
            }
        }

        // Trigger ketika checkbox diubah
        $('#companyExist').on('change', toggleCompanyFields);

        // === 🔥 CEK SAAT HALAMAN PERTAMA KALI DIMUAT ===
        toggleCompanyFields();
    });


    // === Internship Duration Calculator ===
    function calculateDuration() {
        const start = document.getElementById('startDate').value;
        const end = document.getElementById('endDate').value;
        const durationEl = document.getElementById('durationText');

        if (!start || !end) {
            durationEl.textContent = '0 Bulan';
            return;
        }

        const startDate = new Date(start);
        const endDate = new Date(end);

        if (endDate < startDate) {
            durationEl.innerHTML = '<span class="text-danger">End date cannot be before start date!</span>';
            return;
        }

        // Hitung selisih bulan dan hari
        let months = (endDate.getFullYear() - startDate.getFullYear()) * 12;
        months -= startDate.getMonth();
        months += endDate.getMonth();

        const dayDiff = endDate.getDate() - startDate.getDate();
        const totalMonths = months + (dayDiff >= 0 ? 0 : -1);
        const days = (dayDiff >= 0 ? dayDiff : 30 + dayDiff); // asumsi rata-rata 30 hari/bulan

        durationEl.value =
            `${totalMonths > 0 ? totalMonths + ' Month(s) ' : ''}${days > 0 ? days + ' Day(s) ' : ''}` || '0 Month';
    }

    // Saat start date berubah, update min date end date
    $('#startDate').on('blur', function () {
        const startVal = $(this).val();
        $('#endDate').attr('min', startVal);
        calculateDuration();
    });

    // Saat end date berubah, pastikan tidak sebelum start date
    $('#endDate').on('blur', function () {
        const startVal = $('#startDate').val();
        const endVal = $(this).val();

        if (startVal && endVal && new Date(endVal) < new Date(startVal)) {
            alert('End date cannot be earlier than start date!');
            $(this).val('');
            $('#durationText').value('0 Month(s)');
            return;
        }
        calculateDuration();
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('templates.student', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/student/my_internship_letter/add.blade.php ENDPATH**/ ?>