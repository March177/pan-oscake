<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo 'Invalid email format';
        exit; // Stop execution if email is invalid
    }

    // Prepare the update query
    $update_query = "UPDATE users SET username = '$username', email = '$email', role = '$role', status = '$status'";

    // Only update password if it's provided
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password
        $update_query .= ", password = '$hashed_password'"; // Add password to the query
    }

    $update_query .= " WHERE user_id = $user_id";

    // Execute the update query
    if (mysqli_query($conn, $update_query)) {
        echo 'success'; // Return success response
    } else {
        echo 'Database update failed: ' . mysqli_error($conn); // Return error message
    }
} else {
    echo 'Invalid request method.';
}
