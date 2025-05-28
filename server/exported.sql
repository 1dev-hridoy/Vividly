-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 28, 2025 at 05:24 AM
-- Server version: 10.11.11-MariaDB-cll-lve
-- PHP Version: 8.3.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `address_type` enum('shipping','billing') NOT NULL,
  `street_address` varchar(255) NOT NULL,
  `division_id` int(11) NOT NULL,
  `division_name` varchar(100) NOT NULL,
  `district_id` int(11) NOT NULL,
  `district_name` varchar(100) NOT NULL,
  `upzila_id` int(11) NOT NULL,
  `upzila_name` varchar(100) NOT NULL,
  `union_id` int(11) NOT NULL,
  `union_name` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `save_for_future` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `user_id`, `address_type`, `street_address`, `division_id`, `division_name`, `district_id`, `district_name`, `upzila_id`, `upzila_name`, `union_id`, `union_name`, `city`, `postal_code`, `save_for_future`, `created_at`) VALUES
(1, 1, 'shipping', 'Dhaka khilket', 2, 'রাজশাহী', 15, 'রাজশাহী', 140, 'গোদাগাড়ী', 1259, 'দেওপাড়া', 'Dhaka', '1229', 0, '2025-05-25 17:54:12'),
(3, 3, 'shipping', '123 Main Street', 0, 'Dhaka', 0, 'Dhaka', 0, '', 0, '', '', '1207', 0, '2025-05-27 09:15:40'),
(13, 13, 'shipping', '{\"error\":\"Price must be a positive number\"}', 0, '{\"error\":\"Price must be a positive number\"}', 0, '{\"error\":\"Price must be a positive number\"}', 0, '', 0, '', '{\"error\":\"Price must be a positive number\"}', '23322', 0, '2025-05-27 11:11:22'),
(14, 14, 'shipping', 'Khilket', 0, 'UPo', 0, 'UPo', 0, '', 0, '', 'UPo', '1221', 0, '2025-05-27 13:09:41'),
(15, 15, 'shipping', 'xyyx', 0, 'cyyx', 0, 'cyyx', 0, '', 0, '', 'cyyx', 'hxy', 0, '2025-05-27 16:07:38'),
(16, 16, 'shipping', 'ghx', 0, 'xyyx', 0, 'xyyx', 0, '', 0, '', 'xyyx', '44774', 0, '2025-05-27 16:10:28'),
(17, 1, 'shipping', 'Dhaka Bangladesh', 4, 'বরিশাল', 32, 'পিরোজপুর', 246, 'ভান্ডারিয়া', 2220, 'ভান্ডারিয়া সদর', 'Dhaka', '1229', 0, '2025-05-27 16:26:41'),
(18, 18, 'shipping', 'xgxh', 0, 'xyxg', 0, 'xyxg', 0, '', 0, '', 'xyxg', 'chc', 0, '2025-05-27 16:33:08'),
(19, 16, 'shipping', 'hxfu', 0, 'xuyx', 0, 'xuyx', 0, '', 0, '', 'xuyx', '7558', 0, '2025-05-27 16:36:56');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'hridoy09bg', 'hridoy@gmail.com', '$2y$10$BY6uFabgmHxr9GOqK/ju.OXoWWu3tzxa2c391YikCkX4DPHIpi.0i', '2025-05-23 18:56:18');

-- --------------------------------------------------------

--
-- Table structure for table `carousels`
--

CREATE TABLE `carousels` (
  `id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carousels`
--

INSERT INTO `carousels` (`id`, `image_path`, `created_at`, `updated_at`) VALUES
(3, '1748232915_carousel_dummy_1905x730_ffffff_d8592b_product-image.png', '2025-05-26 04:15:15', '2025-05-26 04:15:15'),
(4, '1748232922_carousel_dummy_1905x730_000000_6cdd9f_product-image.png', '2025-05-26 04:15:22', '2025-05-26 04:15:22'),
(5, '1748232928_carousel_dummy_1905x730_000000_f4a4be_product-image.png', '2025-05-26 04:15:28', '2025-05-26 04:15:28'),
(6, '1748232934_carousel_dummy_1905x730_ffffff_624fd1_product-image.png', '2025-05-26 04:15:34', '2025-05-26 04:15:34'),
(7, '1748232940_carousel_dummy_1905x730_000000_f981b1_product-image.png', '2025-05-26 04:15:40', '2025-05-26 04:15:40');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `name`, `image`, `created_at`) VALUES
(3, 'Fashion', 'woman.png', '2025-05-26 13:06:28'),
(4, 'Kitchen', 'kitchen.png', '2025-05-26 13:06:37'),
(5, 'Health', 'healthcare.png', '2025-05-26 13:06:46'),
(6, 'Sports', 'sports.png', '2025-05-26 13:06:55');

-- --------------------------------------------------------

--
-- Table structure for table `colors`
--

CREATE TABLE `colors` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `colors`
--

INSERT INTO `colors` (`id`, `name`) VALUES
(3, 'Black'),
(2, 'Blue'),
(5, 'Purple'),
(1, 'Red'),
(4, 'White'),
(6, 'Yellow');

-- --------------------------------------------------------

--
-- Table structure for table `contact_info`
--

CREATE TABLE `contact_info` (
  `id` int(11) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_info`
--

INSERT INTO `contact_info` (`id`, `phone_number`, `address`, `updated_at`) VALUES
(1, '+8801234567890', '123 Main St, Dhaka, Bangladesh', '2025-05-25 17:41:53');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `custom_order_id` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `shipping_address_id` int(11) NOT NULL,
  `billing_address_id` int(11) DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `shipping_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `shipping_method` enum('standard','express','overnight') NOT NULL,
  `payment_method` enum('card','paypal','apple_pay','bkash','nagad','rocket') NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `custom_order_id`, `user_id`, `shipping_address_id`, `billing_address_id`, `subtotal`, `shipping_cost`, `tax`, `discount`, `total`, `shipping_method`, `payment_method`, `status`, `created_at`) VALUES
(1, 'ORD-20250525-0753', 1, 1, 1, 44.00, 0.00, 3.52, 0.00, 47.52, 'standard', 'nagad', 'pending', '2025-05-25 17:54:12'),
(3, 'ORD-20250527-6911', 3, 3, 3, 500.00, 0.00, 40.00, 0.00, 540.00, 'standard', 'bkash', 'delivered', '2025-05-27 09:15:40'),
(4, 'ORD-20250527-2836', 13, 13, 13, 44.00, 0.00, 3.52, 0.00, 47.52, 'standard', 'bkash', 'pending', '2025-05-27 11:11:22'),
(5, 'ORD-20250527-3764', 14, 14, 14, 44.00, 0.00, 3.52, 0.00, 47.52, 'standard', 'rocket', 'pending', '2025-05-27 13:09:41'),
(6, 'ORD-20250527-1885', 15, 15, 15, 44.00, 0.00, 3.52, 0.00, 47.52, 'standard', 'bkash', 'pending', '2025-05-27 16:07:38'),
(7, 'ORD-20250527-4989', 16, 16, 16, 44.00, 0.00, 3.52, 0.00, 47.52, 'standard', 'bkash', 'pending', '2025-05-27 16:10:28'),
(8, 'ORD-20250527-3915', 1, 17, 17, 88.00, 0.00, 7.04, 0.00, 95.04, 'standard', 'bkash', 'pending', '2025-05-27 16:26:41'),
(9, 'ORD-20250527-6592', 18, 18, 18, 44.00, 0.00, 3.52, 0.00, 47.52, 'standard', 'bkash', 'pending', '2025-05-27 16:33:08'),
(10, 'ORD-20250527-0372', 16, 19, 19, 44.00, 0.00, 3.52, 0.00, 47.52, 'standard', 'rocket', 'pending', '2025-05-27 16:36:56');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `color_id` int(11) DEFAULT NULL,
  `size_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `color_id`, `size_id`, `quantity`, `unit_price`, `total_price`) VALUES
(1, 1, 1, 1, 8, 1, 44.00, 44.00),
(2, 3, 1, 1, 4, 1, 500.00, 500.00),
(3, 4, 1, 2, 2, 1, 44.00, 44.00),
(4, 5, 1, 2, 1, 1, 44.00, 44.00),
(5, 6, 1, 5, 4, 1, 44.00, 44.00),
(6, 7, 1, 5, 4, 1, 44.00, 44.00),
(7, 8, 1, NULL, NULL, 1, 44.00, 44.00),
(8, 8, 1, 1, 8, 1, 44.00, 44.00),
(9, 9, 1, 5, 4, 1, 44.00, 44.00),
(10, 10, 1, 2, 1, 1, 44.00, 44.00);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_method` enum('card','paypal','apple_pay','bkash','nagad','rocket') NOT NULL,
  `transaction_id` varchar(50) DEFAULT NULL,
  `mobile_number` varchar(20) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `payment_method`, `transaction_id`, `mobile_number`, `amount`, `status`, `created_at`) VALUES
(1, 1, 'rocket', '222', '+8801966647898', 47.52, 'pending', '2025-05-25 17:54:12'),
(2, 3, 'bkash', 'TX12345678', '01712345678', 540.00, 'pending', '2025-05-27 09:15:40'),
(3, 4, 'bkash', '8801234567890', '8801234567890', 47.52, 'pending', '2025-05-27 11:11:22'),
(4, 5, 'rocket', '+8801912345678', '+8801912345678', 47.52, 'pending', '2025-05-27 13:09:41'),
(5, 6, 'bkash', 'ফুকা ', '8801234567890', 47.52, 'pending', '2025-05-27 16:07:38'),
(6, 7, 'bkash', 'hxch', '8801234567890', 47.52, 'pending', '2025-05-27 16:10:28'),
(7, 8, 'bkash', 'Hxhd', '44747', 95.04, 'pending', '2025-05-27 16:26:41'),
(8, 9, 'bkash', 'xhhc', '8801234567890', 47.52, 'pending', '2025-05-27 16:33:08'),
(9, 10, 'rocket', 'xhfj', '+8801812345678', 47.52, 'pending', '2025-05-27 16:36:56');

-- --------------------------------------------------------

--
-- Table structure for table `payment_info`
--

CREATE TABLE `payment_info` (
  `id` int(11) NOT NULL,
  `bkash_number` varchar(20) DEFAULT NULL,
  `bkash_note` varchar(100) DEFAULT NULL,
  `nagad_number` varchar(20) DEFAULT NULL,
  `nagad_note` varchar(100) DEFAULT NULL,
  `rocket_number` varchar(20) DEFAULT NULL,
  `rocket_note` varchar(100) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_info`
--

INSERT INTO `payment_info` (`id`, `bkash_number`, `bkash_note`, `nagad_number`, `nagad_note`, `rocket_number`, `rocket_note`, `updated_at`) VALUES
(1, '8801234567890', 'Send as Personal', '+8801812345678', 'Use for payment only', '+8801912345678', 'Add order ID in reference', '2025-05-27 05:42:47');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `short_description` text DEFAULT NULL,
  `long_description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `price` decimal(10,2) DEFAULT 0.00,
  `stock` int(11) DEFAULT 0,
  `category_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `title`, `short_description`, `long_description`, `created_at`, `price`, `stock`, `category_id`) VALUES
(1, 'tt', 'dsd', 'dddfddfdfdf', '2025-05-25 17:53:30', 44.00, 43, 3);

-- --------------------------------------------------------

--
-- Table structure for table `product_colors`
--

CREATE TABLE `product_colors` (
  `product_id` int(10) UNSIGNED NOT NULL,
  `color_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_colors`
--

INSERT INTO `product_colors` (`product_id`, `color_id`) VALUES
(1, 1),
(1, 2),
(1, 5),
(1, 6);

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(10) UNSIGNED DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `image_type` enum('main','additional') DEFAULT 'additional'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_path`, `image_type`) VALUES
(1, 1, '1748195610_main_Screenshot-2025-05-09-at-5.16.29PM-ezgif.com-webp-to-jpg-converter.jpg', 'main'),
(2, 1, '1748195610_add_0_Screenshot_2025-05-13_152739.png', 'additional'),
(3, 1, '1748195610_add_1_luffy-png-psdss.png', 'additional');

-- --------------------------------------------------------

--
-- Table structure for table `product_sizes`
--

CREATE TABLE `product_sizes` (
  `product_id` int(10) UNSIGNED NOT NULL,
  `size_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_sizes`
--

INSERT INTO `product_sizes` (`product_id`, `size_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13);

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `site_name` varchar(100) NOT NULL,
  `site_description` text DEFAULT NULL,
  `site_logo` varchar(255) DEFAULT NULL,
  `footer_tagline` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `site_name`, `site_description`, `site_logo`, `footer_tagline`, `updated_at`) VALUES
(1, 'My E-Commerce Store', 'Your one-stop shop for everything', '../storage/logo_20250526_041702.png', '© 2025 Hrioy ecom. All rights reserved.', '2025-05-26 04:17:02');

-- --------------------------------------------------------

--
-- Table structure for table `sizes`
--

CREATE TABLE `sizes` (
  `id` int(11) NOT NULL,
  `label` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sizes`
--

INSERT INTO `sizes` (`id`, `label`) VALUES
(8, '3XL'),
(9, '4XL'),
(10, '5XL'),
(11, '6XL'),
(12, '7XL'),
(13, '8XL'),
(5, 'L'),
(4, 'M'),
(3, 'S'),
(6, 'XL'),
(2, 'XS'),
(7, 'XXL'),
(1, 'XXS');

-- --------------------------------------------------------

--
-- Table structure for table `social_links`
--

CREATE TABLE `social_links` (
  `id` int(11) NOT NULL,
  `facebook_url` varchar(255) DEFAULT NULL,
  `twitter_url` varchar(255) DEFAULT NULL,
  `instagram_url` varchar(255) DEFAULT NULL,
  `linkedin_url` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `social_links`
--

INSERT INTO `social_links` (`id`, `facebook_url`, `twitter_url`, `instagram_url`, `linkedin_url`, `updated_at`) VALUES
(1, 'https://facebook.com/hridoy', 'https://twitter.com/hridoy', 'https://instagram.com/hridoy', 'https://linkedin.com/in/hridoy\'hj', '2025-05-26 18:24:17');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `phone`, `created_at`) VALUES
(1, 'hridoy', 'khan', 'ttst@gmail.com', '123', '2025-05-25 17:54:12'),
(3, 'John Doe', '', 'john@example.com', '01712345678', '2025-05-27 09:15:40'),
(13, 'hridoy', '', '{\"error\":\"Price must be a positive number\"}', '34434343', '2025-05-27 11:11:22'),
(14, 'hridoy', '', 'tst@ggd.ee', '01937382238', '2025-05-27 13:09:41'),
(15, 'hridoy', 'khan', 'gmail@gho.vjvj', '380206', '2025-05-27 16:07:38'),
(16, 'হৃদয়', '', 'elianaxqc@gmail.com', '6886', '2025-05-27 16:10:28'),
(18, 'dhhx', '', 'hxdy', '8383', '2025-05-27 16:33:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `carousels`
--
ALTER TABLE `carousels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `colors`
--
ALTER TABLE `colors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `contact_info`
--
ALTER TABLE `contact_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `custom_order_id` (`custom_order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `shipping_address_id` (`shipping_address_id`),
  ADD KEY `billing_address_id` (`billing_address_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `color_id` (`color_id`),
  ADD KEY `size_id` (`size_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `payment_info`
--
ALTER TABLE `payment_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_colors`
--
ALTER TABLE `product_colors`
  ADD PRIMARY KEY (`product_id`,`color_id`),
  ADD KEY `color_id` (`color_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD PRIMARY KEY (`product_id`,`size_id`),
  ADD KEY `size_id` (`size_id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sizes`
--
ALTER TABLE `sizes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `label` (`label`);

--
-- Indexes for table `social_links`
--
ALTER TABLE `social_links`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `carousels`
--
ALTER TABLE `carousels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `colors`
--
ALTER TABLE `colors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `contact_info`
--
ALTER TABLE `contact_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `payment_info`
--
ALTER TABLE `payment_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sizes`
--
ALTER TABLE `sizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `social_links`
--
ALTER TABLE `social_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`shipping_address_id`) REFERENCES `addresses` (`id`),
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`billing_address_id`) REFERENCES `addresses` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `order_items_ibfk_3` FOREIGN KEY (`color_id`) REFERENCES `colors` (`id`),
  ADD CONSTRAINT `order_items_ibfk_4` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `product_colors`
--
ALTER TABLE `product_colors`
  ADD CONSTRAINT `product_colors_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_colors_ibfk_2` FOREIGN KEY (`color_id`) REFERENCES `colors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD CONSTRAINT `product_sizes_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_sizes_ibfk_2` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
