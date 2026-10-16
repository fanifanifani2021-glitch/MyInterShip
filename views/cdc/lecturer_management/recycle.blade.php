@extends('templates.cdc')

@push('head-scripts')
@php

$id_kampus = $_SESSION['CDC_Data'] ['id_kampus'];
$sql = "SELECT * FROM tb_kampus WHERE id_kampus = '$id_kampus'";
$exec = $_SERVER['KONEKSI_DB']->query( $sql )->fetch_assoc();
$nama_kampus = $exec['nama_kampus'];

@endphp
@endpush

@section('main-content')


<div class="container">

    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Lecturer Management (Recycle)</h2>
                    <h5 class="text-white op-7 mb-2">Lecturer Management for <?= $nama_kampus ?></h5>



                </div>
                <div class="ml-md-auto py-2 py-md-0">
                    <a aria-label="Add New Lecturer" href="{{ router('lecturer.management') }}" role="button"
                        class="btn btn-primary btn-round"><i class="fa fa-arrow-left mr-2"></i> Go Back</a>
                </div>

            </div>
        </div>
    </div>


    <div class="page-inner mt--5">


        <div class="row mt--2">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3">
                        <div class="card-title">Lecturer List (Recycle Bin)</div>

                        

                        <div class="d-flex flex-column flex-sm-row align-items-center justify-content-center">

                            <div id="select-info" class="text-muted small mb-2 mb-sm-0 mr-sm-3 text-center"></div>

                            <div class="btn-group shadow-sm">
                                <button id="btn-view-selection" class="btn btn-sm btn-info" style="display: none;"
                                    onclick="showSelectedModal()">
                                    <i class="fas fa-eye mr-1"></i> View (<span id="view-count">0</span>)
                                </button>
                                <button id="btn-clear-selection" class="btn btn-sm btn-outline-secondary"
                                    style="display: none;">
                                    <i class="fas fa-times mr-1"></i>Clear
                                </button>

                                <button onclick="bulkRestore()" class="btn btn-success btn-sm">
                                    <i class="fa fa-undo mr-1"></i> Restore
                                </button>
                                <button onclick="bulkDelete()" class="btn btn-danger btn-sm">
                                    <i class="fa fa-trash mr-1"></i> Delete Permanently
                                </button>
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
</div>


@endsection

@push('footer-scripts')

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
            url: "{{ router('datatable.cdc.lecturer.recycle') }}",
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
                url: "{{ router('ajax.cdc.lecturer.permanent_bulk_destroy') }}",
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

    function bulkRestore() {
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
            title: 'Confirm Restoration',
            html: message +
                `<br><br><span class="text-danger font-weight-bold">Selected Lecturer will be restored</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, Restore All'
        }).then((result) => {
            if (!result.isConfirmed) return;

            Swal.fire({
                title: 'Restoring...',
                text: 'Processing request...',
                text: 'Processing request...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: "{{ router('ajax.cdc.lecturer.bulk_restore') }}",
                type: "POST",
                data: {
                    token: _token,
                    select_all: selectAllActive,
                    selected_ids: Array.from(selectedItems.keys()), // Kirim ID Manual
                    except_ids: Array.from(unselectedItems.keys()) // Kirim ID Pengecualian
                },
                success: function (response) {
                    Swal.fire('Restored!', `${totalToDelete} data restored.`, 'success');
                    resetSelectionState();
                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    Swal.fire('Error', 'Failed to restore data.', 'error');
                }
            });
        });
    }
</script>

<script>
    // ==========================================
    // 1. RESTORE LECTURER
    // ==========================================
    $(document).on('click', '.btn-restore', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Restore lecturer?',
            text: 'This lecturer will be restored and active again.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, restore',
            cancelButtonColor: '#d33'
        }).then((result) => {
            if (!result.isConfirmed) return;

            // Tampilkan Loading
            Swal.fire({
                title: 'Restoring...',
                text: 'Please wait while we restore the data.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.post("{{ router('ajax.cdc.lecturer.restore') }}", {
                nim_nik_unit: id,
                _token: _token
            })
            .done(function (res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Restored!',
                    text: res.message || 'Lecturer restored successfully',
                    timer: 1500,
                    showConfirmButton: false
                });
                table.ajax.reload(null, false);
            })
            .fail(function (xhr) {
                // Tutup loading jika error, tampilkan pesan error
                Swal.fire(
                    'Error',
                    xhr.responseJSON?.message || 'Failed to restore lecturer',
                    'error'
                );
            });
        });
    });

    // ==========================================
    // 2. FORCE DELETE (PERMANENT)
    // ==========================================
    $(document).on('click', '.btn-force-delete', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Delete permanently?',
            html: "This action <b>cannot be undone</b>.<br>The lecturer data will be gone forever.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, Delete Permanently'
        }).then((result) => {
            if (!result.isConfirmed) return;

            // Tampilkan Loading
            Swal.fire({
                title: 'Deleting...',
                text: 'Removing data permanently...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.post("{{ router('ajax.cdc.lecturer.permanent.destroy') }}", {
                nim_nik_unit: id, // Konsisten menggunakan nim_nik_unit
                _token: _token,
                _method: 'DELETE'
            })
            .done(function (res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: res.message || 'Lecturer permanently deleted',
                    timer: 1500,
                    showConfirmButton: false
                });
                table.ajax.reload(null, false);
            })
            .fail(function (xhr) {
                Swal.fire(
                    'Error',
                    xhr.responseJSON?.message || 'Failed to delete lecturer',
                    'error'
                );
            });
        });
    });
</script>
@endpush