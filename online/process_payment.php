<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cake_db";

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the posted data from the form with existence check
$email = isset($_POST['email']) ? $_POST['email'] : '';
$first_name = isset($_POST['first_name']) ? $_POST['first_name'] : '';
$last_name = isset($_POST['last_name']) ? $_POST['last_name'] : '';
$address = isset($_POST['address']) ? $_POST['address'] : '';
$postal_code = isset($_POST['postal_code']) ? $_POST['postal_code'] : '';
$city = isset($_POST['city']) ? $_POST['city'] : '';
$discount_code = isset($_POST['discount_code']) ? $_POST['discount_code'] : '';

// Assuming these values come from your JavaScript
$item_name = "Red Velvet Cake";
$price = 900.00;
$delivery = "Free";
$subtotal = $price; // Assuming no discount for simplicity

// Insert order details into the orders table
$payment_status = 'pending'; // Declare this variable
$transaction_id = ''; // Declare this variable

$order_stmt = $conn->prepare("INSERT INTO orders (email, first_name, last_name, address, postal_code, city, payment_status, transaction_id, discount_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$order_stmt->bind_param("sssssssss", $email, $first_name, $last_name, $address, $postal_code, $city, $payment_status, $transaction_id, $discount_code);
$order_stmt->execute();
$order_id = $conn->insert_id; // Get the inserted order ID
$order_stmt->close();

// Insert item details into the order_items table
$item_stmt = $conn->prepare("INSERT INTO order_items (order_id, item_name, price, delivery, subtotal) VALUES (?, ?, ?, ?, ?)");
$item_stmt->bind_param("issss", $order_id, $item_name, $price, $delivery, $subtotal);
$item_stmt->execute();
$item_stmt->close();

// Redirect or display a success message
echo "Order placed successfully! Your order ID is " . $order_id;

// Close the database connection
$conn->close();
