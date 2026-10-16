<?php
namespace Application\Controllers\AuthController;

use Controller;
use Request;
use Response;
use Session;
use Validator;
use DB;
use StudentInternship;
use Url;
use Notification;
use Exception;

class ExpressAuthController extends Controller
{
    public function index()
    {
        $n1 = rand(1, 10);
        $n2 = rand(1, 10);
        
        Session::set('captcha_result', $n1 + $n2);
        
        renderView('auth.login-express', [
            'title' => 'Express Login',
            'n1'    => $n1,
            'n2'    => $n2
        ]);
    }

    public function verification()
    {
        $userAnswer = Request::input('captcha_answer');
        $realAnswer = Session::pull('captcha_result');

        if ($userAnswer === null || (int)$userAnswer !== (int)$realAnswer) {
            Notification::swal2('Verification Failed', 'Captcha answer is incorrect.', 'error');
            Response::back()->send();
            exit;
        }

        $usernameInput = Request::input('username');
        $passwordInput = Request::input('password');
        $adminConfig   = $_SERVER['ADMIN_MAINTENANCE'] ?? [];

        if (!array_key_exists($usernameInput, $adminConfig)) {
            Notification::swal2('Not Authorized', 'Invalid administrative credentials.', 'error');
            Response::back()->send();
            exit;
        }

        $hashedPassword = $adminConfig[$usernameInput];
        if (!password_verify($passwordInput, $hashedPassword)) {
            Notification::swal2('Not Authorized', 'Invalid administrative credentials.', 'error');
            Response::back()->send();
            exit;
        }

        Session::set('FLOGIN', true);
        Session::set('FLOGIN-TIME', date('Y-m-d H:i:s'));

        Notification::swal2('Authorized', 'Welcome to ' . ($_SERVER['APP_NAME'] ?? 'System') . ' Admin!', 'success');
        Response::redirect(router('login.express'))->send();
        exit;
    }

    public function login_process()
    {
        if (Request::has('redirect_intended')) {
            $redirectInput = base64_decode(Request::input('redirect_intended')) ?? '';
            if (Url::validatePath($redirectInput)) {
                Session::set('redirect_intended', $redirectInput);
            }
        }

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
            exit;
        }

        $username = Request::input('username');
        $role     = Request::input('role');

        try {
            $userData = null;

            if ($role == 'UPKPK') {
                $userData = DB::table('upkpk')->where('username', '=', $username)->first();
                if ($userData) {
                    $_SESSION['role']       = 'UPKPK';
                    $_SESSION['CDC_Data']   = $userData;
                    $_SESSION['CDC-Data']   = $userData;
                    $_SESSION['name']       = $userData['name'];
                    $_SESSION['username']   = $userData['username'];
                }

            } elseif ($role == 'Perusahaan') {
                $userData = DB::table('user_company')
                            ->join('company', 'user_company.id_company', '=', 'company.id_company')
                            ->where('user_company.username', '=', $username)
                            ->first();

                if ($userData) {
                    $_SESSION['LOGIN']         = true;
                    $_SESSION['role']          = "industri";
                    $_SESSION['id_company']    = $userData['id_company'];
                    $_SESSION['company_name']  = $userData['name'];
                    $_SESSION['user_fullname'] = $userData['user_fullname'];
                }

            } elseif ($role == 'Mahasiswa') {
                $userData = StudentInternship::where('nim', '=', $username)->first();

                if ($userData) {
                    $_SESSION['role']         = 'Mahasiswa';
                    $_SESSION['Student_Data'] = $userData;
                    $_SESSION['name']         = $userData['name'];
                    $_SESSION['nim']          = $userData['nim'];
                }

            } elseif ($role == 'Dosen') {
                $userData = DB::table('lecturer')->where('nim_nik_unit', '=', $username)->first();

                if ($userData) {
                    $_SESSION['role']          = 'Dosen';
                    $_SESSION['Lecturer_Data'] = $userData;
                    $_SESSION['name']          = $userData['name'];
                    $_SESSION['is_koor']       = ($userData['is_koor'] == 1);
                    $_SESSION['prodi_koor']    = $userData['prodi_koor'];
                }
            }

            if ($userData) {
                $_SESSION['profile_picture'] = $userData['profile_picture'] ?? 'profile.jpg';
                make_snackbar('Welcome back, ' . ($_SESSION['name'] ?? $_SESSION['user_fullname'] ?? 'User'));
                
                $redirectTo = Session::get('redirect_intended') ?? Url::base('dashboard');
                Session::forget('redirect_intended');
                
                Response::redirect($redirectTo)->send();
                exit;
            } else {
                throw new Exception("Account not found for role: $role");
            }

        } catch (Exception $e) {
            error_log("Express Login Error: " . $e->getMessage());
            make_snackbar('Check your credentials and try again.');
            Response::back()->send();
            exit;
        }
    }
}