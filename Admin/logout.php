<?php
session_start();
include 'db/config.php'; // Include database configuration

if (isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];
    $device_id = md5($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);

    // Remove the device session from the database
    $query = "DELETE FROM device_sessions WHERE admin_id = ? AND device_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $admin_id, $device_id);
    $stmt->execute();

    // Destroy the session
    session_destroy();
}

// Redirect to login page
header("Location: login.php");
?>
