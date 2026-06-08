<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Halaman Tidak Ditemukan | E-KOST SYSTEM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        min-height: 100vh;
        background: linear-gradient(135deg, #1E0D3E 0%, #2D1459 60%, #3D1F72 100%);
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        position: relative; overflow: hidden;
        padding: 40px 20px;
    }
    body::before {
        content: '';
        position: absolute; inset: 0;
        background-image:
            radial-gradient(circle at 70% 20%, rgba(0,201,208,.2) 0%, transparent 50%),
            radial-gradient(circle at 15% 80%, rgba(232,69,69,.15) 0%, transparent 45%);
        pointer-events: none;
    }
    /* Floating particles */
    .particle {
        position: absolute;
        border-radius: 50%;
        opacity: .15;
        animation: float 6s ease-in-out infinite;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50%       { transform: translateY(-30px) rotate(180deg); }
    }

    /* 404 Number */
    .error-number {
        font-size: clamp(6rem, 18vw, 14rem);
        font-weight: 800;
        line-height: 1;
        letter-spacing: -.05em;
        background: linear-gradient(135deg, #00C9D0 0%, rgba(255,255,255,.4) 50%, #6EEAF0 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        position: relative;
        z-index: 1;
        text-shadow: none;
        filter: drop-shadow(0 0 60px rgba(0,201,208,.3));
        animation: pulse404 3s ease-in-out infinite;
    }
    @keyframes pulse404 {
        0%, 100% { filter: drop-shadow(0 0 40px rgba(0,201,208,.25)); }
        50%       { filter: drop-shadow(0 0 80px rgba(0,201,208,.5)); }
    }

    /* Icon between 4s */
    .error-icon-center {
        position: absolute;
        left: 50%; top: 50%;
        transform: translate(-50%, -50%);
        font-size: clamp(3rem, 8vw, 6rem);
        color: rgba(255,255,255,.15);
        z-index: 0;
    }

    .error-number-wrap { position: relative; display: inline-block; }

    .error-title {
        font-size: clamp(1.3rem, 3vw, 2rem);
        font-weight: 800;
        color: #ffffff;
        margin-bottom: 12px;
        letter-spacing: -.02em;
    }
    .error-desc {
        font-size: 1rem;
        color: rgba(255,255,255,.65);
        max-width: 480px;
        line-height: 1.7;
        margin-bottom: 36px;
    }

    /* Buttons */
    .btn-ghost {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 13px 28px;
        border-radius: 99px;
        font-weight: 700;
        font-size: .9rem;
        cursor: pointer;
        transition: all .2s ease;
        text-decoration: none;
    }
    .btn-primary-404 {
        background: linear-gradient(135deg, #00B4BA, #008F95);
        color: #fff;
        border: none;
        box-shadow: 0 6px 24px rgba(0,180,186,.35);
    }
    .btn-primary-404:hover { transform: translateY(-2px); box-shadow: 0 10px 32px rgba(0,180,186,.5); color:#fff; }
    .btn-outline-404 {
        background: transparent;
        color: rgba(255,255,255,.85);
        border: 1.5px solid rgba(255,255,255,.2);
        backdrop-filter: blur(8px);
    }
    .btn-outline-404:hover { background: rgba(255,255,255,.1); border-color: rgba(255,255,255,.4); color:#fff; }

    /* Quick links */
    .quick-links { display: flex; gap: 12px; flex-wrap: wrap; justify-content: center; margin-top: 20px; }
    .quick-link {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 16px;
        border-radius: 99px;
        font-size: .8rem;
        font-weight: 600;
        color: rgba(255,255,255,.6);
        border: 1px solid rgba(255,255,255,.12);
        text-decoration: none;
        transition: all .15s;
    }
    .quick-link:hover { color: #fff; border-color: rgba(255,255,255,.3); background: rgba(255,255,255,.06); }

    /* Breadcrumb top */
    .top-bar { position: absolute; top: 24px; left: 50%; transform: translateX(-50%); z-index: 10; }
    .brand-pill {
        display: inline-flex; align-items: center; gap: 8px;
        background: rgba(255,255,255,.06);
        border: 1px solid rgba(255,255,255,.12);
        backdrop-filter: blur(10px);
        padding: 8px 20px;
        border-radius: 99px;
        color: #fff;
        font-weight: 700;
        font-size: .85rem;
        text-decoration: none;
    }
    .brand-dot { width: 8px; height: 8px; border-radius: 50%; background: #00C9D0; }
    </style>
</head>
<body>

<!-- Floating Particles -->
<div class="particle" style="width:80px;height:80px;background:#00C9D0;top:10%;left:8%;animation-delay:0s;animation-duration:7s"></div>
<div class="particle" style="width:50px;height:50px;background:#E84545;top:25%;right:12%;animation-delay:1.5s;animation-duration:5s"></div>
<div class="particle" style="width:35px;height:35px;background:#D97706;bottom:20%;left:15%;animation-delay:3s;animation-duration:8s"></div>
<div class="particle" style="width:60px;height:60px;background:#5C4D78;bottom:30%;right:8%;animation-delay:.5s;animation-duration:6s"></div>
<div class="particle" style="width:25px;height:25px;background:#00C9D0;top:60%;left:5%;animation-delay:2s;animation-duration:9s"></div>

<!-- Brand Top -->
<div class="top-bar">
    <a href="../../index.php" class="brand-pill">
        <div class="brand-dot"></div>
        E-KOST SYSTEM
    </a>
</div>

<!-- Main Content -->
<div class="text-center position-relative" style="z-index:1">
    <!-- 404 Number -->
    <div class="error-number-wrap mb-2">
        <div class="error-number">404</div>
        <i class="bi bi-question-circle-fill error-icon-center"></i>
    </div>

    <h1 class="error-title">Halaman Tidak Ditemukan</h1>
    <p class="error-desc mx-auto">
        Oops! Halaman yang Anda cari tidak tersedia, telah dipindahkan, atau mungkin URL yang Anda masukkan salah.
    </p>

    <!-- Action Buttons -->
    <div class="d-flex gap-3 justify-content-center flex-wrap mb-4">
        <a href="javascript:history.back()" class="btn-ghost btn-outline-404">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <a href="../../index.php" class="btn-ghost btn-primary-404">
            <i class="bi bi-house-fill"></i> Ke Beranda
        </a>
    </div>

    <!-- Quick Links -->
    <div class="quick-links">
        <a href="../../pages/kost_list.php" class="quick-link"><i class="bi bi-search"></i>Cari Kost</a>
        <a href="../../modules/auth/login.php" class="quick-link"><i class="bi bi-box-arrow-in-right"></i>Login</a>
        <a href="../../pages/about.php" class="quick-link"><i class="bi bi-info-circle"></i>Tentang Kami</a>
        <a href="../../pages/contact.php" class="quick-link"><i class="bi bi-envelope"></i>Kontak</a>
    </div>

    <!-- Error Code -->
    <div style="margin-top:48px;color:rgba(255,255,255,.25);font-size:.75rem;font-weight:600;letter-spacing:.1em">
        ERROR CODE: 404 | PAGE NOT FOUND
    </div>
</div>

</body>
</html>
