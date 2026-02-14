<?php
include '../connect.php';

$table = "ordercard";

// Read posted values (safe defaults)
$orders_id = isset($_POST['orders_id']) ? intval($_POST['orders_id']) : null;
$wholesale_customers_name = isset($_POST['wholesale_customers_name']) ? filterRequest('wholesale_customers_name') : null;
$total_items_count = isset($_POST['total_items_count']) ? intval($_POST['total_items_count']) : 0;
$subtotal = isset($_POST['subtotal']) ? floatval($_POST['subtotal']) : 0.0;
$discount_amount = isset($_POST['discount_amount']) ? floatval($_POST['discount_amount']) : 0.0;
$total = isset($_POST['total']) ? floatval($_POST['total']) : 0.0;
$pos_source = isset($_POST['pos_source']) ? intval($_POST['pos_source']) : 1;
$created_at = isset($_POST['created_at']) ? filterRequest('created_at') : date('Y-m-d H:i:s');

// Validate required
if ($orders_id === null) {
    echo json_encode(array("status" => "failure", "message" => "orders_id missing"));
    exit;
}

// Prepare header data (only order card fields)
$data = array(
    "orders_id" => $orders_id,
    "wholesale_customers_name" => $wholesale_customers_name,
    "total_items_count" => $total_items_count,
    "subtotal" => $subtotal,
    "discount_amount" => $discount_amount,
    "total" => $total,
    "pos_source" => $pos_source,
    "created_at" => $created_at
);

// Insert header (do not auto-echo JSON here)
$headerInserted = insertData($table, $data, false);

$itemsInserted = 0;
$items_json = isset($_POST['items']) ? $_POST['items'] : null;

if ($headerInserted > 0 && $items_json) {
    $items = json_decode($items_json, true);
    if (is_array($items)) {
        foreach ($items as $it) {
            $item_orders_id = $orders_id;
            $item_items_id = isset($it['items_id']) ? intval($it['items_id']) : null;
            $item_name = isset($it['items_name']) ? htmlspecialchars(strip_tags($it['items_name'])) : null;
            $item_qty = isset($it['items_quantity']) ? intval($it['items_quantity']) : 0;
            $item_unit_price = isset($it['items_unit_price']) ? floatval($it['items_unit_price']) : 0.0;
            $item_discount_percentage = isset($it['items_discount_percentage']) ? floatval($it['items_discount_percentage']) : 0.0;
            $item_total_price = isset($it['items_total_price']) ? floatval($it['items_total_price']) : 0.0;

            $itemData = array(
                "orders_id" => $item_orders_id,
                "items_id" => $item_items_id,
                "items_name" => $item_name,
                "items_quantity" => $item_qty,
                "items_unit_price" => $item_unit_price,
                "items_discount_percentage" => $item_discount_percentage,
                "items_total_price" => $item_total_price
            );

            insertData("ordercard_items", $itemData, false);
            $itemsInserted++;
        }
    }
}

// Final response
if ($headerInserted > 0) {
    echo json_encode(array(
        "status" => "success",
        "orders_inserted" => intval($headerInserted),
        "items_inserted" => intval($itemsInserted)
    ));
    sendFCM("تنبيه", "تم استلام فاتورة جديدة ","admin", "", "refreshorders" , $accessToken);
} else {
    echo json_encode(array("status" => "failure"));
}