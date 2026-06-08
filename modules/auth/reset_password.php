<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — E-KOST SYSTEM</title>
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
        display: flex; flex-direction: column; justify-content: center;
        padding: 48px 44px; position: relative; overflow: hidden;
    }
    .left-panel::before {
        content: ''; position: absolute; inset: 0;
        background-image: radial-gradient(circle at 80% 20%, rgba(0,201,208,.25) 0%, transparent 55%),
                          radial-gradient(circle at 10% 80%, rgba(5,150,105,.15) 0%, transparent 45%);
        pointer-events: none;
    }
    .left-panel > * { position: relative; }
    .brand-tag { display:inline-flex;align-items:center;gap:6px;background:rgba(5,150,105,.15);color:#10b981;font-size:.8rem;font-weight:700;padding:6px 14px;border-radius:9999px;border:1px solid rgba(5,150,105,.3);margin-bottom:28px; }
    .left-title { font-size:clamp(2rem,3.5vw,3rem);color:#fff;font-weight:800;line-height:1.1;letter-spacing:-.03em; }
    .left-title span { background:linear-gradient(to right,#10b981,#34d399);-webkit-background-clip:text;-webkit-text-fill-color:transparent;display:block; }
    .left-desc { color:#EBE6D0;font-size:.9rem;font-weight:500;margin-top:20px;max-width:340px;line-height:1.6;opacity:.85; }
    .security-tips { margin-top:32px;display:flex;flex-direction:column;gap:12px; }
    .tip-item { display:flex;align-items:center;gap:10px;padding:12px 16px;background:rgba(255,255,255,.05);border-radius:10px;border:1px solid rgba(255,255,255,.08); }
    .tip-icon { width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0; }
    .tip-text { font-size:.8rem;color:#C4A8E0;font-weight:600;line-height:1.4; }

    .right-panel { flex:1;display:flex;align-items:center;justify-content:center;padding:48px 32px; }
    .auth-box { width:100%;max-width:440px; }
    .auth-card { background:#fff;border:1px solid #D6CEBC;box-shadow:0 10px 30px rgba(30,13,62,.08);padding:44px 40px;border-radius:16px; }
    .auth-title { font-size:1.9rem;font-weight:800;letter-spacing:-.03em;line-height:1.2;margin-bottom:6px;color:#1E0D3E; }
    .auth-sub { color:#5C4D78;font-size:.88rem;font-weight:500;margin-bottom:28px; }
    .form-label { font-weight:700;font-size:.75rem;color:#1E0D3E;margin-bottom:6px;text-transform:uppercase;letter-spacing:.03em; }
    .nb-input { width:100%;border:1.5px solid #D6CEBC;border-radius:8px;padding:12px 44px 12px 14px;font-family:inherit;font-weight:500;font-size:.95rem;background:#fff;transition:all .15s;outline:none; }
    .nb-input:focus { border-color:#00B4BA;box-shadow:0 0 0 4px rgba(0,180,186,.12); }
    .input-wrap { position:relative; }
    .toggle-pw { position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;color:#5C4D78;cursor:pointer;font-size:1rem;padding:0; }

    /* Strength Bar */
    .strength-bar-wrap { display:flex;gap:4px;margin-top:8px; }
    .strength-bar { flex:1;height:4px;border-radius:99px;background:#D6CEBC;transition:background .3s; }
    .strength-label { font-size:.75rem;font-weight:700;margin-top:4px; }
    .s-weak .strength-bar:nth-child(1) { background:#DC2626; }
    .s-fair .strength-bar:nth-child(1), .s-fair .strength-bar:nth-child(2) { background:#D97706; }
    .s-good .strength-bar:nth-child(-n+3) { background:#059669; }
    .s-strong .strength-bar { background:#00B4BA; }

    .btn-main { width:100%;background:linear-gradient(135deg,#00B4BA,#008F95);color:#fff;border:none;border-radius:8px;font-weight:700;font-size:.95rem;padding:14px;cursor:pointer;transition:all .2s;margin-top:8px; }
    .btn-main:hover { transform:translateY(-1px);box-shadow:0 6px 20px rgba(0,180,186,.35); }
    .btn-main:active { transform:translateY(1px); }
    .icon-circle { width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,rgba(5,150,105,.15),rgba(5,150,105,.05));border:2px solid rgba(5,150,105,.25);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:1.6rem;color:#059669; }
    .back-link { text-align:center;margin-top:16px; }
    .back-link a { color:#5C4D78;font-size:.85rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px; }
    .back-link a:hover { color:#00B4BA; }
    .match-msg { font-size:.78rem;font-weight:600;margin-top:4px; }
    @media(max-width:768px) { .left-panel{display:none;} .right-panel{padding:32px 16px;} .auth-card{padding:32px 24px;} }
    </style>
</head>
<body>

<div class="left-panel">
    <div>
        <div class="brand-tag"><i class="bi bi-shield-check-fill"></i> Keamanan Akun</div>
        <h1 class="left-title">Buat Password<br><span>Baru yang Kuat!</span></h1>
        <p class="left-desc">Pastikan password baru Anda aman dan tidak mudah ditebak untuk melindungi akun E-KOST System.</p>
        <div class="security-tips">
            <div class="tip-item">
                <div class="tip-icon" style="background:rgba(0,201,208,.15);color:#00C9D0"><i class="bi bi-123"></i></div>
                <div class="tip-text">Gunakan minimal 8 karakter dengan kombinasi huruf dan angka</div>
            </div>
            <div class="tip-item">
                <div class="tip-icon" style="background:rgba(232,69,69,.15);color:#FF5F5F"><i class="bi bi-exclamation-triangle-fill"></i></div>
                <div class="tip-text">Jangan gunakan informasi pribadi seperti nama atau tanggal lahir</div>
            </div>
            <div class="tip-item">
                <div class="tip-icon" style="background:rgba(5,150,105,.15);color:#10b981"><i class="bi bi-check-circle-fill"></i></div>
                <div class="tip-text">Tambahkan karakter spesial (!@#$%) untuk keamanan ekstra</div>
            </div>
        </div>
    </div>
</div>

<div class="right-panel">
    <div class="auth-box">
        <div class="auth-card">
            <div class="icon-circle"><i class="bi bi-key-fill"></i></div>
            <h2 class="auth-title text-center">Password Baru</h2>
            <p class="auth-sub text-center">Buat password baru untuk akun Anda. Pastikan Anda mengingatnya dengan baik.</p>

            <form id="resetForm">
                <div class="mb-4">
                    <label class="form-label">Password Baru</label>
                    <div class="input-wrap">
                        <input type="password" id="newPass" class="nb-input" placeholder="Minimal 8 karakter" required oninput="checkStrength(this.value)">
                        <button type="button" class="toggle-pw" onclick="togglePw('newPass',this)"><i class="bi bi-eye-fill"></i></button>
                    </div>
                    <div class="strength-bar-wrap" id="strengthBars">
                        <div class="strength-bar"></div>
                        <div class="strength-bar"></div>
                        <div class="strength-bar"></div>
                        <div class="strength-bar"></div>
                    </div>
                    <div class="strength-label text-muted" id="strengthLabel">Masukkan password untuk melihat kekuatan</div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Konfirmasi Password</label>
                    <div class="input-wrap">
                        <input type="password" id="confirmPass" class="nb-input" placeholder="Ulangi password baru" required oninput="checkMatch()">
                        <button type="button" class="toggle-pw" onclick="togglePw('confirmPass',this)"><i class="bi bi-eye-fill"></i></button>
                    </div>
                    <div class="match-msg" id="matchMsg"></div>
                </div>
                <button type="submit" class="btn-main" id="submitBtn">
                    <i class="bi bi-shield-check me-2"></i>Reset Password Sekarang
                </button>
            </form>
        </div>
        <div class="back-link">
            <a href="login.php"><i class="bi bi-arrow-left"></i> Kembali ke Halaman Login</a>
        </div>
    </div>
</div>

<script>
function togglePw(id, btn) {
    const inp = document.getElementById(id);
    const isText = inp.type === 'text';
    inp.type = isText ? 'password' : 'text';
    btn.innerHTML = isText ? '<i class="bi bi-eye-fill"></i>' : '<i class="bi bi-eye-slash-fill"></i>';
}

function checkStrength(val) {
    const wrap = document.getElementById('strengthBars');
    const label = document.getElementById('strengthLabel');
    wrap.className = 'strength-bar-wrap';
    let score = 0;
    if (val.length >= 8) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const levels = [
        { cls: '', text: 'Masukkan password', color: '#5C4D78' },
        { cls: 's-weak', text: '⚠ Lemah', color: '#DC2626' },
        { cls: 's-fair', text: '◉ Sedang', color: '#D97706' },
        { cls: 's-good', text: '✓ Kuat', color: '#059669' },
        { cls: 's-strong', text: '✦ Sangat Kuat', color: '#00B4BA' },
    ];
    const lvl = val.length === 0 ? 0 : score;
    wrap.classList.add(levels[lvl].cls);
    label.textContent = levels[lvl].text;
    label.style.color = levels[lvl].color;
}

function checkMatch() {
    const p1 = document.getElementById('newPass').value;
    const p2 = document.getElementById('confirmPass').value;
    const msg = document.getElementById('matchMsg');
    if (!p2) { msg.textContent = ''; return; }
    if (p1 === p2) { msg.textContent = '✓ Password cocok'; msg.style.color = '#059669'; }
    else { msg.textContent = '✗ Password tidak cocok'; msg.style.color = '#DC2626'; }
}

document.getElementById('resetForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const p1 = document.getElementById('newPass').value;
    const p2 = document.getElementById('confirmPass').value;
    if (p1 !== p2) { Swal.fire({ icon:'error', title:'Password Tidak Cocok', text:'Pastikan kedua password Anda sama.', confirmButtonColor:'#DC2626' }); return; }
    if (p1.length < 8) { Swal.fire({ icon:'warning', title:'Password Terlalu Pendek', text:'Gunakan minimal 8 karakter.', confirmButtonColor:'#D97706' }); return; }
    const btn = document.getElementById('submitBtn');
    btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
    setTimeout(() => {
        Swal.fire({
            icon: 'success', title: 'Password Berhasil Direset!',
            text: 'Password Anda telah berhasil diperbarui. Silakan login dengan password baru.',
            confirmButtonText: 'Masuk Sekarang', confirmButtonColor: '#00B4BA',
        }).then(() => { window.location.href = 'login.php'; });
    }, 1500);
});
</script>
</body>
</html>
