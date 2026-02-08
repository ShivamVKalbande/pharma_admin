-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 09, 2025 at 02:54 AM
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
-- Database: `db_urban_nest`
--

-- --------------------------------------------------------

--
-- Table structure for table `tenant`
--

CREATE TABLE `tenant` (
  `id` int(11) NOT NULL,
  `total_rooms` varchar(110) NOT NULL,
  `cost` int(11) NOT NULL,
  `city` varchar(100) NOT NULL,
  `address` varchar(200) NOT NULL,
  `bedroom` int(11) NOT NULL,
  `kitchen` int(11) NOT NULL,
  `hall` int(11) NOT NULL,
  `tenant_type` varchar(100) NOT NULL,
  `contact_name` varchar(200) NOT NULL,
  `contact_number` int(11) NOT NULL,
  `contact_email` varchar(100) NOT NULL,
  `image_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tenant`
--

INSERT INTO `tenant` (`id`, `total_rooms`, `cost`, `city`, `address`, `bedroom`, `kitchen`, `hall`, `tenant_type`, `contact_name`, `contact_number`, `contact_email`, `image_name`) VALUES
(4, '1 BHK Flat', 11000, 'Nagpur', 'Model Town Yamuna Nagar, Pune, Maharashtra , India', 1, 1, 1, 'girls', 'Vrushali falke', 2147483647, 'vf@g.c', '5-room7.png'),
(5, '1 BHK Flat', 2050, 'mumbai', 'xyz nagar, nagpur', 1, 1, 1, 'boys', 'shivam', 122345, 'a@g.c', '7-bedroom1.png'),
(6, '1 room', 1100, 'pune', 'Model Town Yamuna Nagar, Pune, Maharashtra , India', 1, 0, 0, 'boys', 'shivam', 2147483647, 'a@g.c', '6-bedroom3.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tenant`
--
ALTER TABLE `tenant`
  ADD PRIMARY KEY (`id`);
ALTER TABLE  
ADD PRIMARY   ()`cost`, `city`, `address`, `bedroom`, `kitchen`, `hall`, `tenant_type`, `contact_name`, `contact_number`, `contact_email`, `image_name`) VALUES
(4, '1 BHK Flat', 11000, 'Nagpur', 'Model Town Yamuna Nagar)
--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tenant`
--
ALTER TABLE `tenant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
