
<?php $__env->startPush('head-scripts'); ?>
    <!-- Select2 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Styling Custom agar Select2 "Masuk" ke Tema KaiAdmin */
        .select2-container--default .select2-selection--single {
            border: 1px solid #ebedf2 !important;
            height: 45px !important;
            border-radius: 8px !important;
            display: flex !important;
            align-items: center !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 45px !important;
            font-weight: 600;
            color: #495057 !important;
            padding-left: 15px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 45px !important;
        }
        .select2-dropdown {
            border: 1px solid #ebedf2 !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
        }

        /* Profile Box Styling */
        .student-info-box {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            border: 1px solid #ebedf2;
        }
        .student-avatar-small {
            width: 60px; height: 60px;
            border-radius: 8px;
            background: #1572e8;
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 24px;
        }
        .info-item label {
            display: block; font-size: 10px; font-weight: 800;
            text-transform: uppercase; color: #8d9498;
            margin-bottom: 2px; letter-spacing: 0.5px;
        }
        .info-item span { font-weight: 700; color: #2a2b3d; font-size: 14px; }

        .form-label-custom { font-weight: 700; font-size: 12px; color: #495057; margin-bottom: 8px; text-transform: uppercase; }
        .form-label-custom span { color: #f25961; }
        .form-control-custom { border: 1px solid #ebedf2 !important; border-radius: 8px !important; height: 45px !important; font-weight: 600; }
        .form-control-custom:focus { border-color: #1572e8 !important; box-shadow: 0 0 0 3px rgba(21, 114, 232, 0.1) !important; }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="container">
    <div class="page-inner">
        
        <div class="page-header mb-4">
            <h4 class="page-title">Internship Registration</h4>
            <ul class="breadcrumbs">
                <li class="nav-home"><a href="<?php echo e(router('dashboard')); ?>"><i class="flaticon-home"></i></a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="<?php echo e(router('lecturer/my/student.index')); ?>">My Supervised Students</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="<?php echo e(router('lecturer/my/student.detail', ['nim' => $nim])); ?>">Detail</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><b>Add New Record</b></li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card card-full">
                    <form action="<?php echo e(router('lecturer/my/student.internship.store')); ?>" method="POST">
                        <?php echo csrf(); ?>

                        <input type="hidden" name="nim" value="<?php echo e($nim); ?>">

                        <div class="card-body p-4 p-lg-5">
                            
                            <div class="student-info-box shadow-sm mb-4">
                                <div class="row align-items-center">
                                    <!-- Avatar/Icon -->
                                    <div class="col-auto">
                                        <div class="student-avatar-small">
                                            <i class="fas fa-user-graduate"></i>
                                        </div>
                                    </div>

                                    <!-- Nama Mahasiswa -->
                                    <div class="col border-right">
                                        <div class="info-item">
                                            <label>Full Name</label>
                                            <span class="text-uppercase"><?php echo e($student['name']); ?></span>
                                        </div>
                                    </div>

                                    <!-- NIM -->
                                    <div class="col-auto px-4 border-right">
                                        <div class="info-item">
                                            <label>Student ID (NIM)</label>
                                            <span><?php echo e($student['nim']); ?></span>
                                        </div>
                                    </div>

                                    <!-- Detail Prodi & Jurusan -->
                                    <div class="col-md-5 px-4">
                                        <div class="row">
                                            <div class="col-12 mb-2">
                                                <div class="info-item">
                                                    <label>Study Program</label>
                                                    <span><?php echo e($student['jenjang']); ?> <?php echo e($student['prodi_name']); ?> (<?php echo e($student['kode_prodi']); ?>)</span>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="info-item">
                                                    <label>Major / Department</label>
                                                    <span><?php echo e($student['major'] ?? 'Informatics Engineering'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h5 class="fw-bold text-dark mb-4"><i class="fas fa-edit mr-2 text-primary"></i> Internship Details</h5>
                            
                            <div class="row">
                                
                                <div class="col-md-12 mb-4">
                                    <label class="form-label-custom">Target Company <span>*</span></label>
                                    <select name="id_company" id="company-select" class="form-control" required>
                                        <option value="">-- Start typing Company Name --</option>
                                        <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $co): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($co['id_company']); ?>" <?php if(old('id_company') == $co['id_company']): ?> selected <?php endif; ?>><?php echo e($co['name']); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                
                                <div class="col-md-12 mb-4">
                                    <label class="form-label-custom">Internship Position / Role <span>*</span></label>
                                    <input type="text" name="internship_position" class="form-control form-control-custom" 
                                    value="<?php echo e(old('internship_position')); ?>"
                                    placeholder="e.g. Software Engineer, Quality Assurance, etc." required>
                                </div>

                                
                                <div class="col-md-6 mb-4">
                                    <label class="form-label-custom">Start Date <span>*</span></label>
                                    <input type="date" name="start_date" id="start_date" value="<?php echo e(old('start_date')); ?>" class="form-control form-control-custom" required>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label-custom">End Date (Estimation) <span>*</span></label>
                                    <input type="date" name="end_date" id="end_date" class="form-control form-control-custom" required>
                                </div>

                                
                                <div class="col-md-12 mb-4">
                                    <label class="form-label-custom">Period (Duration in Months) <span>*</span></label>
                                    
                                    <input type="number" name="internship_period" id="internship_period" value="<?php echo e(old('internship_period')); ?>" class="form-control form-control-custom" readonly placeholder="Calculated automatically">
                                </div>
                            </div>

                            
                            <div class="d-flex align-items-center mt-2 p-3" style="background: #fff8e1; border-radius: 8px; border: 1px solid #ffe082;">
                                <i class="fas fa-exclamation-triangle text-warning mr-3" style="font-size: 20px;"></i>
                                <p class="text-dark mb-0 small fw-bold">
                                    Note: As a lecturer, this record will be automatically validated as <span class="text-success">Active (Yes)</span>.
                                </p>
                            </div>
                        </div>

                        <div class="card-footer bg-white border-top d-flex justify-content-end align-items-center p-4 px-lg-5">
                            <a href="<?php echo e(router('lecturer/my/student.detail', ['nim' => $nim])); ?>" class="text-muted fw-bold mr-4">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-save-custom shadow">
                                <i class="fas fa-check-circle mr-2"></i> Register Internship
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('#company-select').select2({
            theme: "default",
            width: '100%',
            placeholder: "-- Search Company Name --",
            allowClear: true
        });
    });

    // 2. Fungsi Hitung Durasi (Bulan)
            function updateDuration() {
                const startVal = $('#start_date').val();
                const endVal = $('#end_date').val();

                if (startVal && endVal) {
                    const startDate = new Date(startVal);
                    const endDate = new Date(endVal);

                    if (endDate > startDate) {
                        let months = (endDate.getFullYear() - startDate.getFullYear()) * 12;
                        months -= startDate.getMonth();
                        months += endDate.getMonth();
                        
                        // Validasi minimal 1 bulan jika ada selisih hari
                        const result = months <= 0 ? 1 : months;
                        $('#internship_period').val(result);
                    } else {
                        $('#internship_period').val('');
                    }
                }
            }

            // Trigger saat input tanggal berubah
            $('#start_date, #end_date').on('change', updateDuration);
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('templates.lecturer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/lecturer/internship_management/add_internship.blade.php ENDPATH**/ ?>