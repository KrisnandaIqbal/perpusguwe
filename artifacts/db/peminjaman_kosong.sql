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
-- Table structure for table `collectionloans`
--

DROP TABLE IF EXISTS `collectionloans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `collectionloans` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `CollectionCount` int(11) DEFAULT 0,
  `LateCount` int(11) DEFAULT 0,
  `ExtendCount` int(11) DEFAULT 0,
  `LoanCount` int(11) DEFAULT 0,
  `ReturnCount` int(11) DEFAULT 0,
  `Member_id` int(11) NOT NULL,
  `Branch_id` int(11) DEFAULT NULL,
  `CreateBy` int(11) DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL,
  `CreateTerminal` varchar(100) DEFAULT NULL,
  `UpdateBy` int(11) DEFAULT NULL,
  `UpdateDate` datetime DEFAULT NULL,
  `UpdateTerminal` varchar(100) DEFAULT NULL,
  `KIILastUploadDate` datetime DEFAULT NULL,
  `LocationLibrary_id` int(11) DEFAULT NULL,
  `active` int(11) DEFAULT 1,
  PRIMARY KEY (`ID`) USING BTREE,
  KEY `Member_id` (`Member_id`) USING BTREE,
  KEY `Branch_id` (`Branch_id`) USING BTREE,
  KEY `collectionloans_createby` (`CreateBy`) USING BTREE,
  KEY `collectionloans_updateby` (`UpdateBy`) USING BTREE,
  KEY `FK_collectionloans_location_library` (`LocationLibrary_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=26102100129 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `collectionloanitems`
--

DROP TABLE IF EXISTS `collectionloanitems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `collectionloanitems` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CollectionLoan_id` bigint(20) NOT NULL,
  `LoanDate` datetime DEFAULT NULL,
  `DueDate` datetime DEFAULT NULL,
  `ActualReturn` datetime DEFAULT NULL,
  `LateDays` int(11) DEFAULT NULL,
  `LoanStatus` varchar(50) DEFAULT NULL,
  `Collection_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `CreateBy` int(11) DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL,
  `CreateTerminal` varchar(100) DEFAULT NULL,
  `UpdateBy` int(11) DEFAULT NULL,
  `UpdateDate` datetime DEFAULT NULL,
  `UpdateTerminal` varchar(100) DEFAULT NULL,
  `KIILastUploadDate` datetime DEFAULT NULL,
  `Branch_id` int(11) DEFAULT NULL,
  `active` int(11) DEFAULT 1,
  PRIMARY KEY (`ID`) USING BTREE,
  KEY `collectionloanitems_members` (`member_id`) USING BTREE,
  KEY `collectionloanitems_LoanDate` (`LoanDate`) USING BTREE,
  KEY `collectionloanitems_LoanStatus` (`LoanStatus`) USING BTREE,
  KEY `collectionloanitems_createby` (`CreateBy`) USING BTREE,
  KEY `collectionloanitems_updateby` (`UpdateBy`) USING BTREE,
  KEY `collectionloanitems_collections` (`Collection_id`) USING BTREE,
  KEY `collectionloanitems_collectionloans` (`CollectionLoan_id`) USING BTREE,
  KEY `collectionloanitems_duedate` (`DueDate`) USING BTREE,
  KEY `collectionloanitems_actualreturn` (`ActualReturn`) USING BTREE,
  KEY `collectionloanitems_idx1` (`LoanDate`,`member_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=165 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `collectionloanextends`
--

DROP TABLE IF EXISTS `collectionloanextends`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `collectionloanextends` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `CollectionLoan_id` varchar(255) NOT NULL,
  `CollectionLoanItem_id` int(11) NOT NULL,
  `Collection_id` int(11) NOT NULL,
  `Member_id` int(11) NOT NULL,
  `DateExtend` datetime NOT NULL,
  `DueDateExtend` datetime NOT NULL,
  `CreateBy` int(11) DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL,
  `CreateTerminal` varchar(100) DEFAULT NULL,
  `UpdateBy` int(11) DEFAULT NULL,
  `UpdateDate` datetime DEFAULT NULL,
  `UpdateTerminal` varchar(100) DEFAULT NULL,
  `Branch_id` int(11) DEFAULT NULL,
  `active` int(11) DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `collectionloanextends_colloan_idx` (`CollectionLoan_id`) USING BTREE,
  KEY `collectionloanextends_colloanitem_idx` (`CollectionLoanItem_id`) USING BTREE,
  KEY `collectionloanextends_col_idx` (`Collection_id`) USING BTREE,
  KEY `collectionloanextends_member_idx` (`Member_id`) USING BTREE,
  KEY `collectionloanextends_createby_idx` (`CreateBy`) USING BTREE,
  KEY `collectionloanextends_updateby_idx` (`UpdateBy`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-22 15:32:27
