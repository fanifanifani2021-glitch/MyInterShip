

<?php $__env->startSection('main-content'); ?>
<div class="container">
    <div class="page-inner mt-2">
        <div class="page-header">
            <h4 class="page-title">Support Ticket Detail</h4>
            <ul class="breadcrumbs">
                <li class="nav-home"><a href="<?php echo e(router('landing')); ?>"><i class="flaticon-home"></i></a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="<?php echo e(router('student.helpdesk.index')); ?>">Helpdesk</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="#">Ticket #<?php echo e($ticket->ticket_code); ?></a></li>
            </ul>
        </div>

        <div class="row">
            
            <div class="col-md-8">
                
                <div class="card">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title fw-bold text-primary mb-1"><?php echo e($ticket->subject); ?></h4>
                                <small class="text-muted"><i class="fas fa-user mr-1"></i> Submitted by: <strong><?php echo e($ticket->user_name); ?></strong></small>
                            </div>
                            <div>
                                <?php echo $ticket->getStatusBadge(); ?>

                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="p-3 bg-white rounded border mb-3" style="line-height: 1.7;">
                            <?php echo html_entity_decode(htmlspecialchars_decode($ticket->description ?? '')); ?>

                        </div>

                        
                        <?php $attachments = $ticket->attachments(); ?>
                        <?php if(count($attachments) > 0): ?>
                            <h6 class="fw-bold mt-4 mb-2"><i class="fas fa-paperclip mr-1 text-primary"></i> Attachments (<?php echo e(count($attachments)); ?>):</h6>
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                <?php $__currentLoopData = $attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="p-1 border rounded bg-white shadow-sm mr-2 mb-2">
                                        <a href="<?php echo e($att->file_path); ?>" target="_blank" title="<?php echo e($att->file_name); ?>">
                                            <img src="<?php echo e($att->file_path); ?>" alt="Attachment" class="img-fluid rounded" style="max-width: 180px; max-height: 130px; object-fit: cover;">
                                        </a>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                
                <h5 class="fw-bold my-3 text-secondary"><i class="fas fa-comments mr-1"></i> Conversation Thread</h5>

                <?php $replies = $ticket->replies(); ?>
                <?php if(count($replies) === 0): ?>
                    <div class="alert alert-light border text-center text-muted my-3">
                        <i class="fas fa-info-circle mr-1"></i> No replies yet. The CDC support team will respond to your ticket soon.
                    </div>
                <?php else: ?>
                    <?php $__currentLoopData = $replies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reply): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="card mb-3 <?php echo e($reply->user_type === 'Student' ? 'border-primary' : 'border-success'); ?>">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold <?php echo e($reply->user_type === 'Student' ? 'text-primary' : 'text-success'); ?>">
                                        <i class="fas <?php echo e($reply->user_type === 'Student' ? 'fa-user-graduate' : 'fa-user-shield'); ?> mr-1"></i> 
                                        <?php echo e($reply->user_name); ?> (<?php echo e($reply->user_type); ?>)
                                    </span>
                                    <small class="text-muted"><?php echo e(date('d M Y, H:i', strtotime($reply->created_at))); ?></small>
                                </div>
                                <div class="p-2 bg-light rounded">
                                    <?php echo nl2br(htmlspecialchars($reply->message)); ?>

                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>

                
                <?php if(strtolower($ticket->status) !== 'closed'): ?>
                    <div class="card mt-4">
                        <div class="card-header"><h5 class="card-title fw-bold">Post a Reply</h5></div>
                        <div class="card-body">
                            <form id="form-reply-ticket">
                                <?= csrf() ?>
                                <input type="hidden" name="id_ticket" value="<?php echo e($ticket->id_ticket); ?>">
                                <div class="form-group p-0 mb-3">
                                    <textarea name="message" id="reply-message" class="form-control" rows="4" placeholder="Write your response or follow-up question here..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary fw-bold"><i class="fas fa-reply mr-1"></i> Send Reply</button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning border text-center my-3">
                        <i class="fas fa-lock mr-1"></i> This ticket is closed. Re-opening or replying is disabled.
                    </div>
                <?php endif; ?>
            </div>

            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title fw-bold text-white mb-0">Ticket Details</h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="text-muted">Ticket Code</span>
                                <strong class="text-primary"><?php echo e($ticket->ticket_code); ?></strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="text-muted">Category</span>
                                <strong><?php echo e($ticket->category ? $ticket->category->category_name : 'General'); ?></strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="text-muted">Priority</span>
                                <?php echo $ticket->getPriorityBadge(); ?>

                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="text-muted">Status</span>
                                <?php echo $ticket->getStatusBadge(); ?>

                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="text-muted">Created Date</span>
                                <small><?php echo e(date('d M Y, H:i', strtotime($ticket->created_at))); ?></small>
                            </li>
                        </ul>
                    </div>
                    <div class="card-footer">
                        <a href="<?php echo e(router('student.helpdesk.index')); ?>" class="btn btn-light btn-block">
                            <i class="fas fa-arrow-left mr-1"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('footer-scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let _token = $('meta[name="csrf-token"]').attr('content');

    $(document).ready(function() {
        $('#form-reply-ticket').on('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Posting reply...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: "<?php echo e(router('student.helpdesk.reply')); ?>",
                type: "POST",
                data: $(this).serialize() + '&_token=' + _token,
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        location.reload();
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Internal Server Error occurred.', 'error');
                }
            });
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('templates.student', ['title' => 'Ticket Detail #' . $ticket->ticket_code], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/student/helpdesk/detail.blade.php ENDPATH**/ ?>