<?php

class FileViewer
{
    /**
     * Serve file securely
     *
     * @param string $baseDir   Base directory allowed
     * @param string $file      Requested file (full path)
     * @param array  $allowed   ['ext' => ['mime1','mime2']]
     * @param bool   $download  true = force download
     * @param string|null $asName override filename
     */
    public static function serve(
        string $baseDir,
        string $file,
        array $allowed,
        bool $download = false,
        string $asName = null
    ) {
        $realBase = realpath($baseDir);
        $realFile = realpath($file);

        if (!$realBase || !$realFile) {
            self::abort(404, 'File not found');
        }

        // 🔐 Path traversal protection
        if (strpos($realFile, $realBase) !== 0) {
            self::abort(403, 'Access denied');
        }

        if (!is_file($realFile) || !is_readable($realFile)) {
            self::abort(404, 'File not readable');
        }

        $ext = strtolower(pathinfo($realFile, PATHINFO_EXTENSION));

        // 🔐 Extension whitelist
        if (!isset($allowed[$ext])) {
            self::abort(403, 'File type not allowed');
        }

        // 🔐 MIME validation (finfo)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $realFile);
        finfo_close($finfo);

        if (!in_array($mime, $allowed[$ext], true)) {
            self::abort(403, 'Invalid MIME type');
        }

        self::stream($realFile, $mime, $download, $asName);
    }

    /* -----------------------------
     * Stream File
     * ----------------------------*/
    private static function stream(
        string $file,
        string $mime,
        bool $download,
        ?string $asName
    ) {
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($file));
        header('X-Content-Type-Options: nosniff');
        header('Accept-Ranges: bytes');

        $filename = $asName ?: basename($file);

        if ($download) {
            header('Content-Disposition: attachment; filename="' . $filename . '"');
        } else {
            header('Content-Disposition: inline; filename="' . $filename . '"');
        }

        ob_clean();
        flush();
        readfile($file);
        exit;
    }

    /* -----------------------------
     * Abort helper
     * ----------------------------*/
    private static function abort(int $code, string $message)
    {
        http_response_code($code);

        // kalau mau konsisten pakai Router
        if (class_exists('Router')) {
            Router::json($code, $message);
        }

        echo $message;
        exit;
    }
}
