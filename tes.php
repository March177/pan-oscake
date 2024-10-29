<?php
include 'db/config.php'; // Database connection

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

    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.jpg" />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/animate.css" />
    <link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css" />
    <link rel="stylesheet" href="assets/css/dataTables.bootstrap4.min.css" />
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css" />
    <link rel="stylesheet" href="assets/css/style.css" />
    <style>
        .modal-content {
            border-radius: 15px;
            padding: 20px;
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: auto;
        }

        .transaction-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e5e5e5;
            padding-bottom: 10px;
        }

        .transaction-amount {
            font-size: 1.5em;
            font-weight: bold;
            color: #15B392;
        }

        .order-status-pending {
            background: #FFEFD5;
            /* Light orange background */
            color: orange;
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 10px;
        }

        .order-status-complete {
            background: #E6F8E0;
            /* Light green background */
            color: green;
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 10px;
        }

        .order-status-cancelled {
            background: #FDDDDD;
            /* Light red background */
            color: red;
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 10px;
        }

        .transaction-section {
            margin: 20px 0;
        }

        .transaction-section h6 {
            font-size: 0.9em;
            color: #888;
        }

        .transaction-section p {
            font-size: 1em;
            margin-bottom: 5px;
        }

        .highlight {
            background-color: #e9ecef;
            padding: 5px;
            border-radius: 4px;
        }

        .btn-close-custom {
            background-color: transparent;
            border: none;
            font-size: 1.5em;
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
                        <h4>Transaction</h4>
                        <h6>Manage your Transaction</h6>
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
                                        <th>Product</th>
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
                                                    <!-- Button trigger modal -->
                                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#itemsModal-<?php echo $order['OrderID']; ?>">
                                                        View Product
                                                    </button>

                                                    <!-- Modal -->
                                                    <div class="modal fade" id="itemsModal-<?php echo $order['OrderID']; ?>" tabindex="-1" aria-labelledby="itemsModalLabel-<?php echo $order['OrderID']; ?>" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="itemsModalLabel-<?php echo $order['OrderID']; ?>">Transaction Details for Order <?php echo $order['OrderID']; ?></h5>
                                                                    <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="transaction-header">
                                                                        <div class="transaction-amount">
                                                                            ₱<?php echo number_format($order['TotalPrice'], 2); ?>
                                                                        </div>
                                                                        <span class="
            <?php
                                            // Change class based on status
                                            switch ($order['status']) {
                                                case 'Pending':
                                                    echo 'order-status-pending';
                                                    break;
                                                case 'Complete':
                                                    echo 'order-status-complete';
                                                    break;
                                                case 'Cancelled':
                                                    echo 'order-status-cancelled';
                                                    break;
                                            }
            ?>
        ">
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
                                                                            <p>Payment Method: <span class="highlight"><?php echo htmlspecialchars($order['PaymentMethod']); ?></span></p> <!-- Added Payment Method -->
                                                                        </div>
                                                                    </div>
                                                                    <div class="transaction-section">
                                                                        <div class="d-flex justify-content-between">
                                                                            <p>Discount Type: <span class="highlight"><?php echo htmlspecialchars($order['DiscountType']); ?></span></p> <!-- Added Discount -->
                                                                        </div>
                                                                    </div>
                                                                    <div class="transaction-section">
                                                                        <div class="d-flex justify-content-between">
                                                                            <p>Created by: <span class="highlight"><?php echo htmlspecialchars($order['username']); ?></span></p> <!-- Added Created By -->
                                                                        </div>
                                                                    </div>


                                                                    <div class="transaction-section">
                                                                        <h6>Order Details</h6>
                                                                        <ul>
                                                                            <?php if (isset($orderItems[$order['OrderID']])): ?>
                                                                                <?php foreach ($orderItems[$order['OrderID']] as $item): ?>
                                                                                    <li>
                                                                                        <?php echo htmlspecialchars($item['menu_name']); ?> - <?php echo $item['quantity']; ?> x ₱<?php echo number_format($item['Price'], 2); ?>
                                                                                    </li>
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
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6">No Transaction found</td>
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

    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <script src="assets/js/feather.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/select2.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>

</html>