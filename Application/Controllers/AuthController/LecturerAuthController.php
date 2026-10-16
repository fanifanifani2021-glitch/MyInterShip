<?php
namespace Application\Controllers\AuthController;

use Controller;
use Request;
use Response;
use Session;
use Validator;
use Lecturer;
use Env;
use Url;
use Notification;
use Exception;

class LecturerAuthController extends Controller
{
    public function index()
    {
        renderView('auth.login-lecturer', ['title' => 'Lecturer Login']);
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
            'nik'      => 'required',
            'password' => 'required',
        ];

        $messages = [
            'nik.required'      => 'Email / NIK is required.',
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
            setcookie('nik', Request::input('nik'), time() + (86400 * 30), "/");
        } else {
            setcookie('nik', '', time() - 3600, "/");
        }

        // Rate Limiting Check
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
            $nik      = Request::input('nik');
            $password = Request::input('password');

            // A. Check Local DB First (Bcrypt Local Password)
            $lecturer = Lecturer::where('nim_nik_unit', '=', $nik)->first();

            if ($lecturer && !empty($lecturer['password']) && password_verify($password, $lecturer['password'])) {
                $this->resetFailedLogin();
                $this->loginSuccess($lecturer, $redirectUrl);
            } 
            // B. Fallback: Centralized TalentHub API Authentication
            else {
                $authResult = $this->talenthubLogin($nik, $password);

                if (!$authResult['success']) {
                    $this->recordFailedLogin();
                    Session::flash('old', Request::all());
                    $this->handleError(102, $authResult['message'] ?? 'Invalid username or password.');
                }

                $userData = $authResult['data'];
                $userRole = $userData['role'] ?? '';

                // 🎯 Block if role belongs to Student
                $disallowedRoles = ['Mahasiswa', 'mahasiswa', 'Student', 'student'];

                if (in_array($userRole, $disallowedRoles)) {
                    $this->recordFailedLogin();
                    $this->handleError(103, 'Access denied. Students are not allowed to log in via Lecturer portal.');
                }

                // C. Sync API Data with Local DB
                $apiNik = $userData['nik'] ?? $userData['nip'] ?? $userData['username'] ?? $nik;

                $lecturer = Lecturer::where('nim_nik_unit', '=', $nik)
                                    ->orWhere('nim_nik_unit', '=', $apiNik)
                                    ->first();

                if (!$lecturer) {
                    $this->recordFailedLogin();
                    $this->handleError(106, 'Account valid on TalentHub, but not registered in Internship System. Please contact Administrator.');
                }

                $this->resetFailedLogin();
                $this->loginSuccess($lecturer, $redirectUrl, $userData);
            }

        } catch (Exception $e) {
            $this->recordFailedLogin();
            error_log("Lecturer Login Error: " . $e->getMessage());
            $this->handleError(105, 'An unexpected system error occurred.');
        }
    }

    // =========================================================================
    // PRIVATE HELPER METHODS
    // =========================================================================

    private function loginSuccess($lecturer, $redirectUrl, $biodata = null)
    {
        $data['role']               = 'Dosen';
        $data['POLIBATAM_LECTURER'] = ($lecturer['id_kampus'] == 1);
        $data['name']               = $lecturer['name'];
        $data['nim_nik_unit']       = $lecturer['nim_nik_unit'];
        $data['email_polibatam']    = $lecturer['email_polibatam'] ?? ($biodata['email'] ?? '');
        $data['Lecturer_Data']      = $lecturer;
        $data['is_koor']            = ($lecturer['is_koor'] == 1);
        $data['prodi_koor']         = $lecturer['prodi_koor'];
        $data['is_pembimbing']      = true;
        $data['status']             = $lecturer['status'];
        $data['program_study']      = $lecturer['program_study'];
        $data['profile_picture']    = $lecturer['profile_picture'] ?? 'profile.jpg';

        unset($lecturer['password']);
        $_SESSION = array_merge($_SESSION, $data);

        make_snackbar('Welcome back, ' . $lecturer['name']);

        Session::forget('redirect_intended');

        $destination = !empty($redirectUrl) ? $redirectUrl : Url::base('dashboard');
        Response::redirect($destination)->send();
        exit;
    }

    private function resetFailedLogin()
    {
        Session::set('failed_login', ['attempts' => 0, 'last_login_time' => null, 'cooldown_multiplier' => 1]);
    }

    private function recordFailedLogin()
    {
        $data = Session::get('failed_login', ['attempts' => 0, 'last_login_time' => null, 'cooldown_multiplier' => 1]);
        $data['attempts'] += 1;
        $data['last_login_time'] = time();
        if ($data['attempts'] % 5 == 0) {
            $data['cooldown_multiplier'] += 1;
        }
        Session::set('failed_login', $data);
    }

    private function handleError($code, $msg)
    {
        make_snackbar("($code) $msg");
        Response::back()->send();
        exit;
    }

    /**
     * Centralized TalentHub API Authentication for Lecturers
     */
    private function talenthubLogin($username, $password)
    {
        $curl     = curl_init();
        $postData = http_build_query(['username' => $username, 'password' => $password]);
        $apiKey   = 'JH7maZQTP7UfWxGNE23YIPEL3lanSO7l';

        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://talenthub.polibatam.ac.id/api/myinternship-login/lecturer',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $postData,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_HTTPHEADER     => [
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded',
                'X-API-KEY: ' . $apiKey
            ],
            CURLOPT_USERAGENT      => 'MyInternship System Client',
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($response === false || $httpCode != 200) {
            return [
                'success' => false, 
                'message' => 'Unable to connect to TalentHub authentication service (HTTP ' . $httpCode . ').'
            ];
        }

        $result = json_decode($response, true);
        if (!$result || !isset($result['success']) || $result['success'] !== true) {
            return [
                'success' => false, 
                'message' => $result['message'] ?? 'Invalid NIK or password.'
            ];
        }

        return ['success' => true, 'data' => $result['data']];
    }
}