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
  `price` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `moved_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking_history`
--

LOCK TABLES `booking_history` WRITE;
/*!40000 ALTER TABLE `booking_history` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking_history_customer`
--

LOCK TABLES `booking_history_customer` WRITE;
/*!40000 ALTER TABLE `booking_history_customer` DISABLE KEYS */;
INSERT INTO `booking_history_customer` VALUES (1,17,'Window type (inverter)','2025-07-16','11:20:00','+639392478355','N/A',NULL,'Cancelled',NULL,'2025-07-14 09:42:03',0.00),(2,17,'Aircon cleaning (window type)','2025-07-23','11:20:00','+639123123123','Alvin Jaiku',NULL,'Cancelled',NULL,'2025-07-20 05:00:14',0.00),(3,17,'Window type (inverter)','2025-07-24','14:40:00','+639106339588','N/A',NULL,'Cancelled',NULL,'2025-07-20 05:07:08',0.00),(4,17,'Aircon Repair','2025-08-02','13:00:00','+639392478355','N/A',NULL,'Cancelled',NULL,'2025-07-20 05:09:43',0.00),(5,17,'Aircon Check-up','2025-07-25','11:20:00','+639193924783','N/A',NULL,'Cancelled',NULL,'2025-07-20 05:10:40',0.00);
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking_history_employees`
--

LOCK TABLES `booking_history_employees` WRITE;
/*!40000 ALTER TABLE `booking_history_employees` DISABLE KEYS */;
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
  `payment_proof` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'Pending',
  `note` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookings`
--

LOCK TABLES `bookings` WRITE;
/*!40000 ALTER TABLE `bookings` DISABLE KEYS */;
INSERT INTO `bookings` VALUES (8,'17','Window type (inverter)','2025-07-23','11:20:00','+639123456123','19, Doña Saturnina St., Malagos, Baguio, Davao City',52,2500.00,'uploads/payment_proofs/payment_6876fe7facf07.jpg','Completed','This is a test','2025-07-12 03:39:34','2025-07-16 01:21:03'),(10,'17','Aircon cleaning (window type)','2025-07-23','11:20:00','+639123123123','19, Doña Saturnina St., Mahayag, Bunawan, Davao City',52,800.00,NULL,'Pending','Tesasdasda','2025-07-12 03:44:56','2025-07-16 04:11:56'),(11,'17','Window type (inverter)','2025-07-31','16:20:00','+639123131232','19, Doña Saturnina St., Dalagdag, Calinan, Davao City',NULL,2500.00,NULL,'Pending','Test','2025-07-12 03:47:07','2025-07-12 03:47:07'),(13,'17','Cassette','2025-08-14','11:20:00','+639392478355','19, Doña Saturnina St., Toril Proper, Toril, Davao City',NULL,3200.00,NULL,'Cancelled','Test','2025-07-12 09:40:49','2025-07-20 05:12:01'),(14,'17','Aircon Repair','2025-09-01','08:00:00','+639392478355','19, Doña Saturnina St., Lumiad, Paquibato, Davao City',NULL,1500.00,NULL,'Pending','This is a test','2025-07-12 09:44:56','2025-07-12 10:42:50'),(15,'17','Window type (inverter)','2025-08-23','13:00:00','+639392478355','19, Doña Saturnina St., Bangkas Heights, Toril, Davao City',NULL,2500.00,NULL,'Pending','Test\r\n','2025-07-12 09:47:57','2025-07-12 09:47:57'),(16,'17','Aircon Repair','2025-07-26','14:40:00','+639392478355','19, Doña Saturnina St., Bago Gallera, Talomo, Davao City',52,1500.00,NULL,'Pending','Test','2025-07-12 09:49:17','2025-07-20 05:51:38'),(17,'17','Aircon Relocation','2025-07-28','13:00:00','+639392478355','19, Doña Saturnina St., Toril Proper, Toril, Davao City',52,3500.00,NULL,'Pending','Test','2025-07-12 09:50:32','2025-07-20 05:58:26'),(18,'17','Window type (U shape)','2025-09-04','11:20:00','+639654321646','19 Doña Saturnina St., Brgy. Toril Proper, Toril District, Davao City',NULL,2300.00,NULL,'Pending','This is a test','2025-07-13 01:56:05','2025-07-20 05:22:39'),(20,'17','Aircon cleaning (window type)','2025-07-31','13:00:00','+639392478355','19 Doña Saturnina St., Brgy. Toril Proper, Toril District, Davao City',NULL,800.00,NULL,'Pending','Test','2025-07-17 01:34:49','2025-07-17 01:34:49'),(21,'17','Aircon Relocation','2025-07-31','16:20:00','+639392478355','19 Doña Saturnina St., Brgy. Toril Proper, Toril District, Davao City',NULL,3500.00,NULL,'Pending','Test','2025-07-17 02:57:56','2025-07-17 02:57:56');
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
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (1,'Ken Flores','Technician','mardy@gmail.com','2024-12-05','Active','12344',NULL),(2,'Reymar Flores','Technician','reymar@gmail.com','2024-12-05','Active','1222',NULL),(9,'Mac Nava','Technician','nava@gmail.com','2024-12-06','Active','1111',NULL),(10,'Reymark Romero',' Technician','reymark@gmail.com','2024-12-05','Active','',NULL),(14,'Janno Suaybaguio','Technician','janno@gmail.com','0000-00-00','Active','',NULL),(20,'Anthony Beseril','Technician','anthony@gmail.com','0000-00-00','Active','$2y$10$mF/RC4fVXhOO6BkVBWI5iuRR5BmT2TDcm49uV9myuPczI94rbe7.S',NULL),(21,'Nino Flores','Technician','nino@gmail.com','2025-06-14','Active','$2y$10$/iSNEg52JNz8uGDsjK9vG.JbP/b5xEunRZ8MX5eLsuI4o4sSNvm1e',NULL),(22,'Reymond Fernandez','Technician','reymond@gmail.com','2022-07-23','Active','$2y$10$tJpKXH5PaFmRpUOM3kctuu/09qCf.9t89.CQcC4gguleYUSK6aD4G',NULL),(51,'Dexter Dela Cruz','IT Technician','dexter@gmail.com','2025-07-07','Active','$2y$10$Pqdrsio1Rzf7bLtPemN.5.dcfcdt8zZqeDR7Nm3l9r2tZk.eiGey.',NULL),(52,'Alvin Jaiku','Technician','jakeopisyal@gmail.com','2025-07-09','Active','$2y$10$s0a3z3diSHmyHQS/ssJl8eLbhxpDyuWTQkprKSmY6hclaCPsAhDDG',NULL),(53,'Test Employee','Technician','test@gmail.com','2025-07-09','Active','$2y$10$/MUiohEXR1i8GnAd.GPcwemiGzj0UdFK3gdm9fw25d4vJKZYPKjNy',NULL),(54,'Test2 Employee2','Technician','test2@gmail.com','2025-07-09','Active','$2y$10$P7O7jf8CT3ZciHYhszf8uOZ4t.voBGmGbCeuwgoCUhOyZMEFAChxW',NULL),(55,'Test3','Technician','test3@gmail.com','2025-07-09','Active','$2y$10$FYY/FO54nVX8gJX4NU3OcO9mtuEWdj4FJPH6P2N/V5TKnUp4g5sSC',NULL);
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
  `otp_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_verified` tinyint DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `reset_token` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reset_expire` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'admin','admin','admin','admin@gmail.com','admin','','','','','',NULL,NULL,'2025-07-16 05:22:27',NULL,NULL),(13,'Virdel','Lubaton','vdelccna','vdelccna@gmail.com','$2y$10$B25MsOa8b9lIUxpiDkwA1.8GstgJCjsT3WjOKeDOj5r','+639753634122','Davao City','Poblacion','37-D','8000',NULL,NULL,'2025-07-16 05:22:27',NULL,'0000-00-00 00:00:00'),(14,'Girlie','Dela Pinas','girlie','girlie@gmail.com','$2y$10$GFmQmJGXSHkhSBUQq7ECWOjREkG5qn6zls97lcjziId','+6397324324234','Davao City','Buhangin','Acacia','8000',NULL,NULL,'2025-07-16 05:22:27',NULL,'0000-00-00 00:00:00'),(17,'ken','tumbling','superiorson','kentumbling19@gmail.com','$2y$10$ZAsPWwnHvBqV5qzcXuYesehE2I8K.ZC7i3bpjV7y3TGUEHU8cm.R.','+639392478355','Davao City','Toril','Toril Proper','8000',NULL,1,'2025-07-16 07:45:36',NULL,NULL),(22,'Jake','Opisyal','jaiku','jakeopisyal@gmail.com','$2y$10$n03FKi.2i/yFvVpEUh/qE.2iXV.5DIJnX9fZgYJCQzGZoqO9hOj2y','+639123123213','Davao City','Toril','Toril Proper','8000',NULL,1,'2025-07-17 00:58:05',NULL,NULL);
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

-- Dump completed on 2025-07-20 14:02:13
