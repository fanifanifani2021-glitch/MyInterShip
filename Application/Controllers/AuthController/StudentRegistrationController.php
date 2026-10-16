<?php
namespace Application\Controllers\AuthController;

use Controller;
use Request;
use Response;
use Session;
use Validator;
use Hash;
use Notification;
use DB;
use Exception;

class StudentRegistrationController extends Controller
{
    /**
     * 1. Halaman Form Registrasi Mahasiswa
     */
    public function index()
    {
        try {
            $db = DB::connection();

            // Fetch Data Kampus
            $stmtKampus = $db->prepare("SELECT * FROM tb_kampus WHERE id_kampus = 1");
            $stmtKampus->execute();
            $university = $stmtKampus->fetchAll(\PDO::FETCH_ASSOC);

            // Fetch Data Program Studi
            $stmtProdi = $db->prepare("SELECT * FROM program_study WHERE id_kampus = 1 ORDER BY jurusan ASC");
            $stmtProdi->execute();
            $studyProgram = $stmtProdi->fetchAll(\PDO::FETCH_ASSOC);

            renderView('auth.registration-student', [
                'title'         => 'Student Registration',
                'university'    => $university,
                'study_program' => $studyProgram
            ]);

        } catch (Exception $e) {
            error_log("Student Registration Form Error: " . $e->getMessage());
            Notification::swal2('System Error', 'Unable to load registration page. Please try again later.', 'error');
            Response::back()->send();
            exit;
        }
    }

    /**
     * 2. Proses Submit Registrasi Mahasiswa
     */
    public function submit()
    {
        // === VALIDASI FORM ===
        $rules = [
            'name'             => 'required|min:3',
            'id_kampus'        => 'required',
            'kode_prodi'       => 'required',
            'email'            => 'required|email',
            'nim'              => 'required',
            'no_whatsapp'      => 'required',
            'password'         => 'required|min:8',
            'confirm_password' => 'required'
        ];

        $messages = [
            'name.required'             => 'Full Name is required.',
            'name.min'                  => 'Full Name must be at least 3 characters.',
            'id_kampus.required'        => 'University selection is required.',
            'kode_prodi.required'       => 'Study Program is required.',
            'email.required'            => 'Email address is required.',
            'email.email'               => 'Please provide a valid email address.',
            'nim.required'              => 'NIM (Student ID) is required.',
            'no_whatsapp.required'      => 'WhatsApp Number is required.',
            'password.required'         => 'Password is required.',
            'password.min'              => 'Password must be at least 8 characters long.',
            'confirm_password.required' => 'Password confirmation is required.'
        ];

        $validator = Validator::make(Request::all(), $rules, [], $messages);

        if ($validator->fails()) {
            Session::flash('validation_errors', $validator->errors());
            Session::flash('old', Request::all());

            Notification::swal2('Validation Error', 'Please check your inputs and try again.', 'error');
            Response::back()->send();
            exit;
        }

        $nim          = trim(Request::input('nim'));
        $email        = trim(Request::input('email'));
        $password     = Request::input('password');
        $confirmPass  = Request::input('confirm_password');
        $noWhatsapp   = trim(Request::input('no_whatsapp'));

        // === VALIDASI KONFIRMASI PASSWORD ===
        if ($password !== $confirmPass) {
            Session::flash('old', Request::all());
            Notification::swal2('Password Mismatch', 'Confirm Password does not match.', 'warning');
            Response::back()->send();
            exit;
        }

        // === VALIDASI REGEX KOMPLEKSITAS PASSWORD ===
        $passwordRegex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/';
        if (!preg_match($passwordRegex, $password)) {
            Session::flash('old', Request::all());
            Notification::swal2(
                'Weak Password', 
                'Password must be at least 8 characters and include uppercase, lowercase, number, and special character.', 
                'warning'
            );
            Response::back()->send();
            exit;
        }

        try {
            $db = DB::connection();

            // === CEK DUPLIKASI NIM & EMAIL ===
            $stmtCheck = $db->prepare("
                SELECT nim, email 
                FROM student_internship 
                WHERE nim = :nim OR email = :email OR no_whatsapp = :wa 
                LIMIT 1
            ");
            $stmtCheck->execute([
                'nim'   => $nim,
                'email' => $email,
                'wa'    => $noWhatsapp
            ]);
            $existing = $stmtCheck->fetch(\PDO::FETCH_ASSOC);

            if ($existing) {
                Session::flash('old', Request::all());
                if ($existing['nim'] === $nim) {
                    Notification::swal2('Registration Failed', 'NIM is already registered.', 'error');
                } elseif ($existing['email'] === $email) {
                    Notification::swal2('Registration Failed', 'Email address is already in use.', 'error');
                } else {
                    Notification::swal2('Registration Failed', 'WhatsApp number is already registered.', 'error');
                }
                Response::back()->send();
                exit;
            }

            // === INSERT KE DATABASE ===
            $hashedPassword = Hash::make($password);

            $sqlInsert = "
                INSERT INTO student_internship (
                    nim, name, program_study, email, other_email, 
                    phone, no_whatsapp, username, password, 
                    profile_picture, nik_dospem, id_kampus, 
                    account_status, local_account, created_at
                ) VALUES (
                    :nim, :name, :program_study, :email, :other_email, 
                    :phone, :no_whatsapp, :username, :password, 
                    :profile_picture, :nik_dospem, :id_kampus, 
                    :account_status, :local_account, NOW()
                )
            ";

            $stmtInsert = $db->prepare($sqlInsert);
            $stmtInsert->execute([
                'nim'            => $nim,
                'name'           => Request::input('name'),
                'program_study'  => Request::input('kode_prodi'),
                'email'          => $email,
                'other_email'    => '-',
                'phone'          => $noWhatsapp,
                'no_whatsapp'    => $noWhatsapp,
                'username'       => $nim,
                'password'       => $hashedPassword,
                'profile_picture'=> 'default.jpg',
                'nik_dospem'     => null,
                'id_kampus'      => Request::input('id_kampus', 1),
                'account_status' => 1,
                'local_account'  => 1
            ]);

            Notification::swal2('Success!', 'Account Registered Successfully! Welcome to MyInternship, ' . Request::input('name'), 'success');
            Response::redirect(router('login.student'))->send();
            exit;

        } catch (Exception $e) {
            error_log("Student Registration Submit Error: " . $e->getMessage());
            Session::flash('old', Request::all());
            Notification::swal2('Registration Failed', 'An internal server error occurred while creating your account.', 'error');
            Response::back()->send();
            exit;
        }
    }
}