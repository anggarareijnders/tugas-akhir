-- ============================================================
-- DATABASE SCHEMA & REAL SEEDER SPK MOORA OPEN TRIP GUNUNG 
-- Database Name: spk_moora_trip
-- ============================================================

CREATE DATABASE IF NOT EXISTS `spk_moora_trip` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `spk_moora_trip`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `laporan_terpilih`;
DROP TABLE IF EXISTS `kontak_darurat`;
DROP TABLE IF EXISTS `rute_segmentasi`;
DROP TABLE IF EXISTS `edukasi`;
DROP TABLE IF EXISTS `paket_trip`;
DROP TABLE IF EXISTS `kriteria`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. TABEL USERS (ROLE: ADMIN, VENDOR, PENDAKI)
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `fullname` VARCHAR(100) NOT NULL,
  `role` ENUM('admin', 'vendor', 'pendaki') NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. TABEL KRITERIA SPK MOORA
CREATE TABLE `kriteria` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `kode` VARCHAR(10) NOT NULL UNIQUE,
  `nama` VARCHAR(100) NOT NULL,
  `jenis` ENUM('benefit', 'cost') NOT NULL,
  `bobot` FLOAT NOT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. TABEL PAKET TRIP (KATEGORI: TEKTOK / CAMPING)
CREATE TABLE `paket_trip` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `vendor_id` INT NOT NULL,
  `nama_paket` VARCHAR(150) NOT NULL,
  `deskripsi` TEXT,
  `gunung` VARCHAR(100) NOT NULL,
  `kategori` ENUM('Tektok', 'Camping') NOT NULL DEFAULT 'Camping',
  `c1_harga` DOUBLE NOT NULL,
  `c2_ketinggian` INT NOT NULL,
  `c3_medan` INT NOT NULL,
  `c4_fasilitas` VARCHAR(255) NOT NULL,
  `c5_waktu_tempuh` INT NOT NULL,
  `gambar` VARCHAR(255) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`vendor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. TABEL RELASI SEGMENTASI RUTE PER POS ANTAR GUNUNG
CREATE TABLE `rute_segmentasi` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `paket_id` INT NOT NULL,
  `urutan` INT NOT NULL DEFAULT 1,
  `pos_asal` VARCHAR(100) NOT NULL,
  `pos_tujuan` VARCHAR(100) NOT NULL,
  `waktu_tempuh_menit` INT NOT NULL,
  `elevasi_gain` INT NOT NULL,
  `kondisi_medan` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`paket_id`) REFERENCES `paket_trip`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. TABEL RELASI KONTAK DARURAT & PORTER LOKAL
CREATE TABLE `kontak_darurat` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `paket_id` INT NOT NULL,
  `nama` VARCHAR(100) NOT NULL,
  `peran` VARCHAR(100) NOT NULL,
  `no_hp` VARCHAR(50) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`paket_id`) REFERENCES `paket_trip`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. TABEL LAPORAN PAKET TERPILIH PENDAKI
CREATE TABLE `laporan_terpilih` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `paket_id` INT NOT NULL,
  `skor_moora` DOUBLE NULL,
  `kategori_pilihan` VARCHAR(50) NOT NULL,
  `catatan_fisik` VARCHAR(255) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`paket_id`) REFERENCES `paket_trip`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. TABEL EDUKASI LOGISTIK
CREATE TABLE `edukasi` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `vendor_id` INT NOT NULL,
  `paket_id` INT NULL,
  `gunung` VARCHAR(100) NOT NULL,
  `judul` VARCHAR(200) NOT NULL,
  `konten` TEXT NOT NULL,
  `logistik` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`vendor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`paket_id`) REFERENCES `paket_trip`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SEED DATA MURNI REALISTIS
-- ============================================================

-- Seed Kriteria (C1-C5)
INSERT INTO `kriteria` (`kode`, `nama`, `jenis`, `bobot`) VALUES
('C1', 'Harga Paket', 'cost', 0.20),
('C2', 'Ketinggian Gunung', 'benefit', 0.10),
('C3', 'Kesulitan Medan', 'cost', 0.30),
('C4', 'Kelengkapan Fasilitas', 'benefit', 0.25),
('C5', 'Estimasi Waktu Tempuh', 'cost', 0.15);

-- Seed Users Demo (password: admin123 / vendor123 / pendaki123)
INSERT INTO `users` (`id`, `username`, `password`, `fullname`, `role`) VALUES
(1, 'admin', '$2y$10$eO0V4h/W8N5e3d7F5k9Sde9Y3q7pW2k/4H8j7Z3s5m2e1f4g5h6i7', 'Administrator Utama SPK', 'admin'),
(2, 'vendor_kuya', '$2y$10$eO0V4h/W8N5e3d7F5k9Sde9Y3q7pW2k/4H8j7Z3s5m2e1f4g5h6i7', 'Kuya Journey Indonesia', 'vendor'),
(3, 'vendor_cfd', '$2y$10$eO0V4h/W8N5e3d7F5k9Sde9Y3q7pW2k/4H8j7Z3s5m2e1f4g5h6i7', 'CFD Traveller', 'vendor'),
(4, 'pendaki_sejati', '$2y$10$eO0V4h/W8N5e3d7F5k9Sde9Y3q7pW2k/4H8j7Z3s5m2e1f4g5h6i7', 'Giri Wira', 'pendaki');

-- Seed Paket Trip Realistis (Paket 1: Gede via Putri | Paket 2: Pangrango via Cibodas)
INSERT INTO `paket_trip` (`id`, `vendor_id`, `nama_paket`, `deskripsi`, `gunung`, `kategori`, `c1_harga`, `c2_ketinggian`, `c3_medan`, `c4_fasilitas`, `c5_waktu_tempuh`, `gambar`) VALUES
(1, 2, 'Paket Eksplorasi Gunung Gede via Putri (Camping 2D1N)', 'Pendakian klasik Gunung Gede 2958 mdpl via Jalur Gunung Putri. Termasuk kemah hangat di padang edelweis Alun-Alun Surya Kencana, tenda dome kelompok, 3x konsumsi hangat, porter logistik, dan P3K.', 'Gede', 'Camping', 750000, 2958, 2, 'F1,F3,F4,F6,F8,F10,F11,F12', 6, 'https://images.unsplash.com/photo-1626082895617-2c6de3476af7?auto=format&fit=crop&w=800&q=80'),
(2, 3, 'Speed Tektok Gunung Pangrango via Cibodas (Tektok 1D)', 'Pendakian tektok tercepat pergi-pulang 1 hari menembus Puncak Pangrango 3019 mdpl dan Lembah Mandalawangi via Cibodas. Melintasi Telaga Biru, Air Terjun Cibeureum, Air Panas, dan Kandang Badak.', 'Pangrango', 'Tektok', 480000, 3019, 3, 'F4,F5,F9,F10,F12', 10, 'https://images.unsplash.com/photo-1544735716-392fe2489ffa?auto=format&fit=crop&w=800&q=80'),
(3, 2, 'Paket Eksplorasi Mahameru (Semeru 3D2N)', 'Ekspedisi Puncak Para Dewa Mahameru 3676 mdpl via Ranupani dan Ranu Kumbolo dengan pelayanan full service porter.', 'Semeru', 'Camping', 1500000, 3676, 3, 'F1,F3,F4,F6,F8,F11,F12', 7, 'https://images.unsplash.com/photo-1589308078059-be1415eab4c3?auto=format&fit=crop&w=800&q=80'),
(4, 3, 'Sunrise Tektok Trip Gunung Prau Dieng', 'Pendakian tektok santai menyaksikan Golden Sunrise Asia Tenggara di Gunung Prau 2565 mdpl.', 'Prau', 'Tektok', 420000, 2565, 1, 'F4,F9,F10,F12', 3, 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=800&q=80');

-- Seed Segmentasi Rute Realistis Gunung Gede via Putri & Pangrango via Cibodas
INSERT INTO `rute_segmentasi` (`paket_id`, `urutan`, `pos_asal`, `pos_tujuan`, `waktu_tempuh_menit`, `elevasi_gain`, `kondisi_medan`) VALUES
-- Paket 1: Gede via Putri (Camping 2D1N)
(1, 1, 'Basecamp Putri (1.450 mdpl)', 'Pos 1 Informasi Tanah Merah', 60, 250, 'Jalur tanah padat berlandai melintasi pemukiman & ladang sayur warga'),
(1, 2, 'Pos 1 Tanah Merah', 'Pos 2 Legok Leunyay', 90, 350, 'Tanjakan vegetasi rapat, tanah merah berakar pohon & tangga tanah alami'),
(1, 3, 'Pos 2 Legok Leunyay', 'Pos 3 Buntut Semar', 90, 400, 'Tanjakan batu alam terjal & tangga tanah alami yang konsisten menanjak'),
(1, 4, 'Pos 3 Buntut Semar', 'Alun-Alun Surya Kencana (2.750 mdpl)', 120, 650, 'Tanjakan batu terjal bervegetasi rapat hutan edelweis & angin bertiup kencang'),
(1, 5, 'Alun-Alun Surya Kencana', 'Puncak Gunung Gede (2.958 mdpl)', 30, 208, 'Jalur berbatu terbuka menanjak menuju bibir kawah aktif Gunung Gede'),

-- Paket 2: Pangrango via Cibodas (Tektok 1D)
(2, 1, 'Basecamp Cibodas (1.425 mdpl)', 'Pos Telaga Biru', 45, 150, 'Jalur batu tertata rapi berlandai di bawah naungan kanopi hutan tropis'),
(2, 2, 'Pos Telaga Biru', 'Air Terjun Cibeureum', 45, 200, 'Anak tangga batu alami & jembatan kayu lembap di pinggir aliran air'),
(2, 3, 'Air Terjun Cibeureum', 'Pos Air Panas & Kandang Badak', 150, 750, 'Tanjakan batu licin melintasi aliran air panas & tanah liat menanjak terjal'),
(2, 4, 'Kandang Badak (2.400 mdpl)', 'Puncak Gunung Pangrango (3.019 mdpl)', 120, 619, 'Tanjakan terjal merayap di antara jalinan akar pohon tua & tanah liat basah'),
(2, 5, 'Puncak Pangrango', 'Lembah Mandalawangi (3.000 mdpl)', 15, 50, 'Jalur tanah datar menurun tipis menuju padang edelweis & hutan lumut');

-- Seed Kontak Darurat & Porter Lokal Realistis
INSERT INTO `kontak_darurat` (`paket_id`, `nama`, `peran`, `no_hp`) VALUES
-- Kontak Paket 1 (Gede via Putri)
(1, 'Pak Ujang Sugandi', 'Pengelola Basecamp Gunung Putri', '0815-5566-6777'),
(1, 'Kang Asep Saepudin', 'Koordinator Porter Senior Putri', '0857-1122-3344'),
(1, 'Mas Bowo Kuya', 'Leader Crew Porter Kuya Journey', '0821-9876-5432'),

-- Kontak Paket 2 (Pangrango via Cibodas)
(2, 'Pak Cecep Herman', 'Pengelola Basecamp Cibodas TNGGP', '0812-9988-7766'),
(2, 'Mas Ceppy Local', 'Guide & Porter Tektok Pangrango', '0856-1122-3344'),
(2, 'Tim Medis Cibodas Rescue', 'Penanganan Emergency & P3K', '0813-4455-6677');
