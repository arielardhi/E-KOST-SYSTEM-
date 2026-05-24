<?php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include '../../layouts/header.php';
?>

<div class="container py-4">
    <h2 class="fw-black text-uppercase mb-4">Pengaturan Sistem</h2>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-neubrutal-yellow border-bottom border-3 border-dark py-3">
                    <h5 class="mb-0 fw-black text-uppercase">Informasi Situs</h5>
                </div>
                <div class="card-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Nama Aplikasi</label>
                            <input type="text" class="form-control" value="E-KOST SYSTEM">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Support</label>
                            <input type="email" class="form-control" value="support@ekost.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Telepon Kontak</label>
                            <input type="text" class="form-control" value="+62 812 3456 7890">
                        </div>
                        <button type="button" class="btn btn-primary w-100" onclick="alert('Fitur demo: Pengaturan tidak disimpan')">SIMPAN PERUBAHAN</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-neubrutal-blue border-bottom border-3 border-dark py-3">
                    <h5 class="mb-0 fw-black text-uppercase">Pemeliharaan</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <div class="p-3 border border-2 border-dark bg-light">
                            <h6 class="fw-bold mb-1">Backup Database</h6>
                            <p class="small text-muted mb-2">Unduh salinan database terbaru dalam format .sql</p>
                            <a href="backup.php" class="btn btn-dark btn-sm w-100">JALANKAN BACKUP</a>
                        </div>
                        <div class="p-3 border border-2 border-dark bg-light">
                            <h6 class="fw-bold mb-1">Log Sistem</h6>
                            <p class="small text-muted mb-2">Lihat riwayat aktivitas admin dan error sistem.</p>
                            <a href="logs.php" class="btn btn-dark btn-sm w-100">LIHAT LOG</a>
                        </div>
                        <div class="p-3 border border-2 border-dark bg-light">
                            <h6 class="fw-bold mb-1">Mode Maintenance</h6>
                            <p class="small text-muted mb-2">Nonaktifkan akses publik untuk sementara.</p>
                            <div class="form-check form-switch">
                                <input class="form-check-switch" type="checkbox" id="maintenanceMode">
                                <label class="form-check-label fw-bold" for="maintenanceMode">Aktifkan Mode Perbaikan</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../layouts/footer.php'; ?>
