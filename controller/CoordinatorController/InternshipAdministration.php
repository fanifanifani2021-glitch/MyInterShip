<?php
switch ($method):
    
    default:
        http_response_code(404);
    break;

    case 'index':
        renderView('coordinator.internship_administration.index', ['title' => 'Internship Administration', 'routes' => 'coordinator/internship_administration']);
    break;

    case 'data':
        try {
            // 1. Bersihkan Output Buffer
            if (ob_get_length()) ob_clean();
            header('Content-Type: application/json');

            $pdo = $_SERVER['PDO'];
            
            // Ambil ID Kampus
            $id_kampus = $_SESSION['Lecturer-Data']['id_kampus'] ?? 1; 

            // 2. Definisi Kolom Sorting
            $columns = [
                0 => 'i.id_internship',
                1 => 'si.name',
                2 => 'co.name',
                3 => 'i.status',
                4 => 'i.start_date',
            ];

            // 3. BASE QUERY
            // PERBAIKAN DI SINI: Gunakan COALESCE pada ifb.terisi
            // Agar jika datanya NULL (karena left join), otomatis dianggap 0
            $sql_base = "
                SELECT 
                    i.id_internship, i.status, i.start_date, i.end_date, i.final_report,
                    si.name AS student_name, 
                    si.nim, 
                    
                    -- Parsing Angkatan --
                    SUBSTRING(si.nim, 4, 2) AS generation, 
                    
                    ps.kode_prodi, ps.jenjang, ps.study_program AS prodi, ps.major,
                    co.name AS company_name,
                    
                    -- Validasi Feedback --
                    sfb.id_student_feedback, 
                    sfb.posisi AS sfb_posisi,
                    
                    -- INDUSTRY FEEDBACK LOGIC --
                    ifb.id_industry_feedback,
                    COALESCE(ifb.terisi, 0) AS ifb_terisi -- Paksa jadi 0 jika NULL

                FROM internship i
                JOIN student_internship si ON i.nim = si.nim
                JOIN company co ON i.id_company = co.id_company
                LEFT JOIN program_study ps ON si.program_study = ps.kode_prodi
                LEFT JOIN student_feedback sfb ON i.id_internship = sfb.id_internship
                LEFT JOIN industry_feedback ifb ON i.id_internship = ifb.id_internship
                
                WHERE ps.id_kampus = :id_kampus
            ";

            $params = [':id_kampus' => $id_kampus];

            // 4. DYNAMIC FILTERING

            // ... (Filter Major, Prodi, Generation, Status, Dates SAMA SEPERTI SEBELUMNYA) ...
            // A. Filter Major
            if (!empty($_POST['major']) && $_POST['major'] != 'All') {
                $sql_base .= " AND ps.major = :major";
                $params[':major'] = $_POST['major'];
            }
            // B. Filter Prodi
            if (!empty($_POST['program_study'])) {
                $val = $_POST['program_study'];
                if (strpos($val, 'Major:') === 0) {
                    $sql_base .= " AND ps.major = :filter_major";
                    $params[':filter_major'] = explode(':', $val)[1];
                } elseif (strpos($val, 'PS:') === 0) {
                    $sql_base .= " AND ps.kode_prodi = :filter_prodi";
                    $params[':filter_prodi'] = explode(':', $val)[1];
                }
            }
            // C. Filter Generation
            if (!empty($_POST['generation']) && $_POST['generation'] != 'All') {
                $sql_base .= " AND SUBSTRING(si.nim, 4, 2) = :generation";
                $params[':generation'] = $_POST['generation'];
            }
            // D. Internship Status
            if (!empty($_POST['internship_status']) && $_POST['internship_status'] != 'All') {
                $statusMap = [
                    'Waiting for Validation' => 'Waiting',
                    'Internship in Progress' => 'Yes',
                    'Internship Completed'   => 'Finish',
                    'Internship Rejected'    => 'No'
                ];
                $dbStatus = $statusMap[$_POST['internship_status']] ?? $_POST['internship_status'];
                $sql_base .= " AND i.status = :status";
                $params[':status'] = $dbStatus;
            }
            // E. Dates
            if (!empty($_POST['from_date'])) {
                $sql_base .= " AND i.start_date >= :from_date";
                $params[':from_date'] = $_POST['from_date'];
            }
            if (!empty($_POST['to_date'])) {
                $sql_base .= " AND i.start_date <= :to_date";
                $params[':to_date'] = $_POST['to_date'];
            }

            // --- F. Feedbacks (PERBAIKAN LOGIC DISINI) ---
            
            // Student Feedback (Logic Tetap)
            if (!empty($_POST['std_fb_status']) && $_POST['std_fb_status'] != 'All') {
                if ($_POST['std_fb_status'] == 'filled') {
                    $sql_base .= " AND (sfb.id_student_feedback IS NOT NULL AND sfb.posisi != '-')"; 
                } else {
                    $sql_base .= " AND (sfb.id_student_feedback IS NULL OR sfb.posisi = '-')";
                }
            }
            
            // Industry Feedback (PERBAIKAN UTAMA)
            // Logic: 
            // Filled  = ID Ada DAN Terisi = 1
            // Empty   = ID Null ATAU (ID Ada TAPI Terisi = 0)
            if (!empty($_POST['ind_fb_status']) && $_POST['ind_fb_status'] != 'All') {
                if ($_POST['ind_fb_status'] == 'filled') {
                    // Pastikan benar-benar sudah diisi (1)
                    $sql_base .= " AND (ifb.id_industry_feedback IS NOT NULL AND ifb.terisi = 1)";
                } else {
                    // Kosong jika record tidak ada (NULL) ATAU record ada tapi flag 0
                    $sql_base .= " AND (ifb.id_industry_feedback IS NULL OR ifb.terisi = 0)";
                }
            }

            // Final Report (Logic Tetap)
            if (!empty($_POST['final_rpt_status']) && $_POST['final_rpt_status'] != 'All') {
                if ($_POST['final_rpt_status'] == 'uploaded') {
                    $sql_base .= " AND (i.final_report IS NOT NULL AND i.final_report != '' AND i.final_report != '-')";
                } else {
                    $sql_base .= " AND (i.final_report IS NULL OR i.final_report = '' OR i.final_report = '-')";
                }
            }

            // G. Global Search
            if (!empty($_POST['search']['value'])) {
                $search = $_POST['search']['value'];
                $sql_base .= " AND (si.name LIKE :search OR si.nim LIKE :search OR co.name LIKE :search)";
                $params[':search'] = "%$search%";
            }

            // 5. Hitung Total Data (Filtered)
            $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM ($sql_base) AS count_table");
            $stmtCount->execute($params);
            $totalFiltered = $stmtCount->fetchColumn();
            $totalData = $totalFiltered; 

            // 6. Ordering & Pagination
            if (isset($_POST['order'])) {
                $colIdx = $_POST['order'][0]['column'];
                $colDir = $_POST['order'][0]['dir'];
                $colName = $columns[$colIdx] ?? 'i.id_internship';
                $sql_base .= " ORDER BY $colName $colDir";
            } else {
                $sql_base .= " ORDER BY i.id_internship DESC";
            }

            if (isset($_POST['start']) && $_POST['length'] != -1) {
                $sql_base .= " LIMIT :start, :length";
            }

            // 7. Eksekusi Query
            $stmt = $pdo->prepare($sql_base);
            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val);
            }
            if (isset($_POST['start'])) {
                $stmt->bindValue(':start', (int)$_POST['start'], PDO::PARAM_INT);
                $stmt->bindValue(':length', (int)$_POST['length'], PDO::PARAM_INT);
            }
            $stmt->execute();
            $dataRaw = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 8. Formatting Data
            $data = [];
            foreach ($dataRaw as $row) {
                $start = $row['start_date'] ? date('d M Y', strtotime($row['start_date'])) : '-';
                $end   = $row['end_date'] ? date('d M Y', strtotime($row['end_date'])) : '-';
                
                // --- LOGIC LINK BARU (UPDATED) ---

                // Final Report
                $final_report_link = (!empty($row['final_report']) && $row['final_report'] != '-') 
                    ? 'index.php?viewer=final_report&file=' . base64_encode($row['final_report']) 
                    : null;

                // Student Feedback
                $is_std_filled = ($row['id_student_feedback'] && $row['sfb_posisi'] && $row['sfb_posisi'] != '-');
                $std_fb_link = $is_std_filled
                    ? base_url('index.php?page=print_student_feedback&id_student_feedback=' . base64_encode($row['id_student_feedback'])) 
                    : null;

                // Industry Feedback
                // PERBAIKAN PHP LOGIC:
                // Cek ID harus ada DAN status terisi harus == 1
                // Karena di SQL kita sudah COALESCE ifb_terisi jadi 0 jika null, kita bisa cek value int nya.
                
                $is_ind_filled = ($row['id_industry_feedback'] && (int)$row['ifb_terisi'] === 1);
                
                $ind_fb_link = $is_ind_filled
                    ? base_url('index.php?page=print_industry_feedback&id=' . encrypt($row['id_internship'])) . '&exportLanguage=ID'
                    : null;

                $data[] = [
                    'id_internship'     => $row['id_internship'],
                    'student_name'      => $row['student_name'],
                    
                    'nim'               => $row['nim'], 
                    'nim_encoded'       => base64_encode($row['nim']),
                    
                    'prodi'             => ($row['jenjang'] ?? '') . ' ' . ($row['prodi'] ?? ''),
                    'company_name'      => $row['company_name'],
                    'status'            => $row['status'],
                    'period'            => "$start - $end",
                    'final_report'      => $final_report_link,
                    'student_feedback'  => $std_fb_link,
                    'industry_feedback' => $ind_fb_link,
                    'action'            => base64_encode($row['id_internship'])
                ];
            }

            // 9. Return JSON
            echo json_encode([
                "draw"            => intval($_POST['draw'] ?? 1),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $data
            ]);
            exit;

        } catch (Exception $e) {
            // ... (Error Handling SAMA) ...
            if (class_exists('Logger')) {
                Logger::write('Error_Datatable_Coord', $e->getMessage());
            } else {
                error_log($e->getMessage());
            }

            echo json_encode([
                "draw"            => intval($_POST['draw'] ?? 1),
                "recordsTotal"    => 0,
                "recordsFiltered" => 0,
                "data"            => [],
                "error"           => "Server Error: " . $e->getMessage()
            ]);
            exit;
        }
    break;
endswitch;