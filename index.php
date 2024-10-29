<?php
include 'db/config.php';

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Fetch active Cake Categories from the database
$categoryQuery = "SELECT DISTINCT category_name FROM categories WHERE status = 1"; // status = 1 for active categories
$categoryResult = $conn->query($categoryQuery);

if (!$categoryResult) {
  echo "<p>Error fetching categories. Please try again later.</p>";
  exit; // End execution if the query fails
}

// Prepare an array to hold the latest images per category
$latestImages = [];

// Fetch the latest image for each category from the menu
$cakeCategoryQuery = "SELECT category_name, image_path
FROM menu
WHERE status = 1
AND category_name IS NOT NULL
ORDER BY c_id DESC"; // Assuming 'c_id' is the primary key

$cakeCategoryResult = $conn->query($cakeCategoryQuery);

if ($cakeCategoryResult && $cakeCategoryResult->num_rows > 0) {
  while ($cake = $cakeCategoryResult->fetch_assoc()) {
    $category = htmlspecialchars($cake['category_name']);
    // Store the latest image for the category only if it hasn't been set yet
    if (!isset($latestImages[$category])) {
      $latestImages[$category] = htmlspecialchars($cake['image_path']);
    }
  }
}

// Fetch all cakes from the menu with optional filtering
$filter = isset($_GET['category']) ? $_GET['category'] : 'all';
$priceFilter = isset($_GET['price']) ? $_GET['price'] : '';

// Base query to fetch cakes
$cakesQuery = "SELECT menu_id, menu_name, price, image_path, category_name
FROM menu
WHERE status = 1";

// Apply category filter if selected
if ($filter !== 'all') {
  $cakesQuery .= " AND category_name = '" . $conn->real_escape_string($filter) . "'";
}

// Price filter handling
if ($priceFilter) {
  list($minPrice, $maxPrice) = explode('-', $priceFilter);
  $cakesQuery .= " AND price BETWEEN " . (int)$minPrice . " AND " . (int)$maxPrice;
}

// Fetch cakes based on filters
$cakesResult = $conn->query($cakesQuery);

if (!$cakesResult) {
  echo "<p>Error fetching cakes. Please try again later.</p>";
  exit; // End execution if the query fails
}

// Prepare an array to hold cakes grouped by category
$cakesByCategory = [];
if ($cakesResult->num_rows > 0) {
  while ($cake = $cakesResult->fetch_assoc()) {
    $cakesByCategory[$cake['category_name']][] = $cake;
  }
}

// Fetch categories again for the Cake Menu section filter
$categoryFilterResult = $conn->query($categoryQuery);

// Determine which section to display based on the query parameter
$section = isset($_GET['section']) ? $_GET['section'] : 'home';
$showHome = ($section === 'home');
$showCakeMenu = ($section === 'cakemenu');
$showPayments = ($section === 'payments');
$showAbout = ($section === 'about');
$showContact = ($section === 'contact');




// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cake Shop</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="homepage.css" />
  <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
  <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

  <style>
    /* Navbar */
    .navbar {
      transition: background-color 0.5s ease, box-shadow 0.5s ease, padding 0.3s ease;
      position: fixed;
      width: 100%;
      top: 0;
      z-index: 10;
      padding: 20px 0;
      /* Original padding */
      background-color: rgba(255, 255, 255, 0.5);
    }

    .navbar.transparent {
      background-color: rgba(255, 255, 255, 0.5);
      /* Transparent background */
      box-shadow: none;
      /* No shadow when transparent */
    }

    .navbar.scrolled {
      background-color: rgba(255, 255, 255, 1);
      /* Solid background when scrolled */
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      /* Shadow when scrolled */
      padding: 5px 0;
      /* Smaller padding when scrolled */
    }

    body {
      padding-top: 80px;
      /* Padding to prevent content from being hidden behind navbar */
    }

    .navbar a {
      font-size: 1.25rem;
      margin: 0 20px;
      transition: color 0.3s, font-size 0.3s, border-bottom 0.3s;
      /* Add transition for border */
      position: relative;
      /* Set position for pseudo-element */
    }

    /* Hover effect */
    .navbar a:hover {
      color: #e91e63;
      /* Change color on hover */
    }

    /* Add border bottom on hover */
    .navbar a:hover::after {
      content: "";
      /* Required for pseudo-element */
      position: absolute;
      left: 0;
      right: 0;
      bottom: -5px;
      /* Adjust the position of the line */
      height: 2px;
      /* Height of the line */
      background-color: #e91e63;
      /* Color of the line */
      transition: width 0.3s;
      /* Transition for width effect */
    }


    .dropdown {
      position: relative;
      /* Position relative for the dropdown */
    }

    .dropdown-content {
      display: none;
      /* Initially hidden */
      position: absolute;
      /* Position absolute for dropdown items */
      background-color: white;
      /* Background color */
      min-width: 160px;
      /* Minimum width for dropdown */
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
      /* Shadow for dropdown */
      z-index: 10;
      /* Ensure it appears above other elements */
    }

    .dropdown:hover .dropdown-content {
      display: block;
      /* Show dropdown on hover */
    }

    .dropdown-item {
      padding: 12px 16px;
      /* Increased padding for better spacing */
      color: #4a5568;
      /* Default text color */
      text-decoration: none;
      /* Remove underline */
      display: block;
      /* Ensure block display for full width */
      transition: background-color 0.3s;
      /* Smooth background transition */
    }

    .dropdown-item:hover {
      background-color: #f472b6;
      /* Change background on hover */
      color: white;
      /* Change text color on hover */
    }

    .cake-details-container {
      display: flex;
      justify-content: center;
      align-items: stretch;
      /* Ensure both image and card stretch to the same height */
      max-width: 1200px;
      margin: 0 auto;
      margin-top: 200px;
    }

    .cake-image-large {
      max-width: 500px;
      width: 100%;
      height: 100%;
      /* Ensure the image takes the full height of its container */
      margin-right: 30px;
      border-radius: 12px;
      object-fit: cover;
      /* Ensure the image scales nicely */
    }

    .cake-card {
      background-color: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      padding: 5px;
      flex-grow: 1;
      height: 100%;
      /* Ensure the card takes the full height of its container */
      max-width: 700px;
      width: 100%;
      display: flex;
      flex-direction: column;
    }

    .cake-details-text {
      text-align: left;
      margin-left: 30px;
      max-width: 900px;
      line-height: 1;
    }

    .cake-details-text h4 {
      font-size: 2.5rem;
      margin-bottom: 5px;
      line-height: 1;
    }

    .cake-details-text p {
      font-size: 1rem;
      margin-bottom: 10px;
      line-height: 1;
    }

    .cake-actions {
      display: flex;
      align-items: center;
      margin-top: 10px;
    }

    .quantity-control {
      display: flex;
      align-items: center;
    }

    .quantity-btn {

      color: #333;
      border: none;
      border-radius: 4px;
      padding: 10px;
      cursor: pointer;
      transition: background-color 0.3s;
      margin-right: 10px;
    }



    .quantity-input {
      width: 80px;
      margin-left: 10px;
      border-radius: 4px;
      border: 1px solid #e2e8f0;
      padding: 10px;
      font-size: 1.2rem;
      text-align: center;
    }

    .btn {
      padding: 10px 20px;
      margin-right: 10px;
      margin-bottom: 10px;
      background-color: #e63946;
      color: #ffffff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      transition: background-color 0.3s;
      font-size: 1.2rem;
    }

    .btn:hover {
      background-color: #d62839;
    }

    .pickup-section,
    .message-section {
      margin-top: 20px;
    }

    .pickup-section label,
    .message-section label {
      font-size: 1.2rem;
      margin-bottom: 5px;
      display: block;
    }

    .message-section {
      margin-bottom: 20px;
      /* Add space below the message section */
    }

    .add-to-cart {
      align-self: flex-end;
      /* Align button to the start of the card */
      margin-top: 5px;
    }

    .cart-icon {
      position: absolute;
      /* Change to absolute */
      right: 130px;
      /* Adjust this value to move it to the right */
      top: 81%;
      /* Center vertically */
      transform: translateY(-50%);
      /* Adjust to align vertically */
    }

    .cart-icon img {
      width: 30px;
      /* Match the image size used in the HTML */
      height: 30px;
      /* Match the image size used in the HTML */
    }

    .cart-counter {
      position: absolute;
      top: 5px;
      /* Adjust position */
      right: -21px;
      /* Adjust position */
      background-color: #f472b6;
      color: white;
      border-radius: 50%;
      padding: 2px 6px;
      /* Adjust padding */
      font-size: 0.75rem;
    }

    input[type="date"],
    input[type="time"],
    textarea {
      margin-top: 10px;
      padding: 12px;
      border: 1px solid #e2e8f0;
      border-radius: 4px;
      width: 100%;
      font-size: 1rem;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .cake-details-container {
        flex-direction: column;
        align-items: center;
      }

      .cake-image-large {
        max-width: 100%;
        margin-right: 0;
        margin-bottom: 20px;
        /* Add spacing between image and text */
      }

      .cake-details-text {
        margin-left: 0;
        text-align: center;
      }
    }

    html {
      scroll-behavior: smooth;
    }

    .slider {
      position: relative;
      height: 100%;
      overflow: hidden;
    }

    .slide {
      position: absolute;
      top: -5px;
      left: 0;
      width: 100%;
      height: 100%;
      opacity: 0;
      transition: opacity 0.5s ease-in-out;
    }

    .slide.active {
      opacity: 1;
    }

    /* Ensure all elements use border-box model */
    *,
    *::before,
    *::after {
      box-sizing: border-box;
    }

    .reviews-section {
      padding: 3rem 0;
      /* Padding for the section */
      background-color: #f7fafc;
      /* Light gray background */
    }

    .swiper-container {
      width: 100%;
      /* Ensure the container takes the full width */
      padding-bottom: 2rem;
      /* Extra padding below the slider for pagination dots */
      overflow: hidden;
      /* Prevent overflow */
    }

    .swiper-wrapper {
      display: flex;
      /* Ensure slides are laid out in a row */
    }

    .swiper-slide {
      background: #fff;
      /* White background for the slide */
      padding: 1.5rem;
      /* Padding around the review content */
      border-radius: 1rem;
      /* Rounded corners for the review box */
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      /* Subtle shadow for a card effect */
      width: 100%;
      /* Set width to 100% of the container */
    }

    .swiper-slide img {
      max-width: 100%;
      /* Make sure the image doesn't exceed the container width */
      border-radius: 0.5rem;
      /* Rounded corners for the image */
      margin-top: 1rem;
      /* Spacing above the image */
    }





    .slider {
      position: relative;
      height: 100%;
      overflow: hidden;
    }

    .slide {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      opacity: 0;
      transition: opacity 0.5s ease-in-out;
    }

    .slide.active {
      opacity: 1;
    }


    .dropdown-content {
      display: none;
      /* Hidden by default */
    }

    .dropdown-content a:hover {
      background-color: #d53f8c;
      /* Adjust as needed */
      color: white;
      /* Text color on hover */
    }

    .py-12 {
      margin-top: 133px;
    }


    .mb-6 {

      margin-top: 70px;
    }
  </style>
</head>

<body class="bg-gray-50 text-gray-800">

  <!-- Navbar -->
  <nav class="navbar transparent">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col items-center py-2">
        <a href="?section=home">
          <img src="img/menu/logo.png" alt="Cake Shop Logo" style="width: 300px; height: 120px;">
        </a>
      </div>
      <div class="flex justify-center items-center py-1" style="gap: 5px; flex-wrap: wrap;">
        <a href="?section=home" class="text-gray-800 hover:text-pink-500">Home</a>
        <!-- Dropdown for Cake Menu -->
        <div class="dropdown relative group">
          <a href="?section=cakemenu" class="text-gray-800 hover:text-pink-500">Cake Menu</a>
          <div class="dropdown-content absolute left-0 hidden group-hover:block bg-white border border-gray-300 rounded-lg w-full z-10">
            <a href="?section=cakemenu&category=all" class="dropdown-item block px-4 py-2 hover:bg-pink-500 hover:text-white">All Cakes</a>
            <a href="?section=cakemenu&category=wedding cakes" class="dropdown-item block px-4 py-2 hover:bg-pink-500 hover:text-white">Wedding Cakes</a>
            <a href="?section=cakemenu&category=birthday cakes" class="dropdown-item block px-4 py-2 hover:bg-pink-500 hover:text-white">Birthday Cakes</a>
            <a href="?section=cakemenu&category=cupcakes" class="dropdown-item block px-4 py-2 hover:bg-pink-500 hover:text-white">Cupcakes</a>
          </div>
        </div>

        <a href="?section=payments" class="text-gray-800 hover:text-pink-500">Mode of Payments</a>
        <a href="?section=about" class="text-gray-800 hover:text-pink-500">About Us</a>
        <a href="#contact" class="text-gray-800 hover:text-pink-500">Contact Us</a>

        <div class="cart-icon" style="padding-right: 20px;">
          <a href="cart.php" class="text-gray-800 hover:text-pink-500">
            <img src="img/menu/cart.png" alt="Cart" style="width: 30px; height: 30px;">
            <span class="cart-counter" id="cart-counter">0</span>
          </a>
        </div>
      </div>
    </div>
  </nav>


  <!-- Main Content Below the Navbar -->
  <div style="margin-top: 80px;"> <!-- Adjust this value based on your navbar height -->
    <!-- Your main content goes here -->
  </div>





  <?php if ($showAbout): ?>
    <section class="max-w-4xl mx-auto py-12 bg-gray-50 mt-20">
      <h1 class=" text-4xl font-bold text-pink-600 text-center mb-6">About Us</h1>

      <p class="text-lg text-gray-700">
        <a href="https://Cakeph/.PH" class="text-pink-600  underline" target="_blank">Sweet.PH</a> is a franchisee-operated website by Phelp Cake Shop, a division of Ally Oriental Food Corporation. We independently operate this website solely to simplify online orders and do not represent the franchisor or other franchisees of Sweet Cake Shop.
      </p>

      <h2 class="text-3xl text-pink-600  font-semibold mt-10 mb-4">Our current Sweet Cake Branch:</h2>
      <ul class="list-disc list-inside text-lg text-gray-700">
        <li><a href="#" class="text-pink-600  underline">Sweet Cake Minglanilla</a></li>

      </ul>

      <p class="mt-6 text-lg text-gray-700">For concerns outside our branches, please refer directly to the respective entity.</p>

      <h2 class="text-center text-3xl font-bold text-pink-700  mt-10">Sweet Cake Shop: Where Tradition Meets Taste</h2>
      <p class="text-lg text-gray-700 mt-4">
        Welcome to the Sweet Cake Shop, your premier destination for authentic ube delights. At the heart of our bakery is a passion for preserving the rich, cultural heritage of ube - the Sweet Yam - a staple in Filipino cuisine. Our journey began with a simple mission: to offer an ube experience that is as genuine as it is indulgent.
      </p>

      <!-- First Image Section -->
      <div class="text-center mt-10">
        <img src="img/menu/bday3.png" alt="Grated Sweet Yam" class="rounded-lg mx-auto max-w-md">
        <p class="text-sm text-pink-600  mt-4">Grated Yam, our main ingredient</p>
      </div>

      <!-- New Content and Second Image Section -->
      <h2 class="text-3xl font-bold text-pink-600 mt-10">Authenticity in Every Bite</h2>
      <p class="text-lg text-gray-700 mt-4">
        From the very beginning, we have been committed to using only the finest, natural ube in our recipes. We understand that the essence of a true Sweet Yam Cake lies in its ingredients. That’s why we source our ube directly from local growers, ensuring that each Sweet yam is as fresh and flavorful as possible. This dedication to quality ingredients means every cake we bake is infused with the authentic, rich taste that ube lovers crave.
      </p>

      <!-- Second Image Section -->
      <div class="text-center mt-10">
        <img src="img/menu/bday1.png" alt="Freshly harvested ube" class="rounded-lg mx-auto max-w-md">
        <p class="text-sm text-pink-600  mt-4">Freshly harvested ube</p>
      </div>



      <h2 class="text-3xl font-bold text-pink-700  mt-10">Rooted in Tradition</h2>
      <p class="text-lg text-gray-700 mt-4">
        Our story is deeply entwined with the history of the original ube cakes, which gained popularity during the Spanish colonial period in the Philippines. These traditional cakes were more than just confections; they were a symbol of celebration, a centerpiece at gatherings, and a cherished family treat. At Sweet Cake Shop, we are proud to trace our roots back to these original recipes, blending time-honored methods with contemporary baking techniques to create something truly unique.
      </p>

      <!-- Adding Uploaded Image -->
      <div class="text-center mt-10">
        <img src="img/menu/bday5.png" alt="Sweet Cake Shop Tradition" class="rounded-lg mx-auto max-w-md">
        <p class="text-sm text-pink-500 mt-4">Sweet Cake from our shop</p>
      </div>

    </section>

  <?php endif; ?>








  <?php if ($showPayments): ?>
    <section class="py-12 bg-gray-50 mt-20">
      <div class="max-w-6xl mx-auto px-4 text-center">
        <!-- Section Title -->
        <h2 class="text-4xl font-bold text-pink-600 mb-6">Mode of Payments</h2>

        <!-- Single Combined Payment Logo -->
        <div class="flex justify-center mb-8">
          <img src="img/menu/Mode_of_Payments.webp" alt="Payment Methods" class="h-30 w-100 object-contain">

        </div>

        <!-- Powered by Paymongo -->
        <div class="flex justify-center mb-8">
          <div class="paymongo-box text-center">
            <p class="text-gray-700 font-medium">Powered by</p>
            <img src="img/menu/PayMongo-Badge.svg" alt="Paymongo" class="h-8">
          </div>
        </div>

        <!-- Description Text -->
        <div class="text-gray-600 text-lg">
          <p>We accept Gcash, credit cards, bank transfers, Maya, and GrabPay mobile wallets.</p>
          <p class="mt-4">Select <span class="font-semibold text-pink-600">Secure Payments by PayMongo</span> upon checkout. You will be redirected to PayMongo's secure payment portal.</p>
        </div>
      </div>
    </section>

  <?php endif; ?>





  <!-- Home Section -->
  <?php if ($showHome): ?>
    <!-- Home Section -->
    <section class="relative h-screen mt-20 pt-14"> <!-- Keep padding for layout -->
      <div class="swiper-wrapper">
        <div class="slide bg-cover bg-center h-full active" style="background-image: url('img/menu/358469869_254743360647205_1026146190933563339_n.jpg');"></div>
        <div class="slide bg-cover bg-center h-full" style="background-image: url('img/menu/413853460_770213638480751_4222491395428614504_n.jpg');"></div>
        <div class="slide bg-cover bg-center h-full" style="background-image: url('img/menu/413977274_770213805147401_5341769230694672793_n.jpg');"></div>
      </div>

      <div class="absolute inset-0 flex flex-col justify-center items-center text-white pt-15"> <!-- Keep padding as needed -->

        <a href="?section=cakemenu&category=all" class="mt-6 px-8 py-3 bg-pink-500 text-white rounded-lg shadow hover:bg-pink-600 relative z-10">Order Now</a> <!-- Added z-10 -->
        <h2 class="text-3xl font-bold text-yellow-300">Delicious Cakes for Every Occasion</h2> <!-- Added heading with color -->
        <p class="mt-2 text-lg text-yellow-200">Order your favorite cakes online</p> <!-- Added paragraph with color -->
      </div>
    </section>




    <!-- Purple Cake Provider Section -->
    <section class="bg-gray-50 py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Top Section: Text and Image -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
          <!-- Left Side: Text and Button -->
          <div>
            <h2 class="text-3xl font-bold text-pink-700">The provider of Sweet to our cakes</h2>
            <p class="mt-4 text-lg text-gray-700">
              We prioritize natural flavor and community support by partnering with local farmers, not big corporations. This ensures real sweet in our products and fosters a mutually beneficial relationship with the community.
            </p>
            <a href="#" class="mt-6 inline-block bg-pink-500 text-white py-3 px-10 rounded-lg hover:bg-pink-700">Read More</a>
          </div>

          <!-- Right Side: Image -->
          <div>
            <img src="img/menu/413853460_770213638480751_4222491395428614504_n.jpg" alt="Farmers harvesting ube" class="rounded-lg w-full h-auto"> <!-- Removed shadow-lg -->
          </div>
        </div>

        <div class="mt-12 grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Left: Food Delivery Promo -->
          <div class="flex items-center justify-between">
            <div>
              <img src="img/menu/413977274_770213805147401_5341769230694672793_n.jpg" alt="Farmers harvesting ube" class="rounded-lg w-full h-auto"> <!-- Removed shadow-lg -->
            </div>
          </div>

          <!-- Right Side: Delivery Information -->
          <div class="text-pink-700"> <!-- Removed p-6 -->
            <h3 class="text-2xl font-bold">Rush delivery?</h3>
            <p class="mt-4 text-lg">Search "Sweet Cake" in FoodPanda and GrabFood. App commission and delivery fee applies.</p>
          </div>
        </div>
      </div>
    </section>



    <!-- Categories Section -->
    <section id="categories" class="py-12 bg-gray-100">
      <div class="max-w-6xl mx-auto px-4">
        <h2 class="text-3xl font-semibold text-center">Our Cake Categories</h2>
        <div class="mt-6">
          <?php if ($categoryResult && $categoryResult->num_rows > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
              <?php while ($category = $categoryResult->fetch_assoc()): ?>
                <div class="mt-8">
                  <div class="bg-white rounded-lg shadow p-4 text-center">
                    <a href="?section=cakemenu&category=<?= htmlspecialchars($category['category_name']); ?>">
                      <img src="<?= $latestImages[htmlspecialchars($category['category_name'])] ?? 'img/default.png'; ?>" alt="<?= htmlspecialchars($category['category_name']); ?>" class="rounded-md mb-2 h-32 w-full object-cover">
                      <h3 class="font-semibold text-lg"><?= htmlspecialchars($category['category_name']); ?></h3>
                    </a>
                  </div>
                </div>
              <?php endwhile; ?>
            </div>
          <?php else: ?>
            <p class="text-center">No categories available.</p>
          <?php endif; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>
  <!-- is this correct -->





  <!-- Cake Menu Section -->
  <?php if ($showCakeMenu): ?>
    <section class="py-12 bg-gray-100 pt-100 mt-133"> <!-- Add mt-20 for top margin -->
      <div class="max-w-6xl mx-auto px-4">
        <h2 class="text-3xl font-semibold text-center">Our Cakes</h2>

        <!-- Filters -->
        <form method="GET" action="" id="filterForm">
          <input type="hidden" name="section" value="cakemenu">
          <div class="flex justify-center gap-6 mt-6">
            <div class="flex items-center"> <!-- Align items in the center -->
              <label for="category" class="text-lg font-medium text-gray-700 mr-2">Filter by Category:</label>
              <select name="category" id="category" class="py-2 px-3 border border-gray-300 bg-white rounded-md">
                <option value="all">All</option>
                <?php if ($categoryFilterResult && $categoryFilterResult->num_rows > 0): ?>
                  <?php while ($category = $categoryFilterResult->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($category['category_name']); ?>" <?= $filter === htmlspecialchars($category['category_name']) ? 'selected' : ''; ?>>
                      <?= htmlspecialchars($category['category_name']); ?>
                    </option>
                  <?php endwhile; ?>
                <?php endif; ?>
              </select>
            </div>
            <div class="flex items-center"> <!-- Align items in the center -->
              <label for="price" class="text-lg font-medium text-gray-700 mr-2">Filter by Price:</label>
              <select name="price" id="price" class="py-2 px-3 border border-gray-300 bg-white rounded-md">
                <option value="">All Prices</option>
                <option value="100-200" <?= $priceFilter === '100-200' ? 'selected' : ''; ?>>100 - 200</option>
                <option value="300-500" <?= $priceFilter === '300-500' ? 'selected' : ''; ?>>300 - 500</option>
                <option value="600-1000" <?= $priceFilter === '600-1000' ? 'selected' : ''; ?>>600 - 1000</option>
              </select>
            </div>
          </div>
        </form>


        <!-- Cakes List -->
        <div id="cakes-list" class="grid grid-cols-1 mt-8">
          <?php if (!empty($cakesByCategory)): ?>
            <?php foreach ($cakesByCategory as $category => $cakes): ?>
              <div class="mt-8">
                <h3 class="font-semibold text-lg"><?= htmlspecialchars($category); ?></h3>
                <div class="flex overflow-x-auto gap-4 mt-4">
                  <?php foreach ($cakes as $cake): ?>
                    <a href="#" class="cake-item bg-white rounded-lg shadow p-4 text-center flex-none" style="width: 200px;" data-name="<?= htmlspecialchars($cake['menu_name']); ?>" data-price="<?= number_format($cake['price'], 2); ?>" data-image="<?= htmlspecialchars($cake['image_path']); ?>"> <!-- Added data attributes -->
                      <img src="<?= htmlspecialchars($cake['image_path']); ?>" alt="<?= htmlspecialchars($cake['menu_name']); ?>" class="rounded-md mb-2 h-32 w-full object-cover">
                      <h4 class="font-semibold"><?= htmlspecialchars($cake['menu_name']); ?></h4>
                      <p class="text-gray-700">₱<?= number_format($cake['price'], 2); ?></p>
                    </a>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="text-center">No cakes available for this filter.</p>
          <?php endif; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>





  <!-- Cake Details Section -->
  <div id="cake-details" class="mt-4 hidden">
    <div class="cake-details-container">
      <!-- Cake Image -->
      <img id="cake-image" src="cake-image.jpg" alt="Cake Image" class="cake-image-large" style="display: block;">
      <div class="cake-card">
        <div class="cake-details-text">
          <!-- Cake Name -->
          <h4 id="cake-name" class="font-semibold text-xl text-gray-800">Chocolate Cake</h4>
          <!-- Cake Price -->
          <p class="text-lg font-bold text-gray-700" id="cake-price">₱600.00</p>

          <!-- Quantity Section -->
          <div class="cake-actions">
            <label for="quantity" class="text-sm text-gray-600">Quantity:</label>
            <div class="quantity-control flex items-center">
              <!-- Quantity Controls -->
              <button type="button" id="decrease-quantity" class="quantity-btn text-gray-600">-</button>
              <input type="number" id="quantity" name="quantity" value="1" min="1" class="quantity-input mx-2 w-16 text-center" readonly>
              <button type="button" id="increase-quantity" class="quantity-btn text-gray-600">+</button>
            </div>
          </div>

          <!-- Boxed Pickup Date and Time Section -->
          <div class="pickup-box mt-4">
            <div class="pickup-section">
              <label for="pickup-date">Pickup Date:</label>
              <input type="date" id="pickup-date" name="pickup-date" required>
            </div>
            <div class="pickup-section">
              <label for="pickup-time">Pickup Time:</label>
              <input type="time" id="pickup-time" name="pickup-time" required>
            </div>
          </div>

          <!-- Message Section (Textarea) -->
          <div class="message-section mt-4">
            <label for="cake-message">Message on Cake:</label>
            <textarea id="cake-message" name="cake-message" rows="4" placeholder="Enter your message here" class="w-full p-2 mt-2 border border-gray-300 rounded-lg"></textarea>
          </div>

          <!-- Add to Cart Button -->
          <button class="add-to-cart btn mt-4" id="add-to-cart-btn">Add to Cart</button>
        </div>
      </div>
    </div>
  </div>

  <section class="reviews-section">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h2 class="text-3xl font-bold text-black-700 text-center mb-6">Customer Reviews</h2>

      <div class="swiper-container">
        <div class="swiper-wrapper">
          <!-- Review 1 -->
          <div class="swiper-slide">
            <div class="border border-gray-300 rounded-lg p-4">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <img src="img/customer-avatar.png" alt="Claire" class="h-10 w-10 rounded-full">
                </div>
                <div class="ml-4">
                  <h3 class="text-lg font-semibold">Rod</h3>
                  <p class="text-gray-500">8 months ago</p>
                </div>
              </div>
              <div class="mt-2">
                <p class="text-xl font-bold">Product Review</p>
                <div class="mt-2 text-yellow-500">
                  ★★★★☆
                </div>
                <p class="mt-1">Simply delicious</p>
                <p class="mt-2 text-gray-700">
                  I recently purchased a cake from Sweet Cake Mingla to celebrate my one-year anniversary.
                </p>
                <a href="#" class="text-pink-500 mt-4 inline-block">View product</a>
              </div>
            </div>
          </div>

          <!-- Review 1 -->
          <div class="swiper-slide">
            <div class="border border-gray-300 rounded-lg p-4">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <img src="img/customer-avatar.png" alt="Claire" class="h-10 w-10 rounded-full">
                </div>
                <div class="ml-4">
                  <h3 class="text-lg font-semibold">Claire</h3>
                  <p class="text-gray-500">8 months ago</p>
                </div>
              </div>
              <div class="mt-2">
                <p class="text-xl font-bold">Product Review</p>
                <div class="mt-2 text-yellow-500">
                  ★★★★☆
                </div>
                <p class="mt-1">Simply delicious</p>
                <p class="mt-2 text-gray-700">
                  It was an absolute delight. The cake wasn’t overly sweet, just right. The ube cake boasted remarkable moisture and fluffiness. I highly recommend this cake because it’s simply delicious. It made our celebration even more memorable. Thank you, Sweet Cake Mingla, for creating such a wonderful treat!
                </p>
                <a href="#" class="text-pink-500 mt-4 inline-block">View product</a>
              </div>
            </div>
          </div>



          <!-- Review 1 -->
          <div class="swiper-slide">
            <div class="border border-gray-300 rounded-lg p-4">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <img src="img/customer-avatar.png" alt="Claire" class="h-10 w-10 rounded-full">
                </div>
                <div class="ml-4">
                  <h3 class="text-lg font-semibold">Claire</h3>
                  <p class="text-gray-500">8 months ago</p>
                </div>
              </div>
              <div class="mt-2">
                <p class="text-xl font-bold">Product Review</p>
                <div class="mt-2 text-yellow-500">
                  ★★★★☆
                </div>
                <p class="mt-1">Simply delicious</p>
                <p class="mt-2 text-gray-700">
                  Thank you, Sweet Cake Mingla, for creating such a wonderful treat!
                </p>
                <a href="#" class="text-pink-500 mt-4 inline-block">View product</a>
              </div>
            </div>
          </div>


          <!-- Review 1 -->
          <div class="swiper-slide">
            <div class="border border-gray-300 rounded-lg p-4">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <img src="img/customer-avatar.png" alt="Claire" class="h-10 w-10 rounded-full">
                </div>
                <div class="ml-4">
                  <h3 class="text-lg font-semibold">Claire</h3>
                  <p class="text-gray-500">8 months ago</p>
                </div>
              </div>
              <div class="mt-2">
                <p class="text-xl font-bold">Product Review</p>
                <div class="mt-2 text-yellow-500">
                  ★★★★☆
                </div>
                <p class="mt-1">Simply delicious</p>
                <p class="mt-2 text-gray-700">
                  Thank you, Sweet Cake Mingla, for creating such a wonderful treat!
                </p>
                <a href="#" class="text-pink-500 mt-4 inline-block">View product</a>
              </div>
            </div>
          </div>




          <!-- Review 1 -->
          <div class="swiper-slide">
            <div class="border border-gray-300 rounded-lg p-4">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <img src="img/customer-avatar.png" alt="Claire" class="h-10 w-10 rounded-full">
                </div>
                <div class="ml-4">
                  <h3 class="text-lg font-semibold">Claire</h3>
                  <p class="text-gray-500">8 months ago</p>
                </div>
              </div>
              <div class="mt-2">
                <p class="text-xl font-bold">Product Review</p>
                <div class="mt-2 text-yellow-500">
                  ★★★★☆
                </div>
                <p class="mt-1">Simply delicious</p>
                <p class="mt-2 text-gray-700">
                  Thank you, Sweet Cake Mingla, for creating such a wonderful treat!
                </p>
                <a href="#" class="text-pink-500 mt-4 inline-block">View product</a>
              </div>
            </div>
          </div>




          <!-- Review 1 -->
          <div class="swiper-slide">
            <div class="border border-gray-300 rounded-lg p-4">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <img src="img/customer-avatar.png" alt="Claire" class="h-10 w-10 rounded-full">
                </div>
                <div class="ml-4">
                  <h3 class="text-lg font-semibold">Claire</h3>
                  <p class="text-gray-500">8 months ago</p>
                </div>
              </div>
              <div class="mt-2">
                <p class="text-xl font-bold">Product Review</p>
                <div class="mt-2 text-yellow-500">
                  ★★★★☆
                </div>
                <p class="mt-1">Simply delicious</p>
                <p class="mt-2 text-gray-700">
                  Thank you, Sweet Cake Mingla, for creating such a wonderful treat!
                </p>
                <a href="#" class="text-pink-500 mt-4 inline-block">View product</a>
              </div>
            </div>
          </div>

        </div>
        <div class="swiper-pagination"></div>
      </div>
    </div>
  </section>


  <!-- Footer Section -->
  <footer id="contact" class="bg-pink-600 py-10 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col lg:flex-row justify-between items-center lg:items-start space-y-6 lg:space-y-0 lg:space-x-10">

        <!-- Call to Action Section -->
        <div class="w-full lg:w-1/2 text-center lg:text-left">
          <h2 class="text-2xl font-bold">Launch your own Sweetcakes Branch!</h2>
          <p class="mt-2 text-lg">
            We're on the hunt for passionate entrepreneurs, creative thinkers, and enthusiastic partners to help us spread the joy of Sweetcakes nationwide!
          </p>
        </div>

        <!-- Subscribe Section -->
        <div class="w-full lg:w-1/2 flex flex-col lg:flex-row justify-center lg:justify-end items-center">

          <form class="flex w-full lg:w-auto">
            <input type="email" placeholder="Your Email" class="p-3 w-full lg:w-auto rounded-l-md border-none focus:ring-2 focus:ring-pink-500">
            <button type="submit" class="bg-gray-900 px-5 py-3 text-white rounded-r-md hover:bg-gray-700">OK</button>
          </form>
        </div>
      </div>

      <!-- Footer Bottom Links and Copyright -->
      <div class="flex flex-col lg:flex-row justify-between items-center border-t border-red-500 pt-6 mt-6 text-sm">

        <!-- Footer Links -->
        <div class="flex space-x-8 mb-4 lg:mb-0 mt-50"> <!-- Added mt-4 for top margin -->
          <a href="?section=payments" class="text-gray-800 hover:text-pink-500">Mode of Payments</a>
          <a href="?section=about" class="text-gray-800 hover:text-pink-500">About Us</a>
          <a href="#contact" class="text-gray-800 hover:text-pink-500">Contact Us</a>
        </div>


        <!-- Copyright and Social Links -->
        <div class="flex items-center space-x-6">
          <span>&copy; Sweetcakes All Rights Reserved 2020</span>
          <div class="flex space-x-4">
            <a href="#" class="hover:underline">
              <img src="img/menu/facebook.png" alt="Facebook" class="h-6">
            </a>
            <a href="#" class="hover:underline">
              <img src="img/menu/instagram.png" alt="Instagram" class="h-6">
            </a>
          </div>
        </div>
      </div>
    </div>
  </footer>

  <script>
    // Initialize Swiper
    const swiper = new Swiper('.swiper-container', {
      slidesPerView: 1, // Number of reviews visible at once for mobile
      spaceBetween: 30, // Space between each review
      loop: true, // Enable looping
      autoplay: {
        delay: 2000, // Slide change delay in milliseconds (faster)
        disableOnInteraction: false, // Continue autoplay after interaction
      },
      pagination: false, // Disable pagination dots
      breakpoints: {
        640: {
          slidesPerView: 1,
          spaceBetween: 20,
        },
        1024: {
          slidesPerView: 2,
          spaceBetween: 30,
        },
        1440: {
          slidesPerView: 3,
          spaceBetween: 40,
        },
      },
    });

    // Optional: Manual navigation by clicking on slides
    const slides = document.querySelectorAll('.swiper-slide');
    slides.forEach((slide, index) => {
      slide.addEventListener('click', () => {
        swiper.slideTo(index); // Jump to the clicked slide
      });
    });

    // Pause and resume autoplay on mouse events
    const swiperContainer = document.querySelector('.swiper-container');

    swiperContainer.addEventListener('mouseenter', () => {
      swiper.autoplay.stop(); // Stop autoplay
    });

    swiperContainer.addEventListener('mouseleave', () => {
      swiper.autoplay.start(); // Resume autoplay
    });




    document.addEventListener('DOMContentLoaded', function() {
      var navbar = document.querySelector('.navbar');

      // Initially set navbar to transparent
      navbar.classList.add('transparent');

      // Change navbar style on scroll
      window.addEventListener('scroll', function() {
        if (window.scrollY > 50) { // When scrolled more than 50 pixels
          navbar.classList.add('scrolled');
          navbar.classList.remove('transparent');
        } else {
          navbar.classList.remove('scrolled');
          navbar.classList.add('transparent');
        }
      });

      // Cake filtering script
      const filterForm = document.getElementById('filterForm');
      const categorySelect = document.getElementById('category');
      const priceSelect = document.getElementById('price');

      function fetchCakes() {
        const category = categorySelect.value;
        const price = priceSelect.value;
        const section = "cakemenu";

        const url = new URL(window.location.href);
        url.searchParams.set('section', section);
        url.searchParams.set('category', category);
        url.searchParams.set('price', price);

        fetch(url)
          .then(response => response.text())
          .then(data => {
            // Update the cakes list with the response data
            document.getElementById('cakes-list').innerHTML = new DOMParser().parseFromString(data, 'text/html').getElementById('cakes-list').innerHTML;
          })
          .catch(error => console.error('Error fetching cakes:', error));
      }

      categorySelect.addEventListener('change', fetchCakes);
      priceSelect.addEventListener('change', fetchCakes);

      // Update cart counter from localStorage
      updateCartCounter();

      // Add to cart button event listener
      document.getElementById('add-to-cart-btn').addEventListener('click', function(event) {
        const pickupDate = document.getElementById('pickup-date').value;
        const pickupTime = document.getElementById('pickup-time').value;

        if (!pickupDate || !pickupTime) {
          event.preventDefault(); // Prevent form submission
          alert('Please select both pickup date and time.'); // Alert the user
        } else {
          addToCart(); // Call addToCart function if validations pass
        }
      });

      // Increase quantity button event listener
      document.getElementById('increase-quantity').addEventListener('click', function() {
        const quantityInput = document.getElementById('quantity');
        quantityInput.value = parseInt(quantityInput.value) + 1;
      });

      // Decrease quantity button event listener
      document.getElementById('decrease-quantity').addEventListener('click', function() {
        const quantityInput = document.getElementById('quantity');
        if (quantityInput.value > 1) {
          quantityInput.value = parseInt(quantityInput.value) - 1;
        }
      });

      // Cake item click event listener
      const cakeItems = document.querySelectorAll('.cake-item');
      const cakesList = document.getElementById('cakes-list');
      const cakeDetails = document.getElementById('cake-details');
      const cakeImage = document.getElementById('cake-image');
      const cakeName = document.getElementById('cake-name');
      const cakePrice = document.getElementById('cake-price');

      cakeItems.forEach(item => {
        item.addEventListener('click', function(event) {
          event.preventDefault(); // Prevent the default anchor behavior

          // Get cake details from data attributes
          const image = item.getAttribute('data-image');
          const name = item.getAttribute('data-name');
          const price = item.getAttribute('data-price');

          // Update the cake details section
          cakeImage.src = image; // Set the image source
          cakeImage.alt = name; // Set the image alt text
          cakeName.textContent = name; // Set the cake name
          cakePrice.textContent = `₱${price}`; // Set the cake price

          // Hide the cakes list and show the cake details
          cakesList.style.display = 'none'; // Hide the cakes list
          document.querySelector('section.py-12').style.display = 'none'; // Hide the cake menu section
          cakeImage.style.display = 'block'; // Show the image
          cakeDetails.classList.remove('hidden'); // Show the details section
          cakeDetails.scrollIntoView({
            behavior: 'smooth'
          }); // Scroll to the details section
        });
      });
    });

    const cartCounter = document.getElementById('cart-counter');

    // Function to add item to the cart and manage Local Storage
    function addToCart() {
      const cakeName = document.getElementById('cake-name').textContent;
      const cakePrice = parseFloat(document.getElementById('cake-price').textContent.replace('₱', ''));
      const quantity = parseInt(document.getElementById('quantity').value);
      const pickupDate = document.getElementById('pickup-date').value;
      const pickupTime = document.getElementById('pickup-time').value;
      const cakeMessage = document.getElementById('cake-message').value;
      const cakeImageSrc = document.getElementById('cake-image').src; // Get the image source

      // Validation checks
      if (quantity < 1) {
        alert("Please select a valid quantity.");
        return;
      }

      if (!pickupDate) {
        alert("Please select a pickup date.");
        return;
      }

      if (!pickupTime) {
        alert("Please select a pickup time.");
        return;
      }

      // Prepare cart item object
      const cartItem = {
        name: cakeName,
        price: cakePrice,
        quantity: quantity,
        pickupDate: pickupDate,
        pickupTime: pickupTime,
        message: cakeMessage,
        image: cakeImageSrc // Store the image source
      };

      // Get existing cart from Local Storage
      let cart = JSON.parse(localStorage.getItem('cartItems')) || [];

      // Check if the item is already in the cart
      const existingItemIndex = cart.findIndex(item =>
        item.name === cartItem.name &&
        item.pickupDate === cartItem.pickupDate &&
        item.pickupTime === cartItem.pickupTime
      );

      if (existingItemIndex !== -1) {
        // Item already exists in the cart, replace it
        cart[existingItemIndex] = cartItem;
        alert(`${cakeName} has been updated in your cart!`);
      } else {
        // Add new item to the cart
        cart.push(cartItem);
        alert(`${cakeName} has been added to your cart!`);
      }

      // Update Local Storage
      localStorage.setItem('cartItems', JSON.stringify(cart));
      updateCartCounter();
    }

    // Function to update the cart counter
    function updateCartCounter() {
      const cart = JSON.parse(localStorage.getItem('cartItems')) || [];
      cartCounter.textContent = cart.length;
    }

    // Function to checkout and clear the cart
    function checkout() {
      // Clear the cart from Local Storage
      localStorage.removeItem('cartItems');
      updateCartCounter();
      alert("Checkout successful! Your cart has been cleared.");
    }

    // Update cart counter on page load
    document.addEventListener('DOMContentLoaded', updateCartCounter);
    window.addEventListener('scroll', function() {
      const navbar = document.querySelector('.navbar');
      const logo = document.querySelector('.navbar img'); // Select the logo image
      if (window.scrollY > 50) { // Change size when scrolled down 50px
        navbar.classList.add('scrolled');
        navbar.style.padding = '5px 0'; // Smaller padding when scrolled
        logo.style.width = '150px'; // Smaller logo size when scrolled
        logo.style.height = '60px'; // Smaller logo height when scrolled
      } else {
        navbar.classList.remove('scrolled');
        navbar.style.padding = '20px 0'; // Original padding
        logo.style.width = '300px'; // Original logo size
        logo.style.height = '120px'; // Original logo height
      }
    });
  </script>


</body>

</html>
