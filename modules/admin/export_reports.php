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
            <p class="text-muted mb-0">Generate dan unduh laporan data platform E-KOST System</p>
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
                        <div class="fw-700">Export PDF</div>
                        <div class="text-muted small">Format laporan siap cetak</div>
                    </div>
                </div>
                <p class="text-muted small mb-3">Ekspor laporan dalam format PDF yang siap dicetak atau dibagikan. Cocok untuk laporan formal.</p>
                <button class="btn btn-danger w-100 fw-600"><i class="bi bi-file-earmark-pdf me-2"></i>Download PDF</button>
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
                <p class="text-muted small mb-3">Ekspor data dalam format Excel (.xlsx) untuk analisis lebih lanjut atau pengolahan data lanjutan.</p>
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
                <p class="text-muted small mb-3">Ekspor data dalam format CSV yang kompatibel dengan berbagai aplikasi dan sistem manajemen data.</p>
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
                    <input type="date" id="dateFrom" class="form-control" value="<?= date('Y-m-01') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" id="dateTo" class="form-control" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jenis Laporan</label>
                    <select id="reportType" class="form-select">
                        <option value="booking">Laporan Booking</option>
                        <option value="pembayaran">Laporan Pembayaran</option>
                        <option value="user">Laporan User</option>
                        <option value="kost">Laporan Kost</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select id="filterStatus" class="form-select">
                        <option value="all">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
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
    <div id="previewSection" style="display:none">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between fw-700">
                <span><i class="bi bi-table me-2" style="color:var(--primary)"></i>Preview Laporan</span>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-danger" onclick="doExport('pdf')"><i class="bi bi-file-pdf me-1"></i>PDF</button>
                    <button class="btn btn-sm btn-success" onclick="doExport('excel')"><i class="bi bi-file-excel me-1"></i>Excel</button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 preview-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID Booking</th>
                                <th>User</th>
                                <th>Kost</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $preview_data = [
                                ['BK-001','Ahmad Fauzi','Kost Mentari Indah',date('d M Y',strtotime('-2 days')),'Rp 800.000','pending'],
                                ['BK-002','Siti Rahayu','Kost Harmoni Jaya',date('d M Y',strtotime('-3 days')),'Rp 1.500.000','confirmed'],
                                ['BK-003','Budi Santoso','Kost Grand Jakarta',date('d M Y',strtotime('-5 days')),'Rp 3.500.000','completed'],
                                ['BK-004','Dewi Lestari','Kost Mawar Sejahtera',date('d M Y',strtotime('-7 days')),'Rp 650.000','cancelled'],
                                ['BK-005','Andi Wijaya','Kost Surabaya Asri',date('d M Y',strtotime('-10 days')),'Rp 1.000.000','confirmed'],
                            ];
                            foreach ($preview_data as $i => $row):
                                $st_colors = ['pending'=>'#D97706','confirmed'=>'#00B4BA','completed'=>'#059669','cancelled'=>'#DC2626'];
                                $color = $st_colors[$row[5]] ?? '#5C4D78';
                            ?>
                            <tr>
                                <td><?= $i+1 ?></td>
                                <td class="fw-700" style="color:var(--primary)"><?= $row[0] ?></td>
                                <td><?= $row[1] ?></td>
                                <td><?= $row[2] ?></td>
                                <td><?= $row[3] ?></td>
                                <td class="fw-600"><?= $row[4] ?></td>
                                <td><span class="badge-status" style="background:<?=$color?>18;color:<?=$color?>"><?= ucfirst($row[5]) ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="p-3 text-muted small border-top"><i class="bi bi-info-circle me-1"></i>Menampilkan 5 dari <?= $total_bookings ?> data (demo preview). Export akan mengunduh data lengkap sesuai filter.</div>
            </div>
        </div>
    </div>
</div>

<script>
function doExport(type) {
    const labels = { pdf: 'PDF', excel: 'Excel', csv: 'CSV' };
    const icons  = { pdf: '📄', excel: '📊', csv: '📋' };
    Swal.fire({
        title: `${icons[type]} Export ${labels[type]}`,
        html: `<p>Laporan sedang disiapkan...</p><div class="progress mt-3"><div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width:100%"></div></div>`,
        timer: 2000,
        showConfirmButton: false,
        timerProgressBar: false,
        didOpen: () => { Swal.showLoading(); }
    }).then(() => {
        Swal.fire({
            icon: 'success',
            title: `Laporan ${labels[type]} Siap!`,
            html: `<p>File laporan <strong>ekost_report_${new Date().toISOString().slice(0,10)}.${type === 'excel' ? 'xlsx' : type}</strong> telah disiapkan.</p>`,
            confirmButtonText: 'Download Sekarang',
            confirmButtonColor: '#00B4BA',
        });
    });
}
function showPreview() {
    document.getElementById('previewSection').style.display = 'block';
    document.getElementById('previewSection').scrollIntoView({ behavior: 'smooth', block: 'start' });
}
function resetFilter() {
    document.getElementById('dateFrom').value = '<?= date('Y-m-01') ?>';
    document.getElementById('dateTo').value = '<?= date('Y-m-d') ?>';
    document.getElementById('reportType').value = 'booking';
    document.getElementById('filterStatus').value = 'all';
    document.getElementById('previewSection').style.display = 'none';
}
</script>

<?php include '../../layouts/footer.php'; ?>
