<?php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Pagination setup
$limit = 15;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

try {
    // Get total count
    $total_stmt = $pdo->query("SELECT COUNT(*) FROM system_logs");
    $total_rows = $total_stmt->fetchColumn();
    $total_pages = ceil($total_rows / $limit);

    // Fetch paginated logs
    $logs_stmt = $pdo->prepare("SELECT * FROM system_logs ORDER BY id DESC LIMIT :limit OFFSET :offset");
    $logs_stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $logs_stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $logs_stmt->execute();
    $logs = $logs_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $logs = [];
    $total_pages = 0;
    $error = "Gagal memuat log aktivitas: " . $e->getMessage();
}

include '../../layouts/header.php';
?>

<style>
.pagination .page-link {
    color: #000 !important;
    border: 2px solid #000 !important;
    font-weight: 700;
    margin: 0 3px;
    box-shadow: 2px 2px 0px #000;
    transition: all 0.15s ease;
    border-radius: 0 !important;
}
.pagination .page-link:hover {
    background-color: #00B4BA !important;
    color: #fff !important;
    transform: translate(-1px, -1px);
    box-shadow: 3px 3px 0px #000;
}
.pagination .page-item.active .page-link {
    background-color: #00B4BA !important;
    border-color: #000 !important;
    color: #fff !important;
    box-shadow: none;
    transform: none;
}
.pagination .page-item.disabled .page-link {
    opacity: 0.5;
    box-shadow: none;
    pointer-events: none;
    background-color: #e9ecef !important;
}
.table-row-hover:hover {
    background-color: rgba(0, 180, 186, 0.04) !important;
}
</style>

<div class="container py-4">
    <h2 class="fw-black text-uppercase mb-4">Log Aktivitas Sistem</h2>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success border border-3 border-dark rounded-0 fw-bold shadow-sm mb-4">
            <i class="bi bi-check-circle-fill me-2"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger border border-3 border-dark rounded-0 fw-bold shadow-sm mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger border border-3 border-dark rounded-0 fw-bold shadow-sm mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <div class="card border border-3 border-dark rounded-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="py-3 px-4" style="width: 20%;">Waktu</th>
                            <th class="py-3 px-4" style="width: 15%;">Pengguna</th>
                            <th class="py-3 px-4" style="width: 50%;">Aktivitas</th>
                            <th class="py-3 px-4" style="width: 15%;">IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="bi bi-card-list display-4 d-block mb-2"></i>
                                    Belum ada data log aktivitas sistem.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($logs as $log): 
                                $role = strtolower($log['role'] ?? '');
                                if ($role === 'admin') {
                                    $badge_class = 'bg-danger';
                                } elseif ($role === 'owner') {
                                    $badge_class = 'bg-primary';
                                } elseif ($role === 'user') {
                                    $badge_class = 'bg-success';
                                } else {
                                    $badge_class = 'bg-secondary';
                                }
                            ?>
                                <tr class="table-row-hover">
                                    <td class="px-4 py-3 text-muted" style="font-size: 0.9rem;">
                                        <?php echo date('d M Y, H:i', strtotime($log['created_at'])); ?>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold" style="font-size: 0.9rem;"><?php echo htmlspecialchars($log['username'] ?? 'System'); ?></span>
                                            <span class="badge <?php echo $badge_class; ?> align-self-start mt-1" style="font-size: 0.7rem;"><?php echo strtoupper($log['role'] ?? 'System'); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 fw-bold" style="font-size: 0.95rem; color: #1e0d3e;">
                                        <?php echo htmlspecialchars($log['activity']); ?>
                                    </td>
                                    <td class="px-4 py-3 text-muted" style="font-size: 0.9rem; font-family: monospace;">
                                        <?php echo htmlspecialchars($log['ip_address']); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <nav class="mt-4" aria-label="Navigasi Halaman Log">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>">&laquo;</a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>">&raquo;</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php include '../../layouts/footer.php'; ?>
