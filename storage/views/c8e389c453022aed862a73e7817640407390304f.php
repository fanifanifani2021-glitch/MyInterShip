

<?php $__env->startPush('head-scripts'); ?>
<style>
    /* Upload Box Interactive */
    .pdf-upload-zone {
        border: 2px dashed #cbd5e1;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.25s ease;
    }
    .pdf-upload-zone:hover {
        border-color: #005b9f;
        background: #f1f5f9;
    }

    /* Preview Card Detail File */
    .pdf-preview-card {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 15px;
        background: #ffffff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
    }
    .pdf-icon-box {
        width: 50px;
        height: 50px;
        background: #fee2e2;
        color: #dc2626;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    .badge-info-file {
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 4px;
        font-weight: 600;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="container">
    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <h2 class="text-white pb-2 fw-bold">Student Identity</h2>
            <h5 class="text-white op-7 mb-2">Update your profile details and internship documents</h5>
        </div>
    </div>

    <div class="page-inner mt--5">
        <div class="row mt--2">
            <div class="col-md-12">
                <div class="card card-with-nav">
                    <?php echo $__env->make('student.identity._nav', ['activeTab' => 'identity'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                    <div class="card-body">
                        <div class="row">
                            
                            <div class="col-md-12 mb-4">
                                <h4 class="fw-bold text-primary mb-3"><i class="fas fa-user mr-1"></i> Personal Identity</h4>
                                <form action="<?php echo e(router('student.identity.save_identity')); ?>" method="POST">
                                    <?= csrf() ?>
                                    
                                    <div class="form-group p-0 mb-3">
                                        <label>Full Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control <?= show_error('name'); ?>" name="name" value="<?= old('name', $student_data['name']); ?>" placeholder="Enter Your Full Name" required>
                                        <?= form_error('name'); ?>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-6 mb-3">
                                            <label>NIM</label>
                                            <input type="text" class="form-control" value="<?= $student_data['nim']; ?>" disabled readonly>
                                        </div>
                                        <div class="form-group col-md-6 mb-3">
                                            <label>Program Study</label>
                                            <select class="form-control" disabled readonly>
                                                <?php $__currentLoopData = $prodi_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prodi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?= $prodi['kode_prodi']; ?>" <?= ($student_data['program_study'] == $prodi['kode_prodi']) ? 'selected' : ''; ?>>
                                                        <?= $prodi['jenjang'] . ' ' . $prodi['prodi']; ?>
                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-6 mb-3">
                                            <label>E-Mail <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control <?= show_error('email'); ?>" name="email" value="<?= old('email', $student_data['email']); ?>" placeholder="Enter Your E-Mail" required>
                                            <?= form_error('email'); ?>
                                        </div>
                                        <div class="form-group col-md-6 mb-3">
                                            <label>Other E-Mail</label>
                                            <input type="email" class="form-control <?= show_error('other_email'); ?>" name="other_email" value="<?= old('other_email', $student_data['other_email']); ?>" placeholder="Enter Your Other E-Mail">
                                            <?= form_error('other_email'); ?>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-6 mb-3">
                                            <label>WhatsApp Number <span class="text-danger">*</span></label>
                                            <input type="tel" class="form-control <?= show_error('no_whatsapp'); ?>" name="no_whatsapp" value="<?= old('no_whatsapp', $student_data['no_whatsapp']); ?>" placeholder="Enter Whatsapp Number" required>
                                            <?= form_error('no_whatsapp'); ?>
                                        </div>
                                        <div class="form-group col-md-6 mb-3">
                                            <label>Phone Number <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control <?= show_error('phone'); ?>" name="phone" value="<?= old('phone', $student_data['phone']); ?>" placeholder="Enter Phone Number" required>
                                            <?= form_error('phone'); ?>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-success fw-bold"><i class="fas fa-save mr-1"></i> Save Identity</button>
                                </form>
                            </div>

                            <div class="col-md-12"><hr></div>

                            
                            <div class="col-md-12 mt-2">
                                <h4 class="fw-bold text-primary mb-3"><i class="fas fa-file-alt mr-1"></i> Internship Documents</h4>
                                <form action="<?php echo e(router('student.identity.save_document')); ?>" method="POST" enctype="multipart/form-data">
                                    <?= csrf() ?>
                                    
                                    <div class="form-group p-0 mb-4">
                                        <div class="d-flex justify-content-between">
                                            <label class="fw-bold">Talenthub CV Link <span class="text-danger">*</span></label>
                                            <a href="https://talenthub.polibatam.ac.id" target="_blank" class="small"><i class="fas fa-external-link-alt mr-1"></i> Visit Talenthub</a>
                                        </div>
                                        <input type="text" class="form-control <?= show_error('talenthub_cv'); ?>" name="talenthub_cv" value="<?= old('talenthub_cv', $profile_data['talenthub_cv'] ?? ''); ?>" placeholder="Enter Your Talenthub CV Link" required>
                                        <?= form_error('talenthub_cv'); ?>
                                    </div>

                                    
                                    <div class="form-group p-0 mb-4">
                                        <label class="fw-bold">Academic Transcript (PDF Document Only) <span class="text-danger">*</span></label>
                                        
                                        <?php if(!empty($profile_data['academic_transcript'])): ?>
                                            <div class="mb-3 p-2 border rounded bg-light d-flex align-items-center justify-content-between">
                                                <span><i class="fas fa-file-pdf text-danger mr-2"></i> Current File: <strong><?= basename($profile_data['academic_transcript']); ?></strong></span>
                                                <a href="<?= $profile_data['academic_transcript']; ?>" target="_blank" class="btn btn-xs btn-outline-info">
                                                    <i class="fas fa-eye mr-1"></i> View Current Transcript
                                                </a>
                                            </div>
                                        <?php endif; ?>

                                        
                                        <input type="file" accept="application/pdf" id="academic_transcript_input" name="academic_transcript" style="display: none;" onchange="validateAndPreviewPdf(this)">

                                        
                                        <div class="pdf-upload-zone" id="pdf-upload-zone" onclick="$('#academic_transcript_input').click()">
                                            <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                                            <h6 class="fw-bold text-dark mb-1">Click to Upload Academic Transcript (PDF)</h6>
                                            <p class="text-muted small mb-0">Supported format: Real PDF Document | Maximum Size: 2MB</p>
                                        </div>

                                        
                                        <div class="pdf-preview-card mt-3" id="pdf-preview-card" style="display: none;">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center">
                                                    <div class="pdf-icon-box mr-3">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="fw-bold text-dark mb-1" id="pdf-file-name">file_name.pdf</h6>
                                                        <div>
                                                            <span class="badge badge-secondary badge-info-file mr-1" id="pdf-file-size">Size: 0 MB</span>
                                                            <span class="badge badge-success badge-info-file" id="pdf-file-mime">Type: application/pdf</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <button type="button" class="btn btn-sm btn-outline-info mr-2" onclick="$('#academic_transcript_input').click()">
                                                        <i class="fas fa-sync-alt mr-1"></i> Change
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeSelectedPdf()">
                                                        <i class="fas fa-trash-alt mr-1"></i> Remove
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <?= form_error('academic_transcript'); ?>
                                    </div>

                                    <button type="submit" class="btn btn-success fw-bold"><i class="fas fa-save mr-1"></i> Update Documents</button>
                                </form>
                            </div>
                        </div>
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
    function validateAndPreviewPdf(input) {
        if (!input.files || !input.files[0]) return;

        let file = input.files[0];
        const maxSizeBytes = 2 * 1024 * 1024; // 2MB

        // 1. Validasi Ukuran File (Maksimal 2MB)
        if (file.size > maxSizeBytes) {
            Swal.fire({
                icon: 'error',
                title: 'File Too Large',
                text: `File "${file.name}" exceeds the maximum limit of 2MB (${formatBytes(file.size)}).`,
                confirmButtonColor: '#005b9f'
            });
            removeSelectedPdf();
            return;
        }

        // 2. Validasi Extension dasar
        if (!file.name.toLowerCase().endsWith('.pdf')) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Extension',
                text: 'The selected file must have a .pdf extension.',
                confirmButtonColor: '#005b9f'
            });
            removeSelectedPdf();
            return;
        }

        // 3. 🛡️ VALIDASI DEEP BINARY (MAGIC BYTES CHECK)
        // Membaca 4 byte pertama file: PDF Asli SELALU diawali dengan ASCII "%PDF" (hex: 0x25 0x50 0x44 0x46)
        let reader = new FileReader();
        reader.onloadend = function(e) {
            let arr = (new Uint8Array(e.target.result)).subarray(0, 4);
            let header = "";
            for (let i = 0; i < arr.length; i++) {
                header += arr[i].toString(16);
            }

            // PDF Magic Number Hex = "25504446" (%PDF)
            if (header !== "25504446") {
                Swal.fire({
                    icon: 'error',
                    title: 'Fake or Renamed PDF Detected',
                    text: `File "${file.name}" is not a real PDF document (e.g., PNG/JPG renamed to .pdf).`,
                    confirmButtonColor: '#005b9f'
                });
                removeSelectedPdf();
                return;
            }

            // Jika Lolos Validasi Binary PDF: Tampilkan Info Detail di UI
            $('#pdf-file-name').text(file.name);
            $('#pdf-file-size').text('Size: ' + formatBytes(file.size));
            $('#pdf-file-mime').text('Type: Real PDF Document (%PDF)');

            $('#pdf-upload-zone').hide();
            $('#pdf-preview-card').fadeIn();
        };

        reader.readAsArrayBuffer(file.slice(0, 4));
    }

    function removeSelectedPdf() {
        $('#academic_transcript_input').val('');
        $('#pdf-preview-card').hide();
        $('#pdf-upload-zone').fadeIn();
    }

    function formatBytes(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('templates.student', ['title' => 'Student Identity'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/student/identity/index.blade.php ENDPATH**/ ?>