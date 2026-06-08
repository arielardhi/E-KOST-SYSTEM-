-- ══════════════════════════════════════════════════════════════
-- E-KOST DATABASE — Tabel Barang Kebutuhan Kos
-- Jalankan SETELAH database.sql dan database_extended.sql
-- ══════════════════════════════════════════════════════════════

USE e_kost_db;

-- ── Tabel: barang_kategori (Kategori Barang) ─────────────────
CREATE TABLE IF NOT EXISTS barang_kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL UNIQUE,
    deskripsi TEXT,
    icon VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ── Tabel: barang (Barang Kebutuhan Kos) ─────────────────────
CREATE TABLE IF NOT EXISTS barang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kategori_id INT NOT NULL,
    nama_barang VARCHAR(150) NOT NULL,
    deskripsi TEXT,
    harga DECIMAL(12, 2) NOT NULL,
    stok INT DEFAULT 0,
    kondisi ENUM('Baru', 'Bekas - Sangat Baik', 'Bekas - Baik', 'Bekas - Cukup Baik') DEFAULT 'Baru',
    seller_id INT,
    kota VARCHAR(50),
    alamat TEXT,
    foto_utama VARCHAR(255),
    rating DECIMAL(3, 2),
    jumlah_review INT DEFAULT 0,
    status ENUM('tersedia', 'terjual', 'dihapus') DEFAULT 'tersedia',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES barang_kategori(id) ON DELETE CASCADE,
    FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ── Tabel: barang_foto (Foto Barang) ────────────────────────
CREATE TABLE IF NOT EXISTS barang_foto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    barang_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    is_main BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (barang_id) REFERENCES barang(id) ON DELETE CASCADE
);

-- ── Tabel: barang_review (Review Barang) ────────────────────
CREATE TABLE IF NOT EXISTS barang_review (
    id INT AUTO_INCREMENT PRIMARY KEY,
    barang_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (barang_id) REFERENCES barang(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ══════════════════════════════════════════════════════════════
-- DATA KATEGORI BARANG
-- ══════════════════════════════════════════════════════════════

INSERT INTO barang_kategori (nama_kategori, deskripsi, icon) VALUES
('Tempat Tidur', 'Kasur, Ranjang, Spring Bed, dan perlengkapan tidur lainnya', '🛏️'),
('Lemari & Penyimpanan', 'Lemari, Rak, Laci, dan penyimpanan barang', '🗄️'),
('Meja & Kursi', 'Meja belajar, Meja makan, Kursi, dan furniture duduk', '🪑'),
('Peralatan Dapur', 'Kompor, Panci, Wajan, Peralatan masak-memasak', '🍳'),
('Elektronik', 'Kulkas, Microwave, Kipas Angin, AC, Pemanas Air', '⚡'),
('Pencahayaan', 'Lampu, Lampu Meja, Lampu Gantung, LED', '💡'),
('Dekorasi & Tekstil', 'Bantal, Selimut, Gorden, Poster, Dekorasi dinding', '🎨'),
('Peralatan Kebersihan', 'Sapu, Pel, Ember, Sabun, Pembersih', '🧹'),
('Perlengkapan Kamar Mandi', 'Shower, Wastafel, Cermin, Rak Handuk', '🚿'),
('Lainnya', 'Barang kebutuhan kos lainnya', '📦');

-- ══════════════════════════════════════════════════════════════
-- SAMPLE DATA — BARANG KEBUTUHAN KOS
-- ══════════════════════════════════════════════════════════════

-- TEMPAT TIDUR (Kategori 1)
INSERT INTO barang (kategori_id, nama_barang, deskripsi, harga, stok, kondisi, seller_id, kota, alamat, rating, jumlah_review, status) VALUES
(1, 'Kasur Spring Bed Single 100x200 Cm', 'Kasur spring bed berkualitas tinggi, empuk dan tahan lama. Cocok untuk kamar kos. Garansi 5 tahun.', 1500000, 5, 'Baru', 2, 'Yogyakarta', 'Jl. Kaliurang KM 5', 4.8, 12, 'tersedia'),
(1, 'Ranjang Besi Minimalis Single', 'Ranjang besi kokoh dengan desain minimalis. Cocok untuk kamar kos yang sempit. Mudah dipasang.', 450000, 8, 'Baru', 3, 'Bandung', 'Jl. Ganesha No. 8', 4.5, 8, 'tersedia'),
(1, 'Kasur Busa Empuk 100x200 Cm', 'Kasur busa berkualitas dengan ketebalan 20cm. Nyaman dan terjangkau untuk mahasiswa.', 800000, 12, 'Baru', 4, 'Jakarta', 'Jl. Sudirman No. 123', 4.3, 15, 'tersedia'),
(1, 'Ranjang Kayu Jati Single Bekas', 'Ranjang kayu jati bekas kondisi sangat baik. Kokoh dan tahan lama. Harga terjangkau.', 350000, 2, 'Bekas - Sangat Baik', 4, 'Surabaya', 'Jl. Dharmawangsa No. 56', 4.6, 6, 'tersedia'),
(1, 'Kasur Latex Natural 100x200 Cm', 'Kasur latex alami, anti tungau, dan nyaman untuk tidur panjang. Investasi kesehatan tidur.', 2200000, 3, 'Baru', 2, 'Yogyakarta', 'Jl. Kaliurang KM 5', 4.9, 10, 'tersedia');

-- LEMARI & PENYIMPANAN (Kategori 2)
INSERT INTO barang (kategori_id, nama_barang, deskripsi, harga, stok, kondisi, seller_id, kota, alamat, rating, jumlah_review, status) VALUES
(2, 'Lemari Pakaian 2 Pintu Minimalis', 'Lemari pakaian minimalis dengan 2 pintu. Cocok untuk kamar kos yang terbatas. Material MDF berkualitas.', 650000, 6, 'Baru', 3, 'Bandung', 'Jl. Ganesha No. 8', 4.4, 9, 'tersedia'),
(2, 'Rak Buku 5 Tingkat Besi', 'Rak buku besi kokoh dengan 5 tingkat. Dapat menampung banyak barang. Mudah dipasang.', 280000, 10, 'Baru', 4, 'Jakarta', 'Jl. Sudirman No. 123', 4.2, 7, 'tersedia'),
(2, 'Lemari Plastik 3 Pintu Bekas', 'Lemari plastik bekas kondisi baik. Ringan dan mudah dipindahkan. Cocok untuk barang-barang kecil.', 200000, 3, 'Bekas - Baik', 4, 'Surabaya', 'Jl. Dharmawangsa No. 56', 4.1, 4, 'tersedia'),
(2, 'Laci Bawah Tempat Tidur', 'Laci penyimpanan yang dapat diletakkan di bawah tempat tidur. Menghemat ruang kamar.', 180000, 15, 'Baru', 2, 'Yogyakarta', 'Jl. Kaliurang KM 5', 4.3, 5, 'tersedia'),
(2, 'Rak Dinding Floating 3 Buah', 'Rak dinding floating minimalis, dapat menampung barang dekorasi atau buku. Desain modern.', 320000, 8, 'Baru', 3, 'Bandung', 'Jl. Ganesha No. 8', 4.5, 6, 'tersedia');

-- MEJA & KURSI (Kategori 3)
INSERT INTO barang (kategori_id, nama_barang, deskripsi, harga, stok, kondisi, seller_id, kota, alamat, rating, jumlah_review, status) VALUES
(3, 'Meja Belajar Minimalis 60x40 Cm', 'Meja belajar minimalis dengan ukuran pas untuk kamar kos. Material MDF berkualitas, kokoh.', 350000, 7, 'Baru', 4, 'Jakarta', 'Jl. Sudirman No. 123', 4.6, 11, 'tersedia'),
(3, 'Kursi Belajar Ergonomis', 'Kursi belajar dengan desain ergonomis, nyaman untuk belajar berjam-jam. Dapat disesuaikan ketinggiannya.', 450000, 5, 'Baru', 2, 'Yogyakarta', 'Jl. Kaliurang KM 5', 4.7, 13, 'tersedia'),
(3, 'Meja Lipat Portable', 'Meja lipat yang dapat dibawa kemana-mana. Cocok untuk kamar kos yang terbatas. Mudah disimpan.', 180000, 12, 'Baru', 3, 'Bandung', 'Jl. Ganesha No. 8', 4.2, 8, 'tersedia'),
(3, 'Kursi Plastik Bekas Kondisi Baik', 'Kursi plastik bekas, masih kokoh dan nyaman. Harga sangat terjangkau untuk mahasiswa.', 80000, 6, 'Bekas - Baik', 4, 'Surabaya', 'Jl. Dharmawangsa No. 56', 4.0, 3, 'tersedia'),
(3, 'Meja Makan Kayu 4 Kursi', 'Meja makan kayu dengan 4 kursi, cocok untuk ruang bersama kos atau kamar yang luas.', 1200000, 2, 'Baru', 4, 'Jakarta', 'Jl. Sudirman No. 123', 4.4, 7, 'tersedia');

-- PERALATAN DAPUR (Kategori 4)
INSERT INTO barang (kategori_id, nama_barang, deskripsi, harga, stok, kondisi, seller_id, kota, alamat, rating, jumlah_review, status) VALUES
(4, 'Kompor Gas 2 Tungku', 'Kompor gas 2 tungku dengan bahan stainless steel. Hemat gas dan mudah dibersihkan.', 250000, 8, 'Baru', 2, 'Yogyakarta', 'Jl. Kaliurang KM 5', 4.5, 9, 'tersedia'),
(4, 'Panci Set 3 Buah', 'Set panci 3 ukuran berbeda dengan tutup kaca. Cocok untuk memasak berbagai hidangan.', 180000, 10, 'Baru', 3, 'Bandung', 'Jl. Ganesha No. 8', 4.3, 6, 'tersedia'),
(4, 'Wajan Anti Lengket', 'Wajan anti lengket dengan diameter 30cm. Mudah dibersihkan dan tahan lama.', 120000, 15, 'Baru', 4, 'Jakarta', 'Jl. Sudirman No. 123', 4.4, 8, 'tersedia'),
(4, 'Peralatan Masak Set Lengkap', 'Set peralatan masak lengkap berisi sendok, garpu, pisau, talenan, dan lainnya. Praktis.', 200000, 6, 'Baru', 4, 'Surabaya', 'Jl. Dharmawangsa No. 56', 4.2, 5, 'tersedia'),
(4, 'Kompor Portable Bekas', 'Kompor portable bekas kondisi masih baik. Cocok untuk kamar kos dengan dapur bersama terbatas.', 100000, 4, 'Bekas - Baik', 2, 'Yogyakarta', 'Jl. Kaliurang KM 5', 4.0, 2, 'tersedia');

-- ELEKTRONIK (Kategori 5)
INSERT INTO barang (kategori_id, nama_barang, deskripsi, harga, stok, kondisi, seller_id, kota, alamat, rating, jumlah_review, status) VALUES
(5, 'Kipas Angin Berdiri 16 Inch', 'Kipas angin berdiri dengan diameter 16 inch. Hemat listrik dan cocok untuk kamar kos.', 180000, 10, 'Baru', 3, 'Bandung', 'Jl. Ganesha No. 8', 4.6, 12, 'tersedia'),
(5, 'Kulkas Mini 1 Pintu', 'Kulkas mini 1 pintu dengan kapasitas 50 liter. Cocok untuk kamar kos, hemat listrik.', 800000, 4, 'Baru', 4, 'Jakarta', 'Jl. Sudirman No. 123', 4.7, 14, 'tersedia'),
(5, 'Pemanas Air Listrik 500W', 'Pemanas air listrik untuk mandi air hangat. Hemat energi dan mudah digunakan.', 120000, 8, 'Baru', 2, 'Yogyakarta', 'Jl. Kaliurang KM 5', 4.3, 7, 'tersedia'),
(5, 'Microwave 20 Liter', 'Microwave 20 liter untuk memanaskan makanan. Mudah digunakan dan hemat tempat.', 450000, 3, 'Baru', 4, 'Surabaya', 'Jl. Dharmawangsa No. 56', 4.5, 9, 'tersedia'),
(5, 'Kipas Angin Bekas Kondisi Sangat Baik', 'Kipas angin bekas, masih berfungsi normal dan hemat listrik. Harga sangat terjangkau.', 80000, 5, 'Bekas - Sangat Baik', 3, 'Bandung', 'Jl. Ganesha No. 8', 4.2, 4, 'tersedia');

-- PENCAHAYAAN (Kategori 6)
INSERT INTO barang (kategori_id, nama_barang, deskripsi, harga, stok, kondisi, seller_id, kota, alamat, rating, jumlah_review, status) VALUES
(6, 'Lampu LED Bulb 12W Putih', 'Lampu LED bulb 12W dengan cahaya putih. Hemat listrik hingga 80% dibanding lampu biasa.', 35000, 20, 'Baru', 2, 'Yogyakarta', 'Jl. Kaliurang KM 5', 4.8, 15, 'tersedia'),
(6, 'Lampu Meja Belajar LED', 'Lampu meja belajar LED dengan 3 mode cahaya. Dapat disesuaikan intensitas cahayanya.', 150000, 8, 'Baru', 4, 'Jakarta', 'Jl. Sudirman No. 123', 4.6, 10, 'tersedia'),
(6, 'Lampu Gantung Minimalis', 'Lampu gantung dengan desain minimalis modern. Cocok untuk kamar kos dengan gaya kontemporer.', 180000, 6, 'Baru', 3, 'Bandung', 'Jl. Ganesha No. 8', 4.4, 7, 'tersedia'),
(6, 'Lampu Neon Flex RGB 5 Meter', 'Lampu neon flex RGB yang dapat berubah warna. Cocok untuk dekorasi kamar kos modern.', 250000, 4, 'Baru', 4, 'Surabaya', 'Jl. Dharmawangsa No. 56', 4.5, 8, 'tersedia'),
(6, 'Lampu Standar Bekas', 'Lampu standar bekas masih berfungsi baik. Harga murah untuk kebutuhan dasar.', 25000, 10, 'Bekas - Baik', 2, 'Yogyakarta', 'Jl. Kaliurang KM 5', 3.9, 2, 'tersedia');

-- DEKORASI & TEKSTIL (Kategori 7)
INSERT INTO barang (kategori_id, nama_barang, deskripsi, harga, stok, kondisi, seller_id, kota, alamat, rating, jumlah_review, status) VALUES
(7, 'Bantal Tidur Ergonomis', 'Bantal tidur ergonomis dengan busa memory foam. Nyaman untuk tidur dan menjaga kesehatan leher.', 180000, 12, 'Baru', 3, 'Bandung', 'Jl. Ganesha No. 8', 4.7, 11, 'tersedia'),
(7, 'Selimut Tebal 160x200 Cm', 'Selimut tebal dengan bahan fleece hangat. Cocok untuk musim dingin atau kamar yang sejuk.', 120000, 10, 'Baru', 4, 'Jakarta', 'Jl. Sudirman No. 123', 4.5, 8, 'tersedia'),
(7, 'Gorden Jendela 2 Panel', 'Gorden jendela dengan 2 panel, dapat menghalangi cahaya matahari. Desain minimalis.', 200000, 6, 'Baru', 2, 'Yogyakarta', 'Jl. Kaliurang KM 5', 4.3, 6, 'tersedia'),
(7, 'Poster Motivasi Set 4 Buah', 'Set 4 poster motivasi dengan desain menarik. Cocok untuk dekorasi dinding kamar kos.', 80000, 15, 'Baru', 4, 'Surabaya', 'Jl. Dharmawangsa No. 56', 4.4, 5, 'tersedia'),
(7, 'Sarung Bantal Set 4 Buah Bekas', 'Sarung bantal set 4 buah bekas kondisi baik. Harga terjangkau untuk kebutuhan dasar.', 40000, 8, 'Bekas - Baik', 3, 'Bandung', 'Jl. Ganesha No. 8', 4.0, 2, 'tersedia');

-- PERALATAN KEBERSIHAN (Kategori 8)
INSERT INTO barang (kategori_id, nama_barang, deskripsi, harga, stok, kondisi, seller_id, kota, alamat, rating, jumlah_review, status) VALUES
(8, 'Sapu Lidi Tradisional', 'Sapu lidi tradisional yang efektif membersihkan lantai. Tahan lama dan harga terjangkau.', 25000, 20, 'Baru', 2, 'Yogyakarta', 'Jl. Kaliurang KM 5', 4.2, 4, 'tersedia'),
(8, 'Pel Lantai Microfiber', 'Pel lantai dengan bahan microfiber yang menyerap air. Mudah dibersihkan dan tahan lama.', 80000, 12, 'Baru', 4, 'Jakarta', 'Jl. Sudirman No. 123', 4.6, 9, 'tersedia'),
(8, 'Ember Plastik 10 Liter', 'Ember plastik dengan kapasitas 10 liter. Cocok untuk keperluan membersihkan kamar.', 35000, 15, 'Baru', 3, 'Bandung', 'Jl. Ganesha No. 8', 4.3, 5, 'tersedia'),
(8, 'Sabun Pembersih Lantai 1 Liter', 'Sabun pembersih lantai yang efektif membunuh kuman. Wangi segar dan hemat pemakaian.', 25000, 25, 'Baru', 4, 'Surabaya', 'Jl. Dharmawangsa No. 56', 4.4, 7, 'tersedia'),
(8, 'Alat Pembersih Set Lengkap', 'Set lengkap alat pembersih berisi sapu, pel, sikat, dan ember. Praktis dan hemat.', 150000, 6, 'Baru', 2, 'Yogyakarta', 'Jl. Kaliurang KM 5', 4.5, 6, 'tersedia');

-- PERLENGKAPAN KAMAR MANDI (Kategori 9)
INSERT INTO barang (kategori_id, nama_barang, deskripsi, harga, stok, kondisi, seller_id, kota, alamat, rating, jumlah_review, status) VALUES
(9, 'Cermin Dinding 60x80 Cm', 'Cermin dinding dengan ukuran 60x80 cm. Cocok untuk kamar mandi atau ruang ganti.', 180000, 8, 'Baru', 3, 'Bandung', 'Jl. Ganesha No. 8', 4.5, 7, 'tersedia'),
(9, 'Rak Handuk Stainless Steel', 'Rak handuk stainless steel tahan karat. Kokoh dan mudah dipasang di dinding.', 120000, 10, 'Baru', 4, 'Jakarta', 'Jl. Sudirman No. 123', 4.6, 8, 'tersedia'),
(9, 'Shower Mandi Stainless Steel', 'Shower mandi stainless steel dengan aliran air yang merata. Tahan lama dan mudah dibersihkan.', 150000, 6, 'Baru', 2, 'Yogyakarta', 'Jl. Kaliurang KM 5', 4.4, 6, 'tersedia'),
(9, 'Wastafel Keramik Putih', 'Wastafel keramik putih dengan desain modern. Cocok untuk kamar mandi kos.', 280000, 4, 'Baru', 4, 'Surabaya', 'Jl. Dharmawangsa No. 56', 4.3, 5, 'tersedia'),
(9, 'Tikar Kamar Mandi Anti Slip', 'Tikar kamar mandi anti slip yang aman. Mudah dibersihkan dan tahan lama.', 50000, 15, 'Baru', 3, 'Bandung', 'Jl. Ganesha No. 8', 4.5, 4, 'tersedia');

-- LAINNYA (Kategori 10)
INSERT INTO barang (kategori_id, nama_barang, deskripsi, harga, stok, kondisi, seller_id, kota, alamat, rating, jumlah_review, status) VALUES
(10, 'Kabel Extension 5 Meter', 'Kabel extension dengan panjang 5 meter. Aman dan cocok untuk kamar kos.', 45000, 20, 'Baru', 2, 'Yogyakarta', 'Jl. Kaliurang KM 5', 4.6, 8, 'tersedia'),
(10, 'Kotak Penyimpanan Plastik', 'Kotak penyimpanan plastik dengan berbagai ukuran. Cocok untuk menyimpan barang-barang kecil.', 35000, 18, 'Baru', 4, 'Jakarta', 'Jl. Sudirman No. 123', 4.4, 6, 'tersedia'),
(10, 'Gantungan Baju Besi', 'Gantungan baju besi yang kokoh. Dapat menampung banyak baju dan tahan lama.', 60000, 12, 'Baru', 3, 'Bandung', 'Jl. Ganesha No. 8', 4.5, 5, 'tersedia'),
(10, 'Jam Dinding Digital LED', 'Jam dinding digital LED dengan tampilan besar. Cocok untuk kamar kos modern.', 85000, 8, 'Baru', 4, 'Surabaya', 'Jl. Dharmawangsa No. 56', 4.3, 4, 'tersedia'),
(10, 'Papan Tulis Putih Mini', 'Papan tulis putih mini untuk catatan atau reminder. Cocok untuk meja belajar.', 40000, 14, 'Baru', 2, 'Yogyakarta', 'Jl. Kaliurang KM 5', 4.4, 3, 'tersedia');
