@extends('templates.cdc')

@push('head-scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<style>
    /* Drag & Drop Zone Styling */
    .upload-drop-zone {
        border: 2px dashed #dee2e6;
        border-radius: 12px;
        background-color: #f8f9fa;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        position: relative;
        cursor: pointer;
        min-height: 300px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .upload-drop-zone:hover {
        border-color: #adb5bd;
        background-color: #f1f3f5;
    }

    .upload-drop-zone.dragover {
        border-color: #1269DB;
        background-color: #e8f0fe;
        transform: scale(1.02);
        box-shadow: 0 10px 30px rgba(18, 105, 219, 0.1);
    }

    .upload-icon {
        transition: transform 0.3s ease;
        color: #6c757d;
    }

    .upload-drop-zone.dragover .upload-icon {
        transform: scale(1.2);
        color: #1269DB;
    }

    /* Modern Table Styling */
    .table-import thead th {
        border-top: none;
        background-color: #343a40;
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
    
    .table-import tbody tr {
        transition: background-color 0.2s;
    }

    .table-import .custom-control-label::before, 
    .table-import .custom-control-label::after {
        top: 0.15rem;
    }

    /* --- PERBAIKAN TABLE ALIGNMENT --- */
    .table-import td {
        vertical-align: middle !important; /* Paksa vertikal tengah */
        padding: 12px 15px !important;     /* Beri sedikit ruang napas */
    }

    /* Agar Checkbox benar-benar di tengah secara vertikal & horizontal */
    .table-import .custom-control {
        min-height: 0; 
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 0;
        padding-left: 1.5rem; /* Kompensasi padding default custom-control */
    }
    
    /* Styling Header Tabel agar lebih tegas */
    .table-import thead th {
        vertical-align: middle !important;
        background-color: #1a2035; /* Warna gelap elegan */
        color: #fff;
        border: none;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 15px !important;
    }

    /* Row Hover Effect */
    .table-import tbody tr:hover {
        background-color: #f7f9fc !important;
    }


    /* 1. Reset Container Checkbox */
    .table-import .custom-control.custom-checkbox {
        display: inline-block !important; /* Agar bisa di-center oleh text-center */
        padding-left: 0 !important;       /* Hapus padding yang bikin miring */
        margin: 0 !important;
        width: 16px !important;           /* Paksa lebar fix sesuai visual checkbox */
        height: 16px !important;          /* Paksa tinggi fix */
        min-height: 0 !important;
        vertical-align: middle;
        position: relative;
    }

    /* 2. Reset Posisi Icon (::before dan ::after) */
    .table-import .custom-control-label::before,
    .table-import .custom-control-label::after {
        left: 0 !important; /* Paksa icon nempel di kiri container 16px tadi */
        top: 0 !important;  /* Paksa icon nempel di atas */
        width: 16px !important;
        height: 16px !important;
    }

    /* 3. Reset Area Label */
    .table-import .custom-control-label {
        width: 16px;
        height: 16px;
        margin-bottom: 0;
        cursor: pointer;
        vertical-align: top;
    }
    
    /* --- END FIX --- */

    /* CSS Tambahan untuk Table Cell agar Rapi */
    .table-import td, .table-import th {
        vertical-align: middle !important;
        padding: 12px 10px !important;
    }
</style>
@endpush

@section('main-content')
<div class="container">
    <div class="page-inner">

        <div class="page-header">
            <h4 class="page-title">Import Lecturer</h4>
            <ul class="breadcrumbs">
                <li class="nav-home"><a href="{{ router('dashboard') }}"><i class="flaticon-home"></i></a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item">Lecturer Import</li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm border-0">
                    
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0 font-weight-bold text-dark">Import Data Wizard</h4>
                        <div class="btn-group">
                            <a href="javascript:void(0)" onclick="confirmAction('{{ router('excel.template.import_lecturer') }}', 'Download Template?', 'download')" 
                               class="btn btn-info btn-sm">
                                <i class="fas fa-download mr-1"></i> Template
                            </a>
                            
                            <a href="javascript:void(0)" onclick="confirmAction('{{ router('lecturer.management') }}', 'Leave this page?', 'redirect')" 
                               class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i> Back
                            </a>
                        </div>
                    </div>

                    <div class="card-body" id="step_1">
                        <form id="form_upload">
                            <div class="upload-drop-zone" id="drop_zone">
                                
                                <div class="mb-3">
                                    <i class="fas fa-cloud-upload-alt fa-5x upload-icon"></i>
                                </div>
                                
                                <div id="drop_instruction">
                                    <h5 class="font-weight-bold text-dark">Drag & Drop Excel File Here</h5>
                                    <p class="text-muted small mb-0">Supported format: .xlsx</p>
                                    <button type="button" class="btn btn-block btn-outline-primary btn-sm mt-3" id="btn_browse_manual">
                                        Browse File
                                    </button>
                                </div>
                                
                                <input type="file" id="file_excel" name="file" class="d-none" accept=".xlsx, .xls">
                                
                                <div id="file_info" class="d-none animate__animated animate__fadeIn text-center">
                                    <h4 class="text-primary mb-1"><i class="fas fa-file-excel mr-2"></i></h4>
                                    <h6 class="font-weight-bold text-dark" id="filename_display">filename.xlsx</h6>
                                    <p class="text-success small mb-4"><i class="fas fa-check-circle mr-1"></i>File ready to process</p>
                                    
                                    <button type="submit" class="btn btn-primary btn-lg px-5 shadow" id="btn_process">
                                        <i class="fas fa-cog mr-2"></i> Process Data
                                    </button>
                                    <br>
                                    <button type="button" class="btn btn-link text-muted btn-sm mt-2" id="btn_change_file">Change File</button>
                                </div>

                            </div>
                        </form>
                    </div>

                    <div class="card-body" id="step_2" style="display: none;">
                        
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h4 class="mb-0 font-weight-bold">Review Data</h4>
                                <small class="text-muted">Selected: <span class="text-primary font-weight-bold" id="count_selected_label">0</span> rows</small>
                            </div>
                            <div class="alert alert-soft-info py-2 px-3 mb-0">
                                <i class="fas fa-info-circle mr-1"></i> 
                                Rows marked as <span class="badge badge-danger">Registered</span> will be skipped.
                            </div>
                        </div>

                        <div class="table-responsive" style="max-height: 550px; overflow-y: auto;">
                            <table class="table table-import table-hover mb-0">
                                <thead class="thead-dark sticky-top">
                                    <tr>
                                        <th class="text-center align-middle" width="50">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="check_all">
                                                <label class="custom-control-label" for="check_all"></label>
                                            </div>
                                        </th>
                                        <th class="align-middle">NIK / NIP</th>
                                        <th class="align-middle">Full Name</th>
                                        <th class="align-middle">Email</th>
                                        <th class="text-center align-middle" width="120">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="table_body"></tbody>
                            </table>
                        </div>

                        <div class="card-footer bg-white border-top-0 d-flex justify-content-between align-items-center mt-3 px-0">
                            <small class="text-muted">Total Data Found: <span id="total_data_count">0</span></small>
                            <div>
                                <button class="btn btn-danger mr-2" onclick="resetPage()">
                                    <i class="fas fa-times mr-1"></i> Cancel
                                </button>
                                <button class="btn btn-success shadow" id="btn_execute_import" disabled>
                                    <i class="fas fa-file-import mr-1"></i> Import Selected (<span id="count_selected">0</span>)
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer-scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let isDataDirty = false;
    let lecturerData = [];

    // Helper untuk ambil CSRF Token dari Meta Tag
    function getCsrfToken() {
        return $('meta[name="csrf-token"]').attr('content');
    }

    // ==========================================
    // 1. DRAG & DROP LOGIC
    // ==========================================
    const $dropZone = $('#drop_zone');
    const $fileInput = $('#file_excel');

    // Mencegah bubbling event klik
    $('#btn_process, #btn_change_file').click(function(e){
        e.stopPropagation();
    });

    // Trigger file picker
    $dropZone.click(function(e) {
        if(e.target.id !== 'file_excel' && $(e.target).closest('#btn_process').length === 0) {
             $fileInput.click();
        }
    });
    
    // Explicit trigger tombol browse
    $('#btn_browse_manual, #btn_change_file').click(function(e){
        e.stopPropagation(); 
        $fileInput.click();
    });

    // Drag Effects
    $dropZone.on('dragover dragenter', function(e) {
        e.preventDefault(); e.stopPropagation();
        $(this).addClass('dragover');
    });

    $dropZone.on('dragleave dragend', function(e) {
        e.preventDefault(); e.stopPropagation();
        $(this).removeClass('dragover');
    });

    // Drop Event
    $dropZone.on('drop', function(e) {
        e.preventDefault(); e.stopPropagation();
        $(this).removeClass('dragover');
        const files = e.originalEvent.dataTransfer.files;
        handleFiles(files);
    });

    $fileInput.change(function() { handleFiles(this.files); });

    function handleFiles(files) {
        if (files.length > 0) {
            let file = files[0];
            if(!file.name.match(/\.(xlsx|xls)$/)) {
                Swal.fire('Error', 'Please upload a valid Excel file (.xlsx)', 'error');
                return;
            }

            let container = new DataTransfer();
            container.items.add(file);
            $fileInput[0].files = container.files;

            $('#filename_display').text(file.name);
            $('.upload-icon').removeClass('fa-cloud-upload-alt text-muted').addClass('fa-file-excel text-success');
            $('#file_info').removeClass('d-none');
            $('#drop_instruction').addClass('d-none');
        }
    }


    // ==========================================
    // 2. CONFIRMATION LOGIC
    // ==========================================
    window.confirmAction = function(targetUrl, titleText, actionType) {
        let warningText = isDataDirty ? "You have unsaved parsed data. You will lose your progress!" : "Are you sure you want to proceed?";
        let confirmBtnText = "Yes, Proceed";
        let iconType = isDataDirty ? 'warning' : 'question';

        if (actionType === 'download') {
            warningText = "Do you want to download the excel template?";
            confirmBtnText = "Download";
            iconType = 'info';
            if(isDataDirty) warningText = "Downloading template won't lose your data, but please verify before leaving.";
        }

        Swal.fire({
            title: titleText, text: warningText, icon: iconType,
            showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#d33', confirmButtonText: confirmBtnText
        }).then((result) => {
            if (result.isConfirmed) {
                if (actionType === 'download') window.open(targetUrl, '_blank');
                else window.location.href = targetUrl;
            }
        });
    }

    // ==========================================
    // 3. PREVIEW LOGIC
    // ==========================================
    $('#form_upload').submit(function(e) {
        e.preventDefault();
        
        let formData = new FormData(this);
        
        // [UPDATE] Tambahkan CSRF Token ke FormData
        let _token = getCsrfToken();
        formData.append('_token', _token);
        
        Swal.fire({ title: 'Analyzing File...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        $.ajax({
            url: "{{ router('ajax.import.preview.lecturer') }}",
            type: 'POST', 
            data: formData, 
            contentType: false, processData: false, dataType: 'json',
            success: function(res) {
                Swal.close();
                if(res.status === 1) renderTable(res.data);
                else Swal.fire('Error', res.message, 'error');
            },
            error: function(xhr) { 
                Swal.close();
                if (xhr.status === 429) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Too Many Requests',
                        text: 'You are submitting files too fast. Please wait a moment.',
                        timer: 5000,
                        timerProgressBar: true
                    });
                } else {
                    let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Failed to parse file';
                    Swal.fire('Error', msg, 'error'); 
                }
            }
        });
    });

    function renderTable(data) {
        lecturerData = data;
        let html = '';
        let validCount = 0;
        isDataDirty = true;

        if (data.length === 0) { 
            Swal.fire('Empty', 'No data found', 'warning'); return; 
        }

        data.forEach((row, index) => {
            let isExist = row.is_exist;
            let trClass = isExist ? 'bg-light text-muted' : 'bg-white';
            
            let checkbox = isExist 
                ? '<i class="fas fa-ban text-danger" data-toggle="tooltip" title="Already Registered"></i>' 
                : `<div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input row-check" id="row_${index}" value="${index}" checked>
                        <label class="custom-control-label" for="row_${index}"></label>
                   </div>`;
            
            let status = isExist 
                ? '<span class="badge badge-danger shadow-sm">Registered</span>' 
                : '<span class="badge badge-success shadow-sm">Ready</span>';

            html += `
                <tr class="${trClass}">
                    <td class="text-center align-middle">${checkbox}</td>
                    <td class="align-middle font-weight-bold">${row.nik}</td>
                    <td class="align-middle text-nowrap">${row.name}</td>
                    <td class="align-middle">${row.email || '<span class="text-muted small">No Email</span>'}</td>
                    <td class="text-center align-middle">${status}</td>
                </tr>
            `;

            if(!isExist) validCount++;
        });

        $('#table_body').html(html);
        $('#total_data_count').text(data.length);
        
        $('[data-toggle="tooltip"]').tooltip();

        $('#step_1').fadeOut(300, function(){
            $('#step_2').fadeIn(300);
        });

        updateSelectedCount();
        
        if(validCount === 0) $('#check_all').prop('disabled', true);
    }

    // ==========================================
    // 4. CHECKBOX LOGIC
    // ==========================================
    $(document).on('change', '.row-check', function() {
        updateSelectedCount();
    });

    $('#check_all').change(function() {
        let isChecked = this.checked;
        $('.row-check').prop('checked', isChecked);
        updateSelectedCount();
    });

    function updateSelectedCount() {
        let count = $('.row-check:checked').length;
        let totalValid = $('.row-check').length;

        $('#count_selected').text(count);
        $('#count_selected_label').text(count);
        $('#btn_execute_import').prop('disabled', count === 0);

        if (totalValid > 0 && count === totalValid) {
            $('#check_all').prop('checked', true);
            $('#check_all').prop('indeterminate', false);
        } else if (count === 0) {
            $('#check_all').prop('checked', false);
            $('#check_all').prop('indeterminate', false);
        } else {
            $('#check_all').prop('checked', false);
            $('#check_all').prop('indeterminate', true); 
        }
    }

    // ==========================================
    // 5. EXECUTE IMPORT LOGIC
    // ==========================================
    $('#btn_execute_import').click(function() {
        let selectedIndices = [];
        $('.row-check:checked').each(function() { selectedIndices.push($(this).val()); });
        let payloadData = selectedIndices.map(index => lecturerData[index]);

        Swal.fire({
            title: 'Confirm Import',
            text: `Are you sure want to import ${payloadData.length} lecturers?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Import',
            confirmButtonColor: '#28a745'
        }).then((result) => {
            if(result.isConfirmed) executeImport(payloadData);
        });
    });

    function executeImport(payloadData) {
        Swal.fire({ title: 'Importing...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        
        // [UPDATE] Ambil token dari Meta Tag
        let _token = getCsrfToken();

        $.ajax({
            url: "{{ router('ajax.import.store.lecturer') }}",
            type: 'POST',
            data: { 
                lecturers: payloadData, 
                _token: _token  // Kirim token via POST body
            },
            dataType: 'json',
            success: function(res) {
                if(res.status === 1) {
                    isDataDirty = false;
                    Swal.fire({
                        icon: 'success', title: 'Success', 
                        text: `${res.data.success} data imported successfully.`
                    }).then(() => {
                        window.location.href = "{{ router('lecturer.management') }}";
                    });
                } else {
                    Swal.fire('Failed', res.message, 'error');
                }
            },
            error: function(xhr) { 
                Swal.close();
                if (xhr.status === 429) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Too Many Requests',
                        text: 'You are importing data too fast. Please wait a moment.',
                        timer: 5000,
                        timerProgressBar: true
                    });
                } else {
                    let msg = xhr.responseJSON ? xhr.responseJSON.message : 'System Error';
                    Swal.fire('Error', msg, 'error'); 
                }
            }
        });
    }

    window.resetPage = function() {
        confirmAction(window.location.href, 'Cancel Import?', 'redirect');
    }
</script>
@endpush