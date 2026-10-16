<?php
error_reporting(0);
define('BASE_DIR', __DIR__);

// --- KONFIGURASI ---
$log_file = __DIR__ . '/logs/honeypot.json'; // Pastikan folder ini writable
$secret_key = 'gilang_ganteng_sekali'; // Ganti ini untuk akses Wall of Shame
// -------------------

if (session_status() === PHP_SESSION_NONE) session_start();

$ip     = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$ua     = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
$method = $_SERVER['REQUEST_METHOD'];

// 1. Fungsi Simpan Log ke File JSON
function log_to_honeypot($file, $data) {
    $current_logs = [];
    if (file_exists($file)) {
        $json_data = file_get_contents($file);
        $current_logs = json_decode($json_data, true) ?? [];
    }
    
    // Tambah data baru di awal (stack)
    array_unshift($current_logs, array_merge(['timestamp' => date('Y-m-d H:i:s')], $data));
    
    // Batasi log hanya 100 data terakhir agar file tidak bengkak
    $current_logs = array_slice($current_logs, 0, 100);
    
    file_put_contents($file, json_encode($current_logs, JSON_PRETTY_PRINT));
}

// 2. FITUR: Wall of Shame Rahasia
// Cara akses: namadomain.com/adminer.php?view_shame=gilang_ganteng_sekali
if (isset($_GET['view_shame']) && $_GET['view_shame'] === $secret_key) {
    $logs = file_exists($log_file) ? json_decode(file_get_contents($log_file), true) : [];
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Wall of Shame - Bot Tracker</title>
        <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
        <style>body { padding: 20px; background: #1a2035; color: #fff; } .table { color: #fff; }</style>
    </head>
    <body>
        <h2>💀 Wall of Shame (Adminer Honeypot)</h2>
        <table class="table table-bordered table-striped mt-4">
            <thead class="thead-light">
                <tr>
                    <th>Time</th>
                    <th>IP Address</th>
                    <th>Method</th>
                    <th>Data Attempted</th>
                    <th>User Agent</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $l): ?>
                <tr>
                    <td><?= $l['timestamp'] ?></td>
                    <td><span class="badge badge-danger"><?= $l['ip'] ?></span></td>
                    <td><?= $l['method'] ?></td>
                    <td><pre style="color: #00ff00; background: #000; padding: 5px;"><?= htmlspecialchars(json_encode($l['attempt_data'])) ?></pre></td>
                    <td><small><?= $l['user_agent'] ?></small></td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($logs)) echo "<tr><td colspan='5' class='text-center'>Belum ada ikan yang terjebak...</td></tr>"; ?>
            </tbody>
        </table>
    </body>
    </html>
    <?php
    exit;
}

// 3. Logger Utama (Tetap pakai Logger sistem kamu jika ada)
if (is_file(__DIR__ . '/System/core/Logger.php')) {
    require_once __DIR__ . '/System/core/Logger.php';
    if (class_exists('Logger')) {
        Logger::write('HONEY_POT_HIT', ['target' => 'adminer.php', 'ip' => $ip]);
    }
}

// 4. Catat ke file JSON lokal untuk Wall of Shame
$attempt_data = ($method === 'POST') ? $_POST : 'GET_SCAN';
log_to_honeypot($log_file, [
    'ip' => $ip,
    'method' => $method,
    'user_agent' => $ua,
    'attempt_data' => $attempt_data
]);

// 5. Eksekusi Trap
if ($method === 'POST') {
    sleep(2); // Drama sedikit
    header("Location: https://www.youtube.com/watch?v=dQw4w9WgXcQ");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Adminer</title>
    <style>
        body { background: #f0f0f0; font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: #fff; padding: 20px; border: 1px solid #ccc; box-shadow: 0 2px 5px rgba(0,0,0,0.1); width: 340px; }
        h1 { font-size: 1.5em; margin-bottom: 20px; color: #333; border-bottom: 1px solid #ccc; padding-bottom: 10px; }
        label { display: block; font-size: 0.9em; margin-top: 10px; color: #666; }
        input, select { width: 100%; padding: 6px; margin-top: 4px; box-sizing: border-box; }
        input[type="submit"] { margin-top: 15px; background: #4285f4; color: white; border: none; cursor: pointer; font-weight: bold; padding: 10px; }
        input[type="submit"]:hover { background: #357ae8; }
    </style>
</head>
<body>
    <div class="login-box">
        <h1>Adminer 4.8.1</h1>
        <form method="post">
            <label>System</label>
            <select name="auth[driver]">
                <option value="server">MySQL</option>
                <option value="sqlite">SQLite 3</option>
                <option value="pgsql">PostgreSQL</option>
            </select>
            <label>Server</label>
            <input type="text" name="auth[server]" value="localhost">
            <label>Username</label>
            <input type="text" name="auth[login]" autofocus>
            <label>Password</label>
            <input type="password" name="auth[password]">
            <label>Database</label>
            <input type="text" name="auth[db]">
            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>