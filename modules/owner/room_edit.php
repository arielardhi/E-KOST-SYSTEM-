<?php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header("Location: ../auth/login.php");
    exit();
}

$room_id = $_GET['id'] ?? 0;
$owner_id = $_SESSION['user_id'];

// Verify room ownership by joining with kost
$stmt = $pdo->prepare("
    SELECT km.*, k.name as kost_name, k.owner_id 
    FROM kamar km 
    JOIN kost k ON km.kost_id = k.id 
    WHERE km.id = ? AND k.owner_id = ?
");
$stmt->execute([$room_id, $owner_id]);
$room = $stmt->fetch();

if (!$room) {
    header("Location: kost_manage.php");
    exit();
}

$kost_id = $room['kost_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_name = $_POST['room_name'];
    $size = $_POST['size'];
    $price = $_POST['price'];
    $available = (int)$_POST['available'];
    $facilities = $_POST['facilities'];
    $status = $_POST['status'];

    // Synchronize available rooms and status logically
    if ($status == 'full') {
        $available = 0;
    } elseif ($status == 'available' && $available <= 0) {
        $available = 1;
    }
    
    if ($available <= 0) {
        $status = 'full';
    }

    $stmt = $pdo->prepare("
        UPDATE kamar 
        SET room_name = ?, size = ?, price_per_month = ?, available_rooms = ?, facilities = ?, status = ? 
        WHERE id = ?
    ");
    if ($stmt->execute([$room_name, $size, $price, $available, $facilities, $status, $room_id])) {
        $success = "Data kamar berhasil diperbarui! <a href='room_manage.php?kost_id=$kost_id'>Lihat daftar kamar</a>";
        
        // Refresh room details
        $stmt = $pdo->prepare("
            SELECT km.*, k.name as kost_name, k.owner_id 
            FROM kamar km 
            JOIN kost k ON km.kost_id = k.id 
            WHERE km.id = ? AND k.owner_id = ?
        ");
        $stmt->execute([$room_id, $owner_id]);
        $room = $stmt->fetch();
    } else {
        $error = "Gagal memperbarui data kamar.";
    }
}

include '../../layouts/header.php';
?>

<div class="container">
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="list-group shadow-sm border-0">
                <a href="dashboard.php" class="list-group-item list-group-item-action"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                <a href="kost_manage.php" class="list-group-item list-group-item-action active"><i class="bi bi-house me-2"></i> Kelola Kost</a>
                <a href="bookings.php" class="list-group-item list-group-item-action"><i class="bi bi-calendar-check me-2"></i> Pesanan Masuk</a>
            </div>
        </div>

        <div class="col-md-9">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="kost_manage.php">Kelola Kost</a></li>
                    <li class="breadcrumb-item"><a href="room_manage.php?kost_id=<?php echo $kost_id; ?>"><?php echo htmlspecialchars($room['kost_name']); ?></a></li>
                    <li class="breadcrumb-item active">Edit Kamar</li>
                </ol>
            </nav>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h3 class="mb-4">Edit Kamar</h3>
                    <p class="text-muted">Mengubah tipe kamar untuk kost <strong><?php echo htmlspecialchars($room['kost_name']); ?></strong></p>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Nama/Tipe Kamar</label>
                                <input type="text" name="room_name" class="form-control" value="<?php echo htmlspecialchars($room['room_name']); ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Ukuran Kamar</label>
                                <input type="text" name="size" class="form-control" value="<?php echo htmlspecialchars($room['size']); ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Harga per Bulan (Rp)</label>
                                <input type="number" name="price" class="form-control" value="<?php echo htmlspecialchars($room['price_per_month']); ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Jumlah Kamar Tersedia</label>
                                <input type="number" name="available" class="form-control" value="<?php echo htmlspecialchars($room['available_rooms']); ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Status Kamar</label>
                                <select name="status" class="form-select" required>
                                    <option value="available" <?php echo $room['status'] == 'available' ? 'selected' : ''; ?>>Available</option>
                                    <option value="full" <?php echo $room['status'] == 'full' ? 'selected' : ''; ?>>Full</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Fasilitas Kamar (Pisahkan dengan koma)</label>
                            <textarea name="facilities" class="form-control" rows="2"><?php echo htmlspecialchars($room['facilities']); ?></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="room_manage.php?kost_id=<?php echo $kost_id; ?>" class="btn btn-light me-md-2">Batal</a>
                            <button type="submit" class="btn btn-primary px-5">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../layouts/footer.php'; ?>
