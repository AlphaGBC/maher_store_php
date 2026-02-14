-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- مضيف: 127.0.0.1
-- وقت الجيل: 14 فبراير 2026 الساعة 23:12
-- إصدار الخادم: 11.8.3-MariaDB-log
-- نسخة PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- قاعدة بيانات: `u451877070_MaherStore`
--

-- --------------------------------------------------------

--
-- بنية الجدول `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `admin_name` varchar(100) NOT NULL,
  `admin_password` varchar(100) NOT NULL,
  `admin_email` varchar(255) NOT NULL,
  `admin_phone` varchar(100) NOT NULL,
  `admin_approve` tinyint(4) NOT NULL DEFAULT 0,
  `admin_create` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `admin`
--

INSERT INTO `admin` (`admin_id`, `admin_name`, `admin_password`, `admin_email`, `admin_phone`, `admin_approve`, `admin_create`) VALUES
(1, 'admin', 'admin123admin', 'admin@maherstore.com', '0988888888', 1, '2025-12-17 13:26:30');

-- --------------------------------------------------------

--
-- بنية الجدول `categories`
--

CREATE TABLE `categories` (
  `categories_id` int(11) NOT NULL,
  `categories_name` varchar(100) NOT NULL,
  `categories_image` varchar(255) NOT NULL,
  `categories_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `categories`
--

INSERT INTO `categories` (`categories_id`, `categories_name`, `categories_image`, `categories_date`) VALUES
(1, 'قطع تبريد', '1.png', '2026-01-05 12:14:04'),
(2, 'قطع غسالات', '2.png', '2026-01-05 12:14:04'),
(3, 'قطع مكيفات', '3.png', '2026-01-05 12:14:04'),
(4, 'إلكترونيات', '4.png', '2026-01-05 12:14:04'),
(5, 'عدد وخردوات', '5.png', '2026-01-05 12:14:04'),
(6, 'قطع مراوح', '6.png', '2026-01-07 03:00:36');

-- --------------------------------------------------------

--
-- بنية الجدول `incoming_invoices`
--

CREATE TABLE `incoming_invoices` (
  `invoice_id` int(11) NOT NULL,
  `invoice_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- إرجاع أو استيراد بيانات الجدول `incoming_invoices`
--

INSERT INTO `incoming_invoices` (`invoice_id`, `invoice_date`) VALUES
(2, '2026-02-13 13:43:57');

-- --------------------------------------------------------

--
-- بنية الجدول `incoming_invoice_items`
--

CREATE TABLE `incoming_invoice_items` (
  `incoming_invoice_items_id` int(11) NOT NULL,
  `items_invoice_id` int(11) NOT NULL,
  `items_supplier_id` int(11) NOT NULL,
  `incoming_invoice_items_items_id` int(11) NOT NULL,
  `storehouse_count` int(11) DEFAULT 0,
  `pos1_count` int(11) DEFAULT 0,
  `pos2_count` int(11) DEFAULT 0,
  `cost_price` decimal(10,2) DEFAULT 0.00,
  `incoming_invoice_items_note` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- إرجاع أو استيراد بيانات الجدول `incoming_invoice_items`
--

INSERT INTO `incoming_invoice_items` (`incoming_invoice_items_id`, `items_invoice_id`, `items_supplier_id`, `incoming_invoice_items_items_id`, `storehouse_count`, `pos1_count`, `pos2_count`, `cost_price`, `incoming_invoice_items_note`) VALUES
(3, 2, 1, 1, 10, 1, 2, 100.00, 'ملاحظة اول');

-- --------------------------------------------------------

--
-- Stand-in structure for view `incoming_invoice_itemsview`
-- (See below for the actual view)
--
CREATE TABLE `incoming_invoice_itemsview` (
`incoming_invoice_items_id` int(11)
,`items_invoice_id` int(11)
,`items_supplier_id` int(11)
,`incoming_invoice_items_items_id` int(11)
,`storehouse_count` int(11)
,`pos1_count` int(11)
,`pos2_count` int(11)
,`cost_price` decimal(10,2)
,`incoming_invoice_items_note` varchar(1000)
,`invoice_id` int(11)
,`invoice_date` timestamp
,`supplier_id` int(11)
,`supplier_name` varchar(255)
,`supplier_date` timestamp
,`items_name` varchar(100)
);

-- --------------------------------------------------------


--
-- بنية الجدول `items`
--

CREATE TABLE `items` (
  `items_id` int(11) NOT NULL,
  `items_name` varchar(100) NOT NULL,
  `items_storehouse_count` int(11) NOT NULL,
  `items_pointofsale1_count` int(11) NOT NULL,
  `items_pointofsale2_count` int(11) NOT NULL,
  `items_cost_price` float NOT NULL DEFAULT 0,
  `items_wholesale_price` float NOT NULL DEFAULT 0,
  `items_retail_price` float NOT NULL DEFAULT 0,
  `items_wholesale_discount` smallint(6) NOT NULL DEFAULT 0,
  `items_retail_discount` smallint(6) NOT NULL DEFAULT 0,
  `items_qr` varchar(1000) NOT NULL,
  `items_categories` int(11) NOT NULL,
  `items_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `items`
--

INSERT INTO `items` (`items_id`, `items_name`, `items_storehouse_count`, `items_pointofsale1_count`, `items_pointofsale2_count`, `items_cost_price`, `items_wholesale_price`, `items_retail_price`, `items_wholesale_discount`, `items_retail_discount`, `items_qr`, `items_categories`, `items_date`) VALUES
(1, 'ورد', 15, 2, 5, 100, 102, 105, 0, 0, '', 1, '2026-01-26 17:41:30'),
(2, 'غسالة ورد', 5, 0, 0, 200, 250, 280, 0, 0, '', 2, '2026-01-26 17:55:55'),
(8, '6203 nsk', 0, 0, 0, 2.5, 2.75, 3.125, 1, 1, '', 2, '2026-01-25 19:18:13'),
(9, '6204 nsk', 0, 0, 0, 3, 3.3, 3.75, 1, 1, '', 2, '2026-01-25 19:20:36'),
(10, 'ماهر', 3, 1, 1, 20, 25, 28, 0, 0, '', 2, '2026-01-26 17:55:55'),
(11, 'اختبار', 10, 10, 10, 10, 11, 12.5, 1, 1, 'http://en.m.wikipedia.org', 1, '2026-01-26 11:10:06'),
(12, 'براد ماهر ', 10, 2, 3, 15, 16.5, 18.75, 0, 0, '', 1, '2026-01-26 11:15:52'),
(13, 'ابو عوض', 40, 575, 8268, 75928, 83520.8, 94910, 3, 8, '', 1, '2026-01-26 11:22:59'),
(14, 'سويتش هايلايف حديث', 68, 84, 88, 588, 646.8, 735, 8, 3, 'helloweba.com欢迎您', 2, '2026-01-26 17:55:55');

-- --------------------------------------------------------

--
-- Stand-in structure for view `itemsview`
-- (See below for the actual view)
--
CREATE TABLE `itemsview` (
`items_id` int(11)
,`items_name` varchar(100)
,`items_storehouse_count` int(11)
,`items_pointofsale1_count` int(11)
,`items_pointofsale2_count` int(11)
,`items_cost_price` float
,`items_wholesale_price` float
,`items_retail_price` float
,`items_wholesale_discount` smallint(6)
,`items_retail_discount` smallint(6)
,`items_qr` varchar(1000)
,`items_categories` int(11)
,`items_date` timestamp
,`categories_id` int(11)
,`categories_name` varchar(100)
,`categories_image` varchar(255)
,`categories_date` timestamp
,`itemswholesalepricediscount` double
,`itemsretailpricediscount` double
);

-- --------------------------------------------------------

--
-- بنية الجدول `ordercard`
--

CREATE TABLE `ordercard` (
  `orders_id` int(11) NOT NULL,
  `wholesale_customers_name` varchar(255) DEFAULT NULL,
  `total_items_count` int(11) DEFAULT 0,
  `subtotal` decimal(12,2) DEFAULT 0.00,
  `discount_amount` decimal(12,2) DEFAULT 0.00,
  `total` decimal(12,2) DEFAULT 0.00,
  `pos_source` tinyint(4) DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `received_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `ordercard`
--

INSERT INTO `ordercard` (`orders_id`, `wholesale_customers_name`, `total_items_count`, `subtotal`, `discount_amount`, `total`, `pos_source`, `created_at`, `received_at`) VALUES
(1, '', 1, 280.00, 0.00, 280.00, 1, '2026-01-24 13:05:52', '2026-01-24 13:07:10'),
(2, '', 1, 280.00, 0.00, 280.00, 1, '2026-01-26 11:13:48', '2026-01-26 11:14:02'),
(3, '', 3, 1043.00, 22.05, 1020.95, 1, '2026-01-26 18:55:55', '2026-01-26 19:01:37'),
(4, '', 5, 2968.00, 88.20, 2879.80, 1, '2026-01-26 18:59:54', '2026-01-26 19:03:20');

-- --------------------------------------------------------

--
-- بنية الجدول `ordercard_items`
--

CREATE TABLE `ordercard_items` (
  `id` int(11) NOT NULL,
  `orders_id` int(11) NOT NULL,
  `items_id` int(11) DEFAULT NULL,
  `items_name` varchar(255) DEFAULT NULL,
  `items_quantity` int(11) DEFAULT 0,
  `items_unit_price` decimal(12,2) DEFAULT 0.00,
  `items_discount_percentage` decimal(6,2) DEFAULT 0.00,
  `items_total_price` decimal(12,2) DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `ordercard_items`
--

INSERT INTO `ordercard_items` (`id`, `orders_id`, `items_id`, `items_name`, `items_quantity`, `items_unit_price`, `items_discount_percentage`, `items_total_price`, `created_at`) VALUES
(16, 1, 2, 'غسالة ورد', 1, 280.00, 0.00, 280.00, '2026-01-24 13:07:10'),
(17, 2, 2, 'غسالة ورد', 1, 280.00, 0.00, 280.00, '2026-01-26 11:14:02'),
(18, 3, 2, 'غسالة ورد', 1, 280.00, 0.00, 280.00, '2026-01-26 19:01:37'),
(19, 3, 10, 'ماهر', 1, 28.00, 0.00, 28.00, '2026-01-26 19:01:37'),
(20, 3, 14, 'سويتش هايلايف حديث', 1, 712.95, 3.00, 712.95, '2026-01-26 19:01:37'),
(21, 4, 10, 'ماهر', 1, 28.00, 0.00, 28.00, '2026-01-26 19:03:20'),
(22, 4, 14, 'سويتش هايلايف حديث', 4, 712.95, 3.00, 2851.80, '2026-01-26 19:03:20');

-- --------------------------------------------------------

--
-- بنية الجدول `supplier`
--

CREATE TABLE `supplier` (
  `supplier_id` int(11) NOT NULL,
  `supplier_name` varchar(255) NOT NULL,
  `supplier_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `supplier`
--

INSERT INTO `supplier` (`supplier_id`, `supplier_name`, `supplier_date`) VALUES
(1, 'مورد اول', '2026-01-31 19:01:41'),
(2, 'مورد ثاني', '2026-01-31 19:01:41');

-- --------------------------------------------------------

--
-- بنية الجدول `usd`
--

CREATE TABLE `usd` (
  `usd_id` int(11) NOT NULL,
  `usd_price` varchar(100) NOT NULL,
  `usd_data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `usd`
--

INSERT INTO `usd` (`usd_id`, `usd_price`, `usd_data`) VALUES
(1, '12000', '2025-12-15 18:58:01');

-- --------------------------------------------------------

--
-- بنية الجدول `wholesale_customers`
--

CREATE TABLE `wholesale_customers` (
  `wholesale_customers_id` int(11) NOT NULL,
  `wholesale_customers_name` varchar(100) NOT NULL,
  `wholesale_customers_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `wholesale_customers`
--

INSERT INTO `wholesale_customers` (`wholesale_customers_id`, `wholesale_customers_name`, `wholesale_customers_date`) VALUES
(1, 'مشتري جملة 1', '2025-12-26 00:48:40'),
(2, 'مشتري جملة 2', '2025-12-26 00:48:40'),
(4, 'أيهم علي', '2026-01-25 19:24:50');

--
-- Indexes for dumped tables
--

--
-- فهارس للجدول `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- فهارس للجدول `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`categories_id`);

--
-- فهارس للجدول `incoming_invoices`
--
ALTER TABLE `incoming_invoices`
  ADD PRIMARY KEY (`invoice_id`);

--
-- فهارس للجدول `incoming_invoice_items`
--
ALTER TABLE `incoming_invoice_items`
  ADD PRIMARY KEY (`incoming_invoice_items_id`),
  ADD KEY `fk_invoice` (`items_invoice_id`),
  ADD KEY `items_supplier_id` (`items_supplier_id`),
  ADD KEY `incoming_invoice_items_items_id` (`incoming_invoice_items_items_id`);

--
-- فهارس للجدول `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`items_id`),
  ADD KEY `items_categories` (`items_categories`);

--
-- فهارس للجدول `ordercard`
--
ALTER TABLE `ordercard`
  ADD PRIMARY KEY (`orders_id`);

--
-- فهارس للجدول `ordercard_items`
--
ALTER TABLE `ordercard_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ordercard_items_orders` (`orders_id`);

--
-- فهارس للجدول `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`supplier_id`);

--
-- فهارس للجدول `usd`
--
ALTER TABLE `usd`
  ADD PRIMARY KEY (`usd_id`);

--
-- فهارس للجدول `wholesale_customers`
--
ALTER TABLE `wholesale_customers`
  ADD PRIMARY KEY (`wholesale_customers_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `categories_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `incoming_invoices`
--
ALTER TABLE `incoming_invoices`
  MODIFY `invoice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `incoming_invoice_items`
--
ALTER TABLE `incoming_invoice_items`
  MODIFY `incoming_invoice_items_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `items_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `ordercard_items`
--
ALTER TABLE `ordercard_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `usd`
--
ALTER TABLE `usd`
  MODIFY `usd_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `wholesale_customers`
--
ALTER TABLE `wholesale_customers`
  MODIFY `wholesale_customers_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

-- --------------------------------------------------------

--
-- Structure for view `incoming_invoice_itemsview`
--
DROP TABLE IF EXISTS `incoming_invoice_itemsview`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u451877070_maher`@`127.0.0.1` SQL SECURITY DEFINER VIEW `incoming_invoice_itemsview`  AS SELECT `incoming_invoice_items`.`incoming_invoice_items_id` AS `incoming_invoice_items_id`, `incoming_invoice_items`.`items_invoice_id` AS `items_invoice_id`, `incoming_invoice_items`.`items_supplier_id` AS `items_supplier_id`, `incoming_invoice_items`.`incoming_invoice_items_items_id` AS `incoming_invoice_items_items_id`, `incoming_invoice_items`.`storehouse_count` AS `storehouse_count`, `incoming_invoice_items`.`pos1_count` AS `pos1_count`, `incoming_invoice_items`.`pos2_count` AS `pos2_count`, `incoming_invoice_items`.`cost_price` AS `cost_price`, `incoming_invoice_items`.`incoming_invoice_items_note` AS `incoming_invoice_items_note`, `incoming_invoices`.`invoice_id` AS `invoice_id`, `incoming_invoices`.`invoice_date` AS `invoice_date`, `supplier`.`supplier_id` AS `supplier_id`, `supplier`.`supplier_name` AS `supplier_name`, `supplier`.`supplier_date` AS `supplier_date`, `items`.`items_name` AS `items_name` FROM (((`incoming_invoice_items` join `incoming_invoices` on(`incoming_invoices`.`invoice_id` = `incoming_invoice_items`.`items_invoice_id`)) join `supplier` on(`supplier`.`supplier_id` = `incoming_invoice_items`.`items_supplier_id`)) join `items` on(`items`.`items_id` = `incoming_invoice_items`.`incoming_invoice_items_items_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `itemsview`
--
DROP TABLE IF EXISTS `itemsview`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u451877070_maher`@`127.0.0.1` SQL SECURITY DEFINER VIEW `itemsview`  AS SELECT `items`.`items_id` AS `items_id`, `items`.`items_name` AS `items_name`, `items`.`items_storehouse_count` AS `items_storehouse_count`, `items`.`items_pointofsale1_count` AS `items_pointofsale1_count`, `items`.`items_pointofsale2_count` AS `items_pointofsale2_count`, `items`.`items_cost_price` AS `items_cost_price`, `items`.`items_wholesale_price` AS `items_wholesale_price`, `items`.`items_retail_price` AS `items_retail_price`, `items`.`items_wholesale_discount` AS `items_wholesale_discount`, `items`.`items_retail_discount` AS `items_retail_discount`, `items`.`items_qr` AS `items_qr`, `items`.`items_categories` AS `items_categories`, `items`.`items_date` AS `items_date`, `categories`.`categories_id` AS `categories_id`, `categories`.`categories_name` AS `categories_name`, `categories`.`categories_image` AS `categories_image`, `categories`.`categories_date` AS `categories_date`, `items`.`items_wholesale_price`- `items`.`items_wholesale_price` * `items`.`items_wholesale_discount` / 100 AS `itemswholesalepricediscount`, `items`.`items_retail_price`- `items`.`items_retail_price` * `items`.`items_retail_discount` / 100 AS `itemsretailpricediscount` FROM (`items` join `categories` on(`categories`.`categories_id` = `items`.`items_categories`)) ;

--
-- القيود المفروضة على الجداول الملقاة
--

--
-- قيود الجداول `incoming_invoice_items`
--
ALTER TABLE `incoming_invoice_items`
  ADD CONSTRAINT `fk_invoice` FOREIGN KEY (`items_invoice_id`) REFERENCES `incoming_invoices` (`invoice_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `incoming_invoice_items_ibfk_1` FOREIGN KEY (`items_supplier_id`) REFERENCES `supplier` (`supplier_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `incoming_invoice_items_ibfk_2` FOREIGN KEY (`incoming_invoice_items_items_id`) REFERENCES `items` (`items_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- قيود الجداول `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`items_categories`) REFERENCES `categories` (`categories_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- قيود الجداول `ordercard_items`
--
ALTER TABLE `ordercard_items`
  ADD CONSTRAINT `fk_ordercard_items_orders` FOREIGN KEY (`orders_id`) REFERENCES `ordercard` (`orders_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
