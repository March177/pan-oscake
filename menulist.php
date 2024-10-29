<?php
// Include the database configuration file
include 'db/config.php';

// Function to delete a menu item
function deleteMenuItem($conn, $menuId)
{
    $menuId = mysqli_real_escape_string($conn, $menuId);
    $query = "DELETE FROM menu WHERE menu_id = '$menuId'";
    $result = mysqli_query($conn, $query);
    return $result; // return the result directly
}

$query = "
    SELECT 
        menu_id, 
        menu_name, 
        price, 
        image_path, 
        description, 
        status, 
        category_name
    FROM 
        menu
";

$rows = mysqli_query($conn, $query);
if (!$rows) {
    die('Query Error: ' . mysqli_error($conn));
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['menu_id'])) {
    $menuId = $_POST['menu_id'];
    if (deleteMenuItem($conn, $menuId)) {
        echo json_encode(['success' => true, 'message' => 'Menu item deleted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete menu item.']);
    }
    exit; // Exit after handling the AJAX request
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0" />
    <title>Display Menu</title>
    <link
        rel="shortcut icon"
        type="image/x-icon"
        href="assets/img/favicon.jpg" />

    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/animate.css" />
    <link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css" />
    <link rel="stylesheet" href="assets/css/dataTables.bootstrap4.min.css" />
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css" />
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
                    <div class="row align-items-center">
                        <div class="col">
                            <h4>Menu List</h4>
                            <h6>Manage your menu</h6>
                        </div>
                        <div class="col-auto text-end float-end ms-auto">
                            <a href="addmenu.php" class="btn btn-added">
                                <img src="assets/img/icons/plus.svg" alt="img" class="me-1" />
                                Add New Menu
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
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
                                <li>
                                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="pdf">
                                        <img src="assets/img/icons/pdf.svg" alt="img" />
                                    </a>
                                </li>
                                <li>
                                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="excel">
                                        <img src="assets/img/icons/excel.svg" alt="img" />
                                    </a>
                                </li>
                                <li>
                                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="print">
                                        <img src="assets/img/icons/printer.svg" alt="img" />
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table datanew">
                            <thead>
                                <tr>
                                    <th>Menu Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($rows)) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['menu_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['price']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($row['description'], 0, 30)) . '...'; ?></td>
                                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                                        <td>
                                            <a class="me-3" href="menu-details.php?id=<?php echo $row['menu_id']; ?>">
                                                <img src="assets/img/icons/eye.svg" alt="View" />
                                            </a>
                                            <a class="me-3" href="editmenu.php?id=<?php echo $row['menu_id']; ?>">
                                                <img src="assets/img/icons/edit.svg" alt="Edit" />
                                            </a>
                                            <a class="me-3 confirm-texts" data-id="<?php echo $row['menu_id']; ?>" href="javascript:void(0);">
                                                <img src="assets/img/icons/delete.svg" alt="Delete" />
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script>
        $(document).ready(function() {
            $(document).on("click", ".confirm-texts", function() {
                var menu_id = $(this).data("id");

                Swal.fire({
                    title: "Are you sure?",
                    text: "Proceeding will delete this item permanently from the database!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "Cancel",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "delete_menu.php",
                            type: "POST",
                            data: {
                                menu_id: menu_id
                            },
                            dataType: "json",
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire("Deleted!", response.message, "success");
                                    location.reload(); // Reload the page after deletion
                                } else {
                                    Swal.fire("Error!", response.message, "error");
                                }
                            },
                            error: function() {
                                Swal.fire("Error!", "There was an error deleting the item from the database.", "error");
                            },
                        });
                    }
                });
            });
        });
    </script>
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