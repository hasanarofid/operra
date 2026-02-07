-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 06, 2026 at 07:34 PM
-- Server version: 10.11.15-MariaDB-cll-lve
-- PHP Version: 8.4.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nitw3991_operra`
--

-- --------------------------------------------------------

--
-- Table structure for table `ai_usages`
--

CREATE TABLE `ai_usages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `model_name` varchar(255) NOT NULL,
  `tokens_used` int(11) NOT NULL,
  `estimated_cost` decimal(10,6) NOT NULL,
  `usage_time` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `approvals`
--

CREATE TABLE `approvals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `request_type` varchar(255) NOT NULL,
  `requester_id` bigint(20) UNSIGNED NOT NULL,
  `approver_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `vendor_message_id` varchar(255) DEFAULT NULL,
  `chat_session_id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sender_type` varchar(255) NOT NULL,
  `message_body` text NOT NULL,
  `message_type` varchar(255) NOT NULL DEFAULT 'text',
  `attachment_path` varchar(255) DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `company_id`, `vendor_message_id`, `chat_session_id`, `sender_id`, `sender_type`, `message_body`, `message_type`, `attachment_path`, `read_at`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, 1, NULL, 'customer', 'Halo, saya tertarik dengan produk MacBook Pro M3', 'text', NULL, NULL, '2026-01-28 14:11:41', '2026-01-28 14:21:41'),
(2, NULL, NULL, 1, 4, 'user', 'Halo! Tentu, untuk MacBook Pro M3 stoknya ready kak.', 'text', NULL, NULL, '2026-01-28 14:16:41', '2026-01-28 14:21:41'),
(3, NULL, NULL, 2, NULL, 'customer', 'Halo Jakarta!', 'text', NULL, NULL, '2026-01-28 14:21:42', '2026-01-28 14:21:42'),
(4, NULL, NULL, 3, NULL, 'customer', 'Halo Surabaya!', 'text', NULL, NULL, '2026-01-28 14:21:42', '2026-01-28 14:21:42'),
(5, NULL, NULL, 4, NULL, 'customer', 'Halo, ini adalah simulasi pesan dari Antigravity @ Fri Feb  6 01:41:48 PM WIB 2026', 'text', NULL, NULL, '2026-02-06 06:41:48', '2026-02-06 06:41:48'),
(6, 7, NULL, 4, 12, 'user', 'mas', 'text', NULL, NULL, '2026-02-06 07:50:53', '2026-02-06 07:50:53');

-- --------------------------------------------------------

--
-- Table structure for table `chat_sessions`
--

CREATE TABLE `chat_sessions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `whatsapp_account_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `assigned_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('open','pending','closed','resolved') NOT NULL DEFAULT 'open',
  `last_message_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chat_sessions`
--

INSERT INTO `chat_sessions` (`id`, `company_id`, `whatsapp_account_id`, `customer_id`, `assigned_user_id`, `status`, `last_message_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 2, 4, 'open', '2026-01-28 14:21:41', '2026-01-28 14:21:41', '2026-01-28 14:21:41'),
(2, NULL, 2, 3, 5, 'open', '2026-01-28 14:21:42', '2026-01-28 14:21:42', '2026-01-28 14:21:42'),
(3, NULL, 3, 4, 6, 'open', '2026-01-28 14:21:42', '2026-01-28 14:21:42', '2026-01-28 14:21:42'),
(4, 7, 6, 9, NULL, 'open', '2026-02-06 07:50:53', '2026-02-06 06:41:48', '2026-02-06 07:50:53');

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `enabled_modules` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`enabled_modules`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `pricing_plan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subscription_ends_at` timestamp NULL DEFAULT NULL,
  `is_system_owner` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`, `slug`, `logo`, `address`, `phone`, `email`, `status`, `enabled_modules`, `created_at`, `updated_at`, `pricing_plan_id`, `subscription_ends_at`, `is_system_owner`) VALUES
(1, 'Operra Default', 'operra-default', NULL, NULL, NULL, NULL, 'active', '[\"wa_blast\",\"sales_crm\",\"marketing_crm\",\"customer_service\",\"analytical_crm\"]', '2026-01-28 14:21:40', '2026-01-28 14:21:40', NULL, NULL, 1),
(2, 'Demo Semua Portal', 'all-portals-demo', NULL, NULL, NULL, NULL, 'active', '[\"wa_blast\",\"sales_crm\",\"marketing_crm\",\"customer_service\"]', '2026-01-28 14:21:42', '2026-01-28 14:21:42', 3, '2027-01-28 14:21:42', 0),
(3, 'Demo WhatsApp Blast', 'wa-blast-demo', NULL, NULL, NULL, NULL, 'active', '[\"wa_blast\"]', '2026-01-28 14:21:42', '2026-01-28 14:21:42', 1, '2026-02-28 14:21:42', 0),
(4, 'Demo Sales CRM', 'sales-crm-demo', NULL, NULL, NULL, NULL, 'active', '[\"sales_crm\"]', '2026-01-28 14:21:42', '2026-01-28 14:21:42', NULL, '2026-07-28 14:21:42', 0),
(5, 'Demo Marketing CRM', 'marketing-crm-demo', NULL, NULL, NULL, NULL, 'active', '[\"marketing_crm\"]', '2026-01-28 14:21:42', '2026-01-28 14:21:42', NULL, NULL, 0),
(6, 'Demo Customer Support', 'support-crm-demo', NULL, NULL, NULL, NULL, 'active', '[\"customer_service\"]', '2026-01-28 14:21:42', '2026-01-28 14:21:42', NULL, NULL, 0),
(7, 'hasan crm', 'hasan-crm', NULL, NULL, NULL, NULL, 'active', '[\"wa_blast\"]', '2026-01-28 14:22:48', '2026-02-04 00:32:38', 1, '2026-03-03 17:00:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'lead',
  `customer_status_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lead_score` int(11) NOT NULL DEFAULT 0,
  `lead_source` varchar(255) DEFAULT NULL,
  `assigned_to` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `company_id`, `name`, `email`, `phone`, `address`, `status`, `customer_status_id`, `lead_score`, `lead_source`, `assigned_to`, `created_at`, `updated_at`) VALUES
(1, 1, 'Budi Santoso', 'budi@gmail.com', '08123456789', 'Surabaya', 'lead', NULL, 24, NULL, NULL, '2026-01-28 14:21:40', '2026-01-28 14:21:42'),
(2, NULL, 'Calon Lead 1', NULL, '628999888777', NULL, 'lead', NULL, 83, 'whatsapp', NULL, '2026-01-28 14:21:41', '2026-01-28 14:21:42'),
(3, 1, 'Customer Jakarta 1', NULL, '6285555555555', NULL, 'lead', NULL, 29, NULL, NULL, '2026-01-28 14:21:42', '2026-01-28 14:21:42'),
(4, 1, 'Customer Surabaya 1', NULL, '6286666666666', NULL, 'lead', NULL, 81, NULL, NULL, '2026-01-28 14:21:42', '2026-01-28 14:21:42'),
(5, 7, 'Client 1', 'client1@gmail.com', '0882010366931', 'surabaya', 'lead', NULL, 0, 'whatsapp', NULL, '2026-02-04 14:36:51', '2026-02-04 14:36:51'),
(6, 7, 'Barra', 'barra@gmail.com', '0882009326548', 'Surabaya', 'lead', NULL, 0, 'whatsapp', NULL, '2026-02-04 14:52:46', '2026-02-04 14:52:46'),
(7, 7, 'reny', 'reny@gmail.com', '6281357677221', 'surabaya', 'lead', NULL, 0, 'whatsapp', NULL, '2026-02-05 14:59:30', '2026-02-05 14:59:30'),
(8, 7, 'agung', 'agung@gmail.com', '085559000076', 'lamongan', 'lead', NULL, 0, 'whatsapp', NULL, '2026-02-05 15:02:30', '2026-02-05 15:02:30'),
(9, 7, 'Agung', NULL, '628123456789', NULL, 'lead', NULL, 0, 'whatsapp', NULL, '2026-02-06 06:41:48', '2026-02-06 06:57:57');

-- --------------------------------------------------------

--
-- Table structure for table `customer_statuses`
--

CREATE TABLE `customer_statuses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `color` varchar(255) NOT NULL DEFAULT '#cbd5e1',
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_statuses`
--

INSERT INTO `customer_statuses` (`id`, `company_id`, `name`, `color`, `order`, `created_at`, `updated_at`) VALUES
(1, 7, 'Success', '#25d366', 1, '2026-02-04 14:37:36', '2026-02-04 14:37:36');

-- --------------------------------------------------------

--
-- Table structure for table `external_apps`
--

CREATE TABLE `external_apps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `app_key` varchar(255) NOT NULL,
  `app_secret` varchar(255) NOT NULL,
  `webhook_url` varchar(255) DEFAULT NULL,
  `widget_settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`widget_settings`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventories`
--

CREATE TABLE `inventories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `item_name` varchar(255) NOT NULL,
  `sku` varchar(255) NOT NULL,
  `stock_quantity` int(11) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `invoice_number` varchar(255) NOT NULL,
  `sales_order_id` bigint(20) UNSIGNED NOT NULL,
  `due_date` date NOT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `payment_status` enum('unpaid','partial','paid') NOT NULL DEFAULT 'unpaid',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `company_id`, `invoice_number`, `sales_order_id`, `due_date`, `total_amount`, `payment_status`, `created_at`, `updated_at`) VALUES
(1, 1, 'INV-20260106-001', 1, '2026-02-04', 28000000.00, 'unpaid', '2026-01-28 14:21:40', '2026-01-28 14:21:40');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(1, 'default', '{\"uuid\":\"5049f942-1ce8-4e01-9b86-cb926fca6eb5\",\"displayName\":\"App\\\\Events\\\\NewChatIncoming\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":17:{s:5:\\\"event\\\";O:26:\\\"App\\\\Events\\\\NewChatIncoming\\\":2:{s:7:\\\"session\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\ChatSession\\\";s:2:\\\"id\\\";i:4;s:9:\\\"relations\\\";a:3:{i:0;s:8:\\\"customer\\\";i:1;s:15:\\\"whatsappAccount\\\";i:2;s:12:\\\"assignedUser\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:7:\\\"message\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\ChatMessage\\\";s:2:\\\"id\\\";i:5;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:23:\\\"deleteWhenMissingModels\\\";b:1;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1770360108,\"delay\":null}', 0, NULL, 1770360108, 1770360108);

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

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
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leads_requests`
--

CREATE TABLE `leads_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `business_type` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'new',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `marketing_automations`
--

CREATE TABLE `marketing_automations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `trigger_event` varchar(255) NOT NULL,
  `actions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`actions`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `marketing_automations`
--

INSERT INTO `marketing_automations` (`id`, `company_id`, `name`, `trigger_event`, `actions`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'New Lead Auto-Reply', 'customer_registered', '[{\"type\":\"wait\",\"duration\":\"5m\"},{\"type\":\"send_whatsapp\",\"template\":\"welcome_msg\"}]', 1, '2026-01-28 14:21:42', '2026-01-28 14:21:42');

-- --------------------------------------------------------

--
-- Table structure for table `marketing_blasts`
--

CREATE TABLE `marketing_blasts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `marketing_campaign_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `channel` varchar(255) NOT NULL,
  `status` enum('pending','sending','sent','failed') NOT NULL DEFAULT 'pending',
  `scheduled_at` timestamp NULL DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `total_recipients` int(11) NOT NULL DEFAULT 0,
  `success_count` int(11) NOT NULL DEFAULT 0,
  `failed_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `marketing_blasts`
--

INSERT INTO `marketing_blasts` (`id`, `company_id`, `marketing_campaign_id`, `subject`, `content`, `channel`, `status`, `scheduled_at`, `sent_at`, `total_recipients`, `success_count`, `failed_count`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'Diskon 50% Menanti Anda!', 'Halo pelanggan setia, nikmati diskon khusus akhir tahun...', 'email', 'sent', NULL, '2026-01-26 14:21:42', 1000, 980, 20, '2026-01-28 14:21:42', '2026-01-28 14:21:42');

-- --------------------------------------------------------

--
-- Table structure for table `marketing_campaigns`
--

CREATE TABLE `marketing_campaigns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `status` enum('draft','active','paused','completed') NOT NULL DEFAULT 'draft',
  `start_date` timestamp NULL DEFAULT NULL,
  `end_date` timestamp NULL DEFAULT NULL,
  `target_audience_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `marketing_campaigns`
--

INSERT INTO `marketing_campaigns` (`id`, `company_id`, `name`, `description`, `type`, `status`, `start_date`, `end_date`, `target_audience_count`, `created_at`, `updated_at`) VALUES
(1, 1, 'Year End Sale 2025', 'Promo akhir tahun untuk produk IT', 'Email', 'active', '2026-01-28 14:21:42', '2026-02-28 14:21:42', 1200, '2026-01-28 14:21:42', '2026-01-28 14:21:42'),
(2, 1, 'WhatsApp Welcome Series', 'Otomasi sambutan pelanggan baru via WA', 'WhatsApp', 'active', '2026-01-18 14:21:42', NULL, 500, '2026-01-28 14:21:42', '2026-01-28 14:21:42');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_01_05_041358_create_permission_tables', 1),
(5, '2026_01_05_041609_create_operational_tables', 1),
(6, '2026_01_06_083931_create_erp_tables', 1),
(7, '2026_01_07_113328_create_crm_wa_tables', 1),
(8, '2026_01_12_064823_create_companies_table', 1),
(9, '2026_01_12_064828_add_company_id_to_existing_tables', 1),
(10, '2026_01_12_071315_create_promotions_table', 1),
(11, '2026_01_13_041237_create_marketing_tables', 1),
(12, '2026_01_13_042511_create_support_tables', 1),
(13, '2026_01_15_072821_add_subscription_fields_to_companies_table', 1),
(14, '2026_01_15_084629_add_onboarding_field_to_users_table', 1),
(15, '2026_01_18_125047_add_whatsapp_blast_features_to_portal', 1),
(16, '2026_01_20_025227_create_whatsapp_auto_replies_table', 1),
(17, '2026_01_27_122801_create_payments_table', 1),
(18, '2026_01_28_211613_add_company_id_to_agents_and_messages', 1),
(19, '2026_02_04_220046_create_webhook_logs_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(1, 'App\\Models\\User', 7),
(1, 'App\\Models\\User', 8),
(1, 'App\\Models\\User', 9),
(1, 'App\\Models\\User', 10),
(1, 'App\\Models\\User', 11),
(1, 'App\\Models\\User', 12),
(2, 'App\\Models\\User', 2),
(3, 'App\\Models\\User', 3),
(4, 'App\\Models\\User', 4),
(4, 'App\\Models\\User', 5),
(4, 'App\\Models\\User', 6);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `order_number` varchar(255) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `status` enum('pending','processing','completed','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `months` int(11) NOT NULL DEFAULT 1,
  `proof_of_payment` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `verified_at` timestamp NULL DEFAULT NULL,
  `verified_by` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `company_id`, `amount`, `months`, `proof_of_payment`, `status`, `payment_date`, `verified_at`, `verified_by`, `notes`, `created_at`, `updated_at`) VALUES
(1, 7, 149000.00, 1, 'payment-proofs/9CafuBz5WpsWPhwZ1mascXY4G429OUEp5BBtRwod.jpg', 'pending', '2026-02-04 00:19:08', NULL, NULL, NULL, '2026-02-04 00:19:08', '2026-02-04 00:19:08');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'manage orders', 'web', '2026-01-28 14:21:40', '2026-01-28 14:21:40'),
(2, 'manage inventory', 'web', '2026-01-28 14:21:40', '2026-01-28 14:21:40'),
(3, 'view activities', 'web', '2026-01-28 14:21:40', '2026-01-28 14:21:40'),
(4, 'handle approvals', 'web', '2026-01-28 14:21:40', '2026-01-28 14:21:40'),
(5, 'view analytics', 'web', '2026-01-28 14:21:40', '2026-01-28 14:21:40'),
(6, 'monitor ai cost', 'web', '2026-01-28 14:21:40', '2026-01-28 14:21:40'),
(7, 'manage settings', 'web', '2026-01-28 14:21:40', '2026-01-28 14:21:40'),
(8, 'manage master data', 'web', '2026-01-28 14:21:40', '2026-01-28 14:21:40'),
(9, 'manage sales', 'web', '2026-01-28 14:21:40', '2026-01-28 14:21:40'),
(10, 'manage stock', 'web', '2026-01-28 14:21:40', '2026-01-28 14:21:40'),
(11, 'manage whatsapp accounts', 'web', '2026-01-28 14:21:40', '2026-01-28 14:21:40'),
(12, 'manage agents', 'web', '2026-01-28 14:21:40', '2026-01-28 14:21:40'),
(13, 'view all chats', 'web', '2026-01-28 14:21:40', '2026-01-28 14:21:40'),
(14, 'view assigned chats', 'web', '2026-01-28 14:21:40', '2026-01-28 14:21:40'),
(15, 'send messages', 'web', '2026-01-28 14:21:40', '2026-01-28 14:21:40'),
(16, 'manage leads', 'web', '2026-01-28 14:21:40', '2026-01-28 14:21:40'),
(17, 'manage leads requests', 'web', '2026-01-28 14:21:40', '2026-01-28 14:21:40');

-- --------------------------------------------------------

--
-- Table structure for table `pricing_plans`
--

CREATE TABLE `pricing_plans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `billing_cycle` varchar(255) NOT NULL DEFAULT 'monthly',
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`features`)),
  `is_popular` tinyint(1) NOT NULL DEFAULT 0,
  `badge` varchar(255) DEFAULT NULL,
  `cta_text` varchar(255) NOT NULL DEFAULT 'Mulai Sekarang',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pricing_plans`
--

INSERT INTO `pricing_plans` (`id`, `name`, `slug`, `price`, `billing_cycle`, `features`, `is_popular`, `badge`, `cta_text`, `created_at`, `updated_at`) VALUES
(1, 'Starter (UMKM)', 'starter', 149000.00, 'monthly', '[\"1 Akun WhatsApp\",\"Manajemen Lead Dasar\",\"Shared Inbox (2 Agent)\",\"Follow-up Otomatis\",\"Laporan Harian via WA\"]', 0, 'Cocok untuk Pemula', 'Pilih Starter', '2026-01-28 14:21:40', NULL),
(2, 'Business Pro', 'business-pro', 399000.00, 'monthly', '[\"Multi-Account WhatsApp (Up to 5)\",\"Sales Pipeline & Deal Tracking\",\"Unlimited Agents\",\"WhatsApp Blast (Scheduler)\",\"API Integration Ready\",\"Priority Support\"]', 1, 'Paling Populer', 'Mulai Uji Coba Gratis', '2026-01-28 14:21:40', NULL),
(3, 'Enterprise Custom', 'enterprise', 0.00, 'monthly', '[\"On-Premise Deployment\",\"Full White-Label Branding\",\"Custom Module Development\",\"Dedicated Server & Database\",\"SLA Guarantee 99.9%\",\"Account Manager Khusus\"]', 0, 'Untuk Skala Besar', 'Hubungi Kami', '2026-01-28 14:21:40', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `sku` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `purchase_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `selling_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `min_stock` int(11) NOT NULL DEFAULT 5,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `company_id`, `name`, `sku`, `description`, `purchase_price`, `selling_price`, `min_stock`, `created_at`, `updated_at`) VALUES
(1, 1, 'MacBook Pro M3', 'LAP-MBP-M3', 'Laptop Apple terbaru', 22000000.00, 28000000.00, 5, '2026-01-28 14:21:40', '2026-01-28 14:21:40'),
(2, 1, 'iPhone 15 Pro', 'PHN-I15P', 'Smartphone Apple terbaru', 18000000.00, 21000000.00, 10, '2026-01-28 14:21:40', '2026-01-28 14:21:40');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'super-admin', 'web', '2026-01-28 14:21:40', '2026-01-28 14:21:40'),
(2, 'manager', 'web', '2026-01-28 14:21:40', '2026-01-28 14:21:40'),
(3, 'staff', 'web', '2026-01-28 14:21:40', '2026-01-28 14:21:40'),
(4, 'sales', 'web', '2026-01-28 14:21:40', '2026-01-28 14:21:40');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(2, 1),
(2, 2),
(2, 3),
(3, 1),
(3, 2),
(4, 1),
(4, 2),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(9, 2),
(9, 3),
(10, 1),
(10, 2),
(11, 1),
(12, 1),
(12, 2),
(13, 1),
(13, 2),
(14, 1),
(14, 4),
(15, 1),
(15, 2),
(15, 4),
(16, 1),
(16, 2),
(16, 4),
(17, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sales_orders`
--

CREATE TABLE `sales_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `so_number` varchar(255) NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `order_date` date NOT NULL,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('draft','confirmed','cancelled') NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales_orders`
--

INSERT INTO `sales_orders` (`id`, `company_id`, `so_number`, `customer_id`, `order_date`, `total_amount`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'SO-20260106-001', 1, '2026-01-28', 28000000.00, 'confirmed', '2026-01-28 14:21:40', '2026-01-28 14:21:40');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('2BeuzTnw4AyHCtukWhPFnUV6ZfOi2QKju3DbcYno', NULL, '173.212.212.36', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoic0o4S3FpOVdmajJwRDg2eDlNeGxaTXVJR29Hc1BWUUtDa3BIMHd4bSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770357620),
('5ggRsnNc33VhvPKXupNl5jYWpmPb66UIEWQAj4YN', 12, '103.146.61.90', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSkpSQzdIWjlKQ0pGRnVhSnowYnFOWE1iU1E0SnZzcXdvTmM3MGQzeiI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vb3BlcnJhLmhhc2FuYXJvZmlkLnNpdGUiO3M6NToicm91dGUiO3M6Nzoid2VsY29tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770364695),
('DaDtI18WIXSUtEjnnJLaRzjLt9HUC39ar138oncl', 12, '103.146.61.93', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNjd1b2kyNUpsSGZCUW9LQ2ZzMG9Ea041VnVxeU9acm5vZ1JaY2dBayI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTA6Imh0dHBzOi8vb3BlcnJhLmhhc2FuYXJvZmlkLnNpdGUvY3JtLXdhLWJsYXN0L2luYm94IjtzOjU6InJvdXRlIjtzOjEyOiJjcm0ud2EuaW5ib3giO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxMjt9', 1770368535),
('eYKFXyForIFmTSIvQ1Y1h7khvLgllBkXIUbSkKfN', 12, '103.119.140.251', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiR0RkaXBJVjhsOEd1THZiY29QZURRQk0xQWhrMDNVVFVPbG5UNms2dSI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vb3BlcnJhLmhhc2FuYXJvZmlkLnNpdGUiO3M6NToicm91dGUiO3M6Nzoid2VsY29tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770375531),
('HL89n9ziZ1sy2qEOH2K9qBwzUNu7ugxO6Z5NZXZ0', 12, '36.81.172.193', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiN0JESjZZWXZsNng4dWVQcDIydzB5MXNNU2tCc1RWcmI2TGIydmhNNSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vb3BlcnJhLmhhc2FuYXJvZmlkLnNpdGUiO3M6NToicm91dGUiO3M6Nzoid2VsY29tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjEyO30=', 1770381157),
('hQbPr5fud6dDKrHVDkN8H7BcLqHGEWRdLY6Y2nBB', NULL, '43.131.36.84', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibHlHWVNaZVEydTIwUEVUWEh6N3RPOG82eGlST212cDVadWpzbmQ0QyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly9vcGVycmEuaGFzYW5hcm9maWQuc2l0ZSI7czo1OiJyb3V0ZSI7czo3OiJ3ZWxjb21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770358662),
('QXtguS0SlxhZCl1Q9PC2SMRC9qLl9LGAWXYjuqVg', 1, '103.146.61.93', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNGpwb3hCWHRZekJtdFFvbUR4a2FwVVFldU5zWktaMEZFd295N3N1ciI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTc6Imh0dHA6Ly9vcGVycmEuaGFzYW5hcm9maWQuc2l0ZS9hZG1pbi9tb25pdG9yaW5nL2NvbXBhbmllcyI7czo1OiJyb3V0ZSI7czoyODoiYWRtaW4uc3lzdGVtLmNvbXBhbmllcy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1770361306),
('RG0mtIxOa64NyveVPw3mNj8p0L5iG8Uo4XF8viZd', NULL, '195.178.110.31', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 Chrome/122.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidTA0bXB2WkhxOG94S3BleTd4dWh5OVRYTzhxSnpZSHNQU1NrRWc2WCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vb3BlcnJhLmhhc2FuYXJvZmlkLnNpdGUiO3M6NToicm91dGUiO3M6Nzoid2VsY29tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770376101);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `company_id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 1, 'company_name', 'PT. Operra Solusi Digital', '2026-01-28 14:21:40', '2026-01-28 14:21:40'),
(2, 1, 'company_address', 'Jl. Teknologi No. 1, Jakarta', '2026-01-28 14:21:40', '2026-01-28 14:21:40'),
(3, 1, 'company_phone', '021-12345678', '2026-01-28 14:21:40', '2026-01-28 14:21:40'),
(4, 1, 'company_email', 'info@operra.site', '2026-01-28 14:21:40', '2026-01-28 14:21:40'),
(5, 1, 'currency', 'IDR', '2026-01-28 14:21:40', '2026-01-28 14:21:40'),
(6, NULL, 'meta_access_token', '', '2026-01-28 14:23:29', '2026-01-28 14:23:29'),
(7, NULL, 'meta_webhook_verify_token', '', '2026-01-28 14:23:29', '2026-01-28 14:23:29'),
(8, NULL, 'meta_app_id', '', '2026-01-28 14:23:29', '2026-01-28 14:23:29'),
(9, NULL, 'meta_waba_id', '', '2026-01-28 14:23:29', '2026-01-28 14:23:29');

-- --------------------------------------------------------

--
-- Table structure for table `staff_activities`
--

CREATE TABLE `staff_activities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `activity_type` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `type` enum('in','out','mutation') NOT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_movements`
--

INSERT INTO `stock_movements` (`id`, `company_id`, `product_id`, `warehouse_id`, `quantity`, `type`, `reference`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 20, 'in', 'Initial Stock', '2026-01-28 14:21:40', '2026-01-28 14:21:40');

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `plan_name` varchar(255) NOT NULL,
  `monthly_price` decimal(15,2) NOT NULL,
  `active_customers` int(11) NOT NULL DEFAULT 0,
  `mrr` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `company_id`, `name`, `contact_person`, `phone`, `address`, `created_at`, `updated_at`) VALUES
(1, 1, 'Apple Distributor Indo', 'Andi', '021-88888', 'Jakarta', '2026-01-28 14:21:40', '2026-01-28 14:21:40');

-- --------------------------------------------------------

--
-- Table structure for table `support_knowledge_base`
--

CREATE TABLE `support_knowledge_base` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `support_knowledge_base`
--

INSERT INTO `support_knowledge_base` (`id`, `company_id`, `title`, `slug`, `content`, `category`, `is_published`, `created_at`, `updated_at`) VALUES
(1, 1, 'Cara Reset Password', 'cara-reset-password', 'Langkah-langkah untuk melakukan reset password akun Anda...', 'Akun', 1, '2026-01-28 14:21:42', '2026-01-28 14:21:42'),
(2, 1, 'Panduan Integrasi WhatsApp Official', 'integrasi-wa-official', 'Dokumentasi lengkap mengenai cara menghubungkan nomor WhatsApp Official ke portal Operra...', 'Integrasi', 1, '2026-01-28 14:21:42', '2026-01-28 14:21:42');

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `assigned_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ticket_number` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `status` enum('open','in_progress','resolved','closed') NOT NULL DEFAULT 'open',
  `sla_due_at` timestamp NULL DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `support_tickets`
--

INSERT INTO `support_tickets` (`id`, `company_id`, `customer_id`, `assigned_user_id`, `ticket_number`, `subject`, `description`, `priority`, `status`, `sla_due_at`, `resolved_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 3, 'TKT-20260113-001', 'Masalah Login Dashboard', 'Saya tidak bisa login meskipun password sudah benar.', 'high', 'open', '2026-01-28 18:21:42', NULL, '2026-01-28 14:21:42', '2026-01-28 14:21:42'),
(2, 1, 4, NULL, 'TKT-20260113-002', 'Pertanyaan Refund Dana', 'Berapa lama proses refund dana jika transaksi dibatalkan?', 'medium', 'in_progress', '2026-01-29 14:21:42', NULL, '2026-01-28 14:21:42', '2026-01-28 14:21:42');

-- --------------------------------------------------------

--
-- Table structure for table `support_ticket_messages`
--

CREATE TABLE `support_ticket_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `support_ticket_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `message` text NOT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `has_completed_onboarding` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `company_id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `has_completed_onboarding`) VALUES
(1, 1, 'Admin Operra', 'admin@operra.com', '2026-01-28 14:21:40', '$2y$12$FEHkMoPUIjEgUOHMx6JJVOQQ.MaLS7vme2Kx8DneeT4APw5uMQswu', NULL, '2026-01-28 14:21:40', '2026-02-04 00:19:28', 1),
(2, 1, 'Manager Demo', 'manager@operra.com', '2026-01-28 14:21:41', '$2y$12$0cs8Gs0HMX4QSLCMqyt7NugDWVdjHvPrnnTwVmPTdzEQGOzywse3m', NULL, '2026-01-28 14:21:41', '2026-01-28 14:21:41', 0),
(3, 1, 'Staff Demo', 'staff@operra.com', '2026-01-28 14:21:41', '$2y$12$vRMD8RGomtnHVLjKqP02U.B7Xz9kDmOVs/x0dD3vwTNIR9EbNbtXm', NULL, '2026-01-28 14:21:41', '2026-01-28 14:21:41', 0),
(4, 1, 'Sales Ahmad', 'sales1@operra.com', '2026-01-28 14:21:41', '$2y$12$zgSwPlZ5swuRjjbmZQ7.LupRNIwyZu542yex9aFHCqnoJIAGlkr2e', NULL, '2026-01-28 14:21:41', '2026-01-28 14:21:41', 0),
(5, 1, 'Sales Jakarta', 'sales.jakarta@operra.com', '2026-01-28 14:21:42', '$2y$12$8Lw4VNic.2Oo4nXWHhU6eeRKzm.bbpe9WfmCyI3fcwl0goZQ1ha4O', NULL, '2026-01-28 14:21:42', '2026-01-28 14:21:42', 0),
(6, 1, 'Sales Surabaya', 'sales.surabaya@operra.com', '2026-01-28 14:21:42', '$2y$12$xFAGcDj/Nadt2yZDbo3bnuOTuSwK5x072m857WQmhzIxUCAS5Q38S', NULL, '2026-01-28 14:21:42', '2026-01-28 14:21:42', 0),
(7, 2, 'User Demo (Semua Portal)', 'demo-all@operra.id', '2026-01-28 14:21:42', '$2y$12$y/fY4V.b9dxaazdVvDV3IufVNj.7UVqQeA9YNyO6GO/VyGctC/e2K', NULL, '2026-01-28 14:21:42', '2026-01-28 14:21:42', 0),
(8, 3, 'User Demo (WA Blast)', 'demo-wa@operra.id', '2026-01-28 14:21:42', '$2y$12$y/fY4V.b9dxaazdVvDV3IufVNj.7UVqQeA9YNyO6GO/VyGctC/e2K', NULL, '2026-01-28 14:21:42', '2026-01-28 14:21:42', 0),
(9, 4, 'User Demo (Sales CRM)', 'demo-sales@operra.id', '2026-01-28 14:21:42', '$2y$12$y/fY4V.b9dxaazdVvDV3IufVNj.7UVqQeA9YNyO6GO/VyGctC/e2K', NULL, '2026-01-28 14:21:42', '2026-01-28 14:21:42', 0),
(10, 5, 'User Demo (Marketing)', 'demo-marketing@operra.id', '2026-01-28 14:21:42', '$2y$12$y/fY4V.b9dxaazdVvDV3IufVNj.7UVqQeA9YNyO6GO/VyGctC/e2K', NULL, '2026-01-28 14:21:42', '2026-01-28 14:21:42', 0),
(11, 6, 'User Demo (Customer Support)', 'demo-support@operra.id', '2026-01-28 14:21:42', '$2y$12$y/fY4V.b9dxaazdVvDV3IufVNj.7UVqQeA9YNyO6GO/VyGctC/e2K', NULL, '2026-01-28 14:21:42', '2026-01-28 14:21:42', 0),
(12, 7, 'hasan arofid', 'hasanarofid@gmail.com', '2026-01-28 14:23:06', '$2y$12$oaxz4ZnNtHGa5aUni2.L1.yv7GyfuYVe7UUBnc4XgzBBGXs1BKdfy', 'rZjwGFeSOC8UpQeVBreC4xRorFEXugEijcKh1T547CZeEc04OEZFaQiJ1Gij', '2026-01-28 14:22:49', '2026-02-04 08:32:13', 1);

-- --------------------------------------------------------

--
-- Table structure for table `warehouses`
--

CREATE TABLE `warehouses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `warehouses`
--

INSERT INTO `warehouses` (`id`, `company_id`, `name`, `location`, `created_at`, `updated_at`) VALUES
(1, 1, 'Gudang Utama', 'Jakarta', '2026-01-28 14:21:40', '2026-01-28 14:21:40'),
(2, 1, 'Gudang Cabang', 'Bandung', '2026-01-28 14:21:40', '2026-01-28 14:21:40');

-- --------------------------------------------------------

--
-- Table structure for table `webhook_logs`
--

CREATE TABLE `webhook_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `provider` varchar(255) DEFAULT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `headers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`headers`)),
  `sender_ip` varchar(255) DEFAULT NULL,
  `status_code` int(11) NOT NULL DEFAULT 200,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `webhook_logs`
--

INSERT INTO `webhook_logs` (`id`, `provider`, `payload`, `headers`, `sender_ip`, `status_code`, `created_at`, `updated_at`) VALUES
(1, 'meta', '{\"object\":\"whatsapp_business_account\",\"entry\":[]}', '{\"accept\":[\"*\\/*\"],\"content-type\":[\"application\\/json\"],\"content-length\":[\"52\"],\"host\":[\"operra.hasanarofid.site\"],\"user-agent\":[\"curl\\/8.5.0\"]}', '103.119.140.251', 200, '2026-02-04 15:20:54', '2026-02-04 15:20:54'),
(2, 'unknown', '[]', '{\"accept\":[\"*\\/*\"],\"content-length\":[\"0\"],\"host\":[\"operra.hasanarofid.site\"],\"user-agent\":[\"curl\\/8.5.0\"]}', '103.119.140.251', 200, '2026-02-04 15:21:45', '2026-02-04 15:21:45'),
(3, 'unknown', '{\"hub_mode\":\"subscribe\",\"hub_verify_token\":\"operra_secret_token\",\"hub_challenge\":\"123456\"}', '{\"accept-encoding\":[\"gzip\"],\"host\":[\"operra.hasanarofid.site\"],\"user-agent\":[\"Go-http-client\\/2.0\"]}', '36.81.172.193', 200, '2026-02-05 15:04:52', '2026-02-05 15:04:52'),
(4, 'meta', '{\"object\":\"whatsapp_business_account\",\"entry\":[{\"id\":\"123456789\",\"changes\":[{\"value\":{\"messaging_product\":\"whatsapp\",\"metadata\":{\"display_phone_number\":\"OPERRA_DEMO\",\"phone_number_id\":\"62881026697527\"},\"contacts\":[{\"profile\":{\"name\":\"Simulation Tester\"},\"wa_id\":\"628123456789\"}],\"messages\":[{\"from\":\"628123456789\",\"id\":\"wamid.simulated_1770360108\",\"timestamp\":\"1770360108\",\"text\":{\"body\":\"Halo, ini adalah simulasi pesan dari Antigravity @ Fri Feb  6 01:41:48 PM WIB 2026\"},\"type\":\"text\"}]},\"field\":\"messages\"}]}]}', '{\"accept\":[\"*\\/*\"],\"content-type\":[\"application\\/json\"],\"content-length\":[\"931\"],\"host\":[\"operra.hasanarofid.site\"],\"user-agent\":[\"curl\\/8.5.0\"]}', '103.146.61.93', 200, '2026-02-06 06:41:47', '2026-02-06 06:41:47'),
(5, 'meta', '{\"object\":\"whatsapp_business_account\",\"entry\":[{\"id\":\"1832090140829270\",\"changes\":[{\"value\":{\"messaging_product\":\"whatsapp\",\"metadata\":{\"display_phone_number\":\"6281933715777\",\"phone_number_id\":\"995428430312789\"},\"statuses\":[{\"id\":\"wamid.HBgONjI4ODEwMjY2OTc1MjcVAgARGBI0QTgyRUUzOThERTY4RDNDNjcA\",\"status\":\"failed\",\"timestamp\":\"1770362427\",\"recipient_id\":\"62881026697527\",\"errors\":[{\"code\":131047,\"title\":\"Re-engagement message\",\"message\":\"Re-engagement message\",\"error_data\":{\"details\":\"Message failed to send because more than 24 hours have passed since the customer last replied to this number.\"},\"href\":\"https:\\/\\/developers.facebook.com\\/docs\\/whatsapp\\/cloud-api\\/support\\/error-codes\\/\"}]}]},\"field\":\"messages\"}]}]}', '{\"accept\":[\"*\\/*\"],\"accept-encoding\":[\"deflate, gzip\"],\"content-type\":[\"application\\/json\"],\"content-length\":[\"720\"],\"host\":[\"operra.hasanarofid.site\"],\"user-agent\":[\"facebookexternalua\"],\"x-hub-signature\":[\"sha1=765a744f630c74699e0d75b33d4c658cfe85c208\"],\"x-hub-signature-256\":[\"sha256=2844ed74bcc9568083a76f4e7c9c66253232277f52edfee5df30cd2d6b10b9fd\"]}', '2a03:2880:22ff::', 200, '2026-02-06 07:20:28', '2026-02-06 07:20:28'),
(6, 'meta', '{\"object\":\"whatsapp_business_account\",\"entry\":[{\"id\":\"1832090140829270\",\"changes\":[{\"value\":{\"messaging_product\":\"whatsapp\",\"metadata\":{\"display_phone_number\":\"6281933715777\",\"phone_number_id\":\"995428430312789\"},\"statuses\":[{\"id\":\"wamid.HBgONjI4ODEwMjY2OTc1MjcVAgARGBI0QTgyRUUzOThERTY4RDNDNjcA\",\"status\":\"failed\",\"timestamp\":\"1770362427\",\"recipient_id\":\"62881026697527\",\"errors\":[{\"code\":131047,\"title\":\"Re-engagement message\",\"message\":\"Re-engagement message\",\"error_data\":{\"details\":\"Message failed to send because more than 24 hours have passed since the customer last replied to this number.\"},\"href\":\"https:\\/\\/developers.facebook.com\\/docs\\/whatsapp\\/cloud-api\\/support\\/error-codes\\/\"}]}]},\"field\":\"messages\"}]}]}', '{\"accept\":[\"*\\/*\"],\"accept-encoding\":[\"deflate, gzip\"],\"content-type\":[\"application\\/json\"],\"content-length\":[\"720\"],\"host\":[\"operra.hasanarofid.site\"],\"user-agent\":[\"facebookexternalua\"],\"x-hub-signature\":[\"sha1=765a744f630c74699e0d75b33d4c658cfe85c208\"],\"x-hub-signature-256\":[\"sha256=2844ed74bcc9568083a76f4e7c9c66253232277f52edfee5df30cd2d6b10b9fd\"]}', '2a03:2880:10ff:5f::', 200, '2026-02-06 07:20:30', '2026-02-06 07:20:30'),
(7, 'meta', '{\"object\":\"whatsapp_business_account\",\"entry\":[{\"id\":\"1832090140829270\",\"changes\":[{\"value\":{\"messaging_product\":\"whatsapp\",\"metadata\":{\"display_phone_number\":\"6281933715777\",\"phone_number_id\":\"995428430312789\"},\"statuses\":[{\"id\":\"wamid.HBgONjI4ODEwMjY2OTc1MjcVAgARGBI0QTgyRUUzOThERTY4RDNDNjcA\",\"status\":\"failed\",\"timestamp\":\"1770362427\",\"recipient_id\":\"62881026697527\",\"errors\":[{\"code\":131047,\"title\":\"Re-engagement message\",\"message\":\"Re-engagement message\",\"error_data\":{\"details\":\"Message failed to send because more than 24 hours have passed since the customer last replied to this number.\"},\"href\":\"https:\\/\\/developers.facebook.com\\/docs\\/whatsapp\\/cloud-api\\/support\\/error-codes\\/\"}]}]},\"field\":\"messages\"}]}]}', '{\"accept\":[\"*\\/*\"],\"accept-encoding\":[\"deflate, gzip\"],\"content-type\":[\"application\\/json\"],\"content-length\":[\"720\"],\"host\":[\"operra.hasanarofid.site\"],\"user-agent\":[\"facebookexternalua\"],\"x-hub-signature\":[\"sha1=765a744f630c74699e0d75b33d4c658cfe85c208\"],\"x-hub-signature-256\":[\"sha256=2844ed74bcc9568083a76f4e7c9c66253232277f52edfee5df30cd2d6b10b9fd\"]}', '2a03:2880:10ff:43::', 200, '2026-02-06 07:20:33', '2026-02-06 07:20:33'),
(8, 'meta', '{\"object\":\"whatsapp_business_account\",\"entry\":[{\"id\":\"1832090140829270\",\"changes\":[{\"value\":{\"messaging_product\":\"whatsapp\",\"metadata\":{\"display_phone_number\":\"6281933715777\",\"phone_number_id\":\"995428430312789\"},\"statuses\":[{\"id\":\"wamid.HBgONjI4ODEwMjY2OTc1MjcVAgARGBI0QTgyRUUzOThERTY4RDNDNjcA\",\"status\":\"failed\",\"timestamp\":\"1770362427\",\"recipient_id\":\"62881026697527\",\"errors\":[{\"code\":131047,\"title\":\"Re-engagement message\",\"message\":\"Re-engagement message\",\"error_data\":{\"details\":\"Message failed to send because more than 24 hours have passed since the customer last replied to this number.\"},\"href\":\"https:\\/\\/developers.facebook.com\\/docs\\/whatsapp\\/cloud-api\\/support\\/error-codes\\/\"}]}]},\"field\":\"messages\"}]}]}', '{\"accept\":[\"*\\/*\"],\"accept-encoding\":[\"deflate, gzip\"],\"content-type\":[\"application\\/json\"],\"content-length\":[\"720\"],\"host\":[\"operra.hasanarofid.site\"],\"user-agent\":[\"facebookexternalua\"],\"x-hub-signature\":[\"sha1=765a744f630c74699e0d75b33d4c658cfe85c208\"],\"x-hub-signature-256\":[\"sha256=2844ed74bcc9568083a76f4e7c9c66253232277f52edfee5df30cd2d6b10b9fd\"]}', '2a03:2880:10ff:58::', 200, '2026-02-06 07:20:35', '2026-02-06 07:20:35'),
(9, 'meta', '{\"object\":\"whatsapp_business_account\",\"entry\":[{\"id\":\"1832090140829270\",\"changes\":[{\"value\":{\"messaging_product\":\"whatsapp\",\"metadata\":{\"display_phone_number\":\"6281933715777\",\"phone_number_id\":\"995428430312789\"},\"statuses\":[{\"id\":\"wamid.HBgONjI4ODEwMjY2OTc1MjcVAgARGBI0QTgyRUUzOThERTY4RDNDNjcA\",\"status\":\"failed\",\"timestamp\":\"1770362427\",\"recipient_id\":\"62881026697527\",\"errors\":[{\"code\":131047,\"title\":\"Re-engagement message\",\"message\":\"Re-engagement message\",\"error_data\":{\"details\":\"Message failed to send because more than 24 hours have passed since the customer last replied to this number.\"},\"href\":\"https:\\/\\/developers.facebook.com\\/docs\\/whatsapp\\/cloud-api\\/support\\/error-codes\\/\"}]}]},\"field\":\"messages\"}]}]}', '{\"accept\":[\"*\\/*\"],\"accept-encoding\":[\"deflate, gzip\"],\"content-type\":[\"application\\/json\"],\"content-length\":[\"720\"],\"host\":[\"operra.hasanarofid.site\"],\"user-agent\":[\"facebookexternalua\"],\"x-hub-signature\":[\"sha1=765a744f630c74699e0d75b33d4c658cfe85c208\"],\"x-hub-signature-256\":[\"sha256=2844ed74bcc9568083a76f4e7c9c66253232277f52edfee5df30cd2d6b10b9fd\"]}', '2a03:2880:10ff:5a::', 200, '2026-02-06 07:20:48', '2026-02-06 07:20:48'),
(10, 'meta', '{\"object\":\"whatsapp_business_account\",\"entry\":[{\"id\":\"1832090140829270\",\"changes\":[{\"value\":{\"messaging_product\":\"whatsapp\",\"metadata\":{\"display_phone_number\":\"6281933715777\",\"phone_number_id\":\"995428430312789\"},\"statuses\":[{\"id\":\"wamid.HBgONjI4ODEwMjY2OTc1MjcVAgARGBI0QTgyRUUzOThERTY4RDNDNjcA\",\"status\":\"failed\",\"timestamp\":\"1770362427\",\"recipient_id\":\"62881026697527\",\"errors\":[{\"code\":131047,\"title\":\"Re-engagement message\",\"message\":\"Re-engagement message\",\"error_data\":{\"details\":\"Message failed to send because more than 24 hours have passed since the customer last replied to this number.\"},\"href\":\"https:\\/\\/developers.facebook.com\\/docs\\/whatsapp\\/cloud-api\\/support\\/error-codes\\/\"}]}]},\"field\":\"messages\"}]}]}', '{\"accept\":[\"*\\/*\"],\"accept-encoding\":[\"deflate, gzip\"],\"content-type\":[\"application\\/json\"],\"content-length\":[\"720\"],\"host\":[\"operra.hasanarofid.site\"],\"user-agent\":[\"facebookexternalua\"],\"x-hub-signature\":[\"sha1=765a744f630c74699e0d75b33d4c658cfe85c208\"],\"x-hub-signature-256\":[\"sha256=2844ed74bcc9568083a76f4e7c9c66253232277f52edfee5df30cd2d6b10b9fd\"]}', '2a03:2880:10ff:4a::', 200, '2026-02-06 07:22:30', '2026-02-06 07:22:30'),
(11, 'meta', '{\"object\":\"whatsapp_business_account\",\"entry\":[{\"id\":\"1832090140829270\",\"changes\":[{\"value\":{\"messaging_product\":\"whatsapp\",\"metadata\":{\"display_phone_number\":\"6281933715777\",\"phone_number_id\":\"995428430312789\"},\"statuses\":[{\"id\":\"wamid.HBgMNjI4MTIzNDU2Nzg5FQIAERgSMzE1OTkzMjY2REFCMTA2OUE0AA==\",\"status\":\"failed\",\"timestamp\":\"1770364256\",\"recipient_id\":\"628123456789\",\"errors\":[{\"code\":131026,\"title\":\"Message undeliverable\",\"message\":\"Message undeliverable\",\"error_data\":{\"details\":\"Message Undeliverable.\"}}]}]},\"field\":\"messages\"}]}]}', '{\"accept\":[\"*\\/*\"],\"accept-encoding\":[\"deflate, gzip\"],\"content-type\":[\"application\\/json\"],\"content-length\":[\"537\"],\"host\":[\"operra.hasanarofid.site\"],\"user-agent\":[\"facebookexternalua\"],\"x-hub-signature\":[\"sha1=69bdbdb0a322a9c0242789942c2d990dfe736614\"],\"x-hub-signature-256\":[\"sha256=72b0aa1e63854b8338af66b1a1002aa8b0b32a053af8afdddabbbcc0850311dc\"]}', '2a03:2880:21ff:47::', 200, '2026-02-06 07:50:55', '2026-02-06 07:50:55'),
(12, 'meta', '{\"object\":\"whatsapp_business_account\",\"entry\":[{\"id\":\"1832090140829270\",\"changes\":[{\"value\":{\"messaging_product\":\"whatsapp\",\"metadata\":{\"display_phone_number\":\"6281933715777\",\"phone_number_id\":\"995428430312789\"},\"statuses\":[{\"id\":\"wamid.HBgMNjI4MTIzNDU2Nzg5FQIAERgSMzE1OTkzMjY2REFCMTA2OUE0AA==\",\"status\":\"failed\",\"timestamp\":\"1770364256\",\"recipient_id\":\"628123456789\",\"errors\":[{\"code\":131026,\"title\":\"Message undeliverable\",\"message\":\"Message undeliverable\",\"error_data\":{\"details\":\"Message Undeliverable.\"}}]}]},\"field\":\"messages\"}]}]}', '{\"accept\":[\"*\\/*\"],\"accept-encoding\":[\"deflate, gzip\"],\"content-type\":[\"application\\/json\"],\"content-length\":[\"537\"],\"host\":[\"operra.hasanarofid.site\"],\"user-agent\":[\"facebookexternalua\"],\"x-hub-signature\":[\"sha1=69bdbdb0a322a9c0242789942c2d990dfe736614\"],\"x-hub-signature-256\":[\"sha256=72b0aa1e63854b8338af66b1a1002aa8b0b32a053af8afdddabbbcc0850311dc\"]}', '2a03:2880:10ff:4d::', 200, '2026-02-06 07:50:57', '2026-02-06 07:50:57'),
(13, 'meta', '{\"object\":\"whatsapp_business_account\",\"entry\":[{\"id\":\"1832090140829270\",\"changes\":[{\"value\":{\"messaging_product\":\"whatsapp\",\"metadata\":{\"display_phone_number\":\"6281933715777\",\"phone_number_id\":\"995428430312789\"},\"statuses\":[{\"id\":\"wamid.HBgMNjI4MTIzNDU2Nzg5FQIAERgSMzE1OTkzMjY2REFCMTA2OUE0AA==\",\"status\":\"failed\",\"timestamp\":\"1770364256\",\"recipient_id\":\"628123456789\",\"errors\":[{\"code\":131026,\"title\":\"Message undeliverable\",\"message\":\"Message undeliverable\",\"error_data\":{\"details\":\"Message Undeliverable.\"}}]}]},\"field\":\"messages\"}]}]}', '{\"accept\":[\"*\\/*\"],\"accept-encoding\":[\"deflate, gzip\"],\"content-type\":[\"application\\/json\"],\"content-length\":[\"537\"],\"host\":[\"operra.hasanarofid.site\"],\"user-agent\":[\"facebookexternalua\"],\"x-hub-signature\":[\"sha1=69bdbdb0a322a9c0242789942c2d990dfe736614\"],\"x-hub-signature-256\":[\"sha256=72b0aa1e63854b8338af66b1a1002aa8b0b32a053af8afdddabbbcc0850311dc\"]}', '2a03:2880:10ff:42::', 200, '2026-02-06 07:50:58', '2026-02-06 07:50:58'),
(14, 'meta', '{\"object\":\"whatsapp_business_account\",\"entry\":[{\"id\":\"1832090140829270\",\"changes\":[{\"value\":{\"messaging_product\":\"whatsapp\",\"metadata\":{\"display_phone_number\":\"6281933715777\",\"phone_number_id\":\"995428430312789\"},\"statuses\":[{\"id\":\"wamid.HBgMNjI4MTIzNDU2Nzg5FQIAERgSMzE1OTkzMjY2REFCMTA2OUE0AA==\",\"status\":\"failed\",\"timestamp\":\"1770364256\",\"recipient_id\":\"628123456789\",\"errors\":[{\"code\":131026,\"title\":\"Message undeliverable\",\"message\":\"Message undeliverable\",\"error_data\":{\"details\":\"Message Undeliverable.\"}}]}]},\"field\":\"messages\"}]}]}', '{\"accept\":[\"*\\/*\"],\"accept-encoding\":[\"deflate, gzip\"],\"content-type\":[\"application\\/json\"],\"content-length\":[\"537\"],\"host\":[\"operra.hasanarofid.site\"],\"user-agent\":[\"facebookexternalua\"],\"x-hub-signature\":[\"sha1=69bdbdb0a322a9c0242789942c2d990dfe736614\"],\"x-hub-signature-256\":[\"sha256=72b0aa1e63854b8338af66b1a1002aa8b0b32a053af8afdddabbbcc0850311dc\"]}', '2a03:2880:10ff:70::', 200, '2026-02-06 07:51:00', '2026-02-06 07:51:00'),
(15, 'meta', '{\"object\":\"whatsapp_business_account\",\"entry\":[{\"id\":\"1832090140829270\",\"changes\":[{\"value\":{\"messaging_product\":\"whatsapp\",\"metadata\":{\"display_phone_number\":\"6281933715777\",\"phone_number_id\":\"995428430312789\"},\"statuses\":[{\"id\":\"wamid.HBgMNjI4MTIzNDU2Nzg5FQIAERgSMzE1OTkzMjY2REFCMTA2OUE0AA==\",\"status\":\"failed\",\"timestamp\":\"1770364256\",\"recipient_id\":\"628123456789\",\"errors\":[{\"code\":131026,\"title\":\"Message undeliverable\",\"message\":\"Message undeliverable\",\"error_data\":{\"details\":\"Message Undeliverable.\"}}]}]},\"field\":\"messages\"}]}]}', '{\"accept\":[\"*\\/*\"],\"accept-encoding\":[\"deflate, gzip\"],\"content-type\":[\"application\\/json\"],\"content-length\":[\"537\"],\"host\":[\"operra.hasanarofid.site\"],\"user-agent\":[\"facebookexternalua\"],\"x-hub-signature\":[\"sha1=69bdbdb0a322a9c0242789942c2d990dfe736614\"],\"x-hub-signature-256\":[\"sha256=72b0aa1e63854b8338af66b1a1002aa8b0b32a053af8afdddabbbcc0850311dc\"]}', '2a03:2880:10ff:71::', 200, '2026-02-06 07:51:16', '2026-02-06 07:51:16'),
(16, 'meta', '{\"object\":\"whatsapp_business_account\",\"entry\":[{\"id\":\"1832090140829270\",\"changes\":[{\"value\":{\"messaging_product\":\"whatsapp\",\"metadata\":{\"display_phone_number\":\"6281933715777\",\"phone_number_id\":\"995428430312789\"},\"statuses\":[{\"id\":\"wamid.HBgMNjI4MTIzNDU2Nzg5FQIAERgSMzE1OTkzMjY2REFCMTA2OUE0AA==\",\"status\":\"failed\",\"timestamp\":\"1770364256\",\"recipient_id\":\"628123456789\",\"errors\":[{\"code\":131026,\"title\":\"Message undeliverable\",\"message\":\"Message undeliverable\",\"error_data\":{\"details\":\"Message Undeliverable.\"}}]}]},\"field\":\"messages\"}]}]}', '{\"accept\":[\"*\\/*\"],\"accept-encoding\":[\"deflate, gzip\"],\"content-type\":[\"application\\/json\"],\"content-length\":[\"537\"],\"host\":[\"operra.hasanarofid.site\"],\"user-agent\":[\"facebookexternalua\"],\"x-hub-signature\":[\"sha1=69bdbdb0a322a9c0242789942c2d990dfe736614\"],\"x-hub-signature-256\":[\"sha256=72b0aa1e63854b8338af66b1a1002aa8b0b32a053af8afdddabbbcc0850311dc\"]}', '2a03:2880:10ff:5f::', 200, '2026-02-06 07:52:22', '2026-02-06 07:52:22'),
(17, 'meta', '{\"object\":\"whatsapp_business_account\",\"entry\":[{\"id\":\"1832090140829270\",\"changes\":[{\"value\":{\"messaging_product\":\"whatsapp\",\"metadata\":{\"display_phone_number\":\"6281933715777\",\"phone_number_id\":\"995428430312789\"},\"statuses\":[{\"id\":\"wamid.HBgMNjI4MTIzNDU2Nzg5FQIAERgSMzE1OTkzMjY2REFCMTA2OUE0AA==\",\"status\":\"failed\",\"timestamp\":\"1770364256\",\"recipient_id\":\"628123456789\",\"errors\":[{\"code\":131026,\"title\":\"Message undeliverable\",\"message\":\"Message undeliverable\",\"error_data\":{\"details\":\"Message Undeliverable.\"}}]}]},\"field\":\"messages\"}]}]}', '{\"accept\":[\"*\\/*\"],\"accept-encoding\":[\"deflate, gzip\"],\"content-type\":[\"application\\/json\"],\"content-length\":[\"537\"],\"host\":[\"operra.hasanarofid.site\"],\"user-agent\":[\"facebookexternalua\"],\"x-hub-signature\":[\"sha1=69bdbdb0a322a9c0242789942c2d990dfe736614\"],\"x-hub-signature-256\":[\"sha256=72b0aa1e63854b8338af66b1a1002aa8b0b32a053af8afdddabbbcc0850311dc\"]}', '2a03:2880:10ff:2::', 200, '2026-02-06 07:59:51', '2026-02-06 07:59:51'),
(18, 'meta', '{\"object\":\"whatsapp_business_account\",\"entry\":[{\"id\":\"1832090140829270\",\"changes\":[{\"value\":{\"messaging_product\":\"whatsapp\",\"metadata\":{\"display_phone_number\":\"6281933715777\",\"phone_number_id\":\"995428430312789\"},\"statuses\":[{\"id\":\"wamid.HBgONjI4ODEwMjY2OTc1MjcVAgARGBI0QTgyRUUzOThERTY4RDNDNjcA\",\"status\":\"failed\",\"timestamp\":\"1770362427\",\"recipient_id\":\"62881026697527\",\"errors\":[{\"code\":131047,\"title\":\"Re-engagement message\",\"message\":\"Re-engagement message\",\"error_data\":{\"details\":\"Message failed to send because more than 24 hours have passed since the customer last replied to this number.\"},\"href\":\"https:\\/\\/developers.facebook.com\\/docs\\/whatsapp\\/cloud-api\\/support\\/error-codes\\/\"}]}]},\"field\":\"messages\"}]}]}', '{\"accept\":[\"*\\/*\"],\"accept-encoding\":[\"deflate, gzip\"],\"content-type\":[\"application\\/json\"],\"content-length\":[\"720\"],\"host\":[\"operra.hasanarofid.site\"],\"user-agent\":[\"facebookexternalua\"],\"x-hub-signature\":[\"sha1=765a744f630c74699e0d75b33d4c658cfe85c208\"],\"x-hub-signature-256\":[\"sha256=2844ed74bcc9568083a76f4e7c9c66253232277f52edfee5df30cd2d6b10b9fd\"]}', '173.252.95.61', 200, '2026-02-06 09:06:50', '2026-02-06 09:06:50'),
(19, 'meta', '{\"object\":\"whatsapp_business_account\",\"entry\":[{\"id\":\"1832090140829270\",\"changes\":[{\"value\":{\"messaging_product\":\"whatsapp\",\"metadata\":{\"display_phone_number\":\"6281933715777\",\"phone_number_id\":\"995428430312789\"},\"statuses\":[{\"id\":\"wamid.HBgMNjI4MTIzNDU2Nzg5FQIAERgSMzE1OTkzMjY2REFCMTA2OUE0AA==\",\"status\":\"failed\",\"timestamp\":\"1770364256\",\"recipient_id\":\"628123456789\",\"errors\":[{\"code\":131026,\"title\":\"Message undeliverable\",\"message\":\"Message undeliverable\",\"error_data\":{\"details\":\"Message Undeliverable.\"}}]}]},\"field\":\"messages\"}]}]}', '{\"accept\":[\"*\\/*\"],\"accept-encoding\":[\"deflate, gzip\"],\"content-type\":[\"application\\/json\"],\"content-length\":[\"537\"],\"host\":[\"operra.hasanarofid.site\"],\"user-agent\":[\"facebookexternalua\"],\"x-hub-signature\":[\"sha1=69bdbdb0a322a9c0242789942c2d990dfe736614\"],\"x-hub-signature-256\":[\"sha256=72b0aa1e63854b8338af66b1a1002aa8b0b32a053af8afdddabbbcc0850311dc\"]}', '173.252.79.32', 200, '2026-02-06 11:23:59', '2026-02-06 11:23:59'),
(20, 'meta', '{\"object\":\"whatsapp_business_account\",\"entry\":[{\"id\":\"1832090140829270\",\"changes\":[{\"value\":{\"messaging_product\":\"whatsapp\",\"metadata\":{\"display_phone_number\":\"6281933715777\",\"phone_number_id\":\"995428430312789\"},\"statuses\":[{\"id\":\"wamid.HBgMNjI4MTIzNDU2Nzg5FQIAERgSMzE1OTkzMjY2REFCMTA2OUE0AA==\",\"status\":\"failed\",\"timestamp\":\"1770364256\",\"recipient_id\":\"628123456789\",\"errors\":[{\"code\":131026,\"title\":\"Message undeliverable\",\"message\":\"Message undeliverable\",\"error_data\":{\"details\":\"Message Undeliverable.\"}}]}]},\"field\":\"messages\"}]}]}', '{\"accept\":[\"*\\/*\"],\"accept-encoding\":[\"deflate, gzip\"],\"content-type\":[\"application\\/json\"],\"content-length\":[\"537\"],\"host\":[\"operra.hasanarofid.site\"],\"user-agent\":[\"facebookexternalua\"],\"x-hub-signature\":[\"sha1=69bdbdb0a322a9c0242789942c2d990dfe736614\"],\"x-hub-signature-256\":[\"sha256=72b0aa1e63854b8338af66b1a1002aa8b0b32a053af8afdddabbbcc0850311dc\"]}', '2a03:2880:12ff:4c::', 200, '2026-02-06 11:24:00', '2026-02-06 11:24:00'),
(21, 'meta', '{\"object\":\"whatsapp_business_account\",\"entry\":[{\"id\":\"1832090140829270\",\"changes\":[{\"value\":{\"messaging_product\":\"whatsapp\",\"metadata\":{\"display_phone_number\":\"6281933715777\",\"phone_number_id\":\"995428430312789\"},\"statuses\":[{\"id\":\"wamid.HBgMNjI4MTIzNDU2Nzg5FQIAERgSMzE1OTkzMjY2REFCMTA2OUE0AA==\",\"status\":\"failed\",\"timestamp\":\"1770364256\",\"recipient_id\":\"628123456789\",\"errors\":[{\"code\":131026,\"title\":\"Message undeliverable\",\"message\":\"Message undeliverable\",\"error_data\":{\"details\":\"Message Undeliverable.\"}}]}]},\"field\":\"messages\"}]}]}', '{\"accept\":[\"*\\/*\"],\"accept-encoding\":[\"deflate, gzip\"],\"content-type\":[\"application\\/json\"],\"content-length\":[\"537\"],\"host\":[\"operra.hasanarofid.site\"],\"user-agent\":[\"facebookexternalua\"],\"x-hub-signature\":[\"sha1=69bdbdb0a322a9c0242789942c2d990dfe736614\"],\"x-hub-signature-256\":[\"sha256=72b0aa1e63854b8338af66b1a1002aa8b0b32a053af8afdddabbbcc0850311dc\"]}', '2a03:2880:12ff:41::', 200, '2026-02-06 11:24:03', '2026-02-06 11:24:03'),
(22, 'meta', '{\"object\":\"whatsapp_business_account\",\"entry\":[{\"id\":\"1832090140829270\",\"changes\":[{\"value\":{\"messaging_product\":\"whatsapp\",\"metadata\":{\"display_phone_number\":\"6281933715777\",\"phone_number_id\":\"995428430312789\"},\"statuses\":[{\"id\":\"wamid.HBgMNjI4MTIzNDU2Nzg5FQIAERgSMzE1OTkzMjY2REFCMTA2OUE0AA==\",\"status\":\"failed\",\"timestamp\":\"1770364256\",\"recipient_id\":\"628123456789\",\"errors\":[{\"code\":131026,\"title\":\"Message undeliverable\",\"message\":\"Message undeliverable\",\"error_data\":{\"details\":\"Message Undeliverable.\"}}]}]},\"field\":\"messages\"}]}]}', '{\"accept\":[\"*\\/*\"],\"accept-encoding\":[\"deflate, gzip\"],\"content-type\":[\"application\\/json\"],\"content-length\":[\"537\"],\"host\":[\"operra.hasanarofid.site\"],\"user-agent\":[\"facebookexternalua\"],\"x-hub-signature\":[\"sha1=69bdbdb0a322a9c0242789942c2d990dfe736614\"],\"x-hub-signature-256\":[\"sha256=72b0aa1e63854b8338af66b1a1002aa8b0b32a053af8afdddabbbcc0850311dc\"]}', '2a03:2880:12ff:54::', 200, '2026-02-06 11:24:10', '2026-02-06 11:24:10'),
(23, 'meta', '{\"object\":\"whatsapp_business_account\",\"entry\":[{\"id\":\"1832090140829270\",\"changes\":[{\"value\":{\"messaging_product\":\"whatsapp\",\"metadata\":{\"display_phone_number\":\"6281933715777\",\"phone_number_id\":\"995428430312789\"},\"statuses\":[{\"id\":\"wamid.HBgMNjI4MTIzNDU2Nzg5FQIAERgSMzE1OTkzMjY2REFCMTA2OUE0AA==\",\"status\":\"failed\",\"timestamp\":\"1770364256\",\"recipient_id\":\"628123456789\",\"errors\":[{\"code\":131026,\"title\":\"Message undeliverable\",\"message\":\"Message undeliverable\",\"error_data\":{\"details\":\"Message Undeliverable.\"}}]}]},\"field\":\"messages\"}]}]}', '{\"accept\":[\"*\\/*\"],\"accept-encoding\":[\"deflate, gzip\"],\"content-type\":[\"application\\/json\"],\"content-length\":[\"537\"],\"host\":[\"operra.hasanarofid.site\"],\"user-agent\":[\"facebookexternalua\"],\"x-hub-signature\":[\"sha1=69bdbdb0a322a9c0242789942c2d990dfe736614\"],\"x-hub-signature-256\":[\"sha256=72b0aa1e63854b8338af66b1a1002aa8b0b32a053af8afdddabbbcc0850311dc\"]}', '2a03:2880:12ff:4e::', 200, '2026-02-06 11:30:00', '2026-02-06 11:30:00'),
(24, 'meta', '{\"object\":\"whatsapp_business_account\",\"entry\":[{\"id\":\"1832090140829270\",\"changes\":[{\"value\":{\"messaging_product\":\"whatsapp\",\"metadata\":{\"display_phone_number\":\"6281933715777\",\"phone_number_id\":\"995428430312789\"},\"statuses\":[{\"id\":\"wamid.HBgMNjI4MTIzNDU2Nzg5FQIAERgSMzE1OTkzMjY2REFCMTA2OUE0AA==\",\"status\":\"failed\",\"timestamp\":\"1770364256\",\"recipient_id\":\"628123456789\",\"errors\":[{\"code\":131026,\"title\":\"Message undeliverable\",\"message\":\"Message undeliverable\",\"error_data\":{\"details\":\"Message Undeliverable.\"}}]}]},\"field\":\"messages\"}]}]}', '{\"accept\":[\"*\\/*\"],\"accept-encoding\":[\"deflate, gzip\"],\"content-type\":[\"application\\/json\"],\"content-length\":[\"537\"],\"host\":[\"operra.hasanarofid.site\"],\"user-agent\":[\"facebookexternalua\"],\"x-hub-signature\":[\"sha1=69bdbdb0a322a9c0242789942c2d990dfe736614\"],\"x-hub-signature-256\":[\"sha256=72b0aa1e63854b8338af66b1a1002aa8b0b32a053af8afdddabbbcc0850311dc\"]}', '2a03:2880:22ff:42::', 200, '2026-02-06 11:56:07', '2026-02-06 11:56:07'),
(25, 'meta', '{\"object\":\"whatsapp_business_account\",\"entry\":[{\"id\":\"1832090140829270\",\"changes\":[{\"value\":{\"messaging_product\":\"whatsapp\",\"metadata\":{\"display_phone_number\":\"6281933715777\",\"phone_number_id\":\"995428430312789\"},\"statuses\":[{\"id\":\"wamid.HBgMNjI4MTIzNDU2Nzg5FQIAERgSMzE1OTkzMjY2REFCMTA2OUE0AA==\",\"status\":\"failed\",\"timestamp\":\"1770364256\",\"recipient_id\":\"628123456789\",\"errors\":[{\"code\":131026,\"title\":\"Message undeliverable\",\"message\":\"Message undeliverable\",\"error_data\":{\"details\":\"Message Undeliverable.\"}}]}]},\"field\":\"messages\"}]}]}', '{\"accept\":[\"*\\/*\"],\"accept-encoding\":[\"deflate, gzip\"],\"content-type\":[\"application\\/json\"],\"content-length\":[\"537\"],\"host\":[\"operra.hasanarofid.site\"],\"user-agent\":[\"facebookexternalua\"],\"x-hub-signature\":[\"sha1=69bdbdb0a322a9c0242789942c2d990dfe736614\"],\"x-hub-signature-256\":[\"sha256=72b0aa1e63854b8338af66b1a1002aa8b0b32a053af8afdddabbbcc0850311dc\"]}', '2a03:2880:22ff:45::', 200, '2026-02-06 11:56:09', '2026-02-06 11:56:09'),
(26, 'meta', '{\"object\":\"whatsapp_business_account\",\"entry\":[{\"id\":\"1832090140829270\",\"changes\":[{\"value\":{\"messaging_product\":\"whatsapp\",\"metadata\":{\"display_phone_number\":\"6281933715777\",\"phone_number_id\":\"995428430312789\"},\"statuses\":[{\"id\":\"wamid.HBgMNjI4MTIzNDU2Nzg5FQIAERgSMzE1OTkzMjY2REFCMTA2OUE0AA==\",\"status\":\"failed\",\"timestamp\":\"1770364256\",\"recipient_id\":\"628123456789\",\"errors\":[{\"code\":131026,\"title\":\"Message undeliverable\",\"message\":\"Message undeliverable\",\"error_data\":{\"details\":\"Message Undeliverable.\"}}]}]},\"field\":\"messages\"}]}]}', '{\"accept\":[\"*\\/*\"],\"accept-encoding\":[\"deflate, gzip\"],\"content-type\":[\"application\\/json\"],\"content-length\":[\"537\"],\"host\":[\"operra.hasanarofid.site\"],\"user-agent\":[\"facebookexternalua\"],\"x-hub-signature\":[\"sha1=69bdbdb0a322a9c0242789942c2d990dfe736614\"],\"x-hub-signature-256\":[\"sha256=72b0aa1e63854b8338af66b1a1002aa8b0b32a053af8afdddabbbcc0850311dc\"]}', '2a03:2880:22ff:4::', 200, '2026-02-06 11:56:12', '2026-02-06 11:56:12'),
(27, 'meta', '{\"object\":\"whatsapp_business_account\",\"entry\":[{\"id\":\"1832090140829270\",\"changes\":[{\"value\":{\"messaging_product\":\"whatsapp\",\"metadata\":{\"display_phone_number\":\"6281933715777\",\"phone_number_id\":\"995428430312789\"},\"statuses\":[{\"id\":\"wamid.HBgMNjI4MTIzNDU2Nzg5FQIAERgSMzE1OTkzMjY2REFCMTA2OUE0AA==\",\"status\":\"failed\",\"timestamp\":\"1770364256\",\"recipient_id\":\"628123456789\",\"errors\":[{\"code\":131026,\"title\":\"Message undeliverable\",\"message\":\"Message undeliverable\",\"error_data\":{\"details\":\"Message Undeliverable.\"}}]}]},\"field\":\"messages\"}]}]}', '{\"accept\":[\"*\\/*\"],\"accept-encoding\":[\"deflate, gzip\"],\"content-type\":[\"application\\/json\"],\"content-length\":[\"537\"],\"host\":[\"operra.hasanarofid.site\"],\"user-agent\":[\"facebookexternalua\"],\"x-hub-signature\":[\"sha1=69bdbdb0a322a9c0242789942c2d990dfe736614\"],\"x-hub-signature-256\":[\"sha256=72b0aa1e63854b8338af66b1a1002aa8b0b32a053af8afdddabbbcc0850311dc\"]}', '2a03:2880:22ff:73::', 200, '2026-02-06 11:56:33', '2026-02-06 11:56:33'),
(28, 'meta', '{\"object\":\"whatsapp_business_account\",\"entry\":[{\"id\":\"1832090140829270\",\"changes\":[{\"value\":{\"messaging_product\":\"whatsapp\",\"metadata\":{\"display_phone_number\":\"6281933715777\",\"phone_number_id\":\"995428430312789\"},\"statuses\":[{\"id\":\"wamid.HBgMNjI4MTIzNDU2Nzg5FQIAERgSMzE1OTkzMjY2REFCMTA2OUE0AA==\",\"status\":\"failed\",\"timestamp\":\"1770364256\",\"recipient_id\":\"628123456789\",\"errors\":[{\"code\":131026,\"title\":\"Message undeliverable\",\"message\":\"Message undeliverable\",\"error_data\":{\"details\":\"Message Undeliverable.\"}}]}]},\"field\":\"messages\"}]}]}', '{\"accept\":[\"*\\/*\"],\"accept-encoding\":[\"deflate, gzip\"],\"content-type\":[\"application\\/json\"],\"content-length\":[\"537\"],\"host\":[\"operra.hasanarofid.site\"],\"user-agent\":[\"facebookexternalua\"],\"x-hub-signature\":[\"sha1=69bdbdb0a322a9c0242789942c2d990dfe736614\"],\"x-hub-signature-256\":[\"sha256=72b0aa1e63854b8338af66b1a1002aa8b0b32a053af8afdddabbbcc0850311dc\"]}', '2a03:2880:22ff:71::', 200, '2026-02-06 12:01:33', '2026-02-06 12:01:33'),
(29, 'meta', '{\"object\":\"whatsapp_business_account\",\"entry\":[{\"id\":\"1832090140829270\",\"changes\":[{\"value\":{\"messaging_product\":\"whatsapp\",\"metadata\":{\"display_phone_number\":\"6281933715777\",\"phone_number_id\":\"995428430312789\"},\"statuses\":[{\"id\":\"wamid.HBgMNjI4MTIzNDU2Nzg5FQIAERgSMzE1OTkzMjY2REFCMTA2OUE0AA==\",\"status\":\"failed\",\"timestamp\":\"1770364256\",\"recipient_id\":\"628123456789\",\"errors\":[{\"code\":131026,\"title\":\"Message undeliverable\",\"message\":\"Message undeliverable\",\"error_data\":{\"details\":\"Message Undeliverable.\"}}]}]},\"field\":\"messages\"}]}]}', '{\"accept\":[\"*\\/*\"],\"accept-encoding\":[\"deflate, gzip\"],\"content-type\":[\"application\\/json\"],\"content-length\":[\"537\"],\"host\":[\"operra.hasanarofid.site\"],\"user-agent\":[\"facebookexternalua\"],\"x-hub-signature\":[\"sha1=69bdbdb0a322a9c0242789942c2d990dfe736614\"],\"x-hub-signature-256\":[\"sha256=72b0aa1e63854b8338af66b1a1002aa8b0b32a053af8afdddabbbcc0850311dc\"]}', '2a03:2880:12ff:9::', 200, '2026-02-06 12:18:26', '2026-02-06 12:18:26'),
(30, 'meta', '{\"object\":\"whatsapp_business_account\",\"entry\":[{\"id\":\"1832090140829270\",\"changes\":[{\"value\":{\"messaging_product\":\"whatsapp\",\"metadata\":{\"display_phone_number\":\"6281933715777\",\"phone_number_id\":\"995428430312789\"},\"statuses\":[{\"id\":\"wamid.HBgMNjI4MTIzNDU2Nzg5FQIAERgSMzE1OTkzMjY2REFCMTA2OUE0AA==\",\"status\":\"failed\",\"timestamp\":\"1770364256\",\"recipient_id\":\"628123456789\",\"errors\":[{\"code\":131026,\"title\":\"Message undeliverable\",\"message\":\"Message undeliverable\",\"error_data\":{\"details\":\"Message Undeliverable.\"}}]}]},\"field\":\"messages\"}]}]}', '{\"accept\":[\"*\\/*\"],\"accept-encoding\":[\"deflate, gzip\"],\"content-type\":[\"application\\/json\"],\"content-length\":[\"537\"],\"host\":[\"operra.hasanarofid.site\"],\"user-agent\":[\"facebookexternalua\"],\"x-hub-signature\":[\"sha1=69bdbdb0a322a9c0242789942c2d990dfe736614\"],\"x-hub-signature-256\":[\"sha256=72b0aa1e63854b8338af66b1a1002aa8b0b32a053af8afdddabbbcc0850311dc\"]}', '2a03:2880:12ff:54::', 200, '2026-02-06 12:18:28', '2026-02-06 12:18:28'),
(31, 'meta', '{\"object\":\"whatsapp_business_account\",\"entry\":[{\"id\":\"1832090140829270\",\"changes\":[{\"value\":{\"messaging_product\":\"whatsapp\",\"metadata\":{\"display_phone_number\":\"6281933715777\",\"phone_number_id\":\"995428430312789\"},\"statuses\":[{\"id\":\"wamid.HBgMNjI4MTIzNDU2Nzg5FQIAERgSMzE1OTkzMjY2REFCMTA2OUE0AA==\",\"status\":\"failed\",\"timestamp\":\"1770364256\",\"recipient_id\":\"628123456789\",\"errors\":[{\"code\":131026,\"title\":\"Message undeliverable\",\"message\":\"Message undeliverable\",\"error_data\":{\"details\":\"Message Undeliverable.\"}}]}]},\"field\":\"messages\"}]}]}', '{\"accept\":[\"*\\/*\"],\"accept-encoding\":[\"deflate, gzip\"],\"content-type\":[\"application\\/json\"],\"content-length\":[\"537\"],\"host\":[\"operra.hasanarofid.site\"],\"user-agent\":[\"facebookexternalua\"],\"x-hub-signature\":[\"sha1=69bdbdb0a322a9c0242789942c2d990dfe736614\"],\"x-hub-signature-256\":[\"sha256=72b0aa1e63854b8338af66b1a1002aa8b0b32a053af8afdddabbbcc0850311dc\"]}', '2a03:2880:12ff:48::', 200, '2026-02-06 12:18:29', '2026-02-06 12:18:29'),
(32, 'meta', '{\"object\":\"whatsapp_business_account\",\"entry\":[{\"id\":\"1832090140829270\",\"changes\":[{\"value\":{\"messaging_product\":\"whatsapp\",\"metadata\":{\"display_phone_number\":\"6281933715777\",\"phone_number_id\":\"995428430312789\"},\"statuses\":[{\"id\":\"wamid.HBgMNjI4MTIzNDU2Nzg5FQIAERgSMzE1OTkzMjY2REFCMTA2OUE0AA==\",\"status\":\"failed\",\"timestamp\":\"1770364256\",\"recipient_id\":\"628123456789\",\"errors\":[{\"code\":131026,\"title\":\"Message undeliverable\",\"message\":\"Message undeliverable\",\"error_data\":{\"details\":\"Message Undeliverable.\"}}]}]},\"field\":\"messages\"}]}]}', '{\"accept\":[\"*\\/*\"],\"accept-encoding\":[\"deflate, gzip\"],\"content-type\":[\"application\\/json\"],\"content-length\":[\"537\"],\"host\":[\"operra.hasanarofid.site\"],\"user-agent\":[\"facebookexternalua\"],\"x-hub-signature\":[\"sha1=69bdbdb0a322a9c0242789942c2d990dfe736614\"],\"x-hub-signature-256\":[\"sha256=72b0aa1e63854b8338af66b1a1002aa8b0b32a053af8afdddabbbcc0850311dc\"]}', '2a03:2880:12ff:42::', 200, '2026-02-06 12:18:31', '2026-02-06 12:18:31'),
(33, 'meta', '{\"object\":\"whatsapp_business_account\",\"entry\":[{\"id\":\"1832090140829270\",\"changes\":[{\"value\":{\"messaging_product\":\"whatsapp\",\"metadata\":{\"display_phone_number\":\"6281933715777\",\"phone_number_id\":\"995428430312789\"},\"statuses\":[{\"id\":\"wamid.HBgMNjI4MTIzNDU2Nzg5FQIAERgSMzE1OTkzMjY2REFCMTA2OUE0AA==\",\"status\":\"failed\",\"timestamp\":\"1770364256\",\"recipient_id\":\"628123456789\",\"errors\":[{\"code\":131026,\"title\":\"Message undeliverable\",\"message\":\"Message undeliverable\",\"error_data\":{\"details\":\"Message Undeliverable.\"}}]}]},\"field\":\"messages\"}]}]}', '{\"accept\":[\"*\\/*\"],\"accept-encoding\":[\"deflate, gzip\"],\"content-type\":[\"application\\/json\"],\"content-length\":[\"537\"],\"host\":[\"operra.hasanarofid.site\"],\"user-agent\":[\"facebookexternalua\"],\"x-hub-signature\":[\"sha1=69bdbdb0a322a9c0242789942c2d990dfe736614\"],\"x-hub-signature-256\":[\"sha256=72b0aa1e63854b8338af66b1a1002aa8b0b32a053af8afdddabbbcc0850311dc\"]}', '2a03:2880:12ff:72::', 200, '2026-02-06 12:22:40', '2026-02-06 12:22:40');

-- --------------------------------------------------------

--
-- Table structure for table `whatsapp_accounts`
--

CREATE TABLE `whatsapp_accounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `phone_number` varchar(255) NOT NULL,
  `provider` varchar(255) NOT NULL DEFAULT 'official',
  `api_credentials` text DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('active','inactive','disconnected') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `whatsapp_accounts`
--

INSERT INTO `whatsapp_accounts` (`id`, `company_id`, `name`, `phone_number`, `provider`, `api_credentials`, `is_verified`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'CS Utama Operra', '6281234567890', 'official', NULL, 1, 'active', '2026-01-28 14:21:41', '2026-01-28 14:21:41'),
(2, 1, 'WA Business Jakarta', '923232323', 'fonnte', '{\"token\":\"TbC72oN43CMvkvoWDoBh\",\"endpoint\":\"https:\\/\\/api.fonnte.com\\/send\"}', 0, 'active', '2026-01-28 14:21:41', '2026-01-28 14:21:41'),
(3, 1, 'WA Business Surabaya', '6282222222222', 'generic', '{\"token\":\"DUMMY_TOKEN\",\"key\":\"DUMMY_KEY\",\"endpoint\":\"https:\\/\\/api.wa-provider.com\\/v1\"}', 0, 'active', '2026-01-28 14:21:41', '2026-01-28 14:21:41'),
(6, 7, 'wa', '995428430312789', 'official', '{\"token\":\"\",\"key\":\"\",\"endpoint\":\"\"}', 1, 'active', '2026-02-04 13:23:14', '2026-02-05 14:57:12');

-- --------------------------------------------------------

--
-- Table structure for table `whatsapp_agents`
--

CREATE TABLE `whatsapp_agents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `whatsapp_account_id` bigint(20) UNSIGNED NOT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `last_assigned_at` timestamp NULL DEFAULT NULL,
  `active_chats_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `whatsapp_agents`
--

INSERT INTO `whatsapp_agents` (`id`, `company_id`, `user_id`, `whatsapp_account_id`, `is_available`, `last_assigned_at`, `active_chats_count`, `created_at`, `updated_at`) VALUES
(1, NULL, 4, 1, 1, NULL, 0, '2026-01-28 14:21:41', '2026-01-28 14:21:41'),
(2, NULL, 5, 2, 1, NULL, 0, '2026-01-28 14:21:42', '2026-01-28 14:21:42'),
(3, NULL, 6, 3, 1, NULL, 0, '2026-01-28 14:21:42', '2026-01-28 14:21:42');

-- --------------------------------------------------------

--
-- Table structure for table `whatsapp_auto_replies`
--

CREATE TABLE `whatsapp_auto_replies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `whatsapp_account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `keyword` varchar(255) NOT NULL,
  `response` text NOT NULL,
  `match_type` enum('exact','contains') NOT NULL DEFAULT 'exact',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `whatsapp_auto_replies`
--

INSERT INTO `whatsapp_auto_replies` (`id`, `company_id`, `whatsapp_account_id`, `keyword`, `response`, `match_type`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 7, 6, 'Selamat Datant', 'Selamat Datang di akun kami', 'contains', 1, '2026-02-04 14:35:48', '2026-02-04 14:35:48');

-- --------------------------------------------------------

--
-- Table structure for table `whatsapp_campaigns`
--

CREATE TABLE `whatsapp_campaigns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `whatsapp_account_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `message_template` text NOT NULL,
  `template_name` varchar(255) DEFAULT NULL,
  `template_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`template_data`)),
  `status` enum('draft','scheduled','processing','completed','failed') NOT NULL DEFAULT 'draft',
  `scheduled_at` timestamp NULL DEFAULT NULL,
  `total_recipients` int(11) NOT NULL DEFAULT 0,
  `sent_count` int(11) NOT NULL DEFAULT 0,
  `failed_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `whatsapp_campaign_logs`
--

CREATE TABLE `whatsapp_campaign_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `whatsapp_campaign_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `phone_number` varchar(255) NOT NULL,
  `status` enum('pending','sent','failed') NOT NULL DEFAULT 'pending',
  `error_message` text DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `whatsapp_templates`
--

CREATE TABLE `whatsapp_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `whatsapp_account_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `language` varchar(255) NOT NULL DEFAULT 'id',
  `category` varchar(255) DEFAULT NULL,
  `components` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`components`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ai_usages`
--
ALTER TABLE `ai_usages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ai_usages_company_id_foreign` (`company_id`);

--
-- Indexes for table `approvals`
--
ALTER TABLE `approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approvals_requester_id_foreign` (`requester_id`),
  ADD KEY `approvals_approver_id_foreign` (`approver_id`),
  ADD KEY `approvals_company_id_foreign` (`company_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_messages_chat_session_id_foreign` (`chat_session_id`),
  ADD KEY `chat_messages_sender_id_foreign` (`sender_id`),
  ADD KEY `chat_messages_vendor_message_id_index` (`vendor_message_id`),
  ADD KEY `chat_messages_company_id_foreign` (`company_id`);

--
-- Indexes for table `chat_sessions`
--
ALTER TABLE `chat_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_sessions_whatsapp_account_id_foreign` (`whatsapp_account_id`),
  ADD KEY `chat_sessions_customer_id_foreign` (`customer_id`),
  ADD KEY `chat_sessions_assigned_user_id_foreign` (`assigned_user_id`),
  ADD KEY `chat_sessions_company_id_foreign` (`company_id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `companies_slug_unique` (`slug`),
  ADD KEY `companies_pricing_plan_id_foreign` (`pricing_plan_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customers_assigned_to_foreign` (`assigned_to`),
  ADD KEY `customers_company_id_foreign` (`company_id`),
  ADD KEY `customers_customer_status_id_foreign` (`customer_status_id`);

--
-- Indexes for table `customer_statuses`
--
ALTER TABLE `customer_statuses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_statuses_company_id_name_unique` (`company_id`,`name`);

--
-- Indexes for table `external_apps`
--
ALTER TABLE `external_apps`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `external_apps_app_key_unique` (`app_key`),
  ADD UNIQUE KEY `external_apps_app_secret_unique` (`app_secret`),
  ADD KEY `external_apps_company_id_foreign` (`company_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `inventories`
--
ALTER TABLE `inventories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `inventories_sku_unique` (`sku`),
  ADD KEY `inventories_company_id_foreign` (`company_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`),
  ADD KEY `invoices_sales_order_id_foreign` (`sales_order_id`),
  ADD KEY `invoices_company_id_foreign` (`company_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leads_requests`
--
ALTER TABLE `leads_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `marketing_automations`
--
ALTER TABLE `marketing_automations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `marketing_automations_company_id_foreign` (`company_id`);

--
-- Indexes for table `marketing_blasts`
--
ALTER TABLE `marketing_blasts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `marketing_blasts_company_id_foreign` (`company_id`),
  ADD KEY `marketing_blasts_marketing_campaign_id_foreign` (`marketing_campaign_id`);

--
-- Indexes for table `marketing_campaigns`
--
ALTER TABLE `marketing_campaigns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `marketing_campaigns_company_id_foreign` (`company_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_order_number_unique` (`order_number`),
  ADD KEY `orders_company_id_foreign` (`company_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_company_id_foreign` (`company_id`),
  ADD KEY `payments_verified_by_foreign` (`verified_by`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `pricing_plans`
--
ALTER TABLE `pricing_plans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pricing_plans_slug_unique` (`slug`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_sku_unique` (`sku`),
  ADD KEY `products_company_id_foreign` (`company_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sales_orders`
--
ALTER TABLE `sales_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sales_orders_so_number_unique` (`so_number`),
  ADD KEY `sales_orders_customer_id_foreign` (`customer_id`),
  ADD KEY `sales_orders_company_id_foreign` (`company_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`),
  ADD KEY `settings_company_id_foreign` (`company_id`);

--
-- Indexes for table `staff_activities`
--
ALTER TABLE `staff_activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_activities_user_id_foreign` (`user_id`),
  ADD KEY `staff_activities_company_id_foreign` (`company_id`);

--
-- Indexes for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_movements_product_id_foreign` (`product_id`),
  ADD KEY `stock_movements_warehouse_id_foreign` (`warehouse_id`),
  ADD KEY `stock_movements_company_id_foreign` (`company_id`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `suppliers_company_id_foreign` (`company_id`);

--
-- Indexes for table `support_knowledge_base`
--
ALTER TABLE `support_knowledge_base`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `support_knowledge_base_slug_unique` (`slug`),
  ADD KEY `support_knowledge_base_company_id_foreign` (`company_id`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `support_tickets_ticket_number_unique` (`ticket_number`),
  ADD KEY `support_tickets_company_id_foreign` (`company_id`),
  ADD KEY `support_tickets_customer_id_foreign` (`customer_id`),
  ADD KEY `support_tickets_assigned_user_id_foreign` (`assigned_user_id`);

--
-- Indexes for table `support_ticket_messages`
--
ALTER TABLE `support_ticket_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `support_ticket_messages_support_ticket_id_foreign` (`support_ticket_id`),
  ADD KEY `support_ticket_messages_user_id_foreign` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_company_id_foreign` (`company_id`);

--
-- Indexes for table `warehouses`
--
ALTER TABLE `warehouses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `warehouses_company_id_foreign` (`company_id`);

--
-- Indexes for table `webhook_logs`
--
ALTER TABLE `webhook_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `whatsapp_accounts`
--
ALTER TABLE `whatsapp_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `whatsapp_accounts_phone_number_unique` (`phone_number`),
  ADD KEY `whatsapp_accounts_company_id_foreign` (`company_id`);

--
-- Indexes for table `whatsapp_agents`
--
ALTER TABLE `whatsapp_agents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `whatsapp_agents_user_id_foreign` (`user_id`),
  ADD KEY `whatsapp_agents_whatsapp_account_id_foreign` (`whatsapp_account_id`),
  ADD KEY `whatsapp_agents_company_id_foreign` (`company_id`);

--
-- Indexes for table `whatsapp_auto_replies`
--
ALTER TABLE `whatsapp_auto_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `whatsapp_auto_replies_company_id_foreign` (`company_id`),
  ADD KEY `whatsapp_auto_replies_whatsapp_account_id_foreign` (`whatsapp_account_id`);

--
-- Indexes for table `whatsapp_campaigns`
--
ALTER TABLE `whatsapp_campaigns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `whatsapp_campaigns_company_id_foreign` (`company_id`),
  ADD KEY `whatsapp_campaigns_whatsapp_account_id_foreign` (`whatsapp_account_id`);

--
-- Indexes for table `whatsapp_campaign_logs`
--
ALTER TABLE `whatsapp_campaign_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `whatsapp_campaign_logs_whatsapp_campaign_id_foreign` (`whatsapp_campaign_id`),
  ADD KEY `whatsapp_campaign_logs_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `whatsapp_templates`
--
ALTER TABLE `whatsapp_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `whatsapp_templates_company_id_foreign` (`company_id`),
  ADD KEY `whatsapp_templates_whatsapp_account_id_foreign` (`whatsapp_account_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ai_usages`
--
ALTER TABLE `ai_usages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `approvals`
--
ALTER TABLE `approvals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `chat_sessions`
--
ALTER TABLE `chat_sessions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `customer_statuses`
--
ALTER TABLE `customer_statuses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `external_apps`
--
ALTER TABLE `external_apps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventories`
--
ALTER TABLE `inventories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `leads_requests`
--
ALTER TABLE `leads_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `marketing_automations`
--
ALTER TABLE `marketing_automations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `marketing_blasts`
--
ALTER TABLE `marketing_blasts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `marketing_campaigns`
--
ALTER TABLE `marketing_campaigns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `pricing_plans`
--
ALTER TABLE `pricing_plans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sales_orders`
--
ALTER TABLE `sales_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `staff_activities`
--
ALTER TABLE `staff_activities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `support_knowledge_base`
--
ALTER TABLE `support_knowledge_base`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `support_ticket_messages`
--
ALTER TABLE `support_ticket_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `webhook_logs`
--
ALTER TABLE `webhook_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `whatsapp_accounts`
--
ALTER TABLE `whatsapp_accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `whatsapp_agents`
--
ALTER TABLE `whatsapp_agents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `whatsapp_auto_replies`
--
ALTER TABLE `whatsapp_auto_replies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `whatsapp_campaigns`
--
ALTER TABLE `whatsapp_campaigns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `whatsapp_campaign_logs`
--
ALTER TABLE `whatsapp_campaign_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `whatsapp_templates`
--
ALTER TABLE `whatsapp_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ai_usages`
--
ALTER TABLE `ai_usages`
  ADD CONSTRAINT `ai_usages_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `approvals`
--
ALTER TABLE `approvals`
  ADD CONSTRAINT `approvals_approver_id_foreign` FOREIGN KEY (`approver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `approvals_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `approvals_requester_id_foreign` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_chat_session_id_foreign` FOREIGN KEY (`chat_session_id`) REFERENCES `chat_sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_messages_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `chat_sessions`
--
ALTER TABLE `chat_sessions`
  ADD CONSTRAINT `chat_sessions_assigned_user_id_foreign` FOREIGN KEY (`assigned_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `chat_sessions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_sessions_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_sessions_whatsapp_account_id_foreign` FOREIGN KEY (`whatsapp_account_id`) REFERENCES `whatsapp_accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `companies`
--
ALTER TABLE `companies`
  ADD CONSTRAINT `companies_pricing_plan_id_foreign` FOREIGN KEY (`pricing_plan_id`) REFERENCES `pricing_plans` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `customers_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `customers_customer_status_id_foreign` FOREIGN KEY (`customer_status_id`) REFERENCES `customer_statuses` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `customer_statuses`
--
ALTER TABLE `customer_statuses`
  ADD CONSTRAINT `customer_statuses_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `external_apps`
--
ALTER TABLE `external_apps`
  ADD CONSTRAINT `external_apps_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventories`
--
ALTER TABLE `inventories`
  ADD CONSTRAINT `inventories_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoices_sales_order_id_foreign` FOREIGN KEY (`sales_order_id`) REFERENCES `sales_orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `marketing_automations`
--
ALTER TABLE `marketing_automations`
  ADD CONSTRAINT `marketing_automations_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `marketing_blasts`
--
ALTER TABLE `marketing_blasts`
  ADD CONSTRAINT `marketing_blasts_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `marketing_blasts_marketing_campaign_id_foreign` FOREIGN KEY (`marketing_campaign_id`) REFERENCES `marketing_campaigns` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `marketing_campaigns`
--
ALTER TABLE `marketing_campaigns`
  ADD CONSTRAINT `marketing_campaigns_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sales_orders`
--
ALTER TABLE `sales_orders`
  ADD CONSTRAINT `sales_orders_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sales_orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `settings`
--
ALTER TABLE `settings`
  ADD CONSTRAINT `settings_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_activities`
--
ALTER TABLE `staff_activities`
  ADD CONSTRAINT `staff_activities_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `staff_activities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_movements_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_movements_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD CONSTRAINT `suppliers_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `support_knowledge_base`
--
ALTER TABLE `support_knowledge_base`
  ADD CONSTRAINT `support_knowledge_base_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD CONSTRAINT `support_tickets_assigned_user_id_foreign` FOREIGN KEY (`assigned_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `support_tickets_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `support_tickets_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `support_ticket_messages`
--
ALTER TABLE `support_ticket_messages`
  ADD CONSTRAINT `support_ticket_messages_support_ticket_id_foreign` FOREIGN KEY (`support_ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `support_ticket_messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `warehouses`
--
ALTER TABLE `warehouses`
  ADD CONSTRAINT `warehouses_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `whatsapp_accounts`
--
ALTER TABLE `whatsapp_accounts`
  ADD CONSTRAINT `whatsapp_accounts_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `whatsapp_agents`
--
ALTER TABLE `whatsapp_agents`
  ADD CONSTRAINT `whatsapp_agents_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `whatsapp_agents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `whatsapp_agents_whatsapp_account_id_foreign` FOREIGN KEY (`whatsapp_account_id`) REFERENCES `whatsapp_accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `whatsapp_auto_replies`
--
ALTER TABLE `whatsapp_auto_replies`
  ADD CONSTRAINT `whatsapp_auto_replies_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `whatsapp_auto_replies_whatsapp_account_id_foreign` FOREIGN KEY (`whatsapp_account_id`) REFERENCES `whatsapp_accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `whatsapp_campaigns`
--
ALTER TABLE `whatsapp_campaigns`
  ADD CONSTRAINT `whatsapp_campaigns_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `whatsapp_campaigns_whatsapp_account_id_foreign` FOREIGN KEY (`whatsapp_account_id`) REFERENCES `whatsapp_accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `whatsapp_campaign_logs`
--
ALTER TABLE `whatsapp_campaign_logs`
  ADD CONSTRAINT `whatsapp_campaign_logs_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `whatsapp_campaign_logs_whatsapp_campaign_id_foreign` FOREIGN KEY (`whatsapp_campaign_id`) REFERENCES `whatsapp_campaigns` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `whatsapp_templates`
--
ALTER TABLE `whatsapp_templates`
  ADD CONSTRAINT `whatsapp_templates_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `whatsapp_templates_whatsapp_account_id_foreign` FOREIGN KEY (`whatsapp_account_id`) REFERENCES `whatsapp_accounts` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
