-- MySQL dump 10.13  Distrib 8.0.44, for Win64 (x86_64)
-- Host: localhost    Database: behungry
-- ------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL, -- Note: The original PHP code used 'name', schema used 'username'. I will align with 'name' based on PHP usage, or 'username' if that was intended. Let's assume 'name' maps to 'username' or create a 'name' column. 
  `name` varchar(100) NOT NULL,    -- Added 'name' to match PHP code usage ($user['name'])
  `email` varchar(100) DEFAULT NULL, -- Changed to Nullable to allow Phone-only registration
  `phone` varchar(20) DEFAULT NULL,  -- New Column for Phone
  `password_hash` varchar(255) DEFAULT NULL, -- Changed to 255 for hash and Nullable for Social Login
  `bio` text,
  `profile_image_url` varchar(255),
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `google_id` varchar(100) DEFAULT NULL,   -- New Column for Google
  `facebook_id` varchar(100) DEFAULT NULL, -- New Column for Facebook
  `is_admin` boolean DEFAULT FALSE,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `phone` (`phone`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
-- Password hash for 'realpassword' (BCRYPT)
INSERT INTO `users` (user_id, username, name, email, password_hash, is_admin) VALUES (1,'moises','Moises','moises@gmail.com','$2y$10$YourGeneratedHashHere...', 1);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

-- (Include other tables: recipes, steps, ingredients, comments, likes as defined previously)
-- For brevity, assuming other tables remain unchanged from your original upload.
