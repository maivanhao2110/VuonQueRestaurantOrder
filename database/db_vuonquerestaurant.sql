-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th1 09, 2026 lúc 11:34 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `db_vuonquerestaurant`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `category`
--

INSERT INTO `category` (`id`, `name`, `description`, `is_active`) VALUES
(1, 'Tất cả', 'Tất cả món ăn', 1),
(2, 'Đồ uống', 'Nước uống các loại', 1),
(3, 'Món chính', 'Các món ăn chính', 1),
(4, 'Món phụ', 'Các món ăn phụ', 1),
(5, 'Tráng miệng', 'Món tráng miệng', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `invoice`
--

CREATE TABLE `invoice` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `total_amount` decimal(12,2) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `menu_item`
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
-- Đang đổ dữ liệu cho bảng `menu_item`
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
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `table_number` int(11) NOT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `status` enum('CREATED','CONFIRMED','COOKING','DONE','CANCELLED') DEFAULT 'CREATED',
  `note` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `end_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `customer_name`, `table_number`, `staff_id`, `status`, `note`, `created_at`, `end_at`) VALUES
(1, 'Khách', 1, NULL, 'DONE', '', '2026-01-04 09:28:47', NULL),
(2, 'Khách', 1, NULL, 'DONE', '', '2026-01-04 09:31:46', NULL),
(3, 'Khách', 1, NULL, 'DONE', '', '2026-01-04 09:34:27', NULL),
(4, 'Khách', 1, NULL, 'CREATED', '', '2026-01-04 09:40:30', NULL),
(5, 'Khách', 1, NULL, 'CANCELLED', '', '2026-01-04 09:42:18', NULL),
(6, 'Khách', 1, NULL, 'CONFIRMED', '', '2026-01-04 09:45:04', NULL),
(7, 'Khách', 1, NULL, 'COOKING', '', '2026-01-04 09:59:25', NULL),
(8, 'Khách', 6, NULL, 'CREATED', '', '2026-01-04 10:12:21', NULL),
(9, 'Khách', 2, NULL, 'CREATED', '', '2026-01-04 10:15:57', NULL),
(10, 'Khách', 1, NULL, 'CREATED', '', '2026-01-04 10:17:29', NULL),
(11, 'Khách', 1, NULL, 'CREATED', '', '2026-01-04 10:20:02', NULL),
(12, 'Khách', 1, NULL, 'CREATED', '', '2026-01-04 10:26:16', NULL),
(13, 'Khách', 1, NULL, 'CREATED', '', '2026-01-04 10:32:24', NULL),
(14, 'Khách', 1, NULL, 'CREATED', '', '2026-01-04 10:32:34', NULL),
(15, 'Khách', 1, NULL, 'CREATED', '', '2026-01-04 10:34:02', NULL),
(16, 'Khách', 1, NULL, 'CREATED', '', '2026-01-04 10:46:02', NULL),
(17, 'Khách', 1, NULL, 'CREATED', '', '2026-01-04 10:57:30', NULL),
(18, 'Khách', 1, NULL, 'CREATED', '', '2026-01-04 11:00:13', NULL),
(19, 'Khách', 1, NULL, 'CREATED', '', '2026-01-04 11:11:19', NULL),
(20, 'Khách', 1, NULL, 'CREATED', 'Không cay', '2026-01-04 12:31:28', NULL),
(21, 'Khách', 1, NULL, 'CREATED', '', '2026-01-04 12:40:04', NULL),
(22, 'Khách', 1, NULL, 'CREATED', '', '2026-01-09 15:58:07', NULL),
(23, 'Khách', 1, NULL, 'DONE', '', '2026-01-09 16:53:20', '2026-01-09 17:32:05'),
(24, 'Khách', 1, NULL, 'CREATED', '', '2026-01-09 17:21:09', '2026-01-09 17:21:31'),
(25, 'Khách', 3, NULL, 'CREATED', '', '2026-01-09 17:26:27', '2026-01-09 17:26:36'),
(26, 'Khách', 7, NULL, 'CREATED', '', '2026-01-09 17:28:11', NULL),
(27, 'Khách', 3, NULL, 'CREATED', '', '2026-01-09 17:28:20', '2026-01-09 17:29:44'),
(28, 'Khách', 5, NULL, 'DONE', '', '2026-01-09 17:28:28', '2026-01-09 17:32:21');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_item`
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
-- Đang đổ dữ liệu cho bảng `order_item`
--

INSERT INTO `order_item` (`id`, `order_id`, `menu_item_id`, `quantity`, `price`, `status`, `created_at`) VALUES
(1, 1, 8, 1, 20000.00, 'DONE', '2026-01-04 09:28:47'),
(2, 2, 3, 1, 50000.00, 'WAITING', '2026-01-04 09:31:46'),
(3, 3, 8, 1, 20000.00, 'WAITING', '2026-01-04 09:34:27'),
(4, 4, 4, 1, 35000.00, 'WAITING', '2026-01-04 09:40:30'),
(5, 5, 7, 1, 15000.00, 'WAITING', '2026-01-04 09:42:18'),
(6, 6, 8, 1, 20000.00, 'WAITING', '2026-01-04 09:45:04'),
(7, 7, 8, 1, 20000.00, 'WAITING', '2026-01-04 09:59:25'),
(8, 7, 7, 1, 15000.00, 'WAITING', '2026-01-04 09:59:25'),
(9, 8, 8, 1, 20000.00, 'WAITING', '2026-01-04 10:12:21'),
(10, 9, 8, 15, 20000.00, 'WAITING', '2026-01-04 10:15:57'),
(11, 9, 7, 14, 15000.00, 'WAITING', '2026-01-04 10:15:57'),
(12, 9, 6, 1, 5000.00, 'WAITING', '2026-01-04 10:15:57'),
(13, 9, 1, 1, 48000.00, 'WAITING', '2026-01-04 10:15:57'),
(14, 9, 4, 1, 35000.00, 'WAITING', '2026-01-04 10:15:57'),
(15, 9, 2, 1, 45000.00, 'WAITING', '2026-01-04 10:15:57'),
(16, 9, 3, 1, 50000.00, 'WAITING', '2026-01-04 10:15:57'),
(17, 9, 5, 1, 30000.00, 'WAITING', '2026-01-04 10:15:57'),
(18, 10, 8, 1, 20000.00, 'WAITING', '2026-01-04 10:17:29'),
(19, 10, 7, 1, 15000.00, 'WAITING', '2026-01-04 10:17:29'),
(20, 11, 7, 1, 15000.00, 'WAITING', '2026-01-04 10:20:02'),
(21, 11, 6, 1, 5000.00, 'WAITING', '2026-01-04 10:20:02'),
(22, 12, 8, 1, 20000.00, 'WAITING', '2026-01-04 10:26:16'),
(23, 12, 7, 3, 15000.00, 'WAITING', '2026-01-04 10:26:16'),
(24, 12, 2, 1, 45000.00, 'WAITING', '2026-01-04 10:26:16'),
(25, 13, 8, 1, 20000.00, 'WAITING', '2026-01-04 10:32:24'),
(26, 14, 2, 1, 45000.00, 'WAITING', '2026-01-04 10:32:34'),
(27, 15, 6, 1, 5000.00, 'WAITING', '2026-01-04 10:34:02'),
(28, 16, 2, 1, 45000.00, 'WAITING', '2026-01-04 10:46:02'),
(29, 17, 8, 1, 20000.00, 'WAITING', '2026-01-04 10:57:30'),
(30, 18, 8, 2, 20000.00, 'WAITING', '2026-01-04 11:00:13'),
(31, 19, 7, 17, 15000.00, 'WAITING', '2026-01-04 11:11:19'),
(32, 20, 2, 5, 45000.00, 'WAITING', '2026-01-04 12:31:28'),
(33, 21, 1, 4, 48000.00, 'WAITING', '2026-01-04 12:40:04'),
(34, 21, 4, 1, 35000.00, 'WAITING', '2026-01-04 12:40:04'),
(35, 21, 2, 4, 45000.00, 'WAITING', '2026-01-04 12:40:04'),
(36, 22, 8, 1, 20000.00, 'WAITING', '2026-01-09 15:58:07'),
(37, 23, 8, 1, 20000.00, 'WAITING', '2026-01-09 16:53:20'),
(38, 23, 4, 1, 35000.00, 'WAITING', '2026-01-09 16:53:20'),
(39, 24, 8, 1, 20000.00, 'WAITING', '2026-01-09 17:21:09'),
(40, 25, 8, 1, 20000.00, 'WAITING', '2026-01-09 17:26:27'),
(41, 26, 7, 1, 15000.00, 'WAITING', '2026-01-09 17:28:11'),
(42, 27, 2, 1, 45000.00, 'WAITING', '2026-01-09 17:28:20'),
(43, 28, 5, 1, 30000.00, 'WAITING', '2026-01-09 17:28:28');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_status_log`
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
-- Cấu trúc bảng cho bảng `payment`
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
-- Cấu trúc bảng cho bảng `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `cccd` varchar(20) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`);

--
-- Chỉ mục cho bảng `menu_item`
--
ALTER TABLE `menu_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Chỉ mục cho bảng `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `menu_item_id` (`menu_item_id`);

--
-- Chỉ mục cho bảng `order_status_log`
--
ALTER TABLE `order_status_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_item_id` (`order_item_id`),
  ADD KEY `changed_by` (`changed_by`);

--
-- Chỉ mục cho bảng `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Chỉ mục cho bảng `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `cccd` (`cccd`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `invoice`
--
ALTER TABLE `invoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `menu_item`
--
ALTER TABLE `menu_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT cho bảng `order_item`
--
ALTER TABLE `order_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT cho bảng `order_status_log`
--
ALTER TABLE `order_status_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `payment`
--
ALTER TABLE `payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `invoice_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Các ràng buộc cho bảng `menu_item`
--
ALTER TABLE `menu_item`
  ADD CONSTRAINT `menu_item_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`);

--
-- Các ràng buộc cho bảng `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `order_item_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_item_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_item` (`id`);

--
-- Các ràng buộc cho bảng `order_status_log`
--
ALTER TABLE `order_status_log`
  ADD CONSTRAINT `order_status_log_ibfk_1` FOREIGN KEY (`order_item_id`) REFERENCES `order_item` (`id`),
  ADD CONSTRAINT `order_status_log_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `staff` (`id`);

--
-- Các ràng buộc cho bảng `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoice` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
