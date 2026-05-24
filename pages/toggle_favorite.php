<?php
require_once '../config/database.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Silakan login terlebih dahulu.']);
    exit();
}

$user_id = $_SESSION['user_id'];
$kost_id = $_POST['kost_id'] ?? 0;

if (!$kost_id) {
    echo json_encode(['status' => 'error', 'message' => 'ID Kost tidak valid.']);
    exit();
}

// Check if already favorited
$stmt = $pdo->prepare("SELECT id FROM favorit WHERE user_id = ? AND kost_id = ?");
$stmt->execute([$user_id, $kost_id]);
$fav = $stmt->fetch();

if ($fav) {
    // Remove from favorite
    $stmt = $pdo->prepare("DELETE FROM favorit WHERE user_id = ? AND kost_id = ?");
    $stmt->execute([$user_id, $kost_id]);
    echo json_encode(['status' => 'success', 'action' => 'removed', 'message' => 'Dihapus dari favorit.']);
} else {
    // Add to favorite
    $stmt = $pdo->prepare("INSERT INTO favorit (user_id, kost_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $kost_id]);
    echo json_encode(['status' => 'success', 'action' => 'added', 'message' => 'Ditambahkan ke favorit.']);
}
?>
