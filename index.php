<?php
session_start();
ob_start();

// Include the database connection and header
include 'db.php';
include 'index_header.html';

// Error reporting for debugging (ensure this is off in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



// SQL query to count total registered donors
$sql_total_donors = "SELECT COUNT(*) AS total_donors FROM donors";
$result_total_donors = $conn->query($sql_total_donors);
$total_donors = $result_total_donors ? $result_total_donors->fetch_assoc()['total_donors'] : 0;

// SQL query to count eligible donors (those who haven't donated in the last 84 days)
$sql_eligible_donors = "
    SELECT COUNT(*) AS eligible_donors 
    FROM donors d 
    LEFT JOIN (
        SELECT donor_id, MAX(donation_date) AS last_donation_date
        FROM donation_history
        GROUP BY donor_id
    ) dh ON d.id = dh.donor_id
    WHERE d.account_frozen = FALSE 
    AND (dh.last_donation_date IS NULL OR DATEDIFF(CURDATE(), dh.last_donation_date) >= 84)
";
$result_eligible_donors = $conn->query($sql_eligible_donors);
$eligible_donors = $result_eligible_donors ? $result_eligible_donors->fetch_assoc()['eligible_donors'] : 0;

// Handle POST requests for searching donors
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $blood_group = isset($_POST['blood_group']) ? $_POST['blood_group'] : '';
    $donors = [];

    // Search by location (latitude and longitude)
    if (isset($_POST['search_by_location'])) {
        $latitude = isset($_POST['latitude']) ? $_POST['latitude'] : '';
        $longitude = isset($_POST['longitude']) ? $_POST['longitude'] : '';

        if (!empty($latitude) && !empty($longitude)) {
            $radius = 50; // Radius in kilometers
            $query = "
                SELECT *, 
                       (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) 
                       + sin(radians(?)) * sin(radians(latitude)))) AS distance 
                FROM donors 
                WHERE blood_group = ? 
                AND account_frozen = FALSE 
                HAVING distance < ? 
                ORDER BY distance ASC
            ";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ddssd", $latitude, $longitude, $latitude, $blood_group, $radius);
        }
    } elseif (isset($_POST['search_by_blood_group'])) {
        // Search by blood group with optional location
        $location = trim($_POST['location'] ?? '');
        if (!empty($location)) {
            $query = "SELECT * FROM donors WHERE blood_group = ? AND account_frozen = FALSE AND location LIKE ?";
            $stmt = $conn->prepare($query);
            $locationFilter = '%' . $location . '%';
            $stmt->bind_param("ss", $blood_group, $locationFilter);
        } else {
            // Search by blood group only if no location is provided
            $query = "SELECT * FROM donors WHERE blood_group = ? AND account_frozen = FALSE";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $blood_group);
        }
    }

    // Execute the query and fetch results
    if (isset($stmt)) {
        $stmt->execute();
        if ($stmt->error) {
            error_log("Query execute failed: " . $stmt->error);
            die("Execute failed: " . $stmt->error);
        }
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $donors[] = $row;
        }
        $stmt->close();
    }

    // Store donor data or a message in the session
    $_SESSION['donors'] = $donors;
    if (empty($donors)) {
        $_SESSION['message'] = 'No donors found for the selected criteria.';
    } else {
        unset($_SESSION['message']); // Clear any previous messages
    }

    // Redirect to donor_list.php to display the results
    header('Location: donor_list.php');
    exit;
}

ob_end_flush(); // End output buffering
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Donation Campaign</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                default:
                    document.getElementById("gps-message").innerHTML = "An unknown error occurred.";
            }
        }

        window.onload = getLocation;

        function toggleSearchMode(mode) {
            const bloodGroupForm = document.getElementById('bloodGroupForm');
            const locationForm = document.getElementById('locationForm');
            const bloodGroupButton = document.querySelector('.toggle-btn[data-mode="bloodGroup"]');
            const locationButton = document.querySelector('.toggle-btn[data-mode="location"]');

            if (mode === 'bloodGroup') {
                bloodGroupForm.style.display = 'block';
                locationForm.style.display = 'none';
                bloodGroupButton.classList.add('active');
                locationButton.classList.remove('active');
            } else {
                bloodGroupForm.style.display = 'none';
                locationForm.style.display = 'block';
                locationButton.classList.add('active');
                bloodGroupButton.classList.remove('active');
            }
        }
    </script>
</head>
<body>
<div class="video-container">
    <video id="bg-video" autoplay muted loop>
        <source src="video2.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <div class="search-container">
        <div class="search-header">
            <h1>Search for a Donor</h1>
        </div>

        <!-- Toggle buttons for search mode -->
        <div class="mode-toggle">
            <button class="toggle-btn active" data-mode="bloodGroup" onclick="toggleSearchMode('bloodGroup')">Blood Group Search</button>
            <button class="toggle-btn" data-mode="location" onclick="toggleSearchMode('location')">Location Search</button>
        </div>

        <!-- Search Forms -->
        <div class="search-form">
            <!-- Blood Group Search Form -->
            <div id="bloodGroupForm" class="search-section" style="display: block;">
                <form method="POST" class="form-container">
                    <select name="blood_group" class="form-input" required>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
                    <input type="text" name="location" class="form-input" placeholder="Enter location (optional)">
                    <button type="submit" name="search_by_blood_group" class="form-submit-btn">Search by Blood Group</button>
                </form>
            </div>

            <!-- Location Search Form -->
            <div id="locationForm" class="search-section" style="display: none;">
                <form method="POST" class="form-container">
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                    <select name="blood_group" class="form-input" required>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
                    <button type="submit" name="search_by_location" class="form-submit-btn">Search by Location</button>
                    <p id="gps-message" class="gps-message"></p>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Include footer -->
<?php include 'footer.php'; ?>

</body>
</html>
