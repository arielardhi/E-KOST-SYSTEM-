<?php
require_once '../../config/database.php';
require_once '../../config/google.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

$error = '';
if (isset($_SESSION['google_auth_error'])) {
    $error = $_SESSION['google_auth_error'];
    unset($_SESSION['google_auth_error']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        if ($user['status'] === 'pending') {
            $error = "Akun Anda sedang dalam proses verifikasi oleh Admin.";
        } elseif ($user['status'] === 'rejected') {
            $error = "Akun Anda ditangguhkan (Suspended) oleh Admin.";
        } else {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            try {
                $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
                $log_stmt = $pdo->prepare("INSERT INTO system_logs (user_id, username, role, activity, ip_address) VALUES (?, ?, ?, ?, ?)");
                $log_stmt->execute([$user['id'], $user['username'], $user['role'], 'Login ke sistem', $ip]);
            } catch (Exception $e) {
                // Fail silently
            }

            if ($user['role'] == 'admin')       header("Location: ../admin/dashboard.php");
            elseif ($user['role'] == 'owner')   header("Location: ../owner/dashboard.php");
            else                                header("Location: ../user/dashboard.php");
            exit();
        }
    } else {
        $error = "Username atau password salah!";
    }

    if (!empty($error)) {
        try {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $log_stmt = $pdo->prepare("INSERT INTO system_logs (user_id, username, role, activity, ip_address) VALUES (?, ?, ?, ?, ?)");
            $log_stmt->execute([0, $username, 'guest', "Gagal login - username: $username", $ip]);
        } catch (Exception $e) {
            // Fail silently
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — E-KOST SYSTEM</title>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        min-height: 100vh;
        display: flex;
        background-color: #F8F4E3; /* Cream background */
    }

    /* LEFT PANEL */
    .left-panel {
        width: 42%;
        background: linear-gradient(135deg, #2D1459 0%, #1A0A3A 100%); /* Deep Purple Gradient */
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 48px 44px;
        position: relative;
        overflow: hidden;
        border-right: 1px solid rgba(255, 255, 255, 0.1);
    }
    .left-panel::before {
        content: '';
        position: absolute; inset: 0;
        background-image: radial-gradient(circle at 70% 30%, rgba(91, 201, 204, 0.15) 0%, transparent 60%); /* Turquoise light */
        pointer-events: none;
    }
    .left-panel > * { position: relative; }

    .brand-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(91, 201, 204, 0.15); /* Turquoise transparent */
        color: #5BC9CC; /* Turquoise */
        font-size: .8rem;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 9999px;
        border: 1px solid rgba(91, 201, 204, 0.25);
        margin-bottom: 28px;
    }
    .left-title {
        font-size: clamp(2.4rem, 4vw, 3.5rem);
        color: #fff;
        font-weight: 800;
        line-height: 1.1;
        letter-spacing: -0.03em;
    }
    .left-title span { 
        background: linear-gradient(to right, #5BC9CC, #E85C50); /* Turquoise to Coral */
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: block; 
    }

    .left-desc { color: #EBE6D0; font-size: .95rem; font-weight: 500; margin-top: 20px; max-width: 340px; line-height: 1.6; opacity: 0.85; }

    .feature-tags { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 32px; }
    .ftag {
        display: inline-flex; align-items: center; gap: 6px;
        border: 1px solid rgba(255,255,255,.1);
        background: rgba(255,255,255,.05);
        color: #fff; font-weight: 600; font-size: .78rem;
        padding: 6px 12px;
        border-radius: 6px;
    }
    .ftag-dot { width: 8px; height: 8px; border-radius: 50%; }

    .left-bottom-card {
        background: rgba(255,255,255,.03);
        border: 1px solid rgba(255,255,255,.08);
        padding: 20px;
        border-radius: 12px;
    }
    .left-bottom-card p { color: #EBE6D0; font-size: .85rem; margin: 0; font-style: italic; line-height: 1.6; opacity: 0.8; }
    .left-bottom-card strong { color: #5BC9CC; font-size: .8rem; display: block; margin-top: 8px; font-weight: 600; }

    /* RIGHT PANEL */
    .right-panel {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 48px 32px;
    }
    .login-box { width: 100%; max-width: 420px; }

    .login-card {
        background: #fff;
        border: 1px solid #EBE6D0;
        box-shadow: 0 10px 25px -5px rgba(45, 20, 89, 0.05), 0 8px 10px -6px rgba(45, 20, 89, 0.05);
        padding: 40px 36px;
        border-radius: 16px;
    }
    .login-title {
        font-size: 2rem;
        font-weight: 800;
        letter-spacing: -0.03em;
        line-height: 1.2;
        margin-bottom: 6px;
        color: #2D1459;
    }
    .login-sub { color: #7A6A8E; font-size: .88rem; font-weight: 500; margin-bottom: 28px; }

    .form-label {
        font-weight: 600;
        font-size: .75rem;
        color: #2D1459;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    .nb-input {
        width: 100%;
        border: 1px solid #EBE6D0;
        border-radius: 8px;
        padding: 12px 14px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 500;
        font-size: .95rem;
        background: #fff;
        transition: all .15s ease;
        outline: none;
    }
    .nb-input:focus { border-color: #5BC9CC; box-shadow: 0 0 0 4px rgba(91, 201, 204, 0.15); }

    .input-icon-wrap { position: relative; }
    .input-icon-wrap .nb-input { padding-left: 44px; }
    .input-icon {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
        font-size: 1.1rem; color: #7A6A8E; pointer-events: none;
    }

    .btn-login {
        width: 100%;
        background: #5BC9CC;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: .95rem;
        padding: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-top: 8px;
    }
    .btn-login:hover { background: #3AAFB2; transform: translateY(-1px); }
    .btn-login:active { transform: translateY(1px); }

    .divider-text {
        display: flex; align-items: center; gap: 12px;
        color: #7A6A8E; font-size: .8rem; font-weight: 600;
        text-transform: uppercase; letter-spacing: 1px;
        margin: 20px 0;
    }
    .divider-text::before, .divider-text::after {
        content: ''; flex: 1; height: 1px; background: #EBE6D0;
    }

    .btn-wa {
        width: 100%;
        background: #22c55e;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: .85rem;
        padding: 12px;
        cursor: pointer;
        text-decoration: none;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        transition: all 0.2s ease;
    }
    .btn-wa:hover { background: #16a34a; transform: translateY(-1px); color: #fff; }

    .btn-google {
        width: 100%;
        background: #ffffff;
        color: #1e293b;
        border: 1px solid #EBE6D0;
        border-radius: 8px;
        font-weight: 700;
        font-size: .88rem;
        padding: 12px;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center; gap: 10px;
        transition: all 0.2s ease;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        font-family: inherit;
    }
    .btn-google:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        transform: translateY(-1px);
    }
    .btn-google:active {
        transform: translateY(1px);
    }

    .error-box {
        background: #fef2f2;
        color: #ef4444;
        border: 1px solid #fca5a5;
        border-radius: 8px;
        padding: 12px 16px;
        font-weight: 600;
        font-size: .88rem;
        margin-bottom: 20px;
        display: flex; align-items: center; gap: 10px;
    }

    .link-row { text-align: center; margin-top: 20px; font-size: .88rem; color: #7A6A8E; }
    .link-row a { color: #5BC9CC; font-weight: 600; text-decoration: none; }
    .link-row a:hover { text-decoration: underline; }

    .back-link {
        text-align: center; margin-top: 16px;
    }
    .back-link a {
        color: #7A6A8E; font-size: .85rem; font-weight: 600;
        text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
        transition: color 0.15s ease;
    }
    .back-link a:hover { color: #5BC9CC; }

    .saved-email-chip {
        background: #ffffff;
        border: 2px solid #2D1459;
        border-radius: 8px;
        padding: 6px 12px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.15s ease;
        box-shadow: 2px 2px 0 #2D1459;
    }
    .saved-email-chip:hover {
        background: #F8F4E3;
        transform: translate(-1px, -1px);
        box-shadow: 3px 3px 0 #2D1459;
    }
    .saved-email-chip:active {
        transform: translate(1px, 1px);
        box-shadow: 1px 1px 0 #2D1459;
    }
    .saved-email-chip .chip-text {
        font-size: 0.85rem;
        font-weight: 700;
        color: #2D1459;
    }
    .saved-email-chip .btn-remove-chip {
        background: none;
        border: none;
        color: #ef4444;
        padding: 0;
        margin-left: 4px;
        cursor: pointer;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
    }
    .saved-email-chip .btn-remove-chip:hover {
        color: #dc2626;
    }

    /* Mobile */
    @media (max-width: 768px) {
        .left-panel { display: none; }
        .right-panel { padding: 32px 16px; }
    }
    </style>
</head>
<body>

<!-- LEFT PANEL -->
<div class="left-panel">
    <div>
        <div class="brand-tag d-flex align-items-center"><img src="../../assets/images/logo.png" alt="Logo" class="me-2 rounded" style="height: 20px; width: 20px; background-color: transparent; object-fit: contain;"> E-KOST SYSTEM</div>
        <h1 class="left-title">Selamat<br>Datang<br><span>Kembali!</span></h1>
        <p class="left-desc">Masuk ke akun kamu dan mulai cari kost terbaik atau kelola propertimu dengan mudah.</p>
        <div class="feature-tags">
            <div class="ftag"><div class="ftag-dot" style="background:#5BC9CC;"></div>Cari Kost</div>
            <div class="ftag"><div class="ftag-dot" style="background:#E85C50;"></div>Booking Online</div>
            <div class="ftag"><div class="ftag-dot" style="background:#10b981;"></div>Chat Pemilik</div>
            <div class="ftag"><div class="ftag-dot" style="background:#2D1459;"></div>Dashboard</div>
        </div>
    </div>
    <div class="left-bottom-card">
        <p>"E-KOST System bikin saya nemuin kost yang cocok dalam 10 menit. Prosesnya gampang banget!"</p>
        <strong>— Andi R., Pengguna Aktif</strong>
    </div>
</div>

<!-- RIGHT PANEL -->
<div class="right-panel">
    <div class="login-box">
        <div class="login-card">
            <h2 class="login-title">Masuk</h2>
            <p class="login-sub">Belum punya akun? <a href="register.php" style="color:#5BC9CC;font-weight:600;text-decoration:none;">Daftar gratis</a></p>

            <?php if ($error): ?>
                <div class="error-box">
                    <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <!-- Suggestion Chips for Saved Accounts -->
                <div id="emailSuggestions" class="mb-3 d-none">
                    <label class="form-label text-muted" style="font-size: 0.72rem; text-transform: uppercase;">Akun Terdaftar di Browser Ini:</label>
                    <div class="d-flex flex-wrap gap-2" id="suggestionBadges"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Username atau Email</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-person-fill input-icon"></i>
                        <input type="text" name="username" id="usernameInput" class="nb-input" placeholder="Masukkan username atau email..." required autocomplete="username">
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label">Password</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-lock-fill input-icon"></i>
                        <input type="password" name="password" class="nb-input" placeholder="Masukkan password..." required autocomplete="current-password" id="passInput">
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <label style="font-size:.82rem;font-weight:600;color:#64748b;cursor:pointer;display:flex;align-items:center;gap:6px;">
                        <input type="checkbox" onchange="document.getElementById('passInput').type=this.checked?'text':'password'">
                        Tampilkan Password
                    </label>
                    <a href="forgot_password.php" style="font-size:.82rem;font-weight:700;color:#5BC9CC;text-decoration:none;">Lupa Password?</a>
                </div>
                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Masuk Sekarang
                </button>
            </form>

            <div class="divider-text">atau</div>

            <button type="button" class="btn-google" onclick="startGoogleAuth()">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" style="width:18px;height:18px;flex-shrink:0;">
                    <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                    <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                    <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                    <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                    <path fill="none" d="M0 0h48v48H0z"/>
                </svg>
                Masuk dengan Google
            </button>

        </div>

        <div class="back-link">
            <a href="../../index.php"><i class="bi bi-arrow-left"></i> Kembali ke Beranda</a>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    renderEmailChips();
});

function renderEmailChips() {
    try {
        const emails = JSON.parse(localStorage.getItem('registered_emails') || '[]');
        const container = document.getElementById('emailSuggestions');
        const badgeList = document.getElementById('suggestionBadges');
        
        if (emails.length === 0) {
            container.classList.add('d-none');
            return;
        }
        
        container.classList.remove('d-none');
        badgeList.innerHTML = '';
        
        emails.forEach(email => {
            const chip = document.createElement('div');
            chip.className = 'saved-email-chip';
            chip.onclick = function() {
                selectEmail(email);
            };
            
            const envelopeIcon = document.createElement('i');
            envelopeIcon.className = 'bi bi-envelope-fill text-primary';
            
            const textSpan = document.createElement('span');
            textSpan.className = 'chip-text';
            textSpan.textContent = email;
            
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn-remove-chip';
            removeBtn.innerHTML = '<i class="bi bi-x-circle-fill"></i>';
            removeBtn.onclick = function(e) {
                e.stopPropagation();
                removeEmail(email);
            };
            
            chip.appendChild(envelopeIcon);
            chip.appendChild(textSpan);
            chip.appendChild(removeBtn);
            badgeList.appendChild(chip);
        });
    } catch (e) {
        console.error('Gagal menampilkan email terdaftar:', e);
    }
}

function selectEmail(email) {
    const input = document.getElementById('usernameInput');
    input.value = email;
    input.focus();
    
    // Auto focus password input
    const passwordInput = document.getElementById('passInput');
    if (passwordInput) {
        passwordInput.focus();
    }
}

function removeEmail(email) {
    if (confirm('Hapus saran email "' + email + '" dari browser ini?')) {
        try {
            let emails = JSON.parse(localStorage.getItem('registered_emails') || '[]');
            emails = emails.filter(e => e !== email);
            localStorage.setItem('registered_emails', JSON.stringify(emails));
            renderEmailChips();
        } catch (e) {
            console.error('Gagal menghapus email terdaftar:', e);
        }
    }
}

function startGoogleAuth() {
    const clientId = "<?php echo GOOGLE_CLIENT_ID; ?>";
    const redirectUri = "<?php echo GOOGLE_REDIRECT_URI; ?>";
    if (clientId === 'YOUR_GOOGLE_CLIENT_ID' || clientId === '') {
        window.location.href = `mock_google.php?redirect_uri=${encodeURIComponent(redirectUri)}&state=user`;
    } else {
        const authUrl = `https://accounts.google.com/o/oauth2/v2/auth?client_id=${encodeURIComponent(clientId)}&redirect_uri=${encodeURIComponent(redirectUri)}&response_type=code&scope=openid%20profile%20email&state=user`;
        window.location.href = authUrl;
    }
}
</script>
</body>
</html>
