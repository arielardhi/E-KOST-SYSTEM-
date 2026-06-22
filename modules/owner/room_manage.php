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

// Get rooms
$stmt = $pdo->prepare("SELECT * FROM kamar WHERE kost_id = ?");
$stmt->execute([$kost_id]);
$rooms = $stmt->fetchAll();

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
                    <li class="breadcrumb-item active"><?php echo $kost['name']; ?></li>
                    <li class="breadcrumb-item active">Kelola Kamar</li>
                </ol>
            </nav>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3>Kamar di <?php echo $kost['name']; ?></h3>
                <a href="room_add.php?kost_id=<?php echo $kost_id; ?>" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Kamar</a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Kamar</th>
                                    <th>Ukuran</th>
                                    <th>Harga / Bulan</th>
                                    <th>Sisa Kamar</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($rooms)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">Belum ada kamar yang ditambahkan.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($rooms as $room): ?>
                                        <tr>
                                            <td><strong><?php echo $room['room_name']; ?></strong></td>
                                            <td><?php echo $room['size']; ?></td>
                                            <td>Rp <?php echo number_format($room['price_per_month'], 0, ',', '.'); ?></td>
                                            <td><?php echo $room['available_rooms']; ?></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $room['status'] == 'available' ? 'success' : 
                                                        ($room['status'] == 'maintenance' ? 'warning' : 'danger'); 
                                                ?>">
                                                    <?php echo ucfirst($room['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="room_edit.php?id=<?php echo $room['id']; ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                                                    <a href="room_delete.php?id=<?php echo $room['id']; ?>&kost_id=<?php echo $kost_id; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus kamar ini?')">Hapus</a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../layouts/footer.php'; ?>
