<?php

switch ($method):
    default:
        echo "Not Found";
    break;
    
    case 'index':
        // Generate angka acak 1-10
        $n1 = rand(1, 10);
        $n2 = rand(1, 10);
        
        // Simpan hasilnya di session untuk divalidasi nanti
        Session::set('captcha_result', $n1 + $n2);
        
        renderView('auth.login-express', [
            'title' => 'Express Login',
            'n1'    => $n1,
            'n2'    => $n2
        ]);
    break;

    case 'login_process':
        $response = Response::make();

        // 1. Handle Redirect Intended
        if (Request::has('redirect_intended')) {
            $redirect_input = base64_decode(Request::input('redirect_intended')) ?? '';
            if (Url::validatePath($redirect_input)) {
                Session::set('redirect_intended', $redirect_input);
            }
        }




        // 2. Validasi Input
        $rules = [
            'username' => 'required',
            'password' => 'required',
            'role'     => 'required'
        ];
        
        $validator = Validator::make(Request::all(), $rules);

        if ($validator->fails()) {
            Session::flash('validation_errors', $validator->errors());
            make_snackbar('Username and password are required.');
            Response::back()->send();
        }

        $username = Request::input('username');
        $password = Request::input('password'); // Tetap ambil untuk verifikasi Hash jika diperlukan
        $role     = Request::input('role');

        try {
            $userData = null;

            // 3. Cari Data Berdasarkan Role (Menggunakan PDO via Kelas DB)
            if ($role == 'UPKPK') {
                $userData = DB::table('upkpk')->where('username', '=', $username)->first();
                
                if ($userData) {
                    $_SESSION['role'] = 'UPKPK';
                    $_SESSION['CDC_Data'] = $userData;
                    $_SESSION['CDC-Data'] = $userData; // Kompatibilitas kode lama
                    $_SESSION['name'] = $userData['name'];
                    $_SESSION['username'] = $userData['username'];
                }

            } elseif ($role == 'Perusahaan') {
                // Join menggunakan Query Builder PDO
                $userData = DB::table('user_company')
                            ->join('company', 'user_company.id_company', '=', 'company.id_company')
                            ->where('user_company.username', '=', $username)
                            ->first();

                if ($userData) {
                    $_SESSION['LOGIN'] = TRUE;
                    $_SESSION['role'] = "industri";
                    $_SESSION['id_company'] = $userData['id_company'];
                    $_SESSION['company_name'] = $userData['name'];
                    $_SESSION['user_fullname'] = $userData['user_fullname'];
                }

            } elseif ($role == 'Mahasiswa') {
                // Menggunakan Model StudentInternship (PDO based)
                $userData = StudentInternship::where('nim', '=', $username)->first();

                if ($userData) {
                    $_SESSION['role'] = 'Mahasiswa';
                    $_SESSION['Student_Data'] = $userData;
                    $_SESSION['name'] = $userData['name'];
                    $_SESSION['nim'] = $userData['nim'];
                }

            } elseif ($role == 'Dosen') {
                $userData = DB::table('lecturer')->where('nim_nik_unit', '=', $username)->first();

                if ($userData) {
                    $_SESSION['role'] = 'Dosen';
                    $_SESSION['Lecturer_Data'] = $userData;
                    $_SESSION['name'] = $userData['name'];
                    $_SESSION['is_koor'] = ($userData['is_koor'] == 1);
                    $_SESSION['prodi_koor'] = $userData['prodi_koor'];
                }
            }

            // 4. Verifikasi Akhir & Set Session Global
            if ($userData) {
                // Opsional: Cek Hash Password jika Express Login tetap butuh password
                // if (!Hash::check($password, $userData['password'])) { throw new Exception("Invalid password"); }

                $_SESSION['profile_picture'] = $userData['profile_picture'] ?? 'profile.jpg';
                
                make_snackbar('Welcome back, ' . ($_SESSION['name'] ?? $_SESSION['user_fullname']));
                
                // Redirect ke intended page atau dashboard
                $redirectTo = Session::get('redirect_intended') ?? Url::base('dashboard');
                Session::forget('redirect_intended');
                
                Response::redirect($redirectTo)->send();
            } else {
                throw new Exception("Account not found for role: $role");
            }

        } catch (Exception $e) {
            error_log($e->getMessage());
            make_snackbar('Check your credentials and try again.');
            Response::back()->send();
        }
    break;

    case 'verification':

        $userAnswer = Request::input('captcha_answer');
        $realAnswer = Session::pull('captcha_result'); // Ambil lalu hapus agar tidak bisa dipake ulang

        // Cek Captcha dulu
        if ($userAnswer === null || (int)$userAnswer !== (int)$realAnswer) {
            Notification::swal2('Verification Failed', 'Captcha answer is incorrect.', 'error');
            Response::back()->send();
            exit;
        }


        // 1. Ambil data input menggunakan Request helper
        $username_input = Request::input('username');
        $password_input = Request::input('password');
        $admin_config   = $_SERVER['ADMIN_MAINTENANCE'] ?? [];

        // 2. Validasi Keberadaan Username di Config
        if (!array_key_exists($username_input, $admin_config)) {
            Notification::swal2('Not Authorized', 'Invalid administrative credentials.', 'error');
            Response::back()->send();
            exit;
        }

        // 3. Verifikasi Password (Bcrypt)
        $hashed_password = $admin_config[$username_input];
        if (!password_verify($password_input, $hashed_password)) {
            Notification::swal2('Not Authorized', 'Invalid administrative credentials.', 'error');
            Response::back()->send();
            exit;
        }

        // 4. Set Session khusus Maintenance menggunakan Session Class
        // Menggunakan Session::set() menggantikan $_SESSION langsung
        Session::set('FLOGIN', true);
        Session::set('FLOGIN-TIME', date('Y-m-d H:i:s'));

        // 5. Berikan Feedback & Redirect
        Notification::swal2('Authorized', 'Welcome to ' . ($_SERVER['APP_NAME'] ?? 'System') . ' Admin!', 'success');
        
        // Redirect ke halaman express_login
        Response::redirect( router('login.express') )->send();
        exit;

    break;


endswitch;