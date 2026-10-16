<?php

class Campus extends Model
{
    /**
     * Nama Tabel
     */
    protected $table = 'tb_kampus';

    /**
     * Primary Key
     */
    protected $primaryKey = 'id_kampus';

    /**
     * Definisi Schema & Tipe Data
     * Mengaktifkan fitur Auto-Casting pada core Model.
     */
    protected $schema = [
        'id_kampus'   => ['type' => 'int', 'length' => 11],
        'nama_kampus' => ['type' => 'string', 'length' => 200],
        'website'     => ['type' => 'string', 'length' => 200],
        'logo'        => ['type' => 'string', 'length' => 200],
        'created_at'  => ['type' => 'string', 'length' => 100],
        'updated_at'  => ['type' => 'string', 'length' => 100],
    ];

    /**
     * Daftar Kolom (Mass Assignment)
     */
    public $fillable = [
        'nama_kampus',
        'website',
        'logo',
        'created_at',
        'updated_at'
    ];

    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    /**
     * Mengambil URL Logo Kampus.
     * Jika kolom logo kosong, mengembalikan gambar default.
     */
    public function getLogo()
    {
        $img = !empty($this->logo) ? $this->logo : 'default_logo.png';
        return Url::asset("assets/uploads/campus/" . $img);
    }

    /**
     * Mendapatkan URL Website yang valid (memastikan ada protokol http/https).
     */
    public function getWebsiteUrl()
    {
        $url = $this->website;
        
        if (empty($url)) return '#';

        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "https://" . $url;
        }
        
        return $url;
    }

    /**
     * Relasi: Mendapatkan semua mahasiswa yang terdaftar di kampus ini.
     */
    public function students()
    {
        return StudentInternship::where('id_kampus', $this->id_kampus)->get();
    }
}