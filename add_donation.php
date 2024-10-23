<?php
session_start();
include 'add_donation_header.html'; // Include the header file

include 'db.php';

// Establish connection
$mysqli = new mysqli($host, $user, $pass, $db);

// Check if connection is successful
if ($mysqli->connect_error) {
    die('Database connection error: ' . $mysqli->connect_error);
}

$donor_id = $_SESSION['donor_id']; // Assume donor_id is stored in session after login
$error = '';
$success = '';
$remaining_days = 0;

// Check last donation date
$query = "SELECT MAX(donation_date) as last_donation_date FROM donation_history WHERE donor_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $donor_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$last_donation_date = $row['last_donation_date'];

if ($last_donation_date) {
    $last_date = new DateTime($last_donation_date);
    $current_date = new DateTime();
    $interval = $last_date->diff($current_date);
    $days_since_last_donation = $interval->days;

    // Check if it's less than 84 days since last donation
    if ($days_since_last_donation < 84) {
        $remaining_days = 84 - $days_since_last_donation;
        $error = "You cannot donate yet. Please wait $remaining_days more days.";
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $remaining_days === 0) {
    $hospital = $_POST['hospital_name']; // Corrected form field name
    $blood_quantity = $_POST['blood_quantity']; 
    $receiver_name = !empty($_POST['receiver_name']) ? $_POST['receiver_name'] : null; // Optional field, allow null value

    // Insert new donation record
    $donation_date = date('Y-m-d');
    $query = "INSERT INTO donation_history (donor_id, hospital, receiver_name, blood_quantity, donation_date) VALUES (?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("issis", $donor_id, $hospital, $receiver_name, $blood_quantity, $donation_date); // Fix binding order and types

    if ($stmt->execute()) {
        $success = "Donation recorded successfully!";
    } else {
        $error = "Error recording donation: " . $stmt->error; // Output detailed error
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Donation</title>
    <link rel="stylesheet" href="add_donation.css">
</head>
<body>
<?php include 'loading.php'; ?>
<!-- Main container -->
<main class="container">
    <h2>Add Donation</h2>

    <?php if ($error): ?>
        <div class="error-message" style="color: red;"><?php echo $error; ?></div>
    <?php elseif ($success): ?>
        <div class="success-message" style="color: green;"><?php echo $success; ?></div>
    <?php endif; ?>

    <form action="add_donation.php" method="post">
        
            <input type="text" id="hospital_name" name="hospital_name" placeholder="Hospital name" required> <!-- Field name corrected -->
        
            <input type="number" id="units_donated" name="blood_quantity" min="1" placeholder="Units donated" required> <!-- Placeholder corrected -->
        
            <input type="text" id="receiver_name" name="receiver_name" placeholder="Receiver Name (optional)">
        
        
        <button class="button" type="submit">Submit Donation</button>
    </form>
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
