

<?php $__env->startPush('head-scripts'); ?>
<style>
    /* Background & Layout */
    .page-inner { background: #f4f7f6; min-height: 100vh; padding: 25px 15px !important; }

    /* Discussion Card - Enterprise Style */
    .discussion-card { 
        background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; 
        margin-bottom: 12px; display: flex; transition: all 0.3s ease; 
        overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.02); position: relative;
    }
    .discussion-card:hover { border-color: #1572e8; box-shadow: 0 8px 16px rgba(0,0,0,0.06); }
    
    /* Clickable area wrapper to fix event bubbling */
    .card-link-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; }
    .card-interactive-elements { position: relative; z-index: 2; } /* Ensure buttons are clickable */

    .status-sidebar { width: 6px; flex-shrink: 0; }
    .status-open { background: #31c48d; }
    .status-closed { background: #f05252; }
    
    .card-content { padding: 18px 22px; flex-grow: 1; min-width: 0; }
    
    /* Typography */
    .meta-text { font-size: 11px; color: #64748b; margin-bottom: 8px; display: flex; align-items: center; flex-wrap: wrap; gap: 8px; }
    .post-title { font-size: 1.15rem; font-weight: 700; color: #1e293b; margin-bottom: 8px; line-height: 1.4; display: block; }
    .topic-preview { color: #64748b; font-size: 13px; line-height: 1.5; margin-bottom: 15px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }

    /* Action Footer */
    .post-footer { display: flex; gap: 20px; padding-top: 12px; border-top: 1px solid #f1f5f9; }
    .footer-item { font-size: 12px; font-weight: 600; color: #94a3b8; display: flex; align-items: center; border: none; background: none; padding: 0; }
    .footer-item:hover { color: #1572e8; }
    .footer-item i { margin-right: 6px; }

    /* Sidebar Professional */
    .info-card { background: #fff; border-radius: 10px; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.04); overflow: hidden; margin-bottom: 20px; }
    .info-header { padding: 15px 20px; background: #1572e8; color: white; display: flex; align-items: center; font-weight: 700; }
    .participant-list { list-style: none; padding: 0; margin: 0; }
    .participant-list li { font-size: 13px; padding: 12px 20px; border-bottom: 1px solid #f8fafc; color: #475569; display: flex; align-items: center; }
    .participant-list li i { width: 25px; color: #1572e8; }

    /* Pagination Dropdown */
    .jump-to-wrapper { display: flex; align-items: center; justify-content: center; gap: 12px; margin-top: 40px; }
    .jump-select { width: 140px; height: 35px; border: 1px solid #e2e8f0; border-radius: 6px; padding: 0 10px; font-weight: 600; color: #475569; }

    @media (max-width: 768px) {
        .feed-container { order: 1; }
        .sidebar-container { order: 2; margin-top: 20px; }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="container">
    <div class="page-inner">
        
        <div class="page-header">
            <h4 class="page-title fw-bold">Discussion Forum</h4>
            <ul class="breadcrumbs">
                <li class="nav-home"><a href="<?php echo e(base_url()); ?>"><i class="flaticon-home text-primary"></i></a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="<?php echo e(router('student.internship')); ?>">My Internship</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="<?php echo e(router('student.internship.detail', ['id' => $id_enc])); ?>">Detail</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><b>Discussion</b></li>
            </ul>
        </div>

        <div class="row">
            <div class="col-lg-8 feed-container">
                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0 d-none d-sm-block">Forum Feed</h5>
                    <button class="btn btn-primary shadow-sm fw-bold px-4" data-toggle="modal" data-target="#ModalAdd">
                        <i class="fas fa-plus-circle mr-2"></i> START NEW TOPIC
                    </button>
                </div>

                
                <?php $__empty_1 = true; $__currentLoopData = $dataset; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php 
                        $id_disc_enc = Encrypter::encrypt($data['id_discussion']);
                        $url = router('student.discussion.join', ['id' => $id_enc, 'discussion_id' => $id_disc_enc]);
                        $statusClass = (strtolower($data['discussion_status']) == 'open') ? 'status-open' : 'status-closed';
                    ?>
                    
                    <div class="discussion-card shadow-sm">
                        
                        <a href="<?php echo e($url); ?>" class="card-link-overlay"></a>
                        
                        <div class="status-sidebar <?php echo e($statusClass); ?>"></div>
                        <div class="card-content">
                            <div class="meta-text">
                                <span class="badge badge-count py-1 px-2" style="background: #f1f5f9; color: #475569; font-size: 10px;"><?php echo e(strtoupper($data['discussion_status'])); ?></span>
                                <?php if($data['discussion_tag']): ?>
                                    <span class="text-primary fw-bold small"><i class="fas fa-tag mr-1"></i><?php echo e(strtoupper($data['discussion_tag'])); ?></span>
                                <?php endif; ?>
                                <span>Posted by <b><?php echo e($data['user_name']); ?></b></span>
                                <span>•</span> 
                                <span><?php echo e(time_elapsed_string($data['timestamp_add'])); ?></span>
                            </div>
                            
                            <span class="post-title"><?php echo e($data['discussion_title'] ?: 'Inquiry'); ?></span>
                            <p class="topic-preview"><?php echo e($data['discussion_topic']); ?></p>

                            <div class="post-footer card-interactive-elements">
                                <div class="footer-item"><i class="far fa-comments"></i> Join Conversation</div>
                                <?php if($data['user_id'] == $_SESSION['nim']): ?>
                                    <button class="footer-item text-warning" data-toggle="modal" data-target="#ModalEdit<?php echo e($i); ?>">
                                        <i class="fas fa-edit"></i> Edit Topic
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    
                    <div id="ModalEdit<?php echo e($i); ?>" class="modal fade" tabindex="-1" style="z-index: 1060;">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                                <div class="modal-header bg-warning text-dark">
                                    <h6 class="modal-title fw-bold"><i class="fas fa-edit mr-2"></i>Edit Your Topic</h6>
                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                </div>
                                <form action="<?php echo e(router('student.discussion.update', ['id' => $id_enc])); ?>" method="POST">
                                    <?php echo '<input type="hidden" name="token" value="' . Session::get('token') . '" />'; ?>
                                    <input type="hidden" name="id_discussion" value="<?php echo e($data['id_discussion']); ?>">
                                    <div class="modal-body py-4">
                                        <div class="form-group mb-3">
                                            <label class="fw-bold text-dark small">TITLE</label>
                                            <input type="text" name="discussion_title" class="form-control" value="<?php echo e($data['discussion_title']); ?>" required>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="fw-bold text-dark small">CATEGORY</label>
                                                    <select name="discussion_tag" class="form-control">
                                                        <?php $__currentLoopData = ['General', 'Question', 'Progress', 'Urgent']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($tag); ?>" <?php echo e($data['discussion_tag'] == $tag ? 'selected' : ''); ?>><?php echo e($tag); ?></option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="fw-bold text-dark small">STATUS</label>
                                                    <select name="discussion_status" class="form-control">
                                                        <option value="Open" <?php echo e($data['discussion_status'] == 'Open' ? 'selected' : ''); ?>>Active (Open)</option>
                                                        <option value="Closed" <?php echo e($data['discussion_status'] == 'Closed' ? 'selected' : ''); ?>>Resolved (Closed)</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="fw-bold text-dark small">DETAILED CONTENT</label>
                                            <textarea name="discussion_topic" class="form-control" rows="6" required><?php echo e($data['discussion_topic']); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light border-0">
                                        <button type="button" class="btn btn-link text-muted" data-dismiss="modal">Discard Changes</button>
                                        <button type="submit" class="btn btn-warning px-5 fw-bold shadow-sm">UPDATE POST</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="empty-state bg-white shadow-sm">
                        <i class="fas fa-comments-slash fa-4x text-light mb-3 d-block"></i>
                        <h5 class="fw-bold text-dark">No Discussions Found</h5>
                        <p class="text-muted small px-5">Your discussion feed is currently empty. Be the first to start a conversation.</p>
                        <button class="btn btn-primary px-5 mt-3 fw-bold" data-toggle="modal" data-target="#ModalAdd">CREATE POST</button>
                    </div>
                <?php endif; ?>

                
                <?php if($totalRows > 0): ?>
                <div class="jump-to-wrapper pb-5">
                    <select class="jump-select shadow-sm" onchange="window.location.href='?page=' + this.value">
                        <?php for($p = 1; $p <= $totalPages; $p++): ?>
                            <option value="<?php echo e($p); ?>" <?php echo e($currentPage == $p ? 'selected' : ''); ?>>Page <?php echo e($p); ?></option>
                        <?php endfor; ?>
                    </select>
                    <span class="entries-found"><?php echo e($totalRows); ?> records found</span>
                </div>
                <?php endif; ?>
            </div>

            
            <div class="col-lg-4 sidebar-container">
                <div class="info-card shadow-sm">
                    <div class="info-header"><i class="fas fa-info-circle mr-2"></i> Collaborative Space</div>
                    <div class="card-body p-0">
                        <div class="p-3 bg-light border-bottom small text-muted">A dedicated professional platform for internship progress and technical assistance.</div>
                        <ul class="participant-list">
                            <li><i class="fas fa-user-tie"></i> Industry Supervisors</li>
                            <li><i class="fas fa-chalkboard-teacher"></i> Faculty Lecturers</li>
                            <li><i class="fas fa-user-shield"></i> Internship Coordinators</li>
                            <li><i class="fas fa-building"></i> CDC Personnel</li>
                            <li><i class="fas fa-user-graduate"></i> Fellow Students</li>
                        </ul>
                    </div>
                </div>
                <div class="info-card shadow-sm p-4">
                    <h6 class="fw-bold small text-dark mb-3">COMMUNICATION ETIQUETTE</h6>
                    <ul class="small text-muted pl-3 mb-0" style="line-height: 1.8;">
                        <li>Ensure all topics are work-related.</li>
                        <li>Provide specific details when reporting technical issues.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="ModalAdd" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header bg-primary text-white">
                <h6 class="modal-title fw-bold"><i class="fas fa-plus mr-2"></i>New Discussion Topic</h6>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="<?php echo e(router('student.discussion.store', ['id' => $id_enc])); ?>" method="POST">
                <?php echo '<input type="hidden" name="token" value="' . Session::get('token') . '" />'; ?>
                <div class="modal-body py-4">
                    <div class="form-group mb-3">
                        <label class="fw-bold text-dark small">SUBJECT TITLE <span class="text-danger">*</span></label>
                        <input type="text" name="discussion_title" class="form-control shadow-none" placeholder="e.g. Server configuration issue" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="fw-bold text-dark small">CATEGORY</label>
                        <select name="discussion_tag" class="form-control shadow-none">
                            <option value="General">General Discussion</option>
                            <option value="Question">Technical Inquiry</option>
                            <option value="Progress">Milestone Progress</option>
                            <option value="Urgent">Administrative Issue</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="fw-bold text-dark small">DESCRIPTION <span class="text-danger">*</span></label>
                        <textarea name="discussion_topic" class="form-control shadow-none" rows="5" placeholder="Details about your inquiry..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-link text-muted" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-5 fw-bold shadow">SUBMIT POST</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('templates.student', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/student/my_internship/discussion/index.blade.php ENDPATH**/ ?>