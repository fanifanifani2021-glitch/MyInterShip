<?php

class LecturerProfile extends Model
{
    /**
     * Nama Tabel
     */
    protected $table = 'lecturer_profile';

    /**
     * Primary Key
     */
    protected $primaryKey = 'id_lecturer_profile';

    /**
     * Definisi Schema & Tipe Data
     * Mengaktifkan fitur Auto-Casting pada core Model.
     */
    protected $schema = [
        'id_lecturer_profile' => ['type' => 'int', 'length' => 11],
        'nim_nik_unit'        => ['type' => 'string', 'length' => 100],
        'other_email'         => ['type' => 'string', 'length' => 100],
        'phone'               => ['type' => 'string', 'length' => 100],
    ];

    /**
     * Daftar Kolom (Mass Assignment)
     * id_lecturer_profile tidak dimasukkan karena Auto Increment.
     */
    public $fillable = [
        'nim_nik_unit',
        'other_email',
        'phone'
    ];

    // =========================================================================
    // RELATIONS (Inverse 1-to-1)
    // =========================================================================

    /**
     * Mengambil data Dosen pemilik profil ini.
     * Penggunaan: $profile->lecturer->name
     */
    public function lecturer()
    {
        return Lecturer::where('nim_nik_unit', $this->nim_nik_unit)->first();
    }

    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    /**
     * Mengubah format nomor HP (08xxx) menjadi format Link WhatsApp (628xxx).
     * Berguna untuk tombol "Chat Dosen" di frontend.
     */
    public function getWhatsAppLink()
    {
        $phone = $this->phone;

        if (empty($phone)) return null;

        // Hapus karakter selain angka
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Jika diawali 0, ganti dengan 62
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        return "https://wa.me/" . $phone;
    }

    /**
     * Cek apakah dosen punya email alternatif
     */
    public function hasOtherEmail()
    {
        return !empty($this->other_email);
    }
}