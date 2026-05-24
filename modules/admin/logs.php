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
    <h2 class="fw-black text-uppercase mb-4">Log Aktivitas Sistem</h2>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Waktu</th>
                            <th>Pengguna</th>
                            <th>Aktivitas</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo date('d/m/Y H:i'); ?></td>
                            <td><span class="badge bg-danger">ADMIN</span></td>
                            <td class="fw-bold">Login ke dashboard admin</td>
                            <td>127.0.0.1</td>
                        </tr>
                        <tr>
                            <td><?php echo date('d/m/Y H:i', strtotime('-1 hour')); ?></td>
                            <td><span class="badge bg-primary">OWNER</span></td>
                            <td class="fw-bold">Menambahkan kost baru: "Kost Bunga"</td>
                            <td>192.168.1.5</td>
                        </tr>
                        <tr class="table-light">
                            <td colspan="4" class="text-center py-4 text-muted small italic">Log sistem lama otomatis dibersihkan setiap 30 hari.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../../layouts/footer.php'; ?>
