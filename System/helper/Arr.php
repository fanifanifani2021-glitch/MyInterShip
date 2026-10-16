<?php

class Arr
{
    // =========================================================================
    // 1. ACCESS & RETRIEVAL (GETTER)
    // =========================================================================

    /**
     * Mengambil value dari array menggunakan "dot notation"
     * Usage: Arr::get($data, 'user.profile.name', 'Guest')
     */
    public static function get($array, $key, $default = null)
    {
        if (!static::accessible($array)) {
            return $default;
        }

        if (is_null($key)) {
            return $array;
        }

        if (static::exists($array, $key)) {
            return $array[$key];
        }

        if (strpos($key, '.') === false) {
            return $array[$key] ?? $default;
        }

        foreach (explode('.', $key) as $segment) {
            if (static::accessible($array) && static::exists($array, $segment)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }

        return $array;
    }

    /**
     * Cek apakah key ada (Support Dot Notation)
     * Usage: Arr::has($data, 'user.name')
     */
    public static function has($array, $key)
    {
        if (!$array) return false;

        $keys = (array) $key;

        if (!$array || $keys === []) {
            return false;
        }

        foreach ($keys as $key) {
            $subKeyArray = $array;

            if (static::exists($array, $key)) {
                continue;
            }

            foreach (explode('.', $key) as $segment) {
                if (static::accessible($subKeyArray) && static::exists($subKeyArray, $segment)) {
                    $subKeyArray = $subKeyArray[$segment];
                } else {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Mengambil kolom tertentu (Pluck)
     */
    public static function pluck($array, $value, $key = null)
    {
        $results = [];
        foreach ($array as $item) {
            $itemValue = is_object($item) ? ($item->{$value} ?? null) : ($item[$value] ?? null);

            if (is_null($key)) {
                $results[] = $itemValue;
            } else {
                $itemKey = is_object($item) ? ($item->{$key} ?? null) : ($item[$key] ?? null);
                $results[$itemKey] = $itemValue;
            }
        }
        return $results;
    }

    /**
     * Ambil elemen pertama
     */
    public static function first($array, callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            if (empty($array)) return $default;
            foreach ($array as $item) return $item;
        }

        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return $default;
    }

    /**
     * Ambil elemen terakhir
     */
    public static function last($array, callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            return empty($array) ? $default : end($array);
        }

        return static::first(array_reverse($array, true), $callback, $default);
    }

    // =========================================================================
    // 2. MANIPULATION (SETTER & MODIFY)
    // =========================================================================

    /**
     * Set value menggunakan Dot Notation
     * Usage: Arr::set($data, 'db.host', 'localhost');
     */
    public static function set(&$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            // Jika key belum ada atau bukan array, buat array baru
            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }

    /**
     * Tambahkan element jika key belum ada
     */
    public static function add($array, $key, $value)
    {
        if (is_null($static = static::get($array, $key))) {
            static::set($array, $key, $value);
        }
        return $array;
    }

    /**
     * Hapus value menggunakan Dot Notation
     * Usage: Arr::forget($data, 'user.password');
     */
    public static function forget(&$array, $keys)
    {
        $original = &$array;
        $keys = (array) $keys;

        if (count($keys) === 0) return;

        foreach ($keys as $key) {
            // Jika key ada di top level, langsung unset
            if (static::exists($array, $key)) {
                unset($array[$key]);
                continue;
            }

            // Pecah dot notation
            $parts = explode('.', $key);
            $array = &$original;

            while (count($parts) > 1) {
                $part = array_shift($parts);

                if (isset($array[$part]) && is_array($array[$part])) {
                    $array = &$array[$part];
                } else {
                    continue 2;
                }
            }

            unset($array[array_shift($parts)]);
        }
    }

    /**
     * Ambil dan hapus (Pull)
     */
    public static function pull(&$array, $key, $default = null)
    {
        $value = static::get($array, $key, $default);
        static::forget($array, $key);
        return $value;
    }

    // =========================================================================
    // 3. FILTERING (ONLY & EXCEPT)
    // =========================================================================

    /**
     * Ambil HANYA key tertentu (Whitelist)
     * Usage: Arr::only($input, ['username', 'email'])
     */
    public static function only($array, $keys)
    {
        return array_intersect_key($array, array_flip((array) $keys));
    }

    /**
     * Ambil KECUALI key tertentu (Blacklist)
     * Usage: Arr::except($input, ['password', 'token'])
     */
    public static function except($array, $keys)
    {
        static::forget($array, $keys);
        return $array;
    }

    // =========================================================================
    // 4. UTILITIES & HELPERS
    // =========================================================================

    /**
     * Cek apakah variabel bisa diakses sebagai array
     */
    public static function accessible($value)
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }

    /**
     * Cek apakah key ada di array (Level 1 saja)
     */
    public static function exists($array, $key)
    {
        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }
        return array_key_exists($key, $array);
    }

    /**
     * Cek apakah array adalah Associative (bukan sequential list 0,1,2)
     */
    public static function isAssoc(array $array)
    {
        $keys = array_keys($array);
        return array_keys($keys) !== $keys;
    }

    /**
     * Kebalikan isAssoc
     */
    public static function isList(array $array)
    {
        return !static::isAssoc($array);
    }

    /**
     * Meratakan array multidimensi menjadi 1 dimensi
     * Usage: Arr::flatten([[1,2], [3,4]]) -> [1,2,3,4]
     */
    public static function flatten($array, $depth = INF)
    {
        $result = [];
        foreach ($array as $item) {
            if (!is_array($item)) {
                $result[] = $item;
            } elseif ($depth === 1) {
                $result = array_merge($result, array_values($item));
            } else {
                $result = array_merge($result, static::flatten($item, $depth - 1));
            }
        }
        return $result;
    }

    /**
     * Mengambil elemen acak dari array
     */
    public static function random($array, $number = null)
    {
        $requested = is_null($number) ? 1 : $number;
        $count = count($array);

        if ($requested > $count) {
            throw new InvalidArgumentException("You requested {$requested} items, but there are only {$count} items available.");
        }

        if (is_null($number)) {
            return $array[array_rand($array)];
        }

        if ($number === 0) return [];

        $keys = array_rand($array, $number);
        $results = [];

        foreach ((array) $keys as $key) {
            $results[] = $array[$key];
        }

        return $results;
    }

    /**
     * Menggabungkan array menjadi string (Join)
     * Usage: Arr::join(['a', 'b', 'c'], ', ', ' dan ') -> "a, b dan c"
     */
    public static function join($array, $glue, $finalGlue = '')
    {
        if ($finalGlue === '') return implode($glue, $array);

        if (count($array) === 0) return '';
        if (count($array) === 1) return end($array);

        $finalItem = array_pop($array);
        return implode($glue, $array) . $finalGlue . $finalItem;
    }

    /**
     * Convert array ke Query String URL
     * Usage: Arr::query(['search' => 'buku', 'page' => 1]) -> "search=buku&page=1"
     */
    public static function query($array)
    {
        return http_build_query($array, '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * Membungkus value menjadi array jika belum array
     */
    public static function wrap($value)
    {
        if (is_null($value)) return [];
        return is_array($value) ? $value : [$value];
    }
}