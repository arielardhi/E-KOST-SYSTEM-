<?php
// Deteksi otomatis apakah sedang berjalan di Localhost (XAMPP) atau Online Hosting (InfinityFree)
if (isset($_SERVER['SERVER_NAME']) && ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1')) {
    // 1. Konfigurasi untuk Localhost (Komputer Anda)
    $host = 'localhost';
    $db_name = 'e_kost_db';
    $username = 'root';
    $password = '';
} else {
    // 2. Konfigurasi untuk InfinityFree Hosting
    // SILAKAN UBAH host dan password di bawah ini sesuai akun InfinityFree Anda:
    $host = 'sql202.infinityfree.com'; // Contoh: sqlXXX.infinityfree.com
    $db_name = 'if0_42398626_e_kost_db';
    $username = 'if0_42398626';
    $password = 'UVgeadaUP1Vy'; // Bisa dilihat di Client Area InfinityFree
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

/**
 * Validates and normalizes Indonesian mobile/WhatsApp phone numbers.
 * Supports format: 08xxxxxxxxx, 628xxxxxxxxx, +628xxxxxxxxx.
 * Clean non-digits (except leading +), checks length and prefix.
 * Returns normalized string if valid, false otherwise.
 */
function validate_indonesian_phone($phone) {
    $clean = preg_replace('/[^\d+]/', '', $phone);
    if (str_starts_with($clean, '+628')) {
        $clean = '628' . substr($clean, 4);
    } elseif (str_starts_with($clean, '+')) {
        $clean = substr($clean, 1);
    }
    if (preg_match('/^08\d{8,12}$/', $clean) || preg_match('/^628\d{8,12}$/', $clean)) {
        return $clean;
    }
    return false;
}
?>
