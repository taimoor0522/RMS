-- Supplier Management Tables
-- Add these tables to your rposystem database

-- --------------------------------------------------------
--
-- Table structure for table `rpos_product_categories`
--

CREATE TABLE IF NOT EXISTS `rpos_product_categories` (
  `category_id` varchar(200) NOT NULL,
  `category_name` varchar(200) NOT NULL,
  `category_description` text,
  `created_at` timestamp(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------
--
-- Table structure for table `rpos_suppliers`
--

CREATE TABLE IF NOT EXISTS `rpos_suppliers` (
  `supplier_id` varchar(200) NOT NULL,
  `supplier_code` varchar(200) NOT NULL,
  `supplier_name` varchar(200) NOT NULL,
  `supplier_email` varchar(200) NOT NULL,
  `supplier_phone` varchar(200) NOT NULL,
  `supplier_address` text,
  `supplier_contact_person` varchar(200),
  `supplier_status` varchar(50) DEFAULT 'Active',
  `created_at` timestamp(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  PRIMARY KEY (`supplier_id`),
  UNIQUE KEY `supplier_code` (`supplier_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------
--
-- Table structure for table `rpos_supplier_items`
--

CREATE TABLE IF NOT EXISTS `rpos_supplier_items` (
  `item_id` varchar(200) NOT NULL,
  `item_code` varchar(200) NOT NULL,
  `item_name` varchar(200) NOT NULL,
  `category_id` varchar(200) NOT NULL,
  `supplier_id` varchar(200) NOT NULL,
  `item_description` text,
  `item_price` decimal(10,2) NOT NULL,
  `item_unit` varchar(50) DEFAULT 'unit',
  `item_stock` int(11) DEFAULT 0,
  `item_min_stock` int(11) DEFAULT 0,
  `created_at` timestamp(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  PRIMARY KEY (`item_id`),
  KEY `category_id` (`category_id`),
  KEY `supplier_id` (`supplier_id`),
  CONSTRAINT `supplier_items_category` FOREIGN KEY (`category_id`) REFERENCES `rpos_product_categories` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `supplier_items_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `rpos_suppliers` (`supplier_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------
--
-- Table structure for table `rpos_supplier_orders`
--

CREATE TABLE IF NOT EXISTS `rpos_supplier_orders` (
  `order_id` varchar(200) NOT NULL,
  `order_code` varchar(200) NOT NULL,
  `supplier_id` varchar(200) NOT NULL,
  `staff_id` int(20) NOT NULL,
  `item_id` varchar(200) NOT NULL,
  `item_name` varchar(200) NOT NULL,
  `item_qty` int(11) NOT NULL,
  `item_price` decimal(10,2) NOT NULL,
  `order_total` decimal(10,2) NOT NULL,
  `order_status` varchar(50) DEFAULT 'Pending',
  `order_notes` text,
  `created_at` timestamp(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  PRIMARY KEY (`order_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `staff_id` (`staff_id`),
  KEY `item_id` (`item_id`),
  CONSTRAINT `supplier_orders_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `rpos_suppliers` (`supplier_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `supplier_orders_staff` FOREIGN KEY (`staff_id`) REFERENCES `rpos_staff` (`staff_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `supplier_orders_item` FOREIGN KEY (`item_id`) REFERENCES `rpos_supplier_items` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
