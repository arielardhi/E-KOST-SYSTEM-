<?php
require_once 'config/database.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load System Settings
$sys_settings = [];
try {
    $settings_stmt = $pdo->query("SELECT * FROM system_settings");
    while ($row = $settings_stmt->fetch(PDO::FETCH_ASSOC)) {
        $sys_settings[$row['setting_key']] = $row['setting_value'];
    }
} catch (Exception $e) {
    // Fail silently
}
$app_name = $sys_settings['app_name'] ?? 'E-KOST SYSTEM';
$support_email = $sys_settings['support_email'] ?? 'support@ekost.com';
$contact_phone = $sys_settings['contact_phone'] ?? '+62 812 3456 7890';
$maintenance_mode = intval($sys_settings['maintenance_mode'] ?? 0);

// If not in maintenance mode, redirect to index
if ($maintenance_mode === 0) {
    header("Location: index.php");
    exit();
}

// If logged in as admin, allow going to dashboard
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: modules/admin/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemeliharaan Sistem — <?php echo htmlspecialchars($app_name); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at 50% 50%, #201048 0%, #0d0620 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            overflow: hidden;
            position: relative;
        }
        
        /* Ambient light effects */
        .ambient-light-1 {
            position: absolute;
            width: 350px;
            height: 350px;
            background: rgba(0, 180, 186, 0.15);
            border-radius: 50%;
            filter: blur(80px);
            top: 15%;
            left: 20%;
            animation: floatLight 10s ease-in-out infinite alternate;
        }
        .ambient-light-2 {
            position: absolute;
            width: 400px;
            height: 400px;
            background: rgba(232, 92, 80, 0.1);
            border-radius: 50%;
            filter: blur(100px);
            bottom: 10%;
            right: 15%;
            animation: floatLight 12s ease-in-out infinite alternate-reverse;
        }
        
        @keyframes floatLight {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(50px, 30px) scale(1.1); }
        }

        .maintenance-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 48px 40px;
            max-width: 580px;
            width: 90%;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.1);
            text-align: center;
            position: relative;
            z-index: 10;
        }
        
        /* Status pulse animation */
        .status-pulse-container {
            width: 100px;
            height: 100px;
            margin: 0 auto 28px;
            background: rgba(0, 180, 186, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            border: 1.5px solid rgba(0, 180, 186, 0.2);
        }
        .status-pulse-dot {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #00b4ba 0%, #0ea5e9 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 20px rgba(0, 180, 186, 0.4);
            font-size: 1.5rem;
        }
        .status-pulse-ring {
            position: absolute;
            width: 100%;
            height: 100%;
            border: 2px solid #00b4ba;
            border-radius: 50%;
            animation: pulseRing 2s cubic-bezier(0.215, 0.610, 0.355, 1) infinite;
            opacity: 0;
        }
        
        @keyframes pulseRing {
            0% { transform: scale(0.65); opacity: 0; }
            50% { opacity: 0.5; }
            100% { transform: scale(1.2); opacity: 0; }
        }

        .app-logo {
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #00b4ba;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .app-logo img {
            height: 20px;
            object-fit: contain;
        }
        
        .title {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            margin-bottom: 16px;
            background: linear-gradient(135deg, #ffffff 0%, #e2e8f0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .desc {
            font-size: 0.95rem;
            color: #94a3b8;
            line-height: 1.6;
            margin-bottom: 32px;
            font-weight: 500;
        }
        
        /* Info block */
        .info-block {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 28px;
        }
        .info-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 0.9rem;
        }
        .info-item:not(:last-child) {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .info-label {
            color: #64748b;
            font-weight: 600;
        }
        .info-value {
            color: #e2e8f0;
            font-weight: 700;
            text-decoration: none;
            transition: color 0.15s ease;
        }
        a.info-value:hover {
            color: #00b4ba;
        }
        
        .footer-action {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }
        .btn-admin {
            font-size: 0.8rem;
            font-weight: 700;
            color: #64748b;
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: color 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .btn-admin:hover {
            color: #00b4ba;
            background: rgba(0, 180, 186, 0.05);
            border-color: rgba(0, 180, 186, 0.2);
        }
        
        .logout-link {
            font-size: 0.8rem;
            font-weight: 600;
            color: #ef4444;
            text-decoration: none;
            transition: opacity 0.15s ease;
        }
        .logout-link:hover {
            opacity: 0.8;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="ambient-light-1"></div>
    <div class="ambient-light-2"></div>
    
    <div class="maintenance-card">
        <div class="app-logo">
            <img src="assets/images/logo.png" alt="Logo">
            <?php echo htmlspecialchars($app_name); ?>
        </div>
        
        <div class="status-pulse-container">
            <div class="status-pulse-ring"></div>
            <div class="status-pulse-dot">
                <i class="bi bi-tools text-white"></i>
            </div>
        </div>
        
        <h1 class="title">Situs Sedang Dalam Perbaikan</h1>
        <p class="desc">Kami sedang melakukan pemeliharaan rutin untuk meningkatkan performa dan layanan sistem. Jangan khawatir, kami akan segera kembali online dalam waktu dekat!</p>
        
        <div class="info-block">
            <div class="info-item">
                <span class="info-label">Hubungi Support:</span>
                <a href="mailto:<?php echo htmlspecialchars($support_email); ?>" class="info-value"><?php echo htmlspecialchars($support_email); ?></a>
            </div>
            <div class="info-item">
                <span class="info-label">Telepon Kontak:</span>
                <span class="info-value"><?php echo htmlspecialchars($contact_phone); ?></span>
            </div>
        </div>
        
        <div class="footer-action">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="modules/auth/logout.php" class="logout-link"><i class="bi bi-box-arrow-right me-1"></i> Keluar dari Sesi saat ini (Logout)</a>
            <?php else: ?>
                <a href="modules/auth/login.php" class="btn-admin"><i class="bi bi-shield-lock-fill"></i> Area Staf / Admin</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
