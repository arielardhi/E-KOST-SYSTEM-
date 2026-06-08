<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../modules/auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user profile data
$stmt_user = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt_user->execute([$user_id]);
$user_info = $stmt_user->fetch();

$room_id = $_GET['room_id'] ?? 0;
// Fetch detailed room info, kost info, and main image
$stmt = $pdo->prepare("
    SELECT km.*, k.name as kost_name, k.address, k.city, k.type, k.facilities as kost_facilities,
           (SELECT image_path FROM kost_foto WHERE kost_id = k.id AND is_main = 1 LIMIT 1) as main_image
    FROM kamar km 
    JOIN kost k ON km.kost_id = k.id 
    WHERE km.id = ?
");
$stmt->execute([$room_id]);
$room = $stmt->fetch();

if (!$room) {
    header("Location: kost_list.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Double check available rooms before booking
    if ($room['available_rooms'] <= 0) {
        $error = "Maaf, kamar ini sudah penuh dan tidak dapat dipesan.";
    } else {
        $start_date = $_POST['start_date'];
        $duration = (int)$_POST['duration'];
        
        // Cost calculations (add service fee to database total_price as well)
        $service_fee = 10000; 
        $total_price = ($room['price_per_month'] * $duration) + $service_fee;

        $stmt = $pdo->prepare("INSERT INTO booking (user_id, kamar_id, start_date, duration_months, total_price, status) VALUES (?, ?, ?, ?, ?, 'pending')");
        if ($stmt->execute([$user_id, $room_id, $start_date, $duration, $total_price])) {
            $booking_id = $pdo->lastInsertId();
            
            // Optionally, create a default system notification for the user
            try {
                $notif_stmt = $pdo->prepare("INSERT INTO notifikasi (user_id, message, is_read) VALUES (?, ?, 0)");
                $notif_stmt->execute([$user_id, "Pesanan baru #" . $booking_id . " berhasil dibuat. Silakan selesaikan pembayaran."]);
            } catch (Exception $e) {
                // Fail silently
            }

            header("Location: ../modules/user/booking_detail.php?id=$booking_id&success=1");
            exit();
        } else {
            $error = "Terjadi kesalahan saat memproses pesanan Anda. Silakan coba lagi.";
        }
    }
}

include '../layouts/header.php';
?>

<style>
.booking-header-hero {
    background: linear-gradient(135deg, #1E0D3E 0%, #2D1459 100%);
    border-radius: 16px;
    padding: 2.5rem 2rem;
    color: #ffffff;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(45, 20, 89, 0.15);
}
.booking-header-hero h2 {
    font-weight: 800;
    letter-spacing: -0.02em;
}
.booking-step-badge {
    background: rgba(0, 201, 208, 0.15);
    border: 1px solid rgba(0, 201, 208, 0.3);
    color: #00C9D0;
    padding: 6px 14px;
    border-radius: 99px;
    font-size: 0.8rem;
    font-weight: 700;
}
.booking-card {
    border: 1px solid #EAE4CC !important;
    background-color: #ffffff;
    border-radius: 16px !important;
    box-shadow: 0 8px 24px rgba(45, 20, 89, 0.05) !important;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    overflow: hidden;
}
.booking-card:hover {
    box-shadow: 0 12px 30px rgba(45, 20, 89, 0.08) !important;
}
.booking-card-header {
    background: #FAF6EC !important;
    border-bottom: 1px solid #EAE4CC !important;
    padding: 1.25rem 1.5rem !important;
    font-weight: 800;
    text-transform: uppercase;
    font-size: 0.9rem;
    letter-spacing: 0.03em;
    color: #2D1459 !important;
}
.price-breakdown-box {
    background: #FDFAF2;
    border: 1px dashed #DDD8C4;
    border-radius: 12px;
    padding: 1.25rem;
}
.price-breakdown-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.75rem;
    font-size: 0.9rem;
    font-weight: 600;
    color: #4A3670;
}
.price-breakdown-row:last-child {
    margin-bottom: 0;
}
.price-total-row {
    display: flex;
    justify-content: space-between;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 2px solid #EAE4CC;
}
.room-image-preview {
    height: 180px;
    object-fit: cover;
    width: 100%;
    border-radius: 12px;
    border: 1px solid #EAE4CC;
}
.user-info-pill {
    background-color: #F8F4E3;
    border: 1px solid #EAE4CC;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    font-size: 0.85rem;
    font-weight: 600;
    color: #2D1459;
}
</style>

<div class="container py-4">
    <!-- HERO HEADER -->
    <div class="booking-header-hero d-flex flex-column flex-md-row justify-content-between align-md-items-center gap-3">
        <div>
            <span class="booking-step-badge mb-2 d-inline-block"><i class="bi bi-shield-check me-1"></i> Langkah 2 dari 3: Konfirmasi Pesanan</span>
            <h2>Selesaikan Pesanan Kost Anda</h2>
            <p class="mb-0 opacity-75">Tinjau rincian sewa properti Anda dan tentukan tanggal mulai sewa.</p>
        </div>
        <div class="d-none d-lg-flex align-items-center gap-2">
            <span class="badge bg-success py-2 px-3"><i class="bi bi-1-circle me-1"></i> Pilih Kamar</span>
            <span class="badge bg-primary py-2 px-3"><i class="bi bi-2-circle me-1"></i> Formulir</span>
            <span class="badge bg-secondary py-2 px-3"><i class="bi bi-3-circle me-1"></i> Pembayaran</span>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger shadow-sm mb-4 border-2"><i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $error; ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- DETAIL KOST & USER INFO (LEFT COLUMN) -->
        <div class="col-lg-7">
            <!-- KOST DETAILS -->
            <div class="card booking-card mb-4">
                <div class="booking-card-header">
                    <i class="bi bi-house-door-fill me-2 text-primary"></i> Detail Kamar & Kost
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <img src="<?php echo $room['main_image'] ? $base_url . $room['main_image'] : 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=800&q=80'; ?>" 
                                 class="room-image-preview img-fluid" alt="<?php echo htmlspecialchars($room['kost_name']); ?>">
                        </div>
                        <div class="col-md-7 d-flex flex-column justify-content-between">
                            <div>
                                <span class="badge bg-<?php echo $room['type'] == 'Putra' ? 'primary' : ($room['type'] == 'Putri' ? 'danger' : 'warning'); ?> mb-2">
                                    KOST <?php echo strtoupper($room['type']); ?>
                                </span>
                                <h4 class="fw-bold mb-1" style="color: #2D1459;"><?php echo htmlspecialchars($room['kost_name']); ?></h4>
                                <p class="text-muted small mb-2"><i class="bi bi-geo-alt me-1"></i> <?php echo htmlspecialchars($room['address']); ?>, <?php echo htmlspecialchars($room['city']); ?></p>
                                
                                <div class="bg-light p-2 rounded mb-2 border">
                                    <h6 class="mb-0 fw-bold text-dark" style="font-size:0.9rem;"><?php echo htmlspecialchars($room['room_name']); ?></h6>
                                    <small class="text-muted"><i class="bi bi-aspect-ratio me-1"></i> Ukuran: <?php echo htmlspecialchars($room['size']); ?></small>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="badge bg-light text-dark border border-dark py-2 px-3 fw-bold">
                                    Harga: Rp <?php echo number_format($room['price_per_month'], 0, ',', '.'); ?>/bln
                                </span>
                                <span class="badge bg-<?php echo $room['available_rooms'] > 0 ? 'success-subtle text-success border border-success' : 'danger-subtle text-danger border border-danger'; ?> py-2 px-3 fw-bold">
                                    <i class="bi bi-door-open me-1"></i> Sisa <?php echo $room['available_rooms']; ?> Kamar
                                </span>
                            </div>
                        </div>
                    </div>

                    <?php if ($room['facilities']): ?>
                        <hr class="border-2 my-3">
                        <h6 class="fw-bold mb-2" style="color: #2D1459;">Fasilitas Kamar:</h6>
                        <div class="d-flex flex-wrap gap-2">
                            <?php 
                            $room_facs = explode(',', $room['facilities']);
                            foreach($room_facs as $rf): ?>
                                <span class="badge bg-light text-muted border py-1.5 px-3" style="font-size:0.75rem;"><i class="bi bi-check2-circle text-primary me-1"></i><?php echo trim($rf); ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- USER INFORMATION -->
            <div class="card booking-card">
                <div class="booking-card-header">
                    <i class="bi bi-person-fill me-2 text-primary"></i> Data Penyewa (Profil Anda)
                </div>
                <div class="card-body p-4">
                    <p class="small text-muted mb-3">Berikut adalah profil akun Anda yang akan dikirimkan ke pemilik kost untuk pendaftaran.</p>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="user-info-pill d-flex align-items-center gap-2">
                                <i class="bi bi-person text-primary fs-5"></i>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 0.7rem; text-transform: uppercase;">Nama Lengkap</small>
                                    <span><?php echo htmlspecialchars($user_info['full_name'] ?: $_SESSION['username']); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="user-info-pill d-flex align-items-center gap-2">
                                <i class="bi bi-envelope text-primary fs-5"></i>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 0.7rem; text-transform: uppercase;">Email Aktif</small>
                                    <span><?php echo htmlspecialchars($user_info['email']); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="user-info-pill d-flex align-items-center gap-2">
                                <i class="bi bi-telephone text-primary fs-5"></i>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 0.7rem; text-transform: uppercase;">No Handphone / WhatsApp</small>
                                    <span><?php echo htmlspecialchars($user_info['phone'] ?: 'Belum diatur (Silakan update profil Anda nanti)'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FORM & CALCULATIONS (RIGHT COLUMN) -->
        <div class="col-lg-5">
            <div class="card booking-card">
                <div class="booking-card-header">
                    <i class="bi bi-calendar-range me-2 text-primary"></i> Pengaturan Sewa
                </div>
                <div class="card-body p-4">
                    <?php if ($room['available_rooms'] <= 0): ?>
                        <div class="text-center py-4">
                            <i class="bi bi-slash-circle text-danger fs-1 mb-3"></i>
                            <h5 class="fw-bold text-danger">Kamar Tidak Tersedia</h5>
                            <p class="text-muted small">Maaf, kamar ini saat ini sedang penuh. Silakan cari pilihan kamar atau kost lainnya.</p>
                            <a href="kost_list.php" class="btn btn-outline-dark w-100">Kembali Cari Kost</a>
                        </div>
                    <?php else: ?>
                        <form method="POST" id="bookingForm">
                            <!-- START DATE -->
                            <div class="mb-3">
                                <label class="form-label">Tanggal Mulai Nge-kost</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-calendar-event text-muted"></i></span>
                                    <input type="date" name="start_date" class="form-control border-start-0" required min="<?php echo date('Y-m-d'); ?>" value="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="form-text">Pilih tanggal rencana Anda pertama kali menempati kost.</div>
                            </div>

                            <!-- RENTAL DURATION -->
                            <div class="mb-4">
                                <label class="form-label">Durasi Sewa</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-clock-history text-muted"></i></span>
                                    <select name="duration" class="form-select border-start-0" id="duration" required>
                                        <option value="1" selected>1 Bulan (Sewa Bulanan)</option>
                                        <option value="2">2 Bulan</option>
                                        <option value="3">3 Bulan (Triwulan)</option>
                                        <option value="6">6 Bulan (Setengah Tahun)</option>
                                        <option value="12">12 Bulan (1 Tahun)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- PRICE BREAKDOWN -->
                            <h6 class="fw-bold mb-3" style="color: #2D1459;">Rincian Pembayaran:</h6>
                            <div class="price-breakdown-box mb-4">
                                <div class="price-breakdown-row">
                                    <span>Biaya Sewa / Bulan</span>
                                    <span id="price-per-month-text">Rp <?php echo number_format($room['price_per_month'], 0, ',', '.'); ?></span>
                                </div>
                                <div class="price-breakdown-row">
                                    <span>Durasi Sewa</span>
                                    <span id="duration-display-text">1 Bulan</span>
                                </div>
                                <div class="price-breakdown-row border-bottom pb-2">
                                    <span>Subtotal Sewa</span>
                                    <span id="subtotal-display-text" class="fw-bold">Rp <?php echo number_format($room['price_per_month'], 0, ',', '.'); ?></span>
                                </div>
                                <div class="price-breakdown-row pt-2 text-muted">
                                    <span>Biaya Layanan & Admin</span>
                                    <span>Rp 10.000</span>
                                </div>
                                <div class="price-total-row">
                                    <h6 class="fw-bold mb-0 align-self-center">Total Pembayaran</h6>
                                    <h5 class="fw-bold mb-0 text-primary" id="total-display">Rp <?php echo number_format($room['price_per_month'] + 10000, 0, ',', '.'); ?></h5>
                                </div>
                            </div>

                            <!-- AGREE TO RULES -->
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="agreeRules" required checked>
                                <label class="form-check-label small text-muted" for="agreeRules">Saya menyetujui semua <a href="kost_detail.php?id=<?php echo $room['kost_id']; ?>" target="_blank" class="fw-bold text-primary">peraturan kost</a> yang berlaku.</label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" style="border-radius: 12px;">
                                <i class="bi bi-patch-check-fill"></i> PROSES PESAN SEKARANG <i class="bi bi-chevron-right small"></i>
                            </button>
                            <p class="text-center text-muted small mt-3 mb-0"><i class="bi bi-shield-lock-fill text-success"></i> Transaksi Anda aman dan terlindungi.</p>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const durationSelect = document.getElementById('duration');
    if (durationSelect) {
        durationSelect.addEventListener('change', function() {
            const price = <?php echo $room['price_per_month']; ?>;
            const serviceFee = 10000;
            const duration = parseInt(this.value);
            const subtotal = price * duration;
            const total = subtotal + serviceFee;
            
            // Update UI elements
            document.getElementById('duration-display-text').innerText = duration + ' Bulan';
            document.getElementById('subtotal-display-text').innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
            document.getElementById('total-display').innerText = 'Rp ' + total.toLocaleString('id-ID');
        });
    }
});
</script>

<?php include '../layouts/footer.php'; ?>
