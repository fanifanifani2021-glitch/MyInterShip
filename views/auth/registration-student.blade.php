@extends('templates.auth', ['title' => 'Student Registration'])

@push('head-scripts')
{{-- Select2 CSS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">

<style>
    :root {
        --radius-main: 8px;
        --primary-color: #005b9f;
        --accent-color: #1572e8;
        --bg-color: #f4f6f9;
    }

    body {
        background-color: var(--bg-color);
    }

    .container-register {
        max-width: 860px;
    }

    .card-register {
        border-radius: var(--radius-main);
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px rgba(0,0,0,0.04);
    }

    /* Form Input Styling */
    .form-control {
        border-radius: var(--radius-main) !important;
        font-size: 14px;
        height: 48px;
        border: 1px solid #cbd5e1;
        transition: all 0.2s ease-in-out;
    }
    
    .form-control:focus {
        border-color: var(--accent-color);
        box-shadow: 0 0 0 0.2rem rgba(21, 114, 232, 0.15);
    }

    /* Select2 Custom Styling */
    .select2-container--bootstrap4 .select2-selection--single {
        height: 48px !important;
        font-size: 14px;
        display: flex;
        align-items: center;
        border: 1px solid #cbd5e1;
        border-radius: var(--radius-main) !important;
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        line-height: 48px !important;
        padding-left: 12px;
        color: #334155;
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder {
        line-height: 48px !important;
        color: #94a3b8;
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
        top: 50% !important;
        transform: translateY(-50%);
        right: 12px;
    }

    /* Password Toggle Icon */
    .toggle-pass-btn {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #64748b;
        z-index: 10;
        padding: 5px;
    }

    .section-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--primary-color);
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 8px;
        margin-bottom: 20px;
        margin-top: 10px;
    }
    
    /* Live Password Rules Badge List */
    .pass-requirement-list {
        list-style: none;
        padding-left: 0;
        margin-bottom: 0;
        font-size: 12px;
    }

    .pass-requirement-list li {
        margin-bottom: 4px;
        color: #64748b;
        display: flex;
        align-items: center;
        transition: all 0.2s ease;
    }

    .pass-requirement-list li i {
        font-size: 13px;
        width: 18px;
        margin-right: 6px;
    }

    .pass-requirement-list li.valid {
        color: #16a34a;
        font-weight: 600;
    }

    .pass-requirement-list li.invalid {
        color: #dc2626;
    }

    /* Register Button */
    .btn-register {
        border-radius: var(--radius-main);
        padding: 12px;
        font-weight: 700;
        letter-spacing: 0.5px;
        font-size: 15px;
        background-color: var(--primary-color);
        border: none;
        transition: all 0.3s ease;
    }

    .btn-register:hover:not(:disabled) {
        background-color: #004a80;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 91, 159, 0.25);
    }

    .btn-register:disabled {
        background-color: #cbd5e1;
        color: #94a3b8;
        cursor: not-allowed;
    }
</style>
@endpush

@section('login-form')
<div class="container container-register p-3 mt-4 mb-5">
    
    <div class="text-center mb-4">
        <a href="{{ base_url() }}">
            <img src="{{ base_url('assets/jobcy/img/logo.png') }}" alt="MyInternship Logo" style="max-height: 60px;">
        </a>
    </div>

    <div class="card card-register border-0 bg-white">
        <div class="card-body p-4 p-md-5">
            
            <div class="d-flex align-items-center mb-4">
                <button type="button" onclick="cancel_registration()" class="btn btn-sm btn-outline-secondary mr-3" style="border-radius: var(--radius-main); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <div>
                    <h3 class="font-weight-bold mb-0 text-dark">Student Registration</h3>
                    <p class="text-muted small mb-0">Create your official internship account</p>
                </div>
            </div>

            <div class="alert alert-primary border-0 shadow-sm mb-4" style="border-radius: var(--radius-main); background-color: #e0f2fe; color: #0369a1;">
                <div class="d-flex align-items-center">
                    <div class="mr-3"><i class="fas fa-info-circle fa-lg"></i></div>
                    <div class="small"><strong>Welcome!</strong> Please enter your registered NIM correctly to verify your internship eligibility.</div>
                </div>
            </div>

            <form id="form" action="{{ router('register.student.submit') }}" method="POST">
                <?= csrf() ?>

                {{-- SECTION 1: PERSONAL INFORMATION --}}
                <h5 class="section-title"><i class="fas fa-user-graduate mr-2"></i> Personal Information</h5>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold small text-muted">FULL NAME <span class="text-danger">*</span></label>
                            <input type="text" required name="name" id="name" value="<?= old('name') ?>" placeholder="Enter Your Full Name" class="form-control <?= show_error('name'); ?>">
                            <?= form_error('name'); ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold small text-muted">NIM (STUDENT ID) <span class="text-danger">*</span></label>
                            <input type="text" required name="nim" id="nim" value="<?= old('nim') ?>" placeholder="e.g. 3311801026" class="form-control <?= show_error('nim'); ?>" maxlength="10">
                            <?= form_error('nim'); ?>
                            
                            {{-- NIM FEEDBACK AREA --}}
                            <div id="nim_feedback_area" class="mt-2" style="display: none;">
                                <div id="success_message" class="alert alert-success border-0 py-2 px-3 mb-0" style="font-size: 13px; border-radius: var(--radius-main);">
                                    <i class="fas fa-check-circle mr-1"></i> <strong>Verified!</strong> You are eligible for internship registration.
                                </div>
                                
                                <div id="eligibility_message" class="alert alert-danger border-0 py-2 px-3 mb-0" style="font-size: 13px; border-radius: var(--radius-main); display: none;">
                                    <i class="fas fa-times-circle mr-1"></i> 
                                    <strong>Not Eligible Yet.</strong> <br>
                                    Your Current Semester: <strong>Semester <span id="curr_semester"></span></strong>. <br>
                                    Minimum Required: <strong>Semester <span id="req_semester"></span></strong>.
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold small text-muted">EMAIL ADDRESS <span class="text-danger">*</span></label>
                            <input type="email" required name="email" id="email" value="<?= old('email') ?>" placeholder="Enter Your Active Email" class="form-control <?= show_error('email'); ?>">
                            <?= form_error('email'); ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold small text-muted">UNIVERSITY / POLYTECHNIC <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="id_kampus" id="id_kampus" required style="width: 100%;">
                                <option value=""></option>
                                @foreach ($university as $data)
                                    <option value="{{ $data['id_kampus'] }}" {{ old('id_kampus', 1) == $data['id_kampus'] ? 'selected' : '' }}>
                                        {{ $data['nama_kampus'] }}
                                    </option>
                                @endforeach
                            </select>
                            <?= form_error('id_kampus'); ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold small text-muted">STUDY PROGRAM <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="kode_prodi" id="kode_prodi" required style="width: 100%;">
                                <option value=""></option>
                                @php $currentJurusan = ''; @endphp
                                @foreach ($study_program as $program)
                                    @if ($program['jurusan'] !== $currentJurusan)
                                        @if ($currentJurusan !== '') </optgroup> @endif
                                        <optgroup label="{{ $program['jurusan'] }} | {{ $program['major'] }}">
                                        @php $currentJurusan = $program['jurusan']; @endphp
                                    @endif
                                    
                                    <option value="{{ $program['kode_prodi'] }}" 
                                            data-jenjang="{{ $program['jenjang'] }}" 
                                            {{ old('kode_prodi') == $program['kode_prodi'] ? 'selected' : '' }}>
                                        {{ $program['jenjang'] }} - {{ $program['prodi'] }}
                                    </option>

                                @endforeach
                                @if ($currentJurusan !== '') </optgroup> @endif
                            </select>
                            <?= form_error('kode_prodi'); ?>
                        </div>
                    </div>
                </div>

                {{-- SECTION 2: ACCOUNT CREDENTIALS --}}
                <h5 class="section-title mt-4"><i class="fas fa-lock mr-2"></i> Account Credentials</h5>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold small text-muted">USERNAME <span class="text-danger">*</span></label>
                            <input type="text" required readonly name="username" id="username" value="<?= old('username') ?>" placeholder="Auto-filled from NIM" class="form-control bg-light <?= show_error('username'); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold small text-muted">WHATSAPP NUMBER <span class="text-danger">*</span></label>
                            <input type="text" required name="no_whatsapp" id="no_whatsapp" value="<?= old('no_whatsapp') ?>" placeholder="e.g. 081234567890" class="form-control <?= show_error('no_whatsapp'); ?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- PASSWORD FIELD --}}
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold small text-muted">PASSWORD <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input id="password" required name="password" type="password" class="form-control <?= show_error('password'); ?>" placeholder="Enter New Password">
                                <span class="toggle-pass-btn" onclick="togglePasswordVisibility('password')">
                                    <i class="far fa-eye"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- CONFIRM PASSWORD FIELD --}}
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold small text-muted">CONFIRM PASSWORD <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input id="confirm_password" required name="confirm_password" type="password" class="form-control <?= show_error('confirm_password'); ?>" placeholder="Confirm New Password">
                                <span class="toggle-pass-btn" onclick="togglePasswordVisibility('confirm_password')">
                                    <i class="far fa-eye"></i>
                                </span>
                            </div>
                            <small id="confirm-pass-text" class="form-text mt-1" style="display: none; font-size: 12px;"></small>
                        </div>
                    </div>
                </div>

                {{-- 🎯 INTERACTIVE PASSWORD REQUIREMENT CHECKLIST --}}
                <div class="card bg-light border mb-4 shadow-sm" style="border-radius: var(--radius-main);">
                    <div class="card-body p-3">
                        <span class="font-weight-bold small text-dark d-block mb-2">
                            <i class="fas fa-shield-alt text-primary mr-1"></i> Password Requirements:
                        </span>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="pass-requirement-list">
                                    <li id="rule-length"><i class="far fa-circle text-muted"></i> At least 8 characters long</li>
                                    <li id="rule-uppercase"><i class="far fa-circle text-muted"></i> At least one uppercase letter (A-Z)</li>
                                    <li id="rule-lowercase"><i class="far fa-circle text-muted"></i> At least one lowercase letter (a-z)</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="pass-requirement-list">
                                    <li id="rule-number"><i class="far fa-circle text-muted"></i> At least one number (0-9)</li>
                                    <li id="rule-symbol"><i class="far fa-circle text-muted"></i> At least one symbol (!@#$%^&*...)</li>
                                    <li id="rule-match"><i class="far fa-circle text-muted"></i> Confirm password matches</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-4 pt-2">
                    <div class="custom-control custom-checkbox mb-4">
                        <input type="checkbox" class="custom-control-input" id="agreeCheckbox" required>
                        <label class="custom-control-label user-select-none" for="agreeCheckbox" style="cursor:pointer; font-size: 14px;">
                            I verify that all data provided is accurate and I agree to the <a href="javascript:void(0)" onclick="$('#termsModal').modal('show')" class="font-weight-bold text-primary">Terms of Service</a>.
                        </label>
                    </div>

                    <button id="submit_button" type="submit" class="btn btn-primary btn-block btn-register shadow-sm" disabled>
                        <i class="fas fa-spinner fa-spin mr-2" id="spinner_icon" style="display: none;"></i>
                        CREATE ACCOUNT
                    </button>
                    
                    <div class="text-center mt-4">
                        <a href="javascript:void(0)" onclick="cancel_registration()" class="text-muted small font-weight-bold text-decoration-none">
                            Back to Login Selection
                        </a>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

{{-- MODAL TERMS AND CONDITIONS --}}
<div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-main);">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title font-weight-bold text-dark">Terms and Conditions</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body px-4 pt-3 pb-4 text-muted">
                <h6>1. Eligibility for Internship</h6>
                <p class="small">Students must meet the academic requirements set by their study program to register (Min Sem 3 for D2, Sem 5 for D3, Sem 6/7 for D4).</p>
                <h6>2. Data Privacy</h6>
                <p class="small">All personal data submitted will be used strictly for internship administration and verification purposes within Politeknik Negeri Batam.</p>
                <p class="text-center mt-4 mb-0 small">-- End of Terms --</p>
            </div>
            <div class="modal-footer border-top-0 bg-light px-4 py-3" style="border-bottom-left-radius: var(--radius-main); border-bottom-right-radius: var(--radius-main);">
                <button type="button" class="btn btn-secondary font-weight-bold px-4" data-dismiss="modal" style="border-radius: var(--radius-main);">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer-scripts')
{{-- Select2 Script & SoloAlert --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="./core/component/soloalert.js"></script>

<script>
    let originalProdiHtml = '';

    $(document).ready(function() {
        // Inisialisasi Select2 Bootstrap 4 Theme
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: 'Select an option',
            allowClear: true,
            width: '100%'
        });

        originalProdiHtml = $('#kode_prodi').html();
    });

    function cancel_registration() {
        if (typeof SoloAlert !== 'undefined') {
            SoloAlert.confirm({
                title: "Cancel Registration?",
                body: "Any unsaved data will be lost. Return to login selection?",
                icon: "warning",
                theme: "light",
                useTransparency: true,
                onOk: () => { window.location.href = "{{ router('login.index') }}"; }
            });
        } else {
            if(confirm("Cancel registration and return to login?")) {
                window.location.href = "{{ router('login.index') }}";
            }
        }
    }

    // 🎯 LIVE PASSWORD REAL-TIME VALIDATOR
    function evaluatePasswordRules(password, confirmPassword) {
        const rules = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /\d/.test(password),
            symbol: /[!@#$%^&*(),.?":{}|<>]/.test(password),
            match: password !== '' && password === confirmPassword
        };

        // Update UI Indicator Rules
        updateRuleUI('rule-length', rules.length);
        updateRuleUI('rule-uppercase', rules.uppercase);
        updateRuleUI('rule-lowercase', rules.lowercase);
        updateRuleUI('rule-number', rules.number);
        updateRuleUI('rule-symbol', rules.symbol);
        updateRuleUI('rule-match', rules.match);

        // Feedback Text Confirm Password
        const confirmFeedback = document.getElementById('confirm-pass-text');
        if (confirmPassword.length > 0) {
            confirmFeedback.style.display = 'block';
            if (rules.match) {
                confirmFeedback.className = 'form-text text-success font-weight-bold mt-1';
                confirmFeedback.innerHTML = '<i class="fas fa-check-circle mr-1"></i> Passwords match';
            } else {
                confirmFeedback.className = 'form-text text-danger font-weight-bold mt-1';
                confirmFeedback.innerHTML = '<i class="fas fa-times-circle mr-1"></i> Passwords do not match';
            }
        } else {
            confirmFeedback.style.display = 'none';
        }

        return rules.length && rules.uppercase && rules.lowercase && rules.number && rules.symbol && rules.match;
    }

    function updateRuleUI(elementId, isValid) {
        const el = document.getElementById(elementId);
        if (!el) return;

        const icon = el.querySelector('i');
        if (isValid) {
            el.className = 'valid';
            icon.className = 'fas fa-check-circle text-success';
        } else {
            el.className = 'invalid';
            icon.className = 'far fa-times-circle text-danger';
        }
    }

    function checkFormValidity() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const nim = document.getElementById('nim').value;
        const agreeCheckbox = document.getElementById('agreeCheckbox').checked;
        const submitButton = document.getElementById('submit_button');

        const isPasswordValid = evaluatePasswordRules(password, confirmPassword);
        const eligibility = isMagangEligible(nim);

        if (isPasswordValid && eligibility.status && agreeCheckbox) {
            submitButton.disabled = false;
        } else {
            submitButton.disabled = true;
        }
    }

    // --- EVENT LISTENERS ---
    document.getElementById('password').addEventListener('input', checkFormValidity);
    document.getElementById('confirm_password').addEventListener('input', checkFormValidity);
    document.getElementById('agreeCheckbox').addEventListener('change', checkFormValidity);

    document.getElementById('nim').addEventListener('input', function() {
        const nim = this.value.trim();
        const feedbackArea = document.getElementById('nim_feedback_area');
        const successMsg = document.getElementById('success_message');
        const errorMsg = document.getElementById('eligibility_message');
        const usernameInput = document.getElementById('username');
        
        successMsg.style.display = 'none';
        errorMsg.style.display = 'none';
        usernameInput.value = '';
        this.classList.remove('is-valid', 'is-invalid');

        // Filter prodi otomatis
        filterProdiByNim(nim);

        const nimRegex = /^\d{10}$/;

        if (nimRegex.test(nim)) {
            feedbackArea.style.display = 'block';
            const check = isMagangEligible(nim);
            
            if (check.status) {
                successMsg.style.display = 'block';
                usernameInput.value = nim; 
                this.classList.add('is-valid');
            } else {
                errorMsg.style.display = 'block';
                document.getElementById('curr_semester').innerText = check.sem;
                document.getElementById('req_semester').innerText = check.req;
                this.classList.add('is-invalid');
            }
        } else {
            feedbackArea.style.display = 'none';
        }
        checkFormValidity();
    });

    // --- FILTER PRODI BERDASARKAN NIM ---
    function filterProdiByNim(nim) {
        var $select = $('#kode_prodi');
        $select.html(originalProdiHtml);

        var headCode = nim.charAt(0);
        var targetJenjang = '';

        if (headCode === '2') targetJenjang = 'D2';
        else if (headCode === '3') targetJenjang = 'D3';
        else if (headCode === '4') targetJenjang = 'D4';

        if (targetJenjang !== '') {
            $select.find('option').each(function() {
                var jenjangOption = $(this).data('jenjang');
                var val = $(this).val();

                if (val !== '' && jenjangOption && jenjangOption !== targetJenjang) {
                    $(this).remove();
                }
            });

            $select.find('optgroup').each(function() {
                if ($(this).children('option').length === 0) {
                    $(this).remove();
                }
            });
        }

        if ($select.find('option:selected').length === 0 || $select.val() === null) {
            $select.val('').trigger('change');
        } else {
            $select.trigger('change'); 
        }
    }

    // --- LOGIKA KELAYAKAN MAGANG ---
    function isMagangEligible(nim) {
        if (!nim || nim.length !== 10) return { status: false, sem: 0, req: 0 };

        const headCode = nim.charAt(0); // 2, 3, 4
        const yearCode = parseInt(nim.substring(3, 5), 10);
        const entryYear = 2000 + yearCode; 

        const today = new Date();
        const currentYear = today.getFullYear();
        const currentMonth = today.getMonth() + 1;

        let semesterScore = (currentYear - entryYear) * 2;
        if (currentMonth >= 7) semesterScore += 1; 
        if (semesterScore < 0) semesterScore = 0;

        let minReq = 0;
        let isEligible = false;

        // Rule: D2 (Sem 3), D3 (Sem 5), D4 (Sem 6/7)
        if (headCode === '2') { minReq = 3; isEligible = (semesterScore >= 3); }
        else if (headCode === '3') { minReq = 5; isEligible = (semesterScore >= 5); }
        else if (headCode === '4') { minReq = 6; isEligible = (semesterScore >= 6); }

        return { status: isEligible, sem: semesterScore, req: minReq };
    }

    function togglePasswordVisibility(id) {
        const input = document.getElementById(id);
        const icon = input.nextElementSibling.querySelector('i');
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash'); 
        } else {
            input.type = "password";
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endpush