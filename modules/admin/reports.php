<?php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Get monthly revenue data
$revenue_data = $pdo->query("
    SELECT DATE_FORMAT(created_at, '%M %Y') as month, SUM(amount) as total 
    FROM pembayaran 
    WHERE status = 'verified' 
    GROUP BY month 
    ORDER BY created_at DESC 
    LIMIT 6
")->fetchAll();

include '../../layouts/header.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-black text-uppercase mb-0">Laporan Keuangan</h2>
        <button class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer me-2"></i> CETAK LAPORAN</button>
    </div>

    <div class="row g-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white py-3 border-bottom border-3 border-dark">
                    <h5 class="mb-0 fw-black text-uppercase">Ringkasan Pendapatan Bulanan</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Bulan</th>
                                    <th>Total Pendapatan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($revenue_data)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">Belum ada data pendapatan terverifikasi.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($revenue_data as $row): ?>
                                        <tr>
                                            <td class="fw-bold"><?php echo $row['month']; ?></td>
                                            <td class="fw-black">Rp <?php echo number_format($row['total'], 0, ',', '.'); ?></td>
                                            <td><span class="badge bg-success">TERVERIFIKASI</span></td>
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
