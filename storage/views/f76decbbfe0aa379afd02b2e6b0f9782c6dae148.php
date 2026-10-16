

<?php $__env->startPush('head-scripts'); ?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/material_blue.css">

<style>
    /* =========================================
       1. GLOBAL TABLE STYLING
       ========================================= */
    #basic-datatables tbody tr td {
        vertical-align: middle !important;
        padding: 12px 15px;
    }

    #basic-datatables tbody tr:hover {
        background-color: #f9f9f9 !important;
    }

    #basic-datatables .badge {
        font-weight: 600;
        letter-spacing: 0.5px;
        font-size: 11px;
        padding: 5px 10px;
    }

    /* =========================================
       2. CHECKBOX COLUMN FIX (CENTERING)
       ========================================= */
    #basic-datatables tbody td:first-child {
        text-align: center !important;
        padding: 0 !important;
        width: 50px !important;
        vertical-align: middle !important;
    }

    #basic-datatables tbody td:first-child .custom-control {
        padding-left: 0 !important;
        margin: 0 auto !important;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        min-height: 24px;
        width: 24px;
    }

    #basic-datatables tbody td:first-child .custom-control-label {
        position: relative !important;
        margin-bottom: 0 !important;
        vertical-align: top !important;
        width: 1.25rem;
        height: 1.25rem;
        cursor: pointer;
    }

    #basic-datatables tbody td:first-child .custom-control-label::before {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        display: block !important;
        width: 100% !important;
        height: 100% !important;
        content: "" !important;
        border: 2px solid #d1d3e2;
        border-radius: 4px;
        background-color: #fff;
    }

    #basic-datatables tbody td:first-child .custom-control-label::after {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        display: block !important;
        width: 100% !important;
        height: 100% !important;
        content: "" !important;
        background: no-repeat 50% / 50% 50% !important;
    }

    /* Warna Checked */
    #basic-datatables tbody td:first-child .custom-control-input:checked ~ .custom-control-label::before {
        background-color: #1572e8 !important;
        border-color: #1572e8 !important;
    }

    #basic-datatables tbody td:first-child .custom-control-input:checked ~ .custom-control-label::after {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath fill='%23fff' d='M6.564.75l-3.59 3.612-1.538-1.55L0 4.26l2.974 2.99L8 2.193z'/%3e%3c/svg%3e") !important;
    }

    /* =========================================
       3. FORM INPUT ALIGNMENT (Select2 vs Flatpickr)
       ========================================= */
    
    /* Paksa tinggi Select2 agar konsisten */
    .select2-container .select2-selection--single {
        height: 38px !important; /* Tinggi standar Bootstrap 4 */
        border: 1px solid #ced4da !important;
        display: flex !important;
        align-items: center !important;
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        line-height: normal !important;
        padding-left: 0.75rem !important;
        color: #495057;
    }

    /* Paksa tinggi Flatpickr Input */
    .form-control.datepicker {
        height: 38px !important;
        background-color: #fff !important; /* Mencegah warna abu-abu (readonly) di mobile */
        border: 1px solid #ced4da;
    }

    /* Icon panah select2 di tengah */
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        right: 5px;
    }
</style>

<?php
    $pdo = $_SERVER['PDO'];
    $id_kampus = $_SESSION['CDC_Data']['id_kampus']; 

    // 1. Get Nama Kampus
    $stmt = $pdo->prepare("SELECT nama_kampus FROM tb_kampus WHERE id_kampus = :id_kampus LIMIT 1");
    $stmt->execute([':id_kampus' => $id_kampus]);
    $nama_kampus = $stmt->fetch(PDO::FETCH_ASSOC)['nama_kampus'] ?? '-';

    // 2. Get Major & Prodi
    $stmt_major = $pdo->prepare("SELECT DISTINCT major FROM program_study WHERE id_kampus = :id_kampus ORDER BY major");
    $stmt_major->execute([':id_kampus' => $id_kampus]);
    
    $daftar_major = [];
    while ($row = $stmt_major->fetch(PDO::FETCH_ASSOC)) {
        $jurusan = $row['major'];
        $stmt_prodi = $pdo->prepare("SELECT * FROM program_study WHERE major = :jurusan AND id_kampus = :id_kampus ORDER BY jenjang, study_program");
        $stmt_prodi->execute([':jurusan' => $jurusan, ':id_kampus' => $id_kampus]);
        
        $daftar_major[] = [
            'major'    => $jurusan,
            'programs' => $stmt_prodi->fetchAll(PDO::FETCH_ASSOC)
        ];
    }
?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="container">

    
    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row flex-wrap">
                <div class="mb-3 mb-md-0">
                    <h2 class="text-white pb-2 fw-bold">Internship Management</h2>
                    <h5 class="text-white op-7 mb-2">Internship Data for <?= htmlspecialchars($nama_kampus) ?></h5>
                </div>
                <div class="ml-md-auto py-2 py-md-0">
                    <a href="<?php echo e(router('cdc.internship.import')); ?>" class="btn btn-white btn-border btn-round mr-2">
                        <i class="fa fa-file-import mr-2"></i> Import
                    </a>
                    <a href="<?php echo e(router('cdc.internship.add')); ?>" class="btn btn-secondary btn-round">
                        <i class="fa fa-plus mr-2"></i> Add Data
                    </a>
                </div>
            </div>
        </div>
    </div>

    
    <div class="page-inner mt--5">
        <div class="row mt--2">
            <div class="col-md-12">

                
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="card-title">Filter Data</div>
                    </div>
                    
                    <form id="form_filter">
                        <input type="hidden" name="id_kampus" value="<?php echo e($id_kampus); ?>">
                        
                        <div class="card-body form-row align-items-end"> 
                            
                            
                            <div class="form-group col-md-3">
                                <label>Major</label>
                                <select id="major" name="major" class="form-control select2-filter">
                                    <option value="All">All Major</option>
                                    <?php $__currentLoopData = $daftar_major; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e(htmlspecialchars($group['major'])); ?>">
                                            <?php echo e(htmlspecialchars($group['major'])); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            
                            <div class="form-group col-md-3">
                                <label>Study Program</label>
                                <select id="program_study" name="program_study" class="form-control select2-filter">
                                    <option value="">- Select Study Program -</option>
                                    <option value="All">All Study Program</option>
                                    <?php $__currentLoopData = $daftar_major; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <optgroup label="<?php echo e(htmlspecialchars($group['major'])); ?>" id="<?php echo e('group-' . Str::slug($group['major'])); ?>">
                                            <option value="All-<?php echo e(htmlspecialchars($group['major'])); ?>">
                                                All in <?php echo e(htmlspecialchars($group['major'])); ?>

                                            </option>
                                            <?php $__currentLoopData = $group['programs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prodi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e(htmlspecialchars($prodi['kode_prodi'])); ?>">
                                                    <?php echo e(htmlspecialchars($prodi['jenjang'])); ?> <?php echo e(htmlspecialchars($prodi['study_program'])); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </optgroup>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            
                            <div class="form-group col-md-2">
                                <label>Generation</label>
                                <select id="generation" name="generation" class="form-control select2-filter">
                                    <option value="All">All Generation</option>
                                    <?php for($year = 2017; $year <= date('Y'); $year++): ?>
                                        <option value="<?php echo e(substr($year, 2, 3)); ?>">Gen <?php echo e($year); ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            
                            <div class="form-group col-md-2">
                                <label>Start Date</label>
                                
                                <input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Select Date">
                            </div>

                            
                            <div class="form-group col-md-2">
                                <label>End Date</label>
                                <input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="Select Date">
                            </div>

                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary" id="filterButton">
                                <i class="fas fa-filter mr-1"></i> Filter
                                <span id="spinner" class="fas fa-spin fa-spinner text-light ml-1" role="status" aria-hidden="true" style="display:none;"></span>
                            </button>
                            <button type="button" class="btn btn-secondary ml-2" id="resetButton">
                                <i class="fas fa-undo mr-1"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>

                
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex flex-wrap justify-content-between align-items-center">
                            <div class="card-title mb-2 mb-md-0">Internship List</div>
                            
                            <div class="d-flex flex-wrap align-items-center mt-2 mt-md-0">
                                <div id="select-info" class="text-muted small font-weight-bold mr-3 mb-2 mb-sm-0"></div>
                                
                                <div class="btn-group shadow-sm mr-2 mb-2 mb-sm-0">
                                    <button id="btn-view-selection" class="btn btn-sm btn-secondary" style="display: none;" onclick="showSelectedModal()">
                                        <i class="fas fa-eye mr-1"></i> <span class="d-none d-sm-inline">View</span> (<span id="view-count">0</span>)
                                    </button>
                                    <button id="btn-clear-selection" class="btn btn-sm btn-outline-secondary" style="display: none;"><i class="fas fa-times"></i></button>
                                    <button onclick="bulkDelete()" class="btn btn-danger btn-sm"><i class="fa fa-trash mr-1"></i> Delete</button>
                                </div>

                                <div class="btn-group shadow-sm mb-2 mb-sm-0">
                                    <a href="" class="btn btn-info btn-sm"><i class="fas fa-info-circle mr-1"></i> Manual</a>
                                    <a href="<?php echo e(router('cdc.internship.management.recycle')); ?>" class="btn btn-warning btn-sm"><i class="fas fa-recycle mr-1"></i> Recycle</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="basic-datatables" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th width="30"><input type="checkbox" id="check-all"></th>
                                        <th>Student Info</th>
                                        <th>Company</th>
                                        <th>Period</th>
                                        <th>Status</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    
    
    
    <div class="modal fade" id="modalSelectedList" tabindex="-1" role="dialog">

        <div class="modal-dialog modal-dialog-scrollable" role="document">

            <div class="modal-content">

                <div class="modal-header bg-light">

                    <h5 class="modal-title font-weight-bold">

                        <i class="fas fa-tasks mr-2 text-primary"></i>Selected Data

                    </h5>

                    <button type="button" class="close" data-dismiss="modal">

                        <span>&times;</span>

                    </button>

                </div>



                <div class="modal-body p-0">

                    <ul class="list-group list-group-flush" id="selected-list-container"></ul>



                    <div id="select-all-alert" style="display:none;">

                        <div class="alert alert-primary m-3 border-0 shadow-sm">

                            <div class="d-flex align-items-start">

                                <i class="fas fa-check-double fa-2x text-primary mr-3 mt-1"></i>

                                <div>

                                    <h6 class="font-weight-bold mb-1" id="select-all-title">Select All Active</h6>

                                    <div id="select-all-message" class="small line-height-normal text-dark"></div>

                                </div>

                            </div>

                        </div>



                        <div id="exception-container" style="display: none;" class="m-3 border rounded overflow-hidden shadow-sm">

                            <div class="d-flex justify-content-between align-items-center px-3 py-2 bg-light border-bottom">

                                <span class="font-weight-bold text-danger small text-uppercase">

                                    <i class="fas fa-ban mr-1"></i>

                                    <span id="exception-count-text">Excluded Items</span>

                                </span>

                            </div>

                            <div class="bg-white" style="max-height: 200px; overflow-y: auto;">

                                <ul class="list-group list-group-flush" id="unselected-list-container"></ul>

                            </div>

                        </div>

                    </div>

                </div>



                <div class="modal-footer bg-light">

                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>

                </div>

            </div>

        </div>

    </div>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('footer-scripts'); ?>

<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    let _token = $('meta[name="csrf-token"]').attr('content');
    let table = null;
    let selectAllActive = false; 
    let selectedItems = new Map(); 
    let unselectedItems = new Map(); 

    $(document).ready(function() {
        
        // ==========================================
        // 1. INIT FLATPICKR (DATE PICKER)
        // ==========================================
        const datePickers = $(".datepicker").flatpickr({
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d M Y", // Tampilan user: 01 Jan 2025
            allowInput: true,   // Boleh ketik manual
            static: true        // Agar dropdown tidak tertutup elemen lain
        });

        // ==========================================
        // 2. SELECT2 & ELEMENT CACHING
        // ==========================================
        const $selects = $('.select2-filter').select2({ theme: 'bootstrap4', width: '100%' });
        
        const $major = $('#major');
        const $programStudy = $('#program_study');
        const $generation = $('#generation');
        // Tangkap elemen Flatpickr (input asli, bukan altInput)
        const $startDate = $('#start_date'); 
        const $endDate = $('#end_date'); 
        
        const allProgramStudyOptions = $('<div>').append($programStudy.find('optgroup').clone()).html();

        // ==========================================
        // 3. DEPENDENT DROPDOWN LOGIC
        // ==========================================
        $major.on('change', function() {
            const selectedMajor = $(this).val();
            if ($programStudy.hasClass("select2-hidden-accessible")) $programStudy.select2('destroy');
            $programStudy.empty();

            const allOptionGlobal = $('<option>', { value: 'All', text: 'All Study Program', selected: true });
            $programStudy.append(allOptionGlobal);

            if (selectedMajor === 'All') {
                $programStudy.append(allProgramStudyOptions);
            } else {
                const $temp = $('<div>').html(allProgramStudyOptions);
                const $matchingGroup = $temp.find(`optgroup[label="${selectedMajor}"]`);
                if ($matchingGroup.length) {
                    $programStudy.append($('<option>', { value: 'All-' + selectedMajor, text: 'All in ' + selectedMajor }));
                    $programStudy.append($matchingGroup.clone());
                }
            }
            $programStudy.select2({ theme: 'bootstrap4', width: '100%' });
        });

        // ==========================================
        // 4. RESET BUTTON
        // ==========================================
        $('#resetButton').on('click', function() {
            $major.val('All').trigger('change'); 
            $generation.val('All').trigger('change');
            
            // Clear Flatpickr Instances
            datePickers.forEach(instance => instance.clear());

            $('#form_filter').trigger('submit');
        });

        // ==========================================
        // 5. DATATABLE CONFIG
        // ==========================================
        setTimeout(() => {
            table = $('#basic-datatables').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                pageLength: 15,
                lengthMenu: [10, 15, 25, 50],
                ajax: {
                    url: "<?php echo e(router('datatable.cdc.internship')); ?>",
                    type: "POST",
                    data: function (d) {
                        d.token = _token;
                        d.major = $major.val();
                        d.program_study = $programStudy.val();
                        d.generation = $generation.val();
                        // Kirim data tanggal ke server
                        d.start_date = $startDate.val();
                        d.end_date = $endDate.val();

                        console.log("➡️ [DataTable Request]", d);
                    },
                    dataSrc: function (json) {
                        console.log("⬅️ [DataTable Response]", json);
                        if (json.error) Swal.fire('Error', json.error, 'error');
                        return json.data;
                    },
                    beforeSend: function() {
                        $('#spinner').show();
                        $('#filterButton').attr('disabled', true);
                    },
                    complete: function() {
                        $('#spinner').hide();
                        $('#filterButton').attr('disabled', false);
                    },
                    error: function (xhr) {
                        console.error("❌ [DataTable Error]", xhr.responseText);
                        $('#spinner').hide();
                        $('#filterButton').attr('disabled', false);
                    }
                },
                columns: [
                    { data: 'checkbox', orderable: false, searchable: false },
                    { data: 'student_info' },
                    { data: 'company_info' },
                    { data: 'period' },
                    { data: 'status' },
                    { data: 'aksi', orderable: false, searchable: false }
                ],
                drawCallback: function () {
                    restoreCheckboxState();
                    updateSelectInfo();
                }
            });
        }, 100);

        // --- Event Filter Submit ---
        $('#form_filter').on('submit', function(e) {
            e.preventDefault();
            if(table) table.ajax.reload();
        });

        // ... (Checkbox Logic remains the same) ...
        $('#basic-datatables').on('change', '.row-check', function () {
            let id = $(this).data('id');
            let name = $(this).data('name');
            if (selectAllActive) {
                this.checked ? unselectedItems.delete(id) : unselectedItems.set(id, name);
            } else {
                this.checked ? selectedItems.set(id, name) : selectedItems.delete(id);
            }
            updateSelectInfo();
        });

        $('#check-all').on('click', function () {
            selectAllActive = this.checked;
            selectedItems.clear();
            unselectedItems.clear();
            $('.row-check').prop('checked', selectAllActive);
            updateSelectInfo();
        });

        $('#btn-clear-selection').on('click', function () {
            resetSelectionState();
        });
    });

    // ... (Keep your Helper Functions: restoreCheckboxState, updateSelectInfo, modal functions, bulkDelete, delete_data) ...
    // Paste them here exactly as they were in your previous code.
    
    function restoreCheckboxState() {
        $('.row-check').each(function () {
            let id = $(this).data('id');
            if (selectAllActive) {
                $(this).prop('checked', !unselectedItems.has(id));
            } else {
                $(this).prop('checked', selectedItems.has(id));
            }
        });
    }

    function resetSelectionState() {
        selectAllActive = false;
        selectedItems.clear();
        unselectedItems.clear();
        $('.row-check').prop('checked', false);
        $('#check-all').prop('checked', false).prop('indeterminate', false);
        updateSelectInfo();
    }

    function updateSelectInfo() {
        let totalFiltered = table.page.info().recordsTotal;
        let selectedCount = selectAllActive ? (totalFiltered - unselectedItems.size) : selectedItems.size;
        
        let checkAllBox = $('#check-all');
        if (selectedCount === 0) checkAllBox.prop({ checked: false, indeterminate: false });
        else if (selectedCount === totalFiltered) checkAllBox.prop({ checked: true, indeterminate: false });
        else checkAllBox.prop({ checked: false, indeterminate: true });

        if (selectedCount > 0) {
            $('#btn-clear-selection, #btn-view-selection').show();
            $('#view-count').text(selectedCount);
        } else {
            $('#btn-clear-selection, #btn-view-selection').hide();
            $('#select-info').empty();
        }
    }

    function showSelectedModal() {
        let listContainer = $('#selected-list-container');
        let exceptionList = $('#unselected-list-container');
        let exceptionContainer = $('#exception-container');

        listContainer.empty();
        exceptionList.empty();

        if (selectAllActive) {
            listContainer.hide();
            $('#select-all-alert').show();

            let totalData = table.page.info().recordsTotal;
            let excludedCount = unselectedItems.size;
            let netCount = totalData - excludedCount;

            if (excludedCount > 0) {
                $('#select-all-title').text('Selection with Exceptions');
                $('#select-all-message').html(`Selecting <b>${netCount}</b> records. <span class="text-danger">Excluding ${excludedCount} item(s).</span>`);
                exceptionContainer.show();
                $('#exception-count-text').text(`Excluded Items (${excludedCount})`);

                unselectedItems.forEach((name, id) => {
                    exceptionList.append(createListItem(id, name, 'exclude'));
                });
            } else {
                $('#select-all-title').text('Select All Active');
                $('#select-all-message').html(`Selecting <b>ALL ${totalData}</b> records.`);
                exceptionContainer.hide();
            }

        } else {
            $('#select-all-alert').hide();
            listContainer.show();

            if (selectedItems.size === 0) {
                listContainer.append('<li class="list-group-item text-center text-muted">No items selected</li>');
            } else {
                selectedItems.forEach((name, id) => {
                    listContainer.append(createListItem(id, name, 'manual'));
                });
            }
        }
        $('#modalSelectedList').modal('show');
    }

    function createListItem(id, name, type) {
        if (type === 'manual') {
            return `
                <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                    <span>${name}</span>
                    <button class="btn btn-xs btn-outline-danger" onclick="removeItem('${id}')">
                        <i class="fas fa-times"></i>
                    </button>
                </li>`;
        } else {
            return `
                <li class="list-group-item d-flex justify-content-between align-items-center list-group-item-warning py-2">
                    <span class="text-muted small"><del>${name}</del></span>
                    <button class="btn btn-xs btn-white border shadow-sm text-success" onclick="cancelExclusion('${id}')">
                        <i class="fas fa-plus"></i> Include
                    </button>
                </li>`;
        }
    }

    function removeItem(id) {
        selectedItems.delete(id);
        $(`.row-check[data-id='${id}']`).prop('checked', false);
        showSelectedModal();
        updateSelectInfo();
        if (selectedItems.size === 0) $('#modalSelectedList').modal('hide');
    }

    function cancelExclusion(id) {
        unselectedItems.delete(id);
        $(`.row-check[data-id='${id}']`).prop('checked', true);
        showSelectedModal();
        updateSelectInfo();
    }

    function bulkDelete() {
        let totalFiltered = table.page.info().recordsTotal;
        let totalToDelete = selectAllActive ? (totalFiltered - unselectedItems.size) : selectedItems.size;

        if (totalToDelete <= 0) {
            Swal.fire('Warning', 'Please select at least one internship.', 'warning');
            return;
        }

        Swal.fire({
            title: 'Confirm Deletion',
            html: `Delete <b>${totalToDelete}</b> internship records?<br><small class="text-danger">This action cannot be undone!</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, Delete All'
        }).then((result) => {
            if (!result.isConfirmed) return;

            Swal.fire({
                title: 'Deleting...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: "<?php echo e(router('ajax.cdc.internship.bulk_destroy')); ?>", 
                type: "POST",
                data: {
                    token: _token,
                    _method: 'DELETE',
                    select_all: selectAllActive,
                    selected_ids: Array.from(selectedItems.keys()),
                    except_ids: Array.from(unselectedItems.keys())
                },
                success: function (response) {
                    Swal.fire('Deleted!', `${totalToDelete} data deleted.`, 'success');
                    resetSelectionState();
                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    Swal.fire('Error', 'Failed to delete data.', 'error');
                }
            });
        });
    }

    function delete_data(id) {
        Swal.fire({
            title: 'Delete Internship?',
            text: 'This data will be moved to Recycle Bin.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Deleting...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: "<?php echo e(router('ajax.cdc.internship.destroy')); ?>", 
                    type: "POST",
                    data: {
                        id_internship: id, 
                        _token: "<?php echo e(csrf_token()); ?>",
                        _method: 'DELETE'
                    },
                    dataType: 'json',
                    success: function (response) {
                        Swal.fire('Deleted!', response.message, 'success');
                        table.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Failed to delete data', 'error');
                    }
                });
            }
        });
    }

</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('templates.cdc', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon2\www\pblKita\views/cdc/internship_management/index.blade.php ENDPATH**/ ?>