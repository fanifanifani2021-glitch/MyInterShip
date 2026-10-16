<?php

switch ($method) :

    /**
     * Fetch the list of users to be personified based on role and campus
     * This handles the AJAX search from Select2
     */
    default:
        http_response_code(404);
        break;

    /**
     * Fetch the list of campuses for the personification filter
     * Path: ajax/cdc/personify/campus/list
     */
    case 'campus_list':
        // Kita ambil data kampus yang aktif
        // Sesuaikan nama tabel dan kolom dengan database kamu (asumsi: kampus)
        $campuses = DB::table('tb_kampus')
            ->select('id_kampus', 'nama_kampus')
            ->orderBy('nama_kampus', 'ASC')
            ->get();

        // Menggunakan kelas Response yang sudah kita buat sebelumnya
        return Response::json($campuses);
    break;


    case 'prodi_list':
    // 1. Ambil input
    $campusId = Request::input('campus_id');
    $search   = Request::input('q'); 

    // 2. Validasi: Campus ID wajib ada
    if (!$campusId) {
        echo json_encode([]);
        exit;
    }

    try {
        // 3. Query menggunakan selectRaw untuk Concatenation
        // Format: (IF) D3 Teknik Informatika / D3 Informatics Engineering
        $results = ProgramStudy::selectRaw("
                kode_prodi AS id, 
                CONCAT('(', kode_prodi, ') ', jenjang, ' ', prodi, ' / ', jenjang, ' ', study_program) AS text
            ")
            ->where('id_kampus', '=', $campusId)
            ->where('is_deleted', '=', 0)
            ->whereRaw("
                (kode_prodi LIKE ? OR 
                 jenjang LIKE ? OR 
                 prodi LIKE ? OR 
                 study_program LIKE ? OR 
                 jurusan LIKE ? OR 
                 major LIKE ?)
            ", [
                "%$search%", "%$search%", "%$search%", 
                "%$search%", "%$search%", "%$search%"
            ])
            ->orderBy('prodi', 'ASC')
            ->limit(20)
            ->get();

        // 4. Final Output dengan Gatekeeper
        header('Content-Type: application/json; charset=utf-8');
        
        // Langsung echo json_encode untuk menghindari parser error dari class Response
        echo json_encode($results);
        exit;

    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
    break;
    case 'user_list':
        try { 
            // 1. Ambil Input
            $search   = Request::input('q');
            $role     = Request::input('role');
            $campusId = Request::input('campus_id');
            $prodiId  = Request::input('prodi_id');

            // 2. Validasi Awal
            $isCampusRole = in_array($role, ['student', 'lecturer', 'coordinator']);
            if (!$role || ($isCampusRole && empty($campusId))) {
                echo json_encode([]);
                exit;
            }

            $results = [];

            // 3. Query Logic menggunakan DB Class
            switch ($role) {
                case 'student':
                    $results = StudentInternship::select('nim as id', 'CONCAT(nim, " - ", name) as text')
                        ->where('id_kampus', '=', '1')
                        ->where(function($query) use ($search) {
                            $query->where('name', 'LIKE', "%$search%")
                                ->orWhere('nim', 'LIKE', "%$search%");
                        })
                        ->limit(20)
                        ->get();
                    break;

                case 'lecturer':
                    $results = Lecturer::select('nim_nik_unit as id', 'CONCAT(nim_nik_unit, " - ", name) as text')
                        ->where('id_kampus', '=', $campusId)
                        ->where(function($query) use ($search) {
                            $query->where('name', 'LIKE', "%$search%")
                                ->orWhere('nim_nik_unit', 'LIKE', "%$search%");
                        })
                        ->limit(20)
                        ->get();
                    break;

                case 'hrd':
                case 'supervisor':
                    $results = UserCompany::join('company', 'company.id_company', '=', 'user_company.id_company')
                    ->selectRaw("
                        user_company.id_user_company AS id, 
                        CONCAT(company.name, ' (', user_company.user_fullname, ' - ', user_company.username, ')') AS text
                    ")
                    ->where('user_company.user_type', '=', $role)
                    // Gunakan whereRaw untuk pencarian fleksibel (OR)
                    ->whereRaw("
                        (user_company.user_fullname LIKE ? OR 
                        user_company.username LIKE ? OR 
                        company.name LIKE ?)
                    ", ["%$search%", "%$search%", "%$search%"])
                    ->limit(20)
                    ->get();
                break;

                case 'coordinator':
                    if (!$prodiId) {
                        $results = [];
                    } else {
                        /**
                         * Menggunakan Model Lecturer
                         * Kita Join ke tabel program_study untuk memastikan kita mengambil 
                         * dosen yang memang ditugaskan sebagai koordinator di prodi tersebut.
                         */
                        $results = Lecturer::select('nim_nik_unit as id', 'CONCAT(nim_nik_unit, " - ", name) as text')
                            ->join('program_study', 'lecturer.prodi_koor', '=', 'program_study.kode_prodi')
                            ->where('program_study.kode_prodi', '=', $prodiId)
                            ->where('lecturer.is_deleted', '=', 0)
                            ->where('lecturer.is_koor', '=', 1)
                            ->get();
                    }
                    break;
            }

            // 4. Logging untuk Audit Trail PBL
            Logger::write('AJAX_CDC_PERSONIFY', [
                'admin' => $_SESSION['name'] ?? 'Unknown',
                'role_target' => $role,
                'result_count' => count((array)$results)
            ]);

            // 5. FINAL OUTPUT GATEKEEPER
            return Response::json($results);
            
            // Paksa berhenti agar tidak ada output tambahan dari sistem router
            exit; 

        } catch (Exception $e) {
            header('Content-Type: application/json');
            
            Logger::write('AJAX_CDC_ERROR', ['msg' => $e->getMessage()]);
            
            echo json_encode([
                'status' => false,
                'error' => 'Internal Server Error',
                'debug' => $e->getMessage() // Matikan ini saat production
            ]);
            exit;
        }
    break;

    case 'personify_login':
        try {
            $targetId = Request::input('target_id');
            $role     = Request::input('role');

            $user = null;
            if ($role === 'student') $user = StudentInternship::find($targetId);
            elseif (in_array($role, ['lecturer', 'coordinator'])) $user = Lecturer::find($targetId);
            elseif (in_array($role, ['hrd', 'supervisor'])) $user = UserCompany::find($targetId);

            if ($user) {
                // Trait sudah menangani exit & json_encode
                $user->establishSession(true); 
            }

            // Jika tidak ketemu
            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'message' => 'User data not found.']);
            exit;

        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'message' => $e->getMessage()]);
            exit;
        }
    break;

    case 'switch_back':
        // Hapus data personifikasi
        Session::remove('user_personification');
        Session::remove('is_personifying');
        
        make_snackbar('Personification ended. You are back as Admin.');
        Response::redirect(router('landing')); // Kembali ke dashboard CDC
        exit;
    break;

endswitch;