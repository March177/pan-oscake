<?php
session_start();
include 'db/config.php'; // Include database configuration
if (!isset($_SESSION['admin_id'])) {
    header("Location: loginadmin.php");
    exit;
}

$admin_id = $_SESSION['admin_id'];

// Fetch all active sessions for the admin
$query = "SELECT * FROM device_sessions WHERE admin_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Device Management</title>
</head>
<body>
    <h1>Device Management</h1>
    <table border="1">
        <tr>
            <th>Device ID</th>
            <th>IP Address</th>
            <th>User Agent</th>
            <th>Last Active</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['device_id']; ?></td>
                <td><?php echo $row['ip_address']; ?></td>
                <td><?php echo $row['user_agent']; ?></td>
                <td><?php echo $row['last_active']; ?></td>
                <td>
                    <!-- Provide a button to log out from this device -->
                    <form action="logout_device.php" method="post">
                        <input type="hidden" name="device_id" value="<?php echo $row['device_id']; ?>">
                        <button type="submit">Logout</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
