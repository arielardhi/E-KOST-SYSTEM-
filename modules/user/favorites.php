<?php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle Remove Favorite
if (isset($_GET['remove'])) {
    $kost_id = $_GET['remove'];
    $stmt = $pdo->prepare("DELETE FROM favorit WHERE user_id = ? AND kost_id = ?");
    $stmt->execute([$user_id, $kost_id]);
    header("Location: favorites.php");
    exit();
}

// Get favorites
$stmt = $pdo->prepare("
    SELECT k.*, f.id as fav_id, (SELECT image_path FROM kost_foto WHERE kost_id = k.id AND is_main = 1 LIMIT 1) as main_image
    FROM favorit f
    JOIN kost k ON f.kost_id = k.id
    WHERE f.user_id = ?
");
$stmt->execute([$user_id]);
$favorites = $stmt->fetchAll();

include '../../layouts/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="list-group">
                <a href="dashboard.php" class="list-group-item list-group-item-action"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                <a href="bookings.php" class="list-group-item list-group-item-action"><i class="bi bi-calendar-check me-2"></i> Pesanan Saya</a>
                <a href="favorites.php" class="list-group-item list-group-item-action active"><i class="bi bi-heart me-2"></i> Favorit</a>
                <a href="chat.php" class="list-group-item list-group-item-action"><i class="bi bi-chat-dots me-2"></i> Chat</a>
                <a href="profile.php" class="list-group-item list-group-item-action"><i class="bi bi-person me-2"></i> Profil</a>
            </div>
        </div>

        <div class="col-md-9">
            <h2 class="mb-4 fw-black text-uppercase">Kost Favorit Saya</h2>
            
            <div class="row g-4">
                <?php if (empty($favorites)): ?>
                    <div class="col-12">
                        <div class="card p-5 text-center">
                            <i class="bi bi-heart-break fs-1 text-muted mb-3"></i>
                            <p class="mb-0">Belum ada kost yang disimpan sebagai favorit.</p>
                            <a href="../../pages/kost_list.php" class="btn btn-primary mt-3">Cari Kost Sekarang</a>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($favorites as $kost): ?>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <img src="<?php echo $kost['main_image'] ? $base_url . $kost['main_image'] : 'https://via.placeholder.com/400x300?text=No+Image'; ?>" class="card-img-top" style="height: 200px; object-fit: cover; border-bottom: 3px solid #000;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title fw-bold mb-0"><?php echo $kost['name']; ?></h5>
                                        <span class="badge bg-neubrutal-yellow"><?php echo $kost['type']; ?></span>
                                    </div>
                                    <p class="text-muted small mb-3"><i class="bi bi-geo-alt"></i> <?php echo $kost['city']; ?></p>
                                    <div class="d-grid gap-2">
                                        <a href="../../pages/kost_detail.php?id=<?php echo $kost['id']; ?>" class="btn btn-secondary">Lihat Detail</a>
                                        <a href="?remove=<?php echo $kost['id']; ?>" class="btn btn-danger" onclick="return confirm('Hapus dari favorit?')">Hapus</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../../layouts/footer.php'; ?>
