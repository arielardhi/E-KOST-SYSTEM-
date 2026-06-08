-- ══════════════════════════════════════════════════════════════
-- E-KOST DATABASE EXTENDED — Tambahan 22 Kost + Kamar + Review
-- Jalankan SETELAH database.sql (data original tetap dipakai)
-- ══════════════════════════════════════════════════════════════

USE e_kost_db;

-- (Tambahan Owner & User ditiadakan untuk membatasi jumlah akun sesuai permintaan)

INSERT INTO kost (owner_id, name, type, description, address, city, latitude, longitude, facilities, rules) VALUES

-- ── YOGYAKARTA (7 kost) ──────────────────────────────────────
(2,  'Kost Premium UGM Garden', 'Putri',
 'Kost eksklusif putri tepat di sebelah pintu selatan UGM. Taman asri, ruang baca, dan komunitas belajar aktif.',
 'Jl. Kaliurang KM 4 No. 22, Sleman', 'Yogyakarta', -7.7745, 110.3821,
 'WiFi,AC,Kamar Mandi Dalam,Parkir Motor,Taman,CCTV,Laundry,Kulkas,Dispenser,Ruang Belajar',
 'Khusus perempuan, Jam malam 22.00, Tamu harus izin, Bayar tepat waktu'),

(2,  'Kost Prawirotaman Artistik', 'Campur',
 'Kost bergaya artistik di kawasan budaya Prawirotaman. Dekat batik gallery, kafe, dan pusat seni Yogyakarta.',
 'Jl. Prawirotaman II No. 8, Brontokusuman', 'Yogyakarta', -7.8131, 110.3679,
 'WiFi,AC,Kamar Mandi Dalam,CCTV,Ruang Tamu,Dapur Bersama,Rooftop,Free Netflix',
 'Dilarang merokok dalam kamar, Tamu lapor resepsionis, Kebersihan bersama'),

(3,  'Kost Kotagede Heritage', 'Putra',
 'Kost putra di kawasan heritage Kotagede, dekat pasar perak dan candi. Nuansa heritage Jawa yang autentik.',
 'Jl. Kemasan No. 17, Kotagede', 'Yogyakarta', -7.8354, 110.4044,
 'WiFi,Kipas Angin,Kamar Mandi Luar,Parkir Motor,Dapur Bersama',
 'Jam malam 23.00, Tidak bising, Bayar bulan depan H-7'),

(3,  'Kost Condongcatur Techno', 'Campur',
 'Kost modern dekat kampus UPN dan Amikom. Dilengkapi co-working space dan akses internet super cepat 100 Mbps.',
 'Jl. Ring Road Utara No. 45, Condongcatur', 'Yogyakarta', -7.7487, 110.4055,
 'WiFi 100Mbps,AC,Kamar Mandi Dalam,Co-working Space,CCTV,Parkir Motor,Parkir Mobil,Dispenser',
 'Jam malam 24.00, Tamu diizinkan s.d. 21.00, Tidak merokok'),

(4,  'Kost Bantul Permai', 'Putri',
 'Kost putri nyaman di Bantul, suasana pedesaan yang tenang. Dekat ISI Yogyakarta dan area Kasongan.',
 'Jl. Parangtritis KM 9 No. 3, Bantul', 'Yogyakarta', -7.8853, 110.3391,
 'WiFi,Kipas Angin,Kamar Mandi Dalam,Dapur Bersama,Parkir Motor,Taman',
 'Khusus perempuan, Jam malam 21.30, Tidak merokok'),

(4,  'Kost Seturan Elite', 'Campur',
 'Kost campur premium di kawasan Seturan. Fasilitas setara apartemen — gym, kolam renang, dan rooftop view.',
 'Jl. Seturan Raya No. 12, Depok, Sleman', 'Yogyakarta', -7.7678, 110.4091,
 'WiFi,AC,Kamar Mandi Dalam,Gym,Kolam Renang,Rooftop,CCTV,Parkir Mobil,Parkir Motor,Laundry,Cleaning Service',
 'Tamu lapor resepsionis, Tidak membawa hewan, Dress code area umum'),

(2, 'Kost Wates Kulonprogo', 'Putra',
 'Kost putra terjangkau di Wates, cocok untuk karyawan pabrik dan mahasiswa Politeknik Kulonprogo.',
 'Jl. Tentara Pelajar No. 6, Wates', 'Yogyakarta', -7.8859, 110.1680,
 'WiFi,Kipas Angin,Kamar Mandi Luar,Parkir Motor,Dapur Bersama',
 'Jam malam 22.30, Dilarang mabuk, Bayar tepat waktu'),

-- ── BANDUNG (4 kost) ─────────────────────────────────────────
(2, 'Kost Dago Pakar Hijau', 'Putri',
 'Kost putri mewah di kawasan Dago Pakar. Udara sejuk, pemandangan kota Bandung, dan koneksi alam.',
 'Jl. Dago Pakar Raya No. 32', 'Bandung', -6.8467, 107.6329,
 'WiFi,AC,Kamar Mandi Dalam,CCTV,Taman,Parkir Motor,Ruang Belajar,Kulkas,Microwave',
 'Khusus perempuan, Jam malam 22.00, Tidak merokok'),

(3, 'Kost Buah Batu Strategis', 'Campur',
 'Kost campur modern dekat Buah Batu dan Kopo. Lokasi strategis untuk akses ke berbagai kawasan Bandung.',
 'Jl. Buah Batu No. 78', 'Bandung', -6.9499, 107.6346,
 'WiFi,AC,Kamar Mandi Dalam,CCTV,Parkir Motor,Dapur Bersama,Laundry',
 'Tamu lapor ke penghuni, Jam tamu 20.00, Tidak merokok area umum'),

(3, 'Kost Cimahi Techpark', 'Putra',
 'Kost putra dekat kawasan industri Cimahi dan Polban. Akses mudah ke Bandung via angkot atau ojek online.',
 'Jl. Industri No. 5, Cimahi', 'Bandung', -6.8740, 107.5428,
 'WiFi,Kipas Angin,Kamar Mandi Luar,Parkir Motor,Dapur Bersama',
 'Jam malam 23.00, Tidak bising setelah jam 22.00'),

(4, 'Kost Setrasari Premium', 'Campur',
 'Kost campur premium di Setrasari Mall area. Dekat mall, restoran, dan akses tol Pasteur.',
 'Jl. Setrasari Kulon No. 15', 'Bandung', -6.9023, 107.5921,
 'WiFi,AC,Kamar Mandi Dalam,CCTV,Parkir Mobil,Parkir Motor,Cleaning Service',
 'Tamu s.d. 21.00, Tidak merokok, Tidak boleh masak di kamar'),

-- ── JAKARTA (4 kost) ─────────────────────────────────────────
(4, 'Kost Kemang Eksekutif', 'Campur',
 'Kost premium di Kemang, kawasan expat dan startup Jakarta Selatan. Cocok untuk profesional muda.',
 'Jl. Kemang Raya No. 56, Bangka', 'Jakarta', -6.2627, 106.8141,
 'WiFi,AC,Kamar Mandi Dalam,Parkir Mobil,Parkir Motor,Gym,CCTV,Cleaning Service,Rooftop',
 'Tamu lapor security, Tidak merokok di dalam, Parkir maksimal 1 mobil per kamar'),

(2, 'Kost Kelapa Gading Modern', 'Campur',
 'Kost eksklusif di Kelapa Gading. Dikelilingi mal, restoran mewah, dan akses LRT Jabodebek.',
 'Jl. Boulevard Raya No. 101, Kelapa Gading', 'Jakarta', -6.1596, 106.9009,
 'WiFi,AC,Kamar Mandi Dalam,Parkir Mobil,CCTV,Kolam Renang,Cleaning Service,Concierge',
 'Tidak merokok, Tamu harus terdaftar, Area bebas hewan peliharaan'),

(2, 'Kost Tebet Cozy', 'Putri',
 'Kost putri di Tebet, dekat berbagai kafe instagramable dan taman Tebet Eco Park.',
 'Jl. Tebet Barat Dalam No. 9, Tebet', 'Jakarta', -6.2456, 106.8540,
 'WiFi,AC,Kamar Mandi Dalam,CCTV,Laundry,Dapur Bersama',
 'Khusus perempuan, Jam malam 22.00, Tamu perempuan s.d. 21.00'),

(3, 'Kost Bekasi Timur Permai', 'Putra',
 'Kost putra terjangkau di Bekasi Timur. Akses KRL dan Trans Jakarta tersedia. Cocok untuk karyawan Kawasan Industri.',
 'Jl. Chairil Anwar No. 23, Bekasi Timur', 'Jakarta', -6.2426, 107.0059,
 'WiFi,Kipas Angin,Kamar Mandi Luar,Parkir Motor,Dapur Bersama,Ruang TV',
 'Jam malam 23.00, Tidak merokok, Bayar H-3 awal bulan'),

-- ── SURABAYA (3 kost) ────────────────────────────────────────
(3, 'Kost Rungkut Industri', 'Putra',
 'Kost putra dekat kawasan industri SIER dan ITS. Harga bersahabat, akses mudah ke berbagai rute.',
 'Jl. Rungkut Industri No. 34', 'Surabaya', -7.3265, 112.7672,
 'WiFi,AC,Kamar Mandi Dalam,Parkir Motor,CCTV',
 'Jam malam 22.30, Tidak merokok di kamar'),

(2,  'Kost Gubeng Cantik', 'Putri',
 'Kost putri dekat Stasiun Gubeng dan mall Surabaya. Strategis untuk mobilitas ke seluruh penjuru kota.',
 'Jl. Gubeng Masjid No. 7', 'Surabaya', -7.2711, 112.7512,
 'WiFi,AC,Kamar Mandi Dalam,CCTV,Parkir Motor,Laundry,Dispenser',
 'Khusus perempuan, Jam malam 22.00, Tidak merokok'),

(3,  'Kost Citraland Residence', 'Campur',
 'Kost campur mewah di Citraland Surabaya. Lingkungan perumahan elit, aman, dan nyaman untuk keluarga muda.',
 'Jl. Citraland Golf Boulevard No. 5', 'Surabaya', -7.2928, 112.6621,
 'WiFi,AC,Kamar Mandi Dalam,Parkir Mobil,Parkir Motor,Kolam Renang,CCTV,Cleaning Service',
 'Tamu lapor security, Tidak merokok, Tidak bising setelah 22.00'),

-- ── SEMARANG (2 kost) ────────────────────────────────────────
(4,  'Kost Tembalang Undip', 'Campur',
 'Kost campur strategis 3 menit jalan kaki dari Universitas Diponegoro. Fasilitas lengkap, harga bersahabat.',
 'Jl. Prof. Sudarto No. 11, Tembalang', 'Semarang', -7.0508, 110.4380,
 'WiFi,AC,Kamar Mandi Dalam,Parkir Motor,Dapur Bersama,CCTV,Ruang Belajar',
 'Jam malam 23.00, Tidak merokok, Bayar tepat waktu'),

(2, 'Kost Banyumanik Asri', 'Putri',
 'Kost putri di Banyumanik, suasana asri dan sejuk. Dekat pusat perbelanjaan dan akses tol Semarang.',
 'Jl. Banyumanik No. 28', 'Semarang', -7.0731, 110.4211,
 'WiFi,AC,Kamar Mandi Dalam,CCTV,Parkir Motor,Taman,Dapur Bersama',
 'Khusus perempuan, Jam malam 21.30, Tidak merokok'),

-- ── MALANG (2 kost) ──────────────────────────────────────────
(3, 'Kost Soekarno Hatta Malang', 'Campur',
 'Kost campur di kawasan ramai Soekarno Hatta Malang. Dekat mal, kuliner, dan akses ke kampus besar.',
 'Jl. Soekarno Hatta No. 67, Lowokwaru', 'Malang', -7.9436, 112.6130,
 'WiFi,AC,Kamar Mandi Dalam,CCTV,Parkir Motor,Dapur Bersama,Laundry',
 'Tamu s.d. 21.00, Tidak merokok di kamar'),

(4, 'Kost Batu Apple House', 'Putri',
 'Kost putri di Kota Batu yang sejuk. Cocok untuk mahasiswa dan wisatawan jangka panjang. Dekat Jatim Park.',
 'Jl. Diponegoro No. 3, Kota Batu', 'Malang', -7.8718, 112.5266,
 'WiFi,Kipas Angin,Kamar Mandi Dalam,Parkir Motor,Dapur Bersama,Taman',
 'Khusus perempuan, Jam malam 22.00, Tidak merokok');

-- ══════════════════════════════════════════════════════════════
-- KAMAR untuk setiap kost baru (id 11–32)
-- ══════════════════════════════════════════════════════════════
INSERT INTO kamar (kost_id, room_name, size, price_per_month, available_rooms, facilities, status) VALUES

-- Kost 11 (Premium UGM Garden – Putri)
(11,'Kamar Standar',  '3x3 m', 900000, 3,'Kasur,Lemari,Meja Belajar,Kipas Angin,Cermin','available'),
(11,'Kamar AC',       '3x4 m',1350000, 2,'Kasur,Lemari,Meja Belajar,AC,Kamar Mandi Dalam','available'),
(11,'Kamar Deluxe',   '4x4 m',1800000, 1,'Kasur Queen,Lemari,AC,Kamar Mandi Dalam,Balkon','available'),

-- Kost 12 (Prawirotaman Artistik – Campur)
(12,'Studio Artistik','4x4 m',1700000, 2,'Kasur Queen,Lemari,AC,Kamar Mandi Dalam,TV','available'),
(12,'Studio Deluxe',  '4x5 m',2300000, 1,'Kasur King,Lemari,AC,Kamar Mandi Dalam,TV,Sofa','available'),

-- Kost 13 (Kotagede Heritage – Putra)
(13,'Kamar Standar',  '3x3 m',  600000,5,'Kasur,Lemari,Kipas Angin','available'),
(13,'Kamar Plus',     '3x4 m',  800000,3,'Kasur,Lemari,AC,Meja Belajar','available'),

-- Kost 14 (Condongcatur Techno – Campur)
(14,'Kamar Standar',  '3x4 m', 950000, 4,'Kasur,Lemari,AC,Meja Belajar,Colokan Banyak','available'),
(14,'Kamar Premium',  '4x4 m',1400000, 2,'Kasur,Lemari,AC,Meja Belajar,Kamar Mandi Dalam,Kulkas Mini','available'),

-- Kost 15 (Bantul Permai – Putri)
(15,'Kamar Standar',  '3x3 m',  580000,4,'Kasur,Lemari,Kipas Angin,Meja','available'),
(15,'Kamar AC',       '3x4 m',  850000,2,'Kasur,Lemari,AC,Kamar Mandi Dalam','available'),

-- Kost 16 (Seturan Elite – Campur)
(16,'Kamar Standar',  '4x4 m',2000000,3,'Kasur,Lemari,AC,Kamar Mandi Dalam,TV','available'),
(16,'Kamar Suite',    '5x5 m',3200000,1,'Kasur King,Lemari,AC,Kamar Mandi Dalam,TV,Sofa,Pantry','available'),

-- Kost 17 (Wates Kulonprogo – Putra)
(17,'Kamar Standar',  '3x3 m',  450000,6,'Kasur,Lemari,Kipas Angin','available'),
(17,'Kamar Plus',     '3x4 m',  650000,3,'Kasur,Lemari,AC,Meja Belajar','available'),

-- Kost 18 (Dago Pakar – Putri)
(18,'Kamar Mountain View','3x4 m',1500000,2,'Kasur,Lemari,AC,Kamar Mandi Dalam,Meja Belajar','available'),
(18,'Kamar Suite View',   '4x5 m',2200000,1,'Kasur Queen,Lemari,AC,Kamar Mandi Dalam,Balkon,Kulkas','available'),

-- Kost 19 (Buah Batu – Campur)
(19,'Kamar Standar',  '3x3 m', 1100000,4,'Kasur,Lemari,AC,Kamar Mandi Dalam','available'),
(19,'Kamar Deluxe',   '4x4 m', 1700000,2,'Kasur,Lemari,AC,Kamar Mandi Dalam,TV,Kulkas','available'),

-- Kost 20 (Cimahi Techpark – Putra)
(20,'Kamar Standar',  '3x3 m',  700000,5,'Kasur,Lemari,Kipas Angin,Meja','available'),
(20,'Kamar AC',       '3x4 m',  950000,3,'Kasur,Lemari,AC,Kamar Mandi Dalam','available'),

-- Kost 21 (Setrasari Premium – Campur)
(21,'Kamar Premium',  '4x4 m', 2500000,3,'Kasur,Lemari,AC,Kamar Mandi Dalam,TV,Sofa','available'),
(21,'Kamar Executive','4x5 m', 3500000,1,'Kasur King,Lemari,AC,Kamar Mandi Dalam,TV,Sofa,Dapur Kecil','available'),

-- Kost 22 (Kemang Eksekutif – Campur)
(22,'Kamar Standar',  '4x4 m', 4000000,2,'Kasur,Lemari,AC,Kamar Mandi Dalam,TV','available'),
(22,'Kamar Suite',    '5x5 m', 6500000,1,'Kasur King,Lemari,AC,Kamar Mandi Dalam,TV,Sofa,Dapur','available'),

-- Kost 23 (Kelapa Gading – Campur)
(23,'Kamar Deluxe',   '4x5 m', 5000000,2,'Kasur,Lemari,AC,Kamar Mandi Dalam,TV,Kulkas','available'),
(23,'Penthouse Room', '6x6 m', 9000000,1,'Kasur King,Lemari,AC,Kamar Mandi Dalam,TV,Sofa,Pantry Full','available'),

-- Kost 24 (Tebet Cozy – Putri)
(24,'Kamar AC',       '3x4 m', 2200000,3,'Kasur,Lemari,AC,Kamar Mandi Dalam,Meja Belajar','available'),
(24,'Kamar Plus',     '4x4 m', 3000000,1,'Kasur Queen,Lemari,AC,Kamar Mandi Dalam,TV,Kulkas','available'),

-- Kost 25 (Bekasi Timur – Putra)
(25,'Kamar Standar',  '3x3 m',  800000,6,'Kasur,Lemari,Kipas Angin','available'),
(25,'Kamar AC',       '3x4 m', 1100000,4,'Kasur,Lemari,AC,Meja Belajar','available'),

-- Kost 26 (Rungkut – Putra)
(26,'Kamar Standar',  '3x3 m',  850000,5,'Kasur,Lemari,AC,Kamar Mandi Dalam','available'),
(26,'Kamar Deluxe',   '4x4 m', 1300000,2,'Kasur,Lemari,AC,Kamar Mandi Dalam,TV','available'),

-- Kost 27 (Gubeng – Putri)
(27,'Kamar AC',       '3x3 m', 1000000,3,'Kasur,Lemari,AC,Kamar Mandi Dalam','available'),
(27,'Kamar Plus',     '3x4 m', 1400000,2,'Kasur,Lemari,AC,Kamar Mandi Dalam,Kulkas Mini','available'),

-- Kost 28 (Citraland Sby – Campur)
(28,'Kamar Standar',  '4x4 m', 3000000,2,'Kasur,Lemari,AC,Kamar Mandi Dalam,TV','available'),
(28,'Kamar Suite',    '5x5 m', 4500000,1,'Kasur King,Lemari,AC,Kamar Mandi Dalam,TV,Sofa','available'),

-- Kost 29 (Tembalang Undip – Campur)
(29,'Kamar Standar',  '3x3 m',  750000,5,'Kasur,Lemari,AC,Kamar Mandi Dalam,Meja','available'),
(29,'Kamar Plus',     '3x4 m', 1050000,3,'Kasur,Lemari,AC,Kamar Mandi Dalam,TV,Meja Belajar','available'),

-- Kost 30 (Banyumanik – Putri)
(30,'Kamar AC',       '3x3 m',  820000,4,'Kasur,Lemari,AC,Meja Belajar','available'),
(30,'Kamar Deluxe',   '3x4 m', 1150000,2,'Kasur,Lemari,AC,Kamar Mandi Dalam,Kulkas Mini','available'),

-- Kost 31 (Soekarno Hatta Malang – Campur)
(31,'Kamar Standar',  '3x3 m',  700000,5,'Kasur,Lemari,AC,Kamar Mandi Dalam','available'),
(31,'Kamar Deluxe',   '4x4 m', 1200000,2,'Kasur,Lemari,AC,Kamar Mandi Dalam,TV','available'),

-- Kost 32 (Batu Apple House – Putri)
(32,'Kamar Mountain', '3x3 m',  600000,4,'Kasur,Lemari,Kipas Angin,Selimut Tebal','available'),
(32,'Kamar Hangat',   '3x4 m',  850000,2,'Kasur,Lemari,Kipas Angin,Kamar Mandi Dalam','available');

-- ══════════════════════════════════════════════════════════════
-- REVIEW — Rating & ulasan dari tenant
-- ══════════════════════════════════════════════════════════════
-- user IDs: budi=2,siti=3,andi=4,dewi=5,rudi=6 (owner original, jadikan juga user)
-- user tenant baru: 15–19
-- Note: user_id pakai id tenant baru (15-19) dan review untuk kost 1-32

INSERT INTO review (user_id, kost_id, rating, comment) VALUES
-- Kost lama 1-10
(5, 1, 5, 'Kost terbaik di Yogya! Bersih, aman, dan penjagaannya ketat. Sangat direkomendasikan untuk mahasiswi UGM.'),
(6, 1, 4, 'Lokasi sangat strategis, hanya 10 menit ke kampus. Fasilitas lengkap meski harga agak tinggi.'),
(5, 2, 4, 'Kost Mawar sangat nyaman untuk mahasiswa. Dekat Malioboro dan stasiun, harga terjangkau banget!'),
(5, 3, 5, 'Harmoni Jaya terbaik! Gym dan rooftop-nya seru banget. Wifi kencang, cocok untuk work from home.'),
(6, 4, 4, 'Kost di Lembang sejuk banget udaranya. Wajib bawa jaket tapi worth it! Lingkungan nyaman.'),
(5, 5, 5, 'Grand Jakarta is premium! Worth the price. Kolam renang dan gym bikin hidup makin sehat.'),
(6, 6, 3, 'Harga terjangkau, lokasi oke dekat UI. Tapi kamar mandi luar jadi kurang privasi.'),
(5, 7, 4, 'Kost Surabaya Asri enak, tenang dan bersih. AC dingin, recommended untuk yang kerja di RSUD.'),
(6, 8, 5, 'Kost Semarang paling oke! Di Simpang Lima, gampang ke mana-mana. Pemilik juga ramah.'),
(5, 9, 4, 'Amikom Residence pas banget untuk mahasiswa IT. Wifi cepat, suasana belajar kondusif.'),
(5, 10, 3, 'Kost Malang cukup oke untuk harga segitu. Fasilitas standar tapi bersih dan aman.'),
-- Kost baru 11-32
(6, 11, 5, 'UGM Garden luar biasa! Taman cantik, ruang baca nyaman, komunitas belajarnya aktif banget.'),
(5, 11, 4, 'Kost premium yang worth it. Dekat UGM, fasilitas lengkap. Hanya agak mahal tapi sebanding.'),
(6, 12, 5, 'Prawirotaman vibes-nya keren banget! Desain artistik, dekat kafe dan galeri seni Yogya.'),
(5, 13, 3, 'Kost sederhana tapi bersih. Lokasi Kotagede unik, ada nuansa heritage Jawa. Harga murah!'),
(5, 14, 5, 'Techno banget! Wifi 100 Mbps beneran kencang, co-working space nyaman. Cocok untuk developer.'),
(6, 15, 4, 'Bantul Permai tenang banget, cocok yang suka suasana pedesaan. Dekat ISI Yogya.'),
(5, 16, 5, 'Seturan Elite setara apartemen! Gym dan kolam renang available. Worth the price banget!'),
(6, 18, 5, 'Dago Pakar pemandangannya indah banget. Sejuk, bersih, dan aman. Rekomendasi untuk putri!'),
(5, 19, 4, 'Buah Batu strategis banget. Ke mana-mana gampang. Fasilitas lengkap dengan harga wajar.'),
(5, 22, 5, 'Kemang Eksekutif mewah! Cocok untuk expat dan startup founder. Rooftop view Jakarta!'),
(6, 23, 5, 'Kelapa Gading premium abis. Concierge, kolam renang, dekat mall. Gaya hidup level up!'),
(5, 24, 4, 'Tebet Cozy nyaman banget, dekat Eco Park. Sering jalan kaki pagi ke taman. Worth it!'),
(6, 29, 4, 'Tembalang Undip strategis banget untuk mahasiswa Undip. Wifi cepat, kamar bersih.'),
(5, 31, 4, 'Soekarno Hatta Malang posisinya oke, banyak warung makan dan kafe dekat sini.');

-- ══════════════════════════════════════════════════════════════
-- BOOKING SAMPLE
-- ══════════════════════════════════════════════════════════════
INSERT INTO booking (user_id, kamar_id, start_date, duration_months, total_price, status) VALUES
(5, 1,  '2025-01-01', 6,  4800000, 'confirmed'),
(6, 5,  '2025-02-01', 12, 18000000,'confirmed'),
(5, 10, '2025-01-15', 3,  3600000, 'completed'),
(6, 12, '2025-03-01', 6,  6000000, 'confirmed'),
(5, 16, '2025-02-15', 12, 24000000,'confirmed'),
(5, 20, '2025-04-01', 3,  2100000, 'confirmed'),
(6, 24, '2025-03-01', 6,  8100000, 'confirmed'),
(5, 30, '2025-01-01', 12, 13200000,'completed'),
(6, 35, '2025-05-01', 6,  5100000, 'pending'),
(5, 40, '2025-04-15', 3,  3000000, 'confirmed');
