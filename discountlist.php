<?php
include 'db/config.php';
include 'functions/discount.php';

$filter = [
    'status' => isset($_GET['status']) ? $_GET['status'] : ''
];

$rows = get_filtered_discounts($filter);
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
    <title>Discount List</title>
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
            /* Optional: adjust the max width of the modal */
        }

        .modal-content {
            padding: 20px;
            /* Optional: add padding inside the modal */
        }

        /* Notification styles */
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
                        <h4>Discount List</h4>
                        <h6>Manage your discounts</h6>
                    </div>
                    <div class="page-btn">
                        <a href="adddiscount.php" class="btn btn-added">
                            <img src="assets/img/icons/plus.svg" alt="img" class="me-1" />
                            Add New Discount
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
                                                <option value="Active">Active</option>
                                                <option value="Inactive">Not Available</option>
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
                                            <th>Discount Name</th>
                                            <th>Discount Code</th>
                                            <th>Discount Value</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($rows as $row) : ?>
                                            <tr>
                                                <td><label class="checkboxs"><input type="checkbox" /><span class="checkmarks"></span></label></td>
                                                <td><?php echo htmlspecialchars($row['discount_name']); ?></td>
                                                <td><?php echo htmlspecialchars($row['discount_code']); ?></td>
                                                <td><?php echo htmlspecialchars($row['discount_value']); ?></td>
                                                <td><?php echo htmlspecialchars($row['start_date']); ?></td>
                                                <td><?php echo htmlspecialchars($row['end_date']); ?></td>
                                                <td><?php echo htmlspecialchars($row['status']); ?></td>
                                                <td>
                                                    <a class="me-3 view-discount" data-id="<?php echo htmlspecialchars($row['discount_id']); ?>" href="javascript:void(0);">
                                                        <img src="assets/img/icons/eye.svg" alt="img" />
                                                    </a>

                                                    <a class="me-3 edit-discount" data-id="<?php echo htmlspecialchars($row['discount_id']); ?>" href="javascript:void(0);">
                                                        <img src="assets/img/icons/edit.svg" alt="img" />
                                                    </a>


                                                    <a class="me-3 confirm-text" href="javascript:void(0);" data-id="<?php echo htmlspecialchars($row['discount_id']); ?>">
                                                        <img src="assets/img/icons/delete.svg" alt="img" />
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- Discount Modal (View/Edit) -->
                        <div class="modal fade" id="discountModal" tabindex="-1" aria-labelledby="discountModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="discountModalLabel">Discount Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="discountForm">
                                            <input type="hidden" name="discount_id" id="discount_id">
                                            <div class="mb-3">
                                                <label for="discount_name" class="form-label">Discount Name</label>
                                                <input type="text" class="form-control" id="discount_name" name="discount_name" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="discount_code" class="form-label">Discount Code</label>
                                                <input type="text" class="form-control" id="discount_code" name="discount_code" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="discount_value" class="form-label">Discount Value</label>
                                                <input type="number" class="form-control" id="discount_value" name="discount_value" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="start_date" class="form-label">Start Date</label>
                                                <input type="date" class="form-control" id="start_date" name="start_date" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="end_date" class="form-label">End Date</label>
                                                <input type="date" class="form-control" id="end_date" name="end_date" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="status" class="form-label">Status</label>
                                                <select class="form-control" id="status" name="status" required>
                                                    <option value="Active">Active</option>
                                                    <option value="Inactive">Not Available</option>
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </form>
                                    </div>
                                </div>
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
        $(document).ready(function() {
            $('.datanew').DataTable();

            // Show success message if exists
            if ('<?php echo $successMessage; ?>') {
                $('#notification').fadeIn().delay(3000).fadeOut();
            }

            $('.view-discount').on('click', function() {
                var discountId = $(this).data('id');
                $.ajax({
                    url: 'functions/discount.php',
                    method: 'POST',
                    data: {
                        action: 'get_discount',
                        discount_id: discountId
                    },
                    success: function(data) {
                        var discount = JSON.parse(data);
                        $('#discount_id').val(discount.discount_id);
                        $('#discount_name').val(discount.discount_name);
                        $('#discount_code').val(discount.discount_code);
                        $('#discount_value').val(discount.discount_value);
                        $('#start_date').val(discount.start_date);
                        $('#end_date').val(discount.end_date);
                        $('#status').val(discount.status);
                        $('#discountModal').modal('show');
                    }
                });
            });

            // Edit discount
            $('.edit-discount').on('click', function() {
                var discountId = $(this).data('id');
                $.ajax({
                    url: 'functions/discount.php',
                    method: 'POST',
                    data: {
                        action: 'get_discount',
                        discount_id: discountId
                    },
                    success: function(data) {
                        var discount = JSON.parse(data);
                        $('#discount_id').val(discount.discount_id);
                        $('#discount_name').val(discount.discount_name);
                        $('#discount_code').val(discount.discount_code);
                        $('#discount_value').val(discount.discount_value);
                        $('#start_date').val(discount.start_date);
                        $('#end_date').val(discount.end_date);
                        $('#status').val(discount.status);
                        $('#discountModal').modal('show');
                    }
                });
            });

            // Save discount changes via AJAX
            $('#discountForm').on('submit', function(event) {
                event.preventDefault();
                $.ajax({
                    url: 'functions/discount.php',
                    method: 'POST',
                    data: $(this).serialize() + '&action=save_discount',
                    success: function(response) {
                        if (response === 'success') {
                            window.location.href = 'discountlist.php?success=Discount saved successfully!';
                        }
                    }
                });
            });

            // Confirm delete action
            $('.confirm-text').on('click', function() {
                var discountId = $(this).data('id');
                if (confirm('Are you sure you want to delete this discount?')) {
                    window.location.href = 'functions/discount.php?action=delete_discount&discount_id=' + discountId;
                }
            });
        });

        function performFilter() {
            var status = $('#status-filter').val();
            window.location.href = 'discountlist.php?status=' + status;
        }
    </script>

</body>

</html>