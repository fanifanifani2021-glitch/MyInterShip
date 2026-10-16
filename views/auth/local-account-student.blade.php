@extends('templates.auth')

@section('login-form')

<style>
    /* --- CSS Variables --- */
    :root {
        --radius-main: 5px;
        --primary-color: #005b9f;
        --accent-color: #3498DB;
        --border-color: #e1e4e8;
    }

    body {
        background-color: #f4f6f9;
    }

    .wrapper-login {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        padding: 30px 15px;
    }

    .container-login {
        background: #fff;
        padding: 40px;
        border-radius: 8px; /* Sedikit lebih besar untuk card */
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        max-width: 500px; /* Lebar optimal untuk form */
        width: 100%;
        border: 1px solid var(--border-color);
    }

    .form-control {
        height: 48px;
        font-size: 15px;
        border-radius: var(--radius-main);
        border: 1px solid #ced4da;
    }

    .form-control:focus {
        border-color: var(--accent-color);
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.15);
    }

    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        border-radius: var(--radius-main);
        font-weight: 700;
        padding: 12px;
        font-size: 16px;
        width: 100%;
        transition: 0.3s;
    }

    .btn-primary:hover:not(:disabled) {
        background-color: #004a80;
        transform: translateY(-2px);
    }
    
    .btn-primary:disabled {
        background-color: #a0a0a0;
        border-color: #a0a0a0;
        cursor: not-allowed;
    }

    /* Toggle Password */
    .password-wrapper {
        position: relative;
    }
    .toggle-password {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #6c757d;
        z-index: 10;
        padding: 5px;
    }
    .toggle-password:hover {
        color: var(--primary-color);
    }

    /* Alert Styling */
    .alert-info-custom {
        background-color: #e3f2fd;
        border-color: #bbdefb;
        color: #0d47a1;
        border-radius: var(--radius-main);
        font-size: 14px;
        border: none;
    }
    
    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 5px;
        text-align: center;
    }
</style>

<div class="wrapper wrapper-login">
    <div class="container container-login animated fadeIn">
        
        {{-- Header Logo --}}
        <div class="text-center mb-4">
            <img src="{{ base_url('assets/jobcy/img/logo.png') }}" alt="Logo MyInternship" style="max-height: 55px; width: auto;" class="mb-3">
        </div>

        <h3 class="section-title">Setup Local Account</h3>
        <p class="text-muted small text-center mb-4">Complete your registration to access MyInternship</p>

        <div class="alert alert-info-custom mb-4 p-3 shadow-sm">
            <div class="d-flex">
                <div class="mr-3"><i class="fas fa-info-circle fa-lg mt-1"></i></div>
                <div>
                    <strong>Welcome!</strong> Before you start managing your internship data, please set up a local password for your account.
                </div>
            </div>
        </div>

        <form id="setupForm" action="index.php?form=student_local_account" method="POST">
            <?= csrf() ?>

            <div class="form-group mb-3">
                <label for="username" class="font-weight-bold small text-muted">USERNAME <span class="text-danger">*</span></label>
                <input type="text" required name="username" id="username" 
                       value="<?= old('username', $_SESSION['nim'] ) ?>" 
                       placeholder="Enter Username (Default: NIM)" 
                       class="form-control bg-light <?= show_error('username'); ?>" 
                       readonly>
                <small class="text-muted mt-1 d-block">Your username is locked to your NIM.</small>
                <?= form_error('username'); ?>
            </div>

            <div class="form-group mb-3">
                <label for="no_whatsapp" class="font-weight-bold small text-muted">WHATSAPP NUMBER <span class="text-danger">*</span></label>
                <input type="text" required name="no_whatsapp" id="no_whatsapp" 
                       value="<?= old('no_whatsapp') ?>" 
                       placeholder="e.g. 628123456789" 
                       class="form-control <?= show_error('no_whatsapp'); ?>">
                <?= form_error('no_whatsapp'); ?>
                <small class="text-muted mt-1 d-block">
                    <i class="fas fa-shield-alt mr-1"></i> Used for account recovery & urgent notifications.
                </small>
            </div>

            <div class="row">
                <div class="col-md-6 pr-md-2">
                    <div class="form-group mb-3">
                        <label for="password" class="font-weight-bold small text-muted">PASSWORD <span class="text-danger">*</span></label>
                        <div class="password-wrapper">
                            <input id="password" required name="password" type="password" 
                                   class="form-control <?= show_error('password'); ?>" 
                                   placeholder="Create Password" 
                                   style="padding-right: 40px;">
                            <div class="toggle-password" onclick="togglePasswordVisibility('password')">
                                <i class="fas fa-eye"></i>
                            </div>
                        </div>
                        <div class="invalid-feedback" id="password-feedback">
                            Too weak.
                        </div>
                        <?= form_error('password'); ?>
                    </div>
                </div>
                <div class="col-md-6 pl-md-2">
                    <div class="form-group mb-3">
                        <label for="confirm_password" class="font-weight-bold small text-muted">CONFIRM PASSWORD <span class="text-danger">*</span></label>
                        <div class="password-wrapper">
                            <input id="confirm_password" required name="confirm_password" type="password" 
                                   class="form-control <?= show_error('confirm_password'); ?>" 
                                   placeholder="Retype Password"
                                   style="padding-right: 40px;">
                            <div class="toggle-password" onclick="togglePasswordVisibility('confirm_password')">
                                <i class="fas fa-eye"></i>
                            </div>
                        </div>
                        <div class="invalid-feedback" id="confirm-password-feedback">
                            Not matching.
                        </div>
                        <?= form_error('confirm_password'); ?>
                    </div>
                </div>
            </div>

            <div class="alert alert-light border mt-2 small text-muted mb-4" style="border-radius: var(--radius-main);">
                <strong class="text-dark"><i class="fas fa-lock mr-1"></i> Security Requirements:</strong>
                <ul class="mb-0 pl-3 mt-1" style="line-height: 1.6;">
                    <li>Minimum 8 characters length</li>
                    <li>Include uppercase & lowercase letters</li>
                    <li>Include numbers & symbols (!@#$%^&*)</li>
                    <li>Cannot contain username/email</li>
                </ul>
            </div>

            <div class="form-group mb-0">
                <button id="submit_button" type="submit" class="btn btn-primary shadow-sm" disabled>
                    <span id="btn_text">Create Account</span>
                    <i class="fas fa-spin fa-spinner ml-2" id="spinner_icon" style="display: none;"></i>
                </button>
            </div>

        </form>

    </div>
</div>
@endsection

@push('footer-scripts')
<script>
    // --- 1. Password Validation Regex ---
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/;

    // --- 2. Main Validation Logic ---
    function checkFormValidity() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const submitButton = document.getElementById('submit_button');
        
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('confirm_password');

        // Check Password Strength
        let isPassValid = passwordRegex.test(password);
        if (password) {
            if (!isPassValid) {
                passwordInput.classList.add('is-invalid');
                document.getElementById('password-feedback').style.display = 'block';
            } else {
                passwordInput.classList.remove('is-invalid');
                passwordInput.classList.add('is-valid');
                document.getElementById('password-feedback').style.display = 'none';
            }
        }

        // Check Match
        let isMatch = (password === confirmPassword && password !== '');
        if (confirmPassword) {
            if (!isMatch) {
                confirmInput.classList.add('is-invalid');
                document.getElementById('confirm-password-feedback').style.display = 'block';
            } else {
                confirmInput.classList.remove('is-invalid');
                confirmInput.classList.add('is-valid');
                document.getElementById('confirm-password-feedback').style.display = 'none';
            }
        }

        // Enable Button
        if (isPassValid && isMatch) {
            submitButton.disabled = false;
        } else {
            submitButton.disabled = true;
        }
    }

    // --- 3. Toggle Visibility ---
    function togglePasswordVisibility(id) {
        const input = document.getElementById(id);
        const icon = input.nextElementSibling.querySelector('i'); // Target <i> inside .toggle-password
        
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

    // --- 4. Event Listeners ---
    document.getElementById('password').addEventListener('input', checkFormValidity);
    document.getElementById('confirm_password').addEventListener('input', checkFormValidity);

    // Loading State
    document.getElementById('setupForm').addEventListener('submit', function() {
        const btn = document.getElementById('submit_button');
        const spinner = document.getElementById('spinner_icon');
        const text = document.getElementById('btn_text');

        // Visual feedback only, let form submit naturally
        text.innerText = 'Creating...';
        spinner.style.display = 'inline-block';
        btn.style.opacity = '0.8';
    });

</script>
@endpush