<?php
session_start();
include 'footer_header.html'; // Include the header file
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms and Conditions - Blood Donor Search</title>
    <link rel="stylesheet" href="terms.css"> <!-- Optional CSS file -->
</head>
<body>
<main>
    <h1>Terms and Conditions for Blood Donor Search</h1>
    <p>Effective Date: 23/09/2024</p>

    <h2>1. Acceptance of Terms</h2>
    <p>By accessing or using our website, you agree to comply with these terms and conditions. If you do not agree, please do not use the site.</p>

    <h2>2. Use of the Site</h2>
    <p>You may use our website for lawful purposes only. You agree not to use the site for any fraudulent or illegal activities.</p>

    <h2>3. User Responsibilities</h2>
    <p>You are responsible for maintaining the confidentiality of your account information and for all activities that occur under your account.</p>

    <h2>4. Disclaimer</h2>
    <p>The information provided on this website is for informational purposes only and should not be considered medical advice. Always consult a healthcare professional for medical concerns.</p>

    <h2>5. Limitation of Liability</h2>
    <p>We are not liable for any damages or losses arising from your use of the site or reliance on any information provided on the site.</p>

    <h2>6. Changes to Terms</h2>
    <p>We may update these terms from time to time. We will notify users of significant changes via email or through a notice on our website.</p>

    <h2>7. Governing Law</h2>
    <p>These terms are governed by the laws of India. Any disputes arising from these terms will be resolved in the courts of [Your Jurisdiction].</p>

    <h2>Contact Information</h2>
    <p>If you have any questions about these terms, please contact us at: <a href="mailto:newlifeksv@gmail.com">newlifeksv@gmail.com</a>.</p>
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
