<?php
include "../connect.php";

/**
 * Expected JSON input:
 * {
 *   "incoming_invoice_items_id": 3,
 *   "items_id": 1,
 *   "storehouse_count": 10,
 *   "pos1_count": 5,
 *   "pos2_count": 2,
 *   "cost_price": 100.00,
 *   "note": "Updated note"
 * }
 */

$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);

if (!$data || !isset($data['incoming_invoice_items_id'])) {
    printFailure("Invalid data provided");
    exit;
}

try {
    $con->beginTransaction();

    $id = $data['incoming_invoice_items_id'];
    $items_id = $data['items_id'];
    $new_s_count = $data['storehouse_count'];
    $new_p1_count = $data['pos1_count'];
    $new_p2_count = $data['pos2_count'];
    $new_cost = $data['cost_price'];
    $note = $data['note'];

    // 1. Get old values to adjust items table
    $stmt_old = $con->prepare("SELECT storehouse_count, pos1_count, pos2_count FROM incoming_invoice_items WHERE incoming_invoice_items_id = ?");
    $stmt_old->execute([$id]);
    $old = $stmt_old->fetch(PDO::FETCH_ASSOC);

    if (!$old) {
        throw new Exception("Item not found");
    }

    // 2. Update incoming_invoice_items
    $stmt_update_entry = $con->prepare("UPDATE `incoming_invoice_items` SET 
        `storehouse_count` = ?, 
        `pos1_count` = ?, 
        `pos2_count` = ?, 
        `cost_price` = ?, 
        `incoming_invoice_items_note` = ? 
        WHERE `incoming_invoice_items_id` = ?");
    $stmt_update_entry->execute([$new_s_count, $new_p1_count, $new_p2_count, $new_cost, $note, $id]);

    // 3. Adjust items table (subtract old, add new)
    $diff_s = $new_s_count - $old['storehouse_count'];
    $diff_p1 = $new_p1_count - $old['pos1_count'];
    $diff_p2 = $new_p2_count - $old['pos2_count'];

    $wholesale_price = $new_cost + ($new_cost * 0.10);
    $retail_price = $new_cost + ($new_cost * 0.25);

    $stmt_items = $con->prepare("UPDATE `items` SET 
        `items_storehouse_count` = `items_storehouse_count` + ?, 
        `items_pointofsale1_count` = `items_pointofsale1_count` + ?, 
        `items_pointofsale2_count` = `items_pointofsale2_count` + ?, 
        `items_cost_price` = ?, 
        `items_wholesale_price` = ?, 
        `items_retail_price` = ? 
        WHERE `items_id` = ?");
    $stmt_items->execute([$diff_s, $diff_p1, $diff_p2, $new_cost, $wholesale_price, $retail_price, $items_id]);

    $con->commit();
    printSuccess("Invoice item updated successfully");

} catch (Exception $e) {
    $con->rollBack();
    printFailure("Error: " . $e->getMessage());
}
?>
