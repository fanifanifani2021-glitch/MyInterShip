<?php

class Internship extends Model
{
    protected $table = 'internship';
    protected $primaryKey = 'id_internship';

    protected $fillable = [
        'id_internship_letter', 'nim', 'id_company', 'start_date', 'end_date',
        'id_user_company', 'nik', 'status', 'internship_position', 'file_1',
        'file_2', 'file_3', 'date_finalreport', 'final_report', 'internship_period'
    ];

    protected $schema = [
        'id_internship'      => ['type' => 'int'],
        'start_date'         => ['type' => 'date'],
        'end_date'           => ['type' => 'date'],
        'timestamp_register' => ['type' => 'datetime'],
    ];

    protected $useTimestamps = false;

    // =========================================================================
    // BUSINESS LOGIC METHODS
    // =========================================================================

    /**
     * Menghitung durasi magang (Selisih Bulan)
     * @param string $lang 'en' or 'id'
     */
    public function getDuration($lang = 'en')
    {
        if (empty($this->start_date) || empty($this->end_date)) {
            return "NaN";
        }

        try {
            $start = new DateTime($this->start_date);
            $end   = new DateTime($this->end_date);
            $diff  = $start->diff($end);

            // Rumus: (Tahun * 12) + Bulan
            $months = ($diff->y * 12) + $diff->m;
            
            // Bulatkan ke atas jika ada sisa hari
            if ($diff->d > 0) $months++;
            
            if ($months <= 0) $months = 1;

            return ($lang === 'id') ? "$months Bulan" : "$months Months";
        } catch (Exception $e) {
            return "NaN";
        }
    }

    /**
     * Memformat tanggal pendaftaran
     * @param string $lang 'en' or 'id'
     */
    public function getRegisteredDate($lang = 'en')
    {
        if (empty($this->timestamp_register)) return "NaN";

        $time = strtotime($this->timestamp_register);
        
        if ($lang === 'id') {
            $months = [
                1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            return date('d', $time) . ' ' . $months[(int)date('m', $time)] . ' ' . date('Y', $time);
        }

        return date('d F Y', $time);
    }

    /**
     * Mendapatkan label status yang manusiawi
     * @param string $lang 'en' or 'id'
     */
    public function getStatusLabel($lang = 'en')
    {
        $statusMap = [
            'id' => [
                'waiting' => 'Menunggu',
                'yes'     => 'Sedang Berjalan',
                'finish'  => 'Selesai',
                'no'      => 'Ditolak'
            ],
            'en' => [
                'waiting' => 'Pending/Waiting',
                'yes'     => 'Active / Ongoing',
                'finish'  => 'Completed',
                'no'      => 'Rejected'
            ]
        ];

        return $statusMap[$lang][ strtolower($this->status) ] ?? $this->status;
    }

    /**
     * Helper untuk warna Badge (CSS Class)
     */
    public function getStatusColor()
    {
        $colors = [
            'waiting' => 'warning',
            'yes'     => 'success',
            'finish'  => 'primary',
            'no'      => 'danger'
        ];
        return $colors[ strtolower($this->status) ] ?? 'secondary';
    }

    /**
     * Mendapatkan rentang tanggal magang (Start - End)
     * @param string $lang 'en' or 'id'
     * @return string
     */
    public function getDateRange($lang = 'en')
    {
        if (empty($this->start_date) || empty($this->end_date)) {
            return "NaN";
        }

        // Fungsi internal untuk memformat per tanggal
        $formatDate = function($dateStr) use ($lang) {
            $time = strtotime($dateStr);
            
            if ($lang === 'id') {
                // Singkatan bulan versi Indonesia
                $monthsId = [
                    'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
                    'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
                ];
                $monthIndex = (int)date('m', $time) - 1;
                return date('d', $time) . ' ' . $monthsId[$monthIndex] . ' ' . date('Y', $time);
            }

            // Default English (Jan, Feb, Aug, etc.)
            return date('d M Y', $time);
        };

        return $formatDate($this->start_date) . ' - ' . $formatDate($this->end_date);
    }

    // =========================================================================
    // RELATIONSHIPS (Lazy Loading)
    // =========================================================================

    public function student() { return $this->belongsTo(StudentInternship::class, 'nim', 'nim'); }
    public function company() { return $this->belongsTo(Company::class, 'id_company', 'id_company'); }
    public function lecturer() { return $this->belongsTo(Lecturer::class, 'nik', 'nim_nik_unit'); }

    /**
     * Shortcut untuk mendapatkan Koordinator Magang via Student
     * @return Lecturer|null
     */
    public function getCoordinator()
    {
        return $this->student->getCoordinator();
    }


    /**
     * Shortcut untuk mendapatkan personil UPKPK via Student
     * @return Upkpk|null
     */
    public function getUpkpk()
    {
        return $this->student->getUpkpk();
    }


    /**
     * Shortcut mendapatkan data Kampus via Mahasiswa
     */
    public function campus()
    {
        // Mengambil property campus dari object student
        return $this->student->campus;
    }

}