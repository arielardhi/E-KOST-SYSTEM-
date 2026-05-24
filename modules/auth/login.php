<?php
require_once '../../config/database.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];

        if ($user['role'] == 'admin')       header("Location: ../admin/dashboard.php");
        elseif ($user['role'] == 'owner')   header("Location: ../owner/dashboard.php");
        else                                header("Location: ../user/dashboard.php");
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — E-KOST SYSTEM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700;800;900&family=Archivo+Black&display=swap" rel="stylesheet">
    <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: 'Space Grotesk', sans-serif;
        min-height: 100vh;
        display: flex;
        background-image: url('/e-kost-system/assets/img/texturebg.jpg');
        background-repeat: repeat;
        background-size: 900px auto;
        background-color: #f4f4f0;
    }

    /* Color strips top */
    .color-strip { position: absolute; left: 0; right: 0; }
    .strip-1 { top: 0;  height: 8px; background: #FFD600; border-bottom: 2px solid #000; }
    .strip-2 { top: 8px; height: 8px; background: #FF3CAC; border-bottom: 2px solid #000; }
    .strip-3 { top: 16px; height: 8px; background: #00E0FF; border-bottom: 2px solid #000; }
    .strip-4 { top: 24px; height: 8px; background: #00FF94; border-bottom: 2px solid #000; }
    .strip-5 { top: 32px; height: 8px; background: #FF5C00; border-bottom: 2px solid #000; }

    .field-accent { height: 4px; margin-top: -3px; margin-bottom: 16px; border: none; }
    .accent-y { background: #FFD600; }
    .accent-p { background: #FF3CAC; }
    .accent-g { background: #00FF94; }

    /* LEFT PANEL */
    .left-panel {
        width: 42%;
        background: #001ee1;
        border-right: 5px solid #000;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 48px 44px;
        position: relative;
        overflow: hidden;
    }
    .left-panel::before {
        content: '';
        position: absolute; inset: 0;
        background-image: url('/e-kost-system/assets/img/texturebg.jpg');
        background-size: 600px auto;
        opacity: .06;
    }
    .left-panel > * { position: relative; }

    .brand-tag {
        display: inline-block;
        background: #FFD600;
        color: #000;
        border: 3px solid #000;
        font-family: 'Archivo Black', sans-serif;
        font-size: .75rem;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        padding: 6px 14px;
        box-shadow: 4px 4px 0 #000;
        margin-bottom: 28px;
    }
    .left-title {
        font-family: 'Archivo Black', sans-serif;
        font-size: clamp(2.4rem, 4vw, 4rem);
        color: #fff;
        text-transform: uppercase;
        line-height: .95;
        letter-spacing: -2px;
    }
    .left-title span { color: #FFD600; display: block; }

    .left-desc { color: #a8bcff; font-size: .95rem; font-weight: 500; margin-top: 20px; max-width: 320px; line-height: 1.6; }

    .feature-tags { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 32px; }
    .ftag {
        display: inline-flex; align-items: center; gap: 6px;
        border: 2.5px solid rgba(255,255,255,.4);
        color: #fff; font-weight: 700; font-size: .78rem;
        padding: 6px 12px;
        text-transform: uppercase; letter-spacing: .5px;
    }
    .ftag-dot { width: 8px; height: 8px; border-radius: 50%; }

    .left-bottom-card {
        background: rgba(255,255,255,.08);
        border: 2px solid rgba(255,255,255,.2);
        padding: 20px;
    }
    .left-bottom-card p { color: #cdd6ff; font-size: .82rem; margin: 0; font-style: italic; line-height: 1.5; }
    .left-bottom-card strong { color: #FFD600; font-size: .78rem; display: block; margin-top: 8px; text-transform: uppercase; letter-spacing: 1px; }

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
        border: 4px solid #000;
        box-shadow: 10px 10px 0 #000;
        padding: 40px 36px;
    }
    .login-title {
        font-family: 'Archivo Black', sans-serif;
        font-size: 2rem;
        text-transform: uppercase;
        letter-spacing: -1px;
        line-height: 1;
        margin-bottom: 6px;
    }
    .login-sub { color: #666; font-size: .88rem; font-weight: 500; margin-bottom: 28px; }

    .form-label {
        font-family: 'Archivo Black', sans-serif;
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #000;
        margin-bottom: 6px;
    }
    .nb-input {
        width: 100%;
        border: 3px solid #000;
        border-radius: 0;
        padding: 12px 14px;
        font-family: 'Space Grotesk', sans-serif;
        font-weight: 600;
        font-size: .95rem;
        background: #fff;
        transition: box-shadow .1s, background .1s;
        outline: none;
    }
    .nb-input:focus { background: #FFFDE7; box-shadow: 4px 4px 0 #000; }

    .input-icon-wrap { position: relative; }
    .input-icon-wrap .nb-input { padding-left: 44px; }
    .input-icon {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
        font-size: 1rem; color: #999; pointer-events: none;
    }

    .btn-login {
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
        box-shadow: 6px 6px 0 #000;
        cursor: pointer;
        transition: transform .08s, box-shadow .08s;
        margin-top: 8px;
    }
    .btn-login:hover  { transform: translate(-2px,-2px); box-shadow: 8px 8px 0 #000; background: #0016b0; }
    .btn-login:active { transform: translate(3px,3px); box-shadow: 1px 1px 0 #000; }

    .divider-text {
        display: flex; align-items: center; gap: 12px;
        color: #aaa; font-size: .8rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 1px;
        margin: 20px 0;
    }
    .divider-text::before, .divider-text::after {
        content: ''; flex: 1; height: 2px; background: #e0e0e0;
    }

    .btn-wa {
        width: 100%;
        background: #25D366;
        color: #fff;
        border: 3px solid #000;
        border-radius: 0;
        font-family: 'Archivo Black', sans-serif;
        font-size: .85rem;
        text-transform: uppercase;
        letter-spacing: .5px;
        padding: 12px;
        box-shadow: 5px 5px 0 #000;
        cursor: pointer;
        text-decoration: none;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        transition: transform .08s, box-shadow .08s;
    }
    .btn-wa:hover { transform: translate(-2px,-2px); box-shadow: 7px 7px 0 #000; color: #fff; }

    .error-box {
        background: #FF4B4B;
        color: #fff;
        border: 3px solid #000;
        box-shadow: 4px 4px 0 #000;
        padding: 12px 16px;
        font-weight: 700;
        font-size: .88rem;
        margin-bottom: 20px;
        display: flex; align-items: center; gap: 10px;
    }

    .link-row { text-align: center; margin-top: 20px; font-size: .88rem; }
    .link-row a { color: #001ee1; font-weight: 800; text-decoration: none; border-bottom: 2px solid #001ee1; }
    .link-row a:hover { background: #001ee1; color: #FFD600; padding: 0 4px; }

    .back-link {
        text-align: center; margin-top: 12px;
    }
    .back-link a {
        color: #666; font-size: .82rem; font-weight: 700;
        text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
    }
    .back-link a:hover { color: #001ee1; }

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
    <div class="color-strip strip-1"></div>
    <div class="color-strip strip-2"></div>
    <div class="color-strip strip-3"></div>
    <div class="color-strip strip-4"></div>
    <div class="color-strip strip-5"></div>
    <div>
        <div class="brand-tag">✦ E-KOST SYSTEM</div>
        <h1 class="left-title">Selamat<br>Datang<br><span>Kembali!</span></h1>
        <p class="left-desc">Masuk ke akun kamu dan mulai cari kost terbaik atau kelola propertimu dengan mudah.</p>
        <div class="feature-tags">
            <div class="ftag"><div class="ftag-dot" style="background:#FFD600;"></div>Cari Kost</div>
            <div class="ftag"><div class="ftag-dot" style="background:#FF3CAC;"></div>Booking Online</div>
            <div class="ftag"><div class="ftag-dot" style="background:#00FF94;"></div>Chat Pemilik</div>
            <div class="ftag"><div class="ftag-dot" style="background:#00E0FF;"></div>Dashboard</div>
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
            <h2 class="login-title">Masuk<br>Akun</h2>
            <p class="login-sub">Belum punya akun? <a href="register.php" style="color:#001ee1;font-weight:800;text-decoration:none;border-bottom:2px solid #001ee1;">Daftar gratis</a></p>

            <?php if ($error): ?>
                <div class="error-box">
                    <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-1">
                    <label class="form-label">Username</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-person-fill input-icon"></i>
                        <input type="text" name="username" class="nb-input" placeholder="Masukkan username..." required autocomplete="username">
                    </div>
                </div>
                <div class="field-accent accent-y"></div>
                <div class="mb-1">
                    <label class="form-label">Password</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-lock-fill input-icon"></i>
                        <input type="password" name="password" class="nb-input" placeholder="Masukkan password..." required autocomplete="current-password" id="passInput">
                    </div>
                </div>
                <div class="d-flex justify-content-end mb-4">
                    <label style="font-size:.82rem;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px;">
                        <input type="checkbox" onchange="document.getElementById('passInput').type=this.checked?'text':'password'">
                        Tampilkan Password
                    </label>
                </div>
                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Masuk Sekarang
                </button>
            </form>

            <div class="divider-text">atau</div>

            <a href="https://wa.me/6281234567890?text=Halo%2C+saya+butuh+bantuan+login+E-KOST+System" target="_blank" class="btn-wa">
                <i class="bi bi-whatsapp" style="font-size:1.1rem;"></i> Hubungi Admin via WhatsApp
            </a>
        </div>

        <div class="link-row">
            Belum punya akun? <a href="register.php">Daftar di sini</a>
        </div>
        <div class="back-link">
            <a href="../../index.php"><i class="bi bi-arrow-left"></i> Kembali ke Beranda</a>
        </div>
    </div>
</div>

</body>
</html>
