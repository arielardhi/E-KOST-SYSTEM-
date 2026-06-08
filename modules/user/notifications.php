<?php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role    = $_SESSION['role'];

// Handle mark as read
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['mark_read'])) {
        $nid = (int)$_POST['mark_read'];
        $pdo->prepare("UPDATE notifikasi SET is_read = 1 WHERE id = ? AND user_id = ?")->execute([$nid, $user_id]);
    } elseif (isset($_POST['mark_all_read'])) {
        $pdo->prepare("UPDATE notifikasi SET is_read = 1 WHERE user_id = ?")->execute([$user_id]);
    } elseif (isset($_POST['delete_notif'])) {
        $nid = (int)$_POST['delete_notif'];
        $pdo->prepare("DELETE FROM notifikasi WHERE id = ? AND user_id = ?")->execute([$nid, $user_id]);
    }
    header("Location: notifications.php" . (isset($_GET['filter']) ? '?filter='.$_GET['filter'] : ''));
    exit();
}

$filter = $_GET['filter'] ?? 'all';

$query  = "SELECT * FROM notifikasi WHERE user_id = ?";
$params = [$user_id];
if ($filter === 'unread') {
    $query .= " AND is_read = 0";
} elseif ($filter === 'read') {
    $query .= " AND is_read = 1";
}
$query .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$notifications = $stmt->fetchAll();

$unread_total = (int)$pdo->prepare("SELECT COUNT(*) FROM notifikasi WHERE user_id = ? AND is_read = 0")->execute([$user_id]) ?
    $pdo->prepare("SELECT COUNT(*) FROM notifikasi WHERE user_id = ? AND is_read = 0")->execute([$user_id]) : 0;
$cnt_stmt = $pdo->prepare("SELECT COUNT(*) FROM notifikasi WHERE user_id = ? AND is_read = 0");
$cnt_stmt->execute([$user_id]);
$unread_total = (int)$cnt_stmt->fetchColumn();

$total_stmt = $pdo->prepare("SELECT COUNT(*) FROM notifikasi WHERE user_id = ?");
$total_stmt->execute([$user_id]);
$total_notif = (int)$total_stmt->fetchColumn();

// Helper: icon & color by message content
function notif_icon($message) {
    $msg = strtolower($message);
    if (strpos($msg, 'booking') !== false || strpos($msg, 'pesanan') !== false) return ['bi-calendar-check-fill', '#00B4BA'];
    if (strpos($msg, 'bayar') !== false || strpos($msg, 'pembayaran') !== false || strpos($msg, 'payment') !== false) return ['bi-credit-card-fill', '#059669'];
    if (strpos($msg, 'ditolak') !== false || strpos($msg, 'rejected') !== false || strpos($msg, 'cancel') !== false) return ['bi-x-circle-fill', '#DC2626'];
    if (strpos($msg, 'pesan') !== false || strpos($msg, 'chat') !== false) return ['bi-chat-dots-fill', '#D97706'];
    return ['bi-bell-fill', '#5C4D78'];
}

// Demo notifications if empty
$demo_notifs = [];
if (empty($notifications)) {
    $demo_notifs = [
        ['id'=>'d1','message'=>'Booking Anda di Kost Mentari Indah telah dikonfirmasi oleh pemilik.','is_read'=>0,'created_at'=>date('Y-m-d H:i:s', strtotime('-1 hour'))],
        ['id'=>'d2','message'=>'Pembayaran Anda sebesar Rp 800.000 telah berhasil diverifikasi.','is_read'=>0,'created_at'=>date('Y-m-d H:i:s', strtotime('-3 hours'))],
        ['id'=>'d3','message'=>'Anda mendapatkan pesan baru dari Budi Santoso (Pemilik Kost Mawar).','is_read'=>1,'created_at'=>date('Y-m-d H:i:s', strtotime('-1 day'))],
        ['id'=>'d4','message'=>'Pembayaran Anda telah ditolak. Harap upload ulang bukti pembayaran.','is_read'=>1,'created_at'=>date('Y-m-d H:i:s', strtotime('-2 days'))],
        ['id'=>'d5','message'=>'Pesanan baru Anda di Kost Harmoni Jaya sedang dalam proses review.','is_read'=>1,'created_at'=>date('Y-m-d H:i:s', strtotime('-3 days'))],
    ];
    $notifications = $demo_notifs;
    $unread_total  = 2;
}

include '../../layouts/header.php';
?>

<style>
.notif-item {
    border-left: 4px solid var(--border-color);
    transition: all 0.2s ease;
    cursor: pointer;
}
.notif-item.unread {
    border-left-color: var(--primary);
    background: linear-gradient(to right, rgba(0,180,186,0.04), transparent) !important;
}
.notif-item:hover { transform: translateX(3px); }
.notif-icon-wrap {
    width: 46px; height: 46px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}
.notif-filter-btn { padding: 6px 18px; border-radius: 99px; font-weight: 600; font-size: .875rem; border: 1.5px solid var(--border-color); background:#fff; color:var(--text-muted); cursor:pointer; transition:all .15s; }
.notif-filter-btn.active { background:var(--primary); border-color:var(--primary); color:#fff; }
.unread-dot { width:8px; height:8px; border-radius:50%; background:var(--primary); flex-shrink:0; }
</style>

<div class="container py-4">

    <!-- Header -->
    <div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1" style="color:var(--dark)">
                <i class="bi bi-bell-fill me-2" style="color:var(--primary)"></i>Notifikasi
                <?php if ($unread_total > 0): ?>
                <span class="badge ms-1" style="background:var(--danger);font-size:.7rem;vertical-align:middle"><?= $unread_total ?></span>
                <?php endif; ?>
            </h2>
            <p class="text-muted mb-0">Pusat notifikasi aktivitas akun Anda</p>
        </div>
        <form method="POST">
            <button type="submit" name="mark_all_read" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-check2-all me-1"></i>Tandai Semua Dibaca
            </button>
        </form>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-4">
            <div class="card text-center p-3">
                <div class="fw-800 mb-1" style="font-size:1.8rem;color:var(--dark)"><?= $total_notif ?></div>
                <div class="text-muted small fw-600">Total</div>
            </div>
        </div>
        <div class="col-4">
            <div class="card text-center p-3">
                <div class="fw-800 mb-1" style="font-size:1.8rem;color:var(--primary)"><?= $unread_total ?></div>
                <div class="text-muted small fw-600">Belum Dibaca</div>
            </div>
        </div>
        <div class="col-4">
            <div class="card text-center p-3">
                <div class="fw-800 mb-1" style="font-size:1.8rem;color:var(--success)"><?= $total_notif - $unread_total ?></div>
                <div class="text-muted small fw-600">Sudah Dibaca</div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="d-flex gap-2 mb-4 flex-wrap">
        <a href="?filter=all" class="notif-filter-btn <?= $filter === 'all' ? 'active' : '' ?>">Semua</a>
        <a href="?filter=unread" class="notif-filter-btn <?= $filter === 'unread' ? 'active' : '' ?>">Belum Dibaca <?= $unread_total > 0 ? '<span class="badge bg-danger ms-1" style="font-size:.65rem">'.$unread_total.'</span>' : '' ?></a>
        <a href="?filter=read" class="notif-filter-btn <?= $filter === 'read' ? 'active' : '' ?>">Sudah Dibaca</a>
    </div>

    <!-- Notification List -->
    <?php if (empty($notifications)): ?>
    <div class="card text-center p-5">
        <i class="bi bi-bell-slash display-3 mb-3" style="color:var(--border-color)"></i>
        <h5 class="fw-700">Tidak ada notifikasi</h5>
        <p class="text-muted">Semua aktivitas Anda akan muncul di sini</p>
    </div>
    <?php else: ?>
    <div class="d-flex flex-column gap-2">
        <?php foreach ($notifications as $notif):
            [$icon, $color] = notif_icon($notif['message']);
            $is_demo = isset($notif['id']) && str_starts_with((string)$notif['id'], 'd');
        ?>
        <div class="card notif-item <?= !$notif['is_read'] ? 'unread' : '' ?>">
            <div class="card-body py-3">
                <div class="d-flex align-items-start gap-3">
                    <div class="notif-icon-wrap" style="background:<?= $color ?>18;">
                        <i class="bi <?= $icon ?>" style="color:<?= $color ?>"></i>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <p class="mb-1 fw-<?= !$notif['is_read'] ? '700' : '500' ?>" style="color:var(--text-main);line-height:1.5;">
                                <?= htmlspecialchars($notif['message']) ?>
                            </p>
                            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                <?php if (!$notif['is_read']): ?>
                                <div class="unread-dot"></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-2">
                            <div class="text-muted small">
                                <i class="bi bi-clock me-1"></i><?= date('d M Y, H:i', strtotime($notif['created_at'])) ?>
                            </div>
                            <?php if (!$is_demo): ?>
                            <div class="d-flex gap-2">
                                <?php if (!$notif['is_read']): ?>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="mark_read" value="<?= $notif['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size:.75rem">
                                        <i class="bi bi-check2 me-1"></i>Dibaca
                                    </button>
                                </form>
                                <?php endif; ?>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="delete_notif" value="<?= $notif['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" style="font-size:.75rem" onclick="return confirm('Hapus notifikasi?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                            <?php else: ?>
                            <span class="badge" style="background:rgba(0,180,186,.12);color:var(--primary);font-size:.7rem">Demo</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php include '../../layouts/footer.php'; ?>
