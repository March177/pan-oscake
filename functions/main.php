<php?

    function get_users($filter)
    {
    global $conn; // Ensure you're using the global connection
    $whereClause='' ;

    // Add filtering conditions
    if (!empty($filter['status'])) {
    $status=mysqli_real_escape_string($conn, $filter['status']);
    $whereClause=" WHERE status = '$status'" ;
    }

    $query="SELECT user_id, username, email, age, image, gender, status, role FROM users" . $whereClause;
    $result=mysqli_query($conn, $query);

    if (!$result) {
    die('Query failed: ' . mysqli_error($conn));
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
} 

?>