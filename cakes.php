<?php
// Include the database configuration file
include 'db/config.php';

// Initialize variables for filter
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : '';
$selectedPriceRange = isset($_GET['price_range']) ? $_GET['price_range'] : '';

// Function to handle the price range values
function getPriceRange($priceRange)
{
  switch ($priceRange) {
    case '10-100':
      return [10, 100];
    case '101-500':
      return [101, 500];
    case '501-1000':
      return [501, 1000];
    default:
      return [0, PHP_INT_MAX]; // Show all by default
  }
}

// Get the price range values based on the selected price range
list($minPrice, $maxPrice) = getPriceRange($selectedPriceRange);

// Base query to fetch cakes
$cakesQuery = "SELECT * FROM menu WHERE status = 1";

// Apply category filter if selected
if (!empty($selectedCategory)) {
  $cakesQuery .= " AND category_name = '" . $conn->real_escape_string($selectedCategory) . "'";
}

// Apply price range filter if selected
if (!empty($selectedPriceRange)) {
  $cakesQuery .= " AND price BETWEEN " . intval($minPrice) . " AND " . intval($maxPrice);
}

// Fetch cakes based on filters
$cakesResult = $conn->query($cakesQuery);

// Fetch categories only once
$categoryQuery = "SELECT DISTINCT category_name FROM categories WHERE status = 1";
$categoryResult = $conn->query($categoryQuery);

// Prepare an array to hold cakes grouped by category
$cakesByCategory = [];
if ($cakesResult->num_rows > 0) {
  while ($cake = $cakesResult->fetch_assoc()) {
    $cakesByCategory[$cake['category_name']][] = $cake;
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cakes</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <style>
    /* Add smooth transitions to images and dropdowns */
    img {
      transition: opacity 0.3s ease-in-out;
    }

    /* Fade-in transition for cake cards */
    .cake-card {
      opacity: 0;
      /* Start hidden */
      transform: translateY(20px);
      /* Start slightly below */
      transition: opacity 0.5s ease-out, transform 0.5s ease-out;
      /* Transition for fade-in and upward movement */
    }

    .cake-card.visible {
      opacity: 1;
      /* Visible state */
      transform: translateY(0);
      /* Move to original position */
    }

    /* Lazy load styling */
    .lazyload {
      opacity: 0;
    }

    .loaded {
      opacity: 1;
    }
  </style>
</head>

<body class="bg-gray-50 text-gray-800">

  <div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-semibold text-center mb-8">Our Cakes</h1>

    <!-- Filter Form -->
    <form method="GET" class="mb-8 flex justify-between items-center">
      <div class="flex space-x-4">
        <!-- Category Filter -->
        <div class="flex items-center space-x-2">
          <label for="category" class="block text-sm font-medium">Category:</label>
          <select name="category" id="category" class="block w-full p-2 border rounded-md" onchange="this.form.submit()">
            <option value="">All Categories</option>
            <?php
            // Populate category options
            if ($categoryResult->num_rows > 0) {
              while ($category = $categoryResult->fetch_assoc()) {
                $selected = ($category['category_name'] == $selectedCategory) ? 'selected' : '';
                echo '<option value="' . htmlspecialchars($category['category_name']) . '" ' . $selected . '>' . htmlspecialchars($category['category_name']) . '</option>';
              }
            }
            ?>
          </select>
        </div>

        <!-- Price Range Filter -->
        <div class="flex items-center space-x-2">
          <label for="price_range" class="block text-sm font-medium">Price Range:</label>
          <select name="price_range" id="price_range" class="block w-full p-2 border rounded-md" onchange="this.form.submit()">
            <option value="">All Prices</option>
            <option value="10-100" <?= $selectedPriceRange == '10-100' ? 'selected' : '' ?>>10-100</option>
            <option value="101-500" <?= $selectedPriceRange == '101-500' ? 'selected' : '' ?>>101-500</option>
            <option value="501-1000" <?= $selectedPriceRange == '501-1000' ? 'selected' : '' ?>>501-1000</option>
          </select>
        </div>
      </div>
    </form>

    <!-- Display Cakes by Category -->
    <div class="space-y-12">
      <?php
      // Check if any cakes were found and display them by category
      if (!empty($cakesByCategory)) {
        foreach ($cakesByCategory as $categoryName => $cakes) {
          echo '<h2 class="text-2xl font-bold mt-8 mb-4 text-left">' . htmlspecialchars($categoryName) . '</h2>';
          echo '<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">';
          foreach ($cakes as $cake) {
            $cakeName = htmlspecialchars($cake['menu_name']);
            $cakeImage = htmlspecialchars($cake['image_path']);
            $cakePrice = htmlspecialchars($cake['price']);
            $cakeId = htmlspecialchars($cake['menu_id']); // Added to get the ID
            echo '
                            <div class="cake-card bg-white rounded-lg shadow-lg p-4">
                                <a href="orderarea.php?cake_id=' . $cakeId . '">
                                    <img src="' . $cakeImage . '" alt="' . $cakeName . '" class="rounded-md w-full h-48 object-cover lazyload" loading="lazy" data-src="' . $cakeImage . '">
                                    <h3 class="text-xl font-semibold mt-4">' . $cakeName . '</h3>
                                    <p class="text-lg mt-2">₱' . $cakePrice . '</p>
                                </a>
                            </div>';
          }
          echo '</div>'; // Close inner grid
        }
      } else {
        echo '<p class="text-center text-red-500">No cakes found.</p>';
      }
      ?>
    </div>
  </div>

  <script>
    // Lazy loading images
    document.addEventListener("DOMContentLoaded", function() {
      const lazyImages = document.querySelectorAll('.lazyload');

      const lazyLoad = (image) => {
        const src = image.getAttribute('data-src');
        if (!src) return;
        image.src = src;
        image.onload = () => {
          image.classList.add('loaded');
        };
        image.classList.remove('lazyload');
      };

      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            lazyLoad(entry.target);
            observer.unobserve(entry.target);
          }
        });
      });

      lazyImages.forEach(image => {
        observer.observe(image);
      });
    });

    // Add scroll animation for cake cards
    document.addEventListener("DOMContentLoaded", function() {
      const cakeCards = document.querySelectorAll('.cake-card');

      const revealCard = (card) => {
        card.classList.add('visible'); // Add the visible class to trigger the transition
      };

      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            revealCard(entry.target);
            observer.unobserve(entry.target); // Stop observing once revealed
          }
        });
      });

      cakeCards.forEach(card => {
        observer.observe(card); // Observe each cake card for scrolling
      });
    });
  </script>
</body>

</html>