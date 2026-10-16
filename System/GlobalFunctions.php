<?php
use Session;
use Router;
use Redirect;

// 1. Dump and Die (Wajib buat debugging!)
if (!function_exists('dd')) {
    function dd($data) {
        echo '<div style="background: #18171b; color: #fff; padding: 20px; font-family: monospace; z-index:9999; position:relative;">';
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        echo '</div>';
        die();
    }
}

if (!function_exists('show_error')) {
    /**
     * Mengembalikan class CSS error (Bootstrap/Tailwind/Custom) jika field memiliki error validation.
     *
     * @param string $field Nama field input (contoh: 'name', 'email')
     * @param string $preset Pilihan preset CSS: 'bootstrap', 'tailwind', atau 'custom'
     * @param string $custom_class Class khusus jika memilih preset 'custom'
     * @return string Class CSS yang siap di-echo di atribut class HTML
     */
    function show_error(string $field = "", string $preset = "bootstrap", string $custom_class = "")
    {
        // 1. Ambil Data Error dari Session (Sama persis seperti form_error)
        $errors = Session::get('validation_errors') ?? Session::get('notifikasi') ?? [];

        if (!is_array($errors)) {
            $errors = [];
        }

        // 2. Jika field memiliki error
        if (isset($errors[$field]) && !empty($errors[$field])) {
            
            switch (strtolower($preset)) {
                case 'bootstrap':
                case 'bs':
                    return 'is-invalid border-danger';

                case 'tailwind':
                case 'tw':
                    return 'border-red-500 text-red-600 focus:ring-red-500 focus:border-red-500';

                case 'custom':
                default:
                    return !empty($custom_class) ? $custom_class : 'is-invalid';
            }
        }

        return "";
    }
}

if (!function_exists('form_error')) {
    function form_error(string $field = "", string $custom_message = '', $open_tag = "<small class='text-danger' style='color:red;'>", $closed_tag = '</small>')
    {
        // 1. Ambil Data Error dari Session menggunakan Class Helper (Lebih Aman)
        // Prioritaskan ambil dari 'validation_errors' (Validator baru), 
        // kalau gak ada baru cek 'notifikasi' (Legacy code).
        $errors = Session::get('validation_errors') ?? Session::get('notifikasi') ?? [];

        // 2. Pastikan $errors adalah array (jaga-jaga kalau ada yg set string)
        if (!is_array($errors)) {
            $errors = [];
        }

        // 3. Cek apakah field yang diminta ada di daftar error
        if (isset($errors[$field]) && !empty($errors[$field])) {
            
            // Tentukan pesan: Pakai custom jika ada, kalau tidak pakai dari session
            $message = !empty($custom_message) ? $custom_message : $errors[$field];

            return $open_tag . $message . $closed_tag;
        }

        // 4. Return string kosong jika tidak ada error (biar HTML bersih)
        // Tidak perlu me-return <small class='invalid-feedback'></small> kosong, 
        // karena itu bisa merusak layout CSS (margin/padding).
        return "";
    }
}

if (!function_exists('router')) {
    function router($name, $params = [])
    {
        return Router::url($name, $params);
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field() {
        return '<input type="hidden" name="_token" value="'. Router::csrfToken() .'">';
    }
}

if (!function_exists('redirect')) {
    function redirect($page = "", $timer = 1000)
    {
        return Redirect::to($page, $timer);
    }
}

if (!function_exists('redirect_back')) {
    function redirect_back($timer = 1000, $step = 1)
    {
        return Redirect::back($timer, $step);
    }
}

if (!function_exists('redirect_referrer')) {
    function redirect_referrer($timer = 1000)
    {
        return Redirect::referrer($timer);
    }
}