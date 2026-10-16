

<?php $__env->startPush('head-scripts'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title">Internship Offer Detail</h4>
            <ul class="breadcrumbs">
                <li class="nav-home"><a href="<?php echo e(base_url()); ?>"><i class="flaticon-home"></i></a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="<?php echo e(router('student/internship_offer.list')); ?>">Internship Offer</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a>Detail</a></li>
            </ul>
        </div>

        <div class="row">
            
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">General Information</h4>
                        <?php echo $offer['status'] == 'Open' 
                            ? '<span class="badge badge-success">Open</span>' 
                            : '<span class="badge badge-danger">Closed</span>'; ?>

                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Company</th>
                                <td><?php echo e($offer['company_name']); ?></td>
                            </tr>
                            <tr>
                                <th>Position</th>
                                <td><strong><?php echo e($offer['position']); ?></strong></td>
                            </tr>
                            <tr>
                                <th>Work Period</th>
                                <td><?php echo e(date('d M Y', strtotime($offer['start_date']))); ?> - <?php echo e(date('d M Y', strtotime($offer['end_date']))); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo e($offer['offer_type'] == 'Internship' ? 'Allowance' : 'Salary'); ?></th>
                                <td><?php echo e($offer['salary_range']); ?></td>
                            </tr>
                            <tr>
                                <th>City / Job Type</th>
                                <td><?php echo e($offer['city']); ?> (<?php echo e($offer['job_type']); ?>)</td>
                            </tr>
                            <tr>
                                <th width="30%">Full Address <span class="float-right">:</span></th>
                                <td width="70%">
                                    <span><?php echo e($offer['full_address'] ?? '-'); ?></span>
                                </td>
                            </tr>

                            <tr>
                                <th>Official Website <span class="float-right">:</span></th>
                                <td>
                                    <?php if($offer['formatted_website']): ?>
                                        <a href="<?php echo e($offer['formatted_website']); ?>" target="_blank" class="btn btn-xs btn-outline-primary shadow-sm">
                                            <i class="fas fa-globe mr-1"></i> Visit Website
                                        </a>
                                        <small class="text-muted d-block mt-1"><?php echo e($offer['website']); ?></small>
                                    <?php else: ?>
                                        <span class="text-muted small"><em>Website not provided or invalid</em></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>

                        <h5 class="mt-4 font-weight-bold">Description</h5>
                        <div class="p-3 bg-light rounded"><?php echo htmlspecialchars_decode($offer['description']); ?></div>

                        <h5 class="mt-4 font-weight-bold">Requirements</h5>
                        <div class="p-3 bg-light rounded border-left border-primary"><?php echo htmlspecialchars_decode($offer['requirement']); ?></div>
                    </div>
                </div>
            </div>

            
            <div class="col-md-4">
                <div class="card card-profile">
                    <div class="card-header" style="background-image: url('<?php echo e(base_url('assets/img/blogpost.jpg')); ?>')">
                        <div class="profile-picture">
                            <div class="avatar avatar-xl">
                                <img src="<?php echo e(base_url('assets/uploads/company/'.$offer['company_logo'])); ?>" alt="..." class="avatar-img rounded-circle border border-white">
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="user-profile text-center">
                            <div class="name"><?php echo e($offer['company_name']); ?></div>
                            <div class="job"><?php echo e($offer['employer_type']); ?></div>
                            <div class="desc">Posted <?php echo e(time_elapsed_string($offer['timestamp_add'])); ?></div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <?php if($offer['status'] == 'Open' && ($offer['offer_type'] == 'Internship' || $offer['offer_type'] == 'Magang')): ?>
                            <button onclick="applyOffer('<?php echo e(Encrypter::encrypt($offer['id_offer'])); ?>')" class="btn btn-primary btn-block">
                                <i class="fab fa-wpforms mr-2"></i> Apply Now
                            </button>
                        <?php else: ?>
                            <button class="btn btn-secondary btn-block" disabled>Application Closed</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-center py-4">
                        <h5 class="text-muted mb-2">Interest Gauge</h5>
                        <h2 class="font-weight-bold text-primary">
                            <i class="fas fa-users mr-2"></i> <?php echo e($applicantCount); ?> 
                            <span style="font-size: 1rem;" class="text-muted">Students have applied to this position</span>
                        </h2>
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
    $(document).ready(function() {
        $('#applicant-table').DataTable();
    });

    function applyOffer(encryptedId) {
        Swal.fire({
            title: 'Apply for this position?',
            text: "Ensure your profile and CV are up to date.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#005b9f',
            confirmButtonText: 'Yes, Apply!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'index.php?page=register_offer&id=' + encryptedId;
            }
        });
    }
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('templates.student', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/student/internship_offer/detail.blade.php ENDPATH**/ ?>