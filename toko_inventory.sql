-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 27, 2026 at 12:28 PM
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
-- Database: `toko_inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id_barang` int(11) NOT NULL,
  `nama_barang` varchar(100) DEFAULT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `harga` int(11) DEFAULT NULL,
  `stok` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id_barang`, `nama_barang`, `kategori`, `harga`, `stok`) VALUES
(15, 'bella squere', 'Jilbab', 120000, 10),
(16, 'Kemeja Flannel', 'Atasan', 150000, 25),
(17, 'Celana Jeans Slim', 'Bawahan', 220000, 12),
(18, 'Dress Floral', 'Dress', 185000, 8),
(19, 'Jaket Denim', 'Outerwear', 350000, 3),
(20, 'Kaos Polos', 'Atasan', 75000, 0),
(21, 'Rok Mini', 'Bawahan', 120000, 15),
(22, 'Kemeja Flannel', 'Atasan', 150000, 25),
(23, 'Celana Jeans Slim', 'Bawahan', 220000, 12),
(24, 'Dress Floral', 'Dress', 185000, 8),
(25, 'Jaket Denim', 'Outerwear', 350000, 3),
(26, 'Kaos Polos', 'Atasan', 75000, 0),
(27, 'Rok Mini', 'Bawahan', 120000, 15);

-- --------------------------------------------------------

--
-- Table structure for table `detail_hutang`
--

CREATE TABLE `detail_hutang` (
  `id_detail` int(11) NOT NULL,
  `id_transaksi` int(11) NOT NULL,
  `nama_barang` varchar(150) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `satuan` varchar(30) DEFAULT 'pcs',
  `harga_satuan` decimal(15,0) NOT NULL DEFAULT 0,
  `subtotal` decimal(15,0) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `keuangan`
--

CREATE TABLE `keuangan` (
  `id_keuangan` int(11) NOT NULL,
  `jenis` enum('pemasukan','pengeluaran') NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `jumlah` decimal(15,0) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `tanggal` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kontak`
--

CREATE TABLE `kontak` (
  `id_kontak` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kontak`
--

INSERT INTO `kontak` (`id_kontak`, `nama`, `telepon`, `keterangan`, `created_at`) VALUES
(1, 'gaqha', '08119988772', 'pelanggan', '2026-03-19 23:50:19');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran_hutang`
--

CREATE TABLE `pembayaran_hutang` (
  `id_bayar` int(11) NOT NULL,
  `id_transaksi` int(11) NOT NULL,
  `jumlah_bayar` decimal(15,0) NOT NULL,
  `tanggal_bayar` date NOT NULL,
  `catatan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_hutang`
--

CREATE TABLE `transaksi_hutang` (
  `id_transaksi` int(11) NOT NULL,
  `id_kontak` int(11) NOT NULL,
  `jenis` enum('hutang','piutang') NOT NULL,
  `total_tagihan` decimal(15,0) NOT NULL DEFAULT 0,
  `total_bayar` decimal(15,0) NOT NULL DEFAULT 0,
  `tanggal_transaksi` date NOT NULL,
  `keterangan` text DEFAULT NULL,
  `status` enum('belum_bayar','sebagian','lunas') NOT NULL DEFAULT 'belum_bayar',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `password`, `created_at`) VALUES
(1, 'Administrator', 'admin', '$2y$10$v2fZkdwu5J7j5cyovySPoO6rvMbpMoJ66Rew4bWIV0eAx0nNN4zGq', '2026-03-27 06:47:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id_barang`);

--
-- Indexes for table `detail_hutang`
--
ALTER TABLE `detail_hutang`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_transaksi` (`id_transaksi`);

--
-- Indexes for table `keuangan`
--
ALTER TABLE `keuangan`
  ADD PRIMARY KEY (`id_keuangan`);

--
-- Indexes for table `kontak`
--
ALTER TABLE `kontak`
  ADD PRIMARY KEY (`id_kontak`);

--
-- Indexes for table `pembayaran_hutang`
--
ALTER TABLE `pembayaran_hutang`
  ADD PRIMARY KEY (`id_bayar`),
  ADD KEY `id_transaksi` (`id_transaksi`);

--
-- Indexes for table `transaksi_hutang`
--
ALTER TABLE `transaksi_hutang`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_kontak` (`id_kontak`);

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
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id_barang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `detail_hutang`
--
ALTER TABLE `detail_hutang`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `keuangan`
--
ALTER TABLE `keuangan`
  MODIFY `id_keuangan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kontak`
--
ALTER TABLE `kontak`
  MODIFY `id_kontak` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pembayaran_hutang`
--
ALTER TABLE `pembayaran_hutang`
  MODIFY `id_bayar` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaksi_hutang`
--
ALTER TABLE `transaksi_hutang`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_hutang`
--
ALTER TABLE `detail_hutang`
  ADD CONSTRAINT `detail_hutang_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi_hutang` (`id_transaksi`) ON DELETE CASCADE;

--
-- Constraints for table `pembayaran_hutang`
--
ALTER TABLE `pembayaran_hutang`
  ADD CONSTRAINT `pembayaran_hutang_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi_hutang` (`id_transaksi`) ON DELETE CASCADE;

--
-- Constraints for table `transaksi_hutang`
--
ALTER TABLE `transaksi_hutang`
  ADD CONSTRAINT `transaksi_hutang_ibfk_1` FOREIGN KEY (`id_kontak`) REFERENCES `kontak` (`id_kontak`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
