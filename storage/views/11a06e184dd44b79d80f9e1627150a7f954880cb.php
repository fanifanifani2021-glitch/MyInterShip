

<?php $__env->startPush('head-scripts'); ?>
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
            <h4 class="page-title fw-bold">Discussion Forum</h4>
            <ul class="breadcrumbs">
                <li class="nav-home"><a href="<?php echo e(router('dashboard')); ?>"><i class="flaticon-home"></i></a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="<?php echo e(router('lecturer/my/student.index')); ?>">My Supervised Students</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="<?php echo e(router('lecturer/my/student.detail', ['nim' => $internship['nim']])); ?>">Detail</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                
                <li class="nav-item"><a href="<?php echo e(router('lecturer/my/student.internship.detail', ['nim' => $internship['nim'], 'id_internship' => $internship['id_internship']])); ?>">Internship Detail</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><b>Discussion Feed</b></li>
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



        <div class="row">
            
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold text-dark m-0">Recent Topics</h5>
                    <button class="btn btn-primary btn-rounded-custom shadow-sm" data-toggle="modal" data-target="#ModalAdd">
                        <i class="fas fa-plus mr-2"></i> START NEW TOPIC
                    </button>
                </div>

                
                <?php $__empty_1 = true; $__currentLoopData = $dataset; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $id_disc_enc = Encrypter::encrypt($data->id_discussion);
                        $joinUrl = router('lecturer/my/student.internship.discussion.join', [
                            'nim'           => $nim, 
                            'id_internship' => $id_internship, 
                            'id_discussion' => $id_disc_enc
                        ]);
                        $deleteUrl = router('lecturer/my/student.internship.discussion.destroy', [
                            'nim'           => $nim, 
                            'id_internship' => $id_internship, 
                            'id_discussion' => $id_disc_enc
                        ]);
                    ?>
                    <div class="disc-card shadow-sm <?php echo e($data->isNew() ? 'border-left-primary' : ''); ?>">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    
                                    <?php if($data->isNew()): ?>
                                        <span class="badge badge-primary pulse-animation" style="font-size: 8px;">NEW TOPIC</span>
                                    <?php endif; ?>

                                    <span class="category-badge badge-<?php echo e(strtolower($data->discussion_tag ?? 'general')); ?>">
                                        <?php echo e($data->discussion_tag ?: 'General'); ?>

                                    </span>
                                    <span class="text-muted small">
                                        <i class="far fa-clock mr-1"></i> <?php echo e(time_elapsed_string($data->timestamp_add)); ?>

                                    </span>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    
                                    <?php if($data->user_id === Session::get('nim_nik_unit') && $data->user_type === 'Lecturer'): ?>
                                        <a href="javascript:void(0)" 
                                            class="text-warning mr-3" 
                                            onclick="openEditModal(this)"
                                            data-url="<?php echo e($data->update_url); ?>" 
                                            data-title="<?php echo e($data->discussion_title); ?>"
                                            data-tag="<?php echo e($data->discussion_tag); ?>"
                                            data-topic="<?php echo e(base64_encode($data->discussion_topic)); ?>">
                                                <i class="fas fa-edit"></i>
                                        </a>
                                                                                        
                                        <a href="javascript:void(0)" 
                                        class="text-danger mr-2" 
                                        onclick="confirmDeleteTopic('<?php echo e($deleteUrl); ?>')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    <?php endif; ?>

                                    <span class="badge badge-count <?php echo e(strtolower($data->discussion_status) == 'open' ? 'badge-success' : 'badge-secondary'); ?>" style="font-size: 9px;">
                                        <?php echo e(strtoupper($data->discussion_status)); ?>

                                    </span>
                                </div>
                            </div>
                            
                            
                            <h6 class="fw-bold text-dark mb-2 disc-title"><?php echo e($data->discussion_title ?: 'Inquiry'); ?></h6>
                            <p class="text-muted small mb-3"><?php echo e(Str::limit($data->discussion_topic, 160)); ?></p>

                            <div class="pt-3 border-top disc-footer">
                                <div class="footer-left">
                                    <div class="small text-muted">
                                        Started by <b class="text-primary"><?php echo e($data->user_name); ?></b> 
                                        <span class="user-type-badge ml-1"><?php echo e(strtoupper($data->user_type)); ?></span>
                                    </div>
                                </div>
                                
                                <div class="footer-right">
                                    <span class="text-muted small mr-3">
                                        <i class="far fa-comment-dots mr-1"></i> <?php echo e($data->commentsCount()); ?> Comments
                                    </span>
                                    <a href="<?php echo e($joinUrl); ?>" class="btn btn-link fw-bold text-primary p-0 text-decoration-none">
                                        JOIN CONVERSATION <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                    <div class="text-center py-5 bg-white shadow-sm" style="border-radius: 12px;">
                        <i class="fas fa-comments fa-3x text-muted opacity-25"></i>
                        <p class="mt-3 text-muted">No discussions found for this student.</p>
                        <button class="btn btn-primary btn-sm btn-rounded-custom" data-toggle="modal" data-target="#ModalAdd">Create First Topic</button>
                    </div>
                <?php endif; ?>

                
                <?php if($totalPages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo e($currentPage == $i ? 'active' : ''); ?>">
                                <a class="page-link shadow-sm border-0 mx-1" style="border-radius: 8px;" href="&page=<?php echo e($i); ?>"><?php echo e($i); ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>

            
            <div class="col-lg-4">
                <div class="sidebar-widget mb-4">
                    <div class="widget-header"><i class="fas fa-users mr-2 text-primary"></i> Conversation Access</div>
                    <div class="card-body p-0">
                        <div class="participant-item">
                            <div class="participant-icon"><i class="fas fa-user-tie"></i></div>
                            <div>
                                <div class="fw-bold small">Industry Supervisor & HRD</div>
                                <div class="text-muted" style="font-size: 11px;"><?php echo e($internship->company->name ?? 'Not Set'); ?></div>
                            </div>
                        </div>
                        <div class="participant-item">
                            <div class="participant-icon text-primary"><i class="fas fa-chalkboard-teacher"></i></div>
                            <div>
                                <div class="fw-bold small">Lecturer</div>
                                <div class="text-muted" style="font-size: 11px;">You (<?php echo e(Session::get('name')); ?>)</div>
                            </div>
                        </div>
                        <div class="participant-item">
                            <div class="participant-icon"><i class="fas fa-user-shield"></i></div>
                            <div>
                                <?php
                                    // Ambil data koordinator via model
                                    $coordinator = $internship->getCoordinator();
                                ?>

                                <div class="fw-bold small">Internship Coordinator</div>
                                <div class="text-muted" style="font-size: 11px;">
                                    <?php if($coordinator): ?>
                                        <?php echo e($coordinator->name); ?> <br>
                                        <span class="text-primary" style="font-size: 10px;"><?php echo e($student->programStudy->getFullName('en') ?? ''); ?></span>
                                    <?php else: ?>
                                        <span class="text-danger italic">Not Assigned for this Prodi</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="participant-item">
                            <div class="participant-icon"><i class="fas fa-university"></i></div>
                            <div>
                                <?php
                                    $upkpk = $internship->getUpkpk();
                                ?>

                                <div class="fw-bold small">Career Development Center</div>
                                <div class="text-muted" style="font-size: 11px;">
                                    <?php if($upkpk && $upkpk->campus): ?>
                                        <?php echo e($upkpk->name); ?> <br>
                                        <span class="text-primary fw-bold" style="font-size: 10px;">
                                            <?php echo e($upkpk->campus->nama_kampus); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted italic">CDC Personnel Not Assigned</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="p-4" style="background: #fff; border-radius: 12px; border-left: 4px solid #1572e8; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                    <h6 class="fw-bold text-dark small mb-2"><i class="fas fa-lightbulb mr-2 text-warning"></i> LECTURER TIP</h6>
                    <p class="text-muted mb-0 small" style="line-height: 1.6;">
                        Use this forum to provide immediate technical feedback or address issues raised by the industry supervisor without waiting for the monthly evaluation.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="ModalAdd" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h6 class="modal-title fw-bold"><i class="fas fa-plus-circle mr-2"></i>Create New Topic</h6>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="<?php echo e(router('lecturer/my/student.internship.discussion.store', ['nim' => $nim, 'id_internship' => $id_internship])); ?>" method="POST">
                <?php echo '<input type="hidden" name="token" value="' . Session::get('token') . '" />'; ?>
                <div class="modal-body p-4">
                    <div class="form-group form-group-default">
                        <label class="text-primary fw-bold">TOPIC SUBJECT</label>
                        <input type="text" name="discussion_title" class="form-control" placeholder="What would you like to discuss?" required>
                    </div>
                    <div class="form-group form-group-default mt-3">
                        <label class="text-primary fw-bold">CATEGORY</label>
                        <select name="discussion_tag" class="form-control">
                            <option value="General">General Information</option>
                            <option value="Question">Technical Inquiry</option>
                            <option value="Progress">Progress Update</option>
                            <option value="Urgent">Important / Urgent</option>
                        </select>
                    </div>
                    <div class="form-group form-group-default mt-3">
                        <label class="text-primary fw-bold">MESSAGE CONTENT</label>
                        <textarea name="discussion_topic" class="form-control" rows="5" placeholder="Provide detailed instructions or feedback..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-link text-muted fw-bold" data-dismiss="modal">Discard</button>
                    <button type="submit" class="btn btn-primary btn-rounded-custom px-5 shadow">POST TOPIC</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="ModalEdit" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius: 15px;">
            <div class="modal-header bg-warning text-white border-0">
                <h6 class="modal-title fw-bold"><i class="fas fa-edit mr-2"></i>Edit Topic</h6>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="formEditTopic" action="" method="POST">
                <?php echo '<input type="hidden" name="token" value="' . Session::get('token') . '" />'; ?>
                <!-- Method Spoofing untuk PUT/POST -->
                <input type="hidden" name="_method" value="PUT">
                
                <div class="modal-body p-4">
                    <div class="form-group form-group-default">
                        <label class="text-primary fw-bold">TOPIC SUBJECT</label>
                        <input type="text" id="edit_title" name="discussion_title" class="form-control" required>
                    </div>
                    <div class="form-group form-group-default mt-3">
                        <label class="text-primary fw-bold">CATEGORY</label>
                        <select id="edit_tag" name="discussion_tag" class="form-control">
                            <option value="General">General Information</option>
                            <option value="Question">Technical Inquiry</option>
                            <option value="Progress">Progress Update</option>
                            <option value="Urgent">Important / Urgent</option>
                        </select>
                    </div>
                    <div class="form-group form-group-default mt-3">
                        <label class="text-primary fw-bold">MESSAGE CONTENT</label>
                        <textarea id="edit_topic" name="discussion_topic" class="form-control" rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-link text-muted fw-bold" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning btn-rounded-custom px-5 shadow text-white">SAVE CHANGES</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('footer-scripts'); ?>
<script>
function confirmDeleteTopic(deleteUrl) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This topic and all its comments will be permanently deleted!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Buat form dinamis untuk mengirim request DELETE
            let form = document.createElement('form');
            form.action = deleteUrl;
            form.method = 'POST'; // Kita gunakan POST yang di-spoof menjadi DELETE
            
            form.innerHTML = `
                <?php echo '<input type="hidden" name="token" value="' . Session::get('token') . '" />'; ?>
                <input type="hidden" name="_method" value="DELETE">
            `;
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function openEditModal(el) {
    const updateUrl = $(el).data('url'); // Langsung ambil URL lengkap
    const title = $(el).data('title');
    const tag = $(el).data('tag');
    const encodedTopic = $(el).data('topic');

    const decodedTopic = atob(encodedTopic);

    $('#edit_title').val(title);
    $('#edit_tag').val(tag);
    $('#edit_topic').val(decodedTopic);

    // Set action form langsung dari data-url
    $('#formEditTopic').attr('action', updateUrl);

    $('#ModalEdit').modal('show');
}
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('templates.lecturer', ['routes' => 'intern_student_management'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/lecturer/internship_management/discussion/index.blade.php ENDPATH**/ ?>