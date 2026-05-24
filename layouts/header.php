<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$base_url = "/" . basename(dirname(__DIR__)) . "/";
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-KOST SYSTEM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700;800;900&family=Archivo+Black&family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/style.css">
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?php echo $base_url; ?>index.php">E-KOST SYSTEM</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'index.php' ? 'active fw-black' : ''; ?>" href="<?php echo $base_url; ?>index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'kost_list.php' ? 'active fw-black' : ''; ?>" href="<?php echo $base_url; ?>pages/kost_list.php">Cari Kost</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'barang_api.php' ? 'active fw-black' : ''; ?>" href="<?php echo $base_url; ?>pages/barang_api.php" style="display:flex;align-items:center;gap:5px;">
                            <span style="background:#FF5C00;color:#fff;border:2px solid #000;padding:1px 6px;font-family:'Archivo Black',sans-serif;font-size:.6rem;text-transform:uppercase;letter-spacing:.5px;border-radius:0;margin-right:2px;">REACT</span>Katalog Barang
                        </a>
                    </li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <?php
                        // Hitung unread messages
                        $unread_count = 0;
                        if (isset($pdo)) {
                            $stmt_unread = $pdo->prepare("SELECT COUNT(*) FROM chat WHERE receiver_id = ? AND is_read = 0");
                            $stmt_unread->execute([$_SESSION['user_id']]);
                            $unread_count = (int)$stmt_unread->fetchColumn();
                        }
                        $chat_module = $_SESSION['role'] === 'owner' ? 'owner' : 'user';
                        ?>
                        <li class="nav-item">
                            <a class="nav-link position-relative <?php echo $current_page == 'chat.php' ? 'active fw-black' : ''; ?>" href="<?php echo $base_url; ?>modules/<?php echo $chat_module; ?>/chat.php">
                                <i class="bi bi-chat-dots-fill"></i>
                                <?php if ($unread_count > 0): ?>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill"
                                          style="background:#FFD600;color:#000;font-size:.6rem;padding:3px 6px;">
                                        <?php echo $unread_count > 9 ? '9+' : $unread_count; ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> <?php echo $_SESSION['username']; ?>
                            </a>
                            <ul class="dropdown-menu">
                                <?php if($_SESSION['role'] == 'admin'): ?>
                                    <li><a class="dropdown-item" href="<?php echo $base_url; ?>modules/admin/dashboard.php">Admin Dashboard</a></li>
                                <?php elseif($_SESSION['role'] == 'owner'): ?>
                                    <li><a class="dropdown-item" href="<?php echo $base_url; ?>modules/owner/dashboard.php">Owner Dashboard</a></li>
                                <?php else: ?>
                                    <li><a class="dropdown-item" href="<?php echo $base_url; ?>modules/user/dashboard.php">My Dashboard</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?php echo $base_url; ?>modules/auth/logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_url; ?>modules/auth/login.php">Login</a></li>
                        <li class="nav-item"><a class="btn btn-warning ms-lg-2" href="<?php echo $base_url; ?>modules/auth/register.php">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>
<main class="main-content py-4">
