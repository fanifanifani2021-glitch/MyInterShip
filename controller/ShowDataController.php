<?php
session_start();
switch ($method):

    default:
        response_json(404);
        break;


    case 'companies_ranking_received_student':


        $id_kampus = $_SESSION['CDC_Data']['id_kampus'];

        $currentYear = date('Y');

        // Date range
        $start_date = isset($_GET['start_date']) && $_GET['start_date'] !== ''
            ? $_GET['start_date']
            : "$currentYear-01-01";

        $end_date = isset($_GET['end_date']) && $_GET['end_date'] !== ''
            ? $_GET['end_date']
            : "$currentYear-12-31";

        $major = $_GET['major'] ?? '';
        $prodi = $_GET['prodi'] ?? '';

        // =============================
        // BASE QUERY (tanpa filter prodi)
        // =============================
        $sql = "
            SELECT *
            FROM (
                SELECT
                    c.id_company,
                    c.name AS name_company,
                    COUNT(si.nim) AS jumlah_mahasiswa,
                    ROW_NUMBER() OVER (ORDER BY COUNT(si.nim) DESC) AS ranking
                FROM internship i
                JOIN student_internship si ON si.nim = i.nim
                JOIN company c ON c.id_company = i.id_company
                LEFT JOIN program_study ps ON ps.kode_prodi = si.program_study
                    AND ps.id_kampus = si.id_kampus
                WHERE si.id_kampus = :id_kampus
                AND i.start_date BETWEEN :start_date AND :end_date
                AND i.end_date   BETWEEN :start_date AND :end_date
                AND LOWER(i.status) IN ('yes', 'finish')
        ";

        // =============================
        // DYNAMIC FILTER — masuk ke dalam subquery
        // =============================

        // Filter Major
        if (!empty($major)) {
            $sql .= " AND ps.major = :major ";
        }

        // Filter Berdasarkan Prodi
        if (!empty($prodi)) {

            if (strpos($prodi, 'MAJOR:') === 0) {
                // ALL PRODI dalam 1 major
                $majorName = substr($prodi, 6);
                $sql .= " AND ps.major = :major_all ";
            }

            elseif (strpos($prodi, 'PS:') === 0) {
                // Prodi spesifik
                $kodeProdi = substr($prodi, 3);
                $sql .= " AND ps.kode_prodi = :kode_prodi ";
            }
        }

        // =============================
        // GROUP BY & CLOSE SUBQUERY
        // =============================
        $sql .= "
                GROUP BY
                    c.id_company,
                    c.name
            ) AS ranked
            WHERE ranking <= 20
            ORDER BY ranking;
        ";

        // =============================
        // BIND PARAMETERS
        // =============================
        $bind = [
            ':id_kampus'  => $id_kampus,
            ':start_date' => $start_date,
            ':end_date'   => $end_date,
        ];

        if (!empty($major)) {
            $bind[':major'] = $major;
        }

        if (!empty($prodi)) {

            if (isset($majorName)) {
                $bind[':major_all'] = $majorName;
            }

            if (isset($kodeProdi)) {
                $bind[':kode_prodi'] = $kodeProdi;
            }
        }

        // =============================
        // EXECUTE QUERY
        // =============================
        $stmt = $_SERVER['PDO']->prepare($sql);
        $stmt->execute($bind);

        $data_stat['company_trophies'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($data_stat['company_trophies'])) {
    $data_stat['company_trophies'] = [
        [
            'id_company' => 1,
            'name_company' => 'PT Teknologi Nusantara',
            'jumlah_mahasiswa' => 15,
            'ranking' => 1,
            'tahun' => $currentYear
        ],
        [
            'id_company' => 2,
            'name_company' => 'PT Digital Indonesia',
            'jumlah_mahasiswa' => 12,
            'ranking' => 2,
            'tahun' => $currentYear
        ],
        [
            'id_company' => 3,
            'name_company' => 'PT Solusi Informatika',
            'jumlah_mahasiswa' => 9,
            'ranking' => 3,
            'tahun' => $currentYear
        ],
        [
            'id_company' => 4,
            'name_company' => 'PT Mitra Teknologi',
            'jumlah_mahasiswa' => 7,
            'ranking' => 4,
            'tahun' => $currentYear
        ],
        [
            'id_company' => 5,
            'name_company' => 'PT Digital Kreatif',
            'jumlah_mahasiswa' => 5,
            'ranking' => 5,
            'tahun' => $currentYear
        ]
    ];
}


        // Ambil program study
        $sql = "SELECT 
                    kode_prodi,
                    jenjang,
                    prodi,
                    study_program,
                    jurusan,
                    major
                FROM program_study
                WHERE id_kampus = :id_kampus
                ORDER BY jurusan ASC, study_program ASC";

        $stmt = $_SERVER['PDO']->prepare($sql);
        $stmt->execute(['id_kampus' => $id_kampus]);
        $program_study = $stmt->fetchAll(PDO::FETCH_ASSOC);

        renderView('show_data.companies_ranking_received_student', [
            'data_stat'     => $data_stat,
            'program_study' => $program_study,
            'start_date'    => $start_date,
            'end_date'      => $end_date,
            'major'         => $major,
            'prodi'         => $prodi
        ]);
    break;




    case 'companies_ranking_major':
        $year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
        $major = isset($_GET['major']) ? (string) $_GET['major'] : 'Informatic Engineering';
        $id_kampus = $_SESSION['CDC_Data']['id_kampus'];

        $sql = "
            SELECT * FROM (
                SELECT 
                    YEAR(i.start_date) AS tahun,
                    ps.major AS jurusan,
                    c.id_company,
                    c.name AS name_company,
                    COUNT(si.nim) AS jumlah_mahasiswa,
                    ROW_NUMBER() OVER (
                        PARTITION BY YEAR(i.start_date), ps.major 
                        ORDER BY COUNT(si.nim) DESC
                    ) AS ranking
                FROM internship i
                JOIN student_internship si ON si.nim = i.nim
                JOIN company c ON c.id_company = i.id_company
                JOIN program_study ps ON ps.kode_prodi = si.program_study
                WHERE 
                    si.id_kampus = :id_kampus
                    AND ( ps.major = :major OR ps.jurusan = :major )
                    AND YEAR(i.start_date) BETWEEN :year_start AND :year_end
                    AND LOWER(i.status) IN ('yes', 'finish')
                GROUP BY YEAR(i.start_date), ps.major, c.id_company, c.name
            ) AS ranked
            WHERE ranking <= 10
            ORDER BY tahun, jurusan, ranking;
            ";

        $stmt_stat = $_SERVER['PDO']->prepare($sql);
        $stmt_stat->bindParam(':id_kampus', $id_kampus, PDO::PARAM_INT);
        $stmt_stat->bindParam(':major', $major, PDO::PARAM_STR);
        $stmt_stat->bindParam(':year_start', $year, PDO::PARAM_INT);
        $stmt_stat->bindParam(':year_end', $year, PDO::PARAM_INT);
        $stmt_stat->execute();


        $sql = "
    SELECT DISTINCT ps.major
    FROM program_study ps
    JOIN student_internship si ON si.program_study = ps.kode_prodi
    WHERE si.id_kampus = :id_kampus
    ORDER BY ps.major ASC
";

        $stmt = $_SERVER['PDO']->prepare($sql);
        $stmt->bindParam(':id_kampus', $id_kampus, PDO::PARAM_INT);
        $stmt->execute();

        $majors = $stmt->fetchAll(PDO::FETCH_COLUMN); // hasilnya berupa array berisi nama major

        $sql = "
    SELECT DISTINCT ps.jurusan
    FROM program_study ps
    JOIN student_internship si ON si.program_study = ps.kode_prodi
    WHERE si.id_kampus = :id_kampus
    ORDER BY ps.jurusan ASC
";

        $stmt = $_SERVER['PDO']->prepare($sql);
        $stmt->bindParam(':id_kampus', $id_kampus, PDO::PARAM_INT);
        $stmt->execute();

        $jurusans = $stmt->fetchAll(PDO::FETCH_COLUMN); // hasilnya berupa array berisi nama major


        // Karena datanya banyak baris, gunakan fetchAll
        $data_stat['major_favorites'] = $stmt_stat->fetchAll(PDO::FETCH_ASSOC);

        renderView('show_data.companies_ranking_major', ['data_stat' => $data_stat, 'major' => $major, 'majors' => $majors, 'jurusans' => $jurusans]);
        break;
        
        case 'companies_ranking_major_all':

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['error' => 'Not Allowed']);
                exit;
            }
        
            if (!in_array(strtolower($_SESSION['role']), ['upkpk', 'cdc', 'lecturer'])) {
                http_response_code(404);
                echo json_encode(['error' => 'Not Found']);
                exit;
            }
        
            $id_kampus = $_SESSION['CDC_Data']['id_kampus'] ?? $_SESSION['Lecturer_Data']['id_kampus'] ?? $_SESSION['Lecturer-Data']['id_kampus'];
            $major = $_POST['major'] ?? null;
            $pdo = $_SERVER['PDO'];
        
            if (!$major) {
                http_response_code(400);
                echo json_encode(['error' => 'Major parameter is required']);
                exit;
            }
        
            try {
                // Ambil data ranking (TOP 10 per major)
                $stmt = $pdo->prepare("
                    SELECT * FROM (
                        SELECT 
                            YEAR(i.start_date) AS tahun,
                            ps.major AS jurusan,
                            c.id_company,
                            c.name AS name_company,
                            COUNT(si.nim) AS jumlah_mahasiswa,
                            ROW_NUMBER() OVER (
                                PARTITION BY YEAR(i.start_date), ps.major 
                                ORDER BY COUNT(si.nim) DESC
                            ) AS ranking
                        FROM internship i
                        JOIN student_internship si ON si.nim = i.nim
                        JOIN company c ON c.id_company = i.id_company
                        JOIN program_study ps ON ps.kode_prodi = si.program_study
                        WHERE 
                            si.id_kampus = :id_kampus
                            AND (ps.major = :major OR ps.jurusan = :major)
                            AND LOWER(i.status) IN ('yes', 'finish')
                        GROUP BY YEAR(i.start_date), ps.major, c.id_company, c.name
                    ) AS ranked
                    WHERE ranking <= 10
                    ORDER BY tahun DESC, jurusan, ranking;
                ");
                $stmt->execute([
                    ':id_kampus' => $id_kampus,
                    ':major' => $major
                ]);
        
                $ranking_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $grouped_data = [];
        
                foreach ($ranking_data as $rank) {
                    // Ambil mahasiswa untuk masing-masing ranking
                    $stmt_mhs = $pdo->prepare("
                        SELECT
                            si.nim,
                            si.name AS student_name,
                            ps.jurusan,
                            ps.prodi,
                            ps.jenjang,
                            i.start_date,
                            i.end_date,
                            i.status
                        FROM internship i
                        JOIN student_internship si ON si.nim = i.nim
                        LEFT JOIN program_study ps ON si.program_study = ps.kode_prodi
                        WHERE
                            si.id_kampus = :id_kampus
                            AND i.id_company = :id_company
                            AND (ps.major = :jurusan OR ps.jurusan = :jurusan)
                            AND YEAR(i.start_date) = :year
                            AND LOWER(i.status) IN ('yes', 'finish')
                        ORDER BY i.start_date
                    ");
        
                    $stmt_mhs->execute([
                        ':id_kampus' => $id_kampus,
                        ':id_company' => $rank['id_company'],
                        ':jurusan' => $rank['jurusan'],
                        ':year' => $rank['tahun']
                    ]);
        
                    $students = $stmt_mhs->fetchAll(PDO::FETCH_ASSOC);
        
                    foreach ($students as &$s) {
                        $status = strtolower($s['status']);
                        if ($status == 'yes') $s['status'] = 'Sedang Magang';
                        elseif ($status == 'finish') $s['status'] = 'Selesai';
                        else $s['status'] = ucfirst($status);
                    }
        
                    $grouped_data[] = [
                        'header' => $rank,
                        'students' => $students
                    ];
                }
        
                // Export ke Excel (dengan merge cell per perusahaan)
                $file_dir = 'excel/generate/';
                if (!is_dir($file_dir)) {
                    mkdir($file_dir, 0777, true);
                }
                $filename = 'ranking_export_' . date('Ymd_His') . '.xlsx';
                $file_path = $file_dir . $filename;
        
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
        
                $headers = ['Tahun', 'Jurusan', 'Ranking', 'Perusahaan', 'Jumlah Mahasiswa', 'NIM', 'Nama Mahasiswa', 'Jurusan Mhs', 'Prodi', 'Jenjang', 'Start Date', 'End Date', 'Status'];
                $sheet->fromArray($headers, NULL, 'A1');
        
                $row = 2;
                foreach ($grouped_data as $group) {
                    $studentCount = count($group['students']);
                    $endRow = $row + $studentCount - 1;
        
                    // Merge untuk kolom perusahaan
                    $sheet->mergeCells("A{$row}:A{$endRow}");
                    $sheet->mergeCells("B{$row}:B{$endRow}");
                    $sheet->mergeCells("C{$row}:C{$endRow}");
                    $sheet->mergeCells("D{$row}:D{$endRow}");
                    $sheet->mergeCells("E{$row}:E{$endRow}");
        
                    $sheet->setCellValue("A{$row}", $group['header']['tahun']);
                    $sheet->setCellValue("B{$row}", $group['header']['jurusan']);
                    $sheet->setCellValue("C{$row}", $group['header']['ranking']);
                    $sheet->setCellValue("D{$row}", $group['header']['name_company']);
                    $sheet->setCellValue("E{$row}", $group['header']['jumlah_mahasiswa']);
        
                    foreach ($group['students'] as $student) {
                        $sheet->setCellValue("F{$row}", $student['nim']);
                        $sheet->setCellValue("G{$row}", $student['student_name']);
                        $sheet->setCellValue("H{$row}", $student['jurusan']);
                        $sheet->setCellValue("I{$row}", $student['prodi']);
                        $sheet->setCellValue("J{$row}", $student['jenjang']);
                        $sheet->setCellValue("K{$row}", $student['start_date']);
                        $sheet->setCellValue("L{$row}", $student['end_date']);
                        $sheet->setCellValue("M{$row}", $student['status']);
                        $row++;
                    }
                }
        
                // Auto width kolom
                foreach (range('A', 'M') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
        
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save($file_path);
        
                $url = base_url('index.php?viewer=excel_generate&location=' .base64_encode($filename));
        
                echo json_encode(['file_url' => $url]);
                
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
            break;

endswitch;
