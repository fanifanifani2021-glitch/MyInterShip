<?php

class Request implements JsonSerializable, ArrayAccess
{
    protected static $instance;
    protected $server;
    protected $inputs;

    /**
     * Constructor Public: Memungkinkan $request = new Request;
     */
    public function __construct()
    {
        $this->server = $_SERVER;
        $this->inputs = array_merge($_GET, $_POST, $this->getJsonInput());
        
        // Daftarkan diri ke static instance jika dipanggil lewat 'new'
        if (self::$instance === null) {
            self::$instance = $this;
        }
    }

    /**
     * Singleton Instance
     */
    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * GERBANG STATIS: Menangani Request::all(), Request::method(), dll.
     */
    public static function __callStatic($method, $args)
    {
        $instance = self::instance();
        if (method_exists($instance, $method)) {
            return call_user_func_array([$instance, $method], $args);
        }
        throw new BadMethodCallException("Static method $method tidak ditemukan.");
    }

    /**
     * GERBANG OBJEK: Menangani $request->all()
     */
    public function __call($method, $args)
    {
        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], $args);
        }
        throw new BadMethodCallException("Instance method $method tidak ditemukan.");
    }

    /**
     * GERBANG PROPERTI: Menangani $request->nim
     */
    public function __get($key)
    {
        return $this->input($key);
    }

    /**
     * JSON Input Handler
     */
    protected function getJsonInput()
    {
        if ($this->expectsJson()) {
            $content = file_get_contents('php://input');
            return json_decode($content, true) ?? [];
        }
        return [];
    }

    // =========================================================================
    // IMPLEMENTASI ARRAYACCESS & JSON
    // =========================================================================

    public function offsetExists($offset): bool { return isset($this->inputs[$offset]); }
    #[ReturnTypeWillChange]
    public function offsetGet($offset) { return $this->inputs[$offset] ?? null; }
    public function offsetSet($offset, $value): void { $this->inputs[$offset] = $value; }
    public function offsetUnset($offset): void { unset($this->inputs[$offset]); }
    #[ReturnTypeWillChange]
    public function jsonSerialize() { return $this->all(); }

    // =========================================================================
    // LOGIC METHODS (Dibuat PROTECTED agar masuk lewat gerbang Magic)
    // =========================================================================

    protected function method()
    {
        $method = strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
        if ($method === 'POST' && isset($_POST['_method'])) {
            $override = strtoupper($_POST['_method']);
            if (in_array($override, ['PUT', 'DELETE', 'PATCH'])) return $override;
        }
        return $method;
    }

    protected function isMethod($method) { return $this->method() === strtoupper($method); }
    
    protected function header($key, $default = null)
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        return $this->server[$key] ?? $default;
    }

    protected function hasHeader($key)
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        return isset($this->server[$key]);
    }

    protected function expectsJson()
    {
        $contentType = $this->server['CONTENT_TYPE'] ?? $this->server['HTTP_CONTENT_TYPE'] ?? '';
        return (strpos($this->header('Accept', ''), 'application/json') !== false) || 
               stripos($contentType, 'application/json') !== false;
    }

    // =========================================================================
    // LOGIC METHODS (Dibuat PROTECTED agar masuk lewat gerbang Magic)
    // =========================================================================

    protected function all() { return $this->inputs; }
    
    protected function input($key, $default = null) { return $this->inputs[$key] ?? $default; }

    /**
     * Alias untuk input()
     */
    protected function get($key, $default = null)
    {
        return $this->input($key, $default);
    }

    /**
     * Khusus mengambil data dari Query String ($_GET) saja
     */
    protected function query($key = null, $default = null)
    {
        if (is_null($key)) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }

    /**
     * Khusus mengambil data dari Request Body ($_POST) saja
     */
    protected function post($key = null, $default = null)
    {
        if (is_null($key)) {
            return $_POST;
        }
        return $_POST[$key] ?? $default;
    }

    /**
     * Mengambil hanya beberapa input saja
     * Contoh: $request->only(['username', 'password'])
     */
    protected function only($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();
        $results = [];

        foreach ($keys as $key) {
            $results[$key] = $this->input($key);
        }

        return $results;
    }

    /**
     * Mengambil semua input kecuali beberapa key
     * Contoh: $request->except(['_token', 'submit'])
     */
    protected function except($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();
        $results = $this->all();

        foreach ($keys as $key) {
            unset($results[$key]);
        }

        return $results;
    }
    
    
    protected function has($key) { return isset($this->inputs[$key]); }

    /**
     * [SOLUSI ERROR] Method untuk mengambil input sebagai Integer
     */
    protected function integer($key, $default = 0)
    {
        $value = $this->input($key, $default);
        return (int) $value;
    }

    /**
     * Method untuk mengambil input sebagai String yang sudah dibersihkan
     */
    protected function string($key, $default = '')
    {
        $value = (string) $this->input($key, $default);
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Method untuk mengambil input sebagai Boolean
     */
    protected function boolean($key, $default = false)
    {
        $value = $this->input($key, $default);
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
    protected function file($key) { return $_FILES[$key] ?? null; }
    
    protected function hasFile($key) { return isset($_FILES[$key]) && $_FILES[$key]['error'] !== UPLOAD_ERR_NO_FILE; }


    // =========================================================================
    // 2. INFORMASI HOST, IP & KONEKSI
    // =========================================================================

    /**
     * Mendapatkan IP Address tunggal dari pengunjung.
     */
    protected function ip()
    {
        return $this->server['REMOTE_ADDR'] ?? '';
    }

    /**
     * Mendapatkan daftar IP (berguna jika user menggunakan Proxy/Load Balancer).
     */
    protected function ips()
    {
        $ips = [];
        // Cek jika ada header X-Forwarded-For (biasanya dari Proxy/Cloudflare)
        if ($this->hasHeader('X-Forwarded-For')) {
            $ips = explode(',', $this->header('X-Forwarded-For'));
            $ips = array_map('trim', $ips);
        }
        
        $ips[] = $this->ip();
        return array_unique($ips);
    }


    /**
     * Check if the current request is an AJAX request
     * * @return bool
     */
    public static function isAjax()
    {
        // 1. Cek header standar dari library JS (jQuery, Axios, dll)
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            return true;
        }

        // 2. Cek Accept Header (opsional, jika request mengharapkan JSON)
        if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
            return true;
        }

        return false;
    }
}