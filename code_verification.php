<?php
ob_start();
include 'db.php';
session_start(); // Start the session to access session variables

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['verify_code'])) {
    $verification_code = $_POST['verification_code'];

    // Check if the verification code entered matches the one stored in the session
    if ($verification_code == $_SESSION['verification_code']) {
        // Mark email as verified
        $email = $_SESSION['email'];
        $updateQuery = "UPDATE donors SET email_verified = 1 WHERE email = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $message = "Your email has been verified successfully! You can now register.";
            $_SESSION['email_verified'] = true; // Mark email as verified in the session
            header("Location: register.php"); // Redirect to registration page
            exit;
        } else {
            $message = "Error: Could not verify your email.";
        }
        
        $stmt->close();
    } else {
        $message = "Invalid verification code.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'loading.php'; ?>
    <div class="container">
        <h1>Email Verification</h1>

        <?php if (!empty($message)): ?>
            <div class="message">
                <p><?php echo htmlspecialchars($message); ?></p>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="verification_code" placeholder="Enter Verification Code" required>
            <button type="submit" name="verify_code">Verify Code</button>
        </form>

        <p>Don't have a code? <a href="send_verification.php">Request a new code</a></p>
        <a href="index.php" class="home-btn">Home <span class="arrow">→</span></a>
    </div>
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
