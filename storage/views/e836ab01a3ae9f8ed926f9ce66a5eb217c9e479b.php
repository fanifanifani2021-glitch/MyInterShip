<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="<?= csrf_token() ?>" />

    <title>Companies Ranking - by Major</title>

    <!-- Bootstrap CSS (versi 5.x) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables CSS (opsional, untuk interaktif tabel) -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- DataTables Buttons (CSS & JS) -->
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">

    <style>
        #student-wrapper {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            background-color: #f9f9f9;
        }

        #student-wrapper h5 {
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
        }

        #student-table th {
            background-color: #f1f1f1;
            text-align: center;
        }

        #student-table td {
            vertical-align: middle;
        }

    </style>


</head>

<body>
    <?php
    $year_now = date('Y');
    $selected_year = isset($_GET['year']) ? (int)$_GET['year'] : $year_now;
    ?>

    <div class="container mt-4">

        <!-- Dropdown Tahun + Tombol Back -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center">
                <a href="index.php" class="btn btn-secondary btn-sm me-3">
                    ← Back
                </a>
                <h4 class="mb-0">Top 10 Perusahaan Penerima Mahasiswa Magang berdasarkan Jurusan : <?php echo e($major); ?></h4>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                <div class="d-flex align-items-center">
                    <label for="year-selector" class="me-2 mb-0">Tahun:</label>
                    <select id="year-selector" class="form-select form-select-sm w-auto">
                        <?php for ($y = 2021; $y <= $year_now; $y++): ?>
                        <option value="<?= $y ?>" <?= $y === $selected_year ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="d-flex align-items-center">
                    <label for="major-selector" class="me-2 mb-0">Jurusan:</label>
                    <select id="major-selector" class="form-select form-select-sm w-auto">
                        <option value="" hidden>- Pilih Jurusan -</option>
                        <?php foreach ($majors as $m): ?>
                        <option value="<?= htmlspecialchars($m) ?>" <?= ($m === $major) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m) ?>
                        </option>
                        <?php endforeach; ?>

                    </select>

                </div>
            </div>

        </div>

        <div class="d-flex justify-content-end mb-3">
            <button id="download-all-btn" class="btn btn-success">
                Unduh Semua Data
            </button>
        </div>



        <!-- Tabel Ranking -->
        <table id="ranking-table" class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>Ranking</th>
                    <th>Perusahaan</th>
                    <th>Jumlah Mahasiswa</th>
                    <th>Mahasiswa</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data_stat['major_favorites'] as $row): ?>
                <tr>
                    <td><?= $row['ranking'] ?></td>
                    <td><?= htmlspecialchars($row['name_company']) ?></td>
                    <td><?= $row['jumlah_mahasiswa'] ?> Mahasiswa</td>
                    <td>
                        <button class="btn btn-info btn-sm" data-id="<?= $row['id_company'] ?>" data-year="<?= $row['tahun'] ?>" data-company="<?= htmlspecialchars($row['name_company']) ?>" data-ranking="<?= $row['ranking'] ?>" data-jurusan="<?= $row['jurusan'] ?>">
                            Lihat Semua Mahasiswa
                        </button>


                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Wrapper untuk daftar mahasiswa -->
        <div id="student-wrapper" class="mt-4" style="display: none;">
            <h5 id="student-info-title">Daftar Mahasiswa Magang, Perusahaan : ?? Periode : ?? Jurusan : ??</h5>
            <table id="student-table" class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Jurusan</th>
                        <th>Prodi</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>




    <!-- jQuery (dibutuhkan untuk DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Ambil CSRF token dari meta tag
        let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let currentMajor = document.getElementById('major-selector').value;

        $('#download-all-btn').click(function() {
            Swal.fire({
                title: 'Mohon Tunggu'
                , text: 'Sedang menyiapkan file...'
                , allowOutsideClick: false
                , didOpen: () => {
                    Swal.showLoading()
                }
            });

            $.ajax({
                url: 'index.php?request=show_data&action=companies_ranking_major_all'
                , method: 'POST'
                , data: {
                    token: csrfToken,
                    major: currentMajor,
                }
                , dataType: 'json'
                , success: function(response) {
                    console.log(response);
                    // window.location.href = response.file_url;
                    Swal.fire({
                        icon: 'success'
                        , title: 'Berhasil!'
                        , text: 'File berhasil dibuat.'
                        , showCancelButton: true
                        , confirmButtonText: 'Unduh Sekarang'
                        , cancelButtonText: 'Tutup'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = response.file_url;
                        }
                    });
                }
                , error: function(response) {
                    console.log( response );
                    Swal.fire({
                        icon: 'error'
                        , title: 'Gagal'
                        , text: 'Gagal men-generate file.'
                    });
                }
            });
        });

    </script>



    <script>
        let studentTable = null;

        function initStudentTable(title, rawCompanyName, rawYear, rawMajor) {
            if (studentTable) {
                studentTable.destroy();
            }

            // Sanitasi nama file: hilangkan karakter tidak aman
            const safeCompany = rawCompanyName.replace(/[^a-z0-9]/gi, '_').toLowerCase();
            const safeMajor = rawMajor.replace(/[^a-z0-9]/gi, '_').toLowerCase();
            const safeYear = rawYear.toString().replace(/[^0-9]/g, '');
            const safeFilename = `magang_${safeCompany}_${safeYear}_${safeMajor}`;

            studentTable = $('#student-table').DataTable({
                dom: 'Bfrtip'
                , buttons: [{
                    extend: 'excelHtml5'
                    , title: null
                    , filename: safeFilename
                    , customize: function(xlsx) {
                        const sheet = xlsx.xl.worksheets['sheet1.xml'];
                        const contextRow = `
                    <row r="1">
                        <c t="inlineStr" r="A1"><is><t>${title}</t></is></c>
                    </row>
                `;

                        const rows = $('row', sheet);
                        rows.each(function() {
                            const r = parseInt($(this).attr('r'), 10);
                            $(this).attr('r', r + 1);
                            $('c', this).each(function() {
                                const cellRef = $(this).attr('r');
                                const col = cellRef.replace(/[0-9]/g, '');
                                const rowNum = parseInt(cellRef.replace(/[A-Z]/g, ''), 10);
                                $(this).attr('r', `${col}${rowNum + 1}`);
                            });
                        });

                        sheet.childNodes[0].childNodes[1].innerHTML =
                            contextRow + sheet.childNodes[0].childNodes[1].innerHTML;
                    }
                }]
                , responsive: true
                , destroy: true
                , lengthChange: false
                , paging: true
            });
        }

        function updateMajorRankingURL() {
            const year = document.getElementById('year-selector').value;
            const major = document.getElementById('major-selector').value;
            const encodedMajor = encodeURIComponent(major);

            const url = `index.php?page=show_data&action=companies_ranking_major&year=${year}&major=${encodedMajor}`;
            window.location.href = url;
        }

        document.getElementById('year-selector').addEventListener('change', updateMajorRankingURL);
        document.getElementById('major-selector').addEventListener('change', updateMajorRankingURL);



        $(document).ready(function() {
            $('#ranking-table').DataTable();

            // Tombol lihat mahasiswa
            $(document).on('click', 'button[data-id]', function() {
                const button = $(this);
                const id_company = button.data('id');
                const year = button.data('year');
                const companyName = button.data('company');
                const ranking = button.data('ranking');
                const jurusan = button.data('jurusan');

                // Simpan teks asli tombol dan ganti dengan spinner
                const originalText = button.html();
                button.prop('disabled', true).html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memuat...`);

                $.ajax({
                    url: 'index.php?api=companies_ranking_major'
                    , method: 'POST'
                    , data: {
                        id_company: id_company
                        , year: year
                        , jurusan: jurusan
                        , token: '<?= env(APP_TOKEN) ?? "" ?>' // Ganti sesuai kebutuhan
                    }
                    , dataType: 'json'
                    , success: function(response) {
                            const infoText = `Daftar Mahasiswa Magang, Perusahaan: ${companyName} | Periode: ${year} | Jurusan: ${jurusan} | Peringkat ke-${ranking}`;
                            $('#student-info-title').html(infoText);
                            $('#student-wrapper').slideDown();

                            // Kosongkan tabel jika sudah diinisialisasi
                            if (studentTable && $.fn.DataTable.isDataTable('#student-table')) {
                                studentTable.clear().draw();
                            } else {
                                $('#student-table tbody').empty();
                            }

                            if (Array.isArray(response) && response.length > 0) {
                                response.forEach(row => {
                                    const rowData = [
                                        row.nim
                                        , row.student_name
                                        , row.jurusan
                                        , `${row.jenjang} ${row.prodi}`
                                        , row.start_date
                                        , row.end_date
                                        , row.status
                                    ];
                                    if (studentTable) {
                                        studentTable.row.add(rowData);
                                    } else {
                                        $('#student-table tbody').append(`
                    <tr>
                        <td>${row.nim}</td>
                        <td>${row.student_name}</td>
                        <td>${row.jurusan}</td>
                        <td>${row.jenjang} ${row.prodi}</td>
                        <td>${row.start_date}</td>
                        <td>${row.end_date}</td>
                        <td>${row.status}</td>
                    </tr>
                `);
                                    }
                                });
                                if (studentTable) studentTable.draw();
                            } else {
                                $('#student-table tbody').append(`
            <tr>
                <td colspan="7" class="text-center">Tidak ada data mahasiswa ditemukan.</td>
            </tr>
        `);
                            }

                            initStudentTable(infoText, companyName, year, jurusan);
                        }

                    , error: function(xhr, status, error) {
                        console.error("❌ Terjadi kesalahan:", status, error);
                        console.log("Response Text:", xhr.responseText);
                    }
                    , complete: function() {
                        // Kembalikan tombol ke kondisi semula
                        button.prop('disabled', false).html(originalText);
                    }
                });
            });

        });

    </script>

</body>
</html>
<?php /**PATH C:\laragon\www\myinternship\views/show_data/companies_ranking_major.blade.php ENDPATH**/ ?>