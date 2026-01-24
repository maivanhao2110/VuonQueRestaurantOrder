-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 24, 2026 at 07:33 AM
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
(5, 'Tráng miệng', 'Món tráng miệng', 1),
(6, 'Khai vị', 'Các món ăn khai vị cho một buổi tiệc tuyệt vời', 1);

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

--
-- Dumping data for table `invoice`
--

INSERT INTO `invoice` (`id`, `order_id`, `total_amount`, `type_payment`, `created_at`) VALUES
(1, 30, 229000.00, 'CAST', '2026-01-24 12:48:00');

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
(9, 3, 'Gà hấp hành', 369000.00, 'https://th.bing.com/th/id/OIP.oJtPIWPTzjChLuQQ4TnzewHaET?w=298&h=180&c=7&r=0&o=7&cb=defcache2&dpr=1.3&pid=1.7&rm=3&defcache=1', 'Gà hấp hành thơm mềm, giữ trọn vị ngọt', 1, '2026-01-24 12:18:47'),
(10, 3, 'Gà hấp mắm nhĩ', 369000.00, 'https://th.bing.com/th/id/OIP.uviXzHgscdDXVRWEgqAn0AHaFP?w=261&h=184&c=7&r=0&o=7&cb=defcache2&dpr=1.3&pid=1.7&rm=3&defcache=1', 'Gà hấp mắm nhĩ đậm đà hương vị truyền thống', 1, '2026-01-24 12:18:47'),
(11, 3, 'Gà rang muối', 369000.00, 'https://th.bing.com/th/id/OIP.27hhIwP4dIJJXwIWhElhgQHaEK?w=328&h=184&c=7&r=0&o=7&cb=defcache2&dpr=1.3&pid=1.7&rm=3&defcache=1', 'Gà rang muối da giòn, mặn mà', 1, '2026-01-24 12:18:47'),
(12, 3, 'Gà nướng muối ớt', 369000.00, 'https://th.bing.com/th/id/OIP.I4XIJYqGmC29Z-ANUpwI-AHaEx?w=314&h=180&c=7&r=0&o=7&cb=defcache2&dpr=1.3&pid=1.7&rm=3&defcache=1', 'Gà nướng muối ớt cay thơm hấp dẫn', 1, '2026-01-24 12:18:47'),
(13, 3, 'Gà nướng mắc khén mật ong', 369000.00, 'https://th.bing.com/th/id/OIP.p2bgANdB6HvOaArrXYcgWwHaEo?w=322&h=180&c=7&r=0&o=7&cb=defcache2&dpr=1.3&pid=1.7&rm=3&defcache=1', 'Gà nướng mắc khén mật ong hương vị Tây Bắc', 1, '2026-01-24 12:18:47'),
(14, 3, 'Lẩu gà tiềm ớt hiểm', 389000.00, 'https://th.bing.com/th/id/OIP.lBA3O1XJ4Agtw_bz-Q2sUgHaFj?w=222&h=180&c=7&r=0&o=7&cb=defcache2&dpr=1.3&pid=1.7&rm=3&defcache=1', 'Lẩu gà tiềm ớt hiểm cay nồng bổ dưỡng', 1, '2026-01-24 12:18:47'),
(15, 3, 'Lẩu gà nấu lá giang', 389000.00, 'https://th.bing.com/th/id/OIP.V9HFnafAfU_3rFFBSHZlUQHaFF?w=270&h=186&c=7&r=0&o=7&cb=defcache2&dpr=1.3&pid=1.7&rm=3&defcache=1', 'Lẩu gà nấu lá giang chua thanh dễ ăn', 1, '2026-01-24 12:18:47'),
(16, 3, 'Lẩu gà tiềm thuốc bắc', 389000.00, 'https://th.bing.com/th/id/OIP.EJHbZo18ac3GJV3fYiFZXgHaEK?o=7&cb=defcache2rm=3&defcache=1&rs=1&pid=ImgDetMain&o=7&rm=3', 'Lẩu gà tiềm thuốc bắc bổ dưỡng', 1, '2026-01-24 12:18:47'),
(17, 3, 'Chim bồ câu bằm xúc bánh đa', 169000.00, 'https://th.bing.com/th/id/OIP.BEqPgqGB_K6uqkAMxKfS9QHaFS?w=249&h=180&c=7&r=0&o=7&cb=defcache2&dpr=1.3&pid=1.7&rm=3&defcache=1', 'Chim bồ câu bằm xào ăn kèm bánh đa', 1, '2026-01-24 12:27:11'),
(18, 3, 'Chim bồ câu quay', 169000.00, 'https://th.bing.com/th/id/OIP.zccSqszvpX1Wu-sSNfDLWwHaEo?w=267&h=180&c=7&r=0&o=7&cb=defcache2&dpr=1.3&pid=1.7&rm=3&defcache=1', 'Chim bồ câu quay da giòn', 1, '2026-01-24 12:27:11'),
(19, 3, 'Chim bồ câu nướng muối ớt', 169000.00, 'https://vn1.vdrive.vn/tuulaunamdinh.com/2022/01/Tuu-Lam-Nam-Dinh-Mon-An-50-Chim-Cau-Chim-Cau-Nuong-Muoi-Ot.jpg', 'Chim bồ câu nướng muối ớt', 1, '2026-01-24 12:27:11'),
(20, 3, 'Chim bồ câu tiềm thuốc bắc', 239000.00, 'https://th.bing.com/th/id/OIP.ijM-cXjOxl6XMmv_weFD-QHaE8?w=236&h=180&c=7&r=0&o=7&cb=defcache2&dpr=1.3&pid=1.7&rm=3&defcache=1', 'Chim bồ câu tiềm thuốc bắc thơm ngon', 1, '2026-01-24 12:27:11'),
(21, 3, 'Ếch rang muối', 159000.00, 'https://th.bing.com/th/id/OIP.XCAxaPnYHpNzKn59S0z0mQHaFj?w=209&h=180&c=7&r=0&o=7&cb=defcache2&dpr=1.3&pid=1.7&rm=3&defcache=1', 'Ếch rang muối giòn thơm', 1, '2026-01-24 12:27:11'),
(22, 3, 'Ếch xào mướp', 159000.00, 'https://th.bing.com/th/id/OIP.3dinX_-7BIQh_1AgjnhEpAHaEK?w=320&h=180&c=7&r=0&o=7&cb=defcache2&dpr=1.3&pid=1.7&rm=3&defcache=1', 'Ếch xào mướp thanh mát', 1, '2026-01-24 12:27:11'),
(23, 3, 'Ếch xào măng', 159000.00, 'https://tse3.mm.bing.net/th/id/OIP.fsmbSMkNMVfvePTgrNofrwHaET?cb=defcache2&defcache=1&rs=1&pid=ImgDetMain&o=7&rm=3', 'Ếch xào măng chua nhẹ', 1, '2026-01-24 12:27:11'),
(24, 3, 'Ếch xào lá lốt', 159000.00, 'https://th.bing.com/th/id/OIP.zJDhrN5Hmc7MdN-xOMfSVQHaEK?w=321&h=180&c=7&r=0&o=7&cb=defcache2&dpr=1.3&pid=1.7&rm=3&defcache=1', 'Ếch xào lá lốt thơm', 1, '2026-01-24 12:27:11'),
(25, 3, 'Ếch chiên nước mắm', 159000.00, 'https://th.bing.com/th/id/OIP.bqFeXIu7Q33ouLb1mkcn8AHaF4?w=202&h=180&c=7&r=0&o=7&cb=defcache2&dpr=1.3&pid=1.7&rm=3&defcache=1', 'Ếch chiên nước mắm đậm vị', 1, '2026-01-24 12:27:11'),
(26, 3, 'Ếch chui rơm', 159000.00, 'https://tse1.mm.bing.net/th/id/OIP.b8AN4pUhY21INVFx7UbkZwHaEK?cb=defcache2&defcache=1&rs=1&pid=ImgDetMain&o=7&rm=3', 'Ếch chui rơm đặc sắc', 1, '2026-01-24 12:27:11'),
(27, 3, 'Ếch nướng muối ớt', 159000.00, 'https://th.bing.com/th/id/OIP.t3e_ACAihFZ0-MT-QVs25QHaE8?w=268&h=180&c=7&r=0&o=7&cb=defcache2&dpr=1.3&pid=1.7&rm=3&defcache=1', 'Ếch nướng muối ớt cay ngon', 1, '2026-01-24 12:27:11'),
(28, 3, 'Ếch om chuối đậu', 239000.00, 'https://tse1.mm.bing.net/th/id/OIP.MEq2_kf8IdjHGxUfp8R33gHaEK?cb=defcache2&defcache=1&rs=1&pid=ImgDetMain&o=7&rm=3', 'Ếch om chuối đậu béo bùi', 1, '2026-01-24 12:27:11'),
(29, 3, 'Lươn chiên giòn mắm me', 199000.00, 'https://th.bing.com/th/id/OIP.oWaS-B7RwzzVa9p7RMZO1wHaE8?w=232&h=180&c=7&r=0&o=7&cb=defcache2&dpr=1.3&pid=1.7&rm=3&defcache=1', 'Lươn chiên giòn mắm me', 1, '2026-01-24 12:27:11'),
(30, 3, 'Lươn nướng muối ớt', 199000.00, 'https://th.bing.com/th/id/OIP.cdQrW6C-OMojXp39R-EkEAHaEL?w=302&h=180&c=7&r=0&o=7&cb=defcache2&dpr=1.3&pid=1.7&rm=3&defcache=1', 'Lươn nướng muối ớt thơm', 1, '2026-01-24 12:27:11'),
(31, 3, 'Lươn xào sả ớt', 199000.00, 'https://i.ytimg.com/vi/tcQb5Xw0s6k/maxresdefault.jpg', 'Lươn xào sả ớt cay', 1, '2026-01-24 12:27:11'),
(32, 3, 'Lươn xào lá lốt', 199000.00, 'https://i.ytimg.com/vi/wgwa4vf_0Bk/maxresdefault.jpg', 'Lươn xào lá lốt', 1, '2026-01-24 12:27:11'),
(33, 3, 'Lươn om chuối đậu', 269000.00, 'https://tse2.mm.bing.net/th/id/OIP.OhHHqFhViUEWwE37D9mCVgHaFj?cb=defcache2&defcache=1&rs=1&pid=ImgDetMain&o=7&rm=3', 'Lươn om chuối đậu đậm vị', 1, '2026-01-24 12:27:11'),
(34, 5, 'Nem nắm Vườn Quê', 79000.00, 'https://th.bing.com/th/id/OIP.oJtPIWPTzjChLuQQ4TnzewHaET?w=298&h=180&c=7&r=0&o=7&cb=defcache2&dpr=1.3&pid=1.7&rm=3&defcache=1', '', 1, '2026-01-24 12:29:08'),
(35, 5, 'Cá đù 1 nắng chiên giòn', 89000.00, 'https://th.bing.com/th/id/OIP.uviXzHgscdDXVRWEgqAn0AHaFP?w=261&h=184&c=7&r=0&o=7&cb=defcache2&dpr=1.3&pid=1.7&rm=3&defcache=1', '', 1, '2026-01-24 12:29:08'),
(36, 5, 'Cá trinh 1 nắng chiên giòn', 119000.00, 'https://th.bing.com/th/id/OIP.27hhIwP4dIJJXwIWhElhgQHaEK?w=328&h=184&c=7&r=0&o=7&cb=defcache2&dpr=1.3&pid=1.7&rm=3&defcache=1', '', 1, '2026-01-24 12:29:08'),
(37, 5, 'Khoai tây chiên', 60000.00, 'https://th.bing.com/th/id/OIP.I4XIJYqGmC29Z-ANUpwI-AHaEx?w=314&h=180&c=7&r=0&o=7&cb=defcache2&dpr=1.3&pid=1.7&rm=3&defcache=1', '', 1, '2026-01-24 12:29:08'),
(38, 5, 'Đậu hũ chiên giòn', 60000.00, 'https://th.bing.com/th/id/OIP.p2bgANdB6HvOaArrXYcgWwHaEo?w=322&h=180&c=7&r=0&o=7&cb=defcache2&dpr=1.3&pid=1.7&rm=3&defcache=1', '', 1, '2026-01-24 12:29:08'),
(39, 5, 'Đậu hũ lướt ván', 60000.00, 'https://th.bing.com/th/id/OIP.lBA3O1XJ4Agtw_bz-Q2sUgHaFj?w=222&h=180&c=7&r=0&o=7&cb=defcache2&dpr=1.3&pid=1.7&rm=3&defcache=1', '', 1, '2026-01-24 12:29:08'),
(40, 5, 'Đậu hũ chiên sả', 79000.00, 'https://th.bing.com/th/id/OIP.V9HFnafAfU_3rFFBSHZlUQHaFF?w=270&h=186&c=7&r=0&o=7&cb=defcache2&dpr=1.3&pid=1.7&rm=3&defcache=1', '', 1, '2026-01-24 12:29:08'),
(41, 5, 'Trứng chiên lá mơ', 79000.00, 'https://th.bing.com/th/id/OIP.EJHbZo18ac3GJV3fYiFZXgHaEK?o=7&cb=defcache2&rm=3&defcache=1&rs=1&pid=ImgDetMain', '', 1, '2026-01-24 12:29:08'),
(42, 5, 'Đậu hũ mỡ hành', 79000.00, 'https://th.bing.com/th/id/OIP.EJHbZo18ac3GJV3fYiFZXgHaEK?o=7&cb=defcache2&rm=3&defcache=1&rs=1&pid=ImgDetMain', '', 1, '2026-01-24 12:29:08'),
(54, 2, 'Bia Hà Nội', 25000.00, 'https://th.bing.com/th/id/OIP.yz3n6zY3S3Y4ZpJk8x8n9gHaHa', 'Bia Hà Nội lon/chai', 1, '2026-01-24 12:32:47'),
(55, 2, 'Bia Tiger', 30000.00, 'https://th.bing.com/th/id/OIP.wC0eGz7y3J0N9lFQn7FZJgHaHa', 'Bia Tiger lon', 1, '2026-01-24 12:32:47'),
(56, 2, 'Bia Heineken', 35000.00, 'https://th.bing.com/th/id/OIP.0m8bH0P3y1pKJZy2H8mXqAHaHa', 'Bia Heineken lon', 1, '2026-01-24 12:32:47'),
(57, 2, 'Bia Sài Gòn', 25000.00, 'https://th.bing.com/th/id/OIP.z1X9zWc0n9E3J7K6G5Y1tAHaHa', 'Bia Sài Gòn lon', 1, '2026-01-24 12:32:47'),
(58, 2, 'Coca Cola', 20000.00, 'https://th.bing.com/th/id/OIP.8Q9z1ZyM3k2mXcY7zJ0RZwHaHa', 'Nước ngọt Coca Cola', 1, '2026-01-24 12:32:47'),
(59, 2, 'Pepsi', 20000.00, 'https://th.bing.com/th/id/OIP.xY3H8c1mZ9WJk8GQ0B2KqgHaHa', 'Nước ngọt Pepsi', 1, '2026-01-24 12:32:47'),
(60, 2, '7Up', 20000.00, 'https://th.bing.com/th/id/OIP.6N3M9p1Zx2YkWJ0G8Rk0XgHaHa', 'Nước ngọt 7Up', 1, '2026-01-24 12:32:47'),
(61, 2, 'Nước suối Lavie', 15000.00, 'https://th.bing.com/th/id/OIP.4KxY7m8Z0N3H2RkJX1Y2xAHaHa', 'Nước suối Lavie chai', 1, '2026-01-24 12:32:47'),
(62, 2, 'Trà xanh Không Độ', 20000.00, 'https://th.bing.com/th/id/OIP.j0M3ZxY2N8K1H9WJk7X2mAHaHa', 'Trà xanh Không Độ', 1, '2026-01-24 12:32:47'),
(63, 2, 'Trà chanh', 25000.00, 'https://th.bing.com/th/id/OIP.FZ3Yk8Z0N9JH1mX2K7R0cAHaHa', 'Trà chanh mát lạnh', 1, '2026-01-24 12:32:47'),
(64, 2, 'Trà đào cam sả', 35000.00, 'https://th.bing.com/th/id/OIP.HJ8Zx2M3Y0N9K7R1WcXkAHaHa', 'Trà đào cam sả', 1, '2026-01-24 12:32:47');

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
(30, 'Mai Hảo', 2, 1, 'PAID', '', '2026-01-24 12:47:33', '2026-01-24 12:48:00');

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
(54, 30, 60, 3, 20000.00, 'DONE', '2026-01-24 12:47:33'),
(55, 30, 17, 1, 169000.00, 'DONE', '2026-01-24 12:47:33');

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
(2, 'Trần Thị B', 'quanly1', '$2y$10$atQOhiM6eA3OVG87uBb75eZaUAdwE06k5jI1ehXrooolT7r7wy4Pa', 'MANAGE', 1, '2026-01-10 06:47:57', NULL, '0907654321', 'quanly1@vuonque.com', NULL),
(3, 'Mai Hảo', 'admin', '$2y$10$atQOhiM6eA3OVG87uBb75eZaUAdwE06k5jI1ehXrooolT7r7wy4Pa', 'ADMIN', 1, '2026-01-24 13:11:09', '', '0399714932', 'maivanhao2110@gmail.com', 'Số 2, Đường Võ Oanh, P.25, Q. Bình Thạnh, Thành Phố Hồ Chí Minh');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `menu_item`
--
ALTER TABLE `menu_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `order_item`
--
ALTER TABLE `order_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
