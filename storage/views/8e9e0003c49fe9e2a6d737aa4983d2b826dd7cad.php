

<?php $__env->startSection('main-content'); ?>

<div class="error-header">

    <h1 class="error-title">401 Unauthorized</h1>

    <p class="error-subtitle">You are not authorized to access this page.</p>


    <?php if( $message ): ?>
    <p class="error-desc">
        <?php echo e($message); ?>

    </p>
    <?php else: ?>
    <p class="error-desc">
        Please log in with a valid account or check your permission level.
    </p>
    <?php endif; ?>


</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('templates.errors', ['title' => '401 - Unauthorized'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/errors/error_401.blade.php ENDPATH**/ ?>