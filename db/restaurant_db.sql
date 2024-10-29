-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 19, 2024 at 01:49 PM
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
-- Database: `restaurant_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin1', 'password123'),
(2, 'admin2', 'securepass456'),
(3, 'admin3', 'mypassword789'),
(4, 'admin1', '482c811da5d5b4bc6d497ffa98491e38'),
(5, 'admin2', '2e248e7a3b4fbaf2081b3dff10ee402b'),
(6, 'admin3', '326c7874dcbd44585bb515c361509f78');

-- --------------------------------------------------------

--
-- Table structure for table `cashiers`
--

CREATE TABLE `cashiers` (
  `cashier_id` int(11) NOT NULL,
  `cashier_name` varchar(100) NOT NULL,
  `age` int(11) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cashiers`
--

INSERT INTO `cashiers` (`cashier_id`, `cashier_name`, `age`, `gender`, `email`, `password`, `image`) VALUES
(1, 'marcjh', 28, 'Male', 'john@example.com', '$2y$10$E0Jt1Z2G4Y2Nlf5q50EkFuT1nG1ZLVPyThJkLZ9be3M5aUNNNtLOK', 'uploads/sample.jpg'),
(2, 'qwert', 32, 'Female', 'jan@example.com', '$2y$10$E0Jt1Z2G4Y2Nlf5q50EkFuT1nG1ZLVPyThJkLZ9be3M5aUNNNtLOK', 'uploads/sample.jpg'),
(3, 'jas', 24, 'Female', 'ali@example.com', '$2y$10$E0Jt1Z2G4Y2Nlf5q50EkFuT1nG1ZLVPyThJkLZ9be3M5aUNNNtLOK', 'uploads/sample.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `c_id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('Available','Not Available') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`c_id`, `category_name`, `description`, `status`) VALUES
(1, 'Appetizers', 'Starters and small dishes to begin a meal.', 'Not Available'),
(2, 'Main Course', 'The primary dish in a meal.', 'Available'),
(3, 'Desserts', 'Sweet dishes served at the end of a meal.', 'Available'),
(4, 'Beverages', 'Drinks of various kinds.', 'Available'),
(6, 'Appetizers', 'Light starters', 'Available'),
(8, 'Desserts', 'Sweet treats', 'Available'),
(9, 'Beverages', 'Drinks and refreshments', 'Available'),
(10, 'Salads', 'Fresh and healthy', 'Available'),
(38, 'dadasd', 'adasd', 'Available'),
(39, 'dsaxcxc', 'dada', 'Not Available'),
(40, 'dada', 'dasdad', 'Available'),
(41, 'Sabaw', 'dasdasd', 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `device_sessions`
--

CREATE TABLE `device_sessions` (
  `id` int(11) NOT NULL,
  `admin_id` int(10) UNSIGNED DEFAULT NULL,
  `device_id` varchar(255) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_active` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `device_sessions`
--

INSERT INTO `device_sessions` (`id`, `admin_id`, `device_id`, `ip_address`, `user_agent`, `login_time`, `last_active`) VALUES
(5, 5, '9851fff6fc09b8c7cbefd7ff87b3e299', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36', '2024-09-16 07:07:31', '2024-09-16 07:07:31'),
(6, 6, '9851fff6fc09b8c7cbefd7ff87b3e299', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36', '2024-09-16 07:08:58', '2024-09-16 07:08:58'),
(7, 4, '9851fff6fc09b8c7cbefd7ff87b3e299', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36', '2024-09-17 08:55:49', '2024-09-17 08:55:49'),
(8, 4, '9851fff6fc09b8c7cbefd7ff87b3e299', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36', '2024-09-17 08:57:17', '2024-09-17 08:57:17');

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `menu_name` varchar(255) NOT NULL,
  `code` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_by` varchar(100) NOT NULL,
  `image_path` varchar(255) DEFAULT 'assets/img/product/noimage.png',
  `description` text DEFAULT NULL,
  `discount_type` varchar(50) DEFAULT NULL,
  `status` enum('Available','Not Available') DEFAULT 'Available',
  `category_name` varchar(255) DEFAULT NULL,
  `subcategory_name` varchar(255) DEFAULT NULL,
  `c_id` int(11) DEFAULT NULL,
  `sub_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `menu_name`, `code`, `price`, `created_by`, `image_path`, `description`, `discount_type`, `status`, `category_name`, `subcategory_name`, `c_id`, `sub_id`) VALUES
(81, 'Spaghetti Bolognese', 'dasda', 342.00, 'Admin', 'img/menu/Bluberry-Custard-Bun-Copy-400x400.jpg', 'Soft bun filled with rich blueberry custard.', '10%', 'Not Available', 'Main Course', 'Grilled', NULL, NULL),
(82, 'Grilled Chicken Sandwich', 'B002', 231.00, 'Admin', 'img/menu/slow-roast-pork-belly-w-crackling-skin-400x400.jpg', 'Grilled chicken breast with lettuce, tomato, and mayo.', '20%', 'Not Available', 'Beverages', 'Grilled', NULL, NULL),
(83, 'Dummy Dish 1', 'D010', 563.00, 'admin', 'img/menu/slow-roast-pork-belly-w-crackling-skin-400x400.jpg', 'Delicious dummy dish with fixed image path.', 'None', 'Available', 'Main Course', 'Grilled', NULL, NULL),
(84, 'Dummy Dish 2', 'D011', 153.00, 'admin', 'img/menu/slow-roast-pork-belly-w-crackling-skin-400x400.jpg', 'Another tasty dummy dish.', '10%', 'Available', 'Appetizer', 'Grilled', NULL, NULL),
(85, 'Dummy Dish 3', 'D012', 623.00, 'admin', 'img/menu/slow-roast-pork-belly-w-crackling-skin-400x400.jpg', 'Savory dummy dish with a fixed image.', 'None', 'Available', 'Dessert', 'Grilled', NULL, NULL),
(86, 'Dummy Dish 4', 'D013', 549.00, 'admin', 'img/menu/slow-roast-pork-belly-w-crackling-skin-400x400.jpg', 'Another great dish for testing.', '5%', 'Available', 'Snack', 'Grilled', NULL, NULL),
(87, 'Dummy Dish 5', 'D014', 139.00, 'admin', 'img/menu/slow-roast-pork-belly-w-crackling-skin-400x400.jpg', 'Delicious and fixed image path dummy dish.', 'None', 'Available', 'Main Course', 'Grilled', NULL, NULL),
(88, 'Dummy Dish 6', 'D015', 149.00, 'admin', 'img/menu/slow-roast-pork-belly-w-crackling-skin-400x400.jpg', 'Another example of dummy dish.', '10%', 'Available', 'Beverage', 'Grilled', NULL, NULL),
(96, 'fdfsdfd', '43243', 43243.00, 'Admin', 'img/menu/Chocolate-Carrot-and-Pecan-Cake-400x400.jpg', 'dsada', '20%', 'Available', 'Desserts', 'Fried', NULL, NULL),
(97, 'sadsadad', '43243', 5433.00, 'Admin', 'img/menu/baked-fish-w-lemon-garlic-ceasar-sauce-400x400.jpg', 'eqweqwe', '10%', 'Available', 'Desserts', 'Fried', NULL, NULL),
(98, 'dasad', '43242', 2342.00, 'Admin', 'img/menu/Salted-Caramel-Tres-Leches-400x400.jpg', 'sdasa', '10%', 'Available', 'Beverages', 'Baked', NULL, NULL),
(99, 'dasdadsa', '4323', 342342.00, 'Admin', 'img/menu/White-Chicken-and-Spinach-Lasagna-400x400.jpg', 'dasada', '20%', 'Available', 'Main Course', 'Grilled', NULL, NULL),
(100, 'asdsadadsada', '43342', 242.00, 'Admin', 'img/menu/Trio-Cheese-Baked-Salmon-400x400.jpg', 'dasada', '20%', 'Not Available', 'Main Course', 'Finger Foods', NULL, NULL),
(101, 'dasda', 'dasda', 4324.00, 'Admin', 'img/menu/Coffee-Ganache-Croissant-400x400.jpg', 'dasda', '10%', 'Not Available', 'Appetizers', 'Dips and Spreads', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `onlinecustomer`
--

CREATE TABLE `onlinecustomer` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `floor` varchar(50) DEFAULT NULL,
  `note_to_rider` text DEFAULT NULL,
  `delivery_method` varchar(50) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `tip_amount` decimal(10,2) DEFAULT 0.00,
  `delivery_fee` decimal(10,2) DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL,
  `grand_total` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `onlinecustomer`
--

INSERT INTO `onlinecustomer` (`id`, `customer_name`, `customer_email`, `customer_phone`, `floor`, `note_to_rider`, `delivery_method`, `payment_method`, `tip_amount`, `delivery_fee`, `subtotal`, `grand_total`, `created_at`) VALUES
(1, 'User Name', '', '', '', '', '', '0', 0.00, 0.00, 156.49, 175.27, '2024-09-19 09:55:53'),
(2, '', '', '', '', '', '', '0', 0.00, 0.00, 0.00, 33.60, '2024-09-19 09:55:53'),
(3, 'User Name', '', '', '', '', '', '0', 0.00, 0.00, 156.49, 175.27, '2024-09-19 09:58:00'),
(4, '', '', '', '', '', '', '0', 0.00, 0.00, 0.00, 33.60, '2024-09-19 09:58:01'),
(5, '', '', '', 'dasdsa', 'dsad', 'priority', '0', 5.00, 0.00, 0.00, 0.00, '2024-09-19 09:58:23');

-- --------------------------------------------------------

--
-- Table structure for table `onlineorderitem`
--

CREATE TABLE `onlineorderitem` (
  `id` int(11) NOT NULL,
  `onr_id` int(11) NOT NULL,
  `itemName` varchar(255) NOT NULL,
  `itemPrice` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `onlineorderitem`
--

INSERT INTO `onlineorderitem` (`id`, `onr_id`, `itemName`, `itemPrice`, `quantity`) VALUES
(1, 1, 'Spaghetti Bolognese', 156.49, 1),
(2, 3, 'Spaghetti Bolognese', 156.49, 1);

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `or_id` int(15) NOT NULL,
  `or_name` varchar(50) NOT NULL,
  `or_address` varchar(100) NOT NULL,
  `or_number` int(15) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_date` datetime DEFAULT current_timestamp(),
  `total_price` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`or_id`, `or_name`, `or_address`, `or_number`, `quantity`, `created_date`, `total_price`) VALUES
(1, 'adad', '13123', 0, 1, '2024-09-04 16:03:23', 0.00),
(2, 'dada', 'gdsfddsf', 659265498, 1, '2024-09-04 16:03:23', 0.00),
(3, 'dada', 'gdsfddsf', 659265498, 1, '2024-09-04 16:03:23', 0.00),
(4, 'March Anthony Dela Peña', 'Minglanilla Cebu', 659265498, 1, '2024-09-04 16:03:23', 0.00),
(5, 'March Anthony Dela Peña', 'Minglanilla Cebu', 659265498, 1, '2024-09-04 16:19:43', 0.00),
(6, 'March Anthony Dela Peña', 'Minglanilla Cebu', 0, 1, '2024-09-04 16:21:48', 0.00),
(7, 'March Anthony Dela Peña', 'Minglanilla Cebu', 0, 1, '2024-09-04 16:30:17', 0.00),
(8, 'da', 'dada', 0, 1, '2024-09-04 16:52:22', 0.00),
(9, 'March Anthony Dela Peña', 'Minglanilla Cebu', 659265498, 1, '2024-09-04 16:57:40', 0.00),
(10, 'March Anthony Dela Peña', 'Minglanilla Cebu', 659265498, 1, '2024-09-04 16:57:41', 0.00),
(11, 'March Anthony Dela Peña', 'Minglanilla Cebu', 659265498, 1, '2024-09-04 16:57:42', 0.00),
(12, 'March Anthony Dela Peña', 'Minglanilla Cebu', 659265498, 1, '2024-09-04 16:57:42', 0.00),
(13, 'March Anthony Dela Peña', 'Minglanilla Cebu', 659265498, 1, '2024-09-04 16:57:42', 0.00),
(14, 'March Anthony Dela Peña', 'Minglanilla Cebu', 659265498, 1, '2024-09-04 16:57:43', 0.00),
(15, 'March Anthony Dela Peña', 'Minglanilla Cebu', 659265498, 1, '2024-09-04 16:57:43', 0.00),
(16, 'March Anthony Dela Peña', 'Minglanilla Cebu', 659265498, 1, '2024-09-04 16:57:43', 0.00),
(17, 'March Anthony Dela Peña', 'Minglanilla Cebu', 659265498, 1, '2024-09-04 16:57:48', 0.00),
(18, 'March Anthony Dela Peña', 'Minglanilla Cebu', 659265498, 1, '2024-09-04 16:57:49', 0.00),
(19, 'March Anthony Dela Peña', 'Minglanilla Cebu', 659265498, 1, '2024-09-04 16:57:49', 0.00),
(20, 'March Anthony Dela Peña', 'Minglanilla Cebu', 659265498, 1, '2024-09-04 16:57:49', 0.00),
(21, 'March Anthony Dela Peña', 'Minglanilla Cebu', 659265498, 1, '2024-09-04 16:57:49', 0.00),
(22, 'March Anthony Dela Peña', 'Minglanilla Cebu', 659265498, 1, '2024-09-04 16:58:10', 0.00),
(23, 'March Anthony Dela Peña', 'Minglanilla Cebu', 659265498, 1, '2024-09-04 17:01:56', 0.00),
(24, 'March Anthony Dela Peña', 'Minglanilla Cebu', 659265498, 1, '2024-09-04 17:01:57', 0.00),
(25, 'March Anthony Dela Peña', 'Minglanilla Cebu', 659265498, 1, '2024-09-04 17:01:57', 0.00),
(26, 'March Anthony Dela Peña', 'Minglanilla Cebu', 659265498, 1, '2024-09-04 17:01:57', 0.00),
(27, 'March Anthony Dela Peña', 'Minglanilla Cebu', 659265498, 1, '2024-09-04 17:01:58', 0.00),
(28, 'March Anthony Dela Peña', 'Minglanilla Cebu', 659265498, 1, '2024-09-04 17:01:58', 0.00),
(29, 'March Anthony Dela Peña', 'Minglanilla Cebu', 659265498, 1, '2024-09-04 17:01:58', 0.00),
(30, 'March Anthony Dela Peña', 'Minglanilla Cebu', 659265498, 1, '2024-09-04 17:01:58', 0.00),
(31, 'March Anthony Dela Peña', 'Minglanilla Cebu', 659265498, 1, '2024-09-04 17:01:58', 0.00),
(32, 'March Anthony Dela Peña', 'Minglanilla Cebu', 659265498, 1, '2024-09-04 17:01:59', 0.00),
(33, 'March Anthony Dela Peña', 'Minglanilla Cebu', 659265498, 1, '2024-09-04 17:01:59', 0.00),
(34, 'March Anthony Dela Peña', 'Minglanilla Cebu', 0, 1, '2024-09-04 17:02:31', 0.00),
(35, 'March Anthony Dela Peña', 'Minglanilla Cebu', 0, 1, '2024-09-04 17:09:10', 0.00),
(36, 'Shenny', 'dad', 0, 1, '2024-09-04 17:53:52', 0.00),
(37, 'Shenny', 'dad', 0, 1, '2024-09-04 17:54:01', 0.00),
(38, 'aa', 'aas', 0, 1, '2024-09-04 18:38:04', 0.00),
(39, 'March Anthony Dela Peña', 'Minglanilla Cebu', 659265498, 1, '2024-09-04 19:39:31', 0.00),
(40, 'March Anthony Dela Peña', 'Minglanilla Cebu', 659265498, 1, '2024-09-04 19:48:52', 0.00),
(41, 'assa', 'aa', 0, 1, '2024-09-04 20:15:11', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `customer` varchar(100) NOT NULL DEFAULT 'Cashier',
  `order_type` enum('DINE','TAKE OUT') NOT NULL,
  `discount` enum('Regular','PWD/Senior Citizen') NOT NULL,
  `payment_method` enum('Cash','Debit','Scan') NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_id`, `customer`, `order_type`, `discount`, `payment_method`, `total_price`, `created_at`, `created_by`, `quantity`) VALUES
(1, 'ORD66d526c330409', 'Cashier', 'DINE', 'Regular', 'Cash', 1200.00, '2024-09-02 02:45:23', 'Cashier', 1),
(2, 'ORD66d52741aae7f', 'Cashier', 'TAKE OUT', 'Regular', 'Cash', 1200.00, '2024-09-02 02:47:29', 'Cashier', 1),
(3, 'ORD66d52d452c399', 'Walk-In', 'TAKE OUT', 'PWD/Senior Citizen', 'Debit', 1280.00, '2024-09-01 21:13:09', 'Cashier', 1),
(4, 'ORD66d52e1a993e9', 'Walk-In', 'TAKE OUT', 'PWD/Senior Citizen', 'Debit', 500.00, '2024-09-01 21:16:42', 'Cashier', 1),
(20, 'ORD66d5cfb372fd5', 'Walk-In', 'DINE', 'PWD/Senior Citizen', 'Cash', 26.00, '2024-09-02 08:46:11', 'Cashier', 1),
(21, 'ORD66e52ce372020', 'Walk-In', 'DINE', 'Regular', '', 652.00, '2024-09-14 00:27:47', 'Cashier', 1),
(22, 'ORD66e5313865c87', 'Walk-In', 'DINE', 'PWD/Senior Citizen', 'Debit', 452.00, '2024-09-14 00:46:16', 'Cashier', 1),
(23, 'ORD66e531866322c', 'Walk-In', 'DINE', 'Regular', 'Debit', 321.00, '2024-09-14 00:47:34', 'Cashier', 1),
(24, 'ORD66e531abda7d0', 'Walk-In', 'DINE', 'Regular', 'Cash', 0.00, '2024-09-14 00:48:11', 'Cashier', 1),
(25, 'ORD66e5326e43257', 'Walk-In', 'DINE', 'Regular', 'Cash', 0.00, '2024-09-14 00:51:26', 'Cashier', 1),
(26, 'ORD66e5329d14dfe', 'Walk-In', 'DINE', 'Regular', 'Cash', 0.00, '2024-09-14 00:52:13', 'Cashier', 1),
(27, 'ORD66e5351fc9cac', 'Walk-In', 'DINE', 'Regular', 'Cash', 0.00, '2024-09-14 01:02:55', 'Cashier', 1),
(28, 'ORD66e536112c981', 'Walk-In', 'DINE', 'Regular', 'Cash', 0.00, '2024-09-14 01:06:57', 'Cashier', 1),
(29, 'ORD66e5366006119', 'Walk-In', 'DINE', 'Regular', 'Cash', 0.00, '2024-09-14 01:08:16', 'Cashier', 1),
(30, 'ORD66e536c314e80', 'Walk-In', 'DINE', 'Regular', 'Cash', 0.00, '2024-09-14 01:09:55', 'Cashier', 1),
(31, 'ORD66e944d10ae0c', 'Walk-In', 'TAKE OUT', 'PWD/Senior Citizen', 'Scan', 0.00, '2024-09-17 02:58:57', 'Cashier', 1),
(32, 'ORD66ec081c34c4f', 'Walk-In', 'DINE', 'Regular', '', 0.00, '2024-09-19 05:16:44', 'Cashier', 1);

-- --------------------------------------------------------

--
-- Table structure for table `order_menu`
--

CREATE TABLE `order_menu` (
  `t_id` int(11) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `menu_name` varchar(255) NOT NULL,
  `menu_image` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `subcategory` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_menu`
--

INSERT INTO `order_menu` (`t_id`, `order_id`, `menu_name`, `menu_image`, `category`, `subcategory`, `quantity`, `price`) VALUES
(28, 'ORD66d526c330409', '', '', 'null', '', 0, 0.00),
(21342, 'ORD66d5cfb372fd5', 'Garlic Bread', 'img/menu/Bluberry-Custard-Bun-Copy-400x400.jpg', 'null', '', 1, 4.99),
(23132, 'ORD66d526c330409', 'Grilled Salmon', 'images/grilled_salmon.jpg', 'Fish/Seafood', 'Grilled', 1, 12.99),
(34243, 'ORD66d52741aae7f', 'Grilled Salmon', 'images/grilled_salmon.jpg', 'Fish/Seafood', 'Grilled', 1, 12.99),
(42346, 'ORD66d5cfb372fd5', '', '', 'null', '', 0, 0.00),
(65565, 'ORD66d526c330409', 'Tacos', 'img/testimage.jpg', 'Main Course', 'Mexican', 2, 3.99),
(65567, 'ORD66e52ce372020', 'Spaghetti Bolognese', 'img/menu/slow-roast-pork-belly-w-crackling-skin-400x400.jpg', 'null', '', 1, 13.49),
(65568, 'ORD66e52ce372020', '', '', 'null', '', 0, 0.00),
(65569, 'ORD66e52ce372020', 'Grilled Chicken Sandwich', 'img/menu/slow-roast-pork-belly-w-crackling-skin-400x400.jpg', 'null', '', 1, 8.99),
(65570, 'ORD66e52ce372020', '', '', 'null', '', 0, 0.00),
(65571, 'ORD66e5313865c87', 'Spaghetti Bolognese', 'img/menu/slow-roast-pork-belly-w-crackling-skin-400x400.jpg', 'null', '', 1, 13.49),
(65572, 'ORD66e5313865c87', '', '', 'null', '', 0, 0.00),
(65573, 'ORD66e5313865c87', 'Chocolate Lava Cake', 'img/menu/Bluberry-Custard-Bun-Copy-400x400.jpg', 'null', '', 1, 6.99),
(65574, 'ORD66e5313865c87', '', '', 'null', '', 0, 0.00),
(65575, 'ORD66e5313865c87', 'Classic Caesar Salad', 'img/menu/Bluberry-Custard-Bun-Copy-400x400.jpg', 'null', '', 1, 7.99),
(65576, 'ORD66e5313865c87', '', '', 'null', '', 0, 0.00),
(65577, 'ORD66e5313865c87', 'Blueberry Custard Bun', 'img/menu/Bluberry-Custard-Bun-Copy-400x400.jpg', 'null', '', 1, 5.49),
(65578, 'ORD66e5313865c87', '', '', 'null', '', 0, 0.00),
(65579, 'ORD66e531866322c', 'Spaghetti Bolognese', 'img/menu/Bluberry-Custard-Bun-Copy-400x400.jpg', 'null', '', 1, 342.00),
(65580, 'ORD66e531866322c', '', '', 'null', '', 0, 0.00),
(65581, 'ORD66e531abda7d0', 'Spaghetti Bolognese', 'img/menu/Bluberry-Custard-Bun-Copy-400x400.jpg', 'null', '', 1, 342.00),
(65582, 'ORD66e531abda7d0', '', '', 'null', '', 0, 0.00),
(65583, 'ORD66e531abda7d0', 'Dummy Dish 1', 'img/menu/slow-roast-pork-belly-w-crackling-skin-400x400.jpg', 'null', '', 1, 9.99),
(65584, 'ORD66e531abda7d0', '', '', 'null', '', 0, 0.00),
(65585, 'ORD66e531abda7d0', 'Dummy Dish 5', 'img/menu/slow-roast-pork-belly-w-crackling-skin-400x400.jpg', 'null', '', 1, 14.99),
(65586, 'ORD66e531abda7d0', '', '', 'null', '', 0, 0.00),
(65587, 'ORD66e531abda7d0', 'Dummy Dish 4', 'img/menu/slow-roast-pork-belly-w-crackling-skin-400x400.jpg', 'null', '', 1, 8.49),
(65588, 'ORD66e531abda7d0', '', '', 'null', '', 0, 0.00),
(65589, 'ORD66e5326e43257', 'Lemon Tart', 'img/menu/Bluberry-Custard-Bun-Copy-400x400.jpg', 'null', '', 1, 5.99),
(65590, 'ORD66e5326e43257', '', '', 'null', '', 0, 0.00),
(65591, 'ORD66e5329d14dfe', 'Blueberry Custard Bun', 'img/menu/Bluberry-Custard-Bun-Copy-400x400.jpg', 'null', '', 1, 5.49),
(65592, 'ORD66e5329d14dfe', '', '', 'null', '', 0, 0.00),
(65593, 'ORD66e5329d14dfe', 'Classic Caesar Salad', 'img/menu/Bluberry-Custard-Bun-Copy-400x400.jpg', 'null', '', 1, 7.99),
(65594, 'ORD66e5329d14dfe', '', '', 'null', '', 0, 0.00),
(65595, 'ORD66e5329d14dfe', 'Dummy Dish 3', 'img/menu/slow-roast-pork-belly-w-crackling-skin-400x400.jpg', 'null', '', 1, 15.99),
(65596, 'ORD66e5329d14dfe', '', '', 'null', '', 0, 0.00),
(65597, 'ORD66e5351fc9cac', 'Margherita Pizza', 'img/menu/slow-roast-pork-belly-w-crackling-skin-400x400.jpg', 'null', '', 1, 10.99),
(65598, 'ORD66e5351fc9cac', '', '', 'null', '', 0, 0.00),
(65599, 'ORD66e5351fc9cac', 'Mango Smoothie', 'img/menu/Bluberry-Custard-Bun-Copy-400x400.jpg', 'null', '', 1, 4.49),
(65600, 'ORD66e5351fc9cac', '', '', 'null', '', 0, 0.00),
(65601, 'ORD66e536112c981', 'Classic Caesar Salad', 'img/menu/Bluberry-Custard-Bun-Copy-400x400.jpg', 'null', '', 1, 7.99),
(65602, 'ORD66e536112c981', '', '', 'null', '', 0, 0.00),
(65603, 'ORD66e536112c981', 'Spaghetti Bolognese', 'img/menu/slow-roast-pork-belly-w-crackling-skin-400x400.jpg', 'null', '', 1, 13.49),
(65604, 'ORD66e536112c981', '', '', 'null', '', 0, 0.00),
(65605, 'ORD66e536112c981', 'Chocolate Lava Cake', 'img/menu/Bluberry-Custard-Bun-Copy-400x400.jpg', 'null', '', 1, 6.99),
(65606, 'ORD66e536112c981', '', '', 'null', '', 0, 0.00),
(65607, 'ORD66e5366006119', 'Lemon Tart', 'img/menu/Bluberry-Custard-Bun-Copy-400x400.jpg', 'null', '', 1, 5.99),
(65608, 'ORD66e5366006119', '', '', 'null', '', 0, 0.00),
(65609, 'ORD66e5366006119', 'Margherita Pizza', 'img/menu/slow-roast-pork-belly-w-crackling-skin-400x400.jpg', 'null', '', 1, 10.99),
(65610, 'ORD66e5366006119', '', '', 'null', '', 0, 0.00),
(65611, 'ORD66e5366006119', 'Dummy Dish 2', 'img/menu/slow-roast-pork-belly-w-crackling-skin-400x400.jpg', 'null', '', 1, 12.49),
(65612, 'ORD66e5366006119', '', '', 'null', '', 0, 0.00),
(65613, 'ORD66e5366006119', 'Dummy Dish 6', 'img/menu/slow-roast-pork-belly-w-crackling-skin-400x400.jpg', 'null', '', 1, 11.49),
(65614, 'ORD66e5366006119', '', '', 'null', '', 0, 0.00),
(65615, 'ORD66e5366006119', 'Grilled Chicken Sandwich', 'img/menu/slow-roast-pork-belly-w-crackling-skin-400x400.jpg', 'null', '', 1, 231.00),
(65616, 'ORD66e5366006119', '', '', 'null', '', 0, 0.00),
(65617, 'ORD66e5366006119', 'Dummy Dish 3', 'img/menu/slow-roast-pork-belly-w-crackling-skin-400x400.jpg', 'null', '', 1, 15.99),
(65618, 'ORD66e5366006119', '', '', 'null', '', 0, 0.00),
(65619, 'ORD66e5366006119', 'Dummy Dish 4', 'img/menu/slow-roast-pork-belly-w-crackling-skin-400x400.jpg', 'null', '', 1, 8.49),
(65620, 'ORD66e5366006119', '', '', 'null', '', 0, 0.00),
(65621, 'ORD66e536c314e80', 'Grilled Chicken Sandwich', 'img/menu/slow-roast-pork-belly-w-crackling-skin-400x400.jpg', 'null', '', 1, 8.99),
(65622, 'ORD66e536c314e80', '', '', 'null', '', 0, 0.00),
(65623, 'ORD66e536c314e80', 'Lemon Tart', 'img/menu/Bluberry-Custard-Bun-Copy-400x400.jpg', 'null', '', 1, 5.99),
(65624, 'ORD66e536c314e80', '', '', 'null', '', 0, 0.00),
(65625, 'ORD66e944d10ae0c', 'Classic Caesar Salad', 'img/menu/Bluberry-Custard-Bun-Copy-400x400.jpg', 'null', '', 2, 200.99),
(65626, 'ORD66e944d10ae0c', '', '', 'null', '', 0, 0.00),
(65627, 'ORD66e944d10ae0c', 'Spaghetti Bolognese', 'img/menu/slow-roast-pork-belly-w-crackling-skin-400x400.jpg', 'null', '', 1, 156.49),
(65628, 'ORD66e944d10ae0c', '', '', 'null', '', 0, 0.00),
(65629, 'ORD66e944d10ae0c', 'Blueberry Custard Bun', 'img/menu/Bluberry-Custard-Bun-Copy-400x400.jpg', 'null', '', 3, 55.49),
(65630, 'ORD66e944d10ae0c', '', '', 'null', '', 0, 0.00),
(65631, 'ORD66ec081c34c4f', 'Dummy Dish 6', 'img/menu/slow-roast-pork-belly-w-crackling-skin-400x400.jpg', 'null', '', 1, 149.00),
(65632, 'ORD66ec081c34c4f', '', '', 'null', '', 0, 0.00),
(65633, 'ORD66ec081c34c4f', 'Grilled Chicken Sandwich', 'img/menu/slow-roast-pork-belly-w-crackling-skin-400x400.jpg', 'null', '', 1, 231.00),
(65634, 'ORD66ec081c34c4f', '', '', 'null', '', 0, 0.00),
(65635, 'ORD66ec081c34c4f', 'dasad', 'img/menu/Salted-Caramel-Tres-Leches-400x400.jpg', 'null', '', 3, 2.00),
(65636, 'ORD66ec081c34c4f', '', '', 'null', '', 0, 0.00),
(65637, 'ORD66ec081c34c4f', 'Dummy Dish 3', 'img/menu/slow-roast-pork-belly-w-crackling-skin-400x400.jpg', 'null', '', 2, 623.00),
(65638, 'ORD66ec081c34c4f', '', '', 'null', '', 0, 0.00),
(65639, 'ORD66ec081c34c4f', 'Dummy Dish 2', 'img/menu/slow-roast-pork-belly-w-crackling-skin-400x400.jpg', 'null', '', 1, 153.00),
(65640, 'ORD66ec081c34c4f', '', '', 'null', '', 0, 0.00),
(65641, 'ORD66ec081c34c4f', 'dasda', 'img/menu/Coffee-Ganache-Croissant-400x400.jpg', 'null', '', 1, 4.00),
(65642, 'ORD66ec081c34c4f', '', '', 'null', '', 0, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `subcategories`
--

CREATE TABLE `subcategories` (
  `sub_id` int(11) NOT NULL,
  `c_id` int(11) DEFAULT NULL,
  `subcategory_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subcategories`
--

INSERT INTO `subcategories` (`sub_id`, `c_id`, `subcategory_name`, `description`, `status`) VALUES
(1, 1, 'Finger Foods', 'Small, easy-to-eat appetizers', 'Active'),
(2, 1, 'Dips and Spreads', 'Various types of dips and spreads', 'Active'),
(3, 2, 'Grilled', 'Dishes that are grilled', 'Active'),
(4, 2, 'Fried', 'Dishes that are fried', 'Active'),
(5, 2, 'Baked', 'Dishes that are baked', 'Active'),
(6, 3, 'Cakes', 'Various types of cakes', 'Active'),
(7, 3, 'Ice Creams', 'Different flavors of ice cream', 'Active'),
(8, 3, 'Pastries', 'Various types of pastries', 'Active'),
(9, 4, 'Soft Drinks', 'Non-alcoholic beverages', 'Active'),
(10, 4, 'Juices', 'Freshly squeezed or bottled juices', 'Active'),
(11, 4, 'Alcoholic Beverages', 'Beer, wine, and spirits', 'Active'),
(24, 1, 'Pork', 'Pork-based main dishes.', 'Active'),
(25, 1, 'Pasta', 'Various types of pasta dishes.', 'Active'),
(26, 2, 'Fruit Desserts', 'Desserts made with fresh fruits.', 'Active'),
(27, 2, 'Chocolate Desserts', 'Desserts featuring chocolate as a key ingredient.', 'Active'),
(28, 3, 'Green Salads', 'Salads with a base of green leafy vegetables.', 'Active'),
(29, 3, 'Fruit Salads', 'Salads made primarily with fruits.', 'Active'),
(30, 4, 'Smoothies', 'Blended drinks, often with fruits.', 'Active'),
(31, 4, 'Soft Drinks', 'Carbonated and non-carbonated soft drinks.', 'Active'),
(32, 4, 'aasa', 'fafasfa', 'Inactive');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(60) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`) VALUES
(1, 'march', 'test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.YevzO2r7l9HqlHibtt2x6B/f6A7/wozGa'),
(2, 'marchs', 'ma@gmail.com', 'e10adc3949ba59abbe56e057f20f883e');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cashiers`
--
ALTER TABLE `cashiers`
  ADD PRIMARY KEY (`cashier_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`c_id`);

--
-- Indexes for table `device_sessions`
--
ALTER TABLE `device_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `onlinecustomer`
--
ALTER TABLE `onlinecustomer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `onlineorderitem`
--
ALTER TABLE `onlineorderitem`
  ADD PRIMARY KEY (`id`),
  ADD KEY `onr_id` (`onr_id`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`or_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`);

--
-- Indexes for table `order_menu`
--
ALTER TABLE `order_menu`
  ADD PRIMARY KEY (`t_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `subcategories`
--
ALTER TABLE `subcategories`
  ADD PRIMARY KEY (`sub_id`),
  ADD KEY `category_id` (`c_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `cashiers`
--
ALTER TABLE `cashiers`
  MODIFY `cashier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `c_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `device_sessions`
--
ALTER TABLE `device_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `onlinecustomer`
--
ALTER TABLE `onlinecustomer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `onlineorderitem`
--
ALTER TABLE `onlineorderitem`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `or_id` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `order_menu`
--
ALTER TABLE `order_menu`
  MODIFY `t_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65643;

--
-- AUTO_INCREMENT for table `subcategories`
--
ALTER TABLE `subcategories`
  MODIFY `sub_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `device_sessions`
--
ALTER TABLE `device_sessions`
  ADD CONSTRAINT `device_sessions_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `onlineorderitem`
--
ALTER TABLE `onlineorderitem`
  ADD CONSTRAINT `onlineorderitem_ibfk_1` FOREIGN KEY (`onr_id`) REFERENCES `onlinecustomer` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_menu`
--
ALTER TABLE `order_menu`
  ADD CONSTRAINT `order_menu_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `subcategories`
--
ALTER TABLE `subcategories`
  ADD CONSTRAINT `subcategories_ibfk_1` FOREIGN KEY (`c_id`) REFERENCES `categories` (`c_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
