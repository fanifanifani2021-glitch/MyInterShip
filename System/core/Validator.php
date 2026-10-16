<?php

class Validator
{
    private $data = [];
    private $files = [];
    private $rules = [];
    private $errors = [];
    private $customMessages = [];

    /**
     * @param array $data Data input (biasanya $_POST)
     * @param array $rules Array aturan validasi
     * @param array $files Data file (biasanya $_FILES)
     * @param array $customMessages Pesan error kustom (Opsional)
     */
    public function __construct(array $data, array $rules, array $files = [], array $customMessages = [])
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->files = $files;
        $this->customMessages = $customMessages;
    }

    /**
     * Factory Method (Cara instan panggil class)
     */
    public static function make(array $data, array $rules, array $files = [], array $customMessages = [])
    {
        return new self($data, $rules, $files, $customMessages);
    }

    /**
     * Jalankan Validasi
     */
    public function validate()
    {
        $this->errors = []; // Reset error

        foreach ($this->rules as $field => $ruleset) {
            
            // Ruleset bisa string ("required|min:3") atau array
            $rules = is_string($ruleset) ? explode('|', $ruleset) : $ruleset;

            foreach ($rules as $rule) {
                // Parsing parameter. Contoh: "min_length:5" -> $ruleName="min_length", $params=["5"]
                $params = [];
                if (strpos($rule, ':') !== false) {
                    [$ruleName, $paramStr] = explode(':', $rule, 2);
                    $params = explode(',', $paramStr); // Support multiple params comma separated
                } else {
                    $ruleName = $rule;
                }

                // Skip jika rule kosong
                if (empty($ruleName)) continue;

                // Konversi nama rule ke nama method (snake_case -> camelCase)
                // Contoh: "min_length" -> "validateMinLength"
                $methodName = 'validate' . str_replace('_', '', ucwords($ruleName, '_'));

                if (method_exists($this, $methodName)) {
                    // Tentukan value yang mau divalidasi (File atau Input Biasa)
                    if ($this->isFileRule($ruleName)) {
                        $value = $this->files[$field] ?? null;
                    } else {
                        $value = $this->data[$field] ?? null;
                    }

                    // Panggil method validasi
                    // Jika return FALSE, hentikan pengecekan rule lain untuk field ini
                    if (!$this->$methodName($field, $value, ...$params)) {
                         // break; // Uncomment jika ingin stop di error pertama per field
                    }
                }
            }
        }

        return empty($this->errors);
    }

    // --- Helpers Status ---

    public function fails()
    {
        return !$this->validate();
    }

    public function errors()
    {
        return $this->errors;
    }


    /**
     * AMBIL ERROR PERTAMA (Untuk Kodingan Baru)
     * Contoh baru: Notification::swal2('Error', $validator->firstError(), 'error')
     */
    public function firstError(): string 
    {
        if (count($this->errors) > 0) {
            $first = reset($this->errors);
            // Jika error dalam bentuk array (multi-error per field), ambil yang paling atas
            return is_array($first) ? (string)reset($first) : (string)$first;
        }
        return '';
    }

    /**
     * Cek apakah field tertentu memiliki error
     */
    public function hasError($field): bool
    {
        return isset($this->errors[$field]);
    }

    private function addError($field, $rule, $defaultMessage)
    {
        // Cek apakah ada Custom Message dari user
        // Format custom message array: ['username.required' => 'Username wajib oi!']
        $customKey = "$field.$rule";
        
        if (isset($this->customMessages[$customKey])) {
            $message = $this->customMessages[$customKey];
        } else {
            $message = $defaultMessage;
        }

        // Simpan error (hanya satu error per field biar rapi, hapus 'if' jika mau semua error)
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = $message;
        }
    }

    private function isFileRule($rule)
    {
        return in_array($rule, ['max_size', 'min_size', 'allowed_types', 'required_file']);
    }

    // Helper Size Converter (Dari kodemu)
    private function sizeConverter($bytes)
    {
        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }

    // Helper Convert String Size (1MB -> bytes)
    private function parseSize($sizeStr)
    {
        $size = trim($sizeStr);
        $last = strtolower($size[strlen($size)-1]);
        $val = (int)$size;
        switch($last) {
            case 'g': $val *= 1024 * 1024 * 1024; break;
            case 'm': $val *= 1024 * 1024; break;
            case 'k': $val *= 1024; break;
        }
        return $val;
    }


    // =========================================================================
    // IMPLEMENTASI RULE (Porting dari kodemu)
    // =========================================================================

    /** Rule: required */
    protected function validateRequired($field, $value)
    {
        if (is_null($value) || trim($value) === '') {
            $this->addError($field, 'required', "Field $field tidak boleh kosong!");
            return false;
        }
        return true;
    }

    /** Rule: not_required (Unik dari kodemu) */
    protected function validateNotRequired($field, $value)
    {
        if (!empty($value)) {
            $this->addError($field, 'not_required', "Field $field tidak boleh diisi!");
            return false;
        }
        return true;
    }

    /** Rule: min_length:5 */
    protected function validateMinLength($field, $value, $min)
    {
        if (!empty($value) && strlen($value) < $min) {
            $this->addError($field, 'min_length', "Minimal panjang karakter adalah $min karakter!");
            return false;
        }
        return true;
    }

    /** Rule: max_length:10 */
    protected function validateMaxLength($field, $value, $max)
    {
        if (!empty($value) && strlen($value) > $max) {
            $this->addError($field, 'max_length', "Maksimal panjang karakter adalah $max karakter!");
            return false;
        }
        return true;
    }

    /** Rule: exact_length:5 */
    protected function validateExactLength($field, $value, $length)
    {
        if (!empty($value) && strlen($value) != $length) {
            $this->addError($field, 'exact_length', "Panjang karakter harus $length karakter!");
            return false;
        }
        return true;
    }

    /** Rule: matches:password */
    protected function validateMatches($field, $value, $targetField)
    {
        $targetValue = $this->data[$targetField] ?? null;
        if (!empty($value) && $value !== $targetValue) {
            $this->addError($field, 'matches', "Field $field harus sama dengan $targetField!");
            return false;
        }
        return true;
    }

    // --- Validasi Angka ---

    /** Rule: numeric */
    protected function validateNumeric($field, $value)
    {
        if (!empty($value) && !is_numeric($value)) {
            $this->addError($field, 'numeric', "Nilai hanya boleh terdiri dari Angka!");
            return false;
        }
        return true;
    }

    /** Rule: integer */
    protected function validateInteger($field, $value)
    {
        if (!empty($value) && filter_var($value, FILTER_VALIDATE_INT) === false) {
            $this->addError($field, 'integer', "Nilai harus berupa bilangan bulat!");
            return false;
        }
        return true;
    }

    /** Rule: greater_than:10 */
    protected function validateGreaterThan($field, $value, $min)
    {
        if (!empty($value) && (!is_numeric($value) || $value <= $min)) {
            $this->addError($field, 'greater_than', "Nilai harus lebih besar dari $min!");
            return false;
        }
        return true;
    }

    /** Rule: less_than:100 */
    protected function validateLessThan($field, $value, $max)
    {
        if (!empty($value) && (!is_numeric($value) || $value >= $max)) {
            $this->addError($field, 'less_than', "Nilai harus lebih kecil dari $max!");
            return false;
        }
        return true;
    }

    // --- Validasi Format ---

    /** Rule: valid_email */
    protected function validateValidEmail($field, $value)
    {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, 'valid_email', "Masukkan alamat e-mail yang valid!");
            return false;
        }
        return true;
    }

    /** Rule: valid_url */
    protected function validateValidUrl($field, $value)
    {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
            $this->addError($field, 'valid_url', "Masukkan URL yang valid!");
            return false;
        }
        return true;
    }
    
    /** Rule: alpha */
    protected function validateAlpha($field, $value)
    {
        if (!empty($value) && !ctype_alpha($value)) {
             $this->addError($field, 'alpha', "Nilai hanya boleh terdiri dari Huruf!");
             return false;
        }
        return true;
    }

    /** Rule: alpha_numeric */
    protected function validateAlphaNumeric($field, $value)
    {
        if (!empty($value) && !ctype_alnum($value)) {
             $this->addError($field, 'alpha_numeric', "Nilai hanya boleh Huruf dan Angka!");
             return false;
        }
        return true;
    }

    /** Rule: allowed_values:L,P */
    protected function validateAllowedValues($field, $value, ...$allowed)
    {
        // Handle logic explode yang mungkin terpisah koma di parameter
        // Jika param dikirim "L,P", di sini mungkin jadi satu string atau array tergantung parser.
        // Kita ratakan array-nya.
        if (!empty($value) && !in_array($value, $allowed)) {
             $list = implode(', ', $allowed);
             $this->addError($field, 'allowed_values', "Nilai yang diterima adalah: $list");
             return false;
        }
        return true;
    }

    // --- Validasi Database (Menggunakan $_SERVER['PDO']) ---

    /** Rule: is_unique:users.username */
    protected function validateIsUnique($field, $value, $tableInfo)
    {
        // $tableInfo format: "users" atau "users.column"
        $parts = explode('.', $tableInfo);
        $table = $parts[0];
        $column = $parts[1] ?? $field;

        if (!empty($value)) {
            try {
                // Asumsi koneksi ada di $_SERVER['PDO'] sesuai kodemu sebelumnya
                if (!isset($_SERVER['PDO'])) {
                    throw new Exception("Koneksi PDO tidak ditemukan di \$_SERVER['PDO']");
                }

                $stmt = $_SERVER['PDO']->prepare("SELECT COUNT(*) FROM $table WHERE $column = :val");
                $stmt->bindValue(':val', $value);
                $stmt->execute();
                
                if ($stmt->fetchColumn() > 0) {
                    $this->addError($field, 'is_unique', "Nilai sudah digunakan, gunakan nilai lain!");
                    return false;
                }
            } catch (Exception $e) {
                // Opsi: Log error database
                return false;
            }
        }
        return true;
    }

    /** Rule: unique_except:current_email|users.email */
    protected function validateUniqueExcept($field, $value, $paramStr)
    {
        // Format paramStr: "budi@mail.com|users.email"
        // Kita perlu parse manual karena ada pipa '|' di dalam parameter
        [$exceptValue, $tableInfo] = explode('|', $paramStr);
        
        $parts = explode('.', $tableInfo);
        $table = $parts[0];
        $column = $parts[1] ?? $field;

        if (!empty($value) && $value != $exceptValue) {
             try {
                $stmt = $_SERVER['PDO']->prepare("SELECT COUNT(*) FROM $table WHERE $column = :val");
                $stmt->bindValue(':val', $value);
                $stmt->execute();
                
                if ($stmt->fetchColumn() > 0) {
                    $this->addError($field, 'unique_except', "Nilai sudah digunakan!");
                    return false;
                }
            } catch (Exception $e) { return false; }
        }
        return true;
    }

    // --- Validasi File (Logic dari kodemu) ---

    /** Rule: required (Untuk File) - Perlu nama beda atau logic detection di validateRequired */
    // Di class ini, kita pakai nama khusus 'required_file' jika ingin eksplisit,
    // atau pakai logic di validateRequired untuk cek array $_FILES.
    // Untuk kemudahan, kita buat khusus:
    protected function validateRequiredFile($field, $file)
    {
        if (!isset($file['error']) || $file['error'] == 4) { // Error 4 = No File
            $this->addError($field, 'required', "Wajib upload file!");
            return false;
        }
        return true;
    }

    /** Rule: max_size:2MB */
    protected function validateMaxSize($field, $file, $maxSizeStr)
    {
        if (isset($file['error']) && $file['error'] === 0) {
            $maxBytes = $this->parseSize($maxSizeStr); // Convert "2MB" to bytes
            
            if ($file['size'] > $maxBytes) {
                $readable = $this->sizeConverter($maxBytes);
                $this->addError($field, 'max_size', "Maksimal ukuran file $readable!");
                return false;
            }
        }
        return true;
    }

    /** * Rule: allowed_types:jpg,png,pdf
     * Memvalidasi Ekstensi DAN Mime Type (Double Check)
     */
    protected function validateAllowedTypes($field, $file, ...$types)
    {
        // 1. Cek apakah file benar-benar terupload
        if (isset($file['tmp_name']) && is_uploaded_file($file['tmp_name'])) {
            
            // Ambil Ekstensi File
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            // Ambil Real MIME Type dari file fisik (Akurat)
            $realMime = mime_content_type($file['tmp_name']);
            if (!$realMime && function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $realMime = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);
            }

            // A. VALIDASI EKSTENSI
            // Cek apakah ekstensi ada di daftar yang diperbolehkan (parameter)
            if (!in_array($ext, $types)) {
                $list = implode(', ', $types);
                $this->addError($field, 'allowed_types', "Ekstensi file harus: $list");
                return false;
            }

            // B. VALIDASI MIME TYPE (Security Check)
            // Ambil daftar mime resmi untuk ekstensi tersebut dari Class MimeTypes
            $allowedMimes = MimeTypes::get($ext);

            // Jika ekstensi tidak dikenal di database kita, kita skip cek mime (atau bisa return false kalau mau strict)
            if ($allowedMimes) {
                // Normalisasi: Pastikan allowedMimes jadi array (karena di database ada yg string ada yg array)
                $allowedMimes = is_array($allowedMimes) ? $allowedMimes : [$allowedMimes];

                // Cek apakah Real Mime ada di daftar Allowed Mimes untuk ekstensi itu
                if (!in_array($realMime, $allowedMimes)) {
                    $this->addError($field, 'allowed_types', "File tidak valid! Ekstensi .$ext tidak cocok dengan isi file ($realMime).");
                    return false;
                }
            }
        }
        return true;
    }
}