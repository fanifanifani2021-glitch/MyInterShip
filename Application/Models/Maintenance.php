<?php

class Maintenance extends Model {
    use CanDataTable;

    protected $table = 'maintenance_settings';
    protected $primaryKey = 'id';


        /**
     * Daftar Kolom Mass Assignment
     */
    public $fillable = [
        'id', 'title', 'description', 'start_date', 'end_date', 'is_active', 'allowed_ips'
    ];

    protected $schema = [
        'id'           => ['type' => 'int'],
        'title'        => ['type' => 'string'],
        'description'  => ['type' => 'string'],
        'start_date'   => ['type' => 'datetime'],
        'end_date'     => ['type' => 'datetime'],
        'is_active'    => ['type' => 'bool'],
        'allowed_ips'  => ['type' => 'string'],
    ];

    /**
     * Mengecek apakah ada jadwal maintenance aktif di DB
     */
    public static function getActiveScheduled() {
        $now = date('Y-m-d H:i:s');
        return self::where('is_active', 1)
                   ->where('start_date', '<=', $now)
                   ->where('end_date', '>=', $now)
                   ->first();
    }

    /**
     * Mengambil jadwal maintenance mendatang yang aktif
     * @param int $limit Jumlah data yang ingin ditampilkan
     */
    public static function getUpcoming($limit = 1)
    {
        $now = date('Y-m-d H:i:s');
        
        return self::where('is_active', 1)
                ->where('start_date', '>=', $now)
                ->orderBy('start_date', 'ASC')
                ->limit($limit)
                ->get();
    }
    
}