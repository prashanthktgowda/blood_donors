<?php
session_start(); // Start the session to pass data between pages
// Include the database connection
include 'db.php';
include 'donor_search_header.php';
$donors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Search by Location
    if (isset($_POST['search_by_location'])) {
        $blood_group = $_POST['blood_group'];
        $latitude = isset($_POST['latitude']) ? $_POST['latitude'] : '';
        $longitude = isset($_POST['longitude']) ? $_POST['longitude'] : '';

        // If latitude and longitude are available, prioritize nearby donors using Haversine formula
        if (!empty($latitude) && !empty($longitude)) {
            $radius = 15; // Radius in kilometers
            
            $query = "
                SELECT *, 
                       (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) 
                       + sin(radians(?)) * sin(radians(latitude)))) AS distance 
                FROM donors 
                WHERE blood_group = ? 
                HAVING distance < ? 
                ORDER BY distance ASC";
            
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ddssd", $latitude, $longitude, $latitude, $blood_group, $radius);
        }
        

    } elseif (isset($_POST['search_by_blood_group'])) {
        // Search by Blood Group with Optional Address
        $blood_group = $_POST['blood_group'];
        $location = trim($_POST['location']); // Trim to avoid extra spaces

        if (!empty($location)) {
            $query = "SELECT * FROM donors WHERE blood_group = ? AND location LIKE ?";
            $stmt = $conn->prepare($query);
            $locationFilter = '%' . $location . '%';
            $stmt->bind_param("ss", $blood_group, $locationFilter);
        } else {
            // If no address is provided, search by blood group only
            $query = "SELECT * FROM donors WHERE blood_group = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $blood_group);
        }
    }

    $stmt->execute();
    if ($stmt->error) {
        die("Execute failed: " . $stmt->error);
    }
    $result = $stmt->get_result();

    // Fetch all matching donors
    while ($row = $result->fetch_assoc()) {
        $donors[] = $row;
    }

    $stmt->close();

    // Store the donor data in the session to pass to donor_list.php
    $_SESSION['donors'] = $donors;

    // If no donors are found, store a message in the session
    if (empty($donors)) {
        $_SESSION['message'] = 'No donors found for the selected criteria.';
    } else {
        unset($_SESSION['message']); // Clear any previous messages
    }

    // Redirect to donor_list.php to display the results
    header('Location: donor_list.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Donor</title>
    <link rel="stylesheet" href="donor_search.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition, showError);
            } else {
                document.getElementById("gps-message").innerHTML = "Geolocation is not supported by this browser.";
            }
        }

        function showPosition(position) {
            document.getElementById("latitude").value = position.coords.latitude;
            document.getElementById("longitude").value = position.coords.longitude;
            document.getElementById("gps-message").innerHTML = "GPS coordinates acquired.";
        }

        function showError(error) {
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    document.getElementById("gps-message").innerHTML = "User denied the request for Geolocation.";
                    break;
                case error.POSITION_UNAVAILABLE:
                    document.getElementById("gps-message").innerHTML = "Location information is unavailable.";
                    break;
                case error.TIMEOUT:
                    document.getElementById("gps-message").innerHTML = "The request to get user location timed out.";
                    break;
                case error.UNKNOWN_ERROR:
                    document.getElementById("gps-message").innerHTML = "An unknown error occurred.";
                    break;
            }
        }
    </script>
</head>
<body class="spotify-theme" onload="getLocation()">

    <div class="container">
        <h1>Search Donor</h1>

        <!-- Search Form by Location -->
            <form method="POST" class="search-form">
                <h2>Search by Location</h2>
                <select name="blood_group" required>
                    <option value="">Select Blood Group</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                </select>

                <input type="hidden" id="latitude" name="latitude">
                <input type="hidden" id="longitude" name="longitude">

                <button type="submit" name="search_by_location" class="btn search-btn">Search by Location</button>
                <p id="gps-message">Use address for a better search experience.</p>
            </form>

        <!-- Search Form by Blood Group with Optional Address -->
        <form method="POST" class="search-form">
            <h2>Search by Blood Group</h2>
            <select name="blood_group" required>
                <option value="">Select Blood Group</option>
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
            </select>

            <input type="text" name="location" placeholder="Enter location (optional)">

            <button type="submit" name="search_by_blood_group" class="btn search-btn">Search by Blood Group</button>
        </form>
    </div>
    
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
