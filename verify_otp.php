<?php
ob_start();
session_start();
include 'db.php'; // Ensure this path is correct
include 'admin_dashboard_header.php'; // Adjust the header file for admin if necessary
$message = '';

if (!isset($_SESSION['otp_sent']) || !isset($_SESSION['admin_to_remove'])) {
    header("Location: admin_manage.php");
    exit;
}

// Handle OTP verification
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['verify_otp'])) {
    $entered_otp = $_POST['otp'];

    if ($entered_otp == $_SESSION['otp']) {
        // OTP is correct, proceed to remove the admin
        $admin_id_to_remove = $_SESSION['admin_to_remove'];

        $deleteAdminQuery = "DELETE FROM admin WHERE id = ?";
        $stmt = $conn->prepare($deleteAdminQuery);
        $stmt->bind_param("i", $admin_id_to_remove);

        if ($stmt->execute()) {
            // Successfully removed the admin
            unset($_SESSION['otp']);
            unset($_SESSION['otp_sent']);
            unset($_SESSION['admin_to_remove']);
            $message = "Admin removed successfully.";
        } else {
            $message = "Failed to remove admin.";
        }
        $stmt->close();
    } else {
        $message = "Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="verify_otp.css">
</head>
<body>
    <main>
        <div class="otp-container">
            <h1>OTP Verification</h1>
            <?php if (!empty($message)): ?>
                <p><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>
            <form method="post" action="">
                <label for="otp">Enter the OTP sent to your email:</label>
                <input type="text" name="otp" required>
                <button type="submit" name="verify_otp">Verify OTP</button>
            </form>
        </div>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>
