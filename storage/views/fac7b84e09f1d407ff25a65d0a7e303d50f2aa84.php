

<?php $__env->startPush('head-scripts'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php

$id_kampus = $_SESSION['CDC_Data'] ['id_kampus'];
$sql = "SELECT * FROM tb_kampus WHERE id_kampus = '$id_kampus'";
$exec = $_SERVER['KONEKSI_DB']->query( $sql )->fetch_assoc();
$nama_kampus = $exec['nama_kampus'];

?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>


<div class="container">

    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row flex-wrap">
                <div class="mb-3 mb-md-0">
                    <h2 class="text-white pb-2 fw-bold">Lecturer Management</h2>
                    <h5 class="text-white op-7 mb-2">Lecturer Management for <?= $nama_kampus ?></h5>
                </div>
                
                <div class="ml-md-auto py-2 py-md-0">
                    <a aria-label="Import Lecturer" href="<?php echo e(router('lecturer.import')); ?>" role="button"
                        class="btn btn-white btn-border btn-round mr-2">
                        <i class="fa fa-file-import mr-2"></i> Import
                    </a>
                    <a aria-label="Add New Lecturer" href="<?php echo e(router('lecturer.add')); ?>" role="button"
                        class="btn btn-secondary btn-round">
                        <i class="fa fa-plus mr-2"></i> Add Data
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-inner mt--5">
        <div class="row mt--2">
            <div class="col-md-12">
                <div class="card">
                
                    <div class="card-header">
                        <div class="d-flex flex-wrap justify-content-between align-items-center">

                            <div class="card-title mb-2 mb-md-0">
                                Lecturer List
                            </div>

                            <div class="d-flex flex-wrap align-items-center mt-2 mt-md-0">

                                <div id="select-info" class="text-muted small font-weight-bold mr-3 mb-2 mb-sm-0"></div>

                                <div class="btn-group shadow-sm mr-2 mb-2 mb-sm-0">
                                    <button id="btn-view-selection" class="btn btn-sm btn-secondary" style="display: none;"
                                        onclick="showSelectedModal()">
                                        <i class="fas fa-eye mr-1"></i> <span class="d-none d-sm-inline">View</span> (<span id="view-count">0</span>)
                                    </button>

                                    <button id="btn-clear-selection" class="btn btn-sm btn-outline-secondary" style="display: none;">
                                        <i class="fas fa-times"></i>
                                    </button>

                                    <button onclick="bulkDelete()" class="btn btn-danger btn-sm">
                                        <i class="fa fa-trash mr-1"></i> Delete
                                    </button>
                                </div>

                                <div class="btn-group shadow-sm mb-2 mb-sm-0">
                                    <a href="<?php echo e(router('manual/cdc.lecturer_management')); ?>" class="btn btn-info btn-sm">
                                        <i class="fas fa-info-circle mr-1"></i> Manual
                                    </a>

                                    <a href="<?php echo e(router('lecturer.management.recycle')); ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-recycle mr-1"></i> Recycle
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>

                
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="basic-datatables" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th width="30">
                                            <input type="checkbox" id="check-all">
                                        </th>
                                        <th>NIK</th>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Total Student</th>
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

                    <ul class="list-group list-group-flush" id="selected-list-container">
                    </ul>

                    <div id="select-all-alert" style="display:none;">

                        <div class="alert alert-primary m-3 border-0 shadow-sm">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-check-double fa-2x text-primary mr-3 mt-1"></i>
                                <div>
                                    <h6 class="font-weight-bold mb-1" id="select-all-title">Select All Active</h6>
                                    <div id="select-all-message" class="small line-height-normal text-dark">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="exception-container" style="display: none;"
                            class="m-3 border rounded overflow-hidden shadow-sm">
                            <div
                                class="d-flex justify-content-between align-items-center px-3 py-2 bg-light border-bottom">
                                <span class="font-weight-bold text-danger small text-uppercase">
                                    <i class="fas fa-ban mr-1"></i>
                                    <span id="exception-count-text">Excluded Items</span>
                                </span>
                            </div>

                            <div class="bg-white" style="max-height: 200px; overflow-y: auto;">
                                <ul class="list-group list-group-flush" id="unselected-list-container">
                                </ul>
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

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css"
    href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

<script>
    // ==========================================
    // 1. STATE MANAGEMENT
    // ==========================================
    let selectAllActive = false; // Penanda mode "Select All Global"
    let selectedItems = new Map(); // Mode Manual: Menyimpan item yang DIPILIH (ID => Name)
    let unselectedItems = new Map(); // Mode Global: Menyimpan item yang DIKECUALIKAN (ID => Name)

    const _token = $('meta[name="csrf-token"]').attr('content');

    // ==========================================
    // 2. DATATABLE INITIALIZATION
    // ==========================================
    let table = $('#basic-datatables').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: "<?php echo e(router('datatable.cdc.lecturer')); ?>",
            type: "POST",
            data: function (d) {
                d.token = _token;
            }
        },
        columns: [{
                data: 'checkbox',
                orderable: false,
                searchable: false
            }, // Index 0
            {
                data: 'nim_nik_unit'
            },
            {
                data: 'name'
            },
            {
                data: 'status'
            },
            {
                data: 'jumlah_mahasiswa',
                searchable: false
            },
            {
                data: 'aksi',
                orderable: false,
                searchable: false
            }
        ],
        drawCallback: function () {
            restoreCheckboxState();
            updateSelectInfo();
        }
    });

    // ==========================================
    // 3. EVENT LISTENERS
    // ==========================================

    // A. Event: Checkbox Per Baris (Row Click)
    $('#basic-datatables').on('change', '.row-check', function () {
        let id = $(this).data('id');
        let name = $(this).data('name');

        if (selectAllActive) {
            // --- MODE GLOBAL ---
            if (this.checked) {
                // User mencentang kembali item yang sebelumnya dikecualikan
                unselectedItems.delete(id);
            } else {
                // User membatalkan centang (menambah pengecualian)
                unselectedItems.set(id, name);
            }
        } else {
            // --- MODE MANUAL ---
            if (this.checked) {
                selectedItems.set(id, name);
            } else {
                selectedItems.delete(id);
            }
        }
        updateSelectInfo();
    });

    // B. Event: Header Checkbox (Select All Toggle)
    $('#check-all').on('click', function () {
        selectAllActive = this.checked;

        // Reset state saat toggle master
        selectedItems.clear();
        unselectedItems.clear();

        // Update visual semua checkbox di halaman aktif
        $('.row-check').prop('checked', selectAllActive);

        updateSelectInfo();
    });

    // C. Event: Tombol Clear Selection
    $('#btn-clear-selection').on('click', function () {
        resetSelectionState();
    });


    // ==========================================
    // 4. HELPER FUNCTIONS (LOGIC & UI)
    // ==========================================

    function restoreCheckboxState() {
        $('.row-check').each(function () {
            let id = $(this).data('id');

            if (selectAllActive) {
                // Centang KECUALI yang ada di daftar unselectedItems
                $(this).prop('checked', !unselectedItems.has(id));
            } else {
                // Centang HANYA yang ada di daftar selectedItems
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
        let totalFiltered = table.page.info().recordsTotal; // Gunakan recordsFiltered jika ingin konteks search
        let selectedCount = 0;

        // Hitung jumlah terpilih
        if (selectAllActive) {
            selectedCount = totalFiltered - unselectedItems.size;
        } else {
            selectedCount = selectedItems.size;
        }

        // Update Visual Header Checkbox (Indeterminate Logic)
        let checkAllBox = $('#check-all');
        if (selectedCount === 0) {
            checkAllBox.prop({
                checked: false,
                indeterminate: false
            });
        } else if (selectedCount === totalFiltered) {
            checkAllBox.prop({
                checked: true,
                indeterminate: false
            });
        } else {
            checkAllBox.prop({
                checked: false,
                indeterminate: true
            }); // Garis Minus
        }

        // Update UI Tombol & Text
        if (selectedCount > 0) {
            $('#btn-clear-selection, #btn-view-selection').show();
            $('#view-count').text(selectedCount);

            // Opsional: Tampilkan teks info di sebelah kiri
            // $('#select-info').html(`<span class="badge badge-info">${selectedCount} Selected</span>`);
        } else {
            $('#btn-clear-selection, #btn-view-selection').hide();
            $('#select-info').empty();
        }
    }

    // ==========================================
    // 5. MODAL FUNCTIONS
    // ==========================================

    function showSelectedModal() {
        let listContainer = $('#selected-list-container');
        let exceptionList = $('#unselected-list-container');
        let exceptionContainer = $('#exception-container');

        // Reset konten modal
        listContainer.empty();
        exceptionList.empty();

        if (selectAllActive) {
            // --- TAMPILAN MODE GLOBAL (SELECT ALL) ---
            listContainer.hide();
            $('#select-all-alert').show();

            let totalData = table.page.info().recordsTotal;
            let excludedCount = unselectedItems.size;
            let netCount = totalData - excludedCount;

            // Update Teks
            if (excludedCount > 0) {
                $('#select-all-title').text('Selection with Exceptions');
                $('#select-all-message').html(`
                    You are selecting <b>${netCount}</b> records (from total ${totalData}).<br>
                    <span class="text-danger">Excluding ${excludedCount} item(s) below.</span>
                `);

                // Render List Pengecualian
                exceptionContainer.show();
                $('#exception-count-text').text(`Excluded Items (${excludedCount})`);

                unselectedItems.forEach((name, id) => {
                    exceptionList.append(createListItem(id, name, 'exclude'));
                });
            } else {
                $('#select-all-title').text('Select All Active');
                $('#select-all-message').html(`You are selecting <b>ALL ${totalData}</b> records.`);
                exceptionContainer.hide();
            }

        } else {
            // --- TAMPILAN MODE MANUAL ---
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

    // Helper untuk membuat HTML List Item
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
            // Type Exclude (Coretan)
            return `
                <li class="list-group-item d-flex justify-content-between align-items-center list-group-item-warning py-2">
                    <span class="text-muted small"><del>${name}</del></span>
                    <button class="btn btn-xs btn-white border shadow-sm text-success" onclick="cancelExclusion('${id}')" title="Re-select">
                        <i class="fas fa-plus"></i> Include
                    </button>
                </li>`;
        }
    }

    // A. Hapus item manual
    function removeItem(id) {
        selectedItems.delete(id);
        $(`.row-check[data-id='${id}']`).prop('checked', false);

        showSelectedModal(); // Refresh modal
        updateSelectInfo();

        if (selectedItems.size === 0) $('#modalSelectedList').modal('hide');
    }

    // B. Batalkan pengecualian (Include kembali)
    function cancelExclusion(id) {
        unselectedItems.delete(id);
        $(`.row-check[data-id='${id}']`).prop('checked', true);

        showSelectedModal(); // Refresh modal
        updateSelectInfo();
    }

    // ==========================================
    // 6. ACTION FUNCTIONS (BULK DELETE)
    // ==========================================
    function bulkDelete() {
        let totalFiltered = table.page.info().recordsTotal;
        let totalToDelete = selectAllActive ?
            (totalFiltered - unselectedItems.size) :
            selectedItems.size;

        if (totalToDelete <= 0) {
            Swal.fire('Warning', 'Please select at least one lecturer.', 'warning');
            return;
        }

        let message = selectAllActive ?
            `Delete <b>${totalToDelete}</b> lecturers (All Pages)?<br><small>Excluding ${unselectedItems.size} items.</small>` :
            `Delete <b>${totalToDelete}</b> selected lecturer(s)?`;

        Swal.fire({
            title: 'Confirm Deletion',
            html: message + `<br><br><span class="text-danger font-weight-bold">Cannot be undone!</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, Delete All'
        }).then((result) => {
            if (!result.isConfirmed) return;

            Swal.fire({
                title: 'Deleting...',
                text: 'Processing request...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: "<?php echo e(router('ajax.cdc.lecturer.bulk_destroy')); ?>",
                type: "POST",
                data: {
                    token: _token,
                    _method: 'DELETE',
                    select_all: selectAllActive,
                    selected_ids: Array.from(selectedItems.keys()), // Kirim ID Manual
                    except_ids: Array.from(unselectedItems.keys()) // Kirim ID Pengecualian
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
            title: 'Delete Lecturer?',
            text: 'This lecturer will be moved to Recycle Bin.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {

                // Tampilkan Loading
                Swal.fire({
                    title: 'Deleting...',
                    text: 'Please wait...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: "<?php echo e(router('ajax.cdc.lecturer.destroy')); ?>", // Pastikan route ini mengarah ke case 'destroy'
                    type: "POST",
                    data: {
                        nim_nik_unit: id, // Kirim ID yang masih ter-encode (Base64)
                        _token: "<?php echo e(csrf_token()); ?>", // Jangan lupa CSRF Token
                        _method: 'DELETE' // Metode DELETE
                    },
                    dataType: 'json', // Expect JSON response
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        
                        // Reload Tabel (jika pakai DataTables)
                        if (typeof table !== 'undefined') {
                            table.ajax.reload(null, false);
                        } else {
                            window.location.reload();
                        }
                    },
                    error: function (xhr) {
                        Swal.fire(
                            'Error', 
                            xhr.responseJSON?.message || 'Failed to delete data', 
                            'error'
                        );
                    }
                });
            }
        });
    }

    /**
     * Trigger SweetAlert2 for Password Reset Confirmation
     * @param  {string} id Base64 encoded ID
     * @param  {string} name Lecturer Name
     */
    function reset_password(id, name) {
        Swal.fire({
            title: 'Reset Password?',
            html: `Are you sure you want to reset password for <b>${name}</b>?<br><small class="text-muted">Password will be set back to their NIK.</small>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#17a2b8', // Warna Info/Blue
            confirmButtonText: 'Yes, Reset it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                
                // Show Loading
                Swal.fire({
                    title: 'Resetting...',
                    text: 'Please wait while we update the security credentials.',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: "<?php echo e(router('ajax.cdc.lecturer.reset_password')); ?>", // Pastikan rute ini ada di router_ajax.php
                    type: "POST",
                    data: {
                        id: id,
                        _token: _token // Menggunakan konstanta _token yang sudah kamu buat di state management
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Password Reset!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire('Failed', response.message, 'error');
                        }
                    },
                    error: function (xhr) {
                        Swal.fire('Error', 'An error occurred on the server side.', 'error');
                    }
                });
            }
        });
    }
</script>

<?php $__env->stopPush(); ?>
<?php echo $__env->make('templates.cdc', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/cdc/lecturer_management/index.blade.php ENDPATH**/ ?>