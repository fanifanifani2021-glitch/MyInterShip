<div class="card-header">
    <div class="row row-nav-line">
        <ul class="nav nav-tabs nav-line nav-color-secondary w-100 pl-4" role="tablist">
            <li class="nav-item">
                <a class="nav-link <?php echo e($activeTab === 'identity' ? 'active' : ''); ?>" href="<?php echo e(router('student.identity.index')); ?>">
                    <i class="fas fa-id-card mr-1"></i> Identity & Documents
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo e($activeTab === 'avatar' ? 'active' : ''); ?>" href="<?php echo e(router('student.identity.avatar')); ?>">
                    <i class="fas fa-user-circle mr-1"></i> Profile Picture
                </a>
            </li>
            <?php if(empty($_SESSION['POLIBATAM_STUDENT'])): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo e($activeTab === 'security' ? 'active' : ''); ?>" href="<?php echo e(router('student.identity.security')); ?>">
                        <i class="fas fa-lock mr-1"></i> Security & Password
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div><?php /**PATH C:\xampp_new\htdocs\pblKita\views/student/identity/_nav.blade.php ENDPATH**/ ?>