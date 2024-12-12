-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               11.3.2-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.6.0.6765
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for funeral_booking
CREATE DATABASE IF NOT EXISTS `funeral_booking` /*!40100 DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci */;
USE `funeral_booking`;

-- Dumping structure for table funeral_booking.appointments
CREATE TABLE IF NOT EXISTS `appointments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `status` enum('pending','approved','booked') DEFAULT 'pending',
  `user_id` int(11) NOT NULL,
  `payment_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping data for table funeral_booking.appointments: ~2 rows (approximately)
INSERT INTO `appointments` (`id`, `date`, `status`, `user_id`, `payment_id`, `created_at`, `updated_at`) VALUES
	(1, '2024-12-13', 'approved', 1, '123982938', '2024-11-29 19:39:48', '2024-12-08 14:21:34'),
	(2, '2024-12-08', 'approved', 8, '12323', '2024-12-08 01:17:59', '2024-12-08 01:17:59');

-- Dumping structure for table funeral_booking.memorials
CREATE TABLE IF NOT EXISTS `memorials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `last_name` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `date_of_death` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `appointment_id` (`appointment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping data for table funeral_booking.memorials: ~2 rows (approximately)
INSERT INTO `memorials` (`id`, `last_name`, `first_name`, `appointment_id`, `date_of_death`, `created_at`, `updated_at`) VALUES
	(14, 'asdasdasd', 'asdasdasd', 19, '2024-12-09', '2024-12-08 18:00:08', '2024-12-08 18:00:08'),
	(15, '123', '222', 1231, '2024-12-09', '2024-12-09 06:40:03', '2024-12-09 06:40:03');

-- Dumping structure for table funeral_booking.payments
CREATE TABLE IF NOT EXISTS `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `payment_date` timestamp NULL DEFAULT current_timestamp(),
  `amount` decimal(10,2) NOT NULL,
  `balance` decimal(10,2) DEFAULT 0.00,
  `status` enum('pending','paid','failed') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `appointment_id` (`appointment_id`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping data for table funeral_booking.payments: ~1 rows (approximately)
INSERT INTO `payments` (`id`, `user_id`, `appointment_id`, `payment_date`, `amount`, `balance`, `status`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, '2024-12-04 02:27:00', 1200.00, 2.00, 'paid', '2024-12-04 02:27:41', '2024-12-08 00:54:43'),
	(7, 8, 2, '2024-12-08 10:42:00', 12000.00, 12.00, 'pending', '2024-12-08 03:43:42', '2024-12-08 03:43:42');

-- Dumping structure for table funeral_booking.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `role` enum('User','Admin') DEFAULT 'User',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping data for table funeral_booking.users: ~2 rows (approximately)
INSERT INTO `users` (`id`, `username`, `firstname`, `lastname`, `email`, `password`, `phone`, `role`, `created_at`, `updated_at`) VALUES
	(1, 'admin', 'J', 'Cadondoy', 'admin@gmail.com', '$2y$10$SV4caNRLzVcBy7aZaDU8G.bsYQyDADDhy9DTvGZy05dV4Phb13OYK', '091238475', 'Admin', '2024-11-22 13:48:08', '2024-12-09 13:40:31'),
	(8, 'Ace', 'Ace', 'Sabac', 'Sabac@gmail.com', '$2y$10$Vu7ckY/F6P6nLqTVbE23t.FpVurW60BE1CqU2HWMzLSx.tqQNbz5S', '123718232', 'User', '2024-12-08 01:17:12', '2024-12-08 11:51:26');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
