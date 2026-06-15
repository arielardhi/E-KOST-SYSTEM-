<?php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Get all kost with owner info
$stmt = $pdo->query("SELECT k.*, u.full_name as owner_name FROM kost k JOIN users u ON k.owner_id = u.id ORDER BY k.created_at DESC");
$kosts = $stmt->fetchAll();

include '../../layouts/header.php';
?>

<!-- Load SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-black text-uppercase mb-0">Manajemen Properti Kost</h2>
        <a href="kost_verify.php" class="btn btn-warning">VERIFIKASI KOST BARU</a>
    </div>

    <!-- Success Message Alert -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success border border-3 border-dark rounded-0 fw-bold shadow-sm mb-4">
            <i class="bi bi-check-circle-fill me-2"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Nama Kost</th>
                            <th>Pemilik</th>
                            <th>Tipe</th>
                            <th>Kota</th>
                            <th>Harga Mulai</th>
                            <th>Tgl Daftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($kosts as $k): ?>
                            <tr>
                                <td class="fw-bold"><?php echo $k['name']; ?></td>
                                <td><?php echo $k['owner_name']; ?></td>
                                <td><span class="badge bg-info text-dark"><?php echo $k['type']; ?></span></td>
                                <td><?php echo $k['city']; ?></td>
                                <td class="fw-bold">Rp <?php echo number_format($k['price_start'], 0, ',', '.'); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($k['created_at'])); ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="../../pages/kost_detail.php?id=<?php echo $k['id']; ?>" class="btn btn-primary btn-sm me-1" target="_blank"><i class="bi bi-eye"></i></a>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $k['id']; ?>, '<?php echo htmlspecialchars($k['name'], ENT_QUOTES); ?>')"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    Swal.fire({
        title: `Hapus "${name}"?`,
        text: "Seluruh data kamar, foto, ulasan, dan pesanan terkait kost ini akan terhapus secara permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#5C4D78',
        confirmButtonText: '<i class="bi bi-trash me-1"></i>Ya, Hapus!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `kost_delete.php?id=${id}`;
        }
    });
}
</script>

<?php include '../../layouts/footer.php'; ?>
