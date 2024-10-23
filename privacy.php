<?php
session_start();
include 'footer_header.html'; // Include the header file
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Blood Donor Search</title>
    <link rel="stylesheet" href="privacy.css"> <!-- Optional CSS file -->
</head>
<body>
<main>
    <h1>Privacy Policy for Blood Donor Search</h1>
    <p>Effective Date: [Insert Date]</p>

    <p>Your privacy is important to us. This policy outlines how we collect, use, and protect your personal information when you use our website.</p>

    <h2>1. Information Collection</h2>
    <p>We collect personal information such as your name, contact details, and medical history when you register as a donor or requester.</p>

    <h2>2. Use of Information</h2>
    <p>Your information will be used to connect you with blood donors or requesters and improve our services.</p>

    <h2>3. Data Sharing</h2>
    <p>We may share your information with healthcare providers or legal authorities when required by law.</p>

    <h2>4. Data Security</h2>
    <p>We implement various security measures to protect your personal information.</p>

    <h2>5. User Rights</h2>
    <p>You have the right to access, correct, or delete your personal information at any time.</p>

    <h2>Contact Information</h2>
    <p>For any questions, please contact us at: <a href="mailto:newlifeksv@gmail.com">newlifeksv@gmail.com</a>.</p>
</main>

<?php include 'footer.php'; // Include the footer file ?>

<!-- Dark Mode Toggle Script -->
<script>
    const toggleButton = document.getElementById('dark-mode-toggle');
    toggleButton.addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');
    });
</script>
</body>
</html>