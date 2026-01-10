-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 10, 2026 at 08:02 AM
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
-- Database: `db_vuonquerestaurant`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `name`, `description`, `is_active`) VALUES
(1, 'Tất cả', 'Tất cả món ăn', 1),
(2, 'Đồ uống', 'Nước uống các loại', 1),
(3, 'Món chính', 'Các món ăn chính', 1),
(4, 'Món phụ', 'Các món ăn phụ', 1),
(5, 'Tráng miệng', 'Món tráng miệng', 1);

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `total_amount` decimal(12,2) DEFAULT NULL,
  `type_payment` enum('CAST','BANK') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu_item`
--

CREATE TABLE `menu_item` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_item`
--

INSERT INTO `menu_item` (`id`, `category_id`, `name`, `price`, `image_url`, `description`, `is_available`, `created_at`) VALUES
(1, 3, 'Bún bò Huế', 48000.00, 'https://file.hstatic.net/200000700229/article/bun-bo-hue-1_da318989e7c2493f9e2c3e010e722466.jpg', 'Bún bò Huế cay nồng đậm đà', 1, '2026-01-04 09:25:16'),
(2, 3, 'Cơm tấm sườn bì chả', 45000.00, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS03D3gzDYzWws3V3jc8JkrrDVb4v0EREstEw&s', 'Cơm tấm truyền thống với sườn nướng, bì và chả', 1, '2026-01-04 09:25:16'),
(3, 3, 'Phở bò', 50000.00, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRLfHmDctEmA_aq6zHuxpT9jOsjYfFJ2L9gsQ&s', 'Phở bò Hà Nội truyền thống', 1, '2026-01-04 09:25:16'),
(4, 3, 'Chả giò', 35000.00, 'https://homestory.com.vn/wp-content/uploads/2025/01/cach-lam-cha-gio-tom-thit-khoai-mon.jpg', 'Chả giò giòn rụm', 1, '2026-01-04 09:25:16'),
(5, 4, 'Gỏi cuốn', 30000.00, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRdkl9EWhoKeFKG1eS-qfxluewmVgirVwhkxg&s', 'Gỏi cuốn tôm thịt với rau sống', 1, '2026-01-04 09:25:16'),
(6, 2, 'Trà đá', 5000.00, 'https://product.hstatic.net/200000480127/product/tra_da_da927d9754664321a3e18e3a97adbb1a_master.jpg', 'Trà đá mát lạnh', 1, '2026-01-04 09:25:16'),
(7, 2, 'Nước ngọt', 15000.00, 'https://hyroenergy.vn/wp-content/uploads/2024/10/luong-duong-cua-cac-loai-nuoc-ngot-1.jpg', 'Coca, Pepsi, 7Up', 1, '2026-01-04 09:25:16'),
(8, 2, 'Nước chanh', 20000.00, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRT-kNjpdxonbBrWS6EaxziEegJLyfVw9lsyg&s', 'Nước chanh tươi', 1, '2026-01-04 09:25:16');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `table_number` int(11) NOT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `status` enum('CREATED','CONFIRMED','COOKING','DONE','PAID','CANCELLED') DEFAULT 'CREATED',
  `note` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `end_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_name`, `table_number`, `staff_id`, `status`, `note`, `created_at`, `end_at`) VALUES
(24, 'Khách', 1, NULL, 'PAID', '', '2026-01-10 11:34:49', '2026-01-10 11:35:51'),
(25, 'Khách', 1, NULL, 'PAID', '', '2026-01-10 11:36:18', '2026-01-10 13:54:42'),
(26, 'Khách', 3, NULL, 'PAID', '', '2026-01-10 13:54:21', '2026-01-10 13:56:06'),
(27, 'Khách', 3, NULL, 'PAID', '', '2026-01-10 13:58:25', '2026-01-10 13:58:42');

-- --------------------------------------------------------

--
-- Table structure for table `order_item`
--

CREATE TABLE `order_item` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` enum('WAITING','COOKING','DONE') DEFAULT 'WAITING',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_item`
--

INSERT INTO `order_item` (`id`, `order_id`, `menu_item_id`, `quantity`, `price`, `status`, `created_at`) VALUES
(39, 24, 7, 1, 15000.00, 'WAITING', '2026-01-10 11:34:49'),
(40, 24, 4, 1, 35000.00, 'WAITING', '2026-01-10 11:34:49'),
(41, 24, 3, 1, 50000.00, 'WAITING', '2026-01-10 11:34:49'),
(42, 25, 6, 1, 5000.00, 'DONE', '2026-01-10 11:36:18'),
(43, 25, 1, 1, 48000.00, 'DONE', '2026-01-10 11:36:18'),
(44, 26, 8, 1, 20000.00, 'DONE', '2026-01-10 13:54:21'),
(45, 26, 4, 1, 35000.00, 'DONE', '2026-01-10 13:54:21'),
(46, 27, 8, 1, 20000.00, 'DONE', '2026-01-10 13:58:25'),
(47, 27, 4, 1, 35000.00, 'DONE', '2026-01-10 13:58:25');

-- --------------------------------------------------------

--
-- Table structure for table `order_status_log`
--

CREATE TABLE `order_status_log` (
  `id` int(11) NOT NULL,
  `order_item_id` int(11) NOT NULL,
  `old_status` varchar(20) DEFAULT NULL,
  `new_status` varchar(20) DEFAULT NULL,
  `changed_by` int(11) DEFAULT NULL,
  `changed_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `method` enum('CASH','TRANSFER','QR') DEFAULT NULL,
  `status` enum('PENDING','PAID') DEFAULT 'PENDING',
  `paid_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `position` enum('STAFF','MANAGE','ADMIN') NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `cccd` varchar(20) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `full_name`, `username`, `password_hash`, `position`, `is_active`, `created_at`, `cccd`, `phone`, `email`, `address`) VALUES
(1, 'Nguyễn Văn A', 'nhanvien1', '$2y$10$atQOhiM6eA3OVG87uBb75eZaUAdwE06k5jI1ehXrooolT7r7wy4Pa', 'STAFF', 1, '2026-01-10 06:47:57', NULL, '0901234567', 'nhanvien1@vuonque.com', ''),
(2, 'Trần Thị B', 'quanly1', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZRGdjGj/n3.w7gYTTgI4qA1MrLN3G', 'MANAGE', 1, '2026-01-10 06:47:57', NULL, '0907654321', 'quanly1@vuonque.com', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`);

--
-- Indexes for table `menu_item`
--
ALTER TABLE `menu_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `menu_item_id` (`menu_item_id`);

--
-- Indexes for table `order_status_log`
--
ALTER TABLE `order_status_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_item_id` (`order_item_id`),
  ADD KEY `changed_by` (`changed_by`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `cccd` (`cccd`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_item`
--
ALTER TABLE `menu_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `order_item`
--
ALTER TABLE `order_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `order_status_log`
--
ALTER TABLE `order_status_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `invoice_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `menu_item`
--
ALTER TABLE `menu_item`
  ADD CONSTRAINT `menu_item_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`);

--
-- Constraints for table `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `order_item_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_item_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_item` (`id`);

--
-- Constraints for table `order_status_log`
--
ALTER TABLE `order_status_log`
  ADD CONSTRAINT `order_status_log_ibfk_1` FOREIGN KEY (`order_item_id`) REFERENCES `order_item` (`id`),
  ADD CONSTRAINT `order_status_log_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `staff` (`id`);

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoice` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
