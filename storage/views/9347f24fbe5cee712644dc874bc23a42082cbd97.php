

<?php $__env->startSection('main-content'); ?>

<div class="error-header">

    <h1 class="error-title">500 Internal Server Error</h1>

    <p class="error-subtitle">
        Something went wrong on our server.
    </p>

    <?php if($message): ?>
        <p class="error-desc"><?php echo e($message); ?></p>
    <?php else: ?>
        <p class="error-desc">
            An unexpected error occurred. Please try again later or contact support if the issue persists.
        </p>
    <?php endif; ?>

</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('templates.errors', ['title' => '500 - Internal Server Error'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/errors/error_500.blade.php ENDPATH**/ ?>