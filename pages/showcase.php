<?php include '../layouts/header.php'; ?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap');
/* ── SHOWCASE SPECIFIC ── */
.hero-showcase {
    background: #001ee1;
    border-bottom: 5px solid #000;
    padding: 80px 0 60px;
    position: relative;
    overflow: hidden;
}
.hero-showcase::before {
    content: '';
    position: absolute; inset: 0;
    background-image: url('/e-kost-system/assets/img/texturebg.jpg');
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
    background-image: url('/e-kost-system/assets/img/texturebg.jpg');
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
</style>

<!-- HERO -->
<div class="hero-showcase">
    <div class="container position-relative">
        <div class="row align-items-center gy-4">
            <div class="col-lg-7">
                <div class="hero-badge">✦ Platform Kost #1 di Indonesia</div>
                <h1 class="hero-title mb-3">E-KOST<br><span>SYSTEM</span></h1>
                <p class="hero-subtitle mb-4">Solusi terpadu cari, kelola, dan sewa kost dengan sistem modern, cepat, dan terpercaya.</p>
                <div class="d-flex flex-wrap gap-3">
                    <div class="stat-pill"><strong>500+</strong> Kost Terdaftar</div>
                    <div class="stat-pill"><strong>2K+</strong> Pengguna Aktif</div>
                    <div class="stat-pill"><strong>98%</strong> Kepuasan</div>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-flex justify-content-end">
                <div style="border:4px solid #FFD600;box-shadow:10px 10px 0 #FFD600;overflow:hidden;width:360px;">
                    <img src="https://images.unsplash.com/photo-1554995207-c18c203602cb?auto=format&fit=crop&w=720&q=80"
                         style="width:100%;height:240px;object-fit:cover;display:block;">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MARQUEE -->
<div class="marquee-wrap">
    <div class="marquee-track">
        <?php $items = ['Cari Kost','Booking Online','Chat Pemilik','Filter Canggih','Manajemen Properti','Verifikasi Pembayaran','Real-Time Notifikasi','Dashboard Modern','Statistik Bisnis','Upload Foto Kost']; ?>
        <?php for ($i=0; $i<4; $i++): foreach($items as $item): ?>
            <span class="marquee-item"><span class="marquee-dot">✦</span> <?= $item ?></span>
        <?php endforeach; endfor; ?>
    </div>
</div>

<!-- FITUR TENANT -->
<div class="feature-section" style="background:transparent;">
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
</div>

<!-- DIVIDER -->
<div style="border-top:4px solid #000;background:#001ee1;padding:24px 0;">
    <div class="container text-center">
        <span style="font-family:'Archivo Black',sans-serif;color:#FFD600;font-size:1.1rem;text-transform:uppercase;letter-spacing:1px;">
            ✦ Semua yang kamu butuhkan ada di sini ✦
        </span>
    </div>
</div>

<!-- FITUR OWNER -->
<div class="feature-section" style="background:transparent;">
    <div class="container">
        <div class="row align-items-center g-5 flex-md-row-reverse">
            <div class="col-md-6">
                <div class="feature-image-wrap">
                    <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=800&q=80" alt="Owner">
                    <span class="img-label" style="background:#FF5C00;color:#fff;">For Owners</span>
                </div>
            </div>
            <div class="col-md-6">
                <span class="section-label" style="background:#FF5C00;color:#fff;">🏠 Pemilik Kost</span>
                <h2 class="feature-heading mb-4">Kelola Bisnis<br>Kostmu —<br>Lebih Mudah</h2>

                <div class="feature-item">
                    <div class="feature-icon-box" style="background:#FF5C00;"><span style="color:#fff;">🏘️</span></div>
                    <div>
                        <h6>Manajemen Properti & Kamar</h6>
                        <p>Kelola data kost, ketersediaan kamar, harga, dan foto dalam satu dashboard intuitif.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon-box" style="background:#FFD600;">🔔</div>
                    <div>
                        <h6>Monitoring Pesanan Masuk</h6>
                        <p>Terima notifikasi pesanan baru dan konfirmasi pembayaran dari penyewa secara real-time.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon-box" style="background:#00E0FF;">📊</div>
                    <div>
                        <h6>Statistik Bisnis</h6>
                        <p>Lihat performa kost melalui data jumlah pesanan dan pendapatan bulanan.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon-box" style="background:#00FF94;">💬</div>
                    <div>
                        <h6>Chat Langsung dengan Penyewa</h6>
                        <p>Balas pesan dan pertanyaan penyewa langsung dari dashboard owner kamu.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FITUR ADMIN -->
<div class="feature-section" style="background:#000;border-top:5px solid #000;border-bottom:5px solid #000;">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-md-6">
                <div class="feature-image-wrap" style="box-shadow:10px 10px 0 #FFD600;border-color:#FFD600;">
                    <img src="https://images.unsplash.com/photo-1551288049-bbda64626744?auto=format&fit=crop&w=800&q=80" alt="Admin">
                    <span class="img-label">For Admin</span>
                </div>
            </div>
            <div class="col-md-6">
                <span class="section-label" style="background:#7B2FFF;color:#fff;">🛡️ Administrator</span>
                <h2 class="feature-heading mb-4" style="color:#FFD600;">Kontrol<br>Penuh<br>Sistem</h2>

                <div class="feature-item" style="background:#111;border-color:#FFD600;box-shadow:4px 4px 0 #FFD600;">
                    <div class="feature-icon-box" style="background:#FFD600;border-color:#FFD600;">✅</div>
                    <div>
                        <h6 style="color:#fff;">Verifikasi Pembayaran</h6>
                        <p style="color:#aaa;">Validasi bukti transfer untuk keamanan transaksi antara penyewa dan pemilik.</p>
                    </div>
                </div>
                <div class="feature-item" style="background:#111;border-color:#FFD600;box-shadow:4px 4px 0 #FFD600;">
                    <div class="feature-icon-box" style="background:#FF3CAC;border-color:#FFD600;"><span style="color:#fff;">👥</span></div>
                    <div>
                        <h6 style="color:#fff;">Manajemen User & Properti</h6>
                        <p style="color:#aaa;">Kontrol penuh terhadap data pengguna, pemblokiran akun, dan moderasi konten.</p>
                    </div>
                </div>
                <div class="feature-item" style="background:#111;border-color:#FFD600;box-shadow:4px 4px 0 #FFD600;">
                    <div class="feature-icon-box" style="background:#00E0FF;border-color:#FFD600;">📋</div>
                    <div>
                        <h6 style="color:#fff;">Laporan Sistem</h6>
                        <p style="color:#aaa;">Laporan komprehensif mengenai aktivitas sistem dan pertumbuhan platform.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TECH GRID -->
<div class="feature-section" style="background:transparent;">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-label" style="background:#7B2FFF;color:#fff;">⚡ Keunggulan Platform</span>
            <h2 class="feature-heading" style="font-size:clamp(1.8rem,4vw,3rem);">Kenapa Pilih<br>E-KOST System?</h2>
        </div>
        <div class="row g-3">
            <?php
            $techs = [
                ['icon'=>'bi-shield-check-fill','color'=>'#FFD600','bg'=>'#FFD600','title'=>'Aman & Terpercaya','desc'=>'Data terenkripsi, transaksi terverifikasi admin'],
                ['icon'=>'bi-lightning-charge-fill','color'=>'#001ee1','bg'=>'#00E0FF','title'=>'Super Cepat','desc'=>'Sistem ringan, responsif di semua perangkat'],
                ['icon'=>'bi-chat-dots-fill','color'=>'#fff','bg'=>'#001ee1','title'=>'Chat Real-Time','desc'=>'Komunikasi langsung penyewa & pemilik'],
                ['icon'=>'bi-whatsapp','color'=>'#fff','bg'=>'#25D366','title'=>'Integrasi WhatsApp','desc'=>'Hubungi pemilik via WA langsung dari platform'],
                ['icon'=>'bi-phone-fill','color'=>'#000','bg'=>'#FF3CAC','title'=>'Mobile Friendly','desc'=>'Tampilan optimal di HP, tablet, dan desktop'],
                ['icon'=>'bi-graph-up-arrow','color'=>'#000','bg'=>'#00FF94','title'=>'Analitik Bisnis','desc'=>'Dashboard statistik untuk pemilik kost'],
            ];
            foreach ($techs as $t): ?>
                <div class="col-6 col-md-4">
                    <div class="tech-card">
                        <div style="width:56px;height:56px;background:<?= $t['bg'] ?>;border:2.5px solid #000;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                            <i class="bi <?= $t['icon'] ?>" style="font-size:1.6rem;color:<?= $t['color'] ?>;"></i>
                        </div>
                        <h6><?= $t['title'] ?></h6>
                        <p><?= $t['desc'] ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>


<!-- React & API Section -->
<div style="background:#000;border-top:5px solid #FFD600;border-bottom:5px solid #FFD600;padding:60px 0;margin:0;">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <span style="display:inline-block;background:#FF3CAC;color:#fff;border:3px solid #FFD600;padding:6px 18px;font-family:'Archivo Black',sans-serif;font-size:.75rem;text-transform:uppercase;letter-spacing:1.5px;box-shadow:4px 4px 0 #FFD600;margin-bottom:20px;">⚛️ React + Axios Integration</span>
                <h2 style="font-family:'Archivo Black',sans-serif;font-size:clamp(2rem,5vw,3.5rem);text-transform:uppercase;letter-spacing:-2px;line-height:1;color:#fff;margin-bottom:16px;">Katalog <span style="color:#FFD600;">Produk</span><br>Live API</h2>
                <p style="font-family:'Space Grotesk',sans-serif;font-weight:600;color:#aaa;font-size:1rem;line-height:1.7;margin-bottom:28px;">Data fetching real-time menggunakan <strong style="color:#FFD600;">React Hooks</strong> (useState, useEffect) dan <strong style="color:#FF3CAC;">Axios</strong> untuk mengambil data dari Public API. Dilengkapi filter, search, sorting, dan pagination interaktif.</p>
                <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:32px;">
                    <?php foreach(['useState','useEffect','Conditional Rendering','Axios GET','Public API','Live Filter','Pagination','Loading State'] as $tag): ?>
                    <span style="background:rgba(255,214,0,.12);color:#FFD600;border:2px solid rgba(255,214,0,.4);padding:4px 12px;font-family:'Space Grotesk',sans-serif;font-weight:700;font-size:.75rem;"><?= $tag ?></span>
                    <?php endforeach; ?>
                </div>
                <a href="kost_api.php" style="display:inline-flex;align-items:center;gap:10px;background:#FFD600;color:#000;border:4px solid #FFD600;box-shadow:6px 6px 0 #FFD600;padding:14px 28px;font-family:'Archivo Black',sans-serif;font-size:.9rem;text-transform:uppercase;letter-spacing:1px;text-decoration:none;transition:all .1s;">
                    <i class="bi bi-arrow-right-circle-fill"></i> Buka Halaman React
                </a>
            </div>
            <div class="col-lg-6">
                <div style="border:4px solid #FFD600;box-shadow:10px 10px 0 #FFD600;background:#111;padding:24px;font-family:'Space Grotesk',monospace;font-size:.82rem;line-height:1.8;">
                    <div style="color:#888;margin-bottom:8px;">// React Hooks Implementation</div>
                    <div><span style="color:#FF3CAC;">const</span> <span style="color:#FFD600;">[products, setProducts]</span> = <span style="color:#00E0FF;">useState</span>([]);</div>
                    <div><span style="color:#FF3CAC;">const</span> <span style="color:#FFD600;">[loading, setLoading]</span> &nbsp;= <span style="color:#00E0FF;">useState</span>(<span style="color:#FF5C00;">true</span>);</div>
                    <div><span style="color:#FF3CAC;">const</span> <span style="color:#FFD600;">[error, setError]</span> &nbsp;&nbsp;&nbsp;= <span style="color:#00E0FF;">useState</span>(<span style="color:#FF5C00;">null</span>);</div>
                    <div style="margin-top:8px;"><span style="color:#00E0FF;">useEffect</span>(() => {</div>
                    <div style="padding-left:16px;"><span style="color:#FF3CAC;">const</span> res = <span style="color:#00FF94;">await</span> <span style="color:#FFD600;">axios</span>.get(</div>
                    <div style="padding-left:32px;color:#00FF94;">'fakestoreapi.com/products'</div>
                    <div style="padding-left:16px;">);</div>
                    <div style="padding-left:16px;"><span style="color:#00E0FF;">setProducts</span>(res.data);</div>
                    <div>}, []);</div>
                    <div style="margin-top:8px;color:#888;">// Conditional Rendering</div>
                    <div><span style="color:#FF3CAC;">if</span> (loading) <span style="color:#00E0FF;">return</span> &lt;<span style="color:#FFD600;">LoadingSpinner</span> /&gt;;</div>
                    <div><span style="color:#FF3CAC;">if</span> (error) &nbsp;<span style="color:#00E0FF;">return</span> &lt;<span style="color:#FFD600;">ErrorBox</span> /&gt;;</div>
                    <div><span style="color:#00E0FF;">return</span> &lt;<span style="color:#FFD600;">ProductGrid</span> data={products} /&gt;;</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CTA -->
<div class="cta-section">
    <div class="container text-center position-relative">
        <span class="section-label" style="background:#000;color:#FFD600;">🚀 Mulai Sekarang</span>
        <h2 class="cta-title mb-2">Siap Gabung<br>E-KOST System?</h2>
        <p class="mb-5" style="font-size:1.1rem;font-weight:600;max-width:480px;margin:0 auto 40px;">Bergabunglah dengan ribuan pengguna aktif dan rasakan kemudahannya!</p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="../modules/auth/register.php?role=user" class="btn cta-btn-main">
                👤 Daftar Jadi Penyewa
            </a>
            <a href="../modules/auth/register.php?role=owner" class="btn cta-btn-outline">
                🏠 Daftar Jadi Pemilik
            </a>
        </div>
    </div>
</div>

<?php include '../layouts/footer.php'; ?>
