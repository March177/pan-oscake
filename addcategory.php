<?php
include 'db/config.php';
include 'functions/getcategory.php'; // Include the file with the functions

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $category_name = $_POST['category_name'];

    $description = $_POST['description'];
    $status = $_POST['status'];

    // Call the function to insert category
    $success = insertCategory($conn, $category_name, $description, $status);

    if ($success) {
        $message = "Category item added successfully.";
        $alert_class = "alert-success";
    } else {
        $message = "Error: Could not add category.";
        $alert_class = "alert-danger";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0" />
    <meta name="keywords" content="admin, estimates, bootstrap, business, corporate, creative, invoice, html5, responsive, Projects" />
    <meta name="robots" content="noindex, nofollow" />
    <title>Add Category</title>
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.jpg" />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/animate.css" />
    <link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css" />
    <link rel="stylesheet" href="assets/css/dataTables.bootstrap4.min.css" />
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css" />
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css" />
    <link rel="stylesheet" href="assets/css/style.css" />
    <style>
        .alert-custom {
            position: fixed;
            top: 10%;
            left: 80%;

            width: 300px;
            /* Adjust the width as needed */
            z-index: 9999;
            /* Ensure it's on top of other content */
            display: none;
            /* Hide by default */
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
                        <h4>Category Add</h4>
                        <h6>Create new Category</h6>
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

                        <form action="addcategory.php" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-lg-3 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>Category Name</label>
                                        <input type="text" name="category_name" class="form-control" required />
                                    </div>
                                </div>

                                <div class="col-lg-3 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select name="status" class="form-control" required>
                                            <option value="Available">Available</option>
                                            <option value="Not Available">Not Available</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea name="description" class="form-control"></textarea>
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
                        }, 2000); // Hide after 2 seconds
                    <?php endif; ?>
                });
            </script>
        </div>
    </div>
</body>

</html>