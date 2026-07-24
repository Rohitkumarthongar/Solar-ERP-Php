/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.14-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: laracopilot_echostack54
-- ------------------------------------------------------
-- Server version	10.11.14-MariaDB-0ubuntu0.24.04.1

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
-- Table structure for table `admin_users`
--

DROP TABLE IF EXISTS `admin_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin_users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'user',
  `role_id` bigint(20) unsigned DEFAULT NULL,
  `employee_id` bigint(20) unsigned DEFAULT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_users_email_unique` (`email`),
  UNIQUE KEY `admin_users_employee_id_unique` (`employee_id`),
  KEY `admin_users_role_id_foreign` (`role_id`),
  CONSTRAINT `admin_users_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `admin_users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_users`
--

LOCK TABLES `admin_users` WRITE;
/*!40000 ALTER TABLE `admin_users` DISABLE KEYS */;
INSERT INTO `admin_users` VALUES
(1,'Super Admin','admin@solarerp.com','$2y$12$jMrHBXkqSzJ/buaJ5Fsr2eLmG.uFDsHOo.wumb43Su49R.DwscH0m','admin',5,NULL,NULL,1,NULL,'2026-04-06 19:32:38','2026-04-06 19:32:38'),
(2,'Sales Manager','sales@solarerp.com','$2y$12$Lf6fNY/fj2ewSSkpW4aAB.ZSgIfVzecaBNvlDKz.P7EWuxBPMWAcW','sales',6,NULL,NULL,1,NULL,'2026-04-06 19:32:39','2026-04-06 19:32:39'),
(3,'Tech Lead','tech@solarerp.com','$2y$12$C41ZfIHUqchIW2YszHCVhOzVzh3X4Qr.Y40x4kjMNG/.uCnVduQWW','Technician',2,NULL,NULL,1,NULL,'2026-04-06 19:32:39','2026-04-06 19:32:39');
/*!40000 ALTER TABLE `admin_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `auditable_type` varchar(255) NOT NULL,
  `auditable_id` bigint(20) unsigned NOT NULL,
  `event` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_auditable_type_auditable_id_index` (`auditable_type`,`auditable_id`),
  KEY `audit_logs_user_id_index` (`user_id`),
  KEY `audit_logs_event_index` (`event`),
  KEY `audit_logs_created_at_index` (`created_at`),
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blogs`
--

DROP TABLE IF EXISTS `blogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `blogs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `short_description` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `overview_url` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blogs_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blogs`
--

LOCK TABLES `blogs` WRITE;
/*!40000 ALTER TABLE `blogs` DISABLE KEYS */;
/*!40000 ALTER TABLE `blogs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
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
-- Table structure for table `customer_discoms`
--

DROP TABLE IF EXISTS `customer_discoms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_discoms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) unsigned NOT NULL,
  `discom_name` varchar(255) DEFAULT NULL,
  `k_number` varchar(255) DEFAULT NULL,
  `sanctioned_load` varchar(255) DEFAULT NULL,
  `required_load_kw` varchar(255) DEFAULT NULL,
  `meter_type` varchar(255) DEFAULT NULL,
  `property_type` varchar(255) DEFAULT NULL,
  `roof_area_sqft` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `dcr_report_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `application_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`application_data`)),
  `workflow_status` varchar(255) NOT NULL DEFAULT 'not_started',
  `approval_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approval_remarks` text DEFAULT NULL,
  `application_date` date DEFAULT NULL,
  `submission_number` varchar(255) DEFAULT NULL,
  `discom_portal_username` varchar(255) DEFAULT NULL,
  `discom_portal_password` varchar(255) DEFAULT NULL,
  `meter_number` varchar(255) DEFAULT NULL,
  `application_number` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_discoms_customer_id_foreign` (`customer_id`),
  KEY `customer_discoms_approved_by_foreign` (`approved_by`),
  CONSTRAINT `customer_discoms_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `customer_discoms_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_discoms`
--

LOCK TABLES `customer_discoms` WRITE;
/*!40000 ALTER TABLE `customer_discoms` DISABLE KEYS */;
/*!40000 ALTER TABLE `customer_discoms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_loans`
--

DROP TABLE IF EXISTS `customer_loans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_loans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) unsigned NOT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `loan_amount` decimal(15,2) DEFAULT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `ifsc_code` varchar(255) DEFAULT NULL,
  `loan_status` varchar(255) NOT NULL DEFAULT 'pending',
  `loan_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_loans_customer_id_foreign` (`customer_id`),
  CONSTRAINT `customer_loans_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_loans`
--

LOCK TABLES `customer_loans` WRITE;
/*!40000 ALTER TABLE `customer_loans` DISABLE KEYS */;
/*!40000 ALTER TABLE `customer_loans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_subsidies`
--

DROP TABLE IF EXISTS `customer_subsidies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_subsidies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) unsigned NOT NULL,
  `subsidy_status` varchar(255) NOT NULL DEFAULT 'not_applied',
  `subsidy_amount` decimal(15,2) DEFAULT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `portal_application_no` varchar(255) DEFAULT NULL,
  `subsidy_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_subsidies_customer_id_foreign` (`customer_id`),
  CONSTRAINT `customer_subsidies_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_subsidies`
--

LOCK TABLES `customer_subsidies` WRITE;
/*!40000 ALTER TABLE `customer_subsidies` DISABLE KEYS */;
/*!40000 ALTER TABLE `customer_subsidies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `pincode` varchar(255) DEFAULT NULL,
  `customer_type` enum('residential','commercial','industrial') NOT NULL DEFAULT 'residential',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customers_customer_code_unique` (`customer_code`),
  UNIQUE KEY `customers_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES
(1,'CUST-RES-1001','Rajesh Kumar','rajesh@gmail.com','9876543201','12 Sunder Nagar, Ahmedabad','Ahmedabad','Gujarat','380015','residential',NULL,'2026-04-06 19:32:39','2026-04-06 19:32:39'),
(2,'CUST-RES-1002','Priya Sharma','priya.sharma@gmail.com','9876543202','45 Rose Garden, Surat','Surat','Gujarat','395001','residential',NULL,'2026-04-06 19:32:39','2026-04-06 19:32:39'),
(3,'CUST-COM-1003','Mehta Industries','accounts@mehtaind.com','9876543203','Plot 7, GIDC, Anand','Anand','Gujarat','388001','commercial',NULL,'2026-04-06 19:32:39','2026-04-06 19:32:39'),
(4,'CUST-RES-1004','Suresh Patel','suresh.patel@yahoo.com','9876543204','8 Shanti Nagar, Vadodara','Vadodara','Gujarat','390001','residential',NULL,'2026-04-06 19:32:39','2026-04-06 19:32:39'),
(5,'CUST-IND-1005','Parikh Textiles','info@parikhtextiles.com','9876543205','Industrial Estate, Rajkot','Rajkot','Gujarat','360001','industrial',NULL,'2026-04-06 19:32:39','2026-04-06 19:32:39');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `daily_wage_records`
--

DROP TABLE IF EXISTS `daily_wage_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `daily_wage_records` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `work_date` date NOT NULL,
  `hours_worked` decimal(5,2) NOT NULL DEFAULT 8.00,
  `wattage` decimal(12,2) DEFAULT NULL COMMENT 'Total wattage worked on (in watts, 1KW = 1000 watts)',
  `calculation_type` enum('hourly','watt_based','fixed') NOT NULL DEFAULT 'hourly' COMMENT 'Type of wage calculation used',
  `wage_rate` decimal(10,2) NOT NULL,
  `rate_per_watt_used` decimal(10,4) DEFAULT NULL COMMENT 'Rate per watt used for this calculation (historical record)',
  `total_amount` decimal(10,2) NOT NULL,
  `work_description` varchar(255) DEFAULT NULL,
  `installation_id` bigint(20) unsigned DEFAULT NULL,
  `site_visit_id` bigint(20) unsigned DEFAULT NULL,
  `payment_status` enum('pending','paid') NOT NULL DEFAULT 'pending',
  `payment_date` date DEFAULT NULL,
  `payment_mode` enum('cash','bank_transfer','cheque') DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `daily_wage_records_installation_id_foreign` (`installation_id`),
  KEY `daily_wage_records_site_visit_id_foreign` (`site_visit_id`),
  KEY `daily_wage_records_employee_id_work_date_index` (`employee_id`,`work_date`),
  CONSTRAINT `daily_wage_records_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `daily_wage_records_installation_id_foreign` FOREIGN KEY (`installation_id`) REFERENCES `installations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `daily_wage_records_site_visit_id_foreign` FOREIGN KEY (`site_visit_id`) REFERENCES `site_visits` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `daily_wage_records`
--

LOCK TABLES `daily_wage_records` WRITE;
/*!40000 ALTER TABLE `daily_wage_records` DISABLE KEYS */;
/*!40000 ALTER TABLE `daily_wage_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `document_number` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_type` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `documentable_type` varchar(255) NOT NULL,
  `documentable_id` bigint(20) unsigned NOT NULL,
  `uploaded_by` bigint(20) unsigned DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT 1,
  `parent_document_id` bigint(20) unsigned DEFAULT NULL,
  `is_current_version` tinyint(1) NOT NULL DEFAULT 1,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `tags` text DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `documents_document_number_unique` (`document_number`),
  KEY `documents_documentable_type_documentable_id_index` (`documentable_type`,`documentable_id`),
  KEY `documents_parent_document_id_foreign` (`parent_document_id`),
  KEY `documents_category_index` (`category`),
  KEY `documents_uploaded_by_index` (`uploaded_by`),
  KEY `documents_status_index` (`status`),
  CONSTRAINT `documents_parent_document_id_foreign` FOREIGN KEY (`parent_document_id`) REFERENCES `documents` (`id`) ON DELETE SET NULL,
  CONSTRAINT `documents_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documents`
--

LOCK TABLES `documents` WRITE;
/*!40000 ALTER TABLE `documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_templates`
--

DROP TABLE IF EXISTS `email_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_templates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` longtext NOT NULL,
  `variables` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`variables`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_templates`
--

LOCK TABLES `email_templates` WRITE;
/*!40000 ALTER TABLE `email_templates` DISABLE KEYS */;
INSERT INTO `email_templates` VALUES
(1,'Quotation Email','quotation','Your Solar System Quotation - {quotation_number}','<p>Dear {customer_name},</p><p>Please find attached your quotation <strong>{quotation_number}</strong> for a solar system installation.</p><p><strong>Total Amount: ₹{total_amount}</strong></p><p>This quotation is valid until {valid_until}.</p><p>Please feel free to contact us for any queries.</p><p>Best regards,<br>Palawat Solar Team</p>',NULL,1,'2026-04-06 19:32:39','2026-04-06 19:32:39'),
(2,'Follow Up Email','follow_up','Following up on your Solar Inquiry','<p>Dear {customer_name},</p><p>We wanted to follow up on your recent inquiry about solar panel installation.</p><p>Our team is ready to provide you with a customized solution.</p><p>Best regards,<br>Palawat Solar</p>',NULL,1,'2026-04-06 19:32:39','2026-04-06 19:32:39'),
(3,'Welcome Email','welcome','Welcome to SolarTech Family!','<p>Dear {customer_name},</p><p>Welcome to Palawat Solar! We are thrilled to have you as our customer.</p><p>Your order <strong>{order_number}</strong> has been confirmed.</p><p>Best regards,<br>Palawat Solar Team</p>',NULL,1,'2026-04-06 19:32:39','2026-04-06 19:32:39');
/*!40000 ALTER TABLE `email_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `department` enum('sales','installation','service','admin','accounts') NOT NULL,
  `designation` varchar(255) NOT NULL,
  `employment_type` enum('permanent','contract','daily_wage') NOT NULL DEFAULT 'permanent',
  `contract_start_date` date DEFAULT NULL,
  `contract_end_date` date DEFAULT NULL,
  `contract_amount` decimal(12,2) DEFAULT NULL,
  `daily_wage_rate` decimal(10,2) DEFAULT NULL,
  `installation_rate` decimal(10,2) NOT NULL DEFAULT 0.00,
  `site_visit_rate` decimal(10,2) NOT NULL DEFAULT 0.00,
  `service_rate` decimal(10,2) NOT NULL DEFAULT 0.00,
  `rate_per_watt` decimal(10,4) DEFAULT NULL COMMENT 'Rate in rupees per watt for installation work (1KW = 1000 watts)',
  `use_watt_based_pay` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Enable watt-based salary calculation instead of fixed rates',
  `basic_salary` decimal(12,2) DEFAULT NULL,
  `joining_date` date NOT NULL,
  `address` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_employee_code_unique` (`employee_code`),
  UNIQUE KEY `employees_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES
(1,'EMP-SAL-001','Rahul Verma','rahul.v@solartech.com','9600000001','sales','Sales Executive','permanent',NULL,NULL,NULL,NULL,0.00,0.00,0.00,NULL,0,35000.00,'2022-01-15',NULL,1,'2026-04-06 19:32:39','2026-04-06 19:32:39'),
(2,'EMP-SAL-002','Pooja Nair','pooja.n@solartech.com','9600000002','sales','Senior Sales Executive','permanent',NULL,NULL,NULL,NULL,0.00,0.00,0.00,NULL,0,45000.00,'2021-06-01',NULL,1,'2026-04-06 19:32:39','2026-04-06 19:32:39'),
(3,'EMP-INS-001','Deepak Singh','deepak.s@solartech.com','9600000003','installation','Lead Technician','permanent',NULL,NULL,NULL,NULL,0.00,0.00,0.00,NULL,0,40000.00,'2021-03-10',NULL,1,'2026-04-06 19:32:39','2026-04-06 19:32:39'),
(4,'EMP-INS-002','Ravi Kumar','ravi.k@solartech.com','9600000004','installation','Solar Technician','permanent',NULL,NULL,NULL,NULL,0.00,0.00,0.00,NULL,0,28000.00,'2022-08-20',NULL,1,'2026-04-06 19:32:39','2026-04-06 19:32:39'),
(5,'EMP-ADM-001','Anita Patel','anita.p@solartech.com','9600000005','admin','Office Manager','permanent',NULL,NULL,NULL,NULL,0.00,0.00,0.00,NULL,0,38000.00,'2020-11-05',NULL,1,'2026-04-06 19:32:39','2026-04-06 19:32:39');
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `expenses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `expense_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expenses`
--

LOCK TABLES `expenses` WRITE;
/*!40000 ALTER TABLE `expenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
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
-- Table structure for table `installations`
--

DROP TABLE IF EXISTS `installations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `installations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `installation_number` varchar(255) NOT NULL,
  `customer_id` bigint(20) unsigned NOT NULL,
  `sales_order_id` bigint(20) unsigned DEFAULT NULL,
  `sales_invoice_id` bigint(20) unsigned DEFAULT NULL,
  `scheduled_date` date NOT NULL,
  `completion_date` date DEFAULT NULL,
  `system_size_kw` decimal(8,2) NOT NULL,
  `installation_address` text NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `roof_type` varchar(255) NOT NULL,
  `assigned_team` varchar(255) DEFAULT NULL,
  `assigned_team_id` bigint(20) unsigned DEFAULT NULL,
  `status` enum('scheduled','in_progress','completed','cancelled') NOT NULL DEFAULT 'scheduled',
  `approval_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approval_remarks` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `completion_photos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`completion_photos`)),
  `proof_photos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Installation proof images array' CHECK (json_valid(`proof_photos`)),
  `proof_before_photo` varchar(255) DEFAULT NULL,
  `proof_during_photo` varchar(255) DEFAULT NULL,
  `proof_after_photo` varchar(255) DEFAULT NULL,
  `proof_meter_photo` varchar(255) DEFAULT NULL,
  `proof_panel_photo` varchar(255) DEFAULT NULL,
  `proof_inverter_photo` varchar(255) DEFAULT NULL,
  `proof_submitted` tinyint(1) NOT NULL DEFAULT 0,
  `proof_submitted_at` timestamp NULL DEFAULT NULL,
  `technician_remarks` text DEFAULT NULL,
  `auto_service_created` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `installation_checklist` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`installation_checklist`)),
  `panel_serial_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`panel_serial_details`)),
  `inverter_serial_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`inverter_serial_details`)),
  `inverter_serial_number` varchar(255) DEFAULT NULL,
  `net_meter_serial_number` varchar(255) DEFAULT NULL,
  `initial_meter_reading` varchar(255) DEFAULT NULL,
  `structure_panel_photo` varchar(255) DEFAULT NULL,
  `ground_setup_photo` varchar(255) DEFAULT NULL,
  `roof_setup_photo` varchar(255) DEFAULT NULL,
  `panel_angle_photo` varchar(255) DEFAULT NULL,
  `site_location_photo` varchar(255) DEFAULT NULL,
  `wiring_photo` varchar(255) DEFAULT NULL,
  `meter_setup_photo` varchar(255) DEFAULT NULL,
  `el_test_report` varchar(255) DEFAULT NULL,
  `commissioning_report` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `installations_installation_number_unique` (`installation_number`),
  KEY `installations_customer_id_foreign` (`customer_id`),
  KEY `installations_sales_order_id_foreign` (`sales_order_id`),
  KEY `installations_sales_invoice_id_foreign` (`sales_invoice_id`),
  KEY `installations_approved_by_foreign` (`approved_by`),
  CONSTRAINT `installations_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `installations_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `installations_sales_invoice_id_foreign` FOREIGN KEY (`sales_invoice_id`) REFERENCES `sales_invoices` (`id`) ON DELETE SET NULL,
  CONSTRAINT `installations_sales_order_id_foreign` FOREIGN KEY (`sales_order_id`) REFERENCES `sales_orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `installations`
--

LOCK TABLES `installations` WRITE;
/*!40000 ALTER TABLE `installations` DISABLE KEYS */;
INSERT INTO `installations` VALUES
(1,'INST-20240101-001',1,1,NULL,'2024-01-20','2024-01-22',3.00,'12 Sunder Nagar, Ahmedabad',NULL,NULL,'RCC Flat Roof','Team Alpha',NULL,'completed','pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,0,'2026-04-06 19:32:40','2026-04-06 19:32:40',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `installations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventories`
--

DROP TABLE IF EXISTS `inventories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `min_quantity` int(11) NOT NULL DEFAULT 5,
  `location` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventories_product_id_foreign` (`product_id`),
  CONSTRAINT `inventories_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventories`
--

LOCK TABLES `inventories` WRITE;
/*!40000 ALTER TABLE `inventories` DISABLE KEYS */;
INSERT INTO `inventories` VALUES
(1,1,36,5,NULL,'2026-04-06 19:32:39','2026-04-06 19:32:39'),
(2,2,33,5,NULL,'2026-04-06 19:32:39','2026-04-06 19:32:39'),
(3,3,43,5,NULL,'2026-04-06 19:32:39','2026-04-06 19:32:39'),
(4,4,16,5,NULL,'2026-04-06 19:32:39','2026-04-06 19:32:39'),
(5,5,22,5,NULL,'2026-04-06 19:32:39','2026-04-06 19:32:39'),
(6,6,29,5,NULL,'2026-04-06 19:32:39','2026-04-06 19:32:39'),
(7,7,42,5,NULL,'2026-04-06 19:32:39','2026-04-06 19:32:39'),
(8,8,26,5,NULL,'2026-04-06 19:32:39','2026-04-06 19:32:39');
/*!40000 ALTER TABLE `inventories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_adjustments`
--

DROP TABLE IF EXISTS `inventory_adjustments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_adjustments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `inventory_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `adjustment_type` enum('add','remove','set') NOT NULL,
  `quantity_before` int(11) NOT NULL,
  `quantity_adjusted` int(11) NOT NULL,
  `quantity_after` int(11) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `adjusted_by` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventory_adjustments_inventory_id_foreign` (`inventory_id`),
  KEY `inventory_adjustments_product_id_foreign` (`product_id`),
  CONSTRAINT `inventory_adjustments_inventory_id_foreign` FOREIGN KEY (`inventory_id`) REFERENCES `inventories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_adjustments_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_adjustments`
--

LOCK TABLES `inventory_adjustments` WRITE;
/*!40000 ALTER TABLE `inventory_adjustments` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_adjustments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
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
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
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
-- Table structure for table `leads`
--

DROP TABLE IF EXISTS `leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `leads` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lead_number` varchar(255) NOT NULL,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `package_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `k_number` varchar(255) DEFAULT NULL COMMENT 'EB K Number / Consumer No',
  `monthly_electricity_bill` decimal(10,2) DEFAULT NULL,
  `required_load_kw` varchar(255) DEFAULT NULL,
  `sanctioned_load` varchar(255) DEFAULT NULL,
  `meter_type` varchar(255) DEFAULT NULL COMMENT 'single_phase, three_phase',
  `property_type` varchar(255) DEFAULT NULL,
  `roof_area_sqft` varchar(255) DEFAULT NULL,
  `has_subsidy` tinyint(1) NOT NULL DEFAULT 0,
  `requires_loan` tinyint(1) NOT NULL DEFAULT 0,
  `address` text NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `lead_source` enum('website','referral','cold_call','social_media','exhibition','other') NOT NULL DEFAULT 'website',
  `estimated_value` decimal(12,2) DEFAULT NULL,
  `roof_type` varchar(255) DEFAULT NULL,
  `system_size` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `follow_up_notes` text DEFAULT NULL,
  `next_follow_up_date` date DEFAULT NULL,
  `sms_sent` tinyint(1) NOT NULL DEFAULT 0,
  `email_sent` tinyint(1) NOT NULL DEFAULT 0,
  `last_contacted_at` timestamp NULL DEFAULT NULL,
  `status` enum('new','contacted','follow_up','mature','converted','lost') NOT NULL DEFAULT 'new',
  `assigned_to` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `subsidy_status` varchar(255) DEFAULT NULL,
  `subsidy_amount` double DEFAULT NULL,
  `subsidy_ref_number` varchar(255) DEFAULT NULL,
  `subsidy_notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `leads_lead_number_unique` (`lead_number`),
  KEY `leads_customer_id_foreign` (`customer_id`),
  KEY `leads_package_id_foreign` (`package_id`),
  CONSTRAINT `leads_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leads_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leads`
--

LOCK TABLES `leads` WRITE;
/*!40000 ALTER TABLE `leads` DISABLE KEYS */;
INSERT INTO `leads` VALUES
(1,'LEAD-20260407-1D3',NULL,1,'Amit Shah','amit.shah@gmail.com','9111111111',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'Bopal, Ahmedabad',NULL,NULL,'website',185000.00,NULL,NULL,NULL,NULL,NULL,0,0,NULL,'new',NULL,'2026-04-06 19:32:39','2026-04-06 19:32:39',NULL,NULL,NULL,NULL),
(2,'LEAD-20260407-AE1',NULL,2,'Neha Joshi','neha.joshi@gmail.com','9222222222',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'Vesu, Surat',NULL,NULL,'referral',290000.00,NULL,NULL,NULL,NULL,NULL,0,0,NULL,'contacted',NULL,'2026-04-06 19:32:39','2026-04-06 19:32:39',NULL,NULL,NULL,NULL),
(3,'LEAD-20260407-8D5',NULL,3,'Vinod Mehta','vinod@mehtagroup.com','9333333333',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'Naroda, Ahmedabad',NULL,NULL,'exhibition',550000.00,NULL,NULL,NULL,NULL,NULL,0,0,NULL,'mature',NULL,'2026-04-06 19:32:39','2026-04-06 19:32:39',NULL,NULL,NULL,NULL),
(4,'LEAD-20260407-AE2',NULL,1,'Sanjay Trivedi','sanjay.trivedi@hotmail.com','9444444444',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'Manjalpur, Vadodara',NULL,NULL,'cold_call',185000.00,NULL,NULL,NULL,NULL,NULL,0,0,NULL,'follow_up',NULL,'2026-04-06 19:32:39','2026-04-06 19:32:39',NULL,NULL,NULL,NULL),
(5,'LEAD-20260407-08E',NULL,2,'Kavita Desai','kavita.desai@gmail.com','9555555555',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'Katargam, Surat',NULL,NULL,'social_media',290000.00,NULL,NULL,NULL,NULL,NULL,0,0,NULL,'new',NULL,'2026-04-06 19:32:39','2026-04-06 19:32:39',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `leads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message_logs`
--

DROP TABLE IF EXISTS `message_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `message_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `channel` varchar(255) NOT NULL COMMENT 'email, sms, whatsapp',
  `to` varchar(255) NOT NULL,
  `to_name` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `body` text NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'sent',
  `related_type` varchar(255) DEFAULT NULL,
  `related_id` bigint(20) unsigned DEFAULT NULL,
  `sent_by` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message_logs`
--

LOCK TABLES `message_logs` WRITE;
/*!40000 ALTER TABLE `message_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `message_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'0001_01_01_000000_create_users_table',1),
(2,'0001_01_01_000001_create_cache_table',1),
(3,'0001_01_01_000002_create_jobs_table',1),
(4,'2024_01_01_000002_create_roles_table',1),
(5,'2024_01_01_000003_create_admin_users_table',1),
(6,'2024_01_01_000003_create_customers_table',1),
(7,'2024_01_01_000004_create_packages_table',1),
(8,'2024_01_01_000005_create_leads_table',1),
(9,'2024_01_01_000006_create_quotations_table',1),
(10,'2024_01_01_000008_create_products_table',1),
(11,'2024_01_01_000009_create_quotation_items_table',1),
(12,'2024_01_01_000009_create_sales_orders_table',1),
(13,'2024_01_01_000010_create_sales_order_items_table',1),
(14,'2024_01_01_000011_create_purchase_orders_table',1),
(15,'2024_01_01_000012_create_purchase_order_items_table',1),
(16,'2024_01_01_000013_create_inventories_table',1),
(17,'2024_01_01_000014_create_inventory_adjustments_table',1),
(18,'2024_01_01_000015_create_installations_table',1),
(19,'2024_01_01_000016_create_service_requests_table',1),
(20,'2024_01_01_000017_create_employees_table',1),
(21,'2024_01_01_000018_create_salary_records_table',1),
(22,'2024_01_01_000019_create_notifications_table',1),
(23,'2024_01_01_000020_create_settings_table',1),
(24,'2024_01_01_000021_create_email_templates_table',1),
(25,'2024_01_01_000022_create_print_formats_table',1),
(26,'2024_01_01_000023_create_product_categories_table',1),
(27,'2024_01_01_000024_enhance_leads_table',1),
(28,'2024_01_01_000025_enhance_installations_table',1),
(29,'2024_01_01_000026_create_sms_configurations_table',1),
(30,'2024_01_01_000027_create_message_logs_table',1),
(31,'2026_03_05_165425_create_teams_table',1),
(32,'2026_03_19_085212_create_blogs_table',1),
(33,'2026_03_19_090447_add_advance_payment_to_sales_orders_table',1),
(34,'2026_03_19_090534_create_sales_invoices_table',1),
(35,'2026_03_19_090535_create_payment_receipts_table',1),
(36,'2026_03_19_090535_create_sales_invoice_items_table',1),
(37,'2026_03_19_094148_add_checklist_to_installations_table',1),
(38,'2026_03_19_094148_add_subsidy_fields_to_leads_table',1),
(39,'2026_03_19_094148_create_site_visits_table',1),
(40,'2026_03_19_094836_add_site_visit_id_to_sales_orders_table',1),
(41,'2026_03_19_110312_add_detailed_fields_to_site_visits',1),
(42,'2026_03_19_113835_add_bom_to_sales_documents',1),
(43,'2026_03_19_160000_add_overview_url_to_blogs_table',1),
(44,'2026_03_19_174500_enhance_installations_for_invoice_and_commissioning',1),
(45,'2026_03_19_181500_add_coordinates_to_leads_site_visits_and_installations',1),
(46,'2026_03_23_041549_create_expenses_table',1),
(47,'2026_03_23_042958_create_customer_loans_table',1),
(48,'2026_03_23_042958_create_customer_subsidies_table',1),
(49,'2026_03_23_042959_create_customer_discoms_table',1),
(50,'2026_03_23_043021_add_requires_loan_to_leads_table',1),
(51,'2026_03_28_105956_enhance_employees_for_contract_and_daily_wages',1),
(52,'2026_03_28_110018_create_daily_wage_records_table',1),
(53,'2026_03_28_110110_add_invoice_attachment_to_purchase_orders',1),
(54,'2026_03_28_110137_enhance_site_visits_for_technician_workflow',1),
(55,'2026_04_01_113545_enhance_customer_discoms_table',1),
(56,'2026_04_01_125024_add_meter_and_app_no_to_customer_discoms',1),
(57,'2026_04_01_125109_add_meter_number_to_customer_discoms',1),
(58,'2026_04_01_125911_add_inverter_serial_details_to_installations',1),
(59,'2026_04_01_150414_add_installation_rate_to_teams_table',1),
(60,'2026_04_02_022509_update_teams_and_employees_for_task_based_pay',1),
(61,'2026_04_02_022814_add_ids_for_task_tracking',1),
(62,'2026_04_02_040258_add_dcr_report_path_to_customer_discoms_table',1),
(63,'2026_04_03_134200_add_rate_per_watt_to_employees',1),
(64,'2026_04_03_134201_add_wattage_to_daily_wage_records',1),
(65,'2026_04_06_081225_add_approval_fields_to_critical_tables',1),
(66,'2026_04_06_100000_add_employee_id_to_admin_users_table',1),
(67,'2026_04_06_120000_add_assignment_fields_to_service_requests_table',1),
(68,'2026_04_06_122609_seed_default_roles_table',1),
(69,'2026_04_06_140000_add_recipient_user_id_to_notifications_table',1),
(70,'2026_04_06_180000_extend_print_format_document_types',1),
(71,'2026_04_06_200000_create_audit_logs_table',1),
(72,'2026_04_06_210000_add_priority_to_notifications_table',1),
(73,'2026_04_06_220000_create_documents_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'general',
  `priority` varchar(20) NOT NULL DEFAULT 'normal',
  `related_id` bigint(20) unsigned DEFAULT NULL,
  `related_type` varchar(255) DEFAULT NULL,
  `action_url` varchar(255) DEFAULT NULL,
  `recipient_user_id` bigint(20) unsigned DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_recipient_user_id_foreign` (`recipient_user_id`),
  CONSTRAINT `notifications_recipient_user_id_foreign` FOREIGN KEY (`recipient_user_id`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES
(1,'Welcome to Solar ERP','System initialized successfully. Admin panel is ready.','general','normal',NULL,NULL,NULL,NULL,0,NULL,'2026-04-06 19:32:40','2026-04-06 19:32:40'),
(2,'Low Stock Alert','MC4 Solar Cable stock is below minimum level.','inventory','normal',NULL,NULL,NULL,NULL,0,NULL,'2026-04-06 19:32:40','2026-04-06 19:32:40'),
(3,'New Lead Received','New inquiry from website - Kavita Desai wants 5kW system quote.','lead','normal',NULL,NULL,NULL,NULL,0,NULL,'2026-04-06 19:32:40','2026-04-06 19:32:40');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `packages`
--

DROP TABLE IF EXISTS `packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `packages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `system_size_kw` decimal(8,2) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `suitable_for` enum('residential','commercial','industrial') NOT NULL DEFAULT 'residential',
  `includes` text DEFAULT NULL,
  `items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`items`)),
  `warranty_years` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `packages`
--

LOCK TABLES `packages` WRITE;
/*!40000 ALTER TABLE `packages` DISABLE KEYS */;
INSERT INTO `packages` VALUES
(1,'3kW Home Starter Pack','Perfect for small homes with basic appliances',3.00,185000.00,'residential','6x 500W Solar Panels, 3kW Inverter, Mounting Structure, MC4 Connectors, 25 Year Panel Warranty, 5 Year Installation Warranty',NULL,25,1,1,'2026-04-06 19:32:39','2026-04-06 19:32:39'),
(2,'5kW Premium Home Pack','Ideal for medium homes, covers all appliances',5.00,290000.00,'residential','10x 500W Solar Panels, 5kW Hybrid Inverter, Battery Backup, Mounting Structure, Smart Monitoring, 25 Year Panel Warranty',NULL,25,1,1,'2026-04-06 19:32:39','2026-04-06 19:32:39'),
(3,'10kW Business Pack','For small businesses and commercial establishments',10.00,550000.00,'commercial','20x 500W Solar Panels, 10kW Commercial Inverter, Mounting Structure, Net Metering, AMC for 5 Years',NULL,25,1,1,'2026-04-06 19:32:39','2026-04-06 19:32:39'),
(4,'25kW Industrial Pack','Large scale industrial solar solution',25.00,1250000.00,'industrial','50x 500W Solar Panels, 25kW Industrial Inverter, Heavy Duty Mounting, SCADA Monitoring, 10 Year AMC',NULL,25,1,0,'2026-04-06 19:32:39','2026-04-06 19:32:39');
/*!40000 ALTER TABLE `packages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
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
-- Table structure for table `payment_receipts`
--

DROP TABLE IF EXISTS `payment_receipts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_receipts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sales_invoice_id` bigint(20) unsigned NOT NULL,
  `receipt_number` varchar(255) NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `payment_method` varchar(255) NOT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_receipts_receipt_number_unique` (`receipt_number`),
  KEY `payment_receipts_sales_invoice_id_foreign` (`sales_invoice_id`),
  CONSTRAINT `payment_receipts_sales_invoice_id_foreign` FOREIGN KEY (`sales_invoice_id`) REFERENCES `sales_invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_receipts`
--

LOCK TABLES `payment_receipts` WRITE;
/*!40000 ALTER TABLE `payment_receipts` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_receipts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `print_formats`
--

DROP TABLE IF EXISTS `print_formats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `print_formats` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `document_type` enum('quotation','sales_order','purchase_order','invoice','salary_slip','discom_application','work_application','dcr_form','installation_certificate','service_report','site_visit_report') NOT NULL,
  `header_html` text DEFAULT NULL,
  `footer_html` text DEFAULT NULL,
  `body_template` longtext NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `paper_size` enum('A4','A5','Letter') NOT NULL DEFAULT 'A4',
  `orientation` enum('portrait','landscape') NOT NULL DEFAULT 'portrait',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `print_formats`
--

LOCK TABLES `print_formats` WRITE;
/*!40000 ALTER TABLE `print_formats` DISABLE KEYS */;
/*!40000 ALTER TABLE `print_formats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_categories`
--

DROP TABLE IF EXISTS `product_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(255) DEFAULT 'fas fa-solar-panel',
  `image` varchar(255) DEFAULT NULL,
  `color` varchar(255) DEFAULT 'orange',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_categories_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_categories`
--

LOCK TABLES `product_categories` WRITE;
/*!40000 ALTER TABLE `product_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `sku` varchar(255) NOT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `category` enum('solar_panel','inverter','battery','mounting','cable','other') NOT NULL,
  `brand` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `specifications` text DEFAULT NULL,
  `purchase_price` decimal(12,2) NOT NULL,
  `selling_price` decimal(12,2) NOT NULL,
  `unit` varchar(255) NOT NULL DEFAULT 'piece',
  `warranty_months` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_sku_unique` (`sku`),
  KEY `products_category_id_foreign` (`category_id`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES
(1,'Luminous 500W Mono PERC Panel','SP-LUM-500',NULL,'solar_panel','Luminous',NULL,NULL,12000.00,15000.00,'piece',300,NULL,1,'2026-04-06 19:32:39','2026-04-06 19:32:39'),
(2,'Waaree 550W Bi-Facial Panel','SP-WAR-550',NULL,'solar_panel','Waaree',NULL,NULL,14000.00,17500.00,'piece',300,NULL,1,'2026-04-06 19:32:39','2026-04-06 19:32:39'),
(3,'Growatt 3kW Hybrid Inverter','INV-GRW-3KW',NULL,'inverter','Growatt',NULL,NULL,28000.00,35000.00,'piece',60,NULL,1,'2026-04-06 19:32:39','2026-04-06 19:32:39'),
(4,'Solis 5kW On-Grid Inverter','INV-SOL-5KW',NULL,'inverter','Solis',NULL,NULL,42000.00,52000.00,'piece',60,NULL,1,'2026-04-06 19:32:39','2026-04-06 19:32:39'),
(5,'Luminous 150Ah Solar Battery','BAT-LUM-150',NULL,'battery','Luminous',NULL,NULL,15000.00,19000.00,'piece',36,NULL,1,'2026-04-06 19:32:39','2026-04-06 19:32:39'),
(6,'GI Mounting Structure 3kW','MNT-GI-3KW',NULL,'mounting','Generic',NULL,NULL,8000.00,11000.00,'set',120,NULL,1,'2026-04-06 19:32:39','2026-04-06 19:32:39'),
(7,'MC4 Solar Cable 4mm 100m','CAB-MC4-4MM',NULL,'cable','Polycab',NULL,NULL,4500.00,6000.00,'roll',NULL,NULL,1,'2026-04-06 19:32:39','2026-04-06 19:32:39'),
(8,'DCB 10kW Commercial Inverter','INV-DCB-10KW',NULL,'inverter','DCB',NULL,NULL,85000.00,105000.00,'piece',60,NULL,1,'2026-04-06 19:32:39','2026-04-06 19:32:39');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_order_items`
--

DROP TABLE IF EXISTS `purchase_order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_order_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_order_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` decimal(8,2) NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `total_price` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_order_items_purchase_order_id_foreign` (`purchase_order_id`),
  KEY `purchase_order_items_product_id_foreign` (`product_id`),
  CONSTRAINT `purchase_order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_order_items_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_order_items`
--

LOCK TABLES `purchase_order_items` WRITE;
/*!40000 ALTER TABLE `purchase_order_items` DISABLE KEYS */;
INSERT INTO `purchase_order_items` VALUES
(1,1,2,'Waaree 550W Bi-Facial Panels x10',10.00,14000.00,140000.00,'2026-04-06 19:32:40','2026-04-06 19:32:40');
/*!40000 ALTER TABLE `purchase_order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_orders`
--

DROP TABLE IF EXISTS `purchase_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `po_number` varchar(255) NOT NULL,
  `supplier_name` varchar(255) NOT NULL,
  `supplier_email` varchar(255) DEFAULT NULL,
  `supplier_phone` varchar(255) DEFAULT NULL,
  `supplier_address` text DEFAULT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `final_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','approved','ordered','received','cancelled') NOT NULL DEFAULT 'pending',
  `expected_delivery` date DEFAULT NULL,
  `received_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `invoice_attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`invoice_attachments`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_orders_po_number_unique` (`po_number`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_orders`
--

LOCK TABLES `purchase_orders` WRITE;
/*!40000 ALTER TABLE `purchase_orders` DISABLE KEYS */;
INSERT INTO `purchase_orders` VALUES
(1,'PO-20240101-001','Waaree Energies Ltd','supply@waaree.com','9800000001',NULL,140000.00,7000.00,147000.00,'received','2024-01-15','2024-01-14',NULL,NULL,'2026-04-06 19:32:40','2026-04-06 19:32:40');
/*!40000 ALTER TABLE `purchase_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quotation_items`
--

DROP TABLE IF EXISTS `quotation_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `quotation_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `quotation_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` decimal(8,2) NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `total_price` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quotation_items_quotation_id_foreign` (`quotation_id`),
  KEY `quotation_items_product_id_foreign` (`product_id`),
  CONSTRAINT `quotation_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `quotation_items_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quotation_items`
--

LOCK TABLES `quotation_items` WRITE;
/*!40000 ALTER TABLE `quotation_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `quotation_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quotations`
--

DROP TABLE IF EXISTS `quotations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `quotations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `quotation_number` varchar(255) NOT NULL,
  `lead_id` bigint(20) unsigned DEFAULT NULL,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `package_id` bigint(20) unsigned DEFAULT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_phone` varchar(255) NOT NULL,
  `customer_address` text NOT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `final_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','sent','approved','rejected','expired') NOT NULL DEFAULT 'pending',
  `valid_until` date DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `bom_items` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `quotations_quotation_number_unique` (`quotation_number`),
  KEY `quotations_lead_id_foreign` (`lead_id`),
  KEY `quotations_customer_id_foreign` (`customer_id`),
  KEY `quotations_package_id_foreign` (`package_id`),
  CONSTRAINT `quotations_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `quotations_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE SET NULL,
  CONSTRAINT `quotations_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quotations`
--

LOCK TABLES `quotations` WRITE;
/*!40000 ALTER TABLE `quotations` DISABLE KEYS */;
/*!40000 ALTER TABLE `quotations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES
(1,'Manager','Regional or office manager with operational management access','[\"dashboard\",\"customers\",\"leads\",\"quotations\",\"sales_orders\",\"site_visits\",\"installations\",\"services\",\"employees\",\"reports\"]',NULL,NULL),
(2,'Technician','Field survey and generic technician access','[\"dashboard\",\"site_visits\",\"installations\",\"services\"]',NULL,NULL),
(3,'Installation Technician','Specialized role for project installations and mounting','[\"dashboard\",\"installations\",\"services\",\"site_visits\"]',NULL,NULL),
(4,'Customer Representative','Sales and customer interaction specialist','[\"dashboard\",\"customers\",\"leads\",\"quotations\",\"site_visits\"]',NULL,NULL),
(5,'admin','Full system administrator access','[\"dashboard\",\"customers\",\"leads\",\"quotations\",\"sales_orders\",\"purchase_orders\",\"products\",\"packages\",\"inventory\",\"installations\",\"services\",\"employees\",\"reports\",\"settings\",\"notifications\",\"roles\"]','2026-04-06 19:32:38','2026-04-06 19:32:38'),
(6,'sales','Standard sales floor access','[\"dashboard\",\"customers\",\"leads\",\"quotations\",\"sales_orders\"]','2026-04-06 19:32:38','2026-04-06 19:32:38');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `salary_records`
--

DROP TABLE IF EXISTS `salary_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `salary_records` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `month` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `basic_salary` decimal(12,2) NOT NULL,
  `allowances` decimal(12,2) NOT NULL DEFAULT 0.00,
  `deductions` decimal(12,2) NOT NULL DEFAULT 0.00,
  `net_salary` decimal(12,2) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_mode` enum('cash','bank_transfer','cheque') NOT NULL,
  `status` enum('paid','pending') NOT NULL DEFAULT 'paid',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `salary_records_employee_id_foreign` (`employee_id`),
  CONSTRAINT `salary_records_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salary_records`
--

LOCK TABLES `salary_records` WRITE;
/*!40000 ALTER TABLE `salary_records` DISABLE KEYS */;
INSERT INTO `salary_records` VALUES
(1,1,4,2026,35000.00,5000.00,1500.00,38500.00,'2026-04-07','bank_transfer','paid',NULL,'2026-04-06 19:32:39','2026-04-06 19:32:39'),
(2,2,4,2026,45000.00,5000.00,1500.00,48500.00,'2026-04-07','bank_transfer','paid',NULL,'2026-04-06 19:32:39','2026-04-06 19:32:39'),
(3,3,4,2026,40000.00,5000.00,1500.00,43500.00,'2026-04-07','bank_transfer','paid',NULL,'2026-04-06 19:32:39','2026-04-06 19:32:39'),
(4,4,4,2026,28000.00,5000.00,1500.00,31500.00,'2026-04-07','bank_transfer','paid',NULL,'2026-04-06 19:32:39','2026-04-06 19:32:39'),
(5,5,4,2026,38000.00,5000.00,1500.00,41500.00,'2026-04-07','bank_transfer','paid',NULL,'2026-04-06 19:32:39','2026-04-06 19:32:39');
/*!40000 ALTER TABLE `salary_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_invoice_items`
--

DROP TABLE IF EXISTS `sales_invoice_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_invoice_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sales_invoice_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `tax_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_invoice_items_sales_invoice_id_foreign` (`sales_invoice_id`),
  KEY `sales_invoice_items_product_id_foreign` (`product_id`),
  CONSTRAINT `sales_invoice_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_invoice_items_sales_invoice_id_foreign` FOREIGN KEY (`sales_invoice_id`) REFERENCES `sales_invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_invoice_items`
--

LOCK TABLES `sales_invoice_items` WRITE;
/*!40000 ALTER TABLE `sales_invoice_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales_invoice_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_invoices`
--

DROP TABLE IF EXISTS `sales_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_invoices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) unsigned NOT NULL,
  `sales_order_id` bigint(20) unsigned DEFAULT NULL,
  `invoice_number` varchar(255) NOT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `sub_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `balance_due` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('unpaid','partially_paid','paid','cancelled') NOT NULL DEFAULT 'unpaid',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `bom_items` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sales_invoices_invoice_number_unique` (`invoice_number`),
  KEY `sales_invoices_customer_id_foreign` (`customer_id`),
  KEY `sales_invoices_sales_order_id_foreign` (`sales_order_id`),
  CONSTRAINT `sales_invoices_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sales_invoices_sales_order_id_foreign` FOREIGN KEY (`sales_order_id`) REFERENCES `sales_orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_invoices`
--

LOCK TABLES `sales_invoices` WRITE;
/*!40000 ALTER TABLE `sales_invoices` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales_invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_order_items`
--

DROP TABLE IF EXISTS `sales_order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_order_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sales_order_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` decimal(8,2) NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `total_price` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_order_items_sales_order_id_foreign` (`sales_order_id`),
  KEY `sales_order_items_product_id_foreign` (`product_id`),
  CONSTRAINT `sales_order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_order_items_sales_order_id_foreign` FOREIGN KEY (`sales_order_id`) REFERENCES `sales_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_order_items`
--

LOCK TABLES `sales_order_items` WRITE;
/*!40000 ALTER TABLE `sales_order_items` DISABLE KEYS */;
INSERT INTO `sales_order_items` VALUES
(1,1,1,'6x Luminous 500W Mono PERC Panel',6.00,15000.00,90000.00,'2026-04-06 19:32:40','2026-04-06 19:32:40'),
(2,1,3,'Growatt 3kW Hybrid Inverter',1.00,35000.00,35000.00,'2026-04-06 19:32:40','2026-04-06 19:32:40'),
(3,1,6,'GI Mounting Structure',1.00,11000.00,11000.00,'2026-04-06 19:32:40','2026-04-06 19:32:40');
/*!40000 ALTER TABLE `sales_order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_orders`
--

DROP TABLE IF EXISTS `sales_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(255) NOT NULL,
  `quotation_id` bigint(20) unsigned DEFAULT NULL,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_phone` varchar(255) NOT NULL,
  `customer_address` text NOT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `final_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `advance_payment` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('confirmed','processing','dispatched','completed','cancelled') NOT NULL DEFAULT 'confirmed',
  `payment_status` enum('pending','partial','paid') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `site_visit_id` bigint(20) unsigned DEFAULT NULL,
  `bom_items` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sales_orders_order_number_unique` (`order_number`),
  KEY `sales_orders_quotation_id_foreign` (`quotation_id`),
  KEY `sales_orders_customer_id_foreign` (`customer_id`),
  KEY `sales_orders_site_visit_id_foreign` (`site_visit_id`),
  CONSTRAINT `sales_orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_orders_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_orders_site_visit_id_foreign` FOREIGN KEY (`site_visit_id`) REFERENCES `site_visits` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_orders`
--

LOCK TABLES `sales_orders` WRITE;
/*!40000 ALTER TABLE `sales_orders` DISABLE KEYS */;
INSERT INTO `sales_orders` VALUES
(1,'SO-20240101-001',NULL,1,'Rajesh Kumar','rajesh@gmail.com','9876543201','12 Sunder Nagar, Ahmedabad',185000.00,9250.00,5000.00,189250.00,0.00,'completed','paid',NULL,'2026-04-06 19:32:39','2026-04-06 19:32:39',NULL,NULL),
(2,'SO-20240201-002',NULL,3,'Mehta Industries','accounts@mehtaind.com','9876543203','Plot 7, GIDC, Anand',550000.00,27500.00,15000.00,562500.00,0.00,'processing','partial',NULL,'2026-04-06 19:32:40','2026-04-06 19:32:40',NULL,NULL);
/*!40000 ALTER TABLE `sales_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_requests`
--

DROP TABLE IF EXISTS `service_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_number` varchar(255) NOT NULL,
  `customer_id` bigint(20) unsigned NOT NULL,
  `installation_id` bigint(20) unsigned DEFAULT NULL,
  `service_type` enum('maintenance','repair','inspection','cleaning','warranty') NOT NULL,
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `status` enum('open','in_progress','resolved','closed') NOT NULL DEFAULT 'open',
  `description` text NOT NULL,
  `scheduled_date` date DEFAULT NULL,
  `assigned_to` varchar(255) DEFAULT NULL,
  `assigned_employee_id` bigint(20) unsigned DEFAULT NULL,
  `assigned_team_id` bigint(20) unsigned DEFAULT NULL,
  `resolution_notes` text DEFAULT NULL,
  `service_cost` decimal(10,2) DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `service_requests_ticket_number_unique` (`ticket_number`),
  KEY `service_requests_customer_id_foreign` (`customer_id`),
  KEY `service_requests_installation_id_foreign` (`installation_id`),
  KEY `service_requests_assigned_team_id_foreign` (`assigned_team_id`),
  CONSTRAINT `service_requests_assigned_team_id_foreign` FOREIGN KEY (`assigned_team_id`) REFERENCES `teams` (`id`) ON DELETE SET NULL,
  CONSTRAINT `service_requests_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `service_requests_installation_id_foreign` FOREIGN KEY (`installation_id`) REFERENCES `installations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_requests`
--

LOCK TABLES `service_requests` WRITE;
/*!40000 ALTER TABLE `service_requests` DISABLE KEYS */;
INSERT INTO `service_requests` VALUES
(1,'SRV-20240301-001',1,1,'maintenance','medium','open','Annual maintenance check required. Customer reporting slight dip in generation.','2026-04-14',NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-06 19:32:40','2026-04-06 19:32:40'),
(2,'SRV-20240302-002',2,NULL,'inspection','high','in_progress','Inverter showing error code. Needs immediate inspection.','2026-04-09',NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-06 19:32:40','2026-04-06 19:32:40');
/*!40000 ALTER TABLE `service_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
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
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `group` varchar(255) NOT NULL DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES
(1,'company_name','Palawat Solar','general','2026-04-06 19:32:39','2026-04-06 19:32:39'),
(2,'company_tagline','Powering a Greener Tomorrow','general','2026-04-06 19:32:39','2026-04-06 19:32:39'),
(3,'company_email','info@solartech.com','general','2026-04-06 19:32:39','2026-04-06 19:32:39'),
(4,'company_phone','+91 98765 43210','general','2026-04-06 19:32:39','2026-04-06 19:32:39'),
(5,'company_address','123 Solar Park, Green City, Gujarat - 380001','general','2026-04-06 19:32:39','2026-04-06 19:32:39'),
(6,'company_gst','24AABCS1429B1Z1','general','2026-04-06 19:32:39','2026-04-06 19:32:39'),
(7,'currency','INR','general','2026-04-06 19:32:39','2026-04-06 19:32:39'),
(8,'currency_symbol','₹','general','2026-04-06 19:32:39','2026-04-06 19:32:39'),
(9,'website_hero_title','Switch to Solar, Save More','website','2026-04-06 19:32:39','2026-04-06 19:32:39'),
(10,'website_hero_subtitle','Premium solar solutions for homes and businesses','website','2026-04-06 19:32:39','2026-04-06 19:32:39'),
(11,'mail_driver','smtp','email','2026-04-06 19:32:39','2026-04-06 19:32:39'),
(12,'mail_host','smtp.gmail.com','email','2026-04-06 19:32:39','2026-04-06 19:32:39'),
(13,'mail_port','587','email','2026-04-06 19:32:39','2026-04-06 19:32:39'),
(14,'mail_from_address','info@solartech.com','email','2026-04-06 19:32:39','2026-04-06 19:32:39'),
(15,'mail_from_name','Palawat Solar','email','2026-04-06 19:32:39','2026-04-06 19:32:39');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_visits`
--

DROP TABLE IF EXISTS `site_visits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_visits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `visit_number` varchar(255) NOT NULL,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `lead_id` bigint(20) unsigned DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `scheduled_at` datetime NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'scheduled',
  `discom_details` varchar(255) DEFAULT NULL,
  `has_new_connection` tinyint(1) NOT NULL DEFAULT 0,
  `roof_details` text DEFAULT NULL,
  `system_size_kw` double DEFAULT NULL,
  `technical_notes` text DEFAULT NULL,
  `assigned_to` varchar(255) DEFAULT NULL,
  `assigned_employee_id` bigint(20) unsigned DEFAULT NULL,
  `team_id` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `completed_by` bigint(20) unsigned DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `completion_notes` text DEFAULT NULL,
  `site_photos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`site_photos`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `shadow_analysis` text DEFAULT NULL,
  `wiring_length_estimate` varchar(255) DEFAULT NULL,
  `ac_dc_location` text DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by` varchar(255) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `site_visits_visit_number_unique` (`visit_number`),
  KEY `site_visits_customer_id_foreign` (`customer_id`),
  KEY `site_visits_lead_id_foreign` (`lead_id`),
  KEY `site_visits_created_by_foreign` (`created_by`),
  KEY `site_visits_completed_by_foreign` (`completed_by`),
  CONSTRAINT `site_visits_completed_by_foreign` FOREIGN KEY (`completed_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `site_visits_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `site_visits_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `site_visits_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_visits`
--

LOCK TABLES `site_visits` WRITE;
/*!40000 ALTER TABLE `site_visits` DISABLE KEYS */;
/*!40000 ALTER TABLE `site_visits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sms_configurations`
--

DROP TABLE IF EXISTS `sms_configurations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sms_configurations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `provider` varchar(255) NOT NULL DEFAULT 'twilio' COMMENT 'twilio, msg91, textlocal, fast2sms',
  `account_sid` varchar(255) DEFAULT NULL,
  `auth_token` varchar(255) DEFAULT NULL,
  `from_number` varchar(255) DEFAULT NULL,
  `api_key` varchar(255) DEFAULT NULL COMMENT 'For msg91, textlocal, fast2sms',
  `sender_id` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT 'IN',
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sms_configurations`
--

LOCK TABLES `sms_configurations` WRITE;
/*!40000 ALTER TABLE `sms_configurations` DISABLE KEYS */;
/*!40000 ALTER TABLE `sms_configurations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sms_logs`
--

DROP TABLE IF EXISTS `sms_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sms_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `to_number` varchar(255) NOT NULL,
  `to_name` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `related_type` varchar(255) DEFAULT NULL,
  `related_id` bigint(20) unsigned DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending' COMMENT 'sent, failed, pending',
  `provider_message_id` varchar(255) DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sms_logs`
--

LOCK TABLES `sms_logs` WRITE;
/*!40000 ALTER TABLE `sms_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `sms_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sms_templates`
--

DROP TABLE IF EXISTS `sms_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sms_templates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL COMMENT 'lead_received, quotation_sent, order_confirmed, installation_scheduled, service_created, follow_up, thank_you',
  `message` text NOT NULL,
  `variables_help` text DEFAULT NULL COMMENT 'JSON list of available variables',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sms_templates`
--

LOCK TABLES `sms_templates` WRITE;
/*!40000 ALTER TABLE `sms_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `sms_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task_payments`
--

DROP TABLE IF EXISTS `task_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `taskable_type` varchar(255) NOT NULL,
  `taskable_id` bigint(20) unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `approval_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approval_remarks` text DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_mode` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_payments_taskable_type_taskable_id_index` (`taskable_type`,`taskable_id`),
  KEY `task_payments_employee_id_foreign` (`employee_id`),
  KEY `task_payments_approved_by_foreign` (`approved_by`),
  CONSTRAINT `task_payments_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `task_payments_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task_payments`
--

LOCK TABLES `task_payments` WRITE;
/*!40000 ALTER TABLE `task_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teams`
--

DROP TABLE IF EXISTS `teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `teams` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `leader_id` bigint(20) unsigned DEFAULT NULL,
  `description` text DEFAULT NULL,
  `installation_rate` decimal(10,2) NOT NULL DEFAULT 0.00,
  `site_visit_rate` decimal(10,2) NOT NULL DEFAULT 0.00,
  `service_rate` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teams`
--

LOCK TABLES `teams` WRITE;
/*!40000 ALTER TABLE `teams` DISABLE KEYS */;
/*!40000 ALTER TABLE `teams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
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

-- Dump completed on 2026-04-07  6:32:49
