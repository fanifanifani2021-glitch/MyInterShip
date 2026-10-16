<?php

class Logger
{
    private static $logDir = BASE_DIR . '/logs/';
    private static $maxFileSize = 300000; // 300KB

    /**
     * Tulis Log ke File
     * @param string $name Nama file log (tanpa .txt)
     * @param mixed $content Isi log (String/Array)
     */
    public static function write($name, $content)
    {
        // Pastikan folder logs ada
        if (!is_dir(self::$logDir)) {
            mkdir(self::$logDir, 0755, true);
        }

        $filePath = self::$logDir . $name . '.txt';

        // 1. Cek Rotasi / Backup File jika kegedean
        if (file_exists($filePath) && filesize($filePath) > self::$maxFileSize) {
            self::rotateLog($name, $filePath);
        }

        // 2. Format Konten
        $textData = is_array($content) ? implode("\n", $content) : (string) $content;
        if (empty($textData)) $textData = 'no_data';

        // 3. Susun Pesan Log
        $logEntry  = "Timestamp : " . date("D, d - M - Y | H:i:s") . "\n";
        $logEntry .= "Data      :\n" . $textData . "\n\n";
        $logEntry .= str_repeat("-", 50) . "\n";

        // 4. Append ke file (FILE_APPEND biar gak nimpa log lama)
        return file_put_contents($filePath, $logEntry, FILE_APPEND);
    }

    /**
     * Pindahkan log lama ke folder backup
     */
    private static function rotateLog($name, $filePath)
    {
        $backupDir = self::$logDir . 'backup/';
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $backupName = date("YmdHis") . "_" . $name . ".txt";
        rename($filePath, $backupDir . $backupName);
    }
}