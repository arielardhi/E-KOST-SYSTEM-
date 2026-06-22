<?php
require_once '../../config/database.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header("Location: ../auth/login.php"); exit();
}
$owner_id = $_SESSION['user_id'];

// Handle status updates (toggle maintenance)
if (isset($_GET['action']) && isset($_GET['room_id'])) {
    $r_id = (int)$_GET['room_id'];
    $act = $_GET['action'];
    
    // Verify room ownership
    $check = $pdo->prepare("
        SELECT km.id, km.status, km.kost_id 
        FROM kamar km 
        JOIN kost k ON km.kost_id = k.id 
        WHERE km.id = ? AND k.owner_id = ?
    ");
    $check->execute([$r_id, $owner_id]);
    $room_to_update = $check->fetch();
    
    if ($room_to_update) {
        $new_status = ($act === 'maintenance') ? 'maintenance' : 'available';
        
        $update_stmt = $pdo->prepare("UPDATE kamar SET status = ? WHERE id = ?");
        $update_stmt->execute([$new_status, $r_id]);
        
        header("Location: room_status.php?kost_id=" . $room_to_update['kost_id'] . "&success_status=1");
        exit();
    }
}

// Get owner's kosts
$kosts = $pdo->prepare("SELECT id, name, city FROM kost WHERE owner_id = ? ORDER BY name");
$kosts->execute([$owner_id]); $kosts = $kosts->fetchAll();

$filter_kost = (int)($_GET['kost_id'] ?? ($kosts[0]['id'] ?? 0));

// Get rooms
$rooms = [];
if ($filter_kost) {
    $stmt = $pdo->prepare("
        SELECT km.*,
               (SELECT COUNT(*) FROM booking b WHERE b.kamar_id = km.id AND b.status IN ('pending','confirmed')) as active_bookings
        FROM kamar km WHERE km.kost_id = ?
        ORDER BY km.room_name
    ");
    $stmt->execute([$filter_kost]);
    $rooms = $stmt->fetchAll();
}

function get_room_status($room) {
    if ($room['status'] === 'maintenance') return 'maintenance';
    if ($room['status'] === 'full' || $room['available_rooms'] <= 0) return 'full';
    return 'available';
}

$count_available    = 0;
$count_full         = 0;
$count_maintenance  = 0;
foreach ($rooms as $r) {
    $st = get_room_status($r);
    if ($st === 'available') $count_available++;
    elseif ($st === 'full') $count_full++;
    else $count_maintenance++;
}

$success_msg = '';
if (isset($_GET['success_status'])) {
    $success_msg = "Status kamar berhasil diperbarui.";
}

include '../../layouts/header.php';
?>

<style>
.room-card { border-radius: 14px; transition: all .22s; cursor: default; }
.room-card:hover { transform: translateY(-4px); box-shadow: var(--box-shadow-hover) !important; }
.room-card.available  { border-top: 4px solid #059669; }
.room-card.full       { border-top: 4px solid #DC2626; }
.room-card.maintenance { border-top: 4px solid #D97706; }
.status-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; flex-shrink: 0; }
.room-stat-pill {
    padding: 12px 20px; border-radius: 12px;
    display: flex; align-items: center; gap: 12px; flex: 1;
}
.stat-num { font-size: 2rem; font-weight: 800; line-height: 1; }
.filter-kost-btn { padding:8px 20px;border-radius:99px;font-weight:600;font-size:.875rem;border:1.5px solid var(--border-color);background:#fff;color:var(--text-muted);cursor:pointer;transition:all .15s;text-decoration:none;white-space:nowrap; }
.filter-kost-btn.active { background:var(--primary);border-color:var(--primary);color:#fff; }
.room-facility-tag { display:inline-flex;align-items:center;font-size:.72rem;font-weight:600;padding:3px 10px;border-radius:99px;background:rgba(0,180,186,.08);color:var(--primary);margin:2px; }
</style>

<div class="container py-4">

    <?php if ($success_msg): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?= $success_msg ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="mb-4">
        <h2 class="fw-bold mb-1" style="color:var(--dark)"><i class="bi bi-door-closed-fill me-2" style="color:var(--primary)"></i>Status Kamar</h2>
        <p class="text-muted mb-0">Pantau ketersediaan dan kondisi kamar kost Anda secara real-time</p>
    </div>

    <!-- Kost Filter -->
    <?php if (count($kosts) > 1): ?>
    <div class="d-flex gap-2 mb-4 flex-wrap overflow-auto">
        <?php foreach ($kosts as $k): ?>
        <a href="?kost_id=<?= $k['id'] ?>" class="filter-kost-btn <?= $filter_kost == $k['id'] ? 'active' : '' ?>">
            <i class="bi bi-house me-1"></i><?= htmlspecialchars($k['name']) ?>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Status Summary -->
    <div class="d-flex gap-3 mb-5 flex-wrap">
        <div class="room-stat-pill" style="background:rgba(5,150,105,.08);border:1px solid rgba(5,150,105,.2)">
            <div class="status-dot" style="background:#059669;box-shadow:0 0 0 4px rgba(5,150,105,.2)"></div>
            <div>
                <div class="stat-num" style="color:#059669"><?= $count_available ?></div>
                <div style="font-size:.78rem;font-weight:700;color:#059669">Kamar Kosong</div>
            </div>
        </div>
        <div class="room-stat-pill" style="background:rgba(220,38,38,.08);border:1px solid rgba(220,38,38,.2)">
            <div class="status-dot" style="background:#DC2626;box-shadow:0 0 0 4px rgba(220,38,38,.2)"></div>
            <div>
                <div class="stat-num" style="color:#DC2626"><?= $count_full ?></div>
                <div style="font-size:.78rem;font-weight:700;color:#DC2626">Kamar Terisi</div>
            </div>
        </div>
        <div class="room-stat-pill" style="background:rgba(217,119,6,.08);border:1px solid rgba(217,119,6,.2)">
            <div class="status-dot" style="background:#D97706;box-shadow:0 0 0 4px rgba(217,119,6,.2)"></div>
            <div>
                <div class="stat-num" style="color:#D97706"><?= $count_maintenance ?></div>
                <div style="font-size:.78rem;font-weight:700;color:#D97706">Maintenance</div>
            </div>
        </div>
        <div class="room-stat-pill" style="background:rgba(30,13,62,.05);border:1px solid var(--border-color)">
            <i class="bi bi-door-open" style="color:var(--dark);font-size:1.4rem"></i>
            <div>
                <div class="stat-num" style="color:var(--dark)"><?= count($rooms) ?></div>
                <div style="font-size:.78rem;font-weight:700;color:var(--text-muted)">Total Kamar</div>
            </div>
        </div>
    </div>

    <!-- Room Grid -->
    <?php if (empty($rooms)): ?>
    <div class="card text-center p-5">
        <i class="bi bi-door-closed display-3 mb-3" style="color:var(--border-color)"></i>
        <h5 class="fw-700">Belum ada kamar</h5>
        <p class="text-muted">Tambahkan kamar untuk kost ini terlebih dahulu.</p>
        <div><a href="room_add.php?kost_id=<?= $filter_kost ?>" class="btn btn-primary"><i class="bi bi-plus-lg me-2"></i>Tambah Kamar</a></div>
    </div>
    <?php else: ?>
    <div class="row g-3">
        <?php foreach ($rooms as $room):
            $st = get_room_status($room);
            $st_config = [
                'available'   => ['label'=>'Kosong',     'color'=>'#059669','bg'=>'rgba(5,150,105,.1)',  'icon'=>'bi-door-open-fill'],
                'full'        => ['label'=>'Terisi',     'color'=>'#DC2626','bg'=>'rgba(220,38,38,.1)',  'icon'=>'bi-door-closed-fill'],
                'maintenance' => ['label'=>'Maintenance','color'=>'#D97706','bg'=>'rgba(217,119,6,.1)',  'icon'=>'bi-tools'],
            ][$st];
            $facilities = array_slice(explode(',', $room['facilities'] ?? ''), 0, 4);
        ?>
        <div class="col-md-4 col-sm-6">
            <div class="card room-card <?= $st ?>">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h6 class="fw-700 mb-0"><?= htmlspecialchars($room['room_name']) ?></h6>
                        <span style="background:<?= $st_config['bg'] ?>;color:<?= $st_config['color'] ?>;padding:.3em .8em;border-radius:99px;font-size:.72rem;font-weight:700;white-space:nowrap">
                            <i class="bi <?= $st_config['icon'] ?> me-1"></i><?= $st_config['label'] ?>
                        </span>
                    </div>
                    <div class="d-flex flex-column gap-1 mb-3">
                        <div class="text-muted small"><i class="bi bi-arrows-expand me-1"></i>Ukuran: <?= htmlspecialchars($room['size'] ?? '—') ?></div>
                        <div class="fw-700" style="color:var(--primary)">Rp <?= number_format($room['price_per_month'],0,',','.') ?><span class="text-muted fw-400 small"> / bulan</span></div>
                        <div class="text-muted small"><i class="bi bi-person-check me-1"></i>Sisa: <?= $room['available_rooms'] ?> kamar</div>
                        <?php if ($room['active_bookings'] > 0): ?>
                        <div class="small" style="color:#D97706"><i class="bi bi-calendar-check me-1"></i><?= $room['active_bookings'] ?> booking aktif</div>
                        <?php endif; ?>
                    </div>
                    <?php if ($facilities): ?>
                    <div>
                        <?php foreach ($facilities as $f): ?>
                        <span class="room-facility-tag"><?= trim($f) ?></span>
                        <?php endforeach; ?>
                        <?php if (count(explode(',', $room['facilities'] ?? '')) > 4): ?>
                        <span class="room-facility-tag" style="background:rgba(92,77,120,.08);color:#5C4D78">+<?= count(explode(',', $room['facilities']))-4 ?></span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer d-flex gap-2 justify-content-between">
                    <a href="room_edit.php?id=<?= $room['id'] ?>" class="btn btn-sm btn-outline-secondary flex-grow-1"><i class="bi bi-pencil me-1"></i>Edit</a>
                    <?php if ($st === 'maintenance'): ?>
                    <a href="?action=available&room_id=<?= $room['id'] ?>&kost_id=<?= $filter_kost ?>" class="btn btn-sm btn-warning flex-grow-1 text-white" onclick="return confirm('Tandai selesai maintenance?')"><i class="bi bi-check-lg me-1"></i>Selesai</a>
                    <?php elseif ($st === 'available'): ?>
                    <a href="?action=maintenance&room_id=<?= $room['id'] ?>&kost_id=<?= $filter_kost ?>" class="btn btn-sm btn-outline-warning flex-grow-1" onclick="return confirm('Tandai sedang maintenance?')"><i class="bi bi-tools me-1"></i>Maintenance</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <div class="text-muted small mt-3"><i class="bi bi-info-circle me-1"></i>Gunakan tombol di atas untuk mengubah status Maintenance secara instan, atau gunakan tombol Edit untuk mengubah detail kamar lainnya.</div>
</div>

<?php include '../../layouts/footer.php'; ?>
