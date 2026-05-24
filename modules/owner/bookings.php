<?php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header("Location: ../auth/login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];

// Handle status updates
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];
    $status = ($action == 'confirm') ? 'confirmed' : 'cancelled';
    
    $stmt = $pdo->prepare("UPDATE booking b JOIN kamar km ON b.kamar_id = km.id JOIN kost k ON km.kost_id = k.id SET b.status = ? WHERE b.id = ? AND k.owner_id = ?");
    $stmt->execute([$status, $id, $owner_id]);
    header("Location: bookings.php?success=1");
    exit();
}

$stmt = $pdo->prepare("
    SELECT b.*, k.name as kost_name, km.room_name, u.full_name as tenant_name, u.phone as tenant_phone, p.payment_proof, p.status as payment_status
    FROM booking b 
    JOIN kamar km ON b.kamar_id = km.id 
    JOIN kost k ON km.kost_id = k.id 
    JOIN users u ON b.user_id = u.id
    LEFT JOIN pembayaran p ON b.id = p.booking_id
    WHERE k.owner_id = ? 
    ORDER BY b.created_at DESC
");
$stmt->execute([$owner_id]);
$bookings = $stmt->fetchAll();

include '../../layouts/header.php';
?>

<div class="container">
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="list-group shadow-sm border-0">
                <a href="dashboard.php" class="list-group-item list-group-item-action"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                <a href="kost_manage.php" class="list-group-item list-group-item-action"><i class="bi bi-house me-2"></i> Kelola Kost</a>
                <a href="bookings.php" class="list-group-item list-group-item-action active"><i class="bi bi-calendar-check me-2"></i> Pesanan Masuk</a>
            </div>
        </div>

        <div class="col-md-9">
            <h3 class="mb-4">Daftar Pesanan Masuk</h3>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">Status pesanan berhasil diperbarui.</div>
            <?php endif; ?>

            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Penyewa</th>
                                    <th>Kost / Kamar</th>
                                    <th>Total Bayar</th>
                                    <th>Status Pesanan</th>
                                    <th>Bukti Bayar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($bookings)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">Belum ada pesanan masuk.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($bookings as $b): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo $b['tenant_name']; ?></strong><br>
                                                <small class="text-muted"><?php echo $b['tenant_phone']; ?></small>
                                            </td>
                                            <td>
                                                <?php echo $b['kost_name']; ?><br>
                                                <small class="text-muted"><?php echo $b['room_name']; ?></small>
                                            </td>
                                            <td>Rp <?php echo number_format($b['total_price'], 0, ',', '.'); ?></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $b['status'] == 'pending' ? 'warning' : 
                                                        ($b['status'] == 'confirmed' ? 'success' : 
                                                        ($b['status'] == 'cancelled' ? 'danger' : 'secondary')); 
                                                ?>">
                                                    <?php echo ucfirst($b['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($b['payment_proof']): ?>
                                                    <a href="../../uploads/payments/<?php echo $b['payment_proof']; ?>" target="_blank" class="btn btn-sm btn-outline-info">Lihat Bukti</a>
                                                    <br><small class="text-<?php echo $b['payment_status'] == 'verified' ? 'success' : 'warning'; ?>">
                                                        <?php echo ucfirst($b['payment_status']); ?> oleh Admin
                                                    </small>
                                                <?php else: ?>
                                                    <span class="text-muted small">Belum upload</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($b['status'] == 'pending'): ?>
                                                    <div class="btn-group">
                                                        <a href="?action=confirm&id=<?php echo $b['id']; ?>" class="btn btn-sm btn-success" onclick="return confirm('Konfirmasi pesanan ini?')">Terima</a>
                                                        <a href="?action=cancel&id=<?php echo $b['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Batalkan pesanan ini?')">Tolak</a>
                                                    </div>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
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
