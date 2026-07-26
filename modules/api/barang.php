<?php
/**
 * E-KOST Internal REST API — Barang Kebutuhan Kos
 * Endpoint: /e-kost-system/modules/api/barang.php
 */
require_once '../../config/database.php';
if (isset($_SERVER['SERVER_NAME']) && ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1')) {
    $base_url = "/" . basename(dirname(dirname(__DIR__))) . "/";
} else {
    $base_url = "/";
}
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$action = $_GET['action'] ?? 'list';

if ($action === 'list') {
    $kategori_id = $_GET['kategori_id'] ?? '';
    $kota        = $_GET['kota']        ?? '';
    $max_price   = $_GET['max_price']   ?? '';
    $min_price   = $_GET['min_price']   ?? '';
    $kondisi     = $_GET['kondisi']     ?? '';
    $search      = $_GET['search']      ?? '';
    $limit       = min((int)($_GET['limit'] ?? 20), 50);
    $page        = max(1, (int)($_GET['page'] ?? 1));
    $offset      = ($page - 1) * $limit;

    $where = ["b.status = 'tersedia'"]; 
    $params = [];
    
    if ($kategori_id) { $where[] = "b.kategori_id = ?"; $params[] = $kategori_id; }
    if ($kota)        { $where[] = "b.kota LIKE ?"; $params[] = "%$kota%"; }
    if ($max_price)   { $where[] = "b.harga <= ?"; $params[] = $max_price; }
    if ($min_price)   { $where[] = "b.harga >= ?"; $params[] = $min_price; }
    if ($kondisi)     { $where[] = "b.kondisi = ?"; $params[] = $kondisi; }
    if ($search)      { $where[] = "(b.nama_barang LIKE ? OR b.deskripsi LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
    
    $whereStr = implode(' AND ', $where);

    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM barang b WHERE $whereStr");
    $countStmt->execute($params);
    $total = (int)$countStmt->fetchColumn();

    $stmt = $pdo->prepare("
        SELECT b.id, b.kategori_id, b.nama_barang, b.deskripsi, b.harga, b.stok, b.kondisi,
               b.seller_id, b.kota, b.alamat, b.rating, b.jumlah_review,
               bk.nama_kategori, bk.icon,
               u.full_name AS seller_name, u.phone AS seller_phone,
               (SELECT image_path FROM barang_foto WHERE barang_id=b.id AND is_main=1 LIMIT 1) AS main_image
        FROM barang b 
        LEFT JOIN barang_kategori bk ON b.kategori_id=bk.id
        LEFT JOIN users u ON b.seller_id=u.id
        WHERE $whereStr 
        ORDER BY b.created_at DESC 
        LIMIT $limit OFFSET $offset
    ");
    $stmt->execute($params);
    $barangs = $stmt->fetchAll();

    foreach ($barangs as &$b) {
        $b['harga']           = (int)$b['harga'];
        $b['stok']            = (int)$b['stok'];
        $b['rating']          = $b['rating'] ? (float)$b['rating'] : null;
        $b['jumlah_review']   = (int)$b['jumlah_review'];
        $b['image_url']       = $b['main_image'] ? $base_url.$b['main_image'] : null;
        $b['tersedia']        = $b['stok'] > 0;
    }

    echo json_encode([
        'success' => true,
        'source'  => 'E-KOST Internal API v1.0 — Barang',
        'page'    => $page,
        'limit'   => $limit,
        'total'   => $total,
        'pages'   => (int)ceil($total/$limit),
        'data'    => $barangs
    ], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
    exit();
}

if ($action === 'categories') {
    $categories = $pdo->query("
        SELECT bk.*, COUNT(b.id) as total_barang 
        FROM barang_kategori bk 
        LEFT JOIN barang b ON bk.id=b.kategori_id AND b.status='tersedia'
        GROUP BY bk.id 
        ORDER BY bk.id ASC
    ")->fetchAll();
    echo json_encode(['success'=>true,'data'=>$categories]);
    exit();
}

if ($action === 'cities') {
    $cities = $pdo->query("
        SELECT DISTINCT kota, COUNT(*) as total 
        FROM barang 
        WHERE status='tersedia'
        GROUP BY kota 
        ORDER BY total DESC
    ")->fetchAll();
    echo json_encode(['success'=>true,'data'=>$cities]);
    exit();
}

if ($action === 'stats') {
    $stats = [
        'total_barang'     => (int)$pdo->query("SELECT COUNT(*) FROM barang WHERE status='tersedia'")->fetchColumn(),
        'total_kategori'   => (int)$pdo->query("SELECT COUNT(*) FROM barang_kategori")->fetchColumn(),
        'barang_tersedia'  => (int)$pdo->query("SELECT COUNT(*) FROM barang WHERE status='tersedia' AND stok > 0")->fetchColumn(),
        'total_seller'     => (int)$pdo->query("SELECT COUNT(DISTINCT seller_id) FROM barang WHERE status='tersedia'")->fetchColumn(),
        'kota'             => (int)$pdo->query("SELECT COUNT(DISTINCT kota) FROM barang WHERE status='tersedia'")->fetchColumn(),
        'avg_harga'        => (int)$pdo->query("SELECT COALESCE(AVG(harga),0) FROM barang WHERE status='tersedia'")->fetchColumn(),
        'min_harga'        => (int)$pdo->query("SELECT COALESCE(MIN(harga),0) FROM barang WHERE status='tersedia'")->fetchColumn(),
        'max_harga'        => (int)$pdo->query("SELECT COALESCE(MAX(harga),0) FROM barang WHERE status='tersedia'")->fetchColumn(),
    ];
    echo json_encode(['success'=>true,'data'=>$stats]);
    exit();
}

if ($action === 'detail') {
    $barang_id = $_GET['id'] ?? '';
    if (!$barang_id) {
        echo json_encode(['success'=>false,'message'=>'ID barang tidak ditemukan']);
        exit();
    }

    $stmt = $pdo->prepare("
        SELECT b.*, bk.nama_kategori, bk.icon,
               u.full_name AS seller_name, u.phone AS seller_phone, u.email AS seller_email
        FROM barang b 
        LEFT JOIN barang_kategori bk ON b.kategori_id=bk.id
        LEFT JOIN users u ON b.seller_id=u.id
        WHERE b.id = ?
    ");
    $stmt->execute([$barang_id]);
    $barang = $stmt->fetch();

    if (!$barang) {
        echo json_encode(['success'=>false,'message'=>'Barang tidak ditemukan']);
        exit();
    }

    // Get all photos
    $fotoStmt = $pdo->prepare("SELECT image_path FROM barang_foto WHERE barang_id = ? ORDER BY is_main DESC");
    $fotoStmt->execute([$barang_id]);
    $fotos = $fotoStmt->fetchAll();
    $barang['fotos'] = array_map(function($f) use ($base_url) {
        return ['url' => $base_url . $f['image_path']];
    }, $fotos);

    // Get reviews
    $reviewStmt = $pdo->prepare("
        SELECT br.id, br.rating, br.comment, br.created_at, u.full_name
        FROM barang_review br
        LEFT JOIN users u ON br.user_id=u.id
        WHERE br.barang_id = ?
        ORDER BY br.created_at DESC
        LIMIT 10
    ");
    $reviewStmt->execute([$barang_id]);
    $reviews = $reviewStmt->fetchAll();
    $barang['reviews'] = $reviews;

    $barang['harga'] = (int)$barang['harga'];
    $barang['stok'] = (int)$barang['stok'];
    $barang['rating'] = $barang['rating'] ? (float)$barang['rating'] : null;
    $barang['jumlah_review'] = (int)$barang['jumlah_review'];
    $barang['tersedia'] = $barang['stok'] > 0;

    echo json_encode(['success'=>true,'data'=>$barang], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
    exit();
}

echo json_encode(['success'=>false,'message'=>'Unknown action. Available: list, categories, cities, stats, detail']);
