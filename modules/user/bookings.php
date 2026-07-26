<?php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user bookings
$stmt = $pdo->prepare("
    SELECT b.*, k.name as kost_name, km.room_name, k.id as kost_id, p.status as payment_status
    FROM booking b
    JOIN kamar km ON b.kamar_id = km.id
    JOIN kost k ON km.kost_id = k.id
    LEFT JOIN pembayaran p ON b.id = p.booking_id
    WHERE b.user_id = ?
    ORDER BY b.created_at DESC
");
$stmt->execute([$user_id]);
$bookings = $stmt->fetchAll();

include '../../layouts/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="list-group">
                <a href="dashboard.php" class="list-group-item list-group-item-action"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                <a href="bookings.php" class="list-group-item list-group-item-action active"><i class="bi bi-calendar-check me-2"></i> Pesanan Saya</a>
                <a href="favorites.php" class="list-group-item list-group-item-action"><i class="bi bi-heart me-2"></i> Favorit</a>
                <a href="pesan.php" class="list-group-item list-group-item-action"><i class="bi bi-chat-dots me-2"></i> Chat</a>
                <a href="profile.php" class="list-group-item list-group-item-action"><i class="bi bi-person me-2"></i> Profil</a>
            </div>
        </div>

        <div class="col-md-9">
            <h2 class="mb-4 fw-black text-uppercase">Riwayat Pesanan Saya</h2>
            
            <?php if (empty($bookings)): ?>
                <div class="card p-5 text-center">
                    <p class="mb-0">Anda belum melakukan pemesanan kost.</p>
                    <a href="../../pages/kost_list.php" class="btn btn-primary mt-3">Cari Kost</a>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($bookings as $b): ?>
                        <div class="col-12">
                            <div class="card p-3">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <h5 class="fw-bold mb-1"><?php echo $b['kost_name']; ?></h5>
                                        <p class="text-muted mb-0 small"><?php echo $b['room_name']; ?> • <?php echo date('d M Y', strtotime($b['created_at'])); ?></p>
                                    </div>
                                    <div class="col-md-3 text-md-center">
                                        <span class="badge bg-<?php 
                                            echo $b['status'] == 'pending' ? 'neubrutal-yellow' : 
                                                ($b['status'] == 'confirmed' ? 'neubrutal-blue' : 'danger'); 
                                        ?>">
                                            <?php echo strtoupper($b['status']); ?>
                                        </span>
                                    </div>
                                    <div class="col-md-3 text-md-end">
                                        <a href="booking_detail.php?id=<?php echo $b['id']; ?>" class="btn btn-secondary btn-sm">Detail & Bayar</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../layouts/footer.php'; ?>

