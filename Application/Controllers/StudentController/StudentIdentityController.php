<?php
namespace Application\Controllers\StudentController;

use Controller;
use Request;
use Response;
use Session;
use Validator;
use Hash;
use Notification;
use StudentInternship;
use StudentProfile;
use ProgramStudy;
use File;
use Exception;

class StudentIdentityController extends Controller
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
     * Helper Private: Ambil Data Mahasiswa & Profile via Model ORM
     */
    private function getStudentData()
    {
        // 1. Ambil data Mahasiswa via Model StudentInternship
        $student = StudentInternship::where('nim', $this->nim)->first();

        if (!$student) {
            Notification::swal2('Access Denied', 'Student record not found.', 'error');
            Response::back()->send();
            exit;
        }

        // 2. Ambil Profil Mahasiswa via Model StudentProfile
        $profile = StudentProfile::where('nim', $this->nim)->first();

        // 3. Ambil Daftar Prodi berdasarkan Kampus via Model ProgramStudy
        $prodiList = ProgramStudy::where('id_kampus', $student->id_kampus)->get();

        return [
            'student' => $student,
            'profile' => $profile,
            'prodi'   => $prodiList
        ];
    }

    // =========================================================================
    // 1. HALAMAN DOKUMEN & DATA DIRI (IDENTITY)
    // =========================================================================
    public function index()
    {
        $info = $this->getStudentData();

        renderView('student.identity.index', [
            'title'        => 'Student Identity Profile',
            'routes'       => 'student_identity',
            'student_data' => $info['student'],
            'profile_data' => $info['profile'],
            'prodi_list'   => $info['prodi']
        ]);
    }

    /**
     * Proses Simpan Data Diri Mahasiswa
     */
    public function saveIdentity()
    {
        $rules = [
            'name'        => 'required|min:3',
            'email'       => 'required|email',
            'no_whatsapp' => 'required',
            'phone'       => 'required'
        ];

        $messages = [
            'name.required'        => 'Full Name is required.',
            'name.min'             => 'Full Name must be at least 3 characters.',
            'email.required'       => 'E-Mail address is required.',
            'email.email'          => 'Please enter a valid email address.',
            'no_whatsapp.required' => 'WhatsApp number is required.',
            'phone.required'       => 'Phone number is required.'
        ];

        $validator = Validator::make(Request::all(), $rules, [], $messages);

        if ($validator->fails()) {
            Session::flash('validation_errors', $validator->errors());
            Session::flash('old', Request::all());
            
            Notification::swal2('Validation Error', 'Please check your inputs and try again.', 'error');
            Response::back()->send();
            exit;
        }

        try {
            $student = StudentInternship::where('nim', $this->nim)->first();

            if (!$student) {
                Notification::swal2('Error', 'Student record not found.', 'error');
                Response::back()->send();
                exit;
            }

            // Update menggunakan method Model ORM
            $student->update([
                'name'        => Request::input('name'),
                'email'       => Request::input('email'),
                'other_email' => Request::input('other_email'),
                'no_whatsapp' => Request::input('no_whatsapp'),
                'phone'       => Request::input('phone')
            ]);

            Notification::swal2('Success!', 'Identity profile updated successfully.', 'success');
            Response::back()->send();
            exit;

        } catch (Exception $e) {
            error_log("Save Identity Error: " . $e->getMessage());
            Session::flash('old', Request::all());
            Notification::swal2('Update Failed', 'A system error occurred while updating your identity.', 'error');
            Response::back()->send();
            exit;
        }
    }

    /**
     * Proses Simpan Dokumen Magang (CV & Transkrip PDF)
     */
    public function saveDocument()
    {
        $rules = [
            'talenthub_cv' => 'required|url'
        ];

        $messages = [
            'talenthub_cv.required' => 'Talenthub CV link is required.',
            'talenthub_cv.url'      => 'Please provide a valid URL for your Talenthub CV.'
        ];

        $validator = Validator::make(Request::all(), $rules, [], $messages);

        if ($validator->fails()) {
            Session::flash('validation_errors', $validator->errors());
            Session::flash('old', Request::all());
            
            Notification::swal2('Validation Error', 'Please check the CV link provided.', 'error');
            Response::back()->send();
            exit;
        }

        try {
            $talenthubCv = Request::input('talenthub_cv');
            $transcriptName = null;

            // 🛡️ PERIKSA UPLOAD TRANSKRIP PDF
            if (!empty($_FILES['academic_transcript']['name']) && $_FILES['academic_transcript']['error'] === UPLOAD_ERR_OK) {
                
                $fileTmp  = $_FILES['academic_transcript']['tmp_name'];
                $fileSize = $_FILES['academic_transcript']['size'];
                $ext      = strtolower(pathinfo($_FILES['academic_transcript']['name'], PATHINFO_EXTENSION));

                // 1. Cek Ekstensi
                if ($ext !== 'pdf') {
                    Session::flash('old', Request::all());
                    Notification::swal2('Invalid Extension', 'Academic Transcript must have a .pdf extension.', 'error');
                    Response::back()->send();
                    exit;
                }

                // 2. Cek Ukuran Maksimal 2MB
                if ($fileSize > (2 * 1024 * 1024)) {
                    Session::flash('old', Request::all());
                    Notification::swal2('File Too Large', 'Academic Transcript file size must not exceed 2MB.', 'error');
                    Response::back()->send();
                    exit;
                }

                // 3. 🛡️ STRICT CHECK: VERIFIKASI MIME TYPE BINER DENGAN FILEINFO
                $finfo    = finfo_open(FILEINFO_MIME_TYPE);
                $realMime = finfo_file($finfo, $fileTmp);
                finfo_close($finfo);

                if ($realMime !== 'application/pdf') {
                    Session::flash('old', Request::all());
                    Notification::swal2('Security Alert', 'The uploaded file is not a real PDF document (detected MIME: ' . $realMime . ').', 'error');
                    Response::back()->send();
                    exit;
                }

                // Upload Menggunakan Helper File::upload()
                $transcriptName = File::upload($_FILES['academic_transcript'], 'upload/transcripts/');
                if (!$transcriptName) {
                    throw new Exception("Failed to save transcript file to server.");
                }
            }

            // Upsert ke tabel student_profile
            $profile = StudentProfile::where('nim', $this->nim)->first();

            if ($profile) {
                $updateData = ['talenthub_cv' => $talenthubCv];
                if ($transcriptName) {
                    $updateData['academic_transcript'] = 'upload/transcripts/' . $transcriptName;
                }
                $profile->update($updateData);
            } else {
                $profile = StudentProfile::create([
                    'nim'                 => $this->nim,
                    'talenthub_cv'        => $talenthubCv,
                    'academic_transcript' => $transcriptName ? 'upload/transcripts/' . $transcriptName : ''
                ]);
            }

            // 🔄 RE-SYNC / UPDATE SESSION STUDENT PROFILE (SANGAT PENTING!)
            // Mengambil profil terbaru yang baru saja disimpan untuk langsung memperbarui session
            $latestProfile = StudentProfile::where('nim', $this->nim)->first();
            if ($latestProfile) {
                $profileArray = is_object($latestProfile) && method_exists($latestProfile, 'toArray') 
                    ? $latestProfile->toArray() 
                    : (array) $latestProfile;

                $_SESSION['student_profile'] = $profileArray;
                Session::set('student_profile', $profileArray);
            }

            Notification::swal2('Saved!', 'Internship documents updated successfully.', 'success');
            Response::back()->send();
            exit;

        } catch (Exception $e) {
            error_log("Save Document Error: " . $e->getMessage());
            Session::flash('old', Request::all());
            Notification::swal2('Upload Failed', $e->getMessage(), 'error');
            Response::back()->send();
            exit;
        }
    }

    // =========================================================================
    // 2. HALAMAN FOTO PROFIL & RE-ENCODING KEAMANAN BACKEND
    // =========================================================================
    public function avatar()
    {
        $info = $this->getStudentData();

        renderView('student.identity.avatar', [
            'title'        => 'Profile Picture',
            'routes'       => 'student_identity',
            'student_data' => $info['student']
        ]);
    }

    /**
     * Upload & Re-encode Foto Profil di Backend + Update Session Profile Picture
     */
    public function saveAvatar()
    {
        if (empty($_FILES['new_profile']['tmp_name']) || $_FILES['new_profile']['error'] !== UPLOAD_ERR_OK) {
            Notification::swal2('Upload Failed', 'Please select a valid image file to upload.', 'warning');
            Response::back()->send();
            exit;
        }

        $tmpPath = $_FILES['new_profile']['tmp_name'];

        try {
            // 🛡️ RE-ENCODING GAMBAR DI BACKEND DENGAN GD LIBRARY (Hancurkan Webshell/Script Tersembunyi)
            $imageInfo = @getimagesize($tmpPath);
            if ($imageInfo === false) {
                Notification::swal2('Security Error', 'The uploaded file is not a valid binary image.', 'error');
                Response::back()->send();
                exit;
            }

            $mimeType = $imageInfo['mime'];
            switch ($mimeType) {
                case 'image/jpeg':
                    $srcImage = @imagecreatefromjpeg($tmpPath);
                    break;
                case 'image/png':
                    $srcImage = @imagecreatefrompng($tmpPath);
                    break;
                case 'image/webp':
                    $srcImage = @imagecreatefromwebp($tmpPath);
                    break;
                default:
                    Notification::swal2('Unsupported Format', 'Only JPG, PNG, and WEBP images are allowed.', 'warning');
                    Response::back()->send();
                    exit;
            }

            if (!$srcImage) {
                throw new Exception("Failed to process image structure.");
            }

            // Canvas Baru untuk Menghancurkan EXIF/Metadata Script yang Tersembunyi
            $width  = imagesx($srcImage);
            $height = imagesy($srcImage);
            $cleanImage = imagecreatetruecolor($width, $height);

            // Jaga transparansi jika format PNG / WEBP
            imagealphablending($cleanImage, false);
            imagesavealpha($cleanImage, true);
            imagecopyresampled($cleanImage, $srcImage, 0, 0, 0, 0, $width, $height, $width, $height);

            $uploadDir = 'upload/profile_picture/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $newFilename = 'avatar_' . $this->nim . '_' . time() . '.png';
            $destination = $uploadDir . $newFilename;

            // Simpan gambar PNG hasil re-encoding
            imagepng($cleanImage, $destination, 8);
            imagedestroy($srcImage);
            imagedestroy($cleanImage);

            // 1. Update Database via Model StudentInternship
            $student = StudentInternship::where('nim', $this->nim)->first();
            if ($student) {
                $student->update(['profile_picture' => $newFilename]);
            }

            // 2. 🔄 UPDATE SESSION USER
            $_SESSION['profile_picture'] = $newFilename;
            Session::set('profile_picture', $newFilename);

            Notification::swal2('Updated!', 'Profile picture updated successfully.', 'success');
            Response::back()->send();
            exit;

        } catch (Exception $e) {
            error_log("Save Avatar Error: " . $e->getMessage());
            Notification::swal2('Avatar Error', 'An error occurred while processing profile picture.', 'error');
            Response::back()->send();
            exit;
        }
    }

    /**
     * AJAX Endpoint: Hapus Avatar Kembali ke Default + Update Session
     */
    public function deleteAvatar()
    {
        header('Content-Type: application/json');
        try {
            // 1. Update Database
            $student = StudentInternship::where('nim', $this->nim)->first();
            if ($student) {
                $student->update(['profile_picture' => 'default.jpg']);
            }

            // 2. 🔄 RESET SESSION TO DEFAULT
            $_SESSION['profile_picture'] = 'default.jpg';
            Session::set('profile_picture', 'default.jpg');

            echo json_encode(['status' => 'success', 'message' => 'Profile picture removed successfully.']);
            exit;
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit;
        }
    }

    // =========================================================================
    // 3. HALAMAN KEAMANAN & PASSWORD
    // =========================================================================
    public function security()
    {
        $info = $this->getStudentData();

        renderView('student.identity.security', [
            'title'        => 'Account Security & Password',
            'routes'       => 'student_identity',
            'student_data' => $info['student']
        ]);
    }

    /**
     * Toggle Local Account Status
     */
    public function saveLocalAccount()
    {
        try {
            $status = Request::integer('local_account', 0);

            $student = StudentInternship::where('nim', $this->nim)->first();
            if ($student) {
                $student->update(['local_account' => $status]);
            }

            Notification::swal2('Updated!', 'Local account status changed successfully.', 'success');
            Response::back()->send();
            exit;

        } catch (Exception $e) {
            Notification::swal2('Error', $e->getMessage(), 'error');
            Response::back()->send();
            exit;
        }
    }

    /**
     * Proses Ganti Password Lokal Mahasiswa
     */
    public function changePassword()
    {
        $rules = [
            'current_password' => 'required',
            'new_password'     => 'required|min:6',
            'new_password2'    => 'required'
        ];

        $messages = [
            'current_password.required' => 'Current password is required.',
            'new_password.required'     => 'New password is required.',
            'new_password.min'          => 'New password must be at least 6 characters long.',
            'new_password2.required'    => 'Please confirm your new password.'
        ];

        $validator = Validator::make(Request::all(), $rules, [], $messages);

        if ($validator->fails()) {
            Session::flash('validation_errors', $validator->errors());
            Session::flash('old', Request::all());
            
            Notification::swal2('Validation Failed', 'Please fix the errors in the form.', 'error');
            Response::back()->send();
            exit;
        }

        $currentPass = Request::input('current_password');
        $newPass     = Request::input('new_password');
        $newPassConf = Request::input('new_password2');

        // Validasi Konfirmasi Password
        if ($newPass !== $newPassConf) {
            Session::flash('old', Request::all());
            Notification::swal2('Password Mismatch', 'New password confirmation does not match.', 'warning');
            Response::back()->send();
            exit;
        }

        try {
            $student = StudentInternship::where('nim', $this->nim)->first();

            if (!$student) {
                Notification::swal2('Error', 'Student record not found.', 'error');
                Response::back()->send();
                exit;
            }

            // Verifikasi Password Lama Menggunakan Helper Hash::check()
            if (!empty($student->password) && !Hash::check($currentPass, $student->password)) {
                Session::flash('old', Request::all());
                Notification::swal2('Security Error', 'Your current password is incorrect.', 'error');
                Response::back()->send();
                exit;
            }

            // Encrypt Password Baru Menggunakan Helper Hash::make()
            $hashedPassword = Hash::make($newPass);
            $student->update(['password' => $hashedPassword]);

            Notification::swal2('Password Changed!', 'Your account password has been updated successfully.', 'success');
            Response::back()->send();
            exit;

        } catch (Exception $e) {
            error_log("Change Password Error: " . $e->getMessage());
            Session::flash('old', Request::all());
            Notification::swal2('Error', 'An internal server error occurred while updating your password.', 'error');
            Response::back()->send();
            exit;
        }
    }
}