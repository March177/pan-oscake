<?php
// Include your database config and the file containing the function
include 'db/config.php'; // Your database configuration
include 'functions/getcategory.php'; // Update this path to where get_filtered_categories is defined

// Get the category ID from the URL
$c_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch category details using the ID (adjust the function call accordingly)
$filter = [
  'category_name' => '', // Add your filtering logic as needed
  'description' => '',
  'status' => ''
];

// Fetch the categories using the filter
$categories = get_filtered_categories($filter); // Use appropriate parameters for your case

// Check if any categories exist
if (empty($categories)) {
  echo "No categories found.";
  exit;
}

// Display the first category (assuming you want to show the first one)
$category = $categories[0]; // Or handle it according to your logic

// Now you can display the category details
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=0" />
  <meta name="description" content="POS - Bootstrap Admin Template" />
  <meta
    name="keywords"
    content="admin, estimates, bootstrap, business, corporate, creative, invoice, html5, responsive, Projects" />
  <meta name="author" content="Dreamguys - Bootstrap Admin Template" />
  <meta name="robots" content="noindex, nofollow" />
  <title>Cakes</title>

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
  <!-- <div id="global-loader">
    <div class="whirly-loader"></div>
  </div> -->

  <div class="main-wrapper">
    <div class="header">

      <a id="mobile_btn" class="mobile_btn" href="#sidebar">
        <span class="bar-icon">
          <span></span>
          <span></span>
          <span></span>
        </span>
      </a>


      <?php include 'php/header.php'; ?>


      <?php include 'php/sidebar.php'; ?>
      <div class="page-wrapper">
        <div class="content">
          <div class="page-header">
            <div class="page-title">
              <h4>Category Details</h4>
              <h6>Full details of the category</h6>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-8 col-sm-12">
              <div class="card">
                <div class="card-body">
                  <div class="productdetails">
                    <ul class="product-bar">
                      <li>
                        <h4>Category Name</h4>
                        <h6><?php echo htmlspecialchars($category['category_name']); ?></h6>
                      </li>
                      <li>
                        <h4>Description</h4>
                        <h6><?php echo nl2br(htmlspecialchars($category['description'])); ?></h6>
                      </li>
                      <li>
                        <h4>Status</h4>
                        <h6><?php echo htmlspecialchars($category['status']); ?></h6>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
            <!-- <div class="col-lg-4 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="slider-product-details">
                            <div class="owl-carousel owl-theme product-slide">
                                <div class="slider-product">
                                    <img src="assets/img/product/product69.jpg" alt="img" />
                                    <h4>macbookpro.jpg</h4>
                                    <h6>581kb</h6>
                                </div>
                                <div class="slider-product">
                                    <img src="assets/img/product/product69.jpg" alt="img" />
                                    <h4>macbookpro.jpg</h4>
                                    <h6>581kb</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
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