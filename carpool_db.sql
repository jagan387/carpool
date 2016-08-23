-- phpMyAdmin SQL Dump
-- version 3.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 16, 2015 at 07:03 AM
-- Server version: 5.5.25a
-- PHP Version: 5.4.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `carpool_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE IF NOT EXISTS `locations` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  PRIMARY KEY (`id`,`name`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `name`) VALUES
(9, 'Bannerghatta'),
(12, 'Bellandur'),
(11, 'Dairy Circle'),
(10, 'Forum Mall'),
(8, 'Koramangala'),
(2, 'Marathalli'),
(1, 'Oracle Lexington'),
(3, 'Silk Board');

-- --------------------------------------------------------

--
-- Table structure for table `pools`
--

CREATE TABLE IF NOT EXISTS `pools` (
  `owner` varchar(30) NOT NULL,
  `poolId` bigint(20) NOT NULL AUTO_INCREMENT,
  `startTime` datetime NOT NULL,
  `startFrom` varchar(30) NOT NULL,
  `upTo` varchar(30) NOT NULL,
  `via` varchar(300) NOT NULL,
  `vehicle` bigint(20) NOT NULL,
  `occupancy` tinyint(4) NOT NULL,
  `availability` tinyint(4) NOT NULL,
  PRIMARY KEY (`poolId`),
  KEY `vehicle` (`vehicle`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `pools`
--

INSERT INTO `pools` (`owner`, `poolId`, `startTime`, `startFrom`, `upTo`, `via`, `vehicle`, `occupancy`, `availability`) VALUES
('jungati', 5, '2015-02-12 17:03:26', 'Marathalli', 'Oracle Lexington', 'Bellandur,Silk Board,Forum Mall', 4, 7, 6),
('jungati', 6, '2015-02-12 17:08:59', 'Koramangala', 'Bellandur', 'Silk Board', 5, 4, 4),
('jungati', 7, '2015-02-12 17:12:46', 'Bannerghatta', 'Oracle Lexington', 'Dairy Circle,Forum Mall', 5, 5, 4),
('jungati', 8, '2015-02-12 19:35:55', 'Dairy Circle', 'Oracle Lexington', 'Forum Mall', 5, 7, 3),
('jungati', 9, '2015-02-12 19:38:25', 'Oracle Lexington', 'Dairy Circle', 'Forum Mall', 6, 4, 3),
('jungati', 10, '2015-02-12 23:14:53', 'Bannerghatta', 'Bellandur', 'Dairy Circle', 6, 3, 3),
('jungati', 11, '2015-02-12 23:18:53', 'Forum Mall', 'Koramangala', 'Marathalli', 7, 4, 4),
('jagmo2', 12, '2015-02-15 15:15:42', 'Bannerghatta', 'Bellandur', 'Forum Mall,Koramangala', 8, 4, 4);

-- --------------------------------------------------------

--
-- Table structure for table `pools_users_membership`
--

CREATE TABLE IF NOT EXISTS `pools_users_membership` (
  `poolId` bigint(20) NOT NULL,
  `username` varchar(30) NOT NULL,
  PRIMARY KEY (`poolId`,`username`),
  KEY `poolId` (`poolId`),
  KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pools_users_membership`
--

INSERT INTO `pools_users_membership` (`poolId`, `username`) VALUES
(5, 'jagmo2'),
(7, 'jagmo2'),
(8, 'jagmo2'),
(9, 'jagmo2');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `username` varchar(30) NOT NULL,
  `password` varchar(30) NOT NULL,
  `name` varchar(30) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `email` varchar(40) NOT NULL,
  `gender` varchar(1) NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`username`),
  KEY `password` (`password`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`username`, `password`, `name`, `phone`, `email`, `gender`, `created_on`, `updated_on`) VALUES
('admin', 'password', 'Administrator', '9880407090', 'admin@carpool.com', 'M', '2014-10-19 14:17:21', '2014-12-14 17:49:52'),
('admin1', 'admin1', 'admin1', '9999999999', 'admin1@carpool.com', 'M', '2015-01-26 09:28:27', '2015-01-26 09:28:27'),
('admin2', 'admin2', 'kjhkjhlkhlkjhlkhlkjhlkjhljkhlk', '9999999999', 'admin2@carpool.com', 'M', '2015-01-26 09:28:27', '2015-02-14 15:50:05'),
('jagmo1', 'jagmo1', 'jagmo1', '9999999999', 'jagmo1@carpool.com', 'M', '2015-02-15 09:29:02', '2015-02-15 09:29:02'),
('jagmo2', 'jagmo2', 'jagmo2', '1234567899', 'jagmo2@carpool.com', 'F', '2015-02-15 09:26:08', '2015-02-15 09:44:53'),
('jungati', 'jungati', 'Jagan Mohan Ungati', '9880407090', 'jagan.ungati@oracle.com', 'M', '2014-10-23 06:12:56', '2015-02-15 07:45:23');

--
-- Triggers `users`
--
DROP TRIGGER IF EXISTS `trigger_users_insert`;
DELIMITER //
CREATE TRIGGER `trigger_users_insert` BEFORE INSERT ON `users`
 FOR EACH ROW BEGIN SET NEW.created_on=IF(ISNULL(NEW.created_on) OR NEW.created_on='0000-00-00 00:00:00', CURRENT_TIMESTAMP, IF(NEW.created_on<CURRENT_TIMESTAMP, NEW.created_on, CURRENT_TIMESTAMP));SET NEW.updated_on=NEW.created_on; END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `trigger_users_update`;
DELIMITER //
CREATE TRIGGER `trigger_users_update` BEFORE UPDATE ON `users`
 FOR EACH ROW SET NEW.updated_on=IF(NEW.updated_on<OLD.updated_on, OLD.updated_on, CURRENT_TIMESTAMP)
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE IF NOT EXISTS `vehicles` (
  `vid` bigint(20) NOT NULL AUTO_INCREMENT,
  `uid` varchar(30) NOT NULL,
  `model` varchar(30) NOT NULL,
  `color` varchar(30) NOT NULL,
  `regNo` varchar(30) NOT NULL,
  `occupancy` tinyint(4) NOT NULL,
  PRIMARY KEY (`vid`),
  UNIQUE KEY `regNo` (`regNo`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`vid`, `uid`, `model`, `color`, `regNo`, `occupancy`) VALUES
(4, 'jungati', 'Duster', 'Black', 'KA 31 Z 1111', 7),
(5, 'jungati', 'Dezire', 'White', 'KA 31 Z 2222', 4),
(6, 'jungati', 'Dezire', 'Green', 'KA 31 Z 3333', 4),
(7, 'jungati', 'Innova', 'White', 'KA 31 Z 4444', 4),
(8, 'jagmo2', 'Innova', 'White', 'KA 31 Z 5555', 4);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pools`
--
ALTER TABLE `pools`
  ADD CONSTRAINT `pools_ibfk_1` FOREIGN KEY (`vehicle`) REFERENCES `vehicles` (`vid`);

--
-- Constraints for table `pools_users_membership`
--
ALTER TABLE `pools_users_membership`
  ADD CONSTRAINT `fk_pool_id` FOREIGN KEY (`poolId`) REFERENCES `pools` (`poolid`),
  ADD CONSTRAINT `fk_user_name` FOREIGN KEY (`username`) REFERENCES `users` (`username`);

--
-- Constraints for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `vehicles_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`username`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
