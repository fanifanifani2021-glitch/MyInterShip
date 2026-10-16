<?php
namespace Application\Controllers\AuthController;

use Controller;
use Request;
use Response;
use Session;
use Validator;
use Hash;
use Encrypter;
use Env;
use Notification;
use StudentInternship;
use Url;
use Exception;

class StudentAuthController extends Controller
{
    public function index()
    {
        renderView('auth.login-mahasiswa', ['title' => 'Student Login']);
    }

    public function login_process()
    {
        if (Request::has('redirect_intended')) {
            $redirectInput = base64_decode(Request::input('redirect_intended')) ?? '';
            if (Url::validatePath($redirectInput)) {
                Session::set('redirect_intended', $redirectInput);
            } else {
                Session::set('redirect_intended', Url::base('dashboard')); 
            }
        }

        if (!Session::has('failed_login')) {
            $this->resetFailedLogin();
        }

        $rules = [
            'nim'      => 'required',
            'password' => 'required',
        ];

        $messages = [
            'nim.required'      => 'NIM / Username is required.',
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
            $encryptedNim = Encrypter::encrypt(Request::string('nim'));
            Response::make()->withCookie('nim', $encryptedNim, 43200);
        } else {
            Response::make()->withoutCookie('nim');
        }

        // Rate Limiting Check
        $failedData = Session::get('failed_login');
        $threshold  = Env::get('FAILED_ATTEMPT', 5);
        
        if (($failedData['attempts'] ?? 0) >= $threshold) {
            $cooldown = 120 * pow(2, $failedData['cooldown_multiplier'] ?? 1);
            $lastTime = $failedData['last_login_time'] ?? time();

            if ((time() - $lastTime) < $cooldown) {
                $remaining = $cooldown - (time() - $lastTime);
                error_log('BLOCKED: NIM ' . Request::input('nim') . ' at ' . date('H:i:s'));

                Notification::swal2('Login Failed', 'Too many failed attempts. Please wait ' . $remaining . ' seconds.', 'error');
                Response::back()->send();
                exit;
            } else {
                $this->resetFailedLogin();
            }
        }

        try {
            $inputNim      = Request::input('nim');
            $inputPassword = Request::input('password');

            // A. Check Local DB
            $student = StudentInternship::where('nim', '=', $inputNim)->first();

            // B. Verify Local Password (Bcrypt)
            if ($student && !empty($student['password']) && Hash::check($inputPassword, $student['password'])) {
                $this->resetFailedLogin();
                Notification::swal2('Login Successful', 'Welcome back, ' . $student['name'] . '!', 'success');
                $student->establishSession(); 
                exit;
            } 
            // C. Fallback: Centralized TalentHub API Authentication
            else {
                $authResult = $this->talenthubLogin($inputNim, $inputPassword);

                if (!$authResult['success']) {
                    $this->recordFailedLogin();
                    Session::flash('old', Request::all());
                    $this->handleError(102, $authResult['message'] ?? 'Invalid credentials.');
                }

                $userData = $authResult['data'];

                if (($userData['role'] ?? '') !== 'Mahasiswa') {
                    $this->recordFailedLogin();
                    $this->handleError(103, 'Access denied. Only students are allowed.');
                }

                $student = StudentInternship::where('nim', '=', $inputNim)
                            ->orWhere('nim', '=', ($userData['nik'] ?? ''))
                            ->first();

                if (!$student) {
                    $this->recordFailedLogin();
                    $this->handleError(106, 'Account valid on TalentHub, but not registered in Internship System.');
                }

                $this->resetFailedLogin();
                Notification::swal2('Login Successful', 'Welcome back, ' . $student['name'] . '!', 'success');
                $student->establishSession(); 
                exit;
            }

        } catch (Exception $e) {
            $this->recordFailedLogin();
            error_log("Student Login Error: " . $e->getMessage());
            $this->handleError(105, 'An unexpected system error occurred.');
        }
    }

    // --- Private Helper Methods ---

    private function resetFailedLogin() {
        Session::set('failed_login', [
            'attempts'            => 0,
            'last_login_time'     => null,
            'cooldown_multiplier' => 1
        ]);
    }

    private function recordFailedLogin() {
        $data = Session::get('failed_login', [
            'attempts'            => 0,
            'last_login_time'     => null,
            'cooldown_multiplier' => 1
        ]);
        $data['attempts'] += 1;
        $data['last_login_time'] = time();
        
        if ($data['attempts'] % 5 == 0) {
            $data['cooldown_multiplier'] += 1;
        }
        
        Session::set('failed_login', $data);
    }

    private function handleError($code, $msg) {
        Notification::snackbar("($code) $msg");
        Response::back()->send();
        exit;
    }

    private function talenthubLogin($username, $password) {
        $curl = curl_init();
        $postData = http_build_query(['username' => $username, 'password' => $password]);
        $apiKey   = 'JH7maZQTP7UfWxGNE23YIPEL3lanSO7l';

        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://talenthub.polibatam.ac.id/api/myinternship-login/student',
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

        $response  = curl_exec($curl);
        $httpCode  = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($response === false || $httpCode != 200) {
            return ['success' => false, 'message' => 'Failed to connect to TalentHub authentication service.'];
        }

        $result = json_decode($response, true);
        if (!$result || !isset($result['success']) || $result['success'] !== true) {
            return ['success' => false, 'message' => $result['message'] ?? 'Invalid NIM or password.'];
        }

        return ['success' => true, 'data' => $result['data']];
    }
}