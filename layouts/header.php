<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$base_url = "/" . basename(dirname(__DIR__)) . "/";
$current_page = basename($_SERVER['PHP_SELF']);

// Load Database Config
if (!isset($pdo)) {
    $config_db_path = dirname(__DIR__) . '/config/database.php';
    if (file_exists($config_db_path)) {
        include_once $config_db_path;
    }
}

// Load System Settings
$sys_settings = [];
if (isset($pdo)) {
    try {
        $settings_stmt = $pdo->query("SELECT * FROM system_settings");
        while ($row = $settings_stmt->fetch(PDO::FETCH_ASSOC)) {
            $sys_settings[$row['setting_key']] = $row['setting_value'];
        }
    } catch (Exception $e) {
        // Fail silently
    }
}
$app_name = $sys_settings['app_name'] ?? 'E-KOST SYSTEM';
$support_email = $sys_settings['support_email'] ?? 'support@ekost.com';
$contact_phone = $sys_settings['contact_phone'] ?? '+62 812 3456 7890';

// Global Maintenance Mode Interceptor
$maintenance_mode = intval($sys_settings['maintenance_mode'] ?? 0);
if ($maintenance_mode === 1) {
    $is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    $is_maintenance_page = basename($_SERVER['PHP_SELF']) === 'maintenance.php';
    if (!$is_admin && !$is_maintenance_page) {
        header("Location: " . $base_url . "maintenance.php");
        exit();
    }
}


// Hitung unread messages & notifications if logged in
$unread_count = 0;
$unread_notifications_count = 0;
if (isset($_SESSION['user_id']) && isset($pdo)) {
    try {
        $stmt_unread = $pdo->prepare("SELECT COUNT(*) FROM chat WHERE receiver_id = ? AND is_read = 0");
        $stmt_unread->execute([$_SESSION['user_id']]);
        $unread_count = (int)$stmt_unread->fetchColumn();

        $stmt_unread_notif = $pdo->prepare("SELECT COUNT(*) FROM notifikasi WHERE user_id = ? AND is_read = 0");
        $stmt_unread_notif->execute([$_SESSION['user_id']]);
        $unread_notifications_count = (int)$stmt_unread_notif->fetchColumn();
    } catch (Exception $e) {
        // Silently fail if query fails
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($app_name); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/style.css?v=1.0.1">
    <script>
        window.BASE_URL = '<?php echo $base_url; ?>';
    </script>
    <style>
        @media print {
            .sidebar-wrapper,
            .mobile-topbar,
            .offcanvas-sidebar,
            header,
            footer,
            .btn,
            .no-print {
                display: none !important;
            }
            .content-wrapper, 
            .main-content {
                padding: 0 !important;
                margin: 0 !important;
                border: none !important;
            }
            body {
                background-color: #fff !important;
                color: #000 !important;
                padding: 0 !important;
            }
        }
    </style>
</head>
<body class="<?php echo $current_page != 'index.php' ? 'sidebar-layout' : ''; ?>">

<?php if ($current_page == 'index.php'): ?>
    <!-- TOP NAVBAR (Only for Home Page) -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-elegant fixed-top">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="<?php echo $base_url; ?>index.php">
                    <img src="<?php echo $base_url; ?>assets/images/logo.png" alt="Logo" class="me-2 rounded" style="height: 32px; width: 32px; object-fit: contain;">
                    <?php
                    $app_name_parts = explode(' ', $app_name, 2);
                    echo htmlspecialchars($app_name_parts[0]);
                    if (isset($app_name_parts[1])) {
                        echo ' <span>' . htmlspecialchars($app_name_parts[1]) . '</span>';
                    }
                    ?>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-center">
                        <li class="nav-item">
                            <a class="nav-link active" href="<?php echo $base_url; ?>index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>pages/kost_list.php">Cari Kost</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>pages/barang_api.php">Katalog Barang</a>
                        </li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php
                            $chat_module = $_SESSION['role'] === 'owner' ? 'owner' : 'user';
                            $dashboard_url = $base_url . 'modules/' . $_SESSION['role'] . '/dashboard.php';
                            ?>
                            <?php if ($_SESSION['role'] !== 'admin'): ?>
                            <li class="nav-item me-2">
                                <a class="nav-link position-relative" href="<?php echo $base_url; ?>modules/<?php echo $chat_module; ?>/chat.php" title="Pesan">
                                    <i class="bi bi-chat-dots-fill fs-5"></i>
                                    <?php if ($unread_count > 0 || $unread_notifications_count > 0): ?>
                                        <span class="position-absolute bg-danger border border-white rounded-circle" style="top: 8px; right: 4px; width: 8px; height: 8px;"></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                            <?php endif; ?>
                            <li class="nav-item dropdown">
                                <a class="btn btn-primary btn-sm dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                    <li><a class="dropdown-item fw-bold" href="<?php echo $dashboard_url; ?>">Dashboard Saya</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="<?php echo $base_url; ?>modules/auth/logout.php">Logout</a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li class="nav-item"><a class="nav-link" href="<?php echo $base_url; ?>modules/auth/login.php">Login</a></li>
                            <li class="nav-item"><a class="btn btn-primary ms-lg-2 text-white" href="<?php echo $base_url; ?>modules/auth/register.php">Daftar</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <main class="main-content py-0">
<?php else: ?>
    <!-- SIDEBAR LAYOUT (For all pages EXCEPT Home) -->
    <?php
    $is_logged_in = isset($_SESSION['user_id']);
    $user_role = $is_logged_in ? $_SESSION['role'] : '';

    // Define menu items based on roles
    $menu_items = [];
    if ($is_logged_in) {
        if ($user_role === 'admin') {
            $menu_items = [
                ['label' => 'Dashboard', 'url' => $base_url . 'modules/admin/dashboard.php', 'icon' => 'bi-speedometer2'],
                ['label' => 'Kelola User', 'url' => $base_url . 'modules/admin/users.php', 'icon' => 'bi-people'],
                ['label' => 'Verifikasi User', 'url' => $base_url . 'modules/admin/user_verify.php', 'icon' => 'bi-person-check'],
                ['label' => 'Kelola Kost', 'url' => $base_url . 'modules/admin/kost.php', 'icon' => 'bi-house'],
                ['label' => 'Export Laporan', 'url' => $base_url . 'modules/admin/export_reports.php', 'icon' => 'bi-file-earmark-arrow-down'],
                ['label' => 'Laporan Sistem', 'url' => $base_url . 'modules/admin/reports.php', 'icon' => 'bi-file-earmark-bar-graph'],
                ['label' => 'Backup Data', 'url' => $base_url . 'modules/admin/backup.php', 'icon' => 'bi-database'],
                ['label' => 'Pengaturan', 'url' => $base_url . 'modules/admin/settings.php', 'icon' => 'bi-gear'],
            ];
        } elseif ($user_role === 'owner') {
            $menu_items = [
                ['label' => 'Dashboard', 'url' => $base_url . 'modules/owner/dashboard.php', 'icon' => 'bi-speedometer2'],
                ['label' => 'Notifikasi', 'url' => $base_url . 'modules/owner/notifications.php', 'icon' => 'bi-bell', 'badge' => $unread_notifications_count],
                ['label' => 'Kelola Kost', 'url' => $base_url . 'modules/owner/kost_manage.php', 'icon' => 'bi-house-gear'],
                ['label' => 'Status Kamar', 'url' => $base_url . 'modules/owner/room_status.php', 'icon' => 'bi-door-open'],
                ['label' => 'Booking Masuk', 'url' => $base_url . 'modules/owner/bookings.php', 'icon' => 'bi-calendar-check'],
                ['label' => 'Chat', 'url' => $base_url . 'modules/owner/chat.php', 'icon' => 'bi-chat-dots', 'badge' => $unread_count],
                ['label' => 'Profil Saya', 'url' => $base_url . 'modules/owner/profile.php', 'icon' => 'bi-person-circle'],
            ];
        } else { // user / tenant
            $menu_items = [
                ['label' => 'Dashboard', 'url' => $base_url . 'modules/user/dashboard.php', 'icon' => 'bi-speedometer2'],
                ['label' => 'Notifikasi', 'url' => $base_url . 'modules/user/notifications.php', 'icon' => 'bi-bell', 'badge' => $unread_notifications_count],
                ['label' => 'Pesanan Saya', 'url' => $base_url . 'modules/user/bookings.php', 'icon' => 'bi-calendar-check'],
                ['label' => 'Review & Rating', 'url' => $base_url . 'modules/user/reviews.php', 'icon' => 'bi-star'],
                ['label' => 'Favorit', 'url' => $base_url . 'modules/user/favorites.php', 'icon' => 'bi-heart'],
                ['label' => 'Chat', 'url' => $base_url . 'modules/user/chat.php', 'icon' => 'bi-chat-dots', 'badge' => $unread_count],
                ['label' => 'Profil Saya', 'url' => $base_url . 'modules/user/profile.php', 'icon' => 'bi-person-circle'],
            ];
        }
    } else {
        $menu_items = [
            ['label' => 'Home', 'url' => $base_url . 'index.php', 'icon' => 'bi-house-door'],
            ['label' => 'Cari Kost', 'url' => $base_url . 'pages/kost_list.php', 'icon' => 'bi-search'],
            ['label' => 'Katalog Barang', 'url' => $base_url . 'pages/barang_api.php', 'icon' => 'bi-box-seam'],
        ];
    }

    // Link active checker
    function getLinkActive($item_url) {
        $current_path = $_SERVER['PHP_SELF'];
        $item_path = parse_url($item_url, PHP_URL_PATH);
        $item_file = basename($item_path);
        
        if (basename($current_path) == $item_file) {
            return 'active';
        }
        
        // Match child paths
        if ($item_file == 'kost_manage.php' && in_array(basename($current_path), ['kost_manage.php', 'kost_add.php', 'kost_edit.php', 'room_manage.php', 'room_add.php', 'room_edit.php'])) {
            return 'active';
        }
        if ($item_file == 'users.php' && in_array(basename($current_path), ['users.php', 'user_add.php', 'user_edit.php'])) {
            return 'active';
        }
        if ($item_file == 'kost.php' && in_array(basename($current_path), ['kost.php', 'kost_verify.php'])) {
            return 'active';
        }
        
        return '';
    }

    // Left Sidebar content rendering function
    function renderSidebarContent($menu_items, $base_url, $is_logged_in, $user_role, $unread_count) {
        global $app_name;
        ?>
        <!-- Brand -->
        <div class="sidebar-brand text-center text-lg-start">
            <a href="<?php echo $base_url; ?>index.php" class="sidebar-brand-link d-flex align-items-center justify-content-center justify-content-lg-start">
                <img src="<?php echo $base_url; ?>assets/images/logo.png" alt="Logo" class="me-2 rounded" style="height: 32px; width: 32px; background-color: white; padding: 2px; object-fit: contain;">
                <?php
                $app_name_parts = explode(' ', $app_name, 2);
                echo htmlspecialchars($app_name_parts[0]);
                if (isset($app_name_parts[1])) {
                    echo ' <span>' . htmlspecialchars($app_name_parts[1]) . '</span>';
                }
                ?>
            </a>
        </div>

        <!-- User profile section if logged in -->
        <?php if ($is_logged_in): ?>
            <div class="sidebar-profile">
                <div class="profile-name"><i class="bi bi-person-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['username']); ?></div>
                <div class="profile-role">
                    <?php 
                    if ($user_role === 'admin') echo 'Administrator';
                    elseif ($user_role === 'owner') echo 'Pemilik Kost';
                    else echo 'Penyewa';
                    ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Links -->
        <div class="sidebar-menu d-flex flex-column justify-content-between">
            <div>
                <div class="sidebar-menu-section-title">
                    <?php echo $is_logged_in ? 'Dashboard Menu' : 'Navigasi'; ?>
                </div>
                <?php foreach ($menu_items as $item): ?>
                    <a class="sidebar-nav-link <?php echo getLinkActive($item['url']); ?>" href="<?php echo $item['url']; ?>">
                        <i class="bi <?php echo $item['icon']; ?>"></i> 
                        <span><?php echo $item['label']; ?></span>
                        <?php if (isset($item['badge']) && $item['badge'] > 0): ?>
                            <span class="badge bg-danger ms-auto"><?php echo $item['badge']; ?></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>

                <?php if ($is_logged_in): ?>
                    <div class="sidebar-menu-section-title mt-4">Jelajahi</div>
                    <a class="sidebar-nav-link <?php echo getLinkActive($base_url . 'index.php'); ?>" href="<?php echo $base_url; ?>index.php">
                        <i class="bi bi-house-door"></i> <span>Home</span>
                    </a>
                    <a class="sidebar-nav-link <?php echo getLinkActive($base_url . 'pages/kost_list.php'); ?>" href="<?php echo $base_url; ?>pages/kost_list.php">
                        <i class="bi bi-search"></i> <span>Cari Kost</span>
                    </a>
                    <a class="sidebar-nav-link <?php echo getLinkActive($base_url . 'pages/barang_api.php'); ?>" href="<?php echo $base_url; ?>pages/barang_api.php">
                        <i class="bi bi-box-seam"></i> <span>Katalog Barang</span>
                    </a>
                <?php endif; ?>
            </div>

            <div>
                <div class="sidebar-menu-section-title mt-4">Akun</div>
                <?php if ($is_logged_in): ?>
                    <a class="sidebar-nav-link text-danger" href="<?php echo $base_url; ?>modules/auth/logout.php">
                        <i class="bi bi-box-arrow-right"></i> <span>Logout</span>
                    </a>
                <?php else: ?>
                    <a class="sidebar-nav-link <?php echo getLinkActive($base_url . 'modules/auth/login.php'); ?>" href="<?php echo $base_url; ?>modules/auth/login.php">
                        <i class="bi bi-box-arrow-in-right"></i> <span>Login</span>
                    </a>
                    <a class="sidebar-nav-link <?php echo getLinkActive($base_url . 'modules/auth/register.php'); ?>" href="<?php echo $base_url; ?>modules/auth/register.php">
                        <i class="bi bi-person-plus"></i> <span>Register</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
    ?>

    <!-- Desktop Persistent Left Sidebar -->
    <div class="d-flex min-vh-100 flex-column flex-lg-row w-100">
        <aside class="sidebar-wrapper d-none d-lg-flex">
            <?php renderSidebarContent($menu_items, $base_url, $is_logged_in, $user_role, $unread_count); ?>
        </aside>

        <!-- Mobile Header Navigation -->
        <div class="mobile-topbar d-flex d-lg-none w-100 justify-content-between align-items-center">
            <a href="<?php echo $base_url; ?>index.php" class="navbar-brand d-flex align-items-center">
                <img src="<?php echo $base_url; ?>assets/images/logo.png" alt="Logo" class="me-2 rounded" style="height: 28px; width: 28px; object-fit: contain;">
                <?php
                $app_name_parts = explode(' ', $app_name, 2);
                echo htmlspecialchars($app_name_parts[0]);
                if (isset($app_name_parts[1])) {
                    echo ' <span>' . htmlspecialchars($app_name_parts[1]) . '</span>';
                }
                ?>
            </a>
            <button class="btn btn-outline-dark border-0 p-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas">
                <i class="bi bi-list fs-3"></i>
            </button>
        </div>

        <!-- Mobile Offcanvas Sidebar Drawer -->
        <div class="offcanvas offcanvas-start offcanvas-sidebar d-lg-none" tabindex="-1" id="sidebarOffcanvas">
            <div class="offcanvas-header justify-content-end p-3 pb-0">
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body p-0 d-flex flex-column h-100">
                <?php renderSidebarContent($menu_items, $base_url, $is_logged_in, $user_role, $unread_count); ?>
            </div>
        </div>

        <!-- Right Side Main Content Panel -->
        <div class="flex-grow-1 d-flex flex-column content-wrapper" style="min-width: 0;">
            <main class="main-content py-4 px-3 px-lg-4 flex-grow-1">
<?php endif; ?>
