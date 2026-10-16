

<?php $__env->startPush('head-scripts'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css">

<style>
    /* Square-Rounded Button Style Match image_5eba77.png */
    .btn-modern {
        border-radius: 6px !important;
        padding: 8px 20px;
        font-weight: 600;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: 0.3s;
    }
    .btn-modern:hover { transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,0,0,0.15); }

    /* Select2 Alignment Fix for input-group */
    .select2-container--bootstrap4 .select2-selection--single {
        height: calc(2.25rem + 2px) !important;
        border: 1px solid #ced4da !important;
        border-radius: 0 6px 6px 0 !important; /* Rounded hanya di sisi kanan krn ada prefix M */
    }
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        line-height: 2.25rem !important;
    }

    /* Input Readonly Styling */
    .form-control[readonly] { background-color: #f8f9fa !important; border: 1px solid #ced4da; }
    
    /* CKEditor Height */
    .ck-editor__editable { min-height: 200px; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="container"> 
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title">Add Logbook</h4>
            <ul class="breadcrumbs">
                <li class="nav-home"><a href="index.php"><i class="flaticon-home text-primary"></i></a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                
                <li class="nav-item"><a href="<?php echo e(router('student.internship')); ?>">My Internship</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="<?php echo e(router('student.internship.detail', ['id' => $id_enc])); ?>">Detail</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="<?php echo e(router('student.logbook.index', ['id' => $id_enc])); ?>">Logbook</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><b>Add</b></li>
            </ul>

        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Add Logbook</div>
                    </div>
                    <form action="<?php echo e(router('student.logbook.store', ['id' => $id_enc])); ?>" id="form_logbook" method="POST">
                        <?= csrf(); ?>
                        <?= input_hidden('id_internship', $id_plain) ?>
                        <?= input_hidden('nim', $_SESSION['nim']) ?>

                        <div class="card-body form-row">
                            
                            <div class="form-group col-md-4">
                                <label>Week Num <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light font-weight-bold" id="basic-addon1">M</span>
                                    </div>
                                    <select name="week_num" id="week_num_select" class="form-control select2">
                                        <option value="">-- Choose Week --</option>
                                        <?php $__currentLoopData = $weekSchedule; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sched): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($sched['week']); ?>" 
                                                data-start="<?php echo e($sched['start']); ?>" 
                                                data-end="<?php echo e($sched['end']); ?>"
                                                <?php echo e($sched['week'] == $last_week ? 'selected' : ''); ?>

                                                <?php echo e($sched['filled'] ? 'disabled' : ''); ?>>
                                                <?php echo e($sched['week']); ?> <?php echo e($sched['filled'] ? '(Completed)' : ''); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <?= form_error('week_num'); ?>
                            </div>

                            <div class="form-group col-md-4">
                                <label>Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" id="start_date" class="form-control">
                            </div>

                            <div class="form-group col-md-4">
                                <label>End Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" id="end_date" class="form-control">
                            </div>

                            
                            <div class="form-group col-md-12">
                                <label>Description <span class="text-danger">*</span></label>
                                <div id="editor-description"></div>
                                <textarea name="description" id="hidden-description" style="display:none;"></textarea>
                                <?= form_error('description'); ?>
                            </div>

                            
                            <div class="form-group col-md-12">
                                <label>Documentation <span class="text-danger">*</span></label>
                                <div id="editor-documentation"></div>
                                <textarea name="documentation" id="hidden-documentation" style="display:none;"></textarea>
                                <?= form_error('documentation'); ?>
                            </div>
                        </div>

                        
                        <div class="card-footer">
                            <button type="button" class="btn btn-danger" onclick="confirmation()"><i class="fas fa-times mr-1"></i>Cancel</button>
                            <button type="button" class="btn btn-warning" onclick="reset_confirmation()"><i class="fas fa-sync mr-1"></i>Reset</button>
                            <button type="submit" class="btn btn-success" id="submit_btn"><i class="fas fa-save mr-1"></i>Save</button>
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
<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>

<script>
    let descEditor, docEditor;

    // 1. Initialize CKEditor 5
    ClassicEditor.create(document.querySelector('#editor-description'), {
        toolbar: ['bold', 'italic', 'bulletedList', 'numberedList', 'undo', 'redo'],
        placeholder: 'Describe all job you\'ve done this week'
    }).then(editor => { descEditor = editor; });

    ClassicEditor.create(document.querySelector('#editor-documentation'), {
        toolbar: ['bold', 'italic', 'imageUpload', 'insertTable', 'undo', 'redo'],
        placeholder: 'Provide some photos to prove your job'
    }).then(editor => { docEditor = editor; });

    // 2. Logic Auto-Fill Date
    function updateDates() {
        const selected = $('#week_num_select').find(':selected');
        $('#start_date').val(selected.data('start') || '');
        $('#end_date').val(selected.data('end') || '');
    }

    $(document).ready(function() {
        $('.select2').select2({ theme: 'bootstrap4' });
        
        updateDates(); // Jalankan saat load awal
        $('#week_num_select').on('change', updateDates);

        $('#form_logbook').on('submit', function() {
            $('#hidden-description').val(descEditor.getData());
            $('#hidden-documentation').val(docEditor.getData());
            $('#submit_btn').html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...').prop('disabled', true);
        });
    });
</script>



<link rel='stylesheet' href='./assets/sweetalert2.css'>
<script src='./assets/sweetalert2.all.js'></script>

<!-- Soloalert -->
<script src='./core/component/soloalert.js'></script>


<script>
    // CKEDITOR Initialize
    $(function() {

        // CKEDITOR
        var description = CKEDITOR.replace('description', {
            language: 'en'
            , extraPlugins: 'notification'
            , editorplaceholder: "Describe how was your day going or all job you have done this week"
            , toolbar: [{
                    name: 'basicstyles'
                    , items: ['Bold', 'Italic']
                }
                , {
                    name: 'clipboard'
                    , items: ['Copy', 'Paste']
                }
            ]
            , removePlugins: 'elementspath', // Optional: Remove the elementspath at the bottom
            resize_enabled: false // Optional: Disable the resize handle
        });



        var documentation = CKEDITOR.replace('documentation', {
            language: 'en'
            , extraPlugins: 'notification'
            , editorplaceholder: "Provide some photos to prove your job"
            , toolbar: [{
                    name: 'basicstyles'
                    , items: ['Bold', 'Italic']
                }
                , {
                    name: 'clipboard'
                    , items: ['Copy', 'Paste']
                }
            ]
            , removePlugins: 'elementspath', // Optional: Remove the elementspath at the bottom
            resize_enabled: false // Optional: Disable the resize handle
        });

    });

</script>


<!-- User Custom Script -->
<script>
    function confirmation() {
        // alert('Are you sure you want to cancel ? (Your change will not be saved)');
        SoloAlert.confirm({
            title: "Are you sure you want to cancel ?"
            , body: "Changes you made will not be saved !"
            , useTransparency: true
            , onOk: () => {
                window.location.href = "<?= router('student.logbook.index', ['id' => $id_enc]) ?>";
            }
        });

    }

    function reset_confirmation() {

        SoloAlert.confirm({
            title: "Are you sure you want to reset ?"
            , body: "Changes you made will be reset to default !"
            , useTransparency: true
            , onOk: () => {
                form_reset();
            }
        });

    }


    function form_reset() {

        $("#form_logbook").trigger("reset");

        $("small").empty();

        $(".form-control").removeClass('is-invalid border-success border-danger is-valid');
        $('#cke_description').removeClass('<?= show_error('description')?>');
        $('#cke_documentation').removeClass('<?= show_error('documentation')?>');

    };

</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('templates.student', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/student/my_internship/logbook/add.blade.php ENDPATH**/ ?>