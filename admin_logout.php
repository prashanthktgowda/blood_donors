<?php
session_start();
include 'db.php'; // Include your database connection file

if (isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];
    
    // Get the last activity entry for the admin
    $logout_time = date('Y-m-d H:i:s');
    $updateLogoutQuery = "UPDATE admin_activity SET logout_time = ? WHERE admin_id = ? AND logout_time IS NULL ORDER BY login_time DESC LIMIT 1";
    $stmt = $conn->prepare($updateLogoutQuery);
    $stmt->bind_param("si", $logout_time, $admin_id);
    $stmt->execute();

    session_destroy(); // Destroy the session
    header("Location: admin_login.php"); // Redirect to login page
    exit();
} else {
    header("Location: admin_login.php"); // Redirect if not logged in
    exit();
}
// Close the statement
?>
