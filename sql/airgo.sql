-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 07, 2025 at 08:35 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `airgo`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `name`, `password`) VALUES
(1, 'admin', 'Matt Ranillo Flores', '1234');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` varchar(5) NOT NULL,
  `service` varchar(100) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `phone` varchar(20) NOT NULL,
  `location` varchar(255) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `status` varchar(50) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `service`, `appointment_date`, `appointment_time`, `phone`, `location`, `employee_id`, `price`, `status`, `created_at`, `updated_at`) VALUES
(10, '3', 'Aircon Check-up', '2025-07-04', '13:00:00', '09124536392', 'talomo', NULL, 500.00, 'Pending', '2025-07-03 06:41:49', '2025-07-03 06:41:49');

-- --------------------------------------------------------

--
-- Table structure for table `booking_history`
--

CREATE TABLE `booking_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `service` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `technician` varchar(255) DEFAULT NULL,
  `status` varchar(50) NOT NULL,
  `moved_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_history`
--

INSERT INTO `booking_history` (`id`, `user_id`, `name`, `email`, `service`, `location`, `appointment_date`, `appointment_time`, `phone_number`, `technician`, `status`, `moved_at`) VALUES
(1, 26, 'shane flores', 'shane@gmail.com', 'Aircon Cleaning', 'ecoland', '2025-03-22', '10:00:00', '09124536392', NULL, 'Cancelled', '2025-06-05 22:19:03'),
(2, 18, 'Honey Flores', 'mardy@gmail.com', 'Aircon Cleaning', 'ecoland', '2025-05-23', '16:00:00', '+639124536392', NULL, 'Cancelled', '2025-06-05 22:19:03'),
(3, 4, 'sassie', 'sassie@gmail.com', 'Aircon Check-up', 'tugbok', '2025-05-29', '16:00:00', '+639124536392', NULL, 'Cancelled', '2025-06-05 22:19:03'),
(4, 18, 'Honey Flores', 'mik@gmail.com', 'Aircon Cleaning', 'Bankerohan Davao city', '2025-03-21', '14:00:00', '09124536392', NULL, 'done', '2025-06-05 22:49:53'),
(5, 18, 'Christine Perez', 'hone@gmail.com', 'Aircon Check-up', 'Bankerohan Davao city', '2025-03-28', '11:00:00', '09124536392', NULL, 'done', '2025-06-05 22:49:53'),
(6, 4, 'Honey Flores', 'haki@gmail.com', 'Aircon Check-up', 'ecoland', '2025-06-02', '15:00:00', '+639124536392', NULL, 'done', '2025-06-05 22:49:53'),
(7, 5, 'Honeymardsflores', 'honeymardsflores@gmail.com', 'Aircon Check-up', 'tugbok', '2025-05-29', '17:00:00', '+639124536392', NULL, 'done', '2025-06-05 22:49:53'),
(8, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-05-31', '12:00:00', '+639124536311', NULL, 'done', '2025-06-05 22:49:53'),
(11, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Check-up', 'talomo', '2025-05-30', '11:00:00', '+639124536398', NULL, 'Completed', '2025-06-06 00:02:39'),
(12, 2, 'mardsflores', 'honeymardsflores022602@gmail.com', 'Aircon Check-up', 'Bankeruhan', '2025-05-30', '11:00:00', '+639124536392', NULL, 'Completed', '2025-06-06 00:02:50'),
(13, 18, 'Christine Perez', 'kim@gmail.com', 'Aircon Cleaning', 'agdao', '2025-05-31', '14:00:00', '+639124536392', NULL, 'done', '2025-06-06 03:40:16'),
(14, 2, 'mardsflores', 'honeymardsflores022602@gmail.com', 'Aircon Relocation', 'Bankeruhan', '2025-05-31', '13:00:00', '+639124536392', NULL, 'done', '2025-06-06 03:40:16'),
(16, 18, 'Lyka Villagonzalo', 'hone@gmail.com', 'Aircon Check-up', 'Bankerohan Davao city', '2024-12-06', '11:00:00', '09124536392', NULL, 'done', '2025-06-06 04:10:11'),
(17, 1, 'Honey Flores', 'mardy@gmail.com', 'Aircon Check-up', 'ecoland', '2025-05-15', '15:00:00', '+639124536392', NULL, 'done', '2025-06-06 04:10:11'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Check-up', 'talomo', '2025-07-03', '13:00:00', '09124536392', NULL, 'Completed', '2025-06-23 00:41:14'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Check-up', 'talomo', '2025-07-14', '08:00:00', '09124536392', NULL, 'Completed', '2025-06-23 01:50:26'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Relocation', 'talomo', '2025-06-26', '14:40:00', '09124536392', NULL, 'Completed', '2025-06-23 02:30:12'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-24', '08:00:00', '09124536392', NULL, 'done', '2025-06-23 12:16:48'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Check-up', 'talomo', '2025-06-25', '09:40:00', '09124536392', NULL, 'done', '2025-06-23 12:16:48'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Check-up', 'talomo', '2025-06-30', '08:00:00', '09124536392', NULL, 'done', '2025-06-23 12:16:48'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-25', '08:00:00', '09124536392', NULL, 'done', '2025-06-23 12:20:40'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-28', '14:40:00', '09124536392', NULL, 'done', '2025-06-23 12:20:40'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-25', '14:40:00', '09124536392', NULL, 'done', '2025-06-23 12:57:31'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-25', '14:40:00', '09124536392', NULL, 'Completed', '2025-06-23 12:58:17'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-07-25', '13:00:00', '09124536392', NULL, 'Completed', '2025-06-23 13:30:30'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Check-up', 'talomo', '2025-06-26', '13:00:00', '09124536392', NULL, 'done', '2025-06-23 13:32:05'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-26', '09:40:00', '09124536392', NULL, 'Completed', '2025-06-23 15:22:52'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-26', '08:00:00', '09124536392', NULL, 'Cancelled', '2025-06-23 15:37:41'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-24', '08:00:00', '09124536392', NULL, 'Completed', '2025-06-23 15:38:38'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-24', '08:00:00', '09124536392', NULL, 'Completed', '2025-06-23 15:39:31'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-24', '08:00:00', '09124536392', NULL, 'Completed', '2025-06-23 16:21:32'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Check-up', 'talomo', '2025-06-24', '08:00:00', '09124536392', NULL, 'Cancelled', '2025-06-23 16:22:37'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-25', '08:00:00', '09124536392', NULL, 'Cancelled', '2025-06-23 17:00:45'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Relocation', 'talomo', '2025-07-01', '08:00:00', '09124536392', NULL, 'Cancelled', '2025-06-23 17:02:45'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Relocation', 'talomo', '2025-06-28', '13:00:00', '09124536392', NULL, 'Rejected', '2025-06-23 17:14:51'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-07-04', '08:00:00', '09124536392', NULL, 'Cancelled', '2025-06-23 17:29:12'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Check-up', 'talomo', '2025-06-28', '08:00:00', '09124536392', NULL, 'Cancelled', '2025-06-23 17:36:23'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Check-up', 'talomo', '2025-06-29', '09:40:00', '09124536392', NULL, 'Rejected', '2025-06-23 17:38:03'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Check-up', 'talomo', '2025-07-08', '08:00:00', '09124536392', NULL, 'done', '2025-06-23 17:40:16'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-25', '08:00:00', '09124536392', NULL, 'done', '2025-06-23 17:58:02'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Relocation', 'talomo', '2025-06-30', '13:00:00', '09124536392', NULL, 'Cancelled', '2025-06-23 19:06:47'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-07-08', '08:00:00', '09124536392', NULL, 'done', '2025-06-23 19:08:01'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Check-up', 'talomo', '2025-06-27', '08:00:00', '09090909090', NULL, 'done', '2025-06-24 02:46:17'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Check-up', 'talomo', '2025-06-26', '09:40:00', '09090909090', NULL, 'done', '2025-06-24 02:58:16'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Repair', 'talomo', '2025-06-25', '14:40:00', '09090909090', NULL, 'Completed', '2025-06-24 03:18:25'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Check-up', 'talomo', '2025-06-26', '08:00:00', '09090909090', NULL, 'Completed', '2025-06-24 03:29:47'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Repair', 'talomo', '2025-07-03', '08:00:00', '09090909090', NULL, 'Rejected', '2025-06-24 03:31:26'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Relocation', 'talomo', '2025-06-26', '14:40:00', '09124536392', NULL, 'Completed', '2025-06-24 06:39:20'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Check-up', 'talomo', '2025-06-27', '13:00:00', '09124536392', NULL, 'done', '2025-06-24 08:18:57'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-26', '14:40:00', '09124536392', NULL, 'Completed', '2025-06-24 08:18:57'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-26', '14:40:00', '09124536392', NULL, 'done', '2025-06-24 08:21:56'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-27', '14:40:00', '09124536392', NULL, 'Completed', '2025-06-25 13:21:26'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Check-up', 'talomo', '2025-06-27', '13:00:00', '09124536392', NULL, 'Completed', '2025-06-25 13:21:36'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-27', '14:40:00', '09124536392', NULL, 'Completed', '2025-06-25 13:21:45'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-27', '13:00:00', '09124536392', NULL, 'Completed', '2025-06-26 16:05:18'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Check-up', 'talomo', '2025-07-26', '08:00:00', '09124536392', NULL, 'Completed', '2025-06-26 16:06:20'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-28', '08:00:00', '09124536392', NULL, 'Completed', '2025-06-27 03:19:15'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Check-up', 'talomo', '2025-06-27', '14:40:00', '09124536392', NULL, 'Completed', '2025-06-27 05:55:50'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-27', '08:00:00', '09124536392', NULL, 'done', '2025-06-29 07:01:08'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Relocation', 'talomo', '2025-06-28', '13:00:00', '09124536392', NULL, 'Completed', '2025-06-29 07:12:50'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Relocation', 'talomo', '2025-06-30', '09:40:00', '09124536392', NULL, 'done', '2025-06-29 07:12:50'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-27', '13:00:00', '09124536392', NULL, 'done', '2025-06-29 07:22:54'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-27', '14:40:00', '09124536392', NULL, 'done', '2025-06-29 07:22:54'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Check-up', 'talomo', '2025-07-05', '08:00:00', '09124536392', NULL, 'Completed', '2025-06-29 09:31:51'),
(1, 26, 'shane flores', 'shane@gmail.com', 'Aircon Cleaning', 'ecoland', '2025-03-22', '10:00:00', '09124536392', NULL, 'Cancelled', '2025-06-05 22:19:03'),
(2, 18, 'Honey Flores', 'mardy@gmail.com', 'Aircon Cleaning', 'ecoland', '2025-05-23', '16:00:00', '+639124536392', NULL, 'Cancelled', '2025-06-05 22:19:03'),
(3, 4, 'sassie', 'sassie@gmail.com', 'Aircon Check-up', 'tugbok', '2025-05-29', '16:00:00', '+639124536392', NULL, 'Cancelled', '2025-06-05 22:19:03'),
(4, 18, 'Honey Flores', 'mik@gmail.com', 'Aircon Cleaning', 'Bankerohan Davao city', '2025-03-21', '14:00:00', '09124536392', NULL, 'done', '2025-06-05 22:49:53'),
(5, 18, 'Christine Perez', 'hone@gmail.com', 'Aircon Check-up', 'Bankerohan Davao city', '2025-03-28', '11:00:00', '09124536392', NULL, 'done', '2025-06-05 22:49:53'),
(6, 4, 'Honey Flores', 'haki@gmail.com', 'Aircon Check-up', 'ecoland', '2025-06-02', '15:00:00', '+639124536392', NULL, 'done', '2025-06-05 22:49:53'),
(7, 5, 'Honeymardsflores', 'honeymardsflores@gmail.com', 'Aircon Check-up', 'tugbok', '2025-05-29', '17:00:00', '+639124536392', NULL, 'done', '2025-06-05 22:49:53'),
(8, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-05-31', '12:00:00', '+639124536311', NULL, 'done', '2025-06-05 22:49:53'),
(11, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Check-up', 'talomo', '2025-05-30', '11:00:00', '+639124536398', NULL, 'Completed', '2025-06-06 00:02:39'),
(12, 2, 'mardsflores', 'honeymardsflores022602@gmail.com', 'Aircon Check-up', 'Bankeruhan', '2025-05-30', '11:00:00', '+639124536392', NULL, 'Completed', '2025-06-06 00:02:50'),
(13, 18, 'Christine Perez', 'kim@gmail.com', 'Aircon Cleaning', 'agdao', '2025-05-31', '14:00:00', '+639124536392', NULL, 'done', '2025-06-06 03:40:16'),
(14, 2, 'mardsflores', 'honeymardsflores022602@gmail.com', 'Aircon Relocation', 'Bankeruhan', '2025-05-31', '13:00:00', '+639124536392', NULL, 'done', '2025-06-06 03:40:16'),
(16, 18, 'Lyka Villagonzalo', 'hone@gmail.com', 'Aircon Check-up', 'Bankerohan Davao city', '2024-12-06', '11:00:00', '09124536392', NULL, 'done', '2025-06-06 04:10:11'),
(17, 1, 'Honey Flores', 'mardy@gmail.com', 'Aircon Check-up', 'ecoland', '2025-05-15', '15:00:00', '+639124536392', NULL, 'done', '2025-06-06 04:10:11'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Check-up', 'talomo', '2025-07-03', '13:00:00', '09124536392', NULL, 'Completed', '2025-06-23 00:41:14'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Check-up', 'talomo', '2025-07-14', '08:00:00', '09124536392', NULL, 'Completed', '2025-06-23 01:50:26'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Relocation', 'talomo', '2025-06-26', '14:40:00', '09124536392', NULL, 'Completed', '2025-06-23 02:30:12'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-24', '08:00:00', '09124536392', NULL, 'done', '2025-06-23 12:16:48'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Check-up', 'talomo', '2025-06-25', '09:40:00', '09124536392', NULL, 'done', '2025-06-23 12:16:48'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Check-up', 'talomo', '2025-06-30', '08:00:00', '09124536392', NULL, 'done', '2025-06-23 12:16:48'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-25', '08:00:00', '09124536392', NULL, 'done', '2025-06-23 12:20:40'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-28', '14:40:00', '09124536392', NULL, 'done', '2025-06-23 12:20:40'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-25', '14:40:00', '09124536392', NULL, 'done', '2025-06-23 12:57:31'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-25', '14:40:00', '09124536392', NULL, 'Completed', '2025-06-23 12:58:17'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-07-25', '13:00:00', '09124536392', NULL, 'Completed', '2025-06-23 13:30:30'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Check-up', 'talomo', '2025-06-26', '13:00:00', '09124536392', NULL, 'done', '2025-06-23 13:32:05'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-26', '09:40:00', '09124536392', NULL, 'Completed', '2025-06-23 15:22:52'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-26', '08:00:00', '09124536392', NULL, 'Cancelled', '2025-06-23 15:37:41'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-24', '08:00:00', '09124536392', NULL, 'Completed', '2025-06-23 15:38:38'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-24', '08:00:00', '09124536392', NULL, 'Completed', '2025-06-23 15:39:31'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-24', '08:00:00', '09124536392', NULL, 'Completed', '2025-06-23 16:21:32'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Check-up', 'talomo', '2025-06-24', '08:00:00', '09124536392', NULL, 'Cancelled', '2025-06-23 16:22:37'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-25', '08:00:00', '09124536392', NULL, 'Cancelled', '2025-06-23 17:00:45'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Relocation', 'talomo', '2025-07-01', '08:00:00', '09124536392', NULL, 'Cancelled', '2025-06-23 17:02:45'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Relocation', 'talomo', '2025-06-28', '13:00:00', '09124536392', NULL, 'Rejected', '2025-06-23 17:14:51'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-07-04', '08:00:00', '09124536392', NULL, 'Cancelled', '2025-06-23 17:29:12'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Check-up', 'talomo', '2025-06-28', '08:00:00', '09124536392', NULL, 'Cancelled', '2025-06-23 17:36:23'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Check-up', 'talomo', '2025-06-29', '09:40:00', '09124536392', NULL, 'Rejected', '2025-06-23 17:38:03'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Check-up', 'talomo', '2025-07-08', '08:00:00', '09124536392', NULL, 'done', '2025-06-23 17:40:16'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-25', '08:00:00', '09124536392', NULL, 'done', '2025-06-23 17:58:02'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Relocation', 'talomo', '2025-06-30', '13:00:00', '09124536392', NULL, 'Cancelled', '2025-06-23 19:06:47'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-07-08', '08:00:00', '09124536392', NULL, 'done', '2025-06-23 19:08:01'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Check-up', 'talomo', '2025-06-27', '08:00:00', '09090909090', NULL, 'done', '2025-06-24 02:46:17'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Check-up', 'talomo', '2025-06-26', '09:40:00', '09090909090', NULL, 'done', '2025-06-24 02:58:16'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Repair', 'talomo', '2025-06-25', '14:40:00', '09090909090', NULL, 'Completed', '2025-06-24 03:18:25'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Check-up', 'talomo', '2025-06-26', '08:00:00', '09090909090', NULL, 'Completed', '2025-06-24 03:29:47'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Repair', 'talomo', '2025-07-03', '08:00:00', '09090909090', NULL, 'Rejected', '2025-06-24 03:31:26'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Relocation', 'talomo', '2025-06-26', '14:40:00', '09124536392', NULL, 'Completed', '2025-06-24 06:39:20'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Check-up', 'talomo', '2025-06-27', '13:00:00', '09124536392', NULL, 'done', '2025-06-24 08:18:57'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-26', '14:40:00', '09124536392', NULL, 'Completed', '2025-06-24 08:18:57'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-26', '14:40:00', '09124536392', NULL, 'done', '2025-06-24 08:21:56'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-27', '14:40:00', '09124536392', NULL, 'Completed', '2025-06-25 13:21:26'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Check-up', 'talomo', '2025-06-27', '13:00:00', '09124536392', NULL, 'Completed', '2025-06-25 13:21:36'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-27', '14:40:00', '09124536392', NULL, 'Completed', '2025-06-25 13:21:45'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-27', '13:00:00', '09124536392', NULL, 'Completed', '2025-06-26 16:05:18'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Check-up', 'talomo', '2025-07-26', '08:00:00', '09124536392', NULL, 'Completed', '2025-06-26 16:06:20'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-28', '08:00:00', '09124536392', NULL, 'Completed', '2025-06-27 03:19:15'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Check-up', 'talomo', '2025-06-27', '14:40:00', '09124536392', NULL, 'Completed', '2025-06-27 05:55:50'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-27', '08:00:00', '09124536392', NULL, 'done', '2025-06-29 07:01:08'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Relocation', 'talomo', '2025-06-28', '13:00:00', '09124536392', NULL, 'Completed', '2025-06-29 07:12:50'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Relocation', 'talomo', '2025-06-30', '09:40:00', '09124536392', NULL, 'done', '2025-06-29 07:12:50'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-27', '13:00:00', '09124536392', NULL, 'done', '2025-06-29 07:22:54'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Cleaning', 'talomo', '2025-06-27', '14:40:00', '09124536392', NULL, 'done', '2025-06-29 07:22:54'),
(0, 3, 'kenneth maylos', 'kenneth@gmail.com', 'Aircon Check-up', 'talomo', '2025-07-05', '08:00:00', '09124536392', NULL, 'Completed', '2025-06-29 09:31:51');

-- --------------------------------------------------------

--
-- Table structure for table `booking_history_customer`
--

CREATE TABLE `booking_history_customer` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `service_type` varchar(255) DEFAULT NULL,
  `booking_date` date DEFAULT NULL,
  `booking_time` time DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `technician_name` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `moved_at` datetime DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_history_customer`
--

INSERT INTO `booking_history_customer` (`id`, `user_id`, `service_type`, `booking_date`, `booking_time`, `phone`, `technician_name`, `address`, `status`, `notes`, `moved_at`, `price`) VALUES
(1, 3, 'Aircon Check-up', '2025-06-27', '08:00:00', NULL, 'N/A', NULL, 'Cancelled', NULL, '2025-06-24 01:35:46', 0.00),
(2, 3, 'Aircon Relocation', '2025-06-30', '13:00:00', NULL, 'N/A', NULL, 'Cancelled', NULL, '2025-06-24 01:37:14', 0.00),
(3, 3, 'Aircon Check-up', '2025-06-28', '08:00:00', NULL, 'N/A', NULL, 'Cancelled', NULL, '2025-06-24 01:52:37', 0.00),
(4, 3, 'Aircon Check-up', '2025-06-26', '09:40:00', NULL, 'N/A', NULL, 'Cancelled', NULL, '2025-06-24 03:04:32', 0.00),
(5, 3, 'Aircon Check-up', '2025-06-25', '09:40:00', NULL, 'N/A', NULL, 'Cancelled', NULL, '2025-06-24 03:04:44', 0.00),
(6, 3, 'Aircon Relocation', '2025-06-24', '09:40:00', NULL, 'N/A', NULL, 'Cancelled', NULL, '2025-06-24 10:38:12', 0.00),
(7, 3, 'Aircon Repair', '2025-06-24', '14:40:00', NULL, 'N/A', NULL, 'done', NULL, '2025-06-24 10:41:25', 0.00),
(8, 3, 'Aircon Check-up', '2025-06-30', '08:00:00', NULL, 'Ken Flores', NULL, 'done', NULL, '2025-06-24 11:20:45', 0.00),
(9, 3, 'Aircon Check-up', '2025-07-01', '08:00:00', NULL, 'Ken Flores', NULL, 'done', NULL, '2025-06-24 11:24:15', 0.00),
(10, 3, 'Aircon Cleaning', '2025-06-26', '08:00:00', NULL, 'N/A', NULL, 'done', NULL, '2025-06-24 11:34:34', 0.00),
(11, 3, 'Aircon Cleaning', '2025-07-31', '16:20:00', NULL, 'N/A', NULL, 'Cancelled', NULL, '2025-06-24 14:01:11', 0.00),
(13, 3, 'Aircon Cleaning', '2025-07-26', '14:40:00', NULL, 'N/A', NULL, 'Cancelled', NULL, '2025-06-24 14:43:16', 0.00),
(15, 3, 'Aircon Check-up', '2025-07-12', '14:40:00', NULL, 'N/A', NULL, 'Cancelled', NULL, '2025-06-24 16:30:19', 0.00),
(16, 3, 'Aircon Relocation', '2025-07-11', '13:00:00', NULL, 'N/A', NULL, 'Cancelled', NULL, '2025-06-24 16:34:20', 0.00),
(17, 3, 'Aircon Cleaning', '2025-07-04', '13:00:00', NULL, 'N/A', NULL, 'Cancelled', NULL, '2025-06-24 16:34:23', 0.00),
(18, 3, 'Aircon Check-up', '2025-07-03', '14:40:00', NULL, 'N/A', NULL, 'Cancelled', NULL, '2025-06-24 16:34:26', 0.00),
(19, 3, 'Aircon Cleaning', '2025-06-24', '14:40:00', NULL, 'N/A', NULL, 'Cancelled', NULL, '2025-06-24 16:35:42', 0.00),
(20, 3, 'Aircon Cleaning', '2025-06-25', '09:40:00', NULL, 'N/A', NULL, 'Cancelled', NULL, '2025-06-24 16:35:50', 0.00),
(21, 3, 'Aircon Check-up', '2025-06-26', '08:00:00', NULL, 'N/A', NULL, 'Cancelled', NULL, '2025-06-25 13:09:37', 0.00),
(22, 3, 'Aircon Cleaning', '2025-07-31', '13:00:00', NULL, 'N/A', NULL, 'Cancelled', NULL, '2025-06-26 16:44:45', 0.00),
(23, 3, 'Aircon Cleaning', '2025-05-29', '13:00:00', NULL, 'N/A', NULL, 'Cancelled', NULL, '2025-06-27 10:59:27', 0.00),
(24, 3, 'Aircon Cleaning', '2025-06-28', '08:00:00', NULL, 'N/A', NULL, 'Cancelled', NULL, '2025-06-27 11:19:36', 0.00),
(25, 3, 'Aircon Cleaning', '2025-07-01', '13:00:00', NULL, 'Ken Flores', NULL, 'done', NULL, '2025-06-29 17:18:57', 0.00),
(26, 3, 'Aircon Cleaning', '2025-07-01', '14:40:00', NULL, 'Ken Flores', NULL, 'done', NULL, '2025-06-29 17:18:57', 0.00),
(27, 3, 'Aircon Cleaning', '2025-07-23', '14:40:00', NULL, 'Ken Flores', NULL, 'done', NULL, '2025-06-29 19:29:16', 0.00),
(28, 3, '1 x Aircon Cleaning - Window Type, 1 x Aircon Cleaning - Window Type Inverter, 1 x Aircon Cleaning -', '2025-07-02', '09:40:00', '09124536392', 'N/A', NULL, 'Cancelled', NULL, '2025-07-01 06:28:36', 0.00),
(29, 3, '1 x Aircon Check-up - Check-up Fee, 1 x Aircon Relocation - Relocation Service, 1 x Aircon Repair - ', '2025-07-03', '08:00:00', '09124536392', 'N/A', NULL, 'Cancelled', NULL, '2025-07-01 07:01:51', 0.00),
(30, 3, '1 x Aircon Check-up - Check-up Fee, 1 x Aircon Relocation - Relocation Service, 1 x Aircon Repair - ', '2025-07-02', '13:00:00', '09124536392', 'N/A', NULL, 'Cancelled', NULL, '2025-07-01 07:01:56', 0.00),
(31, 3, '1 x Aircon Relocation - Relocation Service, 1 x Aircon Repair - Capacitor Replacement, 1 x Aircon Re', '2025-07-02', '08:00:00', '09124536392', 'N/A', NULL, 'Cancelled', NULL, '2025-07-01 07:26:38', 0.00),
(32, 3, '1 x Aircon Check-up - Check-up Fee, 1 x Aircon Relocation - Relocation Service, 1 x Aircon Repair - ', '2025-07-02', '08:00:00', '09124536392', 'N/A', NULL, 'Cancelled', NULL, '2025-07-01 07:26:40', 0.00),
(33, 3, 'Aircon Cleaning', '2025-07-02', '13:00:00', '09124536392', 'N/A', NULL, 'Cancelled', NULL, '2025-07-01 07:26:43', 0.00),
(34, 3, '1 x Aircon Check-up - Check-up Fee, 1 x Aircon Relocation - Relocation Service, 1 x Aircon Repair - ', '2025-07-03', '13:00:00', '09124536392', 'N/A', NULL, 'Cancelled', NULL, '2025-07-01 08:41:09', 0.00),
(35, 3, '1 x Aircon Cleaning - Window Type, 1 x Aircon Cleaning - Window Type Inverter, 1 x Aircon Cleaning -', '2025-07-02', '08:00:00', '09124536392', 'N/A', NULL, 'Cancelled', NULL, '2025-07-01 08:56:12', 0.00),
(36, 3, '1 x Aircon Relocation - Relocation Service, 1 x Aircon Repair - Capacitor Replacement, 1 x Aircon Re', '2025-07-04', '13:00:00', '09124536392', 'N/A', NULL, 'Cancelled', NULL, '2025-07-01 08:56:16', 0.00),
(37, 3, '1 x Aircon Check-up - Check-up Fee, 1 x Aircon Relocation - Relocation Service, 1 x Aircon Repair - ', '2025-07-02', '08:00:00', '09124536392', 'N/A', NULL, 'Cancelled', NULL, '2025-07-01 08:56:21', 0.00),
(38, 3, '1 x Aircon Check-up - Check-up Fee, 1 x Aircon Relocation - Relocation Service, 1 x Aircon Repair - ', '2025-07-02', '13:00:00', '09124536392', 'N/A', NULL, 'Cancelled', NULL, '2025-07-01 08:56:48', 0.00),
(39, 3, '1 x Aircon Check-up - Check-up Fee, 1 x Aircon Relocation - Relocation Service, 1 x Aircon Repair - ', '2025-07-03', '13:00:00', NULL, 'N/A', NULL, 'Cancelled', NULL, '2025-07-01 09:19:01', 0.00),
(40, 3, 'Aircon Cleaning', '2025-07-02', '08:00:00', NULL, 'N/A', NULL, 'Cancelled', NULL, '2025-07-01 09:20:20', 0.00),
(41, 3, 'Aircon Relocation', '2025-07-02', '14:40:00', NULL, 'N/A', NULL, 'Cancelled', NULL, '2025-07-01 09:25:11', 0.00),
(42, 3, 'Aircon Check-up', '2025-07-02', '14:40:00', NULL, 'N/A', NULL, 'Cancelled', NULL, '2025-07-01 09:28:36', 0.00),
(43, 3, 'Window type (inverter)', '2025-07-11', '13:00:00', NULL, 'N/A', NULL, 'Cancelled', NULL, '2025-07-02 00:43:39', 0.00),
(44, 3, 'Window type (inverter)', '2025-07-04', '09:40:00', NULL, 'N/A', NULL, 'Cancelled', NULL, '2025-07-02 00:43:42', 0.00),
(45, 3, 'Aircon Relocation', '2025-07-03', '13:00:00', NULL, 'N/A', NULL, 'Cancelled', NULL, '2025-07-02 00:43:45', 0.00),
(46, 3, 'Aircon cleaning (window type)', '2025-07-03', '14:40:00', NULL, 'N/A', NULL, 'Cancelled', NULL, '2025-07-02 00:43:47', 0.00),
(47, 3, 'Aircon Cleaning', '2025-07-03', '08:00:00', NULL, 'N/A', NULL, 'Cancelled', NULL, '2025-07-02 00:43:50', 0.00),
(48, 3, 'Floormounted', '2025-07-02', '13:00:00', NULL, 'N/A', NULL, 'Cancelled', NULL, '2025-07-02 00:43:53', 0.00),
(49, 3, 'Window type (U shape)', '2025-07-02', '13:00:00', NULL, 'N/A', NULL, 'Cancelled', NULL, '2025-07-02 00:43:56', 0.00),
(50, 3, 'Cassette', '2025-07-02', '08:00:00', NULL, 'N/A', NULL, 'Cancelled', NULL, '2025-07-02 00:46:12', 0.00),
(51, 3, 'Cassette', '2025-07-02', '08:00:00', NULL, 'N/A', NULL, 'Cancelled', NULL, '2025-07-02 00:46:14', 0.00),
(52, 3, 'Cassette', '2025-07-02', '13:00:00', NULL, 'N/A', NULL, 'Cancelled', NULL, '2025-07-02 00:48:51', 0.00),
(53, 3, 'Cassette', '2025-07-02', '13:00:00', NULL, 'N/A', NULL, 'Cancelled', NULL, '2025-07-02 00:56:26', 0.00),
(54, 3, 'Split type', '2025-07-02', '09:40:00', '09124536392', 'N/A', NULL, 'Cancelled', NULL, '2025-07-02 01:13:30', 0.00),
(55, 3, '', '0000-00-00', '00:00:00', '', 'N/A', NULL, 'Cancelled', NULL, '2025-07-02 01:20:41', 0.00),
(56, 3, 'Capacitor Thermostat', '2025-07-03', '08:00:00', '09124536392', 'N/A', NULL, 'Cancelled', NULL, '2025-07-02 01:20:44', 0.00),
(57, 3, 'Aircon cleaning (window type)', '2025-07-02', '08:00:00', '09124536392', 'N/A', NULL, 'Cancelled', NULL, '2025-07-02 01:20:47', 0.00),
(58, 3, 'Floormounted', '2025-07-02', '08:00:00', '09124536392', 'N/A', NULL, 'Cancelled', NULL, '2025-07-02 01:21:50', 3000.00),
(59, 3, 'Capacitor Thermostat', '2025-07-02', '13:00:00', '09124536392', 'N/A', NULL, 'Cancelled', NULL, '2025-07-02 03:29:31', 1200.00),
(60, 3, 'Split type', '2025-07-29', '08:00:00', '09124536392', 'Ken Flores', NULL, 'done', NULL, '2025-07-02 17:02:35', 0.00),
(61, 3, 'Aircon cleaning (window type)', '2025-07-02', '14:40:00', '09124536392', 'N/A', NULL, 'Cancelled', NULL, '2025-07-02 17:10:05', 0.00),
(62, 3, 'Aircon cleaning (window type)', '2025-07-31', '08:00:00', '09124536392', 'Ken Flores', NULL, 'done', NULL, '2025-07-03 14:41:49', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `booking_history_employees`
--

CREATE TABLE `booking_history_employees` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `service` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `phone_number` varchar(50) DEFAULT NULL,
  `appointment_date` date DEFAULT NULL,
  `appointment_time` time DEFAULT NULL,
  `status` enum('done','cancelled','completed','rejected') DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `archived_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_history_employees`
--

INSERT INTO `booking_history_employees` (`id`, `booking_id`, `user_id`, `employee_id`, `name`, `service`, `location`, `phone_number`, `appointment_date`, `appointment_time`, `status`, `created_at`, `archived_at`) VALUES
(1, 229, 3, 1, 'kenneth maylos', 'Aircon Cleaning', 'talomo', '09124536392', '2025-07-01', '14:40:00', 'done', '2025-06-29 15:30:26', '2025-06-29 15:47:30'),
(2, 215, 3, 1, 'kenneth maylos', 'Aircon Cleaning', 'talomo', '09124536392', '2025-07-01', '13:00:00', 'done', '2025-06-26 14:58:07', '2025-06-29 15:47:30');

-- --------------------------------------------------------

--
-- Table structure for table `booking_notes`
--

CREATE TABLE `booking_notes` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `note` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_notes`
--

INSERT INTO `booking_notes` (`id`, `booking_id`, `employee_id`, `note`, `created_at`) VALUES
(0, 231, 1, 'heee', '2025-07-01 02:03:30'),
(0, 231, 1, 'heee', '2025-07-01 02:03:30'),
(0, 0, 1, 'tsk', '2025-07-02 06:58:20'),
(0, 8, 1, 'gcjgcghrkgjfk', '2025-07-02 09:20:30');

-- --------------------------------------------------------

--
-- Table structure for table `cancel_booking`
--

CREATE TABLE `cancel_booking` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `service_type` varchar(255) DEFAULT NULL,
  `booking_date` date DEFAULT NULL,
  `booking_time` time DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `technician_name` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`id`, `username`, `password`) VALUES
(1, 'Ken Flores', '123'),
(2, 'Reymar Flores', '123'),
(3, 'Janno Suaybagauio', '123'),
(9, 'Reymark Romero', '123'),
(90, 'Mac Nava', '123'),
(1, 'Ken Flores', '123'),
(2, 'Reymar Flores', '123'),
(3, 'Janno Suaybagauio', '123'),
(9, 'Reymark Romero', '123'),
(90, 'Mac Nava', '123');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `hire_date` date DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `name`, `position`, `email`, `hire_date`, `status`, `password`, `phone`) VALUES
(1, 'Ken Flores', 'Technician', 'mardy@gmail.com', '2024-12-05', 'Active', '12344', NULL),
(2, 'Reymar Flores', 'Technician', 'reymar@gmail.com', '2024-12-05', 'Active', '1222', NULL),
(9, 'Mac Nava', 'Technician', 'nava@gmail.com', '2024-12-06', 'Active', '1111', NULL),
(10, 'Reymark Romero', ' Technician', 'reymark@gmail.com', '2024-12-05', 'Active', '', NULL),
(14, 'Janno Suaybaguio', 'Technician', 'janno@gmail.com', '0000-00-00', 'Active', '', NULL),
(20, 'Anthony Beseril', 'Technician', 'anthony@gmail.com', '0000-00-00', 'Active', '$2y$10$mF/RC4fVXhOO6BkVBWI5iuRR5BmT2TDcm49uV9myuPczI94rbe7.S', NULL),
(21, 'Nino Flores', 'Technician', 'nino@gmail.com', '2025-06-14', 'Active', '$2y$10$/iSNEg52JNz8uGDsjK9vG.JbP/b5xEunRZ8MX5eLsuI4o4sSNvm1e', NULL),
(22, 'Reymond Fernandez', 'Technician', 'reymond@gmail.com', '2022-07-23', 'Active', '$2y$10$tJpKXH5PaFmRpUOM3kctuu/09qCf.9t89.CQcC4gguleYUSK6aD4G', NULL),
(51, 'Dexter Dela Cruz', 'IT Technician', 'dexter@gmail.com', '2025-07-07', 'Active', '$2y$10$Pqdrsio1Rzf7bLtPemN.5.dcfcdt8zZqeDR7Nm3l9r2tZk.eiGey.', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `timestamp`) VALUES
(6, 4, 2, 'cancel booking', '2025-05-25 20:27:47'),
(7, 4, 2, '11', '2025-05-25 20:32:21'),
(8, 4, 2, '11 pm nlng', '2025-05-25 21:18:13'),
(9, 5, 2, 'hello boss', '2025-05-25 21:36:36');

-- --------------------------------------------------------

--
-- Table structure for table `reschedule_requests`
--

CREATE TABLE `reschedule_requests` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `requested_date` date NOT NULL,
  `requested_time` time NOT NULL,
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_notes`
--

CREATE TABLE `service_notes` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `service` varchar(255) NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','reviewed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `username` varchar(25) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `confirm_password` varchar(50) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `city` varchar(100) NOT NULL,
  `district` varchar(50) NOT NULL,
  `barangay` varchar(50) NOT NULL,
  `zipcode` varchar(15) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reset_token` varchar(255) NOT NULL,
  `reset_expire` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `fname`, `lname`, `username`, `email`, `password`, `confirm_password`, `contact`, `city`, `district`, `barangay`, `zipcode`, `created_at`, `reset_token`, `reset_expire`) VALUES
(1, 'admin', 'admin', 'admin', 'admin@gmail.com', 'admin', '', '', '', '', '', '', '2025-07-07 02:36:43', '', '2025-07-07 04:36:14'),
(13, 'Virdel', 'Lubaton', 'vdelccna', 'vdelccna@gmail.com', '$2y$10$B25MsOa8b9lIUxpiDkwA1.8GstgJCjsT3WjOKeDOj5r', '$2y$10$B25MsOa8b9lIUxpiDkwA1.8GstgJCjsT3WjOKeDOj5r', '+639753634122', 'Davao City', 'Poblacion', '37-D', '8000', '2025-07-07 03:55:12', '', '0000-00-00 00:00:00'),
(14, 'Girlie', 'Dela Pinas', 'girlie', 'girlie@gmail.com', '$2y$10$GFmQmJGXSHkhSBUQq7ECWOjREkG5qn6zls97lcjziId', '$2y$10$B25MsOa8b9lIUxpiDkwA1.8GstgJCjsT3WjOKeDOj5r', '+6397324324234', 'Davao City', 'Buhangin', 'Acacia', '8000', '2025-07-07 04:02:15', '', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT 'default_profile.jpg',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `address`, `profile_picture`, `created_at`) VALUES
(1, 'Alice Johnson', 'alice@example.com', 'password123', '555-1234', '123 Main St', 'default_profile.jpg', '2025-05-14 07:03:41'),
(2, 'Bob Brown', 'bob@example.com', 'password456', '555-5678', '456 Oak Ave', 'default_profile.jpg', '2025-05-14 07:03:41'),
(3, 'John Doe', 'john@example.com', 'hashedpassword', '09123456789', 'Davao City', 'default_profile.jpg', '2025-05-14 07:18:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `booking_history_customer`
--
ALTER TABLE `booking_history_customer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `booking_history_employees`
--
ALTER TABLE `booking_history_employees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `cancel_booking`
--
ALTER TABLE `cancel_booking`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reschedule_requests`
--
ALTER TABLE `reschedule_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `service_notes`
--
ALTER TABLE `service_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `booking_history_customer`
--
ALTER TABLE `booking_history_customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `booking_history_employees`
--
ALTER TABLE `booking_history_employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cancel_booking`
--
ALTER TABLE `cancel_booking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `reschedule_requests`
--
ALTER TABLE `reschedule_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
