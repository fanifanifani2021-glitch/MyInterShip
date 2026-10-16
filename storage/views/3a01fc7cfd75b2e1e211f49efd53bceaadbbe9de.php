

<?php $__env->startSection('main-content'); ?>

<div class="error-header">

    <h1 class="error-title">503 Service Unavailable</h1>

    <p class="error-subtitle">
        The server is currently unavailable or under maintenance.
    </p>

    <?php if($message): ?>
        <p class="error-desc"><?php echo e($message); ?></p>
    <?php else: ?>
        <p class="error-desc">
            Please try again later. This service might be temporarily offline or overloaded.
        </p>
    <?php endif; ?>

</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('templates.errors', ['title' => '503 - Service Unavailable'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp_new\htdocs\pblKita\views/errors/error_503.blade.php ENDPATH**/ ?>