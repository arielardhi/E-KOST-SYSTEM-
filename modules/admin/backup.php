<?php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include '../../layouts/header.php';
?>

<div class="container py-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-5 bg-neubrutal-blue">
                <div class="mb-4">
                    <i class="bi bi-database-down display-1"></i>
                </div>
                <h2 class="fw-black text-uppercase mb-3">Backup Database</h2>
                <p class="lead fw-bold mb-4">Klik tombol di bawah untuk mengunduh salinan database sistem saat ini.</p>
                
                <div class="d-grid gap-3">
                    <button class="btn btn-dark btn-lg py-3" onclick="alert('Demo: File SQL sedang disiapkan...')">UNDUH SQL BACKUP</button>
                    <a href="dashboard.php" class="btn btn-light">KEMBALI KE DASHBOARD</a>
                </div>
                
                <div class="mt-4 p-3 border border-2 border-dark bg-white text-start">
                    <small class="fw-bold text-uppercase">Informasi:</small>
                    <ul class="small mb-0 mt-1">
                        <li>Ukuran database: ~2.4 MB</li>
                        <li>Terakhir backup: <?php echo date('d M Y, H:i'); ?></li>
                        <li>Format file: .sql (Gzipped)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../layouts/footer.php'; ?>
