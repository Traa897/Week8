-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 07 Nov 2025 pada 08.00
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `motor_modif_shop`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Knalpot', 'Knalpot racing dan standar', '2025-10-07 03:41:04'),
(2, 'Velg Racing', 'Biar ganteng dibawah', '2025-10-07 03:41:04'),
(3, 'Ban', 'Ban motor berbagai merk', '2025-10-07 03:41:04'),
(4, 'Mesin Motor', 'Mesin Motor Racing', '2025-10-07 03:41:04'),
(5, 'Spion', 'Spion motor berbagai model', '2025-10-07 03:41:04'),
(6, 'Pengereman', 'buat motor mu aman', '2025-10-07 11:58:50'),
(7, 'Lampu Biled', 'kasih terang motor mu itu', '2025-10-07 12:02:50'),
(8, 'Suspensi Shock', 'biar empuk itu motor mu ces', '2025-10-07 12:07:19'),
(9, 'Body Motor', 'biar cakep dibawanya', '2025-10-07 12:08:23'),
(10, 'Velg BSD', '', '2025-11-01 16:28:56'),
(12, 'Knalpot sajaro', '', '2025-11-01 17:54:17'),
(13, 'PAKAI TOKEN', 'Kategori ini dibuat dengan CSRF token yang valid', '2025-11-01 18:06:31');

-- --------------------------------------------------------

--
-- Struktur dari tabel `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `customers`
--

INSERT INTO `customers` (`id`, `name`, `phone`, `email`, `address`, `city`, `created_at`) VALUES
(1, 'Elsya Nur A.H', '+62 821-1106-0362', '10241026@student.itk.ac.id', 'Jl. Soekarno Hatta KM 15', 'Balikpapan', '2025-10-07 03:41:04'),
(2, 'Eagan Ferdianto', '0888888888888', '10241025@student.itk.ac.id', 'di regency rumahnya', 'balikpapan', '2025-10-07 03:41:04'),
(3, 'patra ananda', '081351319657', 'patran05534@gmail.com', 'jl.soekarno.hatta km 21 rt 41', 'balikpapan utara', '2025-10-07 11:50:03'),
(5, 'Ikhsan sabrianto', '08345654387', 'Iksan@cantikgaming.com', 'di kota rumahnya dia', 'balikpapan', '2025-10-07 11:53:51'),
(6, 'Nabil', '083456765432', 'nabil@gmail.com', 'sepinggan dekat AU', 'balikpapan', '2025-10-07 11:54:31'),
(7, 'aji', '08843676543', 'aji22@amor.com', 'orang samarinda keknya', 'samarinda', '2025-10-07 11:55:04'),
(8, 'Fitri Putra', '088642114381', 'fitri.putra@example.com', 'Jl. Putra No. 93', 'Samarinda', '2025-10-31 14:19:46'),
(9, 'Dedi Kurniawan', '084313629109', 'dedi.kurniawan@example.com', 'Jl. Kurniawan No. 44', 'Makassar', '2025-10-31 14:19:46'),
(10, 'Ahmad Setiawan', '084723317795', 'ahmad.setiawan@example.com', 'Jl. Setiawan No. 89', 'Surabaya', '2025-10-31 14:19:46'),
(11, 'Budi Wijaya', '082763145693', 'budi.wijaya@example.com', 'Jl. Wijaya No. 28', 'Bandung', '2025-10-31 14:19:46'),
(12, 'Ahmad Pratama', '088390168501', 'ahmad.pratama@example.com', 'Jl. Pratama No. 178', 'Medan', '2025-10-31 14:19:46'),
(13, 'Indra Kusuma', '082186099012', 'indra.kusuma@example.com', 'Jl. Kusuma No. 96', 'Bandung', '2025-10-31 14:19:46'),
(14, 'Fitri Wijaya', '089168898092', 'fitri.wijaya@example.com', 'Jl. Wijaya No. 151', 'Bandung', '2025-10-31 14:19:46'),
(15, 'Ahmad Wijaya', '084029228675', 'ahmad.wijaya@example.com', 'Jl. Wijaya No. 133', 'Semarang', '2025-10-31 14:19:46'),
(16, 'Joko Setiawan', '082508919336', 'joko.setiawan@example.com', 'Jl. Setiawan No. 51', 'Samarinda', '2025-10-31 14:19:46'),
(19, 'Citra Kusuma', '088670993813', 'citra.kusuma@example.com', 'Jl. Kusuma No. 119', 'Jakarta', '2025-10-31 14:19:46'),
(22, 'Fitri Kusuma', '082148921280', 'fitri.kusuma@example.com', 'Jl. Kusuma No. 134', 'Surabaya', '2025-10-31 14:19:46'),
(23, 'Joko Kusuma', '087007461506', 'joko.kusuma@example.com', 'Jl. Kusuma No. 197', 'Semarang', '2025-10-31 14:19:46'),
(24, 'Budi Santoso', '084122720891', 'budi.santoso@example.com', 'Jl. Santoso No. 192', 'Balikpapan', '2025-10-31 14:19:46'),
(25, 'Gita Rahman', '087944468399', 'gita.rahman@example.com', 'Jl. Rahman No. 199', 'Balikpapan', '2025-10-31 14:19:46'),
(26, 'Hadi Setiawann', '+62-891-0708-0498', 'hadi.setiawan@example.com', 'Jl. Setiawan No. 92', 'Makassar', '2025-10-31 14:19:46'),
(27, 'Joko Pratama', '085997548358', 'joko.pratama@example.com', 'Jl. Pratama No. 140', 'Medan', '2025-10-31 14:19:46'),
(28, 'Kasus', '+62-812-3342-23', 'palasa@gmail.com', 'Jl. Soekarno Hatta No.KM 15, Karang Joang, Kec. Balikpapan Utara, Kota Balikpapan, Kalimantan Timur 76127', 'Kota Balikpapan', '2025-10-31 14:28:13'),
(29, 'Kusikam', '+62-813-5423-6785', 'KUSIK@gmail.com', 'jl 12', 'Bandung', '2025-10-31 14:38:16'),
(31, 'Eka Pratama', '082544641166', 'eka.pratama@example.com', 'Jl. Pratama No. 128', 'Bandung', '2025-11-01 15:54:13'),
(32, 'Gita Santoso', '084131174001', 'gita.santoso@example.com', 'Jl. Santoso No. 111', 'Bandung', '2025-11-01 15:54:13'),
(34, 'Ahmad Putra', '089386661178', 'ahmad.putra@example.com', 'Jl. Putra No. 2', 'Surabaya', '2025-11-01 15:54:13'),
(35, 'Dedi Pratama', '082228290379', 'dedi.pratama@example.com', 'Jl. Pratama No. 130', 'Semarang', '2025-11-01 15:54:13'),
(36, 'Dedi Wijaya', '081639421719', 'dedi.wijaya@example.com', 'Jl. Wijaya No. 105', 'Samarinda', '2025-11-01 15:54:13'),
(37, 'Citra Hidayat', '081139818205', 'citra.hidayat@example.com', 'Jl. Hidayat No. 128', 'Jakarta', '2025-11-01 15:54:13'),
(38, 'Fitri Putra', '086326425834', 'fitri.putra@example.com', 'Jl. Putra No. 127', 'Samarinda', '2025-11-01 15:54:13'),
(39, 'Budi Kurniawan', '088542620447', 'budi.kurniawan@example.com', 'Jl. Kurniawan No. 9', 'Semarang', '2025-11-01 15:54:13'),
(40, 'Indra Santoso', '082849722846', 'indra.santoso@example.com', 'Jl. Santoso No. 132', 'Balikpapan', '2025-11-01 15:54:13'),
(41, 'Dedi Putra', '086902435980', 'dedi.putra@example.com', 'Jl. Putra No. 173', 'Balikpapan', '2025-11-01 15:54:13'),
(42, 'Joko Nugraha', '084885597795', 'joko.nugraha@example.com', 'Jl. Nugraha No. 10', 'Makassar', '2025-11-01 15:54:13'),
(43, 'Joko Putra', '082021219863', 'joko.putra@example.com', 'Jl. Putra No. 187', 'Makassar', '2025-11-01 15:54:13'),
(44, 'Gita Santoso', '084446723836', 'gita.santoso@example.com', 'Jl. Santoso No. 198', 'Medan', '2025-11-01 15:54:13'),
(45, 'Indra Kurniawan', '083374038781', 'indra.kurniawan@example.com', 'Jl. Kurniawan No. 110', 'Samarinda', '2025-11-01 15:54:13'),
(46, 'Gita Putra', '082715278035', 'gita.putra@example.com', 'Jl. Putra No. 33', 'Balikpapan', '2025-11-01 15:54:13'),
(47, 'Hadi Wijaya', '084661220863', 'hadi.wijaya@example.com', 'Jl. Wijaya No. 174', 'Bandung', '2025-11-01 15:54:13'),
(48, 'Budi Nugraha', '084744530027', 'budi.nugraha@example.com', 'Jl. Nugraha No. 13', 'Balikpapan', '2025-11-01 15:54:13'),
(49, 'Eka Rahman', '089542588407', 'eka.rahman@example.com', 'Jl. Rahman No. 24', 'Surabaya', '2025-11-01 15:54:13'),
(50, 'Dedi Nugraha', '081256868147', 'dedi.nugraha@example.com', 'Jl. Nugraha No. 135', 'Samarinda', '2025-11-01 15:54:13'),
(51, 'Dedy', '+62-813-4164-722', 'dedy@gmail.com', 'JL DADYHOME 23', 'Sunda', '2025-11-01 17:56:28');

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(200) NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(15,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `motor_type` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id`, `category_id`, `supplier_id`, `code`, `name`, `brand`, `description`, `price`, `stock`, `motor_type`, `image`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 3, 1, '438320', 'Diablo rosso ring 19', 'Pirreli', 'gacor di jalan licin dan dijalan panas lengket', 500000.00, 40, 'Vario 125', '', '2025-10-07 06:32:21', '2025-10-31 14:55:10', NULL),
(2, 3, 2, '9654', 'Diablo rosso ring 19', 'pirreli', 'Biar lengket itu di aspal', 600000.00, 60, 'Nmax', '', '2025-10-07 06:36:02', '2025-10-24 02:43:22', NULL),
(3, 9, 5, 'SPR3985', 'Body halus', 'Modifan', 'Body motor Aerox', 3000000.00, 0, 'Aerox', '', '2025-10-07 12:09:42', '2025-10-24 02:43:20', NULL),
(4, 6, 3, 'BR3467', 'Master Rem Corsa Costa', 'Brembo', 'biar ga senin kamis kamu ngerem cess', 140000000.00, 4, 'Aerox', '', '2025-10-07 12:27:00', '2025-10-24 02:43:18', NULL),
(5, 1, 1, 'SJIR3453', 'shijiro std racing M4', 'shijiro', 'biar sangar suara motor mu', 2000000.00, 0, 'Vario 125', '', '2025-10-07 12:28:46', '2025-10-07 12:41:38', NULL),
(6, 7, 5, 'AES2754', 'Aes Turbo SE3', 'Aes', 'biar terang hidupmu', 4300000.00, 49, 'Aerox/Nmax', '', '2025-10-07 12:30:53', '2025-10-24 02:46:38', NULL),
(7, 4, 5, 'UM3746', 'Block Mesin', 'UMA Racing', 'Biarr kencang motor mu', 100000000.00, 100, 'Aerox/Nmax/Vario all', '', '2025-10-07 12:32:00', '2025-10-07 12:32:00', NULL),
(8, 5, 4, 'SPK34456', 'Spion Y3', 'KACIK', 'biar ga buta map', 600000.00, 45, 'Vario 125', '', '2025-10-07 12:32:53', '2025-10-07 12:32:53', NULL),
(9, 2, 5, 'KS6353', 'King Speed v3', 'King Speed', 'biar aman ban mu itu ces', 120000000.00, 40, 'ALL Matic', '', '2025-10-07 12:33:55', '2025-10-24 02:55:38', NULL),
(10, 8, 4, 'VN2352', 'Shock depan', 'VNDg', 'biar empuk motor mu ces', 46000000.00, 50, 'all matic', '', '2025-10-07 12:34:51', '2025-10-30 17:45:16', NULL),
(11, 8, 5, 'OH28632', 'Shock Bracker Ohlins', 'Ohlins', 'biar empuk belakang motor mu', 200000000.00, 1, 'Aerox', '', '2025-10-07 12:36:09', '2025-10-24 02:59:43', NULL),
(16, 1, 1, 'SPR0004', 'Cover Body 4', 'Suzuki', 'Produk berkualitas tinggi untuk motor modifikasi', 1270247.00, 75, 'Mio', '', '2025-10-31 14:19:46', '2025-11-07 06:14:13', '2025-11-07 06:14:13'),
(17, 2, 3, 'SPR0005', 'Spion Lipat 5', 'Kawasaki', 'Produk berkualitas tinggi untuk motor modifikasi', 1623045.00, 25, 'Scoopy', '', '2025-10-31 14:19:46', '2025-11-01 17:58:59', '2025-11-01 17:58:59'),
(18, 4, 2, 'SPR0006', 'Shock Breaker 6', 'Suzuki', 'Produk berkualitas tinggi untuk motor modifikasi', 126784.00, 52, 'Mio', '', '2025-10-31 14:19:46', '2025-11-07 06:14:15', '2025-11-07 06:14:15'),
(19, 1, 2, 'SPR0007', 'Knalpot Racing 7', 'Yamaha', 'Produk berkualitas tinggi untuk motor modifikasi', 535313.00, 89, 'Aerox', '', '2025-10-31 14:19:46', '2025-11-07 06:14:18', '2025-11-07 06:14:18'),
(20, 3, 3, 'SPR0008', 'CVT Racing 8', 'Yamaha', 'Produk berkualitas tinggi untuk motor modifikasi', 198277.00, 39, 'Aerox', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(21, 2, 3, 'SPR0009', 'Velg Racing 9', 'Honda', 'Produk berkualitas tinggi untuk motor modifikasi', 413380.00, 93, 'Scoopy', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(22, 2, 2, 'SPR0010', 'Ban Tubeless 10', 'Takegawa', 'Produk berkualitas tinggi untuk motor modifikasi', 554553.00, 21, 'PCX', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(23, 4, 1, 'SPR0011', 'Spion Lipat 11', 'Honda', 'Produk berkualitas tinggi untuk motor modifikasi', 1365640.00, 19, 'Mio', '', '2025-10-31 14:19:46', '2025-10-31 14:37:29', NULL),
(24, 1, 1, 'SPR0012', 'Lampu LED 12', 'KTC', 'Produk berkualitas tinggi untuk motor modifikasi', 747128.00, 88, 'Aerox', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(25, 2, 3, 'SPR0013', 'Gear Set 13', 'Yamaha', 'Produk berkualitas tinggi untuk motor modifikasi', 234620.00, 34, 'PCX', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(26, 4, 1, 'SPR0014', 'Gear Set 14', 'Takegawa', 'Produk berkualitas tinggi untuk motor modifikasi', 253047.00, 15, 'Mio', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(27, 2, 3, 'SPR0015', 'Handgrip Racing 15', 'KTC', 'Produk berkualitas tinggi untuk motor modifikasi', 649120.00, 23, 'Beat', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(28, 4, 1, 'SPR0016', 'Spion Lipat 16', 'Kawasaki', 'Produk berkualitas tinggi untuk motor modifikasi', 595733.00, 51, 'Scoopy', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(29, 4, 2, 'SPR0017', 'CVT Racing 17', 'Honda', 'Produk berkualitas tinggi untuk motor modifikasi', 459391.00, 43, 'PCX', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(30, 4, 1, 'SPR0018', 'Ban Tubeless 18', 'Kawasaki', 'Produk berkualitas tinggi untuk motor modifikasi', 364508.00, 85, 'Vario 150', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(31, 2, 1, 'SPR0019', 'Cover Body 19', 'KTC', 'Produk berkualitas tinggi untuk motor modifikasi', 81101.00, 52, 'PCX', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(32, 2, 1, 'SPR0020', 'Knalpot Racing 20', 'Suzuki', 'Produk berkualitas tinggi untuk motor modifikasi', 800000.00, 37, 'Mio', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(33, 3, 2, 'SPR0021', 'Shock Breaker 21', 'KTC', 'Produk berkualitas tinggi untuk motor modifikasi', 1657415.00, 65, 'Mio', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(34, 2, 1, 'SPR0022', 'Lampu LED 22', 'Honda', 'Produk berkualitas tinggi untuk motor modifikasi', 771207.00, 91, 'Beat', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(35, 1, 3, 'SPR0023', 'Jok Custom 23', 'Yamaha', 'Produk berkualitas tinggi untuk motor modifikasi', 554183.00, 7, 'Scoopy', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(36, 3, 2, 'SPR0024', 'Jok Custom 24', 'Kawasaki', 'Produk berkualitas tinggi untuk motor modifikasi', 1043145.00, 68, 'Vario 150', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(37, 3, 2, 'SPR0025', 'Gear Set 25', 'Takegawa', 'Produk berkualitas tinggi untuk motor modifikasi', 1785455.00, 76, 'Scoopy', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(38, 1, 2, 'SPR0026', 'Shock Breaker 26', 'Takegawa', 'Produk berkualitas tinggi untuk motor modifikasi', 975191.00, 98, 'Aerox', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(39, 3, 3, 'SPR0027', 'Knalpot Racing 27', 'Yamaha', 'Produk berkualitas tinggi untuk motor modifikasi', 1575583.00, 95, 'Beat', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(40, 3, 3, 'SPR0028', 'Lampu LED 28', 'Suzuki', 'Produk berkualitas tinggi untuk motor modifikasi', 1356265.00, 64, 'Vario 150', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(41, 1, 3, 'SPR0029', 'Jok Custom 29', 'Honda', 'Produk berkualitas tinggi untuk motor modifikasi', 961943.00, 68, 'Aerox', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(42, 3, 3, 'SPR0030', 'Busi Iridium 30', 'Yamaha', 'Produk berkualitas tinggi untuk motor modifikasi', 1628109.00, 92, 'Vario 150', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(43, 2, 2, 'SPR0031', 'Knalpot Racing 31', 'Honda', 'Produk berkualitas tinggi untuk motor modifikasi', 1085740.00, 43, 'Aerox', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(44, 2, 3, 'SPR0032', 'Oli Mesin 32', 'KTC', 'Produk berkualitas tinggi untuk motor modifikasi', 1011050.00, 50, 'Beat', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(45, 3, 1, 'SPR0033', 'Ban Tubeless 33', 'KTC', 'Produk berkualitas tinggi untuk motor modifikasi', 890088.00, 33, 'PCX', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(46, 4, 1, 'SPR0034', 'Cover Body 34', 'Suzuki', 'Produk berkualitas tinggi untuk motor modifikasi', 1894453.00, 5, 'Scoopy', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(47, 2, 3, 'SPR0035', 'Velg Racing 35', 'KTC', 'Produk berkualitas tinggi untuk motor modifikasi', 251564.00, 89, 'Vario 150', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(48, 1, 2, 'SPR0036', 'Oli Mesin 36', 'Kawasaki', 'Produk berkualitas tinggi untuk motor modifikasi', 1479897.00, 80, 'Beat', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(49, 4, 2, 'SPR0037', 'Cover Body 37', 'Honda', 'Produk berkualitas tinggi untuk motor modifikasi', 74183.00, 67, 'Vario 150', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(50, 3, 1, 'SPR0038', 'Velg Racing 38', 'Takegawa', 'Produk berkualitas tinggi untuk motor modifikasi', 54851.00, 23, 'Nmax', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(51, 4, 2, 'SPR0039', 'Jok Custom 39', 'Kawasaki', 'Produk berkualitas tinggi untuk motor modifikasi', 1550362.00, 92, 'PCX', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(52, 4, 2, 'SPR0040', 'Spion Lipat 40', 'Takegawa', 'Produk berkualitas tinggi untuk motor modifikasi', 1963310.00, 49, 'Vario 150', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(53, 4, 1, 'SPR0041', 'Knalpot Racing 41', 'Suzuki', 'Produk berkualitas tinggi untuk motor modifikasi', 911046.00, 67, 'Beat', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(54, 2, 1, 'SPR0042', 'Spion Lipat 42', 'KTC', 'Produk berkualitas tinggi untuk motor modifikasi', 1550923.00, 16, 'Vario 150', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(55, 4, 1, 'SPR0043', 'Cover Body 43', 'KTC', 'Produk berkualitas tinggi untuk motor modifikasi', 1318973.00, 19, 'Beat', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(56, 4, 1, 'SPR0044', 'Jok Custom 44', 'Honda', 'Produk berkualitas tinggi untuk motor modifikasi', 1916532.00, 51, 'PCX', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(57, 3, 1, 'SPR0045', 'Cover Body 45', 'Kawasaki', 'Produk berkualitas tinggi untuk motor modifikasi', 1176912.00, 21, 'Mio', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(58, 2, 1, 'SPR0046', 'Velg Racing 46', 'Kawasaki', 'Produk berkualitas tinggi untuk motor modifikasi', 926378.00, 44, 'Vario 150', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(59, 4, 2, 'SPR0047', 'Cover Body 47', 'KTC', 'Produk berkualitas tinggi untuk motor modifikasi', 1070220.00, 95, 'Beat', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(60, 4, 3, 'SPR0048', 'Cover Body 48', 'Suzuki', 'Produk berkualitas tinggi untuk motor modifikasi', 1186978.00, 89, 'Aerox', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(61, 2, 1, 'SPR0049', 'Ban Tubeless 49', 'KTC', 'Produk berkualitas tinggi untuk motor modifikasi', 857685.00, 21, 'Beat', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(62, 2, 2, 'SPR0050', 'Velg Racing 50', 'Yamaha', 'Produk berkualitas tinggi untuk motor modifikasi', 357844.00, 52, 'Aerox', '', '2025-10-31 14:19:46', '2025-10-31 14:19:46', NULL),
(68, 9, 2, 'CIKOCUKI', 'Body kasar kek mukanya', 'CACAKYU', 'KERENNNNNNNNNNNNNNNNNNNNNNNNNNN', 4000000.00, 5, 'Aerox', '', '2025-11-07 00:31:18', '2025-11-07 06:14:08', '2025-11-07 06:14:08');

-- --------------------------------------------------------

--
-- Struktur dari tabel `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `contact_person`, `phone`, `email`, `address`, `city`, `created_at`) VALUES
(1, 'PT. Motor Parts Indonesia', 'Budi Santoso', '081234567890', 'budi@motorparts.com', NULL, 'Jakarta', '2025-10-07 03:41:04'),
(2, 'CV. Sparepart Motor', 'Andi Wijaya', '081234567891', 'andi@sparepart.com', '', 'Bandungg', '2025-10-07 03:41:04'),
(3, 'PT. Brembo', '081234565432', '08543234567', 'Brembo435@gmail.com', 'Jakarta intinya', 'Jakarta', '2025-10-07 11:56:28'),
(4, 'PT. VND', '084736525', '0823455432345', 'vndindo@gmail.com', 'bandung tempat dilan', 'bandung', '2025-10-07 11:57:02'),
(5, 'PT. Dirgaputra Ekapratama', '0876543456', '086543234567', 'dirgatura@gmail.com', 'bekasi kota panas', 'Bekasi', '2025-10-07 11:58:23'),
(6, 'PT.SUKA SIKI', 'sanjaya', '081245422', 'san@gmail.com', 'itk km12', 'ikt', '2025-10-31 14:36:59');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaction_details`
--

CREATE TABLE `transaction_details` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaction_details`
--

INSERT INTO `transaction_details` (`id`, `transaction_id`, `product_id`, `quantity`, `price`, `subtotal`) VALUES
(1, 1, 11, 2, 200000000.00, 400000000.00),
(2, 2, 5, 7, 2000000.00, 14000000.00),
(3, 3, 3, 5, 3000000.00, 15000000.00),
(4, 4, 10, 30, 46000000.00, 1380000000.00),
(5, 5, 16, 5, 1270247.00, 6351235.00),
(6, 6, 16, 5, 1270247.00, 6351235.00),
(7, 7, 17, 15, 1623045.00, 24345675.00),
(8, 8, 19, 4, 535313.00, 2141252.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `transaction_code` varchar(50) NOT NULL,
  `transaction_date` date NOT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `payment_method` enum('cash','transfer','credit') NOT NULL,
  `status` enum('pending','completed','cancelled') DEFAULT 'completed',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi`
--

INSERT INTO `transaksi` (`id`, `customer_id`, `transaction_code`, `transaction_date`, `total_amount`, `payment_method`, `status`, `notes`, `created_at`) VALUES
(1, 3, 'TRX202510074750', '2025-10-07', 400000000.00, 'cash', 'completed', 'bagus', '2025-10-07 12:37:03'),
(2, 3, 'TRX202510075029', '2025-10-07', 14000000.00, 'cash', 'completed', '', '2025-10-07 12:41:38'),
(3, 6, 'TRX202510077231', '2025-10-07', 15000000.00, 'credit', 'completed', '', '2025-10-07 12:42:09'),
(4, 5, 'TRX202510073775', '2025-10-07', 1380000000.00, 'transfer', 'completed', 'anjay pattt keren', '2025-10-07 12:42:40'),
(5, 7, 'TRX202510313175', '2025-10-31', 6351235.00, 'credit', 'completed', 'f', '2025-10-31 14:28:42'),
(6, 7, 'TRX202510315771', '2025-10-31', 6351235.00, 'credit', 'completed', 'f', '2025-10-31 14:28:49'),
(7, 12, 'TRX202510317629', '2025-10-31', 24345675.00, 'cash', 'completed', '', '2025-10-31 14:40:08'),
(8, 3, 'TRX202511071840', '2025-11-07', 2141252.00, 'transfer', 'completed', '', '2025-11-07 00:16:21');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('developer','admin','user') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `idx_deleted_at` (`deleted_at`);

--
-- Indeks untuk tabel `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `transaction_details`
--
ALTER TABLE `transaction_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_code` (`transaction_code`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT untuk tabel `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `transaction_details`
--
ALTER TABLE `transaction_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`);

--
-- Ketidakleluasaan untuk tabel `transaction_details`
--
ALTER TABLE `transaction_details`
  ADD CONSTRAINT `transaction_details_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transaksi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaction_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Ketidakleluasaan untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
