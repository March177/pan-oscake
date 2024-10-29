<?php
include 'db/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the IDs from POST data
    $menu_id = isset($_POST['menu_id']) ? intval($_POST['menu_id']) : 0; // Menu ID
    $c_id = isset($_POST['c_id']) ? intval($_POST['c_id']) : 0; // Category ID
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0; // User ID

    // Use an array to check which ID was provided
    $response = ['success' => false, 'message' => 'Invalid ID.'];

    // Begin transaction
    $conn->begin_transaction();

    if ($menu_id > 0) {
        // Prepare your delete SQL statement for menu items
        $sql = "DELETE FROM menu WHERE menu_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $menu_id);

        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'Menu item deleted successfully.'];
        } else {
            $response = ['success' => false, 'message' => 'Failed to delete menu item.'];
        }

        $stmt->close();
    } elseif ($c_id > 0) {
        // Prepare your delete SQL statement for categories
        $sql = "DELETE FROM categories WHERE c_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $c_id);

        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'Category deleted successfully.'];
        } else {
            $response = ['success' => false, 'message' => 'Failed to delete category.'];
        }

        $stmt->close();
    } elseif ($user_id > 0) {
        // Prepare your delete SQL statement for users
        $sql = "DELETE FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $user_id);

        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'User deleted successfully.'];
        } else {
            $response = ['success' => false, 'message' => 'Failed to delete user.'];
        }

        $stmt->close();
    }

    // Commit the transaction
    $conn->commit();

    // Return the response as JSON
    echo json_encode($response);
}

// Close the database connection
$conn->close();
