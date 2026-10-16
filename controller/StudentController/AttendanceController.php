<?php

try {
    // 1. Dekripsi ID Internship (Keamanan Dasar)
    $id_internship = Encrypter::decrypt($id);
    $nim = Session::get('nim');

    // 2. Verifikasi Kepemilikan Data
    $internship = DB::table('internship')
        ->where('id_internship', $id_internship)->where('nim', $nim)->first();

    if (!$internship) {
        Notification::swal2('Denied', 'Internship data not found!', 'error');
        Response::redirect(router('student.internship'))->send(); exit;
    }
} catch (Exception $e) {
    Notification::swal2('Security Warning', 'Invalid Identifier.', 'warning');
    Response::redirect(router('student.internship'))->send(); exit;
}

/**
 * Helper: Menghitung Jarak GPS (Haversine)
 */
function getDistance($lat1, $lon1, $lat2, $lon2) {
    $R = 6371000;
    $dLat = deg2rad($lat2 - $lat1); $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
    return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
}

switch ($method):
case 'index':
    // 1. Ambil data dasar periode magang
    $today = date('Y-m-d');
    $start = $internship['start_date'];
    $end   = $internship['end_date'];
    
    // 2. Perbaikan Logic Tahun & Bulan (Dinamis sesuai masa magang)
    $startYear = (int)date('Y', strtotime($start));
    $endYear   = (int)date('Y', strtotime($end));
    
    // Default view: Jika hari ini sudah lewat, tampilkan bulan terakhir magang
    $defaultMonth = ($today > $end) ? (int)date('n', strtotime($end)) : (int)date('n');
    $defaultYear  = ($today > $end) ? $endYear : (int)date('Y');
    
    $month = (int)Request::get('month', $defaultMonth);
    $year  = (int)Request::get('year', $defaultYear);

    // 3. Hitung Statistik untuk Completion Bar
    $dStart = new DateTime($start);
    $dEnd   = new DateTime($end);
    $totalDays = $dStart->diff($dEnd)->days + 1;
    
    $attendanceCount = DB::table('attendance')
        ->where('id_internship', $id_internship)
        ->count();
    $percentage = ($totalDays > 0) ? round(($attendanceCount / $totalDays) * 100) : 0;

    // 4. Logika Hak Akses Tombol
    $isInternshipActive = ($today >= $start && $today <= $end);
    
    // Tombol Manual SELALU MUNCUL agar data tertinggal bisa diisi
    $canAddManual = true; 

    // Live GPS Check-In HANYA saat magang masih berjalan (Live Verification)
    $todayRecord = DB::table('attendance')->where('id_internship', $id_internship)->where('date', $today)->first();
    $canCheckIn  = ($isInternshipActive && !$todayRecord);
    $canCheckOut = ($todayRecord && $todayRecord['attendance_type'] === 'Present' && $todayRecord['check_out'] === '-');

    // 5. Query List Data Kalender
    $attendance = DB::table('attendance')
        ->where('id_internship', '=', $id_internship)
        ->whereRaw("MONTH(date) = ?", [$month])
        ->whereRaw("YEAR(date) = ?", [$year])
        ->get();

    return renderView('student.my_internship.attendance.index', [
        'title'                 => 'Attendance Management',
        'routes'                => 'my_internship',
        'exec'                  => $attendance,
        'id_enc'                => $id,
        'month'                 => $month,
        'year'                  => $year,
        'start_year'            => $startYear,
        'end_year'              => $endYear,
        'internship_start'      => $start,
        'internship_end'        => $end,
        'attendance_percentage' => $percentage,
        'attendance_count'      => $attendanceCount,
        'total_period_days'     => $totalDays,
        'can_check_in'          => $canCheckIn,
        'can_check_out'         => $canCheckOut,
        'can_add_manual'        => $canAddManual,
        'is_internship_active'  => $isInternshipActive,
        'today_att_id'          => $todayRecord ? Encrypter::encrypt($todayRecord['id_attendance']) : null
    ]);
break;

    case 'checkIn':
        // 1. Cek apakah sudah ada data absensi untuk hari ini
        $today = date('Y-m-d');
        $alreadySubmitted = DB::table('attendance')
            ->where('id_internship', '=', $id_internship)
            ->where('date', '=', $today)
            ->exists();

        if ($alreadySubmitted) {
            // Jika sudah ada, tolak dan kembalikan ke halaman sebelumnya
            Notification::swal2('Already Submitted', 'You have already recorded your attendance for today.', 'warning');
            Redirect::back(1);
            exit;
        }

        // 2. Ambil data aturan lokasi (Geofencing) berdasarkan ID Perusahaan
        // Internship data sudah tersedia di preamble (bagian atas file)
        $locations = DB::table('attendance_location')
            ->where('id_company', '=', $internship['id_company'])
            ->where('is_deleted', '=', 0)
            ->get();

        // 3. Kirim data ke View agar peta bisa dirender dengan benar
        return renderView('student.my_internship.attendance.check_in', [
            'title'                => 'Check In Attendance',
            'routes'               => 'my_internship',
            'id_enc'               => $id, // ID Terenkripsi untuk Breadcrumbs/Form Action
            'attendance_locations' => $locations,
            'has_location_rule'    => (count($locations) > 0)
        ]);
    break;

    case 'checkIn-store':
        // 1. Ambil data input
        $user_lat = Request::post('latitude');
        $user_lng = Request::post('longitude');
        
        // Ambil aturan lokasi (Internship data sudah ada di preamble)
        $rules = DB::table('attendance_location')->where('id_company', $internship['id_company'])->where('is_deleted', 0)->get();

        if ( count($rules) > 1 ) {
            
            $is_inside = false; $min_dist = null; $nearest = null;

            foreach ($rules as $loc) {
                $dist = getDistance($user_lat, $user_lng, $loc['latitude'], $loc['longitude']);
                if ($min_dist === null || $dist < $min_dist) { $min_dist = $dist; $nearest = $loc; }
                if ($dist <= $loc['radius_meters']) $is_inside = true;
            }

            if (!$is_inside) {
                Notification::snackbar("Outside Area (".round($min_dist)."m)");
                Redirect::back(1); exit;
            }
        }

        // 2. Mulai Transaksi menggunakan class DB
        DB::beginTransaction();
        
        try {
            // Simpan data presensi utama
            $att_id = DB::table('attendance')->insertGetId([
                'id_internship' => $id_internship, 
                'nim' => $nim, 
                'attendance_type' => 'Present',
                'date' => date('Y-m-d'), 
                'week' => ceil(date('j')/7), 
                'check_in' => date('Y-m-d H:i:s'),
                'description' => '-',
                'check_out' => '-', 
                'approval_spv' => 'Waiting', 
                'approval_dpm' => 'Waiting', 
                'is_history' => 0
            ]);

            if ( count($rules) > 1 ) {
                // Simpan log lokasi GPS
                DB::table('attendance_record_location')->insert([
                    'id_attendance'   => $att_id, 
                    'latitude'        => $user_lat, 
                    'longitude'       => $user_lng,
                    'accuracy'        => Request::post('accuracy'), 
                    'distance_meter'  => $min_dist,
                    'master_location' => $nearest['location'] ?? 'Office', // Sesuaikan key dump: 'location'
                    'master_latitude' => $nearest['latitude'], 
                    'master_longitude' => $nearest['longitude'],
                    'master_radius_meters' => $nearest['radius_meters'], 
                    'attendance_type' => 'IN', 
                    'is_inside'       => 1
                ]);
            }

            
            // 3. Selesaikan Transaksi
            DB::commit();
            
            Notification::swal2('Success', 'Check-in recorded successfully.', 'success');
            Redirect::to(router('student.attendance.index', ['id' => $id])); exit;

        } catch (Exception $e) {
            // 4. Batalkan jika gagal
            DB::rollBack(); 
            
            // Log error untuk debugging
            if (function_exists('dump_to_file')) {
                dump_to_file('attendance_error', $e->getMessage());
            }
            
            Notification::snackbar("Failed to save attendance. Please try again.");
            Redirect::back(1); exit;
        }
    break;

    case 'checkOut':
        try {
            // 1. Logika Penentuan Record (Berdasarkan ID atau Hari Ini)
            if (!empty($attendance_id)) {
                // Jika ada parameter ID, dekripsi dan cari datanya
                $dec_att_id = Encrypter::decrypt($attendance_id);
                $record = DB::table('attendance')
                    ->where('id_attendance', '=', $dec_att_id)
                    ->where('id_internship', '=', $id_internship) // Keamanan: pastikan milik internship ini
                    ->first();
            } else {
                // Fallback: Cari data absensi hari ini (logika lama kamu)
                $today = date('Y-m-d');
                $record = DB::table('attendance')
                    ->where('id_internship', '=', $id_internship)
                    ->where('date', '=', $today)
                    ->first();
            }

            // 2. Validasi Record
            if (!$record || $record['check_out'] !== '-') {
                Notification::swal2('Warning', 'Check-out not available for this record.', 'warning');
                Redirect::back(1); exit;
            }

            // 3. Ambil data pendukung lainnya (GPS & Locations)
            $checkInGps = DB::table('attendance_record_location')
                ->where('id_attendance', '=', $record['id_attendance'])
                ->where('attendance_type', '=', 'IN')
                ->first();

            $locations = DB::table('attendance_location')
                ->where('id_company', '=', $internship['id_company'])
                ->where('is_deleted', '=', 0)
                ->get();

            return renderView('student.my_internship.attendance.check_out', [
                'title'                => 'Check Out Attendance',
                'routes'               => 'my_internship',
                'id_enc'               => $id,
                'att_id_enc'           => Encrypter::encrypt($record['id_attendance']),
                'record'               => $record,
                'check_in_gps'         => $checkInGps,
                'attendance_locations' => $locations,
                'has_location_rule'    => (count($locations) > 0)
            ]);

        } catch (Exception $e) { 
            Notification::swal2('Error', 'Invalid access or data not found.', 'error');
            Redirect::back(1); exit; 
        }
    break;

    case 'checkOut-store':
        // 1. Inisialisasi Data Dasar
        $user_lat    = Request::post('latitude');
        $user_lng    = Request::post('longitude');
        $accuracy    = Request::post('accuracy');
        $description = Request::post('description');

        // 2. Cari Record Absensi (Berdasarkan ID atau Hari Ini)
        try {
            if (!empty($attendance_id)) {
                // Jika ID tersedia, gunakan ID spesifik tersebut
                $dec_att_id = Encrypter::decrypt($attendance_id);
                $record = DB::table('attendance')
                    ->where('id_attendance', '=', $dec_att_id)
                    ->where('id_internship', '=', $id_internship)
                    ->first();
            } else {
                // Fallback: Cari data absensi hari ini
                $today = date('Y-m-d');
                $record = DB::table('attendance')
                    ->where('id_internship', '=', $id_internship)
                    ->where('date', '=', $today)
                    ->first();
            }
        } catch (Exception $e) {
            Notification::swal2('Error', 'Invalid attendance reference.', 'error');
            Redirect::back(1); exit;
        }

        // Verifikasi: Apakah data ditemukan? Apakah sudah check-out?
        if (!$record) {
            Notification::swal2('Error', 'Attendance record not found.', 'error');
            Redirect::back(1); exit;
        }
        if ($record['check_out'] !== '-') {
            Notification::swal2('Warning', 'You have already checked out for this record.', 'warning');
            Redirect::back(1); exit;
        }

        // 3. Logika Geofencing (Validasi Lokasi)
        $rules = DB::table('attendance_location')
            ->where('id_company', '=', $internship['id_company'])
            ->where('is_deleted', '=', 0)
            ->get();

        $is_inside = true; 
        $min_dist  = 0; 
        $nearest   = null;

        if (count($rules) > 0) {
            $is_inside = false; 
            $min_dist  = null; 

            foreach ($rules as $loc) {
                $dist = getDistance($user_lat, $user_lng, $loc['latitude'], $loc['longitude']);
                if ($min_dist === null || $dist < $min_dist) { 
                    $min_dist = $dist; 
                    $nearest = $loc; 
                }
                if ($dist <= $loc['radius_meters']) $is_inside = true;
            }

            // Validasi Jarak
            if (!$is_inside) {
                Notification::snackbar("Outside Area (".round($min_dist)."m). Please be near the office to check out.");
                Redirect::back(1); exit;
            }
        }

        // 4. Eksekusi Database dengan Transaksi
        DB::beginTransaction();
        try {
            // A. Update Waktu Checkout
            DB::table('attendance')->where('id_attendance', $record['id_attendance'])->update([
                'check_out'   => date('Y-m-d H:i:s'),
                'description' => $description
            ]);

            // B. Catat log lokasi Checkout jika ada aturan lokasi
            if (count($rules) > 0) {
                DB::table('attendance_record_location')->insert([
                    'id_attendance'        => $record['id_attendance'], 
                    'latitude'             => $user_lat ?? 0, 
                    'longitude'            => $user_lng ?? 0,
                    'accuracy'             => $accuracy ?? 0, 
                    'distance_meter'       => $min_dist ?? 0,
                    'master_location'      => $nearest['location'] ?? 'Office',
                    'master_latitude'      => $nearest['latitude'] ?? 0, 
                    'master_longitude'     => $nearest['longitude'] ?? 0,
                    'master_radius_meters' => $nearest['radius_meters'] ?? 0, 
                    'attendance_type'      => 'OUT', 
                    'is_inside'            => 1 // Pastikan 1 karena sudah lolos validasi di atas
                ]);
            }

            DB::commit();
            Notification::swal2('Success', 'Check-out recorded. Have a safe trip home!', 'success');
            // Redirect kembali ke index absensi
            Response::redirect(router('student.attendance.index', ['id' => $id])); exit;

        } catch (Exception $e) { 
            DB::rollBack(); 
            Notification::snackbar("Failed to save data: " . $e->getMessage()); 
            Redirect::back(1); exit; 
        }
    break;

    case 'store':
        // Proses Manual Add (History)
        if (DB::table('attendance')->where('id_internship', $id_internship)->where('date', Request::post('attendance_date'))->exists()) {
            Notification::snackbar("Duplicate entry for this date."); Redirect::back(); exit;
        }

        $_SERVER['PDO']->beginTransaction();
        try {
            $att_id = DB::table('attendance')->insertGetId([
                'id_internship' => $id_internship, 'nim' => $nim, 'attendance_type' => Request::post('attendance_type'),
                'description' => Request::post('description'), 'date' => Request::post('attendance_date'),
                'week' => ceil(date('j', strtotime(Request::post('attendance_date')))/7),
                'check_in' => Request::post('check_in', '-'), 'check_out' => Request::post('check_out', '-'),
                'approval_spv' => 'Waiting', 'approval_dpm' => 'Waiting', 'is_history' => 1
            ]);

            // Handle Signature Image
            $img_parts = explode(',', Request::post('validation'));
            $img_data = base64_decode($img_parts[1] ?? '');
            $path = "./upload/attendance/" . bin2hex(random_bytes(10)) . ".png";
            if (!is_dir("./upload/attendance/")) mkdir("./upload/attendance/", 0755, true);
            file_put_contents($path, $img_data);

            DB::table('attendance-signature')->insert(['id_attendance' => $att_id, 'signature' => $path]);

            $_SERVER['PDO']->commit();
            Notification::swal2('Success', 'Manual attendance recorded.', 'success');
            Response::redirect(router('student.attendance.index', ['id' => $id]));
        } catch (Exception $e) { $_SERVER['PDO']->rollBack(); Notification::snackbar("Save failed."); Redirect::back(); }
    break;


    case 'addHistory':
        // 1. Ambil batasan tanggal magang
        $startDate = $internship['start_date'];
        $endDate   = $internship['end_date'];
        
        // 2. Tentukan Tanggal Maksimal (Kemarin)
        // History hanya untuk tanggal yang sudah lewat agar hari ini wajib pakai GPS
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        
        // Jika hari ini masih dalam range magang, batasnya adalah kemarin.
        // Jika magang sudah selesai, batasnya adalah tanggal selesai magang.
        $maxDate = ($yesterday < $endDate) ? $yesterday : $endDate;

        // 3. Ambil daftar tanggal yang sudah ada datanya (agar tidak double)
        $existingRecords = DB::table('attendance')
            ->where('id_internship', '=', $id_internship)
            ->select('date')
            ->get();
        
        $disabledDates = array_column($existingRecords, 'date');

        return renderView('student.my_internship.attendance.add', [
            'title'          => 'Add Attendance History',
            'id_enc'         => $id,
            'id_internship'  => $id_internship,
            'min_date'       => $startDate,
            'max_date'       => $maxDate,
            'disabled_dates' => $disabledDates
        ]);
    break;

    case 'addHistory-store':
        $date = Request::post('attendance_date');
        $type = Request::post('attendance_type');
        $signatureData = Request::post('validation'); // Data Base64 dari Signature Pad
        
        // 1. VALIDASI SERVER-SIDE
        $exists = DB::table('attendance')
            ->where('id_internship', $id_internship)
            ->where('date', $date)
            ->exists();

        if ($exists) {
            Notification::snackbar("Error: Record already exists for this date.");
            Redirect::back(1); exit;
        }

        if ($date > date('Y-m-d')) {
            Notification::snackbar("Error: You cannot add attendance for future dates.");
            Redirect::back(1); exit;
        }

        // 2. PROSES SIMPAN DATA
        DB::beginTransaction();
        try {
            $week = ceil(date('j', strtotime($date)) / 7);

            // Simpan ke tabel attendance utama
            $att_id = DB::table('attendance')->insertGetId([
                'id_internship'   => $id_internship,
                'nim'             => $_SESSION['nim'],
                'attendance_type' => $type,
                'date'            => $date,
                'week'            => $week,
                'check_in'        => Request::post('check_in') ?: '--:--',
                'check_out'       => Request::post('check_out') ?: '--:--',
                'description'     => Request::post('description'),
                'is_history'      => 1,
                'approval_spv'    => 'Waiting',
                'approval_dpm'    => 'Waiting'
            ]);

            // 3. PENGOLAHAN TANDA TANGAN
            if (!empty($signatureData)) {
                // Hilangkan header data:image/png;base64,
                $imgArr = explode(',', $signatureData);
                $decodedImg = base64_decode($imgArr[1]);

                // Buat nama file unik (hexadecimal sesuai contoh di database)
                $fileName = Str::uuid() . bin2hex(random_bytes(4)) . '.png';
                $targetDir = './upload/attendance/';
                $fullPath = $targetDir . $fileName;

                // Pastikan folder tersedia
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }

                // Simpan file ke storage
                file_put_contents($fullPath, $decodedImg);

                // Simpan path ke tabel attendance-signature
                DB::table('`attendance-signature`')->insert([
                    'id_attendance' => $att_id,
                    'signature'     => $fullPath // Menyimpan path: ./upload/attendance/xxx.png
                ]);
            }

            DB::commit();
            Notification::swal2('Success', 'Attendance history and signature recorded.', 'success');
            Response::redirect(router('student.attendance.index', ['id' => $id])); exit;

        } catch (Exception $e) {
            DB::rollBack();
            // Notification::snackbar("Error: " . $e->getMessage());
            Redirect::back(1); exit;
        }
    break;

    case 'detail':
        $id_att = Encrypter::decrypt($attendance_id);
        $data_att = DB::table('attendance')->where('id_attendance', $id_att)->first();
        
        // Ambil log lokasi (untuk GPS)
        $logs = DB::table('attendance_record_location')->where('id_attendance', $id_att)->get();
        
        // Ambil tanda tangan jika ini adalah history manual
        $signature = null;
        if ($data_att['is_history']) {
            $signature = DB::table('`attendance-signature`')->where('id_attendance', $id_att)->first();
        }

        return renderView('student.my_internship.attendance.detail', [
            'title'     => 'Attendance Detail',
            'routes'    => 'my_internship',
            'attendance'=> $data_att,
            'logs'      => $logs,
            'signature' => $signature,
            'id_enc'    => $id
        ]);
    break;

    default:
        view('exception/error_404'); exit;
    break;
endswitch;