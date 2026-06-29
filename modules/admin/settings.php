<?php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$success = '';
$error = '';

// Handle POST request for site information
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_site_info'])) {
    $app_name = trim($_POST['app_name'] ?? '');
    $support_email = trim($_POST['support_email'] ?? '');
    $contact_phone = trim($_POST['contact_phone'] ?? '');

    if (empty($app_name)) {
        $error = 'Nama Aplikasi tidak boleh kosong.';
    } else {
        try {
            $pdo->beginTransaction();
            
            $update_stmt = $pdo->prepare("UPDATE system_settings SET setting_value = ? WHERE setting_key = ?");
            $update_stmt->execute([$app_name, 'app_name']);
            $update_stmt->execute([$support_email, 'support_email']);
            $update_stmt->execute([$contact_phone, 'contact_phone']);

            // Log activity
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $log_stmt = $pdo->prepare("INSERT INTO system_logs (user_id, username, role, activity, ip_address) VALUES (?, ?, ?, ?, ?)");
            $log_stmt->execute([
                $_SESSION['user_id'],
                $_SESSION['username'] ?? 'admin',
                $_SESSION['role'] ?? 'admin',
                "Mengubah pengaturan Informasi Situs (Nama Aplikasi: $app_name)",
                $ip
            ]);

            $pdo->commit();
            $success = 'Informasi situs berhasil diperbarui!';
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = 'Gagal memperbarui informasi situs: ' . $e->getMessage();
        }
    }
}

// Handle POST request for maintenance mode
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_maintenance'])) {
    $maintenance_val = isset($_POST['maintenance_mode']) ? '1' : '0';
    try {
        $pdo->beginTransaction();

        $update_stmt = $pdo->prepare("UPDATE system_settings SET setting_value = ? WHERE setting_key = ?");
        $update_stmt->execute([$maintenance_val, 'maintenance_mode']);

        // Log activity
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $log_stmt = $pdo->prepare("INSERT INTO system_logs (user_id, username, role, activity, ip_address) VALUES (?, ?, ?, ?, ?)");
        $log_val = ($maintenance_val === '1') ? 'Mengaktifkan Mode Maintenance' : 'Menonaktifkan Mode Maintenance';
        $log_stmt->execute([
            $_SESSION['user_id'],
            $_SESSION['username'] ?? 'admin',
            $_SESSION['role'] ?? 'admin',
            $log_val,
            $ip
        ]);

        $pdo->commit();
        $success = 'Mode maintenance berhasil diperbarui!';
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $error = 'Gagal memperbarui mode maintenance: ' . $e->getMessage();
    }
}

// Load settings from database
$settings = [];
try {
    $settings_stmt = $pdo->query("SELECT * FROM system_settings");
    while ($row = $settings_stmt->fetch(PDO::FETCH_ASSOC)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
} catch (Exception $e) {
    // Fallback to defaults
}

$app_name = $settings['app_name'] ?? 'E-KOST SYSTEM';
$support_email = $settings['support_email'] ?? 'support@ekost.com';
$contact_phone = $settings['contact_phone'] ?? '+62 812 3456 7890';
$maintenance_mode = intval($settings['maintenance_mode'] ?? 0);

include '../../layouts/header.php';
?>

<div class="container py-4">
    <h2 class="fw-black text-uppercase mb-4">Pengaturan Sistem</h2>

    <?php if ($success): ?>
        <div class="alert alert-success border border-3 border-dark rounded-0 fw-bold shadow-sm mb-4">
            <i class="bi bi-check-circle-fill me-2"></i> <?php echo $success; ?>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger border border-3 border-dark rounded-0 fw-bold shadow-sm mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-neubrutal-yellow border-bottom border-3 border-dark py-3">
                    <h5 class="mb-0 fw-black text-uppercase">Informasi Situs</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="settings.php">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Aplikasi</label>
                            <input type="text" name="app_name" class="form-control" value="<?php echo htmlspecialchars($app_name); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email Support</label>
                            <input type="email" name="support_email" class="form-control" value="<?php echo htmlspecialchars($support_email); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Telepon Kontak</label>
                            <input type="text" name="contact_phone" class="form-control" value="<?php echo htmlspecialchars($contact_phone); ?>">
                        </div>
                        <button type="submit" name="save_site_info" class="btn btn-primary w-100 fw-bold border border-2 border-dark">SIMPAN PERUBAHAN</button>
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
                            <h6 class="fw-bold mb-1">Log Sistem</h6>
                            <p class="small text-muted mb-2">Lihat riwayat aktivitas admin dan error sistem.</p>
                            <a href="logs.php" class="btn btn-dark btn-sm w-100 fw-bold">LIHAT LOG</a>
                        </div>
                        <div class="p-3 border border-2 border-dark bg-light">
                            <h6 class="fw-bold mb-1">Mode Maintenance</h6>
                            <p class="small text-muted mb-2">Nonaktifkan akses publik untuk sementara.</p>
                            <form method="POST" action="settings.php">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="maintenance_mode" id="maintenanceMode" value="1" <?php echo $maintenance_mode === 1 ? 'checked' : ''; ?> onchange="this.form.submit()" style="cursor: pointer;">
                                    <label class="form-check-label fw-bold" for="maintenanceMode" style="cursor: pointer;">Aktifkan Mode Perbaikan</label>
                                </div>
                                <input type="hidden" name="save_maintenance" value="1">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../layouts/footer.php'; ?>
