<?php
// Adjust the path based on the actual location of config.php
include __DIR__ . '/../db/config.php'; // Adjust path as necessary

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if 'menu_id' is set in the POST request
    if (isset($_POST['menu_id'])) {
        // Inputs
        $menu_id = intval($_POST['menu_id']);
        $menu_name = trim($_POST['menu_name']);
        $category = trim($_POST['category']);
        $description = trim($_POST['description']);
        $discount_type = trim($_POST['discount_type']);
        $price = trim($_POST['price']);
        $status = trim($_POST['status']);

        // Handle file upload
        $allowedTypes = ['image/jpeg', 'image/png'];
        $uploadDir = __DIR__ . '/../img/menu/';
        $fileName = basename($_FILES['image']['name']);
        $fileType = $_FILES['image']['type'];

        // Determine the image path (use existing image if no new file is uploaded)
        $imagePath = trim($_POST['existing_image']);

        if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
            if (in_array($fileType, $allowedTypes)) {
                // Create directory if it does not exist
                if (!is_dir($uploadDir)) {
                    if (!mkdir($uploadDir, 0755, true)) {
                        echo "Failed to create upload directory.";
                        exit();
                    }
                }

                // Move the uploaded file to the target directory
                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fileName)) {
                    $imagePath = 'img/menu/' . $fileName; // Update image path to the new uploaded file
                } else {
                    echo "Failed to move uploaded file.";
                    exit();
                }
            } else {
                echo "Invalid file type. Only jpg and png images are allowed.";
                exit();
            }
        } else if ($_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            // Handle other file upload errors (except for no file)
            echo "File upload error. Error code: " . $_FILES['image']['error'];
            exit();
        }

        // Prepare SQL statement to update the menu
        $stmt = $conn->prepare("UPDATE menu SET menu_name = ?, category_name = ?, description = ?, discount_type = ?, price = ?, status = ?, image_path = ? WHERE menu_id = ?");
        if ($stmt === false) {
            echo "Prepare failed: " . htmlspecialchars($conn->error);
            exit();
        }

        $stmt->bind_param("sssssssi", $menu_name, $category, $description, $discount_type, $price, $status, $imagePath, $menu_id);

        if ($stmt->execute()) {
            // Redirect to the desired page
            echo "success";
        } else {
            echo "Execute failed: " . htmlspecialchars($stmt->error);
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "Menu ID is missing.";
    }
}
?>
