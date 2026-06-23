<?php
require_once '../../config/database.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php"); exit();
}

// Stats for summary cards
$total_bookings = $pdo->query("SELECT COUNT(*) FROM booking")->fetchColumn();
$total_revenue  = $pdo->query("SELECT COUNT(*) * 10000 FROM pembayaran WHERE status='verified'")->fetchColumn();
$total_users    = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_kost     = $pdo->query("SELECT COUNT(*) FROM kost")->fetchColumn();

// Preview and filter variables
$is_preview          = isset($_GET['preview']) && $_GET['preview'] == 1;
$preview_report_type = $_GET['report_type'] ?? 'booking';
$preview_date_from   = $_GET['date_from'] ?? date('Y-m-01');
$preview_date_to     = $_GET['date_to'] ?? date('Y-m-d');
$preview_status      = $_GET['status'] ?? 'all';

$preview_rows = [];
$preview_headers = [];
$total_records = 0;

if ($is_preview) {
    $dt_from = $preview_date_from . ' 00:00:00';
    $dt_to   = $preview_date_to . ' 23:59:59';
    
    if ($preview_report_type === 'booking') {
        $preview_headers = ['No', 'ID Booking', 'Penyewa', 'Kost', 'Kamar', 'Mulai Sewa', 'Total', 'Status'];
        
        $count_sql = "SELECT COUNT(*) FROM booking WHERE created_at BETWEEN ? AND ?";
        $params = [$dt_from, $dt_to];
        if ($preview_status !== 'all') {
            $count_sql .= " AND status = ?";
            $params[] = $preview_status;
        }
        $c_stmt = $pdo->prepare($count_sql);
        $c_stmt->execute($params);
        $total_records = $c_stmt->fetchColumn();
        
        $sql = "
            SELECT b.id, u.full_name as tenant_name, k.name as kost_name, km.room_name, b.start_date, b.total_price, b.status
            FROM booking b
            JOIN users u ON b.user_id = u.id
            JOIN kamar km ON b.kamar_id = km.id
            JOIN kost k ON km.kost_id = k.id
            WHERE b.created_at BETWEEN ? AND ?
        ";
        if ($preview_status !== 'all') {
            $sql .= " AND b.status = ?";
        }
        $sql .= " ORDER BY b.created_at DESC LIMIT 5";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $i = 1;
        foreach ($rows as $row) {
            $preview_rows[] = [
                $i++,
                'BK-' . str_pad($row['id'], 5, '0', STR_PAD_LEFT),
                $row['tenant_name'],
                $row['kost_name'],
                $row['room_name'],
                date('d M Y', strtotime($row['start_date'])),
                'Rp ' . number_format($row['total_price'], 0, ',', '.'),
                $row['status']
            ];
        }
    } elseif ($preview_report_type === 'pembayaran') {
        $preview_headers = ['No', 'ID Pembayaran', 'ID Booking', 'Penyewa', 'Jumlah', 'Tanggal Bayar', 'Status'];
        
        $p_status = '';
        if ($preview_status !== 'all') {
            $p_status = 'pending';
            if ($preview_status === 'confirmed') $p_status = 'verified';
            elseif ($preview_status === 'cancelled') $p_status = 'rejected';
        }
        
        $count_sql = "SELECT COUNT(*) FROM pembayaran WHERE created_at BETWEEN ? AND ?";
        $params = [$dt_from, $dt_to];
        if ($preview_status !== 'all') {
            $count_sql .= " AND status = ?";
            $params[] = $p_status;
        }
        $c_stmt = $pdo->prepare($count_sql);
        $c_stmt->execute($params);
        $total_records = $c_stmt->fetchColumn();
        
        $sql = "
            SELECT p.id, p.booking_id, u.full_name as tenant_name, p.amount, p.status, p.payment_date
            FROM pembayaran p
            JOIN users u ON p.user_id = u.id
            WHERE p.created_at BETWEEN ? AND ?
        ";
        if ($preview_status !== 'all') {
            $sql .= " AND p.status = ?";
        }
        $sql .= " ORDER BY p.created_at DESC LIMIT 5";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $i = 1;
        foreach ($rows as $row) {
            $status_label = 'pending';
            if ($row['status'] === 'verified') $status_label = 'confirmed';
            elseif ($row['status'] === 'rejected') $status_label = 'cancelled';
            
            $preview_rows[] = [
                $i++,
                'PM-' . str_pad($row['id'], 5, '0', STR_PAD_LEFT),
                'BK-' . str_pad($row['booking_id'], 5, '0', STR_PAD_LEFT),
                $row['tenant_name'],
                'Rp ' . number_format($row['amount'], 0, ',', '.'),
                $row['payment_date'] ? date('d M Y', strtotime($row['payment_date'])) : '-',
                $status_label
            ];
        }
    } elseif ($preview_report_type === 'user') {
        $preview_headers = ['No', 'ID User', 'Username', 'Nama Lengkap', 'Role', 'Status Akun', 'Tanggal Daftar'];
        
        $u_status = '';
        if ($preview_status !== 'all') {
            $u_status = 'pending';
            if ($preview_status === 'confirmed') $u_status = 'verified';
            elseif ($preview_status === 'cancelled') $u_status = 'rejected';
        }
        
        $count_sql = "SELECT COUNT(*) FROM users WHERE created_at BETWEEN ? AND ?";
        $params = [$dt_from, $dt_to];
        if ($preview_status !== 'all') {
            $count_sql .= " AND status = ?";
            $params[] = $u_status;
        }
        $c_stmt = $pdo->prepare($count_sql);
        $c_stmt->execute($params);
        $total_records = $c_stmt->fetchColumn();
        
        $sql = "
            SELECT id, username, full_name, role, status, created_at
            FROM users
            WHERE created_at BETWEEN ? AND ?
        ";
        if ($preview_status !== 'all') {
            $sql .= " AND status = ?";
        }
        $sql .= " ORDER BY created_at DESC LIMIT 5";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $i = 1;
        foreach ($rows as $row) {
            $status_label = 'pending';
            if ($row['status'] === 'verified') $status_label = 'completed';
            elseif ($row['status'] === 'rejected') $status_label = 'cancelled';
            
            $preview_rows[] = [
                $i++,
                'USR-' . str_pad($row['id'], 5, '0', STR_PAD_LEFT),
                $row['username'],
                $row['full_name'] ?? '-',
                ucfirst($row['role']),
                ucfirst($row['status']),
                date('d M Y', strtotime($row['created_at'])),
                $status_label
            ];
        }
    } elseif ($preview_report_type === 'kost') {
        $preview_headers = ['No', 'ID Kost', 'Nama Kost', 'Pemilik', 'Tipe', 'Kota', 'Kamar', 'Status'];
        
        $count_sql = "SELECT COUNT(*) FROM kost WHERE created_at BETWEEN ? AND ?";
        $params = [$dt_from, $dt_to];
        $c_stmt = $pdo->prepare($count_sql);
        $c_stmt->execute($params);
        $total_records = $c_stmt->fetchColumn();
        
        $sql = "
            SELECT k.id, k.name, u.full_name as owner_name, k.type, k.city,
                   (SELECT COUNT(*) FROM kamar WHERE kost_id = k.id) as room_count
            FROM kost k
            JOIN users u ON k.owner_id = u.id
            WHERE k.created_at BETWEEN ? AND ?
        ";
        $sql .= " ORDER BY k.created_at DESC LIMIT 5";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $i = 1;
        foreach ($rows as $row) {
            $preview_rows[] = [
                $i++,
                'KST-' . str_pad($row['id'], 5, '0', STR_PAD_LEFT),
                $row['name'],
                $row['owner_name'],
                $row['type'],
                $row['city'],
                $row['room_count'] . ' Kamar',
                'completed'
            ];
        }
    }
}

include '../../layouts/header.php';
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
.export-card { border-top: 3px solid var(--primary); cursor: pointer; transition: all .2s; }
.export-card:hover { transform: translateY(-3px); box-shadow: var(--box-shadow-hover) !important; }
.export-icon { width:56px;height:56px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.5rem; }
.filter-section { border-left: 4px solid var(--primary); }
.report-type-btn { padding:10px 20px;border-radius:99px;font-weight:600;font-size:.875rem;border:1.5px solid var(--border-color);background:#fff;color:var(--text-muted);cursor:pointer;transition:all .15s;display:inline-flex;align-items:center;gap:6px; }
.report-type-btn.active, .report-type-btn:hover { background:var(--primary);border-color:var(--primary);color:#fff; }
.preview-table thead th { background:linear-gradient(to right,#1E0D3E,#2D1459);color:#fff; }
.badge-status { padding:.35em .8em;border-radius:99px;font-weight:700;font-size:.75rem; }
</style>

<div class="container py-4">

    <!-- Header -->
    <div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1" style="color:var(--dark)"><i class="bi bi-file-earmark-arrow-down-fill me-2" style="color:var(--primary)"></i>Export Laporan</h2>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-3 mb-5">
        <?php foreach ([
            ['Total Booking','bi-calendar-check-fill',$total_bookings,'#00B4BA'],
            ['Total Pendapatan','bi-cash-coin','Rp '.number_format($total_revenue,0,',','.'),'#059669'],
            ['Total User','bi-people-fill',$total_users,'#D97706'],
            ['Total Kost','bi-house-fill',$total_kost,'#DC2626'],
        ] as [$label,$icon,$val,$color]): ?>
        <div class="col-6 col-md-3">
            <div class="card p-3" style="border-top:3px solid <?=$color?>">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="bi <?=$icon?>" style="color:<?=$color?>;font-size:1.1rem"></i>
                    <span class="text-muted small fw-600"><?=$label?></span>
                </div>
                <div class="fw-800" style="font-size:1.4rem;color:var(--dark)"><?=$val?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Export Cards -->
    <h5 class="fw-700 mb-3" style="color:var(--dark)"><i class="bi bi-download me-2" style="color:var(--primary)"></i>Format Export</h5>
    <div class="row g-3 mb-5">
        <div class="col-md-4">
            <div class="card export-card p-4" onclick="doExport('pdf')">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="export-icon" style="background:rgba(220,38,38,.1);color:#DC2626"><i class="bi bi-file-earmark-pdf-fill"></i></div>
                    <div>
                        <div class="fw-700">Export PDF / Print</div>
                        <div class="text-muted small">Format laporan siap cetak</div>
                    </div>
                </div>

                <button class="btn btn-danger w-100 fw-600"><i class="bi bi-file-earmark-pdf me-2"></i>Download PDF / Cetak</button>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card export-card p-4" onclick="doExport('excel')" style="border-top-color:#059669">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="export-icon" style="background:rgba(5,150,105,.1);color:#059669"><i class="bi bi-file-earmark-excel-fill"></i></div>
                    <div>
                        <div class="fw-700">Export Excel</div>
                        <div class="text-muted small">Spreadsheet untuk analisis</div>
                    </div>
                </div>

                <button class="btn btn-success w-100 fw-600"><i class="bi bi-file-earmark-excel me-2"></i>Download Excel</button>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card export-card p-4" onclick="doExport('csv')" style="border-top-color:#D97706">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="export-icon" style="background:rgba(217,119,6,.1);color:#D97706"><i class="bi bi-file-earmark-text-fill"></i></div>
                    <div>
                        <div class="fw-700">Export CSV</div>
                        <div class="text-muted small">Raw data universal</div>
                    </div>
                </div>

                <button class="btn btn-warning w-100 fw-600 text-white"><i class="bi bi-file-earmark-text me-2"></i>Download CSV</button>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card filter-section mb-4">
        <div class="card-header fw-700"><i class="bi bi-funnel-fill me-2" style="color:var(--primary)"></i>Filter Laporan</div>
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" id="dateFrom" class="form-control" value="<?= htmlspecialchars($preview_date_from) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" id="dateTo" class="form-control" value="<?= htmlspecialchars($preview_date_to) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jenis Laporan</label>
                    <select id="reportType" class="form-select">
                        <option value="booking" <?= $preview_report_type === 'booking' ? 'selected' : '' ?>>Laporan Booking</option>
                        <option value="pembayaran" <?= $preview_report_type === 'pembayaran' ? 'selected' : '' ?>>Laporan Pembayaran</option>
                        <option value="user" <?= $preview_report_type === 'user' ? 'selected' : '' ?>>Laporan User</option>
                        <option value="kost" <?= $preview_report_type === 'kost' ? 'selected' : '' ?>>Laporan Kost</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select id="filterStatus" class="form-select">
                        <option value="all" <?= $preview_status === 'all' ? 'selected' : '' ?>>Semua Status</option>
                        <option value="pending" <?= $preview_status === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="confirmed" <?= $preview_status === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                        <option value="completed" <?= $preview_status === 'completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="cancelled" <?= $preview_status === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
            </div>
            <div class="d-flex gap-2 mt-3 flex-wrap">
                <button class="btn btn-primary" onclick="showPreview()"><i class="bi bi-eye me-2"></i>Preview Laporan</button>
                <button class="btn btn-outline-secondary" onclick="resetFilter()"><i class="bi bi-arrow-counterclockwise me-2"></i>Reset Filter</button>
            </div>
        </div>
    </div>

    <!-- Preview Table -->
    <div id="previewSection" style="display: <?= $is_preview ? 'block' : 'none' ?>">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between fw-700">
                <span><i class="bi bi-table me-2" style="color:var(--primary)"></i>Preview Laporan (Maksimal 5 baris terbaru)</span>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-danger animate-hover" onclick="doExport('pdf')"><i class="bi bi-file-pdf me-1"></i>PDF</button>
                    <button class="btn btn-sm btn-success animate-hover" onclick="doExport('excel')"><i class="bi bi-file-excel me-1"></i>Excel</button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 preview-table">
                        <thead>
                            <tr>
                                <?php foreach ($preview_headers as $h): ?>
                                    <th><?= htmlspecialchars($h) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($preview_rows)): ?>
                                <tr>
                                    <td colspan="<?= count($preview_headers) ?: 1 ?>" class="text-center py-4 text-muted">Tidak ada data yang sesuai dengan filter.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($preview_rows as $row): 
                                    $st_val = end($row);
                                    $row_data = array_slice($row, 0, -1);
                                    
                                    $st_colors = ['pending'=>'#D97706','confirmed'=>'#00B4BA','completed'=>'#059669','cancelled'=>'#DC2626'];
                                    $color = $st_colors[$st_val] ?? '#5C4D78';
                                    
                                    $status_label = ucfirst($st_val);
                                    if ($st_val === 'completed' && $preview_report_type === 'user') $status_label = 'Verified';
                                    if ($st_val === 'completed' && $preview_report_type === 'kost') $status_label = 'Active';
                                ?>
                                <tr>
                                    <?php foreach ($row_data as $idx => $val): ?>
                                        <?php if ($idx === 1): ?>
                                            <td class="fw-700" style="color:var(--primary)"><?= htmlspecialchars($val) ?></td>
                                        <?php else: ?>
                                            <td><?= htmlspecialchars($val) ?></td>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    <td><span class="badge-status" style="background:<?=$color?>18;color:<?=$color?>"><?= $status_label ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="p-3 text-muted small border-top"><i class="bi bi-info-circle me-1"></i>Menampilkan <?= count($preview_rows) ?> dari <?= $total_records ?> data yang cocok. Unduh file lengkap melalui tombol ekspor di atas.</div>
            </div>
        </div>
    </div>
</div>

<script>
function doExport(type) {
    const reportType = document.getElementById('reportType').value;
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;
    const status = document.getElementById('filterStatus').value;
    
    const labels = { pdf: 'PDF', excel: 'Excel', csv: 'CSV' };
    const icons  = { pdf: '📄', excel: '📊', csv: '📋' };
    
    Swal.fire({
        title: `${icons[type]} Export ${labels[type]}`,
        html: `<p>Laporan sedang disiapkan...</p><div class="progress mt-3"><div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width:100%"></div></div>`,
        timer: 1500,
        showConfirmButton: false,
        timerProgressBar: false,
        didOpen: () => { Swal.showLoading(); }
    }).then(() => {
        const url = `export_action.php?type=${type}&report_type=${reportType}&date_from=${dateFrom}&date_to=${dateTo}&status=${status}`;
        window.open(url, '_blank');
    });
}
function showPreview() {
    const reportType = document.getElementById('reportType').value;
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;
    const status = document.getElementById('filterStatus').value;
    
    window.location.href = `export_reports.php?preview=1&report_type=${reportType}&date_from=${dateFrom}&date_to=${dateTo}&status=${status}`;
}
function resetFilter() {
    document.getElementById('dateFrom').value = '<?= date('Y-m-01') ?>';
    document.getElementById('dateTo').value = '<?= date('Y-m-d') ?>';
    document.getElementById('reportType').value = 'booking';
    document.getElementById('filterStatus').value = 'all';
    window.location.href = 'export_reports.php';
}
</script>

<?php include '../../layouts/footer.php'; ?>
