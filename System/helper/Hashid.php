<?php

class Hashid
{
    /** @var string Alphabet acak sebagai kunci pengganti (salt) */
    protected static $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    /** @var int Minimum panjang string hashid yang dihasilkan */
    protected static $minLength = 4;

    /** @var string Salt rahasia untuk mengacak urutan karakter */
    protected static $salt = 'myinternship-secret-salt-2026';

    /**
     * Inisialisasi Kunci Salt Kustom dari Config/Env (Opsional)
     * 
     * @param string $salt
     * @param int $minLength
     */
    public static function init($salt = null, $minLength = 4)
    {
        if ($salt !== null) {
            self::$salt = $salt;
        }
        self::$minLength = (int) $minLength;
    }

    /**
     * Encode Angka ID menjadi String Hashid Pendek
     * Contoh: Hashid::encode(56) -> "aX8k"
     * 
     * @param int $id ID angka positif
     * @return string
     */
    public static function encode($id)
    {
        if (!is_numeric($id) || $id < 0) {
            return '';
        }

        $id = (int) $id;
        $alphabet = self::getShuffledAlphabet();
        $base = strlen($alphabet);
        $encoded = '';

        // Konversi Angka ke Base Alphabet
        do {
            $remainder = $id % $base;
            $encoded = $alphabet[$remainder] . $encoded;
            $id = (int) ($id / $base);
        } while ($id > 0);

        // Tambahkan Padding jika kurang dari minLength
        while (strlen($encoded) < self::$minLength) {
            $padChar = $alphabet[ord($encoded[0]) % $base];
            $encoded = $padChar . $encoded;
        }

        return $encoded;
    }

    /**
     * Decode String Hashid kembali menjadi Angka ID Asli
     * Contoh: Hashid::decode("aX8k") -> 56
     * 
     * @param string $hash
     * @return int|null Mengembalikan ID angka, atau NULL jika hash tidak valid
     */
    public static function decode($hash)
    {
        if (empty($hash) || !is_string($hash)) {
            return null;
        }

        $alphabet = self::getShuffledAlphabet();
        $base = strlen($alphabet);
        $id = 0;

        // Bersihkan padding awal jika ada
        while (strlen($hash) > 1 && !self::isValidFirstChar($hash, $alphabet, $base)) {
            $hash = substr($hash, 1);
        }

        // Konversi Base Alphabet kembali ke Integer
        for ($i = 0; $i < strlen($hash); $i++) {
            $char = $hash[$i];
            $position = strpos($alphabet, $char);

            if ($position === false) {
                return null; // Karakter tidak valid
            }

            $id = ($id * $base) + $position;
        }

        return $id > 0 ? $id : null;
    }

    /**
     * Helper: Pengacak Alphabet berbasis Salt (Agar unik tiap project)
     */
    protected static function getShuffledAlphabet()
    {
        $alphabet = self::$alphabet;
        $salt = self::$salt;

        if (empty($salt)) {
            return $alphabet;
        }

        // Acak urutan alphabet berdasarkan hash HMAC dari salt
        $hash = md5($salt);
        $length = strlen($alphabet);
        $shuffled = '';

        $chars = str_split($alphabet);
        for ($i = 0; $i < $length; $i++) {
            $index = hexdec($hash[$i % strlen($hash)]) % count($chars);
            $shuffled .= $chars[$index];
            array_splice($chars, $index, 1);
        }

        return $shuffled;
    }

    /**
     * Helper: Cek integritas padding awal
     */
    protected static function isValidFirstChar($hash, $alphabet, $base)
    {
        $firstChar = $hash[0];
        $secondChar = $hash[1] ?? '';
        
        if (empty($secondChar)) return true;

        $expectedPad = $alphabet[ord($secondChar) % $base];
        return $firstChar !== $expectedPad;
    }
}