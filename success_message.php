<?php
session_start(); // Start the session to access session variables
include 'footer_header.html'; // Include the header file
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success</title>
    <link rel="stylesheet" href="success_message.css"> <!-- Include your CSS -->
</head>
<body>

<main class="container">
    <h1>Success!</h1>
    <p>Your query has been successfully sent. We will get back to you shortly.</p>
    <a href="index.php" class="button">Go to Home</a>
</main>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    .container {
        width: 80%;
        margin: 0 auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    h1 {
        color: #28a745; /* Green color for success */
    }

    .button {
        display: inline-block;
        margin-top: 20px;
        padding: 10px 20px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 5px;
    }

    .button:hover {
        background-color: #0056b3;
    }
</style>

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
