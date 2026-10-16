

<?php $__env->startSection('content'); ?>
    <div class="icon-box">
        <i class="fas fa-exchange-alt"></i>
    </div>

    <h1 class="title">We've Moved This Page!</h1>
    
    <p class="subtitle">
        The page you are looking for has been moved to a new location due to system updates.
    </p>

    <div class="redirect-box">
        Redirecting you automatically in <span id="timer" class="countdown"><?php echo e($seconds ?? 5); ?></span> seconds to:
        <span class="redirect-url"><?php echo e($newUrl); ?></span>
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
        timerElement.textContent = seconds;
        
        if (seconds <= 0) {
            clearInterval(countdown);
            window.location.href = newUrl;
        }
    }, 1000);
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('templates.moved', ['title' => 'Page Moved'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/errors/moved.blade.php ENDPATH**/ ?>