<?php
include '../connect.php';

// Get filters from request
$pos_source = isset($_GET['pos_source']) ? intval($_GET['pos_source']) : null;
$customer_type = isset($_GET['customer_type']) ? intval($_GET['customer_type']) : null; // 1 = wholesale, 0 = retail
$start_date = isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : null;
$end_date = isset($_GET['end_date']) ? htmlspecialchars($_GET['end_date']) : null;
$customer_name = isset($_GET['customer_name']) ? htmlspecialchars($_GET['customer_name']) : null;

// Build base query
$query = "
SELECT 
  o.orders_id,
  o.wholesale_customers_name,
  o.total_items_count,
  o.subtotal,
  o.discount_amount,
  o.total,
  o.pos_source,
  o.created_at,
  o.received_at,
  oi.id as ordersdetails_id,
  oi.items_id,
  oi.items_name,
  oi.items_quantity,
  oi.items_unit_price,
  oi.items_discount_percentage,
  oi.items_total_price,
  CASE WHEN o.wholesale_customers_name IS NOT NULL AND o.wholesale_customers_name != '' THEN 1 ELSE 0 END as is_wholesale
FROM ordercard o
LEFT JOIN ordercard_items oi ON o.orders_id = oi.orders_id
WHERE 1=1
";

$params = [];

// Filter by POS source
if ($pos_source !== null && ($pos_source === 1 || $pos_source === 2)) {
  $query .= " AND o.pos_source = :pos_source";
  $params[':pos_source'] = $pos_source;
}

// Filter by customer type (wholesale vs retail)
if ($customer_type === 1) {
  // Wholesale only - name must be NOT NULL AND not empty
  $query .= " AND o.wholesale_customers_name IS NOT NULL AND o.wholesale_customers_name != ''";
} elseif ($customer_type === 0) {
  // Retail only - name must be NULL or empty
  $query .= " AND (o.wholesale_customers_name IS NULL OR o.wholesale_customers_name = '')";
}

// Filter by date range
if (!empty($start_date)) {
  $query .= " AND DATE(o.created_at) >= :start_date";
  $params[':start_date'] = $start_date;
}
if (!empty($end_date)) {
  $query .= " AND DATE(o.created_at) <= :end_date";
  $params[':end_date'] = $end_date;
}

// Filter by customer name
if (!empty($customer_name)) {
  $query .= " AND o.wholesale_customers_name LIKE :customer_name";
  $params[':customer_name'] = "%{$customer_name}%";
}

// Order by created_at descending
$query .= " ORDER BY o.created_at DESC";

// Execute query
global $con;
$stmt = $con->prepare($query);

// Bind parameters
foreach ($params as $key => $value) {
  $stmt->bindValue($key, $value);
}

try {
  $stmt->execute();
  $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  // Group results by order
  $orders = [];
  foreach ($results as $row) {
    $orderId = (int)$row['orders_id'];
    $wholesaleName = (string)($row['wholesale_customers_name'] ?? '');
    $isWholesale = !empty($wholesaleName) ? 1 : 0; // Determine once: is_wholesale is 1 only if name is not empty
    
    if (!isset($orders[$orderId])) {
      $orders[$orderId] = [
        'orders_id' => (int)$row['orders_id'],
        'wholesale_customers_name' => $wholesaleName,
        'total_items_count' => (int)$row['total_items_count'],
        'subtotal' => (float)$row['subtotal'],
        'discount_amount' => (float)$row['discount_amount'],
        'total' => (float)$row['total'],
        'pos_source' => (int)$row['pos_source'],
        'created_at' => (string)$row['created_at'],
        'is_wholesale' => $isWholesale,
        'items' => []
      ];
    }
    
    // Add item if it exists
    if ($row['ordersdetails_id'] !== null) {
      $orders[$orderId]['items'][] = [
        'ordersdetails_id' => (int)$row['ordersdetails_id'],
        'items_id' => (int)$row['items_id'],
        'items_name' => (string)$row['items_name'],
        'items_quantity' => (int)$row['items_quantity'],
        'items_unit_price' => (float)$row['items_unit_price'],
        'items_discount_percentage' => (float)$row['items_discount_percentage'],
        'items_total_price' => (float)$row['items_total_price'],
        'is_wholesale' => (int)$row['is_wholesale']
      ];
    }
  }
  
  // Convert to indexed array (remove associative keys) to ensure JSON arrays
  $ordersArray = array_values($orders);

  // Final validation and cleanup
  foreach ($ordersArray as &$ord) {
    // Ensure items is re-indexed as array
    if (isset($ord['items']) && is_array($ord['items'])) {
      $ord['items'] = array_values($ord['items']);
    } else {
      $ord['items'] = [];
    }
  }
  unset($ord);

  $response = [
    'status' => 'success',
    'data' => $ordersArray
  ];

  header('Content-Type: application/json; charset=utf-8');
  header('Cache-Control: no-cache, no-store, must-revalidate');
  // Use JSON_NUMERIC_CHECK to preserve numeric types and NOT JSON_FORCE_OBJECT
  echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
  
} catch (Exception $e) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'status' => 'failure',
    'message' => 'Database error: ' . $e->getMessage()
  ]);
}
?>
