<?php
session_start(); // Start session to use session variables
include 'db/config.php'; // Database connection

// Handle Edit Order
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['OrderID'])) {
    $orderId = $_POST['OrderID'];
    $status = $_POST['Status'];

    // Prepare and bind
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE OrderID = ?");
    $stmt->bind_param("si", $status, $orderId);

    // Execute the statement
    if ($stmt->execute()) {
        $_SESSION['success'] = 'Order updated successfully!'; // Store success message in session
    } else {
        $_SESSION['error'] = 'Error updating order: ' . $stmt->error; // Store error message in session
    }

    $stmt->close();
}

// Handle Delete Order
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deleteOrderID'])) {
    $deleteOrderId = $_POST['deleteOrderID'];

    // Prepare and bind
    $stmt = $conn->prepare("DELETE FROM orders WHERE OrderID = ?");
    $stmt->bind_param("i", $deleteOrderId);

    // Execute the statement
    if ($stmt->execute()) {
        $_SESSION['success'] = 'Order deleted successfully!'; // Store success message in session
    } else {
        $_SESSION['error'] = 'Error deleting order: ' . $stmt->error; // Store error message in session
    }

    $stmt->close();
}

// Fetch orders and their respective items
$queryOrders = "
    SELECT o.*, u.username 
    FROM orders o
    LEFT JOIN users u ON o.CreatedBy = u.user_id
";
$orders = $conn->query($queryOrders);

// Get order items for each order
$orderItems = [];
if ($orders->num_rows > 0) {
    while ($order = $orders->fetch_assoc()) {
        $orderId = $order['OrderID'];
        $queryOrderItems = "SELECT * FROM orderitems WHERE OrderID = '$orderId'";
        $itemsResult = $conn->query($queryOrderItems);

        $items = [];
        if ($itemsResult->num_rows > 0) {
            while ($item = $itemsResult->fetch_assoc()) {
                $items[] = $item;
            }
        }
        $orderItems[$orderId] = $items;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0" />
    <title>Orders and Order Product</title>

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
    <link rel="stylesheet" href="pos.css" />

    <Style>
        .toast {
            position: fixed;
            top: 25px;
            right: 30px;
            border-radius: 12px;
            background: #fff;
            padding: 20px 35px 20px 25px;
            box-shadow: 0 6px 20px -5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transform: translateX(calc(100% + 30px));
            transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.35);
        }

        .toast.active {
            transform: translateX(0%);
        }

        .toast .toast-content {
            display: flex;
            align-items: center;
        }

        .toast-content .check {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 35px;
            min-width: 35px;
            background-color: #2770ff;
            color: #fff;
            font-size: 20px;
            border-radius: 50%;
        }

        .toast-content .message {
            display: flex;
            flex-direction: column;
            margin: 0 20px;
        }

        .toast .close {
            position: absolute;
            top: 10px;
            right: 15px;
            padding: 5px;
            cursor: pointer;
            opacity: 0.7;
        }

        .toast .close:hover {
            opacity: 1;
        }

        .toast.success {
            background-color: #d4edda;
            /* Success color */
            color: #155724;
            /* Success text color */
        }

        .toast.error {
            background-color: #f8d7da;
            /* Error color */
            color: #721c24;
            /* Error text color */
        }
    </Style>
</head>

<body>
    <div class="main-wrapper">
        <div class="header">
            <?php include 'php/header.php'; ?>
        </div>
        <?php include 'php/sidebar.php'; ?>

        <div class="page-wrapper">
            <div class="toast">
                <div class="toast-content">
                    <div class="check">✓</div>
                    <div class="message"></div>
                    <div class="close" onclick="this.parentElement.parentElement.classList.remove('active');">&times;</div>
                </div>
            </div>
            <div class="content">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Orders</h4>
                        <h6>Manage your Orders</h6>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer Name</th>
                                        <th>Total Amount</th>
                                        <th>Order Date</th>
                                        <th>Order Type</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($orders->num_rows > 0): ?>
                                        <?php foreach ($orders as $order): ?>
                                            <tr>
                                                <td><?php echo $order['OrderID']; ?></td>
                                                <td><?php echo $order['CustomerName']; ?></td>
                                                <td><?php echo $order['TotalPrice']; ?></td>
                                                <td><?php echo $order['CreatedAt']; ?></td>
                                                <td><?php echo $order['OrderType']; ?></td>
                                                <td>
                                                    <!-- Button trigger view modal -->
                                                    <a href="#" data-bs-toggle="modal" data-bs-target="#viewModal-<?php echo $order['OrderID']; ?>">
                                                        <img src="assets/img/icons/eye.svg" alt="View" class="eye-icon" />
                                                    </a>

                                                    <!-- Button trigger edit modal -->
                                                    <a href="#" data-bs-toggle="modal" data-bs-target="#editModal-<?php echo $order['OrderID']; ?>">
                                                        <img src="assets/img/icons/edit.svg" alt="Edit" class="edit-icon" />
                                                    </a>

                                                    <!-- Button trigger delete modal -->
                                                    <a href="#" data-bs-toggle="modal" data-bs-target="#deleteModal-<?php echo $order['OrderID']; ?>">
                                                        <img src="assets/img/icons/delete.svg" alt="Delete" class="delete-icon" />
                                                    </a>

                                                    <!-- View Modal -->
                                                    <div class="modal fade" id="viewModal-<?php echo $order['OrderID']; ?>" tabindex="-1" aria-labelledby="viewModalLabel-<?php echo $order['OrderID']; ?>" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="viewModalLabel-<?php echo $order['OrderID']; ?>">Transaction Details for Order <?php echo $order['OrderID']; ?></h5>
                                                                    <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="transaction-header">
                                                                        <div class="transaction-amount">
                                                                            ₱<?php echo number_format($order['TotalPrice'], 2); ?>
                                                                        </div>
                                                                        <span class="<?php
                                                                                        switch ($order['status']) {
                                                                                            case 'Pending':
                                                                                                echo 'order-status-pending';
                                                                                                break;
                                                                                            case 'Completed':
                                                                                                echo 'order-status-complete';
                                                                                                break;
                                                                                            case 'Cancelled':
                                                                                                echo 'order-status-cancelled';
                                                                                                break;
                                                                                        }
                                                                                        ?>">
                                                                            <?php echo htmlspecialchars($order['status']); ?>
                                                                        </span>
                                                                    </div>

                                                                    <div class="transaction-section">
                                                                        <h6>Order Information</h6>
                                                                        <p>Customer Name: <span class="highlight"><?php echo htmlspecialchars($order['CustomerName']); ?></span></p>
                                                                    </div>
                                                                    <div class="transaction-section">
                                                                        <div class="d-flex justify-content-between">
                                                                            <p>Order Type: <span class="highlight"><?php echo htmlspecialchars($order['OrderType']); ?></span></p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="transaction-section">
                                                                        <div class="d-flex justify-content-between">
                                                                            <p>Payment Method: <span class="highlight"><?php echo htmlspecialchars($order['PaymentMethod']); ?></span></p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="transaction-section">
                                                                        <div class="d-flex justify-content-between">
                                                                            <p>Discount Type: <span class="highlight"><?php echo htmlspecialchars($order['DiscountType']); ?></span></p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="transaction-section">
                                                                        <div class="d-flex justify-content-between">
                                                                            <p>Created by: <span class="highlight"><?php echo htmlspecialchars($order['username']); ?></span></p>
                                                                        </div>
                                                                    </div>

                                                                    <div class="transaction-section">
                                                                        <h6>Order Details</h6>
                                                                        <ul>
                                                                            <?php if (isset($orderItems[$order['OrderID']])): ?>
                                                                                <?php foreach ($orderItems[$order['OrderID']] as $item): ?>
                                                                                    <li><?php echo htmlspecialchars($item['menu_name']); ?> - <?php echo $item['quantity']; ?> x ₱<?php echo number_format($item['Price'], 2); ?></li>
                                                                                <?php endforeach; ?>
                                                                            <?php else: ?>
                                                                                <li>No Product found</li>
                                                                            <?php endif; ?>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Edit Modal -->
                                                    <div class="modal fade" id="editModal-<?php echo $order['OrderID']; ?>" tabindex="-1" aria-labelledby="editModalLabel-<?php echo $order['OrderID']; ?>" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="editModalLabel-<?php echo $order['OrderID']; ?>">Edit Status for Order <?php echo $order['OrderID']; ?></h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <!-- Edit form -->
                                                                    <form action="" method="POST">
                                                                        <input type="hidden" name="OrderID" value="<?php echo $order['OrderID']; ?>" />
                                                                        <div class="form-group">
                                                                            <label for="Status">Status</label>
                                                                            <select name="Status" class="form-control" required>
                                                                                <option value="Pending" <?php echo ($order['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                                                                <option value="Completed" <?php echo ($order['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                                                                <option value="Cancelled" <?php echo ($order['status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                                                            </select>
                                                                        </div>
                                                                        <button type="submit" class="btn btn-primary">Save changes</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Delete Confirmation Modal -->
                                                    <div class="modal fade" id="deleteModal-<?php echo $order['OrderID']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel-<?php echo $order['OrderID']; ?>" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="deleteModalLabel-<?php echo $order['OrderID']; ?>">Confirm Deletion</h5>
                                                                    <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    Are you sure you want to delete order <strong><?php echo $order['OrderID']; ?></strong>? This action cannot be undone.
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <form action="" method="POST" style="display: inline;">
                                                                        <input type="hidden" name="deleteOrderID" value="<?php echo $order['OrderID']; ?>" />
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                                                                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6">No Order found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- Toast Notification -->
    <div class="toast" id="toast">
        <div class="toast-content">
            <div class="check">✔</div>
            <div class="message">
                <div class="text" id="toast-message"></div>
            </div>
            <div class="close" onclick="closeToast()">✖</div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toast = document.querySelector('.toast');
            const successMessage = '<?php echo isset($_SESSION['success']) ? $_SESSION['success'] : ''; ?>';
            const errorMessage = '<?php echo isset($_SESSION['error']) ? $_SESSION['error'] : ''; ?>';

            if (successMessage) {
                showToast(successMessage, 'success');
                <?php unset($_SESSION['success']); ?> // Clear the message after displaying
            }

            if (errorMessage) {
                showToast(errorMessage, 'error');
                <?php unset($_SESSION['error']); ?> // Clear the message after displaying
            }
        });

        function showToast(message, type) {
            const toast = document.querySelector('.toast');
            toast.classList.add('active');
            toast.querySelector('.message').textContent = message;

            setTimeout(() => {
                toast.classList.remove('active');
            }, 3000); // Adjust duration as needed
        }
    </script>
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