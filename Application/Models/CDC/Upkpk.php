<?php

class Upkpk extends Model
{
    /**
     * Nama Tabel
     */
    protected $table = 'upkpk';

    /**
     * Primary Key
     */
    protected $primaryKey = 'id_upkpk';

    /**
     * Definisi Schema & Tipe Data
     * Mengaktifkan fitur Auto-Casting pada core Model.
     */
    protected $schema = [
        'id_upkpk'        => ['type' => 'int', 'length' => 11],
        'username'        => ['type' => 'string', 'length' => 100],
        'name'            => ['type' => 'string', 'length' => 100],
        'status'          => ['type' => 'string', 'length' => 100],
        'password'        => ['type' => 'string', 'length' => 200],
        'profile_picture' => ['type' => 'string', 'length' => 200],
        'created_at'      => ['type' => 'string', 'length' => 100],
        'updated_at'      => ['type' => 'string', 'length' => 100],
        'id_kampus'       => ['type' => 'int', 'length' => 11],
    ];

    /**
     * Daftar Kolom (Mass Assignment)
     * id_upkpk tidak dimasukkan karena Auto Increment.
     */
    public $fillable = [
        'username',
        'name',
        'status',
        'password',
        'profile_picture',
        'created_at',
        'updated_at',
        'id_kampus'
    ];

    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    /**
     * Cek apakah akun UPKPK aktif.
     * @return bool
     */
    public function isActive()
    {
        return $this->status === 'Active';
    }

    /**
     * Mengambil URL Foto Profil.
     * Jika kosong, akan menggunakan 'profile.jpg' sebagai default.
     */
    public function getAvatar()
    {
        $img = !empty($this->profile_picture) ? $this->profile_picture : 'default.jpg';
        return Url::asset("uploads/profile_picture/" . $img);
    }

    /**
     * Helper untuk memperbarui password dengan enkripsi hash.
     * @param string $plainPassword
     */
    public function setNewPassword($plainPassword)
    {
        $hash = password_hash($plainPassword, PASSWORD_DEFAULT);
        
        return self::where('id_upkpk', $this->id_upkpk)->update([
            'password' => $hash,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }


    /**
     * Relasi ke data Kampus
     */
    public function campus()
    {
        return $this->belongsTo(Campus::class, 'id_kampus', 'id_kampus');
    }
}