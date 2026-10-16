<?php

class ProgramStudy extends Model
{
    /**
     * Nama Tabel
     */
    protected $table = 'program_study';

    /**
     * Primary Key
     */
    protected $primaryKey = 'kode_prodi';

    /**
     * Definisi Schema & Tipe Data
     * Mengaktifkan fitur Auto-Casting pada core Model.
     */
    protected $schema = [
        'kode_prodi'    => ['type' => 'string', 'length' => 100],
        'jenjang'       => ['type' => 'string', 'length' => 100],
        'prodi'         => ['type' => 'string', 'length' => 100],
        'study_program' => ['type' => 'string', 'length' => 100],
        'jurusan'       => ['type' => 'string', 'length' => 100],
        'major'         => ['type' => 'string', 'length' => 100],
        'id_kampus'     => ['type' => 'int', 'length' => 11],
        'created_at'    => ['type' => 'datetime', 'length' => null],
        'updated_at'    => ['type' => 'string', 'length' => 255],
        'is_deleted'    => ['type' => 'bool', 'length' => 1],
        'deleted_at'    => ['type' => 'datetime', 'length' => null],
        'deleted_by'    => ['type' => 'string', 'length' => 255],
    ];

    /**
     * Daftar Kolom (Mass Assignment)
     */
    public $fillable = [
        'kode_prodi', 'jenjang', 'prodi', 'study_program', 'jurusan',
        'major', 'id_kampus', 'created_at', 'updated_at', 'is_deleted',
        'deleted_at', 'deleted_by'
    ];

    // =========================================================================
    // RELATIONS
    // =========================================================================

    /**
     * Relasi ke Kampus (Many-to-One).
     */
    public function campus()
    {
        return Campus::where('id_kampus', $this->id_kampus)->first();
    }

    /**
     * Mendapatkan semua mahasiswa di Program Studi ini.
     */
    public function students()
    {
        // Asumsi di StudentInternship ada kolom kode_prodi
        return StudentInternship::where('program_study', $this->prodi)->get();
    }

    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    /**
     * Mengambil nama lengkap prodi beserta jenjangnya.
     * Contoh: "D4 - Teknik Informatika"
     */
    public function getFullName($lang = 'id')
    {
        if ($lang === 'en') {
            return "({$this->kode_prodi}) {$this->jenjang} - {$this->study_program}";
        }
        return "({$this->kode_prodi}) {$this->jenjang} - {$this->prodi}";
    }

    /**
     * Alias untuk mendapatkan Jurusan dalam bahasa Inggris.
     */
    public function getEnglishMajor()
    {
        return $this->major ?? $this->jurusan;
    }

    // =========================================================================
    // SOFT DELETE HANDLING
    // =========================================================================

    /**
     * Melakukan Soft Delete.
     */
    public function softDelete($actor = 'System')
    {
        return self::where('kode_prodi', $this->kode_prodi)->update([
            'is_deleted' => true,
            'deleted_at' => date('Y-m-d H:i:s'),
            'deleted_by' => $actor
        ]);
    }

    /**
     * Mengembalikan data yang sudah di-soft delete.
     */
    public function restore()
    {
        return self::where('kode_prodi', $this->kode_prodi)->update([
            'is_deleted' => false,
            'deleted_at' => null,
            'deleted_by' => null
        ]);
    }

    /**
     * Scope Manual: Query untuk mengambil prodi yang TIDAK dihapus.
     */
    public static function queryNotDeleted()
    {
        return self::where('is_deleted', false);
    }
}