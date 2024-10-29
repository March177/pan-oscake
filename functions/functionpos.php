<?php
function getAllCategories($conn) {
    // SQL query to select distinct categories
    $query = "SELECT DISTINCT category_name FROM menu WHERE category_name IS NOT NULL AND category_name != '' ORDER BY category_name ASC";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die('Error: ' . mysqli_error($conn)); // Output error if query fails
    }

    // Array to hold the categories
    $categories = [];

    // Fetching categories from the result set
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row['category_name'];
    }

    return $categories;
}