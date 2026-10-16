<?php
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

switch ( $method ) :

    default:
        Router::handleError('404', 'Not Found');
    break;

    case 'template_import':
        try {
            // 2. Ambil nama file
            $filenameRaw = isset($_GET['filename']) ? base64_decode($_GET['filename']) : 'template_lecturer.xlsx';
            
            // Validasi nama file agar aman (cegah path traversal)
            $filename = basename($filenameRaw);

            // 3. Inisialisasi Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // 4. LOGIC DATA (Langsung saja tanpa switch jika hanya 1 tipe, atau pakai if)
    
                // --- HEADER ---
                $headers = [
                    'A1' => 'NIK / NIP (Required)', 
                    'B1' => 'Full Name (Required)', 
                    'C1' => 'Email Polibatam (Optional)'
                ];

                foreach ($headers as $cell => $val) {
                    $sheet->setCellValue($cell, $val);
                }

                // --- DUMMY DATA ---
                $dummy = [
                    ['198801012020011001', 'Budi Santoso, M.Kom', 'budi@polibatam.ac.id'],
                    ['199005052021021002', 'Siti Aminah, S.T., M.T.', 'siti@polibatam.ac.id'],
                ];

                $row = 2;
                foreach ($dummy as $data) {
                    $sheet->setCellValue('A' . $row, $data[0]); 
                    $sheet->setCellValue('B' . $row, $data[1]); 
                    $sheet->setCellValue('C' . $row, $data[2]); 
                    
                    // Format Text untuk NIK
                    $sheet->getStyle('A' . $row)->getNumberFormat()
                        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    
                    $row++;
                }

                // --- STYLING ---
                $headerStyle = [
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF1269DB'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ];
                $sheet->getStyle('A1:C1')->applyFromArray($headerStyle);
                $sheet->getRowDimension('1')->setRowHeight(25);

                foreach(range('A','C') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

            // End of Logic Data

            // 5. OUTPUT KE BROWSER
            // HAPUS BUFFER SEBELUMNYA (PENTING UNTUK MENGHINDARI FILE CORRUPT/BLANK)
            if (ob_get_length()) {
                ob_end_clean();
            }

            // Header HTTP
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            header('Expires: 0'); // Pastikan tidak di-cache
            header('Pragma: public');

            // Simpan ke Output PHP
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            
            // HENTIKAN SCRIPT DISINI (PENTING)
            exit; 

        } catch (Exception $e) {
            // Jika error, jangan blank, tapi tampilkan pesan
            // Bersihkan buffer dulu biar pesan error terlihat bersih
            if (ob_get_length()) ob_end_clean();
            
            http_response_code(500);
            die("Error generating Excel: " . $e->getMessage());
        }
    break;
    
    case 'preview_import_lecturer' :
        // 0. DEBUG: Matikan buffer agar error langsung muncul (PENTING untuk kasus blank response)
        if (ob_get_length()) ob_end_clean(); 
        
        // Set Header manual biar kalau error tetap dianggap JSON
        header('Content-Type: application/json; charset=utf-8');

        try {
            // 1. Cek apakah $_FILES ada isinya
            if (empty($_FILES)) {
                // Biasanya karena post_max_size terlampaui
                throw new Exception("Server received no files. Check 'post_max_size' in php.ini");
            }

            // 2. Cek Error Upload bawaan PHP
            if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                $phpFileUploadErrors = [
                    1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
                    2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form',
                    3 => 'The uploaded file was only partially uploaded',
                    4 => 'No file was uploaded',
                    6 => 'Missing a temporary folder',
                    7 => 'Failed to write file to disk.',
                    8 => 'A PHP extension stopped the file upload.',
                ];
                $errorCode = $_FILES['file']['error'];
                $errorMessage = $phpFileUploadErrors[$errorCode] ?? 'Unknown upload error';
                
                throw new Exception("Upload Error ($errorCode): " . $errorMessage);
            }

            if (!isset($_FILES['file']['tmp_name']) || empty($_FILES['file']['tmp_name'])) {
                throw new Exception('File tmp_name is empty.');
            }


            $file = $_FILES['file']['tmp_name'];

            // 3. Cek Ketersediaan Library PhpSpreadsheet
            if (!class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
                throw new Exception('Library PhpSpreadsheet not found / not loaded.');
            }

            // 4. Load Excel (Bungkus Try Catch khusus loading karena ini rawan memory limit)
            try {
                // Naikkan memori limit sementara untuk proses ini
                ini_set('memory_limit', '512M'); 
                ini_set('max_execution_time', 300);

                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
                $sheet       = $spreadsheet->getActiveSheet();
                $rows        = $sheet->toArray();
            } catch (\Throwable $t) {
                throw new Exception("PhpSpreadsheet Error: " . $t->getMessage());
            }

            // Hapus Header
            unset($rows[0]);

            if (empty($rows)) {
                throw new Exception('Excel file is empty (No data found).');
            }

            // 5. Logic Database (Optimized WHERE IN)
            $id_kampus = $_SESSION['CDC-Data']['id_kampus'] ?? 1;
            $excel_data = [];
            $nik_list   = [];

            foreach ($rows as $row) {
                $nik   = trim($row[0] ?? '');
                $nama  = trim($row[1] ?? '');
                $email = trim($row[2] ?? '');

                if (empty($nik)) continue;

                $nik_list[] = $nik;
                $excel_data[] = [
                    'nik'   => $nik,
                    'name'  => $nama,
                    'email' => $email
                ];
            }

            // Remove duplicate NIKs from query list
            $nik_list = array_unique($nik_list);
            $existing_map = [];

            if (!empty($nik_list)) {
                $pdo = $_SERVER['PDO'];
                $placeholders = implode(',', array_fill(0, count($nik_list), '?'));
                $sql = "SELECT nim_nik_unit FROM lecturer WHERE id_kampus = ? AND nim_nik_unit IN ($placeholders)";
                
                $params = array_merge([$id_kampus], $nik_list);
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $existing_niks = $stmt->fetchAll(PDO::FETCH_COLUMN);
                $existing_map = array_flip($existing_niks);
            }



            // 6. Formatting Result
            $result_data = [];
            foreach ($excel_data as $index => $row) {
                $is_exist = isset($existing_map[ $row['nik'] ]);
                $result_data[] = [
                    'index'    => $index,
                    'nik'      => $row['nik'],
                    'name'     => $row['name'],
                    'email'    => $row['email'],
                    'is_exist' => $is_exist,
                    'status'   => $is_exist ? 'Registered' : 'Ready'
                ];
            }

            // 7. RETURN SUKSES (Manual echo json_encode biar yakin)
            echo json_encode([
                'status'  => 1,
                'code'    => 200,
                'message' => 'Success parsing data',
                'data'    => $result_data
            ]);
            exit;

        } catch (Exception $e) {
            // 8. CATCH ERROR (Manual echo)
            http_response_code(400); // Bad Request
            echo json_encode([
                'status'  => 0,
                'code'    => 400,
                'message' => $e->getMessage()
            ]);
            exit;
        }

    break;

    case 'import_lecturer':
        
        // 1. Validasi Input
        $lecturers = $_POST['lecturers'] ?? [];
        
        if (empty($lecturers) || !is_array($lecturers)) {
            Router::json(400, 'No data received or invalid format.');
        }

        // 2. Persiapan Data
        $id_kampus = $_SESSION['CDC-Data']['id_kampus'] ?? 1; // Default 1 jika session hilang
        $success   = 0;
        $failed    = 0;

        try {
            // Mulai Transaksi
            $pdo->beginTransaction();

            // Siapkan Query Insert
            // Kita set password default sama dengan NIK agar user bisa login pertama kali
            $sql = "INSERT INTO lecturer (
                        nim_nik_unit, 
                        name, 
                        email_polibatam, 
                        password, 
                        status, 
                        id_kampus, 
                        created_at
                    ) VALUES (
                        :nik, 
                        :nama, 
                        :email, 
                        :password, 
                        'Active', 
                        :id_kampus, 
                        NOW()
                    )";

            $stmt = $pdo->prepare($sql);

            foreach ($lecturers as $row) {
                
                // Sanitasi data sederhana
                $nik   = trim($row['nik'] ?? '');
                $nama  = trim($row['name'] ?? '');
                $email = trim($row['email'] ?? '');

                // Skip jika NIK kosong (Safety check)
                if (empty($nik)) {
                    $failed++;
                    continue;
                }

                // Generate Default Password (Disini saya pakai NIK)
                $default_password = password_hash($nik, PASSWORD_DEFAULT);
                // Opsi lain: password_hash('123456', PASSWORD_DEFAULT);

                try {
                    $stmt->execute([
                        ':nik'       => $nik,
                        ':nama'      => $nama,
                        ':email'     => $email, // Pastikan kolom DB nullable jika email kosong
                        ':password'  => $default_password,
                        ':id_kampus' => $id_kampus
                    ]);
                    
                    $success++;

                } catch (PDOException $e) {
                    // Tangkap error per baris (misal: Duplicate Entry lolos filter preview)
                    // Code 23000 = Integrity constraint violation (Unique)
                    if ($e->getCode() == '23000') {
                        $failed++; 
                        // Opsi: Log error duplicate
                    } else {
                        // Jika error lain (misal syntax), lempar ke catch utama
                        throw $e; 
                    }
                }
            }

            // Commit Transaksi (Simpan Permanen)
            $pdo->commit();

            // Return JSON Response
            Router::json(200, 'Import Process Completed', [
                'success' => $success,
                'failed'  => $failed
            ]);

        } catch (Exception $e) {
            Router::log('Database_Error', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            // Jika ada error fatal, batalkan semua perubahan
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            
            Router::json(500, 'System Error: ' . $e->getMessage());
        }

    break;

endswitch;