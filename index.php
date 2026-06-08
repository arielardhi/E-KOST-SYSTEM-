<?php 
require_once 'config/database.php';
include 'layouts/header.php'; 
?>

<!-- HERO SECTION -->
<section class="hero-elegant d-flex align-items-center">
    <div class="container position-relative">
        <div class="row align-items-center gy-5">
            <div class="col-lg-6 text-center text-lg-start">
                <div class="hero-badge-el">
                    <i class="bi bi-patch-check-fill"></i> Platform Kost Terpercaya #1 di Indonesia
                </div>
                <h1 class="hero-title-el mb-3">
                    Cari & Kelola<br>Kost Jadi Lebih<br><span>Sederhana</span>
                </h1>
                <p class="hero-subtitle-el mb-4 mx-auto mx-lg-0">
                    Solusi modern untuk menemukan kost impian kamu secara instan, serta mengelola properti kost secara profesional dalam satu dashboard pintar.
                </p>
                <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-3 mb-4">
                    <a href="pages/kost_list.php" class="btn btn-primary btn-lg px-4 py-2 text-white"><i class="bi bi-search me-2"></i>Cari Kost Sekarang</a>
                    <a href="modules/auth/register.php?role=owner" class="btn btn-outline-light btn-lg px-4 py-2"><i class="bi bi-house-add me-2"></i>Mulai Pasang Kost</a>
                </div>
                <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-4">
                    <div class="hero-stat-pill">
                        <strong>500+</strong> Properti Kost
                    </div>
                    <div class="hero-stat-pill">
                        <strong>2,000+</strong> Penyewa Aktif
                    </div>
                    <div class="hero-stat-pill">
                        <strong>99%</strong> Transaksi Aman
                    </div>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <div class="hero-image-container">
                    <img src="https://images.unsplash.com/photo-1554995207-c18c203602cb?auto=format&fit=crop&w=800&q=80"
                         style="width: 100%; height: 420px; object-fit: cover; display: block;" alt="Modern Bedroom">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MARQUEE LANDING -->
<div class="marquee-wrap-el" aria-hidden="true">
    <div class="marquee-track-el">
        <?php $items = ['Pencarian Kost Mudah', 'Booking Online Instan', 'Pembayaran Aman', 'Chat dengan Pemilik', 'Manajemen Kamar Cerdas', 'Laporan Keuangan Realtime', 'Statistik Bisnis Owner', 'Filter Fasilitas Lengkap']; ?>
        <?php for ($i=0; $i<4; $i++): foreach($items as $item): ?>
            <span class="marquee-item-el"><i class="bi bi-star-fill text-indigo"></i> <?= $item ?></span>
        <?php endforeach; endfor; ?>
    </div>
</div>

<!-- FEATURE TENANTS SECTION -->
<section class="feature-section-el">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="position-relative" style="border-radius: 16px; overflow: hidden; box-shadow: var(--box-shadow-hover);">
                    <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=800&q=80" alt="Tenant Room" style="width:100%; height: 380px; object-fit: cover;">
                </div>
            </div>
            <div class="col-lg-6">
                <span class="badge bg-primary mb-3">👤 Pencari Kost</span>
                <h2 class="mb-4">Dapatkan Kost Terbaik Tanpa Ribet</h2>
                <p class="mb-4">Kami mempermudah proses pencarian dan pemesanan kost dengan sistem terintegrasi yang transparan dan aman.</p>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="feature-card-el h-100">
                            <div class="feature-icon-el">🔍</div>
                            <div>
                                <h6>Filter Cerdas</h6>
                                <p>Temukan kost berdasarkan kota, tipe kost, harga, dan ketersediaan.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-card-el h-100">
                            <div class="feature-icon-el">📅</div>
                            <div>
                                <h6>Booking Instan</h6>
                                <p>Pesan kamar kost impian secara online, langsung dari platform kami.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-card-el h-100">
                            <div class="feature-icon-el">💳</div>
                            <div>
                                <h6>Sistem Pembayaran</h6>
                                <p>Metode pembayaran digital dengan proses verifikasi yang cepat.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-card-el h-100">
                            <div class="feature-icon-el">💬</div>
                            <div>
                                <h6>Hubungi Pemilik</h6>
                                <p>Fitur live-chat langsung dengan pemilik kost dari aplikasi.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MID DIVIDER BANNER -->
<section class="py-4 border-top border-bottom bg-light">
    <div class="container text-center">
        <span class="fw-bold text-muted small text-uppercase tracking-wider">
            ✦ Efisiensi, Keamanan, dan Kenyamanan Dalam Satu Platform Terintegrasi ✦
        </span>
    </div>
</section>

<!-- FEATURE OWNERS SECTION (DARK BACKGROUND) -->
<section class="feature-section-el dark-mode">
    <div class="container">
        <div class="row align-items-center g-5 flex-lg-row-reverse">
            <div class="col-lg-6">
                <div class="position-relative" style="border-radius: 16px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.3);">
                    <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=800&q=80" alt="Owner Dashboard Mockup" style="width:100%; height: 380px; object-fit: cover;">
                </div>
            </div>
            <div class="col-lg-6">
                <span class="badge bg-warning mb-3">🏘️ Pemilik Kost</span>
                <h2 class="mb-4">Kelola Bisnis Kost Secara Profesional</h2>
                <p class="mb-4 text-light-muted">Maksimalkan pendapatan bisnis kost Anda dengan tools manajemen properti yang lengkap, mudah, dan akurat.</p>
                
                <div class="row g-3">
                    <div class="col-12">
                        <div class="feature-card-el">
                            <div class="feature-icon-el">📊</div>
                            <div>
                                <h6>Dashboard Bisnis & Keuangan</h6>
                                <p>Pantau laporan pemasukan, status okupansi kamar, dan transaksi booking secara real-time.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="feature-card-el">
                            <div class="feature-icon-el">🚪</div>
                            <div>
                                <h6>Manajemen Properti & Kamar</h6>
                                <p>Atur ketersediaan kamar, spesifikasi harga, serta kelola foto properti secara fleksibel.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="feature-card-el">
                            <div class="feature-icon-el">✅</div>
                            <div>
                                <h6>Verifikasi Pembayaran Mudah</h6>
                                <p>Terima pengajuan booking penyewa setelah memvalidasi bukti pembayaran secara sistematis.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CALL TO ACTION (CTA) -->
<section class="cta-elegant text-white text-center">
    <div class="container position-relative py-4">
        <h2 class="mb-3">Temukan Kamar Terbaikmu Hari Ini</h2>
        <p class="mb-4 opacity-75 max-width-600 mx-auto">Gabung bersama ribuan mahasiswa dan pekerja yang telah menemukan hunian idaman mereka dengan mudah.</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="modules/auth/register.php" class="btn btn-light btn-lg px-4 text-primary fw-bold">Daftar Sekarang</a>
            <a href="modules/auth/login.php" class="btn btn-outline-light btn-lg px-4">Masuk Akun</a>
        </div>
    </div>
</section>

<?php include 'layouts/footer.php'; ?>
