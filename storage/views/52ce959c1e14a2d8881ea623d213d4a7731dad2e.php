

<?php $__env->startSection('main-content'); ?>

<div class="error-header">

    <h1 class="error-title">419 Page Expired</h1>

    <p class="error-subtitle">
        Your session has expired or the security token is invalid.
    </p>

    <?php if($message): ?>
        <p class="error-desc"><?php echo e($message); ?></p>
    <?php else: ?>
        <p class="error-desc">
            Please refresh the page and try again. This often happens if the form was open for too long or the session ended.
        </p>
    <?php endif; ?>

</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('templates.errors', ['title' => '419 - Page Expired'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/errors/error_419.blade.php ENDPATH**/ ?>