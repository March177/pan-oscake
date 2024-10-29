<?php
// Start output buffering
ob_start();
session_start(); // Start the session

// Include your database configuration here
include 'db/config.php'; // Ensure this path is correct

// Define PayMongo secret key (use environment variable in production)
$api_key = 'sk_test_a1ZMzqyoDN52eagaxepwv6rm'; // Replace with your actual key

// Function to retrieve existing webhooks
function retrieveWebhooks($api_key)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.paymongo.com/v1/webhooks");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic ' . base64_encode($api_key . ':'),
        'accept: application/json'
    ]);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        throw new Exception('cURL Error: ' . curl_error($ch));
    }
    curl_close($ch);

    return json_decode($response, true);
}

// Function to fetch successful payments
function fetchSuccessfulPayments($apiKey)
{
    $url = "https://api.paymongo.com/v1/payments";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic ' . base64_encode($apiKey . ':'),
        'Content-Type: application/json',
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception('cURL Error: ' . curl_error($ch));
    }

    curl_close($ch);

    return json_decode($response, true);
}

// Retrieve existing webhooks
try {
    $webhooks = retrieveWebhooks($api_key);
    error_log("Retrieved webhooks: " . print_r($webhooks, true));
} catch (Exception $e) {
    error_log('Error retrieving webhooks: ' . $e->getMessage());
}

// Process payment when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_GET['webhook'])) {
    // Sanitize form inputs
    $firstName = htmlspecialchars($_POST['first_name'] ?? '');
    $lastName = htmlspecialchars($_POST['last_name'] ?? '');
    $address = htmlspecialchars($_POST['address'] ?? '');
    $postalCode = htmlspecialchars($_POST['postal_code'] ?? '');
    $city = htmlspecialchars($_POST['city'] ?? '');
    $pickupTime = htmlspecialchars($_POST['pickup_time'] ?? '');
    $pickupDate = htmlspecialchars($_POST['pickup_date'] ?? '');
    $createdAt = date("Y-m-d H:i:s");

    // Check if 'items' is set and not empty
    if (!isset($_POST['items']) || empty($_POST['items'])) {
        die("No items provided.");
    }

    // Decode items and check validity
    $items = json_decode($_POST['items'], true);
    if ($items === null) {
        die("Invalid items format.");
    }


    // Get the total amount from form submission (in cents)
    $totalAmount = isset($_POST['total_amount']) ? intval($_POST['total_amount']) : 0;
    $productNames = isset($_POST['product_name']) ? explode(', ', $_POST['product_name']) : ['Unknown Item']; // Convert to an array

    // Check if the total amount is valid
    if ($totalAmount <= 0) {
        echo "Invalid total amount.";
        exit;
    }


    // Store the customer and items data in session
    $_SESSION['payment_data'] = [
        'first_name' => $firstName,
        'last_name' => $lastName,
        'address' => $address,
        'postal_code' => $postalCode,
        'city' => $city,
        'pickup_time' => $pickupTime,
        'pickup_date' => $pickupDate,
        'items' => $items,
        'total_amount' => $totalAmount,
        'created_at' => $createdAt
    ];

    // Prepare to create a payment link with PayMongo
    $ch = curl_init();
    $success_url = "https://yourdomain.com/receipt.php"; // Your receipt URL
    $cancel_url = "https://yourdomain.com/paymentarea.php"; // Your payment area URL

    // Prepare API call
    curl_setopt($ch, CURLOPT_URL, "https://api.paymongo.com/v1/links");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'data' => [
            'attributes' => [
                'amount' => $totalAmount,
                'currency' => "PHP",
                'description' => 'Payment for: ' . implode(', ', array_column($items, 'name')),
                'payment_method_types' => ['gcash', 'paymaya', 'card'], // Add desired payment methods
                'success_url' => $success_url,
                'cancel_url' => $cancel_url,
            ]
        ]
    ]));

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic ' . base64_encode($api_key . ':'),
        'Content-Type: application/json'
    ]);

    // Execute the request and capture the response
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        throw new Exception('cURL Error: ' . curl_error($ch));
    }
    curl_close($ch);

    // Decode the JSON response
    $responseData = json_decode($response, true);

    // Check for errors in the response
    if (isset($responseData['errors'])) {
        throw new Exception("Error: " . print_r($responseData['errors'], true));
    }

    // Insert customer and order data
    // Insert customer and order data
    try {
        // Insert into online_customers table
        $stmt = $conn->prepare("INSERT INTO online_customers (first_name, last_name, address, postal_code, city, pickup_time, pickup_date, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisssss", $firstName, $lastName, $address, $postalCode, $city, $pickupTime, $pickupDate, $createdAt);
        $stmt->execute();

        // Get the last inserted customer ID
        $customerId = $conn->insert_id;

        // Insert into orderitems table
        $stmt = $conn->prepare("INSERT INTO orderitems (customer_id, menu_id, menu_name, quantity, price, created_at) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissds", $customerId, $menuId, $menuName, $quantity, $price, $createdAt); // Adjust types as necessary

        // Loop through each item and insert into orderitems
        foreach ($items as $item) {
            $menuId = $item['menu_id']; // Assuming you have menu_id in your items array
            $menuName = $item['product-name']; // Assuming you have name in your items array
            $quantity = $item['quantity']; // Quantity from the item
            $price = $item['price']; // Price from the item

            $stmt->execute();
        }

        // Log success and clear session data
        error_log("Order items processed successfully for customer ID: $customerId");
        unset($_SESSION['payment_data']); // Clear session data
    } catch (Exception $e) {
        error_log("Error inserting data into orderitems: " . $e->getMessage());
    }

    // Redirect to the PayMongo checkout URL if available
    if (isset($responseData['data']['attributes']['checkout_url'])) {
        $checkout_url = $responseData['data']['attributes']['checkout_url'];
        header('Location: ' . $checkout_url);
        exit;
    } else {
        throw new Exception("Failed to create payment link. Response: " . print_r($responseData, true));
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET['webhook'])) {
    // Webhook handling for payment confirmation

    // Read the JSON payload from PayMongo
    $payload = json_decode(file_get_contents('php://input'), true);

    // Log the received payload for debugging
    error_log("Received payload: " . print_r($payload, true));

    if (!isset($payload['data']['attributes']['status'])) {
        http_response_code(400); // Bad Request
        exit('Invalid payload');
    }

    // Log the success of payment confirmation
    error_log("Payment confirmed for order ID: " . $payload['data']['id']);
    echo json_encode(["status" => "success", "message" => "Payment processing initiated."]);
    http_response_code(200);
    exit;
}
