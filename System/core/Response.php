<?php

class Response
{
    protected $content;
    protected $original;
    protected $statusCode = 200;
    protected $headers = [];
    protected $cookies = [];
    protected $exception;

    /**
     * Inisialisasi Response
     */
    public function __construct($content = '', $status = 200, $headers = [])
    {
        $this->original = $content;
        $this->setContent($content);
        $this->statusCode = $status;
        $this->headers = $headers;
    }

    /**
     * Membuat instance response baru secara statis (Factory)
     */
    public static function make($content = '', $status = 200, array $headers = [])
    {
        return new static($content, $status, $headers);
    }

    // =========================================================================
    // REDIRECT METHODS (Terminal & Static)
    // =========================================================================

    /**
     * Redirect ke path internal atau URL eksternal secara aman.
     */
    public static function redirect($path, $status = 302)
    {
        // 1. Validasi keamanan via kelas Url
        if (!Url::validatePath($path, true)) {
            $path = '/'; 
        }

        /**
         * 2. Perbaikan Logika Resolving URL:
         * Cek apakah $path diawali dengan http:// atau https://
         */
        $isAlreadyAbsolute = preg_match('#^https?://#i', $path);

        // Jika sudah absolut, gunakan langsung. Jika masih relatif, baru tambahkan base_url
        $url = $isAlreadyAbsolute ? $path : Url::base($path);

        http_response_code($status);
        header("Location: " . $url);
        exit;
    }

    /** Alias untuk redirect */
    public static function to($path, $status = 302)
    {
        return self::redirect($path, $status);
    }

    /** Redirect ke halaman sebelumnya */
    public static function back($fallback = '/')
    {
        return self::redirect(Url::previous($fallback));
    }

    /** Redirect dengan penundaan (Refresh header) */
    public static function refresh($seconds = 3, $path = null)
    {
        $url = $path ? (Url::isInternal($path) ? Url::base($path) : $path) : Url::current();
        header("Refresh: {$seconds}; url={$url}");
        return new static;
    }

    // =========================================================================
    // CONTENT MANAGEMENT
    // =========================================================================
    public function setContent($content)
    {
        $this->original = $content;

        // PERBAIKAN: Tambahkan is_object($content) agar stdClass (hasil query) ikut ter-encode ke JSON
        if ($content instanceof Model || is_array($content) || is_object($content)) {
            $this->content = json_encode($content);
            
            // Cek jika json_encode gagal (misal karena karakter non-UTF8)
            if ($this->content === false) {
                $this->content = json_encode(['error' => 'JSON Encoding Error: ' . json_last_error_msg()]);
            }
            
            $this->header('Content-Type', 'application/json');
        } else {
            $this->content = (string) $content;
        }

        return $this;
    }


    public function getContent() { return $this->content; }
    public function getOriginalContent() { return $this->original; }

    // =========================================================================
    // STATUS, HEADERS & COOKIES (Fluent)
    // =========================================================================

    public function status($code)
    {
        $this->statusCode = $code;
        return $this;
    }

    public function header($name, $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function withHeader($name, $value) { return $this->header($name, $value); }

    public function withCookie($name, $value, $minutes = 60, $path = '/', $domain = null, $secure = false, $httpOnly = true)
    {
        $expiry = time() + ($minutes * 60);
        $this->cookies[$name] = compact('name', 'value', 'expiry', 'path', 'domain', 'secure', 'httpOnly');
        return $this;
    }

    public function withoutCookie($name)
    {
        return $this->withCookie($name, '', -2628000); 
    }

    // =========================================================================
    // EXCEPTION & FORMATTING
    // =========================================================================

    public function withException(Exception $e)
    {
        $this->exception = $e;
        return $this;
    }

    public function toArray()
    {
        return ($this->original instanceof Model) ? $this->original->toArray() : (array)$this->original;
    }

    public function toJson($options = 0)
    {
        return json_encode($this->original, $options);
    }

    /** Shortcut JSON Statis */
    public static function json($data, $status = 200, array $headers = [])
    {
        $instance = new static($data, $status, $headers);
        return $instance->send();
    }

    // =========================================================================
    // FINAL EXECUTION
    // =========================================================================

    /**
     * Mengirimkan semua data ke browser dan menghentikan script.
     */
    public function send()
    {
        if (!headers_sent()) {
            // 1. Set Status Code
            http_response_code($this->statusCode);

            // 2. Send Headers
            foreach ($this->headers as $name => $value) {
                header("$name: $value");
            }

            // 3. Send Cookies
            foreach ($this->cookies as $c) {
                setcookie($c['name'], $c['value'], $c['expiry'], $c['path'], $c['domain'], $c['secure'], $c['httpOnly']);
            }
        }

        // 4. Send Content
        echo $this->content;

        exit;
    }
}