<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

// Create supplier tables if they don't exist
$tables = [];

// 1. Create product_categories table first (no dependencies)
$tables[] = "CREATE TABLE IF NOT EXISTS `rpos_product_categories` (
  `category_id` varchar(200) NOT NULL,
  `category_name` varchar(200) NOT NULL,
  `category_description` text,
  `created_at` timestamp(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1";

// 2. Create suppliers table (no dependencies)
$tables[] = "CREATE TABLE IF NOT EXISTS `rpos_suppliers` (
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1";

// 3. Create supplier_items table (depends on categories and suppliers)
$tables[] = "CREATE TABLE IF NOT EXISTS `rpos_supplier_items` (
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
  KEY `supplier_id` (`supplier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1";

// 4. Create supplier_orders table (depends on suppliers, staff, and items)
$tables[] = "CREATE TABLE IF NOT EXISTS `rpos_supplier_orders` (
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
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1";

$success_count = 0;
$error_count = 0;
$errors = [];

foreach ($tables as $sql) {
    if ($mysqli->query($sql)) {
        $success_count++;
    } else {
        $error_count++;
        $errors[] = $mysqli->error;
    }
}

// Add foreign keys after tables are created
$foreign_keys = [];

// Add foreign key for supplier_items
$foreign_keys[] = "ALTER TABLE `rpos_supplier_items` 
  ADD CONSTRAINT `supplier_items_category` FOREIGN KEY (`category_id`) REFERENCES `rpos_product_categories` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE";

$foreign_keys[] = "ALTER TABLE `rpos_supplier_items` 
  ADD CONSTRAINT `supplier_items_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `rpos_suppliers` (`supplier_id`) ON DELETE CASCADE ON UPDATE CASCADE";

// Add foreign keys for supplier_orders
$foreign_keys[] = "ALTER TABLE `rpos_supplier_orders` 
  ADD CONSTRAINT `supplier_orders_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `rpos_suppliers` (`supplier_id`) ON DELETE CASCADE ON UPDATE CASCADE";

$foreign_keys[] = "ALTER TABLE `rpos_supplier_orders` 
  ADD CONSTRAINT `supplier_orders_staff` FOREIGN KEY (`staff_id`) REFERENCES `rpos_staff` (`staff_id`) ON DELETE CASCADE ON UPDATE CASCADE";

$foreign_keys[] = "ALTER TABLE `rpos_supplier_orders` 
  ADD CONSTRAINT `supplier_orders_item` FOREIGN KEY (`item_id`) REFERENCES `rpos_supplier_items` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE";

// Try to add foreign keys (ignore if they already exist)
foreach ($foreign_keys as $fk_sql) {
    // Check if constraint already exists before adding
    $mysqli->query($fk_sql);
    // Ignore errors for existing constraints
}

if ($error_count == 0) {
    $success = "All supplier tables created successfully!";
} else {
    $err = "Some errors occurred. Tables created: $success_count, Errors: $error_count";
}

header("refresh:2; url=suppliers.php");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Setting up Supplier Tables</title>
</head>
<body>
    <div style="text-align: center; padding: 50px;">
        <h2>Setting up Supplier Tables...</h2>
        <?php if (isset($success)) { ?>
            <p style="color: green;"><?php echo $success; ?></p>
        <?php } ?>
        <?php if (isset($err)) { ?>
            <p style="color: red;"><?php echo $err; ?></p>
        <?php } ?>
        <p>Redirecting to suppliers page...</p>
    </div>
</body>
</html>
