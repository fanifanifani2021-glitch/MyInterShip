

<?php $__env->startPush('head-scripts'); ?>
<style>
    .page-inner { background: #f8f9fa; min-height: 100vh; padding: 30px 20px !important; }

    .card { border-radius: 12px !important; border: none; transition: transform 0.2s; }
    
    /* Header Section */
    .feedback-header { background: linear-gradient(135deg, #1572e8 0%, #0056b3 100%); border-radius: 12px; }
    
    /* Assessment Table */
    .table-assessment thead th { 
        background: #f1f5f9; color: #475569; font-weight: 800; 
        font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px;
        border: none; padding: 15px;
    }
    .table-assessment tbody td { vertical-align: middle; padding: 18px 15px; border-bottom: 1px solid #f1f5f9; }
    
    /* Custom Radio Styling */
    .assessment-option { 
        cursor: pointer; padding: 10px; border-radius: 8px; 
        transition: 0.2s; display: block; text-align: center;
    }
    .assessment-option:hover { background: #eff6ff; }
    .custom-control-input:checked ~ .custom-control-label::before { background-color: #1572e8 !important; border-color: #1572e8 !important; }

    /* Narrative Cards */
    .label-sharp { font-weight: 800; color: #1e293b; font-size: 0.85rem; text-transform: uppercase; margin-bottom: 10px; display: block; }
    .textarea-feedback { border: 1px solid #e2e8f0 !important; border-radius: 10px !important; padding: 15px !important; line-height: 1.6; }
    .textarea-feedback:focus { border-color: #1572e8 !important; box-shadow: 0 0 0 3px rgba(21, 114, 232, 0.1) !important; }
    
    @media (max-width: 768px) {
        .page-header { display: block !important; }
        .breadcrumbs { padding-left: 0; margin-top: 10px; overflow-x: auto; white-space: nowrap; }
    }
</style>

<style>
    /* Styling Header Tabel yang Rapi */
    .table-assessment thead th.mark-header {
        vertical-align: middle;
        padding: 10px 5px;
        min-width: 90px;
    }
    .mark-number {
        display: block;
        font-size: 1.2rem;
        font-weight: 800;
        line-height: 1;
    }
    .mark-label {
        display: block;
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        margin-top: 4px;
        opacity: 0.8;
    }

    /* Visual Indikator "Read Only" */
    .form-locked-notice {
        background: #fff9db;
        border-left: 4px solid #fcc419;
        color: #856404;
        padding: 12px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        font-weight: 600;
        transition: 0.3s;
    }
    
    /* Efek pada Textarea saat terkunci */
    .textarea-feedback:disabled {
        background-color: #f8f9fa !important;
        color: #adb5bd;
        cursor: not-allowed;
    }
</style>

<style>
    /* Reset sel agar label bisa memenuhi area */
    .table-assessment tbody td.mark-cell {
        padding: 0 !important;
        vertical-align: middle;
        position: relative;
    }

    /* Label yang menutupi seluruh sel TD */
    .assessment-label {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 60px; /* Atur tinggi sel agar lebih lega */
        margin-bottom: 0;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    /* Efek hover saat mouse di atas kotak sel */
    .assessment-label:hover {
        background-color: #f1f5f9;
    }

    /* Ukuran Radio Button yang lebih besar agar mudah dibidik */
    .custom-radio-lg .custom-control-label::before,
    .custom-radio-lg .custom-control-label::after {
        width: 1.5rem;
        height: 1.5rem;
        top: -0.25rem;
        left: -0.75rem;
    }

    /* Indikator visual saat sel terpilih */
    input[type="radio"]:checked + .assessment-label {
        background-color: #e1effe;
    }
    
    /* Tambahan agar saat "Locked", radio button terlihat pudar */
    input[type="radio"]:disabled + .assessment-label {
        cursor: not-allowed;
        opacity: 0.6;
    }
</style>

<style>
    /* 1. Warna Dasar Bulatan (Saat Tidak Terpilih) */
    .assessment-label .custom-control-label::before {
        border: 2px solid #adb5bd !important; /* Garis pinggir abu-abu lebih tegas */
        background-color: #fff !important;
        box-shadow: none !important;
        width: 1.25rem;
        height: 1.25rem;
        top: 0.15rem;
    }

    /* 2. Efek "Fill" Bulatan (Saat Terpilih) */
    input[type="radio"]:checked + .assessment-label .custom-control-label::before {
        border-color: #1572e8 !important;
        background-color: #1572e8 !important; /* Mengisi seluruh lingkaran dengan biru */
    }

    /* 3. Titik Putih di Tengah (Sebagai Indikator) */
    input[type="radio"]:checked + .assessment-label .custom-control-label::after {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e") !important;
        width: 1.25rem;
        height: 1.25rem;
        top: 0.15rem;
        transform: scale(0.6); /* Mengecilkan titik putih agar manis di tengah */
    }

    /* 4. Konsistensi Highlight Sel (Sesuai Turn Sebelumnya) */
    input[type="radio"]:checked + .assessment-label {
        background-color: #e1effe; /* Latar belakang sel biru muda */
        border-radius: 4px;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title fw-bold">Student Feedback</h4>
            <ul class="breadcrumbs">
                <li class="nav-home"><a href="<?php echo e(base_url()); ?>"><i class="flaticon-home text-primary"></i></a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="<?php echo e(router('student.internship')); ?>">My Internship</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="<?php echo e(router('student.internship.detail', ['id' => $id_enc])); ?>">Detail</a></li>
                
                <li class="separator"><i class="flaticon-right-arrow"></i></li>

                <li class="nav-item"><b>Feedback - Student</b></li>
            </ul>
        </div>


        
        <div id="edit-notice" class="form-locked-notice shadow-sm">
            <i class="fas fa-lock mr-3"></i>
            <div>
                <span class="d-block">Read-Only Mode</span>
                <small class="font-weight-normal">Please click the <b>"Edit Feedback"</b> button in the top right to make changes.</small>
            </div>
        </div>

        <form action="<?php echo e(router('student.feedback.store', ['id' => $id_enc])); ?>" method="POST" id="form_feedback">
            <?= csrf(); ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-primary mb-0"><i class="fas fa-tasks mr-2"></i>Competence Assessment</h6>
                    
                    
                    <div class="action-buttons">
                        <button type="button" class="btn btn-warning btn-sm px-4 fw-bold edit_btn" onclick="enable_feedback_form()">
                            <i class="fas fa-edit mr-1"></i> EDIT FEEDBACK
                        </button>
                        <div class="d-none save-cancel-group">
                            <button type="submit" class="btn btn-success btn-sm px-4 fw-bold shadow-sm"><i class="fas fa-save mr-1"></i> SAVE</button>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-assessment mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center" width="60">NO</th>
                                    <th class="text-left">EVALUATION CRITERIA</th>
                                    
                                    <th class="text-center mark-header"><span class="mark-number">4</span><span class="mark-label">Strongly Agree</span></th>
                                    <th class="text-center mark-header"><span class="mark-number">3</span><span class="mark-label">Agree</span></th>
                                    <th class="text-center mark-header"><span class="mark-number">2</span><span class="mark-label">Disagree</span></th>
                                    <th class="text-center mark-header"><span class="mark-number">1</span><span class="mark-label">Strongly Disagree</span></th>
                                </tr>
                            </thead>
                            <tbody>
    <?php 
        $criteria_items = [
            'posisi' => 'The internship position matches my competence and study program.',
            'ilmu_dari_poltek' => 'Knowledge gained from Polibatam is applicable to my tasks.',
            'ilmu_baru' => 'I gained significant new skills and professional knowledge.',
            'data_magang' => 'Ease of access to company data and learning resources.'
        ];
        $n = 1;
    ?>

    <?php $__currentLoopData = $criteria_items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $text): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <tr>
        <td class="text-center fw-bold text-muted" style="vertical-align: middle;"><?php echo e($n++); ?>.</td>
        <td class="fw-bold text-dark" style="vertical-align: middle; padding-left: 15px;">
            <?php echo e($text); ?>

        </td>
        
        
        <?php for($v = 4; $v >= 1; $v--): ?>
        <td class="mark-cell">
            <input type="radio" 
                   id="<?php echo e($key.$v); ?>" 
                   name="<?php echo e($key); ?>" 
                   value="<?php echo e($v); ?>" 
                   class="d-none" 
                   <?php echo e($row[$key] == $v ? 'checked' : ''); ?> 
                   disabled 
                   required>
            
            
            <label for="<?php echo e($key.$v); ?>" class="assessment-label">
                <div class="custom-control custom-radio custom-control-inline mr-0">
                    
                    <div class="custom-control-label p-0"></div>
                </div>
            </label>
        </td>
        <?php endfor; ?>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <label class="label-sharp"><i class="fas fa-heart text-danger mr-2"></i>My Impression</label>
                            <textarea name="kesan" class="form-control textarea-feedback" rows="4" placeholder="How was your overall experience at this company?" disabled required><?php echo e($row['kesan'] == '-' ? '' : $row['kesan']); ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <label class="label-sharp"><i class="fas fa-exclamation-triangle text-warning mr-2"></i>Problems Encountered</label>
                            <textarea name="kendala" class="form-control textarea-feedback" rows="5" placeholder="Describe any technical or non-technical obstacles..." disabled required><?php echo e($row['kendala'] == '-' ? '' : $row['kendala']); ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <label class="label-sharp"><i class="fas fa-comment-dots text-info mr-2"></i>Feedback for Company</label>
                            <textarea name="masukan" class="form-control textarea-feedback" rows="5" placeholder="Suggestions for improvement..." disabled required><?php echo e($row['masukan'] == '-' ? '' : $row['masukan']); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="mb-5 text-right">
                <button type="button" class="btn btn-outline-info fw-bold shadow-sm" onclick="window.open('<?php echo e(router('student.feedback.download', ['id' => $id_enc])); ?>', '_blank')">
                    <i class="fas fa-download mr-2"></i> Download PDF Report
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('footer-scripts'); ?>
<script>
    function enable_feedback_form() {
        // 1. Sembunyikan Notice Banner
        $('#edit-notice').fadeOut(300);

        // 2. Aktifkan Input
        $('#form_feedback :input').prop('disabled', false);
        
        // 3. Toggle Button
        $('.edit_btn').addClass('d-none');
        $('.save-cancel-group').removeClass('d-none');

        // 4. Beri feedback visual pada textarea
        $('.textarea-feedback').css('background-color', '#fff');
        
        // 5. Scroll ke bagian input
        $('html, body').animate({
            scrollTop: $(".card").first().offset().top - 20
        }, 500);
    }
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('templates.student', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/student/my_internship/student_feedback/index.blade.php ENDPATH**/ ?>