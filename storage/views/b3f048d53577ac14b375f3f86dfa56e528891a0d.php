

<?php $__env->startPush('head-scripts'); ?>
<!-- CKEditor Script -->
<script src="./library/ckeditor/ckeditor.js"></script>

<style>
    .upload-zone {
        border: 2px dashed #cbd5e1;
        border-radius: 10px;
        padding: 25px;
        text-align: center;
        background: #f8fafc;
        transition: all 0.25s ease;
        cursor: pointer;
    }
    .upload-zone:hover {
        border-color: #005b9f;
        background: #f1f5f9;
    }
    .upload-action-btn { display: inline-flex; gap: 10px; margin-top: 10px; }
    .attachment-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }
    .preview-card-item {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px;
        background: #ffffff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    }
    .preview-image-box {
        width: 100%;
        height: 130px;
        border-radius: 6px;
        overflow: hidden;
        border: 1px solid #cbd5e1;
        margin-bottom: 8px;
    }
    .preview-image-box img { width: 100%; height: 100%; object-fit: cover; }
    .badge-size { font-size: 10px; padding: 3px 6px; border-radius: 4px; font-weight: 600; }
    .file-name-truncated {
        font-size: 12px; font-weight: 700; color: #1e293b;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="container">
    <div class="page-inner mt-2">
        <div class="page-header">
            <h4 class="page-title">Create Support Ticket</h4>
            <ul class="breadcrumbs">
                <li class="nav-home"><a href="<?php echo e(router('landing')); ?>"><i class="flaticon-home"></i></a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="<?php echo e(router('student.helpdesk.index')); ?>">Helpdesk</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="#">Create Ticket</a></li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title fw-bold text-primary">New Ticket Form</h4>
                    </div>

                    <form id="form-create-ticket" enctype="multipart/form-data">
                        <?= csrf() ?>
                        <div class="card-body">
                            <div class="row">
                                
                                <div class="form-group col-md-6 mb-3">
                                    <label>Issue Category <span class="text-danger">*</span></label>
                                    <select name="id_category" id="field-id_category" class="form-control" required>
                                        <option value="">-- Select Category --</option>
                                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($cat->id_category); ?>"><?php echo e($cat->category_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <div class="invalid-feedback" id="error-id_category"></div>
                                </div>

                                
                                <div class="form-group col-md-6 mb-3">
                                    <label>Priority Level <span class="text-danger">*</span></label>
                                    <select name="priority" id="field-priority" class="form-control" required>
                                        <option value="Low">Low (General Inquiry)</option>
                                        <option value="Medium" selected>Medium (Need Assistance)</option>
                                        <option value="High">High (Urgent)</option>
                                        <option value="Urgent">Urgent (Critical Error)</option>
                                    </select>
                                    <div class="invalid-feedback" id="error-priority"></div>
                                </div>
                            </div>

                            
                            <div class="form-group p-0 mb-3">
                                <label>Subject / Issue Title <span class="text-danger">*</span></label>
                                <input type="text" name="subject" id="field-subject" class="form-control" placeholder="e.g., Unable to submit internship logbook" required autocomplete="off">
                                <div class="invalid-feedback" id="error-subject"></div>
                            </div>

                            
                            <div class="form-group p-0 mb-4">
                                <label>Detailed Description <span class="text-danger">*</span></label>
                                <textarea name="description" id="field-description" class="form-control"></textarea>
                                <div class="invalid-feedback" id="error-description"></div>
                            </div>

                            
                            <div class="form-group p-0 mb-3">
                                <label class="fw-bold">Attach Image / Screenshot Proofs (Optional)</label>

                                <input type="file" id="input_file_gallery" accept="image/jpeg,image/png,image/webp" multiple style="display: none;" onchange="handleMultipleImageSelection(this)">
                                <input type="file" id="input_file_camera" accept="image/*" capture="environment" style="display: none;" onchange="handleMultipleImageSelection(this)">

                                <div class="upload-zone" id="upload-zone">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-2"></i>
                                    <h6 class="fw-bold text-dark mb-1">Upload Screenshots / Proof Images</h6>
                                    <p class="text-muted small mb-3">Supported formats: JPG, PNG, WEBP. Automatic Browser Compression Enabled.</p>

                                    <div class="upload-action-btn">
                                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" onclick="$('#input_file_gallery').click()">
                                            <i class="fas fa-images mr-1"></i> Choose Files
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" onclick="$('#input_file_camera').click()">
                                            <i class="fas fa-camera mr-1"></i> Take Photo
                                        </button>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-3" id="attachment-header-info" style="display: none !important;">
                                    <h6 class="fw-bold text-dark mb-0"><i class="fas fa-paperclip text-primary mr-1"></i> Attached Images (<span id="file-counter">0</span>)</h6>
                                    <button type="button" class="btn btn-xs btn-outline-primary" onclick="$('#input_file_gallery').click()">
                                        <i class="fas fa-plus mr-1"></i> Add More
                                    </button>
                                </div>

                                <div class="attachment-grid" id="attachment-grid"></div>
                            </div>

                        </div>

                        <div class="card-footer d-flex justify-content-start">
                            <a href="<?php echo e(router('student.helpdesk.index')); ?>" class="btn btn-light mr-2">
                                <i class="fas fa-times mr-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary fw-bold">
                                <i class="fas fa-paper-plane mr-1"></i> Submit Ticket
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('footer-scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let editorInstance;
    let compressedFilesList = [];
    let _token = $('meta[name="csrf-token"]').attr('content');

    $(document).ready(function() {
        if (typeof CKEDITOR !== 'undefined') {
            editorInstance = CKEDITOR.replace('field-description', {
                language: 'en',
                extraPlugins: 'editorplaceholder',
                editorplaceholder: 'Explain the details of the problem you experienced here...',
                removeButtons: 'PasteFromWord,Styles,Format,Font,FontSize',
                height: '18em',
            });
        }

        $('#form-create-ticket').on('submit', function(e) {
            e.preventDefault();

            if (editorInstance) {
                editorInstance.updateElement();
            }

            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');

            Swal.fire({
                title: 'Submitting ticket...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            let formData = new FormData(this);

            compressedFilesList.forEach((fileBlob) => {
                formData.append('image_proofs[]', fileBlob, fileBlob.name);
            });

            $.ajax({
                url: "<?php echo e(router('student.helpdesk.store')); ?>",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = "<?php echo e(router('student.helpdesk.index')); ?>";
                        });
                    } else if (res.status === 'validation_error') {
                        Swal.close();
                        $.each(res.errors, function(field, message) {
                            $('#field-' + field).addClass('is-invalid');
                            $('#error-' + field).text(message);
                        });
                    } else {
                        Swal.fire('Failed', res.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'An internal server error occurred.', 'error');
                }
            });
        });
    });

    async function handleMultipleImageSelection(input) {
        if (!input.files || input.files.length === 0) return;

        const allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];
        let rawFiles = Array.from(input.files);

        Swal.fire({
            title: 'Compressing Images...',
            text: `Processing ${rawFiles.length} file(s) in browser...`,
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });

        for (let file of rawFiles) {
            if (!allowedMimeTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File Skipped',
                    text: `File "${file.name}" is not a valid image.`,
                    confirmButtonColor: '#005b9f'
                });
                continue;
            }

            try {
                let compressedBlob = await compressSingleImage(file);
                compressedBlob.originalName = file.name;
                compressedBlob.originalSize = file.size;

                compressedFilesList.push(compressedBlob);
            } catch (err) {
                console.error("Error compressing file:", file.name, err);
            }
        }

        Swal.close();
        $('#input_file_gallery').val('');
        $('#input_file_camera').val('');
        renderPreviewGrid();
    }

    function compressSingleImage(file) {
        return new Promise((resolve, reject) => {
            let reader = new FileReader();
            reader.onload = function(e) {
                let img = new Image();
                img.src = e.target.result;
                img.onerror = () => reject(new Error("Corrupted Image"));

                img.onload = function() {
                    let maxWidth = 1200;
                    let width = img.width;
                    let height = img.height;

                    if (width > maxWidth) {
                        height = Math.round((height * maxWidth) / width);
                        width = maxWidth;
                    }

                    let canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    let ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    canvas.toBlob((blob) => {
                        if (blob) {
                            blob.name = file.name;
                            resolve(blob);
                        } else {
                            reject(new Error("Canvas export failed"));
                        }
                    }, 'image/jpeg', 0.70);
                };
            };
            reader.readAsDataURL(file);
        });
    }

    function renderPreviewGrid() {
        let gridContainer = $('#attachment-grid');
        gridContainer.empty();

        if (compressedFilesList.length === 0) {
            $('#attachment-header-info').attr('style', 'display: none !important;');
            $('#upload-zone').fadeIn();
            return;
        }

        $('#upload-zone').hide();
        $('#attachment-header-info').attr('style', 'display: flex !important;');
        $('#file-counter').text(compressedFilesList.length);

        compressedFilesList.forEach((fileItem, index) => {
            let imgUrl = URL.createObjectURL(fileItem);
            let origSize = formatBytes(fileItem.originalSize);
            let compSize = formatBytes(fileItem.size);

            let cardHtml = `
            <div class="preview-card-item">
                <div>
                    <div class="preview-image-box">
                        <img src="${imgUrl}" alt="Attachment">
                    </div>
                    <div class="file-name-truncated" title="${fileItem.originalName}">${fileItem.originalName}</div>
                    <div class="mt-2 mb-2">
                        <span class="badge badge-secondary badge-size">${origSize}</span>
                        <i class="fas fa-arrow-right text-muted mx-1" style="font-size: 8px;"></i>
                        <span class="badge badge-success badge-size">${compSize}</span>
                    </div>
                </div>
                <div class="text-right border-top pt-2 mt-2">
                    <button type="button" class="btn btn-xs btn-outline-danger btn-block" onclick="removeSingleImage(${index})">
                        <i class="fas fa-trash-alt mr-1"></i> Remove
                    </button>
                </div>
            </div>`;

            gridContainer.append(cardHtml);
        });
    }

    function removeSingleImage(index) {
        compressedFilesList.splice(index, 1);
        renderPreviewGrid();
    }

    function formatBytes(bytes, decimals = 1) {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('templates.student', ['title' => 'Create Support Ticket'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/student/helpdesk/create.blade.php ENDPATH**/ ?>