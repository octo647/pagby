-- MySQL dump 10.13  Distrib 8.4.7, for Linux (x86_64)
--
-- Host: localhost    Database: pagby
-- ------------------------------------------------------
-- Server version	8.4.7-0ubuntu0.25.10.2

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
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('356a192b7913b04c54574d18c28d46e6395428ab','i:1;',1764182165),('356a192b7913b04c54574d18c28d46e6395428ab:timer','i:1764182165;',1764182165);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contacts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `owner_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cpf` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notas` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `address` text COLLATE utf8mb4_unicode_ci,
  `neighborhood` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cep` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tenant_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_count` int DEFAULT NULL,
  `tipo` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `contacts_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contacts`
--

LOCK TABLES `contacts` WRITE;
/*!40000 ALTER TABLE `contacts` DISABLE KEYS */;
INSERT INTO `contacts` VALUES (6,'Frederico Morais',NULL,'fred@gmail.com','32987007302',NULL,'Av. Pres. Costa e Silva, 500','S. Pedro',NULL,'Juiz de Fora','MG','Salão Dumont',NULL,'Barbearia','2025-09-28 20:11:17','2025-09-28 20:11:17'),(67,'José Antunes','22222222201','antunes@gmail.com','54564654564',NULL,'Rua Aristóteles Braga','São Pedro','36037010','Juiz de Fora','MG','Salão Bar',3,'Barbearia','2025-11-21 18:56:06','2025-11-21 18:56:06'),(68,'Antônio Antunes','346545645466','antunes@gmail2.com','56465456454',NULL,'Rua Aristóteles Braga','São Pedro','36037010','Juiz de Fora','MG','Salão Barber',2,'Barbearia','2025-11-21 19:17:18','2025-11-21 19:17:18'),(69,'Leandro de Moura','5465465456456','kjj@jjki.kjkj','5456456454','','Rua Aristóteles Braga','São Pedro','36037010','Juiz de Fora','MG','Moura Barber',2,'Salão de Beleza','2025-11-21 19:24:17','2025-11-22 13:13:18'),(70,'Geraldo Vasconcelos','56465456454','algo@gmail.com','5456456454','','Rua Aristóteles Braga, 34','São Pedro','36037010','Juiz de Fora','MG','Barbearia do Vavá',2,'Barbearia','2025-11-21 19:43:10','2025-11-22 13:21:38'),(71,'Eduardo Mascarenhas','12345678909','edu@algo.com','56456456456',NULL,'Rua Aristóteles Braga, 300','São Pedro','36037010','Juiz de Fora','MG','Barbearia do Dudu',2,'Barbearia','2025-11-22 20:19:03','2025-11-22 20:19:03'),(72,'Eduardo Jorge Maciel','55555555502','maciel@gmail.com','32654564564',NULL,'Rua Aristóteles Braga, 300','São Pedro','36037010','Juiz de Fora','MG','Ville Belle',2,'Salão de Beleza','2025-11-26 13:46:51','2025-11-26 13:46:51');
/*!40000 ALTER TABLE `contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `domains`
--

DROP TABLE IF EXISTS `domains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `domains` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tenant_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `domains_domain_unique` (`domain`),
  KEY `domains_tenant_id_foreign` (`tenant_id`),
  CONSTRAINT `domains_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `domains`
--

LOCK TABLES `domains` WRITE;
/*!40000 ALTER TABLE `domains` DISABLE KEYS */;
INSERT INTO `domains` VALUES (9,'bar.localhost','bar','2025-08-10 12:35:23','2025-08-10 12:35:23'),(35,'bicholegal.localhost','bicholegal','2025-09-28 10:17:48','2025-09-28 10:17:48'),(37,'dumont.localhost','dumont','2025-09-28 20:15:36','2025-09-28 20:15:36'),(38,'labelle.localhost','labelle','2025-09-28 20:38:36','2025-09-28 20:38:36'),(54,'dudu.localhost','dudu','2025-11-22 20:30:01','2025-11-22 20:30:01'),(64,'villebelle.localhost','villebelle','2025-11-26 18:35:47','2025-11-26 18:35:47');
/*!40000 ALTER TABLE `domains` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'1_create_users_table',1),(2,'2019_09_15_000010_create_tenants_table',1),(3,'2019_09_15_000020_create_domains_table',1),(4,'2025_07_29_202604_contacts',1),(5,'2_create_cache_table',1),(6,'3_create_jobs_table',1),(7,'6_create_roles_table',1),(8,'7_create_role_user_table',1),(9,'2025_08_15_081112_update_contacts_table_add_missing_fields',2),(10,'2025_08_24_111441_add_subscription_fields_to_tenants_table',3),(12,'2025_08_25_153410_add_extra_columns_to_users_table',4),(13,'2025_09_16_110341_create_sessions_table',5),(14,'2025_09_28_000001_change_type_to_string_in_tenants_table',5),(15,'2025_10_09_073439_create_pag_by_payments_table',6),(16,'2025_10_09_074428_create_payments_table',7),(17,'2025_10_12_162053_add_mp_payment_id_to_payments_table',8),(18,'2025_10_12_162126_add_mp_payment_id_to_payments_table',8),(19,'2025_10_09_073439_create_pagby_payments_table',9),(20,'2025_10_09_074428_create_tenants_plans_payments_table',10),(21,'2025_10_12_162126_add_mp_payment_id_to_tenants_plans_payments_table',10),(22,'2025_10_24_120431_tenants_plans_table',11);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pagby_payments`
--

DROP TABLE IF EXISTS `pagby_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pagby_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_id` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mp_payment_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `external_id` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `employee_count` int DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mp_data` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pag_by_payments_mp_payment_id_unique` (`mp_payment_id`),
  KEY `pag_by_payments_tenant_id_status_index` (`tenant_id`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagby_payments`
--

LOCK TABLES `pagby_payments` WRITE;
/*!40000 ALTER TABLE `pagby_payments` DISABLE KEYS */;
INSERT INTO `pagby_payments` VALUES (10,'temp_58',NULL,NULL,'2f47f869019c4a66af2387d9bcd25aa2','premium',NULL,'pending',59.90,NULL,NULL,'2025-10-22 19:53:01','2025-10-22 19:53:02'),(11,'temp_58',NULL,NULL,'8b46cc553e664421accb7dfcbd7e441b','premium',NULL,'pending',59.90,NULL,NULL,'2025-10-22 19:55:03','2025-10-22 19:55:05'),(12,'temp_59',NULL,NULL,'1524e67874c444238dded3b6bdab73c6','basico',NULL,'pending',29.90,NULL,NULL,'2025-10-22 20:05:18','2025-10-22 20:05:19'),(13,'temp_60',NULL,NULL,'9c68763387a54169984fffc7ca85ab7b','premium',NULL,'pending',59.90,NULL,NULL,'2025-10-23 11:41:10','2025-10-23 11:41:12'),(14,'temp_64',NULL,NULL,NULL,'premium',NULL,'pending',59.90,NULL,NULL,'2025-10-23 12:40:30','2025-10-23 12:40:30'),(15,'temp_65',NULL,NULL,NULL,'premium',NULL,'pending',59.90,NULL,NULL,'2025-10-23 12:41:56','2025-10-23 12:41:56'),(16,'foo',NULL,NULL,NULL,'Básico',2,'Ativo',59.80,NULL,NULL,'2025-11-14 20:39:35','2025-11-14 20:39:35'),(17,'bar','67',NULL,NULL,'basico',3,'pending',89.70,NULL,NULL,'2025-11-26 13:41:54','2025-11-26 13:41:54'),(18,'bar','67',NULL,NULL,'premium',3,'pending',179.70,NULL,NULL,'2025-11-26 13:42:57','2025-11-26 13:42:57');
/*!40000 ALTER TABLE `pagby_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_user`
--

DROP TABLE IF EXISTS `role_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `role_user_role_id_foreign` (`role_id`),
  KEY `role_user_user_id_foreign` (`user_id`),
  CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_user`
--

LOCK TABLES `role_user` WRITE;
/*!40000 ALTER TABLE `role_user` DISABLE KEYS */;
INSERT INTO `role_user` VALUES (1,NULL,NULL,1,1);
/*!40000 ALTER TABLE `role_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,NULL,NULL,'Admin');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tenants`
--

DROP TABLE IF EXISTS `tenants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tenants` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook_client_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook_client_secret` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_client_id` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_client_secret` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_login_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cnpj` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fantasy_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `number` int DEFAULT NULL,
  `complement` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `neighborhood` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cep` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Ativo',
  `trial_started_at` timestamp NULL DEFAULT NULL,
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `subscription_status` enum('trial','active','expired','suspended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'trial',
  `current_plan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subscription_started_at` timestamp NULL DEFAULT NULL,
  `subscription_ends_at` timestamp NULL DEFAULT NULL,
  `is_blocked` tinyint(1) NOT NULL DEFAULT '0',
  `data` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tenants_email_unique` (`email`),
  UNIQUE KEY `tenants_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tenants`
--

LOCK TABLES `tenants` WRITE;
/*!40000 ALTER TABLE `tenants` DISABLE KEYS */;
INSERT INTO `tenants` VALUES ('bar','Barbearia','antunes@gmail.com','',NULL,'','helder.couto@ufjf.br','.8CnK;p@dnSH!;X','',NULL,1,'Salão Bar','','Barbearia Bar','bar','',NULL,'','','','Ubá','MG','images/bar/logo.png','Básico','Ativo','2025-10-01 14:42:30','2026-10-08 14:42:30','expired','Básico','2025-10-09 15:09:01','2025-11-25 15:09:01',1,'{\"tenancy_db_name\": \"tenantbar\"}','2025-08-10 15:35:19','2025-11-26 13:09:49'),('bicholegal','PetShop','biclholegal@algo.com','','','','',NULL,NULL,NULL,0,'','','Petshop Bicho Legal','bicholegal','',NULL,'','','','Anta','MG','images/bicholegal/logo.jpeg','Básico','Ativo','2025-09-28 16:17:48','2025-10-28 16:17:48','expired',NULL,NULL,NULL,1,'{\"tenancy_db_name\": \"tenantbicholegal\"}','2025-09-28 16:17:43','2025-11-26 11:27:59'),('dudu','Barbearia','edu@algo.com','56456456456','','','','','','',0,'Barbearia do Dudu','','Barbearia do Dudu','dudu','Rua Aristóteles Braga, 300',NULL,'','São Pedro','36037010','Juiz de Fora','MG','images/dudu/logo.png','Trial','Ativo','2025-11-23 02:30:01','2025-12-23 02:30:01','trial',NULL,NULL,NULL,0,'{\"tenancy_db_name\": \"tenantdudu\"}',NULL,'2025-11-23 02:30:01'),('dumont','Barbearia','fred@gmail.com','','','','',NULL,NULL,NULL,0,'','','Salão Dumont','dumont','Rua Aristóteles Braga, 36',NULL,'','S. Pedro','36037-010','Juiz de Fora','MG','images/dumont/logo.jpeg','Básico','Ativo','2025-09-29 02:15:36','2025-10-29 02:15:36','expired',NULL,NULL,NULL,1,'{\"tenancy_db_name\": \"tenantdumont\"}','2025-09-29 02:15:31','2025-09-29 02:15:31'),('labelle','SalaoBeleza','jose@algo.com','','','','',NULL,NULL,NULL,0,'La Belle','','La Belle','labelle','Rua São Luiz, 25',NULL,'','Centro','36039-010','Juiz de Fora','MG','images/labelle/logo.png','Básico','Ativo','2025-09-28 23:38:36','2025-11-30 23:38:36','active',NULL,NULL,NULL,0,'{\"tenancy_db_name\": \"tenantlabelle\"}','2025-09-28 23:38:30','2025-11-26 00:12:49'),('villebelle','SalaoBeleza1','maciel@gmail.com','32654564564','','','','','','',0,'Ville Belle','','Ville Belle','villebelle','Rua Aristóteles Braga, 300',NULL,'','São Pedro','36037010','Juiz de Fora','MG','tenants/villebelle/logo.jpeg','trial','Ativo','2025-11-26 18:35:47','2025-12-26 18:35:47','trial',NULL,NULL,NULL,0,'{\"tenancy_db_name\": \"tenantvillebelle\"}',NULL,'2025-11-26 18:35:48');
/*!40000 ALTER TABLE `tenants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tenants_plans`
--

DROP TABLE IF EXISTS `tenants_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tenants_plans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `duration_days` int NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tenants_plans_tenant_id_foreign` (`tenant_id`),
  CONSTRAINT `tenants_plans_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tenants_plans`
--

LOCK TABLES `tenants_plans` WRITE;
/*!40000 ALTER TABLE `tenants_plans` DISABLE KEYS */;
/*!40000 ALTER TABLE `tenants_plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tenants_plans_payments`
--

DROP TABLE IF EXISTS `tenants_plans_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tenants_plans_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `external_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mp_payment_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preference_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_id` bigint unsigned NOT NULL,
  `tenant_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'BRL',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_detail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payer_data` json DEFAULT NULL,
  `mercadopago_data` json DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tenants_plans_payments_external_id_unique` (`external_id`),
  KEY `tenants_plans_payments_contact_id_foreign` (`contact_id`),
  KEY `tenants_plans_payments_status_plan_index` (`status`,`plan`),
  KEY `tenants_plans_payments_external_id_index` (`external_id`),
  CONSTRAINT `tenants_plans_payments_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tenants_plans_payments`
--

LOCK TABLES `tenants_plans_payments` WRITE;
/*!40000 ALTER TABLE `tenants_plans_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `tenants_plans_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('Ativo','Inativo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Ativo',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrador','admin@algo.com                                         ',NULL,'$2y$12$waSLbgNMUHjRLaI9.fCJNOiw4XHSbLi4BDBeRKmW2HcWgLjW7rPse','Ativo','QKVh01NE3kYRbyamciN1jX8m7u4smQbMcmaktDRoeZQSxaZFRWmIZvGDIRme',NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-27 17:24:09
