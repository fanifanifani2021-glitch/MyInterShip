<?php

class Date
{
    // Konfigurasi Default Language ('ID' atau 'EN')
    public static $currentLang = 'ID';

    // =========================================================================
    // 1. FORMATTING & DISPLAY
    // =========================================================================

    /**
     * Format Tanggal Bilingual
     * Date::format('2025-08-17') -> 17 Agustus 2025
     */
    public static function format($date, $format = 'd F Y', $lang = null)
    {
        if (empty($date) || $date == '0000-00-00') return '-';
        
        $lang = $lang ?? self::$currentLang;
        $timestamp = strtotime($date);
        $output = date($format, $timestamp);

        if ($lang == 'EN') return $output;
        return self::translateToID($output);
    }

    /**
     * [BARU] Menampilkan waktu relatif (Human Readable)
     * Date::ago('2025-01-01 10:00:00') -> "2 jam yang lalu" atau "2 hours ago"
     */
    public static function ago($date, $lang = null)
    {
        if (empty($date)) return '-';
        
        $lang = $lang ?? self::$currentLang;
        $time = strtotime($date);
        $diff = time() - $time;
        
        if ($diff < 1) { 
            return ($lang == 'ID') ? 'Baru saja' : 'Just now'; 
        }

        $tokens = [
            31536000 => ['tahun', 'year'],
            2592000  => ['bulan', 'month'],
            604800   => ['minggu', 'week'],
            86400    => ['hari', 'day'],
            3600     => ['jam', 'hour'],
            60       => ['menit', 'minute'],
            1        => ['detik', 'second']
        ];

        foreach ($tokens as $unit => $text) {
            if ($diff < $unit) continue;
            
            $numberOfUnits = floor($diff / $unit);
            
            if ($lang == 'ID') {
                return $numberOfUnits . ' ' . $text[0] . ' yang lalu';
            } else {
                $suffix = ($numberOfUnits > 1) ? 's' : '';
                return $numberOfUnits . ' ' . $text[1] . $suffix . ' ago';
            }
        }
    }

    /**
     * [BARU] Hitung Umur
     * Date::age('1990-05-20') -> 35
     */
    public static function age($birthDate)
    {
        if (empty($birthDate)) return 0;
        return date_diff(date_create($birthDate), date_create('today'))->y;
    }

    // =========================================================================
    // 2. CALCULATION & UTILITIES
    // =========================================================================

    /**
     * Hitung Durasi (Bilingual)
     * Date::duration($start, $end) -> 3 Bulan 2 Hari
     */
    public static function duration($startDate, $endDate, $lang = null)
    {
        if (empty($startDate) || empty($endDate)) return '-';

        $lang = $lang ?? self::$currentLang;

        try {
            $start = new DateTime($startDate);
            $end   = new DateTime($endDate);
            $end->modify('+1 day'); // Inclusive

            $interval = $start->diff($end);
            
            $m = $interval->m + ($interval->y * 12);
            $d = $interval->d;

            $parts = [];

            if ($lang == 'ID') {
                if ($m > 0) $parts[] = "$m Bulan";
                if ($d > 0) $parts[] = "$d Hari";
                if (empty($parts)) return "1 Hari";
            } else {
                if ($m > 0) $parts[] = $m . " " . ($m > 1 ? "Months" : "Month");
                if ($d > 0) $parts[] = $d . " " . ($d > 1 ? "Days" : "Day");
                if (empty($parts)) return "1 Day";
            }

            return implode(' ', $parts);

        } catch (Exception $e) {
            return '-';
        }
    }

    /**
     * [BARU] Mendapatkan tanggal hari ini / sekarang
     * Date::now() -> 2025-01-15 10:30:00
     */
    public static function now($format = 'Y-m-d H:i:s')
    {
        return date($format);
    }

    /**
     * [BARU] Konversi ke Format Database (Y-m-d)
     * Berguna saat menerima input form d/m/Y
     * Date::toSql('31/12/2024') -> 2024-12-31
     */
    public static function toSql($date)
    {
        if (empty($date)) return null;
        return date('Y-m-d', strtotime(str_replace('/', '-', $date)));
    }

    /**
     * [BARU] Cek apakah hari libur (Sabtu/Minggu)
     */
    public static function isWeekend($date)
    {
        $day = date('N', strtotime($date));
        return ($day >= 6);
    }

    /**
     * [BARU] Generate Range Tanggal (PENTING untuk Grafik/Analytics)
     * Menghasilkan array tanggal dari start sampai end
     */
    public static function range($start, $end, $format = 'Y-m-d')
    {
        $period = new DatePeriod(
            new DateTime($start),
            new DateInterval('P1D'),
            (new DateTime($end))->modify('+1 day')
        );

        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->format($format);
        }
        return $dates;
    }

    // =========================================================================
    // 3. HELPERS (NAMES)
    // =========================================================================

    public static function monthName($number, $lang = null)
    {
        $lang = $lang ?? self::$currentLang;
        $months = [
            'ID' => [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'],
            'EN' => [1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December']
        ];
        return $months[$lang][(int)$number] ?? '';
    }

    public static function dayName($date, $lang = null)
    {
        $lang = $lang ?? self::$currentLang;
        $dayNum = date('N', strtotime($date));
        $days = [
            'ID' => [1 => 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
            'EN' => [1 => 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
        ];
        return $days[$lang][$dayNum] ?? '';
    }

    // --- INTERNAL HELPER ---
    private static function translateToID($str)
    {
        $search = [
            'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December',
            'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec',
            'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday',
            'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'
        ];
        $replace = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des',
            'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu',
            'Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'
        ];
        return str_replace($search, $replace, $str);
    }
}