

<?php $__env->startPush('head-scripts'); ?>
<style>
    .page-inner { background: #f4f7f6; min-height: 100vh; padding: 25px 20px !important; }
    
    /* Hero Section - Banner & Branding */
    .company-hero-card { 
        background: white; border-radius: 15px; overflow: hidden; 
        border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.05); margin-bottom: 30px;
    }
    
    .hero-banner { 
        height: 200px; 
        background: linear-gradient(rgba(0,0,0,0.2), rgba(0,0,0,0.5)), url('<?php echo e(Url::asset("assets/img/blogpost.jpg")); ?>'); 
        background-size: cover; background-position: center; 
        position: relative;
    }

    .hero-body { padding: 0 30px 30px; position: relative; }

    /* Logo Box - Elevated and centered on the line */
    .hero-logo-wrapper {
        width: 150px; height: 150px; background: white; 
        border-radius: 20px; padding: 12px; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        margin-top: -75px; /* Floating effect */
        display: flex; align-items: center; justify-content: center;
        border: 5px solid white;
    }
    .hero-logo-wrapper img { max-width: 100%; max-height: 100%; border-radius: 10px; object-fit: contain; }

    /* Hero Typography - Ensuring name is visible */
    .hero-text-area { margin-top: 15px; flex: 1; }
    .hero-company-name { font-size: 2rem; font-weight: 850; color: #1e293b; letter-spacing: -1px; line-height: 1.2; }
    .hero-sub-info { color: #64748b; font-size: 0.95rem; font-weight: 600; display: flex; align-items: center; gap: 20px; margin-top: 5px; }

    /* Stats Pill */
    .stat-pill { 
        background: white; border: 1px solid #eef2f6; border-radius: 15px;
        padding: 18px 25px; display: flex; align-items: center; gap: 15px; transition: 0.3s;
    }
    .stat-pill:hover { transform: translateY(-3px); box-shadow: 0 8px 15px rgba(0,0,0,0.05); }
    .stat-pill i { font-size: 22px; color: #1572e8; background: #f0f7ff; padding: 12px; border-radius: 12px; }
    .stat-label { font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }
    .stat-value { font-size: 18px; font-weight: 800; color: #1e293b; }

    /* Content Styling */
    .sidebar-card { border-radius: 15px; border: none; }
    .sidebar-title { font-size: 13px; font-weight: 800; color: #1572e8; text-transform: uppercase; margin-bottom: 20px; letter-spacing: 1px; }
    
    .contact-row { display: flex; gap: 15px; margin-bottom: 20px; }
    .contact-row i { color: #1572e8; font-size: 18px; width: 25px; text-align: center; margin-top: 3px; }
    .contact-row label { display: block; font-size: 10px; font-weight: 800; color: #94a3b8; margin-bottom: 2px; }
    .contact-row p { margin: 0; font-size: 13px; font-weight: 600; color: #334155; line-height: 1.5; }

    /* Tabs Customization */
    .nav-pills-custom .nav-link { 
        border-radius: 10px; color: #64748b; font-weight: 700; font-size: 13px; 
        padding: 12px 24px; margin-right: 12px; transition: 0.3s;
    }
    .nav-pills-custom .nav-link.active { background: #1572e8 !important; color: white !important; box-shadow: 0 5px 15px rgba(21, 114, 232, 0.3); }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="container">
    <div class="page-inner">
        
        <div class="page-header mb-4">
            <h4 class="page-title fw-bold">Company Profile</h4>
            <ul class="breadcrumbs">
                <li class="nav-home"><a href="<?php echo e(base_url()); ?>"><i class="flaticon-home text-primary"></i></a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="<?php echo e(router('lecturer.company.index')); ?>">Company List</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><b>Detail</b></li>
            </ul>
        </div>

        
        <div class="company-hero-card shadow-sm">
            <div class="hero-banner"></div>
            <div class="hero-body">
                <div class="d-flex align-items-start flex-wrap">
                    <div class="hero-logo-wrapper">
                        
                        <img src="<?php echo e($data->getLogo()); ?>" alt="Logo" onerror="this.src='<?php echo e(Url::asset('upload/default/default_company.png')); ?>'">
                    </div>
                    <div class="hero-text-area px-md-4">
                        <div class="d-flex justify-content-between align-items-start flex-wrap w-100">
                            <div>
                                <h1 class="hero-company-name mb-1"><?php echo e($data->name); ?></h1>
                                <div class="hero-sub-info">
                                    <span><i class="fas fa-industry text-primary mr-1"></i> <?php echo e($data->getDisplayType()); ?></span>
                                    <span><i class="fas fa-map-marker-alt text-danger mr-1"></i> <?php echo e($data->city); ?></span>
                                </div>
                                <div class="mt-3">
                                    <?php echo $data->getStatusBadge(); ?>

                                </div>
                            </div>
                            <div class="mt-3">
                                <?php if($data->getWhatsAppLink()): ?>
                                <a href="<?php echo e($data->getWhatsAppLink()); ?>" target="_blank" class="btn btn-success fw-bold px-4 shadow-sm mr-2">
                                    <i class="fab fa-whatsapp mr-2"></i> WHATSAPP HR
                                </a>
                                <?php endif; ?>
                                <a href="mailto:<?php echo e($data->email); ?>" class="btn btn-outline-primary fw-bold px-4 border-2">
                                    <i class="far fa-envelope mr-2"></i> EMAIL
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            
            <div class="col-lg-4">
                <div class="stat-pill shadow-sm mb-4">
                    <i class="fas fa-users"></i>
                    <div>
                        <div class="stat-label">System Access</div>
                        <div class="stat-value text-primary"><?php echo e($data->access_type == 1 ? 'Full Partner' : 'Standard'); ?></div>
                    </div>
                </div>

                <div class="card sidebar-card shadow-sm p-4 mb-4">
                    <h6 class="sidebar-title">Contact Information</h6>
                    <div class="contact-row">
                        <i class="fas fa-map-marked-alt"></i>
                        <div class="contact-text">
                            <label>FULL ADDRESS</label>
                            <p><?php echo e($data->getFullAddress()); ?></p>
                        </div>
                    </div>
                    <div class="contact-row">
                        <i class="fas fa-globe"></i>
                        <div class="contact-text">
                            <label>WEBSITE</label>
                            <p><a href="<?php echo e($data->getWebsiteUrl()); ?>" target="_blank" class="text-primary"><?php echo e($data->website ?: '-'); ?></a></p>
                        </div>
                    </div>
                    <div class="contact-row">
                        <i class="fas fa-phone-square-alt"></i>
                        <div class="contact-text">
                            <label>PHONE</label>
                            <p><?php echo e($data->phone); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
                    <ul class="nav nav-pills nav-pills-custom mb-4" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="pill" href="#overview">OVERVIEW</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#staff">COMPANY STAFF</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#history">INTERN HISTORY</a>
                        </li>
                    </ul>
                    
                    <div class="tab-content" id="pills-tabContent">
                        
                        <div class="tab-pane fade show active" id="overview">
                            <h6 class="fw-bold text-primary mb-3"><i class="fas fa-info-circle mr-2"></i>About the Company</h6>
                            <div class="text-muted line-height-lg" style="font-size: 14px; text-align: justify;">
                                <?php echo nl2br(htmlspecialchars_decode($data->description)) ?: '<i>No description available.</i>'; ?>

                            </div>
                        </div>

                        
                        <div class="tab-pane fade" id="staff">
                            <div class="table-responsive">
                                <table class="table table-hover w-100 datatable-mini">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="py-3">Staff Name</th>
                                            <th class="py-3">Role</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark"><?php echo e($u['user_fullname']); ?></div>
                                                <small class="text-muted"><?php echo e($u['user_email']); ?></small>
                                            </td>
                                            <td><span class="badge badge-outline-primary px-3"><?php echo e($u['user_type']); ?></span></td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr><td colspan="2" class="text-center py-4">No staff registered.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        
                        <div class="tab-pane fade" id="history">
                            <div class="table-responsive">
                                <table class="table table-hover w-100 datatable-mini">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="py-3">Student Name</th>
                                            <th class="py-3">Period</th>
                                            <th class="py-3 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <?php
                                            $statusRaw = strtolower(trim($s['status']));
                                            $badgeClass = 'badge-light';
                                            
                                            if (in_array($statusRaw, ['finish', 'completed', 'selesai'])) $badgeClass = 'badge-success';
                                            elseif (in_array($statusRaw, ['yes', 'ongoing', 'active', 'sedang magang'])) $badgeClass = 'badge-primary';
                                            elseif (in_array($statusRaw, ['no', 'rejected', 'blacklist', 'ditolak'])) $badgeClass = 'badge-danger';
                                            elseif ($statusRaw == 'waiting') $badgeClass = 'badge-info';

                                            $period = '-';
                                            if (!empty($s['start_date']) && !empty($s['end_date'])) {
                                                $period = date('d M Y', strtotime($s['start_date'])) . ' - ' . date('d M Y', strtotime($s['end_date']));
                                            }
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark"><?php echo e($s['student_name']); ?></div>
                                                <small class="text-muted"><?php echo e($s['nim']); ?> | <?php echo e($s['program_study']); ?></small>
                                            </td>
                                            <td><small class="fw-bold text-muted"><?php echo e($period); ?></small></td>
                                            <td class="text-center">
                                                <span class="badge <?php echo e($badgeClass); ?> px-3 py-2 fw-bold text-uppercase" style="font-size: 10px; min-width: 80px;">
                                                    <?php echo e($s['status'] ?: 'Unknown'); ?>

                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr><td colspan="3" class="text-center py-4">No internship history.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
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
<script>
    $(document).ready(function() {
        $('.datatable-mini').DataTable({
            "pageLength": 10,
            "dom": 'tp', 
            "language": { "emptyTable": "No data available" }
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('templates.lecturer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/lecturer/company_list/detail.blade.php ENDPATH**/ ?>