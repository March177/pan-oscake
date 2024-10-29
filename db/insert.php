<?php
include __DIR__ . '/../db/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    echo '<pre>';
    print_r($_POST);
    echo '</pre>';


    echo '<pre>';
    print_r($_FILES);
    echo '</pre>';

    $menu_name = trim($_POST['menu_name']);

    $category_name = trim($_POST['category_name']);

    $description = trim($_POST['description']);
    $discount_type = trim($_POST['discount_type']);
    $price = trim($_POST['price']);
    $status = trim($_POST['status']);


    if (empty($menu_name)  || empty($category_name) || empty($description) || empty($price) || empty($status)) {
        echo "All fields are required.";
        exit();
    }

    if (!is_numeric($price) || $price <= 0) {
        echo "Price must be a positive number.";
        exit();
    }

    $created_by = "Admin";


    $allowedTypes = ['image/jpeg', 'image/png'];
    $uploadDir = __DIR__ . '/../img/menu/';


    if (!isset($_FILES['menu_image'])) {
        echo "No file was uploaded.";
        exit();
    }

    $fileName = basename($_FILES['menu_image']['name']);
    $uploadFile = $uploadDir . $fileName;
    $fileType = $_FILES['menu_image']['type'];
    $fileError = $_FILES['menu_image']['error'];

    if ($fileError === UPLOAD_ERR_OK) {
        if (in_array($fileType, $allowedTypes)) {

            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    echo "Failed to create upload directory.";
                    exit();
                }
            }


            if (move_uploaded_file($_FILES['menu_image']['tmp_name'], $uploadFile)) {

                $stmt = $conn->prepare("INSERT INTO menu (menu_name, category_name, created_by, description, discount_type, price, status, image_path) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?)");
                if ($stmt === false) {
                    echo "Prepare failed: " . htmlspecialchars($conn->error);
                    exit();
                }

                $imagePath = 'img/menu/' . $fileName;
                $stmt->bind_param("ssssssss", $menu_name, $category_name, $created_by, $description, $discount_type, $price, $status, $imagePath);

                if ($stmt->execute()) {

                    header("Location: /cake/menulist.php");
                    exit();
                } else {
                    echo "Execute failed: " . htmlspecialchars($stmt->error);
                }

                $stmt->close();
            } else {
                echo "Failed to move uploaded file.";
            }
        } else {
            echo "Invalid file type. Only jpg and png images are allowed.";
        }
    } else {
        echo "File upload error. Error code: " . $fileError;
    }

    // Close the database connection
    $conn->close();
}
