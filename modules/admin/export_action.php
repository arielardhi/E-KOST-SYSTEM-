<?php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("Akses ditolak. Anda bukan Administrator.");
}

$type        = $_GET['type'] ?? 'csv';
$report_type = $_GET['report_type'] ?? 'booking';
$date_from   = $_GET['date_from'] ?? date('Y-m-01');
$date_to     = $_GET['date_to'] ?? date('Y-m-d');
$status      = $_GET['status'] ?? 'all';

$datetime_from = $date_from . ' 00:00:00';
$datetime_to   = $date_to . ' 23:59:59';

$data = [];
$headers = [];

// Formulate query based on report type
if ($report_type === 'booking') {
    $headers = ['No', 'ID Booking', 'Nama Penyewa', 'Nama Kost', 'Kamar', 'Mulai Sewa', 'Durasi', 'Total Harga', 'Status', 'Tanggal Dibuat'];
    $sql = "
        SELECT b.id as booking_id, u.full_name as tenant_name, k.name as kost_name, km.room_name, 
               b.start_date, b.duration_months, b.total_price, b.status, b.created_at
        FROM booking b
        JOIN users u ON b.user_id = u.id
        JOIN kamar km ON b.kamar_id = km.id
        JOIN kost k ON km.kost_id = k.id
        WHERE b.created_at BETWEEN ? AND ?
    ";
    $params = [$datetime_from, $datetime_to];
    if ($status !== 'all') {
        $sql .= " AND b.status = ?";
        $params[] = $status;
    }
    $sql .= " ORDER BY b.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $i = 1;
    foreach ($rows as $row) {
        $data[] = [
            $i++,
            'BK-' . str_pad($row['booking_id'], 5, '0', STR_PAD_LEFT),
            $row['tenant_name'],
            $row['kost_name'],
            $row['room_name'],
            date('d-m-Y', strtotime($row['start_date'])),
            $row['duration_months'] . ' Bulan',
            'Rp ' . number_format($row['total_price'], 0, ',', '.'),
            ucfirst($row['status']),
            date('d-m-Y H:i', strtotime($row['created_at']))
        ];
    }
} elseif ($report_type === 'pembayaran') {
    $headers = ['No', 'ID Pembayaran', 'ID Booking', 'Nama Penyewa', 'Jumlah Bayar', 'Status Verifikasi', 'Tanggal Bayar', 'Tanggal Dibuat'];
    $sql = "
        SELECT p.id as payment_id, p.booking_id, u.full_name as tenant_name, p.amount, p.status, p.payment_date, p.created_at
        FROM pembayaran p
        JOIN users u ON p.user_id = u.id
        WHERE p.created_at BETWEEN ? AND ?
    ";
    $params = [$datetime_from, $datetime_to];
    if ($status !== 'all') {
        // Map filterStatus to pembayaran status
        $p_status = 'pending';
        if ($status === 'confirmed') $p_status = 'verified';
        elseif ($status === 'cancelled') $p_status = 'rejected';
        
        $sql .= " AND p.status = ?";
        $params[] = $p_status;
    }
    $sql .= " ORDER BY p.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $i = 1;
    foreach ($rows as $row) {
        $status_label = 'Pending';
        if ($row['status'] === 'verified') $status_label = 'Terverifikasi';
        elseif ($row['status'] === 'rejected') $status_label = 'Ditolak';

        $data[] = [
            $i++,
            'PM-' . str_pad($row['payment_id'], 5, '0', STR_PAD_LEFT),
            'BK-' . str_pad($row['booking_id'], 5, '0', STR_PAD_LEFT),
            $row['tenant_name'],
            'Rp ' . number_format($row['amount'], 0, ',', '.'),
            $status_label,
            $row['payment_date'] ? date('d-m-Y H:i', strtotime($row['payment_date'])) : '-',
            date('d-m-Y H:i', strtotime($row['created_at']))
        ];
    }
} elseif ($report_type === 'user') {
    $headers = ['No', 'ID User', 'Username', 'Email', 'Nama Lengkap', 'No. HP', 'Role', 'Status Akun', 'Tanggal Daftar'];
    $sql = "
        SELECT id, username, email, full_name, phone, role, status, created_at
        FROM users
        WHERE created_at BETWEEN ? AND ?
    ";
    $params = [$datetime_from, $datetime_to];
    if ($status !== 'all') {
        $u_status = 'pending';
        if ($status === 'confirmed') $u_status = 'verified';
        elseif ($status === 'cancelled') $u_status = 'rejected';
        
        $sql .= " AND status = ?";
        $params[] = $u_status;
    }
    $sql .= " ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $i = 1;
    foreach ($rows as $row) {
        $data[] = [
            $i++,
            'USR-' . str_pad($row['id'], 5, '0', STR_PAD_LEFT),
            $row['username'],
            $row['email'],
            $row['full_name'] ?? '-',
            $row['phone'] ?? '-',
            ucfirst($row['role']),
            ucfirst($row['status']),
            date('d-m-Y H:i', strtotime($row['created_at']))
        ];
    }
} elseif ($report_type === 'kost') {
    $headers = ['No', 'ID Kost', 'Nama Kost', 'Pemilik (Owner)', 'Tipe', 'Kota', 'Harga Mulai', 'Jumlah Kamar', 'Tanggal Terdaftar'];
    $sql = "
        SELECT k.id as kost_id, k.name as kost_name, u.full_name as owner_name, k.type, k.city, k.price_start, k.created_at,
               (SELECT COUNT(*) FROM kamar WHERE kost_id = k.id) as room_count
        FROM kost k
        JOIN users u ON k.owner_id = u.id
        WHERE k.created_at BETWEEN ? AND ?
    ";
    $params = [$datetime_from, $datetime_to];
    // Kost doesn't have status filter
    $sql .= " ORDER BY k.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $i = 1;
    foreach ($rows as $row) {
        $data[] = [
            $i++,
            'KST-' . str_pad($row['kost_id'], 5, '0', STR_PAD_LEFT),
            $row['kost_name'],
            $row['owner_name'],
            $row['type'],
            $row['city'],
            'Rp ' . number_format($row['price_start'], 0, ',', '.'),
            $row['room_count'] . ' Kamar',
            date('d-m-Y H:i', strtotime($row['created_at']))
        ];
    }
}

$filename = "ekost_report_" . $report_type . "_" . date('Ymd_His');

// --- EXPORT CSV ---
if ($type === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
    
    $output = fopen('php://output', 'w');
    // Add UTF-8 BOM for Excel compatibility
    fputs($output, chr(239) . chr(187) . chr(191));
    
    fputcsv($output, $headers);
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit();
}

// --- EXPORT EXCEL (HTML Table approach) ---
if ($type === 'excel') {
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=\"" . $filename . ".xls\"");
    header("Pragma: no-cache");
    header("Expires: 0");
    
    echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    echo '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head>';
    echo '<body>';
    echo '<h2>LAPORAN E-KOST SYSTEM</h2>';
    echo '<p><b>Jenis Laporan:</b> ' . strtoupper($report_type) . '</p>';
    echo '<p><b>Periode:</b> ' . date('d M Y', strtotime($date_from)) . ' s/d ' . date('d M Y', strtotime($date_to)) . '</p>';
    echo '<p><b>Filter Status:</b> ' . strtoupper($status) . '</p>';
    echo '<br>';
    echo '<table border="1" cellpadding="5" cellspacing="0">';
    echo '<tr style="background-color: #f2f2f2; font-weight: bold;">';
    foreach ($headers as $h) {
        echo '<th>' . htmlspecialchars($h) . '</th>';
    }
    echo '</tr>';
    foreach ($data as $row) {
        echo '<tr>';
        foreach ($row as $val) {
            echo '<td>' . htmlspecialchars($val) . '</td>';
        }
        echo '</tr>';
    }
    echo '</table>';
    echo '</body></html>';
    exit();
}

// --- PRINT PDF ---
if ($type === 'pdf') {
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cetak Laporan - E-KOST System</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            @media print {
                .no-print { display: none !important; }
                body { font-size: 12px; background: #fff; color: #000; }
                .table th { background-color: #f2f2f2 !important; color: #000 !important; }
            }
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 30px; }
            .report-header { border-bottom: 3px double #000; padding-bottom: 15px; margin-bottom: 25px; }
            .report-title { font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
            .meta-label { font-weight: 600; width: 140px; display: inline-block; }
            .table-styled th { background-color: #1E0D3E; color: #fff; }
        </style>
    </head>
    <body>
        <div class="container-fluid">
            <!-- Print controls -->
            <div class="d-flex justify-content-between align-items-center mb-4 no-print bg-light p-3 border rounded shadow-sm">
                <div>
                    <h5 class="mb-0 fw-bold">Pratinjau Cetak Laporan</h5>
                    <small class="text-muted">Gunakan tombol cetak untuk menyimpan sebagai PDF atau mencetak langsung.</small>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary fw-bold" onclick="window.print()"><i class="bi bi-printer me-2"></i>Cetak Laporan</button>
                    <button class="btn btn-secondary fw-bold" onclick="window.close()">Tutup Halaman</button>
                </div>
            </div>

            <!-- Report Header -->
            <div class="report-header d-flex justify-content-between align-items-end">
                <div>
                    <h1 class="report-title mb-1 text-uppercase">E-KOST System</h1>
                    <p class="text-muted mb-0">Platform Manajemen Kost dan Hunian Terpercaya</p>
                </div>
                <div class="text-end">
                    <h5 class="fw-bold mb-0">LAPORAN DATA PLATFORM</h5>
                    <small class="text-muted">Tanggal Cetak: <?= date('d-m-Y H:i') ?></small>
                </div>
            </div>

            <!-- Meta info -->
            <div class="card p-3 mb-4 bg-light border-0 rounded-3">
                <div class="row g-2">
                    <div class="col-md-6">
                        <div><span class="meta-label">Jenis Laporan:</span> <strong class="text-uppercase"><?= $report_type ?></strong></div>
                        <div><span class="meta-label">Status Filter:</span> <strong class="text-uppercase"><?= $status ?></strong></div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div><span class="meta-label">Rentang Periode:</span> <strong><?= date('d M Y', strtotime($date_from)) ?></strong> s/d <strong><?= date('d M Y', strtotime($date_to)) ?></strong></div>
                        <div><span class="meta-label">Total Data:</span> <strong><?= count($data) ?> baris</strong></div>
                    </div>
                </div>
            </div>

            <!-- Main Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle table-styled">
                    <thead>
                        <tr>
                            <?php foreach ($headers as $h): ?>
                                <th><?= htmlspecialchars($h) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data)): ?>
                            <tr>
                                <td colspan="<?= count($headers) ?>" class="text-center py-4 text-muted">Tidak ada data yang sesuai dengan filter.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($data as $row): ?>
                                <tr>
                                    <?php foreach ($row as $val): ?>
                                        <td><?= htmlspecialchars($val) ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Footer signature -->
            <div class="row mt-5 pt-4">
                <div class="col-6"></div>
                <div class="col-6 text-center">
                    <p class="mb-5">Disetujui dan Dicetak Oleh,</p>
                    <div style="height: 60px;"></div>
                    <h6 class="fw-bold text-decoration-underline mb-0"><?= htmlspecialchars($_SESSION['username'] ?? 'Administrator') ?></h6>
                    <small class="text-muted">System Administrator</small>
                </div>
            </div>
        </div>

        <script>
            // Auto trigger print dialog on page load
            window.addEventListener('load', function() {
                setTimeout(function() {
                    window.print();
                }, 500);
            });
        </script>
    </body>
    </html>
    <?php
    exit();
}
?>
