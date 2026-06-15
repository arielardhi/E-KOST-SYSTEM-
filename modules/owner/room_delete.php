<?php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header("Location: ../auth/login.php");
    exit();
}

$room_id = $_GET['id'] ?? 0;
$kost_id = $_GET['kost_id'] ?? 0;
$owner_id = $_SESSION['user_id'];

// Verify room ownership by joining with kost
$stmt = $pdo->prepare("
    SELECT km.*, k.owner_id 
    FROM kamar km 
    JOIN kost k ON km.kost_id = k.id 
    WHERE km.id = ? AND k.owner_id = ?
");
$stmt->execute([$room_id, $owner_id]);
$room = $stmt->fetch();

if ($room) {
    // Delete room
    $stmt_del = $pdo->prepare("DELETE FROM kamar WHERE id = ?");
    $stmt_del->execute([$room_id]);
}

header("Location: room_manage.php?kost_id=" . $kost_id);
exit();
