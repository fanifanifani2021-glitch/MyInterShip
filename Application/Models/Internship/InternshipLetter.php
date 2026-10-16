<?php

class InternshipLetter extends Model
{
    /**
     * Nama Tabel
     */
    protected $table = 'internship_letter';

    /**
     * Primary Key (Tipe Varchar/String)
     */
    protected $primaryKey = 'id_internship_letter';

    /**
     * Definisi Schema & Tipe Data
     * Mengaktifkan fitur Auto-Casting pada core Model.
     */
    protected $schema = [
        'id_internship_letter' => ['type' => 'string', 'length' => 100],
        'id_company'           => ['type' => 'int', 'length' => 11],
        'student_nim'          => ['type' => 'string', 'length' => 100],
        'class'                => ['type' => 'string', 'length' => 100],
        'semester'             => ['type' => 'int', 'length' => 11],
        'is_new_company'       => ['type' => 'bool', 'length' => 1],
        'expected_startdate'   => ['type' => 'string', 'length' => null],
        'expected_enddate'     => ['type' => 'string', 'length' => null],
        'letter_languange'     => ['type' => 'string', 'length' => null], // Enum EN/ID
        'status'               => ['type' => 'string', 'length' => null], // Enum
        'coor_approval'        => ['type' => 'string', 'length' => null], // Enum
        'cdc_approval'         => ['type' => 'string', 'length' => null], // Enum
        'acceptance_status'    => ['type' => 'string', 'length' => null], // Enum
        'has_reply'            => ['type' => 'string', 'length' => null], // Enum
        'company_reply_letter' => ['type' => 'string', 'length' => 200],
        'letter_number'        => ['type' => 'string', 'length' => 50],
        'full_letter_number'   => ['type' => 'string', 'length' => 100],
        'letter_date'          => ['type' => 'string', 'length' => 50],
        'data_hash'            => ['type' => 'string', 'length' => 255],
        'created_at'           => ['type' => 'string', 'length' => 100],
        'updated_at'           => ['type' => 'string', 'length' => 100],
    ];

    /**
     * Daftar Kolom (Mass Assignment)
     */
    public $fillable = [
        'id_internship_letter', 'id_company', 'student_nim', 'class', 'semester',
        'is_new_company', 'expected_startdate', 'expected_enddate', 'letter_languange',
        'status', 'coor_approval', 'cdc_approval', 'acceptance_status', 'has_reply',
        'company_reply_letter', 'letter_number', 'full_letter_number', 'letter_date',
        'data_hash', 'created_at', 'updated_at'
    ];

    // =========================================================================
    // RELATIONS
    // =========================================================================

    /**
     * Relasi ke Perusahaan tujuan magang.
     */
    public function company()
    {
        return Company::where('id_company', $this->id_company)->first();
    }

    /**
     * Relasi ke Mahasiswa pemohon surat.
     */
    public function student()
    {
        return StudentInternship::where('nim', $this->student_nim)->first();
    }

    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    /**
     * Cek apakah surat sudah disetujui sepenuhnya (Koordinator & CDC).
     */
    public function isFullyApproved()
    {
        return $this->coor_approval === 'approved' && $this->cdc_approval === 'approved';
    }

    /**
     * Mengambil URL file balasan dari perusahaan.
     * Path: assets/uploads/reply_letters/
     */
    public function getReplyLetterLink()
    {
        if (empty($this->company_reply_letter)) {
            return null;
        }
        return Url::asset('uploads/reply_letters/' . $this->company_reply_letter);
    }

    /**
     * Mendapatkan label status dengan format yang lebih rapi.
     */
    public function getStatusLabel()
    {
        return ucfirst(strtolower($this->status));
    }

    /**
     * Cek apakah perusahaan yang dituju adalah perusahaan baru (belum ada di sistem).
     */
    public function isNewCompany()
    {
        return $this->is_new_company === true;
    }
}