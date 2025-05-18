-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 18. Mai 2025 um 11:35
-- Server-Version: 10.4.32-MariaDB
-- PHP-Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `booksphere`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `voucher_code` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total`, `order_date`, `voucher_code`) VALUES
(1, 2, 0.00, '2025-05-16 20:57:24', NULL),
(2, 2, 59.97, '2025-05-16 20:59:19', NULL),
(3, 2, 151.92, '2025-05-16 21:39:03', NULL),
(4, 2, 43.98, '2025-05-16 22:02:44', NULL),
(5, 2, 18.99, '2025-05-16 22:03:25', NULL),
(6, 2, 22.99, '2025-05-16 22:05:26', NULL),
(7, 2, 18.99, '2025-05-17 18:24:45', NULL),
(8, 2, 29.99, '2025-05-17 18:28:59', NULL),
(9, 2, 54.98, '2025-05-17 18:34:57', NULL),
(10, 2, 54.98, '2025-05-17 18:35:18', NULL),
(11, 2, 29.99, '2025-05-17 18:38:34', NULL),
(12, 2, 29.99, '2025-05-17 18:40:49', NULL),
(13, 2, 29.99, '2025-05-17 18:41:11', NULL),
(14, 2, 29.99, '2025-05-17 18:42:10', NULL),
(15, 2, 29.99, '2025-05-17 18:42:11', NULL),
(16, 2, 29.99, '2025-05-17 18:42:11', NULL),
(17, 2, 0.00, '2025-05-17 18:43:46', 'SUPER50');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 2, 5, 2, 14.99),
(3, 3, 1, 8, 18.99),
(5, 4, 1, 1, 18.99),
(6, 4, 2, 1, 24.99),
(7, 5, 1, 1, 18.99),
(8, 6, 6, 1, 22.99),
(9, 7, 1, 1, 18.99),
(10, 8, 3, 1, 29.99),
(11, 9, 3, 1, 29.99),
(12, 9, 2, 1, 24.99),
(13, 10, 3, 1, 29.99),
(14, 10, 2, 1, 24.99),
(15, 11, 3, 1, 29.99),
(16, 12, 3, 1, 29.99),
(17, 13, 3, 1, 29.99),
(18, 14, 3, 1, 29.99),
(19, 15, 3, 1, 29.99),
(20, 16, 3, 1, 29.99),
(21, 17, 3, 1, 29.99);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `products`
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
-- Daten für Tabelle `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `rating`, `category`, `image`) VALUES
(1, 'The Star Map', 'An epic space adventure.', 18.99, 4.5, 'Science Fiction', 'book1.jpg'),
(2, 'History of Magic', 'Explore ancient magical traditions.', 24.99, 4.8, 'Fantasy', 'book2.jpg'),
(3, 'The Coding Galaxy', 'A beginner’s guide to web development.', 29.99, 4.7, 'Technology', 'book3.jpg'),
(4, 'World War Chronicles', 'A detailed history of WWII.', 34.99, 4.6, 'History', 'book4.jpg'),
(5, 'Mindset Mastery', 'Unlock your true potential.', 14.99, 4.3, 'Self-Help', 'book5.jpg'),
(6, 'Cooking Around the World', 'Recipes from every continent.', 22.99, 4.4, 'Cooking', 'book6.jpg');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
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
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `salutation`, `firstname`, `lastname`, `address`, `postalcode`, `city`, `email`, `username`, `password`, `role`, `payment_info`, `active`) VALUES
(1, 'Mr.', 'Admin', 'User', 'Admin Street 1', '1000', 'Vienna', 'admin@booksphere.com', 'admin', '$2y$10$A7W71u.WocHCebmDcEcLhehth6Rk2Ced0nXUEgw.BZNng303.Ndwa', 'admin', 'Admin Card', 1),
(2, 'Herr', 'Armin', 'Zukorlic', 'Leonardsfsdf-Bernstein Strasse 4-6', '1220', 'Wien', 'armin@armin.com', 'armin', '$2y$10$ykCbCf7b/JjfmDcengJiNO6ewVkRaHnN2Qx9yDiVj2RFhc0WaT6iq', 'customer', 'PayPal', 1),
(3, 'Herr', 'David', 'Nagy', 'testerwerwr', '1020', 'wien', 'david@gmail.com', 'undefined', '$2y$10$TruFok6D7vNr/sPOxgtdCe8R09MD8RfSlpay2YP3BAppoSMXvpzYO', 'customer', 'test', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `vouchers`
--

CREATE TABLE `vouchers` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `used_value` decimal(10,2) NOT NULL DEFAULT 0.00,
  `expiry_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `vouchers`
--

INSERT INTO `vouchers` (`id`, `code`, `value`, `used_value`, `expiry_date`, `created_at`) VALUES
(1, 'SAVE10', 10.00, 10.00, '2025-12-31', '2025-05-17 17:45:28'),
(2, 'WELCOME20', 20.00, 20.00, '2025-06-30', '2025-05-17 17:45:28'),
(3, 'SUPER50', 50.00, 0.00, '2026-01-01', '2025-05-17 17:45:28');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indizes für die Tabelle `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indizes für die Tabelle `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indizes für die Tabelle `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indizes für die Tabelle `vouchers`
--
ALTER TABLE `vouchers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT für Tabelle `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT für Tabelle `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints der Tabelle `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints der Tabelle `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
