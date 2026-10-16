<?php
// get-session-timeout.php

// Ambil nilai session.gc_maxlifetime dari konfigurasi PHP
$sessionTimeout = ini_get('session.gc_maxlifetime');

// Kembalikan nilai tersebut dalam format JSON
echo json_encode(['sessionTimeout' => $sessionTimeout ?? 3600]);
