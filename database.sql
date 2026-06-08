CREATE DATABASE IF NOT EXISTS e_kost_db;
USE e_kost_db;

-- Table: users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role ENUM('admin', 'owner', 'user') NOT NULL,
    full_name VARCHAR(100),
    phone VARCHAR(20),
    avatar VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: kost
CREATE TABLE kost (
    id INT AUTO_INCREMENT PRIMARY KEY,
    owner_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    type ENUM('Putra', 'Putri', 'Campur') NOT NULL,
    description TEXT,
    address TEXT NOT NULL,
    city VARCHAR(50) NOT NULL,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    rules TEXT,
    facilities TEXT,
    price_start DECIMAL(12, 2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table: kamar (rooms)
CREATE TABLE kamar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kost_id INT NOT NULL,
    room_name VARCHAR(50) NOT NULL,
    size VARCHAR(20),
    price_per_month DECIMAL(12, 2) NOT NULL,
    available_rooms INT DEFAULT 1,
    facilities TEXT,
    status ENUM('available', 'full') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kost_id) REFERENCES kost(id) ON DELETE CASCADE
);

-- Table: kost_foto
CREATE TABLE kost_foto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kost_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    is_main BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kost_id) REFERENCES kost(id) ON DELETE CASCADE
);

-- Table: booking
CREATE TABLE booking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    kamar_id INT NOT NULL,
    start_date DATE NOT NULL,
    duration_months INT NOT NULL,
    total_price DECIMAL(12, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (kamar_id) REFERENCES kamar(id) ON DELETE CASCADE
);

-- Table: pembayaran (payments)
CREATE TABLE pembayaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    user_id INT NOT NULL,
    amount DECIMAL(12, 2) NOT NULL,
    payment_proof VARCHAR(255),
    status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    payment_date DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES booking(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table: review
CREATE TABLE review (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    kost_id INT NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (kost_id) REFERENCES kost(id) ON DELETE CASCADE
);

-- Table: favorit
CREATE TABLE favorit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    kost_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (kost_id) REFERENCES kost(id) ON DELETE CASCADE
);

-- Table: chat
CREATE TABLE chat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table: notifikasi
CREATE TABLE notifikasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Default Admin Account (password: admin123)
INSERT INTO users (id, username, password, email, role, full_name) 
VALUES (1, 'admin', '$2y$10$ddjRJLKdUTIl03cjRjgLYOBSrIp8NCCYytr2wTRWW8TloiBQNjVve', 'admin@ekost.com', 'admin', 'System Administrator');

-- ══════════════════════════════════════════════════════
-- SAMPLE DATA — Kost & Kamar
-- ══════════════════════════════════════════════════════

-- Sample Owners & Users (Exactly 3 Owners & 2 Users)
INSERT INTO users (id, username, password, email, role, full_name, phone) VALUES
(2, 'owner1', '$2y$10$L5HCXp707ijD.LsPLfsx/.hb8Whq.VBcv6RNlUUwOkaPMV75OFUDS', 'owner1@ekost.com', 'owner', 'Owner Satu', '081111111111'),
(3, 'owner2', '$2y$10$L5HCXp707ijD.LsPLfsx/.hb8Whq.VBcv6RNlUUwOkaPMV75OFUDS', 'owner2@ekost.com', 'owner', 'Owner Dua',  '082222222222'),
(4, 'owner3', '$2y$10$L5HCXp707ijD.LsPLfsx/.hb8Whq.VBcv6RNlUUwOkaPMV75OFUDS', 'owner3@ekost.com', 'owner', 'Owner Tiga', '083333333333'),
(5, 'user1',  '$2y$10$L5HCXp707ijD.LsPLfsx/.hb8Whq.VBcv6RNlUUwOkaPMV75OFUDS', 'user1@ekost.com',  'user',  'User Satu',  '084444444444'),
(6, 'user2',  '$2y$10$L5HCXp707ijD.LsPLfsx/.hb8Whq.VBcv6RNlUUwOkaPMV75OFUDS', 'user2@ekost.com',  'user',  'User Dua',   '085555555555');

-- Sample Kost (10 data di berbagai kota)
INSERT INTO kost (owner_id, name, type, description, address, city, latitude, longitude, facilities, rules) VALUES
(2, 'Kost Mentari Indah',  'Putri',  'Kost nyaman dan bersih untuk wanita, dekat kampus UGM. Lingkungan aman dan tenang dengan penjagaan 24 jam.', 'Jl. Kaliurang KM 5 No. 12, Sleman', 'Yogyakarta', -7.7689, 110.3884, 'WiFi,AC,Kamar Mandi Dalam,Parkir Motor,Dapur Bersama,CCTV,Laundry', 'Tidak boleh membawa tamu menginap, Tidak merokok di dalam kamar, Jam malam 23.00'),
(2, 'Kost Mawar Sejahtera', 'Putra', 'Kost putra strategis dekat Malioboro dan stasiun tugu. Cocok untuk mahasiswa dan karyawan muda.', 'Jl. Dagen No. 45, Sosromenduran', 'Yogyakarta', -7.7956, 110.3650, 'WiFi,Kipas Angin,Kamar Mandi Luar,Parkir Motor,Dapur Bersama', 'Tidak boleh membawa tamu menginap, Jam malam 22.00'),
(3, 'Kost Harmoni Jaya',   'Campur','Kost campur modern dengan fasilitas lengkap. Lokasi strategis dekat pusat kota Bandung dan kampus ITB.', 'Jl. Ganesha No. 8, Coblong', 'Bandung', -6.8956, 107.6107, 'WiFi,AC,Kamar Mandi Dalam,Parkir Mobil,Parkir Motor,Rooftop,Gym,CCTV', 'Tamu harus lapor ke resepsionis, Tidak merokok di area umum'),
(3, 'Kost Sejuk Bandung',  'Putri', 'Kost eksklusif putri di kawasan sejuk Lembang. Dekat wisata dan pusat perbelanjaan Bandung.', 'Jl. Raya Lembang No. 67', 'Bandung', -6.8118, 107.6165, 'WiFi,AC,Kamar Mandi Dalam,Taman,CCTV,Laundry,Kulkas', 'Khusus wanita, Tidak boleh merokok, Jam malam 22.00'),
(4, 'Kost Grand Jakarta',  'Campur','Kost premium di pusat bisnis Jakarta Selatan. Ideal untuk profesional muda dan mahasiswa pascasarjana.', 'Jl. Sudirman No. 123, Kebayoran Baru', 'Jakarta', -6.2181, 106.8098, 'WiFi,AC,Kamar Mandi Dalam,Parkir Mobil,Parkir Motor,Kolam Renang,Gym,CCTV,Cleaning Service', 'Tidak merokok di dalam gedung, Tamu hanya sampai jam 21.00'),
(4, 'Kost Ceria Mahasiswa', 'Putra','Kost ramah mahasiswa dekat Universitas Indonesia. Harga terjangkau dengan fasilitas memadai.', 'Jl. Margonda Raya No. 34, Depok', 'Jakarta', -6.3728, 106.8302, 'WiFi,Kipas Angin,Kamar Mandi Luar,Parkir Motor,Dapur Bersama,Ruang Belajar', 'Jam malam 23.00, Tidak merokok di kamar'),
(4, 'Kost Surabaya Asri',  'Campur','Kost modern dekat Universitas Airlangga dan RSUD Dr. Soetomo. Lingkungan tenang dan aman.', 'Jl. Dharmawangsa No. 56', 'Surabaya', -7.2690, 112.7578, 'WiFi,AC,Kamar Mandi Dalam,Parkir Motor,CCTV,Laundry', 'Tidak membawa tamu menginap, Kebersihan kamar tanggung jawab penghuni'),
(2, 'Kost Merdeka Semarang','Putri','Kost putri nyaman di kawasan Simpang Lima Semarang. Dekat pusat perbelanjaan dan perkantoran.', 'Jl. Pandanaran No. 89', 'Semarang', -6.9932, 110.4203, 'WiFi,AC,Kamar Mandi Dalam,Parkir Motor,CCTV,Dapur Bersama', 'Khusus perempuan, Jam malam 22.00'),
(2, 'Kost Amikom Residence','Campur','Kost strategis 5 menit dari Universitas Amikom Yogyakarta. Fasilitas lengkap dan harga bersahabat untuk mahasiswa.', 'Jl. Ring Road Utara No. 5, Condongcatur', 'Yogyakarta', -7.7520, 110.4019, 'WiFi,AC,Kamar Mandi Dalam,Parkir Motor,Dapur Bersama,CCTV,Ruang Belajar', 'Jam malam 23.00, Tidak merokok di kamar, Bayar tepat waktu'),
(3, 'Kost Malang Nyaman',  'Putra', 'Kost putra dekat Universitas Brawijaya dan UMM. Harga terjangkau dengan lingkungan bersih.', 'Jl. Veteran No. 12, Ketawanggede', 'Malang', -7.9428, 112.6131, 'WiFi,Kipas Angin,Kamar Mandi Luar,Parkir Motor,Dapur Bersama', 'Jam malam 22.30, Tidak boleh membawa hewan peliharaan');

-- Kamar untuk setiap kost
INSERT INTO kamar (kost_id, room_name, size, price_per_month, available_rooms, facilities, status) VALUES
-- Kost 1 (Mentari Indah Yogya - Putri)
(1,'Kamar Standar','3x3 m',800000, 3,'Kasur,Lemari,Meja Belajar,Kipas Angin','available'),
(1,'Kamar AC',    '3x4 m',1200000,2,'Kasur,Lemari,Meja Belajar,AC,Kamar Mandi Dalam','available'),
-- Kost 2 (Mawar Yogya - Putra)
(2,'Kamar Standar','3x3 m',650000, 5,'Kasur,Lemari,Kipas Angin','available'),
(2,'Kamar Plus',  '4x4 m',900000, 2,'Kasur,Lemari,Meja Belajar,AC','available'),
-- Kost 3 (Harmoni Bandung - Campur)
(3,'Kamar Standar','3x4 m',1500000,4,'Kasur,Lemari,AC,Kamar Mandi Dalam','available'),
(3,'Kamar Deluxe','4x5 m',2500000,2,'Kasur King,Lemari,AC,Kamar Mandi Dalam,TV,Kulkas','available'),
-- Kost 4 (Sejuk Bandung - Putri)
(4,'Kamar AC',    '3x3 m',1300000,3,'Kasur,Lemari,AC,Meja','available'),
-- Kost 5 (Grand Jakarta - Campur)
(5,'Kamar Standar','4x4 m',3500000,5,'Kasur,Lemari,AC,Kamar Mandi Dalam,TV','available'),
(5,'Kamar Suite', '5x5 m',6000000,2,'Kasur King,Lemari,AC,Kamar Mandi Dalam,TV,Sofa,Mini Bar','available'),
-- Kost 6 (Ceria Jakarta - Putra)
(6,'Kamar Standar','3x3 m',1200000,6,'Kasur,Lemari,Kipas Angin','available'),
(6,'Kamar AC',    '3x4 m',1800000,3,'Kasur,Lemari,AC,Meja Belajar','available'),
-- Kost 7 (Surabaya Asri)
(7,'Kamar Standar','3x4 m',1000000,4,'Kasur,Lemari,AC,Kamar Mandi Dalam','available'),
(7,'Kamar Deluxe','4x4 m',1500000,2,'Kasur,Lemari,AC,Kamar Mandi Dalam,TV','available'),
-- Kost 8 (Semarang)
(8,'Kamar AC',    '3x3 m',900000, 4,'Kasur,Lemari,AC,Meja','available'),
-- Kost 9 (Amikom Residence)
(9,'Kamar Standar','3x3 m',700000, 6,'Kasur,Lemari,Kipas Angin,Meja Belajar','available'),
(9,'Kamar AC',    '3x4 m',1100000,4,'Kasur,Lemari,AC,Meja Belajar,Kamar Mandi Dalam','available'),
-- Kost 10 (Malang)
(10,'Kamar Standar','3x3 m',600000,5,'Kasur,Lemari,Kipas Angin','available'),
(10,'Kamar Plus', '3x4 m',850000, 3,'Kasur,Lemari,AC,Meja','available');
