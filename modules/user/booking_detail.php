<?php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("
    SELECT b.*, k.name as kost_name, km.room_name, km.price_per_month, o.full_name as owner_name 
    FROM booking b 
    JOIN kamar km ON b.kamar_id = km.id 
    JOIN kost k ON km.kost_id = k.id 
    JOIN users o ON k.owner_id = o.id
    WHERE b.id = ? AND b.user_id = ?
");
$stmt->execute([$id, $_SESSION['user_id']]);
$booking = $stmt->fetch();

if (!$booking) {
    header("Location: dashboard.php");
    exit();
}

// Get latest payment for this booking
$pay_stmt = $pdo->prepare("SELECT * FROM pembayaran WHERE booking_id = ? ORDER BY id DESC LIMIT 1");
$pay_stmt->execute([$id]);
$payment = $pay_stmt->fetch();

$success_msg = isset($_GET['success']) ? "Pesanan berhasil dibuat! Silakan lakukan pembayaran." : "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['payment_proof'])) {
    $target_dir = "../../uploads/payments/";
    $file_extension = pathinfo($_FILES["payment_proof"]["name"], PATHINFO_EXTENSION);
    $file_name = "pay_" . $id . "_" . time() . "." . $file_extension;
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES["payment_proof"]["tmp_name"], $target_file)) {
        $stmt = $pdo->prepare("INSERT INTO pembayaran (booking_id, user_id, amount, payment_proof, status, payment_date) VALUES (?, ?, ?, ?, 'pending', NOW())");
        $stmt->execute([$id, $_SESSION['user_id'], $booking['total_price'], $file_name]);
        
        $success_msg = "Bukti pembayaran berhasil diunggah. Menunggu verifikasi.";
        // Refresh booking data
        $stmt = $pdo->prepare("SELECT b.*, k.name as kost_name, km.room_name, km.price_per_month, o.full_name as owner_name FROM booking b JOIN kamar km ON b.kamar_id = km.id JOIN kost k ON km.kost_id = k.id JOIN users o ON k.owner_id = o.id WHERE b.id = ?");
        $stmt->execute([$id]);
        $booking = $stmt->fetch();
        
        // Refresh payment data
        $pay_stmt->execute([$id]);
        $payment = $pay_stmt->fetch();
    }
}

include '../../layouts/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Detail Pesanan #<?php echo $id; ?></li>
                </ol>
            </nav>

            <?php if ($success_msg): ?>
                <div class="alert alert-success"><?php echo $success_msg; ?></div>
            <?php endif; ?>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Detail Pesanan</h5>
                    <span class="badge bg-<?php echo $booking['status'] == 'pending' ? 'warning' : ($booking['status'] == 'confirmed' ? 'success' : 'danger'); ?>">
                        <?php echo strtoupper($booking['status']); ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <p class="text-muted mb-1">Nama Kost</p>
                            <h6><?php echo $booking['kost_name']; ?></h6>
                            <p class="text-muted mb-1 mt-3">Tipe Kamar</p>
                            <h6><?php echo $booking['room_name']; ?></h6>
                        </div>
                        <div class="col-sm-6">
                            <p class="text-muted mb-1">Tanggal Mulai</p>
                            <h6><?php echo date('d M Y', strtotime($booking['start_date'])); ?></h6>
                            <p class="text-muted mb-1 mt-3">Durasi Sewa</p>
                            <h6><?php echo $booking['duration_months']; ?> Bulan</h6>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Total Harga</h5>
                        <h4 class="text-primary fw-bold mb-0">Rp <?php echo number_format($booking['total_price'], 0, ',', '.'); ?></h4>
                    </div>
                </div>
            </div>

            <?php if ($booking['status'] == 'pending'): ?>
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <?php if ($payment): ?>
                            <?php if ($payment['status'] == 'pending'): ?>
                                <h5 class="fw-bold mb-3 text-warning"><i class="bi bi-clock-history me-2"></i>Menunggu Verifikasi</h5>
                                <div class="alert alert-warning mb-4">
                                    <i class="bi bi-info-circle-fill me-2"></i> Bukti pembayaran Anda telah diunggah dan sedang menunggu proses verifikasi oleh Admin.
                                </div>
                                <div class="mb-3">
                                    <p class="text-muted mb-2 fw-semibold">Bukti Transfer yang Diunggah:</p>
                                    <a href="../../uploads/payments/<?php echo htmlspecialchars($payment['payment_proof']); ?>" target="_blank">
                                        <img src="../../uploads/payments/<?php echo htmlspecialchars($payment['payment_proof']); ?>" class="img-thumbnail rounded shadow-sm" style="max-height: 250px; object-fit: contain; background: #fafafa;">
                                    </a>
                                </div>
                            <?php elseif ($payment['status'] == 'rejected'): ?>
                                <h5 class="fw-bold mb-3 text-danger"><i class="bi bi-exclamation-octagon-fill me-2"></i>Bukti Pembayaran Ditolak</h5>
                                <div class="alert alert-danger mb-4">
                                    <i class="bi bi-x-circle-fill me-2"></i> Bukti pembayaran sebelumnya tidak disetujui atau ditolak oleh Admin. Silakan lakukan transfer ulang atau unggah bukti pembayaran yang valid.
                                </div>
                                
                                <h5 class="fw-bold mb-3">Instruksi Pembayaran</h5>
                                <p>Silakan transfer sesuai total harga ke rekening berikut:</p>
                                <div class="bg-light p-3 rounded mb-4 border border-1 border-danger-subtle">
                                    <p class="mb-1"><strong>Bank BCA</strong></p>
                                    <p class="mb-1">No. Rekening: 1234567890</p>
                                    <p class="mb-0">A/N: E-KOST SYSTEM</p>
                                </div>
                                
                                <h5 class="fw-bold mb-3">Upload Bukti Pembayaran Baru</h5>
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <input type="file" name="payment_proof" class="form-control" required accept="image/*">
                                        <small class="text-muted d-block mt-1">Format: JPG, JPEG, PNG. Maksimal 2MB.</small>
                                    </div>
                                    <button type="submit" class="btn btn-primary"><i class="bi bi-upload me-2"></i>Unggah Bukti Baru</button>
                                </form>
                            <?php endif; ?>
                        <?php else: ?>
                            <h5 class="fw-bold mb-3">Instruksi Pembayaran</h5>
                            <p>Silakan transfer sesuai total harga ke rekening berikut:</p>
                            <div class="bg-light p-3 rounded mb-4">
                                <p class="mb-1"><strong>Bank BCA</strong></p>
                                <p class="mb-1">No. Rekening: 1234567890</p>
                                <p class="mb-0">A/N: E-KOST SYSTEM</p>
                            </div>
                            
                            <h5 class="fw-bold mb-3">Upload Bukti Pembayaran</h5>
                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <input type="file" name="payment_proof" class="form-control" required accept="image/*">
                                    <small class="text-muted d-block mt-1">Format: JPG, JPEG, PNG. Maksimal 2MB.</small>
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="bi bi-upload me-2"></i>Unggah Bukti</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php elseif ($booking['status'] == 'confirmed'): ?>
                <div class="card shadow-sm border-0 mb-4 border-start border-4 border-success">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3 text-success"><i class="bi bi-check-circle-fill me-2"></i>Pembayaran Terverifikasi</h5>
                        <div class="alert alert-success mb-3">
                            <i class="bi bi-check-circle-fill me-2"></i> Pemesanan Anda telah dikonfirmasi dan pembayaran telah berhasil diverifikasi oleh Admin. Silakan hubungi pemilik kost melalui menu Chat untuk koordinasi lebih lanjut.
                        </div>
                        <?php if ($payment): ?>
                            <div class="mt-3">
                                <p class="text-muted mb-2 fw-semibold">Bukti Transfer:</p>
                                <a href="../../uploads/payments/<?php echo htmlspecialchars($payment['payment_proof']); ?>" target="_blank">
                                    <img src="../../uploads/payments/<?php echo htmlspecialchars($payment['payment_proof']); ?>" class="img-thumbnail rounded" style="max-height: 180px; object-fit: contain;">
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php elseif ($booking['status'] == 'cancelled'): ?>
                <div class="card shadow-sm border-0 mb-4 border-start border-4 border-danger">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3 text-danger"><i class="bi bi-x-circle-fill me-2"></i>Pemesanan Dibatalkan</h5>
                        <p class="mb-0 text-muted">Pemesanan kost ini telah dibatalkan. Jika Anda merasa ini adalah kesalahan, hubungi layanan dukungan kami atau pemilik kost.</p>
                    </div>
                </div>
            <?php elseif ($booking['status'] == 'completed'): ?>
                <div class="card shadow-sm border-0 mb-4 border-start border-4 border-info">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3 text-info"><i class="bi bi-check-all me-2"></i>Pemesanan Selesai</h5>
                        <p class="mb-0 text-muted">Masa sewa atau pesanan ini telah selesai. Terima kasih telah menggunakan layanan E-Kost System!</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../layouts/footer.php'; ?>
