-- Active: 1741870211460@@127.0.0.1@3306@umkmlaundry
-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Jun 07, 2025 at 12:03 PM
-- Server version: 10.4.16-MariaDB
-- PHP Version: 7.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `umkmlaundry`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_pending`
--

CREATE TABLE `admin_pending` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `nama_laundry` varchar(100) DEFAULT NULL,
  `alamat_laundry` varchar(255) NOT NULL,
  `bukti_laundry` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admin_pending`
--

INSERT INTO `admin_pending` (`admin_id`, `username`, `password`, `nama`, `no_hp`, `alamat`, `nama_laundry`, `alamat_laundry`, `bukti_laundry`, `status`) VALUES
(1, 'sugeng', '$2y$10$ki7uNiFtre/85WPG3/mX5.UCQtq7qq2kDQdHbxqEFb6MNusTJ4i3i', 'Sugeng', '6281234567890', 'Palembang', 'Sugeng A', 'Palembang A', '', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `laundry`
--

CREATE TABLE `laundry` (
  `laundry_id` int(11) NOT NULL,
  `nama_laundry` varchar(100) NOT NULL,
  `alamat` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `laundry`
--

INSERT INTO `laundry` (`laundry_id`, `nama_laundry`, `alamat`) VALUES
(1, 'Intan Laundry', 'Jalan Pemuda No 26, Boja'),
(2, 'UT Laundry', 'Alamat UT Laundry'),
(3, 'YNS Laundry', 'Alamat YNS Laundry'),
(4, 'Annadif Laundry', 'Alamat Annadif Laundry'),
(11, 'Sugeng A', 'Palembang A');

-- --------------------------------------------------------

--
-- Table structure for table `pakaian`
--

CREATE TABLE `pakaian` (
  `pakaian_id` int(11) NOT NULL,
  `transaksi_id` int(11) NOT NULL,
  `jenis_paket` enum('ck','cs','dc') NOT NULL,
  `id_paket` int(11) NOT NULL,
  `pakaian_jenis` varchar(255) NOT NULL,
  `pakaian_jumlah` int(11) NOT NULL CHECK (`pakaian_jumlah` >= 0),
  `pakaian_tarif` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pakaian`
--

INSERT INTO `pakaian` (`pakaian_id`, `transaksi_id`, `jenis_paket`, `id_paket`, `pakaian_jenis`, `pakaian_jumlah`, `pakaian_tarif`) VALUES
(29, 46, 'dc', 3, 'kemeja 1', 1, 0),
(31, 40, 'ck', 0, 'Kemeja Putih 1, Rok Hitam Panjang 2, Jilbab Hitam 2, Blazer Hitam 1', 6, 0),
(38, 65, 'ck', 1, 'kemeja 10', 10, 0),
(39, 66, 'ck', 2, 'kemeja 10', 10, 0),
(40, 66, 'ck', 2, 'kemeja 10', 10, 0),
(41, 66, 'ck', 2, 'kemeja 10', 10, 0),
(136, 79, 'dc', 3, 'kaos', 1, 0),
(141, 80, 'dc', 6, 'baju 10', 9, 0),
(143, 81, 'ck', 3, 'kemeja 10', 10, 0),
(147, 84, 'dc', 3, 'baju', 2, 0),
(150, 87, 'ck', 15, 'kemeja 10', 1, 0),
(151, 87, 'dc', 7, 'kemeja 10', 1, 0),
(153, 88, 'ck', 15, 'kemeja 1', 1, 0),
(156, 89, 'cs', 18, 'Sejadah', 1, 0),
(157, 89, 'ck', 15, 'kemeja 1', 1, 0),
(159, 91, 'cs', 12, 'Jaket Kulit', 1, 0),
(160, 92, 'cs', 20, 'Keset', 1, 0),
(163, 94, 'ck', 15, 'baju 1', 1, 0),
(164, 93, 'ck', 15, 'sempak 10', 10, 0),
(165, 98, 'ck', 16, 'celana dalam 10, bh 10', 20, 0),
(167, 99, 'ck', 15, 'cangcut 10', 10, 0),
(169, 102, 'ck', 1, 'celana 10, jaket 5, daster 1', 16, 0),
(171, 103, 'ck', 3, 'Kemeja Putih 1, Rok Hitam Panjang 2, Jilbab Hitam 2, Blazer Hitam 1', 6, 0),
(172, 106, 'dc', 12, 'kemeja 10', 9, 0),
(173, 112, 'dc', 1, 'Cuci Kering', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `pemilik`
--

CREATE TABLE `pemilik` (
  `pemilik_id` int(11) NOT NULL,
  `pemilik_nama` varchar(255) NOT NULL,
  `pemilik_hp` varchar(20) NOT NULL,
  `laundry_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pemilik`
--

INSERT INTO `pemilik` (`pemilik_id`, `pemilik_nama`, `pemilik_hp`, `laundry_id`) VALUES
(1, 'anik', '628964562734', 1),
(2, 'Utari', '62895328096161', 2),
(3, 'Bu atin', '62895872365362', 4),
(4, 'Bu Yulia', '6283726352632', 3);

-- --------------------------------------------------------

--
-- Table structure for table `tb_cuci_komplit`
--

CREATE TABLE `tb_cuci_komplit` (
  `id_ck` int(11) NOT NULL,
  `nama_paket_ck` varchar(100) NOT NULL,
  `waktu_kerja_ck` varchar(20) NOT NULL,
  `kuantitas_ck` int(11) NOT NULL,
  `tarif_ck` int(11) NOT NULL,
  `laundry_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tb_cuci_komplit`
--

INSERT INTO `tb_cuci_komplit` (`id_ck`, `nama_paket_ck`, `waktu_kerja_ck`, `kuantitas_ck`, `tarif_ck`, `laundry_id`) VALUES
(1, 'Cuci Komplit Reguler', '2 Hari', 1, 8000, 1),
(2, 'Cuci Komplit Kilat', '1 Hari', 1, 15000, 1),
(3, 'Cuci Komplit Express', '5 Jam', 1, 20000, 1),
(7, 'Cuci Komplit Reguler', '2 Hari', 1, 8000, 2),
(8, 'Cuci Komplit Kilat', '1 Hari', 1, 15000, 2),
(9, 'Cuci Komplit Express', '5 Jam', 1, 20000, 2),
(15, 'Cuci Komplit Reguler', '2 Hari', 1, 8000, 3),
(16, 'Cuci Komplit Kilat', '1 Hari', 1, 15000, 3),
(17, 'Cuci Komplit Express', '5 Jam', 1, 20000, 3);

-- --------------------------------------------------------

--
-- Table structure for table `tb_cuci_satuan`
--

CREATE TABLE `tb_cuci_satuan` (
  `id_cs` int(11) NOT NULL,
  `nama_cs` varchar(100) NOT NULL,
  `waktu_kerja_cs` varchar(20) NOT NULL,
  `kuantitas_cs` int(11) NOT NULL,
  `tarif_cs` int(11) NOT NULL,
  `keterangan_cs` text NOT NULL,
  `laundry_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tb_cuci_satuan`
--

INSERT INTO `tb_cuci_satuan` (`id_cs`, `nama_cs`, `waktu_kerja_cs`, `kuantitas_cs`, `tarif_cs`, `keterangan_cs`, `laundry_id`) VALUES
(1, 'Jaket Kulit', '1 Hari', 1, 15000, '', 1),
(2, 'Jaket Non Kulit', '1 Hari', 1, 6000, '', 1),
(3, 'Boneka Mini', '1 Hari', 1, 3000, '', 1),
(4, 'Boneka Kecil', '1 Hari', 1, 6000, '', 1),
(5, 'Boneka Sedang', '1 Hari', 1, 10000, '', 1),
(6, 'Boneka Besar', '1 Hari', 1, 20000, '', 1),
(7, 'Sejadah', '1 Hari', 1, 20000, '', 1),
(8, 'Selimut', '1 Hari', 1, 20000, '', 1),
(9, 'Keset', '1 Hari', 1, 20000, '', 1),
(10, 'Karpet kecil', '1 Hari', 1, 10000, '', 1),
(11, 'Karpet Besar', '2 Hari', 1, 25000, '', 1),
(12, 'Jaket Kulit', '1 Hari', 1, 15000, '', 3),
(13, 'Jaket Non Kulit', '1 Hari', 1, 6000, '', 3),
(14, 'Boneka Mini', '1 Hari', 1, 3000, '', 3),
(15, 'Boneka Kecil', '1 Hari', 1, 6000, '', 3),
(16, 'Boneka Sedang', '1 Hari', 1, 10000, '', 3),
(17, 'Boneka Besar', '1 Hari', 1, 20000, '', 3),
(18, 'Sejadah', '1 Hari', 1, 20000, '', 3),
(19, 'Selimut', '1 Hari', 1, 20000, '', 3),
(20, 'Keset', '1 Hari', 1, 20000, '', 3),
(21, 'Jaket Kulit', '1 Hari', 1, 15000, '', 2),
(22, 'Jaket Non Kulit', '1 Hari', 1, 6000, '', 2),
(23, 'Boneka Mini', '1 Hari', 1, 3000, '', 2),
(24, 'Boneka Kecil', '1 Hari', 1, 6000, '', 2),
(25, 'Boneka Sedang', '1 Hari', 1, 10000, '', 2),
(26, 'Boneka Besar', '1 Hari', 1, 20000, '', 2),
(27, 'Sejadah', '1 Hari', 1, 20000, '', 2),
(28, 'Selimut', '1 Hari', 1, 20000, '', 2),
(29, 'Keset', '1 Hari', 1, 20000, '', 2);

-- --------------------------------------------------------

--
-- Table structure for table `tb_dry_clean`
--

CREATE TABLE `tb_dry_clean` (
  `id_dc` int(11) NOT NULL,
  `nama_paket_dc` varchar(100) NOT NULL,
  `waktu_kerja_dc` varchar(20) NOT NULL,
  `kuantitas_dc` int(11) NOT NULL,
  `tarif_dc` int(11) NOT NULL,
  `laundry_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tb_dry_clean`
--

INSERT INTO `tb_dry_clean` (`id_dc`, `nama_paket_dc`, `waktu_kerja_dc`, `kuantitas_dc`, `tarif_dc`, `laundry_id`) VALUES
(1, 'Cuci Kering Reguler', '2 Hari', 1, 6000, 1),
(2, 'Cuci Kering Kilat', '1 Hari', 1, 9000, 1),
(3, 'Cuci Kering Express', '5 Jam', 1, 15000, 1),
(6, 'Cuci Kering Wangi', '3 Hari', 1, 15000, 1),
(7, 'Cuci Kering Reguler', '2 Hari', 1, 6000, 3),
(8, 'Cuci Kering Kilat', '1 Hari', 1, 9000, 3),
(9, 'Cuci Kering Express', '5 Jam', 1, 15000, 3),
(10, 'Cuci Kering Reguler', '2 Hari', 1, 6000, 2),
(11, 'Cuci Kering Kilat', '1 Hari', 1, 9000, 2),
(12, 'Cuci Kering Express', '5 Jam', 1, 15000, 2);

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--
DROP TABLE IF EXISTS `transaksi`;

CREATE TABLE `transaksi` (
  `transaksi_id` INT(11) NOT NULL AUTO_INCREMENT,
  `transaksi_tgl` DATE NOT NULL,
  `transaksi_pelanggan` INT(11) NOT NULL,
  `transaksi_berat` FLOAT NOT NULL,
  `transaksi_total_harga` INT(11) NOT NULL,
  `transaksi_tgl_selesai` DATE NOT NULL,
  `transaksi_status` ENUM('menunggu','proses','selesai','diantar') NOT NULL DEFAULT 'menunggu',
  `status_pembayaran` ENUM('belum_bayar','lunas') NOT NULL DEFAULT 'belum_bayar',
  `laundry_id` INT(11) NOT NULL,
  PRIMARY KEY (`transaksi_id`),
  KEY `transaksi_pelanggan` (`transaksi_pelanggan`),
  KEY `laundry_id` (`laundry_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`transaksi_id`, `transaksi_tgl`, `transaksi_pelanggan`, `transaksi_berat`, `transaksi_total_harga`, `transaksi_tgl_selesai`, `transaksi_status`, `laundry_id`) VALUES
(40, '2025-04-24', 4, 3, 33000, '2025-04-26', 'proses', 1),
(41, '2025-04-24', 4, 0, 0, '2025-04-26', '', 1),
(43, '2025-04-25', 4, 0, 0, '2025-04-26', '', 1),
(44, '2025-04-25', 4, 0, 0, '2025-04-28', '', 1),
(45, '2025-04-25', 4, 2, 30000, '2025-04-27', 'proses', 1),
(46, '2025-04-25', 4, 1, 15000, '2025-04-26', 'proses', 1),
(47, '2025-04-25', 4, 0, 20000, '2025-04-27', 'selesai', 1),
(49, '2025-04-26', 4, 0, 20000, '2025-04-27', 'proses', 1),
(50, '2025-04-26', 4, 1, 8000, '2025-05-01', 'selesai', 1),
(51, '2025-04-26', 4, 1, 8000, '2025-04-29', 'proses', 1),
(52, '2025-04-26', 4, 1, 9000, '2025-04-30', 'proses', 1),
(53, '2025-04-26', 4, 1, 26000, '2025-04-28', 'proses', 1),
(54, '2025-04-26', 4, 0, 6000, '0000-00-00', '', 1),
(55, '2025-04-26', 4, 2, 40000, '2025-04-29', 'proses', 1),
(56, '2025-04-26', 4, 2, 70000, '2025-04-28', 'proses', 1),
(57, '2025-04-26', 4, 1, 6000, '2025-04-28', 'proses', 1),
(58, '2025-04-26', 4, 2, 18000, '2025-04-28', 'proses', 1),
(59, '2025-04-26', 4, 1, 15000, '2025-04-28', 'proses', 1),
(60, '2025-04-26', 4, 1, 15000, '2025-04-28', '', 1),
(61, '2025-04-26', 4, 2, 30000, '2025-04-28', '', 1),
(62, '2025-04-26', 4, 1, 15000, '2025-04-28', '', 1),
(63, '2025-04-26', 4, 1, 15000, '2025-04-28', '', 1),
(64, '2025-04-26', 4, 0, 20000, '2025-04-28', 'proses', 1),
(65, '2025-04-26', 4, 2, 16000, '2025-04-28', '', 1),
(66, '2025-04-26', 4, 1, 15000, '2025-04-28', '', 1),
(67, '2025-04-26', 4, 0, 30000, '2025-04-28', 'proses', 1),
(68, '2025-04-26', 4, 0, 3000, '2025-04-28', 'proses', 1),
(79, '2025-04-26', 4, 1, 15000, '2025-04-28', '', 1),
(80, '2025-04-26', 4, 1, 15000, '2025-04-28', '', 1),
(81, '2025-04-26', 4, 1, 20000, '2025-04-28', '', 1),
(83, '2025-04-26', 4, 0, 20000, '2025-04-28', 'proses', 1),
(84, '2025-04-26', 4, 1, 15000, '2025-04-28', 'proses', 1),
(85, '2025-04-28', 4, 0, 20000, '2025-04-30', 'selesai', 1),
(86, '2025-04-28', 8, 0, 20000, '2025-04-30', 'selesai', 3),
(87, '2025-04-28', 8, 2, 14000, '2025-04-30', 'proses', 3),
(88, '2025-04-28', 8, 1, 8000, '2025-04-30', 'selesai', 3),
(89, '2025-04-28', 8, 1, 28000, '2025-04-30', 'selesai', 3),
(91, '2025-04-28', 8, 0, 15000, '2025-04-30', 'selesai', 3),
(92, '2025-04-28', 10, 0, 20000, '2025-04-30', 'selesai', 3),
(93, '2025-04-28', 8, 2, 16000, '2025-04-30', 'selesai', 3),
(94, '2025-04-28', 8, 1, 8000, '2025-04-30', '', 3),
(95, '2025-04-28', 8, 0, 20000, '2025-04-30', 'selesai', 3),
(96, '2025-04-29', 8, 0, 20000, '2025-05-01', 'selesai', 3),
(97, '2025-04-29', 10, 0, 10000, '2025-05-01', 'selesai', 3),
(98, '2025-04-29', 10, 0, 300000, '2025-05-01', 'selesai', 3),
(99, '2025-05-08', 12, 1, 8000, '2025-05-10', 'proses', 3),
(100, '2025-05-09', 4, 0, 6000, '2025-05-11', 'menunggu', 1),
(101, '2025-05-17', 7, 0, 30000, '2025-05-19', 'selesai', 2),
(102, '2025-05-17', 4, 2, 16000, '2025-05-20', '', 1),
(103, '2025-05-17', 4, 1.5, 30000, '2025-05-20', 'proses', 1),
(104, '2025-05-22', 7, 0, 0, '2025-05-24', 'menunggu', 2),
(105, '2025-05-22', 7, 0, 0, '2025-05-24', 'menunggu', 2),
(106, '2025-05-24', 7, 0, 135000, '2025-05-26', 'menunggu', 2),
(107, '2025-05-24', 7, 0, 0, '2025-05-26', 'menunggu', 2),
(108, '2025-05-24', 7, 0, 0, '2025-05-26', 'menunggu', 2),
(109, '2025-05-25', 7, 0, 3000, '2025-05-27', 'proses', 1),
(110, '2025-05-25', 7, 0, 6000, '2025-05-27', 'menunggu', 3),
(111, '2025-06-07', 7, 0, 21000, '2025-06-09', 'lunas', 1),
(112, '2025-06-07', 7, 0, 6000, '2025-06-09', 'menunggu', 1);

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_detail`
--

CREATE TABLE `transaksi_detail` (
  `detail_id` int(11) NOT NULL,
  `transaksi_id` int(11) NOT NULL,
  `jenis_paket` enum('ck','cs','dc') NOT NULL,
  `id_paket` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 1,
  `subtotal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `transaksi_detail`
--

INSERT INTO `transaksi_detail` (`detail_id`, `transaksi_id`, `jenis_paket`, `id_paket`, `jumlah`, `subtotal`) VALUES
(2, 40, 'dc', 2, 3, 9000),
(3, 40, 'cs', 4, 1, 6000),
(4, 41, 'ck', 1, 3, 24000),
(6, 43, 'cs', 11, 4, 100000),
(7, 44, 'dc', 1, 1, 6000),
(8, 45, 'ck', 2, 2, 105000),
(9, 46, 'dc', 3, 1, 15000),
(10, 47, 'cs', 9, 1, 20000),
(12, 49, 'cs', 9, 1, 20000),
(13, 50, 'ck', 1, 1, 8000),
(14, 51, 'ck', 1, 16, 8000),
(15, 52, 'dc', 2, 1, 9000),
(16, 53, 'cs', 7, 1, 20000),
(17, 53, 'dc', 1, 1, 6000),
(18, 54, 'cs', 4, 1, 6000),
(19, 55, 'ck', 3, 2, 200000),
(20, 56, 'cs', 6, 2, 40000),
(21, 56, 'dc', 3, 2, 150000),
(22, 57, 'dc', 1, 15, 90000),
(23, 58, 'dc', 2, 2, 9000),
(24, 59, 'dc', 6, 3, 45000),
(25, 60, 'dc', 3, 2, 30000),
(26, 61, 'dc', 3, 10, 150000),
(27, 62, 'dc', 3, 1, 15000),
(28, 63, 'dc', 3, 10, 150000),
(29, 64, 'cs', 10, 2, 20000),
(30, 65, 'ck', 1, 10, 80000),
(31, 66, 'cs', 1, 1, 15000),
(32, 66, 'ck', 2, 10, 150000),
(33, 67, 'cs', 1, 2, 30000),
(34, 68, 'cs', 3, 1, 3000),
(49, 79, 'dc', 3, 1, 15000),
(50, 80, 'cs', 6, 1, 20000),
(51, 80, 'dc', 6, 9, 135000),
(52, 81, 'cs', 8, 1, 20000),
(53, 81, 'cs', 8, 1, 20000),
(54, 81, 'ck', 3, 10, 200000),
(58, 83, 'cs', 7, 1, 20000),
(59, 84, 'cs', 7, 1, 20000),
(60, 84, 'dc', 3, 2, 30000),
(61, 85, 'cs', 9, 1, 20000),
(62, 86, 'cs', 20, 1, 20000),
(63, 87, 'ck', 15, 1, 8000),
(64, 87, 'dc', 7, 1, 6000),
(65, 88, 'cs', 20, 1, 20000),
(66, 88, 'ck', 15, 1, 8000),
(67, 89, 'cs', 18, 1, 20000),
(68, 89, 'ck', 15, 1, 8000),
(70, 91, 'cs', 12, 1, 15000),
(71, 92, 'cs', 20, 1, 20000),
(72, 93, 'ck', 15, 10, 80000),
(73, 94, 'ck', 15, 1, 8000),
(74, 95, 'cs', 20, 1, 20000),
(75, 96, 'cs', 20, 1, 20000),
(76, 97, 'cs', 16, 1, 10000),
(77, 98, 'ck', 16, 20, 300000),
(78, 99, 'ck', 15, 10, 80000),
(79, 100, 'cs', 4, 1, 6000),
(80, 101, 'cs', 21, 2, 30000),
(81, 102, 'ck', 1, 16, 128000),
(82, 103, 'ck', 3, 6, 120000),
(83, 104, 'cs', 1, 1, 0),
(84, 105, 'cs', 13, 2, 0),
(85, 106, 'dc', 12, 9, 135000),
(86, 107, 'cs', 1, 2, 0),
(87, 108, 'cs', 3, 1, 0),
(88, 109, 'cs', 3, 1, 3000),
(89, 110, 'cs', 15, 1, 6000),
(90, 111, 'cs', 1, 1, 15000),
(91, 111, 'cs', 2, 1, 6000),
(92, 112, 'dc', 1, 1, 6000);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `level` enum('superadmin','admin','pengguna') DEFAULT 'pengguna',
  `laundry_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `nama`, `no_hp`, `alamat`, `level`, `laundry_id`) VALUES
(1, 'superadmin', '$2y$10$ae/.zYUndYAQW6L86gQaxOnHut13QLi97FJxt6SFtY2YGSVpk8BB6', 'Super Admin', '62895328096161', 'Dusun Jurang Brengos, Desa Merbuh', 'superadmin', NULL),
(2, 'intan', '$2y$10$5LMDlj5oDwB3XaqIIGFKtu3ZVzdri4fpzj2/jS.MAanaheSd78H2y', 'Intan Laundry', '6285740407769', 'Jalan Pemuda No 26, Dusun Jagalan', 'admin', 1),
(3, 'ut', '$2y$10$7crPW9j.o977iYMccWmOD.MhU8eqqyt3Ziacf6/cbGRIGpSqUUXXe', 'ut Laundry', '6285740407769', 'Jalan Pemuda No 26, Dusun Jagalan', 'admin', 2),
(4, 'joko', '$2y$10$RL.s5esriQGCH3TmOZOznuhJIdH8/a2rIlJFVr6mSOD0s14xgPCX6', 'Joko Santoso', '6281234567890', 'Kendal', 'pengguna', NULL),
(7, 'mutia', '$2y$10$uhdXbia9ZWUTeJgrXzX.tu0SyPTNP09ZHt70dNXaj0mZZ/XWEruAq', 'Mutiara Ilma Daniati', '6285972927987', 'dusun jurang brengos', 'pengguna', NULL),
(8, 'andre', '$2y$10$v8zzYNQCDqca6W2W/Qm6e.d3DgvmB7S9ZH/ccovYh3SnBkJQkjrCK', 'Mahfud Andrea Yulianto', '6287872692330', 'Dusun Merbuh', 'pengguna', NULL),
(9, 'yns', '$2y$10$It2oASEj8cNC.jM/U5J2s.zsE1FP010jl5Y5CN2131JcFya6cx57S', 'YNS Laundry', '6285740407769', 'Boja', 'admin', 3),
(10, 'ilma', '$2y$10$Pij67h1LsIeQFJ4xSWNEZecN9K7ST7NZAZaq3XqOb5vjo.0eDZ4nS', 'Daniati Ilma', '62895340319292', 'Dusun Jurang Brengos', 'pengguna', 3),
(11, 'azza', '$2y$10$IGZXi3ELPdkXu8f4Cw32Teza84ibQa1rjy8.kYgdOgYpIFQDQbFta', 'Azza Arsistawa', '628346374628', 'Pemalang', 'pengguna', 2),
(12, 'sekar', '$2y$10$j7sMsstcKUjdTzOPFyvB.OFlIFoNieVI67RtDUFZlYws2jAeu0VGO', 'Sekar Ayu Kumadita', '6288228618059', 'Dusun Jurang Brengos', 'pengguna', 3),
(13, 'annadif', '$2y$10$jV8KGOBAxC.kNyPCBlOxCeUTQ2jdZSPuS2eY3GokWeHLHEaY7yE0K', 'Annadif Laundry', '6288228618059', 'Alamat Annadif Laundry', 'admin', 4),
(16, 'aa', '$2y$10$8z/qLhdHWt91Rp7f7sVsrOF6tu10NbLkCYzLgSHp10F7J4lPJO2JW', '', '6288228618059', 'boja', 'pengguna', NULL),
(17, 'rizky', '$2y$10$buWFgTI9bgeKd6JrcoW7ZOqUBlyuzdVfsBEF44j3RiG9veuK9VOpG', '', '628346374628', 'Bekasi', 'pengguna', NULL),
(19, 'sugeng', '$2y$10$ki7uNiFtre/85WPG3/mX5.UCQtq7qq2kDQdHbxqEFb6MNusTJ4i3i', 'Sugeng', '6281234567890', 'Palembang', 'admin', 11);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_pending`
--
ALTER TABLE `admin_pending`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `laundry`
--
ALTER TABLE `laundry`
  ADD PRIMARY KEY (`laundry_id`);

--
-- Indexes for table `pakaian`
--
ALTER TABLE `pakaian`
  ADD PRIMARY KEY (`pakaian_id`),
  ADD KEY `transaksi_id` (`transaksi_id`);

--
-- Indexes for table `pemilik`
--
ALTER TABLE `pemilik`
  ADD PRIMARY KEY (`pemilik_id`),
  ADD KEY `laundry_id` (`laundry_id`);

--
-- Indexes for table `tb_cuci_komplit`
--
ALTER TABLE `tb_cuci_komplit`
  ADD PRIMARY KEY (`id_ck`),
  ADD KEY `laundry_id` (`laundry_id`);

--
-- Indexes for table `tb_cuci_satuan`
--
ALTER TABLE `tb_cuci_satuan`
  ADD PRIMARY KEY (`id_cs`),
  ADD KEY `laundry_id` (`laundry_id`);

--
-- Indexes for table `tb_dry_clean`
--
ALTER TABLE `tb_dry_clean`
  ADD PRIMARY KEY (`id_dc`),
  ADD KEY `laundry_id` (`laundry_id`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`transaksi_id`),
  ADD KEY `transaksi_pelanggan` (`transaksi_pelanggan`),
  ADD KEY `laundry_id` (`laundry_id`);

--
-- Indexes for table `transaksi_detail`
--
ALTER TABLE `transaksi_detail`
  ADD PRIMARY KEY (`detail_id`),
  ADD KEY `transaksi_id` (`transaksi_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `laundry_id` (`laundry_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_pending`
--
ALTER TABLE `admin_pending`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `laundry`
--
ALTER TABLE `laundry`
  MODIFY `laundry_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `pakaian`
--
ALTER TABLE `pakaian`
  MODIFY `pakaian_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=174;

--
-- AUTO_INCREMENT for table `pemilik`
--
ALTER TABLE `pemilik`
  MODIFY `pemilik_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tb_cuci_komplit`
--
ALTER TABLE `tb_cuci_komplit`
  MODIFY `id_ck` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `tb_cuci_satuan`
--
ALTER TABLE `tb_cuci_satuan`
  MODIFY `id_cs` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `tb_dry_clean`
--
ALTER TABLE `tb_dry_clean`
  MODIFY `id_dc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `transaksi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT for table `transaksi_detail`
--
ALTER TABLE `transaksi_detail`
  MODIFY `detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pakaian`
--
ALTER TABLE `pakaian`
  ADD CONSTRAINT `pakaian_ibfk_1` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`transaksi_id`) ON DELETE CASCADE;

--
-- Constraints for table `pemilik`
--
ALTER TABLE `pemilik`
  ADD CONSTRAINT `pemilik_ibfk_1` FOREIGN KEY (`laundry_id`) REFERENCES `laundry` (`laundry_id`) ON DELETE CASCADE;

--
-- Constraints for table `tb_cuci_komplit`
--
ALTER TABLE `tb_cuci_komplit`
  ADD CONSTRAINT `tb_cuci_komplit_ibfk_1` FOREIGN KEY (`laundry_id`) REFERENCES `laundry` (`laundry_id`) ON DELETE CASCADE;

--
-- Constraints for table `tb_cuci_satuan`
--
ALTER TABLE `tb_cuci_satuan`
  ADD CONSTRAINT `tb_cuci_satuan_ibfk_1` FOREIGN KEY (`laundry_id`) REFERENCES `laundry` (`laundry_id`) ON DELETE CASCADE;

--
-- Constraints for table `tb_dry_clean`
--
ALTER TABLE `tb_dry_clean`
  ADD CONSTRAINT `tb_dry_clean_ibfk_1` FOREIGN KEY (`laundry_id`) REFERENCES `laundry` (`laundry_id`) ON DELETE CASCADE;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`transaksi_pelanggan`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`laundry_id`) REFERENCES `laundry` (`laundry_id`) ON DELETE CASCADE;

--
-- Constraints for table `transaksi_detail`
--
ALTER TABLE `transaksi_detail`
  ADD CONSTRAINT `transaksi_detail_ibfk_1` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`transaksi_id`) ON DELETE CASCADE;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`laundry_id`) REFERENCES `laundry` (`laundry_id`) ON DELETE CASCADE;
COMMIT;
ALTER TABLE laundry ADD COLUMN no_rekening VARCHAR(100) DEFAULT NULL;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
