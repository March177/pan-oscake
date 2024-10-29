<?php
$host = 'localhost';  // Database host
$user = 'root';       // Database username
$pass = '';           // Database password
$db   = 'restaurant_db'; // Database name

try {
    // Create a PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Paymongo API Key
$apiKey = 'sk_test_a1ZMzqyoDN52eagaxepwv6rm'; // Replace with your actual Paymongo API key

// Function to fetch payments from Paymongo
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
    curl_close($ch);

    // Return the decoded response data
    return json_decode($response, true);
}

// Function to insert successful payments into the database
function insertPaymentsIntoDatabase($payments, $pdo)
{
    foreach ($payments as $payment) {
        $attributes = $payment['attributes'];
        $billing = $attributes['billing'];
        $refunds = !empty($attributes['refunds']) ? json_encode($attributes['refunds']) : null; // Encode refunds as JSON

        // Check if the payment already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM online_payments WHERE id = ?");
        $stmt->execute([$payment['id']]);
        $exists = $stmt->fetchColumn();

        // If the payment does not exist, insert it
        if ($exists == 0) {
            $stmt = $pdo->prepare("
                INSERT INTO online_payments (id, amount, status, created_at, description, billing_name, billing_email, billing_phone, balance_transaction_id, refunds)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            // Execute the statement with the appropriate values
            $stmt->execute([
                $payment['id'],
                $attributes['amount'] / 100, // Convert amount to decimal
                $attributes['status'],
                date('Y-m-d H:i:s', $attributes['created_at']),
                $attributes['description'],
                $billing['name'],
                $billing['email'],
                $billing['phone'],
                $attributes['balance_transaction_id'],
                $refunds,
            ]);
        }
    }
}

try {
    // Fetch payments from Paymongo
    $payments = fetchSuccessfulPayments($apiKey);

    // Check if any payments were retrieved
    if (isset($payments['data']) && !empty($payments['data'])) {
        // Insert payments into the database
        insertPaymentsIntoDatabase($payments['data'], $pdo);

        // Display the payments in a table format
        echo '<table border="1" cellpadding="10" cellspacing="0">';
        echo '<tr>
                <th>ID</th>
                <th>Amount (PHP)</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Description</th>
                <th>Billing Name</th>
                <th>Billing Email</th>
                <th>Billing Phone</th>
                <th>Balance Transaction ID</th>
                <th>Refunds</th>
              </tr>';

        foreach ($payments['data'] as $payment) {
            $attributes = $payment['attributes'];
            $billing = $attributes['billing'];
            $refunds = !empty($attributes['refunds']) ? implode(', ', $attributes['refunds']) : 'None';

            echo '<tr>
                    <td>' . htmlspecialchars($payment['id']) . '</td>
                    <td>' . htmlspecialchars(number_format($attributes['amount'] / 100, 2)) . '</td>
                    <td>' . htmlspecialchars($attributes['status']) . '</td>
                    <td>' . date('Y-m-d H:i:s', $attributes['created_at']) . '</td>
                    <td>' . htmlspecialchars($attributes['description']) . '</td>
                    <td>' . htmlspecialchars($billing['name']) . '</td>
                    <td>' . htmlspecialchars($billing['email']) . '</td>
                    <td>' . htmlspecialchars($billing['phone']) . '</td>
                    <td>' . htmlspecialchars($attributes['balance_transaction_id']) . '</td>
                    <td>' . htmlspecialchars($refunds) . '</td>
                  </tr>';
        }
        echo '</table>';
    } else {
        echo "No payments found.";
    }
} catch (PDOException $e) {
    echo "Database connection error: " . $e->getMessage(); // Show connection error
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
