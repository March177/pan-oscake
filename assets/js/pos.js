document.addEventListener("DOMContentLoaded", function () {
  const productListsContainer = document.getElementById(
    "product-lists-container"
  );
  const totalItemsElement = document.getElementById("total-items");
  const clearAllButton = document.getElementById("clear-all");
  const paymentMethods = document.querySelectorAll(".paymentmethod");

  // Elements for subtotal, discount, and total
  const subtotalElement = document.getElementById("subtotal-value");
  const discountElement = document.getElementById("discount-value");
  const totalElement = document.getElementById("total-value");
  const checkoutTotalElement = document.querySelector(".btn-totallabel h6"); // New element for checkout total

  function createProductList(categoryId) {
    const ul = document.createElement("ul");
    ul.className = "product-lists";
    ul.dataset.categoryId = categoryId;
    return ul;
  }
  paymentMethods.forEach(function (method) {
    method.addEventListener("click", function () {
      // Remove 'active' class from all payment methods
      paymentMethods.forEach(function (m) {
        m.classList.remove("active");
      });

      // Add 'active' class to the clicked payment method
      this.classList.add("active");
    });
  });

  function addToProductList(item) {
    let ul = [
      ...productListsContainer.querySelectorAll("ul.product-lists"),
    ].find((ul) => ul.querySelector(`li[data-id="${item.id}"]`));

    if (!ul) {
      ul = createProductList(item.categoryId);
      productListsContainer.appendChild(ul);
    }

    const existingItem = ul.querySelector(`li[data-id="${item.id}"]`);
    if (existingItem) {
      const quantityField = existingItem.querySelector(".quantity-field");
      quantityField.value = parseInt(quantityField.value, 10) + 1;
    } else {
      const listItem = document.createElement("li");
      listItem.dataset.id = item.id;
      listItem.innerHTML = `
                <div class="productimg">
                    <div class="productimgs">
                        <img src="${item.image}" alt="img" />
                    </div>
                    <div class="productcontet">
                        <h4>
                            ${item.name}
                            <a href="javascript:void(0);" class="ms-2" data-bs-toggle="modal" data-bs-target="#edit">
                                <img src="assets/img/icons/edit-5.svg" alt="img"/>
                            </a>
                        </h4>
                        <div class="productlinkset">
                            <h5>PT${item.id}</h5>
                        </div>
                        <div class="increment-decrement">
                            <div class="input-groups">
                                <input type="button" value="-" class="button-minus dec button"/>
                                <input type="text" name="child" value="1" class="quantity-field"/>
                                <input type="button" value="+" class="button-plus inc button"/>
                            </div>
                        </div>
                    </div>
                </div>
            `;

      const priceItem = document.createElement("li");
      priceItem.dataset.id = item.id;
      priceItem.className = "price-item"; // Added class to identify price items
      priceItem.textContent = `Price: ${item.price}`;

      const deleteItem = document.createElement("li");
      deleteItem.innerHTML = `
                <a href="javascript:void(0);" class="remove-item" data-id="${item.id}">
                    <img src="assets/img/icons/delete-2.svg" alt="img"/>
                </a>
            `;

      ul.appendChild(listItem);
      ul.appendChild(priceItem);
      ul.appendChild(deleteItem);
    }
    updateTotals(); // Update totals whenever a product is added
  }

  function addProductList(item) {
    console.log("Adding product:", item); // Debug log
    addToProductList(item);
  }

  function updateTotals() {
    let subtotal = 0;
    let totalItems = 0;
    const discountPercentage = 0.1; // 10% discount for PWD/Senior

    [...productListsContainer.querySelectorAll("ul.product-lists")].forEach(
      (ul) => {
        [...ul.querySelectorAll(".price-item")].forEach((priceItem) => {
          const priceText = priceItem.textContent.replace("Price: ", "");
          const price = parseFloat(priceText);
          const quantityField =
            priceItem.previousElementSibling.querySelector(".quantity-field");
          const quantity = parseInt(quantityField.value, 10);
          if (!isNaN(price) && !isNaN(quantity)) {
            subtotal += price * quantity;
          }
        });
        totalItems += [...ul.querySelectorAll(".quantity-field")].reduce(
          (total, field) => total + parseInt(field.value, 10),
          0
        );
      }
    );

    // Update subtotal
    subtotalElement.textContent = `${subtotal.toFixed(2)}`;

    // Calculate and update discount
    const discount = subtotal * discountPercentage;
    discountElement.textContent = `${discount.toFixed(2)}`;

    // Update total
    const total = subtotal - discount;
    totalElement.textContent = `${total.toFixed(2)}`;

    // Update total items count
    totalItemsElement.textContent = totalItems;

    // Update checkout total
    if (checkoutTotalElement) {
      checkoutTotalElement.textContent = `${total.toFixed(2)}₱`;
    }
  }
  $(document).ready(function () {
    $("#checkoutBtn").click(function () {
      // Collect data from your form or cart
      var order_type = $("#orderType").val();
      var discount = $("#discount").val();
      var total_price = $("#total-value").text().replace("₱", "").trim();
      var payment_method = $('input[name="payment_method"]:checked').val();
      var customer = $("#walkin-name").val() || "Walk-in Customer";
      var created_by = "1"; // Replace with actual user ID or name of the person creating the order

      // Collect ordered items
      var orderedItems = [];
      $(".product-lists li[data-id]").each(function () {
        var item = {
          menu_id: $(this).attr("data-id"),
          menu_name: $(this).find("h4").text().trim(),
          menu_image: $(this).find("img").attr("src"),
          category: $(this).closest("ul").attr("data-category-id"),
          quantity: $(this).find(".quantity-field").val(),
          price: parseFloat(
            $(this).next(".price-item").text().replace("Price: ", "").trim()
          ),
        };
        orderedItems.push(item);
      });

      // Data to be sent
      var data = {
        customer: customer,
        order_type: order_type,
        discount: discount,
        payment_method: payment_method,
        total_price: total_price,
        created_by: created_by,
        orderedItems: orderedItems,
      };

      // Send data via AJAX
      $.ajax({
        url: "place_order.php",
        type: "POST",
        data: JSON.stringify(data),
        contentType: "application/json",
        dataType: "json",
        success: function (response) {
          if (response.status === "success") {
            alert(response.message);
            console.log("Order ID: " + response.order_id);
            window.location.href = "pos.php"; // Redirect to POS page after successful insertion
          } else {
            alert("Error: " + response.message);
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          alert(
            "There was an error processing your order: " + jqXHR.responseText
          );
          console.error("AJAX error: " + textStatus + " : " + errorThrown);
        },
      });
    });
  });

  document.getElementById("category").addEventListener("change", function () {
    const selectedCategory = this.value;
    const products = document.querySelectorAll(".productset");

    products.forEach((product) => {
      const productCategory = product.getAttribute("data-category");

      if (selectedCategory === "" || productCategory === selectedCategory) {
        product.style.display = "block";
      } else {
        product.style.display = "none";
      }
    });
  });

  function handleQuantityChange(event) {
    const button = event.target;
    const input = button.parentElement.querySelector(".quantity-field");
    let value = parseInt(input.value, 10);

    if (isNaN(value) || value < 0) {
      value = 0; // Reset to zero if invalid or negative
    }

    if (button.classList.contains("button-minus")) {
      if (value > 0) {
        value--;
      }
    } else if (button.classList.contains("button-plus")) {
      value++;
    }

    input.value = value;
    updateTotals(); // Update totals after changing quantity
  }

  function handleRemove(event) {
    const target = event.target.closest(".remove-item");
    if (target) {
      const ul = target.closest("ul.product-lists");
      if (ul) {
        ul.remove(); // Remove the entire <ul> element
        updateTotals(); // Update totals after removal
      }
    }
  }

  function clearAll() {
    productListsContainer.innerHTML = ""; // Remove all <ul> elements
    updateTotals(); // Update totals after clearing all
  }

  document.querySelectorAll(".productset").forEach((item) => {
    item.addEventListener("click", function () {
      const id = this.getAttribute("data-id");
      const name = this.getAttribute("data-name");
      const price = this.getAttribute("data-price");
      const image = this.getAttribute("data-image");
      const categoryId = this.getAttribute("data-category-id");

      addProductList({ id, name, price, image, categoryId });
    });
  });

  document.addEventListener("click", function (event) {
    if (
      event.target.classList.contains("button-minus") ||
      event.target.classList.contains("button-plus")
    ) {
      handleQuantityChange(event);
    } else if (event.target.closest(".remove-item")) {
      handleRemove(event);
    }
  });

  clearAllButton.addEventListener("click", clearAll); // Attach the clearAll function to the "Clear all" button
});
