<?php

class Encrypter
{
    /** @var string Kunci enkripsi (harus 32 karakter untuk AES-256) */
    protected static $key;

    /** @var string Algoritma cipher */
    protected static $cipher = 'AES-256-CBC';

    /**
     * Inisialisasi kunci enkripsi.
     * Biasanya dipanggil di awal aplikasi (misal: dari file config/env).
     */
    public static function init($key)
    {
        self::$key = $key;
    }

    /**
     * Enkripsi string atau array menjadi payload terenkripsi.
     * * @param mixed $value Data yang akan dienkripsi.
     * @param bool $serialize Apakah data perlu di-serialize (untuk array/object).
     * @return string
     */
    public static function encrypt($value, $serialize = true)
    {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::$cipher));

        $value = $serialize ? serialize($value) : $value;

        $ciphertext = openssl_encrypt($value, self::$cipher, self::$key, 0, $iv);

        if ($ciphertext === false) {
            throw new Exception("Could not encrypt the data.");
        }

        // Buat MAC (Message Authentication Code) untuk mencegah manipulasi data (integrity check)
        $mac = self::hash($iv = base64_encode($iv), $ciphertext);

        // Bungkus semua dalam JSON payload lalu di-base64
        $json = json_encode(compact('iv', 'ciphertext', 'mac'));

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Could not encrypt the data (JSON Error).");
        }

        return base64_encode($json);
    }

    /**
     * Dekripsi payload kembali ke data asli.
     * * @param string $payload Payload hasil enkripsi.
     * @param bool $unserialize Apakah data perlu di-unserialize.
     * @return mixed
     */
    public static function decrypt($payload, $unserialize = true)
    {
        $payload = json_decode(base64_decode($payload), true);

        // 1. Validasi struktur payload
        if (!self::validPayload($payload)) {
            throw new Exception("The payload is invalid.");
        }

        // 2. Verifikasi MAC (Integrity Check)
        if (!hash_equals($payload['mac'], self::hash($payload['iv'], $payload['ciphertext']))) {
            throw new Exception("The MAC is invalid (Data might be tampered).");
        }

        $iv = base64_decode($payload['iv']);

        // 3. Eksekusi Dekripsi
        $decrypted = openssl_decrypt($payload['ciphertext'], self::$cipher, self::$key, 0, $iv);

        if ($decrypted === false) {
            throw new Exception("Could not decrypt the data.");
        }

        return $unserialize ? unserialize($decrypted) : $decrypted;
    }

    /**
     * Membuat signature HMAC untuk integritas data.
     */
    protected static function hash($iv, $value)
    {
        return hash_hmac('sha256', $iv . $value, self::$key);
    }

    /**
     * Validasi apakah payload memiliki komponen yang lengkap.
     */
    protected static function validPayload($payload)
    {
        return is_array($payload) && isset($payload['iv'], $payload['ciphertext'], $payload['mac']) &&
               strlen(base64_decode($payload['iv'], true)) === openssl_cipher_iv_length(self::$cipher);
    }
}