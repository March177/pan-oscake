<?php
include 'db/config.php';

if (!isset($_GET['user_id'])) {
    die('User ID not provided.');
}

$user_id = intval($_GET['user_id']);

// Fetch the user details
$query = "SELECT * FROM users WHERE user_id = $user_id";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
} else {
    die('User not found.');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    // Check if the email already exists for another user
    $email_check_query = "SELECT * FROM users WHERE email = '$email' AND user_id != $user_id";
    $email_check_result = mysqli_query($conn, $email_check_query);

    if (mysqli_num_rows($email_check_result) > 0) {
        die('Email already exists for another user.');
    }

    // If password is empty, do not update it
    if (empty($password)) {
        $update_query = "UPDATE users SET username = '$username', email = '$email', role = '$role', status = '$status' WHERE user_id = $user_id";
    } else {
        // Hash the new password before updating
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $update_query = "UPDATE users SET username = '$username', email = '$email', password = '$hashed_password', role = '$role', status = '$status' WHERE user_id = $user_id";
    }

    if (mysqli_query($conn, $update_query)) {
        echo 'success';
    } else {
        echo 'Error updating user: ' . mysqli_error($conn);
    }
    exit; // Exit after processing the form
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0" />
    <meta name="description" content="User Edit Page" />
    <meta name="keywords" content="admin, edit, user, bootstrap, responsive" />
    <meta name="author" content="Your Name" />
    <meta name="robots" content="noindex, nofollow" />
    <title>Edit User</title>
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.jpg" />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/animate.css" />
    <link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css" />
    <link rel="stylesheet" href="assets/css/dataTables.bootstrap4.min.css" />
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css" />
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css" />
    <link rel="stylesheet" href="assets/css/style.css" />
    <style>
        .alert {
            display: none;
            position: fixed;
            top: 10px;
            right: 20px;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
            z-index: 1000;
        }
    </style>
</head>

<body>
    <div class="main-wrapper">
        <div class="header">
            <?php include 'php/header.php'; ?>
        </div>
        <?php include 'php/sidebar.php'; ?>

        <div class="page-wrapper">
            <div class="content">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Edit User</h4>
                        <h6>Edit existing User</h6>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="alert" id="success-alert">Update successful!</div>

                        <form id="edit-user-form" method="POST">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['user_id']); ?>" />

                            <div class="row">
                                <div class="col-lg-4 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>Username</label>
                                        <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required />
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required />
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>Password</label>
                                        <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password" />
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>Role</label>
                                        <select name="role" class="form-control">
                                            <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                            <option value="cashier" <?php echo ($user['role'] == 'cashier') ? 'selected' : ''; ?>>Cashier</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select name="status" class="form-control">
                                            <option value="active" <?php echo ($user['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                            <option value="inactive" <?php echo ($user['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <button type="submit" class="btn btn-primary">Update User</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <script src="assets/js/feather.min.js"></script>
    <script src="assets/js/jquery.slimscroll.min.js"></script>
    <script src="assets/js/jquery.dataTables.min.js"></script>
    <script src="assets/js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/plugins/select2/js/select2.min.js"></script>
    <script src="assets/plugins/sweetalert/sweetalert2.all.min.js"></script>
    <script src="assets/plugins/sweetalert/sweetalerts.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script>
        $(document).ready(function() {
            $('#edit-user-form').on('submit', function(e) {
                e.preventDefault(); // Prevent the default form submission

                // Client-side validation
                var username = $('input[name="username"]').val().trim();
                var email = $('input[name="email"]').val().trim();

                if (username === '' || email === '') {
                    alert('Please fill in all required fields.');
                    return false; // Prevent form submission
                }

                $.ajax({
                    url: 'functions/users.php', // URL to the update script
                    type: 'POST',
                    data: $(this).serialize(), // Serialize form data
                    success: function(response) {
                        if (response.trim() === 'success') {
                            $('#success-alert').fadeIn().delay(2000).fadeOut();
                            // Optionally redirect
                            setTimeout(function() {
                                window.location.href = 'userlist.php'; // Example redirect after success
                            }, 2000);
                        } else {
                            alert('An error occurred: ' + response);
                        }
                    },
                    error: function() {
                        alert('An error occurred. Please try again.');
                    }
                });
            });
        });
    </script>

</body>

</html>