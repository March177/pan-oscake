<?php
include '../db/config.php'; // Make sure the path is correct

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Fetch category ID from POST
    $c_id = isset($_POST['c_id']) ? intval($_POST['c_id']) : 0;
    
    // Ensure all fields are filled
    if (empty($_POST['category_name']) || empty($_POST['status'])) {
        echo 'Please fill all required fields.';
        exit;
    }

    // Sanitize inputs
    $category_name = mysqli_real_escape_string($conn, $_POST['category_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    // Update category in the database
    $sql = "UPDATE categories SET category_name = ?, description = ?, status = ? WHERE c_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $category_name, $description, $status, $c_id);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'Failed to update category.';
    }

    $stmt->close();
}
?>
