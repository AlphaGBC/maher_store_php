<?php
include "../connect.php";

/**
 * Expected JSON input:
 * {
 *   "transfer_date": "2026-02-19 12:00:00",
 *   "items": [
 *     {
 *       "items_id": 1,
 *       "storehouse_count": 90,  // New storehouse count after deduction
 *       "pos1_count": 5,         // Quantity to ADD to pos1
 *       "pos2_count": 5,         // Quantity to ADD to pos2
 *       "note": "Transfer note"
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

    // 1. Insert into transfer table
    $transfer_date = isset($data['transfer_date']) ? $data['transfer_date'] : date("Y-m-d H:i:s");
    $stmt_transfer = $con->prepare("INSERT INTO `transfer` (`transfer_date`) VALUES (?)");
    $stmt_transfer->execute([$transfer_date]);
    $transfer_id = $con->lastInsertId();

    // Prepare statements for reuse
    $stmt_item_insert = $con->prepare("INSERT INTO `transfer_of_items` 
        (`transfer_of_items_transfer_id`, `transfer_of_items_items_id`, `storehouse_count`, `pos1_count`, `pos2_count`, `transfer_of_items_note`) 
        VALUES (?, ?, ?, ?, ?, ?)");

    $stmt_item_update = $con->prepare("UPDATE `items` SET 
        `items_storehouse_count` = ?, 
        `items_pointofsale1_count` = `items_pointofsale1_count` + ?, 
        `items_pointofsale2_count` = `items_pointofsale2_count` + ? 
        WHERE `items_id` = ?");

    $total_pos1_affected = 0;
    $total_pos2_affected = 0;

    foreach ($data['items'] as $item) {
        $items_id = $item['items_id'];
        $storehouse_count = $item['storehouse_count']; // This is the NEW total in storehouse
        $pos1_add = $item['pos1_count'];               // This is the ADDED quantity to pos1
        $pos2_add = $item['pos2_count'];               // This is the ADDED quantity to pos2
        $note = isset($item['note']) ? $item['note'] : "";

        $total_pos1_affected += $pos1_add;
        $total_pos2_affected += $pos2_add;

        // 2. Insert into transfer_of_items (Archive)
        $stmt_item_insert->execute([
            $transfer_id,
            $items_id,
            $storehouse_count,
            $pos1_add,
            $pos2_add,
            $note
        ]);

        // 3. Update items table
        $stmt_item_update->execute([
            $storehouse_count,
            $pos1_add,
            $pos2_add,
            $items_id
        ]);
    }

    // 4. Send FCM Notifications based on affected POS
    if ($total_pos1_affected > 0 || $total_pos2_affected > 0) {
        $topic = "";
        if ($total_pos1_affected > 0 && $total_pos2_affected > 0) {
            $topic = "point";
        } elseif ($total_pos1_affected > 0) {
            $topic = "point1";
        } elseif ($total_pos2_affected > 0) {
            $topic = "point2";
        }

        if ($topic != "") {
            sendFCM("تنبيه", "تم تحويل منتجات جديدة إليكم", $topic, "", "refreshitems", $accessToken);
        }
    }

    $con->commit();
    printSuccess("Transfer completed and inventory updated successfully");

} catch (Exception $e) {
    $con->rollBack();
    printFailure("Error: " . $e->getMessage());
}
?>
