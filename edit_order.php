<?php
session_start(); // Start session to use session variables
include 'db/config.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $orderId = $_POST['OrderID'];
    $customerName = $_POST['CustomerName'];
    $orderType = $_POST['OrderType'];
    $paymentMethod = $_POST['PaymentMethod'];
    $discountType = $_POST['DiscountType'];
    $totalPrice = $_POST['TotalPrice'];
    $status = $_POST['Status'];

    // Prepare and bind
    $stmt = $conn->prepare("UPDATE orders SET CustomerName = ?, OrderType = ?, PaymentMethod = ?, DiscountType = ?, TotalPrice = ?, status = ? WHERE OrderID = ?");
    $stmt->bind_param("ssssssi", $customerName, $orderType, $paymentMethod, $discountType, $totalPrice, $status, $orderId);

    // Execute the statement
    if ($stmt->execute()) {
        $_SESSION['success'] = 'Order updated successfully!'; // Store success message in session
    } else {
        $_SESSION['error'] = 'Error updating order: ' . $stmt->error; // Store error message in session
    }

    $stmt->close();
    $conn->close();

    // Redirect back to transaction.php
    header('Location: transaction.php');
    exit();
}
