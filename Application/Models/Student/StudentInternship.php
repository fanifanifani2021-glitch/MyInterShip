<?php

use AuthenticatableTrait;
use Campus;
use Lecturer;
use Model;
use ProgramStudy;
use StudentProfile;
use Upkpk;
use Url;

class StudentInternship extends Model
{
    use AuthenticatableTrait;

    /**
     * Nama Tabel di Database
     */
    protected $table = 'student_internship';

    /**
     * Primary Key
     */
    protected $primaryKey = 'nim';

    /**
     * [SETTING SESSION DISINI]
     * Tentukan kolom apa saja yang ingin masuk ke Session
     */
    protected $sessionFields = ['nim', 'name', 'email', 'id_kampus', 'program_study'];
    
    /**
     * Tentukan Role untuk model ini
     */
    protected $sessionRole = 'Mahasiswa';

    /**
     * Custom Redirect jika dibutuhkan
     */
    public function redirectAfterLogin()
    {
        return empty($this->username) ? 'student_local_account' : Url::base('dashboard');
    }

    /**
     * [BARU] Definisi Schema & Tipe Data
     * Ini mengaktifkan fitur Auto-Casting di core Model.
     */
    protected $schema = [
        'nim'             => ['type' => 'string', 'length' => 50],
        'name'            => ['type' => 'string', 'length' => 100],
        'program_study'   => ['type' => 'string', 'length' => 100],
        'email'           => ['type' => 'string', 'length' => 100],
        'other_email'     => ['type' => 'string', 'length' => 100],
        'phone'           => ['type' => 'string', 'length' => 100],
        'no_whatsapp'     => ['type' => 'string', 'length' => 100],
        'username'        => ['type' => 'string', 'length' => 255],
        'password'        => ['type' => 'string', 'length' => 200],
        'profile_picture' => ['type' => 'string', 'length' => 100],
        'nik_dospem'      => ['type' => 'string', 'length' => 200],
        
        // Auto Cast ke Integer
        'id_kampus'       => ['type' => 'int', 'length' => 11],
        
        // Auto Cast ke Boolean (True/False)
        'account_status'  => ['type' => 'bool', 'length' => 1],
        'local_account'   => ['type' => 'bool', 'length' => 1],
        'password_reset'  => ['type' => 'bool', 'length' => 1],
        
        'created_at'      => ['type' => 'datetime', 'length' => null],
        'updated_at'      => ['type' => 'string', 'length' => 100],
    ];

    /**
     * Daftar Kolom Mass Assignment
     */
    public $fillable = [
        'nim', 'name', 'program_study', 'email', 'other_email', 
        'phone', 'no_whatsapp', 'username', 'password', 'profile_picture', 
        'nik_dospem', 'id_kampus', 'account_status', 'local_account', 
        'password_reset', 'created_at', 'updated_at'
    ];

    // =========================================================================
    // RELATIONS
    // =========================================================================

    /**
     * Relasi ke StudentProfile (One-to-One)
     */
    public function profile()
    {
        return StudentProfile::where('nim', $this->nim)->first();
    }
    
    public function hasProfile()
    {
        return $this->profile() !== null;
    }

    /**
     * Mendapatkan data Dosen Pembimbing
     * Menggunakan Model Lecturer yang sudah dibuat.
     */
    public function getDospem()
    {
        if (empty($this->nik_dospem)) {
            return null;
        }
        
        // Menggunakan Model Lecturer (bukan DB::table lagi agar return Object)
        // Kolom primary key Lecturer adalah 'nim_nik_unit'
        return Lecturer::where('nim_nik_unit', $this->nik_dospem)->first();
    }

    /**
     * Mendapatkan NIK / NIP Dosen Pembimbing
     * @return string|null
     */
    public function getDospemNik()
    {
        // 1. Cek langsung dari atribut nik_dospem milik mahasiswa
        if (!empty($this->nik_dospem)) {
            return $this->nik_dospem;
        }

        // 2. Jika tidak ada, coba ambil via relasi Lecturer
        $dospem = $this->getDospem();
        if ($dospem) {
            return $dospem->nim_nik_unit ?? $dospem->nik ?? null;
        }

        return null;
    }

    // =========================================================================
    // HELPER METHODS (Business Logic)
    // =========================================================================

    /**
     * Cek apakah akun mahasiswa aktif.
     * Tidak perlu (int) casting lagi karena $this->account_status sudah Boolean.
     * @return bool
     */
    public function isActive()
    {
        return $this->account_status === true;
    }

    /**
     * Cek apakah akun lokal.
     * @return bool
     */
    public function isLocalAccount()
    {
        return $this->local_account === true;
    }

    /**
     * Mengambil URL Foto Profil
     */
    public function getAvatar()
    {
        $img = !empty($this->profile_picture) ? $this->profile_picture : 'default.jpg';
        return Url::asset("uploads/profile_picture/" . $img);
    }

    public function getAvatarUrl()
    {
        $photo = !empty($this->profile_picture) ? $this->profile_picture : 'default.jpg';
        return router('viewer.student.avatar', ['filename' => base64_encode($photo)]);
    }

    /**
     * Helper untuk Set Password Baru
     */
    public function setNewPassword($plainPassword)
    {
        $hash = password_hash($plainPassword, PASSWORD_DEFAULT);
        
        return self::where('nim', $this->nim)->update([
            'password' => $hash,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }


    public function programStudy()
    {
        return $this->belongsTo(ProgramStudy::class, 'program_study', 'kode_prodi');
    }


    /**
     * Mendapatkan Koordinator Magang untuk Prodi Mahasiswa ini
     * @return Lecturer|null
     */
    public function getCoordinator()
    {
        if (empty($this->program_study)) {
            return null;
        }

        // Mencari di tabel lecturer: is_koor aktif, prodi cocok, dan belum dihapus
        return Lecturer::where('is_koor', true)
                    ->where('prodi_koor', $this->program_study)
                    ->where('is_deleted', false)
                    ->first();
    }


    /**
     * Mendapatkan personil UPKPK (CDC) berdasarkan id_kampus mahasiswa
     * @return Upkpk|null
     */
    public function getUpkpk()
    {
        if (empty($this->id_kampus)) {
            return null;
        }

        // Mencari personil UPKPK yang aktif di kampus yang sama
        return Upkpk::where('id_kampus', $this->id_kampus)
                    ->where('status', 'Active')
                    ->first();
    }

    /**
     * Relasi ke data Kampus
     */
    public function campus()
    {
        return $this->belongsTo(Campus::class, 'id_kampus', 'id_kampus');
    }
}