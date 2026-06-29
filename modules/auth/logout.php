<?php
require_once '../../config/database.php';
session_start();

try {
    if (isset($_SESSION['user_id'])) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $log_stmt = $pdo->prepare("INSERT INTO system_logs (user_id, username, role, activity, ip_address) VALUES (?, ?, ?, ?, ?)");
        $log_stmt->execute([
            $_SESSION['user_id'],
            $_SESSION['username'] ?? 'unknown',
            $_SESSION['role'] ?? 'unknown',
            'Logout dari sistem',
            $ip
        ]);
    }
} catch (Exception $e) {
    // Fail silently
}

session_destroy();
header("Location: login.php");
exit();
?>
