<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Companies Ranking - Received Student</title>

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

    <div class="container mt-4">

        <!-- Dropdown Tahun + Tombol Back -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center">
                <a href="index.php" class="btn btn-secondary btn-sm me-3">
                    ← Back
                </a>
            </div>
        </div>


        <!-- FILTER SECTION -->
        <div class="card shadow-sm mb-4" style="border-radius: 6px; border: 1px solid #e5e7eb;">

            <div class="p-3 pb-0">
                <h5 class="fw-bold mb-3" style="color:#2b2b2b;">Filter Data Mahasiswa Magang</h5>
            </div>

            <form id="rankingFilterForm" method="GET" action="index.php">
                <input type="hidden" name="page" value="show_data">
                <input type="hidden" name="action" value="companies_ranking_received_student">

                <div class="p-3 pt-0">

                    <div class="row gy-3">

                        <!-- Start Date -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Start Date</label>
                            <input type="date" id="filter_start_date" name="start_date" class="form-control"
                                value="<?= $start_date ?>">
                        </div>

                        <!-- End Date -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">End Date</label>
                            <input type="date" id="filter_end_date" name="end_date" class="form-control"
                                value="<?= $end_date ?>">
                        </div>

                        <!-- Major -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Major</label>
                            <select id="filter_major" name="major" class="form-select" selectedMajor="{{ $major }}">
                                <option value="">— All Majors —</option>
                            </select>
                        </div>

                        <!-- Program Study -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Study Program</label>
                            <select id="filter_prodi" name="prodi" class="form-select" selectedProdi="{{ $prodi }}">
                                <option value="">— All Study Programs —</option>
                            </select>
                        </div>

                    </div>

                    <!-- Button -->
                    <div class="mt-3 text-end">
                        <button type="button" class="btn btn-primary px-4" onclick="submitRankingFilter()">
                            Filter
                        </button>
                    </div>

                </div>

            </form>

        </div>



        <!-- RANKING TABLE SECTION -->
        <div class="card shadow-sm p-3 mb-4" style="border-radius: 6px; border: 1px solid #e5e7eb;">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0" style="color:#2b2b2b;">
                    Top 10 Perusahaan Penerima Mahasiswa Magang
                </h5>
            </div>

            <table id="ranking-table" class="table table-bordered table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width:80px; text-align:center;">Ranking</th>
                        <th>Perusahaan</th>
                        <th style="width:180px; text-align:center;">Jumlah Mahasiswa</th>
                        <th style="width:150px; text-align:center;">Mahasiswa</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($data_stat['company_trophies'] as $row): ?>
                    <tr>
                        <td class="text-center fw-bold"><?= $row['ranking'] ?></td>
                        <td><?= htmlspecialchars($row['name_company']) ?></td>
                        <td class="text-center"><?= $row['jumlah_mahasiswa'] ?> Mahasiswa</td>
                        <td class="text-center">
                            <button class="btn btn-info btn-sm" data-id="<?= $row['id_company'] ?>"
                                data-major="<?= $major ?>" data-prodi="<?= $prodi ?>"
                                data-start_date="{{ $start_date }}" data-end_date="{{ $end_date }}"
                                data-company="<?= htmlspecialchars($row['name_company']) ?>"
                                data-ranking="<?= $row['ranking'] ?>">
                                Lihat Semua
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>


        <!-- Wrapper untuk daftar mahasiswa -->
        <div id="student-wrapper" class="mt-4" style="display: none;">
            <h5 id="student-info-title">Daftar Mahasiswa Magang, Perusahaan : ?? Periode : ??</h5>

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


    <script>
    function submitRankingFilter() {
        document.getElementById("rankingFilterForm").submit();
    }

    // ---- DATA dari PHP ----
    let programStudy = @json($program_study);

    // ---- GROUP PRODI BY MAJOR ----
    let groupedProdi = {};
    programStudy.forEach(p => {
        if (!groupedProdi[p.major]) groupedProdi[p.major] = [];
        groupedProdi[p.major].push(p);
    });

    // ---- GENERATE DROPDOWN MAJOR ----
    let majorSelect = document.getElementById('filter_major');
    Object.keys(groupedProdi).forEach(major => {
        majorSelect.innerHTML += `<option value="${major}">${major}</option>`;
    });

    // ---- GENERATE DROPDOWN PRODI ----
    let prodiSelect = document.getElementById('filter_prodi');

    function generateProdiDropdown(selectedMajor = "") {
        prodiSelect.innerHTML = `<option value="">— All Study Programs —</option>`;

        // SHOW ALL MAJOR
        if (selectedMajor === "") {
            Object.keys(groupedProdi).forEach(major => {
                prodiSelect.innerHTML += `<optgroup label="${major}">`;
                prodiSelect.innerHTML += `<option value="MAJOR:${major}">All Program Study in ${major}</option>`;

                groupedProdi[major].forEach(p => {
                    prodiSelect.innerHTML += `
                        <option value="PS:${p.kode_prodi}">
                            ${p.jenjang} - ${p.study_program}
                        </option>`;
                });

                prodiSelect.innerHTML += `</optgroup>`;
            });
            return;
        }

        // SHOW SPECIFIC MAJOR
        prodiSelect.innerHTML += `<optgroup label="${selectedMajor}">`;
        prodiSelect.innerHTML += `<option value="MAJOR:${selectedMajor}">All Program Study in ${selectedMajor}</option>`;

        groupedProdi[selectedMajor].forEach(p => {
            prodiSelect.innerHTML += `
                <option value="PS:${p.kode_prodi}">
                    ${p.jenjang} - ${p.study_program}
                </option>`;
        });

        prodiSelect.innerHTML += `</optgroup>`;
    }

    // ---- AMBIL SELECTED DARI ATTRIBUTE ----
    let selectedMajor = $('#filter_major').attr('selectedMajor');  // Contoh: "Informatics Engineering"
    let selectedProdi = $('#filter_prodi').attr('selectedProdi');  // Contoh: "PS:IF" atau "MAJOR:Informatics Engineering"

    // ---- LOAD AWAL (berdasarkan GET) ----
    if (selectedMajor && selectedMajor !== "") {
        $('#filter_major').val(selectedMajor);
        generateProdiDropdown(selectedMajor);
    } else {
        generateProdiDropdown();
    }

    // Set selected Prodi (PS / MAJOR)
    if (selectedProdi && selectedProdi !== "") {
        $('#filter_prodi').val(selectedProdi);
    }

    // ---- EVENT CHANGE MAJOR ----
    $('#filter_major').on('change', function () {
        generateProdiDropdown($(this).val());
        $('#filter_prodi').val(""); // reset pilihan prodi saat ganti major
    });

</script>




    <script>
        let studentTable = null;

        function initStudentTable(title, rawCompanyName, start_date, end_date) {
            if (studentTable) {
                studentTable.destroy();
            }

            // Sanitasi nama file: hilangkan karakter tidak aman
            const safeCompany = rawCompanyName
                    .replace(/[^a-z0-9]/gi, '_')   // karakter tidak aman → _
                    .replace(/_+/g, '_')           // rapihkan underscore berlebihan
                    .replace(/^_+|_+$/g, '')       // hapus underscore di awal/akhir
                    .toLowerCase();

            // Sanitasi tanggal: ganti "-" jadi "_"
            const safeStart = start_date.replace(/-/g, '_');
            const safeEnd = end_date.replace(/-/g, '_');

            // Filename final sesuai permintaan
            const safeFilename = `magang_${safeStart}_to_${safeEnd}_${safeCompany}`;
            
            studentTable = $('#student-table').DataTable({
                responsive: true,
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                    title: null,
                    filename: safeFilename,
                    customize: function (xlsx) {
                        const sheet = xlsx.xl.worksheets['sheet1.xml'];
                        const contextRow = `
                    <row r="1">
                        <c t="inlineStr" r="A1"><is><t>${title}</t></is></c>
                    </row>
                `;

                        const rows = $('row', sheet);
                        rows.each(function () {
                            const r = parseInt($(this).attr('r'), 10);
                            $(this).attr('r', r + 1);
                            $('c', this).each(function () {
                                const cellRef = $(this).attr('r');
                                const col = cellRef.replace(/[0-9]/g, '');
                                const rowNum = parseInt(cellRef.replace(/[A-Z]/g,
                                    ''), 10);
                                $(this).attr('r', `${col}${rowNum + 1}`);
                            });
                        });

                        sheet.childNodes[0].childNodes[1].innerHTML =
                            contextRow + sheet.childNodes[0].childNodes[1].innerHTML;
                    }
                }],
                responsive: true,
                destroy: true,
                lengthChange: false,
                paging: true
            });
        }

        $(document).ready(function () {
            $('#ranking-table').DataTable({
                responsive: true,
            });


            // Tombol lihat mahasiswa
            $(document).on('click', 'button[data-id]', function () {
                const button = $(this);

                const id_company = button.data('id');
                const start_date = button.data('start_date');
                const end_date = button.data('end_date');
                const major = button.data('major');
                const prodi = button.data('prodi');
                const companyName = button.data('company');
                const ranking = button.data('ranking');

                // Simpan teks asli tombol dan ganti dengan spinner
                const originalText = button.html();
                button.prop('disabled', true).html(
                    `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memuat...`
                    );

                $.ajax({
                    url: 'index.php?api=companies_ranking_received_student',
                    method: 'POST',
                    data: {
                        id_company: id_company,
                        start_date: start_date,
                        end_date: end_date,
                        major: major,
                        prodi: prodi,
                        token: '<?= env(APP_TOKEN) ?? "" ?>' // Ganti sesuai kebutuhan
                    },
                    dataType: 'json',
                    success: function (response) {
                            const infoText =
                                `Daftar Mahasiswa Magang, Perusahaan: ${companyName} | Periode: ${start_date} - ${end_date} | Peringkat ke-${ranking}`;
                            $('#student-info-title').text(infoText);
                            $('#student-wrapper').slideDown();

                            if (studentTable && $.fn.DataTable.isDataTable('#student-table')) {
                                studentTable.clear(); // bersihkan datanya
                            } else {
                                $('#student-table tbody').empty(); // fallback
                            }

                            if (Array.isArray(response) && response.length > 0) {
                                response.forEach(row => {
                                    const rowData = [
                                        row.nim, row.student_name, row.jurusan,
                                        `${row.jenjang} ${row.prodi}`, row
                                        .start_date, row.end_date, row.status
                                    ];

                                    if (studentTable) {
                                        studentTable.row.add(
                                        rowData); // tambahkan ke DataTable
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

                                if (studentTable) {
                                    studentTable.draw(); // render ulang
                                }
                            } else {
                                $('#student-table tbody').append(`
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada data mahasiswa ditemukan.</td>
                                    </tr>
                                `);
                            }

                            // Inisialisasi ulang (opsional jika pertama kali)
                            initStudentTable(infoText, companyName, start_date, end_date);
                        }

                        ,
                    error: function (xhr, status, error) {
                        console.error("❌ Terjadi kesalahan:", status, error);
                        console.log("Response Text:", xhr.responseText);
                    },
                    complete: function () {
                        // Kembalikan tombol ke kondisi semula
                        button.prop('disabled', false).html(originalText);
                    }
                });
            });

        });
    </script>

</body>

</html>