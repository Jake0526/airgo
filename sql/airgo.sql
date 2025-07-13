-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: localhost    Database: airgo
-- ------------------------------------------------------
-- Server version	8.0.42

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` VALUES (1,'admin','Matt Ranillo Flores','1234');
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `booking_history`
--

DROP TABLE IF EXISTS `booking_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `booking_history` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `service` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `phone_number` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `technician` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `moved_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking_history`
--

LOCK TABLES `booking_history` WRITE;
/*!40000 ALTER TABLE `booking_history` DISABLE KEYS */;
INSERT INTO `booking_history` VALUES (1,26,'shane flores','shane@gmail.com','Aircon Cleaning','ecoland','2025-03-22','10:00:00','09124536392',NULL,'Cancelled','2025-06-05 22:19:03'),(2,18,'Honey Flores','mardy@gmail.com','Aircon Cleaning','ecoland','2025-05-23','16:00:00','+639124536392',NULL,'Cancelled','2025-06-05 22:19:03'),(3,4,'sassie','sassie@gmail.com','Aircon Check-up','tugbok','2025-05-29','16:00:00','+639124536392',NULL,'Cancelled','2025-06-05 22:19:03'),(4,18,'Honey Flores','mik@gmail.com','Aircon Cleaning','Bankerohan Davao city','2025-03-21','14:00:00','09124536392',NULL,'done','2025-06-05 22:49:53'),(5,18,'Christine Perez','hone@gmail.com','Aircon Check-up','Bankerohan Davao city','2025-03-28','11:00:00','09124536392',NULL,'done','2025-06-05 22:49:53'),(6,4,'Honey Flores','haki@gmail.com','Aircon Check-up','ecoland','2025-06-02','15:00:00','+639124536392',NULL,'done','2025-06-05 22:49:53'),(7,5,'Honeymardsflores','honeymardsflores@gmail.com','Aircon Check-up','tugbok','2025-05-29','17:00:00','+639124536392',NULL,'done','2025-06-05 22:49:53'),(8,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-05-31','12:00:00','+639124536311',NULL,'done','2025-06-05 22:49:53'),(11,3,'kenneth maylos','kenneth@gmail.com','Aircon Check-up','talomo','2025-05-30','11:00:00','+639124536398',NULL,'Completed','2025-06-06 00:02:39'),(12,2,'mardsflores','honeymardsflores022602@gmail.com','Aircon Check-up','Bankeruhan','2025-05-30','11:00:00','+639124536392',NULL,'Completed','2025-06-06 00:02:50'),(13,18,'Christine Perez','kim@gmail.com','Aircon Cleaning','agdao','2025-05-31','14:00:00','+639124536392',NULL,'done','2025-06-06 03:40:16'),(14,2,'mardsflores','honeymardsflores022602@gmail.com','Aircon Relocation','Bankeruhan','2025-05-31','13:00:00','+639124536392',NULL,'done','2025-06-06 03:40:16'),(16,18,'Lyka Villagonzalo','hone@gmail.com','Aircon Check-up','Bankerohan Davao city','2024-12-06','11:00:00','09124536392',NULL,'done','2025-06-06 04:10:11'),(17,1,'Honey Flores','mardy@gmail.com','Aircon Check-up','ecoland','2025-05-15','15:00:00','+639124536392',NULL,'done','2025-06-06 04:10:11'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Check-up','talomo','2025-07-03','13:00:00','09124536392',NULL,'Completed','2025-06-23 00:41:14'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Check-up','talomo','2025-07-14','08:00:00','09124536392',NULL,'Completed','2025-06-23 01:50:26'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Relocation','talomo','2025-06-26','14:40:00','09124536392',NULL,'Completed','2025-06-23 02:30:12'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-24','08:00:00','09124536392',NULL,'done','2025-06-23 12:16:48'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Check-up','talomo','2025-06-25','09:40:00','09124536392',NULL,'done','2025-06-23 12:16:48'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Check-up','talomo','2025-06-30','08:00:00','09124536392',NULL,'done','2025-06-23 12:16:48'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-25','08:00:00','09124536392',NULL,'done','2025-06-23 12:20:40'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-28','14:40:00','09124536392',NULL,'done','2025-06-23 12:20:40'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-25','14:40:00','09124536392',NULL,'done','2025-06-23 12:57:31'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-25','14:40:00','09124536392',NULL,'Completed','2025-06-23 12:58:17'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-07-25','13:00:00','09124536392',NULL,'Completed','2025-06-23 13:30:30'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Check-up','talomo','2025-06-26','13:00:00','09124536392',NULL,'done','2025-06-23 13:32:05'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-26','09:40:00','09124536392',NULL,'Completed','2025-06-23 15:22:52'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-26','08:00:00','09124536392',NULL,'Cancelled','2025-06-23 15:37:41'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-24','08:00:00','09124536392',NULL,'Completed','2025-06-23 15:38:38'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-24','08:00:00','09124536392',NULL,'Completed','2025-06-23 15:39:31'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-24','08:00:00','09124536392',NULL,'Completed','2025-06-23 16:21:32'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Check-up','talomo','2025-06-24','08:00:00','09124536392',NULL,'Cancelled','2025-06-23 16:22:37'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-25','08:00:00','09124536392',NULL,'Cancelled','2025-06-23 17:00:45'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Relocation','talomo','2025-07-01','08:00:00','09124536392',NULL,'Cancelled','2025-06-23 17:02:45'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Relocation','talomo','2025-06-28','13:00:00','09124536392',NULL,'Rejected','2025-06-23 17:14:51'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-07-04','08:00:00','09124536392',NULL,'Cancelled','2025-06-23 17:29:12'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Check-up','talomo','2025-06-28','08:00:00','09124536392',NULL,'Cancelled','2025-06-23 17:36:23'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Check-up','talomo','2025-06-29','09:40:00','09124536392',NULL,'Rejected','2025-06-23 17:38:03'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Check-up','talomo','2025-07-08','08:00:00','09124536392',NULL,'done','2025-06-23 17:40:16'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-25','08:00:00','09124536392',NULL,'done','2025-06-23 17:58:02'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Relocation','talomo','2025-06-30','13:00:00','09124536392',NULL,'Cancelled','2025-06-23 19:06:47'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-07-08','08:00:00','09124536392',NULL,'done','2025-06-23 19:08:01'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Check-up','talomo','2025-06-27','08:00:00','09090909090',NULL,'done','2025-06-24 02:46:17'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Check-up','talomo','2025-06-26','09:40:00','09090909090',NULL,'done','2025-06-24 02:58:16'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Repair','talomo','2025-06-25','14:40:00','09090909090',NULL,'Completed','2025-06-24 03:18:25'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Check-up','talomo','2025-06-26','08:00:00','09090909090',NULL,'Completed','2025-06-24 03:29:47'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Repair','talomo','2025-07-03','08:00:00','09090909090',NULL,'Rejected','2025-06-24 03:31:26'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Relocation','talomo','2025-06-26','14:40:00','09124536392',NULL,'Completed','2025-06-24 06:39:20'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Check-up','talomo','2025-06-27','13:00:00','09124536392',NULL,'done','2025-06-24 08:18:57'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-26','14:40:00','09124536392',NULL,'Completed','2025-06-24 08:18:57'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-26','14:40:00','09124536392',NULL,'done','2025-06-24 08:21:56'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-27','14:40:00','09124536392',NULL,'Completed','2025-06-25 13:21:26'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Check-up','talomo','2025-06-27','13:00:00','09124536392',NULL,'Completed','2025-06-25 13:21:36'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-27','14:40:00','09124536392',NULL,'Completed','2025-06-25 13:21:45'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-27','13:00:00','09124536392',NULL,'Completed','2025-06-26 16:05:18'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Check-up','talomo','2025-07-26','08:00:00','09124536392',NULL,'Completed','2025-06-26 16:06:20'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-28','08:00:00','09124536392',NULL,'Completed','2025-06-27 03:19:15'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Check-up','talomo','2025-06-27','14:40:00','09124536392',NULL,'Completed','2025-06-27 05:55:50'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-27','08:00:00','09124536392',NULL,'done','2025-06-29 07:01:08'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Relocation','talomo','2025-06-28','13:00:00','09124536392',NULL,'Completed','2025-06-29 07:12:50'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Relocation','talomo','2025-06-30','09:40:00','09124536392',NULL,'done','2025-06-29 07:12:50'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-27','13:00:00','09124536392',NULL,'done','2025-06-29 07:22:54'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-27','14:40:00','09124536392',NULL,'done','2025-06-29 07:22:54'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Check-up','talomo','2025-07-05','08:00:00','09124536392',NULL,'Completed','2025-06-29 09:31:51'),(1,26,'shane flores','shane@gmail.com','Aircon Cleaning','ecoland','2025-03-22','10:00:00','09124536392',NULL,'Cancelled','2025-06-05 22:19:03'),(2,18,'Honey Flores','mardy@gmail.com','Aircon Cleaning','ecoland','2025-05-23','16:00:00','+639124536392',NULL,'Cancelled','2025-06-05 22:19:03'),(3,4,'sassie','sassie@gmail.com','Aircon Check-up','tugbok','2025-05-29','16:00:00','+639124536392',NULL,'Cancelled','2025-06-05 22:19:03'),(4,18,'Honey Flores','mik@gmail.com','Aircon Cleaning','Bankerohan Davao city','2025-03-21','14:00:00','09124536392',NULL,'done','2025-06-05 22:49:53'),(5,18,'Christine Perez','hone@gmail.com','Aircon Check-up','Bankerohan Davao city','2025-03-28','11:00:00','09124536392',NULL,'done','2025-06-05 22:49:53'),(6,4,'Honey Flores','haki@gmail.com','Aircon Check-up','ecoland','2025-06-02','15:00:00','+639124536392',NULL,'done','2025-06-05 22:49:53'),(7,5,'Honeymardsflores','honeymardsflores@gmail.com','Aircon Check-up','tugbok','2025-05-29','17:00:00','+639124536392',NULL,'done','2025-06-05 22:49:53'),(8,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-05-31','12:00:00','+639124536311',NULL,'done','2025-06-05 22:49:53'),(11,3,'kenneth maylos','kenneth@gmail.com','Aircon Check-up','talomo','2025-05-30','11:00:00','+639124536398',NULL,'Completed','2025-06-06 00:02:39'),(12,2,'mardsflores','honeymardsflores022602@gmail.com','Aircon Check-up','Bankeruhan','2025-05-30','11:00:00','+639124536392',NULL,'Completed','2025-06-06 00:02:50'),(13,18,'Christine Perez','kim@gmail.com','Aircon Cleaning','agdao','2025-05-31','14:00:00','+639124536392',NULL,'done','2025-06-06 03:40:16'),(14,2,'mardsflores','honeymardsflores022602@gmail.com','Aircon Relocation','Bankeruhan','2025-05-31','13:00:00','+639124536392',NULL,'done','2025-06-06 03:40:16'),(16,18,'Lyka Villagonzalo','hone@gmail.com','Aircon Check-up','Bankerohan Davao city','2024-12-06','11:00:00','09124536392',NULL,'done','2025-06-06 04:10:11'),(17,1,'Honey Flores','mardy@gmail.com','Aircon Check-up','ecoland','2025-05-15','15:00:00','+639124536392',NULL,'done','2025-06-06 04:10:11'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Check-up','talomo','2025-07-03','13:00:00','09124536392',NULL,'Completed','2025-06-23 00:41:14'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Check-up','talomo','2025-07-14','08:00:00','09124536392',NULL,'Completed','2025-06-23 01:50:26'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Relocation','talomo','2025-06-26','14:40:00','09124536392',NULL,'Completed','2025-06-23 02:30:12'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-24','08:00:00','09124536392',NULL,'done','2025-06-23 12:16:48'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Check-up','talomo','2025-06-25','09:40:00','09124536392',NULL,'done','2025-06-23 12:16:48'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Check-up','talomo','2025-06-30','08:00:00','09124536392',NULL,'done','2025-06-23 12:16:48'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-25','08:00:00','09124536392',NULL,'done','2025-06-23 12:20:40'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-28','14:40:00','09124536392',NULL,'done','2025-06-23 12:20:40'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-25','14:40:00','09124536392',NULL,'done','2025-06-23 12:57:31'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-25','14:40:00','09124536392',NULL,'Completed','2025-06-23 12:58:17'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-07-25','13:00:00','09124536392',NULL,'Completed','2025-06-23 13:30:30'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Check-up','talomo','2025-06-26','13:00:00','09124536392',NULL,'done','2025-06-23 13:32:05'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-26','09:40:00','09124536392',NULL,'Completed','2025-06-23 15:22:52'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-26','08:00:00','09124536392',NULL,'Cancelled','2025-06-23 15:37:41'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-24','08:00:00','09124536392',NULL,'Completed','2025-06-23 15:38:38'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-24','08:00:00','09124536392',NULL,'Completed','2025-06-23 15:39:31'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-24','08:00:00','09124536392',NULL,'Completed','2025-06-23 16:21:32'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Check-up','talomo','2025-06-24','08:00:00','09124536392',NULL,'Cancelled','2025-06-23 16:22:37'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-25','08:00:00','09124536392',NULL,'Cancelled','2025-06-23 17:00:45'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Relocation','talomo','2025-07-01','08:00:00','09124536392',NULL,'Cancelled','2025-06-23 17:02:45'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Relocation','talomo','2025-06-28','13:00:00','09124536392',NULL,'Rejected','2025-06-23 17:14:51'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-07-04','08:00:00','09124536392',NULL,'Cancelled','2025-06-23 17:29:12'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Check-up','talomo','2025-06-28','08:00:00','09124536392',NULL,'Cancelled','2025-06-23 17:36:23'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Check-up','talomo','2025-06-29','09:40:00','09124536392',NULL,'Rejected','2025-06-23 17:38:03'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Check-up','talomo','2025-07-08','08:00:00','09124536392',NULL,'done','2025-06-23 17:40:16'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-25','08:00:00','09124536392',NULL,'done','2025-06-23 17:58:02'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Relocation','talomo','2025-06-30','13:00:00','09124536392',NULL,'Cancelled','2025-06-23 19:06:47'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-07-08','08:00:00','09124536392',NULL,'done','2025-06-23 19:08:01'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Check-up','talomo','2025-06-27','08:00:00','09090909090',NULL,'done','2025-06-24 02:46:17'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Check-up','talomo','2025-06-26','09:40:00','09090909090',NULL,'done','2025-06-24 02:58:16'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Repair','talomo','2025-06-25','14:40:00','09090909090',NULL,'Completed','2025-06-24 03:18:25'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Check-up','talomo','2025-06-26','08:00:00','09090909090',NULL,'Completed','2025-06-24 03:29:47'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Repair','talomo','2025-07-03','08:00:00','09090909090',NULL,'Rejected','2025-06-24 03:31:26'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Relocation','talomo','2025-06-26','14:40:00','09124536392',NULL,'Completed','2025-06-24 06:39:20'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Check-up','talomo','2025-06-27','13:00:00','09124536392',NULL,'done','2025-06-24 08:18:57'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-26','14:40:00','09124536392',NULL,'Completed','2025-06-24 08:18:57'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-26','14:40:00','09124536392',NULL,'done','2025-06-24 08:21:56'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-27','14:40:00','09124536392',NULL,'Completed','2025-06-25 13:21:26'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Check-up','talomo','2025-06-27','13:00:00','09124536392',NULL,'Completed','2025-06-25 13:21:36'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-27','14:40:00','09124536392',NULL,'Completed','2025-06-25 13:21:45'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-27','13:00:00','09124536392',NULL,'Completed','2025-06-26 16:05:18'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Check-up','talomo','2025-07-26','08:00:00','09124536392',NULL,'Completed','2025-06-26 16:06:20'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-28','08:00:00','09124536392',NULL,'Completed','2025-06-27 03:19:15'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Check-up','talomo','2025-06-27','14:40:00','09124536392',NULL,'Completed','2025-06-27 05:55:50'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-27','08:00:00','09124536392',NULL,'done','2025-06-29 07:01:08'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Relocation','talomo','2025-06-28','13:00:00','09124536392',NULL,'Completed','2025-06-29 07:12:50'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Relocation','talomo','2025-06-30','09:40:00','09124536392',NULL,'done','2025-06-29 07:12:50'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-27','13:00:00','09124536392',NULL,'done','2025-06-29 07:22:54'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Cleaning','talomo','2025-06-27','14:40:00','09124536392',NULL,'done','2025-06-29 07:22:54'),(0,3,'kenneth maylos','kenneth@gmail.com','Aircon Check-up','talomo','2025-07-05','08:00:00','09124536392',NULL,'Completed','2025-06-29 09:31:51');
/*!40000 ALTER TABLE `booking_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `booking_history_customer`
--

DROP TABLE IF EXISTS `booking_history_customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `booking_history_customer` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `service_type` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `booking_date` date DEFAULT NULL,
  `booking_time` time DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `technician_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_general_ci,
  `status` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `moved_at` datetime DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking_history_customer`
--

LOCK TABLES `booking_history_customer` WRITE;
/*!40000 ALTER TABLE `booking_history_customer` DISABLE KEYS */;
INSERT INTO `booking_history_customer` VALUES (1,3,'Aircon Check-up','2025-06-27','08:00:00',NULL,'N/A',NULL,'Cancelled',NULL,'2025-06-24 01:35:46',0.00),(2,3,'Aircon Relocation','2025-06-30','13:00:00',NULL,'N/A',NULL,'Cancelled',NULL,'2025-06-24 01:37:14',0.00),(3,3,'Aircon Check-up','2025-06-28','08:00:00',NULL,'N/A',NULL,'Cancelled',NULL,'2025-06-24 01:52:37',0.00),(4,3,'Aircon Check-up','2025-06-26','09:40:00',NULL,'N/A',NULL,'Cancelled',NULL,'2025-06-24 03:04:32',0.00),(5,3,'Aircon Check-up','2025-06-25','09:40:00',NULL,'N/A',NULL,'Cancelled',NULL,'2025-06-24 03:04:44',0.00),(6,3,'Aircon Relocation','2025-06-24','09:40:00',NULL,'N/A',NULL,'Cancelled',NULL,'2025-06-24 10:38:12',0.00),(7,3,'Aircon Repair','2025-06-24','14:40:00',NULL,'N/A',NULL,'done',NULL,'2025-06-24 10:41:25',0.00),(8,3,'Aircon Check-up','2025-06-30','08:00:00',NULL,'Ken Flores',NULL,'done',NULL,'2025-06-24 11:20:45',0.00),(9,3,'Aircon Check-up','2025-07-01','08:00:00',NULL,'Ken Flores',NULL,'done',NULL,'2025-06-24 11:24:15',0.00),(10,3,'Aircon Cleaning','2025-06-26','08:00:00',NULL,'N/A',NULL,'done',NULL,'2025-06-24 11:34:34',0.00),(11,3,'Aircon Cleaning','2025-07-31','16:20:00',NULL,'N/A',NULL,'Cancelled',NULL,'2025-06-24 14:01:11',0.00),(13,3,'Aircon Cleaning','2025-07-26','14:40:00',NULL,'N/A',NULL,'Cancelled',NULL,'2025-06-24 14:43:16',0.00),(15,3,'Aircon Check-up','2025-07-12','14:40:00',NULL,'N/A',NULL,'Cancelled',NULL,'2025-06-24 16:30:19',0.00),(16,3,'Aircon Relocation','2025-07-11','13:00:00',NULL,'N/A',NULL,'Cancelled',NULL,'2025-06-24 16:34:20',0.00),(17,3,'Aircon Cleaning','2025-07-04','13:00:00',NULL,'N/A',NULL,'Cancelled',NULL,'2025-06-24 16:34:23',0.00),(18,3,'Aircon Check-up','2025-07-03','14:40:00',NULL,'N/A',NULL,'Cancelled',NULL,'2025-06-24 16:34:26',0.00),(19,3,'Aircon Cleaning','2025-06-24','14:40:00',NULL,'N/A',NULL,'Cancelled',NULL,'2025-06-24 16:35:42',0.00),(20,3,'Aircon Cleaning','2025-06-25','09:40:00',NULL,'N/A',NULL,'Cancelled',NULL,'2025-06-24 16:35:50',0.00),(21,3,'Aircon Check-up','2025-06-26','08:00:00',NULL,'N/A',NULL,'Cancelled',NULL,'2025-06-25 13:09:37',0.00),(22,3,'Aircon Cleaning','2025-07-31','13:00:00',NULL,'N/A',NULL,'Cancelled',NULL,'2025-06-26 16:44:45',0.00),(23,3,'Aircon Cleaning','2025-05-29','13:00:00',NULL,'N/A',NULL,'Cancelled',NULL,'2025-06-27 10:59:27',0.00),(24,3,'Aircon Cleaning','2025-06-28','08:00:00',NULL,'N/A',NULL,'Cancelled',NULL,'2025-06-27 11:19:36',0.00),(25,3,'Aircon Cleaning','2025-07-01','13:00:00',NULL,'Ken Flores',NULL,'done',NULL,'2025-06-29 17:18:57',0.00),(26,3,'Aircon Cleaning','2025-07-01','14:40:00',NULL,'Ken Flores',NULL,'done',NULL,'2025-06-29 17:18:57',0.00),(27,3,'Aircon Cleaning','2025-07-23','14:40:00',NULL,'Ken Flores',NULL,'done',NULL,'2025-06-29 19:29:16',0.00),(28,3,'1 x Aircon Cleaning - Window Type, 1 x Aircon Cleaning - Window Type Inverter, 1 x Aircon Cleaning -','2025-07-02','09:40:00','09124536392','N/A',NULL,'Cancelled',NULL,'2025-07-01 06:28:36',0.00),(29,3,'1 x Aircon Check-up - Check-up Fee, 1 x Aircon Relocation - Relocation Service, 1 x Aircon Repair - ','2025-07-03','08:00:00','09124536392','N/A',NULL,'Cancelled',NULL,'2025-07-01 07:01:51',0.00),(30,3,'1 x Aircon Check-up - Check-up Fee, 1 x Aircon Relocation - Relocation Service, 1 x Aircon Repair - ','2025-07-02','13:00:00','09124536392','N/A',NULL,'Cancelled',NULL,'2025-07-01 07:01:56',0.00),(31,3,'1 x Aircon Relocation - Relocation Service, 1 x Aircon Repair - Capacitor Replacement, 1 x Aircon Re','2025-07-02','08:00:00','09124536392','N/A',NULL,'Cancelled',NULL,'2025-07-01 07:26:38',0.00),(32,3,'1 x Aircon Check-up - Check-up Fee, 1 x Aircon Relocation - Relocation Service, 1 x Aircon Repair - ','2025-07-02','08:00:00','09124536392','N/A',NULL,'Cancelled',NULL,'2025-07-01 07:26:40',0.00),(33,3,'Aircon Cleaning','2025-07-02','13:00:00','09124536392','N/A',NULL,'Cancelled',NULL,'2025-07-01 07:26:43',0.00),(34,3,'1 x Aircon Check-up - Check-up Fee, 1 x Aircon Relocation - Relocation Service, 1 x Aircon Repair - ','2025-07-03','13:00:00','09124536392','N/A',NULL,'Cancelled',NULL,'2025-07-01 08:41:09',0.00),(35,3,'1 x Aircon Cleaning - Window Type, 1 x Aircon Cleaning - Window Type Inverter, 1 x Aircon Cleaning -','2025-07-02','08:00:00','09124536392','N/A',NULL,'Cancelled',NULL,'2025-07-01 08:56:12',0.00),(36,3,'1 x Aircon Relocation - Relocation Service, 1 x Aircon Repair - Capacitor Replacement, 1 x Aircon Re','2025-07-04','13:00:00','09124536392','N/A',NULL,'Cancelled',NULL,'2025-07-01 08:56:16',0.00),(37,3,'1 x Aircon Check-up - Check-up Fee, 1 x Aircon Relocation - Relocation Service, 1 x Aircon Repair - ','2025-07-02','08:00:00','09124536392','N/A',NULL,'Cancelled',NULL,'2025-07-01 08:56:21',0.00),(38,3,'1 x Aircon Check-up - Check-up Fee, 1 x Aircon Relocation - Relocation Service, 1 x Aircon Repair - ','2025-07-02','13:00:00','09124536392','N/A',NULL,'Cancelled',NULL,'2025-07-01 08:56:48',0.00),(39,3,'1 x Aircon Check-up - Check-up Fee, 1 x Aircon Relocation - Relocation Service, 1 x Aircon Repair - ','2025-07-03','13:00:00',NULL,'N/A',NULL,'Cancelled',NULL,'2025-07-01 09:19:01',0.00),(40,3,'Aircon Cleaning','2025-07-02','08:00:00',NULL,'N/A',NULL,'Cancelled',NULL,'2025-07-01 09:20:20',0.00),(41,3,'Aircon Relocation','2025-07-02','14:40:00',NULL,'N/A',NULL,'Cancelled',NULL,'2025-07-01 09:25:11',0.00),(42,3,'Aircon Check-up','2025-07-02','14:40:00',NULL,'N/A',NULL,'Cancelled',NULL,'2025-07-01 09:28:36',0.00),(43,3,'Window type (inverter)','2025-07-11','13:00:00',NULL,'N/A',NULL,'Cancelled',NULL,'2025-07-02 00:43:39',0.00),(44,3,'Window type (inverter)','2025-07-04','09:40:00',NULL,'N/A',NULL,'Cancelled',NULL,'2025-07-02 00:43:42',0.00),(45,3,'Aircon Relocation','2025-07-03','13:00:00',NULL,'N/A',NULL,'Cancelled',NULL,'2025-07-02 00:43:45',0.00),(46,3,'Aircon cleaning (window type)','2025-07-03','14:40:00',NULL,'N/A',NULL,'Cancelled',NULL,'2025-07-02 00:43:47',0.00),(47,3,'Aircon Cleaning','2025-07-03','08:00:00',NULL,'N/A',NULL,'Cancelled',NULL,'2025-07-02 00:43:50',0.00),(48,3,'Floormounted','2025-07-02','13:00:00',NULL,'N/A',NULL,'Cancelled',NULL,'2025-07-02 00:43:53',0.00),(49,3,'Window type (U shape)','2025-07-02','13:00:00',NULL,'N/A',NULL,'Cancelled',NULL,'2025-07-02 00:43:56',0.00),(50,3,'Cassette','2025-07-02','08:00:00',NULL,'N/A',NULL,'Cancelled',NULL,'2025-07-02 00:46:12',0.00),(51,3,'Cassette','2025-07-02','08:00:00',NULL,'N/A',NULL,'Cancelled',NULL,'2025-07-02 00:46:14',0.00),(52,3,'Cassette','2025-07-02','13:00:00',NULL,'N/A',NULL,'Cancelled',NULL,'2025-07-02 00:48:51',0.00),(53,3,'Cassette','2025-07-02','13:00:00',NULL,'N/A',NULL,'Cancelled',NULL,'2025-07-02 00:56:26',0.00),(54,3,'Split type','2025-07-02','09:40:00','09124536392','N/A',NULL,'Cancelled',NULL,'2025-07-02 01:13:30',0.00),(55,3,'','0000-00-00','00:00:00','','N/A',NULL,'Cancelled',NULL,'2025-07-02 01:20:41',0.00),(56,3,'Capacitor Thermostat','2025-07-03','08:00:00','09124536392','N/A',NULL,'Cancelled',NULL,'2025-07-02 01:20:44',0.00),(57,3,'Aircon cleaning (window type)','2025-07-02','08:00:00','09124536392','N/A',NULL,'Cancelled',NULL,'2025-07-02 01:20:47',0.00),(58,3,'Floormounted','2025-07-02','08:00:00','09124536392','N/A',NULL,'Cancelled',NULL,'2025-07-02 01:21:50',3000.00),(59,3,'Capacitor Thermostat','2025-07-02','13:00:00','09124536392','N/A',NULL,'Cancelled',NULL,'2025-07-02 03:29:31',1200.00),(60,3,'Split type','2025-07-29','08:00:00','09124536392','Ken Flores',NULL,'done',NULL,'2025-07-02 17:02:35',0.00),(61,3,'Aircon cleaning (window type)','2025-07-02','14:40:00','09124536392','N/A',NULL,'Cancelled',NULL,'2025-07-02 17:10:05',0.00),(62,3,'Aircon cleaning (window type)','2025-07-31','08:00:00','09124536392','Ken Flores',NULL,'done',NULL,'2025-07-03 14:41:49',0.00);
/*!40000 ALTER TABLE `booking_history_customer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `booking_history_employees`
--

DROP TABLE IF EXISTS `booking_history_employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `booking_history_employees` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `employee_id` int DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `service` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone_number` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `appointment_date` date DEFAULT NULL,
  `appointment_time` time DEFAULT NULL,
  `status` enum('done','cancelled','completed','rejected') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `archived_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `employee_id` (`employee_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking_history_employees`
--

LOCK TABLES `booking_history_employees` WRITE;
/*!40000 ALTER TABLE `booking_history_employees` DISABLE KEYS */;
INSERT INTO `booking_history_employees` VALUES (1,229,3,1,'kenneth maylos','Aircon Cleaning','talomo','09124536392','2025-07-01','14:40:00','done','2025-06-29 15:30:26','2025-06-29 15:47:30'),(2,215,3,1,'kenneth maylos','Aircon Cleaning','talomo','09124536392','2025-07-01','13:00:00','done','2025-06-26 14:58:07','2025-06-29 15:47:30');
/*!40000 ALTER TABLE `booking_history_employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `booking_notes`
--

DROP TABLE IF EXISTS `booking_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `booking_notes` (
  `id` int NOT NULL,
  `booking_id` int NOT NULL,
  `employee_id` int NOT NULL,
  `note` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking_notes`
--

LOCK TABLES `booking_notes` WRITE;
/*!40000 ALTER TABLE `booking_notes` DISABLE KEYS */;
INSERT INTO `booking_notes` VALUES (0,231,1,'heee','2025-07-01 02:03:30'),(0,231,1,'heee','2025-07-01 02:03:30'),(0,0,1,'tsk','2025-07-02 06:58:20'),(0,8,1,'gcjgcghrkgjfk','2025-07-02 09:20:30');
/*!40000 ALTER TABLE `booking_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` varchar(5) COLLATE utf8mb4_general_ci NOT NULL,
  `service` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `employee_id` int DEFAULT NULL,
  `price` decimal(10,2) DEFAULT '0.00',
  `status` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'Pending',
  `note` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookings`
--

LOCK TABLES `bookings` WRITE;
/*!40000 ALTER TABLE `bookings` DISABLE KEYS */;
INSERT INTO `bookings` VALUES (1,'17','Aircon cleaning (window type)','2025-07-11','08:00:00','+639392478355','19, Doña Saturnina St., Toril Proper, Toril, Davao City',52,800.00,'Pending','This is a test','2025-07-11 12:00:43','2025-07-12 03:29:06'),(2,'17','Aircon Repair','2025-07-13','11:20:00','+639106339588','19, Doña Saturnina St., Bato, Toril, Davao City',NULL,1500.00,'Pending','Test number 2','2025-07-12 00:37:42','2025-07-12 00:37:42'),(3,'17','Split type','2025-07-13','08:00:00','+639392478355','19, Doña Saturnina St., Baracatan, Toril, Davao City',NULL,2800.00,'Pending','Test','2025-07-12 01:08:33','2025-07-12 01:08:33'),(4,'17','Aircon cleaning (window type)','2025-07-16','08:00:00','+639392478355','19, Doña Saturnina St., Kap. Tomas Monteverde, Sr., Agdao, Davao City',NULL,800.00,'Pending','Test\r\n','2025-07-12 01:09:57','2025-07-12 01:09:57'),(5,'17','Aircon cleaning (window type)','2025-07-22','08:00:00','+639392478355','19, Doña Saturnina St., Callawa, Buhangin, Davao City',NULL,800.00,'Pending','Test','2025-07-12 01:10:33','2025-07-12 01:10:33'),(6,'17','Window type (inverter)','2025-07-24','14:40:00','+639106339588','19, Doña Saturnina St., Mapula, Paquibato, Davao City',NULL,2500.00,'Pending','Testing','2025-07-12 01:12:02','2025-07-12 01:12:02'),(7,'17','Aircon Check-up','2025-07-25','11:20:00','+639193924783','19, Doña Saturnina St., Pampanga, Buhangin, Davao City',NULL,500.00,'Pending','Test','2025-07-12 01:12:23','2025-07-12 01:12:23'),(8,'17','Window type (inverter)','2025-07-23','11:20:00','+639123456123','19, Doña Saturnina St., Malagos, Baguio, Davao City',NULL,2500.00,'Pending','This is a test','2025-07-12 03:39:34','2025-07-12 03:39:34'),(9,'17','Aircon cleaning (window type)','2025-07-23','11:20:00','+639123123123','19, Doña Saturnina St., Panacan, Bunawan, Davao City',NULL,800.00,'Pending','Testing 123','2025-07-12 03:41:31','2025-07-12 03:41:31'),(10,'17','Aircon cleaning (window type)','2025-07-23','11:20:00','+639123123123','19, Doña Saturnina St., Mahayag, Bunawan, Davao City',NULL,800.00,'Pending','Tesasdasda','2025-07-12 03:44:56','2025-07-12 03:44:56'),(11,'17','Window type (inverter)','2025-07-31','16:20:00','+639123131232','19, Doña Saturnina St., Dalagdag, Calinan, Davao City',NULL,2500.00,'Pending','Test','2025-07-12 03:47:07','2025-07-12 03:47:07'),(12,'17','Aircon Repair','2025-08-02','13:00:00','+639392478355','19, Doña Saturnina St., Gov. Paciano Bangoy, Agdao, Davao City',NULL,1500.00,'Pending','Tasdasdas','2025-07-12 03:48:34','2025-07-12 03:48:34');
/*!40000 ALTER TABLE `bookings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cancel_booking`
--

DROP TABLE IF EXISTS `cancel_booking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cancel_booking` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `service_type` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `booking_date` date DEFAULT NULL,
  `booking_time` time DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `technician_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_general_ci,
  `notes` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cancel_booking`
--

LOCK TABLES `cancel_booking` WRITE;
/*!40000 ALTER TABLE `cancel_booking` DISABLE KEYS */;
/*!40000 ALTER TABLE `cancel_booking` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee`
--

DROP TABLE IF EXISTS `employee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee` (
  `id` int NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee`
--

LOCK TABLES `employee` WRITE;
/*!40000 ALTER TABLE `employee` DISABLE KEYS */;
INSERT INTO `employee` VALUES (1,'Ken Flores','123'),(2,'Reymar Flores','123'),(3,'Janno Suaybagauio','123'),(9,'Reymark Romero','123'),(90,'Mac Nava','123'),(1,'Ken Flores','123'),(2,'Reymar Flores','123'),(3,'Janno Suaybagauio','123'),(9,'Reymark Romero','123'),(90,'Mac Nava','123');
/*!40000 ALTER TABLE `employee` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `position` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `hire_date` date DEFAULT NULL,
  `status` enum('Active','Inactive') COLLATE utf8mb4_general_ci DEFAULT 'Active',
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (1,'Ken Flores','Technician','mardy@gmail.com','2024-12-05','Active','12344',NULL),(2,'Reymar Flores','Technician','reymar@gmail.com','2024-12-05','Active','1222',NULL),(9,'Mac Nava','Technician','nava@gmail.com','2024-12-06','Active','1111',NULL),(10,'Reymark Romero',' Technician','reymark@gmail.com','2024-12-05','Active','',NULL),(14,'Janno Suaybaguio','Technician','janno@gmail.com','0000-00-00','Active','',NULL),(20,'Anthony Beseril','Technician','anthony@gmail.com','0000-00-00','Active','$2y$10$mF/RC4fVXhOO6BkVBWI5iuRR5BmT2TDcm49uV9myuPczI94rbe7.S',NULL),(21,'Nino Flores','Technician','nino@gmail.com','2025-06-14','Active','$2y$10$/iSNEg52JNz8uGDsjK9vG.JbP/b5xEunRZ8MX5eLsuI4o4sSNvm1e',NULL),(22,'Reymond Fernandez','Technician','reymond@gmail.com','2022-07-23','Active','$2y$10$tJpKXH5PaFmRpUOM3kctuu/09qCf.9t89.CQcC4gguleYUSK6aD4G',NULL),(51,'Dexter Dela Cruz','IT Technician','dexter@gmail.com','2025-07-07','Active','$2y$10$Pqdrsio1Rzf7bLtPemN.5.dcfcdt8zZqeDR7Nm3l9r2tZk.eiGey.',NULL),(52,'Alvin Jaiku','Technician','jakeopisyal@gmail.com','2025-07-09','Active','$2y$10$s0a3z3diSHmyHQS/ssJl8eLbhxpDyuWTQkprKSmY6hclaCPsAhDDG',NULL),(53,'Test Employee','Technician','test@gmail.com','2025-07-09','Active','$2y$10$/MUiohEXR1i8GnAd.GPcwemiGzj0UdFK3gdm9fw25d4vJKZYPKjNy',NULL),(54,'Test Employee','Technician','test2@gmail.com','2025-07-09','Active','$2y$10$P7O7jf8CT3ZciHYhszf8uOZ4t.voBGmGbCeuwgoCUhOyZMEFAChxW',NULL);
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sender_id` int NOT NULL,
  `receiver_id` int NOT NULL,
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `timestamp` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` VALUES (6,4,2,'cancel booking','2025-05-25 20:27:47'),(7,4,2,'11','2025-05-25 20:32:21'),(8,4,2,'11 pm nlng','2025-05-25 21:18:13'),(9,5,2,'hello boss','2025-05-25 21:36:36');
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reschedule_requests`
--

DROP TABLE IF EXISTS `reschedule_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reschedule_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `requested_date` date NOT NULL,
  `requested_time` time NOT NULL,
  `requested_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `booking_id` (`booking_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reschedule_requests`
--

LOCK TABLES `reschedule_requests` WRITE;
/*!40000 ALTER TABLE `reschedule_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `reschedule_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_notes`
--

DROP TABLE IF EXISTS `service_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_notes` (
  `id` int NOT NULL,
  `booking_id` int NOT NULL,
  `employee_id` int NOT NULL,
  `service` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `note` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','reviewed') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  PRIMARY KEY (`id`),
  KEY `booking_id` (`booking_id`),
  KEY `employee_id` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_notes`
--

LOCK TABLES `service_notes` WRITE;
/*!40000 ALTER TABLE `service_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fname` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `lname` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(25) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `contact` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `city` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `district` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `barangay` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `zipcode` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `reset_token` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reset_expire` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'admin','admin','admin','admin@gmail.com','admin','','','','','','2025-07-07 02:36:43','','2025-07-07 04:36:14'),(13,'Virdel','Lubaton','vdelccna','vdelccna@gmail.com','$2y$10$B25MsOa8b9lIUxpiDkwA1.8GstgJCjsT3WjOKeDOj5r','+639753634122','Davao City','Poblacion','37-D','8000','2025-07-07 03:55:12','','0000-00-00 00:00:00'),(14,'Girlie','Dela Pinas','girlie','girlie@gmail.com','$2y$10$GFmQmJGXSHkhSBUQq7ECWOjREkG5qn6zls97lcjziId','+6397324324234','Davao City','Buhangin','Acacia','8000','2025-07-07 04:02:15','','0000-00-00 00:00:00'),(17,'ken','tumbling','superiorson','kentumbling19@gmail.com','$2y$10$ZAsPWwnHvBqV5qzcXuYesehE2I8K.ZC7i3bpjV7y3TGUEHU8cm.R.','+639392478355','Davao City','Toril','Toril Proper','8000','2025-07-10 00:32:35',NULL,NULL);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `address` text COLLATE utf8mb4_general_ci,
  `profile_picture` varchar(255) COLLATE utf8mb4_general_ci DEFAULT 'default_profile.jpg',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Alice Johnson','alice@example.com','password123','555-1234','123 Main St','default_profile.jpg','2025-05-14 07:03:41'),(2,'Bob Brown','bob@example.com','password456','555-5678','456 Oak Ave','default_profile.jpg','2025-05-14 07:03:41'),(3,'John Doe','john@example.com','hashedpassword','09123456789','Davao City','default_profile.jpg','2025-05-14 07:18:26');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'airgo'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-07-12 15:18:50
