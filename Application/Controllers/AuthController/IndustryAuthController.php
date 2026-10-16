<?php
namespace Application\Controllers\AuthController;

use Controller;
use Request;
use Response;
use Session;
use Validator;
use DB;
use Env;
use Url;
use Exception;
use PDO;

class IndustryAuthController extends Controller
{
    public function index()
    {
        renderView('auth.login-industry', ['title' => 'Industry Login']);
    }

    public function login_process()
    {
        $redirect = base64_decode(Request::input('redirect', ''));
        Session::set('REDIRECT', $redirect);

        $failedAttempts = Session::get('failed_login.attempts', 0);
        $threshold      = Env::get('FAILED_ATTEMPT', 5);

        if ($failedAttempts >= $threshold) {
            $cooldown = 300; // 5 menit
            $lastTime = Session::get('failed_login.last_login_time', time());

            if ((time() - $lastTime) < $cooldown) {
                make_snackbar("Too many failed login attempts. Please wait a few minutes.");
                Response::back()->send();
                exit;
            }
            Session::set('failed_login.attempts', 0);
        }

        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        $messages = [
            'username.required' => 'Username is required.',
            'password.required' => 'Password is required.',
        ];

        $validator = Validator::make(Request::all(), $rules, [], $messages);

        if ($validator->fails()) {
            Session::flash('validation_errors', $validator->errors());
            Session::flash('old', Request::all());
            make_snackbar('Please check your submitted data.');
            Response::back()->send();
            exit;
        }

        if (Request::has('remember_me')) {
            setcookie('username', Request::input('username'), time() + 7200, "/");
        } else {
            setcookie('username', '', time() - 3600, "/");
        }

        try {
            $db = DB::connection();
            $sql = "
                SELECT user_company.*, company.name AS company_name, company.logo AS company_logo
                FROM user_company
                LEFT JOIN company ON user_company.id_company = company.id_company
                WHERE user_company.username = :username
                LIMIT 1
            ";

            $stmt = $db->prepare($sql);
            $stmt->execute(['username' => Request::input('username')]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $this->recordFailedLogin();
                make_snackbar("(01) Account not found.");
                Response::back()->send();
                exit;
            }

            if (!password_verify(Request::input('password'), $user['password'])) {
                $this->recordFailedLogin();
                make_snackbar("(02) Password is incorrect.");
                Response::back()->send();
                exit;
            }

            unset($user['password']);

            // Set Session Data Industry
            $_SESSION['LOGIN']           = true;
            $_SESSION['role']            = "industri";
            $_SESSION['username']        = $user['username'];
            $_SESSION['id_user_company'] = $user['id_user_company'];
            $_SESSION['id_company']      = $user['id_company'];
            $_SESSION['user_fullname']   = $user['user_fullname'];
            $_SESSION['user_email']      = $user['user_email'];
            $_SESSION['user_type']       = $user['user_type'];
            $_SESSION['company_name']    = $user['company_name'];
            $_SESSION['profile_picture'] = $user['profile_picture'] ?? 'profile.jpg';
            $_SESSION['failed_login']    = [];

            make_snackbar('Welcome to MyInternship, ' . $user['user_fullname']);

            $destination = !empty($redirect) ? $redirect : Url::base('dashboard');
            Response::redirect($destination)->send();
            exit;

        } catch (Exception $e) {
            error_log("Industry Login Error: " . $e->getMessage());
            make_snackbar("(03) Internal Server Error.");
            Response::back()->send();
            exit;
        }
    }

    private function recordFailedLogin()
    {
        $attempts = Session::get('failed_login.attempts', 0) + 1;
        Session::set('failed_login.attempts', $attempts);
        Session::set('failed_login.last_login_time', time());
    }
}