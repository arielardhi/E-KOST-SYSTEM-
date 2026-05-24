<?php 
require_once '../config/database.php';
include '../layouts/header.php'; 

$city = $_GET['city'] ?? '';
$type = $_GET['type'] ?? '';
$max_price = $_GET['max_price'] ?? '';

// Check favorites if logged in
$user_favorites = [];
if (isset($_SESSION['user_id'])) {
    $stmt_fav = $pdo->prepare("SELECT kost_id FROM favorit WHERE user_id = ?");
    $stmt_fav->execute([$_SESSION['user_id']]);
    $user_favorites = $stmt_fav->fetchAll(PDO::FETCH_COLUMN);
}

$query = "SELECT k.*, u.full_name as owner_name, 
          (SELECT image_path FROM kost_foto WHERE kost_id = k.id AND is_main = 1 LIMIT 1) as main_image
          FROM kost k 
          JOIN users u ON k.owner_id = u.id 
          WHERE 1=1";

$params = [];
if ($city) {
    $query .= " AND k.city LIKE ?";
    $params[] = "%$city%";
}
if ($type) {
    $query .= " AND k.type = ?";
    $params[] = $type;
}
if ($max_price) {
    $query .= " AND k.price_start <= ?";
    $params[] = $max_price;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$kosts = $stmt->fetchAll();
?>

<div class="container">
    <div class="row">
        <!-- Sidebar Filter -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body p-4">
                    <h5 class="fw-black text-uppercase mb-4">Filter Pencarian</h5>
                    <form method="GET">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kota</label>
                            <input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars($city); ?>" placeholder="Semua Kota">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tipe Kost</label>
                            <select name="type" class="form-select">
                                <option value="">Semua Tipe</option>
                                <option value="Putra" <?php echo $type == 'Putra' ? 'selected' : ''; ?>>Putra</option>
                                <option value="Putri" <?php echo $type == 'Putri' ? 'selected' : ''; ?>>Putri</option>
                                <option value="Campur" <?php echo $type == 'Campur' ? 'selected' : ''; ?>>Campur</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Harga Maksimal</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="max_price" class="form-control" value="<?php echo htmlspecialchars($max_price); ?>" placeholder="Contoh: 1000000">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">TERAPKAN FILTER</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-black text-uppercase mb-0">Hasil Pencarian</h2>
                <span class="badge bg-dark border border-2 border-dark py-2 px-3"><?php echo count($kosts); ?> Kost ditemukan</span>
            </div>

            <div class="row g-4">
                <?php if (empty($kosts)): ?>
                    <div class="col-12 text-center py-5">
                        <div class="mb-4">
                            <i class="bi bi-search display-1 text-muted"></i>
                        </div>
                        <h4 class="fw-black text-uppercase">Kost Tidak Ditemukan</h4>
                        <p class="text-muted">Maaf, tidak ada kost yang sesuai dengan kriteria Anda.</p>
                        <a href="kost_list.php" class="btn btn-outline-dark px-4">RESET FILTER</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($kosts as $kost): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 kost-card">
                                <div class="position-relative">
                                    <img src="<?php echo $kost['main_image'] ? $base_url . $kost['main_image'] : 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=800&q=80'; ?>" 
                                         class="card-img-top" alt="<?php echo $kost['name']; ?>" style="height: 200px; object-fit: cover;">
                                    
                                    <!-- Favorite Indicator -->
                                    <?php if (in_array($kost['id'], $user_favorites)): ?>
                                        <div class="position-absolute top-0 start-0 m-2">
                                            <span class="badge bg-white border border-2 border-dark p-2">
                                                <i class="bi bi-heart-fill text-danger fs-5"></i>
                                            </span>
                                        </div>
                                    <?php endif; ?>

                                    <span class="badge bg-<?php echo $kost['type'] == 'Putra' ? 'primary' : ($kost['type'] == 'Putri' ? 'danger' : 'warning'); ?> position-absolute top-0 end-0 m-2 border border-2 border-dark shadow-sm">
                                        <?php echo strtoupper($kost['type']); ?>
                                    </span>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title fw-black text-uppercase text-truncate mb-1"><?php echo $kost['name']; ?></h5>
                                    <p class="text-muted small mb-3"><i class="bi bi-geo-alt"></i> <?php echo $kost['city']; ?></p>
                                    <div class="mb-3">
                                        <div class="fw-black" style="font-size: 1.1rem;">Rp <?php echo number_format($kost['price_start'], 0, ',', '.'); ?> <small class="text-muted fw-normal">/ bulan</small></div>
                                    </div>
                                    <a href="kost_detail.php?id=<?php echo $kost['id']; ?>" class="btn btn-primary w-100 py-2 fw-bold">LIHAT DETAIL</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../layouts/footer.php'; ?>
