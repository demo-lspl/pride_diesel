-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 09, 2020 at 02:07 PM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.2.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pride_diesel`
--

-- --------------------------------------------------------

--
-- Table structure for table `cards`
--

CREATE TABLE `cards` (
  `id` int(11) NOT NULL,
  `card_number` varchar(50) NOT NULL,
  `card_limit` varchar(50) NOT NULL,
  `policy_number` int(3) NOT NULL,
  `card_status` varchar(8) NOT NULL COMMENT '0 = Inactive, 1 = Active, 2 = Hold',
  `company_id` int(11) NOT NULL,
  `driver_id` int(11) DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cards`
--

INSERT INTO `cards` (`id`, `card_number`, `card_limit`, `policy_number`, `card_status`, `company_id`, `driver_id`, `date_created`, `date_modified`) VALUES
(1, '7083052035236900009', '500', 1, '1', 21, NULL, '2020-07-08 04:30:24', '2020-07-08 05:45:41'),
(2, '7083052035236900017', '1000', 1, '1', 16, 1, '2020-07-08 04:30:24', '2020-07-08 04:35:31'),
(3, '7083052035236900025', '', 2, '1', 16, 2, '2020-07-08 04:30:24', '2020-07-08 04:30:24'),
(4, '7083052035236900033', '', 1, '0', 0, NULL, '2020-07-08 04:30:24', '2020-07-08 04:30:24'),
(5, '7083052035236900041', '', 1, '0', 0, NULL, '2020-07-08 04:30:24', '2020-07-08 04:30:24'),
(6, '7083052035236900058', '', 1, '0', 0, NULL, '2020-07-08 04:30:24', '2020-07-08 04:30:24'),
(7, '7083052035236900066', '', 1, '0', 0, NULL, '2020-07-08 04:30:24', '2020-07-08 04:30:24'),
(8, '7083052035236900074', '', 1, '0', 0, NULL, '2020-07-08 04:30:24', '2020-07-08 04:30:24'),
(9, '7083052035236900082', '', 1, '0', 0, NULL, '2020-07-08 04:30:24', '2020-07-08 04:30:24'),
(10, '7083052035236900090', '', 1, '0', 0, NULL, '2020-07-08 04:30:24', '2020-07-08 04:30:24'),
(11, '7083052035236900108', '', 1, '0', 0, NULL, '2020-07-08 04:30:24', '2020-07-08 04:30:24'),
(12, '7083052035236900116', '', 1, '0', 0, NULL, '2020-07-08 04:30:24', '2020-07-08 04:30:24'),
(13, '7083052035236900124', '', 1, '0', 0, NULL, '2020-07-08 04:30:24', '2020-07-08 04:30:24'),
(14, '7083052035236900132', '', 1, '0', 0, NULL, '2020-07-08 04:30:24', '2020-07-08 04:30:24'),
(15, '7083052035236900140', '', 1, '0', 0, NULL, '2020-07-08 04:30:24', '2020-07-08 04:30:24'),
(16, '7083052035236900157', '', 1, '0', 0, NULL, '2020-07-08 04:30:24', '2020-07-08 04:30:24'),
(17, '7083052035236900165', '', 1, '0', 0, NULL, '2020-07-08 04:30:24', '2020-07-08 04:30:24'),
(18, '7083052035236900173', '', 1, '0', 0, NULL, '2020-07-08 04:30:24', '2020-07-08 04:30:24'),
(19, '7083052035236900181', '', 1, '0', 0, NULL, '2020-07-08 04:30:24', '2020-07-08 04:30:24'),
(20, '7083052035236900199', '', 1, '0', 0, NULL, '2020-07-08 04:30:24', '2020-07-08 04:30:24'),
(21, '7083052035236900207', '', 1, '0', 0, NULL, '2020-07-08 04:30:24', '2020-07-08 04:30:24'),
(22, '7083052035236900215', '', 1, '0', 0, NULL, '2020-07-08 04:30:24', '2020-07-08 04:30:24'),
(23, '7083052035236900223', '', 1, '0', 0, NULL, '2020-07-08 04:30:24', '2020-07-08 04:30:24'),
(24, '7083052035236900231', '', 1, '0', 0, NULL, '2020-07-08 04:30:24', '2020-07-08 04:30:24'),
(25, '7083052035236900249', '', 1, '0', 0, NULL, '2020-07-08 04:30:24', '2020-07-08 04:30:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cards`
--
ALTER TABLE `cards`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cards`
--
ALTER TABLE `cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
