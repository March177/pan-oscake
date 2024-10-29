<?php

include 'functions/getcategory.php';


$filter = [
  'status' => isset($_GET['status']) ? $_GET['status'] : ''
];

$rows = get_filtered_categories($filter);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=0" />

  <meta
    name="keywords"
    content="admin, estimates, bootstrap, business, corporate, creative, invoice, html5, responsive, Projects" />

  <meta name="robots" content="noindex, nofollow" />
  <title>Category</title>

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
      <?php include 'php/header.php' ?>
    </div>
    <?php include 'php/sidebar.php'; ?>

    <div class="page-wrapper">
      <div class="content">
        <div class="page-header">
          <div class="page-title">
            <h4>Category List</h4>
            <h6>Manage your category</h6>
          </div>
          <div class="page-btn">
            <a href="addcategory.php" class="btn btn-added">
              <img src="assets/img/icons/plus.svg" alt="img" class="me-1" />
              Add New Category
            </a>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
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

            <div class="card mb-0" id="filter_inputs">
              <div class="card-body pb-0">
                <div class="row">
                  <div class="col-lg col-sm-6 col-12"></div>
                  <div class="col-lg-2 col-sm-4 col-6">
                    <div class="form-group">
                      <select class="select" name="status" id="status-filter">
                        <option value="">Choose Status</option>
                        <option value="Active">Available</option>
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
                      <th>
                        <label class="checkboxs">
                          <input type="checkbox" id="select-all" />
                          <span class="checkmarks"></span>
                        </label>
                      </th>
                      <th>Category Name</th>
                      <th>Description</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($rows as $row) : ?>
                      <tr>
                        <td>
                          <label class="checkboxs">
                            <input type="checkbox" />
                            <span class="checkmarks"></span>
                          </label>
                        </td>
                        <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                        <td><?php echo htmlspecialchars(substr($row['description'], 0, 50)) . '...'; ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td>
                          <a class="me-3" href="categorydetails.php?id=<?php echo htmlspecialchars($row['c_id']); ?>">
                            <img src="assets/img/icons/eye.svg" alt="img" />
                          </a>
                          <a class="me-3" href="editcategory.php?c_id=<?php echo $row['c_id']; ?>">
                            <img src="assets/img/icons/edit.svg" alt="img" />
                          </a>
                          <a class="me-3 confirm-text" href="javascript:void(0);" data-id="<?php echo htmlspecialchars($row['c_id']); ?>">
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

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function() {
      $('#status-filter').on('change', function() {
        performFilter();
      });

      function performFilter() {
        var status = $('#status-filter').val();

        $.ajax({
          url: 'getcategory.php',
          type: 'GET',
          data: {
            status: status
          },
          success: function(response) {
            $('#table tbody').html(response); // Replace the table body with the new rows
          },
          error: function() {
            alert('An error occurred while processing the request.');
          }
        });
      }
    });

    $(document).ready(function() {
      $(document).on("click", ".confirm-text", function() {
        var $this = $(this);
        var c_id = $this.data("id");

        var deleteUrl = "delete_menu.php";

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
              url: deleteUrl,
              type: "POST",
              data: {
                c_id: c_id
              },
              dataType: "json",
              success: function(response) {
                if (response.success) {
                  Swal.fire("Deleted!", response.message, "success");
                  $this.closest("tr").remove(); // Remove the row from the table
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