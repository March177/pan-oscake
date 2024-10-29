<?php
include 'db/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0; // User ID

    $response = ['success' => false, 'message' => 'Invalid User ID.'];

    if ($user_id > 0) {
        // Prepare the SQL statement
        $sql = "DELETE FROM users WHERE user_id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('i', $user_id);

            // Execute the prepared statement
            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => 'User deleted successfully.'];
            } else {
                // Check for SQL error
                $response = ['success' => false, 'message' => 'SQL Error: ' . $stmt->error];
            }
            $stmt->close();
        } else {
            // SQL statement preparation error
            $response = ['success' => false, 'message' => 'SQL Statement Preparation Error: ' . $conn->error];
        }
    }

    echo json_encode($response);
}

$conn->close();
