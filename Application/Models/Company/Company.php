<?php

class Company extends Model
{
    /**
     * Nama Tabel
     */
    protected $table = 'company';

    /**
     * Primary Key
     */
    protected $primaryKey = 'id_company';

    /**
     * Definisi Schema & Tipe Data
     * Mengaktifkan fitur Auto-Casting pada core Model.
     */
    protected $schema = [
        'id_company'  => ['type' => 'int', 'length' => 11],
        'name'        => ['type' => 'string', 'length' => 192],
        'type'        => ['type' => 'string', 'length' => 100],
        'type_other'  => ['type' => 'string', 'length' => 200],
        'phone'       => ['type' => 'string', 'length' => 100],
        'email'       => ['type' => 'string', 'length' => 100],
        'website'     => ['type' => 'string', 'length' => 100],
        'facebook'    => ['type' => 'string', 'length' => 100],
        'twitter'     => ['type' => 'string', 'length' => 100],
        'instagram'   => ['type' => 'string', 'length' => 100],
        'linkedin'    => ['type' => 'string', 'length' => 100],
        'logo'        => ['type' => 'string', 'length' => 255],
        'address'     => ['type' => 'string', 'length' => 255],
        'province'    => ['type' => 'string', 'length' => 100],
        'city'        => ['type' => 'string', 'length' => 100],
        'description' => ['type' => 'string', 'length' => null], // Longtext
        'status'      => ['type' => 'string', 'length' => 100],
        'access_type' => ['type' => 'string', 'length' => 100],
        'id_kampus'   => ['type' => 'int', 'length' => 11],
    ];

    /**
     * Daftar Kolom (Mass Assignment)
     */
    public $fillable = [
        'name', 'type', 'type_other', 'phone', 'email', 'website',
        'facebook', 'twitter', 'instagram', 'linkedin', 'logo',
        'address', 'province', 'city', 'description', 'status',
        'access_type', 'id_kampus'
    ];


    
    /**
     * [BARU] Relasi One-to-One ke CompanyMou.
     * Mengambil data MOU terkait perusahaan ini.
     * Penggunaan: $company->mou->mou_status
     */
    public function mou()
    {
        // Menggunakan id_company sebagai kunci pencarian
        return CompanyMou::where('id_company', $this->id_company)->first();
    }

    /**
     * Helper untuk cek apakah perusahaan sudah memiliki MOU.
     */
    public function hasMou()
    {
        return $this->mou() !== null;
    }


    /**
     * Relasi ke Kampus asal.
     * Mengidentifikasi kampus mana yang mendaftarkan atau menaungi perusahaan ini.
     * Penggunaan: $company->addByCampus()->nama_kampus
     */
    public function addByCampus()
    {
        return Campus::where('id_kampus', $this->id_kampus)->first();
    }


    // =========================================================================
    // HELPER METHODS (Business Logic)
    // =========================================================================

    /**
     * Mendapatkan Tipe Perusahaan untuk Display.
     * Prioritas: Jika type_other memiliki nilai, gunakan itu. 
     * Jika tidak, gunakan type reguler.
     */
    public function getDisplayType()
    {
        // trim() memastikan string yang hanya berisi spasi juga dianggap kosong
        if (!empty(trim($this->type_other))) {
            return $this->type_other;
        }
        
        return $this->type ?? '-';
    }

    /**
     * Mendapatkan Badge HTML untuk Status Perusahaan.
     * Logika: 
     * - 1 atau 'verified' -> Hijau (Verified)
     * - 0 atau 'not verified' -> Merah (Not Verified)
     */
    public function getStatusBadge()
    {
        // Normalisasi: hilangkan spasi dan ubah ke huruf kecil
        $status = strtolower(trim($this->status));

        if ($status === '1' || $status === 'verified') {
            return '<span class="badge badge-success">
                        <i class="fas fa-check-circle mr-1"></i> Verified
                    </span>';
        }

        if ($status === '0' || $status === 'not verified') {
            return '<span class="badge badge-danger">
                        <i class="fas fa-times-circle mr-1"></i> Not Verified
                    </span>';
        }

        // Fallback jika data kosong atau tidak dikenali
        return '<span class="badge badge-secondary">Pending / Unknown</span>';
    }


    /**
     * Mengambil URL Logo Perusahaan.
     * Menggunakan viewer untuk fallback jika tidak ditemukan.
     */
    public function getLogo()
    {
        $logoRaw = trim($this->logo);
        // Pastikan nama file ini ada di folder /upload/default/
        $logoSource = 'default.png'; 

        if (!empty($logoRaw) && $logoRaw !== '-') {
            $logoSource = $logoRaw;
        }
        
        // Gunakan rtrim untuk menghilangkan padding '=' yang sering merusak rute
        $encodedSource = rtrim(strtr(base64_encode($logoSource), '+/', '-_'), '=');
        
        return router('viewer.company.logo', ['filename' => $encodedSource]);
    }
    /**
     * Menggabungkan alamat lengkap (Alamat, Kota, Provinsi).
     */
    public function getFullAddress()
    {
        return "{$this->address}, {$this->city}, {$this->province}";
    }

    /**
     * Membuat link WhatsApp otomatis dari nomor telepon perusahaan.
     */
    public function getWhatsAppLink()
    {
        if (empty($this->phone)) return null;
        $cleanPhone = preg_replace('/[^0-9]/', '', $this->phone);
        if (substr($cleanPhone, 0, 1) === '0') {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }
        return "https://wa.me/" . $cleanPhone;
    }

    /**
     * Memastikan URL website memiliki protokol (http/https).
     */
    public function getWebsiteUrl()
    {
        if (empty($this->website)) return '#';
        return (preg_match("~^(?:f|ht)tps?://~i", $this->website)) 
            ? $this->website 
            : "https://" . $this->website;
    }

}