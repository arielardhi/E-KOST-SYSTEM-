<?php
require_once '../config/database.php';
include '../layouts/header.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT k.*, u.full_name as owner_name, u.phone as owner_phone FROM kost k JOIN users u ON k.owner_id = u.id WHERE k.id = ?");
$stmt->execute([$id]);
$kost = $stmt->fetch();

if (!$kost) {
    echo "<div class='container text-center py-5'><h3 class='fw-black text-uppercase'>Kost tidak ditemukan!</h3><a href='kost_list.php' class='btn btn-primary'>Kembali</a></div>";
    include '../layouts/footer.php';
    exit();
}

// Handle Review Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review']) && isset($_SESSION['user_id'])) {
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    $user_id = $_SESSION['user_id'];
    
    try {
        $stmt_rev = $pdo->prepare("INSERT INTO review (kost_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt_rev->execute([$id, $user_id, $rating, $comment]);
        echo "<script>alert('Terima kasih atas ulasan Anda!'); window.location.href='kost_detail.php?id=$id';</script>";
    } catch (Exception $e) {
        $error_msg = "Gagal mengirim ulasan.";
    }
}

// Get rooms
$stmt = $pdo->prepare("SELECT * FROM kamar WHERE kost_id = ?");
$stmt->execute([$id]);
$rooms = $stmt->fetchAll();

// Get photos
$stmt = $pdo->prepare("SELECT * FROM kost_foto WHERE kost_id = ?");
$stmt->execute([$id]);
$photos = $stmt->fetchAll();

// Get reviews
$stmt_reviews = $pdo->prepare("SELECT r.*, u.full_name, u.avatar FROM review r JOIN users u ON r.user_id = u.id WHERE r.kost_id = ? ORDER BY r.created_at DESC");
$stmt_reviews->execute([$id]);
$reviews = $stmt_reviews->fetchAll();

// Calculate average rating
$stmt_avg = $pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews FROM review WHERE kost_id = ?");
$stmt_avg->execute([$id]);
$rating_stats = $stmt_avg->fetch();
$avg_rating = round($rating_stats['avg_rating'] ?? 0, 1);
?>

<div class="container py-4">
    <div class="row">
        <!-- Gallery & Details -->
        <div class="col-md-8 mb-4">
            <!-- Neubrutalism Gallery -->
            <div id="kostCarousel" class="carousel slide card mb-4 overflow-hidden" data-bs-ride="carousel" style="border-width: 4px;">
                <div class="carousel-inner">
                    <?php if (empty($photos)): ?>
                        <div class="carousel-item active">
                            <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=800&q=80" class="d-block w-100" style="height: 450px; object-fit: cover;">
                        </div>
                    <?php else: ?>
                        <?php foreach ($photos as $index => $photo): ?>
                            <div class="carousel-item <?php echo $index == 0 ? 'active' : ''; ?>">
                                <img src="<?php echo $base_url . $photo['image_path']; ?>" class="d-block w-100" style="height: 450px; object-fit: cover;">
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#kostCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon bg-dark rounded-circle p-3 border border-2 border-white"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#kostCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon bg-dark rounded-circle p-3 border border-2 border-white"></span>
                </button>
            </div>

            <div class="card mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="badge bg-<?php echo $kost['type'] == 'Putra' ? 'primary' : ($kost['type'] == 'Putri' ? 'danger' : 'warning'); ?> border border-2 border-dark mb-2">
                                KOST <?php echo strtoupper($kost['type']); ?>
                            </span>
                            <h2 class="fw-black text-uppercase mb-1"><?php echo $kost['name']; ?></h2>
                            <p class="text-muted fw-bold"><i class="bi bi-geo-alt"></i> <?php echo $kost['address']; ?>, <?php echo $kost['city']; ?></p>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-warning text-dark border border-2 border-dark px-3 py-2 fw-black">
                                    <i class="bi bi-star-fill me-1"></i> <?php echo $avg_rating; ?> / 5.0
                                </span>
                                <small class="text-muted fw-bold">(<?php echo $rating_stats['total_reviews']; ?> Ulasan)</small>
                            </div>
                        </div>
                        <?php
                        $is_fav = false;
                        if (isset($_SESSION['user_id'])) {
                            $stmt_f = $pdo->prepare("SELECT id FROM favorit WHERE user_id = ? AND kost_id = ?");
                            $stmt_f->execute([$_SESSION['user_id'], $id]);
                            $is_fav = $stmt_f->fetch();
                        }
                        ?>
                        <button class="btn <?php echo $is_fav ? 'btn-danger' : 'btn-outline-dark'; ?> fav-btn fw-bold" data-id="<?php echo $id; ?>" style="border-width: 3px; box-shadow: 4px 4px 0 #000;">
                            <i class="bi <?php echo $is_fav ? 'bi-heart-fill' : 'bi-heart'; ?>"></i> <?php echo $is_fav ? 'FAVORIT' : 'SIMPAN'; ?>
                        </button>
                    </div>
                    
                    <hr class="border-3 opacity-100">
                    
                    <h5 class="fw-black text-uppercase mb-3">Deskripsi Properti</h5>
                    <p class="fw-bold"><?php echo nl2br(htmlspecialchars($kost['description'])); ?></p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h5 class="fw-black text-uppercase mb-3">Fasilitas Kost</h5>
                            <div class="d-flex flex-wrap gap-2">
                                <?php 
                                $facs = explode(',', $kost['facilities']);
                                foreach($facs as $f): ?>
                                    <span class="badge bg-light text-dark border border-2 border-dark py-2 px-3 fw-bold"><?php echo trim($f); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="fw-black text-uppercase mb-3">Peraturan</h5>
                            <p class="small fw-bold text-muted"><?php echo nl2br(htmlspecialchars($kost['rules'])); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ROOMS CATALOG -->
            <h4 class="fw-black text-uppercase mb-3">Pilihan Kamar</h4>
            <?php if (empty($rooms)): ?>
                <div class="card mb-3 p-4 text-center border-2">
                    <p class="mb-0 fw-bold text-muted">
                        <i class="bi bi-exclamation-circle text-warning me-2"></i> Belum ada pilihan kamar yang terdaftar atau tersedia untuk kost ini saat ini.
                    </p>
                </div>
            <?php else: ?>
                <?php foreach ($rooms as $room): ?>
                    <div class="card mb-3 kost-card">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h5 class="fw-black text-uppercase mb-2"><?php echo $room['room_name']; ?></h5>
                                    <div class="d-flex gap-3 mb-3">
                                        <span class="small fw-bold"><i class="bi bi-aspect-ratio me-1"></i> <?php echo $room['size']; ?></span>
                                        <span class="small fw-bold text-<?php echo $room['available_rooms'] > 0 ? 'success' : 'danger'; ?>">
                                            <i class="bi bi-door-open me-1"></i> Sisa <?php echo $room['available_rooms']; ?> kamar
                                        </span>
                                    </div>
                                    <p class="mb-0 small fw-bold text-muted"><?php echo $room['facilities']; ?></p>
                                </div>
                                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                    <div class="h4 fw-black mb-3">Rp <?php echo number_format($room['price_per_month'], 0, ',', '.'); ?> <small class="text-muted fw-normal" style="font-size: .9rem;">/ bln</small></div>
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                        <?php if ($_SESSION['role'] === 'user'): ?>
                                            <a href="booking.php?room_id=<?php echo $room['id']; ?>" class="btn btn-primary w-100 fw-bold py-2">PESAN SEKARANG</a>
                                        <?php else: ?>
                                            <button class="btn btn-secondary w-100 fw-bold py-2" disabled title="Hanya akun Penyewa yang dapat memesan kost">KHUSUS PENYEWA</button>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <a href="../modules/auth/login.php" class="btn btn-primary w-100 fw-bold py-2">LOGIN UNTUK PESAN</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- REVIEW SECTION -->
            <div class="card mt-5 mb-4 border-3">
                <div class="card-header bg-neubrutal-yellow py-3 border-bottom border-3 border-dark">
                    <h5 class="mb-0 fw-black text-uppercase">Ulasan Pengguna</h5>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'user'): ?>
                        <form method="POST" class="mb-5 p-4 bg-light border border-2 border-dark" style="box-shadow: 6px 6px 0 #000;">
                            <h6 class="fw-black text-uppercase mb-3">Tulis Ulasan Anda</h6>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Rating Bintang</label>
                                <select name="rating" class="form-select border-2 border-dark" required>
                                    <option value="5">⭐⭐⭐⭐⭐ (Sangat Baik)</option>
                                    <option value="4">⭐⭐⭐⭐ (Baik)</option>
                                    <option value="3">⭐⭐⭐ (Cukup)</option>
                                    <option value="2">⭐⭐ (Kurang)</option>
                                    <option value="1">⭐ (Buruk)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Komentar</label>
                                <textarea name="comment" class="form-control border-2 border-dark" rows="3" placeholder="Bagikan pengalaman Anda menginap di sini..." required></textarea>
                            </div>
                            <button type="submit" name="submit_review" class="btn btn-primary fw-bold px-4 py-2">KIRIM ULASAN</button>
                        </form>
                    <?php endif; ?>

                    <?php if (empty($reviews)): ?>
                        <p class="text-center text-muted fw-bold py-4">Belum ada ulasan untuk kost ini.</p>
                    <?php else: ?>
                        <div class="review-list">
                            <?php foreach ($reviews as $rev): ?>
                                    <?php
                                    $avatar_url = 'https://via.placeholder.com/50?text=U';
                                    if ($rev['avatar']) {
                                        if (filter_var($rev['avatar'], FILTER_VALIDATE_URL)) {
                                            $avatar_url = $rev['avatar'];
                                        } else {
                                            $avatar_url = $base_url . 'uploads/avatars/' . $rev['avatar'];
                                        }
                                    }
                                    ?>
                                    <img src="<?php echo $avatar_url; ?>" 
                                         class="border border-2 border-dark" style="width: 50px; height: 50px; object-fit: cover;">
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="fw-black text-uppercase mb-1"><?php echo $rev['full_name']; ?></h6>
                                            <span class="text-warning">
                                                <?php for($i=0; $i<$rev['rating']; $i++) echo '⭐'; ?>
                                            </span>
                                        </div>
                                        <p class="mb-1 fw-bold"><?php echo htmlspecialchars($rev['comment']); ?></p>
                                        <small class="text-muted fw-bold"><?php echo date('d M Y', strtotime($rev['created_at'])); ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-md-4">
            <div class="card sticky-top" style="top: 20px; border-width: 4px;">
                <div class="card-header bg-neubrutal-blue text-white py-3 border-bottom border-3 border-dark">
                    <h5 class="mb-0 fw-black text-uppercase">Informasi Pemilik</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-neubrutal-yellow border border-2 border-dark p-3 me-3">
                            <i class="bi bi-person-fill fs-3"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-black text-uppercase"><?php echo $kost['owner_name']; ?></h6>
                            <small class="text-muted fw-bold">Aktif sejak <?php echo date('M Y', strtotime($kost['created_at'])); ?></small>
                        </div>
                    </div>
                    <div class="d-grid gap-3">
                        <?php
                        $wa_phone = preg_replace('/\D/', '', $kost['owner_phone']);
                        if (str_starts_with($wa_phone, '0')) $wa_phone = '62' . substr($wa_phone, 1);
                        elseif (!str_starts_with($wa_phone, '62')) $wa_phone = '62' . $wa_phone;
                        $wa_msg = urlencode("Halo, saya tertarik dengan kost *" . $kost['name'] . "*. Apakah masih tersedia?");
                        ?>
                        <a href="https://wa.me/<?php echo $wa_phone; ?>?text=<?php echo $wa_msg; ?>" target="_blank"
                           class="btn btn-success fw-black py-3 border border-3 border-dark" style="box-shadow: 4px 4px 0 #000;">
                           <i class="bi bi-whatsapp me-2"></i> CHAT VIA WHATSAPP
                        </a>
                        
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php $chat_role = ($_SESSION['role'] === 'owner') ? 'owner' : 'user'; ?>
                            <a href="<?php echo $base_url; ?>modules/<?php echo $chat_role; ?>/chat.php?receiver_id=<?php echo $kost['owner_id']; ?>"
                               class="btn btn-outline-dark fw-black py-3 border border-3 border-dark" style="box-shadow: 4px 4px 0 #000;">
                               <i class="bi bi-chat-dots me-2"></i> CHAT DI SISTEM
                            </a>
                        <?php else: ?>
                            <a href="<?php echo $base_url; ?>modules/auth/login.php" class="btn btn-outline-dark fw-black py-3 border border-3 border-dark" style="box-shadow: 4px 4px 0 #000;">
                                <i class="bi bi-chat-dots me-2"></i> LOGIN UNTUK CHAT
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mt-4 p-3 bg-light border border-2 border-dark">
                        <small class="fw-black text-uppercase d-block mb-2">Tips Aman:</small>
                        <ul class="small fw-bold mb-0 ps-3">
                            <li>Cek langsung ke lokasi kost</li>
                            <li>Pastikan fasilitas sesuai deskripsi</li>
                            <li>Jangan transfer DP sembarangan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../layouts/footer.php'; ?>
