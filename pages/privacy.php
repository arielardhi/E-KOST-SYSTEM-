<?php include '../config/database.php'; include '../layouts/header.php'; ?>

<style>
.policy-container { max-width: 900px; margin: 0 auto; }
.toc-sidebar { position: sticky; top: 100px; }
.toc-link { display:block;padding:8px 14px;border-radius:8px;font-size:.875rem;font-weight:600;color:var(--text-muted);text-decoration:none;border-left:3px solid transparent;transition:all .15s; }
.toc-link:hover, .toc-link.active { color:var(--primary);border-left-color:var(--primary);background:rgba(0,180,186,.06); }
.policy-section { scroll-margin-top: 100px; }
.policy-section h3 { font-size: 1.2rem; font-weight: 800; color: var(--dark); margin-bottom: 12px; padding-bottom: 8px; border-bottom: 2px solid var(--border-color); }
.policy-list li { padding: 6px 0; color: var(--text-muted); line-height: 1.7; }
.policy-list li::marker { color: var(--primary); }
.policy-hero { background: linear-gradient(135deg, #1E0D3E, #2D1459); border-radius: 16px; padding: 36px 40px; margin-bottom: 36px; position: relative; overflow: hidden; }
.policy-hero::after { content:''; position:absolute;inset:0;background:radial-gradient(circle at 80% 50%,rgba(0,201,208,.2) 0%,transparent 55%);pointer-events:none; }
.last-updated { display:inline-flex;align-items:center;gap:6px;background:rgba(0,201,208,.15);color:#00C9D0;font-size:.8rem;font-weight:700;padding:6px 14px;border-radius:99px;border:1px solid rgba(0,201,208,.25); }
</style>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php"><i class="bi bi-house me-1"></i>Home</a></li>
            <li class="breadcrumb-item active">Kebijakan Privasi</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- TOC Sidebar -->
        <div class="col-lg-3 d-none d-lg-block">
            <div class="toc-sidebar">
                <div class="card p-3">
                    <div class="fw-700 mb-3" style="color:var(--dark);font-size:.85rem;text-transform:uppercase;letter-spacing:.05em"><i class="bi bi-list-ul me-2" style="color:var(--primary)"></i>Daftar Isi</div>
                    <a href="#section-intro"       class="toc-link">1. Pendahuluan</a>
                    <a href="#section-data"        class="toc-link">2. Data yang Dikumpulkan</a>
                    <a href="#section-usage"       class="toc-link">3. Penggunaan Data</a>
                    <a href="#section-sharing"     class="toc-link">4. Berbagi Data</a>
                    <a href="#section-security"    class="toc-link">5. Keamanan Data</a>
                    <a href="#section-cookies"     class="toc-link">6. Cookies</a>
                    <a href="#section-rights"      class="toc-link">7. Hak Pengguna</a>
                    <a href="#section-children"    class="toc-link">8. Privasi Anak</a>
                    <a href="#section-changes"     class="toc-link">9. Perubahan Kebijakan</a>
                    <a href="#section-contact"     class="toc-link">10. Kontak</a>
                </div>
                <div class="card mt-3 p-3" style="border-top:3px solid var(--primary)">
                    <div class="text-muted small mb-2 fw-600">Halaman Terkait</div>
                    <a href="terms.php" class="btn btn-outline-primary btn-sm w-100 mb-2"><i class="bi bi-file-text me-2"></i>Syarat & Ketentuan</a>
                    <a href="contact.php" class="btn btn-outline-secondary btn-sm w-100"><i class="bi bi-envelope me-2"></i>Hubungi Kami</a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="policy-hero">
                <div class="position-relative" style="z-index:1">
                    <span class="last-updated mb-3 d-inline-flex"><i class="bi bi-calendar3"></i>Terakhir diperbarui: <?= date('d F Y') ?></span>
                    <h1 class="text-white fw-800 mb-2" style="font-size:2rem;letter-spacing:-.03em">Kebijakan Privasi</h1>
                    <p style="color:rgba(255,255,255,.75);font-size:.95rem;margin:0;max-width:500px">E-KOST SYSTEM berkomitmen untuk melindungi privasi dan data pribadi Anda. Baca kebijakan ini dengan seksama.</p>
                </div>
            </div>

            <div class="d-flex flex-column gap-4 policy-container">
                <div class="card p-4 policy-section" id="section-intro">
                    <h3><i class="bi bi-info-circle-fill me-2" style="color:var(--primary)"></i>1. Pendahuluan</h3>
                    <p style="color:var(--text-muted);line-height:1.8">Kebijakan Privasi ini menjelaskan bagaimana E-KOST SYSTEM ("kami", "kita") mengumpulkan, menggunakan, dan melindungi informasi yang Anda berikan saat menggunakan layanan kami. Dengan menggunakan platform E-KOST SYSTEM, Anda menyetujui praktik yang dijelaskan dalam kebijakan ini.</p>
                    <div class="mt-3 p-3 rounded-3" style="background:rgba(0,180,186,.06);border:1px solid rgba(0,180,186,.15)">
                        <p class="mb-0 fw-600" style="color:var(--primary);font-size:.9rem"><i class="bi bi-shield-check-fill me-2"></i>Kami menghargai kepercayaan Anda dan berkomitmen untuk menjaga kerahasiaan data pribadi Anda sesuai peraturan perundang-undangan yang berlaku.</p>
                    </div>
                </div>

                <div class="card p-4 policy-section" id="section-data">
                    <h3><i class="bi bi-database-fill me-2" style="color:#D97706"></i>2. Data yang Dikumpulkan</h3>
                    <p style="color:var(--text-muted)">Kami mengumpulkan beberapa jenis informasi untuk menyediakan dan meningkatkan layanan kami:</p>
                    <ul class="policy-list ps-4">
                        <li><strong>Informasi Akun:</strong> Nama, username, alamat email, dan kata sandi terenkripsi</li>
                        <li><strong>Informasi Profil:</strong> Nama lengkap, nomor telepon, dan foto profil (opsional)</li>
                        <li><strong>Data Transaksi:</strong> Riwayat pemesanan kost, status pembayaran, dan detail booking</li>
                        <li><strong>Data Komunikasi:</strong> Pesan yang dikirim melalui fitur chat platform</li>
                        <li><strong>Data Teknis:</strong> Alamat IP, jenis browser, sistem operasi, dan log aktivitas</li>
                        <li><strong>Data Lokasi:</strong> Kota pencarian dan preferensi lokasi kost</li>
                    </ul>
                </div>

                <div class="card p-4 policy-section" id="section-usage">
                    <h3><i class="bi bi-gear-fill me-2" style="color:#059669"></i>3. Penggunaan Data</h3>
                    <p style="color:var(--text-muted)">Data yang kami kumpulkan digunakan untuk:</p>
                    <ul class="policy-list ps-4">
                        <li>Menyediakan, mengoperasikan, dan memelihara layanan platform</li>
                        <li>Memproses transaksi pemesanan dan pembayaran kost</li>
                        <li>Mengirim notifikasi terkait aktivitas akun dan pemesanan</li>
                        <li>Meningkatkan pengalaman pengguna dan fitur platform</li>
                        <li>Mendeteksi dan mencegah penipuan atau penyalahgunaan layanan</li>
                        <li>Mematuhi kewajiban hukum yang berlaku</li>
                        <li>Mengirim informasi promosi (hanya dengan persetujuan Anda)</li>
                    </ul>
                </div>

                <div class="card p-4 policy-section" id="section-sharing">
                    <h3><i class="bi bi-share-fill me-2" style="color:#5C4D78"></i>4. Berbagi Data</h3>
                    <p style="color:var(--text-muted)">Kami tidak menjual data pribadi Anda. Data mungkin dibagikan dalam situasi berikut:</p>
                    <ul class="policy-list ps-4">
                        <li><strong>Pemilik Kost:</strong> Informasi penyewa yang diperlukan untuk proses booking</li>
                        <li><strong>Penyedia Layanan:</strong> Mitra teknis yang membantu operasional platform</li>
                        <li><strong>Kewajiban Hukum:</strong> Jika diwajibkan oleh hukum atau otoritas berwenang</li>
                        <li><strong>Perlindungan Hak:</strong> Untuk melindungi hak dan keamanan pengguna lain</li>
                    </ul>
                    <div class="mt-3 p-3 rounded-3" style="background:rgba(5,150,105,.06);border:1px solid rgba(5,150,105,.15)">
                        <p class="mb-0 small fw-600" style="color:#059669"><i class="bi bi-check-circle-fill me-2"></i>Kami tidak akan menjual, memperdagangkan, atau menyewakan data pribadi Anda kepada pihak ketiga tanpa persetujuan eksplisit Anda.</p>
                    </div>
                </div>

                <div class="card p-4 policy-section" id="section-security">
                    <h3><i class="bi bi-shield-lock-fill me-2" style="color:#DC2626"></i>5. Keamanan Data</h3>
                    <p style="color:var(--text-muted)">Kami mengimplementasikan langkah-langkah keamanan yang tepat untuk melindungi data Anda:</p>
                    <div class="row g-3">
                        <?php foreach ([
                            ['bi-lock-fill','Enkripsi Password','Kata sandi disimpan menggunakan enkripsi bcrypt yang kuat','#DC2626'],
                            ['bi-shield-check-fill','HTTPS','Semua komunikasi dienkripsi menggunakan SSL/TLS','#059669'],
                            ['bi-person-badge-fill','Akses Terbatas','Akses data dibatasi hanya untuk personel yang berwenang','#D97706'],
                            ['bi-arrow-repeat','Backup Rutin','Data dicadangkan secara berkala untuk mencegah kehilangan','#00B4BA'],
                        ] as [$icon,$title,$desc,$color]): ?>
                        <div class="col-sm-6">
                            <div class="d-flex gap-2 p-3 rounded-3" style="background:rgba(30,13,62,.04);border:1px solid var(--border-color)">
                                <i class="bi <?=$icon?>" style="color:<?=$color?>;font-size:1.2rem;flex-shrink:0;margin-top:2px"></i>
                                <div><div class="fw-700 small"><?=$title?></div><div class="text-muted" style="font-size:.78rem"><?=$desc?></div></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="card p-4 policy-section" id="section-cookies">
                    <h3><i class="bi bi-cookie me-2" style="color:#D97706"></i>6. Cookies</h3>
                    <p style="color:var(--text-muted);line-height:1.8">Kami menggunakan cookies untuk meningkatkan pengalaman Anda. Cookies adalah file kecil yang disimpan di perangkat Anda untuk mengingat preferensi dan menjaga sesi login Anda tetap aktif. Anda dapat mengatur browser untuk menolak cookies, namun hal ini mungkin mempengaruhi fungsi platform.</p>
                </div>

                <div class="card p-4 policy-section" id="section-rights">
                    <h3><i class="bi bi-person-check-fill me-2" style="color:var(--primary)"></i>7. Hak Pengguna</h3>
                    <p style="color:var(--text-muted)">Anda memiliki hak atas data pribadi Anda, termasuk:</p>
                    <ul class="policy-list ps-4">
                        <li><strong>Hak Akses:</strong> Mendapatkan salinan data pribadi yang kami miliki</li>
                        <li><strong>Hak Koreksi:</strong> Memperbarui atau memperbaiki data yang tidak akurat</li>
                        <li><strong>Hak Penghapusan:</strong> Meminta penghapusan data pribadi Anda</li>
                        <li><strong>Hak Portabilitas:</strong> Mendapatkan data dalam format yang dapat dibaca mesin</li>
                        <li><strong>Hak Keberatan:</strong> Menolak pemrosesan data untuk tujuan tertentu</li>
                    </ul>
                </div>

                <div class="card p-4 policy-section" id="section-children">
                    <h3><i class="bi bi-people-fill me-2" style="color:#5C4D78"></i>8. Privasi Anak</h3>
                    <p style="color:var(--text-muted);line-height:1.8">Layanan kami tidak ditujukan untuk anak-anak di bawah usia 17 tahun. Kami tidak secara sengaja mengumpulkan data pribadi dari anak-anak. Jika Anda menemukan bahwa anak Anda telah memberikan data kepada kami, silakan hubungi kami untuk penghapusan data tersebut.</p>
                </div>

                <div class="card p-4 policy-section" id="section-changes">
                    <h3><i class="bi bi-arrow-repeat me-2" style="color:#D97706"></i>9. Perubahan Kebijakan</h3>
                    <p style="color:var(--text-muted);line-height:1.8">Kami dapat memperbarui Kebijakan Privasi ini dari waktu ke waktu. Perubahan signifikan akan diinformasikan melalui notifikasi di platform atau email. Penggunaan Anda yang berkelanjutan atas layanan setelah perubahan dianggap sebagai penerimaan atas kebijakan yang diperbarui.</p>
                </div>

                <div class="card p-4 policy-section" id="section-contact">
                    <h3><i class="bi bi-envelope-fill me-2" style="color:var(--primary)"></i>10. Kontak</h3>
                    <p style="color:var(--text-muted)">Untuk pertanyaan atau permintaan terkait privasi data Anda, hubungi kami:</p>
                    <div class="d-flex flex-column gap-2 mt-3">
                        <div class="d-flex align-items-center gap-2"><i class="bi bi-envelope-fill" style="color:var(--primary)"></i><span style="color:var(--text-muted)">support@ekost.com</span></div>
                        <div class="d-flex align-items-center gap-2"><i class="bi bi-telephone-fill" style="color:var(--primary)"></i><span style="color:var(--text-muted)">+62 812 3456 7890</span></div>
                        <div class="d-flex align-items-center gap-2"><i class="bi bi-geo-alt-fill" style="color:var(--primary)"></i><span style="color:var(--text-muted)">Jakarta, Indonesia</span></div>
                    </div>
                    <div class="mt-3"><a href="contact.php" class="btn btn-primary"><i class="bi bi-envelope me-2"></i>Kirim Pesan</a></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Highlight active TOC link on scroll
const sections = document.querySelectorAll('.policy-section');
const tocLinks  = document.querySelectorAll('.toc-link');
window.addEventListener('scroll', () => {
    let current = '';
    sections.forEach(s => { if (window.scrollY >= s.offsetTop - 120) current = s.id; });
    tocLinks.forEach(l => {
        l.classList.remove('active');
        if (l.getAttribute('href') === '#' + current) l.classList.add('active');
    });
});
</script>

<?php include '../layouts/footer.php'; ?>
