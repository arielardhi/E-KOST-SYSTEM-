<?php 
require_once 'config/database.php';
include 'layouts/header.php'; 
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap');
/* ── SHOWCASE SPECIFIC ── */
.hero-showcase {
    background: #001ee1;
    border-bottom: 5px solid #000;
    padding: 100px 0 80px; /* Diperbesar paddingnya */
    position: relative;
    overflow: hidden;
}
.hero-showcase::before {
    content: '';
    position: absolute; inset: 0;
    background-image: url('assets/img/texturebg.jpg');
    background-size: 700px auto;
    opacity: .08;
}
.hero-badge {
    display: inline-block;
    background: #FFD600;
    color: #000;
    border: 3px solid #000;
    font-family: 'Archivo Black', sans-serif;
    font-size: .8rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    padding: 6px 16px;
    box-shadow: 4px 4px 0 #000;
    margin-bottom: 20px;
}
.hero-title {
    font-family: 'Archivo Black', sans-serif;
    font-size: clamp(2.4rem, 6vw, 5rem);
    line-height: 1;
    color: #fff;
    text-transform: uppercase;
    letter-spacing: -2px;
}
.hero-title span { color: #FFD600; }
.hero-subtitle { color: #a8bcff; font-size: 1.1rem; font-weight: 500; max-width: 520px; }

.stat-pill {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: rgba(255,255,255,.1);
    border: 2px solid rgba(255,255,255,.3);
    padding: 10px 20px;
    font-weight: 700;
    color: #fff;
}
.stat-pill strong { font-family: 'Archivo Black', sans-serif; font-size: 1.4rem; color: #FFD600; }

/* ── SECTION LABELS ── */
.section-label {
    display: inline-block;
    font-family: 'Archivo Black', sans-serif;
    font-size: .75rem;
    text-transform: uppercase;
    letter-spacing: 2px;
    padding: 5px 14px;
    border: 2.5px solid #000;
    box-shadow: 3px 3px 0 #000;
    margin-bottom: 12px;
}

/* ── FEATURE BLOCKS ── */
.feature-section { padding: 80px 0; }
.feature-image-wrap {
    border: 4px solid #000;
    box-shadow: 10px 10px 0 #000;
    overflow: hidden;
    position: relative;
}
.feature-image-wrap img { display: block; width: 100%; height: 320px; object-fit: cover; }
.img-label {
    position: absolute;
    bottom: 16px; left: 16px;
    background: #FFD600;
    border: 2.5px solid #000;
    font-family: 'Archivo Black', sans-serif;
    font-size: .75rem;
    text-transform: uppercase;
    padding: 5px 12px;
    box-shadow: 3px 3px 0 #000;
}

.feature-heading {
    font-family: 'Archivo Black', sans-serif;
    font-size: clamp(1.6rem, 3vw, 2.4rem);
    text-transform: uppercase;
    line-height: 1.05;
    letter-spacing: -1px;
}

/* ── FEATURE ITEMS (checklist rows) ── */
.feature-item {
    display: flex;
    gap: 16px;
    align-items: flex-start;
    padding: 16px;
    border: 2.5px solid #000;
    box-shadow: 4px 4px 0 #000;
    margin-bottom: 12px;
    background: #fff;
    transition: transform .12s, box-shadow .12s;
}
.feature-item:hover {
    transform: translate(-2px,-2px);
    box-shadow: 6px 6px 0 #000;
}
.feature-icon-box {
    width: 44px; height: 44px;
    display: flex; align-items: center; justify-content: center;
    border: 2.5px solid #000;
    font-size: 1.3rem;
    flex-shrink: 0;
}
.feature-item h6 { font-family: 'Archivo Black', sans-serif; font-size: .9rem; text-transform: uppercase; margin-bottom: 4px; }
.feature-item p  { font-size: .83rem; color: #444; margin: 0; }

/* ── TECH GRID ── */
.tech-card {
    border: 3px solid #000;
    box-shadow: 6px 6px 0 #000;
    padding: 28px 20px;
    text-align: center;
    background: #fff;
    transition: transform .12s, box-shadow .12s;
}
.tech-card:hover { transform: translate(-3px,-3px); box-shadow: 9px 9px 0 #000; }
.tech-card i { font-size: 2.2rem; }
.tech-card h6 { font-family: 'Archivo Black', sans-serif; text-transform: uppercase; font-size: .85rem; margin: 10px 0 6px; }
.tech-card p { font-size: .78rem; color: #555; margin: 0; }

/* ── CTA ── */
.cta-section {
    background: #FFD600;
    border-top: 5px solid #000;
    border-bottom: 5px solid #000;
    padding: 80px 0;
    position: relative;
    overflow: hidden;
}
.cta-section::before {
    content: '';
    position: absolute; inset: 0;
    background-image: url('assets/img/texturebg.jpg');
    background-size: 700px auto;
    opacity: .15;
}
.cta-title {
    font-family: 'Archivo Black', sans-serif;
    font-size: clamp(2rem, 4vw, 3.5rem);
    text-transform: uppercase;
    line-height: 1;
    letter-spacing: -2px;
}
.cta-btn-main {
    background: #000;
    color: #FFD600 !important;
    border: 3px solid #000 !important;
    font-size: 1rem;
    padding: 14px 36px;
    box-shadow: 6px 6px 0 rgba(0,0,0,.3);
}
.cta-btn-main:hover { background: #111; transform: translate(-2px,-2px); box-shadow: 8px 8px 0 rgba(0,0,0,.3); }
.cta-btn-outline {
    background: transparent;
    color: #000 !important;
    border: 3px solid #000 !important;
    font-size: 1rem;
    padding: 14px 36px;
    box-shadow: 6px 6px 0 #000;
}
.cta-btn-outline:hover { background: rgba(0,0,0,.08); transform: translate(-2px,-2px); box-shadow: 8px 8px 0 #000; }

/* ── MARQUEE ── */
.marquee-wrap {
    background: #000;
    border-top: 4px solid #000;
    border-bottom: 4px solid #000;
    overflow: hidden;
    padding: 12px 0;
    white-space: nowrap;
}
.marquee-track {
    display: inline-flex;
    animation: marquee 22s linear infinite;
}
.marquee-item {
    font-family: 'Archivo Black', sans-serif;
    text-transform: uppercase;
    font-size: .85rem;
    color: #FFD600;
    letter-spacing: 1px;
    padding: 0 32px;
}
.marquee-dot {
    color: #FF5C00;
    margin: 0 4px;
}
@keyframes marquee { from { transform: translateX(0); } to { transform: translateX(-50%); } }

/* ── OWNER SECTION CUSTOM ── */
/* BAGIAN INI UNTUK MENGATUR WARNA BACKGROUND OWNER (HITAM) */
.feature-section.owner-dark {
    background-color: #000000ff !important; /* WARNA BG HITAM */
    color: #ffffff !important;
    border-top: 5px solid #000;
    border-bottom: 5px solid #000;
}
.feature-section.owner-dark .feature-heading {
    color: #FFD600;
}
.feature-section.owner-dark .feature-item {
    background: #ffffffff;
    border-color: #333;
    box-shadow: 4px 4px 0 #FFD600;
}
.feature-section.owner-dark .feature-item h6 {
    color: #000000ff;
}
.feature-section.owner-dark .feature-item p {
    color: #000000ff;
}
.feature-section.owner-dark .feature-icon-box {
    border-color: #FFD600;
}
</style>

<!-- HERO -->
<section class="hero-showcase">
    <div class="container position-relative">
        <div class="row align-items-center gy-4">
            <div class="col-lg-6"> <!-- Diubah dari col-lg-7 -->
                <div class="hero-badge">✦ Platform Kost #1 di Indonesia</div>
                <h1 class="hero-title mb-3">E-KOST<br><span>SYSTEM</span></h1>
                <p class="hero-subtitle mb-4">Solusi terpadu cari, kelola, dan sewa kost dengan sistem modern, cepat, dan terpercaya.</p>
                <div class="d-flex flex-wrap gap-3">
                    <div class="stat-pill"><strong>500+</strong> Kost Terdaftar</div>
                    <div class="stat-pill"><strong>2K+</strong> Pengguna Aktif</div>
                    <div class="stat-pill"><strong>98%</strong> Kepuasan</div>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-flex justify-content-end"> <!-- Diubah dari col-lg-5 -->
                <!-- GAMBAR HERO DIPERBESAR (width: 480px, height: 320px) -->
                <div style="border:6px solid #FFD600;box-shadow:15px 15px 0 #FFD600;overflow:hidden;width:480px;">
                    <img src="https://images.unsplash.com/photo-1554995207-c18c203602cb?auto=format&fit=crop&w=720&q=80"
                         style="width:100%;height:320px;object-fit:cover;display:block;" alt="Hero Image">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MARQUEE -->
<div class="marquee-wrap" aria-hidden="true">
    <div class="marquee-track">
        <?php $items = ['Cari Kost','Booking Online','Chat Pemilik','Filter Canggih','Manajemen Properti','Verifikasi Pembayaran','Real-Time Notifikasi','Dashboard Modern','Statistik Bisnis','Upload Foto Kost']; ?>
        <?php for ($i=0; $i<4; $i++): foreach($items as $item): ?>
            <span class="marquee-item"><span class="marquee-dot">✦</span> <?= $item ?></span>
        <?php endforeach; endfor; ?>
    </div>
</div>

<!-- FITUR TENANT -->
<section class="feature-section" style="background:transparent;">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-md-6">
                <div class="feature-image-wrap">
                    <img src="https://images.unsplash.com/photo-1554995207-c18c203602cb?auto=format&fit=crop&w=800&q=80" alt="Tenant">
                    <span class="img-label">For Tenants</span>
                </div>
            </div>
            <div class="col-md-6">
                <span class="section-label" style="background:#00E0FF;">👤 Pencari Kost</span>
                <h2 class="feature-heading mb-4">Temukan Kost<br>Impianmu —<br>Tanpa Ribet</h2>

                <div class="feature-item">
                    <div class="feature-icon-box" style="background:#FFD600;">🔍</div>
                    <div>
                        <h6>Pencarian & Filter Canggih</h6>
                        <p>Cari berdasarkan lokasi, harga, tipe (Putra/Putri/Campur), dan fasilitas yang kamu butuhkan.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon-box" style="background:#FF3CAC;"><span style="color:#fff;">📅</span></div>
                    <div>
                        <h6>Booking Online Instan</h6>
                        <p>Pesan kamar langsung dari platform tanpa perlu datang ke lokasi terlebih dahulu.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon-box" style="background:#00FF94;">💳</div>
                    <div>
                        <h6>Sistem Pembayaran Terintegrasi</h6>
                        <p>Upload bukti bayar dan pantau status verifikasi real-time dari dashboard kamu.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon-box" style="background:#7B2FFF;"><span style="color:#fff;">💬</span></div>
                    <div>
                        <h6>Chat Langsung ke Pemilik</h6>
                        <p>Hubungi pemilik via chat sistem atau WhatsApp langsung dari halaman kost.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- DIVIDER -->
<div style="border-top:4px solid #000;background:#001ee1;padding:24px 0;" aria-hidden="true">
    <div class="container text-center">
        <span style="font-family:'Archivo Black',sans-serif;color:#FFD600;font-size:1.1rem;text-transform:uppercase;letter-spacing:1px;">
            ✦ Semua yang kamu butuhkan ada di sini ✦
        </span>
    </div>
</div>

<!-- FITUR OWNER (BACKGROUND HITAM) -->
<section class="feature-section owner-dark">
    <div class="container">
        <div class="row align-items-center g-5 flex-md-row-reverse">
            <div class="col-md-6">
                <div class="feature-image-wrap" style="border-color: #FFD600; box-shadow: 10px 10px 0 #FFD600;">
                    <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=800&q=80" alt="Owner">
                    <span class="img-label">For Owners</span>
                </div>
            </div>
            <div class="col-md-6">
                <span class="section-label" style="background:#FF5C00;color:#fff;border-color:#FFD600;">🏠 Pemilik Kost</span>
                <h2 class="feature-heading mb-4">Kelola Bisnis<br>Kost Lebih<br>Profesional</h2>

                <div class="feature-item">
                    <div class="feature-icon-box" style="background:#FFD600;">📊</div>
                    <div>
                        <h6>Dashboard Bisnis Lengkap</h6>
                        <p>Pantau jumlah pesanan, status pembayaran, dan statistik kost kamu dalam satu layar.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon-box" style="background:#00E0FF;">🏗️</div>
                    <div>
                        <h6>Manajemen Properti & Kamar</h6>
                        <p>Tambah kost baru, atur tipe kamar, fasilitas, hingga ketersediaan stok kamar secara mandiri.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon-box" style="background:#FF3CAC;"><span style="color:#fff;">✅</span></div>
                    <div>
                        <h6>Konfirmasi Pesanan Cepat</h6>
                        <p>Terima atau tolak pesanan masuk dengan mudah setelah memverifikasi bukti bayar.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- TECH STACK -->
<section class="feature-section" style="background:#f0f0f0; border-top:5px solid #000;">
    <div class="container text-center">
        <span class="section-label" style="background:#fff;">⚙️ Teknologi</span>
        <h2 class="feature-heading mb-5">Modern Tech Stack</h2>
        <div class="row g-4">
            <div class="col-6 col-md-3">
                <article class="tech-card">
                    <i class="bi bi-filetype-php text-primary" aria-hidden="true"></i>
                    <h6>PHP Native</h6>
                    <p>Core Logic</p>
                </article>
            </div>
            <div class="col-6 col-md-3">
                <article class="tech-card">
                    <i class="bi bi-database-fill text-danger" aria-hidden="true"></i>
                    <h6>MySQL</h6>
                    <p>Database</p>
                </article>
            </div>
            <div class="col-6 col-md-3">
                <article class="tech-card">
                    <i class="bi bi-bootstrap-fill" style="color:#7952b3;" aria-hidden="true"></i>
                    <h6>Bootstrap 5</h6>
                    <p>UI Framework</p>
                </article>
            </div>
            <div class="col-6 col-md-3">
                <article class="tech-card">
                    <i class="bi bi-palette-fill text-warning" aria-hidden="true"></i>
                    <h6>Neubrutalism</h6>
                    <p>Design Style</p>
                </article>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container text-center position-relative" style="z-index:2;">
        <h2 class="cta-title mb-4">Mulai Gunakan<br>E-KOST SYSTEM Sekarang</h2>
        <div class="d-flex justify-content-center gap-3">
            <a href="modules/auth/register.php" class="btn cta-btn-main">DAFTAR SEKARANG</a>
            <a href="modules/auth/login.php" class="btn cta-btn-outline">MASUK KE AKUN</a>
        </div>
    </div>
</section>

<?php include 'layouts/footer.php'; ?>
