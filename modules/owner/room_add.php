<?php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header("Location: ../auth/login.php");
    exit();
}

$kost_id = $_GET['kost_id'] ?? 0;
$owner_id = $_SESSION['user_id'];

// Verify kost ownership
$stmt = $pdo->prepare("SELECT * FROM kost WHERE id = ? AND owner_id = ?");
$stmt->execute([$kost_id, $owner_id]);
$kost = $stmt->fetch();

if (!$kost) {
    header("Location: kost_manage.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_name = $_POST['room_name'];
    $size = $_POST['size'];
    $price = $_POST['price'];
    $available = $_POST['available'];
    $facilities = $_POST['facilities'];

    $stmt = $pdo->prepare("INSERT INTO kamar (kost_id, room_name, size, price_per_month, available_rooms, facilities, status) VALUES (?, ?, ?, ?, ?, ?, 'available')");
    if ($stmt->execute([$kost_id, $room_name, $size, $price, $available, $facilities])) {
        $success = "Kamar berhasil ditambahkan! <a href='room_manage.php?kost_id=$kost_id'>Lihat daftar kamar</a>";
    } else {
        $error = "Gagal menambahkan kamar.";
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
            </div>
        </div>

        <div class="col-md-9">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h3 class="mb-4">Tambah Kamar Baru</h3>
                    <p class="text-muted">Menambahkan tipe kamar untuk <strong><?php echo $kost['name']; ?></strong></p>
                    
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
                                <input type="text" name="room_name" class="form-control" placeholder="Contoh: Kamar Deluxe A" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Ukuran Kamar</label>
                                <input type="text" name="size" class="form-control" placeholder="Contoh: 3x4 m" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Harga per Bulan (Rp)</label>
                                <input type="number" name="price" class="form-control" placeholder="Contoh: 1500000" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jumlah Kamar Tersedia</label>
                                <input type="number" name="available" class="form-control" value="1" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Fasilitas Kamar (Pisahkan dengan koma)</label>
                            <textarea name="facilities" class="form-control" rows="2" placeholder="Contoh: AC, Kamar Mandi Dalam, Kasur, Lemari"></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="room_manage.php?kost_id=<?php echo $kost_id; ?>" class="btn btn-light me-md-2">Batal</a>
                            <button type="submit" class="btn btn-primary px-5">Simpan Kamar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../layouts/footer.php'; ?>
