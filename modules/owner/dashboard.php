<?php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header("Location: ../auth/login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];

// Stats
$stmt = $pdo->prepare("SELECT COUNT(*) FROM kost WHERE owner_id = ?");
$stmt->execute([$owner_id]);
$total_kost = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM booking b JOIN kamar km ON b.kamar_id = km.id JOIN kost k ON km.kost_id = k.id WHERE k.owner_id = ?");
$stmt->execute([$owner_id]);
$total_bookings = $stmt->fetchColumn();

// Recent bookings for owner
$stmt = $pdo->prepare("
    SELECT b.*, k.name as kost_name, km.room_name, u.full_name as tenant_name 
    FROM booking b 
    JOIN kamar km ON b.kamar_id = km.id 
    JOIN kost k ON km.kost_id = k.id 
    JOIN users u ON b.user_id = u.id
    WHERE k.owner_id = ? 
    ORDER BY b.created_at DESC LIMIT 5
");
$stmt->execute([$owner_id]);
$recent_bookings = $stmt->fetchAll();

include '../../layouts/header.php';
?>

<div class="container">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="list-group shadow-sm border-0">
                <a href="dashboard.php" class="list-group-item list-group-item-action active"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                <a href="kost_manage.php" class="list-group-item list-group-item-action"><i class="bi bi-house me-2"></i> Kelola Kost</a>
                <a href="bookings.php" class="list-group-item list-group-item-action"><i class="bi bi-calendar-check me-2"></i> Pesanan Masuk</a>
                <a href="chat.php" class="list-group-item list-group-item-action"><i class="bi bi-chat-dots me-2"></i> Chat</a>
                <a href="profile.php" class="list-group-item list-group-item-action"><i class="bi bi-person me-2"></i> Profil</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3>Dashboard Owner</h3>
                <a href="kost_add.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Kost Baru</a>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card bg-success text-white shadow-sm border-0">
                        <div class="card-body">
                            <h5>Total Kost Saya</h5>
                            <h2 class="fw-bold"><?php echo $total_kost; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-info text-white shadow-sm border-0">
                        <div class="card-body">
                            <h5>Pesanan Masuk</h5>
                            <h2 class="fw-bold"><?php echo $total_bookings; ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Pesanan Masuk Terbaru</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Penyewa</th>
                                    <th>Kost / Kamar</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recent_bookings)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Belum ada pesanan masuk.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recent_bookings as $booking): ?>
                                        <tr>
                                            <td><?php echo $booking['tenant_name']; ?></td>
                                            <td>
                                                <strong><?php echo $booking['kost_name']; ?></strong><br>
                                                <small class="text-muted"><?php echo $booking['room_name']; ?></small>
                                            </td>
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
                                                <a href="booking_manage.php?id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-outline-primary">Kelola</a>
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
