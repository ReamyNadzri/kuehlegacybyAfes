-- MySQL Database Schema for KuehLegacy
-- Converted from Oracle SQL on 2026-01-27
-- Database: kuehlegacy

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kuehlegacy`
--
CREATE DATABASE IF NOT EXISTS `kuehlegacy` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `kuehlegacy`;

-- --------------------------------------------------------
-- Table structure for table `admin`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `USERNAME` varchar(50) NOT NULL,
  `NAME` varchar(100) DEFAULT NULL,
  `PASSWORD` varchar(30) DEFAULT NULL,
  `EMAIL` varchar(100) DEFAULT NULL,
  `IMAGE` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`USERNAME`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `foodtype`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `foodtype`;
CREATE TABLE `foodtype` (
  `FOODTYPECODE` int(11) NOT NULL AUTO_INCREMENT,
  `TYPENAME` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`FOODTYPECODE`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `method`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `method`;
CREATE TABLE `method` (
  `METHODID` int(11) NOT NULL AUTO_INCREMENT,
  `METHODNAME` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`METHODID`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `origin`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `origin`;
CREATE TABLE `origin` (
  `ORIGINCODE` int(11) NOT NULL AUTO_INCREMENT,
  `NAMESTATE` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`ORIGINCODE`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `popularity`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `popularity`;
CREATE TABLE `popularity` (
  `POPULARID` int(11) NOT NULL AUTO_INCREMENT,
  `LEVEL` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`POPULARID`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `shop`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `shop`;
CREATE TABLE `shop` (
  `SHOPID` int(11) NOT NULL AUTO_INCREMENT,
  `SHOPNAME` varchar(100) DEFAULT NULL,
  `LOCATION` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`SHOPID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `USERNAME` varchar(50) NOT NULL,
  `NAME` varchar(100) DEFAULT NULL,
  `PASSWORD` varchar(100) DEFAULT NULL,
  `EMAIL` varchar(100) DEFAULT NULL,
  `IMAGE` varchar(255) DEFAULT NULL,
  `OAUTH_PROVIDER` varchar(50) DEFAULT NULL,
  `OAUTH_UID` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`USERNAME`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `kueh`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `kueh`;
CREATE TABLE `kueh` (
  `KUEHID` int(11) NOT NULL AUTO_INCREMENT,
  `KUEHNAME` varchar(100) DEFAULT NULL,
  `KUEHDESC` text DEFAULT NULL,
  `TAGKUEH` varchar(100) DEFAULT NULL,
  `VIDEO` varchar(255) DEFAULT NULL,
  `FOODTYPECODE` int(11) DEFAULT NULL,
  `METHODID` int(11) DEFAULT NULL,
  `POPULARID` int(11) DEFAULT NULL,
  `ORIGINID` int(11) DEFAULT NULL,
  `IMAGE` varchar(255) DEFAULT NULL,
  `USERNAME` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`KUEHID`),
  KEY `FK_KUEH_FOODTYPE` (`FOODTYPECODE`),
  KEY `FK_KUEH_METHOD` (`METHODID`),
  KEY `FK_KUEH_POPULAR` (`POPULARID`),
  KEY `FK_KUEH_ORIGIN` (`ORIGINID`),
  KEY `FK_KUEH_USERNAME` (`USERNAME`),
  KEY `IDX_KUEHNAME` (`KUEHNAME`),
  CONSTRAINT `FK_KUEH_FOODTYPE` FOREIGN KEY (`FOODTYPECODE`) REFERENCES `foodtype` (`FOODTYPECODE`),
  CONSTRAINT `FK_KUEH_METHOD` FOREIGN KEY (`METHODID`) REFERENCES `method` (`METHODID`),
  CONSTRAINT `FK_KUEH_POPULAR` FOREIGN KEY (`POPULARID`) REFERENCES `popularity` (`POPULARID`),
  CONSTRAINT `FK_KUEH_ORIGIN` FOREIGN KEY (`ORIGINID`) REFERENCES `origin` (`ORIGINCODE`),
  CONSTRAINT `FK_KUEH_USERNAME` FOREIGN KEY (`USERNAME`) REFERENCES `users` (`USERNAME`)
) ENGINE=InnoDB AUTO_INCREMENT=241 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `items`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `items`;
CREATE TABLE `items` (
  `ITEMID` int(11) NOT NULL AUTO_INCREMENT,
  `KUEHID` int(11) DEFAULT NULL,
  `NAMEITEM` varchar(200) DEFAULT NULL,
  `QUANTITY` decimal(10,2) DEFAULT NULL,
  `UNIT` varchar(50) DEFAULT NULL,
  `SHOPID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ITEMID`),
  KEY `FK_ITEMS_KUEH` (`KUEHID`),
  KEY `FK_ITEMS_SHOP` (`SHOPID`),
  KEY `IDX_ITEMS_KUEHID` (`KUEHID`),
  CONSTRAINT `FK_ITEMS_KUEH` FOREIGN KEY (`KUEHID`) REFERENCES `kueh` (`KUEHID`) ON DELETE CASCADE,
  CONSTRAINT `FK_ITEMS_SHOP` FOREIGN KEY (`SHOPID`) REFERENCES `shop` (`SHOPID`)
) ENGINE=InnoDB AUTO_INCREMENT=1121 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `steps`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `steps`;
CREATE TABLE `steps` (
  `STEPID` int(11) NOT NULL AUTO_INCREMENT,
  `KUEHID` int(11) DEFAULT NULL,
  `STEP` text DEFAULT NULL,
  PRIMARY KEY (`STEPID`),
  KEY `FK_STEPS_KUEH` (`KUEHID`),
  CONSTRAINT `FK_STEPS_KUEH` FOREIGN KEY (`KUEHID`) REFERENCES `kueh` (`KUEHID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=464 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `favorite`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `favorite`;
CREATE TABLE `favorite` (
  `FAVID` int(11) NOT NULL AUTO_INCREMENT,
  `USERNAME` varchar(50) DEFAULT NULL,
  `KUEHID` int(11) DEFAULT NULL,
  `DATEFAV` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`FAVID`),
  KEY `FK_FAVORITE_USERNAME` (`USERNAME`),
  KEY `FK_FAVORITE_KUEH` (`KUEHID`),
  KEY `IDX_FAVORITE_USERNAME` (`USERNAME`),
  CONSTRAINT `FK_FAVORITE_USERNAME` FOREIGN KEY (`USERNAME`) REFERENCES `users` (`USERNAME`) ON DELETE CASCADE,
  CONSTRAINT `FK_FAVORITE_KUEH` FOREIGN KEY (`KUEHID`) REFERENCES `kueh` (`KUEHID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=181 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`USERNAME`, `NAME`, `PASSWORD`, `EMAIL`, `IMAGE`) VALUES
('admin', 'admin', 'admin', 'admin@kuehlegacy.com', NULL),
('muzahathir', 'muzahathir', '1234', 'muzahathir@gmail.com', NULL);

--
-- Dumping data for table `foodtype`
--

INSERT INTO `foodtype` (`FOODTYPECODE`, `TYPENAME`) VALUES
(1, 'Tradisional'),
(2, 'Moden'),
(3, 'Luar Negara'),
(4, 'Eksotik'),
(5, 'Perayaan');

--
-- Dumping data for table `method`
--

INSERT INTO `method` (`METHODID`, `METHODNAME`) VALUES
(1, 'Kukus'),
(2, 'Goreng'),
(3, 'Bakar'),
(4, 'Rebus'),
(5, 'Sejuk');

--
-- Dumping data for table `origin`
--

INSERT INTO `origin` (`ORIGINID`, `ORIGINNAME`) VALUES
(1, 'Terengganu'),
(2, 'Johor'),
(3, 'Kelantan'),
(4, 'Pahang'),
(5, 'Perak'),
(6, 'Kedah'),
(7, 'Perlis'),
(8, 'Pulau Pinang'),
(9, 'Selangor'),
(10, 'Negeri Sembilan'),
(11, 'Melaka'),
(12, 'Sabah'),
(13, 'Sarawak'),
(14, 'Kuala Lumpur'),
(15, 'Labuan'),
(16, 'Putrajaya');

--
-- Dumping data for table `popularity`
--

INSERT INTO `popularity` (`POPULARID`, `LEVEL`) VALUES
(1, 'Sangat Popular'),
(2, 'Popular'),
(3, 'Sederhana'),
(4, 'Kurang Popular'),
(5, 'Jarang');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
