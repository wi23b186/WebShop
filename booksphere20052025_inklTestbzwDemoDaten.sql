-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 20, 2025 at 05:37 PM
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
-- Database: `booksphere`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `voucher_code` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total`, `order_date`, `voucher_code`) VALUES
(10, 2, 54.98, '2025-05-17 18:35:18', NULL),
(13, 2, 29.99, '2025-05-17 18:41:11', NULL),
(14, 2, 29.99, '2025-05-17 18:42:10', NULL),
(15, 2, 29.99, '2025-05-17 18:42:11', NULL),
(18, 2, 18.98, '2025-05-18 17:43:41', 'TEST1'),
(19, 2, 0.00, '2025-05-18 17:49:01', '7UCMF'),
(20, 2, 0.00, '2025-05-18 17:59:37', '7UCMF'),
(21, 1, 89.98, '2025-05-19 16:44:06', NULL),
(22, 1, 0.00, '2025-05-19 16:44:27', '7UCMF'),
(23, 2, 0.00, '2025-05-19 17:19:30', NULL),
(24, 2, 0.00, '2025-05-19 17:28:05', NULL),
(25, 5, 39.99, '2025-05-20 10:13:44', NULL),
(26, 5, 90.96, '2025-05-20 14:41:24', 'IB4TV'),
(27, 5, 44.99, '2025-05-20 14:44:11', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(13, 10, 3, 1, 29.99),
(14, 10, 2, 1, 24.99),
(17, 13, 3, 1, 29.99),
(18, 14, 3, 1, 29.99),
(19, 15, 3, 1, 29.99),
(22, 18, 1, 1, 18.99),
(23, 18, 2, 1, 24.99),
(24, 19, 1, 1, 18.99),
(25, 19, 2, 1, 24.99),
(28, 21, 1, 2, 15.00),
(29, 21, 3, 2, 29.99),
(30, 22, 2, 1, 24.99),
(33, 25, 1, 1, 15.00),
(34, 25, 2, 1, 24.99),
(35, 26, 4, 1, 34.99),
(36, 26, 6, 2, 22.99),
(37, 26, 2, 1, 24.99),
(38, 27, 1, 1, 15.00),
(39, 27, 3, 1, 29.99);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `rating` float DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `image` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `rating`, `category`, `image`) VALUES
(1, 'The Star Map', 'An epic space adventure.', 15.00, 4, 'Science Fiction', 'book1.jpg'),
(2, 'History of Magic', 'Explore ancient magical traditions.', 24.99, 4.8, 'Fantasy', 'book2.jpg'),
(3, 'The Coding Galaxy', 'A beginner’s guide to web development.', 29.99, 4.7, 'Technology', 'book3.jpg'),
(4, 'World War Chronicles', 'A detailed history of WWII.', 34.99, 4.6, 'History', 'book4.jpg'),
(5, 'Mindset Mastery', 'Unlock your true potential.', 14.99, 4.3, 'Self-Help', 'book5.jpg'),
(6, 'Cooking Around the World', 'Recipes from every continent.', 22.99, 4.4, 'Cooking', 'book6.jpg'),
(13, 'Test', 'Test', 1.00, 1, 'Test', '682b73348a8b6_test.png');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `salutation` varchar(10) DEFAULT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `postalcode` varchar(10) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `payment_info` varchar(255) DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `salutation`, `firstname`, `lastname`, `address`, `postalcode`, `city`, `email`, `username`, `password`, `role`, `payment_info`, `active`) VALUES
(1, 'Mr.', 'Admin', 'User', 'Admin Street 1', '1000', 'Vienna', 'admin@booksphere.com', 'admin', '$2y$10$A7W71u.WocHCebmDcEcLhehth6Rk2Ced0nXUEgw.BZNng303.Ndwa', 'admin', 'Admin Card', 1),
(2, 'Herr', 'Armin', 'Zukorlic', 'Leonardsfsdf-Bernstein Strasse 4-6', '9999', 'Wien', 'armin@armin.com', 'armin', '$2y$10$UDnzNE2uMar5I9NhaN5wy.USmPhvC2GL3H4O84PC.jzMf/TgE99q6', 'customer', 'PayPal', 1),
(3, 'Herr', 'David', 'Nagy', 'testerwerwr', '1020', 'wien', 'david@gmail.com', 'undefined', '$2y$10$TruFok6D7vNr/sPOxgtdCe8R09MD8RfSlpay2YP3BAppoSMXvpzYO', 'customer', 'test', 1),
(4, 'Herr', 'DavidTest', 'DavidTest', 'WERwr', '10202', 'AER', 'test@gmail.com', 'DavidTest', '$2y$10$HEtLGY0iCyg7b3C61V8Xue7VcbSuRkJxHcHqhsFN6LTgRoeeTfy7S', 'customer', 'testinfos', 1),
(5, 'Herr', 'Wili', 'Wili', 'Mustergasse 3/2/1', '1020', 'Wien', 'test@gmkadfmk.com', 'wili', '$2y$10$isPAtchxyVzLrPpwy6.H.eNHVipDu9S7JE6vNQvAu7s5x8E7qt45W', 'customer', '120000000', 1);

-- --------------------------------------------------------

--
-- Table structure for table `vouchers`
--

CREATE TABLE `vouchers` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `value` decimal(10,2) DEFAULT NULL,
  `used_value` decimal(10,2) NOT NULL DEFAULT 0.00,
  `expiry_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vouchers`
--

INSERT INTO `vouchers` (`id`, `code`, `value`, `used_value`, `expiry_date`, `created_at`) VALUES
(1, 'SAVE10', 10.00, 10.00, '2025-12-31', '2025-05-17 17:45:28'),
(2, 'WELCOME20', 20.00, 20.00, '2025-06-30', '2025-05-17 17:45:28'),
(3, 'SUPER50', 50.00, 0.00, '2026-01-01', '2025-05-17 17:45:28'),
(12, 'TEST1', 25.00, 25.00, '2025-05-20', '2025-05-18 16:45:04'),
(13, 'TEST2', 4.00, 0.00, '2025-05-07', '2025-05-18 16:45:18'),
(15, 'IB4TV', 15.00, 15.00, '2025-05-21', '2025-05-18 17:13:31'),
(21, 'JTD3C', 10.00, 0.00, '2025-05-31', '2025-05-20 14:38:23'),
(22, 'EWRPO', 100.00, 0.00, '2025-05-30', '2025-05-20 14:38:35');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `vouchers`
--
ALTER TABLE `vouchers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
