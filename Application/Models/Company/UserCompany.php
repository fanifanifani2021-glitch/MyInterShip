<?php

class UserCompany extends Model
{
    /**
     * Nama Tabel
     */
    protected $table = 'user_company';

    /**
     * Primary Key
     */
    protected $primaryKey = 'id_user_company';

    /**
     * Definisi Schema & Tipe Data
     * Mengaktifkan fitur Auto-Casting pada core Model.
     */
    protected $schema = [
        'id_user_company' => ['type' => 'int', 'length' => 11],
        'id_company'      => ['type' => 'int', 'length' => 11],
        'user_fullname'   => ['type' => 'string', 'length' => 100],
        'user_phone'      => ['type' => 'string', 'length' => 100],
        'user_email'      => ['type' => 'string', 'length' => 100],
        'username'        => ['type' => 'string', 'length' => 100],
        'password'        => ['type' => 'string', 'length' => 255],
        'user_type'       => ['type' => 'string', 'length' => 100],
        'profile_picture' => ['type' => 'string', 'length' => 100],
    ];

    /**
     * Daftar Kolom (Mass Assignment)
     */
    public $fillable = [
        'id_company',
        'user_fullname',
        'user_phone',
        'user_email',
        'username',
        'password',
        'user_type',
        'profile_picture'
    ];

    // =========================================================================
    // RELATIONS
    // =========================================================================

    /**
     * Relasi ke Perusahaan tempat user ini bekerja.
     * Penggunaan: $userCompany->company()->name
     */
    public function company()
    {
        return Company::where('id_company', $this->id_company)->first();
    }

    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    /**
     * Mengambil URL Foto Profil.
     */
    public function getAvatar()
    {
        $img = !empty($this->profile_picture) ? $this->profile_picture : 'default.jpg';
        return Url::asset("uploads/profile_picture/" . $img);
    }

    /**
     * Helper untuk memperbarui password dengan enkripsi hash.
     */
    public function setNewPassword($plainPassword)
    {
        $hash = password_hash($plainPassword, PASSWORD_DEFAULT);
        
        return self::where('id_user_company', $this->id_user_company)->update([
            'password' => $hash
        ]);
    }

    /**
     * Membuat link WhatsApp otomatis dari nomor telepon user perusahaan.
     */
    public function getWhatsAppLink()
    {
        if (empty($this->user_phone)) return null;
        
        $phone = preg_replace('/[^0-9]/', '', $this->user_phone);
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        return "https://wa.me/" . $phone;
    }

    /**
     * Cek apakah user adalah admin perusahaan (jika ada pembagian role).
     */
    public function isAdmin()
    {
        return strtolower($this->user_type) === 'admin';
    }
}