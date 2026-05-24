<?php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Handle Verification
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];
    $status = ($action == 'verify') ? 'verified' : 'rejected';
    
    try {
        $pdo->beginTransaction();
        
        // Update payment status
        $stmt = $pdo->prepare("UPDATE pembayaran SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        
        // If verified, update booking status too
        if ($status == 'verified') {
            $stmt = $pdo->prepare("SELECT booking_id FROM pembayaran WHERE id = ?");
            $stmt->execute([$id]);
            $booking_id = $stmt->fetchColumn();
            
            $stmt = $pdo->prepare("UPDATE booking SET status = 'confirmed' WHERE id = ?");
            $stmt->execute([$booking_id]);
        }
        
        $pdo->commit();
        header("Location: payments.php?success=1");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        header("Location: payments.php?error=" . $e->getMessage());
        exit();
    }
}

// Get payments
$stmt = $pdo->query("
    SELECT p.*, u.full_name as user_name, b.id as booking_id, k.name as kost_name
    FROM pembayaran p
    JOIN users u ON p.user_id = u.id
    JOIN booking b ON p.booking_id = b.id
    JOIN kamar km ON b.kamar_id = km.id
    JOIN kost k ON km.kost_id = k.id
    ORDER BY p.created_at DESC
");
$payments = $stmt->fetchAll();

include '../../layouts/header.php';
?>

<div class="container py-4">
    <h2 class="fw-black text-uppercase mb-4">Verifikasi Pembayaran</h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Status pembayaran berhasil diperbarui!</div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Kost</th>
                            <th>Jumlah</th>
                            <th>Bukti</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $p): ?>
                            <tr>
                                <td class="fw-bold"><?php echo $p['user_name']; ?></td>
                                <td><?php echo $p['kost_name']; ?> (ID: <?php echo $p['booking_id']; ?>)</td>
                                <td class="fw-bold">Rp <?php echo number_format($p['amount'], 0, ',', '.'); ?></td>
                                <td>
                                    <?php if ($p['payment_proof']): ?>
                                        <a href="../../<?php echo $p['payment_proof']; ?>" target="_blank" class="btn btn-light btn-sm border border-2 border-dark">LIHAT BUKTI</a>
                                    <?php else: ?>
                                        <span class="text-muted">No proof</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $p['status'] == 'verified' ? 'success' : ($p['status'] == 'pending' ? 'warning' : 'danger'); ?>">
                                        <?php echo strtoupper($p['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($p['status'] == 'pending'): ?>
                                        <div class="btn-group">
                                            <a href="?action=verify&id=<?php echo $p['id']; ?>" class="btn btn-success btn-sm me-1" onclick="return confirm('Verifikasi pembayaran ini?')"><i class="bi bi-check-lg"></i></a>
                                            <a href="?action=reject&id=<?php echo $p['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tolak pembayaran ini?')"><i class="bi bi-x-lg"></i></a>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small">SELESAI</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../../layouts/footer.php'; ?>
