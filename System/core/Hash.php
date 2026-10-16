<?php

class Hash
{
    /** @var string Algoritma default (Bcrypt) */
    protected static $algorithm = PASSWORD_BCRYPT;

    /** @var array Opsi default untuk hashing */
    protected static $options = [
        'cost' => 10,
    ];

    /**
     * Membuat hash dari sebuah string.
     * * @param string $value Data yang akan di-hash (password).
     * @param array $options Opsi tambahan (seperti cost).
     * @return string
     */
    public static function make($value, array $options = [])
    {
        $hash = password_hash($value, self::$algorithm, array_merge(self::$options, $options));

        if ($hash === false) {
            throw new Exception("Bcrypt hashing is not supported or an error occurred.");
        }

        return $hash;
    }

    /**
     * Memverifikasi apakah string plain sesuai dengan hash yang ada.
     * * @param string $value String input (password dari form).
     * @param string $hashedValue String hash dari database.
     * @return bool
     */
    public static function check($value, $hashedValue)
    {
        if (strlen($hashedValue) === 0) {
            return false;
        }

        return password_verify($value, $hashedValue);
    }

    /**
     * Mengecek apakah hash perlu dibuat ulang (misal: jika parameter cost berubah).
     * * @param string $hashedValue
     * @param array $options
     * @return bool
     */
    public static function needsRehash($hashedValue, array $options = [])
    {
        return password_needs_rehash($hashedValue, self::$algorithm, array_merge(self::$options, $options));
    }

    /**
     * Mengambil informasi tentang hash yang diberikan.
     * * @param string $hashedValue
     * @return array
     */
    public static function info($hashedValue)
    {
        return password_get_info($hashedValue);
    }
}