

<?php $__env->startSection('main-content'); ?>

<div class="error-header">

    <h1 class="error-title">429 Too Many Requests</h1>

    <p class="error-subtitle">
        You have sent too many requests in a short period of time.
    </p>

    <?php if($message): ?>
        <p class="error-desc"><?php echo e($message); ?></p>
    <?php else: ?>
        <p class="error-desc">
            Please slow down and try again later. This is a rate-limit protection to keep the system stable.
        </p>
    <?php endif; ?>

</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('templates.errors', ['title' => '429 - Too Many Requests'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/errors/error_429.blade.php ENDPATH**/ ?>