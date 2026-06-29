<?php
require_once '../../config/database.php';
require_once '../../config/google.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

$error   = '';
$success = '';
if (isset($_SESSION['google_auth_error'])) {
    $error = $_SESSION['google_auth_error'];
    unset($_SESSION['google_auth_error']);
}

$prefill_role = $_GET['role'] ?? 'user';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username  = $_POST['username'];
    $email     = $_POST['email'];
    $password  = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role      = $_POST['role'];
    $full_name = $_POST['full_name'];
    $phone     = $_POST['phone'];

    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->fetch()) {
        $error = "Username atau Email sudah terdaftar!";
    } else {
        $status = 'verified';
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, full_name, phone, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$username, $email, $password, $role, $full_name, $phone, $status])) {
            $success = "Registrasi berhasil! Silakan masuk ke akun Anda.";
        } else {
            $error = "Terjadi kesalahan saat registrasi.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar — E-KOST SYSTEM</title>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        min-height: 100vh;
        background-color: #F8F4E3; /* Cream background */
        display: flex;
    }

    /* ── LEFT PANEL ── */
    .left-panel {
        width: 44%;
        min-height: 100vh;
        background: linear-gradient(135deg, #2D1459 0%, #1A0A3A 100%); /* Deep Purple Gradient */
        display: flex;
        flex-direction: column;
        padding: 0;
        position: relative;
        overflow: hidden;
        border-right: 1px solid rgba(255, 255, 255, 0.1);
    }
    .left-panel::before {
        content: '';
        position: absolute; inset: 0;
        background-image: radial-gradient(circle at 70% 30%, rgba(91, 201, 204, 0.15) 0%, transparent 60%); /* Turquoise aura */
        pointer-events: none;
    }

    .panel-block {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 48px 44px;
        position: relative;
    }

    .brand-logo {
        position: absolute;
        top: 56px; left: 44px;
        font-weight: 800;
        font-size: 1.15rem;
        letter-spacing: -0.03em;
        color: #ffffff;
    }
    .brand-logo span { color: #5BC9CC; }

    .big-number {
        font-size: clamp(4rem, 10vw, 7rem);
        line-height: .85;
        font-weight: 800;
        color: transparent;
        -webkit-text-stroke: 2px rgba(255, 255, 255, 0.15);
        letter-spacing: -4px;
        margin-bottom: 16px;
        position: relative;
        z-index: 1;
    }

    .panel-title {
        font-size: clamp(1.8rem, 3vw, 2.8rem);
        line-height: 1.1;
        font-weight: 800;
        color: #fff;
        margin-bottom: 16px;
        position: relative;
        z-index: 1;
        letter-spacing: -0.03em;
    }
    .panel-title em { color: #E85C50; font-style: normal; }

    .panel-desc {
        color: #EBE6D0;
        font-size: .9rem;
        font-weight: 500;
        line-height: 1.6;
        max-width: 320px;
        position: relative;
        z-index: 1;
        margin-bottom: 32px;
        opacity: 0.85;
    }

    .role-cards { display: flex; gap: 12px; position: relative; z-index: 1; flex-wrap: wrap; }
    .role-card {
        flex: 1;
        min-width: 130px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 16px 14px;
        background: rgba(255, 255, 255, 0.03);
    }
    .role-card.tenant { border-color: rgba(91, 201, 204, 0.35); background: rgba(91, 201, 204, 0.06); }
    .role-card.owner  { border-color: rgba(232, 92, 80, 0.35); background: rgba(232, 92, 80, 0.06); }
    .role-card .rc-icon { font-size: 1.6rem; margin-bottom: 8px; }
    .role-card .rc-title { font-weight: 700; font-size: .8rem; text-transform: uppercase; letter-spacing: 0.05em; }
    .role-card.tenant .rc-title { color: #5BC9CC; }
    .role-card.owner  .rc-title { color: #E85C50; }
    .role-card .rc-desc { color: #A498B6; font-size: .75rem; margin-top: 4px; }

    /* ── RIGHT PANEL ── */
    .right-panel {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 28px;
        overflow-y: auto;
    }

    .reg-box { width: 100%; max-width: 500px; }

    .reg-card {
        background: #fff;
        border: 1px solid #EBE6D0;
        box-shadow: 0 10px 25px -5px rgba(45, 20, 89, 0.05), 0 8px 10px -6px rgba(45, 20, 89, 0.05);
        padding: 36px 32px;
        border-radius: 16px;
    }

    .reg-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid #EBE6D0;
    }
    .reg-title {
        font-size: 1.8rem;
        font-weight: 800;
        letter-spacing: -0.03em;
        line-height: 1.2;
        color: #2D1459;
    }
    .step-tag {
        background: rgba(91, 201, 204, 0.1);
        color: #5BC9CC;
        font-weight: 600;
        font-size: .75rem;
        padding: 6px 14px;
        border-radius: 9999px;
        white-space: nowrap;
    }

    /* Role Switcher */
    .role-switcher {
        display: flex;
        border: 1px solid #EBE6D0;
        border-radius: 8px;
        margin-bottom: 24px;
        overflow: hidden;
        background: #EBE6D0;
        padding: 4px;
    }
    .role-btn {
        flex: 1;
        padding: 10px;
        border: none;
        background: transparent;
        font-weight: 700;
        font-size: .82rem;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s ease;
        color: #7A6A8E;
    }
    .role-btn.active-user { background: #ffffff; color: #5BC9CC; box-shadow: 0 1px 3px rgba(45,20,89,0.1); }
    .role-btn.active-owner { background: #ffffff; color: #E85C50; box-shadow: 0 1px 3px rgba(45,20,89,0.1); }
    .role-btn:not(.active-user):not(.active-owner):hover { color: #2D1459; }

    /* Form fields */
    .form-label {
        font-weight: 600;
        font-size: .75rem;
        color: #2D1459;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        display: block;
    }

    .nb-input {
        width: 100%;
        border: 1px solid #EBE6D0;
        border-radius: 8px;
        padding: 11px 14px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 500;
        font-size: .92rem;
        background: #fff;
        outline: none;
        transition: all 0.15s ease;
    }
    .nb-input:focus { border-color: #5BC9CC; box-shadow: 0 0 0 4px rgba(91, 201, 204, 0.15); }

    .input-icon-wrap { position: relative; }
    .input-icon-wrap .nb-input { padding-left: 44px; }
    .input-icon {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
        font-size: 1.05rem; color: #7A6A8E; pointer-events: none;
    }

    .row-fields { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

    .btn-register {
        width: 100%;
        background: #5BC9CC;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1rem;
        padding: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-top: 8px;
    }
    .btn-register:hover { background: #3AAFB2; transform: translateY(-1px); }
    .btn-register:active { transform: translateY(1px); }
    .btn-register.owner-mode { background: #E85C50; }
    .btn-register.owner-mode:hover { background: #D04F44; }

    .error-box {
        background: #fef2f2;
        color: #ef4444;
        border: 1px solid #fca5a5;
        border-radius: 8px;
        padding: 12px 16px;
        font-weight: 600;
        font-size: .85rem;
        margin-bottom: 20px;
        display: flex; align-items: center; gap: 10px;
    }
    .success-box {
        background: #ecfdf5;
        color: #10b981;
        border: 1px solid #a7f3d0;
        border-radius: 8px;
        padding: 12px 16px;
        font-weight: 600;
        font-size: .85rem;
        margin-bottom: 20px;
        display: flex; align-items: center; gap: 10px;
    }

    .link-row { text-align: center; margin-top: 20px; font-size: .87rem; color: #7A6A8E; }
    .link-row a { color: #5BC9CC; font-weight: 600; text-decoration: none; }
    .link-row a:hover { text-decoration: underline; }

    .back-link { text-align: center; margin-top: 12px; }
    .back-link a {
        color: #7A6A8E; font-size: .85rem; font-weight: 600;
        text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
        transition: color 0.15s ease;
    }
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
        margin-top: 10px;
    }
    .btn-google:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        transform: translateY(-1px);
    }
    .btn-google:active {
        transform: translateY(1px);
    }

    .divider-text {
        display: flex; align-items: center; gap: 12px;
        color: #7A6A8E; font-size: .8rem; font-weight: 600;
        text-transform: uppercase; letter-spacing: 1px;
        margin: 20px 0;
    }
    .divider-text::before, .divider-text::after {
        content: ''; flex: 1; height: 1px; background: #EBE6D0;
    }

    @media (max-width: 768px) {
        .left-panel { display: none; }
        .right-panel { padding: 28px 16px; }
        .row-fields { grid-template-columns: 1fr; }
        .reg-card { padding: 24px 20px; }
    }
    </style>
</head>
<body>

<!-- LEFT PANEL -->
<div class="left-panel">
    <div class="brand-logo d-flex align-items-center"><img src="../../assets/images/logo.png" alt="Logo" class="me-2 rounded" style="height: 28px; width: 28px; background-color: transparent; object-fit: contain;"> E-KOST <span>SYSTEM</span></div>

    <div class="panel-block" style="padding-top: 120px; justify-content: flex-end;">
        <div class="big-number">01</div>
        <h2 class="panel-title">Buat Akun<br><em>Gratis!</em></h2>
        <p class="panel-desc">Bergabung dengan ribuan pengguna aktif. Cari kost, booking online, dan chat langsung — semua di satu tempat.</p>

        <div class="role-cards">
            <div class="role-card tenant">
                <div class="rc-icon">🏠</div>
                <div class="rc-title">Pencari Kost</div>
                <div class="rc-desc">Cari, filter & booking kost impian</div>
            </div>
            <div class="role-card owner">
                <div class="rc-icon">📊</div>
                <div class="rc-title">Pemilik Kost</div>
                <div class="rc-desc">Kelola properti & pantau pesanan</div>
            </div>
        </div>
    </div>
</div>

<!-- RIGHT PANEL -->
<div class="right-panel">
    <div class="reg-box">
        <div class="reg-card">
            <div class="reg-header">
                <div class="reg-title">Daftar</div>
                <div class="step-tag">Cepat & Gratis</div>
            </div>

            <!-- Role switcher -->
            <div class="role-switcher" id="roleSwitcher">
                <button type="button" class="role-btn <?php echo $prefill_role == 'user' ? 'active-user' : ''; ?>"
                        onclick="setRole('user')">
                    👤 Penyewa
                </button>
                <button type="button" class="role-btn <?php echo $prefill_role == 'owner' ? 'active-owner' : ''; ?>"
                        onclick="setRole('owner')">
                    🏘️ Pemilik Kost
                </button>
            </div>

            <?php if ($error): ?>
                <div class="error-box"><i class="bi bi-exclamation-triangle-fill"></i> <?= $error ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="success-box">
                    <i class="bi bi-check-circle-fill"></i>
                    <?= $success ?> <a href="login.php" style="color:#5BC9CC;font-weight:600;margin-left:6px;">Login →</a>
                </div>
            <?php endif; ?>

            <form method="POST" id="regForm">
                <input type="hidden" name="role" id="roleInput" value="<?= htmlspecialchars($prefill_role) ?>">

                <!-- Nama Lengkap -->
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-person-fill input-icon"></i>
                        <input type="text" name="full_name" class="nb-input" placeholder="Nama lengkap kamu..." required>
                    </div>
                </div>

                <!-- Username & Email -->
                <div class="row-fields mb-3">
                    <div>
                        <label class="form-label">Username</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-at input-icon"></i>
                            <input type="text" name="username" class="nb-input" placeholder="username..." required>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Email</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-envelope-fill input-icon"></i>
                            <input type="email" name="email" class="nb-input" placeholder="email@..." required>
                        </div>
                    </div>
                </div>

                <!-- Password & No HP -->
                <div class="row-fields mb-2">
                    <div>
                        <label class="form-label">Password</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-lock-fill input-icon"></i>
                            <input type="password" name="password" id="passInput" class="nb-input" placeholder="min. 8 karakter" required>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">No. HP / WA</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-whatsapp input-icon"></i>
                            <input type="text" name="phone" class="nb-input" placeholder="08xxxxxxxxxx" required>
                        </div>
                    </div>
                </div>

                <!-- Show password -->
                <div class="d-flex align-items-center gap-2 mb-4" style="font-size:.82rem;font-weight:600;color:#64748b;cursor:pointer;">
                    <input type="checkbox" id="showPass" onchange="document.getElementById('passInput').type=this.checked?'text':'password'">
                    <label for="showPass" style="cursor:pointer;margin:0;">Tampilkan Password</label>
                </div>

                <button type="submit" class="btn-register" id="registerBtn">
                    <i class="bi bi-person-plus-fill me-2"></i> Buat Akun Sekarang
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
                Daftar dengan Google
            </button>
        </div>

        <div class="link-row">
            Sudah punya akun? <a href="login.php">Masuk di sini</a>
        </div>
        <div class="back-link">
            <a href="../../index.php"><i class="bi bi-arrow-left"></i> Kembali ke Beranda</a>
        </div>
    </div>
</div>

<script>
function setRole(role) {
    document.getElementById('roleInput').value = role;
    const btns = document.querySelectorAll('.role-btn');
    btns.forEach(b => b.classList.remove('active-user','active-owner'));
    const btn = role === 'user' ? btns[0] : btns[1];
    btn.classList.add(role === 'user' ? 'active-user' : 'active-owner');
    const regBtn = document.getElementById('registerBtn');
    regBtn.classList.toggle('owner-mode', role === 'owner');
}
// init
setRole('<?= $prefill_role ?>');

function startGoogleAuth() {
    const clientId = "<?php echo GOOGLE_CLIENT_ID; ?>";
    const redirectUri = "<?php echo GOOGLE_REDIRECT_URI; ?>";
    const role = document.getElementById('roleInput').value;
    if (clientId === 'YOUR_GOOGLE_CLIENT_ID') {
        window.location.href = `mock_google.php?redirect_uri=${encodeURIComponent(redirectUri)}&state=${encodeURIComponent(role)}`;
    } else {
        const authUrl = `https://accounts.google.com/o/oauth2/v2/auth?client_id=${encodeURIComponent(clientId)}&redirect_uri=${encodeURIComponent(redirectUri)}&response_type=code&scope=openid%20profile%20email&state=${encodeURIComponent(role)}`;
        window.location.href = authUrl;
    }
}
</script>
</body>
</html>
