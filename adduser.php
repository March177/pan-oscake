<?php
session_start(); // Start session
include 'db/config.php'; // Database connection

// Initialize variables to store error messages and form data
$errorMessages = [];
$formData = [];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $formData['username'] = trim($_POST['username']);
    $formData['password'] = trim($_POST['password']);
    $formData['role'] = $_POST['role'];
    $formData['email'] = trim($_POST['email']);
    $formData['age'] = trim($_POST['age']);
    $formData['gender'] = $_POST['gender'];

    // Validate inputs
    if (empty($formData['username'])) {
        $errorMessages['username'] = "Username is required.";
    }

    if (empty($formData['password'])) {
        $errorMessages['password'] = "Password is required.";
    }

    if (empty($formData['role'])) {
        $errorMessages['role'] = "Role is required.";
    }

    if (empty($formData['email'])) {
        $errorMessages['email'] = "Email is required.";
    } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errorMessages['email'] = "Invalid email format.";
    }

    if (empty($formData['age']) || !is_numeric($formData['age'])) {
        $errorMessages['age'] = "Valid age is required.";
    }

    if (empty($formData['gender'])) {
        $errorMessages['gender'] = "Gender is required.";
    }

    // Check if an image was uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $imageName = $_FILES['image']['name'];
        $targetDir = "img/menu/"; // Directory where images will be uploaded
        $targetFile = $targetDir . basename($imageName);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if file is an image
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check === false) {
            $errorMessages['image'] = "File is not an image.";
        }

        // Check file size (e.g., limit to 2MB)
        if ($_FILES['image']['size'] > 2000000) {
            $errorMessages['image'] = "Image size should not exceed 2MB.";
        }

        // Allow certain file formats
        $allowedFormats = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowedFormats)) {
            $errorMessages['image'] = "Only JPG, JPEG, PNG & GIF files are allowed.";
        }

        // If no errors, try to upload the image
        if (empty($errorMessages['image'])) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                // Prepare the full image path to insert into the database
                $imagePath = $targetDir . basename($imageName); // Full image path

                // Proceed to insert into the database
                if (empty($errorMessages)) {
                    // Hash the password for security
                    $hashedPassword = password_hash($formData['password'], PASSWORD_DEFAULT);

                    // SQL to insert user
                    $sql = "INSERT INTO users (username, password, role, email, age, gender, image) 
                        VALUES ('{$formData['username']}', '$hashedPassword', '{$formData['role']}', '{$formData['email']}', {$formData['age']}, '{$formData['gender']}', '$imagePath')";

                    if ($conn->query($sql) === TRUE) {
                        $_SESSION['message'] = "User added successfully."; // Set success message
                        $_SESSION['alert_class'] = "alert-success"; // Set alert class
                        header("Location: userlist.php"); // Redirect to user list
                        exit(); // Ensure no further code is executed
                    } else {
                        $_SESSION['message'] = "Error: Could not add user."; // Set error message
                        $_SESSION['alert_class'] = "alert-danger"; // Set alert class for error
                    }
                }
            } else {
                $errorMessages['image'] = "There was an error uploading the image.";
            }
        } else {
            $errorMessages['image'] = "Image upload is required.";
        }
    }

    // If no validation errors, proceed to insert into database
    if (empty($errorMessages)) {
        // Hash the password for security
        $hashedPassword = password_hash($formData['password'], PASSWORD_DEFAULT);

        // SQL to insert user
        $sql = "INSERT INTO users (username, password, role, email, age, gender, image) 
                VALUES ('{$formData['username']}', '$hashedPassword', '{$formData['role']}', '{$formData['email']}', {$formData['age']}, '{$formData['gender']}', '$imageName')";

        if ($conn->query($sql) === TRUE) {
            $_SESSION['message'] = "User added successfully."; // Set success message
            $_SESSION['alert_class'] = "alert-success"; // Set alert class
            header("Location: userlist.php"); // Redirect to user list
            exit(); // Ensure no further code is executed
        } else {
            $_SESSION['message'] = "Error: Could not add user."; // Set error message
            $_SESSION['alert_class'] = "alert-danger"; // Set alert class for error
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add User</title>
    <link
        rel="shortcut icon"
        type="image/x-icon"
        href="assets/img/favicon.jpg" />

    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/animate.css" />
    <link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css" />
    <link rel="stylesheet" href="assets/css/dataTables.bootstrap4.min.css" />
    <link
        rel="stylesheet"
        href="assets/plugins/fontawesome/css/fontawesome.min.css" />
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css" />
    <link rel="stylesheet" href="assets/css/style.css" />
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
                        <h4>Add User</h4>
                        <h6>Create New User</h6>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <form action="adduser.php" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-lg-4 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>Username</label>
                                        <input type="text" name="username" class="form-control" required value="<?php echo htmlspecialchars($formData['username'] ?? ''); ?>" />
                                        <?php if (isset($errorMessages['username'])): ?>
                                            <div class="text-danger"><?php echo $errorMessages['username']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>Password</label>
                                        <input type="password" name="password" class="form-control" required />
                                        <?php if (isset($errorMessages['password'])): ?>
                                            <div class="text-danger"><?php echo $errorMessages['password']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>Role</label>
                                        <select name="role" class="form-control" required>
                                            <option value="">Select Role</option>
                                            <option value="admin" <?php echo (isset($formData['role']) && $formData['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                                            <option value="cashier" <?php echo (isset($formData['role']) && $formData['role'] === 'cashier') ? 'selected' : ''; ?>>Cashier</option>
                                        </select>
                                        <?php if (isset($errorMessages['role'])): ?>
                                            <div class="text-danger"><?php echo $errorMessages['role']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>" />
                                        <?php if (isset($errorMessages['email'])): ?>
                                            <div class="text-danger"><?php echo $errorMessages['email']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>Age</label>
                                        <input type="number" name="age" class="form-control" required value="<?php echo htmlspecialchars($formData['age'] ?? ''); ?>" />
                                        <?php if (isset($errorMessages['age'])): ?>
                                            <div class="text-danger"><?php echo $errorMessages['age']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>Gender</label>
                                        <select name="gender" class="form-control" required>
                                            <option value="">
                                            <option value="Male" <?php echo (isset($formData['gender']) && $formData['gender'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                                            <option value="Female" <?php echo (isset($formData['gender']) && $formData['gender'] === 'Female') ? 'selected' : ''; ?>>Female</option>
                                            <option value="Other" <?php echo (isset($formData['gender']) && $formData['gender'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                                        </select>
                                        <?php if (isset($errorMessages['gender'])): ?>
                                            <div class="text-danger"><?php echo $errorMessages['gender']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>Upload Image</label>
                                        <input type="file" name="image" class="form-control" required />
                                        <?php if (isset($errorMessages['image'])): ?>
                                            <div class="text-danger"><?php echo $errorMessages['image']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">Add User</button>
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
</body>

</html>