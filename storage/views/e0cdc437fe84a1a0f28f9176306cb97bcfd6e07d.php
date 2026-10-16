

<?php $__env->startSection('main-content'); ?>

<div class="error-header">
    <h1 class="error-title">400 Bad Request</h1>

    <p class="error-subtitle">Your request cannot be processed.</p>

    <?php if( $message ): ?>
    <p class="error-desc">
        <?php echo e($message); ?>

    </p>
    <?php else: ?>
    <p class="error-desc">
        It looks like something is wrong with the request you sent.
    </p>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('templates.errors', ['title' => '400 - Bad Request'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/errors/error_400.blade.php ENDPATH**/ ?>