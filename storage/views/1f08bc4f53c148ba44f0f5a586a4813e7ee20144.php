

<?php $__env->startPush('head-scripts'); ?>
<style>
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
    }
    .info-card-auth {
        border-left: 4px solid #005b9f;
        background-color: #f8fafc;
    }
    .password-rules-box {
        background-color: #fffbe0;
        border: 1px solid #ffe58f;
        border-radius: 8px;
        padding: 12px 16px;
    }
    .password-rules-list {
        margin-bottom: 0;
        padding-left: 20px;
    }
    .password-rules-list li {
        font-size: 12px;
        color: #595959;
        margin-bottom: 4px;
    }
    .password-rules-list li:last-child {
        margin-bottom: 0;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="container">
    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <h2 class="text-white pb-2 fw-bold">Account Security</h2>
            <h5 class="text-white op-7 mb-2">Change your password and manage local login credentials</h5>
        </div>
    </div>

    <div class="page-inner mt--5">
        <div class="row mt--2">
            <div class="col-md-12">
                <div class="card card-with-nav">
                    <?php echo $__env->make('student.identity._nav', ['activeTab' => 'security'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                    <div class="card-body">
                        
                        
                        <div class="card info-card-auth mb-4 shadow-sm">
                            <div class="card-body">
                                <h5 class="fw-bold text-primary mb-2">
                                    <i class="fas fa-info-circle mr-1"></i> How Authentication Works on MyInternship
                                </h5>

                                <?php if(!empty($_SESSION['POLIBATAM_STUDENT']) || ($_SESSION['StudentInternship_Data']['id_kampus'] ?? 0) == 1): ?>
                                    
                                    <p class="text-dark mb-2">
                                        As a <strong>Polibatam Student</strong>, your primary login method is using your <strong>Polibatam Learning Account</strong>.
                                    </p>
                                    <ul class="pl-3 mb-2 text-muted small">
                                        <li>You can log in directly using your <strong>Learning Account credentials</strong>.</li>
                                        <li>You can also activate a <strong>MyInternship Local Account</strong> below. This allows you to log in using your <strong>NIM</strong> and a <strong>Custom Local Password</strong> as a backup option if the Learning Account system is undergoing maintenance.</li>
                                    </ul>
                                <?php else: ?>
                                    
                                    <p class="text-dark mb-2">
                                        As an <strong>External / Partner University Student</strong>, your authentication relies entirely on your <strong>MyInternship Local Account</strong>.
                                    </p>
                                    <ul class="pl-3 mb-2 text-muted small">
                                        <li>Log in using your registered <strong>NIM / Student ID</strong> and your local account password.</li>
                                        <li>Keep your password secure and update it periodically below.</li>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>

                        
                        <?php if(!empty($_SESSION['POLIBATAM_STUDENT']) || ($_SESSION['StudentInternship_Data']['id_kampus'] ?? 0) == 1): ?>
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h4 class="fw-bold text-primary mb-3"><i class="fas fa-user-shield mr-1"></i> MyInternship Local Account Status</h4>
                                    
                                    <form action="<?php echo e(router('student.identity.save_local_account')); ?>" method="POST" class="p-3 border rounded bg-light">
                                        <?= csrf() ?>
                                        <div class="form-group p-0 mb-3">
                                            <label class="fw-bold">Local Account Backup Login Status</label>
                                            <select name="local_account" class="form-control col-md-6">
                                                <option value="1" <?= (!empty($student_data['local_account']) && $student_data['local_account'] == 1) ? 'selected' : ''; ?>>
                                                    Active (Allow login using NIM & Local Password)
                                                </option>
                                                <option value="0" <?= (empty($student_data['local_account']) || $student_data['local_account'] == 0) ? 'selected' : ''; ?>>
                                                    Disabled (Only allow Polibatam Learning Account login)
                                                </option>
                                            </select>
                                            <small class="text-muted d-block mt-1">Enabling this option allows you to set a local password below to log in during SSO/Learning Account outages.</small>
                                        </div>

                                        <button type="submit" class="btn btn-primary btn-sm fw-bold">
                                            <i class="fas fa-save mr-1"></i> Update Local Account Status
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <hr>
                        <?php endif; ?>

                        
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <h4 class="fw-bold text-primary mb-3"><i class="fas fa-key mr-1"></i> Change Local Account Password</h4>
                                
                                
                                <div class="password-rules-box mb-4">
                                    <h6 class="fw-bold text-warning-d dark mb-2" style="color: #d48806;">
                                        <i class="fas fa-shield-alt mr-1"></i> Password Requirements:
                                    </h6>
                                    <ul class="password-rules-list">
                                        <li>Minimum length of <strong>6 characters</strong> (8+ characters recommended for higher security).</li>
                                        <li>Must match the <strong>New Password Confirmation</strong> field exactly.</li>
                                        <li>Your <strong>Current Password</strong> must be verified correctly before making changes.</li>
                                        <li>Recommended to include a combination of <strong>letters, numbers, and special symbols</strong>.</li>
                                    </ul>
                                </div>

                                <form action="<?php echo e(router('student.identity.change_password')); ?>" method="POST">
                                    <?= csrf() ?>
                                    
                                    
                                    <div class="form-group p-0 mb-3">
                                        <label>Current Password <span class="text-danger">*</span></label>
                                        <div class="password-wrapper">
                                            <input type="password" class="form-control pass-input <?= show_error('current_password'); ?>" name="current_password" value="<?= old('current_password'); ?>" placeholder="Enter Your Current Password" required style="padding-right: 45px;">
                                            <div class="toggle-password" onclick="togglePasswordVisibility(this)">
                                                <i class="fas fa-eye"></i>
                                            </div>
                                        </div>
                                        <?= form_error('current_password'); ?>
                                    </div>

                                    <div class="row">
                                        
                                        <div class="form-group col-md-6 mb-3">
                                            <label>New Password <span class="text-danger">*</span></label>
                                            <div class="password-wrapper">
                                                <input type="password" class="form-control pass-input <?= show_error('new_password'); ?>" name="new_password" value="<?= old('new_password'); ?>" placeholder="Enter Your New Password" required minlength="6" style="padding-right: 45px;">
                                                <div class="toggle-password" onclick="togglePasswordVisibility(this)">
                                                    <i class="fas fa-eye"></i>
                                                </div>
                                            </div>
                                            <?= form_error('new_password'); ?>
                                        </div>

                                        
                                        <div class="form-group col-md-6 mb-3">
                                            <label>Confirm New Password <span class="text-danger">*</span></label>
                                            <div class="password-wrapper">
                                                <input type="password" class="form-control pass-input <?= show_error('new_password2'); ?>" name="new_password2" value="<?= old('new_password2'); ?>" placeholder="Confirm Your New Password" required minlength="6" style="padding-right: 45px;">
                                                <div class="toggle-password" onclick="togglePasswordVisibility(this)">
                                                    <i class="fas fa-eye"></i>
                                                </div>
                                            </div>
                                            <?= form_error('new_password2'); ?>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-success fw-bold">
                                        <i class="fas fa-lock mr-1"></i> Save New Password
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('footer-scripts'); ?>
<script>
    function togglePasswordVisibility(btn) {
        let input = $(btn).siblings('.pass-input');
        let icon = $(btn).find('i');

        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    }
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('templates.student', ['title' => 'Account Security'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/student/identity/security.blade.php ENDPATH**/ ?>