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
-- Table structure for table `members`
--

DROP TABLE IF EXISTS `members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `members` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `MemberNo` varchar(50) NOT NULL,
  `Fullname` varchar(255) NOT NULL,
  `PlaceOfBirth` varchar(255) DEFAULT NULL,
  `DateOfBirth` datetime DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `AddressNow` varchar(255) DEFAULT NULL,
  `Phone` varchar(100) DEFAULT NULL,
  `InstitutionName` varchar(255) DEFAULT NULL,
  `InstitutionAddress` varchar(255) DEFAULT NULL,
  `InstitutionPhone` varchar(255) DEFAULT NULL,
  `IdentityType_id` int(11) DEFAULT NULL,
  `IdentityNo` varchar(100) DEFAULT NULL,
  `EducationLevel_id` int(11) DEFAULT NULL,
  `Sex_id` int(11) DEFAULT NULL,
  `MaritalStatus_id` int(11) DEFAULT NULL,
  `Job_id` int(11) DEFAULT NULL,
  `RegisterDate` datetime DEFAULT NULL,
  `TglPersetujuan` datetime DEFAULT NULL,
  `AlasanPerbaikan` varchar(30) DEFAULT NULL,
  `CatatanPerbaikan` varchar(500) DEFAULT NULL,
  `TglKeputusan` datetime DEFAULT NULL,
  `KeputusanBy` int(11) DEFAULT NULL,
  `NotifJenis` varchar(20) DEFAULT NULL,
  `NotifTerkirim` tinyint(1) DEFAULT NULL,
  `NotifDate` datetime DEFAULT NULL,
  `EndDate` datetime DEFAULT NULL,
  `MotherMaidenName` varchar(100) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `JenisPermohonan_id` int(11) DEFAULT NULL,
  `JenisAnggota_id` int(11) DEFAULT NULL,
  `StatusAnggota_id` int(11) DEFAULT NULL,
  `LoanReturnLateCount` int(11) DEFAULT 0,
  `Branch_id` int(11) DEFAULT NULL,
  `PhotoUrl` varchar(255) DEFAULT NULL,
  `CreateBy` int(11) DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL,
  `CreateTerminal` varchar(100) DEFAULT NULL,
  `UpdateBy` int(11) DEFAULT NULL,
  `UpdateDate` datetime DEFAULT NULL,
  `UpdateTerminal` varchar(100) DEFAULT NULL,
  `NoHp` varchar(100) DEFAULT NULL,
  `NamaDarurat` varchar(100) DEFAULT NULL,
  `TelpDarurat` varchar(100) DEFAULT NULL,
  `AlamatDarurat` varchar(255) DEFAULT NULL,
  `StatusHubunganDarurat` varchar(100) DEFAULT NULL,
  `City` varchar(255) DEFAULT NULL,
  `Province` varchar(255) DEFAULT NULL,
  `CityNow` varchar(255) DEFAULT NULL,
  `ProvinceNow` varchar(255) DEFAULT NULL,
  `Kecamatan` varchar(100) DEFAULT NULL,
  `Kelurahan` varchar(100) DEFAULT NULL,
  `RT` varchar(50) DEFAULT NULL,
  `RW` varchar(50) DEFAULT NULL,
  `KecamatanNow` varchar(100) DEFAULT NULL,
  `KelurahanNow` varchar(100) DEFAULT NULL,
  `RTNow` varchar(50) DEFAULT NULL,
  `RWNow` varchar(50) DEFAULT NULL,
  `Kelas_id` int(11) DEFAULT NULL,
  `TahunAjaran` varchar(50) DEFAULT NULL,
  `Agama_id` int(11) DEFAULT NULL,
  `Fakultas_id` int(11) DEFAULT NULL,
  `Jurusan_id` int(11) DEFAULT NULL,
  `ProgramStudi_id` int(11) DEFAULT NULL,
  `JenjangPendidikan_id` int(11) DEFAULT NULL,
  `UnitKerja_id` int(11) DEFAULT NULL,
  `SubUnitKerja_id` int(11) DEFAULT NULL,
  `status_non_asn` enum('staf','magang','magang_nasional','magang_biasa','magang_reguler') DEFAULT NULL,
  `KeteranganLain` varchar(255) DEFAULT NULL,
  `IsLunasBiayaPendaftaran` bit(1) DEFAULT b'0',
  `BiayaPendaftaran` decimal(10,0) DEFAULT 0,
  `TanggalBebasPustaka` datetime DEFAULT NULL,
  `KIILastUploadDate` datetime DEFAULT NULL,
  `IsKeranjang` int(11) NOT NULL DEFAULT 0,
  `active` int(11) DEFAULT 1,
  `IsOnlineActive` int(11) DEFAULT 0,
  PRIMARY KEY (`ID`) USING BTREE,
  KEY `Branch_id` (`Branch_id`) USING BTREE,
  KEY `MemberNo` (`MemberNo`) USING BTREE,
  KEY `member_job` (`Job_id`) USING BTREE,
  KEY `member_sex` (`Sex_id`) USING BTREE,
  KEY `member_agama` (`Agama_id`) USING BTREE,
  KEY `member_unit_kerja` (`UnitKerja_id`) USING BTREE,
  KEY `members_status_perkawinan` (`MaritalStatus_id`) USING BTREE,
  KEY `members_jenispermohonan` (`JenisPermohonan_id`) USING BTREE,
  KEY `members_statusanggota` (`StatusAnggota_id`) USING BTREE,
  KEY `members_education` (`EducationLevel_id`) USING BTREE,
  KEY `members_jenis_anggota` (`JenisAnggota_id`) USING BTREE,
  KEY `members_identitytype` (`IdentityType_id`) USING BTREE,
  KEY `members_kelassiswa` (`Kelas_id`) USING BTREE,
  KEY `members_fakultas` (`Fakultas_id`) USING BTREE,
  KEY `members_jurusan` (`Jurusan_id`) USING BTREE,
  KEY `members_createby` (`CreateBy`) USING BTREE,
  KEY `members_updateby` (`UpdateBy`) USING BTREE,
  KEY `members_dateofbirth` (`DateOfBirth`) USING BTREE,
  KEY `members_createdate` (`CreateDate`) USING BTREE,
  KEY `members_updatedate` (`UpdateDate`) USING BTREE,
  KEY `members_kii` (`KIILastUploadDate`) USING BTREE,
  KEY `members_registerdate` (`RegisterDate`) USING BTREE,
  KEY `members_programstudi_idx` (`ProgramStudi_id`) USING BTREE,
  KEY `members_jenjang_pendidikan` (`JenjangPendidikan_id`) USING BTREE,
  KEY `idx_members_subunit` (`SubUnitKerja_id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-22 15:33:09
