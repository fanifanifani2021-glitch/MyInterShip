<?php

class File
{
    /**
     * Upload file dari $_FILES
     * @param array $fileInput Data $_FILES['nama_input']
     * @param string $destination Folder tujuan (relative terhadap index.php)
     * @return string|bool Nama file baru jika sukses, false jika gagal
     */
    public static function upload($fileInput, $destination = 'upload/')
    {
        if (!isset($fileInput['tmp_name']) || $fileInput['error'] !== 0) {
            return false;
        }

        // Buat folder jika belum ada
        if (!is_dir($destination)) {
            mkdir($destination, 0777, true);
        }

        // Generate nama unik (timestamp + random string)
        $ext = pathinfo($fileInput['name'], PATHINFO_EXTENSION);
        $newName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
        
        $target = rtrim($destination, '/') . '/' . $newName;

        if (move_uploaded_file($fileInput['tmp_name'], $target)) {
            return $newName;
        }

        return false;
    }

    /**
     * Hapus file jika ada
     */
    public static function delete($path)
    {
        if (file_exists($path)) {
            return unlink($path);
        }
        return false;
    }
}