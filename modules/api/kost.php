<?php
/**
 * E-KOST Internal REST API
 * Endpoint: /e-kost-system/modules/api/kost.php
 */
require_once '../../config/database.php';
$base_url = "/" . basename(dirname(dirname(__DIR__))) . "/";
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$action = $_GET['action'] ?? 'list';

if ($action === 'list') {
    $city      = $_GET['city']      ?? '';
    $type      = $_GET['type']      ?? '';
    $max_price = $_GET['max_price'] ?? '';
    $min_price = $_GET['min_price'] ?? '';
    $limit     = min((int)($_GET['limit'] ?? 20), 50);
    $page      = max(1, (int)($_GET['page'] ?? 1));
    $offset    = ($page - 1) * $limit;

    $where = ["1=1", "(SELECT COALESCE(SUM(available_rooms), 0) FROM kamar WHERE kost_id = k.id) > 0"]; $params = [];
    if ($city)      { $where[] = "k.city LIKE ?";     $params[] = "%$city%"; }
    if ($type)      { $where[] = "k.type = ?";         $params[] = $type; }
    if ($max_price) { $where[] = "EXISTS (SELECT 1 FROM kamar WHERE kost_id=k.id AND price_per_month <= ?)"; $params[] = $max_price; }
    if ($min_price) { $where[] = "EXISTS (SELECT 1 FROM kamar WHERE kost_id=k.id AND price_per_month >= ?)"; $params[] = $min_price; }
    $whereStr = implode(' AND ', $where);

    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM kost k WHERE $whereStr");
    $countStmt->execute($params);
    $total = (int)$countStmt->fetchColumn();

    $stmt = $pdo->prepare("
        SELECT k.id, k.name, k.type, k.description, k.address, k.city,
               k.latitude, k.longitude, k.facilities, k.created_at,
               u.full_name AS owner_name, u.phone AS owner_phone,
               (SELECT price_per_month FROM kamar WHERE kost_id=k.id ORDER BY price_per_month ASC  LIMIT 1) AS price_min,
               (SELECT price_per_month FROM kamar WHERE kost_id=k.id ORDER BY price_per_month DESC LIMIT 1) AS price_max,
               (SELECT COALESCE(SUM(available_rooms), 0) FROM kamar WHERE kost_id=k.id) AS rooms_available,
               (SELECT COUNT(*) FROM kamar WHERE kost_id=k.id) AS rooms_total,
               (SELECT image_path FROM kost_foto WHERE kost_id=k.id AND is_main=1 LIMIT 1) AS main_image,
               (SELECT ROUND(AVG(rating),1) FROM review WHERE kost_id=k.id) AS avg_rating,
               (SELECT COUNT(*) FROM review WHERE kost_id=k.id) AS review_count
        FROM kost k JOIN users u ON k.owner_id=u.id
        WHERE $whereStr ORDER BY k.created_at DESC LIMIT $limit OFFSET $offset
    ");
    $stmt->execute($params);
    $kosts = $stmt->fetchAll();

    foreach ($kosts as &$k) {
        $k['facilities_list'] = $k['facilities'] ? array_map('trim', explode(',', $k['facilities'])) : [];
        $k['price_min']       = $k['price_min'] ? (int)$k['price_min'] : null;
        $k['price_max']       = $k['price_max'] ? (int)$k['price_max'] : null;
        $k['avg_rating']      = $k['avg_rating'] ? (float)$k['avg_rating'] : null;
        $k['rooms_available'] = (int)$k['rooms_available'];
        $k['rooms_total']     = (int)$k['rooms_total'];
        $k['image_url']       = $k['main_image'] ? $base_url.$k['main_image'] : null;
    }

    echo json_encode(['success'=>true,'source'=>'E-KOST Internal API v1.0','page'=>$page,'limit'=>$limit,'total'=>$total,'pages'=>(int)ceil($total/$limit),'data'=>$kosts], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
    exit();
}

if ($action === 'cities') {
    $cities = $pdo->query("SELECT DISTINCT city, COUNT(*) as total FROM kost GROUP BY city ORDER BY total DESC")->fetchAll();
    echo json_encode(['success'=>true,'data'=>$cities]);
    exit();
}

if ($action === 'stats') {
    $stats = [
        'total_kost'   => (int)$pdo->query("SELECT COUNT(*) FROM kost")->fetchColumn(),
        'total_rooms'  => (int)$pdo->query("SELECT COUNT(*) FROM kamar")->fetchColumn(),
        'available'    => (int)$pdo->query("SELECT COUNT(*) FROM kamar WHERE status='available'")->fetchColumn(),
        'total_owners' => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='owner'")->fetchColumn(),
        'total_users'  => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn(),
        'cities'       => (int)$pdo->query("SELECT COUNT(DISTINCT city) FROM kost")->fetchColumn(),
        'avg_price'    => (int)$pdo->query("SELECT COALESCE(AVG(price_per_month),0) FROM kamar")->fetchColumn(),
        'min_price'    => (int)$pdo->query("SELECT COALESCE(MIN(price_per_month),0) FROM kamar")->fetchColumn(),
        'max_price'    => (int)$pdo->query("SELECT COALESCE(MAX(price_per_month),0) FROM kamar")->fetchColumn(),
    ];
    echo json_encode(['success'=>true,'data'=>$stats]);
    exit();
}

echo json_encode(['success'=>false,'message'=>'Unknown action. Available: list, cities, stats']);
