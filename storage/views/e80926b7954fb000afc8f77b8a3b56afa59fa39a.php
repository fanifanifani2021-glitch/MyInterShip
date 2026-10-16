

<?php $__env->startSection('login-form'); ?>

<style>
    /* Styling khusus untuk halaman login */
    .wrapper-login {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        background-color: #f4f6f9;
        padding: 20px;
    }

    .container-login {
        background: #fff;
        padding: 40px;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        max-width: 450px;
        width: 100%;
        border: 1px solid #e1e4e8;
    }

    .form-control {
        height: 48px;
        font-size: 15px;
        border-radius: 5px;
        border: 1px solid #ced4da;
    }

    .form-control:focus {
        border-color: #3498DB;
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.15);
    }

    .btn-primary {
        background-color: #005b9f;
        border-color: #005b9f;
        border-radius: 5px;
        font-weight: 600;
        padding: 12px;
        font-size: 16px;
    }

    .btn-primary:hover {
        background-color: #004a80;
    }

    .btn-outline-secondary {
        border-radius: 5px;
        font-size: 14px;
        padding: 10px;
    }

    /* Fix Toggle Password Style */
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
        user-select: none;
    }
    
    .toggle-password:hover {
        color: #005b9f;
    }
</style>

<div class="wrapper wrapper-login" role="main" aria-labelledby="page-title">
    <div class="container container-login animated fadeIn">
        
        
        <div class="text-center mb-4">
            <a href="<?php echo e(base_url()); ?>">
                <img src="<?php echo e(base_url('assets/jobcy/img/logo.png')); ?>" alt="Logo MyInternship" style="max-height: 55px; width: auto;" class="mb-3">
            </a>
        </div>

        
        <div class="text-center mb-4">
            <h4 class="font-weight-bold mb-1 text-dark">Student Login</h4>
            <p class="text-muted small mb-0">Masuk sebagai Mahasiswa</p>
        </div>
        
        <div class="login-form">

            
            <?php if (!empty($_SESSION['redirect_intended'])) : ?>
            <div class="alert alert-warning border-0 small shadow-sm mb-4" style="background-color: #fff8e1; color: #5d4037; border-radius: 5px; border-left: 4px solid #ffb74d;">
                <div class="d-flex align-items-start">
                    <div class="mr-2 mt-1"><i class="fas fa-reply fa-lg text-warning"></i></div>
                    <div class="w-100" style="overflow: hidden;">
                        <strong>Redirecting you back</strong><br>
                        <span class="text-muted">You will be redirected to:</span>
                        <div class="bg-white p-1 px-2 rounded mt-1 border border-light text-truncate text-muted font-monospace" style="max-width: 100%; font-family: monospace;">
                            <?= htmlspecialchars($_SESSION['redirect_intended']) ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            
            <div class="alert alert-info border-0 small shadow-sm mb-4" style="background-color: #e3f2fd; color: #0d47a1; border-radius: 5px;">
                <div class="d-flex">
                    <div class="mr-2"><i class="fas fa-info-circle mt-1"></i></div>
                    <div>
                        <strong>Polibatam Student?</strong><br>
                        You can login using your <b>Learning Account</b> (e.g., <i>nim@students.polibatam.ac.id</i>) or your registered local account.
                    </div>
                </div>
            </div>

            
            <?php if( $_SERVER['API_PROBLEM'] ?? false ): ?>
            <div class="alert alert-warning border-0 small shadow-sm mb-3" style="background-color: #fff3cd; color: #856404;">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                <strong>Notice:</strong> There is an issue with the Learning account login. Please try again later or use your local account.
            </div>
            <?php endif; ?>

            
            <?= form_error('failed_attempt', '', '<div class="alert alert-danger border-0 small shadow-sm mb-3"> <i class="fas fa-times-circle mr-1"></i>', '</div>'); ?>

            <form id="loginForm" action="<?php echo e(router('login.student.post')); ?>" method="POST">
                <?php echo csrf_field(); ?>

                
                
                <?= input_hidden('redirect_intended', base64_encode($_SESSION['redirect_intended'] ?? '')) ?>

                <div class="form-group mb-3">
                    <label for="nim" class="font-weight-bold small text-muted">USERNAME / NIM / EMAIL <span class="text-danger">*</span></label>
                    <input type="text" required name="nim" id="nim" 
                           value="<?= old('nim', $_COOKIE['nim'] ?? '') ?>" 
                           placeholder="Enter NIM or Learning Email" 
                           class="form-control <?= show_error('nim'); ?>" 
                           autocomplete="username">
                    <?= form_error('nim'); ?>
                </div>

                <div class="form-group mb-4">
                    <div class="d-flex justify-content-between">
                        <label for="password" class="font-weight-bold small text-muted">PASSWORD <span class="text-danger">*</span></label>
                    </div>
                    
                    
                    <div class="password-wrapper">
                        <input id="password" required name="password" type="password" 
                               class="form-control <?= show_error('password'); ?>" 
                               placeholder="Enter your password" 
                               autocomplete="current-password"
                               style="padding-right: 45px;">
                        
                        <div class="toggle-password" onclick="togglePasswordVisibility()">
                            <i class="fas fa-eye" id="eye-icon"></i>
                        </div>
                    </div>
                    <?= form_error('password'); ?>
                </div>

                
                <?php if (($_SESSION['failed_login']['multiplier'] ?? 0) >= 5) : ?>
                <div class="alert alert-light border small text-muted mb-3">
                    <strong class="text-danger">Too many failed attempts (<?= $_SESSION['failed_login']['multiplier'] ?>).</strong>
                    <br>
                    Having trouble? Contact <a target="_blank" href="https://api.whatsapp.com/send/?phone=6281364440803&text=Halo%20Helpdesk,%20saya%20mengalami%20kendala%20login%20mahasiswa." class="font-weight-bold text-primary">Helpdesk</a>.
                </div>
                <?php endif; ?>

                <div class="form-group mb-4">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="remember_me" id="remember_me" <?= (($_COOKIE['nim'] ?? '') != '') ? 'checked' : ''; ?>>
                        <label class="custom-control-label text-muted small user-select-none" for="remember_me" style="cursor: pointer;">Remember my NIM on this device</label>
                    </div>
                </div>

                <button id="sign_in_button" type="submit" class="btn btn-primary btn-block shadow-sm">
                    <span id="btn_text">Sign In</span>
                    <i class="fas fa-spin fa-spinner ml-2" id="spinner_icon" style="display: none;"></i>
                </button>

            </form>

            
            <div class="separator-solid my-4 border-top"></div>

            <div class="row">
                <div class="col-6 pr-1">
                    <a href="<?php echo e(router('login.index')); ?>" class="btn btn-outline-secondary btn-block">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                </div>
                <div class="col-6 pl-1">
                    <a href="<?php echo e(router('landing')); ?>" class="btn btn-outline-secondary btn-block">
                        <i class="fas fa-home mr-1"></i> Home
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    // Toggle Password Visibility
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }

    // Loading State on Submit
    document.getElementById('loginForm').addEventListener('submit', function() {
        const btn = document.getElementById('sign_in_button');
        const spinner = document.getElementById('spinner_icon');
        const text = document.getElementById('btn_text');

        // Simple visual feedback
        text.innerText = 'Signing In...';
        spinner.style.display = 'inline-block';
    });
</script>

<?php $__env->stopSection(); ?>

<?php
$_SESSION['redirect_intended'] = null;
?>
<?php echo $__env->make('templates.auth', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp_new\htdocs\pblKita\views/auth/login-mahasiswa.blade.php ENDPATH**/ ?>