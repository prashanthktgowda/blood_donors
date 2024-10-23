<?php
ob_start(); // Start output buffering at the beginning to prevent any output before PDF generation
session_start(); // Start the session to access session variables

include "footer_header.html";
// Include PHPMailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php'; // Ensure you've installed PHPMailer via Composer

$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_query'])) {
    // Get form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $query = $_POST['query'];

    // Prepare the email content
    $mailContent = "Name: $name\n";
    $mailContent .= "Email: $email\n";
    $mailContent .= "Query: $query\n";

    // Send the email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Use your email service provider's SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'newlifeksv@gmail.com'; // Your Gmail or SMTP username
        $mail->Password = 'pxuo fjyj dhib wtqj'; // Your Gmail or SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('no-reply@yourdomain.com', 'New Life');
        $mail->addAddress('newlifeksv@gmail.com'); // Recipient's email

        $mail->Subject = 'New Query from Donor';
        $mail->Body    = $mailContent;

        $mail->send();
        header("Location: success_message.php"); // Redirect to success page
        exit();
    } catch (Exception $e) {
        $errorMessage = 'Mailer Error: ' . $mail->ErrorInfo;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Query</title>
    <link rel="stylesheet" href="send_query.css"> <!-- Include your CSS -->
</head>
<body>

<?php if (!empty($errorMessage)): ?>
    <div class="error-message">
        <p><?php echo $errorMessage; ?></p>
    </div>
<?php endif; ?>
<main>
<?php include 'loading.php'; ?>
<div class="container">
    <h1>Send Your Query</h1>
    <form method="POST">
        <input type="text" name="name" placeholder="Your Name" required>
        <input type="email" name="email" placeholder="Your Email" required>
        <textarea name="query" placeholder="Your Query" required></textarea>
        <button type="submit" name="send_query" class="form-button">Send Query</button>
    </form>
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
