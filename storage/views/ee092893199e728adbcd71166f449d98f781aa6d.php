

<?php $__env->startSection('icon'); ?>
    <i class="fas fa-exchange-alt"></i>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('heading', 'Page Moved Permanently'); ?>

<?php $__env->startSection('message'); ?>
    The page you are looking for has been moved to a new location due to system updates.
<?php $__env->stopSection(); ?>

<?php $__env->startSection('action_area'); ?>
    <div class="info-box">
        Redirecting you automatically in <span id="timer" class="countdown"><?php echo e($seconds ?? 5); ?></span> seconds to:<br>
        <a href="<?php echo e($newUrl); ?>" class="font-weight-bold text-primary mt-2 d-block" style="word-break: break-all;">
            <?php echo e($newUrl); ?>

        </a>
    </div>

    <a href="<?php echo e($newUrl); ?>" class="btn btn-primary">
        <i class="fas fa-arrow-right mr-2"></i> Go There Now
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('footer-scripts'); ?>
<script>
    let seconds = <?php echo e($seconds ?? 5); ?>;
    const timerElement = document.getElementById('timer');
    const newUrl = "<?php echo e($newUrl); ?>";

    const countdown = setInterval(function() {
        seconds--;
        if(timerElement) timerElement.textContent = seconds;
        
        if (seconds <= 0) {
            clearInterval(countdown);
            window.location.href = newUrl;
        }
    }, 1000);
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('templates.errors_redirection', ['title' => 'Page Moved'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/errors/error_301.blade.php ENDPATH**/ ?>