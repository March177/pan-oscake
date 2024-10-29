<?php
include 'db/config.php';

// Prepare the SQL query to fetch categories
$categoryQuery = "SELECT c.c_id, c.category_name 
                  FROM categories c
                  LEFT JOIN menu m ON c.category_name = m.category_name
                  GROUP BY c.c_id, c.category_name";
$categoryResult = $conn->query($categoryQuery);

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0" />
    <meta name="keywords" content="admin, estimates, bootstrap, business, corporate, creative, invoice, html5, responsive, Projects" />
    <meta name="robots" content="noindex, nofollow" />
    <title>Add Product</title>

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
                        <h4>Menu Add</h4>
                        <h6>Create new Menu</h6>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
                            <div class="success-message show">Menu added successfully.</div>
                        <?php endif; ?>

                        <form action="functions/getmenu.php" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-lg-3 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>Menu Name</label>
                                        <input type="text" name="menu_name" class="form-control" required />
                                    </div>
                                </div>

                                <div class="col-lg-3 col-sm-6 col-12">
                                    <div class="mb-3">
                                        <label for="c_id" class="form-label">Category</label>
                                        <select name="category_name" id="c_id" class="form-select" required>
                                            <option value="">Choose Category</option>
                                            <?php
                                            if ($categoryResult->num_rows > 0) {
                                                while ($row = $categoryResult->fetch_assoc()) {
                                                    echo '<option value="' . htmlspecialchars($row['category_name']) . '">' . htmlspecialchars($row['category_name']) . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>


                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea name="description" class="form-control" required></textarea>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>Price</label>
                                        <input type="number" name="price" class="form-control" step="0.01" min="0" required />
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
                                <div class="col-lg-3 col-sm-6 col-12">
                                    <div class="form-group image-upload text-center">
                                        <input type="file" name="menu_image" id="file-upload" accept="image/*" style="display:none;" />
                                        <label for="file-upload" class="upload-label d-flex flex-column align-items-center justify-content-center">
                                            <img src="assets/img/upload-image.png" alt="" class="upload-image mb-2" />
                                            <h4 class="mb-0">Upload Image</h4>
                                            <img id="image-preview" class="preview-image mt-2" />
                                        </label>
                                    </div>
                                </div>

                                <div class="col-lg-12 text-right">
                                    <button type="submit" class="btn btn-primary">Add Menu</button>
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
        document.getElementById('file-upload').addEventListener('change', function() {
            var file = this.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var img = document.getElementById('image-preview');
                    img.src = e.target.result;
                    img.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });


        setTimeout(function() {
            var message = document.querySelector('.success-message');
            if (message) {
                message.classList.remove('show');
            }
        }, 5000);
    </script>
</body>

</html>