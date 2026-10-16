<?php
namespace Application\Controllers\StudentController;

use Company;
use Controller;
use DateTime;
use Exception;
use File;
use Internship;
use Notification;
use Request;
use Response;
use Session;
use StudentInternship;
use StudentProfile;
use Validator;

class RegisterCooperationController extends Controller
{
    private $nim;

    public function __construct()
    {
        $this->nim = Session::get('nim') ?? ($_SESSION['nim'] ?? null);

        if (!$this->nim) {
            Notification::swal2('Session Expired', 'Your session has expired. Please log in again.', 'warning');
            Response::redirect(router('login.student'))->send();
            exit;
        }
    }

    /**
     * Helper Private: Cek Kelengkapan Profil & Dokumen Mahasiswa
     */
    private function checkProfileCompleteness()
    {
        $student = StudentInternship::where('nim', $this->nim)->first();
        $profile = StudentProfile::where('nim', $this->nim)->first();

        // Syarat Wajib: Profil Ada, Link Talenthub CV Terisi, dan Transkrip Nilai Terisi
        if (!$student || !$profile || empty($profile->talenthub_cv) || empty($profile->academic_transcript)) {
            return [
                'complete' => false,
                'student'  => $student,
                'profile'  => $profile
            ];
        }

        return [
            'complete' => true,
            'student'  => $student,
            'profile'  => $profile
        ];
    }
    

    /**
     * Halaman Form Pendaftaran Magang Kerjasama
     */
    public function create()
    {
        $check = $this->checkProfileCompleteness();

        // 🛡️ BLOKIR JIKA PROFIL / DOKUMEN BELUM LENGKAP
        if (!$check['complete']) {
            Notification::swal2(
                'Profile Incomplete!',
                'You must complete your Student Identity and upload required documents (Talenthub CV & Academic Transcript) before registering for an internship.',
                'warning'
            );
            Response::redirect(router('student.identity.index'))->send();
            exit;
        }

        $ongoingInternship = Internship::where('nim', $this->nim)
                                        ->whereIn('status', ['Yes', 'yes', 'YES'])
                                        ->first();

        $waitingInternship = Internship::where('nim', $this->nim)
                                        ->whereIn('status', ['Waiting', 'waiting', 'WAITING'])
                                        ->first();

        // Ambil Daftar Perusahaan Kerjasama (Verified)
        $companies = Company::orderBy('name', 'ASC')->get();

        renderView('student.register_internship.cooperation', [
            'title'              => 'Register Internship - Cooperation',
            'routes'             => 'register_cooperation',
            'student_data'       => $check['student'],
            'student_profile'    => $check['profile'],
            'companies'          => $companies,
            'ongoing_internship' => $ongoingInternship,
            'waiting_internship' => $waitingInternship
        ]);
    }

    /**
     * Proses Submit Pendaftaran Magang Kerjasama
     */
    public function store()
    {
        $check = $this->checkProfileCompleteness();

        if (!$check['complete']) {
            Notification::swal2('Profile Incomplete', 'Please complete your student identity and documents first.', 'error');
            Response::redirect(router('student.identity.index'))->send();
            exit;
        }

        // 🛡️ BLOKIR SUBMIT BILA ADA ONGOING/WAITING INTERNSHIP
        $ongoing = Internship::where('nim', $this->nim)->whereIn('status', ['Yes', 'yes', 'YES'])->first();
        if ($ongoing) {
            Notification::swal2('Action Blocked', 'You currently have an active ongoing internship.', 'warning');
            Response::redirect(router('student.internship'))->send();
            exit;
        }

        $hasWaiting = Internship::where('nim', $this->nim)->whereIn('status', ['Waiting', 'waiting', 'WAITING'])->first();
        if ($hasWaiting) {
            Notification::swal2('Application Pending', 'You already have an active pending internship application.', 'warning');
            Response::redirect(router('student.internship'))->send();
            exit;
        }

        $rules = [
            'id_company'     => 'required',
            'expected_start' => 'required|date',
            'expected_end'   => 'required|date'
        ];

        $messages = [
            'id_company.required'     => 'Please select a company.',
            'expected_start.required' => 'Expected start date is required.',
            'expected_end.required'   => 'Expected end date is required.'
        ];

        $validator = Validator::make(Request::all(), $rules, [], $messages);

        if ($validator->fails()) {
            Session::flash('validation_errors', $validator->errors());
            Session::flash('old', Request::all());
            
            Notification::swal2('Validation Error', 'Please check your input values.', 'error');
            Response::back()->send();
            exit;
        }

        try {
            $idCompany     = Request::input('id_company');
            $expectedStart = Request::input('expected_start');
            $expectedEnd   = Request::input('expected_end');

            $student = $check['student'];
            $profile = $check['profile'];

            // 🧮 HITUNG SELISIH BULAN DARI start_date KE end_date
            $totalMonths = 0;
            if (!empty($expectedStart) && !empty($expectedEnd)) {
                $startObj = new DateTime($expectedStart);
                $endObj   = new DateTime($expectedEnd);
                $diff     = $startObj->diff($endObj);

                // Rumus: (Jumlah Tahun * 12) + Jumlah Bulan
                $totalMonths = ($diff->y * 12) + $diff->m;

                // Bulatkan ke atas jika ada sisa hari (> 0 hari)
                if ($diff->d > 0) {
                    $totalMonths++;
                }

                // Minimal durasi 1 bulan jika selisih tanggal sangat dekat
                if ($totalMonths <= 0) {
                    $totalMonths = 1;
                }
            }

            // 🎯 SIMPAN MENGGUNAKAN MODEL INTERNSHIP::CREATE()
            $newInternship = Internship::create([
                'nim'                  => $this->nim,
                'id_company'           => $idCompany,
                'start_date'           => $expectedStart,
                'end_date'             => $expectedEnd,
                'status'               => 'waiting',
                'file_1'               => $profile->talenthub_cv ?? '',           
                'file_2'               => $profile->academic_transcript ?? '',    
                'file_3'               => '',
                'id_user_company'      => '',
                'nik'                  => $student ? ($student->getDospemNik() ?? '') : '', 
                'internship_position'  => '',
                'internship_period'    => $totalMonths, // 👈 Terisi angka total bulan (misal: 3, 6, 12)
                'date_finalreport'     => '',
                'final_report'         => ''
            ]);

            if (!$newInternship) {
                throw new Exception("Failed to create internship application record.");
            }

            Notification::swal2('Success!', 'Your internship registration has been submitted successfully.', 'success');
            Response::redirect(router('student.internship'))->send();
            exit;

        } catch (Exception $e) {
            error_log("Register Cooperation Error: " . $e->getMessage());
            Session::flash('old', Request::all());
            Notification::swal2('Error', 'An error occurred while submitting registration: ' . $e->getMessage(), 'error');
            Response::back()->send();
            exit;
        }
        
    }
}