<?php

/**
 * Notification System
 * Engine notifikasi modular berbasis CDN.
 */
class Notification
{
    private static $libraries = [
        'jquery'    => ['js_head' => ['https://code.jquery.com/jquery-3.7.1.min.js']],
        'swal2'     => ['css' => ['https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css'], 'js_footer' => ['https://cdn.jsdelivr.net/npm/sweetalert2@11']],
        'izitoast'  => ['css' => ['https://cdn.jsdelivr.net/npm/izitoast@1.4.0/dist/css/iziToast.min.css'], 'js_footer' => ['https://cdn.jsdelivr.net/npm/izitoast@1.4.0/dist/js/iziToast.min.js']],
        'snackbar'  => ['css' => ['https://cdn.jsdelivr.net/npm/node-snackbar@latest/dist/snackbar.min.css'], 'js_footer' => ['https://cdn.jsdelivr.net/npm/node-snackbar@latest/dist/snackbar.min.js']]
    ];

    /**
     * Inisialisasi awal sistem notifikasi.
     * @return void
     */
    public static function init()
    {
        self::use('jquery');
        self::addStyle(".swal2-container, .iziToast-wrapper, .snackbar-container { z-index: 999999 !important; }");
    }

    /**
     * Mendaftarkan library CDN yang akan dimuat.
     * @param string $libName
     * @return void
     */
    public static function use($libName)
    {
        $active = Session::get('active_notif_libs', []);
        if (!in_array($libName, $active)) {
            $active[] = $libName;
            Session::set('active_notif_libs', $active);
        }
    }

    /**
     * Menambahkan CSS custom ke halaman.
     * @param string $css
     */
    public static function addStyle($css)
    {
        $current = Session::get('notif_custom_css', []);
        $current[] = $css;
        Session::set('notif_custom_css', $current);
    }

    /**
     * Menambahkan Script custom ke halaman.
     * @param string $js
     */
    public static function addScript($js)
    {
        $current = Session::get('notif_custom_js', []);
        $current[] = $js;
        Session::set('notif_custom_js', $current);
    }

    /**
     * Render aset di bagian <head>.
     */
    public static function renderHead()
    {
        $libs = Session::get('active_notif_libs', []);
        $css = Session::get('notif_custom_css', []);
        if (empty($libs) && empty($css)) return;

        foreach ($libs as $l) {
            if (isset(self::$libraries[$l]['css'])) {
                foreach (self::$libraries[$l]['css'] as $file) echo "<link rel='stylesheet' href='$file'>\n";
            }
            if (isset(self::$libraries[$l]['js_head'])) {
                foreach (self::$libraries[$l]['js_head'] as $file) echo "<script src='$file'></script>\n";
            }
        }
        if (!empty($css)) echo "<style>" . implode("\n", $css) . "</style>";
    }

    /**
     * Render library dan eksekusi trigger di footer.
     */
    public static function renderFooter()
    {
        $libs = Session::get('active_notif_libs', []);
        $queue = Session::get('notification_queue', []);
        $js = Session::get('notif_custom_js', []);
        if (empty($libs) && empty($queue) && empty($js)) return;

        foreach ($libs as $l) {
            if (isset(self::$libraries[$l]['js_footer'])) {
                foreach (self::$libraries[$l]['js_footer'] as $file) echo "<script src='$file'></script>\n";
            }
        }

        echo "<script>$(function() {";
        foreach ($queue as $n) {
            $args = trim(json_encode($n['params']), '[]');
            echo "if(typeof {$n['method']} === 'function') { {$n['method']}($args); }";
        }
        if (!empty($js)) echo implode("\n", $js);
        echo "});</script>";
    }

    /**
     * Tampilkan SweetAlert2.
     * @param string $title
     * @param string $msg
     * @param string $type success|error|warning|info
     */
    public static function swal2($title, $msg, $type = 'success')
    {
        self::use('swal2');
        $q = Session::get('notification_queue', []);
        $q[] = ['method' => 'Swal.fire', 'params' => [['title' => $title, 'text' => $msg, 'icon' => $type]]];
        Session::set('notification_queue', $q);
    }


    /**
     * Tampilkan IziToast.
     * @param string $title
     * @param string $msg
     * @param string $type success|error|warning|info
     */
    public static function izi($title, $msg, $type = 'success')
    {
        self::use('izitoast');
        $q = Session::get('notification_queue', []);
        $q[] = ['method' => 'iziToast.' . $type, 'params' => [['title' => $title, 'message' => $msg]]];
        Session::set('notification_queue', $q);
    }

    /**
     * Tampilkan Snackbar.
     * @param string $msg
     * @param int $duration Durasi dalam milidetik
     */
    public static function snackbar($msg, $pos = "bottom-center", $duration = 3000)
    {
        self::use('snackbar');
        $q = Session::get('notification_queue', []);
        $q[] = ['method' => 'Snackbar.show', 'params' => [['text' => $msg, 'position' => $pos, 'duration' => $duration]]];
        Session::set('notification_queue', $q);
    }
}   