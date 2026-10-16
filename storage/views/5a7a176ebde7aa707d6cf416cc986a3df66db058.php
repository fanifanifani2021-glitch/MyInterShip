

<?php $__env->startPush('head-scripts'); ?>
<style>
    /* Styling Upload Zone & Preview Card */
    .avatar-upload-zone {
        border: 2px dashed #cbd5e1;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.25s ease;
    }
    .avatar-upload-zone:hover {
        border-color: #005b9f;
        background: #f1f5f9;
    }

    .avatar-preview-card {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 15px;
        background: #ffffff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
    }
    .avatar-thumb-box {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #cbd5e1;
        background: #f1f5f9;
    }
    .avatar-thumb-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
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
            <h2 class="text-white pb-2 fw-bold">Profile Picture</h2>
            <h5 class="text-white op-7 mb-2">Manage and update your avatar photo</h5>
        </div>
    </div>

    <div class="page-inner mt--5">
        <div class="row mt--2">
            <div class="col-md-12">
                <div class="card card-with-nav">
                    <?php echo $__env->make('student.identity._nav', ['activeTab' => 'avatar'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                    <div class="card-body">
                        <div class="row align-items-center mb-4">
                            <div class="col-md-auto text-center mb-3 mb-md-0">
                                <div class="avatar avatar-xxl" style="width: 130px; height: 130px;">
                                    <img src="<?php echo e(router('user.profile-picture')); ?>" alt="Profile" class="avatar-img rounded-circle border shadow-sm" id="avatar-current-img">
                                </div>
                            </div>
                            <div class="col-md">
                                <h5 class="fw-bold text-dark mb-1">Current Profile Picture</h5>
                                <p class="text-muted small mb-2">Upload a new photo to update your profile. Image will be compressed in browser and re-encoded securely on the server.</p>
                                
                                <?php if(!in_array($student_data['profile_picture'], ['profile.jpg', 'default.jpg', null])): ?>
                                    <button type="button" onclick="deleteAvatar()" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash-alt mr-1"></i> Remove Current Avatar</button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <hr>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h5 class="fw-bold text-primary mb-3"><i class="fas fa-camera mr-1"></i> Upload New Profile Picture</h5>
                                
                                <form id="form-avatar" action="<?php echo e(router('student.identity.save_avatar')); ?>" method="POST" enctype="multipart/form-data">
                                    <?= csrf() ?>

                                    
                                    <input type="file" name="new_profile" id="new_profile_input" accept="image/jpeg,image/png,image/webp" style="display: none;" onchange="validateAndCompressAvatar(this)">

                                    
                                    <div class="avatar-upload-zone" id="avatar-upload-zone" onclick="$('#new_profile_input').click()">
                                        <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-2"></i>
                                        <h6 class="fw-bold text-dark mb-1">Click to Select New Profile Picture</h6>
                                        <p class="text-muted small mb-0">Supported formats: JPG, PNG, WEBP | Max Size: 2MB | Automatic Compression Enabled</p>
                                    </div>

                                    
                                    <div class="avatar-preview-card mt-3" id="avatar-preview-card" style="display: none;">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-thumb-box mr-3">
                                                    <img id="avatar-new-thumb" src="#" alt="New Preview">
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold text-dark mb-1" id="avatar-file-name">filename.png</h6>
                                                    <div>
                                                        <span class="badge badge-secondary badge-info-file mr-1" id="avatar-orig-size">Original: 0 MB</span>
                                                        <i class="fas fa-arrow-right text-muted mx-1" style="font-size: 10px;"></i>
                                                        <span class="badge badge-success badge-info-file mr-1" id="avatar-comp-size">Compressed: 0 KB</span>
                                                        <span class="badge badge-info badge-info-file" id="avatar-mime-type">JPG Image</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <button type="button" class="btn btn-sm btn-outline-info mr-2" onclick="$('#new_profile_input').click()">
                                                    <i class="fas fa-sync-alt mr-1"></i> Change
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeSelectedAvatar()">
                                                    <i class="fas fa-trash-alt mr-1"></i> Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <?= form_error('new_profile'); ?>

                                    <div class="mt-4">
                                        <button type="submit" class="btn btn-success fw-bold" id="btn-save-avatar" disabled>
                                            <i class="fas fa-save mr-1"></i> Save New Avatar
                                        </button>
                                    </div>
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
    let _token = $('meta[name="csrf-token"]').attr('content');
    let compressedAvatarBlob = null; // Menyimpan Blob hasil kompresi Canvas

    async function validateAndCompressAvatar(input) {
        if (!input.files || !input.files[0]) return;

        let file = input.files[0];
        const allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];
        const maxSizeBytes = 2 * 1024 * 1024; // 2MB

        // 1. Validasi Ukuran File (Maksimal 2MB)
        if (file.size > maxSizeBytes) {
            Swal.fire({
                icon: 'error',
                title: 'File Too Large',
                text: `File "${file.name}" exceeds the 2MB limit (${formatBytes(file.size)}).`,
                confirmButtonColor: '#005b9f'
            });
            removeSelectedAvatar();
            return;
        }

        // 2. Validasi MIME Type Dasar
        if (!allowedMimeTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Image Format',
                text: 'Only JPG, PNG, and WEBP images are supported.',
                confirmButtonColor: '#005b9f'
            });
            removeSelectedAvatar();
            return;
        }

        // 3. 🛡️ DEEP BINARY VALIDATION (MAGIC BYTES CHECK)
        let isRealImage = await checkImageMagicBytes(file);
        if (!isRealImage) {
            Swal.fire({
                icon: 'error',
                title: 'Fake Image Detected',
                text: `File "${file.name}" is not a real image binary (e.g., PDF/Renamed File).`,
                confirmButtonColor: '#005b9f'
            });
            removeSelectedAvatar();
            return;
        }

        // 4. FRONTEND CANVAS COMPRESSION
        Swal.fire({
            title: 'Compressing Avatar...',
            text: 'Optimizing image size in browser...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });

        try {
            let blob = await compressAvatarCanvas(file);
            Swal.close();

            compressedAvatarBlob = blob;

            // Render Info & Thumbnail Preview
            $('#avatar-file-name').text(file.name);
            $('#avatar-orig-size').text('Original: ' + formatBytes(file.size));
            $('#avatar-comp-size').text('Compressed: ' + formatBytes(blob.size));
            $('#avatar-mime-type').text(file.type.split('/')[1].toUpperCase());
            
            let thumbUrl = URL.createObjectURL(blob);
            $('#avatar-new-thumb').attr('src', thumbUrl);

            $('#avatar-upload-zone').hide();
            $('#avatar-preview-card').fadeIn();
            $('#btn-save-avatar').prop('disabled', false);

        } catch (err) {
            Swal.fire('Error', 'Could not process image file structure.', 'error');
            removeSelectedAvatar();
        }
    }

    // CHECK MAGIC BYTES BINARY GAMBAR
    function checkImageMagicBytes(file) {
        return new Promise((resolve) => {
            let reader = new FileReader();
            reader.onloadend = function(e) {
                let arr = (new Uint8Array(e.target.result)).subarray(0, 4);
                let header = "";
                for (let i = 0; i < arr.length; i++) {
                    header += arr[i].toString(16).padStart(2, '0');
                }

                // Check Magic Bytes:
                // JPEG: ffd8ff
                // PNG:  89504e47
                // WEBP: 52494646 (RIFF)
                let isJpeg = header.startsWith("ffd8ff");
                let isPng  = header.startsWith("89504e47");
                let isWebp = header.startsWith("52494646");

                resolve(isJpeg || isPng || isWebp);
            };
            reader.readAsArrayBuffer(file.slice(0, 4));
        });
    }

    // COMPRESS CANVAS AVATAR (MAX WIDTH 800PX)
    function compressAvatarCanvas(file) {
        return new Promise((resolve, reject) => {
            let reader = new FileReader();
            reader.onload = function(e) {
                let img = new Image();
                img.src = e.target.result;
                img.onerror = () => reject(new Error("Corrupted Image"));

                img.onload = function() {
                    let maxWidth = 800;
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
                            reject(new Error("Canvas Export Failed"));
                        }
                    }, 'image/jpeg', 0.80);
                };
            };
            reader.readAsDataURL(file);
        });
    }

    function removeSelectedAvatar() {
        $('#new_profile_input').val('');
        compressedAvatarBlob = null;
        $('#avatar-preview-card').hide();
        $('#avatar-upload-zone').fadeIn();
        $('#btn-save-avatar').prop('disabled', true);
    }

    function deleteAvatar() {
        Swal.fire({
            title: 'Remove Avatar?',
            text: 'Are you sure you want to remove your current profile picture?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, remove it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?php echo e(router('student.identity.delete_avatar')); ?>",
                    type: "POST",
                    data: { _token: _token, _method: 'DELETE' },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            location.reload();
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    }
                });
            }
        });
    }

    function formatBytes(bytes, decimals = 1) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('templates.student', ['title' => 'Profile Picture'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/student/identity/avatar.blade.php ENDPATH**/ ?>