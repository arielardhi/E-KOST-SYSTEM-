<?php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Handle database backup download
if (isset($_GET['action']) && $_GET['action'] == 'download') {
    try {
        $filename = "ekost_db_backup_" . date("Ymd_His") . ".sql";
        
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Fetch all tables
        $tables = [];
        $stmt = $pdo->query("SHOW TABLES");
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }

        $sql_dump = "-- E-KOST Database Backup\n";
        $sql_dump .= "-- Generated: " . date("Y-m-d H:i:s") . "\n";
        $sql_dump .= "-- Host: $host\n";
        $sql_dump .= "-- Database: $db_name\n";
        $sql_dump .= "-- ------------------------------------------------------\n\n";
        $sql_dump .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            $sql_dump .= "--\n";
            $sql_dump .= "-- Table structure for table `$table`\n";
            $sql_dump .= "--\n\n";
            $sql_dump .= "DROP TABLE IF EXISTS `$table`;\n";

            $create_stmt = $pdo->query("SHOW CREATE TABLE `$table`");
            $create_row = $create_stmt->fetch(PDO::FETCH_NUM);
            $sql_dump .= $create_row[1] . ";\n\n";

            $sql_dump .= "--\n";
            $sql_dump .= "-- Dumping data for table `$table`\n";
            $sql_dump .= "--\n\n";

            $data_stmt = $pdo->query("SELECT * FROM `$table`");
            $rows = $data_stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($rows)) {
                $cols = array_keys($rows[0]);
                $cols_str = implode("`, `", $cols);
                
                $sql_dump .= "INSERT INTO `$table` (`" . $cols_str . "`) VALUES\n";
                
                $values = [];
                foreach ($rows as $row) {
                    $row_values = [];
                    foreach ($cols as $col) {
                        $val = $row[$col];
                        if ($val === null) {
                            $row_values[] = "NULL";
                        } else {
                            $row_values[] = $pdo->quote($val);
                        }
                    }
                    $values[] = "(" . implode(", ", $row_values) . ")";
                }
                
                $sql_dump .= implode(",\n", $values) . ";\n\n";
            } else {
                $sql_dump .= "-- (No data to dump)\n\n";
            }
        }

        $sql_dump .= "SET FOREIGN_KEY_CHECKS=1;\n";
        
        // Update last backup time in session
        $_SESSION['last_backup_time'] = date('d M Y, H:i');
        
        try {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $log_stmt = $pdo->prepare("INSERT INTO system_logs (user_id, username, role, activity, ip_address) VALUES (?, ?, ?, ?, ?)");
            $log_stmt->execute([
                $_SESSION['user_id'],
                $_SESSION['username'] ?? 'admin',
                $_SESSION['role'] ?? 'admin',
                "Mengunduh backup database: $filename",
                $ip
            ]);
        } catch (Exception $log_ex) {
            // Fail silently
        }

        echo $sql_dump;
        exit();
    } catch (Exception $e) {
        die("Terjadi kesalahan saat membackup database: " . $e->getMessage());
    }
}

// Calculate actual database size
$db_size_display = "~2.4 MB";
try {
    $size_query = $pdo->prepare("
        SELECT SUM(data_length + index_length) AS size 
        FROM information_schema.TABLES 
        WHERE table_schema = ?
    ");
    $size_query->execute([$db_name]);
    $db_size_row = $size_query->fetch();
    $db_size_bytes = $db_size_row['size'] ?? 0;
    if ($db_size_bytes > 0) {
        $db_size_mb = round($db_size_bytes / (1024 * 1024), 2);
        if ($db_size_mb < 0.1) {
            $db_size_display = round($db_size_bytes / 1024, 2) . " KB";
        } else {
            $db_size_display = $db_size_mb . " MB";
        }
    }
} catch (Exception $e) {
    // Fallback to default
}

include '../../layouts/header.php';
?>

<div class="container py-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-5 bg-neubrutal-blue">
                <div class="mb-4">
                    <i class="bi bi-database-down display-1"></i>
                </div>
                <h2 class="fw-black text-uppercase mb-3">Backup Database</h2>
                <p class="lead fw-bold mb-4">Klik tombol di bawah untuk mengunduh salinan database sistem saat ini.</p>
                
                <div class="d-grid gap-3">
                    <a href="backup.php?action=download" class="btn btn-dark btn-lg py-3">UNDUH SQL BACKUP</a>
                </div>
                
                <div class="mt-4 p-3 border border-2 border-dark bg-white text-start">
                    <small class="fw-bold text-uppercase">Informasi:</small>
                    <ul class="small mb-0 mt-1">
                        <li>Ukuran database: <?php echo $db_size_display; ?></li>
                        <li>Terakhir backup: <?php echo isset($_SESSION['last_backup_time']) ? $_SESSION['last_backup_time'] : date('d M Y, H:i'); ?></li>
                        <li>Format file: .sql</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../layouts/footer.php'; ?>
