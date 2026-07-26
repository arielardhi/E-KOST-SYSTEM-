<?php
require_once '../config/database.php';
include '../layouts/header.php';

// Get filter params
$city       = trim($_GET['city'] ?? '');
$type       = $_GET['type'] ?? '';
$min_price  = (int)($_GET['min_price'] ?? 0);
$max_price  = (int)($_GET['max_price'] ?? 0);
$facilities = $_GET['facilities'] ?? [];
$status     = $_GET['room_status'] ?? '';

// Get unique cities
$cities = $pdo->query("SELECT DISTINCT city FROM kost ORDER BY city")->fetchAll(PDO::FETCH_COLUMN);

// Count active filters
$active_filters = 0;
if ($city) $active_filters++;
if ($type) $active_filters++;
if ($min_price) $active_filters++;
if ($max_price) $active_filters++;
if (!empty($facilities)) $active_filters += count($facilities);
if ($status) $active_filters++;

// Check favorites if logged in
$user_favorites = [];
if (isset($_SESSION['user_id'])) {
    $stmt_fav = $pdo->prepare("SELECT kost_id FROM favorit WHERE user_id = ?");
    $stmt_fav->execute([$_SESSION['user_id']]);
    $user_favorites = $stmt_fav->fetchAll(PDO::FETCH_COLUMN);
}

$query  = "SELECT k.*, u.full_name as owner_name,
           (SELECT image_path FROM kost_foto WHERE kost_id = k.id AND is_main = 1 LIMIT 1) as main_image,
           (SELECT COALESCE(SUM(available_rooms), 0) FROM kamar WHERE kost_id = k.id) as total_available_rooms
           FROM kost k
           JOIN users u ON k.owner_id = u.id
           WHERE 1=1";
$params = [];
if ($city)  { $query .= " AND k.city LIKE ?";       $params[] = "%$city%"; }
if ($type)  { $query .= " AND k.type = ?";           $params[] = $type; }
if ($min_price) { $query .= " AND k.price_start >= ?"; $params[] = $min_price; }
if ($max_price) { $query .= " AND k.price_start <= ?"; $params[] = $max_price; }
if (!empty($facilities)) {
    foreach ($facilities as $f) {
        $query .= " AND k.facilities LIKE ?";
        $params[] = "%$f%";
    }
}
$query .= " ORDER BY k.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$kosts = $stmt->fetchAll();

// Filter by room status (post-query for demo)
if ($status === 'available') {
    $kosts = array_filter($kosts, function($k) use ($pdo) {
        $c = $pdo->prepare("SELECT COUNT(*) FROM kamar WHERE kost_id = ? AND status = 'available'");
        $c->execute([$k['id']]);
        return (int)$c->fetchColumn() > 0;
    });
} elseif ($status === 'full') {
    $kosts = array_filter($kosts, function($k) use ($pdo) {
        $c = $pdo->prepare("SELECT COUNT(*) FROM kamar WHERE kost_id = ? AND status = 'available'");
        $c->execute([$k['id']]);
        return (int)$c->fetchColumn() === 0;
    });
}

$all_facilities = ['WiFi','AC','Parkir Motor','Parkir Mobil','Dapur Bersama','Kamar Mandi Dalam','Laundry','CCTV','Gym','Rooftop','Kulkas','TV'];
?>

<style>
/* Filter sidebar */
.filter-sidebar { position: sticky; top: 90px; }
.filter-section-title { font-size:.78rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);margin-bottom:10px;padding-bottom:6px;border-bottom:1px solid var(--border-color); }
.filter-active-badge { display:inline-flex;align-items:center;justify-content:center;width:20px;height:20px;border-radius:50%;background:var(--primary);color:#fff;font-size:.7rem;font-weight:800;margin-left:6px;flex-shrink:0; }
.price-range { position:relative;padding:4px 0; }
.range-input { width:100%;-webkit-appearance:none;height:4px;border-radius:99px;background:var(--border-color);outline:none;cursor:pointer; }
.range-input::-webkit-slider-thumb { -webkit-appearance:none;width:18px;height:18px;border-radius:50%;background:var(--primary);cursor:pointer;border:3px solid #fff;box-shadow:0 2px 6px rgba(0,180,186,.4); }
.price-labels { display:flex;justify-content:space-between;font-size:.75rem;font-weight:700;color:var(--text-muted);margin-top:6px; }
.facility-check { display:flex;align-items:center;gap:8px;padding:5px 0;cursor:pointer; }
.facility-check input { flex-shrink:0;accent-color:var(--primary); }
.facility-check span { font-size:.85rem;font-weight:600;color:var(--text-muted); }

/* Kost card */
.kost-card { transition:all .25s ease; border:none; }
.kost-card:hover { transform:translateY(-5px); box-shadow: 0 20px 40px rgba(30,13,62,.15) !important; }
.kost-card .card-img-wrap { overflow:hidden;position:relative; }
.kost-card .card-img-wrap img { transition:transform .4s ease; height:200px;object-fit:cover;width:100%; }
.kost-card:hover .card-img-wrap img { transform:scale(1.05); }
.kost-type-badge { position:absolute;top:10px;right:10px;padding:.35em .9em;border-radius:99px;font-weight:700;font-size:.72rem;backdrop-filter:blur(4px); }
.kost-fav-badge { position:absolute;top:10px;left:10px; }
.empty-state { padding:60px 20px;text-align:center; }
.filter-chip { display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:99px;background:rgba(0,180,186,.1);border:1px solid rgba(0,180,186,.25);color:var(--primary);font-size:.78rem;font-weight:700;margin:3px; }
.sort-select { padding:8px 12px;border:1.5px solid var(--border-color);border-radius:8px;font-size:.875rem;background:#fff;color:var(--dark);font-family:inherit;font-weight:600;cursor:pointer; }
</style>

<div class="container py-4">
    <div class="row g-4">
        <!-- FILTER SIDEBAR -->
        <div class="col-lg-3">
            <!-- Mobile Toggle Button (Visible only on mobile) -->
            <button class="btn btn-primary d-lg-none w-100 mb-3 text-white fw-bold" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas" aria-controls="filterOffcanvas">
                <i class="bi bi-funnel-fill me-2"></i>Filter Pencarian
            </button>

            <!-- Offcanvas Sidebar Wrapper -->
            <div class="offcanvas-lg offcanvas-start" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
                <div class="offcanvas-header bg-light border-bottom d-lg-none">
                    <h5 class="offcanvas-title fw-bold" id="filterOffcanvasLabel"><i class="bi bi-funnel-fill me-2" style="color:var(--primary)"></i>Filter Pencarian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#filterOffcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body p-0">
                    <div class="card filter-sidebar w-100">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <span class="fw-700">
                                <i class="bi bi-funnel-fill me-2" style="color:var(--primary)"></i>Filter
                                <?php if ($active_filters > 0): ?>
                                <span class="filter-active-badge"><?= $active_filters ?></span>
                                <?php endif; ?>
                            </span>
                            <?php if ($active_filters > 0): ?>
                            <a href="kost_list.php" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:.75rem">Reset</a>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <form method="GET" id="filterForm">
                                <!-- Lokasi -->
                                <div class="mb-4">
                                    <div class="filter-section-title"><i class="bi bi-geo-alt me-1"></i>Lokasi / Kota</div>
                                    <select name="city" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option value="">Semua Kota</option>
                                        <?php foreach ($cities as $c): ?>
                                        <option value="<?= $c ?>" <?= $city == $c ? 'selected' : '' ?>><?= $c ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Tipe Kost -->
                                <div class="mb-4">
                                    <div class="filter-section-title"><i class="bi bi-people me-1"></i>Jenis Kost</div>
                                    <div class="d-flex flex-column gap-1">
                                        <?php foreach ([''=>'Semua','Putra'=>'Putra','Putri'=>'Putri','Campur'=>'Campur'] as $val => $label): ?>
                                        <label class="facility-check">
                                            <input type="radio" name="type" value="<?= $val ?>" <?= $type === $val ? 'checked' : '' ?> onchange="this.form.submit()">
                                            <span><?= $label ?></span>
                                        </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <!-- Harga Range -->
                                <div class="mb-4">
                                    <div class="filter-section-title"><i class="bi bi-cash me-1"></i>Rentang Harga</div>
                                    <div class="mb-2">
                                        <label class="form-label" style="font-size:.78rem;font-weight:600;color:var(--text-muted)">Min: <strong style="color:var(--primary)" id="minLabel">Rp <?= number_format($min_price,0,',','.') ?></strong></label>
                                        <input type="range" name="min_price" class="range-input" min="0" max="5000000" step="100000" value="<?= $min_price ?>" id="minRange" oninput="document.getElementById('minLabel').textContent='Rp '+Number(this.value).toLocaleString('id-ID')">
                                    </div>
                                    <div>
                                        <label class="form-label" style="font-size:.78rem;font-weight:600;color:var(--text-muted)">Max: <strong style="color:var(--primary)" id="maxLabel">Rp <?= $max_price ? number_format($max_price,0,',','.') : 'Semua' ?></strong></label>
                                        <input type="range" name="max_price" class="range-input" min="0" max="10000000" step="100000" value="<?= $max_price ?: 10000000 ?>" id="maxRange" oninput="document.getElementById('maxLabel').textContent=this.value>=10000000?'Semua':'Rp '+Number(this.value).toLocaleString('id-ID')">
                                    </div>
                                </div>

                                <!-- Fasilitas -->
                                <div class="mb-4">
                                    <div class="filter-section-title"><i class="bi bi-stars me-1"></i>Fasilitas</div>
                                    <div style="max-height:200px;overflow-y:auto">
                                        <?php foreach ($all_facilities as $f): ?>
                                        <label class="facility-check">
                                            <input type="checkbox" name="facilities[]" value="<?= $f ?>" <?= in_array($f, $facilities) ? 'checked' : '' ?>>
                                            <span><?= $f ?></span>
                                        </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <!-- Status Kamar -->
                                <div class="mb-4">
                                    <div class="filter-section-title"><i class="bi bi-door-open me-1"></i>Status Kamar</div>
                                    <div class="d-flex flex-column gap-1">
                                        <?php foreach ([''=>'Semua','available'=>'Ada Kamar Kosong','full'=>'Penuh'] as $val => $label): ?>
                                        <label class="facility-check">
                                            <input type="radio" name="room_status" value="<?= $val ?>" <?= $status === $val ? 'checked' : '' ?>>
                                            <span><?= $label ?></span>
                                        </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary fw-700 flex-grow-1 text-white"><i class="bi bi-search me-1"></i>Cari</button>
                                    <a href="kost_list.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-counterclockwise"></i></a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div class="col-lg-9">
            <!-- Active Filter Chips -->
            <?php if ($active_filters > 0): ?>
            <div class="mb-3 d-flex flex-wrap gap-1 align-items-center">
                <span class="text-muted small fw-600 me-1">Filter aktif:</span>
                <?php if ($city): ?><div class="filter-chip"><i class="bi bi-geo-alt"></i><?= htmlspecialchars($city) ?></div><?php endif; ?>
                <?php if ($type): ?><div class="filter-chip"><i class="bi bi-people"></i><?= htmlspecialchars($type) ?></div><?php endif; ?>
                <?php if ($min_price): ?><div class="filter-chip"><i class="bi bi-arrow-up"></i>Min Rp <?= number_format($min_price,0,',','.') ?></div><?php endif; ?>
                <?php if ($max_price): ?><div class="filter-chip"><i class="bi bi-arrow-down"></i>Max Rp <?= number_format($max_price,0,',','.') ?></div><?php endif; ?>
                <?php foreach ($facilities as $f): ?><div class="filter-chip"><i class="bi bi-check"></i><?= htmlspecialchars($f) ?></div><?php endforeach; ?>
                <?php if ($status): ?><div class="filter-chip"><i class="bi bi-door-open"></i><?= $status==='available'?'Ada Kamar':'Penuh' ?></div><?php endif; ?>
                <a href="kost_list.php" class="btn btn-sm btn-outline-danger py-0 ms-2 fw-600" style="font-size:.75rem"><i class="bi bi-x"></i> Hapus Semua</a>
            </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold mb-0" style="color:var(--dark)">
                    Hasil Pencarian
                    <span class="badge ms-2" style="background:var(--primary);font-size:.75rem;vertical-align:middle"><?= count($kosts) ?> kost</span>
                </h2>
                <select class="sort-select" onchange="sortKost(this.value)">
                    <option value="newest">Terbaru</option>
                    <option value="price_asc">Harga Termurah</option>
                    <option value="price_desc">Harga Termahal</option>
                </select>
            </div>

            <?php if (empty($kosts)): ?>
            <div class="card empty-state">
                <i class="bi bi-search display-2 mb-3" style="color:var(--border-color)"></i>
                <h4 class="fw-700">Kost Tidak Ditemukan</h4>
                <p class="text-muted mb-4">Tidak ada kost yang sesuai dengan filter yang dipilih. Coba sesuaikan filter pencarian Anda.</p>
                <a href="kost_list.php" class="btn btn-primary px-4"><i class="bi bi-arrow-counterclockwise me-2"></i>Reset Semua Filter</a>
            </div>
            <?php else: ?>
            <div class="row g-4" id="kostGrid">
                <?php foreach ($kosts as $kost): ?>
                <div class="col-sm-6 col-xl-4 kost-item" data-price="<?= $kost['price_start'] ?>">
                    <div class="card h-100 kost-card shadow-sm">
                        <div class="card-img-wrap">
                            <img src="<?= $kost['main_image'] ? $base_url . $kost['main_image'] : 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=800&q=80' ?>"
                                 alt="<?= htmlspecialchars($kost['name']) ?>">
                            <!-- Fav -->
                            <?php if (in_array($kost['id'], $user_favorites)): ?>
                            <div class="kost-fav-badge">
                                <span class="badge bg-white text-danger border shadow-sm"><i class="bi bi-heart-fill"></i></span>
                            </div>
                            <?php endif; ?>
                            <!-- Type -->
                            <span class="kost-type-badge <?= $kost['type']==='Putra'?'bg-primary text-white':($kost['type']==='Putri'?'bg-danger text-white':'bg-warning text-dark') ?>">
                                <?= strtoupper($kost['type']) ?>
                            </span>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-700 text-truncate mb-1"><?= htmlspecialchars($kost['name']) ?></h5>
                            <p class="text-muted small mb-2">
                                <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($kost['city']) ?>
                                <span class="ms-2 badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size: 0.7rem;"><i class="bi bi-door-open-fill"></i> Sisa <?= $kost['total_available_rooms'] ?> kamar</span>
                            </p>
                            <?php if ($kost['facilities']): ?>
                            <div class="mb-2 d-flex flex-wrap gap-1">
                                <?php foreach (array_slice(explode(',', $kost['facilities']), 0, 3) as $f): ?>
                                <span style="font-size:.7rem;font-weight:600;padding:2px 8px;border-radius:99px;background:rgba(0,180,186,.08);color:var(--primary)"><?= trim($f) ?></span>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                            <div class="mt-auto">
                                <div class="fw-800 mb-2" style="font-size:1.1rem;color:var(--dark)">
                                    Rp <?= number_format($kost['price_start'],0,',','.') ?>
                                    <span class="text-muted fw-400" style="font-size:.8rem"> / bulan</span>
                                </div>
                                <a href="kost_detail.php?id=<?= $kost['id'] ?>" class="btn btn-primary w-100 fw-700">
                                    <i class="bi bi-eye me-2"></i>Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function sortKost(val) {
    const grid = document.getElementById('kostGrid');
    if (!grid) return;
    const items = Array.from(grid.querySelectorAll('.kost-item'));
    items.sort((a, b) => {
        const pa = parseFloat(a.dataset.price);
        const pb = parseFloat(b.dataset.price);
        if (val === 'price_asc') return pa - pb;
        if (val === 'price_desc') return pb - pa;
        return 0;
    });
    items.forEach(i => grid.appendChild(i));
}

// Update range input hidden value on submit
document.getElementById('filterForm').addEventListener('submit', function() {
    const maxVal = document.getElementById('maxRange').value;
    if (parseInt(maxVal) >= 10000000) document.getElementById('maxRange').value = '';
});
</script>

<?php include '../layouts/footer.php'; ?>
