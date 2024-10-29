<?php


// Fetch filtered discounts
function get_filtered_discounts($filter)
{
    global $conn;
    $discounts = [];

    $query = "SELECT discount_id, discount_value, discount_name, discount_code, start_date, end_date, status FROM discounts";
    $params = [];
    $types = '';

    if (!empty($filter['status'])) {
        $query .= " WHERE status = ?";
        $params[] = $filter['status'];
        $types .= 's';
    }

    $stmt = $conn->prepare($query);
    if ($stmt) {
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $discounts[] = $row;
        }
        $stmt->close();
    } else {
        error_log("Query preparation failed: " . $conn->error);
    }

    return $discounts;
}

// Fetch discount details for view/edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'get_discount') {
    $discount_id = $_POST['discount_id'];

    $query = "SELECT * FROM discounts WHERE discount_id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param('i', $discount_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $discount = $result->fetch_assoc();
        echo json_encode($discount);
        $stmt->close();
    }
    exit();
}

// Save discount changes (edit functionality)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'save_discount') {
    $discount_id = $_POST['discount_id'];
    $discount_name = $_POST['discount_name'];
    $discount_code = $_POST['discount_code'];
    $discount_value = $_POST['discount_value'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];

    $query = "UPDATE discounts SET discount_name = ?, discount_code = ?, discount_value = ?, start_date = ?, end_date = ?, status = ? WHERE discount_id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param('ssisssi', $discount_name, $discount_code, $discount_value, $start_date, $end_date, $status, $discount_id);
        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }
        $stmt->close();
    }
    exit();
}

// Delete discount functionality
if (isset($_GET['action']) && $_GET['action'] == 'delete_discount') {
    $discount_id = $_GET['discount_id'];

    $query = "DELETE FROM discounts WHERE discount_id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param('i', $discount_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = 'Discount deleted successfully!';
            $_SESSION['alert_class'] = 'alert-success';
        } else {
            $_SESSION['message'] = 'Error deleting discount!';
            $_SESSION['alert_class'] = 'alert-danger';
        }
        $stmt->close();
    }
    header('Location: discountlist.php');
    exit();
}
