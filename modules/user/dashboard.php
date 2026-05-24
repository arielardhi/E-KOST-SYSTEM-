<?php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user stats
$stmt = $pdo->prepare("SELECT COUNT(*) FROM booking WHERE user_id = ?");
$stmt->execute([$user_id]);
$total_bookings = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM favorit WHERE user_id = ?");
$stmt->execute([$user_id]);
$total_favorites = $stmt->fetchColumn();

// Get recent bookings
$stmt = $pdo->prepare("
    SELECT b.*, k.name as kost_name, km.room_name 
    FROM booking b 
    JOIN kamar km ON b.kamar_id = km.id 
    JOIN kost k ON km.kost_id = k.id 
    WHERE b.user_id = ? 
    ORDER BY b.created_at DESC LIMIT 5
");
$stmt->execute([$user_id]);
$recent_bookings = $stmt->fetchAll();

include '../../layouts/header.php';
?>

<div class="container">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="list-group shadow-sm border-0">
                <a href="dashboard.php" class="list-group-item list-group-item-action active"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                <a href="bookings.php" class="list-group-item list-group-item-action"><i class="bi bi-calendar-check me-2"></i> Pesanan Saya</a>
                <a href="favorites.php" class="list-group-item list-group-item-action"><i class="bi bi-heart me-2"></i> Favorit</a>
                <a href="chat.php" class="list-group-item list-group-item-action"><i class="bi bi-chat-dots me-2"></i> Chat</a>
                <a href="profile.php" class="list-group-item list-group-item-action"><i class="bi bi-person me-2"></i> Profil</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <h3 class="mb-4">Halo, <?php echo $_SESSION['username']; ?>!</h3>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card bg-primary text-white shadow-sm border-0">
                        <div class="card-body">
                            <h5>Total Pesanan</h5>
                            <h2 class="fw-bold"><?php echo $total_bookings; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-danger text-white shadow-sm border-0">
                        <div class="card-body">
                            <h5>Kost Favorit</h5>
                            <h2 class="fw-bold"><?php echo $total_favorites; ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Pesanan Terbaru</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Kost</th>
                                    <th>Kamar</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recent_bookings)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Belum ada pesanan.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recent_bookings as $booking): ?>
                                        <tr>
                                            <td><?php echo $booking['kost_name']; ?></td>
                                            <td><?php echo $booking['room_name']; ?></td>
                                            <td><?php echo date('d M Y', strtotime($booking['created_at'])); ?></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $booking['status'] == 'pending' ? 'warning' : 
                                                        ($booking['status'] == 'confirmed' ? 'success' : 
                                                        ($booking['status'] == 'cancelled' ? 'danger' : 'secondary')); 
                                                ?>">
                                                    <?php echo ucfirst($booking['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="booking_detail.php?id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-outline-primary">Detail</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../layouts/footer.php'; ?>
