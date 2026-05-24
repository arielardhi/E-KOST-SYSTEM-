<?php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header("Location: ../auth/login.php");
    exit();
}

$id = $_GET['id'] ?? 0;
$owner_id = $_SESSION['user_id'];

// Verify ownership before deleting
$stmt = $pdo->prepare("DELETE FROM kost WHERE id = ? AND owner_id = ?");
$stmt->execute([$id, $owner_id]);

header("Location: kost_manage.php");
exit();
?>
