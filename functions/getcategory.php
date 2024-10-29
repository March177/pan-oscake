<?php
include 'db/config.php'; // Ensure this path is correct

// Check if form is submitted for delete, update, or insert operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debugging: Log POST data
    file_put_contents('debug.log', print_r($_POST, true), FILE_APPEND);

    // Get the IDs from POST data
    $menu_id = isset($_POST['menu_id']) ? intval($_POST['menu_id']) : 0; // Menu ID
    $c_id = isset($_POST['c_id']) ? intval($_POST['c_id']) : 0; // Category ID

    // DELETE CATEGORY
    if (isset($_POST['delete_category']) && $c_id > 0) {
        $sql = "DELETE FROM categories WHERE c_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $c_id);

        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'Category deleted successfully.'];
        } else {
            $response = ['success' => false, 'message' => 'Failed to delete category.'];
        }

        $stmt->close();
    }
    // DELETE MENU ITEM
    elseif (isset($_POST['delete_menu']) && $menu_id > 0) {
        $sql = "DELETE FROM menu WHERE menu_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $menu_id);

        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'Menu item deleted successfully.'];
        } else {
            $response = ['success' => false, 'message' => 'Failed to delete menu item.'];
        }

        $stmt->close();
    }
    // UPDATE CATEGORY
    elseif (isset($_POST['update_category']) && $c_id > 0) {
        // Sanitize and validate input
        $category_name = mysqli_real_escape_string($conn, $_POST['category_name']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $status = mysqli_real_escape_string($conn, $_POST['status']);

        // Ensure that required fields are not empty
        if (!empty($category_name) && !empty($description) && !empty($status)) {
            $sql = "UPDATE categories SET category_name = ?, description = ?, status = ? WHERE c_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sssi', $category_name, $description, $status, $c_id);

            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => 'Category updated successfully.'];
            } else {
                $response = ['success' => false, 'message' => 'Failed to update category.'];
            }

            $stmt->close();
        } else {
            $response = ['success' => false, 'message' => 'All fields are required.'];
        }
    }
}

// Function to fetch filtered categories
function get_filtered_categories($filter)
{
    global $conn;

    $query = "SELECT * FROM categories WHERE 1=1";

    if (!empty($filter['category_name'])) {
        $query .= " AND category_name LIKE '%" . mysqli_real_escape_string($conn, $filter['category_name']) . "%'";
    }

    if (!empty($filter['description'])) {
        $query .= " AND description LIKE '%" . mysqli_real_escape_string($conn, $filter['description']) . "%'";
    }

    if (!empty($filter['status'])) {
        $query .= " AND status = '" . mysqli_real_escape_string($conn, $filter['status']) . "'";
    }

    $result = mysqli_query($conn, $query);

    $categories = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }

    return $categories;
}

// Function to insert a new category
function insertCategory($conn, $category_name, $description, $status)
{
    if ($conn) {
        $sql = "INSERT INTO categories (category_name, description, status) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $category_name, $description, $status);
        return $stmt->execute();
    } else {
        return false;
    }
}
