<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password — E-KOST SYSTEM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Plus Jakarta Sans', sans-serif; min-height: 100vh; display: flex; background-color: #F3EED8; }

    .left-panel {
        width: 42%;
        background: linear-gradient(135deg, #1E0D3E 0%, #2D1459 100%);
        display: flex; flex-direction: column; justify-content: space-between;
        padding: 48px 44px; position: relative; overflow: hidden;
    }
    .left-panel::before {
        content: ''; position: absolute; inset: 0;
        background-image: radial-gradient(circle at 70% 20%, rgba(0,201,208,.22) 0%, transparent 55%),
                          radial-gradient(circle at 20% 80%, rgba(232,69,69,.12) 0%, transparent 45%);
        pointer-events: none;
    }
    .left-panel > * { position: relative; }
    .brand-tag { display:inline-flex;align-items:center;gap:6px;background:rgba(0,201,208,.15);color:#00C9D0;font-size:.8rem;font-weight:700;padding:6px 14px;border-radius:9999px;border:1px solid rgba(0,201,208,.3);margin-bottom:28px; }
    .left-title { font-size:clamp(2.2rem,4vw,3.2rem);color:#fff;font-weight:800;line-height:1.1;letter-spacing:-.03em; }
    .left-title span { background:linear-gradient(to right,#00C9D0,#6EEAF0);-webkit-background-clip:text;-webkit-text-fill-color:transparent;display:block; }
    .left-desc { color:#EBE6D0;font-size:.95rem;font-weight:500;margin-top:20px;max-width:340px;line-height:1.6;opacity:.85; }
    .steps-list { list-style:none;padding:0;margin-top:32px;display:flex;flex-direction:column;gap:16px; }
    .steps-list li { display:flex;align-items:center;gap:12px;color:#C4A8E0;font-size:.875rem;font-weight:600; }
    .step-num { width:28px;height:28px;border-radius:50%;background:rgba(0,201,208,.2);border:1.5px solid #00C9D0;display:flex;align-items:center;justify-content:center;color:#00C9D0;font-size:.75rem;font-weight:800;flex-shrink:0; }
    .left-bottom-card { background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);padding:20px;border-radius:12px; }
    .left-bottom-card p { color:#EBE6D0;font-size:.85rem;margin:0;font-style:italic;line-height:1.6;opacity:.8; }
    .left-bottom-card strong { color:#00C9D0;font-size:.8rem;display:block;margin-top:8px;font-weight:700; }

    .right-panel { flex:1;display:flex;align-items:center;justify-content:center;padding:48px 32px; }
    .auth-box { width:100%;max-width:440px; }
    .auth-card { background:#fff;border:1px solid #D6CEBC;box-shadow:0 10px 30px rgba(30,13,62,.08);padding:44px 40px;border-radius:16px; }
    .auth-title { font-size:1.9rem;font-weight:800;letter-spacing:-.03em;line-height:1.2;margin-bottom:6px;color:#1E0D3E; }
    .auth-sub { color:#5C4D78;font-size:.88rem;font-weight:500;margin-bottom:28px; }
    .form-label { font-weight:700;font-size:.75rem;color:#1E0D3E;margin-bottom:6px;text-transform:uppercase;letter-spacing:.03em; }
    .nb-input { width:100%;border:1.5px solid #D6CEBC;border-radius:8px;padding:12px 14px;font-family:inherit;font-weight:500;font-size:.95rem;background:#fff;transition:all .15s;outline:none; }
    .nb-input:focus { border-color:#00B4BA;box-shadow:0 0 0 4px rgba(0,180,186,.12); }
    .input-icon-wrap { position:relative; }
    .input-icon-wrap .nb-input { padding-left:44px; }
    .input-icon { position:absolute;left:14px;top:50%;transform:translateY(-50%);font-size:1.1rem;color:#5C4D78;pointer-events:none; }
    .btn-main { width:100%;background:linear-gradient(135deg,#00B4BA,#008F95);color:#fff;border:none;border-radius:8px;font-weight:700;font-size:.95rem;padding:14px;cursor:pointer;transition:all .2s;margin-top:8px; }
    .btn-main:hover { transform:translateY(-1px);box-shadow:0 6px 20px rgba(0,180,186,.35); }
    .btn-main:active { transform:translateY(1px); }
    .link-row { text-align:center;margin-top:20px;font-size:.88rem;color:#5C4D78; }
    .link-row a { color:#00B4BA;font-weight:700;text-decoration:none; }
    .link-row a:hover { text-decoration:underline; }
    .back-link { text-align:center;margin-top:16px; }
    .back-link a { color:#5C4D78;font-size:.85rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px; }
    .back-link a:hover { color:#00B4BA; }
    .icon-circle { width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,rgba(0,180,186,.15),rgba(0,180,186,.05));border:2px solid rgba(0,180,186,.25);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:1.6rem;color:#00B4BA; }
    @media(max-width:768px) { .left-panel{display:none;} .right-panel{padding:32px 16px;} .auth-card{padding:32px 24px;} }
    </style>
</head>
<body>

<!-- LEFT PANEL -->
<div class="left-panel">
    <div>
        <div class="brand-tag"><i class="bi bi-house-fill"></i> E-KOST SYSTEM</div>
        <h1 class="left-title">Lupa<br>Password?<br><span>Tak Masalah!</span></h1>
        <p class="left-desc">Kami akan bantu Anda memulihkan akses ke akun E-KOST System dengan cepat dan aman.</p>
        <ul class="steps-list">
            <li><div class="step-num">1</div>Masukkan email yang terdaftar</li>
            <li><div class="step-num">2</div>Cek link reset di kotak masuk email</li>
            <li><div class="step-num">3</div>Buat password baru yang kuat</li>
            <li><div class="step-num">4</div>Masuk kembali ke akun Anda</li>
        </ul>
    </div>
    <div class="left-bottom-card">
        <p>Keamanan akun Anda adalah prioritas kami. Link reset hanya berlaku selama 60 menit.</p>
        <strong>— Tim E-KOST SYSTEM</strong>
    </div>
</div>

<!-- RIGHT PANEL -->
<div class="right-panel">
    <div class="auth-box">
        <div class="auth-card">
            <div class="icon-circle"><i class="bi bi-envelope-open-fill"></i></div>
            <h2 class="auth-title text-center">Reset Password</h2>
            <p class="auth-sub text-center">Masukkan email Anda dan kami akan mengirimkan link untuk membuat password baru.</p>

            <form id="forgotForm">
                <div class="mb-4">
                    <label class="form-label">Alamat Email</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-envelope-fill input-icon"></i>
                        <input type="email" id="emailInput" class="nb-input" placeholder="contoh@email.com" required>
                    </div>
                </div>
                <button type="submit" class="btn-main">
                    <i class="bi bi-send-fill me-2"></i>Kirim Link Reset Password
                </button>
            </form>

            <div class="link-row">Ingat password Anda? <a href="login.php">Masuk sekarang</a></div>
        </div>
        <div class="back-link">
            <a href="../../index.php"><i class="bi bi-arrow-left"></i> Kembali ke Beranda</a>
        </div>
    </div>
</div>

<script>
document.getElementById('forgotForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const email = document.getElementById('emailInput').value;
    const btn = this.querySelector('button[type=submit]');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';

    setTimeout(() => {
        Swal.fire({
            icon: 'success',
            title: 'Email Terkirim!',
            html: `<p>Link reset password telah dikirim ke:</p><strong>${email}</strong><p class="mt-2 text-muted" style="font-size:.875rem">Link berlaku selama <strong>60 menit</strong>. Cek folder Spam jika tidak ditemukan di Inbox.</p>`,
            confirmButtonText: 'OK, Mengerti',
            confirmButtonColor: '#00B4BA',
            showClass: { popup: 'animate__animated animate__fadeInDown' }
        }).then(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-send-fill me-2"></i>Kirim Link Reset Password';
            document.getElementById('emailInput').value = '';
        });
    }, 1500);
});
</script>
</body>
</html>
