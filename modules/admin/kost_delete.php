<?php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$id = $_GET['id'] ?? 0;

// Admin can delete any kost. Cascade delete takes care of kamar, fotos, reviews, favorites etc.
$stmt = $pdo->prepare("DELETE FROM kost WHERE id = ?");
$stmt->execute([$id]);

$_SESSION['success'] = "Kost berhasil dihapus!";
header("Location: kost.php");
exit();
?>
