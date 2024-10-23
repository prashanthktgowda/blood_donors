<?php
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer Example</title>
    <style>
        /* Footer Styles */
        .footer {
            background-color: #ffffff; /* Light background for iOS style */
            color: #333; /* Dark text for readability */
            padding: 20px 10px; /* Padding for spacing */
            position: relative; /* Positioning */
            bottom: 0; /* Stick to bottom */
            width: 100%; /* Full width */
            box-shadow: 0 -1px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
            gap: 10px;
        }

        .footer-container {
            display: flex; /* Flexbox for layout */
            justify-content: space-between; /* Space between sections */
            flex-wrap: wrap; /* Allow wrapping on small screens */
            
        }

        .footer-section {
            flex: 1; /* Each section takes equal space */
            margin: 10px; /* Margin for spacing */
        }

        .footer-section h4 {
            margin-bottom: 10px; /* Space between heading and content */
            font-size: 16px; /* Heading size */
            font-weight: 600; /* Bold headings */
        }

        .footer-link {
            display: block; /* Each link on a new line */
            color: #007bff; /* Bootstrap primary color */
            text-decoration: none; /* Remove underline */
            padding: 5px 0; /* Padding for links */
            transition: color 0.3s ease; /* Smooth color transition */
        }

        .footer-link:hover {
            color: #0056b3; /* Darker color on hover */
            text-decoration: underline; /* Underline on hover */
        }

        .footer-bottom {
            text-align: center; /* Centered text */
            margin-top: 20px; /* Space above */
            font-size: 0.9em; /* Slightly smaller font size */
            color: #777; /* Lighter text color */
        }

        /* Responsive Styles */
        @media screen and (max-width: 768px) {
            .footer-container {
                flex-direction: column; /* Stack sections on small screens */
              
            }

            .footer-section {
                text-align: center; /* Center text on small screens */
            }
        }
    </style>
</head>
<body>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h4>About Us</h4>
                <a href="about.php" class="footer-link">Learn More</a>
            </div>
            
            <div class="footer-section">
                <h4>Contact</h4>
                <a href="contact.php" class="footer-link">Get in Touch</a>
                <p>Email: newlifeksv@gmail.com</p>
                <p>Phone: +91 876-243-1756</p>
            </div>
            <div class="footer-section">
                <h4>Resources</h4>
                <a href="privacy.php" class="footer-link">Privacy Policy</a>
                <a href="terms.php" class="footer-link">Terms of Service</a>
            </div>
            <div class="footer-section">
                <h4>Follow Us</h4>
                <a href="#" class="footer-link">Facebook</a>
                <a href="#" class="footer-link">Twitter</a>
                <a href="#" class="footer-link">Instagram</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 Blood Bank. All Rights Reserved.</p>
        </div>
    </footer>

</body>
</html>
