<?php

class CompanyMou extends Model
{
    /**
     * Nama Tabel
     */
    protected $table = 'company_mou';

    /**
     * Primary Key
     */
    protected $primaryKey = 'id_mou';

    /**
     * Definisi Schema & Tipe Data
     * Mengaktifkan fitur Auto-Casting pada core Model.
     */
    protected $schema = [
        'id_mou'          => ['type' => 'int', 'length' => 11],
        'id_company'      => ['type' => 'int', 'length' => 11],
        'id_kampus'       => ['type' => 'int', 'length' => 11],
        'mou_type'        => ['type' => 'string', 'length' => 200],
        'mou_date'        => ['type' => 'date', 'length' => null],
        'mou_file'        => ['type' => 'string', 'length' => 200],
        'mou_status'      => ['type' => 'string', 'length' => 200],
        'mou_description' => ['type' => 'string', 'length' => 255],
        'created_at'      => ['type' => 'datetime', 'length' => null],
        'updated_at'      => ['type' => 'string', 'length' => 200],
    ];

    /**
     * Daftar Kolom (Mass Assignment)
     */
    public $fillable = [
        'id_company',
        'id_kampus',
        'mou_type',
        'mou_date',
        'mou_file',
        'mou_status',
        'mou_description',
        'created_at',
        'updated_at'
    ];

    // =========================================================================
    // RELATIONS
    // =========================================================================

    /**
     * Mendapatkan data Perusahaan terkait MOU ini.
     */
    public function company()
    {
        return Company::where('id_company', $this->id_company)->first();
    }

    /**
     * Mendapatkan data Kampus terkait MOU ini.
     */
    public function campus()
    {
        return Campus::where('id_kampus', $this->id_kampus)->first();
    }

    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    /**
     * Mengambil URL/Link file dokumen MOU.
     * Path: assets/uploads/mou/
     */
    public function getFileLink()
    {
        if (empty($this->mou_file)) {
            return null;
        }
        return Url::asset('uploads/mou/' . $this->mou_file);
    }

    /**
     * Cek apakah status MOU aktif.
     */
    public function isActive()
    {
        return strtolower($this->mou_status) === 'active';
    }

    /**
     * Memformat tanggal MOU menjadi format Indonesia (d F Y).
     */
    public function getFormattedDate()
    {
        if (empty($this->mou_date)) return '-';
        return date('d M Y', strtotime($this->mou_date));
    }
}