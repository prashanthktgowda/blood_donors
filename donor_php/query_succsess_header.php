<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dark Mode Toggle Example</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Reset some default browser styles */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4; /* Light mode background */
            color: #333; /* Light mode text color */
            transition: background-color 0.3s ease, color 0.3s ease;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            background-color: skyblue; /* Dark mode header */
            padding: 15px;
            color: #121212;
            border-bottom: 1px solid #fff;
            display: flex;
            align-items: center;
            position: relative; /* Added for the loading overlay */
        }

        header .header-container {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }

        header h1 {
            margin: 0;
            font-size: 24px;
            color: #121212; /* Light text in header */
            flex-grow: 1; /* Allow title to grow and center */
            text-align: center; /* Center the title */
        }

        header #dark-mode-toggle{
            color: black;
            background-color: transparent;
            padding: 10px 15px;
            text-align: center;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-size: 16px; /* Button text size */
            margin-right: 10px; /* Space between buttons */
        }

        header .admin-login {
            color: black;
            padding: 10px 15px;
            text-align: center;
            border: none;
            text-decoration: none;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-size: 16px; /* Button text size */
            margin-right: 10px; /* Space between buttons */  
        }

        header .button-group {
               display: flex;
            gap: 10px; /* Space between buttons */
        }

        header #dark-mode-toggle:hover, header .admin-login:hover {
            transform: translateY(-2px); /* Slight lift effect */
            color: blue;
        }

        /* Main content area styles */
        main {
            flex: 1; /* Allow main to grow and fill available space */
            padding: 20px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Dark Mode Styles */
        body.dark-mode {
            background-color: #121212; /* Dark background */
            color: #e0e0e0; /* Light text */
        }

        body.dark-mode header {
            background-color: #1e1e1e; /* Dark mode header */
        }

        body.dark-mode .admin-login {
            background-color: transparent; /* Different color for dark mode */
            color: whitesmoke;
        }

        body.dark-mode .admin-login:hover {
            color: blue;
        }

        body.dark-mode h1 {
            color: whitesmoke;
        }

        body.dark-mode main {
            background-color: #1e1e1e; /* Dark background for main content */
            color: #e0e0e0; /* Light text for main content */
        }

        body.dark-mode #dark-mode-toggle {
            background-color: transparent; /* Different color for dark mode */
            color: whitesmoke;
        }

        body.dark-mode #dark-mode-toggle:hover {
            color: blue;
        }

        body.dark-mode #dark-mode-toggle .fa-sun {
            display: none; /* Hide sun icon in dark mode */
        }

        body.dark-mode #dark-mode-toggle .fa-moon {
            display: inline; /* Show moon icon in dark mode */
        }

        body:not(.dark-mode) #dark-mode-toggle .fa-moon {
            display: none; /* Hide moon icon in light mode */
        }

        body:not(.dark-mode) #dark-mode-toggle .fa-sun {
            display: inline; /* Show sun icon in light mode */
        }

        /* Loading Overlay Styles */
        #loadingOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 8px;
            background: linear-gradient(90deg, rgba(0, 255, 255, 0.3), rgba(0, 255, 255, 1), rgba(0, 255, 255, 0.3), rgba(1, 255, 255, 2));
            background-size: 200% 100%;
            animation: wave 2s linear infinite;
            z-index: 9999; /* Ensure it appears above everything */
        }

        @keyframes wave {
            0% {
                background-position: 0 0;
            }
            100% {
                background-position: 200% 0;
            }
        }
    </style>
</head>
<body>

<!-- Loading Overlay -->

<header>
    <div class="header-container">
        <button id="dark-mode-toggle" aria-label="Toggle Dark/Light Mode">
            <i class="fas fa-sun" id="toggle-icon"></i> <!-- Default icon -->
        </button>
        <h1>NEW LIFE</h1>
        <div class="button-group">
            <a href="index.php" class="admin-login">HOME</a>
        </div>
    </div>
    <div id="loadingOverlay"></div>

</header>


<script>
    const toggleButton = document.getElementById('dark-mode-toggle');
    const body = document.body;
    const toggleIcon = document.getElementById('toggle-icon');
    const loadingOverlay = document.getElementById('loadingOverlay');

    // Simulate page loading
    window.addEventListener('load', () => {
        setTimeout(() => {
            loadingOverlay.style.display = 'none';
        }, 2000); // Hide after 2 seconds
    });

    toggleButton.addEventListener('click', () => {
        body.classList.toggle('dark-mode');
        if (body.classList.contains('dark-mode')) {
            toggleIcon.classList.remove('fa-sun');
            toggleIcon.classList.add('fa-moon');
        } else {
            toggleIcon.classList.remove('fa-moon');
            toggleIcon.classList.add('fa-sun');
        }
    });
</script>

</body>
</html>
