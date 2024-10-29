<?php
// Include your database config and the file containing the function
include 'db/config.php'; // Your database configuration
include 'functions/getmenu.php'; // Update this path to where get_menu_by_id is defined

// Get the menu ID from the URL
$menu_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch menu details using the ID
$menu = get_menu_by_id($menu_id); // Use appropriate parameters for your case

// Check if the menu exists
if (!$menu) {
    echo "Menu not found.";
    exit;
}

// Now you can display the menu details
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0" />
  <meta name="description" content="POS - Bootstrap Admin Template" />
  <meta name="keywords" content="admin, estimates, bootstrap, business, corporate, creative, invoice, html5, responsive, Projects" />
  <meta name="author" content="Dreamguys - Bootstrap Admin Template" />
  <meta name="robots" content="noindex, nofollow" />
  <title>Menu Details</title>

  <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.jpg" />
  <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
  <link rel="stylesheet" href="assets/css/animate.css" />
  <link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css" />
  <link rel="stylesheet" href="assets/plugins/owlcarousel/owl.carousel.min.css" />
  <link rel="stylesheet" href="assets/css/dataTables.bootstrap4.min.css" />
  <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css" />
  <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css" />
  <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body>
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
    </div>

    <div class="page-wrapper">
      <div class="content">
        <div class="page-header">
          <div class="page-title">
            <h4>Menu Details</h4>
            <h6>Full details of the menu item</h6>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-8 col-sm-12">
            <div class="card">
              <div class="card-body">
                <div class="productdetails">
                  <ul class="product-bar">
                    <li>
                      <h4>Menu Name</h4>
                      <h6><?php echo htmlspecialchars($menu['menu_name']); ?></h6>
                    </li>
                    <li>
                      <h4>Description</h4>
                      <h6><?php echo nl2br(htmlspecialchars($menu['description'])); ?></h6>
                    </li>
                    <li>
                      <h4>Price</h4>
                      <h6><?php echo htmlspecialchars($menu['price']); ?></h6>
                    </li>
                    <li>
                      <h4>Status</h4>
                      <h6><?php echo htmlspecialchars($menu['status']); ?></h6>
                    </li>
                    <li>
                      <h4>Category</h4>
                      <h6><?php echo htmlspecialchars($menu['category_name']); ?></h6>
                    </li>
                  </ul>
                  <?php if (!empty($menu['image_path'])): ?>
                    <div class="image-preview-container">
                      <img src="<?php echo htmlspecialchars($menu['image_path']); ?>" alt="Menu Image" class="img-fluid" />
                    </div>
                  <?php else: ?>
                    <p>No image available for this menu item.</p>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="assets/js/jquery-3.6.0.min.js"></script>
  <script src="assets/js/bootstrap.bundle.min.js"></script>
  <script src="assets/plugins/owlcarousel/owl.carousel.min.js"></script>
  <script src="assets/plugins/select2/js/select2.min.js"></script>
  <script src="assets/js/script.js"></script>
</body>

</html>
