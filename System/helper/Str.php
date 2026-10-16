<?php

class Str
{
    // =========================================================================
    // 1. GENERATOR & RANDOM
    // =========================================================================

    /**
     * Membuat string acak (berguna untuk token/password default).
     */
    public static function random($length = 16)
    {
        $string = '';
        while (($len = strlen($string)) < $length) {
            $size = $length - $len;
            $bytes = random_bytes($size);
            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }
        return $string;
    }

    /**
     * Menghasilkan UUID v4 (Cryptographically Secure)
     */
    public static function uuid()
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // version 4
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // variant DCE 1.1
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Cek apakah string adalah UUID valid
     */
    public static function isUuid($value)
    {
        if (!is_string($value)) return false;
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $value) === 1;
    }

    // =========================================================================
    // 2. CASE CONVERSION (CAMEL, SNAKE, STUDLY)
    // =========================================================================

    /**
     * Convert ke 'studly_case' -> 'StudlyCase'
     */
    public static function studly($value)
    {
        $value = ucwords(str_replace(['-', '_'], ' ', $value));
        return str_replace(' ', '', $value);
    }

    /**
     * Convert ke 'camelCase'
     */
    public static function camel($value)
    {
        return lcfirst(static::studly($value));
    }

    /**
     * Convert ke 'snake_case'
     */
    public static function snake($value, $delimiter = '_')
    {
        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', ucwords($value));
            $value = static::lower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value));
        }
        return $value;
    }

    /**
     * Convert ke 'kebab-case'
     */
    public static function kebab($value)
    {
        return static::snake($value, '-');
    }

    /**
     * Title Case: "teknik informatika" -> "Teknik Informatika"
     */
    public static function title($value)
    {
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }
    
    public static function upper($value)
    {
        return mb_strtoupper($value, 'UTF-8');
    }

    public static function lower($value)
    {
        return mb_strtolower($value, 'UTF-8');
    }

    /**
     * URL Friendly Slug
     */
    public static function slug($string, $separator = '-')
    {
        $string = mb_strtolower($string, 'UTF-8');
        $string = preg_replace('/[^\s\p{L}\p{N}]+/u', ' ', $string);
        $string = preg_replace('/[\s]+/', $separator, $string);
        return trim($string, $separator);
    }

    // =========================================================================
    // 3. INSPECTION (CHECKER)
    // =========================================================================

    /**
     * Cek apakah string mengandung kata tertentu
     */
    public static function contains($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Cek apakah string diawali dengan kata tertentu
     */
    public static function startsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ((string) $needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Cek apakah string diakhiri dengan kata tertentu
     */
    public static function endsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && substr($haystack, -strlen($needle)) === (string) $needle) {
                return true;
            }
        }
        return false;
    }

    /**
     * Hitung jumlah kata
     */
    public static function wordCount($string)
    {
        return str_word_count($string);
    }

    // =========================================================================
    // 4. MANIPULATION & EXTRACTION
    // =========================================================================

    /**
     * Potong string (Limit characters)
     */
    public static function limit($value, $limit = 100, $end = '...')
    {
        if (mb_strwidth($value, 'UTF-8') <= $limit) {
            return $value;
        }
        return rtrim(mb_strimwidth($value, 0, $limit, '', 'UTF-8')) . $end;
    }

    /**
     * Potong berdasarkan jumlah kata (Limit Words)
     * Str::words('Halo saya sedang coding', 2) -> "Halo saya..."
     */
    public static function words($value, $words = 100, $end = '...')
    {
        preg_match('/^\s*+(?:\S++\s*+){1,'.$words.'}/u', $value, $matches);

        if (!isset($matches[0]) || strlen($value) === strlen($matches[0])) {
            return $value;
        }

        return rtrim($matches[0]) . $end;
    }

    /**
     * Ambil string SEBELUM kata kunci
     * Str::before('User@index', '@') -> 'User'
     */
    public static function before($subject, $search)
    {
        if ($search === '') return $subject;
        $result = strstr($subject, (string) $search, true);
        return $result === false ? $subject : $result;
    }

    /**
     * Ambil string SETELAH kata kunci
     * Str::after('User@index', '@') -> 'index'
     */
    public static function after($subject, $search)
    {
        return $search === '' ? $subject : array_reverse(explode($search, $subject, 2))[0];
    }

    /**
     * Ambil string DI ANTARA dua kata
     * Str::between('Begin [Content] End', '[', ']') -> 'Content'
     */
    public static function between($subject, $from, $to)
    {
        if ($from === '' || $to === '') return $subject;
        return static::before(static::after($subject, $from), $to);
    }

    /**
     * Masking string (Sensor)
     * Str::mask('johndoe@gmail.com', '*', 3) -> joh**************
     */
    public static function mask($string, $character, $index, $length = null, $encoding = 'UTF-8')
    {
        if ($string === '') return '';
        $segment = mb_substr($string, $index, $length, $encoding);
        if ($segment === '') return $string;
        
        return mb_substr($string, 0, $index, $encoding)
            . str_repeat($character, mb_strlen($segment, $encoding))
            . mb_substr($string, $index + mb_strlen($segment, $encoding), null, $encoding);
    }

    /**
     * Hapus whitespace berlebih (Squish)
     * Str::squish('  halo    dunia  ') -> 'halo dunia'
     */
    public static function squish($value)
    {
        return preg_replace('~(\s|\x{3164})+~u', ' ', trim($value));
    }


    /**
     * Membersihkan string dari karakter berbahaya (XSS / Injection Prevention)
     * Penggunaan: Str::sanitize($input)
     * 
     * @param string $value Teks yang akan dibersihkan
     * @return string Teks yang sudah steril
     */
    public static function sanitize($value)
    {
        if (is_null($value)) return '';
        
        $sanitized = trim((string) $value);
        $sanitized = stripslashes($sanitized);
        $sanitized = htmlspecialchars($sanitized, ENT_QUOTES, 'UTF-8');
        
        return $sanitized;
    }
}