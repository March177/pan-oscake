<?php
include 'db/config.php';
include 'functions/user.php';

$filter = [
    'status' => isset($_GET['status']) ? $_GET['status'] : ''
];

$rows = get_users($filter);
$message = '';
$alert_class = '';

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $alert_class = $_SESSION['alert_class'];
    unset($_SESSION['message']); // Clear message after displaying
    unset($_SESSION['alert_class']); // Clear alert class
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0" />
    <title>User List</title>
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
        .modal-dialog {
            max-width: 600px;
        }

        .modal-content {
            padding: 20px;
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            display: none;
            padding: 15px;
            background-color: #28a745;
            color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
                        <h4>User List</h4>
                        <h6>Manage your users</h6>
                    </div>
                    <div class="page-btn">
                        <a href="adduser.php" class="btn btn-added">
                            <img src="assets/img/icons/plus.svg" alt="img" class="me-1" />
                            Add New User
                        </a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <!-- Notification Section -->
                        <?php if ($message): ?>
                            <div class="alert <?php echo $alert_class; ?>">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <div class="table-top">
                            <div class="search-set">
                                <div class="search-path">
                                    <a class="btn btn-filter" id="filter_search">
                                        <img src="assets/img/icons/filter.svg" alt="img" />
                                        <span><img src="assets/img/icons/closes.svg" alt="img" /></span>
                                    </a>
                                </div>
                                <div class="search-input">
                                    <a class="btn btn-searchset">
                                        <img src="assets/img/icons/search-white.svg" alt="img" />
                                    </a>
                                </div>
                            </div>
                            <div class="wordset">
                                <ul>
                                    <li><a data-bs-toggle="tooltip" title="pdf"><img src="assets/img/icons/pdf.svg" alt="img" /></a></li>
                                    <li><a data-bs-toggle="tooltip" title="excel"><img src="assets/img/icons/excel.svg" alt="img" /></a></li>
                                    <li><a data-bs-toggle="tooltip" title="print"><img src="assets/img/icons/printer.svg" alt="img" /></a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="card mb-0" id="filter_inputs">
                            <div class="card-body pb-0">
                                <div class="row">
                                    <div class="col-lg col-sm-6 col-12"></div>
                                    <div class="col-lg-2 col-sm-4 col-6">
                                        <div class="form-group">
                                            <select class="select" name="status" id="status-filter">
                                                <option value="">Choose Status</option>
                                                <option value="Active" <?php if ($filter['status'] == 'Active') echo 'selected'; ?>>Active</option>
                                                <option value="Inactive" <?php if ($filter['status'] == 'Inactive') echo 'selected'; ?>>Not Available</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-1 col-sm-6 col-12">
                                        <div class="form-group">
                                            <a class="btn btn-filters ms-auto" onclick="performFilter()">
                                                <img src="assets/img/icons/search-whites.svg" alt="img" />
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="table">
                            <div class="table-responsive">
                                <table class="table datanew">
                                    <thead>
                                        <tr>
                                            <th><label class="checkboxs"><input type="checkbox" id="select-all" /><span class="checkmarks"></span></label></th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Age</th>
                                            <th>Image</th>
                                            <th>Gender</th>
                                            <th>Role</th> <!-- Add this line -->
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($rows as $row) : ?>
                                            <tr>
                                                <td><label class="checkboxs"><input type="checkbox" /><span class="checkmarks"></span></label></td>
                                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                                <td><?php echo htmlspecialchars($row['age']); ?></td>
                                                <td>
                                                    <?php if (!empty($row['image'])): ?>
                                                        <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="User Image" style="width:50px;height:50px;">
                                                    <?php else: ?>
                                                        No Image
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($row['gender']); ?></td>
                                                <td><?php echo htmlspecialchars($row['role']); ?></td> <!-- Add this line -->
                                                <td><?php echo htmlspecialchars($row['status']); ?></td>
                                                <td>
                                                    <a class="me-3 view-user" data-id="<?php echo htmlspecialchars($row['user_id']); ?>" href="javascript:void(0);">
                                                        <img src="assets/img/icons/eye.svg" alt="img" />
                                                    </a>
                                                    <a class="me-3 edit-user" href="edituser.php?user_id=<?php echo htmlspecialchars($row['user_id']); ?>">
                                                        <img src="assets/img/icons/edit.svg" alt="Edit User" />
                                                    </a>


                                                    <a class="me-3 confirm-text delete-user" href="javascript:void(0);" data-id="<?php echo htmlspecialchars($row['user_id']); ?>">
                                                        <img src="assets/img/icons/delete.svg" alt="img" />
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

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
        function performFilter() {
            const status = document.getElementById('status-filter').value;
            window.location.href = `userlist.php?status=${status}`;
        }
    </script>
</body>

</html>