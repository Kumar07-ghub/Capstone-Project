-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql104.infinityfree.com
-- Generation Time: Jul 22, 2025 at 11:33 PM
-- Server version: 11.4.7-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_39505851_grocery_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `total` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `stripe_session_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `first_name`, `last_name`, `email`, `phone`, `address`, `city`, `province`, `postal_code`, `country`, `instructions`, `payment_method`, `status`, `total`, `created_at`, `stripe_session_id`) VALUES
(35, 8, 'Tarun kumar', 'Abburi', 'tarunkumarabburi99@gmail.com', '1234567890', '69 Avenue', 'Kitchner', 'ON', 'H9E 7DH', 'Canada', '', 'card', 'Shipped', '5.40', '2025-07-22 23:13:24', NULL),
(36, 8, 'Tarun kumari', 'Abburi', 'tarunkumarabburi99@gmail.com', '1234567890', '89 Avenue', 'Kitchner', 'ON', 'H9E 7DH', 'Canada', '', 'cash', 'Pending', '2.02', '2025-07-23 00:35:20', NULL),
(37, 8, 'Tarun kumari', 'Abburi', 'tarunkumarabburi99@gmail.com', '1234567890', '79 Avenue', 'Kitchner', 'ON', 'H8E 7M0', 'Canada', '', 'card', 'Pending', '1.46', '2025-07-23 00:38:59', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 35, 5, 1, '3.49'),
(2, 35, 2, 1, '1.29'),
(3, 36, 3, 1, '1.79'),
(4, 37, 2, 1, '1.29');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(100) NOT NULL,
  `image_alt` varchar(255) DEFAULT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'General',
  `description` text DEFAULT NULL,
  `average_rating` decimal(3,2) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `image`, `image_alt`, `category`, `description`, `average_rating`, `unit`, `stock_quantity`) VALUES
(1, 'Apples', '2.99', 'apples.jpg', 'Fresh red apples on a wooden table', 'Fruits', 'Fresh red apples sourced locally.', NULL, '1 lb', 200),
(2, 'Bananas', '1.29', 'bananas.jpg', 'Organic bananas in a bunch', 'Fruits', 'Organic bananas rich in potassium.', NULL, '1 bunch', 120),
(3, 'Carrots', '1.79', 'carrots.jpg', 'Crunchy orange carrots', 'Vegetables', 'Crunchy and sweet carrots.', NULL, '1 lb', 90),
(4, 'Broccoli', '2.25', 'broccoli.jpg', 'Green broccoli florets', 'Vegetables', 'Fresh green broccoli florets.', NULL, '1 head', 80),
(5, 'Milk', '3.49', 'milk.jpg', '1-litre milk bottle with blue label', 'Dairy', '2% pasteurized milk, great for daily use.', NULL, '1 litre', 75),
(6, 'Cheese', '5.99', 'cheese.jpg', 'Block of cheddar cheese', 'Dairy', 'Cheddar cheese block.', NULL, '200g', 50),
(7, 'Yogurt', '2.19', 'yogurt.jpg', 'Plain yogurt container', 'Dairy', 'Plain yogurt with probiotics.', NULL, '500 ml', 60),
(8, 'Tomatoes', '2.89', 'tomatoes.jpg', 'Bright red tomatoes in a bowl', 'Vegetables', 'Juicy red tomatoes ideal for salads.', NULL, '1 lb', 95),
(9, 'Oranges', '3.59', 'oranges.jpg', 'Fresh oranges in a basket', 'Fruits', 'Sweet and tangy oranges.', NULL, '1 kg', 85);

-- --------------------------------------------------------

--
-- Table structure for table `product_ratings`
--

CREATE TABLE `product_ratings` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `rating` int(11) NOT NULL,
  `review` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_ratings`
--

INSERT INTO `product_ratings` (`id`, `product_id`, `user_id`, `rating`, `review`, `created_at`) VALUES
(1, 8, NULL, 5, 'Juicy red tomatoes are very good', '2025-07-22 04:26:51'),
(2, 1, 8, 5, '', '2025-07-22 22:10:59'),
(3, 2, 8, 5, 'Organic bananas are good', '2025-07-22 22:12:02'),
(4, 3, 7, 5, 'Crunchy and sweet carrots are perfect', '2025-07-23 00:20:39');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `photo`, `role`, `created_at`) VALUES
(7, 'Admin', 'User', 'admin@example.com', '$2y$10$iC4zZUzAJ1EC53tJSMq8DukeOtson1RminMKyMFd25vXu9L/YdfG6', NULL, 'admin', '2025-07-22 20:38:51'),
(8, 'Tarun kumari', 'Abburi', 'tarunkumarabburi99@gmail.com', '$2y$10$kmEcXOY0E/l6ov5JhHl9geCP2M9/s5zEfUWcXHrjIse3qDtVrpfIy', NULL, 'user', '2025-07-22 22:00:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `product_ratings`
--
ALTER TABLE `product_ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `product_ratings`
--
ALTER TABLE `product_ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `product_ratings`
--
ALTER TABLE `product_ratings`
  ADD CONSTRAINT `product_ratings_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_ratings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
