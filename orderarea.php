<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Order Cake</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.1/dist/tailwind.min.css" rel="stylesheet" />
  <style>
    .input-container {
      display: flex;
      justify-content: space-between;
      margin-bottom: 1rem;
    }

    .input-box {
      flex: 1;
      margin-right: 1rem;
    }

    .greeting-textarea {
      resize: none;
    }

    .action-button {
      background-color: #ec4899;
      color: white;
      padding: 0.5rem 1rem;
      border: none;
      border-radius: 0.25rem;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .action-button:hover {
      background-color: #db2777;
    }

    .quantity-button {
      background-color: #f3f4f6;
      border: 1px solid #d1d5db;
      padding: 0.5rem;
      cursor: pointer;
    }

    .quantity-input {
      text-align: center;
      border: none;
    }

    .notification {
      display: none;
      color: red;
      margin-top: 1rem;
    }

    .cart-icon {
      position: relative;
      cursor: pointer;
    }

    .cart-section {
      position: absolute;
      top: 50px;
      right: 0;
      background-color: white;
      border: 1px solid #ddd;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      width: 300px;
      padding: 1rem;
      display: none;
    }

    .cart-list {
      max-height: 150px;
      overflow-y: auto;
      margin-bottom: 1rem;
    }

    .item-count {
      position: absolute;
      top: -5px;
      right: -10px;
      background: red;
      color: white;
      border-radius: 50%;
      padding: 2px 5px;
      font-size: 12px;
      cursor: pointer;
    }
  </style>
</head>

<body>
  <div class="container mx-auto p-8">
    <div class="main-section">
      <!-- Cart Icon -->
      <div class="cart-icon mb-4" id="cart-icon">
        <a href="cart.php" id="item-count-link"> <!-- Make item count clickable -->
          <div class="item-count" id="item-count">0</div>
        </a>
        <div class="cart-section" id="cart-section">
          <div class="cart-list" id="cart-list"></div>
          <div>Total Price: $<span id="total-price">0.00</span></div>
        </div>
      </div>


      <!-- Cake Section -->
      <div class="w-2/3">
        <?php
        include 'db/config.php';
        if (isset($_GET['cake_id'])) {
          $cakeId = intval($_GET['cake_id']);
          $cakeQuery = "SELECT * FROM menu WHERE menu_id = $cakeId";
          $cakeResult = $conn->query($cakeQuery);

          if ($cakeResult && $cakeResult->num_rows > 0) {
            $cake = $cakeResult->fetch_assoc();
        ?>
            <div class="flex flex-col md:flex-row max-w-5xl mx-auto bg-white rounded-lg shadow-lg p-6">
              <div class="w-full md:w-1/2">
                <img src="<?= htmlspecialchars($cake['image_path']) ?>" alt="<?= htmlspecialchars($cake['menu_name']) ?>" class="rounded-md w-full h-auto object-cover mb-4" />
              </div>
              <div class="w-full md:w-1/2 md:pl-10 mt-6 md:mt-0">
                <h2 class="text-md text-gray-500">Purple Cake Shop</h2>
                <h1 class="text-4xl font-bold mb-2 text-pink-600"><?= htmlspecialchars($cake['menu_name']) ?></h1>

                <div class="flex items-center mb-2">
                  <span class="star text-2xl">★</span>
                  <span class="star text-2xl">★</span>
                  <span class="star text-2xl">★</span>
                  <span class="star text-2xl">★</span>
                  <span class="star text-2xl">☆</span>
                  <span class="ml-2 text-sm text-gray-600">(4/5)</span>
                </div>

                <p class="text-xl text-gray-800 mb-4">Price: $<?= htmlspecialchars($cake['price']) ?></p>

                <div class="mb-4">
                  <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                  <div class="flex items-center">
                    <button id="remove-btn" class="quantity-button">-</button>
                    <input type="number" id="quantity" value="1" min="1" class="quantity-input mx-2 w-12" readonly />
                    <button id="add-btn" class="quantity-button">+</button>
                  </div>
                </div>

                <div class="input-container">
                  <div class="input-box">
                    <label for="pickup_date" class="block text-sm font-medium text-gray-700">Pickup Date</label>
                    <input type="date" id="pickup_date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm" required />
                  </div>
                  <div class="input-box">
                    <label for="pickup_time" class="block text-sm font-medium text-gray-700">Pickup Time</label>
                    <input type="time" id="pickup_time" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm" required />
                  </div>
                </div>

                <div class="mb-4">
                  <label for="greeting" class="block text-sm font-medium text-gray-700">Personalized Greeting/Dedication (Max 25 words)</label>
                  <textarea id="greeting" rows="4" maxlength="150" class="greeting-textarea mt-1 block w-full border border-gray-300 rounded-md shadow-sm" placeholder="Write your greeting here..."></textarea>
                  <div id="greeting-error" class="notification">Greeting must be 25 words or less.</div>
                </div>

                <button id="add-to-cart-btn" class="action-button">Add to Cart</button>
                <div id="notification" class="notification">Order placed successfully!</div>
              </div>
            </div>
        <?php
          } else {
            echo "<p>Cake not found.</p>";
          }
        } else {
          echo "<p>No cake selected.</p>";
        }
        ?>
      </div>
    </div>
  </div>

  <script>
    const addBtn = document.getElementById('add-btn');
    const removeBtn = document.getElementById('remove-btn');
    const quantityInput = document.getElementById('quantity');
    const greetingInput = document.getElementById('greeting');
    const greetingError = document.getElementById('greeting-error');
    const addToCartBtn = document.getElementById('add-to-cart-btn');
    const itemCount = document.getElementById('item-count');
    const totalPriceDisplay = document.getElementById('total-price');

    const pickupDateInput = document.getElementById('pickup_date');
    const pickupTimeInput = document.getElementById('pickup_time');

    // Function to update cart display
    function updateCartDisplay() {
      let cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];
      let totalPrice = parseFloat(localStorage.getItem('totalPrice')) || 0;

      // Calculate the total number of items based on the add to cart action
      let totalQuantity = cartItems.length;

      itemCount.innerText = totalQuantity; // Update item count in cart icon
      totalPriceDisplay.innerText = totalPrice.toFixed(2); // Update total price display
    }

    // Call the function to update the cart display when the page loads
    updateCartDisplay();

    addBtn.addEventListener('click', () => {
      quantityInput.value = parseInt(quantityInput.value) + 1;
    });

    removeBtn.addEventListener('click', () => {
      if (parseInt(quantityInput.value) > 1) {
        quantityInput.value = parseInt(quantityInput.value) - 1;
      }
    });

    addToCartBtn.addEventListener('click', () => {
      const quantity = parseInt(quantityInput.value);
      const cakeName = "<?= htmlspecialchars($cake['menu_name']) ?>"; // Use PHP to get the cake name
      const cakePrice = parseFloat("<?= htmlspecialchars($cake['price']) ?>"); // Use PHP to get the cake price
      const cakeImage = "<?= htmlspecialchars($cake['image_path']) ?>"; // Use PHP to get the cake image path
      const greeting = greetingInput.value;
      const pickupDate = pickupDateInput.value;
      const pickupTime = pickupTimeInput.value;

      // Validate greeting length
      const wordCount = greeting.split(/\s+/).filter(word => word).length;
      if (wordCount > 25) {
        greetingError.style.display = 'block';
        return; // Stop execution if the greeting is too long
      } else {
        greetingError.style.display = 'none';
      }

      // Validate pickup date and time
      if (!pickupDate) {
        alert("Please select a pickup date.");
        return; // Stop execution if no date is selected
      }

      if (!pickupTime) {
        alert("Please select a pickup time.");
        return; // Stop execution if no time is selected
      }

      // Prepare cart item object
      const cartItem = {
        name: cakeName,
        price: cakePrice,
        quantity: quantity, // Still keep the quantity in the cart item if you want to use it later
        greeting: greeting,
        pickupDate: pickupDate,
        pickupTime: pickupTime,
        image: cakeImage // Add image path to the cart item
      };

      // Get existing items from localStorage
      let cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];
      let totalPrice = parseFloat(localStorage.getItem('totalPrice')) || 0;

      // Add new item to the cart
      cartItems.push(cartItem);

      // Update total price (count each "Add to Cart" as one item)
      totalPrice += cakePrice;
      localStorage.setItem('cartItems', JSON.stringify(cartItems));
      localStorage.setItem('totalPrice', totalPrice);

      // Update cart display
      updateCartDisplay();

      // Reset inputs after adding item to cart
      quantityInput.value = 1;
      greetingInput.value = '';
      pickupDateInput.value = '';
      pickupTimeInput.value = '';
    });

    // Cart icon click event to toggle visibility
    document.getElementById('cart-icon').addEventListener('click', () => {
      const cartSection = document.getElementById('cart-section');
      cartSection.style.display = cartSection.style.display === 'none' ? 'block' : 'none';
    });
  </script>



</body>

</html>