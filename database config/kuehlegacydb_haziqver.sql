-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.31 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for kuehlegacy
CREATE DATABASE IF NOT EXISTS `kuehlegacy` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `kuehlegacy`;

-- Dumping structure for table kuehlegacy.admin
CREATE TABLE IF NOT EXISTS `admin` (
  `USERNAME` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `NAME` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `PASSWORD` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `EMAIL` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `IMAGE` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`USERNAME`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table kuehlegacy.admin: ~2 rows (approximately)
DELETE FROM `admin`;
INSERT INTO `admin` (`USERNAME`, `NAME`, `PASSWORD`, `EMAIL`, `IMAGE`) VALUES
	('admin', 'admin', 'admin', 'admin@kuehlegacy.com', NULL),
	('muzahathir', 'muzahathir', '1234', 'muzahathir@gmail.com', NULL);

-- Dumping structure for table kuehlegacy.favorite
CREATE TABLE IF NOT EXISTS `favorite` (
  `FAVID` int NOT NULL AUTO_INCREMENT,
  `USERNAME` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `KUEHID` int DEFAULT NULL,
  `DATEFAV` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`FAVID`),
  KEY `FK_FAVORITE_USERNAME` (`USERNAME`),
  KEY `FK_FAVORITE_KUEH` (`KUEHID`),
  KEY `IDX_FAVORITE_USERNAME` (`USERNAME`),
  CONSTRAINT `FK_FAVORITE_KUEH` FOREIGN KEY (`KUEHID`) REFERENCES `kueh` (`KUEHID`) ON DELETE CASCADE,
  CONSTRAINT `FK_FAVORITE_USERNAME` FOREIGN KEY (`USERNAME`) REFERENCES `users` (`USERNAME`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=184 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table kuehlegacy.favorite: ~2 rows (approximately)
DELETE FROM `favorite`;
INSERT INTO `favorite` (`FAVID`, `USERNAME`, `KUEHID`, `DATEFAV`) VALUES
	(182, 'MUHAMMAD HAZIQ AKRAM MOHD ASHRI', 10, '2026-01-27 23:31:04'),
	(183, 'MUHAMMAD HAZIQ AKRAM MOHD ASHRI', 4, '2026-01-27 23:53:20');

-- Dumping structure for table kuehlegacy.foodtype
CREATE TABLE IF NOT EXISTS `foodtype` (
  `FOODTYPECODE` int NOT NULL AUTO_INCREMENT,
  `TYPENAME` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`FOODTYPECODE`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table kuehlegacy.foodtype: ~5 rows (approximately)
DELETE FROM `foodtype`;
INSERT INTO `foodtype` (`FOODTYPECODE`, `TYPENAME`) VALUES
	(1, 'Tradisional'),
	(2, 'Moden'),
	(3, 'Luar Negara'),
	(4, 'Eksotik'),
	(5, 'Perayaan');

-- Dumping structure for table kuehlegacy.items
CREATE TABLE IF NOT EXISTS `items` (
  `ITEMID` int NOT NULL AUTO_INCREMENT,
  `KUEHID` int DEFAULT NULL,
  `NAMEITEM` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `QUANTITY` decimal(10,2) DEFAULT NULL,
  `UNIT` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `SHOPID` int DEFAULT NULL,
  PRIMARY KEY (`ITEMID`),
  KEY `FK_ITEMS_KUEH` (`KUEHID`),
  KEY `FK_ITEMS_SHOP` (`SHOPID`),
  KEY `IDX_ITEMS_KUEHID` (`KUEHID`),
  CONSTRAINT `FK_ITEMS_KUEH` FOREIGN KEY (`KUEHID`) REFERENCES `kueh` (`KUEHID`) ON DELETE CASCADE,
  CONSTRAINT `FK_ITEMS_SHOP` FOREIGN KEY (`SHOPID`) REFERENCES `shop` (`SHOPID`)
) ENGINE=InnoDB AUTO_INCREMENT=1200 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table kuehlegacy.items: ~79 rows (approximately)
DELETE FROM `items`;
INSERT INTO `items` (`ITEMID`, `KUEHID`, `NAMEITEM`, `QUANTITY`, `UNIT`, `SHOPID`) VALUES
	(1121, 1, 'Tepung Beras - 500g', NULL, NULL, NULL),
	(1122, 1, 'Tepung Ubi Kayu - 100g', NULL, NULL, NULL),
	(1123, 1, 'Santan Pekat - 400ml', NULL, NULL, NULL),
	(1124, 1, 'Air Pandan - 200ml', NULL, NULL, NULL),
	(1125, 1, 'Gula Pasir - 300g', NULL, NULL, NULL),
	(1126, 1, 'Garam - 1/4 sudu teh', NULL, NULL, NULL),
	(1127, 1, 'Pewarna Makanan (merah, hijau, kuning)', NULL, NULL, NULL),
	(1128, 2, 'Pulut Beras - 400g', NULL, NULL, NULL),
	(1129, 2, 'Santan - 500ml', NULL, NULL, NULL),
	(1130, 2, 'Garam - 1 sudu teh', NULL, NULL, NULL),
	(1131, 2, 'Telur - 3 biji', NULL, NULL, NULL),
	(1132, 2, 'Gula Pasir - 200g', NULL, NULL, NULL),
	(1133, 2, 'Tepung Jagung - 3 sudu besar', NULL, NULL, NULL),
	(1134, 2, 'Air Pandan - 150ml', NULL, NULL, NULL),
	(1135, 2, 'Daun Pandan - 5 helai', NULL, NULL, NULL),
	(1136, 3, 'Tepung Pulut - 250g', NULL, NULL, NULL),
	(1137, 3, 'Ubi Kentang - 100g (direbus dan dilenyek)', NULL, NULL, NULL),
	(1138, 3, 'Air Pandan - 150ml', NULL, NULL, NULL),
	(1139, 3, 'Garam - 1/4 sudu teh', NULL, NULL, NULL),
	(1140, 3, 'Gula Melaka - 150g (dipotong dadu kecil)', NULL, NULL, NULL),
	(1141, 3, 'Kelapa Parut Putih - 200g', NULL, NULL, NULL),
	(1142, 3, 'Garam untuk kelapa - sedikit', NULL, NULL, NULL),
	(1143, 4, 'Telur - 5 biji', NULL, NULL, NULL),
	(1144, 4, 'Gula Kastor - 150g', NULL, NULL, NULL),
	(1145, 4, 'Tepung Superfine - 150g', NULL, NULL, NULL),
	(1146, 4, 'Esen Vanila - 1 sudu teh', NULL, NULL, NULL),
	(1147, 4, 'Serbuk Penaik - 1/2 sudu teh', NULL, NULL, NULL),
	(1148, 4, 'Mentega Cair - 2 sudu besar', NULL, NULL, NULL),
	(1149, 5, 'Tepung Gandum - 200g', NULL, NULL, NULL),
	(1150, 5, 'Telur - 2 biji', NULL, NULL, NULL),
	(1151, 5, 'Santan - 300ml', NULL, NULL, NULL),
	(1152, 5, 'Air Pandan - 100ml', NULL, NULL, NULL),
	(1153, 5, 'Garam - sedikit', NULL, NULL, NULL),
	(1154, 5, 'Kelapa Parut - 300g', NULL, NULL, NULL),
	(1155, 5, 'Gula Melaka - 200g', NULL, NULL, NULL),
	(1156, 5, 'Daun Pandan - 3 helai', NULL, NULL, NULL),
	(1157, 6, 'Santan Pekat - 500ml', NULL, NULL, NULL),
	(1158, 6, 'Gula Pasir - 400g', NULL, NULL, NULL),
	(1159, 6, 'Tepung Gandum - 250g', NULL, NULL, NULL),
	(1160, 6, 'Tepung Beras - 50g', NULL, NULL, NULL),
	(1161, 6, 'Telur - 4 biji', NULL, NULL, NULL),
	(1162, 6, 'Garam - 1/4 sudu teh', NULL, NULL, NULL),
	(1163, 7, 'Beras Pulut - 400g', NULL, NULL, NULL),
	(1164, 7, 'Santan - 400ml', NULL, NULL, NULL),
	(1165, 7, 'Air Pandan - 100ml', NULL, NULL, NULL),
	(1166, 7, 'Kunyit Hidup - 2cm', NULL, NULL, NULL),
	(1167, 7, 'Garam - 1 sudu teh', NULL, NULL, NULL),
	(1168, 7, 'Kelapa Parut - 300g', NULL, NULL, NULL),
	(1169, 7, 'Gula Melaka - 250g', NULL, NULL, NULL),
	(1170, 7, 'Daun Pandan - 4 helai', NULL, NULL, NULL),
	(1171, 8, 'Tepung Beras - 200g', NULL, NULL, NULL),
	(1172, 8, 'Tepung Ubi Kayu - 100g', NULL, NULL, NULL),
	(1173, 8, 'Santan - 600ml', NULL, NULL, NULL),
	(1174, 8, 'Air Pandan - 250ml', NULL, NULL, NULL),
	(1175, 8, 'Gula Pasir - 200g', NULL, NULL, NULL),
	(1176, 8, 'Garam - 1 sudu teh', NULL, NULL, NULL),
	(1177, 8, 'Tepung Gandum - 2 sudu besar', NULL, NULL, NULL),
	(1178, 9, 'Tepung Beras - 250g', NULL, NULL, NULL),
	(1179, 9, 'Santan - 300ml', NULL, NULL, NULL),
	(1180, 9, 'Air - 100ml', NULL, NULL, NULL),
	(1181, 9, 'Gula Pasir - 200g', NULL, NULL, NULL),
	(1182, 9, 'Telur - 2 biji', NULL, NULL, NULL),
	(1183, 9, 'Serbuk Penaik - 1/2 sudu teh', NULL, NULL, NULL),
	(1184, 9, 'Bijan Putih - untuk hiasan', NULL, NULL, NULL),
	(1185, 10, 'Ubi Kayu Parut - 600g', NULL, NULL, NULL),
	(1186, 10, 'Santan Pekat - 400ml', NULL, NULL, NULL),
	(1187, 10, 'Gula Pasir - 300g', NULL, NULL, NULL),
	(1188, 10, 'Telur - 4 biji', NULL, NULL, NULL),
	(1189, 10, 'Mentega Cair - 100g', NULL, NULL, NULL),
	(1190, 10, 'Esen Vanila - 1 sudu teh', NULL, NULL, NULL),
	(1191, 10, 'Daun Pandan - 3 helai', NULL, NULL, NULL),
	(1192, 10, 'Garam - 1/2 sudu teh', NULL, NULL, NULL),
	(1193, 241, '500g ubi keledek (dikupas dan dipotong)', NULL, NULL, NULL),
	(1194, 241, '150g tepung gandum', NULL, NULL, NULL),
	(1195, 241, '2 sudu besar gula', NULL, NULL, NULL),
	(1196, 241, '1/2 sudu teh garam', NULL, NULL, NULL),
	(1197, 241, '300g gula untuk salutan', NULL, NULL, NULL),
	(1198, 241, '150ml air untuk gula', NULL, NULL, NULL),
	(1199, 241, 'Minyak untuk menggoreng', NULL, NULL, NULL);

-- Dumping structure for table kuehlegacy.kueh
CREATE TABLE IF NOT EXISTS `kueh` (
  `KUEHID` int NOT NULL AUTO_INCREMENT,
  `KUEHNAME` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `KUEHDESC` text COLLATE utf8mb4_unicode_ci,
  `TAGKUEH` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `VIDEO` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `FOODTYPECODE` int DEFAULT NULL,
  `METHODID` int DEFAULT NULL,
  `POPULARID` int DEFAULT NULL,
  `ORIGINID` int DEFAULT NULL,
  `IMAGE` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `USERNAME` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`KUEHID`),
  KEY `FK_KUEH_FOODTYPE` (`FOODTYPECODE`),
  KEY `FK_KUEH_METHOD` (`METHODID`),
  KEY `FK_KUEH_POPULAR` (`POPULARID`),
  KEY `FK_KUEH_ORIGIN` (`ORIGINID`),
  KEY `FK_KUEH_USERNAME` (`USERNAME`),
  KEY `IDX_KUEHNAME` (`KUEHNAME`),
  CONSTRAINT `FK_KUEH_FOODTYPE` FOREIGN KEY (`FOODTYPECODE`) REFERENCES `foodtype` (`FOODTYPECODE`),
  CONSTRAINT `FK_KUEH_METHOD` FOREIGN KEY (`METHODID`) REFERENCES `method` (`METHODID`),
  CONSTRAINT `FK_KUEH_ORIGIN` FOREIGN KEY (`ORIGINID`) REFERENCES `origin` (`ORIGINCODE`),
  CONSTRAINT `FK_KUEH_POPULAR` FOREIGN KEY (`POPULARID`) REFERENCES `popularity` (`POPULARID`),
  CONSTRAINT `FK_KUEH_USERNAME` FOREIGN KEY (`USERNAME`) REFERENCES `users` (`USERNAME`)
) ENGINE=InnoDB AUTO_INCREMENT=242 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table kuehlegacy.kueh: ~11 rows (approximately)
DELETE FROM `kueh`;
INSERT INTO `kueh` (`KUEHID`, `KUEHNAME`, `KUEHDESC`, `TAGKUEH`, `VIDEO`, `FOODTYPECODE`, `METHODID`, `POPULARID`, `ORIGINID`, `IMAGE`, `USERNAME`) VALUES
	(1, 'Kuih Lapis', 'Kuih Lapis adalah sejenis kuih tradisional berbilang lapisan yang sangat popular di Malaysia. Setiap lapisan dibuat daripada tepung beras, santan dan gula, kemudian dikukus satu persatu sehingga membentuk lapisan berwarna-warni yang cantik. Teksturnya yang kenyal dan manis menjadikannya hidangan istimewa dalam majlis-majlis keraian.', NULL, 'https://www.youtube.com/watch?v=example1', 1, 1, 1, 4, 'kuih_lapis.jpg', 'Haziq Akram'),
	(2, 'Kuih Seri Muka', 'Kuih Seri Muka atau Kuih Salat terdiri daripada dua lapisan - lapisan bawah pulut putih dan lapisan atas custard pandan berwarna hijau. Nama "seri muka" bermaksud "wajah cantik" kerana lapisan pandan yang licin dan menawan. Gabungan pulut yang wangi dengan custard pandan yang lembut menghasilkan rasa yang sempurna.', NULL, 'https://www.youtube.com/watch?v=example2', 3, 1, 1, 10, 'seri_muka.jpg', 'Haziq Akram'),
	(3, 'Onde-Onde', 'Onde-Onde adalah kuih tradisional berbentuk bulat berwarna hijau daripada jus pandan. Diisi dengan gula melaka cair di tengahnya dan disalut dengan kelapa parut putih. Apabila digigit, gula melaka yang manis akan meleleh di dalam mulut memberikan pengalaman yang unik dan sedap.', NULL, 'https://www.youtube.com/watch?v=example3', 1, 4, 1, 1, 'onde_onde.jpg', 'Haziq Akram'),
	(4, 'Kuih Bahulu', 'Kuih Bahulu adalah kek span tradisional Melayu yang dibuat dalam acuan khas berbentuk bunga. Teksturnya yang lembut dan gebu dengan rasa mentega dan vanilla yang harum menjadikannya popular terutama semasa perayaan Hari Raya. Bahulu yang baik mestilah naik mengembang dengan permukaan yang rata.', NULL, 'https://www.youtube.com/watch?v=example4', 2, 3, 1, 1, 'bahulu.jpg', 'Haziq Akram'),
	(5, 'Kuih Ketayap', 'Kuih Ketayap atau Kuih Dadar adalah pancake nipis berwarna hijau pandan yang diisi dengan kelapa parut bergula dan digulung. Kombinasi daun pandan yang wangi dengan kelapa manis memberikan rasa tropika yang autentik. Cara menggulung yang kemas menunjukkan kemahiran tukang masak.', NULL, 'https://www.youtube.com/watch?v=example5', 1, 3, 1, 3, 'ketayap.jpg', 'Haziq Akram'),
	(6, 'Kuih Kapit', 'Kuih Kapit atau Love Letters adalah sejenis kuih rangup nipis yang dibuat menggunakan acuan besi panas. Adunan santan dan gula dibakar di atas api sehingga garing, kemudian digulung atau dilipat semasa panas. Kuih ini adalah wajib dalam perayaan Tahun Baru Cina dan Hari Raya.', NULL, 'https://www.youtube.com/watch?v=example6', 2, 3, 2, 4, 'kapit.jpg', 'Haziq Akram'),
	(7, 'Pulut Inti', 'Pulut Inti terdiri daripada pulut kuning bersantan yang ditutup dengan serawa kelapa bergula melaka. Warna kuning daripada kunyit dan aroma pandan menjadikan kuih ini sangat menarik. Perpaduan pulut yang wangi dengan kelapa manis adalah kombinasi klasik dalam masakan Melayu.', NULL, 'https://www.youtube.com/watch?v=example7', 3, 1, 1, 10, 'pulut_inti.jpg', 'Haziq Akram'),
	(8, 'Kuih Talam', 'Kuih Talam terdiri daripada dua lapisan - lapisan bawah berwarna hijau pandan dan lapisan atas putih santan. Tekstur lapisan bawah yang kenyal bergabung dengan lapisan atas yang lembut creamy menghasilkan kontras yang menarik. Nama "talam" merujuk kepada dulang yang digunakan untuk mengukus kuih ini.', NULL, 'https://www.youtube.com/watch?v=example8', 1, 1, 1, 8, 'talam.png', 'Haziq Akram'),
	(9, 'Kuih Cara Manis', 'Kuih Cara adalah kuih comel berbentuk bunga yang dimasak dalam acuan khas. Dibuat daripada tepung beras, santan dan gula, teksturnya lembut dengan bahagian tepi yang rangup. Biasanya dihias dengan bijan putih di atas. Kuih ini sangat popular dalam majlis-majlis tradisional.', NULL, 'https://www.youtube.com/watch?v=example9', 1, 3, 2, 7, 'cara.jpg', 'Haziq Akram'),
	(10, 'Kuih Bingka Ubi Kayu', 'Kuih Bingka Ubi adalah kuih tradisional yang diperbuat daripada ubi kayu parut, santan, gula dan telur. Dibakar sehingga permukaannya berwarna keemasan dengan bahagian dalam yang lembut dan moist. Aroma pandan dan vanila memberikan bau yang harum. Sangat popular di negeri-negeri utara.', NULL, 'https://www.youtube.com/watch?v=example10', 1, 3, 1, 2, 'bingka_ubi.jpeg', 'Haziq Akram'),
	(241, 'KUIH KERIA', 'Kuih Keria adalah sejenis kuih tradisional Malaysia yang diperbuat daripada ubi keledek. Kuih berbentuk donat ini digoreng hingga garing dan kemudian disalut dengan gula. Rasanya yang manis dan teksturnya yang lembut di dalam serta rangup di luar menjadikannya kegemaran ramai. Kuih ini sangat popular semasa bulan Ramadan dan majlis-majlis keramaian.', NULL, 'https://www.youtube.com/watch?v=example', 1, 2, 2, 2, '241_1769529688.jpg', 'MUHAMMAD HAZIQ AKRAM MOHD ASHRI');

-- Dumping structure for table kuehlegacy.method
CREATE TABLE IF NOT EXISTS `method` (
  `METHODID` int NOT NULL AUTO_INCREMENT,
  `METHODNAME` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`METHODID`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table kuehlegacy.method: ~5 rows (approximately)
DELETE FROM `method`;
INSERT INTO `method` (`METHODID`, `METHODNAME`) VALUES
	(1, 'Kukus'),
	(2, 'Goreng'),
	(3, 'Bakar'),
	(4, 'Rebus'),
	(5, 'Sejuk');

-- Dumping structure for table kuehlegacy.origin
CREATE TABLE IF NOT EXISTS `origin` (
  `ORIGINCODE` int NOT NULL AUTO_INCREMENT,
  `NAMESTATE` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ORIGINCODE`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table kuehlegacy.origin: ~16 rows (approximately)
DELETE FROM `origin`;
INSERT INTO `origin` (`ORIGINCODE`, `NAMESTATE`) VALUES
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

-- Dumping structure for table kuehlegacy.popularity
CREATE TABLE IF NOT EXISTS `popularity` (
  `POPULARID` int NOT NULL AUTO_INCREMENT,
  `LEVEL` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`POPULARID`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table kuehlegacy.popularity: ~5 rows (approximately)
DELETE FROM `popularity`;
INSERT INTO `popularity` (`POPULARID`, `LEVEL`) VALUES
	(1, 'Sangat Popular'),
	(2, 'Popular'),
	(3, 'Sederhana'),
	(4, 'Kurang Popular'),
	(5, 'Jarang');

-- Dumping structure for table kuehlegacy.shop
CREATE TABLE IF NOT EXISTS `shop` (
  `SHOPID` int NOT NULL AUTO_INCREMENT,
  `SHOPNAME` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `LOCATION` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`SHOPID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table kuehlegacy.shop: ~0 rows (approximately)
DELETE FROM `shop`;

-- Dumping structure for table kuehlegacy.steps
CREATE TABLE IF NOT EXISTS `steps` (
  `STEPID` int NOT NULL AUTO_INCREMENT,
  `KUEHID` int DEFAULT NULL,
  `STEP` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`STEPID`),
  KEY `FK_STEPS_KUEH` (`KUEHID`),
  CONSTRAINT `FK_STEPS_KUEH` FOREIGN KEY (`KUEHID`) REFERENCES `kueh` (`KUEHID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=545 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table kuehlegacy.steps: ~81 rows (approximately)
DELETE FROM `steps`;
INSERT INTO `steps` (`STEPID`, `KUEHID`, `STEP`) VALUES
	(464, 1, 'Campurkan tepung beras, tepung ubi kayu dan garam. Gaul rata.'),
	(465, 1, 'Masukkan santan dan air pandan secara beransur-ansur sambil dikacau sehingga sebati.'),
	(466, 1, 'Masukkan gula dan kacau hingga larut. Tapis adunan.'),
	(467, 1, 'Bahagikan adunan kepada beberapa bahagian dan masukkan pewarna mengikut citarasa.'),
	(468, 1, 'Sapukan minyak pada loyang. Tuang satu lapisan adunan dan kukus 3-5 minit hingga masak.'),
	(469, 1, 'Ulang proses dengan warna berbeza sehingga adunan habis. Pastikan setiap lapisan masak dahulu.'),
	(470, 1, 'Kukus lapisan terakhir selama 10 minit. Sejukkan dan potong kepada bentuk yang dikehendaki.'),
	(471, 2, 'Rendam beras pulut semalaman. Toskan dan kukus bersama santan dan garam selama 20 minit.'),
	(472, 2, 'Padatkan pulut dalam loyang yang telah disapu minyak. Ketepikan.'),
	(473, 2, 'Pukul telur bersama gula hingga sebati. Masukkan tepung jagung.'),
	(474, 2, 'Masak santan dengan daun pandan. Tuangkan ke dalam campuran telur sambil dikacau.'),
	(475, 2, 'Masak atas api sederhana sambil dikacau sehingga pekat. Masukkan air pandan.'),
	(476, 2, 'Tuangkan adunan pandan di atas pulut. Ratakan dan kukus selama 30 minit.'),
	(477, 2, 'Sejukkan sepenuhnya sebelum dipotong. Sapu pisau dengan minyak untuk memudahkan pemotongan.'),
	(478, 3, 'Campurkan tepung pulut dengan ubi kentang yang dilenyek.'),
	(479, 3, 'Masukkan air pandan sedikit demi sedikit sambil diuli hingga menjadi doh yang lembut.'),
	(480, 3, 'Bulat-bulatkan doh sebesar biji limau kasturi. Pipihkan dan letak gula melaka di tengah.'),
	(481, 3, 'Tutup rapi dan bulatkan semula. Pastikan gula melaka tidak bocor.'),
	(482, 3, 'Didihkan air dan masukkan onde-onde. Masak hingga timbul ke permukaan air.'),
	(483, 3, 'Angkat dan toskan. Golek di dalam kelapa parut yang telah digaul dengan sedikit garam.'),
	(484, 3, 'Hidangkan sejuk atau suam.'),
	(485, 4, 'Panaskan acuan bahulu di dalam oven 180??C. Sapukan minyak pada acuan.'),
	(486, 4, 'Pukul telur dan gula menggunakan mixer berkelajuan tinggi sehingga kembang dan gebu (10-15 minit).'),
	(487, 4, 'Ayak tepung dan serbuk penaik. Masukkan ke dalam adunan telur secara beransur-ansur.'),
	(488, 4, 'Masukkan esen vanila dan mentega cair. Gaul perlahan menggunakan spatula.'),
	(489, 4, 'Tuangkan adunan ke dalam acuan bahulu hingga 3/4 penuh.'),
	(490, 4, 'Bakar pada suhu 180??C selama 10-12 minit atau sehingga keemasan.'),
	(491, 4, 'Keluarkan bahulu dari acuan sejurus selepas keluar dari oven. Sejukkan atas redai.'),
	(492, 5, 'Untuk inti: Masak kelapa parut dengan gula melaka dan daun pandan hingga gula cair dan melekat. Sejukkan.'),
	(493, 5, 'Untuk kulit: Campurkan tepung, telur, santan, air pandan dan garam. Kacau hingga licin.'),
	(494, 5, 'Tapis adunan dan biarkan rehat selama 30 minit.'),
	(495, 5, 'Panaskan kuali dadar. Cedok sedikit adunan dan putar kuali supaya nipis dan rata.'),
	(496, 5, 'Masak sebelah sahaja sehingga matang. Angkat dan ulang hingga habis.'),
	(497, 5, 'Letakkan inti kelapa di atas kulit, lipat tepi kiri dan kanan, kemudian gulung.'),
	(498, 5, 'Potong serong dan hidangkan.'),
	(499, 6, 'Pukul telur dan gula hingga kembang dan putih gebu.'),
	(500, 6, 'Masukkan santan, tepung gandum dan tepung beras. Kacau hingga sebati.'),
	(501, 6, 'Tapis adunan dan biarkan rehat 2 jam atau semalaman untuk hasil yang lebih rangup.'),
	(502, 6, 'Panaskan acuan kapit di atas api. Cedok adunan ke dalam acuan.'),
	(503, 6, 'Tekan acuan rapat dan bakar kedua-dua belah sehingga keemasan (lebih kurang 1 minit).'),
	(504, 6, 'Buka acuan dan cepat-cepat gulung atau lipat kapit semasa masih panas.'),
	(505, 6, 'Sejukkan di dalam bekas kedap udara. Kapit yang baik mestilah rangup dan garing.'),
	(506, 7, 'Rendam beras pulut 4 jam atau semalaman. Toskan.'),
	(507, 7, 'Kukus pulut bersama santan, air pandan, kunyit dan garam selama 25-30 minit.'),
	(508, 7, 'Kacau sesekali supaya santan sebati. Pastikan pulut masak dan wangi.'),
	(509, 7, 'Untuk serawa: Masak kelapa parut dengan gula melaka dan daun pandan sambil dikacau sehingga gula larut dan melekat.'),
	(510, 7, 'Letakkan pulut kuning dalam dulang. Padatkan sedikit.'),
	(511, 7, 'Taburkan serawa kelapa di atas pulut. Ratakan.'),
	(512, 7, 'Sejukkan dan potong kepada bentuk segi empat sama atau segi tiga.'),
	(513, 8, 'Untuk lapisan bawah: Campurkan tepung beras, tepung ubi kayu, 300ml santan, air pandan dan gula. Kacau rata.'),
	(514, 8, 'Tuangkan adunan hijau ke dalam loyang. Kukus 15 minit hingga masak.'),
	(515, 8, 'Untuk lapisan atas: Campurkan 300ml santan, tepung gandum, garam. Masak sambil dikacau sehingga pekat.'),
	(516, 8, 'Tuangkan adunan putih di atas lapisan hijau yang telah masak. Ratakan.'),
	(517, 8, 'Kukus lagi 15-20 minit sehingga lapisan atas masak.'),
	(518, 8, 'Biarkan sejuk sepenuhnya sebelum dipotong. Sapu pisau dengan minyak untuk pemotongan yang kemas.'),
	(519, 9, 'Campurkan tepung beras, gula, santan, air dan telur. Pukul hingga sebati.'),
	(520, 9, 'Masukkan serbuk penaik dan gaul rata. Tapis adunan.'),
	(521, 9, 'Panaskan acuan cara di atas api sederhana. Sapukan sedikit minyak.'),
	(522, 9, 'Tuangkan adunan ke dalam acuan hingga 3/4 penuh. Taburkan bijan.'),
	(523, 9, 'Tutup acuan dan masak sehingga tepi kuih garing dan bahagian tengah masak.'),
	(524, 9, 'Keluarkan cara menggunakan garfu atau lidi. Sejukkan atas redai.'),
	(525, 9, 'Hidangkan sejuk. Simpan dalam bekas kedap udara.'),
	(526, 10, 'Panaskan oven ke suhu 180??C. Sapukan loyang dengan mentega.'),
	(527, 10, 'Campurkan ubi kayu parut dengan gula. Kacau hingga gula larut.'),
	(528, 10, 'Masukkan telur satu persatu sambil dipukul. Pukul hingga sebati.'),
	(529, 10, 'Tuangkan santan, mentega cair, esen vanila dan garam. Kacau rata.'),
	(530, 10, 'Tambah daun pandan yang telah diikat simpul. Tuangkan ke dalam loyang.'),
	(531, 10, 'Bakar selama 45-60 minit atau sehingga permukaan keemasan dan bila cucuk lidi keluar bersih.'),
	(532, 10, 'Sejukkan dalam loyang selama 10 minit. Keluarkan dan potong. Hidangkan sejuk atau suam.'),
	(533, 241, 'Kukus ubi keledek sehingga lembut selama 20-25 minit'),
	(534, 241, 'Lenyekkan ubi keledek yang telah dikukus hingga halus'),
	(535, 241, 'Campurkan tepung gandum, gula dan garam ke dalam ubi keledek'),
	(536, 241, 'Uli adunan sehingga sebati dan boleh dibentuk'),
	(537, 241, 'Bentukkan adunan menjadi bulat dan lorekkan bahagian tengah seperti donat'),
	(538, 241, 'Panaskan minyak dengan api sederhana'),
	(539, 241, 'Goreng kuih keria sehingga perang keemasan'),
	(540, 241, 'Angkat dan toskan minyak berlebihan'),
	(541, 241, 'Masak gula dengan air sehingga pekat dan berbuih'),
	(542, 241, 'Gaul kuih keria dalam gula panas sehingga bersalut'),
	(543, 241, 'Keluarkan dan sejukkan di atas dulang'),
	(544, 241, 'Kuih keria siap untuk dihidangkan');

-- Dumping structure for table kuehlegacy.users
CREATE TABLE IF NOT EXISTS `users` (
  `USERNAME` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `NAME` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `PASSWORD` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `EMAIL` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `PHONENUM` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `IMAGE` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `OAUTH_PROVIDER` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `OAUTH_UID` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`USERNAME`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table kuehlegacy.users: ~2 rows (approximately)
DELETE FROM `users`;
INSERT INTO `users` (`USERNAME`, `NAME`, `PASSWORD`, `EMAIL`, `IMAGE`, `OAUTH_PROVIDER`, `OAUTH_UID`) VALUES
	('Haziq Akram', 'Haziq', NULL, 'haziqakram99@gmail.com', 'https://lh3.googleusercontent.com/a/ACg8ocII1zvTTQdl_SpS6313oIMeL7CuHpI_AIv3G7ECByaDLh_PBKao=s96-c', NULL, NULL),
	('MUHAMMAD HAZIQ AKRAM MOHD ASHRI', 'MUHAMMAD HAZIQ AKRAM', NULL, '2024741533@student.uitm.edu.my', 'https://lh3.googleusercontent.com/a/ACg8ocIFXt0JJU_jH8eo27aSjefMIbzbM1JLBCWzZ_IhehhIX5tW=s96-c', NULL, NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
