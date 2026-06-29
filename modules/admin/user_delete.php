<?php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$id = $_GET['id'] ?? 0;

if ($id == $_SESSION['user_id']) {
    die("Anda tidak bisa menghapus akun Anda sendiri!");
}

if ($id) {
    // Get username before deleting
    $username_to_delete = '';
    try {
        $stmt_get = $pdo->prepare("SELECT username FROM users WHERE id = ?");
        $stmt_get->execute([$id]);
        $username_to_delete = $stmt_get->fetchColumn();
    } catch (Exception $e) {
        // Fail silently
    }

    if ($username_to_delete) {
        try {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $log_stmt = $pdo->prepare("INSERT INTO system_logs (user_id, username, role, activity, ip_address) VALUES (?, ?, ?, ?, ?)");
            $log_stmt->execute([
                $_SESSION['user_id'],
                $_SESSION['username'] ?? 'admin',
                $_SESSION['role'] ?? 'admin',
                "Menghapus pengguna: $username_to_delete",
                $ip
            ]);
        } catch (Exception $log_ex) {
            // Fail silently
        }
    }

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: users.php");
exit();
?>
