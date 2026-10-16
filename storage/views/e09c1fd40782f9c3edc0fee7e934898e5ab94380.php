

<?php $__env->startSection('title', 'Router Analytics'); ?>

<?php $__env->startSection('content'); ?>

    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 font-weight-bold">
            <i class="fas fa-route text-primary mr-2"></i>
            Router Analytics
        </h4>

        <span class="badge bg-info text-white p-2 shadow-sm" style="font-size: 0.9rem;">
            Total Logs: <?php echo e($totalLogs); ?>

        </span>
    </div>

    
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">

            <?php if(empty($groupedData)): ?>
                <div class="p-5 text-center text-muted">
                    <i class="fas fa-check-circle fa-3x mb-3 text-success"></i><br>
                    <h5>No Error Logs Found</h5>
                    <p class="mb-0">Great! Your router seems healthy.</p>
                </div>
            <?php else: ?>
                
                
                <div class="table-responsive" style="max-height: 75vh; overflow-y: auto;">
                    
                    <table class="table table-hover table-fixed-head mb-0" style="font-size: 0.9rem;">
                        <thead>
                            <tr>
                                <th class="align-middle text-center" style="width: 50px;">#</th>
                                <th class="align-middle" style="width: 110px;">Time</th>
                                <th class="align-middle text-center" style="width: 80px;">Code</th>
                                <th class="align-middle text-center" style="width: 90px;">Method</th>
                                <th class="align-middle">Route / Path</th>
                                <th class="align-middle">Referer</th>
                                <th class="align-middle" style="width: 140px;">IP Address</th>
                                <th class="align-middle">User Agent</th>
                            </tr>
                        </thead>
                        
                        
                        <?php $globalIndex = 1; ?>

                        <?php $__currentLoopData = $groupedData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date => $logs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            
                            
                            <tbody class="border-top-0">
                                
                                
                                <tr class="sticky-date-header">
                                    
                                    <td colspan="100%" class="py-2 px-3 align-middle bg-dark text-white border-0">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <i class="far fa-calendar-alt mr-2 text-warning"></i> 
                                                <span class="font-weight-bold" style="font-size: 1rem;">
                                                    <?php echo e(date('l, d F Y', strtotime($date))); ?>

                                                </span>
                                            </div>
                                            <span class="badge bg-light text-dark shadow-sm">
                                                <?php echo e(count($logs)); ?> Events
                                            </span>
                                        </div>
                                    </td>
                                </tr>

                                
                                <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr style="border-bottom: 1px solid #f0f0f0;">
                                        <td class="text-muted align-middle text-center"><?php echo e($globalIndex++); ?></td>

                                        <td class="align-middle">
                                            <span class="font-weight-bold text-dark font-monospace">
                                                <?php echo e(date('H:i:s', strtotime($log['timestamp'] ?? 'now'))); ?>

                                            </span>
                                        </td>

                                        <td class="align-middle text-center">
                                            <?php 
                                                $code = $log['code'] ?? 500; 
                                                // FIX: Gunakan class 'bg-*' sesuai parent layout
                                                $badgeClass = 'bg-secondary';
                                                
                                                if ($code >= 500) $badgeClass = 'bg-danger'; 
                                                elseif ($code == 404) $badgeClass = 'bg-warning text-white'; 
                                                elseif ($code == 403) $badgeClass = 'bg-dark'; 
                                                elseif ($code == 419) $badgeClass = 'bg-info text-white'; 
                                            ?>
                                            <span class="badge <?php echo e($badgeClass); ?> p-2 shadow-sm" style="min-width: 50px;">
                                                <?php echo e($code); ?>

                                            </span>
                                        </td>

                                        <td class="align-middle text-center">
                                            <?php 
                                                $method = strtoupper($log['method'] ?? '-'); 
                                                // FIX: Gunakan class 'bg-*' sesuai parent layout
                                                $methodClass = 'bg-secondary';
                                                
                                                if ($method === 'GET') $methodClass = 'bg-primary'; 
                                                if ($method === 'POST') $methodClass = 'bg-success'; 
                                                if ($method === 'PUT' || $method === 'PATCH') $methodClass = 'bg-warning text-white'; 
                                                if ($method === 'DELETE') $methodClass = 'bg-danger'; 
                                            ?>
                                            <span class="badge <?php echo e($methodClass); ?> w-100 py-1 shadow-sm">
                                                <?php echo e($method); ?>

                                            </span>
                                        </td>

                                        <td class="align-middle">
                                            <code class="text-danger font-weight-bold small" style="word-break: break-all;">
                                                <?php echo e($log['route'] ?? $log['message'] ?? '-'); ?>

                                            </code>
                                        </td>

                                        <td class="align-middle">
                                            <code class="text-danger font-weight-bold small" style="word-break: break-all;">
                                                <?php echo e($log['referer'] ?? '-'); ?>

                                            </code>
                                        </td>

                                        <td class="align-middle">
                                            <small class="text-monospace font-weight-bold text-secondary bg-light px-2 py-1 rounded border">
                                                <?php echo e($log['ip'] ?? '-'); ?>

                                            </small>
                                        </td>

                                        <td class="align-middle">
                                            <div style="max-width: 250px; line-height: 1.2;">
                                                <small class="text-muted d-block text-truncate" title="<?php echo e($log['user_agent'] ?? '-'); ?>">
                                                    <?php echo e($log['user_agent'] ?? '-'); ?>

                                                </small>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                            

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </table>
                </div>

            <?php endif; ?>

        </div>
    </div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('templates.simple', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/analytics/index.blade.php ENDPATH**/ ?>