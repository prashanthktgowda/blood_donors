<?php
// Include the database connection
session_start(); // Start the session
include 'db.php'; // Ensure this path is correct
include 'admin_forgot_password_header.html'; // Adjust the header file for admin if necessary
require 'vendor/autoload.php'; // Ensure this path is correct (only for Composer users)

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if the email exists in the admin table
    $checkQuery = "SELECT * FROM admin WHERE email = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $token = bin2hex(random_bytes(32));
        // Set time zone and expiry
        date_default_timezone_set('Asia/Kolkata'); // Set to your correct time zone
        $expiry = date('Y-m-d H:i:s', strtotime('+30 minutes')); // Token valid for 30 minutes

        // Save token and expiry in the database
        $updateQuery = "UPDATE admin SET reset_token = ?, reset_expiry = ? WHERE email = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("sss", $token, $expiry, $email);
        $updateStmt->execute();

        // Send reset link via email using PHPMailer
        $resetLink = "http://localhost/blood_bank_mobile/admin_reset_password.php?token=$token"; // Update to your admin reset password page

        // PHPMailer setup
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Use your email service provider's SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'newlifeksv@gmail.com'; // Your Gmail or SMTP username
            $mail->Password = 'pxuo fjyj dhib wtqj'; // Your Gmail or SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Email details
            $mail->setFrom('no-reply@yourdomain.com', 'New Life Admin');
            $mail->addAddress($email); // Add recipient email address
            $mail->Subject = "Admin Password Reset Request";
            $mail->isHTML(true);
            $mail->Body = "Click the following link to reset your password: <a href='$resetLink'>$resetLink</a>";

            // Send the email
            $mail->send();
            $message = "A password reset link has been sent to your email."; // Success message

        } catch (Exception $e) {
            $message = "Failed to send email. Error: {$mail->ErrorInfo}";
        }

        $updateStmt->close();
    } else {
        $message = "No admin account found with this email.";
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
    <title>Admin Forgot Password</title>
    <link rel="stylesheet" href="admin_forgot_password.css">

    <?php if (!empty($message)): ?>
        <!-- Redirect after 5 seconds -->
        <meta http-equiv="refresh" content="5;url=index.php">
    <?php endif; ?>
</head>
<body>
<?php include 'loading.php'; ?>
    <main class="container">
        <h1>Admin Forgot Password</h1>        
        <?php if (!empty($message)): ?>
            <div class="message">
                <p><?php echo htmlspecialchars($message); ?></p>
            </div>
        <?php endif; ?>
        <?php if (empty($message)): ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Enter your registered email" required>
            <button type="submit" class ='btn'>Reset Password</button>
        </form>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>

    <script>
        const toggleButton = document.getElementById('dark-mode-toggle');
        if (toggleButton) { // Check if toggleButton exists
            toggleButton.addEventListener('click', () => {
                document.body.classList.toggle('dark-mode');
            });
        }
    </script>
</body>
</html>
