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
    <h2 class="fw-black text-uppercase mb-4">Verifikasi Kost Baru</h2>

    <div class="card p-5 text-center">
        <div class="mb-4">
            <i class="bi bi-patch-check display-1 text-primary"></i>
        </div>
        <h4 class="fw-black text-uppercase">Semua Properti Terverifikasi</h4>
        <p class="text-muted">Saat ini tidak ada pengajuan kost baru yang perlu ditinjau.</p>
        <div class="mt-3">
            <a href="kost.php" class="btn btn-outline-dark">LIHAT SEMUA KOST</a>
        </div>
    </div>
</div>

<?php include '../../layouts/footer.php'; ?>
