<?php
require_once '../../config/database.php';
session_start();

$redirect_uri = $_GET['redirect_uri'] ?? '';
$state = $_GET['state'] ?? 'user'; // role parameter

// Load some existing users from database as options
$mock_users = [];
try {
    $stmt = $pdo->query("SELECT email, full_name, role, avatar FROM users WHERE role != 'admin' ORDER BY id ASC LIMIT 5");
    $mock_users = $stmt->fetchAll();
} catch (Exception $e) {
    // Fail silently if database is not configured
}

// Add some default demo users if DB is empty
if (empty($mock_users)) {
    $mock_users = [
        ['email' => 'user1@ekost.com', 'full_name' => 'User Satu (Demo)', 'role' => 'user', 'avatar' => ''],
        ['email' => 'owner1@ekost.com', 'full_name' => 'Owner Satu (Demo)', 'role' => 'owner', 'avatar' => '']
    ];
}

// If form is submitted for kustom account
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $name = $_POST['full_name'] ?? '';
    $avatar = $_POST['avatar'] ?? '';
    $chosen_role = $_POST['role'] ?? $state;

    if (!empty($email)) {
        // Generate mock profile data
        $profile = [
            'email' => $email,
            'name' => $name ?: explode('@', $email)[0],
            'picture' => $avatar ?: 'https://lh3.googleusercontent.com/a/default-user'
        ];
        
        $mock_code = 'MOCK_CODE_' . base64_encode(json_encode($profile));
        
        // Redirect back to google_auth.php
        $target_url = $redirect_uri ?: 'google_auth.php';
        $separator = (strpos($target_url, '?') === false) ? '?' : '&';
        header("Location: " . $target_url . $separator . "code=" . urlencode($mock_code) . "&state=" . urlencode($chosen_role));
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulasi Login Google</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f4f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .google-card {
            background: #ffffff;
            border-radius: 28px;
            width: 100%;
            max-width: 450px;
            padding: 40px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            border: 1px solid #e0e0e0;
        }
        .google-logo {
            width: 75px;
            margin-bottom: 24px;
        }
        .google-title {
            font-size: 24px;
            font-weight: 500;
            color: #1f1f1f;
            margin-bottom: 8px;
            text-align: center;
        }
        .google-subtitle {
            font-size: 16px;
            color: #444746;
            margin-bottom: 32px;
            text-align: center;
        }
        .google-subtitle span {
            color: #0b57d0;
            font-weight: 500;
        }
        .account-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border-bottom: 1px solid #f1f3f4;
            cursor: pointer;
            transition: background 0.2s ease;
            text-decoration: none;
            color: inherit;
        }
        .account-item:first-child {
            border-top: 1px solid #f1f3f4;
        }
        .account-item:hover {
            background-color: #f8fafd;
        }
        .account-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: #0b57d0;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 12px;
            overflow: hidden;
            font-size: 14px;
        }
        .account-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .account-info {
            flex-grow: 1;
        }
        .account-name {
            font-size: 14px;
            font-weight: 500;
            color: #1f1f1f;
        }
        .account-email {
            font-size: 12px;
            color: #5f6368;
        }
        .account-role {
            font-size: 11px;
            background: #e8f0fe;
            color: #1967d2;
            padding: 2px 8px;
            border-radius: 12px;
            font-weight: 500;
        }
        .custom-account-btn {
            display: flex;
            align-items: center;
            padding: 16px;
            cursor: pointer;
            color: #0b57d0;
            font-weight: 500;
            font-size: 14px;
            border-bottom: 1px solid #f1f3f4;
            transition: background 0.2s ease;
        }
        .custom-account-btn:hover {
            background-color: #f8fafd;
        }
        .custom-form {
            display: none;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #f1f3f4;
        }
        .form-floating > label {
            color: #5f6368;
        }
        .btn-google-blue {
            background-color: #0b57d0;
            color: white;
            border: none;
            border-radius: 100px;
            font-weight: 500;
            padding: 10px 24px;
            transition: background 0.2s;
        }
        .btn-google-blue:hover {
            background-color: #0842a0;
            color: white;
        }
        .footer-text {
            font-size: 12px;
            color: #5f6368;
            margin-top: 32px;
            line-height: 1.5;
            text-align: justify;
        }
        .footer-links {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-top: 16px;
            color: #5f6368;
        }
        .footer-links a {
            color: #5f6368;
            text-decoration: none;
        }
        .footer-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="google-card shadow-lg">
    <div class="text-center">
        <!-- SVG Google Logo -->
        <svg class="google-logo" viewBox="0 0 24 24" width="75" height="30" xmlns="http://www.w3.org/2000/svg">
            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285f4"/>
            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34a853"/>
            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#fbbc05"/>
            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#ea4335"/>
        </svg>
    </div>
    
    <div class="google-title">Pilih akun</div>
    <div class="google-subtitle">untuk melanjutkan ke <span>E-KOST SYSTEM</span></div>

    <!-- Accounts List -->
    <div class="accounts-container">
        <?php foreach ($mock_users as $mu): 
            $initial = strtoupper(substr($mu['full_name'] ?: $mu['email'], 0, 1));
            // Create mock code for this pre-existing account
            $profile = [
                'email' => $mu['email'],
                'name' => $mu['full_name'],
                'picture' => filter_var($mu['avatar'], FILTER_VALIDATE_URL) ? $mu['avatar'] : ''
            ];
            $code = 'MOCK_CODE_' . base64_encode(json_encode($profile));
            $target_url = $redirect_uri ?: 'google_auth.php';
            $separator = (strpos($target_url, '?') === false) ? '?' : '&';
            $href = $target_url . $separator . "code=" . urlencode($code) . "&state=" . urlencode($mu['role']);
        ?>
            <a href="<?= $href ?>" class="account-item">
                <div class="account-avatar">
                    <?php if (!empty($mu['avatar'])): ?>
                        <?php 
                        $avatar_url = filter_var($mu['avatar'], FILTER_VALIDATE_URL) ? $mu['avatar'] : '../../uploads/avatars/' . $mu['avatar'];
                        ?>
                        <img src="<?= htmlspecialchars($avatar_url) ?>" alt="Avatar">
                    <?php else: ?>
                        <?= $initial ?>
                    <?php endif; ?>
                </div>
                <div class="account-info">
                    <div class="account-name"><?= htmlspecialchars($mu['full_name']) ?></div>
                    <div class="account-email"><?= htmlspecialchars($mu['email']) ?></div>
                </div>
                <div class="account-role"><?= strtoupper($mu['role']) ?></div>
            </a>
        <?php endforeach; ?>
        
        <!-- Use custom account -->
        <div class="custom-account-btn" onclick="toggleCustomForm()">
            <div class="account-avatar" style="background-color: transparent; border: 1px solid #dadce0; color: #0b57d0;">
                <i class="bi bi-person-plus-fill"></i>
            </div>
            <div class="account-info">Gunakan akun kustom baru</div>
        </div>
    </div>

    <!-- Custom Account Form -->
    <form method="POST" class="custom-form" id="customForm">
        <input type="hidden" name="role" id="customRole" value="<?= htmlspecialchars($state) ?>">
        <div class="form-floating mb-3">
            <input type="email" class="form-control" name="email" id="floatingEmail" placeholder="name@example.com" required>
            <label for="floatingEmail">Alamat Email Google</label>
        </div>
        <div class="form-floating mb-3">
            <input type="text" class="form-control" name="full_name" id="floatingName" placeholder="Nama Lengkap" required>
            <label for="floatingName">Nama Lengkap</label>
        </div>
        <div class="form-floating mb-3">
            <input type="url" class="form-control" name="avatar" id="floatingAvatar" placeholder="URL Foto Profil (Opsional)">
            <label for="floatingAvatar">URL Foto Profil Google (Opsional)</label>
        </div>

        <!-- Custom Role Switcher (matches UI for register/login context) -->
        <div class="mb-4">
            <label class="form-label text-muted small fw-bold">ROLE AKUN BARU:</label>
            <div class="btn-group w-100" role="group">
                <input type="radio" class="btn-check" name="role_radio" id="roleUser" autocomplete="off" checked onclick="document.getElementById('customRole').value='user'">
                <label class="btn btn-outline-primary" for="roleUser">🏠 PENYEWA</label>

                <input type="radio" class="btn-check" name="role_radio" id="roleOwner" autocomplete="off" onclick="document.getElementById('customRole').value='owner'">
                <label class="btn btn-outline-primary" for="roleOwner">💼 PEMILIK KOST</label>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <button type="button" class="btn btn-light" onclick="toggleCustomForm()">Batal</button>
            <button type="submit" class="btn btn-google-blue">Lanjutkan</button>
        </div>
    </form>

    <div class="footer-text">
        Untuk melanjutkan, Google akan membagikan nama, alamat email, preferensi bahasa, dan foto profil Anda dengan E-KOST SYSTEM.
    </div>

    <div class="footer-links">
        <a href="#">Privasi</a>
        <a href="#">Persyaratan</a>
    </div>
</div>

<script>
    function toggleCustomForm() {
        const list = document.querySelector('.accounts-container');
        const form = document.getElementById('customForm');
        if (form.style.display === 'block') {
            form.style.display = 'none';
            list.style.display = 'block';
        } else {
            form.style.display = 'block';
            list.style.display = 'none';
        }
    }
    // Set active radio based on state param
    window.addEventListener('DOMContentLoaded', () => {
        const stateRole = "<?= $state ?>";
        if (stateRole === 'owner') {
            document.getElementById('roleOwner').checked = true;
            document.getElementById('customRole').value = 'owner';
        } else {
            document.getElementById('roleUser').checked = true;
            document.getElementById('customRole').value = 'user';
        }
    });
</script>
</body>
</html>
