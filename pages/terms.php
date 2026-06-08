<?php include '../config/database.php'; include '../layouts/header.php'; ?>

<style>
.policy-container { max-width: 900px; margin: 0 auto; }
.toc-sidebar { position: sticky; top: 100px; }
.toc-link { display:block;padding:8px 14px;border-radius:8px;font-size:.875rem;font-weight:600;color:var(--text-muted);text-decoration:none;border-left:3px solid transparent;transition:all .15s; }
.toc-link:hover, .toc-link.active { color:var(--primary);border-left-color:var(--primary);background:rgba(0,180,186,.06); }
.policy-section { scroll-margin-top: 100px; }
.policy-section h3 { font-size:1.2rem;font-weight:800;color:var(--dark);margin-bottom:12px;padding-bottom:8px;border-bottom:2px solid var(--border-color); }
.policy-list li { padding:6px 0;color:var(--text-muted);line-height:1.7; }
.policy-list li::marker { color:var(--primary); }
.policy-hero { background:linear-gradient(135deg,#1E0D3E,#2D1459);border-radius:16px;padding:36px 40px;margin-bottom:36px;position:relative;overflow:hidden; }
.policy-hero::after { content:'';position:absolute;inset:0;background:radial-gradient(circle at 20% 50%,rgba(232,69,69,.2) 0%,transparent 55%);pointer-events:none; }
.last-updated { display:inline-flex;align-items:center;gap:6px;background:rgba(232,69,69,.15);color:#FF7070;font-size:.8rem;font-weight:700;padding:6px 14px;border-radius:99px;border:1px solid rgba(232,69,69,.25); }
</style>

<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php"><i class="bi bi-house me-1"></i>Home</a></li>
            <li class="breadcrumb-item active">Syarat & Ketentuan</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-3 d-none d-lg-block">
            <div class="toc-sidebar">
                <div class="card p-3">
                    <div class="fw-700 mb-3" style="color:var(--dark);font-size:.85rem;text-transform:uppercase;letter-spacing:.05em"><i class="bi bi-list-ul me-2" style="color:var(--primary)"></i>Daftar Isi</div>
                    <a href="#s-umum"           class="toc-link">1. Ketentuan Umum</a>
                    <a href="#s-akun"           class="toc-link">2. Akun Pengguna</a>
                    <a href="#s-layanan"        class="toc-link">3. Layanan Platform</a>
                    <a href="#s-booking"        class="toc-link">4. Pemesanan & Pembayaran</a>
                    <a href="#s-kewajiban"      class="toc-link">5. Kewajiban Pengguna</a>
                    <a href="#s-larangan"       class="toc-link">6. Larangan</a>
                    <a href="#s-hak-platform"   class="toc-link">7. Hak Platform</a>
                    <a href="#s-pembatalan"     class="toc-link">8. Pembatalan & Refund</a>
                    <a href="#s-tanggung-jawab" class="toc-link">9. Batasan Tanggung Jawab</a>
                    <a href="#s-hukum"          class="toc-link">10. Hukum yang Berlaku</a>
                </div>
                <div class="card mt-3 p-3" style="border-top:3px solid var(--primary)">
                    <div class="text-muted small mb-2 fw-600">Halaman Terkait</div>
                    <a href="privacy.php" class="btn btn-outline-primary btn-sm w-100 mb-2"><i class="bi bi-shield-lock me-2"></i>Kebijakan Privasi</a>
                    <a href="contact.php" class="btn btn-outline-secondary btn-sm w-100"><i class="bi bi-envelope me-2"></i>Hubungi Kami</a>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="policy-hero">
                <div class="position-relative" style="z-index:1">
                    <span class="last-updated mb-3 d-inline-flex"><i class="bi bi-calendar3"></i>Terakhir diperbarui: <?= date('d F Y') ?></span>
                    <h1 class="text-white fw-800 mb-2" style="font-size:2rem;letter-spacing:-.03em">Syarat & Ketentuan</h1>
                    <p style="color:rgba(255,255,255,.75);font-size:.95rem;margin:0;max-width:500px">Harap baca syarat dan ketentuan penggunaan platform E-KOST SYSTEM dengan cermat sebelum menggunakan layanan kami.</p>
                </div>
            </div>

            <div class="d-flex flex-column gap-4 policy-container">
                <div class="card p-4 policy-section" id="s-umum">
                    <h3><i class="bi bi-file-text-fill me-2" style="color:var(--primary)"></i>1. Ketentuan Umum</h3>
                    <p style="color:var(--text-muted);line-height:1.8">Dengan mengakses atau menggunakan platform E-KOST SYSTEM, Anda menyatakan telah membaca, memahami, dan menyetujui Syarat & Ketentuan ini. Jika Anda tidak menyetujui, mohon untuk tidak menggunakan layanan kami. Syarat & Ketentuan ini berlaku bagi semua pengguna, termasuk penyewa, pemilik kost, dan administrator.</p>
                    <div class="mt-3 p-3 rounded-3" style="background:rgba(0,180,186,.06);border:1px solid rgba(0,180,186,.15)">
                        <p class="mb-0 fw-600 small" style="color:var(--primary)"><i class="bi bi-info-circle-fill me-2"></i>Kami berhak mengubah syarat dan ketentuan ini kapan saja. Perubahan akan berlaku setelah dipublikasikan di platform.</p>
                    </div>
                </div>

                <div class="card p-4 policy-section" id="s-akun">
                    <h3><i class="bi bi-person-circle me-2" style="color:#D97706"></i>2. Akun Pengguna</h3>
                    <ul class="policy-list ps-4">
                        <li>Pengguna harus berusia minimal 17 tahun untuk membuat akun</li>
                        <li>Anda bertanggung jawab atas keamanan dan kerahasiaan kata sandi akun Anda</li>
                        <li>Setiap akun hanya boleh digunakan oleh satu orang; berbagi akun tidak diperbolehkan</li>
                        <li>Informasi yang diberikan saat registrasi harus akurat dan terkini</li>
                        <li>Kami berhak menangguhkan atau menghapus akun yang melanggar ketentuan</li>
                    </ul>
                </div>

                <div class="card p-4 policy-section" id="s-layanan">
                    <h3><i class="bi bi-grid-fill me-2" style="color:#059669"></i>3. Layanan Platform</h3>
                    <p style="color:var(--text-muted)">E-KOST SYSTEM menyediakan:</p>
                    <ul class="policy-list ps-4">
                        <li>Platform pencarian dan pemesanan kost online</li>
                        <li>Sistem manajemen untuk pemilik kost</li>
                        <li>Fitur komunikasi antara penyewa dan pemilik kost</li>
                        <li>Sistem pembayaran dan verifikasi booking</li>
                        <li>Layanan ulasan dan rating kost</li>
                    </ul>
                    <p style="color:var(--text-muted);margin-top:12px">Kami bertindak sebagai perantara dan tidak bertanggung jawab atas kondisi fisik kost atau perselisihan antara penyewa dan pemilik.</p>
                </div>

                <div class="card p-4 policy-section" id="s-booking">
                    <h3><i class="bi bi-calendar-check-fill me-2" style="color:#DC2626"></i>4. Pemesanan & Pembayaran</h3>
                    <ul class="policy-list ps-4">
                        <li>Booking dianggap sah setelah dikonfirmasi oleh pemilik kost</li>
                        <li>Pembayaran harus dilakukan sesuai metode yang tersedia di platform</li>
                        <li>Bukti pembayaran harus diunggah dalam format yang jelas dan terbaca</li>
                        <li>Verifikasi pembayaran dilakukan oleh admin platform dalam 1x24 jam kerja</li>
                        <li>Harga yang tertera adalah harga per bulan belum termasuk biaya tambahan lainnya</li>
                        <li>Kami tidak bertanggung jawab atas transaksi di luar platform</li>
                    </ul>
                </div>

                <div class="card p-4 policy-section" id="s-kewajiban">
                    <h3><i class="bi bi-check-square-fill me-2" style="color:var(--primary)"></i>5. Kewajiban Pengguna</h3>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 h-100" style="background:rgba(0,180,186,.06);border:1px solid rgba(0,180,186,.15)">
                                <div class="fw-700 mb-2" style="color:var(--primary)"><i class="bi bi-person-fill me-1"></i>Penyewa</div>
                                <ul class="policy-list ps-3 mb-0" style="font-size:.875rem">
                                    <li>Memberikan informasi yang jujur dan akurat</li>
                                    <li>Menghormati peraturan kost yang berlaku</li>
                                    <li>Melakukan pembayaran sesuai kesepakatan</li>
                                    <li>Menjaga kebersihan dan ketertiban</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 h-100" style="background:rgba(5,150,105,.06);border:1px solid rgba(5,150,105,.15)">
                                <div class="fw-700 mb-2" style="color:#059669"><i class="bi bi-house-fill me-1"></i>Pemilik Kost</div>
                                <ul class="policy-list ps-3 mb-0" style="font-size:.875rem">
                                    <li>Menyediakan informasi kost yang akurat</li>
                                    <li>Memastikan kondisi kamar sesuai iklan</li>
                                    <li>Merespons booking dalam waktu 24 jam</li>
                                    <li>Memberikan pelayanan yang baik kepada penyewa</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card p-4 policy-section" id="s-larangan">
                    <h3><i class="bi bi-slash-circle-fill me-2" style="color:#DC2626"></i>6. Larangan</h3>
                    <p style="color:var(--text-muted)">Pengguna dilarang untuk:</p>
                    <ul class="policy-list ps-4">
                        <li>Melakukan transaksi palsu atau penipuan melalui platform</li>
                        <li>Menggunakan platform untuk kegiatan ilegal</li>
                        <li>Menyebarkan konten yang menyinggung, mengancam, atau SARA</li>
                        <li>Mencoba mengakses sistem atau data pengguna lain tanpa izin</li>
                        <li>Membuat akun palsu atau menggunakan identitas orang lain</li>
                        <li>Memberikan ulasan palsu atau memanipulasi sistem rating</li>
                    </ul>
                </div>

                <div class="card p-4 policy-section" id="s-hak-platform">
                    <h3><i class="bi bi-shield-fill me-2" style="color:#5C4D78"></i>7. Hak Platform</h3>
                    <p style="color:var(--text-muted)">E-KOST SYSTEM berhak untuk:</p>
                    <ul class="policy-list ps-4">
                        <li>Menangguhkan atau menonaktifkan akun yang melanggar ketentuan</li>
                        <li>Menghapus konten yang tidak sesuai dengan kebijakan</li>
                        <li>Memperbarui, memodifikasi, atau menghentikan layanan kapan saja</li>
                        <li>Membagikan informasi kepada pihak berwenang jika diperlukan</li>
                        <li>Menolak pendaftaran atau pemesanan tanpa harus memberikan alasan</li>
                    </ul>
                </div>

                <div class="card p-4 policy-section" id="s-pembatalan">
                    <h3><i class="bi bi-arrow-counterclockwise me-2" style="color:#D97706"></i>8. Pembatalan & Refund</h3>
                    <ul class="policy-list ps-4">
                        <li>Pembatalan booking harus dilakukan minimal 3 hari sebelum tanggal check-in</li>
                        <li>Refund diproses dalam 5-7 hari kerja setelah pembatalan disetujui</li>
                        <li>Pembatalan setelah 24 jam sebelum check-in tidak mendapat refund</li>
                        <li>Biaya platform sebesar 5% tidak dapat dikembalikan</li>
                        <li>Kebijakan refund pemilik kost dapat berbeda dan harus dikonfirmasi langsung</li>
                    </ul>
                </div>

                <div class="card p-4 policy-section" id="s-tanggung-jawab">
                    <h3><i class="bi bi-exclamation-triangle-fill me-2" style="color:#DC2626"></i>9. Batasan Tanggung Jawab</h3>
                    <p style="color:var(--text-muted);line-height:1.8">E-KOST SYSTEM tidak bertanggung jawab atas kerugian tidak langsung, insidental, atau konsekuensial yang timbul dari penggunaan layanan. Platform tidak menjamin keakuratan informasi yang diberikan oleh pemilik kost. Kami tidak bertanggung jawab atas perselisihan antara penyewa dan pemilik kost.</p>
                </div>

                <div class="card p-4 policy-section" id="s-hukum">
                    <h3><i class="bi bi-bank2 me-2" style="color:var(--primary)"></i>10. Hukum yang Berlaku</h3>
                    <p style="color:var(--text-muted);line-height:1.8">Syarat & Ketentuan ini diatur dan diinterpretasikan sesuai dengan hukum Republik Indonesia. Setiap perselisihan yang timbul akan diselesaikan melalui musyawarah mufakat. Jika tidak tercapai kesepakatan, penyelesaian dilakukan melalui Pengadilan Negeri Jakarta Pusat.</p>
                    <div class="d-flex gap-2 mt-3 flex-wrap">
                        <a href="privacy.php" class="btn btn-outline-primary btn-sm"><i class="bi bi-shield-lock me-2"></i>Kebijakan Privasi</a>
                        <a href="contact.php" class="btn btn-primary btn-sm"><i class="bi bi-envelope me-2"></i>Hubungi Kami</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const sections2 = document.querySelectorAll('.policy-section');
const tocLinks2 = document.querySelectorAll('.toc-link');
window.addEventListener('scroll', () => {
    let current = '';
    sections2.forEach(s => { if (window.scrollY >= s.offsetTop - 120) current = s.id; });
    tocLinks2.forEach(l => {
        l.classList.remove('active');
        if (l.getAttribute('href') === '#' + current) l.classList.add('active');
    });
});
</script>

<?php include '../layouts/footer.php'; ?>
