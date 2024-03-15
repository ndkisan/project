-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 15, 2024 at 04:38 AM
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
-- Database: `furniture`
--

-- --------------------------------------------------------

--
-- Table structure for table `order_checkout`
--

CREATE TABLE `order_checkout` (
  `id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_price` varchar(60) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `order_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_checkout`
--

INSERT INTO `order_checkout` (`id`, `user_name`, `address`, `phone`, `product_name`, `quantity`, `total_price`, `payment_method`, `order_date`) VALUES
(14, 'Kisan', '11 A Bharathi Nagar Kovai Road', '08778558229', 'Sofa bed', 1, '8', 'cash_delivery', '2024-03-15 03:05:32'),
(15, 'Kisan', '11 A Bharathi Nagar Kovai Road', '08778558229', 'Sofa bed', 1, '8', 'cash_delivery', '2024-03-15 03:14:03'),
(16, 'Kisan', '11 A Bharathi Nagar Kovai Road', '08778558229', 'Sofa bed', 1, '8', 'cash_delivery', '2024-03-15 03:15:04'),
(17, 'Kisan', '11 A Bharathi Nagar Kovai Road', '08778558229', 'Sofa bed', 1, '8', 'cash_delivery', '2024-03-15 03:15:08'),
(18, 'Kisan', '11 A Bharathi Nagar Kovai Road', '08778558229', 'Sofa bed', 1, '8', 'cash_delivery', '2024-03-15 03:15:09'),
(19, 'Kisan', '11 A Bharathi Nagar Kovai Road', '08778558229', 'Rectangular Dining Table', 1, '30', 'cash_delivery', '2024-03-15 03:31:32'),
(20, 'Kisan', '11 A Bharathi Nagar Kovai Road', '08778558229', 'Sofa bed', 1, '8', 'cash_delivery', '2024-03-15 03:55:54'),
(21, 'Kisan', '11 A Bharathi Nagar Kovai Road', '08778558229', 'Sofa bed', 1, '8', 'cash_delivery', '2024-03-15 03:58:02');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `name` varchar(60) NOT NULL,
  `price` varchar(60) NOT NULL,
  `description` varchar(255) NOT NULL,
  `image` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`name`, `price`, `description`, `image`) VALUES
('Sofa bed', '8,000', 'Versatile option, serving as both seating and sleeping space, perfect for guests or small apartments.', 'product_images/product1.jpg'),
('Futon', '11,000', 'Similar to a daybed, a futon can double as a couch and bed. It is a traditional Japanese style of bedding that is perfect for small rooms and apartments. It’s a folding bed so it is easily stored and can be placed pretty much anywhere in your home.', 'product_images/product3.jpg'),
('Daybed', '4,000', 'A daybed is an incredibly versatile bed style because it can be used as a bed, a bench, and a sofa. While it serves the same purpose as a futon, it’s a bit more elegant. It comes in a variety of shapes and sizes, though it’s typically used for twin beds.', 'product_images/product2.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `products1`
--

CREATE TABLE `products1` (
  `name` varchar(60) NOT NULL,
  `price` varchar(60) NOT NULL,
  `description` varchar(255) NOT NULL,
  `image` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products1`
--

INSERT INTO `products1` (`name`, `price`, `description`, `image`) VALUES
('Rectangular Dining Table', '30,000', 'Rectangular dining tables are a popular choice in apartments where they can seat as few as four with room for two extra people at the head of the table when entertaining.', 'product1_images/product.jpg'),
('Square Dining Table', '45,000', 'These are the best space-saving dining tables, because they can be tucked in against a wall and moved out of the way if need be. Use angular, armless dining chairs and they’ll slide under to take up minimal floor space too.', 'product1_images/product1.jpg'),
('Round Dining Table', '38,000', 'This shape of table suits any style of property, from provincial wooden designs with a contoured pedestal leg in a country cottage to those that are sleek with walnut veneers for a penthouse apartment, such as in the Dariel Studio example.', 'product1_images/product2.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_name` varchar(60) NOT NULL,
  `user_username` varchar(60) NOT NULL,
  `user_password` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_name`, `user_username`, `user_password`) VALUES
('admin', 'admin@gmail.com', 'ADMIN123'),
('Kisan', 'kisannallusamy@gmail.com', 'Kisan@121'),
('Sunil', 'aldossinbox@gmail.com', 'Kisan@121');

-- --------------------------------------------------------

--
-- Table structure for table `user_cart`
--

CREATE TABLE `user_cart` (
  `user_name` varchar(60) NOT NULL,
  `product_name` varchar(60) NOT NULL,
  `product_price` varchar(60) NOT NULL,
  `quantity` int(60) NOT NULL,
  `image` varchar(50) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_cart`
--

INSERT INTO `user_cart` (`user_name`, `product_name`, `product_price`, `quantity`, `image`, `description`) VALUES
('Nahul', 'Canopy Bed', '7000', 2, 'product_images/product1.jpg', 'Featuring tall posts with an overhead frame, canopy beds create a cozy and intimate sleeping space, adding drama and luxury to the bedroom décor.');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `order_checkout`
--
ALTER TABLE `order_checkout`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_name`),
  ADD UNIQUE KEY `user_username` (`user_username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `order_checkout`
--
ALTER TABLE `order_checkout`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
