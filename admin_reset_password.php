<?php
ob_start();
session_start();
include 'db.php'; // Ensure this path is correct
include 'admin_reset_password_header.html'; // Adjust the header file for admin if necessary

$message = '';
$token = $_GET['token'] ?? '';

if (empty($token)) {
    $message = "Invalid token.";
} else {
    // Verify the token
    $query = "SELECT * FROM admin WHERE reset_token = ? AND reset_expiry > NOW()";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Token is valid, proceed with password reset
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $new_password = $_POST['new_password'];
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update the password in the database
            $updateQuery = "UPDATE admin SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE reset_token = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("ss", $hashed_password, $token);
            if ($updateStmt->execute()) {
                $message = "Password has been reset successfully. Redirecting to admin login...";
                // Redirect after 2 seconds
                header("refresh:2;url=admin_login.php");
            } else {
                $message = "Error updating password. Please try again.";
            }
            $updateStmt->close();
        }
    } else {
        $message = "Invalid or expired token.";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="admin_reset_password.css">
</head>
<body>
<?php include 'loading.php'; ?>
    <main class="container">
        <h1>Reset Password</h1>
        <?php if (!empty($message)): ?>
            <div class="message">
                <p><?php echo htmlspecialchars($message); ?></p>
            </div>
        <?php endif; ?>
        <?php if (empty($message) && !empty($token)): ?>
        <form method="POST">
            <input type="password" name="new_password" placeholder="Enter new password" required>
            <input type="password" name="new_password" placeholder="Confirm new password" required>

            <button type="submit" class="btn">Reset Password</button>
        </form>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>

    <script>
        const toggleButton = document.getElementById('dark-mode-toggle');
        toggleButton.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
        });
    </script>
</body>
</html>
