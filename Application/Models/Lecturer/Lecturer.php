<?php

class Lecturer extends Model
{
    /**
     * Nama Tabel
     */
    protected $table = 'lecturer';

    /**
     * Primary Key
     */
    protected $primaryKey = 'nim_nik_unit';

    /**
     * Definisi Schema & Tipe Data
     * Mengaktifkan fitur Auto-Casting pada core Model.
     */
    protected $schema = [
        'nim_nik_unit'    => ['type' => 'string', 'length' => 100],
        'name'            => ['type' => 'string', 'length' => 100],
        'email_polibatam' => ['type' => 'string', 'length' => 100],
        'is_koor'         => ['type' => 'bool', 'length' => 1],
        'prodi_koor'      => ['type' => 'string', 'length' => 200],
        'status'          => ['type' => 'string', 'length' => null], // Enum
        'password'        => ['type' => 'string', 'length' => 200],
        'profile_picture' => ['type' => 'string', 'length' => 100],
        'id_kampus'       => ['type' => 'int', 'length' => 11],
        'created_at'      => ['type' => 'datetime', 'length' => null],
        'updated_at'      => ['type' => 'string', 'length' => 255],
        'is_deleted'      => ['type' => 'bool', 'length' => 1],
        'deleted_at'      => ['type' => 'datetime', 'length' => null],
        'deleted_by'      => ['type' => 'string', 'length' => 255],
    ];

    /**
     * Daftar Kolom (Mass Assignment)
     */
    public $fillable = [
        'nim_nik_unit',
        'name',
        'email_polibatam',
        'is_koor',
        'prodi_koor',
        'status',
        'password',
        'profile_picture',
        'id_kampus',
        'created_at',
        'updated_at',
        'is_deleted',
        'deleted_at',
        'deleted_by'
    ];

    // =========================================================================
    // RELATIONS
    // =========================================================================

    /**
     * Mengambil daftar mahasiswa bimbingan dosen ini.
     */
    public function students()
    {
        return StudentInternship::where('nik_dospem', $this->nim_nik_unit)
                                ->where('account_status', true)
                                ->get();
    }

    /**
     * Relasi 1-to-1 ke LecturerProfile
     */
    public function profile()
    {
        return LecturerProfile::where('nim_nik_unit', $this->nim_nik_unit)->first();
    }

    /**
     * Cek kelengkapan profil
     */
    public function hasProfile()
    {
        return $this->profile() !== null;
    }

    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    /**
     * Cek apakah akun aktif (Status Active DAN Belum dihapus)
     */
    public function isActive()
    {
        return $this->status === 'Active' && $this->is_deleted === false;
    }

    /**
     * Cek apakah Dosen ini adalah Koordinator Prodi
     */
    public function isKoordinator()
    {
        return $this->is_koor === true;
    }

    /**
     * Mengambil URL Foto Profil
     */
    public function getAvatar()
    {
        $img = !empty($this->profile_picture) ? $this->profile_picture : 'default.jpg';
        return Url::asset("uploads/profile_picture/" . $img);
    }

    /**
     * Set Password Baru (Hash)
     */
    public function setNewPassword($plainPassword)
    {
        $hash = password_hash($plainPassword, PASSWORD_DEFAULT);
        
        return self::where('nim_nik_unit', $this->nim_nik_unit)->update([
            'password' => $hash,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    // =========================================================================
    // SOFT DELETE HANDLING
    // =========================================================================

    /**
     * Melakukan Soft Delete
     */
    public function softDelete($actor = 'System')
    {
        return self::where('nim_nik_unit', $this->nim_nik_unit)->update([
            'is_deleted' => true,
            'deleted_at' => date('Y-m-d H:i:s'),
            'deleted_by' => $actor,
            'status'     => 'Not Active'
        ]);
    }

    /**
     * Mengembalikan data yang sudah di-soft delete
     */
    public function restore()
    {
        return self::where('nim_nik_unit', $this->nim_nik_unit)->update([
            'is_deleted' => false,
            'deleted_at' => null,
            'deleted_by' => null,
            'status'     => 'Active'
        ]);
    }

    /**
     * Scope Manual: Query untuk mengambil dosen yang TIDAK dihapus
     */
    public static function queryNotDeleted()
    {
        return self::where('is_deleted', false);
    }
}