<?php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Stats
$total_users    = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_kost     = $pdo->query("SELECT COUNT(*) FROM kost")->fetchColumn();
$total_bookings = $pdo->query("SELECT COUNT(*) FROM booking")->fetchColumn();
$total_revenue  = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM pembayaran WHERE status='verified'")->fetchColumn();

// Monthly booking data (last 6 months)
$booking_monthly = $pdo->query("
    SELECT DATE_FORMAT(created_at,'%b %Y') as month, MONTH(created_at) as m, YEAR(created_at) as y, COUNT(*) as total
    FROM booking WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY y,m,month ORDER BY y,m
")->fetchAll();

// Monthly revenue data
$revenue_monthly = $pdo->query("
    SELECT DATE_FORMAT(created_at,'%b %Y') as month, MONTH(created_at) as m, YEAR(created_at) as y, COALESCE(SUM(amount),0) as total
    FROM pembayaran WHERE status='verified' AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY y,m,month ORDER BY y,m
")->fetchAll();

// Room status
$room_available = $pdo->query("SELECT COUNT(*) FROM kamar WHERE status='available'")->fetchColumn();
$room_full      = $pdo->query("SELECT COUNT(*) FROM kamar WHERE status='full'")->fetchColumn();

// Users by role
$users_by_role = $pdo->query("SELECT role, COUNT(*) as total FROM users GROUP BY role")->fetchAll();

// Recent users
$recent_users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll();

include '../../layouts/header.php';
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
.stat-card-admin {
    position: relative; overflow: hidden;
    transition: transform .2s ease, box-shadow .2s ease;
}
.stat-card-admin:hover { transform: translateY(-3px); }
.stat-card-admin .stat-icon {
    width: 52px; height: 52px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem; flex-shrink: 0;
}
.stat-card-admin .stat-val { font-size: 1.8rem; font-weight: 800; line-height: 1; margin-bottom: 4px; }
.stat-card-admin .stat-label { font-size: .8rem; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; }
.stat-card-admin::after {
    content: ''; position: absolute; right: -20px; top: -20px;
    width: 100px; height: 100px; border-radius: 50%;
    background: rgba(255,255,255,.07); pointer-events: none;
}
.chart-card { border-top: 3px solid var(--primary); }
.chart-card .card-header { font-weight: 700; font-size: .9rem; }
</style>

<div class="container py-4">

    <!-- Header -->
    <div class="mb-4">
        <h2 class="fw-bold mb-1" style="color:var(--dark)"><i class="bi bi-speedometer2 me-2" style="color:var(--primary)"></i>Dashboard Admin</h2>
        <p class="text-muted mb-0">Ringkasan statistik dan performa platform E-KOST System</p>
    </div>

    <!-- Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card stat-card-admin p-3" style="background:linear-gradient(135deg,#00B4BA,#007F85);border:none;">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:rgba(255,255,255,.15);color:#fff"><i class="bi bi-people-fill"></i></div>
                    <div>
                        <div class="stat-val text-white"><?= number_format($total_users) ?></div>
                        <div class="stat-label" style="color:rgba(255,255,255,.8)">Total User</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card stat-card-admin p-3" style="background:linear-gradient(135deg,#2D1459,#1E0D3E);border:none;">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:rgba(255,255,255,.15);color:#fff"><i class="bi bi-house-fill"></i></div>
                    <div>
                        <div class="stat-val text-white"><?= number_format($total_kost) ?></div>
                        <div class="stat-label" style="color:rgba(255,255,255,.8)">Total Kost</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card stat-card-admin p-3" style="background:linear-gradient(135deg,#D97706,#B45309);border:none;">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:rgba(255,255,255,.15);color:#fff"><i class="bi bi-calendar-check-fill"></i></div>
                    <div>
                        <div class="stat-val text-white"><?= number_format($total_bookings) ?></div>
                        <div class="stat-label" style="color:rgba(255,255,255,.8)">Total Booking</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card stat-card-admin p-3" style="background:linear-gradient(135deg,#059669,#047857);border:none;">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:rgba(255,255,255,.15);color:#fff"><i class="bi bi-cash-coin"></i></div>
                    <div>
                        <div class="stat-val text-white" style="font-size:1.2rem">Rp <?= number_format($total_revenue/1000000, 1) ?>Jt</div>
                        <div class="stat-label" style="color:rgba(255,255,255,.8)">Pendapatan</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card chart-card h-100">
                <div class="card-header"><i class="bi bi-bar-chart-fill me-2" style="color:var(--primary)"></i>Booking per Bulan</div>
                <div class="card-body"><canvas id="bookingChart" height="120"></canvas></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card chart-card h-100">
                <div class="card-header"><i class="bi bi-door-open-fill me-2" style="color:#D97706"></i>Status Kamar</div>
                <div class="card-body d-flex align-items-center justify-content-center"><canvas id="roomChart" height="180"></canvas></div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card chart-card h-100">
                <div class="card-header"><i class="bi bi-currency-dollar me-2" style="color:#059669"></i>Pendapatan per Bulan</div>
                <div class="card-body"><canvas id="revenueChart" height="120"></canvas></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card chart-card h-100">
                <div class="card-header"><i class="bi bi-person-lines-fill me-2" style="color:#5C4D78"></i>User per Role</div>
                <div class="card-body d-flex align-items-center justify-content-center"><canvas id="userChart" height="180"></canvas></div>
            </div>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <span><i class="bi bi-person-plus-fill me-2" style="color:var(--primary)"></i>Pengguna Terbaru</span>
            <a href="users.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead><tr><th>Username</th><th>Nama Lengkap</th><th>Role</th><th>Email</th><th>Tgl Daftar</th></tr></thead>
                    <tbody>
                        <?php foreach ($recent_users as $u): ?>
                        <tr>
                            <td class="fw-700"><?= htmlspecialchars($u['username']) ?></td>
                            <td><?= htmlspecialchars($u['full_name'] ?: '—') ?></td>
                            <td><span class="badge" style="background:<?= $u['role']==='admin'?'rgba(220,38,38,.12);color:#DC2626':($u['role']==='owner'?'rgba(0,180,186,.12);color:#00B4BA':'rgba(92,77,120,.12);color:#5C4D78') ?>"><?= strtoupper($u['role']) ?></span></td>
                            <td style="font-size:.875rem"><?= htmlspecialchars($u['email']) ?></td>
                            <td style="font-size:.875rem"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
const chartDefaults = {
    font: { family: "'Plus Jakarta Sans', sans-serif" },
    color: '#5C4D78'
};
Chart.defaults.font.family = chartDefaults.font.family;
Chart.defaults.color = chartDefaults.color;

const commonGridColor = 'rgba(30,13,62,.06)';

// Booking Chart
const bkLabels = <?= json_encode(array_column($booking_monthly, 'month')) ?>;
const bkData   = <?= json_encode(array_column($booking_monthly, 'total')) ?>;
// Add demo data if empty
const bookingLabels = bkLabels.length ? bkLabels : ['Jan','Feb','Mar','Apr','Mei','Jun'];
const bookingData   = bkData.length ? bkData.map(Number) : [4,7,5,12,8,15];

new Chart(document.getElementById('bookingChart'), {
    type: 'bar',
    data: {
        labels: bookingLabels,
        datasets: [{
            label: 'Jumlah Booking',
            data: bookingData,
            backgroundColor: 'rgba(0,180,186,.75)',
            borderColor: '#00B4BA',
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: commonGridColor }, ticks: { precision: 0 } },
            x: { grid: { display: false } }
        }
    }
});

// Revenue Chart
const rvLabels = <?= json_encode(array_column($revenue_monthly, 'month')) ?>;
const rvData   = <?= json_encode(array_column($revenue_monthly, 'total')) ?>;
const revLabels = rvLabels.length ? rvLabels : ['Jan','Feb','Mar','Apr','Mei','Jun'];
const revData   = rvData.length ? rvData.map(Number) : [3200000,5600000,4100000,8800000,6200000,11500000];

new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels: revLabels,
        datasets: [{
            label: 'Pendapatan (Rp)',
            data: revData,
            backgroundColor: 'rgba(5,150,105,.1)',
            borderColor: '#059669',
            borderWidth: 2.5,
            pointBackgroundColor: '#059669',
            pointRadius: 5,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: commonGridColor }, ticks: { callback: v => 'Rp ' + (v/1000000).toFixed(1) + 'Jt' } },
            x: { grid: { display: false } }
        }
    }
});

// Room Status Doughnut
new Chart(document.getElementById('roomChart'), {
    type: 'doughnut',
    data: {
        labels: ['Tersedia','Terisi','Maintenance'],
        datasets: [{
            data: [<?= $room_available ?>, <?= $room_full ?>, <?= max(0, (int)$room_available-(int)$room_full) ?>],
            backgroundColor: ['#059669','#DC2626','#D97706'],
            borderWidth: 3,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        cutout: '65%',
        plugins: { legend: { position: 'bottom', labels: { padding: 16, font: { size: 12, weight: '600' } } } }
    }
});

// Users by Role Doughnut
const roleLabels = <?= json_encode(array_column($users_by_role, 'role')) ?>;
const roleCounts = <?= json_encode(array_column($users_by_role, 'total')) ?>;
new Chart(document.getElementById('userChart'), {
    type: 'doughnut',
    data: {
        labels: roleLabels.length ? roleLabels.map(r => r.charAt(0).toUpperCase()+r.slice(1)) : ['Admin','Owner','User'],
        datasets: [{
            data: roleCounts.length ? roleCounts.map(Number) : [1, 5, 20],
            backgroundColor: ['#DC2626','#00B4BA','#5C4D78'],
            borderWidth: 3,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        cutout: '65%',
        plugins: { legend: { position: 'bottom', labels: { padding: 16, font: { size: 12, weight: '600' } } } }
    }
});
</script>

<?php include '../../layouts/footer.php'; ?>
