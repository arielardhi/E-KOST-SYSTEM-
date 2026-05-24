<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../modules/auth/login.php");
    exit();
}

$room_id = $_GET['room_id'] ?? 0;
$stmt = $pdo->prepare("SELECT km.*, k.name as kost_name FROM kamar km JOIN kost k ON km.kost_id = k.id WHERE km.id = ?");
$stmt->execute([$room_id]);
$room = $stmt->fetch();

if (!$room) {
    header("Location: kost_list.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $start_date = $_POST['start_date'];
    $duration = $_POST['duration'];
    $total_price = $room['price_per_month'] * $duration;

    $stmt = $pdo->prepare("INSERT INTO booking (user_id, kamar_id, start_date, duration_months, total_price, status) VALUES (?, ?, ?, ?, ?, 'pending')");
    if ($stmt->execute([$user_id, $room_id, $start_date, $duration, $total_price])) {
        $booking_id = $pdo->lastInsertId();
        header("Location: ../modules/user/booking_detail.php?id=$booking_id&success=1");
        exit();
    } else {
        $error = "Terjadi kesalahan saat memproses pesanan.";
    }
}

include '../layouts/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h3 class="fw-bold mb-4">Konfirmasi Pesanan</h3>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6><?php echo $room['kost_name']; ?></h6>
                        <p class="mb-0 text-muted"><?php echo $room['room_name']; ?> - Rp <?php echo number_format($room['price_per_month'], 0, ',', '.'); ?>/bulan</p>
                    </div>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Tanggal Mulai Nge-kost</label>
                            <input type="date" name="start_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Durasi Sewa (Bulan)</label>
                            <select name="duration" class="form-select" id="duration" required>
                                <option value="1">1 Bulan</option>
                                <option value="3">3 Bulan</option>
                                <option value="6">6 Bulan</option>
                                <option value="12">12 Bulan</option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-4 p-3 border-top">
                            <h5 class="mb-0">Total Pembayaran</h5>
                            <h4 class="mb-0 text-primary fw-bold" id="total-display">Rp <?php echo number_format($room['price_per_month'], 0, ',', '.'); ?></h4>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mt-4 py-2">Pesan Sekarang</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('duration').addEventListener('change', function() {
    const price = <?php echo $room['price_per_month']; ?>;
    const duration = this.value;
    const total = price * duration;
    document.getElementById('total-display').innerText = 'Rp ' + total.toLocaleString('id-ID');
});
</script>

<?php include '../layouts/footer.php'; ?>
