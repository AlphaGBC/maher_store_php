<?php
include "../connect.php";

/**
 * Expected JSON input:
 * {
 *   "invoice_date": "2026-02-15 12:00:00",
 *   "items": [
 *     {
 *       "items_id": 1,
 *       "supplier_id": 1,
 *       "storehouse_count": 10,
 *       "pos1_count": 5,
 *       "pos2_count": 2,
 *       "cost_price": 100.00,
 *       "note": "Some note"
 *     },
 *     ...
 *   ]
 * }
 */

$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);

if (!$data || !isset($data['items']) || empty($data['items'])) {
    printFailure("Invalid data provided");
    exit;
}

try {
    $con->beginTransaction();

    // 1. Insert into incoming_invoices
    $invoice_date = isset($data['invoice_date']) ? $data['invoice_date'] : date("Y-m-d H:i:s");
    $stmt_invoice = $con->prepare("INSERT INTO `incoming_invoices` (`invoice_date`) VALUES (?)");
    $stmt_invoice->execute([$invoice_date]);
    $invoice_id = $con->lastInsertId();

    // Prepare statements for reuse
    $stmt_item_insert = $con->prepare("INSERT INTO `incoming_invoice_items` 
        (`items_invoice_id`, `items_supplier_id`, `incoming_invoice_items_items_id`, `storehouse_count`, `pos1_count`, `pos2_count`, `cost_price`, `incoming_invoice_items_note`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt_item_update = $con->prepare("UPDATE `items` SET 
        `items_storehouse_count` = `items_storehouse_count` + ?, 
        `items_pointofsale1_count` = `items_pointofsale1_count` + ?, 
        `items_pointofsale2_count` = `items_pointofsale2_count` + ?, 
        `items_cost_price` = ?, 
        `items_wholesale_price` = ?, 
        `items_retail_price` = ? 
        WHERE `items_id` = ?");

    foreach ($data['items'] as $item) {
        $items_id = $item['items_id'];
        $supplier_id = $item['supplier_id'];
        $storehouse_count = $item['storehouse_count'];
        $pos1_count = $item['pos1_count'];
        $pos2_count = $item['pos2_count'];
        $cost_price = $item['cost_price'];
        $note = isset($item['note']) ? $item['note'] : "";

        // 2. Insert into incoming_invoice_items
        $stmt_item_insert->execute([
            $invoice_id,
            $supplier_id,
            $items_id,
            $storehouse_count,
            $pos1_count,
            $pos2_count,
            $cost_price,
            $note
        ]);

        // 3. Calculate new prices
        $wholesale_price = $cost_price + ($cost_price * 0.10);
        $retail_price = $cost_price + ($cost_price * 0.25);

        // 4. Update items table
        $stmt_item_update->execute([
            $storehouse_count,
            $pos1_count,
            $pos2_count,
            $cost_price,
            $wholesale_price,
            $retail_price,
            $items_id
        ]);
    }

    $con->commit();
    printSuccess("Invoice added and inventory updated successfully");

} catch (Exception $e) {
    $con->rollBack();
    printFailure("Error: " . $e->getMessage());
}
?>
