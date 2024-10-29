
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
