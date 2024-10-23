<?php
session_start(); // Start the session

// Include the database connection
include 'db.php'; 
include 'admin_dashboard_header.php'; 
require 'vendor/autoload.php'; // Ensure this path is correct for Composer users

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php"); // Redirect to login if admin is not logged in
    exit;
}

$logged_in_admin_id = $_SESSION['admin_id'];

// Handle admin removal request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_admin_id'])) {
    $remove_admin_id = $_POST['remove_admin_id'];

    // Prevent removing the currently logged-in admin
    if ($remove_admin_id == $logged_in_admin_id) {
        $message = "You cannot remove yourself.";
    } else {
        // Generate OTP
        $otp = rand(100000, 999999);
        // Set OTP expiry (valid for 10 minutes)
        date_default_timezone_set('Asia/Kolkata');
        $expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        // Get the email of the admin to be removed
        $adminQuery = "SELECT email FROM admin WHERE id = ?";
        $stmt = $conn->prepare($adminQuery);
        $stmt->bind_param("i", $remove_admin_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            $email = $admin['email'];

            // Save OTP and expiry to the database for the admin
            $updateQuery = "UPDATE admin SET otp = ?, otp_expiry = ? WHERE id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("ssi", $otp, $expiry, $remove_admin_id);
            $updateStmt->execute();

            // Send OTP via email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Use your email service provider's SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = 'newlifeksv@gmail.com'; // Your SMTP username
                $mail->Password = 'pxuo fjyj dhib wtqj'; // Your SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Email content
                $mail->setFrom('no-reply@yourdomain.com', 'New Life Admin');
                $mail->addAddress($email);
                $mail->Subject = "OTP for Admin Removal";
                $mail->isHTML(true);
                $mail->Body = "Your OTP for admin removal is: <b>$otp</b>. It is valid for 10 minutes.";

                $mail->send();
                $message = "OTP sent to $email. Please verify.";

                // Redirect to OTP verification page
                header("Location: verify_otp.php?admin_id=$remove_admin_id");
                exit;

            } catch (Exception $e) {
                $message = "Failed to send OTP. Error: {$mail->ErrorInfo}";
            }
            $updateStmt->close();
        } else {
            $message = "Admin not found.";
        }
        $stmt->close();
    }
}

// Fetch all admins except the logged-in admin
$adminQuery = "SELECT id, username, email FROM admin WHERE id != ?";
$stmt = $conn->prepare($adminQuery);
$stmt->bind_param("i", $logged_in_admin_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admins</title>
    <link rel="stylesheet" href="admin_manage.css"> <!-- Link to your CSS file -->
</head>
<body>
<?php include 'loading.php'; ?>
    <main class="container">
        <h1>Manage Admins</h1>
        <?php if (!empty($message)): ?>
            <div class="message">
                <p><?php echo htmlspecialchars($message); ?></p>
            </div>
        <?php endif; ?>

        <?php if ($result->num_rows > 0): ?>
            <ul>
                <?php while ($admin = $result->fetch_assoc()): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($admin['username']); ?></strong> (<?php echo htmlspecialchars($admin['email']); ?>)
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="remove_admin_id" value="<?php echo $admin['id']; ?>">
                            <button type="submit">Remove Admin</button>
                        </form>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No other admins found.</p>
        <?php endif; ?>

    </main>

    <?php include 'footer.php'; ?>
</body>
</html>

<?php
// Close the database connection
$stmt->close();
$conn->close();
?>
