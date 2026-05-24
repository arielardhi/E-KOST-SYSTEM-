<?php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Get stats
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_kost = $pdo->query("SELECT COUNT(*) FROM kost")->fetchColumn();
$total_bookings = $pdo->query("SELECT COUNT(*) FROM booking")->fetchColumn();
$total_revenue = $pdo->query("SELECT SUM(amount) FROM pembayaran WHERE status = 'verified'")->fetchColumn() ?? 0;

// Recent users
$recent_users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll();

include '../../layouts/header.php';
?>

<div class="container py-4">
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card bg-neubrutal-yellow p-3 text-center h-100">
                <h6 class="text-uppercase fw-black mb-1">Total Pengguna</h6>
                <div class="display-5 fw-black"><?php echo $total_users; ?></div>
                <small class="fw-bold">USER TERDAFTAR</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-neubrutal-blue p-3 text-center h-100">
                <h6 class="text-uppercase fw-black mb-1">Total Kost</h6>
                <div class="display-5 fw-black"><?php echo $total_kost; ?></div>
                <small class="fw-bold">PROPERTI KOST</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-neubrutal-pink p-3 text-center h-100">
                <h6 class="text-uppercase fw-black mb-1">Total Pesanan</h6>
                <div class="display-5 fw-black"><?php echo $total_bookings; ?></div>
                <small class="fw-bold">TRANSAKSI BOOKING</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white p-3 text-center h-100">
                <h6 class="text-uppercase fw-black mb-1">Pendapatan</h6>
                <div class="h3 fw-black mb-0">Rp <?php echo number_format($total_revenue, 0, ',', '.'); ?></div>
                <small class="fw-bold">TOTAL TERVERIFIKASI</small>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Navigation Menu -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-dark text-white py-3 border-bottom border-3 border-dark">
                    <h5 class="mb-0 fw-black text-uppercase">Menu Navigasi</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="users.php" class="list-group-item list-group-item-action py-3 fw-bold"><i class="bi bi-people me-2"></i> Manajemen Pengguna</a>
                    <a href="kost.php" class="list-group-item list-group-item-action py-3 fw-bold"><i class="bi bi-house me-2"></i> Manajemen Kost</a>
                    <a href="payments.php" class="list-group-item list-group-item-action py-3 fw-bold"><i class="bi bi-credit-card me-2"></i> Verifikasi Pembayaran</a>
                    <a href="reports.php" class="list-group-item list-group-item-action py-3 fw-bold"><i class="bi bi-file-earmark-bar-graph me-2"></i> Laporan Sistem</a>
                    <a href="backup.php" class="list-group-item list-group-item-action py-3 fw-bold"><i class="bi bi-database me-2"></i> Backup Data</a>
                    <a href="settings.php" class="list-group-item list-group-item-action py-3 fw-bold"><i class="bi bi-gear me-2"></i> Pengaturan</a>
                </div>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white py-3 border-bottom border-3 border-dark">
                    <h5 class="mb-0 fw-black text-uppercase">Pengguna Terbaru</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Email</th>
                                    <th>Tgl Daftar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_users as $user): ?>
                                    <tr>
                                        <td class="fw-bold"><?php echo $user['username']; ?></td>
                                        <td><span class="badge bg-<?php echo $user['role'] == 'admin' ? 'danger' : ($user['role'] == 'owner' ? 'primary' : 'warning'); ?>"><?php echo strtoupper($user['role']); ?></span></td>
                                        <td><?php echo $user['email']; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../layouts/footer.php'; ?>
