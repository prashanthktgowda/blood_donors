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
    <link rel="stylesheet" href="about.css">
</head>
<body>
<main>
    <main class="container">
        <!-- Page 1: Welcome Message -->
        <h1>Welcome to Our Free Blood Donor Search Website</h1>
        <p>In times of emergency, finding a blood donor quickly and easily can make all the difference. Our platform connects people in need of blood with voluntary donors who are ready to help, ensuring that no one has to wait for the blood they need. Whether you need blood for an emergency or are simply preparing in advance, our free platform is here to assist you every step of the way.</p>

        <p>Our website allows voluntary donors to register and provide their information, making it easily searchable by those in need. With just a few clicks, users can search for donors based on blood type, location, and availability. This streamlined process ensures that the necessary blood is available as soon as possible, potentially saving lives.</p>

        <p>The best part? Our service is entirely free for both donors and recipients. Donors who want to make a difference can easily sign up, while those in need can search without any financial barrier. We believe that access to life-saving blood should never be hindered by cost or availability, and this platform is designed to bring people together in times of need.</p>

        <h2>Features of Our Platform:</h2>
        <ul>
            <li><strong>Free Registration:</strong> Donors can sign up quickly and add details such as blood type, location, and contact information.</li>
            <li><strong>Search Functionality:</strong> Users in need can search based on various filters like blood type, city, or urgency.</li>
            <li><strong>Real-time Availability:</strong> Find donors who are immediately available to donate blood based on their donation history and location.</li>
        </ul>

        <p>Our goal is to make the blood donation process smoother and more efficient, ensuring no patient goes without the help they need.</p>

        <!-- Page 2: Security and Privacy Terms and Conditions -->
        <h2>Security and Privacy Terms and Conditions</h2>
        <main class="terms">
            <p>We are committed to ensuring the security and privacy of all users who register and search on our platform. When it comes to dealing with sensitive health-related information, we take several security measures to protect both donors and recipients.</p>

            <ul>
                <li><strong>Data Privacy:</strong> All personal data collected during registration, including contact details and medical information (e.g., blood type), will be securely stored and will not be shared with any third parties without explicit consent.</li>
                <li><strong>Access Control:</strong> Only authorized users who have registered will have access to our donor search database. Search requests and donation history will be accessible only to those who are logged in and have a legitimate need for this information.</li>
                <li><strong>Encryption:</strong> All communications between users and our platform are encrypted to ensure that personal information is not intercepted or misused by unauthorized parties.</li>
                <li><strong>Voluntary Participation:</strong> Registration as a donor on our platform is entirely voluntary, and users can remove or update their details at any time. We encourage donors to keep their availability and contact information current to provide accurate results during emergencies.</li>
                <li><strong>No Financial Transactions:</strong> Our platform does not involve any payment for services or donor participation. We do not ask for financial details, and all services remain free.</li>
                <li><strong>User Responsibility:</strong> Both donors and requesters are expected to provide accurate information. Misrepresentation of information can lead to the removal of accounts.</li>
                <li><strong>Data Security:</strong> We employ industry-standard security measures such as firewalls, encryption, and regular system audits to ensure your data remains secure.</li>
            </ul>
        </main>
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
