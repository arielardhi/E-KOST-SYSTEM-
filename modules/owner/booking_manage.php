<?php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header("Location: ../auth/login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];
$id = $_GET['id'] ?? 0;

// Fetch booking details and verify owner ownership
$stmt = $pdo->prepare("
    SELECT b.*, k.name as kost_name, k.address as kost_address, k.city as kost_city, 
           km.room_name, km.price_per_month,
           u.full_name as tenant_name, u.email as tenant_email, u.phone as tenant_phone,
           p.id as payment_id, p.payment_proof, p.status as payment_status, p.amount as payment_amount, p.payment_date
    FROM booking b
    JOIN kamar km ON b.kamar_id = km.id
    JOIN kost k ON km.kost_id = k.id
    JOIN users u ON b.user_id = u.id
    LEFT JOIN pembayaran p ON b.id = p.booking_id
    WHERE b.id = ? AND k.owner_id = ?
");
$stmt->execute([$id, $owner_id]);
$booking = $stmt->fetch();

if (!$booking) {
    header("Location: dashboard.php");
    exit();
}

$error_msg = "";
$success_msg = "";
if (isset($_GET['success'])) {
    if ($_GET['success'] == 1) $success_msg = "Status pesanan berhasil diperbarui!";
    elseif ($_GET['success'] == 2) $success_msg = "Penyewa berhasil check-out. Kamar kini telah dikosongkan dan tersedia kembali!";
    elseif ($_GET['success'] == 3) $success_msg = "Masa sewa penyewa berhasil diperpanjang!";
}

// Handle Lease Management (Extend / Check-out)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['lease_action'])) {
    $lease_action = $_POST['lease_action'];
    
    if ($lease_action == 'checkout') {
        try {
            $pdo->beginTransaction();
            
            // Get booking details
            $status_stmt = $pdo->prepare("SELECT status, kamar_id FROM booking WHERE id = ?");
            $status_stmt->execute([$id]);
            $bk = $status_stmt->fetch();
            
            if ($bk && $bk['status'] === 'confirmed') {
                // Update booking status to completed
                $stmt = $pdo->prepare("UPDATE booking SET status = 'completed' WHERE id = ?");
                $stmt->execute([$id]);
                
                // Increment available rooms and set kamar to available
                $inc_stmt = $pdo->prepare("UPDATE kamar SET available_rooms = available_rooms + 1, status = 'available' WHERE id = ?");
                $inc_stmt->execute([$bk['kamar_id']]);
                
                // Notify tenant
                $msg = "Masa sewa Anda untuk " . $booking['room_name'] . " di " . $booking['kost_name'] . " telah selesai (check-out). Terima kasih telah menyewa!";
                $notif_stmt = $pdo->prepare("INSERT INTO notifikasi (user_id, message, is_read) VALUES (?, ?, 0)");
                $notif_stmt->execute([$booking['user_id'], $msg]);
            }
            
            $pdo->commit();
            header("Location: booking_manage.php?id=" . $id . "&success=2");
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            $error_msg = $e->getMessage();
        }
    } elseif ($lease_action == 'extend') {
        $extra_months = (int)($_POST['extra_months'] ?? 0);
        if ($extra_months > 0) {
            try {
                $pdo->beginTransaction();
                
                // Get current booking price and duration
                $status_stmt = $pdo->prepare("SELECT duration_months, total_price, kamar_id FROM booking WHERE id = ?");
                $status_stmt->execute([$id]);
                $bk = $status_stmt->fetch();
                
                if ($bk) {
                    // Get room price per month
                    $room_stmt = $pdo->prepare("SELECT price_per_month FROM kamar WHERE id = ?");
                    $room_stmt->execute([$bk['kamar_id']]);
                    $price_per_month = $room_stmt->fetchColumn();
                    
                    $new_duration = $bk['duration_months'] + $extra_months;
                    $new_price = $bk['total_price'] + ($price_per_month * $extra_months);
                    
                    // Update booking
                    $stmt = $pdo->prepare("UPDATE booking SET duration_months = ?, total_price = ? WHERE id = ?");
                    $stmt->execute([$new_duration, $new_price, $id]);
                    
                    // Notify tenant
                    $msg = "Masa sewa Anda untuk " . $booking['room_name'] . " di " . $booking['kost_name'] . " telah diperpanjang sebanyak " . $extra_months . " bulan oleh pemilik kost.";
                    $notif_stmt = $pdo->prepare("INSERT INTO notifikasi (user_id, message, is_read) VALUES (?, ?, 0)");
                    $notif_stmt->execute([$booking['user_id'], $msg]);
                }
                
                $pdo->commit();
                header("Location: booking_manage.php?id=" . $id . "&success=3");
                exit();
            } catch (Exception $e) {
                $pdo->rollBack();
                $error_msg = $e->getMessage();
            }
        }
    }
}

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $status = ($action == 'confirm') ? 'confirmed' : 'cancelled';
    $payment_status = ($action == 'confirm') ? 'verified' : 'rejected';
    
    try {
        $pdo->beginTransaction();
        
        // Get the current status and kamar_id before updating
        $status_stmt = $pdo->prepare("SELECT status, kamar_id FROM booking WHERE id = ?");
        $status_stmt->execute([$id]);
        $bk = $status_stmt->fetch();
        
        // Update booking status
        $stmt = $pdo->prepare("UPDATE booking b JOIN kamar km ON b.kamar_id = km.id JOIN kost k ON km.kost_id = k.id SET b.status = ? WHERE b.id = ? AND k.owner_id = ?");
        $stmt->execute([$status, $id, $owner_id]);
        
        // Update payment status (if payment exists for this booking)
        $stmt = $pdo->prepare("UPDATE pembayaran p JOIN booking b ON p.booking_id = b.id JOIN kamar km ON b.kamar_id = km.id JOIN kost k ON km.kost_id = k.id SET p.status = ?, p.payment_date = NOW() WHERE b.id = ? AND k.owner_id = ?");
        $stmt->execute([$payment_status, $id, $owner_id]);
        
        // If status changed from pending to cancelled (rejected), restore room count
        if ($bk && $bk['status'] === 'pending' && $status === 'cancelled') {
            $inc_stmt = $pdo->prepare("UPDATE kamar SET available_rooms = available_rooms + 1, status = 'available' WHERE id = ?");
            $inc_stmt->execute([$bk['kamar_id']]);
        }
        
        // Send notification to tenant
        $msg = ($status === 'confirmed') 
            ? "Pemesanan Anda untuk " . $booking['room_name'] . " di " . $booking['kost_name'] . " telah DISETUJUI oleh pemilik kost."
            : "Pemesanan Anda untuk " . $booking['room_name'] . " di " . $booking['kost_name'] . " telah DITOLAK/DIBATALKAN oleh pemilik kost.";
        
        $notif_stmt = $pdo->prepare("INSERT INTO notifikasi (user_id, message, is_read) VALUES (?, ?, 0)");
        $notif_stmt->execute([$booking['user_id'], $msg]);
        
        $pdo->commit();
        header("Location: booking_manage.php?id=" . $id . "&success=1");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $error_msg = $e->getMessage();
    }
}

include '../../layouts/header.php';
?>

<div class="container py-2">
    <!-- Breadcrumb & Back button -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="bookings.php">Pesanan Masuk</a></li>
            <li class="breadcrumb-item active" aria-current="page">Kelola Pesanan #<?php echo $id; ?></li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Detail Kelola Pesanan #<?php echo $id; ?></h3>
        <a href="bookings.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i>Kembali</a>
    </div>

    <?php if ($success_msg): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?php echo $success_msg; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($error_msg): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $error_msg; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Left Column: Details of Booking & Tenant -->
        <div class="col-md-7 mb-4">
            <!-- Tenant Information -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-person-badge me-2 text-primary"></i>Informasi Penyewa</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-4 text-muted mb-2">Nama Lengkap</div>
                        <div class="col-sm-8 fw-semibold mb-2"><?php echo htmlspecialchars($booking['tenant_name']); ?></div>
                        
                        <div class="col-sm-4 text-muted mb-2">No. Telepon / WA</div>
                        <div class="col-sm-8 mb-2">
                            <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $booking['tenant_phone']); ?>" target="_blank" class="fw-semibold me-2">
                                <?php echo htmlspecialchars($booking['tenant_phone']); ?> <i class="bi bi-whatsapp ms-1 text-success"></i>
                            </a>
                            <a href="chat.php?receiver_id=<?php echo $booking['user_id']; ?>" class="btn btn-sm btn-primary">
                                <i class="bi bi-chat-dots-fill me-1"></i> Chat Hubungi
                            </a>
                        </div>
                        
                        <div class="col-sm-4 text-muted mb-2">Email</div>
                        <div class="col-sm-8 mb-2"><?php echo htmlspecialchars($booking['tenant_email']); ?></div>
                    </div>
                </div>
            </div>

            <!-- Booking details -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2 text-primary"></i>Detail Sewa Kost</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-4 text-muted mb-3">Nama Kost</div>
                        <div class="col-sm-8 fw-bold mb-3"><?php echo htmlspecialchars($booking['kost_name']); ?></div>
                        
                        <div class="col-sm-4 text-muted mb-3">Alamat Kost</div>
                        <div class="col-sm-8 mb-3 text-muted small"><?php echo htmlspecialchars($booking['kost_address']); ?>, <?php echo htmlspecialchars($booking['kost_city']); ?></div>

                        <div class="col-sm-4 text-muted mb-3">Tipe / Nama Kamar</div>
                        <div class="col-sm-8 fw-semibold mb-3"><?php echo htmlspecialchars($booking['room_name']); ?></div>

                        <div class="col-sm-4 text-muted mb-3">Mulai Sewa</div>
                        <div class="col-sm-8 fw-semibold mb-3"><?php echo date('d F Y', strtotime($booking['start_date'])); ?></div>

                        <div class="col-sm-4 text-muted mb-3">Durasi Sewa</div>
                        <div class="col-sm-8 fw-semibold mb-3"><?php echo htmlspecialchars($booking['duration_months']); ?> Bulan</div>

                        <div class="col-sm-4 text-muted mb-3">Harga per Bulan</div>
                        <div class="col-sm-8 fw-semibold mb-3">Rp <?php echo number_format($booking['price_per_month'], 0, ',', '.'); ?></div>

                        <hr>

                        <div class="col-sm-4 text-muted fw-bold">Total Pembayaran</div>
                        <div class="col-sm-8 fs-5 fw-bold text-primary">Rp <?php echo number_format($booking['total_price'], 0, ',', '.'); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Payment & Action Buttons -->
        <div class="col-md-5 mb-4">
            <!-- Verification Status Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-shield-check me-2 text-primary"></i>Status & Verifikasi</h5>
                </div>
                <div class="card-body text-center py-4">
                    <h6 class="text-muted mb-2">Status Pesanan saat ini:</h6>
                    <div class="mb-4">
                        <span class="badge fs-6 bg-<?php 
                            echo $booking['status'] == 'pending' ? 'warning' : 
                                ($booking['status'] == 'confirmed' ? 'success' : 
                                ($booking['status'] == 'cancelled' ? 'danger' : 'secondary')); 
                        ?>">
                            <?php echo strtoupper($booking['status']); ?>
                        </span>
                    </div>

                    <?php if ($booking['status'] == 'pending'): ?>
                        <?php if ($booking['payment_proof']): ?>
                            <div class="alert alert-warning text-start mb-4">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i> Penyewa sudah mengunggah bukti pembayaran. Silakan periksa bukti transfer di bawah dengan teliti sebelum menerima pesanan.
                            </div>
                            <form method="POST" class="d-flex justify-content-center gap-2">
                                <button type="submit" name="action" value="confirm" class="btn btn-success" onclick="return confirm('Apakah Anda yakin dana pembayaran sudah masuk dan menyetujui pesanan ini?')">
                                    <i class="bi bi-check-circle me-1"></i> Terima Pesanan
                                </button>
                                <button type="submit" name="action" value="cancel" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menolak bukti pembayaran dan membatalkan/menolak pesanan ini?')">
                                    <i class="bi bi-x-circle me-1"></i> Tolak Pesanan
                                </button>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-secondary text-start mb-0">
                                <i class="bi bi-info-circle-fill me-2"></i> Belum ada bukti pembayaran yang diunggah oleh penyewa untuk pesanan ini.
                            </div>
                        <?php endif; ?>
                    <?php elseif ($booking['status'] == 'confirmed'): ?>
                        <div class="alert alert-success text-start mb-0">
                            <i class="bi bi-check-circle-fill me-2"></i> Pesanan ini telah **diterima** dan pembayaran telah **diverifikasi** oleh Anda.
                        </div>
                    <?php elseif ($booking['status'] == 'cancelled'): ?>
                        <div class="alert alert-danger text-start mb-0">
                            <i class="bi bi-x-circle-fill me-2"></i> Pesanan ini telah **ditolak** atau **dibatalkan**.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($booking['status'] == 'confirmed'): ?>
                <!-- Lease Management Card -->
                <div class="card shadow-sm border-0 mb-4 border-start border-4 border-primary">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-key-fill me-2 text-primary"></i>Manajemen Kontrak Sewa</h5>
                    </div>
                    <div class="card-body">
                        <?php 
                        $start_date = $booking['start_date'];
                        $duration = (int)$booking['duration_months'];
                        $checkout_time = strtotime("+$duration months", strtotime($start_date));
                        $checkout_date_formatted = date('d F Y', $checkout_time);
                        $days_remaining = ceil(($checkout_time - time()) / (60 * 60 * 24));
                        ?>
                        <div class="mb-3">
                            <p class="text-muted mb-1 small fw-semibold">Perkiraan Akhir Sewa / Checkout:</p>
                            <h6 class="fw-bold text-dark mb-1"><i class="bi bi-calendar-check text-success me-2"></i><?php echo $checkout_date_formatted; ?></h6>
                            <?php if ($days_remaining > 0): ?>
                                <small class="text-success fw-bold"><i class="bi bi-clock me-1"></i>Sisa <?php echo $days_remaining; ?> hari masa sewa</small>
                            <?php else: ?>
                                <small class="text-danger fw-bold"><i class="bi bi-exclamation-triangle me-1"></i>Masa sewa telah habis (lewat <?php echo abs($days_remaining); ?> hari)</small>
                            <?php endif; ?>
                        </div>
                        <hr>
                        
                        <!-- Actions -->
                        <h6 class="fw-bold mb-3"><i class="bi bi-tools me-2"></i>Aksi Pengelola:</h6>
                        
                        <!-- Checkout Action -->
                        <form method="POST" class="mb-3">
                            <input type="hidden" name="lease_action" value="checkout">
                            <button type="submit" class="btn btn-danger w-100 btn-sm" onclick="return confirm('Apakah Anda yakin ingin menyelesaikan sewa ini? Status pesanan akan diset Selesai (Completed) dan kamar akan dikosongkan/tersedia kembali.')">
                                <i class="bi bi-box-arrow-right me-2"></i>Checkout / Berhenti Sewa
                            </button>
                        </form>
                        
                        <!-- Extend Action -->
                        <form method="POST" class="bg-light p-3 rounded border border-1">
                            <input type="hidden" name="lease_action" value="extend">
                            <label class="form-label mb-2 fw-semibold small">Perpanjang Masa Sewa</label>
                            <div class="input-group mb-2">
                                <select name="extra_months" class="form-select form-select-sm" required>
                                    <option value="1">+1 Bulan</option>
                                    <option value="2">+2 Bulan</option>
                                    <option value="3">+3 Bulan</option>
                                    <option value="6">+6 Bulan</option>
                                    <option value="12">+12 Bulan</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary px-3">
                                    <i class="bi bi-plus-circle me-1"></i> Perpanjang
                                </button>
                            </div>
                            <small class="text-muted" style="font-size: 0.75rem;">Total harga pesanan akan otomatis terakumulasi sesuai harga sewa kamar bulanan.</small>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Payment Proof Card -->
            <?php if ($booking['payment_proof']): ?>
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-image me-2 text-primary"></i>Bukti Pembayaran</h5>
                    </div>
                    <div class="card-body text-center">
                        <p class="text-muted text-start mb-2">Tanggal Unggah: <span class="fw-semibold text-dark"><?php echo date('d M Y H:i', strtotime($booking['payment_date'])); ?></span></p>
                        <a href="../../uploads/payments/<?php echo htmlspecialchars($booking['payment_proof']); ?>" target="_blank">
                            <img src="../../uploads/payments/<?php echo htmlspecialchars($booking['payment_proof']); ?>" class="img-fluid rounded shadow-sm border border-1 border-secondary-subtle" style="max-height: 400px; object-fit: contain;">
                        </a>
                        <small class="text-muted d-block mt-2">Klik gambar untuk memperbesar di tab baru.</small>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../layouts/footer.php'; ?>
