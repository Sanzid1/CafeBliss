-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 26, 2024 at 03:49 PM
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
-- Database: `cafebliss`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(1, 'Breakfast'),
(2, 'Lunch'),
(3, 'Beverages'),
(4, 'Desserts');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `item_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `availability_status` tinyint(1) NOT NULL DEFAULT 1,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`item_id`, `category_id`, `item_name`, `description`, `price`, `availability_status`, `stock_quantity`, `created_at`, `image_path`) VALUES
(1, 1, 'Pancakes', 'Fluffy pancakes with syrup', 599.00, 1, 50, '2024-11-15 16:35:27', '1.PNG'),
(2, 1, 'Avocado Toast', 'Toasted sourdough topped with fresh avocado and spices', 649.00, 1, 50, '2024-12-12 11:10:49', '2.PNG'),
(3, 2, 'Grilled Chicken Sandwich', 'Served with lettuce, tomato, and our special sauce', 899.00, 1, 50, '2024-12-12 11:10:49', '3.PNG'),
(4, 2, 'Veggie Wrap', 'A mix of fresh vegetables wrapped in a whole wheat tortilla', 799.00, 1, 50, '2024-12-12 11:55:12', '4.PNG'),
(5, 3, 'Espresso', 'Rich and aromatic shot of espresso.', 249.00, 1, 50, '2024-12-12 11:55:12', '5.PNG'),
(6, 3, 'Fruit Smoothie', 'Blend of seasonal fruits for a refreshing taste.', 499.00, 1, 50, '2024-12-12 11:59:19', '6.PNG'),
(7, 4, 'Cheesecake', 'Creamy cheesecake with a graham cracker crust.', 549.00, 1, 50, '2024-12-12 11:59:19', '7.PNG'),
(8, 4, 'Chocolate Brownie', 'Rich chocolate brownie served warm.', 399.00, 1, 50, '2024-12-12 12:02:32', '8.PNG'),
(9, 2, 'Pizza', 'Delicious Italian Pizza. Freshly baked !', 999.00, 1, 50, '2024-12-12 16:56:37', '675b15c5d303d.jpg'),
(10, 2, 'Burger', '', 350.00, 1, 50, '2024-12-12 17:19:52', '675b1b3883171.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `delivery_type` enum('Dine-in','Pickup','Delivery') NOT NULL,
  `order_status` varchar(50) NOT NULL DEFAULT '''Pending''',
  `payment_method` enum('Cash on Delivery','Bank Transfer','Bkash') NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `delivery_address` varchar(255) DEFAULT NULL,
  `delivery_person` varchar(100) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `order_date`, `delivery_type`, `order_status`, `payment_method`, `total_amount`, `delivery_address`, `delivery_person`, `modified_by`) VALUES
(1, 11, '2024-12-12 12:10:00', 'Dine-in', 'Ready for Pickup', 'Bkash', 4642.00, '', NULL, NULL),
(2, 11, '2024-12-12 12:10:21', 'Pickup', 'Preparing', 'Cash on Delivery', 2297.00, '', NULL, NULL),
(3, 11, '2024-12-12 12:10:45', 'Delivery', 'Pending', 'Bank Transfer', 1498.00, 'sgwsdgdsg', NULL, NULL),
(4, 12, '2024-12-12 12:28:58', 'Dine-in', 'Delivered', 'Cash on Delivery', 3394.00, '', NULL, NULL),
(5, 12, '2024-12-12 12:29:27', 'Delivery', 'Out for Delivery', 'Bank Transfer', 3394.00, 'sgwsdgdsg', 'asda', NULL),
(6, 12, '2024-12-12 14:30:30', 'Pickup', 'Pending', 'Cash on Delivery', 1298.00, '', NULL, NULL),
(7, 11, '2024-12-12 16:33:14', 'Dine-in', 'Ready for Pickup', 'Cash on Delivery', 3095.00, '', NULL, NULL),
(8, 12, '2024-12-12 16:35:16', 'Pickup', 'Delivered', 'Cash on Delivery', 1248.00, '', NULL, NULL),
(9, 15, '2024-12-18 09:13:32', 'Pickup', 'Preparing', 'Bkash', 11237.00, '', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `item_id`, `quantity`, `price`) VALUES
(1, 1, 1, 1, 599.00),
(2, 1, 2, 1, 649.00),
(3, 1, 3, 1, 899.00),
(4, 1, 4, 1, 799.00),
(5, 1, 5, 1, 249.00),
(6, 1, 6, 1, 499.00),
(7, 1, 7, 1, 549.00),
(8, 1, 8, 1, 399.00),
(9, 2, 1, 1, 599.00),
(10, 2, 3, 1, 899.00),
(11, 2, 4, 1, 799.00),
(12, 3, 1, 1, 599.00),
(13, 3, 3, 1, 899.00),
(14, 4, 1, 2, 599.00),
(15, 4, 2, 2, 649.00),
(16, 4, 8, 1, 399.00),
(17, 4, 6, 1, 499.00),
(18, 5, 1, 2, 599.00),
(19, 5, 2, 2, 649.00),
(20, 5, 8, 1, 399.00),
(21, 5, 6, 1, 499.00),
(22, 6, 2, 2, 649.00),
(23, 7, 1, 1, 599.00),
(24, 7, 2, 3, 649.00),
(25, 7, 7, 1, 549.00),
(26, 8, 2, 1, 649.00),
(27, 8, 1, 1, 599.00),
(28, 9, 9, 1, 999.00),
(29, 9, 1, 1, 599.00),
(30, 9, 2, 1, 649.00),
(31, 9, 3, 10, 899.00);

-- --------------------------------------------------------

--
-- Table structure for table `saved_items`
--

CREATE TABLE `saved_items` (
  `save_id` int(11) NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `address` varchar(255) NOT NULL,
  `user_type` enum('Customer','Employee','Admin') NOT NULL,
  `employee_id` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `password`, `phone_number`, `address`, `user_type`, `employee_id`, `created_at`) VALUES
(1, 'abcd asd', 'abcd@gmail.com', '$2y$10$Otb51byO3atAz6iYLvQYTOBbRXN1U3.cMzB.qb8bEV6BVGoHLd60O', '0172494958', '169/1, Consord , Dhaka 1217', 'Employee', NULL, '2024-11-15 17:31:53'),
(2, 'moses', 'moses@gmail.com', '$2y$10$Tb39PgUflEXyxOwfqHlSNe.89.17t1QtGE45DPjfQic.rPVmcluSe', '0179354465', '152/1, Cencored , Khulna1215', 'Customer', NULL, '2024-11-15 17:33:53'),
(3, 'Amal Amalai', 'Amali@gmail.com', '$2y$10$rIukAgxs5xfx2hTe0/BEPuT9holwkbpPr5EigIM4lf.54Bo2uK3ES', '0172494959', 'Ontheway /1, Amal , Dhaka 1516', 'Customer', NULL, '2024-11-16 08:05:31'),
(4, 'cafe1', 'cafe1@gmail.com', '$2y$10$3LQpfzCtWrV5pTCFM/sP/.K38vF9j1wRar9XLVY.nAkKfTb5NOrtS', '0172494957', 'cafe banani rampura dhaka1214', 'Employee', NULL, '2024-11-16 09:15:06'),
(6, 'Musa', 'Musa@asiya.com', '$2y$10$bqA3WpvnvmGCQFMtM8sQCOhgMUU9xO1NIjR8gKUbfCzlUNFPwmxvS', '0189596436', '158/420 , musanagar , dhaka', 'Customer', NULL, '2024-11-16 09:42:38'),
(7, 'Musa', 'Musa+asiya@gmail.com', '$2y$10$v2j/2Qu1Nnj13oHwFnqlBuhvXZsHJezYzBm3w.whV5STuBRiBtaAK', '0172494958', '169/g', 'Customer', NULL, '2024-12-10 00:50:48'),
(8, 'test1', 'test1@gmail.com', '$2y$10$c1diok03t0tZxPbSEBD49.SFv3iwshggqbk1mb8kzTVQ0jm45/Z5W', '0189596437', '1/1 at moni', 'Customer', NULL, '2024-12-10 01:05:18'),
(10, 'cafe2', 'cafe2@gmail.com', '$2y$10$9RXbw4opRqQNTUnUlos7VOmpiaOV2o.EcRe1Evf/qnAQClPYOLkqe', '0189596435', 'dasdasdfafas', 'Customer', NULL, '2024-12-10 01:12:54'),
(11, 'test3', 'test3@gmail.com', '$2y$10$oLfGpK6lDw//LNH4t62Ai.cLVLHGMRFgrNvwsGQJhLNNWTTLz5HgK', '0172494959', 'asdasddwdwdads', 'Customer', NULL, '2024-12-12 10:20:54'),
(12, 'test4', 'test4@gmail.com', '$2y$10$0Va6AoJSMEkvlwknsknXAu.F9Eaf7wgnqeB3xZ2GoPyHFfZdVWsTy', '0172494958', 'rewtwrhh', 'Employee', NULL, '2024-12-12 12:27:06'),
(13, 'ibra', 'ibra@gmail.com', '$2y$10$br9pwChHjL1Fj2uUPJ8NWeVkrv4rKab3PqIt15AyFVsY7FcI.EYOu', '0189596434', 'asdasfsafsasfa', 'Admin', NULL, '2024-12-12 14:29:16'),
(14, 'test5', 'test5@gmail.com', '$2y$10$qtCVMNFC.3mo9TC9A8ZTMuZc2ARzkCLneV6Qne59KXCAvQzUzzyYC', '018959643645', 'asdadafas', 'Admin', NULL, '2024-12-12 17:24:06'),
(15, 'Test6@gmail.com', 'test6@gmail.com', '$2y$10$fcv.UX/ohB2s72AEfvwu..jJtA7AC.JZKBzBiln6E.HAbV1SqsXly', '018959643678', 'sadasasfasf', 'Customer', NULL, '2024-12-12 17:32:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `saved_items`
--
ALTER TABLE `saved_items`
  ADD PRIMARY KEY (`save_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `saved_items`
--
ALTER TABLE `saved_items`
  MODIFY `save_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `menu_items` (`item_id`);

--
-- Constraints for table `saved_items`
--
ALTER TABLE `saved_items`
  ADD CONSTRAINT `saved_items_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `menu_items` (`item_id`),
  ADD CONSTRAINT `saved_items_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
