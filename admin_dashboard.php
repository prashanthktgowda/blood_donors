<?php
ob_start();
session_start();

// Include the database connection
include 'db.php'; // Ensure this path is correct
include 'admin_dashboard_header.html';

// Set auto-logout time limit (in seconds)
$autoLogoutTime = 600; // 10 minutes = 600 seconds

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php"); // Redirect to login page if not logged in
    exit;
}

// Check for inactivity (last activity timestamp)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $autoLogoutTime)) {
    // Last request was over 10 minutes ago
    session_unset(); // Unset $_SESSION variables
    session_destroy(); // Destroy the session
    header("Location: admin_login.php?message=Session expired. Please log in again.");
    exit;
}

// Update last activity time stamp
$_SESSION['last_activity'] = time(); // Update last activity time to current time

// Fetch admin details from the database
$admin_id = $_SESSION['admin_id'];
$adminQuery = "SELECT * FROM admin WHERE id = ?";
$stmt = $conn->prepare($adminQuery);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
} else {
    // Handle error if admin not found
    echo "Admin not found.";
    exit;
}

// Close statement and connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin_dashboard.css"> <!-- Link to your CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<?php include 'loading.php'; ?>
    <main>
        <div class="container">
            <h1>Welcome, <?php echo htmlspecialchars($admin['username']); ?>!</h1>

            <h2>Profile Details</h2>
            <ul>
                <li><strong>Username:</strong> <?php echo htmlspecialchars($admin['username']); ?></li>
                <li><strong>Email:</strong> <?php echo htmlspecialchars($admin['email']); ?></li>
                <li><strong>Mobile Number:</strong> <?php echo htmlspecialchars($admin['mobile_number']); ?></li>
                <li><strong>Last Login:</strong> <?php echo htmlspecialchars($admin['last_login']); ?></li>
                <li><strong>Last Logout:</strong> <?php echo htmlspecialchars($admin['last_logout']); ?></li>
            </ul>

            <!-- Edit Profile Button -->
            <ul>
                <li><a href="admin_profile_edit.php"><i class="fas fa-edit"></i></a></li>
            </ul>
        </div>
    </main>

  <script>

        // Auto-logout after 10 minutes of inactivity
        let inactivityTime = function () {
            let time;
            window.onload = resetTimer;
            window.onmousemove = resetTimer;
            window.onmousedown = resetTimer; // Catches touch events
            window.ontouchstart = resetTimer;
            window.ontouchmove = resetTimer;
            window.onclick = resetTimer; // Catches touchpad clicks
            window.onkeypress = resetTimer;
            window.addEventListener('scroll', resetTimer, true); // Catches scrolling

            function logout() {
                alert("You have been logged out due to inactivity.");
                window.location.href = 'admin_logout.php'; // Adjust to your logout URL
            }

            function resetTimer() {
                clearTimeout(time);
                time = setTimeout(logout, 600000); // 10 minutes in milliseconds
            }
        };

        inactivityTime();
    </script>
   
    <?php include 'footer.php'; ?>
</body>
</html>

