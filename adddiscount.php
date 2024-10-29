<?php
session_start(); // Start session
include 'db/config.php'; // Database connection

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $discount_name = $_POST['discount_name'];
    $discount_value = $_POST['discount_value'];
    $discount_code = $_POST['discount_code'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // SQL to insert discount
    $sql = "INSERT INTO discounts (discount_name, discount_value, discount_code, start_date, end_date)
            VALUES ('$discount_name', '$discount_value', '$discount_code', '$start_date', '$end_date')";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "Discount added successfully."; // Set success message in session
        $_SESSION['alert_class'] = "alert-success"; // Set alert class
    } else {
        $_SESSION['message'] = "Error: Could not add discount."; // Set error message
        $_SESSION['alert_class'] = "alert-danger"; // Set alert class for error
    }
    header("Location: discountlist.php"); // Redirect to discount list
    exit(); // Ensure no further code is executed
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Discount</title>
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
    <style>
        .alert-custom {
            position: fixed;
            top: 10%;
            left: 80%;
            width: 300px;
            z-index: 9999;
            display: none;
            text-align: center;
            padding: 10px;
            border-radius: 5px;
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
                        <h4>Discount Add</h4>
                        <h6>Create New Discount</h6>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div id="alert-custom" class="alert-custom">
                            <?php if (isset($message)): ?>
                                <div class="alert <?php echo $alert_class; ?>">
                                    <?php echo $message; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <form action="adddiscount.php" method="POST">
                            <div class="row">
                                <div class="col-lg-3 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>Discount Name</label>
                                        <input type="text" name="discount_name" class="form-control" required />
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>Discount Value</label>
                                        <input type="number" step="0.01" name="discount_value" class="form-control" required />
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>Discount Code</label>
                                        <input type="text" name="discount_code" class="form-control" required />
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>Start Date</label>
                                        <input type="datetime-local" name="start_date" class="form-control" required />
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>End Date</label>
                                        <input type="datetime-local" name="end_date" class="form-control" required />
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <button type="submit" class="btn btn-submit">Submit</button>
                                    <a href="menulist.php" class="btn btn-cancel">Cancel</a>
                                </div>
                            </div>
                        </form>
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
                    // Show alert if message is set
                    <?php if (isset($message)): ?>
                        $("#alert-custom").show();
                        setTimeout(function() {
                            $("#alert-custom").fadeOut();
                        }, 2000);
                    <?php endif; ?>
                });
            </script>
        </div>
    </div>
</body>

</html>