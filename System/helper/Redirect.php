<?php

/**
 * Redirect Handler
 * Mengelola pengalihan halaman berbasis JavaScript dengan dukungan Flash Data.
 */
class Redirect
{
    private $url;
    private $timer;
    private $isHistoryMode = false;

    public function __construct($target, $timer = 1000, $isHistory = false)
    {
        $this->timer = $timer;
        $this->isHistoryMode = $isHistory;
        
        // PAKSA Status Code ke 302 agar Session::manageHybridFlash tidak menghapus notifikasi
        if (!headers_sent()) {
            http_response_code(302);
        }

        if ($this->isHistoryMode) {
            $this->url = $target;
        } else {
            $this->url = $this->resolveUrl($target);
        }
    }

    private function resolveUrl($page)
    {
        $base = rtrim(Url::base(), '/'); 
        if ($page === "") return $base . '/index.php';
        if (strpos($page, $base) === 0) return $page;
        if (preg_match('#^https?://#i', $page)) return $page;
        if (substr($page, 0, 1) === "/") return $base . $page;
        return $base . '/index.php?page=' . $page;
    }

    public static function to($page = "", $timer = 1000)
    {
        return new self($page, $timer, false);
    }

    public static function back($timer = 1000, $step = 1)
    {
        return new self(-$step, $timer, true);
    }


        /**
     * Kirim Flash Data (Support Chaining)
     */
    public function with($key, $value = null)
    {
        if (class_exists('Session')) {
            if (is_array($key)) {
                foreach ($key as $k => $v) {
                    Session::flash($k, $v);
                }
            } else {
                Session::flash($key, $value);
            }
        }
        return $this;
    }


    public function __destruct()
    {
        echo "<script>setTimeout(function() {";
        echo ($this->isHistoryMode) ? "window.history.go({$this->url});" : "window.location.href = '{$this->url}';";
        echo "}, {$this->timer});</script>";
    }


}