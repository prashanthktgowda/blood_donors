<?php
ob_start(); // Start output buffering
session_start();

include 'db.php';
include 'email_verification_header.html';

$errorMessage = '';
$successMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['verify'])) {
    $verification_code = $_POST['verification_code'];
    $email = $_SESSION['temp_email'];

    // Check the verification code
    if ($verification_code == $_SESSION['verification_code']) {
        // Move data from temp_donors to donors
        $moveQuery = "INSERT INTO donors (name, email, password, mobile, blood_group, location, latitude, longitude)
                      SELECT name, email, password, mobile, blood_group, location, latitude, longitude 
                      FROM temp_donors WHERE email=?";
        
        $stmt = $conn->prepare($moveQuery);
        $stmt->bind_param("s", $email);

        if ($stmt->execute()) {
            // Delete from temp_donors
            $deleteQuery = "DELETE FROM temp_donors WHERE email=?";
            $stmt = $conn->prepare($deleteQuery);
            $stmt->bind_param("s", $email);

            if ($stmt->execute()) {
                $successMessage = "Registration successful. You can now log in.";
                session_unset(); // Clear session data
                session_destroy(); // Destroy the session

                // Redirect to login.php
                header("Location: login.php");
                exit();
            } else {
                $errorMessage = "Error deleting from temporary donors: " . $stmt->error;
            }
        } else {
            $errorMessage = "Error moving data to donors: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $errorMessage = "Invalid verification code.";
    }
}

$conn->close(); // Close the database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <link rel="stylesheet" href="email_verification.css">
</head>
<body>
<?php include 'loading.php'; ?>
    <div class="container">
        <h1>Email Verification</h1>
        <h4>Check your registered email...</h4>
        <?php if (!empty($successMessage)): ?>
            <div class="success-message">
                <p><?php echo $successMessage; ?></p>
            </div>
        <?php elseif (!empty($errorMessage)): ?>
            <div class="error-message">
                <p><?php echo $errorMessage; ?></p>
            </div>
        <?php else: ?>
            <form method="POST">
                <input type="text" name="verification_code" placeholder="Enter verification code" required>
                <button type="submit" name="verify" class="btn">Verify</button>
            </form>
        <?php endif; ?>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleButton = document.getElementById('dark-mode-toggle');

        // Apply dark mode if it's enabled in localStorage
        if (localStorage.getItem('darkMode') === 'enabled') {
            document.body.classList.add('dark-mode');
        }

        // Dark mode toggle button functionality
        toggleButton.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            if (document.body.classList.contains('dark-mode')) {
                localStorage.setItem('darkMode', 'enabled');
            } else {
                localStorage.removeItem('darkMode');
            }
        });
    });
    </script>
    
    <?php include 'footer.php'; ?>
</body>
</html>
