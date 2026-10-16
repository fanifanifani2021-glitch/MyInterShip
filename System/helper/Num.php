<?php

class Num
{
    // --- METHOD DARI KODEMU ---

    /**
     * Format ke Rupiah (Rp 150.000)
     */
    public static function rupiah($angka)
    {
        return "Rp " . number_format($angka, 0, ',', '.');
    }

    /**
     * Format ukuran file (Bytes ke MB/GB)
     */
    public static function size($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Alias untuk size() agar mirip Laravel
     */
    public static function fileSize($bytes, $precision = 2)
    {
        return self::size($bytes, $precision);
    }

    // --- METHOD BARU (LARAVEL STYLE) ---

    /**
     * 1. Abbreviate (Singkatan Angka untuk Dashboard)
     * Contoh: 1500 -> 1.5K, 1000000 -> 1M
     */
    public static function abbreviate($number, $precision = 1)
    {
        if ($number < 1000) {
            return number_format($number, $precision);
        }

        $suffixes = ['', 'K', 'M', 'B', 'T'];
        $suffixIndex = 0;

        while ($number >= 1000 && $suffixIndex < count($suffixes) - 1) {
            $number /= 1000;
            $suffixIndex++;
        }

        return number_format($number, $precision) . $suffixes[$suffixIndex];
    }

    /**
     * 2. Clamp (Membatasi Angka)
     * Memastikan angka tidak kurang dari min dan tidak lebih dari max.
     * Contoh: clamp(105, 0, 100) -> 100
     */
    public static function clamp($number, $min, $max)
    {
        return max($min, min($max, $number));
    }

    /**
     * 3. Percentage (Format Persen)
     * Contoh: 0.25 -> 25%, 98 -> 98%
     */
    public static function percentage($number, $precision = 0)
    {
        return number_format($number, $precision) . '%';
    }

    /**
     * 4. Ordinal (Urutan)
     * Contoh: 1 -> 1st, 2 -> 2nd (English)
     * Jika locale 'id', output: ke-1, ke-2
     */
    public static function ordinal($number, $locale = 'en')
    {
        if ($locale === 'id') {
            return 'ke-' . $number;
        }

        // Logic English (st, nd, rd, th)
        $ends = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
        
        if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
            return $number . 'th';
        }
        
        return $number . $ends[$number % 10];
    }

    /**
     * 5. Format Umum (Wrapper number_format yang lebih bersih)
     * Default menggunakan format Indonesia (koma untuk desimal)
     */
    public static function format($number, $precision = 0, $locale = 'id')
    {
        if ($locale === 'id') {
            return number_format($number, $precision, ',', '.');
        }
        return number_format($number, $precision, '.', ',');
    }
}