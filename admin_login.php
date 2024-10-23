<?php
ob_start();
session_start();

// Include the database connection
include 'db.php'; // Ensure this path is correct
include 'admin_login_header.html'; // Include your header if needed

$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get login form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the email exists in the admin table
    $checkQuery = "SELECT * FROM admin WHERE email = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If email exists, verify the password
        $admin = $result->fetch_assoc();

        if (password_verify($password, $admin['password'])) {
            // Password is correct, start the session
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['username']; // Or any other name field

             // Capture the admin's IP address
             $ip_address = $_SERVER['REMOTE_ADDR'];

             // Log the login activity
             $login_time = date('Y-m-d H:i:s');
             $activityQuery = "INSERT INTO admin_activity (admin_id, login_time, ip_address) VALUES (?, ?, ?)";
             $stmt_activity = $conn->prepare($activityQuery);
             $stmt_activity->bind_param("iss", $admin['id'], $login_time, $ip_address);
             $stmt_activity->execute();

            // Redirect to a dashboard or another page
            header("Location: admin_dashboard.php"); // Change this to the appropriate page
            exit;
        } else {
            // Incorrect password
            $errorMessage = "Incorrect password. Please try again.";
        }
    } else {
        // Email does not exist
        $errorMessage = "No account found with this email.";
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="admin_login.css">
</head>
<body>
<?php include 'loading.php'; ?>
    <main>
        <div class="container">
            <h1>Admin Login</h1>

            <?php if (!empty($errorMessage)): ?>
                <div class="error-message">
                    <p><?php echo $errorMessage; ?></p>
                </div>
            <?php endif; ?>

            <form method="POST">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" class ='form-button' >Login</button>
            </form>
            <p>Forgot your password? <a href="admin_forgot_password.php">Reset it here</a></p>

            <!-- Home Button -->
        </div>
    </main>

    <script>
        // Toggle dark mode
        const toggleButton = document.getElementById('dark-mode-toggle');
        if (toggleButton) {
            toggleButton.addEventListener('click', () => {
                document.body.classList.toggle('dark-mode');
                // Optionally, save dark mode preference in local storage
                if (document.body.classList.contains('dark-mode')) {
                    localStorage.setItem('darkMode', 'enabled');
                } else {
                    localStorage.removeItem('darkMode');
                }
            });
        }

        // Check local storage for dark mode preference
        if (localStorage.getItem('darkMode') === 'enabled') {
            document.body.classList.add('dark-mode');
        }
    </script>
    <?php include 'footer.php'; ?>

</body>
</html>
