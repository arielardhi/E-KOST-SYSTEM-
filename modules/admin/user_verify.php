<?php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Handle AJAX POST requests for verifying / rejecting users
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? $_POST['action'] ?? '';
    $user_id = intval($input['user_id'] ?? $_POST['user_id'] ?? 0);
    
    if ($user_id > 0 && in_array($action, ['approve', 'reject'])) {
        $status = ($action === 'approve') ? 'verified' : 'rejected';
        
        try {
            $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
            $success = $stmt->execute([$status, $user_id]);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => $success]);
            exit();
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit();
        }
    }
}

// Get real users from DB
$search  = trim($_GET['search'] ?? '');
$filter  = $_GET['status'] ?? 'all';

// Prepare query for active filters
$query = "SELECT * FROM users WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND (username LIKE ? OR email LIKE ? OR full_name LIKE ?)";
    $s = "%$search%";
    $params[] = $s;
    $params[] = $s;
    $params[] = $s;
}

if ($filter !== 'all') {
    $query .= " AND status = ?";
    $params[] = $filter;
}

$query .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll();

// Stats
$total    = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$verified = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'verified'")->fetchColumn();
$pending  = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'pending'")->fetchColumn();
$rejected = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'rejected'")->fetchColumn();

include '../../layouts/header.php';
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
.verify-badge { padding:.35em .9em;border-radius:99px;font-weight:700;font-size:.75rem;letter-spacing:.03em; }
.badge-pending  { background:rgba(217,119,6,.15);color:#D97706; }
.badge-verified { background:rgba(5,150,105,.15);color:#059669; }
.badge-rejected { background:rgba(220,38,38,.12);color:#DC2626; }
.user-avatar { width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.9rem;flex-shrink:0; }
.filter-pill { padding:6px 16px;border-radius:99px;font-weight:600;font-size:.85rem;border:1.5px solid var(--border-color);background:#fff;color:var(--text-muted);cursor:pointer;transition:all .15s;text-decoration:none;display:inline-block; }
.filter-pill.active { background:var(--primary);border-color:var(--primary);color:#fff; }
.table-row-hover:hover { background-color:rgba(0,180,186,.04) !important; }
.stat-card-verify { border-top:3px solid var(--primary); }
</style>

<div class="container py-4">
    <!-- Header -->
    <div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1" style="color:var(--dark)"><i class="bi bi-person-check-fill me-2" style="color:var(--primary)"></i>Verifikasi User</h2>
            <p class="text-muted mb-0">Kelola status verifikasi pengguna terdaftar</p>
        </div>
        <span class="badge" style="background:var(--primary);font-size:.8rem;padding:.5em 1em;border-radius:99px"><?= count($users) ?> ditampilkan</span>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card stat-card-verify p-3 text-center">
                <div class="fw-800 mb-1" style="font-size:2rem;color:var(--dark)"><?= $total ?></div>
                <div class="text-muted small fw-600">Total User</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card p-3 text-center" style="border-top:3px solid #059669">
                <div class="fw-800 mb-1" style="font-size:2rem;color:#059669"><?= $verified ?></div>
                <div class="text-muted small fw-600">Verified</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card p-3 text-center" style="border-top:3px solid #D97706">
                <div class="fw-800 mb-1" style="font-size:2rem;color:#D97706"><?= $pending ?></div>
                <div class="text-muted small fw-600">Pending</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card p-3 text-center" style="border-top:3px solid #DC2626">
                <div class="fw-800 mb-1" style="font-size:2rem;color:#DC2626"><?= $rejected ?></div>
                <div class="text-muted small fw-600">Rejected</div>
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
                <div class="input-group" style="max-width:320px">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Cari username, email, nama..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <input type="hidden" name="status" value="<?= htmlspecialchars($filter) ?>">
                <button type="submit" class="btn btn-primary">Cari</button>
                <?php if ($search): ?>
                <a href="user_verify.php" class="btn btn-outline-secondary">Reset</a>
                <?php endif; ?>
            </form>
            <div class="d-flex gap-2 mt-3 flex-wrap">
                <a href="?status=all<?= $search ? '&search='.urlencode($search) : '' ?>" class="filter-pill <?= $filter==='all' ? 'active' : '' ?>">Semua</a>
                <a href="?status=verified<?= $search ? '&search='.urlencode($search) : '' ?>" class="filter-pill <?= $filter==='verified' ? 'active' : '' ?>">✓ Verified</a>
                <a href="?status=pending<?= $search ? '&search='.urlencode($search) : '' ?>" class="filter-pill <?= $filter==='pending' ? 'active' : '' ?>">⏳ Pending</a>
                <a href="?status=rejected<?= $search ? '&search='.urlencode($search) : '' ?>" class="filter-pill <?= $filter==='rejected' ? 'active' : '' ?>">✗ Rejected</a>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Tgl Daftar</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                        <tr><td colspan="7" class="text-center py-5 text-muted"><i class="bi bi-people display-4 d-block mb-2"></i>Tidak ada user ditemukan</td></tr>
                        <?php else: foreach ($users as $u):
                            $initials = strtoupper(substr($u['full_name'] ?: $u['username'], 0, 1));
                            $colors   = ['#00B4BA','#D97706','#059669','#DC2626','#5C4D78'];
                            $bg       = $colors[$u['id'] % 5];
                            
                            $st = $u['status'];
                            $badgeCls = ($st === 'pending') ? 'warning' : (($st === 'verified') ? 'success' : 'danger');
                            $stLabel = ($st === 'pending') ? 'Pending' : (($st === 'verified') ? 'Verified' : 'Rejected');
                        ?>
                        <tr class="table-row-hover">
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="user-avatar" style="background:<?= $bg ?>22;color:<?= $bg ?>"><?= $initials ?></div>
                                    <div>
                                        <div class="fw-700" style="font-size:.9rem"><?= htmlspecialchars($u['username']) ?></div>
                                        <div class="text-muted" style="font-size:.75rem"><?= htmlspecialchars($u['full_name'] ?: '—') ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge" style="background:<?= $u['role']==='admin' ? 'rgba(220,38,38,.12);color:#DC2626' : ($u['role']==='owner' ? 'rgba(0,180,186,.12);color:#00B4BA' : 'rgba(92,77,120,.12);color:#5C4D78') ?>">
                                    <?= strtoupper($u['role']) ?>
                                </span>
                            </td>
                            <td style="font-size:.875rem"><?= htmlspecialchars($u['email']) ?></td>
                            <td style="font-size:.875rem"><?= htmlspecialchars($u['phone'] ?? '—') ?></td>
                            <td style="font-size:.875rem"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                            <td><span class="verify-badge badge-<?= $st ?>"><?= $stLabel ?></span></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <?php if ($st !== 'verified'): ?>
                                    <button class="btn btn-sm btn-success" onclick="doAction('approve', <?= $u['id'] ?>, '<?= htmlspecialchars($u['username']) ?>')" title="Approve">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                    <?php endif; ?>
                                    <?php if ($st !== 'rejected'): ?>
                                    <button class="btn btn-sm btn-danger" onclick="doAction('reject', <?= $u['id'] ?>, '<?= htmlspecialchars($u['username']) ?>')" title="Reject">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                    <?php endif; ?>
                                    <a href="user_edit.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function doAction(action, userId, username) {
    const isApprove = action === 'approve';
    Swal.fire({
        title: isApprove ? `Approve "${username}"?` : `Reject "${username}"?`,
        text: isApprove ? 'User akan mendapatkan akses penuh ke platform.' : 'User akan ditolak aksesnya ke platform.',
        icon: isApprove ? 'question' : 'warning',
        showCancelButton: true,
        confirmButtonColor: isApprove ? '#059669' : '#DC2626',
        cancelButtonColor: '#5C4D78',
        confirmButtonText: isApprove ? '<i class="bi bi-check-lg me-1"></i>Ya, Approve' : '<i class="bi bi-x-lg me-1"></i>Ya, Reject',
        cancelButtonText: 'Batal',
    }).then(result => {
        if (result.isConfirmed) {
            fetch('user_verify.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ action: action, user_id: userId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: isApprove ? 'User Diapprove!' : 'User Direject!',
                        text: `Status "${username}" telah diperbarui.`,
                        timer: 2000,
                        showConfirmButton: false,
                        timerProgressBar: true,
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Terjadi kesalahan saat memperbarui status user.',
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Koneksi error: ' + error.message,
                });
            });
        }
    });
}
</script>

<?php include '../../layouts/footer.php'; ?>
