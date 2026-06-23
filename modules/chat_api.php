<?php
require_once '../config/database.php';
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$action  = $_GET['action'] ?? '';

// --- Kirim pesan ---
if ($action === 'send' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data        = json_decode(file_get_contents('php://input'), true);
    $receiver_id = (int)($data['receiver_id'] ?? 0);
    $message     = trim($data['message'] ?? '');

    if (!$receiver_id || !$message) {
        echo json_encode(['success' => false]);
        exit();
    }

    $stmt = $pdo->prepare("INSERT INTO chat (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $receiver_id, $message]);
    $new_id = $pdo->lastInsertId();

    $stmt = $pdo->prepare("SELECT * FROM chat WHERE id = ?");
    $stmt->execute([$new_id]);
    $msg = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'message' => $msg]);
    exit();
}

// --- Ambil pesan baru (polling) ---
if ($action === 'poll') {
    $receiver_id = (int)($_GET['receiver_id'] ?? 0);
    $last_id     = (int)($_GET['last_id']     ?? 0);

    if (!$receiver_id) { echo json_encode([]); exit(); }

    $stmt = $pdo->prepare("
        SELECT * FROM chat
        WHERE id > ?
          AND ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?))
        ORDER BY created_at ASC
    ");
    $stmt->execute([$last_id, $user_id, $receiver_id, $receiver_id, $user_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mark as read
    $pdo->prepare("UPDATE chat SET is_read = 1 WHERE sender_id = ? AND receiver_id = ? AND is_read = 0")
        ->execute([$receiver_id, $user_id]);

    echo json_encode($messages);
    exit();
}

// --- Hitung unread total ---
if ($action === 'unread_count') {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM chat WHERE receiver_id = ? AND is_read = 0");
    $stmt->execute([$user_id]);
    echo json_encode(['count' => (int)$stmt->fetchColumn()]);
    exit();
}

echo json_encode(['error' => 'Invalid action']);
