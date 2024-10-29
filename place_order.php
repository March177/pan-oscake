
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db/config.php'; // Include your database configuration

// Get the incoming JSON data
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['orderedItems']) && !empty($data['orderedItems'])) {
    // Prepare to insert the order
    $customer = $data['customer'];
    $orderType = $data['order_type'];
    $discount = $data['discount'];
    $paymentMethod = $data['payment_method'];
    $totalPrice = $data['total_price'];
    $createdBy = $data['created_by']; // Assuming you have the user ID

    // Insert the order into the Orders table
    $orderQuery = "INSERT INTO orders (CustomerName, OrderType, DiscountType, PaymentMethod, TotalPrice, CreatedBy) 
                   VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($orderQuery);
    $stmt->bind_param("ssssdi", $customer, $orderType, $discount, $paymentMethod, $totalPrice, $createdBy);

    if ($stmt->execute()) {
        $orderID = $stmt->insert_id; // Get the last inserted order ID

        // Now insert the ordered items into OrderItems table
        $orderItemQuery = "INSERT INTO orderitems (OrderID, menu_id, menu_name, quantity, Price) 
                           VALUES (?, ?, ?, ?, ?)";
        $itemStmt = $conn->prepare($orderItemQuery);

        foreach ($data['orderedItems'] as $item) {
            // Check if the required fields are set
            if (isset($item['menu_id']) && isset($item['menu_name']) && isset($item['quantity']) && isset($item['price'])) {
                $menuID = $item['menu_id'];
                $menuName = $item['menu_name'];
                $quantity = $item['quantity'];
                $price = $item['price'];

                // Validate quantity
                if ($quantity <= 0) {
                    echo json_encode(['status' => 'error', 'message' => 'Quantity must be greater than zero.']);
                    exit;
                }

                $itemStmt->bind_param("issid", $orderID, $menuID, $menuName, $quantity, $price);
                $itemStmt->execute();
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Ordered item data is incomplete.']);
                exit;
            }
        }

        // Close statements
        $itemStmt->close();
        $stmt->close();

        // Send success response
        echo json_encode(['status' => 'success', 'message' => 'Order placed successfully.', 'order_id' => $orderID]);
    } else {
        // Error inserting order
        echo json_encode(['status' => 'error', 'message' => 'Failed to place order.']);
    }
} else {
    // No ordered items
    echo json_encode(['status' => 'error', 'message' => 'No items to order.']);
}

$conn->close();
?>