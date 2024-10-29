<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.1/dist/tailwind.min.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<body class="bg-gray-50">
    <div class="max-w-5xl mx-auto p-8">
        <!-- Cart Header -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-pink-700">Your Cart</h1>
            <a href="homepage.php" class="text-pink-700 underline">Continue Shopping</a>
        </div>

        <!-- Labels for Product, Quantity, and Total -->
        <div class="flex justify-between mb-4">
            <div>
                <p class="text-sm font-semibold text-gray-600">Product</p>
                <hr class="w-full border-gray-300 mb-2">
            </div>
            <div class="flex flex-col items-center">
                <p class="text-sm font-semibold text-gray-600">Quantity</p>
            </div>
            <div class="flex flex-col items-center">
                <p class="text-sm font-semibold text-gray-600">Total</p>
            </div>
        </div>

        <!-- Cart Items -->
        <div id="cart-items" class="space-y-6">
            <!-- Cart items will be populated here dynamically -->
        </div>

        <div class="flex justify-end">
            <!-- This flex container will align the card to the right -->
            <div style="width: 335px;">
                <div class="flex items-center justify-end">
                    <!-- Keep this justify-end to align contents to the right -->
                    <div class="flex flex-col text-right">
                        <!-- Add text-right to align text right -->
                        <h2 class="text-xl font-semibold">Estimated total</h2>
                        <p class="text-sm text-gray-500">Taxes included</p>
                    </div>
                    <h2 id="total-price" class="text-2xl font-bold text-pink-700 ml-4">₱500.00</h2>
                </div>
                <div class="flex justify-end">
                    <!-- Align the button to the right -->
                    <button id="checkout-button" class="mt-4 w-1/2 py-2 bg-pink-200 text-pink-700 font-semibold rounded-full hover:bg-pink-300">
                        Check out
                    </button>
                </div>
            </div>
        </div>

        <script>
            // Load cart items from localStorage
            const cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];
            const totalPriceElement = document.getElementById('total-price');
            const cartItemsContainer = document.getElementById('cart-items');

            // Clear cart items container to avoid duplication
            cartItemsContainer.innerHTML = '';

            // Populate cart items
            const populateCartItems = () => {
                cartItemsContainer.innerHTML = ''; // Clear current items
                cartItems.forEach((item, index) => {
                    const itemTotalPrice = (item.price * item.quantity).toFixed(2);

                    const itemElement = document.createElement('div');
                    itemElement.innerHTML = `
<div class="flex justify-between items-center border-b pb-4" data-id="${index}">
    <div class="flex items-center">
        <img src="${item.image}" alt="${item.name}" class="h-20 w-20 object-cover mr-4 rounded-lg" />
        <div>
            <h2 class="text-lg font-semibold text-pink-700">${item.name}</h2>
            <p class="text-gray-600">₱${item.price.toFixed(2)}</p>
            <p class="text-gray-500 text-sm">Pickup: ${item.pickupDate} at ${item.pickupTime}</p>
        </div>
    </div>
    <div class="flex items-center">
        <div class="flex items-center border rounded-full" style="margin-right: 280px;">
    <button class="px-2 py-1 text-pink-700 border-r decrease-quantity" data-id="${index}">−</button>
    <span class="px-4 quantity">${item.quantity}</span>
    <button class="px-2 py-1 text-pink-700 border-l increase-quantity" data-id="${index}">+</button>
</div>

        <div class="ml-8 text-lg font-semibold text-pink-700 item-total-price" data-id="${index}">₱${itemTotalPrice}</div> <!-- Added margin-left for the price -->
        <button class="ml-4 text-gray-400 hover:text-red-500 delete-item flex items-center" data-id="${index}">
            <i class="fas fa-times mr-1"></i>
            <span class="text-sm">Delete</span>
        </button>
    </div>
</div>
`;

                    cartItemsContainer.appendChild(itemElement);
                });

                // Recalculate total price
                calculateTotalPrice();
            };

            const calculateTotalPrice = () => {
                const totalPrice = cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0).toFixed(2);
                totalPriceElement.textContent = `₱${totalPrice}`;
            };

            // Function to handle item deletion
            const deleteItem = (index) => {
                cartItems.splice(index, 1); // Remove item from the array
                localStorage.setItem('cartItems', JSON.stringify(cartItems)); // Update localStorage
                populateCartItems(); // Refresh the cart display
            };

            // Initial population of cart items
            populateCartItems();

            // Event delegation for delete buttons
            cartItemsContainer.addEventListener('click', (event) => {
                if (event.target.closest('.delete-item')) {
                    const itemId = event.target.closest('.delete-item').getAttribute('data-id');
                    deleteItem(itemId);
                }
            });

            // Handle quantity change
            const updateItemTotalPrice = (itemId) => {
                const itemTotalPriceElement = document.querySelector(`.item-total-price[data-id="${itemId}"]`);
                const item = cartItems[itemId];
                const itemTotalPrice = (item.price * item.quantity).toFixed(2);
                itemTotalPriceElement.textContent = `₱${itemTotalPrice}`;
            };

            // Increase quantity functionality
            const increaseQuantity = (itemId) => {
                cartItems[itemId].quantity += 1;
                localStorage.setItem('cartItems', JSON.stringify(cartItems));
                populateCartItems();
            };

            // Decrease quantity functionality
            const decreaseQuantity = (itemId) => {
                if (cartItems[itemId].quantity > 1) {
                    cartItems[itemId].quantity -= 1;
                    localStorage.setItem('cartItems', JSON.stringify(cartItems));
                    populateCartItems();
                } else {
                    // If quantity is 1, remove the item
                    deleteItem(itemId);
                }
            };

            // Event delegation for increase/decrease buttons
            cartItemsContainer.addEventListener('click', (event) => {
                const button = event.target.closest('.increase-quantity') || event.target.closest('.decrease-quantity');
                if (button) {
                    const itemId = button.getAttribute('data-id');
                    if (button.classList.contains('increase-quantity')) {
                        increaseQuantity(itemId);
                    } else if (button.classList.contains('decrease-quantity')) {
                        decreaseQuantity(itemId);
                    }
                }
            });

            // Function to add an item to the cart
            function addToCart(newItem) {
                const existingItemIndex = cartItems.findIndex(item => item.id === newItem.id); // Check if item already exists
                if (existingItemIndex > -1) {
                    // Item exists, increase quantity
                    cartItems[existingItemIndex].quantity += newItem.quantity; // Assuming newItem has quantity
                } else {
                    // Item does not exist, add new item
                    cartItems.push(newItem);
                }
                localStorage.setItem('cartItems', JSON.stringify(cartItems));
                populateCartItems(); // Refresh the cart display
            }

            // Update the checkout button click event
            const checkoutButton = document.getElementById('checkout-button');
            checkoutButton.addEventListener('click', () => {
                const cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];

                // Check if there are items in the cart
                if (cartItems.length > 0) {
                    // Convert cart items to a JSON string and encode it
                    const itemsJson = encodeURIComponent(JSON.stringify(cartItems));

                    // Redirect to payment.php with cart details as a query parameter
                    window.location.href = `payment.php?items=${itemsJson}`;
                }
            });
        </script>
    </div>
</body>

</html>