<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Area</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.1/dist/tailwind.min.css" rel="stylesheet">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            height: 250px;
            width: 100%;
            border-radius: 8px;
        }

        .flex-container {
            display: flex;
            justify-content: space-between;
        }

        .order-summary {
            flex-basis: 40%;
            /* Adjust the width of the order summary */
            margin-left: 20px;
            /* Space between form and order summary */
        }
    </style>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        let map;
        let marker;
        let subtotal = 0;
        const minglanillaCoords = [10.2458, 123.7963]; // Coordinates for Minglanilla, Cebu

        document.addEventListener("DOMContentLoaded", function() {
            const params = getQueryParams();
            const items = JSON.parse(decodeURIComponent(params.items));
            let pickupTime = ''; // Default to empty string if undefined
            let pickupDate = ''; // Default to empty string if undefined

            if (Array.isArray(items) && items.length > 0) {
                const delivery = "Free";
                const orderSummary = document.getElementById("order-summary");
                orderSummary.innerHTML = '';

                items.forEach(item => {
                    const price = parseFloat(item.price);
                    const quantity = parseInt(item.quantity);

                    if (!isNaN(price) && price >= 0 && !isNaN(quantity) && quantity > 0) {
                        const itemSubtotal = price * quantity;
                        subtotal += itemSubtotal;

                        // Get pickup details from item
                        pickupTime = item.pickupTime || ''; // Extract pickupTime from item
                        pickupDate = item.pickupDate || ''; // Extract pickupDate from item

                        const itemElement = document.createElement("div");
                        itemElement.classList.add("flex", "justify-between", "items-center", "mb-2");
                        itemElement.innerHTML = `
                    <div class="flex items-center">
                        <img src="${item.image}" alt="${item.name}" class="w-12 h-12 object-cover mr-2 rounded">
                        <span>${item.name} (${quantity})</span>
                    </div>
                    <span>₱${itemSubtotal.toFixed(2)}</span>
                `;
                        orderSummary.appendChild(itemElement);
                    }
                });

                // Display pickup time and date below order summary
                const pickupInfoElement = document.createElement("div");
                pickupInfoElement.classList.add("mt-2", "text-sm", "text-gray-600");
                pickupInfoElement.innerHTML = `
            <p>Pickup Time: ${pickupTime || 'N/A'}</p>
            <p>Pickup Date: ${pickupDate || 'N/A'}</p>
        `;
                orderSummary.appendChild(pickupInfoElement);

                document.getElementById("item-delivery").textContent = delivery;
                document.getElementById("item-total").textContent = `₱${subtotal.toFixed(2)}`;
                document.getElementById("total-amount-input").value = subtotal * 100;
                document.getElementById("product-name").value = items.map(item => item.name).join(', ');

                // Populate hidden fields for pickup details
                document.getElementById("pickup-time").value = pickupTime;
                document.getElementById("pickup-date").value = pickupDate;

                // Add items to the hidden input field
                document.getElementById("items-input").value = JSON.stringify(items); // Ensure this input exists
            } else {
                alert("Invalid order details. Please check your selection.");
            }

            initializeMap();
        });

        function updatePaymentOption() {
            const paymentOption = document.querySelector('input[name="payment_option"]:checked').value;
            const totalAmount = paymentOption === "half" ? subtotal / 2 : subtotal;
            document.getElementById("item-total").textContent = `₱${totalAmount.toFixed(2)}`;
            document.getElementById("total-amount-input").value = totalAmount * 100;
        }

        function getQueryParams() {
            const params = {};
            const queryString = window.location.search.substring(1);
            const queryArray = queryString.split('&');
            queryArray.forEach(param => {
                const [key, value] = param.split('=');
                params[decodeURIComponent(key)] = decodeURIComponent(value.replace(/\+/g, ' '));
            });
            return params;
        }

        function initializeMap() {
            map = L.map('map').setView(minglanillaCoords, 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            marker = L.marker(minglanillaCoords, {
                    draggable: true
                }).addTo(map)
                .bindPopup('Drag me to your location!')
                .openPopup();

            marker.on('dragend', function(event) {
                const position = marker.getLatLng();
                getAddress(position.lat, position.lng);
                document.getElementById("latitude").value = position.lat;
                document.getElementById("longitude").value = position.lng;
            });
        }

        function getLocation() {
            // Directly set map to Minglanilla, Cebu coordinates
            marker.setLatLng(minglanillaCoords).update();
            map.setView(minglanillaCoords, 13);
            document.getElementById("latitude").value = minglanillaCoords[0];
            document.getElementById("longitude").value = minglanillaCoords[1];
            getAddress(minglanillaCoords[0], minglanillaCoords[1]);
        }

        function showError(error) {
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    alert("User denied the request for Geolocation.");
                    break;
                case error.POSITION_UNAVAILABLE:
                    alert("Location information is unavailable.");
                    break;
                case error.TIMEOUT:
                    alert("The request to get user location timed out.");
                    break;
                case error.UNKNOWN_ERROR:
                    alert("An unknown error occurred.");
                    break;
            }
        }

        function getAddress(lat, lng) {
            fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`)
                .then(response => response.json())
                .then(data => {
                    // Populate the address and city
                    document.querySelector('input[name="address"]').value = data.display_name; // Full address
                    document.querySelector('input[name="city"]').value = "Cebu"; // Set city as "Cebu"

                    const addressComponents = data.address;
                    document.querySelector('input[name="postal_code"]').value = addressComponents.postcode || '';
                })
                .catch(error => {
                    console.error("Error fetching the address:", error);
                    // Handle error if needed
                });
        }
    </script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto p-5 flex flex-col md:flex-row gap-5">
        <div class="bg-white p-5 rounded-md shadow-md flex-grow">
            <h1 class="text-3xl font-bold mb-5">Payment Area</h1>

            <form action="process_payment.php" method="POST">
                <input type="hidden" name="total_amount" id="total-amount-input">
                <input type="hidden" name="product_name" id="product-name">
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">
                <input type="hidden" name="pickup_time" id="pickup-time">
                <input type="hidden" name="pickup_date" id="pickup-date">
                <input type="hidden" name="items" id="items-input"> <!-- Hidden input for items -->


                <div class="mb-5">
                    <h2 class="text-xl font-semibold">Info</h2>
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <input type="text" name="first_name" placeholder="First name" class="p-2 border border-gray-300 rounded-md" required>
                        <input type="text" name="last_name" placeholder="Last name" class="p-2 border border-gray-300 rounded-md" required>
                        <input type="text" name="address" placeholder="Address" class="col-span-2 p-2 border border-gray-300 rounded-md" required>
                        <input type="text" name="postal_code" placeholder="Postal code" class="p-2 border border-gray-300 rounded-md" required>
                        <input type="text" name="city" placeholder="City" class="p-2 border border-gray-300 rounded-md" required>
                    </div>
                </div>

                <div class="mb-5">
                    <h2 class="text-xl font-semibold">Pickup Location</h2>
                    <div id="map" class="rounded-md"></div>
                    <button type="button" onclick="getLocation()" class="mt-2 p-2 bg-blue-500 text-white rounded-md">Get My Location</button>
                </div>





                <button type="submit" class="p-3 bg-green-500 text-white rounded-md">Proceed to Payment</button>
            </form>
        </div>


        <div class="bg-white p-5 rounded-md shadow-md order-summary">
            <h2 class="text-xl font-semibold">Order Details</h2>


            <div class="mb-5">
                <h2 class="text-xl font-semibold">Order Summary</h2>
                <div id="order-summary" class="mt-3"></div>
            </div>
            <div class="flex justify-between mt-4">
                <span>Delivery:</span>
                <span id="item-delivery">Free</span>
            </div>
            <div class="flex justify-between mt-2">
                <span>Subtotal:</span>
                <span id="item-subtotal">₱0.00</span> <!-- This line will now show the subtotal -->
            </div>

            <div class="flex mb-5">
                <label class="mr-4">Payment Option:</label>
                <label><input type="radio" name="payment_option" value="full" checked onchange="updatePaymentOption()"> Full</label>
                <label><input type="radio" name="payment_option" value="half" onchange="updatePaymentOption()"> Half</label>
            </div>

            <div class="flex justify-between items-center mb-5">
                <span>Total:</span>
                <span id="item-total">₱0.00</span>
            </div>
        </div>
    </div>
</body>

</html>