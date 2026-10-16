

<?php $__env->startPush('head-scripts'); ?>
<?php
// Mengambil nilai id_kampus dari session
$id_kampus = $_SESSION['CDC_Data']['id_kampus'];

// Menyiapkan koneksi PDO (pastikan PDO sudah diinisialisasi sebelumnya)
$pdo = $_SERVER['PDO'];

// Menyiapkan query dengan PDO prepared statement, dengan limit 1
$sql = "SELECT * FROM tb_kampus WHERE id_kampus = :id_kampus LIMIT 1";
$stmt = $pdo->prepare($sql);

// Mengikat parameter :id_kampus ke nilai $id_kampus
$stmt->bindParam(':id_kampus', $id_kampus, PDO::PARAM_STR);

// Eksekusi statement
$stmt->execute();

// Mendapatkan hasil sebagai array asosiatif
$exec = $stmt->fetch(PDO::FETCH_ASSOC);

// Mengambil nama kampus dari hasil query
$nama_kampus = $exec['nama_kampus'];

// Ambil daftar jurusan (major)
$sql = "SELECT * FROM tb_kampus ORDER BY id_kampus";
$stmt = $pdo->prepare($sql);
$stmt->execute();

$daftar_kampus = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>


<div class="container">

    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Study Program Management</h2>
                    <h5 class="text-white op-7 mb-2">Managing All Study Program for All University</h5>
                </div>
                <div class="ml-md-auto py-2 py-md-0">
                    <a aria-label="Add New Study Program" href="<?php echo e(base_url('cdc/study_program/add')); ?>" class="btn btn-primary btn-round"><i class="fa fa-plus"></i> Add</a>
                </div>
            </div>
        </div>
    </div>



    <div class="page-inner mt--5">


        <div class="row mt--2">
            <div class="col-md-12">

                <!-- Filter -->
                <div class="card">
                    <form id="form_filter"> <?= csrf() ?>
                        <div class="card-body form-row">


                            <div class="form-group col-md-12">
                                <label>University <b class="text-danger">*</b></label>
                                <select id="university" name="university" class="form-control">
                                    <option value="All">All University</option>

                                    <?php $__currentLoopData = $daftar_kampus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($data['id_kampus']); ?>" <?php if($id_kampus==$data['id_kampus']): ?> <?php echo e('selected'); ?> <?php endif; ?>><?php echo e(htmlspecialchars($data['nama_kampus'])); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>

                                <small class="invalid-feedback text-sm text-danger">Choose one</small>
                            </div>



                        </div>
                        <div class="card-footer">

                            <button type="submit" class="btn btn-primary" id="filterButton">
                                <i class="fas fa-filter mr-1"></i>Filter
                                <span id="spinner" class="fas fa-spin fa-spinner text-light ml-1" role="status" aria-hidden="true" style="display:none;"></span>
                            </button>
                        </div>


                    </form>

                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Study Program List</div>
                    </div>
                    <div class="card-body">
                        <table id="basic-datatables" class="table table-striped table-bordered">

                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>University</th>
                                    <th>Code</th>
                                    <th>Study Program</th>
                                    <th>Major</th>
                                    <th>Stats</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


<?php $__env->stopSection(); ?>

<?php $__env->startPush('footer-scripts'); ?>

<!-- Datatables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

<!-- Datatables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

<!-- JS Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

<script>
    // Ambil token CSRF
    let _token = $('meta[name="csrf-token"]').attr('content');

    // =========================================================================
    // HELPER: GLOBAL ERROR HANDLER (HANDLE 429 THROTTLE)
    // =========================================================================
    function handleAjaxError(xhr, defaultMessage = "Something went wrong") {
        Swal.close(); // Tutup loading spinner

        if (xhr.status === 429) {
            // Menangani Rate Limit (Throttle)
            Swal.fire({
                title: 'Too Many Requests',
                text: 'You are performing actions too quickly. Please wait a moment before trying again.',
                icon: 'warning',
                timer: 5000,
                timerProgressBar: true,
                showConfirmButton: true
            });
        } else {
            // Menangani Error Lainnya
            let msg = defaultMessage;
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.error) {
                msg = xhr.responseJSON.error;
            }

            Swal.fire({
                title: 'Error',
                text: msg,
                icon: 'error',
                timer: 3000,
                showConfirmButton: true
            });
        }
    }

    // =========================================================================
    // 1. DATATABLE CONFIGURATION
    // =========================================================================
    let table; // Deklarasi global agar bisa diakses fungsi lain

    $(document).ready(function() {
        table = $('#basic-datatables').DataTable({
            responsive: true,
            pageLength: 20,
            lengthMenu: [10, 15, 25, 50],
            processing: true,
            serverSide: true,
            ajax: {
                // Gunakan Helper Router (Sesuaikan nama route Anda)
                url: "<?php echo e(router('datatable.cdc.study_program')); ?>",
                type: "POST",
                // Kirim Token via Header (Best Practice)
                headers: { 'X-CSRF-TOKEN': _token },
                data: function(d) {
                    d._token = _token; // Fallback
                    // Ambil parameter filter
                    d.coor = (new URLSearchParams(window.location.search).get('coor') === '1') ? 1 : 0;
                    d.university = $('#university').val();
                },
                beforeSend: function() {
                    $('#spinner').show();
                    $('#filterButton').attr('disabled', true);
                },
                complete: function() {
                    $('#spinner').hide();
                    $('#filterButton').attr('disabled', false);
                },
                // Handle Error (Termasuk Throttle 429)
                error: function (xhr, error, code) {
                    handleAjaxError(xhr, "Failed to load data table");
                    $('#basic-datatables_processing').hide();
                    $('#spinner').hide();
                    $('#filterButton').attr('disabled', false);
                }
            },
            columns: [
                { data: 'no' },
                { data: 'nama_kampus' },
                { data: 'kode_prodi' },
                { data: 'study_program' },
                { data: 'major' },
                { data: 'stats' },
                { data: 'aksi', orderable: false, searchable: false }
            ]
        });

        // Filter Submit
        $('#form_filter').on('submit', function(e) {
            e.preventDefault();
            table.ajax.reload(); 
        });
    });

    // =========================================================================
    // 2. HELPER: RELOAD TABLE (PRESERVE PAGE)
    // =========================================================================
    function reload_table_preserve_page() {
        const currentPage = table.page();
        
        table.ajax.reload(function() {
            // Logika agar tidak error jika data di halaman terakhir habis
            const pageSize = table.page.info().length;
            const totalRecords = table.page.info().recordsDisplay;
            const totalPages = Math.ceil(totalRecords / pageSize);
            
            let goToPage = currentPage;
            if (totalRecords > 0 && currentPage >= totalPages) {
                goToPage = totalPages - 1;
            }
            
            table.page(goToPage).draw('page');
        }, false);
    }

    // =========================================================================
    // 3. DELETE FUNCTION
    // =========================================================================
    function delete_data(id) {
        Swal.fire({
            title: 'Delete Confirmation',
            text: 'Are you sure you want to delete current Study Program?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                
                // Loading State
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while data is being deleted.',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    // Gunakan Helper Router (Sesuaikan nama route Anda)
                    url: "index.php?form=study_program&aksi=delete",
                    type: "POST",
                    data: {
                        _token: _token,
                        _method: "DELETE", // Method Spoofing untuk Router::delete
                        id: id // Pastikan di backend menerima $_POST['id']
                    },
                    dataType: 'json',
                    success: function(response) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: response.message || 'Study Program successfully deleted.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        reload_table_preserve_page();
                    },
                    // Menggunakan Global Error Handler (Otomatis handle 429)
                    error: function(xhr) {
                        handleAjaxError(xhr, 'Failed to delete Study Program.');
                    }
                });
            }
        });
    }

</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('templates.cdc', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon2\www\pblKita\views/cdc/study_program_management/index.blade.php ENDPATH**/ ?>