<?php

    $conn = mysqli_connect(
        getenv("DB_HOST"),
        getenv("DB_USERNAME"),
        getenv("DB_PASSWORD"),
        getenv("DB_DATABASE"),
        getenv("DB_PORT") ? : "3306"
    );

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

?>
