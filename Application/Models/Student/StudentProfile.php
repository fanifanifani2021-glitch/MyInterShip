<?php

class StudentProfile extends Model
{
    /**
     * Nama Tabel
     */
    protected $table = 'student_profile';

    /**
     * Primary Key
     */
    protected $primaryKey = 'id';

    /**
     * Definisi Schema & Tipe Data
     * Mengaktifkan fitur Auto-Casting pada core Model.
     */
    protected $schema = [
        'id'                  => ['type' => 'int', 'length' => 11],
        'nim'                 => ['type' => 'string', 'length' => 50],
        'talenthub_cv'        => ['type' => 'string', 'length' => 255],
        'other_cv'            => ['type' => 'string', 'length' => 255],
        'academic_transcript' => ['type' => 'string', 'length' => 255],
    ];

    /**
     * Daftar Kolom (Mass Assignment)
     */
    public $fillable = [
        'nim',
        'talenthub_cv',
        'other_cv',
        'academic_transcript'
    ];

    // =========================================================================
    // RELATIONS (Inverse 1-to-1)
    // =========================================================================

    /**
     * Mengambil data Mahasiswa pemilik profile ini.
     * Penggunaan: $profile->student->name
     */
    public function student()
    {
        return StudentInternship::where('nim', $this->nim)->first();
    }

    // =========================================================================
    // HELPER METHODS (Accessors)
    // =========================================================================

    /**
     * Mengambil URL Talenthub yang valid (memastikan ada http/https).
     * Penggunaan: $profile->getTalentHubUrl()
     */
    public function getTalentHubUrl()
    {
        $url = $this->talenthub_cv;
        
        if (empty($url)) return '#';

        // Cek apakah diawali http/https, jika tidak tambahkan https://
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "https://" . $url;
        }
        
        return $url;
    }

    /**
     * Mengambil Full URL untuk File CV Tambahan.
     * Path: assets/uploads/cv/
     */
    public function getOtherCvLink()
    {
        if (empty($this->other_cv)) {
            return null;
        }
        return Url::asset('assets/uploads/cv/' . $this->other_cv);
    }

    /**
     * Mengambil Full URL untuk Transkrip Nilai.
     * Path: assets/uploads/transcript/
     */
    public function getTranscriptLink()
    {
        if (empty($this->academic_transcript)) {
            return null;
        }
        return Url::asset('assets/uploads/transcript/' . $this->academic_transcript);
    }
}