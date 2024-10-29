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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        img {
            transition: opacity 0.3s ease-in-out;
        }

        .cake-card {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.5s ease-out, transform 0.5s ease-out;
            background-color: white;
            border-radius: 0.5rem;
            padding: 0.5rem;
            height: 220px;
            width: 160px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .cake-card.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .cake-card img {
            max-height: 100px;
            object-fit: cover;
            margin-bottom: 0.5rem;
            border-radius: 0.25rem;
        }

        .cakes-container {
            max-width: 100%;
            padding-right: 2rem;
            margin: 0;
        }

        #cartContainer {
            position: fixed;
            top: 0;
            right: 0;
            width: 80%;
            max-width: 400px;
            height: 100%;
            background-color: #edf2f7;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.3);
            transform: translateX(100%);
            transition: transform 0.3s ease;
            overflow-y: auto;
        }

        #cartContainer.open {
            transform: translateX(0);
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800">

    <div class="max-w-6xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-semibold text-left mb-8">Our Cakes</h1>

        <!-- Filter Form -->
        <form method="GET" class="mb-8 flex justify-between items-center">
            <div class="flex space-x-4">
                <!-- Category Filter -->
                <div class="flex items-center space-x-2">
                    <label for="category" class="block text-sm font-medium">Category:</label>
                    <select name="category" id="category" class="block w-full p-2 border rounded-md" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        <?php
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
        <div class="max-w-3xl pl-0 pr-8 space-y-12">
            <?php
            if (!empty($cakesByCategory)) {
                foreach ($cakesByCategory as $categoryName => $cakes) {
                    echo '<h2 class="text-2xl font-bold mt-8 mb-4 text-left">' . htmlspecialchars($categoryName) . '</h2>';
                    echo '<div class="mx-2">';
                    echo '<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-2 gap-y-6">';
                    foreach ($cakes as $cake) {
                        $cakeName = htmlspecialchars($cake['menu_name']);
                        $cakeImage = htmlspecialchars($cake['image_path']);
                        $cakePrice = htmlspecialchars($cake['price']);
                        $cakeId = htmlspecialchars($cake['menu_id']);
                        echo '
                        <div class="cake-card visible">
                            <a href="javascript:void(0);" onclick="addToCart(\'' . $cakeId . '\', \'' . $cakeName . '\', \'' . $cakePrice . '\', \'' . $cakeImage . '\')">
                                <img src="' . $cakeImage . '" alt="' . $cakeName . '" class="rounded-md w-full h-48 object-cover lazyload" loading="lazy" data-src="' . $cakeImage . '">
                                <h3 class="text-xl font-semibold mt-4">' . $cakeName . '</h3>
                                <p class="text-lg mt-2">₱' . $cakePrice . '</p>
                            </a>
                        </div>';
                    }
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p class="text-center text-red-500">No cakes found.</p>';
            }
            ?>
        </div>

    </div>

    <!-- Cart Icon -->
    <div class="fixed top-4 right-4">
        <button id="cartToggle" class="p-2 rounded-full bg-blue-600 text-white">
            <i class="fas fa-shopping-cart"></i>
        </button>
    </div>

    <!-- Cart Container -->
    <div id="cartContainer" class="fixed right-0 top-16 w-1/3 bg-white shadow-lg p-4 transition-transform transform translate-x-full">
        <h2 class="text-lg font-bold mb-4">Shopping Cart</h2>
        <div id="cartItems" class="mb-4"></div>
        <div id="cartTotal" class="font-bold mb-4">Total: ₱0.00</div>

        <!-- Order Type and Discount Type Selection -->
        <div class="mb-4 flex space-x-4">
            <!-- Order Type Selection -->
            <div class="w-1/2">
                <label for="order_type">Order Type:</label>
                <select name="order_type" id="order_type">
                    <option value="Dine">Dine</option>
                    <option value="Takeout">Takeout</option>
                </select>
            </div>
            <!-- Discount Type Selection -->
            <div class="w-1/2">
                <label for="discount_type">Discount Type:</label>
                <select name="discount_type" id="discount_type">
                    <option value="None">None</option>
                    <option value="PWD">PWD</option>
                    <option value="Senior">Senior Citizen</option>
                </select>
            </div>
        </div>

        <button id="placeOrder" class="w-full bg-blue-600 text-white py-2 rounded-md">Place Order</button>
    </div>

    <script>
        // Add event listener to toggle cart
        document.getElementById('cartToggle').addEventListener('click', function() {
            const cartContainer = document.getElementById('cartContainer');
            cartContainer.classList.toggle('open');
        });

        // Cart array
        let cart = [];

        // Function to add cakes to the cart
        function addToCart(id, name, price, image) {
            const item = {
                id: id,
                name: name,
                price: parseFloat(price),
                image: image
            };
            cart.push(item);
            updateCart();
        }

        // Function to update the cart display
        function updateCart() {
            const cartItemsContainer = document.getElementById('cartItems');
            const cartTotalDisplay = document.getElementById('cartTotal');
            cartItemsContainer.innerHTML = '';
            let total = 0;

            cart.forEach(item => {
                const cartItem = document.createElement('div');
                cartItem.classList.add('flex', 'items-center', 'mb-2');
                cartItem.innerHTML = `
                    <img src="${item.image}" alt="${item.name}" class="w-16 h-16 object-cover mr-2">
                    <div>
                        <p>${item.name} - ₱${item.price.toFixed(2)}</p>
                    </div>
                `;
                cartItemsContainer.appendChild(cartItem);
                total += item.price;
            });

            cartTotalDisplay.innerHTML = `Total: ₱${total.toFixed(2)}`;
        }

        // Place Order Event Listener
        document.getElementById('placeOrder').addEventListener('click', function() {
            const orderType = document.getElementById('order_type').value;
            const discountType = document.getElementById('discount_type').value;
            const orderData = {
                items: cart,
                order_type: orderType,
                discount_type: discountType,
                total_amount: cart.reduce((sum, item) => sum + item.price, 0)
            };

            // Send the order data to the server
            fetch('place_order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(orderData),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Order placed successfully!');
                        cart = []; // Clear the cart after placing the order
                        updateCart();
                    } else {
                        alert('Error placing order: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('There was an error processing your order.');
                });
        });
    </script>
</body>

</html>