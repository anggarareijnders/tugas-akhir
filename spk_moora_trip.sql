-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 25, 2026 at 11:37 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `spk_moora_trip`
--

-- --------------------------------------------------------

--
-- Table structure for table `edukasi`
--

CREATE TABLE `edukasi` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `paket_id` int(11) DEFAULT NULL,
  `gunung` varchar(100) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `konten` text NOT NULL,
  `logistik` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `edukasi`
--

INSERT INTO `edukasi` (`id`, `vendor_id`, `paket_id`, `gunung`, `judul`, `konten`, `logistik`, `created_at`) VALUES
(1, 2, 5, 'Merbabu', 'Panduan Logistik Merbabu', 'abcd lorem ldll', 'Jaket, Headlamp', '2026-07-24 07:37:55'),
(2, 2, 1, 'gede', 'adada', 'aaaa', 'aaaa', '2026-07-24 16:42:49'),
(3, 2, 7, 'Guntur', 'Logistik dan panduan guntur via cikahuripan', 'membawa jaket yang tebal, kaos kaki , karena disana suhu nya dingin', 'jaket\r\nkaos kaki', '2026-07-28 13:57:38'),
(4, 2, 7, 'Guntur', 'panduan logistik', 'membawa air mineral 2 botol minimal karena tidak ada sumber mata air disana', 'jaket\r\nkaos kaki', '2026-07-28 14:01:20'),
(5, 2, 7, 'Guntur', 'aaa', 'aaa', 'aaa', '2026-07-28 14:02:09'),
(6, 2, 7, 'Guntur', 'aaa', 'aaa', 'aaa', '2026-07-28 14:04:08'),
(7, 13, 9, 'Gede', 'Panduan Logistik', 'Gunung Gede memiliki ketinggian 2.958 mdpl dengan klasifikasi medan sedang.  Trek didominasi oleh tanah, bebatuan, dan akar pohon dengan kanopi hutan yang cukup rapat. Suhu di Alun-Alun Surya Kencana bisa sangat dingin, terutama saat malam hingga menjelang pagi.', 'Sepatu Trekking Anti-Slip\r\nJaket Windproof / Polar\r\nJas Hujan (Penting untuk cuaca hutan tropis)\r\n Carrier (untuk Camping)\r\nPakaian Ganti (Quick-dry, hindari bahan katun/jeans)\r\nHeadlamp & Baterai Cadangan\r\nObat-obatan Pribadi', '2026-07-29 05:56:32'),
(8, 13, 14, 'Sindoro', 'Panduan Logistik dan edukasi', 'Berada di ketinggian 3.153 mdpl, Gunung Sindoro menawarkan tantangan medan kategori sedang dengan estimasi waktu tempuh sekitar 6 jam hingga ke area camp. Pendaki akan melewati trek tanah padat yang semakin ke atas akan berubah menjadi bebatuan terjal. Waspadai paparan gas belerang dan debu saat cuaca kemarau, terutama ketika mendekati area puncak.', 'Masker / Buff (Wajib untuk menahan debu dan bau belerang)\r\nJaket Tebal (Insulasi penahan suhu dingin 3.000+ mdpl)\r\nSleeping Bag Thermal\r\nTrekking Pole (Meringankan beban lutut)\r\nSepatu Trekking (Rekomendasi High-Cut)\r\nKacamata Anti-UV / Kacamata Debu', '2026-07-29 05:57:53'),
(9, 13, 13, 'Sumbing', 'Edukasi Sumbing via Gajah Mungkur', 'Gunung Sumbing merupakan destinasi dengan elevasi tinggi mencapai 3.371 mdpl. Berbeda dengan gunung lainnya di daftar ini, Sumbing diklasifikasikan memiliki medan sulit dengan waktu tempuh panjang sekitar 7 jam. Treknya terkenal ekstrem, sangat curam, berdebu tebal, dan memiliki banyak bebatuan lepas (scree). Sangat menguji ketahanan kardiovaskular dan kekuatan otot kaki.', 'Sepatu Trekking High-Cut (Wajib untuk melindungi engkel dari batu lepas)\r\nGaiter (Mencegah debu dan kerikil masuk ke sepatu)\r\nTrekking Pole (Sangat krusial untuk medan sulit)\r\nJaket Penahan Angin (Windbreaker)\r\nKacamata Debu & Buff Tebal\r\nPersediaan Air Minum Ekstra\r\nSleeping Bag Comfort Limit Rendah', '2026-07-29 05:58:42'),
(10, 13, 12, 'Prau', 'Panduan Camping Ceria & Aman di Sabana Gunung Prau', 'Gunung Prau memiliki ketinggian 2.568 mdpl dan merupakan satu-satunya gunung di daftar ini dengan klasifikasi medan mudah. Dengan waktu tempuh yang sangat singkat, yakni sekitar 3 jam, gunung ini sangat direkomendasikan bagi pemula. Jalurnya berupa undakan tanah yang tertata. Namun, jangan remehkan suhu malam hari di sabana puncaknya karena angin bertiup kencang dan suhu bisa turun mendekati titik beku (frost).', 'Jaket Tebal (Sangat penting, disarankan berbahan Down / Bulu Angsa)\r\nKupluk / Beanie & Sarung Tangan Polar\r\nSleeping Bag Tebal\r\nKaos Kaki Hangat (Cadangan untuk tidur)\r\nSepatu / Sandal Gunung Tertutup', '2026-07-29 05:59:24'),
(11, 13, 11, 'Merbabu', 'Persiapan Melintasi Sabana Terbuka Gunung Merbabu', 'Merbabu berdiri megah di ketinggian 3.142 mdpl dengan medan kategori sedang dan waktu tempuh sekitar 6 jam. Tantangan utama di gunung ini bukan hanya tanjakan tanahnya, melainkan hembusan angin gunung yang sangat kencang karena hilangnya batas vegetasi (pohon) saat memasuki area sabana terbuka.', 'Jaket Windbreaker / Penahan Angin (Wajib untuk sabana)\r\nTopi Rimba / Sun Hat (Menahan terik matahari)\r\nSunblock / Tabir Surya\r\nPakaian Lapis Dalam (Base Layer)\r\nSleeping Bag & Matras Alumunium Foil\r\nTrekking Pole', '2026-07-29 06:01:04'),
(12, 13, 10, 'Ciremai', 'Kesiapan Fisik & Logistik Menuju Atap Jawa Barat (Ciremai)', 'Sebagai atap Jawa Barat, Ciremai memiliki ketinggian 3.078 mdpl. Berdasarkan paket yang ditawarkan, jalurnya memiliki klasifikasi medan sedang dengan estimasi waktu perjalanan mencapai 7 jam. Pendakian akan didominasi oleh lintasan hutan rapat, tanjakan akar, dan kontur elevasi yang panjang. Diperlukan manajemen napas dan langkah yang stabil.', 'Sepatu Trekking Anti-Slip\r\nJas Hujan / Ponco (Cuaca di hutan Ciremai sulit diprediksi)\r\nTrekking Pole\r\nPakaian Ganti Cadangan (Terbungkus plastik kedap air)\r\nHeadlamp (Bawa baterai cadangan ekstra)\r\nObat-obatan Pribadi & P3K Dasar Minimum', '2026-07-29 06:02:02'),
(13, 13, 15, 'Tektok Gede', 'Tektok Gede edukasi', 'lorem ipsum', 'hydropack \r\njaket\r\nspatu trail\r\nsarung tangan\r\nair mineral', '2026-07-29 06:03:08'),
(14, 3, 20, 'Slamet', 'Mitigasi Debu & Kesiapan Mental di Gunung Slamet', 'Slamet (3.432 mdpl)  Treknya berupa tanjakan tiada henti dengan tanah yang sangat berdebu tebal di musim kemarau, dan bebatuan lepas yang cukup ekstrem dari batas vegetasi menuju puncak.', 'Masker / Buff Tebal (Debu jalur Bambangan sangat tebal)\r\nSarung Tangan Gesek (Untuk merangkak di bebatuan puncak)\r\nKacamata Debu\r\nSepatu High-Cut & Gaiter\r\nTrekking Pole', '2026-07-29 08:08:18'),
(15, 3, 19, 'CIremai', 'Mitigasi Fisik Menuju Atap Jawa Barat (Ciremai)', 'Ciremai (3.078 mdpl) via Apuy memiliki Medan Sedang dengan waktu tempuh sekitar 7 jam. Pendakian akan didominasi oleh lintasan hutan rapat dan kontur elevasi yang tajam. Diperlukan ritme langkah yang konsisten.', 'Sepatu Trekking Anti-Slip\r\nJas Hujan / Ponco (Wajib dibawa kapan pun)\r\nTrekking Pole\r\nPakaian Ganti (Terbungkus plastik kedap air / Drybag)\r\nHeadlamp (Bawa baterai cadangan ekstra)', '2026-07-29 08:09:12'),
(16, 3, 18, 'Merbabu', 'Kesiapan Melintasi Sabana Terbuka Gunung Merbabu', 'Merbabu (3.142 mdpl) Tantangan utamanya adalah hilangnya batas vegetasi pepohonan saat memasuki area sabana, sehingga hembusan angin sangat kencang dan suhu terasa lebih dingin.', 'Jaket Windbreaker (Wajib penahan angin sabana)\r\nTopi Rimba / Sun Hat & Sunblock\r\nPakaian Baselayer\r\nSleeping Bag & Matras Alumunium Foil', '2026-07-29 08:10:08'),
(17, 3, 17, 'Papandayan', 'Panduan Santai & Aman di Gunung Papandayan', 'Papandayan (2.665 mdpl) memiliki Medan Mudah (didominasi jalur landai berbatu). Waktu tempuh ke area camp Pondok Saladah hanya sekitar 2-3 jam. Pengunjung akan melewati area kawah belerang di awal pendakian.', 'Masker Medis / Kain (Menahan bau belerang kawah)\r\nJaket Tebal (Suhu Pondok Saladah cukup dingin)\r\nSleeping Bag\r\nSepatu atau Sandal Gunung Tertutup\r\nObat-obatan Pribadi Dasar', '2026-07-29 08:11:05'),
(18, 3, 16, 'Cikuray', 'Panduan Fisik & Logistik Cikuray \"Jalur Tanpa Bonus\"', 'Gunung Cikuray (2.821 mdpl) memiliki karakteristik Medan Sulit. Dari basecamp hingga area camp memakan waktu sekitar 6-7 jam tanpa adanya kontur jalan datar. Jalur didominasi oleh tanjakan akar pohon yang tinggi dan tanah padat di dalam hutan rapat. Sangat dibutuhkan manajemen napas dan kekuatan lutut yang prima.', 'Sepatu Trekking Anti-Slip (Wajib)\r\nTrekking Pole (Sangat krusial untuk menahan beban lutut)\r\nJaket Windproof & Pakaian Ganti Dry-Fit\r\nSleeping Bag Comfort Limit Rendah\r\nJas Hujan / Ponco\r\nSarung Tangan & Headlamp', '2026-07-29 08:11:44');

-- --------------------------------------------------------

--
-- Table structure for table `kontak_darurat`
--

CREATE TABLE `kontak_darurat` (
  `id` int(11) NOT NULL,
  `paket_id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `peran` varchar(100) NOT NULL,
  `no_hp` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kontak_darurat`
--

INSERT INTO `kontak_darurat` (`id`, `paket_id`, `nama`, `peran`, `no_hp`, `created_at`) VALUES
(7, 5, 'Kang Acong', 'Informasi & SAR', '0812-3456-7890', '2026-07-24 07:36:45'),
(9, 6, 'Ketua Basecamp', 'Informasi & SAR', '0812-3456-7890', '2026-07-25 04:27:40'),
(13, 7, 'Ketua Basecamp', 'Informasi & SAR', '0812-3456-7890', '2026-07-28 13:54:36'),
(14, 8, 'Ketua Basecamp', 'Informasi & SAR', '0812-3456-7890', '2026-07-29 02:01:10'),
(16, 9, 'Ketua Basecamp', 'Informasi & SAR', '0812-3456-7890', '2026-07-29 05:30:37'),
(17, 10, 'Ketua Basecamp', 'Porter', '0812-3456-7890', '2026-07-29 05:38:57'),
(18, 11, 'Ketua Basecamp', 'Porter', '0812-3456-7890', '2026-07-29 05:41:07'),
(19, 12, 'Ketua Basecamp', 'Informasi & SAR', '0812-3456-7890', '2026-07-29 05:44:00'),
(20, 13, 'Ketua Basecamp', 'Informasi & SAR', '0812-3456-7890', '2026-07-29 05:47:22'),
(21, 14, 'Ketua Basecamp', 'Informasi & SAR', '0812-3456-7890', '2026-07-29 05:49:50'),
(22, 15, 'Ketua Basecamp', 'Informasi & SAR', '0812-3456-7890', '2026-07-29 05:52:08'),
(23, 16, 'Ketua Basecamp', 'Informasi & SAR', '0812-3456-7890', '2026-07-29 06:43:41'),
(25, 17, 'Ketua Basecamp', 'Informasi & SAR', '0812-3456-7890', '2026-07-29 07:38:42'),
(26, 18, 'Ketua Basecamp', 'Informasi & SAR', '0812-3456-7890', '2026-07-29 07:41:01'),
(27, 19, 'Ketua Basecamp', 'Informasi & SAR', '0812-3456-7890', '2026-07-29 07:44:35'),
(28, 20, 'Ketua Basecamp', 'Informasi & SAR', '0812-3456-7890', '2026-07-29 07:49:28'),
(29, 2, 'Pak Cecep Herman', 'Pengelola Basecamp Cibodas TNGGP', '0812-9988-7766', '2026-08-11 05:53:06'),
(30, 2, 'Mas Ceppy Local', 'Guide & Porter Tektok Pangrango', '0856-1122-3344', '2026-08-11 05:53:06'),
(31, 2, 'Tim Medis Cibodas Rescue', 'Penanganan Emergency & P3K', '0813-4455-6677', '2026-08-11 05:53:06'),
(32, 4, 'Ketua Basecamp', 'Informasi & SAR', '0812-3456-7890', '2026-08-11 05:53:51'),
(33, 1, 'Pak Ujang Sugandi', 'Pengelola Basecamp Gunung Putri', '0815-5566-6777', '2026-08-11 05:55:34'),
(34, 1, 'Kang Asep Saepudin', 'Koordinator Porter Senior Putri', '0857-1122-3344', '2026-08-11 05:55:34'),
(35, 1, 'Mas Bowo Kuya', 'Leader Crew Porter Kuya Journey', '0821-9876-5432', '2026-08-11 05:55:34'),
(36, 21, 'Ketua Basecamp', 'Informasi & SAR', '0812-3456-7890', '2026-11-25 09:20:40'),
(42, 22, 'Ketua Basecamp', 'Informasi & SAR', '0812-3456-7890', '2026-11-25 10:02:51'),
(43, 24, 'Ketua Basecamp', 'Informasi & SAR', '0812-3456-7890', '2026-11-25 10:03:17'),
(44, 23, 'Ketua Basecamp', 'Informasi & SAR', '0812-3456-7890', '2026-11-25 10:05:10'),
(45, 25, 'Ketua Basecamp', 'Informasi & SAR', '0812-3456-7890', '2026-11-25 10:09:25');

-- --------------------------------------------------------

--
-- Table structure for table `kriteria`
--

CREATE TABLE `kriteria` (
  `id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `kode` varchar(10) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jenis` enum('benefit','cost') NOT NULL,
  `bobot` float NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kriteria`
--

INSERT INTO `kriteria` (`id`, `users_id`, `kode`, `nama`, `jenis`, `bobot`, `updated_at`) VALUES
(1, 0, 'C1', 'Harga Paket', 'cost', 0.2, '2026-07-25 03:38:09'),
(2, 0, 'C2', 'Ketinggian Gunung', 'benefit', 0.1, '2026-07-25 03:38:09'),
(3, 0, 'C3', 'Kesulitan Medan', 'cost', 0.3, '2026-07-25 03:38:09'),
(4, 0, 'C4', 'Kelengkapan Fasilitas', 'benefit', 0.25, '2026-07-25 03:38:09'),
(5, 0, 'C5', 'Estimasi Waktu Tempuh', 'cost', 0.15, '2026-07-25 03:38:09');

-- --------------------------------------------------------

--
-- Table structure for table `laporan_terpilih`
--

CREATE TABLE `laporan_terpilih` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `paket_id` int(11) NOT NULL,
  `skor_moora` double DEFAULT NULL,
  `kategori_pilihan` varchar(50) NOT NULL,
  `catatan_fisik` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laporan_terpilih`
--

INSERT INTO `laporan_terpilih` (`id`, `user_id`, `paket_id`, `skor_moora`, `kategori_pilihan`, `catatan_fisik`, `created_at`) VALUES
(1, 5, 5, -0.28771359142804, 'Camping', '', '2026-07-24 07:40:21'),
(2, 5, 5, -0.28771359142804, 'Camping', '', '2026-07-24 07:52:25'),
(3, 5, 6, 0.045180294277839, 'Camping', '', '2026-07-25 07:33:17'),
(4, 5, 6, 0.36110783838476, 'Camping', '', '2026-07-25 07:37:18'),
(5, 5, 17, 0.023853370652788, 'Camping', '', '2026-07-30 08:41:19');

-- --------------------------------------------------------

--
-- Table structure for table `paket_trip`
--

CREATE TABLE `paket_trip` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `nama_paket` varchar(150) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `gunung` varchar(100) NOT NULL,
  `kategori` enum('Tektok','Camping') NOT NULL DEFAULT 'Camping',
  `c1_harga` double NOT NULL,
  `c2_ketinggian` int(11) NOT NULL,
  `c3_medan` int(11) NOT NULL,
  `c4_fasilitas` varchar(255) NOT NULL,
  `c5_waktu_tempuh` int(11) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status_kuota` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `paket_trip`
--

INSERT INTO `paket_trip` (`id`, `vendor_id`, `nama_paket`, `deskripsi`, `gunung`, `kategori`, `c1_harga`, `c2_ketinggian`, `c3_medan`, `c4_fasilitas`, `c5_waktu_tempuh`, `gambar`, `created_at`, `status_kuota`) VALUES
(1, 2, 'Paket Eksplorasi Gunung Gede via Putri (Camping 2D1N)', 'Pendakian klasik Gunung Gede 2958 mdpl via Jalur Gunung Putri. Termasuk kemah hangat di padang edelweis Alun-Alun Surya Kencana, tenda dome kelompok, 3x konsumsi hangat, porter logistik, dan P3K.', 'Gede', 'Camping', 750000, 2958, 2, 'F1,F3,F4,F6,F8,F10,F11,F12', 6, 'uploads/paket/1786427734_6a7ab956cabb9.jpeg', '2026-07-24 05:40:37', 'Tersedia'),
(2, 3, 'Speed Tektok Gunung Pangrango via Cibodas (Tektok 1D)', 'Pendakian tektok tercepat pergi-pulang 1 hari menembus Puncak Pangrango 3019 mdpl dan Lembah Mandalawangi via Cibodas. Melintasi Telaga Biru, Air Terjun Cibeureum, Air Panas, dan Kandang Badak.', 'Pangrango', 'Tektok', 480000, 3019, 3, 'F4,F5,F9,F10,F12', 10, 'uploads/paket/1786427586_6a7ab8c20691c.jpeg', '2026-07-24 05:40:37', 'Tersedia'),
(4, 3, 'Sunrise Tektok Trip Gunung Prau Dieng', 'Pendakian tektok santai menyaksikan Golden Sunrise Asia Tenggara di Gunung Prau 2565 mdpl.', 'Prau', 'Tektok', 420000, 2565, 1, 'F4,F9,F10,F12', 3, 'uploads/paket/1786427631_6a7ab8ef281cb.jpeg', '2026-07-24 05:40:37', 'Tersedia'),
(5, 2, 'Eksplorasi Merbabu via Selo', 'Trip 3 Hari 2 malam di gunung merbabu, menikmati savana dengan sunrise sembari meminum es buah', 'Merbabu', 'Camping', 550000, 3241, 2, 'F1,F2,F3,F6,F7,F9,F10,F11,F12', 6, 'uploads/paket/1784878605_6a63160d1d1cc.webp', '2026-07-24 07:36:45', 'Tersedia'),
(6, 2, 'Papandayan Explore', '', 'Papandayan', 'Camping', 350000, 2665, 2, 'F1,F2,F3,F9,F11', 3, 'uploads/paket/1784953629_6a643b1d8e8e9.webp', '2026-07-25 04:27:09', 'Tersedia'),
(7, 2, 'Ekspolrasi Gunung Guntur via Cikahuripan', 'Menikmati Puncak 4 Gunung Guntur', 'Guntur', 'Camping', 325000, 2249, 2, 'F1,F2,F3,F6,F7,F11', 3, 'uploads/paket/1785223518_6a68595ed8540.jpeg', '2026-07-28 07:25:18', 'Tersedia'),
(8, 2, 'Eksplore Gunung Sindoro via Kledung', 'Gunung sindoro mepo Cianjur Terminal Pasihr hayam', 'Sindoro', 'Camping', 580000, 3153, 2, 'F1,F6,F7,F9,F10,F11,F12', 6, 'uploads/paket/1785290470_6a695ee6639de.webp', '2026-07-29 02:01:10', 'Tersedia'),
(9, 13, 'Gunung Gede via Putri 2D 1N 26-27 JULI 2026', 'Rasakan sensasi pendakian klasik Jawa Barat melintasi Telaga Biru, Air Terjun Pemandian Air Panas, hingga bermalam di Alun-Alun Surya Kencana yang dipenuhi bunga Edelweiss. Dengan tingkat kesulitan medan sedang, paket ini menawarkan keseimbangan sempurna antara petualangan hutan tropis dan keindahan lanskap pegunungan', 'Gede', 'Camping', 375000, 2958, 2, 'F1,F5,F6,F7,F9,F11,F12', 7, 'uploads/paket/1785302941_6a698f9d054c1.jpeg', '2026-07-29 05:29:01', 'Tersedia'),
(10, 13, 'Gunung Ciremai via  Apuy 30-31 Juli 2026 2d 1n', 'Alternatif favorit menuju atap Jawa Barat dengan trek yang lebih bersahabat dibandingkan jalur lainnya. Meskipun berstatus Medan Sedang-Sulit, jalur Apuy menawarkan pemandangan tebing yang eksotis dan waktu tempuh yang sedikit lebih singkat. Paket ini cocok untuk pendaki berpengalaman yang ingin memangkas waktu tanpa mengorbankan panorama memukau di puncaknya.', 'Ciremai', 'Camping', 555000, 3078, 2, 'F1,F5,F6,F7,F9,F11,F12', 7, 'uploads/paket/1785303537_6a6991f19716b.jpeg', '2026-07-29 05:38:57', 'Tersedia'),
(11, 13, 'Gunung Merbabu 01-02 Agustus 2d 1n via selo', 'Jelajahi surga sabana terindah di Pulau Jawa dengan trek tanah yang dinamis. Perjalanan menuju area camp Sabana 2 menyuguhkan latar belakang kemegahan Gunung Merapi yang tiada duanya. Destinasi favorit level menengah', 'Merbabu', 'Camping', 650000, 3142, 2, 'F1,F5,F6,F7,F9,F11,F12', 6, 'uploads/paket/1785303667_6a69927398dba.webp', '2026-07-29 05:41:07', 'Tersedia'),
(12, 13, 'Prau via Patakbanten 2D 1N 03-04 Agustus 2026 Mepo Cianjur Terminal Rawabango', 'Nikmati pesona Golden Sunrise terbaik se-Asia Tenggara. Jalur berupa undakan tertata yang sangat ramah bagi pendaki pemula ini akan mengantarkan Anda ke area camp sabana yang luas dalam waktu singkat. Pilihan tepat untuk pelarian santai di akhir pekan.', 'Prau', 'Camping', 550000, 2568, 1, 'F1,F5,F7,F8,F9,F11,F12', 3, 'uploads/paket/1785303840_6a69932043f0a.jpeg', '2026-07-29 05:44:00', 'Tersedia'),
(13, 13, 'Sumbing via  Gajah Mungkur 2D 1N 04-05 Agustus Mepo Cianjur Ramayana', 'Ekspedisi menantang menuju atap tertinggi kedua di Jawa Tengah. Bersiaplah menghadapi tanjakan ekstrem dan bebatuan lepas menuju puncak. Paket ini dikhususkan bagi pendaki berpengalaman yang mencari pemandangan lautan awan epik dengan bayaran keringat maksimal', 'Sumbing', 'Camping', 350000, 3371, 3, 'F1,F6,F9,F11,F12', 7, 'uploads/paket/1785304042_6a6993ea0bd68.jpeg', '2026-07-29 05:47:22', 'Tersedia'),
(14, 13, 'Gunung Sindoro via Kledung 05-06 Agustus 2026 2D 1N Mepo KARANGTENGAH BOJONG', 'Taklukkan kembaran Gunung Sumbing melalui trek tanah yang terus menanjak konstan. Karakteristik medannya akan sangat menguji ketahanan napas Anda sebelum akhirnya disuguhi keindahan kawah aktif dan lautan awan di puncaknya.', 'Sindoro', 'Camping', 400000, 3153, 2, 'F1,F5,F6,F7,F8,F9,F11,F12', 6, 'uploads/paket/1785304190_6a69947eb7d38.webp', '2026-07-29 05:49:50', 'Tersedia'),
(15, 13, 'Tektok Gunung Gede 07-08 Agustus 2026', 'Tektok sehat gunung gede', 'Gede', 'Tektok', 250000, 2958, 2, 'F5,F6,F9,F11,F12', 5, 'uploads/paket/1785304328_6a699508e82f1.jpeg', '2026-07-29 05:52:08', 'Tersedia'),
(16, 3, 'Eksplore Gunung Cikuray Via Tapak Gerot 2D 1N Mepo Cijedil Al-Albahjah 7-8 Agustus 2026', 'Dikenal dengan julukan jalur \'tanpa bonus\', tanjakan akar yang rapat akan terus membakar kalori dan menyiksa lutut Anda. Namun, pengorbanan itu akan diganjar oleh samudera awan paling spektakuler di puncak kerucut sempurnanya.', 'Cikuray', 'Camping', 450000, 2821, 3, 'F1,F5,F9,F11,F12', 6, 'uploads/paket/1785307421_6a69a11d9453c.jpeg', '2026-07-29 06:43:41', 'Tersedia'),
(17, 3, 'Camping Ceria Gunung Papandayan via Camp David 26–27 September 2026 2D 1N Mepo Cianjur Tugu Lampu Gentur', 'Paket camping ceria ramah pemula yang menyajikan lanskap kawah aktif eksotis dan Hutan Mati. Trek yang dominan landai menjadikan destinasi ini sangat aman untuk pemanasan fisik dan pendakian santai bersama rekan.', 'Papandayan', 'Camping', 300000, 2665, 1, 'F1,F4,F10,F11,F12', 2, 'uploads/paket/1785309604_6a69a9a448e53.jpeg', '2026-07-29 07:20:04', 'Tersedia'),
(18, 3, 'Pesona Sabana Gunung Merbabu via Selo 12–13 September 2026 2D 1N Mepo Cianjur Ramayana', 'Jelajahi surga sabana terindah di Pulau Jawa dengan trek tanah yang dinamis. Perjalanan menuju area camp Sabana 2 menyuguhkan latar belakang kemegahan Gunung Merapi yang tiada duanya. Destinasi favorit level menengah.', 'Merbabu', 'Camping', 385000, 3142, 2, 'F1,F4,F6,F10,F11,F12', 6, 'uploads/paket/1785310861_6a69ae8d8ce78.webp', '2026-07-29 07:41:01', 'Tersedia'),
(19, 3, 'Atap Jawa Barat Gunung Ciremai via Apuy 19–20 Agustus 2026 2D 1N Mepo Cianjur Terminal Rawabango', 'Alternatif favorit menuju atap Jawa Barat dengan trek yang bersahabat namun tetap menantang. Paket ini menawarkan pemandangan tebing yang eksotis dan sangat cocok bagi Anda yang mencari pengalaman pendakian intens di tanah Pasundan.', 'Cireamai', 'Camping', 450000, 3078, 2, 'F1,F4,F6,F9,F10,F12', 7, 'uploads/paket/1785311075_6a69af63e78e3.jpg', '2026-07-29 07:44:35', 'Tersedia'),
(20, 3, 'Penaklukan Atap Jawa Tengah Gunung Slamet via Bambangan 14–15 Agustus 2026 2D 1N Mepo Cianjur Ramayana', 'Penaklukan atap tertinggi Jawa Tengah tanpa kompromi. Trek panjang yang menguras tenaga ini didominasi tanah berdebu di hutan rapat, menguji kekuatan kaki Anda hingga batas maksimal demi menggapai Puncak Surono.', 'Slamet', 'Camping', 315000, 2958, 2, 'F1,F4,F9,F10,F11,F12', 6, 'uploads/paket/1785311368_6a69b0885e8f6.jpeg', '2026-07-29 07:49:28', 'Tersedia'),
(21, 13, 'Eksplorasi Sunan Ibu 19-20 september', 'Menjelajahi Keindahan golden sunrise sunan ibu', 'Sunan ibu', 'Tektok', 200000, 2343, 1, 'F4,F6,F7,F8,F9,F10,F12', 1, 'uploads/paket/1795598440_6b06a868c5344.jpeg', '2026-11-25 09:20:40', 'Tersedia'),
(22, 14, 'Gunung Artapela', 'Eksplore gunung artapela bandung', 'Artapela', 'Camping', 210000, 2183, 2, 'F1,F2,F3,F4,F10,F11', 3, 'uploads/paket/1795599062_6b06aad603b80.jpeg', '2026-11-25 09:31:02', 'Tersedia'),
(23, 14, 'Eksplorasi Gunung cikuray via tapak gerot 20-22 Desember', 'Menjelajahi lautan awan cikuray via tapak gerot', 'Gunung Cikuray', 'Camping', 300000, 2821, 2, 'F1,F4,F5,F6,F9,F10,F11', 6, 'uploads/paket/1795599403_6b06ac2bcafc6.jpeg', '2026-11-25 09:36:43', 'Tersedia'),
(24, 14, 'Camping Ceria Gunung kasur 20-21 Oktober', 'Camping di gunung kasur', 'Gunung Kasur', 'Camping', 150000, 1060, 1, 'F1,F4,F9,F11', 1, 'uploads/paket/1795599810_6b06adc2be289.jpeg', '2026-11-25 09:43:30', 'Tersedia'),
(25, 14, 'Eksplorasi Gunung Guntur 25-27 desember mepo cibadak', 'Mencari keindahan dari beberapa puncak gunung guntur', 'Gunung Guntur', 'Camping', 300000, 2249, 2, 'F1,F4,F9,F10,F11', 1, 'uploads/paket/1795601365_6b06b3d55a081.jpeg', '2026-11-25 10:09:25', 'Tersedia');

-- --------------------------------------------------------

--
-- Table structure for table `rute_segmentasi`
--

CREATE TABLE `rute_segmentasi` (
  `id` int(11) NOT NULL,
  `paket_id` int(11) NOT NULL,
  `urutan` int(11) NOT NULL DEFAULT 1,
  `pos_asal` varchar(100) NOT NULL,
  `pos_tujuan` varchar(100) NOT NULL,
  `waktu_tempuh_menit` int(11) NOT NULL,
  `elevasi_gain` int(11) NOT NULL,
  `kondisi_medan` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rute_segmentasi`
--

INSERT INTO `rute_segmentasi` (`id`, `paket_id`, `urutan`, `pos_asal`, `pos_tujuan`, `waktu_tempuh_menit`, `elevasi_gain`, `kondisi_medan`, `created_at`) VALUES
(11, 5, 1, 'Basecamp', 'Pos 1', 60, 250, 'Jalur tanah padat landai', '2026-07-24 07:36:45'),
(12, 5, 2, 'Pos 1', 'Pos 2', 60, 250, 'agak sedikit menanjak jalur akar', '2026-07-24 07:36:45'),
(13, 5, 3, 'Pos 2 ', 'Pos 3', 60, 250, 'Jalur pasir ', '2026-07-24 07:36:45'),
(14, 5, 4, 'Pos 3', 'Savana 1', 120, 330, 'Jalur menanjak penuh debu pasir kalau musim kemarau', '2026-07-24 07:36:45'),
(16, 6, 1, 'Basecamp', 'Pos 1', 60, 250, 'Jalur tanah padat landai', '2026-07-25 04:27:40'),
(20, 7, 1, 'Basecamp', 'Pos 1', 60, 250, 'Jalur tanah padat landai', '2026-07-28 13:54:36'),
(21, 8, 1, 'Basecamp', 'Pos 1', 60, 250, 'Jalur tanah padat landai', '2026-07-29 02:01:10'),
(25, 9, 1, 'Basecamp', 'Pos 1', 60, 250, 'Jalur tanah padat agak menanjak', '2026-07-29 05:30:37'),
(26, 9, 2, 'pos 1 ', 'pos 3  lawang saketeng', 180, 400, 'melewati jalur yang menanjak dan relatif lebih terbuka', '2026-07-29 05:30:37'),
(27, 9, 3, 'pos 3 lawang saketeng', 'pos 4', 60, 250, '', '2026-07-29 05:30:37'),
(28, 10, 1, 'Basecamp', 'Pos 1', 60, 250, 'Jalur tanah padat landai', '2026-07-29 05:38:57'),
(29, 11, 1, 'Basecamp', 'Pos 1', 60, 250, 'Jalur tanah padat landai', '2026-07-29 05:41:07'),
(30, 12, 1, 'Basecamp', 'Pos 1', 60, 250, 'Jalur tanah padat landai', '2026-07-29 05:44:00'),
(31, 13, 1, 'Basecamp', 'Pos 1', 60, 250, 'Jalur tanah padat landai', '2026-07-29 05:47:22'),
(32, 14, 1, 'Basecamp', 'Pos 1', 60, 250, 'Jalur tanah padat landai', '2026-07-29 05:49:50'),
(33, 15, 1, 'Basecamp', 'Pos 1', 60, 250, 'Jalur tanah padat landai', '2026-07-29 05:52:08'),
(34, 16, 1, 'Basecamp', 'Pos 1', 60, 250, 'Jalur tanah padat landai', '2026-07-29 06:43:41'),
(36, 17, 1, 'Basecamp', 'Pos 1', 60, 250, 'Jalur tanah padat landai', '2026-07-29 07:38:42'),
(37, 18, 1, 'Basecamp', 'Pos 1', 60, 250, 'Jalur tanah padat landai', '2026-07-29 07:41:01'),
(38, 19, 1, 'Basecamp', 'Pos 1', 60, 250, 'Jalur tanah padat landai', '2026-07-29 07:44:35'),
(39, 20, 1, 'Basecamp', 'Pos 1', 60, 250, 'Jalur tanah padat landai', '2026-07-29 07:49:28'),
(40, 2, 1, 'Basecamp Cibodas (1.425 mdpl)', 'Pos Telaga Biru', 45, 150, 'Jalur batu tertata rapi berlandai di bawah naungan kanopi hutan tropis', '2026-08-11 05:53:06'),
(41, 2, 2, 'Pos Telaga Biru', 'Air Terjun Cibeureum', 45, 200, 'Anak tangga batu alami & jembatan kayu lembap di pinggir aliran air', '2026-08-11 05:53:06'),
(42, 2, 3, 'Air Terjun Cibeureum', 'Pos Air Panas & Kandang Badak', 150, 750, 'Tanjakan batu licin melintasi aliran air panas & tanah liat menanjak terjal', '2026-08-11 05:53:06'),
(43, 2, 4, 'Kandang Badak (2.400 mdpl)', 'Puncak Gunung Pangrango (3.019 mdpl)', 120, 619, 'Tanjakan terjal merayap di antara jalinan akar pohon tua & tanah liat basah', '2026-08-11 05:53:06'),
(44, 2, 5, 'Puncak Pangrango', 'Lembah Mandalawangi (3.000 mdpl)', 15, 50, 'Jalur tanah datar menurun tipis menuju padang edelweis & hutan lumut', '2026-08-11 05:53:06'),
(45, 4, 1, 'Basecamp', 'Pos 1', 60, 250, 'Jalur tanah padat landai', '2026-08-11 05:53:51'),
(46, 1, 1, 'Basecamp Putri (1.450 mdpl)', 'Pos 1 Informasi Tanah Merah', 60, 250, 'Jalur tanah padat berlandai melintasi pemukiman & ladang sayur warga', '2026-08-11 05:55:34'),
(47, 1, 2, 'Pos 1 Tanah Merah', 'Pos 2 Legok Leunyay', 90, 350, 'Tanjakan vegetasi rapat, tanah merah berakar pohon & tangga tanah alami', '2026-08-11 05:55:34'),
(48, 1, 3, 'Pos 2 Legok Leunyay', 'Pos 3 Buntut Semar', 90, 400, 'Tanjakan batu alam terjal & tangga tanah alami yang konsisten menanjak', '2026-08-11 05:55:34'),
(49, 1, 4, 'Pos 3 Buntut Semar', 'Alun-Alun Surya Kencana (2.750 mdpl)', 120, 650, 'Tanjakan batu terjal bervegetasi rapat hutan edelweis & angin bertiup kencang', '2026-08-11 05:55:34'),
(50, 1, 5, 'Alun-Alun Surya Kencana', 'Puncak Gunung Gede (2.958 mdpl)', 30, 208, 'Jalur berbatu terbuka menanjak menuju bibir kawah aktif Gunung Gede', '2026-08-11 05:55:34'),
(51, 21, 1, 'Basecamp', 'Pos 1', 60, 250, 'Jalur tanah padat landai', '2026-11-25 09:20:40'),
(57, 22, 1, 'Basecamp', 'Pos 1', 60, 250, 'Jalur tanah padat landai', '2026-11-25 10:02:51'),
(58, 22, 2, 'pos 1 tapang leuweung', 'pos 2 legok saketeng', 60, 250, 'jalur batu', '2026-11-25 10:02:51'),
(59, 22, 3, 'pos 2 legok saketeng', 'camp area ', 60, 250, 'jalan rerumputan', '2026-11-25 10:02:51'),
(60, 24, 1, 'Basecamp', 'camp area', 60, 250, 'agak menanjak', '2026-11-25 10:03:17'),
(61, 23, 1, 'Basecamp', 'Pos 1', 60, 250, 'Jalur tanah padat landai', '2026-11-25 10:05:10'),
(62, 23, 2, 'pos 1 ', 'pos 2', 120, 250, 'jalur menanjak tanah', '2026-11-25 10:05:10'),
(63, 23, 3, 'pos 2 ', 'pos 3', 60, 250, 'jalur menanjak tanah tetap', '2026-11-25 10:05:10'),
(64, 23, 4, 'pos 3', 'camp area', 60, 250, 'jalur lebat vegetasi', '2026-11-25 10:05:10'),
(65, 25, 1, 'Basecamp', 'Pos 1', 60, 250, 'Jalur tanah padat landai', '2026-11-25 10:09:25');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `nama_pemilik` varchar(150) DEFAULT NULL,
  `nomor_kontak` varchar(50) DEFAULT NULL,
  `nib` varchar(50) DEFAULT NULL,
  `sertifikat_path` varchar(255) DEFAULT NULL,
  `role` enum('admin','vendor','pendaki') NOT NULL,
  `status_akun` enum('pending','approved','rejected') NOT NULL DEFAULT 'approved',
  `pesan_notifikasi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `fullname`, `nama_pemilik`, `nomor_kontak`, `nib`, `sertifikat_path`, `role`, `status_akun`, `pesan_notifikasi`, `created_at`) VALUES
(1, 'admin', '$2y$10$iWTHiRT3z4cNu0ReUZUcD.tL1JwM2a27IByOe3FFNjxnjkbh.Jftm', 'Administrator Utama SPK', NULL, NULL, NULL, NULL, 'admin', 'approved', NULL, '2026-07-24 05:40:37'),
(2, 'vendor_kuya', '$2y$10$jjseU5tjPRqDDoA.rNSAD.DYdh4ZUq5NjGsgmkp4WTqWmTxTvIKIS', 'Kuya Journey Indonesiaa', NULL, NULL, NULL, NULL, 'vendor', 'approved', NULL, '2026-07-24 05:40:37'),
(3, 'vendor_cfd', '$2y$10$jjseU5tjPRqDDoA.rNSAD.DYdh4ZUq5NjGsgmkp4WTqWmTxTvIKIS', 'CFD Traveller', NULL, NULL, NULL, NULL, 'vendor', 'approved', NULL, '2026-07-24 05:40:37'),
(4, 'pendaki_sejati', '$2y$10$eO0V4h/W8N5e3d7F5k9Sde9Y3q7pW2k/4H8j7Z3s5m2e1f4g5h6i7', 'Giri Wira', NULL, NULL, NULL, NULL, 'pendaki', 'approved', NULL, '2026-07-24 05:40:37'),
(5, 'dika', '$2y$10$EQ0oZyf/lWw553LgCxPTwuJr1AUZlp9eae8aG8mw9YtMm7gQLZIiy', 'dika satria', NULL, NULL, NULL, NULL, 'pendaki', 'approved', NULL, '2026-07-24 07:38:20'),
(8, 'markicabs_journey', '$2y$10$iH3HLcrsnyThUyy1hBDioeiT579dbTFq/6v2RTVKmNeU.P3wCs3sa', 'MARKICABS', 'DZAWIN', '088178901234', '1234512345123', 'uploads/dokumen_vendor/sertifikat_1784958173_6a644cdd257f4.png', 'vendor', 'approved', 'akun anda berhasil diverifikasi oleh admin, anda bisa masuk sekarang', '2026-07-25 05:42:53'),
(9, 'cacicu_traveller', '$2y$10$9RuDEfVzG.0bIdCcvUWaL.aK.KCQmCxk9kaWnEcIyzzvPvZf/esDa', 'Serrr Traveller', 'windah', '087822341927', '0988900988909', 'uploads/dokumen_vendor/sertifikat_1784960630_6a645676b06c6.png', 'vendor', 'approved', 'akun anda berhasil diverifikasi oleh admin, anda bisa masuk sekarang', '2026-07-25 06:23:50'),
(10, 'anya', '$2y$10$sGB3hsFTdiyEEInUhGF0N.vPLxlR6E6ht750pEjT65BJzTV6w6Y1u', 'anya zahira', NULL, NULL, NULL, NULL, 'pendaki', 'approved', NULL, '2026-07-28 06:33:28'),
(11, 'vanya', '$2y$10$tR7tCEPuLZjcviBN2j7/jOTKGDgjwoTmm.BBWfpBDn75AeXdNLsEa', 'vanya_zahira', NULL, NULL, NULL, NULL, 'pendaki', 'approved', NULL, '2026-07-28 06:41:17'),
(12, 'gas_uhuy', '$2y$10$zuVT38p2qlxbdI9FgvMyL.S8LGIxUI7cunHWLWGSXwKoijABlokGa', 'Gas Uhuy', 'Zidan', '0871223451234', '0987654321233', 'uploads/dokumen_vendor/sertifikat_1785221131_6a68500b8d3b0.png', 'vendor', 'approved', 'akun anda berhasil diverifikasi oleh admin, anda bisa masuk sekarang', '2026-07-28 06:45:31'),
(13, 'vendor_pta', '$2y$10$K7Oc.khKvFJn0IPPxcYsHu89fKVhgOBmOLf80axZZGvnaByBsT4w6', 'PTA CIANJUR', 'Mufid', '087822114455', '1234567812345', 'uploads/dokumen_vendor/sertifikat_1785301905_6a698b914c8a4.png', 'vendor', 'approved', 'akun anda berhasil diverifikasi oleh admin, anda bisa masuk sekarang', '2026-07-29 05:11:45'),
(14, 'irahadeui', '$2y$10$2/4.hyK8fvc7dGL80iVKSuJl8eqrFH0dp5KHBG94RbKfzVNx0b6OS', 'irahadeui_trip', 'Saddam Gibran', '087123476122', '1423412345', 'uploads/dokumen_vendor/sertifikat_1795597906_6b06a652387cb.png', 'vendor', 'approved', 'akun anda berhasil diverifikasi oleh admin, anda bisa masuk sekarang', '2026-11-25 09:11:46');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `edukasi`
--
ALTER TABLE `edukasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_id` (`vendor_id`),
  ADD KEY `paket_id` (`paket_id`);

--
-- Indexes for table `kontak_darurat`
--
ALTER TABLE `kontak_darurat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `paket_id` (`paket_id`);

--
-- Indexes for table `kriteria`
--
ALTER TABLE `kriteria`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode` (`kode`);

--
-- Indexes for table `laporan_terpilih`
--
ALTER TABLE `laporan_terpilih`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `paket_id` (`paket_id`);

--
-- Indexes for table `paket_trip`
--
ALTER TABLE `paket_trip`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- Indexes for table `rute_segmentasi`
--
ALTER TABLE `rute_segmentasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `paket_id` (`paket_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `edukasi`
--
ALTER TABLE `edukasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `kontak_darurat`
--
ALTER TABLE `kontak_darurat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `kriteria`
--
ALTER TABLE `kriteria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `laporan_terpilih`
--
ALTER TABLE `laporan_terpilih`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `paket_trip`
--
ALTER TABLE `paket_trip`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `rute_segmentasi`
--
ALTER TABLE `rute_segmentasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `edukasi`
--
ALTER TABLE `edukasi`
  ADD CONSTRAINT `edukasi_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `edukasi_ibfk_2` FOREIGN KEY (`paket_id`) REFERENCES `paket_trip` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `kontak_darurat`
--
ALTER TABLE `kontak_darurat`
  ADD CONSTRAINT `kontak_darurat_ibfk_1` FOREIGN KEY (`paket_id`) REFERENCES `paket_trip` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `laporan_terpilih`
--
ALTER TABLE `laporan_terpilih`
  ADD CONSTRAINT `laporan_terpilih_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `laporan_terpilih_ibfk_2` FOREIGN KEY (`paket_id`) REFERENCES `paket_trip` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `paket_trip`
--
ALTER TABLE `paket_trip`
  ADD CONSTRAINT `paket_trip_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rute_segmentasi`
--
ALTER TABLE `rute_segmentasi`
  ADD CONSTRAINT `rute_segmentasi_ibfk_1` FOREIGN KEY (`paket_id`) REFERENCES `paket_trip` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
