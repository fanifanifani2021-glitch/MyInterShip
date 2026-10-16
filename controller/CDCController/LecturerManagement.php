<?php
defined('BASE_DIR') or die('Direct script access is Not Allowed');

switch ($method):

    default:
        http_response_code(404);
        break;

    /**
     * Fetch server-side JSON data for Lecturers DataTable.
     * * @param int $_POST['length'] Limit data.
     * @param int $_POST['start'] Offset data.
     * @param array $_POST['search'] Search value.
     * @return string JSON Response for DataTables.
     */
    case 'data':
        $limit     = (int) $_POST['length'];
        $offset    = (int) $_POST['start'];
        $search    = trim($_POST['search']['value'] ?? '');
        $id_kampus = $_SESSION['CDC-Data']['id_kampus'];

        $where = " WHERE lecturer.id_kampus = :id_kampus AND is_deleted = false ";
        if ($search !== '') {
            $where .= " AND (lecturer.nim_nik_unit LIKE :search OR lecturer.name LIKE :search OR lecturer.email_polibatam LIKE :search) ";
        }

        // --- HITUNG TOTAL ---
        $stmtTotal = $_SERVER['PDO']->prepare("SELECT COUNT(*) FROM lecturer WHERE id_kampus = :id_kampus AND is_deleted = false");
        $stmtTotal->execute([':id_kampus' => $id_kampus]);
        $recordsTotal = $stmtTotal->fetchColumn();

        // --- HITUNG FILTERED ---
        $stmtFiltered = $_SERVER['PDO']->prepare("SELECT COUNT(*) FROM lecturer $where");
        $stmtFiltered->bindParam(':id_kampus', $id_kampus);
        if ($search !== '') {
            $searchParam = "%$search%";
            $stmtFiltered->bindParam(':search', $searchParam);
        }
        $stmtFiltered->execute();
        $recordsFiltered = $stmtFiltered->fetchColumn();

        // --- QUERY UTAMA ---
        $query = "SELECT lecturer.*, 
                  (SELECT COUNT(*) FROM student_internship WHERE student_internship.nik_dospem = lecturer.nim_nik_unit) AS jumlah_mahasiswa
                  FROM lecturer $where ORDER BY lecturer.name ASC LIMIT :offset, :limit";

        $stmt = $_SERVER['PDO']->prepare($query);
        $stmt->bindParam(':id_kampus', $id_kampus);
        if ($search !== '') $stmt->bindParam(':search', $searchParam);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $data = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id_encoded = base64_encode($row['nim_nik_unit']);
            $raw_name   = addslashes($row['name']); // Untuk handle kutip di JS

            $data[] = [
                'checkbox' => "<input type='checkbox' class='row-check' data-id='{$id_encoded}' data-name='". htmlspecialchars($row['name'])."'>",
                'nim_nik_unit' => htmlspecialchars($row['nim_nik_unit']),
                'name' => htmlspecialchars($row['name']),
                'email' => htmlspecialchars($row['email_polibatam']),
                'status' => $row['status'],
                'jumlah_mahasiswa' => $row['jumlah_mahasiswa'] . ' Student(s)',
                'aksi' => "
                    <div style='white-space: nowrap;'>
                        <a href='" . router('lecturer.edit', ['id' => $id_encoded]) . "' class='btn btn-sm btn-warning mr-1' title='Edit'>
                            <i class='fas fa-edit text-white'></i>
                        </a>

                        <button type='button' class='btn btn-sm btn-info mr-1' 
                                onclick=\"reset_password('$id_encoded', '$raw_name')\" title='Reset Password'>
                            <i class='fas fa-key'></i>
                        </button>

                        <button type='button' class='btn btn-sm btn-danger' onclick=\"delete_data('$id_encoded')\" title='Delete'>
                            <i class='fas fa-trash-alt'></i>
                        </button>
                    </div>
                ",
            ];
        }

        echo json_encode([
            'draw' => intval($_POST['draw']),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    break;

    /**
     * Logic to Reset Lecturer Password to default.
     */
    case 'reset_password':
        try {
            $id = base64_decode(Request::input('id'));
            
            // Contoh Logic: Reset password ke NIM dosen tersebut
            // Pastikan menggunakan Hash class yang sudah kita buat
            $newPassword = Hash::make($id); 

            $update = DB::table('lecturer')
                ->where('nim_nik_unit', $id)
                ->update(['password' => $newPassword]);

            if ($update) {
                echo json_encode(['status' => true, 'message' => "Password for lecturer with NIK : $id has been reset to default."]);
            } else {
                echo json_encode(['status' => false, 'message' => "No changes made or user not found."]);
            }
            exit;
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => false, 'message' => $e->getMessage()]);
            exit;
        }
    break;


    case 'destroy':
        // 1. Ambil Input (Masih ter-encode Base64)
        $id_raw = $_POST['nim_nik_unit'] ?? null;

        if (empty($id_raw)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'ID is required']);
            exit;
        }

        // 2. Decode ID
        $nim_nik_unit = base64_decode($id_raw);

        // 3. Ambil Info Session
        $id_kampus = $_SESSION['CDC-Data']['id_kampus'];
        
        // Siapkan string "deleted_by"
        $deleted_by_str = $_SESSION['CDC-Data']['username'] . ' - ' . $_SESSION['CDC-Data']['name'];

        try {
            $pdo->beginTransaction();

            // 4. Query Soft Delete (Update is_deleted = 1)
            $sql = "UPDATE lecturer 
                    SET is_deleted = 1, 
                        deleted_at = CURRENT_TIMESTAMP, 
                        deleted_by = ? 
                    WHERE id_kampus = ? 
                    AND nim_nik_unit = ?";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$deleted_by_str, $id_kampus, $nim_nik_unit]);

            if ($stmt->rowCount() === 0) {
                // Opsional: Cek jika data tidak ditemukan atau sudah terhapus
                throw new Exception("Data not found or already deleted.");
            }

            $pdo->commit();
            echo json_encode(['status' => 'success', 'message' => 'Lecturer deleted successfully']);

        } catch (Exception $e) {
            $pdo->rollBack();
            Router::log("Database_Error", ['message' => $e->getMessage(), 'id' => $nim_nik_unit]);
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    break;

    case 'bulk_destroy':
        // 1. Ambil & Bersihkan Input
        $is_select_all    = filter_var($_POST['select_all'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $selected_ids_raw = $_POST['selected_ids'] ?? [];
        $except_ids_raw   = $_POST['except_ids'] ?? [];
        
        // Ambil Info Session
        $id_kampus = $_SESSION['CDC-Data']['id_kampus'];
        
        // Siapkan string "deleted_by" di luar SQL agar rapi
        $deleted_by_str = $_SESSION['CDC-Data']['username'] . ' - ' . $_SESSION['CDC-Data']['name'];

        try {
            $pdo->beginTransaction();

            // --- LOGIC UTAMA ---
            
            // SKENARIO 1: Mode SELECT ALL (Hapus semua KECUALI yang di-uncheck)
            // JS mengirim: select_all = true, except_ids = [...]
            if ($is_select_all) {
                
                // Decode ID Pengecualian
                $except_ids = [];
                if (is_array($except_ids_raw) && !empty($except_ids_raw)) {
                    $except_ids = array_map('base64_decode', $except_ids_raw);
                }

                if (!empty($except_ids)) {
                    // Hapus SEMUA, KECUALI ID tertentu (NOT IN)
                    $placeholders = implode(',', array_fill(0, count($except_ids), '?'));
                    
                    $sql = "UPDATE lecturer 
                            SET is_deleted = 1, 
                                deleted_at = CURRENT_TIMESTAMP, 
                                deleted_by = ? 
                            WHERE id_kampus = ? 
                            AND nim_nik_unit NOT IN ($placeholders)";
                    
                    // Urutan param: deleted_by, id_kampus, ...except_ids
                    $params = array_merge([$deleted_by_str, $id_kampus], $except_ids);
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($params);

                } else {
                    // Hapus SEMUA tanpa pengecualian
                    $sql = "UPDATE lecturer 
                            SET is_deleted = 1, 
                                deleted_at = CURRENT_TIMESTAMP, 
                                deleted_by = ? 
                            WHERE id_kampus = ?";
                    
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$deleted_by_str, $id_kampus]);
                }

            } 
            // SKENARIO 2: Mode MANUAL (Hanya hapus yang dipilih)
            // JS mengirim: select_all = false, selected_ids = [...]
            else {
                
                if (!is_array($selected_ids_raw) || empty($selected_ids_raw)) {
                    throw new Exception("No data selected to delete.");
                }

                // Decode ID yang dipilih
                $ids_to_delete = array_map('base64_decode', $selected_ids_raw);
                
                // Gunakan IN untuk menghapus ID spesifik
                $placeholders = implode(',', array_fill(0, count($ids_to_delete), '?'));

                $sql = "UPDATE lecturer 
                        SET is_deleted = 1, 
                            deleted_at = CURRENT_TIMESTAMP, 
                            deleted_by = ? 
                        WHERE id_kampus = ? 
                        AND nim_nik_unit IN ($placeholders)";
                
                // Urutan param: deleted_by, id_kampus, ...ids_to_delete
                $params = array_merge([$deleted_by_str, $id_kampus], $ids_to_delete);

                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
            }

            $pdo->commit();
            echo json_encode(['status' => 'success', 'message' => 'Data deleted successfully']);

        } catch (Exception $e) {
            $pdo->rollBack();
            Router::log("Database_Error", ['message' => $e->getMessage(), 'code' => $selected_ids_raw ]);
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }

    break;


    case 'data_recycle':

        $limit  = (int) $_POST['length'];
        $offset = (int) $_POST['start'];
        $search = trim($_POST['search']['value'] ?? '');
        $id_kampus = $_SESSION['CDC-Data']['id_kampus']; // wajib

        /* ===============================
        * BASE WHERE
        * =============================== */
        $where = " WHERE lecturer.id_kampus = :id_kampus AND is_deleted = true ";

        if ($search !== '') {
            $where .= "
                AND (
                    lecturer.nim_nik_unit LIKE :search
                    OR lecturer.name LIKE :search
                    OR lecturer.email_polibatam LIKE :search
                )
            ";
        }

        /* ===============================
        * TOTAL DATA
        * =============================== */
        $stmtTotal = $_SERVER['PDO']->prepare("
            SELECT COUNT(*) 
            FROM lecturer
            WHERE id_kampus = :id_kampus AND is_deleted = true 
        ");
        $stmtTotal->execute([
            ':id_kampus' => $id_kampus
        ]);
        $recordsTotal = $stmtTotal->fetchColumn();

        /* ===============================
        * FILTERED DATA
        * =============================== */
        $stmtFiltered = $_SERVER['PDO']->prepare("
            SELECT COUNT(*)
            FROM lecturer
            $where
        ");

        $stmtFiltered->bindParam(':id_kampus', $id_kampus);

        if ($search !== '') {
            $searchParam = "%$search%";
            $stmtFiltered->bindParam(':search', $searchParam);
        }

        $stmtFiltered->execute();
        $recordsFiltered = $stmtFiltered->fetchColumn();

        /* ===============================
        * MAIN QUERY
        * =============================== */
        $query = "
            SELECT 
                lecturer.*,
                (
                    SELECT COUNT(*) 
                    FROM student_internship 
                    WHERE student_internship.nik_dospem = lecturer.nim_nik_unit
                ) AS jumlah_mahasiswa
            FROM lecturer
            $where
            ORDER BY lecturer.name ASC
            LIMIT :offset, :limit
        ";

        $stmt = $_SERVER['PDO']->prepare($query);
        $stmt->bindParam(':id_kampus', $id_kampus);

        if ($search !== '') {
            $stmt->bindParam(':search', $searchParam);
        }

        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        /* ===============================
        * DATA OUTPUT
        * =============================== */
        $data = [];
        $no = $offset + 1;

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $id_encoded = base64_encode($row['nim_nik_unit']);

            $data[] = [
                // 'no' => $no++,
                'checkbox' => "
                    <input type='checkbox'
                        class='row-check'
                        data-id='{$id_encoded}'
                        data-name='". htmlspecialchars($row['name']). "'>
                ",
                'nim_nik_unit' => htmlspecialchars($row['nim_nik_unit']),
                'name' => htmlspecialchars($row['name']),
                'email' => htmlspecialchars($row['email_polibatam']),
                'status' => $row['status'],
                'jumlah_mahasiswa' => $row['jumlah_mahasiswa'] . ' Student(s)',
                'aksi' => '<button 
                        class="btn btn-sm btn-success btn-restore"
                        data-id="'.$id_encoded.'">
                        <i class="fas fa-undo mr-1"></i> Restore
                    </button>

                    <button 
                        class="btn btn-sm btn-danger btn-force-delete"
                        data-id="'.$id_encoded.'">
                        <i class="fas fa-trash-alt mr-1"></i> Delete Permanently
                    </button>'
            ];
        }

        /* ===============================
        * RESPONSE
        * =============================== */
        echo json_encode([
            'draw' => intval($_POST['draw']),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    break;

    case 'restore':
        $nim_nik_unit_raw = $_POST['nim_nik_unit'] ?? null;

        if (empty($nim_nik_unit_raw)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'ID is required']);
            exit;
        }

        // 1. Decode ID
        $nim_nik_unit = base64_decode($nim_nik_unit_raw);
        
        $id_kampus = $_SESSION['CDC-Data']['id_kampus'];

        try {
            $pdo->beginTransaction();

            // 2. Query Restore (Set is_deleted = 0)
            $stmt = $pdo->prepare("
                UPDATE lecturer 
                SET is_deleted = 0, 
                    deleted_at = NULL, 
                    deleted_by = NULL 
                WHERE id_kampus = ? AND nim_nik_unit = ?
            ");
            
            $stmt->execute([$id_kampus, $nim_nik_unit]);

            if ($stmt->rowCount() === 0) {
                throw new Exception("Data not found or already active.");
            }

            $pdo->commit();
            echo json_encode(['status' => 'success', 'message' => 'Lecturer restored successfully']);

        } catch (Exception $e) {
            $pdo->rollBack();
            Router::log("Database_Error", ['message' => $e->getMessage()]);
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    break;

    case 'permanent_destroy':
        // Perhatikan: Di JS Force Delete Anda mengirim 'id_location', 
        // jadi kita tangkap itu disini.
        $id_raw = $_POST['nim_nik_unit'] ?? null;

        if (empty($id_raw)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'ID is required']);
            exit;
        }

        // 1. Decode ID
        $nim_nik_unit = base64_decode($id_raw);
        
        $id_kampus = $_SESSION['CDC-Data']['id_kampus'];

        try {
            $pdo->beginTransaction();

            // 2. Query Hard Delete (Hapus Permanen dari DB)
            $stmt = $pdo->prepare("
                DELETE FROM lecturer 
                WHERE id_kampus = ? AND nim_nik_unit = ?
            ");
            
            $stmt->execute([$id_kampus, $nim_nik_unit]);

            if ($stmt->rowCount() === 0) {
                // Opsional: Cek apakah data memang ada sebelumnya
                throw new Exception("Data not found.");
            }

            $pdo->commit();
            echo json_encode(['status' => 'success', 'message' => 'Lecturer permanently deleted']);

        } catch (Exception $e) {
            $pdo->rollBack();
            Router::log("Database_Error", ['message' => $e->getMessage()]); // Pastikan ID error DB tidak terekspos ke user jika sensitif
            http_response_code(500);
            
            // Cek constraint error (misal data masih dipakai di tabel lain)
            if (strpos($e->getMessage(), 'Integrity constraint violation') !== false) {
                echo json_encode(['status' => 'error', 'message' => 'Cannot delete: This lecturer is linked to other data.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
        }
    break;


    case 'permanent_bulk_destroy':
        // 1. Ambil & Bersihkan Input
        $is_select_all    = filter_var($_POST['select_all'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $selected_ids_raw = $_POST['selected_ids'] ?? [];
        $except_ids_raw   = $_POST['except_ids'] ?? [];
        $search_value     = $_POST['search_value'] ?? ''; // <--- PENTING: Ambil search value
        
        // Ambil Info Session
        $id_kampus = $_SESSION['CDC-Data']['id_kampus'];
        
        // Note: DELETE permanen tidak butuh deleted_by, karena barisnya hilang dari DB.
        // Kecuali kamu punya tabel log terpisah.

        try {
            $pdo->beginTransaction();

            // --- SKENARIO 1: Mode SELECT ALL (Filtered / Global) ---
            if ($is_select_all) {
                
                // A. Decode ID Pengecualian
                $except_ids = [];
                if (is_array($except_ids_raw) && !empty($except_ids_raw)) {
                    $except_ids = array_map('base64_decode', $except_ids_raw);
                }

                // B. Bangun Query Dasar (TARGET HANYA DATA SAMPAH)
                $sql = "DELETE FROM lecturer 
                        WHERE id_kampus = ? 
                        AND is_deleted = 1"; // <--- Safety guard: Hanya hapus yg sudah soft-deleted
                
                $params = [$id_kampus];

                // C. Terapkan Filter Pencarian (PENTING AGAR TIDAK SALAH HAPUS)
                if (!empty($search_value)) {
                    $sql .= " AND (
                                nim_nik_unit LIKE ? OR 
                                name LIKE ? 
                            )"; 
                    $keyword = "%$search_value%";
                    $params[] = $keyword;
                    $params[] = $keyword;
                }

                // D. Terapkan Pengecualian (NOT IN)
                if (!empty($except_ids)) {
                    $placeholders = implode(',', array_fill(0, count($except_ids), '?'));
                    $sql .= " AND nim_nik_unit NOT IN ($placeholders)";
                    
                    // Gabungkan params
                    $params = array_merge($params, $except_ids);
                }

                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);

            } 
            // --- SKENARIO 2: Mode MANUAL (Hapus Checklist) ---
            else {
                
                if (!is_array($selected_ids_raw) || empty($selected_ids_raw)) {
                    throw new Exception("No data selected to delete permanently.");
                }

                // Decode ID
                $ids_to_delete = array_map('base64_decode', $selected_ids_raw);
                
                // Query DELETE Manual
                $placeholders = implode(',', array_fill(0, count($ids_to_delete), '?'));

                $sql = "DELETE FROM lecturer 
                        WHERE id_kampus = ? 
                        AND is_deleted = 1 
                        AND nim_nik_unit IN ($placeholders)";
                
                // Urutan param: id_kampus, ...ids_to_delete
                $params = array_merge([$id_kampus], $ids_to_delete);

                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
            }

            $pdo->commit();
            echo json_encode(['status' => 'success', 'message' => 'Data deleted permanently']);

        } catch (Exception $e) {
            $pdo->rollBack();
            Router::log("Database_Error", ['message' => $e->getMessage()]);
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }

    break;

    case 'bulk_restore':
        // 1. Ambil & Bersihkan Input
        $is_select_all    = filter_var($_POST['select_all'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $selected_ids_raw = $_POST['selected_ids'] ?? [];
        $except_ids_raw   = $_POST['except_ids'] ?? [];
        
        // Ambil Info Session
        $id_kampus = $_SESSION['CDC-Data']['id_kampus'];
        
        // Siapkan string "deleted_by" di luar SQL agar rapi
        $deleted_by_str = $_SESSION['CDC-Data']['username'] . ' - ' . $_SESSION['CDC-Data']['name'];

        try {
            $pdo->beginTransaction();

            // --- LOGIC UTAMA ---
            
            // SKENARIO 1: Mode SELECT ALL (Hapus semua KECUALI yang di-uncheck)
            // JS mengirim: select_all = true, except_ids = [...]
            if ($is_select_all) {
                
                // Decode ID Pengecualian
                $except_ids = [];
                if (is_array($except_ids_raw) && !empty($except_ids_raw)) {
                    $except_ids = array_map('base64_decode', $except_ids_raw);
                }

                if (!empty($except_ids)) {
                    // Hapus SEMUA, KECUALI ID tertentu (NOT IN)
                    $placeholders = implode(',', array_fill(0, count($except_ids), '?'));
                    
                    $sql = "UPDATE lecturer 
                            SET is_deleted = false, 
                                deleted_at = null, 
                                deleted_by = null 
                            WHERE id_kampus = ? 
                            AND nim_nik_unit NOT IN ($placeholders)";
                    
                    // Urutan param: deleted_by, id_kampus, ...except_ids
                    $params = array_merge([$id_kampus], $except_ids);
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($params);

                } else {
                    // Hapus SEMUA tanpa pengecualian
                    $sql = "UPDATE lecturer 
                            SET is_deleted = false, 
                                deleted_at = null, 
                                deleted_by = null 
                            WHERE id_kampus = ?";
                    
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$id_kampus]);
                }

            } 
            // SKENARIO 2: Mode MANUAL (Hanya hapus yang dipilih)
            // JS mengirim: select_all = false, selected_ids = [...]
            else {
                
                if (!is_array($selected_ids_raw) || empty($selected_ids_raw)) {
                    throw new Exception("No data selected to delete.");
                }

                // Decode ID yang dipilih
                $ids_to_delete = array_map('base64_decode', $selected_ids_raw);
                
                // Gunakan IN untuk menghapus ID spesifik
                $placeholders = implode(',', array_fill(0, count($ids_to_delete), '?'));

                $sql = "UPDATE lecturer 
                        SET is_deleted = false, 
                            deleted_at = null, 
                            deleted_by = null 
                        WHERE id_kampus = ? 
                        AND nim_nik_unit IN ($placeholders)";
                
                // Urutan param: deleted_by, id_kampus, ...ids_to_delete
                $params = array_merge([$id_kampus], $ids_to_delete);

                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
            }

            $pdo->commit();
            echo json_encode(['status' => 'success', 'message' => 'Data restored successfully']);

        } catch (Exception $e) {
            $pdo->rollBack();
            Router::log("Database_Error", ['message' => $e->getMessage(), 'code' => $selected_ids_raw ]);
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }

    break;

    case 'store':
        set_rules('lecturer_nik' , 'NIK' , ['required', 'is_unique:lecturer.nim_nik_unit'], ['required' => 'This field is required !', 'is_unique' => 'NIK has been used !'] );
        set_rules('lecturer_name' , 'Nama' , ['required'], ['required' => 'This field is required !'] );
        set_rules('lecturer_email' , 'E-Mail' , ['required', 'valid_email'], ['required' => 'This field is required !', 'valid_email' => 'This email is not valid !'] );
        set_rules('lecturer_status' , 'Status' , ['required', 'allowed_values:Active,Not Active'], ['required' => 'This field is required !', 'allowed_values' => 'Value is not allowed !'] );

        $cek_form = form_validation();  


        if ( $cek_form === false ) {
            make_snackbar('Check your submitted data and try again later');
            redirect_back(1); exit;
        }

        try {
            // Koneksi database menggunakan PDO
            $pdo = $_SERVER['PDO'];
        
            // Data untuk di-insert
            $data_lecturer = array(
                'nim_nik_unit' => trim($_POST['lecturer_nik']),
                'name' => trim($_POST['lecturer_name']),
                'email_polibatam' => trim($_POST['lecturer_email']),
                'is_koor' => 0,
                'prodi_koor' => NULL,
                'status' => trim($_POST['lecturer_status'] ?? 'Active'),
                'password' => password_hash($_POST['lecturer_nik'] ?? $_SERVER['LECTURER_DEFAULT_PASSWORD'], PASSWORD_DEFAULT),
                'profile_picture' => 'profile.jpg',
                'id_kampus' => $_SESSION['CDC_Data']['id_kampus'] ?? '1',
            );
        
            // Query SQL untuk prepared statement
            $sql = "INSERT INTO lecturer (nim_nik_unit, name, email_polibatam, is_koor, prodi_koor, status, password, profile_picture, id_kampus)
                    VALUES (:nim_nik_unit, :name, :email_polibatam, :is_koor, :prodi_koor, :status, :password, :profile_picture, :id_kampus)";
        
            // Prepare statement
            $stmt = $pdo->prepare($sql);
        
            // Bind parameter ke statement
            $stmt->bindParam(':nim_nik_unit', $data_lecturer['nim_nik_unit']);
            $stmt->bindParam(':name', $data_lecturer['name']);
            $stmt->bindParam(':email_polibatam', $data_lecturer['email_polibatam']);
            $stmt->bindParam(':is_koor', $data_lecturer['is_koor']);
            $stmt->bindParam(':prodi_koor', $data_lecturer['prodi_koor']);
            $stmt->bindParam(':status', $data_lecturer['status']);
            $stmt->bindParam(':password', $data_lecturer['password']);
            $stmt->bindParam(':profile_picture', $data_lecturer['profile_picture']);
            $stmt->bindParam(':id_kampus', $data_lecturer['id_kampus']);
        
            // Eksekusi statement
            $stmt->execute();
        
            // Jika berhasil
            // make_snackbar('New Lecturer has been added successfully');
            make_swal2('success', 'Add Success', 'New Lecturer has been added successfully');
            redirect( router('lecturer.management') , 1);
            exit;
        
        } catch (PDOException $e) {
            // Jika terjadi error
            error_log('Database Error: ' . $e->getMessage()); // Log error ke file log server
            make_snackbar('An error occurred while processing your request. Please try again later.');
            redirect_back(1);
            exit;
        }

    break;

    case 'edit':
            $nim_nik_unit = base64_decode($id_encoded);

            // 2. Ambil ID Kampus dari Session
            $id_kampus = $_SESSION['CDC-Data']['id_kampus'] ?? 0;

            // 3. Cek ke Database
            /** @var PDO $pdo */
            $pdo = $_SERVER['PDO'];

            $stmt = $pdo->prepare("
                SELECT * FROM lecturer 
                WHERE id_kampus = ? 
                AND nim_nik_unit = ? 
                AND is_deleted = 0 
                LIMIT 1
            ");
            
            $stmt->execute([$id_kampus, $nim_nik_unit]);
            $lecturer = $stmt->fetch(PDO::FETCH_ASSOC);

            // 4. Jika data tidak ditemukan / Akses ilegal
            if (!$lecturer) {
                return Router::handleError(404, "Lecturer data not found or access denied.");
            }

            // 5. Render View dengan Data Dosen
            renderView('cdc.lecturer_management.edit', [
                'title'    => 'Edit Lecturer',
                'route'    => 'lecturer_management',
                'data' => $lecturer, // Kirim data hasil query ke view
                'id_encoded' => $id_encoded
            ]);
    break;


    case 'update':
        
        // 0. Cek Session & ID Kampus
        // Pastikan key session konsisten (CDC-Data vs CDC_Data), gunakan fallbacks
        $id_kampus = $_SESSION['CDC-Data']['id_kampus'] ?? ($_SESSION['CDC_Data']['id_kampus'] ?? 0);

        if (empty($id_kampus)) {
            make_snackbar('Session expired. Please login again.', 'error');
            redirect(base_url('login'));
            exit;
        }

        // 1. Ambil Data Input
        // Asumsi: old_lecturer_nik dari form input hidden sudah berupa RAW ID (bukan base64).
        // Jika di view form Anda value-nya base64, uncomment baris bawah ini:
        // $_POST['old_lecturer_nik'] = base64_decode($_POST['old_lecturer_nik']);

        $old_nik = $_POST['old_lecturer_nik'] ?? '';
        $new_nik = $_POST['lecturer_nik'];
        $name    = $_POST['lecturer_name'];
        $email   = $_POST['lecturer_email'];
        $status  = $_POST['lecturer_status'];

        // 2. Validasi Input
        set_rules('old_lecturer_nik', 'Old NIK', 
            ['required', 'exists:lecturer.nim_nik_unit'], 
            ['required' => 'Old NIK missing', 'exists' => 'Original lecturer data not found']
        );
        
        // Validasi Unique: Cek apakah NIK baru sudah dipakai orang lain (kecuali punya sendiri)
        set_rules('lecturer_nik', 'NIK', 
            ['required', 'unique_except:lecturer.nim_nik_unit|' . $old_nik], 
            ['required' => 'NIK is required', 'unique_except' => 'NIK already used by another lecturer']
        );
        
        set_rules('lecturer_name', 'Nama', ['required'], ['required' => 'Name is required']);
        set_rules('lecturer_email', 'E-Mail', ['required', 'valid_email'], ['required' => 'Email is required', 'valid_email' => 'Invalid email format']);
        set_rules('lecturer_status', 'Status', ['required', 'allowed_values:Active,Not Active'], ['required' => 'Status is required']);

        $cek_form = form_validation(); 

        if ($cek_form === false) {
            make_snackbar('Check your submitted data and try again later', 'error');
            redirect_back(1); 
            exit;
        }

        /** @var PDO $pdo */
        $pdo = $_SERVER['PDO']; 

        try {
            // 3. Mulai Transaksi
            $pdo->beginTransaction();

            // 4. Query Update
            // Mengubah Primary Key (nim_nik_unit) diperbolehkan jika tidak ada constraint CASCADE yang melarang.
            $sql = "UPDATE lecturer 
                    SET nim_nik_unit = :new_nik, 
                        name = :name, 
                        email_polibatam = :email, 
                        status = :status,
                        updated_at = NOW() 
                    WHERE nim_nik_unit = :old_nik 
                    AND id_kampus = :id_kampus";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':new_nik'   => $new_nik,
                ':name'      => $name,
                ':email'     => $email,
                ':status'    => $status,
                ':old_nik'   => $old_nik,
                ':id_kampus' => $id_kampus
            ]);

            // Cek error code manual (opsional jika PDO mode exception)
            if ($stmt->errorCode() !== '00000') {
                throw new Exception("Database error: " . implode(", ", $stmt->errorInfo()));
            }

            // 5. Commit Transaksi
            $pdo->commit();

            make_swal2('success', 'Edit Success', 'Lecturer has been edited successfully');
            
            // Redirect ke halaman list/management
            redirect( router('lecturer.management') , 1); 
            exit;

        } catch (Exception $e) {
            // 6. Rollback jika gagal
            $pdo->rollBack();

            // Cek apakah error karena Duplicate Entry (Code 1062 untuk MySQL)
            if (strpos($e->getMessage(), '1062 Duplicate entry') !== false) {
                 make_snackbar('Failed: NIK or Email already exists.', 'error');
            } else {
                 make_snackbar('System Error: ' . $e->getMessage(), 'error');
            }
            
            redirect_back(1);
            exit;
        }
    break;

endswitch;