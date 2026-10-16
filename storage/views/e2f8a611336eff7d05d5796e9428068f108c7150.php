

<?php $__env->startPush('head-scripts'); ?>
<style>
    /* Layout & Background */
    .page-inner { background: #f4f3ef; min-height: 100vh; padding: 25px 15px !important; }

    /* Konsistensi Radius 12px sesuai Header Card */
    .student-header-card, .topic-card, .comment-card, .reply-box-card {
        background: #fff;
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }

    /* Student Info Styling */
    .student-avatar {
        width: 55px; height: 55px;
        background: #1572e8;
        color: #fff;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px; font-weight: bold;
    }
    .info-label { font-size: 10px; color: #8d9498; text-transform: uppercase; font-weight: 800; margin-bottom: 2px; letter-spacing: 0.5px; }
    .info-value { font-size: 13px; color: #2a2b3d; font-weight: 700; line-height: 1.2; display: inline-block; }

    /* Topic Card - Full Width with Blue Accent */
    .topic-card {
        margin-bottom: 25px;
        border-left: 5px solid #1572e8;
        overflow: hidden;
    }
    .topic-header {
        padding: 20px 25px;
        border-bottom: 1px solid #f1f1f1;
    }
    .topic-body { padding: 25px; }
    .topic-title { font-size: 24px; font-weight: 700; color: #2a2b3d; margin-bottom: 15px; }

    /* Comments Section */
    .responses-title {
        font-size: 13px;
        font-weight: 800;
        color: #8d9498;
        text-transform: uppercase;
        margin-bottom: 15px;
        padding-left: 5px;
    }
    .comment-card {
        padding: 20px;
        margin-bottom: 15px;
    }
    .avatar-sm { width: 35px; height: 35px; border-radius: 6px; background: #eee; }

    /* Full Length Reply Box - Sticky at Bottom */
    .reply-box-wrapper {
        position: sticky;
        bottom: 20px;
        z-index: 1000;
        margin-top: 30px;
    }
    .reply-box-card {
        padding: 10px;
        display: flex;
        gap: 10px;
        border: 1px solid #ebedf2;
    }
    .reply-input {
        border: none !important;
        background: transparent;
        padding: 10px 15px;
        flex: 1;
        font-size: 14px;
    }
    .reply-input:focus { outline: none; }

    /* Button - Not Pill, Radius 8px */
    .btn-custom-send {
        background: #1572e8;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 0 20px;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-custom-send:hover { background: #1266d4; }
    .btn-custom-send:disabled { background: #cbd5e0; }

    /* Desktop Border Helper */
    @media (min-width: 768px) {
        .border-md-right { border-right: 1px solid #ebedf2 !important; }
        .border-md-left { border-left: 1px solid #ebedf2 !important; }
    }

    /* Mobile Adjustments */
    @media (max-width: 767.98px) {
        .info-group-mobile { margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #f8fafb; }
    }
</style>

<style>
    .border-left-primary {
        border-left: 4px solid #1572e8 !important;
    }
    
    .comment-card {
        transition: all 0.2s ease;
        padding: 18px 20px;
        margin-bottom: 12px;
    }

    .comment-card:hover {
        background-color: #fafafa;
    }

    /* Mempercantik tampilan teks komentar */
    .comment-text {
        color: #3f4254 !important;
    }
</style>
<style>
    /* Pastikan dropdown menu muncul di depan sticky box */
    .dropdown-menu {
        z-index: 1060 !important; /* Level di atas sticky footer dan modal-backdrop */
    }

    .reply-box-wrapper {
        z-index: 1050; /* Sedikit di bawah dropdown */
    }

    /* Agar card tidak memotong dropdown (overflow issue) */
    .comment-card {
        overflow: visible !important;
    }
</style>
<style>
    /* Background & Page Layout */
    .page-inner { background: #f4f3ef; min-height: 100vh; padding: 25px 15px !important; }

    /* Student Info Header Card */
    .student-header-card {
        background: #fff;
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        margin-bottom: 25px;
    }
    .student-avatar {
        width: 55px; height: 55px;
        background: #1572e8;
        color: #fff;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px; font-weight: bold;
    }
    .info-label { font-size: 10px; color: #8d9498; text-transform: uppercase; font-weight: 800; margin-bottom: 2px; letter-spacing: 0.5px; }
    .info-value { font-size: 13px; color: #2a2b3d; font-weight: 700; line-height: 1.2; }

    /* Discussion Card Styling */
    .disc-card { 
        background: #fff; border: none; border-radius: 12px; 
        margin-bottom: 15px; transition: all 0.3s ease; 
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    }
    .disc-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.06); }
    
    .category-badge {
        padding: 4px 10px; border-radius: 6px; font-size: 9px; font-weight: 800; text-transform: uppercase;
    }
    .badge-general { background: rgba(21, 114, 232, 0.1); color: #1572e8; }
    .badge-question { background: rgba(255, 173, 0, 0.1); color: #ffad00; }
    .badge-urgent { background: rgba(242, 89, 97, 0.1); color: #f25961; }

    /* Sidebar Participant Styling */
    .sidebar-widget { background: #fff; border-radius: 12px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.03); overflow: hidden; }
    .widget-header { padding: 15px 20px; border-bottom: 1px solid #f1f1f1; font-weight: 700; color: #1e293b; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
    .participant-item { padding: 12px 20px; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid #f8fafc; }
    .participant-icon { width: 32px; height: 32px; border-radius: 50%; background: #f0f2f5; display: flex; align-items: center; justify-content: center; font-size: 14px; color: #64748b; }

    .btn-rounded-custom { border-radius: 8px !important; font-weight: 600; }

    /* Custom Border Logic: Hanya muncul di desktop (MD ke atas) */
    @media (min-width: 768px) {
        .border-md-right {
            border-right: 1px solid #ebedf2 !important;
        }
    }

    /* Penyesuaian Spacing Mobile */
    @media (max-width: 767.98px) {
        .student-header-card .card-body {
            padding: 1.5rem !important;
        }
        
        .info-group-mobile {
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f8fafb; /* Garis pemisah halus antar item di mobile */
        }
        
        .info-group-mobile:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .student-avatar {
            width: 50px; height: 50px;
            font-size: 18px;
        }
    }

    /* Memastikan teks program studi tidak terpotong dan rapi */
    .info-value {
        word-break: break-word;
        display: block;
    }


    /* Menangani Breadcrumbs agar tidak overflow di mobile */
    @media (max-width: 576px) {
        .breadcrumbs {
            display: flex;
            overflow-x: auto;
            white-space: nowrap;
            padding-bottom: 5px;
        }
    }

    /* Perbaikan Responsivitas Kartu Diskusi */
    .disc-card .card-body {
        position: relative;
    }

    /* Stacking footer di mobile */
    .disc-footer {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap; /* Izinkan elemen turun jika tidak muat */
        gap: 15px;
    }

    @media (max-width: 767.98px) {
        .disc-footer {
            flex-direction: column;
            align-items: flex-start; /* Rata kiri di mobile */
            gap: 12px;
        }
        
        .disc-footer .footer-left, 
        .disc-footer .footer-right {
            width: 100%;
            justify-content: space-between;
            display: flex;
            align-items: center;
        }

        .disc-title {
            font-size: 15px !important;
        }
    }

    /* Badge user type agar lebih manis */
    .user-type-badge {
        font-size: 8px;
        padding: 2px 6px;
        vertical-align: middle;
        background: #f8f9fa;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        color: #64748b;
        font-weight: 800;
    }
    .border-md-left {
        border-left: none;
    }

    @media (min-width: 768px) {
        .border-md-left {
            border-left: 1px solid #ebedf2 !important;
        }
    }

    .info-group-mobile {
        transition: all 0.2s ease;
    }

    /* Memperhalus tampilan badge dalam header */
    .student-header-card .badge {
        letter-spacing: 0.5px;
        font-weight: 700;
        border-radius: 4px;
    }


    /* Menjaga info-value agar tidak memaksa baris baru (line break) */
    .info-value {
        display: inline-block; /* Berubah dari block ke inline-block */
        margin-bottom: 0 !important;
    }

    /* Menyesuaikan posisi vertical icon di dalam badge */
    .student-header-card .badge i {
        vertical-align: middle;
        margin-top: -1px;
    }

</style>

<style>
    .border-left-primary {
        border-left: 4px solid #1572e8 !important;
    }

    .pulse-animation {
        animation: pulse-red 2s infinite;
        box-shadow: 0 0 0 0 rgba(21, 114, 232, 0.7);
    }

    @keyframes  pulse-red {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(21, 114, 232, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(21, 114, 232, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(21, 114, 232, 0); }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="container">
    <div class="page-inner">
        
        
        <div class="page-header mb-4">
            <h4 class="page-title fw-bold">Discussion Thread</h4>

            <ul class="breadcrumbs">
                <li class="nav-home"><a href="<?php echo e(router('dashboard')); ?>"><i class="flaticon-home"></i></a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="<?php echo e(router('lecturer/my/student.index')); ?>">My Supervised Students</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="<?php echo e(router('lecturer/my/student.detail', ['nim' => $internship['nim']])); ?>">Detail</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                
                <li class="nav-item"><a href="<?php echo e(router('lecturer/my/student.internship.detail', ['nim' => $internship['nim'], 'id_internship' => $internship['id_internship']])); ?>">Internship Detail</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="<?php echo e(router('lecturer/my/student.internship.discussion', ['nim' => $internship['nim'], 'id_internship' => $internship['id_internship']])); ?>">Discussion Feed</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><b>Join Discussion</b></li>
            </ul>
        </div>

        
        <div class="student-header-card shadow-sm">
            <div class="card-body p-4">
                <div class="row align-items-center gy-4">
                    
                    <!-- Zona 1: Identitas Mahasiswa -->
                    <div class="col-12 col-md-3 border-md-right info-group-mobile">
                        <div class="d-flex align-items-center">
                            <div class="student-avatar mr-3 shadow-sm">
                                <?php echo e(substr($student->name, 0, 1)); ?>

                            </div>
                            <div>
                                <div class="info-label">Student Name</div>
                                <div class="info-value text-uppercase"><?php echo e($student->name); ?></div>
                                <div class="text-muted small fw-bold"><?php echo e($student->nim); ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Zona 2: Informasi Akademik -->
                    <div class="col-12 col-sm-6 col-md-2 border-md-right info-group-mobile">
                        <div class="info-label">Study Program</div>
                        <div class="info-value">
                            <?php if($student->programStudy): ?>
                                <?php echo e($student->programStudy->jenjang); ?> - <?php echo e($student->programStudy->study_program); ?>

                            <?php else: ?>
                                <?php echo e($student->program_study); ?>

                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Zona 3: Informasi Industri & Posisi Magang -->
                    <div class="col-12 col-sm-6 col-md-3 border-md-right info-group-mobile">
                        <div class="info-label">Internship Information</div>
                        <div class="info-value text-primary mb-1">
                            <i class="fas fa-briefcase mr-1"></i> <?php echo e($internship->internship_position ?: 'Position Not Set'); ?>

                        </div>
                        <div class="info-value small text-dark">
                            <i class="fas fa-building mr-1"></i> <?php echo e($internship->company->name); ?>

                        </div>
                    </div>

                    <!-- Zona 4: Timeline & Durasi -->
                    <div class="col-12 col-md-4 info-group-mobile">
                        <div class="info-label">Internship Period</div>
                        <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                            <div class="info-value mb-0">
                                <i class="far fa-calendar-alt mr-1 text-muted"></i> 
                                <?php echo e($internship->getDateRange('en')); ?>

                            </div>
                            
                            
                            <span class="badge badge-primary" style="font-size: 8px; padding: 3px 7px; line-height: 1;">
                                <i class="fas fa-hourglass-half mr-1" style="font-size: 7px;"></i> 
                                <?php echo e(strtoupper($internship->getDuration('en'))); ?>

                            </span>

                            
                            <span class="badge badge-<?php echo e($internship->getStatusColor()); ?> ml-md-auto" style="font-size: 8px; padding: 3px 7px; line-height: 1;">
                                <?php echo e(strtoupper($internship->getStatusLabel('en'))); ?>

                            </span>
                        </div>
                    </div>

                </div>

                
                <div class="mt-3 pt-3 border-top d-flex flex-wrap gap-4">
                    <div class="small">
                        <span class="info-label d-inline mr-2">Email:</span>
                        <a href="mailto:<?php echo e($student->email); ?>" class="text-primary fw-bold text-decoration-none small">
                            <i class="far fa-envelope mr-1"></i> <?php echo e($student->email ?? 'N/A'); ?>

                        </a>
                    </div>
                    <div class="small border-md-left pl-md-4">
                        <span class="info-label d-inline mr-2">WhatsApp:</span>
                        <span class="fw-bold small <?php echo e($student->no_whatsapp ? 'text-success' : 'text-muted'); ?>">
                            <i class="fab fa-whatsapp mr-1"></i> <?php echo e($student->no_whatsapp ?: 'Not Provided'); ?>

                        </span>
                    </div>
                </div>
            </div>
        </div>

        <hr />

        
        <div class="topic-card shadow-sm">
            <div class="topic-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm d-flex align-items-center justify-content-center bg-primary text-white mr-3 fw-bold">
                        <?php echo e(substr($topic->user_name, 0, 1)); ?>

                    </div>
                    <div>
                        <div class="fw-bold text-dark" style="font-size: 14px;"><?php echo e($topic->user_name); ?></div>
                        <div class="text-muted small"><?php echo e(strtoupper($topic->user_type)); ?> • <?php echo e($topic->getTimeAgo()); ?></div>
                    </div>
                </div>
                <span class="badge badge-light border text-primary" style="font-size: 10px; font-weight: 700;">
                    <?php echo e(strtoupper($topic->discussion_tag)); ?>

                </span>
            </div>
            <div class="topic-body">
                <h2 class="topic-title"><?php echo e($topic->discussion_title); ?></h2>
                <div class="text-muted" style="font-size: 15px; line-height: 1.8;">
                    <?php echo nl2br(e($topic->discussion_topic)); ?>

                </div>
            </div>
        </div>

        
        <div class="responses-title d-flex align-items-center justify-content-between">
            <div>
                <i class="fas fa-comments mr-2 text-primary"></i> 
                Responses (<?php echo e(count($comments)); ?>)
            </div>
            
            <div class="flex-grow-1 ml-3" style="height: 1px; background: #ebedf2;"></div>
        </div>

        <div id="comment-container" class="mt-3">
            <?php $__empty_1 = true; $__currentLoopData = $comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $isMe = ($comment['user_id'] == Session::get('nim_nik_unit'));
                ?>

                <?php
                    $id_comm_enc = Encrypter::encrypt($comment['id_comment']);
                    $deleteCommUrl = router('lecturer/my/student.internship.discussion.comment_destroy', [
                        'nim' => $nim, 
                        'id_internship' => $id_internship, 
                        'id_discussion' => Encrypter::encrypt($topic->id_discussion),
                        'id_comment' => $id_comm_enc
                    ]);
                ?>
                
                <div class="comment-card shadow-sm <?php echo e($isMe ? 'border-left-primary' : ''); ?>">
                    <div class="d-flex align-items-start">
                        <!-- Avatar Section -->
                        <div class="avatar-sm d-flex align-items-center justify-content-center <?php echo e($isMe ? 'bg-primary text-white' : 'bg-light text-muted'); ?> shadow-sm fw-bold mr-3" style="flex-shrink: 0; width: 40px; height: 40px; border-radius: 10px;">
                            <?php echo e(substr($comment['user_name'], 0, 1)); ?>

                        </div>

                        <!-- Content Section -->
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div>
                                    <span class="fw-bold text-dark" style="font-size: 14px;"><?php echo e($comment['user_name']); ?></span>
                                    <span class="user-type-badge ml-1" style="background: <?php echo e($isMe ? 'rgba(21, 114, 232, 0.1)' : '#f8f9fa'); ?>; color: <?php echo e($isMe ? '#1572e8' : '#64748b'); ?>; border: none;">
                                        <?php echo e(strtoupper($comment['user_type'])); ?>

                                    </span>
                                </div>
                                
                                <div class="d-flex align-items-center">
                                    <small class="text-muted mr-2" style="font-size: 11px;">
                                        <i class="far fa-clock mr-1"></i><?php echo e(time_elapsed_string($comment['timestamp_add'])); ?>

                                    </small>

                                    <?php if($isMe): ?>
                                        <div class="dropdown">
                                            <button class="btn btn-link btn-sm text-muted p-0" data-toggle="dropdown" aria-haspopup="true">
                                                <i class="fas fa-ellipsis-h"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right border-0 shadow-sm">
                                                <a class="dropdown-item small py-2" href="javascript:void(0)" 
                                                onclick="openEditCommentModal(this)"
                                                data-url="<?php echo e(router('lecturer/my/student.internship.discussion.comment_update', ['nim'=>$nim, 'id_internship'=>$id_internship, 'id_discussion'=>Encrypter::encrypt($topic->id_discussion), 'id_comment'=>$id_comm_enc])); ?>"
                                                data-comment="<?php echo e(base64_encode($comment['comment'])); ?>">
                                                    <i class="fas fa-edit mr-2 text-warning"></i> Edit Response
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item small py-2 text-danger" href="javascript:void(0)" onclick="destroyComment('<?php echo e($deleteCommUrl); ?>')">
                                                    <i class="fas fa-trash-alt mr-2"></i> Delete
                                                </a>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Text Comment -->
                            <div class="comment-text text-dark" style="font-size: 14.5px; line-height: 1.6; white-space: pre-wrap;"><?php echo e($comment['comment']); ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-5 bg-white shadow-sm mb-4" style="border-radius: 12px; border: 2px dashed #f1f1f1;">
                    <i class="fas fa-comment-dots fa-3x text-light mb-3"></i>
                    <p class="text-muted mb-0 fw-bold">No responses yet.</p>
                    <small class="text-muted">Be the first to share your thoughts in this discussion.</small>
                </div>
            <?php endif; ?>
        </div>

        
        <div class="reply-box-wrapper">
            <form action="<?php echo e(router('lecturer/my/student.internship.discussion.comment_store', ['nim'=>$nim, 'id_internship'=>$id_internship, 'id_discussion'=>Encrypter::encrypt($topic->id_discussion)])); ?>" method="POST">
                <?php echo '<input type="hidden" name="token" value="' . Session::get('token') . '" />'; ?> 
                
                <div class="reply-box-card shadow">
                    <input type="text" name="comment" class="reply-input" placeholder="Write a response..." autocomplete="off" required>
                    <button type="submit" class="btn-custom-send" id="btn-reply" <?php if(strtolower($topic->discussion_status) != 'open'): ?> disabled <?php endif; ?>>
                        <i class="fas fa-paper-plane mr-2"></i> SEND
                    </button>
                </div>
            </form>
            
            <?php if(strtolower($topic->discussion_status) != 'open'): ?>
                <div class="text-center mt-2">
                    <span class="badge badge-light border text-muted" style="font-size: 10px;">This conversation is closed and no longer accepting replies.</span>
                </div>
            <?php endif; ?>
        </div>


        <div id="ModalEditComment" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content border-0" style="border-radius: 12px;">
                    <div class="modal-header border-0 pb-0">
                        <h6 class="modal-title fw-bold"><i class="fas fa-edit mr-2 text-warning"></i>Edit Your Response</h6>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <form id="formEditComment" action="" method="POST">
                        <?php echo '<input type="hidden" name="token" value="' . Session::get('token') . '" />'; ?>
                        <input type="hidden" name="_method" value="PUT">
                        <div class="modal-body">
                            <div class="form-group p-0">
                                <textarea id="edit_comment_text" name="comment" class="form-control" rows="5" 
                                        style="border-radius: 8px; background: #f8fafc;" placeholder="Update your response..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-link text-muted fw-bold" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4 fw-bold" style="border-radius: 8px;">SAVE CHANGES</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('footer-scripts'); ?>
<script>
    $('#comment-form').on('submit', function(e) {
        e.preventDefault();
        const comment = $('#comment-input').val();
        const btn = $('#btn-reply');

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> SENDING...');

        $.ajax({
            url: "<?php echo e(router('lecturer/my/student.internship.discussion.comment_store', ['nim'=>$nim, 'id_internship'=>$id_internship, 'id_discussion'=>Encrypter::encrypt($topic->id_discussion)])); ?>",
            type: "POST",
            data: { comment: comment, _token: "<?php echo e(csrf_token()); ?>" },
            dataType: "json",
            success: function(res) {
                if(res.status === 'success') {
                    window.location.reload();
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-2"></i> SEND');
            }
        });
    });

    function destroyComment(deleteUrl) {
        Swal.fire({
            title: 'Delete Comment?',
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Buat form manual
                let form = document.createElement('form');
                form.action = deleteUrl;
                form.method = 'POST';
                form.innerHTML = `
                    <?php echo '<input type="hidden" name="token" value="' . Session::get('token') . '" />'; ?>
                    <input type="hidden" name="_method" value="DELETE">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function openEditCommentModal(el) {
        const url = $(el).data('url');
        const encodedComment = $(el).data('comment');
        
        // Decode Base64 (pake window.atob)
        const decodedComment = atob(encodedComment);
        
        // Masukkan data ke modal
        $('#edit_comment_text').val(decodedComment);
        $('#formEditComment').attr('action', url);
        
        // Munculkan Modal
        $('#ModalEditComment').modal('show');
    }
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('templates.lecturer', ['routes' => 'intern_student_management'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/lecturer/internship_management/discussion/detail.blade.php ENDPATH**/ ?>