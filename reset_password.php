<?php
// Include the database connection
include 'db.php'; // Ensure this path is correct
include 'reset_password_header.html';

$message = '';
$successMessage = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token exists in the donors table and hasn't expired
    $checkTokenQuery = "SELECT * FROM donors WHERE reset_token = ? AND reset_expiry > NOW()";
    $stmt = $conn->prepare($checkTokenQuery);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If the token is valid and form is submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Get the new password from the form
            $newPassword = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];

            if ($newPassword === $confirmPassword) {
                // Hash the new password
                $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

                // Update the password in the database, clear the reset token and expiry
                $updatePasswordQuery = "UPDATE donors SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE reset_token = ?";
                $updateStmt = $conn->prepare($updatePasswordQuery);
                $updateStmt->bind_param("ss", $hashedPassword, $token);
                $updateStmt->execute();

                if ($updateStmt->affected_rows > 0) {
                    $successMessage = "Your password has been successfully reset.";
                } else {
                    $message = "Password reset failed. Please try again.";
                }
                // Deactivate the token
                $updateStmt->close();
            } else {
                $message = "Passwords do not match.";
            }
        }
    } else {
        $message = "Invalid or expired token.";
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    $message = "No reset token provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="reset_password.css">
    <?php if (!empty($successMessage)): ?>
        <!-- Redirect after 5 seconds -->
        <meta http-equiv="refresh" content="5;url=login.php">
    <?php endif; ?>
</head>
<body>
<?php include 'loading.php'; ?>
    <div class="container">
        <h3>Reset Password</h3>

        <?php if (!empty($message)): ?>
            <div class="message" style="color: red;">
                <p><?php echo htmlspecialchars($message); ?></p>
                <meta http-equiv="refresh" content="5;url=login.php">
            </div>
        <?php elseif (!empty($successMessage)): ?>
            <div class="message" style="color: green;">
                <p><?php echo htmlspecialchars($successMessage); ?></p>
                <meta http-equiv="refresh" content="5;url=login.php">
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['token']) && $result->num_rows > 0): ?>
            <form method="POST">
                <input type="password" name="password" placeholder="Enter new password" required>
                <input type="password" name="confirm_password" placeholder="Confirm new password" required>
                <button type="submit" class="btn">Reset Password</button>
            </form>
        <?php endif; ?>
    </div>

    <script>
        // Auto-hide message after 5 seconds
        setTimeout(() => {
            const messageDiv = document.querySelector('.message');
            if (messageDiv) {
                messageDiv.style.display = 'none';
            }
        }, 5000); // Hide after 5 seconds

        // Toggle dark mode
        const toggleButton = document.getElementById('dark-mode-toggle');
        if (toggleButton) {
            toggleButton.addEventListener('click', () => {
                document.body.classList.toggle('dark-mode');
                // Save dark mode preference in local storage
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
