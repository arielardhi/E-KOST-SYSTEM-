<?php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header("Location: ../auth/login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM kost WHERE owner_id = ? ORDER BY created_at DESC");
$stmt->execute([$owner_id]);
$kosts = $stmt->fetchAll();

include '../../layouts/header.php';
?>

<div class="container">
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="list-group shadow-sm border-0">
                <a href="dashboard.php" class="list-group-item list-group-item-action"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                <a href="kost_manage.php" class="list-group-item list-group-item-action active"><i class="bi bi-house me-2"></i> Kelola Kost</a>
                <a href="bookings.php" class="list-group-item list-group-item-action"><i class="bi bi-calendar-check me-2"></i> Pesanan Masuk</a>
                <a href="chat.php" class="list-group-item list-group-item-action"><i class="bi bi-chat-dots me-2"></i> Chat</a>
            </div>
        </div>

        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3>Kelola Kost Saya</h3>
                <a href="kost_add.php" class="btn btn-primary">Tambah Kost</a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Kost</th>
                                    <th>Lokasi</th>
                                    <th>Tipe</th>
                                    <th>Kamar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($kosts)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4">Belum ada kost yang didaftarkan.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($kosts as $kost): 
                                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM kamar WHERE kost_id = ?");
                                        $stmt->execute([$kost['id']]);
                                        $room_count = $stmt->fetchColumn();
                                    ?>
                                        <tr>
                                            <td><strong><?php echo $kost['name']; ?></strong></td>
                                            <td><?php echo $kost['city']; ?></td>
                                            <td><span class="badge bg-info"><?php echo $kost['type']; ?></span></td>
                                            <td><?php echo $room_count; ?> Kamar</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="kost_edit.php?id=<?php echo $kost['id']; ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                                                    <a href="room_manage.php?kost_id=<?php echo $kost['id']; ?>" class="btn btn-sm btn-outline-primary">Kamar</a>
                                                    <a href="kost_delete.php?id=<?php echo $kost['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus kost ini?')">Hapus</a>
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
