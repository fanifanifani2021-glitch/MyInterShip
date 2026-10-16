

<?php $__env->startSection('main-content'); ?>

<?php
$id_kampus = $_SESSION['CDC_Data']['id_kampus'];

$data_stat = [];

// Siapkan nilai tahun awal dan akhir
$year_start = 2020;
$year_end = date('Y');


// Statistik Mahasiswa Magang (Top 10 Perusahaan per Tahun)
$sql = "
SELECT *
FROM (
SELECT
YEAR(i.start_date) AS tahun,
c.id_company,
c.name AS name_company,
COUNT(si.nim) AS jumlah_mahasiswa,
ROW_NUMBER() OVER (PARTITION BY YEAR(i.start_date) ORDER BY COUNT(si.nim) DESC) AS ranking
FROM
internship i
JOIN
student_internship si ON si.nim = i.nim
JOIN
company c ON c.id_company = i.id_company
WHERE
si.id_kampus = :id_kampus AND
YEAR(i.start_date) BETWEEN :year_start AND :year_end
AND LOWER(i.status) IN ('yes', 'finish')
GROUP BY
tahun, c.id_company, c.name
) AS ranked
WHERE ranking <= 10 ORDER BY tahun, ranking; ";

$stmt_stat = $_SERVER['PDO']->prepare($sql);
$stmt_stat->bindParam(':id_kampus', $id_kampus, PDO::PARAM_INT);
$stmt_stat->bindParam(':year_start', $year_start, PDO::PARAM_INT);
$stmt_stat->bindParam(':year_end', $year_end, PDO::PARAM_INT);
$stmt_stat->execute();

// Karena datanya banyak baris, gunakan fetchAll
$data_stat['company_trophies'] = $stmt_stat->fetchAll(PDO::FETCH_ASSOC);


$sql = " SELECT * FROM ( SELECT YEAR(i.start_date) AS tahun, ps.major AS jurusan, c.id_company, c.name AS name_company, COUNT(si.nim) AS jumlah_mahasiswa, ROW_NUMBER() OVER ( PARTITION BY YEAR(i.start_date), ps.major ORDER BY COUNT(si.nim) DESC ) AS ranking FROM internship i JOIN student_internship si ON si.nim=i.nim JOIN company c ON c.id_company=i.id_company JOIN program_study ps ON ps.kode_prodi=si.program_study WHERE si.id_kampus=:id_kampus AND YEAR(i.start_date) BETWEEN :year_start AND :year_end AND LOWER(i.status) IN ('yes', 'finish' ) GROUP BY YEAR(i.start_date), ps.major, c.id_company, c.name ) AS ranked WHERE ranking <=5 ORDER BY tahun, jurusan, ranking; ";

$stmt_stat = $_SERVER['PDO']->prepare($sql);
    $stmt_stat->bindParam(':id_kampus', $id_kampus, PDO::PARAM_INT);
    $stmt_stat->bindParam(':year_start', $year_start, PDO::PARAM_INT);
    $stmt_stat->bindParam(':year_end', $year_end, PDO::PARAM_INT);
    $stmt_stat->execute();

    // Karena datanya banyak baris, gunakan fetchAll
    $data_stat['major_favorites'] = $stmt_stat->fetchAll(PDO::FETCH_ASSOC);

    // Query tb_kampus
    $sql_kampus = " SELECT * FROM tb_kampus WHERE id_kampus=:id_kampus"; $stmt_kampus=$_SERVER['PDO']->prepare($sql_kampus);
    $stmt_kampus->bindParam(':id_kampus', $id_kampus, PDO::PARAM_INT);
    $stmt_kampus->execute();
    $data_kampus = $stmt_kampus->fetch(PDO::FETCH_ASSOC);

    // Query Count Kampus
    $sql_count_kampus = "SELECT COUNT(id_kampus) AS Count_Kampus FROM tb_kampus";
    $stmt_count_kampus = $_SERVER['PDO']->prepare($sql_count_kampus);
    $stmt_count_kampus->execute();
    $count_university = $stmt_count_kampus->fetch(PDO::FETCH_ASSOC)['Count_Kampus'];

    // Query Count upkpk
    $sql_count_cdc = "SELECT COUNT(id_upkpk) AS Count_upkpk FROM upkpk";
    $stmt_count_cdc = $_SERVER['PDO']->prepare($sql_count_cdc);
    $stmt_count_cdc->execute();
    $count_cdc = $stmt_count_cdc->fetch(PDO::FETCH_ASSOC)['Count_upkpk'];

    // Query Count user_company
    $sql_count_user_company = "SELECT COUNT(id_user_company) AS Count_user_company FROM user_company";
    $stmt_count_user_company = $_SERVER['PDO']->prepare($sql_count_user_company);
    $stmt_count_user_company->execute();
    $count_user_company = $stmt_count_user_company->fetch(PDO::FETCH_ASSOC)['Count_user_company'];

    // Query Count student_internship
    $sql_count_student = "SELECT * FROM student_internship WHERE id_kampus = :id_kampus";
    $stmt_count_student = $_SERVER['PDO']->prepare($sql_count_student);
    $stmt_count_student->bindParam(':id_kampus', $id_kampus, PDO::PARAM_INT);
    $stmt_count_student->execute();
    $count_student = $stmt_count_student->rowCount();

    // Query Count lecturer
    $sql_count_lecturer = "SELECT * FROM lecturer WHERE id_kampus = :id_kampus";
    $stmt_count_lecturer = $_SERVER['PDO']->prepare($sql_count_lecturer);
    $stmt_count_lecturer->bindParam(':id_kampus', $id_kampus, PDO::PARAM_INT);
    $stmt_count_lecturer->execute();
    $count_lecturer = $stmt_count_lecturer->rowCount();

    // Query Count program_study
    $sql_count_prodi = "SELECT * FROM program_study WHERE id_kampus = :id_kampus";
    $stmt_count_prodi = $_SERVER['PDO']->prepare($sql_count_prodi);
    $stmt_count_prodi->bindParam(':id_kampus', $id_kampus, PDO::PARAM_INT);
    $stmt_count_prodi->execute();
    $count_prodi = $stmt_count_prodi->rowCount();

    // Query Count total company
    $sql_count_total_company = "SELECT * FROM company";
    $stmt_count_total_company = $_SERVER['PDO']->prepare($sql_count_total_company);
    $stmt_count_total_company->execute();
    $count_total_company = $stmt_count_total_company->rowCount();

    // Query Count verified company
    $sql_count_company = "SELECT * FROM company WHERE status NOT IN ('Not Verified', 'Blacklist')";
    $stmt_count_company = $_SERVER['PDO']->prepare($sql_count_company);
    $stmt_count_company->execute();
    $count_company = $stmt_count_company->rowCount();

    // Query Count blacklist company
    $sql_count_blacklist_company = "SELECT * FROM company WHERE status IN ('Blacklist')";
    $stmt_count_blacklist_company = $_SERVER['PDO']->prepare($sql_count_blacklist_company);
    $stmt_count_blacklist_company->execute();
    $count_blacklist_company = $stmt_count_blacklist_company->rowCount();

    // Query Count new company
    $sql_count_new_company = "SELECT * FROM company WHERE status IN ('Not Verified')";
    $stmt_count_new_company = $_SERVER['PDO']->prepare($sql_count_new_company);
    $stmt_count_new_company->execute();
    $count_new_company = $stmt_count_new_company->rowCount();


    ?>


    <div class="container">

        <div class="panel-header bg-primary-gradient">
            <div class="page-inner py-5">
                <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                    <div>
                        <h1 class="text-white pb-2 fw-bold">Welcome To My Internship</h1>
                        <h4 class="text-white op-7 mb-2">Managing Industrial Learning through Structured Internship</h4>
                    </div>
                </div>
            </div>
        </div>



        <div class="page-inner mt--5">
            <div class="row mt--2">

                <div class="col-md-12">
                    <div class="card shadow mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center" id="headingAuditor">
                            <div class="span-title fw-bold">
                                Ranking of companies receiving internship students
                            </div>
                            <div class="span-mode me-2"></div>
                            <a href="index.php?page=show_data&action=companies_ranking_received_student&year=<?php echo e(date('Y')); ?>" class="btn btn-info btn-sm" id="show-data-companies-trophy-btn">
                                Show Data
                            </a>
                        </div>

                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-12 d-flex gap-2 flex-wrap align-items-center">
                                    <select id="year-selector" class="form-select mt-3 mr-2"></select>
                                    <button id="trophies_generatePDF" class="btn btn-info btn-sm mt-3">Download Chart as PDF</button>
                                    
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <canvas id="chart-company" height="300" width="600"></canvas>
                                    <p id="trophies_chartLegend" class="text-center fw-bold mt-2">Current year : xxxx</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow">
                        <div class="card-header d-flex justify-content-between align-items-center" id="headingMajor">
                            <div class="d-flex flex-column">
                                <div class="span-title fw-bold">
                                    Top 5 Companies for Internship by Major
                                </div>
                                <div class="span-mode"></div>
                            </div>

                            <a href="index.php?page=show_data&action=companies_ranking_major&year=2021&major=Informatics Engineering" class="btn btn-info btn-sm" id="show-data-companies-major-btn">
                                Show Data
                            </a>
                        </div>

                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-12 d-flex gap-2 flex-wrap align-items-center">
                                    <select id="major_year-selector" class="form-select mt-3 mr-2">
                                        <!-- Option will be populated with JavaScript -->
                                    </select>

                                    <select id="major-selector" class="form-select mt-3 mr-2">
                                        <option value="">Select Major</option>
                                    </select>

                                    
                                    
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <canvas id="major_chart" height="300" width="600"></canvas>
                                    <p id="major_chartLegend" class="text-center fw-bold mt-2">Current year : xxxx</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="card full-height">
                        <div class="card-body">
                            <div class="card-title">My Internship Statistics</div>
                            <div class="card-category">Daily information about statistics in system</div>
                            <div class="d-flex flex-wrap justify-content-around pb-2 pt-4">
                                <div class="px-2 pb-2 pb-md-0 text-center">
                                    <div id="circles-0"></div>
                                    <h6 class="fw-bold mt-3 mb-0">University</h6>
                                </div>
                                <div class="px-2 pb-2 pb-md-0 text-center">
                                    <div id="circles-1"></div>
                                    <h6 class="fw-bold mt-3 mb-0">Company</h6>
                                </div>

                                <div class="px-2 pb-2 pb-md-0 text-center">
                                    <div id="circles-5"></div>
                                    <h6 class="fw-bold mt-3 mb-0">Company User</h6>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>

                <!-- Politeknik Statistik -->
                <div class="col-md-6">
                    <div class="card full-height">
                        <div class="card-body">
                            <div class="card-title"><?= $data_kampus['nama_kampus'] ?> statistics</div>
                            <div class="card-category">Daily information about your university statistics in system</div>
                            <div class="d-flex flex-wrap justify-content-around pb-2 pt-4">

                                <div class="px-2 pb-2 pb-md-0 text-center">
                                    <div id="circles-2"></div>
                                    <h6 class="fw-bold mt-3 mb-0">Lecturer</h6>
                                </div>
                                <div class="px-2 pb-2 pb-md-0 text-center">
                                    <div id="circles-3"></div>
                                    <h6 class="fw-bold mt-3 mb-0">Student</h6>
                                </div>

                                <div class="px-2 pb-2 pb-md-0 text-center">
                                    <div id="circles-4"></div>
                                    <h6 class="fw-bold mt-3 mb-0">Study Program</h6>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>


                <div class="col-md-12">
                    <div class="card full-height">
                        <div class="card-body">
                            <div class="card-title">Manual Guide Application</div>
                            <div class="card-category">Download Manual Guide</div>
                            <div class="d-flex flex-wrap justify-content-around pb-2 pt-4">
                                <div class="px-2 pb-2 pb-md-0 text-center">
                                    <h4 class="fw-bold mt-3 mb-0 text-primary">
                                        <a style="text-decoration: none;" title="Download Manual Guide for UPKPK" aria-label="Download Manual Guide for UPKPK" href="viewer.php?src=manual_guide" download="" target="_blank"> <i class="fas fa-download"></i> Download Manual Guide </a>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- <div class="col-md-6">
                <div class="card full-height">
                    <div class="card-body">
                        <div class="card-title">Video Tutorial To Use Application</div>
                        <div class="card-category">Watch Video Tutorial</div>
                        <div class="d-flex flex-wrap justify-content-around pb-2 pt-4">
                            <div class="px-2 pb-2 pb-md-0 text-center">
                                <h4 class="fw-bold mt-3 mb-0 text-primary">
                                    <a style="text-decoration: none;" title="Watch Video Tutorial in Youtube" aria-label="Watch Video Tutorial in Youtube" 
                                    href="https://youtube.com/" download="" target="_blank"> <i class="fab fa-youtube"></i> Watch Video Tutorial</a>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->

            </div>
        </div>
    </div>

    <?php $__env->stopSection(); ?>

    <?php $__env->startPush('footer-scripts'); ?>
    <script src="./assets/js/plugin/chart-circle/circles.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    
    <script>
        // Data dari PHP
        const rawData = <?php echo json_encode($data_stat['company_trophies'], 15, 512) ?>;

        // Ubah data ke format per tahun
        const dataByYear = {};
        rawData.forEach(row => {
            const year = row.tahun;
            if (!dataByYear[year]) dataByYear[year] = [];
            dataByYear[year].push({
                label: row.name_company
                , value: row.jumlah_mahasiswa
                , ranking: row.ranking, // Tambahkan ranking
                year: row.tahun // Menyimpan tahun untuk tooltip
            });
        });

        // Ambil elemen dropdown, canvas, dan p legend
        const yearSelect = document.getElementById("year-selector");
        const ctx = document.getElementById("chart-company").getContext("2d");
        const chartLegend = document.getElementById("trophies_chartLegend");

        // Generate tahun dropdown (2021 - sekarang)
        const currentYear = new Date().getFullYear();
        for (let year = 2021; year <= currentYear; year++) {
            const option = document.createElement("option");
            option.value = year;
            option.text = `${year}`;
            yearSelect.appendChild(option);
        }

        // Default tahun: tahun sebelumnya
        const defaultYear = currentYear;
        yearSelect.value = defaultYear;

        // Inisialisasi chart
        let chart = null;

        function updateChart(year) {
            const dataset = dataByYear[year] || [];

            const labels = dataset.map(d => d.label);
            const data = dataset.map(d => d.value);
            const rankings = dataset.map(d => `Rank ${d.ranking}`); // Mengambil ranking

            const maxY = Math.max(...data, 0) + 5;

            const colors = dataset.map((_, index) => {
                if (index === 0) return 'rgba(255, 99, 132, 0.6)';
                if (index === 1) return 'rgba(54, 162, 235, 0.6)';
                if (index === 2) return 'rgba(255, 159, 64, 0.6)';
                return 'rgba(75, 192, 192, 0.6)';
            });

            if (chart) chart.destroy();

            chart = new Chart(ctx, {
                type: 'bar'
                , data: {
                    labels: labels,

                    datasets: [{
                        label: `Student(s) received in (${year})`
                        , data: data
                        , backgroundColor: colors
                        , borderColor: colors.map(color => color.replace('0.6', '1'))
                        , borderWidth: 1
                    }]
                }
                , options: {
                    responsive: true
                    , maintainAspectRatio: true
                    , plugins: {
                        legend: {
                            display: false // ✅ Legend non-interaktif
                        }
                        , datalabels: {
                            anchor: 'end'
                            , align: 'top'
                            , formatter: (value) => value, // Hanya tampilkan jumlah mahasiswa
                            color: 'black'
                            , font: {
                                weight: 'bold'
                            }
                        }
                        , tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const index = context.dataIndex;
                                    const item = dataset[index];

                                    // Menampilkan tooltip dengan tahun yang sesuai
                                    return `Student(s) received in (${item.year}): ${item.value} students, (Rank ${item.ranking})`;
                                }
                            }
                        }
                    }
                    , scales: {
                        x: {
                            title: {
                                display: true
                                , text: 'Company'
                            }
                        }
                        , y: {
                            beginAtZero: true
                            , suggestedMax: maxY
                            , title: {
                                display: true
                                , text: 'Total Students'
                            }
                            , ticks: {
                                precision: 0
                            }
                        }
                    }
                }
                , plugins: [ChartDataLabels]
            });

            chartLegend.textContent = `Ranking of companies receiving internship students (${year})`;
        }

        // Pertama kali tampilkan default tahun
        updateChart(defaultYear);

        // Update chart saat dropdown diubah
        yearSelect.addEventListener('change', function() {
            updateChart(this.value);
        });

        // Event listener untuk tombol Download PDF
        document.getElementById("trophies_generatePDF").addEventListener("click", function() {
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF();

            // Ambil gambar dari canvas (toDataURL akan menghasilkan gambar PNG)
            const canvasDataUrl = document.getElementById("chart-company").toDataURL("image/png");

            // Masukkan gambar ke PDF
            doc.addImage(canvasDataUrl, 'PNG', 10, 10, 180, 100); // Sesuaikan ukuran gambar

            // Simpan PDF
            doc.save(`trophies_chart_${defaultYear}.pdf`);
        });

        document.getElementById('trophies_generateExcel').addEventListener('click', function() {
            // Ambil tahun yang sedang dipilih (kalau kamu pakai select year)
            const selectedYear = document.getElementById('year-selector').value;

            // Filter data sesuai tahun (jika ada)
            const filteredData = selectedYear ?
                rawData.filter(item => item.tahun === selectedYear) :
                rawData;

            // Format data jadi array of objects untuk SheetJS
            const excelData = filteredData.map(item => ({
                Tahun: item.tahun
                , ID_Company: item.id_company
                , Nama_Perusahaan: item.name_company
                , Jumlah_Mahasiswa: item.jumlah_mahasiswa
                , Ranking: item.ranking
            }));

            // Convert ke worksheet
            const worksheet = XLSX.utils.json_to_sheet(excelData);
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, "Trophies");

            // Download
            XLSX.writeFile(workbook, `trophies_${selectedYear || 'all'}.xlsx`);
        });

    </script>

    <script>
        document.getElementById('year-selector').addEventListener('change', function() {
            const year = this.value;
            const url = `index.php?page=show_data&action=companies_ranking_received_student&year=${year}`;
            document.getElementById('show-data-companies-trophy-btn').setAttribute('href', url);
        });

    </script>


    
    <script>
        const majorData = <?php echo json_encode($data_stat['major_favorites'], 15, 512) ?>;

        const majorYearSelector = document.getElementById('major_year-selector');
        const majorSelector = document.getElementById('major-selector');
        const majorChartCanvas = document.getElementById('major_chart').getContext('2d');
        const majorChartLegend = document.getElementById('major_chartLegend');

        const uniqueYears = [...new Set(majorData.map(item => item.tahun))];
        const uniqueMajors = [...new Set(majorData.map(item => item.jurusan))];

        // Populate dropdown tahun
        uniqueYears.forEach(year => {
            const opt = document.createElement('option');
            opt.value = year;
            opt.textContent = year;
            majorYearSelector.appendChild(opt);
        });

        // Populate dropdown jurusan
        uniqueMajors.forEach(major => {
            const opt = document.createElement('option');
            opt.value = major;
            opt.textContent = major;
            majorSelector.appendChild(opt);
        });

        // Inisialisasi chart kosong
        const majorChart = new Chart(majorChartCanvas, {
            type: 'bar'
            , data: {
                labels: []
                , datasets: [{
                    label: ''
                    , data: []
                    , backgroundColor: []
                , }]
            }
            , options: {
                responsive: true
                , plugins: {
                    legend: {
                        display: false
                    }
                    , tooltip: {
                        callbacks: {
                            label: function(context) {
                                const item = majorData.find(d =>
                                    d.tahun == majorYearSelector.value &&
                                    d.jurusan == majorSelector.value &&
                                    d.name_company === context.label
                                );
                                if (item) {
                                    return `Student(s) received in (${item.tahun}): ${item.jumlah_mahasiswa} student(s), (Rank ${item.ranking})`;
                                }
                                return context.label;
                            }
                        }
                    }
                    , datalabels: {
                        anchor: 'end'
                        , align: 'top'
                        , formatter: (value) => value
                        , color: '#000'
                        , font: {
                            weight: 'bold'
                        }
                    }
                }
                , scales: {
                    x: {
                        title: {
                            display: true
                            , text: 'Company'
                        }
                    }
                    , y: {
                        beginAtZero: true
                        , suggestedMax: 10
                        , ticks: {
                            stepSize: 5
                        }
                        , title: {
                            display: true
                            , text: 'Total Student'
                        }
                    }
                }
            }
            , plugins: [ChartDataLabels]
        });

        function updateMajorChart() {
            const selectedYear = majorYearSelector.value;
            const selectedMajor = majorSelector.value;

            const filtered = majorData.filter(item =>
                item.tahun == selectedYear &&
                item.jurusan == selectedMajor
            );

            const sorted = [1, 2, 3, 4, 5].map(rank => {
                return filtered.find(item => parseInt(item.ranking) === rank);
            }).filter(Boolean);

            const labels = sorted.map(item => item.name_company);
            const values = sorted.map(item => parseInt(item.jumlah_mahasiswa));
            const colors = sorted.map((_, index) => {
                if (index === 0) return 'rgba(255, 99, 132, 0.6)';
                if (index === 1) return 'rgba(54, 162, 235, 0.6)';
                if (index === 2) return 'rgba(255, 159, 64, 0.6)';
                return 'rgba(75, 192, 192, 0.6)';
            });

            const maxValue = Math.max(...values, 0) + 5;

            // Update chart data
            majorChart.data.labels = labels;
            majorChart.data.datasets[0].data = values;
            majorChart.data.datasets[0].backgroundColor = colors;
            majorChart.data.datasets[0].label = `Students Received (${selectedYear} - ${selectedMajor})`;
            majorChart.options.scales.y.suggestedMax = maxValue;

            majorChart.update();

            majorChartLegend.textContent = `Top 5 Companies for Internship for ${selectedMajor} Major Students in ${selectedYear}`;
        }

        majorYearSelector.addEventListener('change', updateMajorChart);
        majorSelector.addEventListener('change', updateMajorChart);

        // Set default pilihan pertama
        if (uniqueYears.length && uniqueMajors.length) {
            majorYearSelector.value = uniqueYears[0];
            majorSelector.value = uniqueMajors[0];
            updateMajorChart();
        }

    </script>

    <script>
        document.getElementById('major_year-selector').addEventListener('change', updateMajorURL);
        document.getElementById('major-selector').addEventListener('change', updateMajorURL);

        function updateMajorURL() {
            const year = document.getElementById('major_year-selector').value;
            const major = document.getElementById('major-selector').value;

            // Bangun URL hanya jika tahun terisi
            let url = `index.php?page=show_data&action=companies_ranking_major&year=${year}`;
            if (major) {
                url += `&major=${encodeURIComponent(major)}`;
            }

            document.getElementById('show-data-companies-major-btn').setAttribute('href', url);
        }

    </script>





    <script>
        Circles.create({
            id: 'circles-0'
            , radius: 45
            , value: '<?= $count_university ?>'
            , maxValue: '<?= $count_university ?>'
            , width: 7
            , text: '<?= $count_university ?>'
            , colors: ['#f1f1f1', '#FE5D26']
            , duration: 400
            , wrpClass: 'circles-wrp'
            , textClass: 'circles-text'
            , styleWrapper: true
            , styleText: true
        })

        Circles.create({
            id: 'circles-1'
            , radius: 45
            , value: '<?= $count_company ?>'
            , maxValue: '<?= $count_company ?>'
            , width: 7
            , text: '<?= $count_company ?>'
            , colors: ['#f1f1f1', '#F2C078']
            , duration: 400
            , wrpClass: 'circles-wrp'
            , textClass: 'circles-text'
            , styleWrapper: true
            , styleText: true
        })

        Circles.create({
            id: 'circles-2'
            , radius: 45
            , value: '<?= $count_lecturer ?? 0 ?>'
            , maxValue: '<?= $count_lecturer ?? 0 ?>'
            , width: 7
            , text: '<?= $count_lecturer ?? 0?>'
            , colors: ['#f1f1f1', '#FAEDCA']
            , duration: 400
            , wrpClass: 'circles-wrp'
            , textClass: 'circles-text'
            , styleWrapper: true
            , styleText: true
        })

        Circles.create({
            id: 'circles-3'
            , radius: 45
            , value: '<?= $count_student ?? 0?>'
            , maxValue: '<?= $count_student ?? 0 ?>'
            , width: 7
            , text: '<?= $count_student ?? 0 ?>'
            , colors: ['#f1f1f1', '#c1dbb3']
            , duration: 400
            , wrpClass: 'circles-wrp'
            , textClass: 'circles-text'
            , styleWrapper: true
            , styleText: true
        })


        Circles.create({
            id: 'circles-4'
            , radius: 45
            , value: '<?= $count_prodi ?>'
            , maxValue: '<?= $count_prodi ?>'
            , width: 7
            , text: '<?= $count_prodi ?>'
            , colors: ['#f1f1f1', '#6861CE']
            , duration: 400
            , wrpClass: 'circles-wrp'
            , textClass: 'circles-text'
            , styleWrapper: true
            , styleText: true
        })

        Circles.create({
            id: 'circles-5'
            , radius: 45
            , value: '<?= $count_user_company ?>'
            , maxValue: '<?= $count_user_company ?>'
            , width: 7
            , text: '<?= $count_user_company ?>'
            , colors: ['#f1f1f1', '#7ebc89']
            , duration: 400
            , wrpClass: 'circles-wrp'
            , textClass: 'circles-text'
            , styleWrapper: true
            , styleText: true
        })

    </script>
    <?php $__env->stopPush(); ?>

<?php echo $__env->make('templates.cdc', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/cdc/dashboard.blade.php ENDPATH**/ ?>