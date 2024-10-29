<?php
session_start();

include 'db/config.php';

// Get form data without required checks
$customerName = $_POST['name'] ?? '';
$customerEmail = $_POST['email'] ?? '';
$customerPhone = $_POST['phone'] ?? '';
$floor = $_POST['floor'] ?? '';
$noteToRider = $_POST['note_to_rider'] ?? '';
$deliveryMethod = $_POST['deliveryMethod'] ?? '';
$paymentMethod = $_POST['paymentMethod'] ?? '';
$tipAmount = isset($_POST['tipAmount']) ? floatval($_POST['tipAmount']) : 0;
$deliveryFee = isset($_POST['deliveryFee']) ? floatval($_POST['deliveryFee']) : 0;
$subtotal = isset($_POST['subtotal']) ? floatval($_POST['subtotal']) : 0;
$grandTotal = isset($_POST['grandTotal']) ? floatval($_POST['grandTotal']) : 0;
$orderItems = json_decode($_POST['order_items'], true); // Decode JSON into an array

// Prepare and bind for orders
$stmt = $conn->prepare("INSERT INTO onlinecustomer (customer_name, customer_email, customer_phone, floor, note_to_rider, delivery_method, payment_method, tip_amount, delivery_fee, subtotal, grand_total) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssdddds", $customerName, $customerEmail, $customerPhone, $floor, $noteToRider, $deliveryMethod, $paymentMethod, $tipAmount, $deliveryFee, $subtotal, $grandTotal);

// Execute the statement for orders
if ($stmt->execute()) {
    $orderId = $stmt->insert_id; // Get the last inserted order ID

    // Prepare and bind for onlineorderitem
    $itemStmt = $conn->prepare("INSERT INTO onlineorderitem (onr_id, itemName, itemPrice, quantity) VALUES (?, ?, ?, ?)");
    $itemStmt->bind_param("isdi", $orderId, $itemName, $itemPrice, $quantity);

    // Loop through each order item and insert
    foreach ($orderItems as $item) {
        $itemName = $item['name'];
        $itemPrice = $item['price'];
        $quantity = $item['quantity'];
        if (!$itemStmt->execute()) {
            error_log($itemStmt->error); // Log item insertion errors
        }
    }

    echo "Order submitted successfully!";
} else {
    error_log($stmt->error); // Log order insertion errors
    echo "Error: " . $stmt->error;
}

// Close connections
$itemStmt->close();
$stmt->close();
$conn->close();
