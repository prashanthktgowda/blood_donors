<?php
ob_start();
session_start();

// Include the database connection
include 'db.php';
include 'login_header.html'; // Make sure this path is correct

$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get login form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the email exists in the donors table
    $checkQuery = "SELECT * FROM donors WHERE email = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If email exists, verify the password
        $donor = $result->fetch_assoc();

        if (password_verify($password, $donor['password'])) {
            // Password is correct, start the session
            $_SESSION['donor_id'] = $donor['id'];
            $_SESSION['donor_name'] = $donor['name'];

            // Redirect to a dashboard or another page
            header("Location: dashboard.php"); // Change this to the appropriate page
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
ob_end_flush(); // Flush the output buffer
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
<?php include 'loading.php'; ?>
    <main>
        <div class="container">
            <h1>Donor Login</h1>

            <?php if (!empty($errorMessage)): ?>
                <div class="error-message">
                    <p><?php echo $errorMessage; ?></p>
                </div>
            <?php endif; ?>

            <form method="POST">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" class = 'form-button' >Login</button>
            </form>
            <p>Don't have an account? <a href="register.php">Register here</a></p>
            <p>Forgot your password? <a href="forgot_password.php">Reset it here</a></p>

            <!-- Home Button -->
        </div>
    </main>

    <script>
        // Dark mode toggle functionality if needed
        const toggleButton = document.getElementById('dark-mode-toggle');
        if (toggleButton) {
            toggleButton.addEventListener('click', () => {
                document.body.classList.toggle('dark-mode');
                // Save preference in local storage
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
