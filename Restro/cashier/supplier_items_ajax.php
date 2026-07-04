<?php
session_start();
include('config/config.php');
include('config/checklogin.php');

$supplier_id = $_GET['supplier_id'] ?? '';

echo '<option value="">Select Item</option>';

if (!empty($supplier_id)) {
    $table_check = $mysqli->query("SHOW TABLES LIKE 'rpos_supplier_items'");
    if ($table_check->num_rows > 0) {
        $ret = "SELECT * FROM rpos_supplier_items WHERE supplier_id = ? ORDER BY item_name";
        $stmt = $mysqli->prepare($ret);
        if ($stmt) {
            $stmt->bind_param('s', $supplier_id);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($item = $res->fetch_object()) {
                echo "<option value='$item->item_id' data-price='$item->item_price'>$item->item_name - Rs. " . number_format($item->item_price, 2) . "</option>";
            }
        }
    }
}
?>
