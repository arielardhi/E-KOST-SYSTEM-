<?php
require_once '../../config/database.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

$error   = '';
$success = '';
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
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, full_name, phone) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$username, $email, $password, $role, $full_name, $phone])) {
            $success = "Registrasi berhasil!";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700;800;900&family=Archivo+Black&display=swap" rel="stylesheet">
    <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: 'Space Grotesk', sans-serif;
        min-height: 100vh;
        background-image: url('/e-kost-system/assets/img/texturebg.jpg');
        background-size: 900px auto;
        background-color: #f4f4f0;
        display: flex;
    }

    /* ── LEFT PANEL ── */
    .left-panel {
        width: 44%;
        min-height: 100vh;
        background: #000;
        border-right: 5px solid #000;
        display: flex;
        flex-direction: column;
        padding: 0;
        position: relative;
        overflow: hidden;
    }

    .panel-block {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 48px 44px;
        position: relative;
    }

    /* Colour blocks stacked */
    .color-strip {
        position: absolute;
        left: 0; right: 0;
        border-bottom: 4px solid #000;
    }
    .strip-1 { top: 0; height: 8px; background: #FFD600; }
    .strip-2 { top: 8px; height: 8px; background: #FF3CAC; }
    .strip-3 { top: 16px; height: 8px; background: #00E0FF; }
    .strip-4 { top: 24px; height: 8px; background: #00FF94; }
    .strip-5 { top: 32px; height: 8px; background: #FF5C00; }

    .brand-logo {
        position: absolute;
        top: 56px; left: 44px;
        font-family: 'Archivo Black', sans-serif;
        font-size: .75rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        background: #FFD600;
        color: #000;
        border: 3px solid #000;
        box-shadow: 5px 5px 0 #FFD600;
        padding: 7px 16px;
    }

    .big-number {
        font-family: 'Archivo Black', sans-serif;
        font-size: clamp(5rem, 12vw, 9rem);
        line-height: .85;
        color: transparent;
        -webkit-text-stroke: 3px #FFD600;
        text-transform: uppercase;
        letter-spacing: -4px;
        margin-bottom: 16px;
        position: relative;
        z-index: 1;
    }

    .panel-title {
        font-family: 'Archivo Black', sans-serif;
        font-size: clamp(1.8rem, 3vw, 2.8rem);
        line-height: 1;
        text-transform: uppercase;
        letter-spacing: -1.5px;
        color: #fff;
        margin-bottom: 16px;
        position: relative;
        z-index: 1;
    }
    .panel-title em { color: #FFD600; font-style: normal; }

    .panel-desc {
        color: #888;
        font-size: .88rem;
        font-weight: 600;
        line-height: 1.6;
        max-width: 300px;
        position: relative;
        z-index: 1;
        margin-bottom: 32px;
    }

    .role-cards { display: flex; gap: 12px; position: relative; z-index: 1; flex-wrap: wrap; }
    .role-card {
        flex: 1;
        min-width: 120px;
        border: 3px solid;
        padding: 16px 14px;
        cursor: default;
    }
    .role-card.tenant { border-color: #FFD600; background: rgba(255,214,0,.08); }
    .role-card.owner  { border-color: #FF5C00; background: rgba(255,92,0,.08); }
    .role-card .rc-icon { font-size: 1.6rem; margin-bottom: 8px; }
    .role-card .rc-title { font-family: 'Archivo Black', sans-serif; font-size: .78rem; text-transform: uppercase; letter-spacing: 1px; }
    .role-card.tenant .rc-title { color: #FFD600; }
    .role-card.owner  .rc-title { color: #FF5C00; }
    .role-card .rc-desc { color: #666; font-size: .72rem; margin-top: 4px; }

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
        border: 4px solid #000;
        box-shadow: 10px 10px 0 #000;
        padding: 36px 32px;
    }

    .reg-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 28px;
        padding-bottom: 20px;
        border-bottom: 3px solid #000;
    }
    .reg-title {
        font-family: 'Archivo Black', sans-serif;
        font-size: 2.2rem;
        text-transform: uppercase;
        letter-spacing: -2px;
        line-height: .95;
    }
    .step-tag {
        background: #001ee1;
        color: #FFD600;
        border: 3px solid #000;
        font-family: 'Archivo Black', sans-serif;
        font-size: .7rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 5px 12px;
        box-shadow: 3px 3px 0 #000;
        white-space: nowrap;
    }

    /* Role Switcher */
    .role-switcher {
        display: flex;
        gap: 0;
        border: 3px solid #000;
        margin-bottom: 24px;
        overflow: hidden;
    }
    .role-btn {
        flex: 1;
        padding: 12px;
        border: none;
        background: #f4f4f0;
        font-family: 'Archivo Black', sans-serif;
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .5px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: background .12s, color .12s;
        border-right: 3px solid #000;
    }
    .role-btn:last-child { border-right: none; }
    .role-btn.active-user { background: #FFD600; color: #000; }
    .role-btn.active-owner { background: #FF5C00; color: #fff; }
    .role-btn:not(.active-user):not(.active-owner):hover { background: #eee; }

    /* Form fields */
    .form-label {
        font-family: 'Archivo Black', sans-serif;
        font-size: .7rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #000;
        margin-bottom: 5px;
        display: block;
    }

    .nb-input {
        width: 100%;
        border: 3px solid #000;
        border-radius: 0;
        padding: 11px 14px;
        font-family: 'Space Grotesk', sans-serif;
        font-weight: 600;
        font-size: .92rem;
        background: #fff;
        outline: none;
        transition: box-shadow .1s, background .1s;
    }
    .nb-input:focus { background: #FFFDE7; box-shadow: 4px 4px 0 #000; }

    .nb-select {
        width: 100%;
        border: 3px solid #000;
        border-radius: 0;
        padding: 11px 14px;
        font-family: 'Archivo Black', sans-serif;
        font-size: .8rem;
        background: #fff;
        text-transform: uppercase;
        outline: none;
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23000' stroke-width='2' fill='none'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
    }
    .nb-select:focus { background-color: #FFFDE7; box-shadow: 4px 4px 0 #000; }

    .input-icon-wrap { position: relative; }
    .input-icon-wrap .nb-input { padding-left: 44px; }
    .input-icon {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
        font-size: .95rem; color: #999; pointer-events: none;
    }

    .field-accent {
        height: 4px;
        margin-top: -3px;
        margin-bottom: 16px;
        border: none;
    }
    .accent-y { background: #FFD600; }
    .accent-p { background: #FF3CAC; }
    .accent-b { background: #00E0FF; }
    .accent-g { background: #00FF94; }
    .accent-o { background: #FF5C00; }
    .accent-v { background: #7B2FFF; }

    .row-fields { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

    .btn-register {
        width: 100%;
        background: #001ee1;
        color: #FFD600;
        border: 3px solid #000;
        border-radius: 0;
        font-family: 'Archivo Black', sans-serif;
        font-size: 1rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 14px;
        box-shadow: 7px 7px 0 #000;
        cursor: pointer;
        transition: transform .08s, box-shadow .08s;
        margin-top: 8px;
    }
    .btn-register:hover  { transform: translate(-3px,-3px); box-shadow: 10px 10px 0 #000; background: #0016b0; }
    .btn-register:active { transform: translate(3px,3px); box-shadow: 2px 2px 0 #000; }
    .btn-register.owner-mode { background: #FF5C00; color: #fff; }
    .btn-register.owner-mode:hover { background: #e04e00; }

    .error-box {
        background: #FF4B4B;
        color: #fff;
        border: 3px solid #000;
        box-shadow: 4px 4px 0 #000;
        padding: 12px 16px;
        font-weight: 700;
        font-size: .85rem;
        margin-bottom: 20px;
        display: flex; align-items: center; gap: 10px;
    }
    .success-box {
        background: #00FF94;
        color: #000;
        border: 3px solid #000;
        box-shadow: 4px 4px 0 #000;
        padding: 12px 16px;
        font-weight: 700;
        font-size: .85rem;
        margin-bottom: 20px;
        display: flex; align-items: center; gap: 10px;
    }

    .link-row { text-align: center; margin-top: 16px; font-size: .87rem; font-weight: 700; }
    .link-row a { color: #001ee1; font-weight: 800; text-decoration: none; border-bottom: 2px solid #001ee1; }
    .link-row a:hover { background: #001ee1; color: #FFD600; padding: 0 4px; }

    .back-link { text-align: center; margin-top: 8px; }
    .back-link a {
        color: #666; font-size: .82rem; font-weight: 700;
        text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
    }
    .back-link a:hover { color: #001ee1; }

    /* Coloured section dividers inside form */
    .section-divider {
        display: flex; align-items: center; gap: 10px;
        margin: 20px 0 16px;
    }
    .section-divider span {
        font-family: 'Archivo Black', sans-serif;
        font-size: .68rem;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        white-space: nowrap;
        padding: 4px 10px;
        border: 2px solid #000;
    }
    .section-divider::before, .section-divider::after {
        content: ''; flex: 1; height: 2px; background: #000;
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
    <div class="color-strip strip-1"></div>
    <div class="color-strip strip-2"></div>
    <div class="color-strip strip-3"></div>
    <div class="color-strip strip-4"></div>
    <div class="color-strip strip-5"></div>
    <div class="brand-logo">✦ E-KOST SYSTEM</div>

    <div class="panel-block" style="padding-top: 120px; justify-content: flex-end;">
        <div class="big-number">01</div>
        <h2 class="panel-title">Buat<br>Akun<br><em>Gratis!</em></h2>
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
                <div>
                    <div class="reg-title">Daftar<br>Akun</div>
                </div>
                <div class="step-tag">Gratis ✦ Cepat</div>
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
                    Registrasi berhasil! <a href="login.php" style="color:#001ee1;font-weight:800;margin-left:6px;">Login →</a>
                </div>
            <?php endif; ?>

            <form method="POST" id="regForm">
                <input type="hidden" name="role" id="roleInput" value="<?= htmlspecialchars($prefill_role) ?>">

                <!-- Nama Lengkap -->
                <div class="mb-1">
                    <label class="form-label">Nama Lengkap</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-person-fill input-icon"></i>
                        <input type="text" name="full_name" class="nb-input" placeholder="Nama lengkap kamu..." required>
                    </div>
                </div>
                <div class="field-accent accent-y"></div>

                <!-- Username & Email -->
                <div class="row-fields mb-1">
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
                <div class="field-accent accent-p"></div>

                <!-- Password & No HP -->
                <div class="row-fields mb-1">
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
                <div class="field-accent accent-b"></div>

                <!-- Show password -->
                <div class="d-flex align-items-center gap-2 mb-4" style="font-size:.82rem;font-weight:700;cursor:pointer;">
                    <input type="checkbox" id="showPass" onchange="document.getElementById('passInput').type=this.checked?'text':'password'">
                    <label for="showPass" style="cursor:pointer;margin:0;">Tampilkan Password</label>
                </div>

                <button type="submit" class="btn-register" id="registerBtn">
                    <i class="bi bi-person-plus-fill me-2"></i> Buat Akun Sekarang
                </button>
            </form>
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
</script>
</body>
</html>
