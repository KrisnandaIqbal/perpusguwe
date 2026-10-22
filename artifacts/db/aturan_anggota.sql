/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.6.28-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: perpustakaan_inlislite
-- ------------------------------------------------------
-- Server version	10.6.28-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `memberrules`
--

DROP TABLE IF EXISTS `memberrules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `memberrules` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NameCategory` varchar(50) NOT NULL,
  `Contents` text DEFAULT NULL,
  `isPublish` bit(1) DEFAULT b'1',
  `SortNum` int(11) DEFAULT 0,
  `CreateBy` int(11) DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL,
  `CreateTerminal` varchar(100) DEFAULT NULL,
  `UpdateBy` int(11) DEFAULT NULL,
  `UpdateDate` datetime DEFAULT NULL,
  `UpdateTerminal` varchar(100) DEFAULT NULL,
  `Branch_id` int(11) DEFAULT NULL,
  `active` int(11) DEFAULT 1,
  PRIMARY KEY (`ID`) USING BTREE,
  KEY `memberrules_createby` (`CreateBy`) USING BTREE,
  KEY `memberrules_updateby` (`UpdateBy`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `memberrules`
--

LOCK TABLES `memberrules` WRITE;
/*!40000 ALTER TABLE `memberrules` DISABLE KEYS */;
INSERT INTO `memberrules` VALUES (1,'Persyaratan','<ul>                <li>Minimal berusia 17 tahun&nbsp;</li>                <li>Warga Negara Indonesia<br></li>                <li>Menunjukkan tanda pengenal&nbsp;</li>            </ul>','',4,NULL,'2013-12-29 11:01:50','192.168.101.59',1,'2026-05-23 20:10:01','127.0.0.1',20986,1),(3,'Tata Tertib','<ul>            <li>Kartu anggota harus dibawa setiap kali berkunjung</li>            <li>Kartu anggota tidak boleh dipinjamkan kepada orang lain</li>            <li>Jika kartu anggota&nbsp;hilang, wajib membawa surat keterangan dari pihak berwenang untuk penggantiannya</li>            </ul>','',2,NULL,'2013-06-18 15:04:49','192.168.16.38',NULL,'2016-02-29 18:10:26','192.168.0.1',20986,1);
/*!40000 ALTER TABLE `memberrules` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-22 15:32:12
