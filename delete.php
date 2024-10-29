<?php
// Include the database configuration file
include 'db/config.php';

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['menu_id'])) {
    $menuId = intval($_POST['menu_id']); // Ensure it's an integer

    // Function to delete a menu item
    function deleteMenuItem($conn, $menuId)
    {
        $stmt = $conn->prepare("DELETE FROM menu WHERE menu_id = ?");
        $stmt->bind_param('i', $menuId);
        return $stmt->execute() ? true : false;
    }

    // Attempt to delete the menu item
    if (deleteMenuItem($conn, $menuId)) {
        // Return a success response as JSON
        echo json_encode(['success' => true, 'message' => 'Menu item deleted successfully.']);
    } else {
        // Return an error response as JSON
        echo json_encode(['success' => false, 'message' => 'Failed to delete menu item.']);
    }
    exit();
}
