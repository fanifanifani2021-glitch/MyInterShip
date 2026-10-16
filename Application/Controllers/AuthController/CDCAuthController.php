<?php
namespace Application\Controllers\AuthController;

use Controller;
use Request;
use Response;
use Session;
use Validator;
use Upkpk;
use Env;
use Url;
use Notification;
use Exception;

class CDCAuthController extends Controller
{
    public function index()
    {
        renderView('auth.login-cdc', ['title' => 'CDC Staff Login']);
    }

    public function login_process()
    {
        $redirectUrl = '';
        if (Request::has('redirect_intended') && Request::input('redirect_intended') != '') {
            $redirectInput = base64_decode(Request::input('redirect_intended')) ?? '';
            if (Url::validatePath($redirectInput)) {
                $redirectUrl = $redirectInput;
            } else {
                $redirectUrl = Url::base('dashboard'); 
            }
            Session::set('redirect_intended', $redirectUrl);
        }

        if (!Session::has('failed_login')) {
            $this->resetFailedLogin();
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
            make_snackbar('Please check your credentials and try again.');
            Response::back()->send();
            exit;
        }

        if (Request::has('remember_me')) {
            setcookie('username', Request::input('username'), time() + (86400 * 30), "/");
        } else {
            setcookie('username', '', time() - 3600, "/");
        }

        $failedThreshold = Env::get('FAILED_ATTEMPT', 5);
        $failedData      = Session::get('failed_login');

        if (($failedData['attempts'] ?? 0) >= $failedThreshold) {
            $cooldown = 120 * pow(2, $failedData['cooldown_multiplier'] ?? 1);
            $lastTime = $failedData['last_login_time'] ?? time();

            if ((time() - $lastTime) < $cooldown) {
                $remaining = $cooldown - (time() - $lastTime);
                make_snackbar("Too many failed attempts. Please wait $remaining seconds.");
                Response::back()->send();
                exit;
            } else {
                $this->resetFailedLogin();
            }
        }

        try {
            $username = Request::input('username');
            $password = Request::input('password');

            $cdc = Upkpk::where('username', '=', $username)->first();

            if (!$cdc || !password_verify($password, $cdc['password'])) {
                $this->recordFailedLogin();
                Notification::swal2('Login Failed', 'Invalid username or password.', 'error');
                Response::back()->send();
                exit;
            }

            if (($cdc['status'] ?? '') !== 'Active') {
                Notification::swal2('Account Inactive', 'Your account has been deactivated.', 'warning');
                Response::back()->send();
                exit;
            }

            $this->resetFailedLogin();

            // Set Session Data CDC / UPKPK
            $_SESSION['role']            = 'UPKPK';
            $_SESSION['CDC_Data']        = $cdc;
            $_SESSION['CDC-Data']        = $cdc;
            $_SESSION['name']            = $cdc['name'];
            $_SESSION['username']        = $cdc['username'];
            $_SESSION['profile_picture'] = $cdc['profile_picture'] ?? 'profile.jpg';

            Notification::swal2('Welcome', 'Welcome back, CDC User ' . $cdc['name'], 'success');

            $destination = !empty($redirectUrl) ? $redirectUrl : Url::base('dashboard');
            Session::forget('redirect_intended');

            Response::redirect($destination)->send();
            exit;

        } catch (Exception $e) {
            $this->recordFailedLogin();
            error_log("CDC Login Error: " . $e->getMessage());
            make_snackbar("An unexpected system error occurred.");
            Response::back()->send();
            exit;
        }
    }

    private function resetFailedLogin() {
        Session::set('failed_login', ['attempts' => 0, 'last_login_time' => null, 'cooldown_multiplier' => 1]);
    }

    private function recordFailedLogin() {
        $data = Session::get('failed_login', ['attempts' => 0, 'last_login_time' => null, 'cooldown_multiplier' => 1]);
        $data['attempts'] += 1;
        $data['last_login_time'] = time();
        if ($data['attempts'] % 5 == 0) $data['cooldown_multiplier'] += 1;
        Session::set('failed_login', $data);
    }
}