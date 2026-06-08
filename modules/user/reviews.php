<?php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get all kost (for dropdown)
$kosts = $pdo->query("SELECT id, name, city FROM kost ORDER BY name")->fetchAll();

// Get user's bookings (to allow review only for booked kost)
$stmt = $pdo->prepare("
    SELECT DISTINCT k.id, k.name, k.city
    FROM booking b
    JOIN kamar km ON b.kamar_id = km.id
    JOIN kost k ON km.kost_id = k.id
    WHERE b.user_id = ? AND b.status = 'confirmed'
");
$stmt->execute([$user_id]);
$booked_kosts = $stmt->fetchAll();

// Handle review submission
$success_msg = '';
$error_msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review'])) {
    $kost_id = (int)$_POST['kost_id'];
    $rating  = (int)$_POST['rating'];
    $comment = trim($_POST['comment']);
    if ($kost_id && $rating >= 1 && $rating <= 5 && $comment) {
        // Check if already reviewed
        $chk = $pdo->prepare("SELECT id FROM review WHERE user_id = ? AND kost_id = ?");
        $chk->execute([$user_id, $kost_id]);
        if ($chk->fetch()) {
            $error_msg = "Anda sudah memberikan review untuk kost ini.";
        } else {
            $ins = $pdo->prepare("INSERT INTO review (user_id, kost_id, rating, comment) VALUES (?, ?, ?, ?)");
            $ins->execute([$user_id, $kost_id, $rating, $comment]);
            $success_msg = "Review berhasil dikirim! Terima kasih atas penilaian Anda.";
        }
    } else {
        $error_msg = "Harap lengkapi semua field.";
    }
}

// Filter by kost
$filter_kost = (int)($_GET['kost_id'] ?? 0);

// Get all reviews
$review_query = "
    SELECT r.*, u.username, u.full_name, u.avatar, k.name as kost_name, k.city
    FROM review r
    JOIN users u ON r.user_id = u.id
    JOIN kost k ON r.kost_id = k.id
";
$review_params = [];
if ($filter_kost) {
    $review_query .= " WHERE r.kost_id = ?";
    $review_params[] = $filter_kost;
}
$review_query .= " ORDER BY r.created_at DESC";
$stmt = $pdo->prepare($review_query);
$stmt->execute($review_params);
$reviews = $stmt->fetchAll();

// Get avg rating per kost
$avg_ratings = $pdo->query("
    SELECT kost_id, AVG(rating) as avg_rating, COUNT(*) as total_reviews
    FROM review GROUP BY kost_id
")->fetchAll(PDO::FETCH_ASSOC) ?: [];

// Build avg ratings as assoc
$avg_map = [];
$tmp = $pdo->query("SELECT kost_id, AVG(rating) as avg_rating, COUNT(*) as total_reviews FROM review GROUP BY kost_id")->fetchAll();
foreach ($tmp as $r) { $avg_map[$r['kost_id']] = $r; }

// Stats
$total_reviews = count($reviews);
$my_reviews_count = $pdo->prepare("SELECT COUNT(*) FROM review WHERE user_id = ?");
$my_reviews_count->execute([$user_id]);
$my_reviews_count = (int)$my_reviews_count->fetchColumn();
$overall_avg = $pdo->query("SELECT AVG(rating) FROM review")->fetchColumn();

include '../../layouts/header.php';
?>

<style>
/* ── STAR RATING ── */
.star-rating { display:flex; flex-direction:row-reverse; gap:4px; justify-content:flex-end; }
.star-rating input { display:none; }
.star-rating label {
    font-size: 2rem; cursor: pointer;
    color: #D6CEBC; transition: color 0.15s ease;
}
.star-rating input:checked ~ label,
.star-rating label:hover,
.star-rating label:hover ~ label { color: #D97706; }

/* ── REVIEW CARD ── */
.review-card {
    border-left: 4px solid var(--primary) !important;
    transition: all 0.2s ease;
}
.review-card:hover { transform: translateX(4px); }

/* ── STAR DISPLAY ── */
.stars-display { color: #D97706; letter-spacing: 2px; }
.stars-display .empty { color: #D6CEBC; }

/* ── RATING BAR ── */
.rating-bar-wrap { display:flex; align-items:center; gap:10px; margin-bottom:6px; }
.rating-bar { flex:1; background:#EAE4CC; border-radius:99px; height:8px; overflow:hidden; }
.rating-bar-fill { height:100%; background:linear-gradient(90deg,#D97706,#F59E0B); border-radius:99px; transition:width 0.6s ease; }

/* ── KOST RATING CARD ── */
.kost-rating-card { border-top: 3px solid var(--primary); }
.big-rating { font-size: 3rem; font-weight: 800; line-height:1; color: var(--dark); }

/* ── AVATAR ── */
.reviewer-avatar {
    width: 44px; height: 44px;
    background: linear-gradient(135deg, var(--primary), #008F95);
    border-radius: 50%;
    display: flex; align-items:center; justify-content:center;
    color: #fff; font-weight: 700; font-size: 1.1rem;
    flex-shrink: 0;
}

/* ── FILTER TABS ── */
.filter-tab { cursor:pointer; padding:6px 16px; border-radius:99px; font-weight:600; font-size:0.875rem; transition:all 0.15s; border:1px solid var(--border-color); background:#fff; color:var(--text-muted); }
.filter-tab.active { background:var(--primary); color:#fff; border-color:var(--primary); }
</style>

<div class="container py-4">

    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1" style="color:var(--dark)"><i class="bi bi-star-fill me-2" style="color:#D97706"></i>Review & Rating</h2>
            <p class="text-muted mb-0">Bagikan pengalaman Anda menginap di kost pilihan</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reviewModal">
            <i class="bi bi-plus-lg me-2"></i>Tulis Review
        </button>
    </div>

    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div class="fw-800 mb-1" style="font-size:2rem;color:var(--primary);"><?= $total_reviews ?></div>
                <div class="text-muted small fw-600">Total Review</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div class="fw-800 mb-1" style="font-size:2rem;color:#D97706;"><?= $my_reviews_count ?></div>
                <div class="text-muted small fw-600">Review Saya</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div class="fw-800 mb-1" style="font-size:2rem;color:var(--success);"><?= $overall_avg ? number_format($overall_avg, 1) : '–' ?></div>
                <div class="text-muted small fw-600">Rata-rata Rating</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div class="fw-800 mb-1" style="font-size:2rem;color:var(--dark);"><?= count($avg_map) ?></div>
                <div class="text-muted small fw-600">Kost Diulas</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left: Rating Summary per Kost -->
        <div class="col-lg-4">
            <?php if (!empty($avg_map)): ?>
            <?php foreach (array_slice($avg_map, 0, 5, true) as $kid => $ar):
                $kname = '';
                foreach ($kosts as $k) { if ($k['id'] == $kid) { $kname = $k['name']; break; } }
                $avg = round($ar['avg_rating'], 1);
                $distribution = [];
                for ($s = 5; $s >= 1; $s--) {
                    $cnt = $pdo->prepare("SELECT COUNT(*) FROM review WHERE kost_id = ? AND rating = ?");
                    $cnt->execute([$kid, $s]);
                    $distribution[$s] = (int)$cnt->fetchColumn();
                }
                $total_r = $ar['total_reviews'];
            ?>
            <div class="card mb-3 kost-rating-card">
                <div class="card-body">
                    <h6 class="fw-700 mb-1"><?= htmlspecialchars($kname) ?></h6>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="big-rating"><?= number_format($avg, 1) ?></div>
                        <div>
                            <div class="stars-display mb-1">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="bi bi-star<?= $i <= round($avg) ? '-fill' : '' ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <div class="text-muted small"><?= $total_r ?> ulasan</div>
                        </div>
                    </div>
                    <?php for ($s = 5; $s >= 1; $s--):
                        $pct = $total_r > 0 ? round(($distribution[$s] / $total_r) * 100) : 0;
                    ?>
                    <div class="rating-bar-wrap">
                        <span style="width:12px;font-size:.75rem;font-weight:700;color:var(--text-muted)"><?= $s ?></span>
                        <div class="rating-bar"><div class="rating-bar-fill" style="width:<?= $pct ?>%"></div></div>
                        <span style="width:24px;font-size:.75rem;color:var(--text-muted)"><?= $distribution[$s] ?></span>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <div class="card p-4 text-center">
                <i class="bi bi-star display-4 text-muted mb-3"></i>
                <p class="text-muted">Belum ada rating kost.</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Right: Review List -->
        <div class="col-lg-8">
            <!-- Alerts -->
            <?php if ($success_msg): ?>
            <div class="alert alert-success d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-check-circle-fill"></i><?= $success_msg ?>
            </div>
            <?php endif; ?>
            <?php if ($error_msg): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-exclamation-circle-fill"></i><?= $error_msg ?>
            </div>
            <?php endif; ?>

            <!-- Filter -->
            <div class="card mb-4">
                <div class="card-body py-3">
                    <form method="GET" class="d-flex gap-2 align-items-center flex-wrap">
                        <i class="bi bi-funnel text-muted"></i>
                        <select name="kost_id" class="form-select form-select-sm" style="max-width:240px;">
                            <option value="">Semua Kost</option>
                            <?php foreach ($kosts as $k): ?>
                            <option value="<?= $k['id'] ?>" <?= $filter_kost == $k['id'] ? 'selected' : '' ?>><?= htmlspecialchars($k['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                        <?php if ($filter_kost): ?>
                        <a href="reviews.php" class="btn btn-sm btn-outline-secondary">Reset</a>
                        <?php endif; ?>
                        <span class="ms-auto badge bg-secondary"><?= count($reviews) ?> review</span>
                    </form>
                </div>
            </div>

            <!-- Review Cards -->
            <?php if (empty($reviews)): ?>
            <div class="card text-center p-5">
                <i class="bi bi-chat-square-text display-3 mb-3" style="color:var(--border-color)"></i>
                <h5 class="fw-700">Belum Ada Review</h5>
                <p class="text-muted">Jadilah yang pertama memberikan ulasan!</p>
                <div><button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reviewModal"><i class="bi bi-plus-lg me-2"></i>Tulis Review</button></div>
            </div>
            <?php else: ?>
            <div class="d-flex flex-column gap-3">
                <?php foreach ($reviews as $rv):
                    $initials = strtoupper(substr($rv['full_name'] ?: $rv['username'], 0, 1));
                ?>
                <div class="card review-card">
                    <div class="card-body">
                        <div class="d-flex gap-3 align-items-start">
                            <div class="reviewer-avatar"><?= $initials ?></div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                    <div>
                                        <div class="fw-700"><?= htmlspecialchars($rv['full_name'] ?: $rv['username']) ?></div>
                                        <div class="text-muted small"><i class="bi bi-house me-1"></i><?= htmlspecialchars($rv['kost_name']) ?> — <?= htmlspecialchars($rv['city']) ?></div>
                                    </div>
                                    <div class="text-end">
                                        <div class="stars-display" style="font-size:1rem;">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="bi bi-star<?= $i <= $rv['rating'] ? '-fill' : '' ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <div class="text-muted" style="font-size:.75rem;"><?= date('d M Y', strtotime($rv['created_at'])) ?></div>
                                    </div>
                                </div>
                                <p class="mt-2 mb-0" style="color:var(--text-main);line-height:1.6;"><?= nl2br(htmlspecialchars($rv['comment'])) ?></p>
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

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-800"><i class="bi bi-star-fill me-2" style="color:#D97706"></i>Tulis Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <?php if (empty($booked_kosts)): ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Anda hanya bisa mereview kost yang telah Anda pesan dan dikonfirmasi.
                    </div>
                    <?php else: ?>
                    <div class="mb-4">
                        <label class="form-label">Pilih Kost</label>
                        <select name="kost_id" class="form-select" required>
                            <option value="">-- Pilih Kost --</option>
                            <?php foreach ($booked_kosts as $bk): ?>
                            <option value="<?= $bk['id'] ?>"><?= htmlspecialchars($bk['name']) ?> — <?= htmlspecialchars($bk['city']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Rating</label>
                        <div class="star-rating mb-1">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                            <input type="radio" name="rating" id="star<?= $i ?>" value="<?= $i ?>" required>
                            <label for="star<?= $i ?>"><i class="bi bi-star-fill"></i></label>
                            <?php endfor; ?>
                        </div>
                        <div class="text-muted small">Klik bintang untuk memberi rating</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Komentar Review</label>
                        <textarea name="comment" class="form-control" rows="4" placeholder="Ceritakan pengalaman Anda menginap di sini..." required></textarea>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <?php if (!empty($booked_kosts)): ?>
                    <button type="submit" name="submit_review" class="btn btn-primary"><i class="bi bi-send me-2"></i>Kirim Review</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if ($success_msg || $error_msg): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var modal = new bootstrap.Modal(document.getElementById('reviewModal'));
    <?php if ($error_msg): ?>modal.show();<?php endif; ?>
});
</script>
<?php endif; ?>

<?php include '../../layouts/footer.php'; ?>
