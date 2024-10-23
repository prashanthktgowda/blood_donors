<?php
session_start();
include 'footer_header.html'; // Include the header file
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Blood Donor Search</title>
    <link rel="stylesheet" href="contact.css">
</head>
<body>
<main>
    <section class="terms">
        <h1>Welcome to New Life</h1>
        <ul>
            <li><strong>Organization:</strong> New Life!</li>
            <li><strong>Address:</strong> KSV Nilaya, Millers road,Vasanthnagar,Bengaluru-560052</li>
            <li><strong>Email:</strong> <a href="mailto:newlifeksv@gmail.com">newlifeksv@gmail.com</a></li>
            <li><strong>Phone Number:</strong> +91 8762431756</li>
        </ul>
    </section>
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
