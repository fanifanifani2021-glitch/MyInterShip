

<?php $__env->startPush('head-scripts'); ?>

<!-- Datatables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

<!-- CSS Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">

<!-- DataTables Buttons CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">

<style>
    .dt-buttons {
        display: none;
    }

</style>


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

// Menyiapkan query untuk mendapatkan distinct major dari program_study berdasarkan id_kampus
$sql_major = "SELECT DISTINCT major FROM program_study WHERE id_kampus = :id_kampus";
$stmt_major = $pdo->prepare($sql_major);

// Mengikat parameter :id_kampus ke nilai $id_kampus (gunakan kembali variabel yang sudah ada)
$stmt_major->bindParam(':id_kampus', $id_kampus, PDO::PARAM_STR);

// Eksekusi statement untuk mendapatkan hasil
$stmt_major->execute();

// Mengumpulkan hasil major sebagai array
$majors = $stmt_major->fetchAll(PDO::FETCH_COLUMN);

// Ambil daftar jurusan (major)
$sql = "SELECT DISTINCT(major) FROM program_study WHERE id_kampus = :id_kampus ORDER BY major";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id_kampus', $id_kampus, PDO::PARAM_STR);
$stmt->execute();

$daftar_major = [];

while ($data_jurusan = $stmt->fetch(PDO::FETCH_ASSOC)) {
$jurusan = $data_jurusan['major'];

// Ambil daftar program studi untuk setiap jurusan
$sql2 = "SELECT * FROM program_study WHERE major = :jurusan AND id_kampus = :id_kampus ORDER BY jenjang";
$stmt2 = $pdo->prepare($sql2);
$stmt2->bindParam(':jurusan', $jurusan, PDO::PARAM_STR);
$stmt2->bindParam(':id_kampus', $id_kampus, PDO::PARAM_STR);
$stmt2->execute();

$daftar_prodi = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// Simpan ke array besar
$daftar_major[] = [
'major' => $jurusan,
'programs' => $daftar_prodi
];
}

$sql3 = "SELECT * FROM tb_kampus";
$stmt3 = $pdo->prepare($sql3);
$stmt3->execute();

$daftar_kampus = $stmt3->fetchAll(PDO::FETCH_ASSOC);
?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="container">

    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Student Management</h2>
                    <h5 class="text-white op-7 mb-2">Student Management for <?= $nama_kampus ?></h5>



                </div>
                <div class="ml-md-auto py-2 py-md-0">
                    <a aria-label="Import Student" href="<?= base_url('index.php?page=import_student_intern') ?>" role="button" class="btn btn-primary btn-round"><i class="fa fa-file-import mr-2"></i> Import</a>
                    <a aria-label="Add New Student" href="<?= base_url('index.php?page=add_student_intern') ?>" role="button" class="btn btn-primary btn-round"><i class="fa fa-plus mr-2"></i> Add Student</a>
                </div>

            </div>
        </div>
    </div>



    <div class="page-inner mt--5">


        <div class="row mt--2">
            <div class="col-md-12">

                <!-- Filter -->
                <div class="card">
                    <form id="form_filter"> <?= csrf() ?> <?= input_hidden('id_kampus', $_SESSION['CDC_Data']['id_kampus']) ?>
                        <div class="card-body form-row">


                            <div class="form-group col-md-4">
                                <label>Major</label>
                                <select id="major" name="major" class="form-control">
                                    <option value="All">All Major</option>

                                    <?php $__currentLoopData = $daftar_major; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e(htmlspecialchars($group['major'])); ?>">
                                        <?php echo e(htmlspecialchars($group['major'])); ?>

                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>

                                <small class="invalid-feedback text-sm text-danger">Choose one</small>
                            </div>


                            <div class="form-group col-md-4">
                                <label>Study Program</label>

                                <select id="program_study" name="program_study" class="form-control">
                                    <option value="">- Select Study Program -</option>
                                    <option value="All">All Study Program</option>

                                    <?php $__currentLoopData = $daftar_major; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <optgroup label="<?php echo e(htmlspecialchars($group['major'])); ?>" id="<?php echo e('group-' . htmlspecialchars($group['major'])); ?>">
                                        <option value="All-<?php echo e(htmlspecialchars($group['major'])); ?>">
                                            All Study Program in <?php echo e(htmlspecialchars($group['major'])); ?>

                                        </option>

                                        <?php $__currentLoopData = $group['programs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prodi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e(htmlspecialchars($prodi['kode_prodi'])); ?>">
                                            <?php echo e(htmlspecialchars($prodi['jenjang'])); ?> <?php echo e(htmlspecialchars($prodi['prodi'])); ?> -
                                            (<?php echo e(htmlspecialchars($prodi['jenjang'])); ?> <?php echo e(htmlspecialchars($prodi['study_program'])); ?>)
                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </optgroup>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>

                                <?= form_error('program_study') ?>

                            </div>


                            <div class="form-group col-md-4">
                                <label>Generation</label>


                                <select id="generation" name="generation" class="form-control">
                                    <option value="All">All Generation</option>
                                    <?php for($year = 2017; $year <= date('Y') + 1; $year++): ?> <option value="<?php echo e(substr($year, 2, 3)); ?>">Generation <?php echo e($year); ?></option>
                                        <?php endfor; ?>
                                </select>

                                <?= form_error('generation') ?>

                            </div>




                        </div>
                        <div class="card-footer">

                            <button type="submit" class="btn btn-primary" id="filterButton">
                                <i class="fas fa-filter mr-1"></i>Filter
                                <span id="spinner" class="fas fa-spin fa-spinner text-light ml-1" role="status" aria-hidden="true" style="display:none;"></span>
                            </button>

                            <button type="button" class="btn btn-secondary ml-2" id="resetButton">
                                <i class="fas fa-undo mr-1"></i>Reset
                            </button>

                        </div>


                    </form>

                </div>

                <!-- Modal Pilih Kampus -->
                <div class="modal fade" id="modalSelectKampus" tabindex="-1" role="dialog" aria-labelledby="selectKampusLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title" id="selectKampusLabel">Assign Lecturer</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <form id="form_pilih_kampus">
                                <div class="modal-body">
                                    <?= csrf() ?>
                                    <input type="hidden" id="assign_supervisor_nim" name="nim" value="">

                                    <div class="form-group">
                                        <label for="select_kampus">Choose Campus</label>
                                        <select id="select_kampus" name="id_kampus" class="form-control" required onchange="loadLecturerOptions(this.value)">
                                            <option value="" selected>- Select Campus -</option>
                                            <?php $__currentLoopData = $daftar_kampus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kampus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($kampus['id_kampus']); ?>" <?php if($id_kampus==$kampus['id_kampus']): ?> selected <?php endif; ?>><?php echo e($kampus['nama_kampus']); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>


                                    <div class="form-group">
                                        <label for="select_lecturer">Choose Lecturer</label>
                                        <select id="select_lecturer" name="nik_dospem" class="form-control" required>
                                            <option value="" selected>- Select Lecturer -</option>
                                            <!-- Akan diisi berdasarkan kampus -->
                                        </select>
                                    </div>


                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>


                <div class="modal fade" id="exportFormatModal" tabindex="-1" role="dialog" aria-labelledby="exportFormatLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exportFormatLabel">Choose Export Format</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body text-center">
                                <p>Please select the format you want to export the data in:</p>
                                <button class="btn btn-outline-primary mr-2" id="exportAsCSV">
                                    CSV
                                </button>
                                <button class="btn btn-outline-success" id="exportAsXLSX">
                                    XLSX
                                </button>

                                <div id="exportLoadingSpinner" style="display: none; text-align: center; margin-top: 15px;">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                    <p>Please wait, exporting data...</p>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>


                <div class="card" id="card_datatable">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="m-0">Student Data</h4>

                        <button id="btnExportFullExcel" class="btn btn-info btn-sm d-none" data-toggle="modal" data-target="#exportFormatModal">
                            <i class="fa fa-file-export"></i> Export Data
                        </button>

                    </div>


                    <div class="card-body">
                        <table id="basic-datatables" class="table table-bordered table-striped">
                            <thead>
                                <tr role="row">
                                    <th>No.</th>
                                    <th>Program Study</th>
                                    <th>Generation</th>
                                    <th>NIM</th>
                                    <th>Name</th>
                                    <th>Lecturer</th>
                                    <th>Latest Internship</th>
                                    <th>Internship Date</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('footer-scripts'); ?>

<!-- Datatables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>


<!-- DataTables Buttons JS -->
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>

<!-- JSZip (required for Excel export) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

<!-- JS Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

<script>
    const _token = $('meta[name="csrf-token"]').attr('content');

    function assign_supervisor(nim, nikDospem = '', idKampus = '') {
        $('#assign_supervisor_nim').val(nim);
        $('#select_kampus').val(idKampus).trigger('change');

        // Simpan nikDospem ke atribut untuk dipakai setelah AJAX selesai
        $('#select_lecturer').attr('data-current-dospem', nikDospem);

        $('#modalSelectKampus').modal('show');
    }


    function loadLecturerOptions(kampusId) {
        const $lecturer = $('#select_lecturer');
        const $kampus = $('#select_kampus');

        // Disable kedua dropdown selama proses berjalan
        $kampus.prop('disabled', true);
        $lecturer.prop('disabled', true);

        // Hanya destroy jika select2 sudah aktif
        if ($lecturer.hasClass("select2-hidden-accessible")) {
            $lecturer.select2('destroy');
        }

        $lecturer.html('<option disabled selected>Loading...</option>');

        $.ajax({
            url: 'index.php?request=getLectureByCampus'
            , type: 'POST'
            , data: {
                id_kampus: kampusId
                , _token
            }
            , success: function(res) {
                let lecturers = [];
                try {
                    lecturers = JSON.parse(res);
                } catch (e) {
                    console.error('Invalid JSON response');
                    $lecturer.html('<option disabled selected>Error parsing data</option>');
                    return;
                }

                // let options = '<option value="" disabled selected>- Select Lecturer -</option>';
                let options = '<option value="Unassigned" selected>- Unassigned -</option>';
                lecturers.forEach(d => {
                    options += `<option value="${d.nim_nik_unit}">${d.name}</option>`;
                });

                $lecturer.html(options);
            }
            , error: function() {
                $lecturer.html('<option disabled selected>Error loading lecturers</option>');
            }
            , complete: function() {
                $kampus.prop('disabled', false);
                $lecturer.prop('disabled', false);

                $lecturer.select2({
                    theme: 'bootstrap4'
                    , width: '100%'
                    , dropdownParent: $('#modalSelectKampus')
                });

                // Pilih kembali dospem jika ada (yang tersimpan di atribut)
                const currentDospem = $lecturer.attr('data-current-dospem');
                if (currentDospem && currentDospem !== '0' && currentDospem !== '-') {
                    $lecturer.val(currentDospem).trigger('change');
                } else {
                    $lecturer.val('Unassigned').trigger('change');
                }
            }

        });
    }


    $(document).ready(function() {

        $('#select_kampus').select2({
            theme: 'bootstrap4'
            , width: '100%'
            , dropdownParent: $('#modalSelectKampus')
        });


        $('#select_lecturer').select2({
            theme: 'bootstrap4'
            , width: '100%'
            , dropdownParent: $('#modalSelectKampus') // << ini penting!
        });


        $('#major, #program_study, #generation').select2({
            theme: 'bootstrap4'
            , width: '100%'
        });

        $('#resetButton').on('click', function() {
            $('#major').val('All').trigger('change');
            $('#program_study').val('').trigger('change');
            $('#generation').val('All').trigger('change');
        });

        const allProgramStudyOptions = $('#program_study').html();

        $('#major').on('change', function() {
            const selectedMajor = $(this).val();
            const $programStudy = $('#program_study');

            if (selectedMajor === 'All') {
                $programStudy.html(allProgramStudyOptions);
            } else {
                const tempSelect = $('<select>' + allProgramStudyOptions + '</select>');
                const matchingGroup = tempSelect.find(`optgroup[label="${selectedMajor}"]`).clone();

                const defaultOption = $('<option>', {
                    value: ''
                    , text: '- Select Study Program -'
                    , disabled: true
                    , selected: true
                });

                const allOption = $('<option>', {
                    value: 'All-' + selectedMajor
                    , text: 'All Study Program in ' + selectedMajor
                });

                $programStudy.html('').append(defaultOption, allOption, matchingGroup);
            }

            $programStudy.select2('destroy').select2({
                theme: 'bootstrap4'
                , width: '100%'
            });
        });

        $('#major').trigger('change');

        const table = $('#basic-datatables').DataTable({
            responsive: true
            , pageLength: 15
            , lengthMenu: [10, 15, 25, 50]
            , serverSide: true
            , processing: true
            , dom: 'Blfrtip'
            , ajax: {
                url: 'index.php?request=datatable&data=student_internship_data_cdc'
                , type: 'POST'
                , data: function(d) {
                    d._token = _token;
                    d.coor = (new URLSearchParams(window.location.search).get('coor') === '1') ? 1 : 0;
                    d.id_kampus = $('input[name="id_kampus"]').val();
                    d.major = $('#major').val();
                    d.program_study = $('#program_study').val();
                    d.generation = $('#generation').val();
                }
                , beforeSend: function() {
                    $('#spinner').show();
                    $('#filterButton').attr('disabled', true);
                }
                , complete: function() {
                    $('#spinner').hide();
                    $('#filterButton').attr('disabled', false);
                }
            }
            , columns: [{
                    data: 'no'
                }
                , {
                    data: 'program_study'
                }
                , {
                    data: 'generation'
                }
                , {
                    data: 'nim'
                }
                , {
                    data: 'nama'
                }
                , {
                    data: 'dosen'
                }
                , {
                    data: 'status'
                }
                , {
                    data: 'tanggal'
                }
                , {
                    data: 'aksi'
                }
            ]
        });

        table.on('xhr', function() {
            const json = table.ajax.json();
            if (json.recordsFiltered > 0) {
                $('#btnExportFullExcel').removeClass('d-none').show();
            } else {
                $('#btnExportFullExcel').addClass('d-none').hide();
            }
        });

        function triggerExport(format) {
            $('#exportLoadingSpinner').show();

            $.ajax({
                url: `index.php?request=datatable&data=student_internship_data_cdc&action=export&format=${format}`
                , type: 'POST'
                , data: {
                    _token: _token
                    , coor: (new URLSearchParams(window.location.search).get('coor') === '1') ? 1 : 0
                    , id_kampus: $('input[name="id_kampus"]').val()
                    , major: $('#major').val()
                    , program_study: $('#program_study').val()
                    , generation: $('#generation').val()
                    , ajax: true
                }
                , xhrFields: {
                    responseType: 'blob'
                }
                , success: function(blob, status, xhr) {
                    let disposition = xhr.getResponseHeader('Content-Disposition');
                    let filename = format === 'xlsx' ? 'export.xlsx' : 'export.csv';

                    if (disposition && disposition.includes('filename=')) {
                        filename = disposition.split('filename=')[1].replace(/["']/g, '').trim();
                    }

                    const link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = filename;
                    link.click();

                    $('#exportLoadingSpinner').hide();
                }
                , error: function() {
                    alert('Export failed. Please try again.');
                    $('#exportLoadingSpinner').hide();
                }
            });
        }

        $('#exportAsCSV').on('click', () => triggerExport('csv'));
        $('#exportAsXLSX').on('click', () => triggerExport('xlsx'));

        $('#form_filter').on('submit', function(e) {
            e.preventDefault();
            table.ajax.reload();
        });

        $('#form_pilih_kampus').on('submit', function(e) {
            e.preventDefault();

            const formData = $(this).serialize();

            console.log(formData);

            Swal.fire({
                title: 'Processing...'
                , text: 'Please wait while assigning supervisor.'
                , allowOutsideClick: false
                , didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: 'index.php?request=assignSupervisor'
                , type: 'POST'
                , data: formData
                , success: function() {
                    Swal.fire({
                        title: 'Success'
                        , text: 'Supervisor assigned successfully!'
                        , icon: 'success'
                        , timer: 2500
                    });
                    $('#modalSelectKampus').modal('hide');
                    $('#form_pilih_kampus')[0].reset();
                    $('#select_lecturer').html('<option value="" disabled selected>- Select Lecturer -</option>').trigger('change');
                    reload_table_preserve_page();
                }
                , error: function(res) {
                    console.log(res);
                    Swal.fire('Error', 'Failed to assign supervisor. Please try again later.', 'error');
                }
            });
        });
    });

    function generateVerificationText(length = 6) {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        let result = '';
        for (let i = 0; i < length; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return result;
    }

    function swal_confirm(id) {
        const verificationText = generateVerificationText();

        Swal.fire({
            title: 'Delete Confirmation'
            , html: `
            <p>This action is <b>irreversible</b>.</p>
            <p>Type <code>${verificationText}</code> below to confirm deletion:</p>
            <input type="text" id="confirmText" class="swal2-input" placeholder="Type here..." autocomplete="off" style="text-align:center;">
        `
            , icon: 'warning'
            , showCancelButton: true
            , confirmButtonText: 'Confirm Delete'
            , cancelButtonText: 'Cancel'
            , preConfirm: () => {
                const typed = document.getElementById('confirmText').value;
                if (typed !== verificationText) {
                    Swal.showValidationMessage('Verification text does not match.');
                }
                return typed;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...'
                    , text: 'Please wait while data is being deleted.'
                    , allowOutsideClick: false
                    , didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: 'index.php?request=removeStudent'
                    , type: 'POST'
                    , data: {
                        token: _token
                        , '_method': 'DELETE'
                        , nim: id
                    }
                    , success: function() {
                        Swal.fire('Success', 'Student account deleted successfully!', 'success');
                        reload_table_preserve_page();
                    }
                    , error: function() {
                        Swal.fire('Deleting Failed!', 'Please try again later.', 'error');
                    }
                });
            }
        });
    }

    function reset_password(id) {
        Swal.fire({
            title: 'Reset Confirmation'
            , text: 'Are you sure you want to reset the password for this student?'
            , icon: 'question'
            , showCancelButton: true
            , confirmButtonColor: '#DD6B55'
            , confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...'
                    , text: 'Please wait while password is being resetted.'
                    , allowOutsideClick: false
                    , didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: 'index.php?request=resetStudentPassword'
                    , type: 'POST'
                    , data: {
                        token: _token
                        , nim: id
                    }
                    , success: function() {
                        Swal.fire('Success', 'Password has been reset successfully!', 'success');
                        reload_table_preserve_page();
                    }
                    , error: function() {
                        Swal.fire('Reset Password Failed', 'Please try again later!', 'error');
                    }
                });
            }
        });
    }

    function reload_table_preserve_page() {
        const table = $('#basic-datatables').DataTable();
        const currentPage = table.page();
        const pageSize = table.page.info().length;

        table.ajax.reload(function() {
            const totalRecords = table.page.info().recordsDisplay;
            const totalPages = Math.ceil(totalRecords / pageSize);
            const goToPage = currentPage >= totalPages ? totalPages - 1 : currentPage;
            table.page(goToPage).draw('page');
        }, false);
    }

</script>




<?php if($_GET['show'] == 1): ?> :
<script type='text/javascript'>
    $(document).ready(function() {
        $('#program_study').val("<?php echo e($_GET['prodi'] ?? 'IF'); ?>");
        $('#major').val("<?php echo e($_GET['major'] ?? 'Informatics Engineering'); ?>");
        $('#form_filter').submit();
    });

</script>
<?php endif; ?>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('templates.cdc', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp_new\htdocs\pblKita\views/cdc/student_management/index.blade.php ENDPATH**/ ?>