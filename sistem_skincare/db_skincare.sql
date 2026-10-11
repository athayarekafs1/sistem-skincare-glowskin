-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 07, 2026 at 05:07 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_skincare`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id_barang` int(11) NOT NULL,
  `nama_barang` varchar(150) NOT NULL,
  `kategori` varchar(50) DEFAULT NULL,
  `harga` decimal(12,2) NOT NULL,
  `stok` int(11) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id_barang`, `nama_barang`, `kategori`, `harga`, `stok`, `gambar`, `deskripsi`, `created_at`) VALUES
(5, 'Glow Serum Vitamin C', 'Serum', 150000.00, 50, '6ac615e56f304.jpeg', 'Serum pencerah wajah dengan vitamin C 10% untuk kulit glowing.', '2026-10-07 09:45:09'),
(6, 'Niacinamide Serum 10%', 'Serum', 135000.00, 45, '6ac6175b4ae73.jpeg', 'Serum untuk mencerahkan dan mengontrol minyak berlebih.', '2026-10-07 09:45:09'),
(7, 'Hyaluronic Serum', 'Serum', 165000.00, 39, '6ac6180a40dc1.jpeg', 'Serum hidrasi untuk kulit kering dan kusam.', '2026-10-07 09:45:09'),
(8, 'Acne Spot Treatment', 'Treatment', 85000.00, 60, '6ac61a4675559.jpeg', 'Obat jerawat cepat kering dalam 3 hari.', '2026-10-07 09:45:09'),
(9, 'Retinol Night Cream', 'Treatment', 195000.00, 25, '6ac61b5b48ce1.jpeg', 'Krim malam anti-aging dengan retinol 0.5%.', '2026-10-07 09:45:09'),
(10, 'Centella Calming Gel', 'Treatment', 110000.00, 35, '6ac61c75714be.jpeg', 'Gel menenangkan kulit kemerahan dan iritasi.', '2026-10-07 09:45:09'),
(11, 'Sunscreen SPF 50 PA++++', 'Sunscreen', 120000.00, 99, '6ac61d7820c86.jpeg', 'Tabir surya ringan tidak lengket, cocok untuk kulit berminyak.', '2026-10-07 09:45:09'),
(12, 'Sunscreen Tinted SPF 35', 'Sunscreen', 140000.00, 69, '6ac61e48f0730.jpeg', 'Sunscreen dengan efek tinted, sekaligus jadi base makeup.', '2026-10-07 09:45:09'),
(13, 'Moisturizer Hyaluronic Acid', 'Moisturizer', 180000.00, 40, '6ac61f7f44aa5.jpeg', 'Pelembab dengan hyaluronic acid untuk kulit lembab seharian.', '2026-10-07 09:45:09'),
(14, 'Moisturizer Ceramide', 'Moisturizer', 165000.00, 53, '6ac6206619d35.jpeg', 'Pelembab dengan ceramide untuk memperkuat skin barrier.', '2026-10-07 09:45:09'),
(15, 'Gentle Facial Wash', 'Cleanser', 65000.00, 120, '6ac620f3165c0.jpeg', 'Sabun cuci muka lembut, cocok untuk semua jenis kulit.', '2026-10-07 09:45:09'),
(16, 'Micellar Water', 'Cleanser', 75000.00, 79, '6ac621ab2ace9.jpeg', 'Pembersih makeup dan kotoran tanpa bilas.', '2026-10-07 09:45:09'),
(17, 'Toner Centella', 'Toner', 95000.00, 64, '6ac622721fac5.jpeg', 'Toner menenangkan dengan centella asiatica.', '2026-10-07 09:45:09'),
(18, 'Exfoliating Toner AHA BHA', 'Toner', 125000.00, 43, '6ac6232862162.jpeg', 'Toner eksfoliasi untuk mengangkat sel kulit mati.', '2026-10-07 09:45:09'),
(20, 'Sheet Mask Set (5 pcs)', 'Mask', 55000.00, 45, '6ac624aa15d72.jpeg', 'Set masker wajah dengan berbagai varian', '2026-10-07 10:53:30');

-- --------------------------------------------------------

--
-- Table structure for table `detail_transaksi`
--

CREATE TABLE `detail_transaksi` (
  `id_detail` int(11) NOT NULL,
  `id_transaksi` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_transaksi`
--

INSERT INTO `detail_transaksi` (`id_detail`, `id_transaksi`, `id_barang`, `jumlah`, `subtotal`) VALUES
(13, 8, 20, 2, 110000.00),
(14, 9, 16, 1, 75000.00),
(15, 9, 14, 1, 165000.00),
(16, 10, 14, 1, 165000.00),
(17, 11, 20, 1, 55000.00),
(18, 12, 20, 1, 55000.00),
(19, 13, 12, 1, 140000.00),
(20, 14, 17, 1, 95000.00),
(21, 15, 18, 1, 125000.00),
(22, 16, 7, 1, 165000.00),
(23, 17, 11, 1, 120000.00),
(24, 18, 18, 1, 125000.00),
(25, 18, 20, 1, 55000.00);

-- --------------------------------------------------------

--
-- Table structure for table `komisi`
--

CREATE TABLE `komisi` (
  `id_komisi` int(11) NOT NULL,
  `id_transaksi` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `jumlah_komisi` decimal(12,2) NOT NULL,
  `status` enum('Pending','Dibayar') DEFAULT 'Pending',
  `tanggal_komisi` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `komisi`
--

INSERT INTO `komisi` (`id_komisi`, `id_transaksi`, `id_user`, `jumlah_komisi`, `status`, `tanggal_komisi`) VALUES
(1, 1, 2, 26750.00, 'Pending', '2026-10-07'),
(2, 2, 2, 11750.00, 'Pending', '2026-10-07'),
(3, 3, 2, 11750.00, 'Pending', '2026-10-07'),
(4, 4, 2, 4250.00, 'Pending', '2026-10-07'),
(5, 5, 2, 6000.00, 'Pending', '2026-10-07'),
(6, 6, 2, 6000.00, 'Dibayar', '2026-10-07'),
(7, 7, 2, 6000.00, 'Dibayar', '2026-10-07'),
(8, 8, 4, 5500.00, 'Pending', '2026-10-07'),
(9, 9, 5, 12000.00, 'Pending', '2026-10-07'),
(10, 10, 5, 8250.00, 'Pending', '2026-10-07'),
(11, 11, 5, 2750.00, 'Pending', '2026-10-07'),
(12, 12, 5, 2750.00, 'Pending', '2026-10-07'),
(13, 13, 5, 7000.00, 'Pending', '2026-10-07'),
(14, 14, 5, 4750.00, 'Pending', '2026-10-07'),
(15, 15, 5, 6250.00, 'Pending', '2026-10-07'),
(16, 16, 5, 8250.00, 'Pending', '2026-10-07'),
(17, 17, 5, 6000.00, 'Pending', '2026-10-07'),
(18, 18, 6, 9000.00, 'Dibayar', '2026-10-07');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `nama_pemesan` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `alamat` text DEFAULT NULL,
  `jumlah` int(11) NOT NULL,
  `total_bayar` decimal(12,2) NOT NULL,
  `metode_bayar` varchar(50) DEFAULT 'QRIS',
  `catatan` text DEFAULT NULL,
  `status` enum('Baru','Dibayar','Diproses','Selesai','Batal') DEFAULT 'Baru',
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id_pesanan`, `id_barang`, `nama_pemesan`, `no_hp`, `alamat`, `jumlah`, `total_bayar`, `metode_bayar`, `catatan`, `status`, `tanggal`) VALUES
(1, 20, 'Rianty', '081511884451', 'Cipulir', 2, 110000.00, 'QRIS', '', 'Selesai', '2026-10-07 12:10:31'),
(2, 20, 'Reka', '081511884451', 'Jl Merdeka', 1, 55000.00, 'QRIS', '', 'Selesai', '2026-10-07 12:57:05'),
(9, 18, 'Athaya Reka', '081511884453', 'sasa', 1, 125000.00, 'QRIS', '', 'Selesai', '2026-10-07 13:13:36'),
(10, 17, 'Yulianti', '098754568786', 'Tejo', 1, 95000.00, 'QRIS', '', 'Selesai', '2026-10-07 13:15:57'),
(11, 12, 'Athaya Reka', '089767657868', 'grogol', 1, 140000.00, 'QRIS', '', 'Selesai', '2026-10-07 13:23:47'),
(12, 20, 'Sukma', '098754568786', 'Babadan', 1, 55000.00, 'QRIS', '', 'Selesai', '2026-10-07 13:26:19'),
(13, 14, 'Lila', '081511884453', 'Indo', 1, 165000.00, 'QRIS', '', 'Selesai', '2026-10-07 13:27:35'),
(14, 14, 'Indah', '089767657868', 'jonggol', 1, 240000.00, 'Transfer Bank', '', 'Selesai', '2026-10-07 13:30:42'),
(15, 11, 'Sukma', '098754568786', 'Cipulir', 1, 120000.00, 'E-Wallet', '', 'Selesai', '2026-10-07 14:23:26'),
(16, 20, 'Karista', '098754568786', 'Jl Merdeka', 1, 180000.00, 'QRIS', '', 'Selesai', '2026-10-07 14:35:09');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan_detail`
--

CREATE TABLE `pesanan_detail` (
  `id_detail` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pesanan_detail`
--

INSERT INTO `pesanan_detail` (`id_detail`, `id_pesanan`, `id_barang`, `jumlah`, `subtotal`) VALUES
(1, 2, 20, 1, 55000.00),
(8, 9, 18, 1, 125000.00),
(9, 10, 17, 1, 95000.00),
(10, 11, 12, 1, 140000.00),
(11, 12, 20, 1, 55000.00),
(12, 13, 14, 1, 165000.00),
(13, 14, 14, 1, 165000.00),
(14, 14, 16, 1, 75000.00),
(15, 15, 11, 1, 120000.00),
(16, 16, 20, 1, 55000.00),
(17, 16, 18, 1, 125000.00);

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `kode_transaksi` varchar(50) NOT NULL,
  `tanggal` datetime DEFAULT current_timestamp(),
  `id_user` int(11) NOT NULL,
  `nama_pelanggan` varchar(100) DEFAULT NULL,
  `metode_bayar` varchar(50) DEFAULT 'Cash',
  `total_bayar` decimal(12,2) NOT NULL,
  `uang_diterima` decimal(12,2) DEFAULT 0.00,
  `kembalian` decimal(12,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `kode_transaksi`, `tanggal`, `id_user`, `nama_pelanggan`, `metode_bayar`, `total_bayar`, `uang_diterima`, `kembalian`) VALUES
(1, 'TRX-20261007-926F3', '2026-10-07 15:30:08', 2, 'reka', 'QRIS', 535000.00, 0.00, 0.00),
(2, 'TRX-20261007-2C829', '2026-10-07 15:38:16', 2, 'dinda', 'QRIS', 235000.00, 0.00, 0.00),
(3, 'TRX-20261007-9DA00', '2026-10-07 15:41:05', 2, 'dinda', 'QRIS', 235000.00, 0.00, 0.00),
(4, 'TRX-20261007-F3078', '2026-10-07 15:45:24', 2, 'Agus', 'E-Wallet', 85000.00, 0.00, 0.00),
(5, 'TRX-20261007-3F770', '2026-10-07 15:47:07', 2, 'Dwi Agus', 'Cash', 120000.00, 0.00, 0.00),
(6, 'TRX-20261007-C69E3', '2026-10-07 15:54:18', 2, 'Agus', 'Cash', 120000.00, 150000.00, 30000.00),
(7, 'TRX-20261007-1B42E', '2026-10-07 15:54:38', 2, 'Agus', 'QRIS', 120000.00, 120000.00, 0.00),
(8, 'TRX-20261007-75E34', '2026-10-07 19:23:46', 4, 'Rianty', 'QRIS', 110000.00, 110000.00, 0.00),
(9, 'TRX-20261007-27FEB', '2026-10-07 20:44:20', 5, 'Indah', 'Transfer Bank', 240000.00, 240000.00, 0.00),
(10, 'TRX-20261007-63658', '2026-10-07 20:44:52', 5, 'Lila', 'QRIS', 165000.00, 165000.00, 0.00),
(11, 'TRX-20261007-32E1C', '2026-10-07 20:45:18', 5, 'Reka', 'QRIS', 55000.00, 55000.00, 0.00),
(12, 'TRX-20261007-97EED', '2026-10-07 20:45:37', 5, 'Sukma', 'QRIS', 55000.00, 55000.00, 0.00),
(13, 'TRX-20261007-2CCFD', '2026-10-07 21:00:16', 5, 'Athaya Reka', 'QRIS', 140000.00, 140000.00, 0.00),
(14, 'TRX-20261007-71F74', '2026-10-07 21:02:03', 5, 'Yulianti', 'QRIS', 95000.00, 95000.00, 0.00),
(15, 'TRX-20261007-6D2E5', '2026-10-07 21:08:29', 5, 'Athaya Reka', 'QRIS', 125000.00, 125000.00, 0.00),
(16, 'TRX-20261007-DCF7F', '2026-10-07 21:21:20', 5, 'Agus', 'Cash', 165000.00, 200000.00, 35000.00),
(17, 'TRX-20261007-8830D', '2026-10-07 21:23:59', 5, 'Sukma', 'E-Wallet', 120000.00, 120000.00, 0.00),
(18, 'TRX-20261007-B6CE4', '2026-10-07 21:36:26', 6, 'Karista', 'QRIS', 180000.00, 180000.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','sales') NOT NULL,
  `komisi_persen` decimal(5,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nama_lengkap`, `username`, `password`, `role`, `komisi_persen`, `created_at`) VALUES
(1, 'Admin GlowSkin', 'admin', 'admin123', 'admin', 0.00, '2026-10-07 08:11:22'),
(2, 'Budi Sales', 'budi', 'sales123', 'sales', 5.00, '2026-10-07 08:11:22'),
(3, 'Siti Sales', 'siti', 'sales123', 'sales', 5.00, '2026-10-07 08:11:22'),
(4, 'Athaya', 'Athaya', '123', 'sales', 5.00, '2026-10-07 12:06:22'),
(5, 'Salsabila', 'Salsabila', '123', 'sales', 5.00, '2026-10-07 12:06:49'),
(6, 'Regi', 'Regi', '123', 'sales', 5.00, '2026-10-07 12:07:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id_barang`);

--
-- Indexes for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_transaksi` (`id_transaksi`),
  ADD KEY `id_barang` (`id_barang`);

--
-- Indexes for table `komisi`
--
ALTER TABLE `komisi`
  ADD PRIMARY KEY (`id_komisi`),
  ADD KEY `id_transaksi` (`id_transaksi`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`),
  ADD KEY `id_barang` (`id_barang`);

--
-- Indexes for table `pesanan_detail`
--
ALTER TABLE `pesanan_detail`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_pesanan` (`id_pesanan`),
  ADD KEY `id_barang` (`id_barang`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD UNIQUE KEY `kode_transaksi` (`kode_transaksi`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id_barang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `komisi`
--
ALTER TABLE `komisi`
  MODIFY `id_komisi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `pesanan_detail`
--
ALTER TABLE `pesanan_detail`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD CONSTRAINT `detail_transaksi_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_transaksi_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`);

--
-- Constraints for table `komisi`
--
ALTER TABLE `komisi`
  ADD CONSTRAINT `komisi_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`) ON DELETE CASCADE,
  ADD CONSTRAINT `komisi_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`);

--
-- Constraints for table `pesanan_detail`
--
ALTER TABLE `pesanan_detail`
  ADD CONSTRAINT `pesanan_detail_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE,
  ADD CONSTRAINT `pesanan_detail_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`);

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
