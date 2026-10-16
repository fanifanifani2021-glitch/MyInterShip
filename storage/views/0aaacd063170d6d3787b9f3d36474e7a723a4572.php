

<?php $__env->startSection('main-content'); ?>

<div class="error-header">

    <h1 class="error-title">404 Not Found</h1>

    <p class="error-subtitle">
        The page you are looking for could not be found.
    </p>

    <?php if($message): ?>
        <p class="error-desc"><?php echo e($message); ?></p>
    <?php else: ?>
        <p class="error-desc">
            The resource you are trying to access does not exist or may have been moved.
        </p>
    <?php endif; ?>

</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('templates.errors', ['title' => '404 - Not Found'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/errors/error_404.blade.php ENDPATH**/ ?>