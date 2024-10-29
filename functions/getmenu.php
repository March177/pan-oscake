<?php
include __DIR__ . '/../db/config.php';

// Function to filter menu items based on provided criteria
function get_filtered_menu($filter)
{
    global $conn;

    $query = "SELECT * FROM menu WHERE 1=1";

    if (!empty($filter['menu_name'])) {
        $query .= " AND menu_name LIKE '%" . mysqli_real_escape_string($conn, $filter['menu_name']) . "%'";
    }

    if (!empty($filter['category_name'])) {
        $query .= " AND category_name LIKE '%" . mysqli_real_escape_string($conn, $filter['category_name']) . "%'";
    }

    if (!empty($filter['subcategory_name'])) {
        $query .= " AND subcategory_name LIKE '%" . mysqli_real_escape_string($conn, $filter['subcategory_name']) . "%'";
    }

    if (!empty($filter['description'])) {
        $query .= " AND description LIKE '%" . mysqli_real_escape_string($conn, $filter['description']) . "%'";
    }
    if (!empty($filter['price'])) {
        $query .= " AND price LIKE '%" . mysqli_real_escape_string($conn, $filter['price']) . "%'";
    }

    if (!empty($filter['created_by'])) {
        $query .= " AND created_by LIKE '%" . mysqli_real_escape_string($conn, $filter['created_by']) . "%'";
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

// Function to get menu by ID
function get_menu_by_id($menu_id)
{
    global $conn;

    // Sanitize the menu ID to prevent SQL injection
    $menu_id = intval($menu_id); // Ensure it's an integer

    // Query to get the menu by ID
    $query = "SELECT * FROM menu WHERE menu_id = $menu_id"; // Use 'menu_id' as the primary key

    $result = mysqli_query($conn, $query);

    // Check if any rows are returned
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result); // Return the menu details as an associative array
    } else {
        return null; // Return null if no menu is found
    }
}

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

    $price = trim($_POST['price']);
    $status = trim($_POST['status']);

    if (empty($menu_name) || empty($category_name) || empty($description) || empty($price) || empty($status)) {
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
                $stmt = $conn->prepare("INSERT INTO menu (menu_name, category_name, created_by, description,price, status, image_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
                if ($stmt === false) {
                    echo "Prepare failed: " . htmlspecialchars($conn->error);
                    exit();
                }

                $imagePath = 'img/menu/' . $fileName;
                $stmt->bind_param("sssssss", $menu_name, $category_name, $created_by, $description, $price, $status, $imagePath);

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
