<?php
$current_page = basename($_SERVER['PHP_SELF']);
if ($current_page == 'index.php'):
?>
    </main> <!-- End of main-content -->
    <footer class="bg-dark text-light py-5 mt-5" style="border-top: 1px solid rgba(255,255,255,0.05);">
        <div class="container">
            <div class="row g-4 justify-content-between">
                <div class="col-md-5">
                    <h5 class="fw-bold text-white mb-3" style="letter-spacing: 0.5px;"><?php echo htmlspecialchars($app_name); ?></h5>
                    <p class="text-muted small lh-lg" style="max-width: 400px; text-align: justify;">Platform pencarian dan pengelolaan kost terbaik untuk mahasiswa dan pekerja. Cari kost impianmu dengan mudah dan cepat.</p>
                </div>
                <div class="col-md-3 col-6">
                    <h5 class="fw-bold text-white mb-3" style="letter-spacing: 0.5px;">Tautan Cepat</h5>
                    <ul class="list-unstyled d-flex flex-column gap-2 small mb-0">
                        <li><a href="#" class="text-muted text-decoration-none hover-white-transition">Tentang Kami</a></li>
                        <li><a href="#" class="text-muted text-decoration-none hover-white-transition">Bantuan</a></li>
                        <li><a href="#" class="text-muted text-decoration-none hover-white-transition">Syarat & Ketentuan</a></li>
                    </ul>
                </div>
                <div class="col-md-3 col-6">
                    <h5 class="fw-bold text-white mb-3" style="letter-spacing: 0.5px;">Kontak</h5>
                    <p class="text-muted small mb-2"><i class="bi bi-envelope-fill me-2 text-secondary"></i><?php echo htmlspecialchars($support_email); ?></p>
                    <p class="text-muted small mb-0"><i class="bi bi-telephone-fill me-2 text-secondary"></i><?php echo htmlspecialchars($contact_phone); ?></p>
                </div>
            </div>
            <hr class="my-4" style="border-color: rgba(255,255,255,0.1);">
            <div class="text-center text-muted small">
                &copy; <?php echo date('Y'); ?> <span class="text-white fw-semibold"><?php echo htmlspecialchars($app_name); ?></span>. All rights reserved.
            </div>
        </div>
    </footer>
    <style>
        .hover-white-transition {
            transition: color 0.2s ease-in-out;
        }
        .hover-white-transition:hover {
            color: #ffffff !important;
        }
    </style>
<?php else: ?>
            </main> <!-- End of main-content -->
            <footer class="py-3 px-4 border-top bg-white mt-auto">
                <div class="d-flex flex-column flex-md-row align-items-center justify-content-between text-muted small gap-2">
                    <div style="font-weight: 500;">&copy; <?php echo date('Y'); ?> <span class="text-dark fw-semibold"><?php echo htmlspecialchars($app_name); ?></span>. All rights reserved.</div>
                    <div class="d-flex gap-4">
                        <a href="#" class="text-decoration-none text-muted hover-dark-transition">Tentang Kami</a>
                        <a href="#" class="text-decoration-none text-muted hover-dark-transition">Bantuan</a>
                        <a href="#" class="text-decoration-none text-muted hover-dark-transition">Syarat & Ketentuan</a>
                    </div>
                </div>
            </footer>
            <style>
                .hover-dark-transition {
                    transition: color 0.2s ease-in-out;
                    font-weight: 500;
                }
                .hover-dark-transition:hover {
                    color: #212529 !important;
                }
            </style>
        </div> <!-- End of content-wrapper -->
    </div> <!-- End of d-flex -->
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo $base_url; ?>assets/js/main.js"></script>
</body>
</html>
