<?php
session_start();
switch ($method):

    default:
        response_json(404);
        break;


    case 'companies_ranking_received_student':
    case 'companies_ranking_major':

        $id_kampus = isset($_SESSION['CDC_Data']['id_kampus']) 
            ? $_SESSION['CDC_Data']['id_kampus'] 
            : (isset($_SESSION['Lecturer_Data']['id_kampus']) 
                ? $_SESSION['Lecturer_Data']['id_kampus'] 
                : 1);

        $currentYear = isset($_GET['year']) && is_numeric($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

        // Parameter Filter
        $start_date = isset($_GET['start_date']) && $_GET['start_date'] !== '' ? trim($_GET['start_date']) : "$currentYear-01-01";
        $end_date   = isset($_GET['end_date']) && $_GET['end_date'] !== ''   ? trim($_GET['end_date'])   : "$currentYear-12-31";
        $major      = isset($_GET['major'])      ? trim($_GET['major'])      : '';
        $prodi      = isset($_GET['prodi'])      ? trim($_GET['prodi'])      : '';
        $company    = isset($_GET['company'])    ? trim($_GET['company'])    : '';
        $search     = isset($_GET['search'])     ? trim($_GET['search'])     : '';

        // =============================
        // QUERY DATA MAHASISWA MAGANG
        // =============================
        $sql = "
            SELECT 
                i.id_internship,
                si.nim,
                si.name AS student_name,
                COALESCE(ps.jurusan, ps.major, '-') AS jurusan,
                COALESCE(ps.major, ps.jurusan, '-') AS major,
                COALESCE(ps.prodi, ps.study_program, '-') AS prodi,
                COALESCE(ps.study_program, ps.prodi, '-') AS study_program,
                COALESCE(ps.jenjang, '') AS jenjang,
                COALESCE(c.name, '-') AS company_name,
                c.id_company,
                i.start_date,
                i.end_date,
                i.status
            FROM internship i
            JOIN student_internship si ON si.nim = i.nim
            LEFT JOIN company c ON c.id_company = i.id_company
            LEFT JOIN program_study ps ON ps.kode_prodi = si.program_study
                AND ps.id_kampus = si.id_kampus
            WHERE si.id_kampus = :id_kampus
        ";

        $bind = [
            ':id_kampus' => $id_kampus
        ];

        // 1. Filter Tanggal Mulai (start_date)
        if ($start_date !== '') {
            $sql .= " AND i.start_date >= :start_date ";
            $bind[':start_date'] = $start_date;
        }

        // 2. Filter Tanggal Selesai (end_date)
        if ($end_date !== '') {
            $sql .= " AND i.end_date <= :end_date ";
            $bind[':end_date'] = $end_date;
        }

        // 3. Filter Jurusan (major)
        if ($major !== '') {
            $sql .= " AND (ps.major = :major OR ps.jurusan = :major) ";
            $bind[':major'] = $major;
        }

        // 4. Filter Program Studi (prodi / study program)
        if ($prodi !== '') {
            if (strpos($prodi, 'MAJOR:') === 0) {
                $majorName = substr($prodi, 6);
                $sql .= " AND (ps.major = :major_all OR ps.jurusan = :major_all) ";
                $bind[':major_all'] = $majorName;
            } elseif (strpos($prodi, 'PS:') === 0) {
                $kodeProdi = substr($prodi, 3);
                $sql .= " AND ps.kode_prodi = :kode_prodi ";
                $bind[':kode_prodi'] = $kodeProdi;
            } else {
                $sql .= " AND (ps.kode_prodi = :prodi_val OR ps.prodi = :prodi_val OR ps.study_program = :prodi_val) ";
                $bind[':prodi_val'] = $prodi;
            }
        }

        // 5. Filter Perusahaan (company)
        if ($company !== '') {
            $sql .= " AND (c.id_company = :id_company OR c.name LIKE :company_name) ";
            $bind[':id_company'] = $company;
            $bind[':company_name'] = '%' . $company . '%';
        }

        // 6. Pencarian berdasarkan Nama, NIM, atau Perusahaan
        if ($search !== '') {
            $sql .= " AND (si.name LIKE :search_name OR si.nim LIKE :search_nim OR c.name LIKE :search_company) ";
            $bind[':search_name'] = '%' . $search . '%';
            $bind[':search_nim'] = '%' . $search . '%';
            $bind[':search_company'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY i.start_date DESC, si.nim ASC ";

        $stmt = $_SERVER['PDO']->prepare($sql);
        $stmt->execute($bind);
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Format status dan badge
        foreach ($students as &$s) {
            $statusRaw = strtolower(trim($s['status']));
            if ($statusRaw === 'yes') {
                $s['status_label'] = 'Sedang Magang';
                $s['badge_class']  = 'badge bg-success';
            } elseif ($statusRaw === 'finish') {
                $s['status_label'] = 'Selesai';
                $s['badge_class']  = 'badge bg-secondary';
            } elseif ($statusRaw === 'waiting') {
                $s['status_label'] = 'Menunggu';
                $s['badge_class']  = 'badge bg-warning text-dark';
            } elseif ($statusRaw === 'rejected') {
                $s['status_label'] = 'Ditolak';
                $s['badge_class']  = 'badge bg-danger';
            } else {
                $s['status_label'] = ucfirst($statusRaw);
                $s['badge_class']  = 'badge bg-info text-dark';
            }
        }
        unset($s);

        // =============================
        // FITUR EXPORT EXCEL & CSV
        // =============================
        if (isset($_GET['export']) && in_array($_GET['export'], ['excel', 'csv'])) {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Data Magang Mahasiswa');

            $headers = ['No', 'Nama Mahasiswa', 'NIM', 'Jurusan', 'Program Studi', 'Perusahaan', 'Tanggal Mulai', 'Tanggal Selesai', 'Status Magang'];
            $sheet->fromArray($headers, NULL, 'A1');

            // Styling Header Table
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0D6EFD']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ]
            ];
            $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

            $rowNum = 2;
            $no = 1;
            foreach ($students as $row) {
                $prodiDisplay = trim(($row['jenjang'] ? $row['jenjang'] . ' ' : '') . $row['prodi']);
                $sheet->setCellValue('A' . $rowNum, $no++);
                $sheet->setCellValue('B' . $rowNum, $row['student_name']);
                $sheet->setCellValueExplicit('C' . $rowNum, (string)$row['nim'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->setCellValue('D' . $rowNum, $row['jurusan']);
                $sheet->setCellValue('E' . $rowNum, $prodiDisplay);
                $sheet->setCellValue('F' . $rowNum, $row['company_name']);
                $sheet->setCellValue('G' . $rowNum, $row['start_date']);
                $sheet->setCellValue('H' . $rowNum, $row['end_date']);
                $sheet->setCellValue('I' . $rowNum, $row['status_label']);
                $rowNum++;
            }

            foreach (range('A', 'I') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $filename = 'Data_Magang_Mahasiswa_' . date('Ymd_His');

            if (ob_get_length()) {
                ob_end_clean();
            }

            if ($_GET['export'] === 'csv') {
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
                $writer->save('php://output');
                exit;
            } else {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $filename . '.xlsx"');
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save('php://output');
                exit;
            }
        }

        // =============================
        // AMBIL DATA PILIHAN FILTER
        // =============================
        // 1. Program Studi
        $sql_ps = "SELECT 
                    kode_prodi,
                    jenjang,
                    prodi,
                    study_program,
                    jurusan,
                    major
                FROM program_study
                WHERE id_kampus = :id_kampus
                ORDER BY jurusan ASC, study_program ASC";
        $stmt_ps = $_SERVER['PDO']->prepare($sql_ps);
        $stmt_ps->execute([':id_kampus' => $id_kampus]);
        $program_study = $stmt_ps->fetchAll(PDO::FETCH_ASSOC);

        // 2. Daftar Major / Jurusan
        $sql_major = "SELECT DISTINCT major, jurusan 
                      FROM program_study 
                      WHERE id_kampus = :id_kampus 
                      ORDER BY major ASC";
        $stmt_major = $_SERVER['PDO']->prepare($sql_major);
        $stmt_major->execute([':id_kampus' => $id_kampus]);
        $majors = $stmt_major->fetchAll(PDO::FETCH_ASSOC);

        // 3. Daftar Perusahaan
        $sql_comp = "SELECT DISTINCT c.id_company, c.name 
                     FROM company c 
                     WHERE c.name NOT LIKE '%<script%' AND c.name != '' 
                     ORDER BY c.name ASC";
        $stmt_comp = $_SERVER['PDO']->query($sql_comp);
        $companies = $stmt_comp->fetchAll(PDO::FETCH_ASSOC);

        renderView('show_data.companies_ranking_received_student', [
            'title'         => 'Data Mahasiswa Magang',
            'route'         => 'dashboard',
            'routes'        => 'dashboard',
            'students'      => $students,
            'program_study' => $program_study,
            'majors'        => $majors,
            'companies'     => $companies,
            'start_date'    => $start_date,
            'end_date'      => $end_date,
            'major'         => $major,
            'prodi'         => $prodi,
            'company'       => $company,
            'search'        => $search
        ]);
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
    
        $id_kampus = $_SESSION['CDC_Data']['id_kampus'] ?? $_SESSION['Lecturer_Data']['id_kampus'] ?? $_SESSION['Lecturer-Data']['id_kampus'] ?? 1;
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
