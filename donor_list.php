<?php
session_start();

include 'db.php';
include 'donor_list_header.html';

// Retrieve the donors from the session
$donors = isset($_SESSION['donors']) ? $_SESSION['donors'] : [];

// Fetch the donation history to sort the donors based on eligibility
$donorData = [];

foreach ($donors as $donor) {
    $donor_id = $donor['id'];

    // Query to get the last donation date and the total number of donations
    $query = "SELECT MAX(donation_date) AS last_donation, COUNT(*) AS total_donations 
              FROM donation_history 
              WHERE donor_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $donor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $history = $result->fetch_assoc();

    // Calculate the days since the last donation
    $lastDonationDate = $history['last_donation'];
    $totalDonations = $history['total_donations'];
    $daysSinceLastDonation = $lastDonationDate ? (new DateTime())->diff(new DateTime($lastDonationDate))->days : null;

    // Store the donor details, including donation history
    $donorData[] = [
        'details' => $donor,
        'last_donation' => $lastDonationDate,
        'total_donations' => $totalDonations,
        'days_since_last' => $daysSinceLastDonation
    ];

    $stmt->close();
}

// Sort donors by the gap from last donation (ascending order)
usort($donorData, function ($a, $b) {
    return ($a['days_since_last'] ?? 0) - ($b['days_since_last'] ?? 0);
});

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Donors</title>
    <link rel="stylesheet" href="donor_list_ios.css">
</head>
<body>
<?php include 'loading.php'; ?>
    <h2>Available Donors</h2>
    
    <div class="donor-grid">
        <?php
        if (!empty($donorData)) {
            foreach ($donorData as $donorInfo) {
                $donor = $donorInfo['details'];
                $lastDonation = $donorInfo['last_donation'];
                $totalDonations = $donorInfo['total_donations'];
                
                echo '<div class="donor-card">';
                echo '<div class="donor-info">';
                echo '<h2>' . htmlspecialchars($donor['name']) . '</h2>';
                echo '<p>Blood Group: <span class="blood-group">' . htmlspecialchars($donor['blood_group']) . '</span></p>';
                echo '<p>Contact: <span class="donor-contact">' . htmlspecialchars($donor['mobile']) . '</span></p>';
                echo '<p>Total Donations: ' . htmlspecialchars($totalDonations) . '</p>';
                echo '<p>Last Donation: ' . ($lastDonation ? htmlspecialchars($lastDonation) : 'N/A') . '</p>';
                echo '<button class="call-btn" onclick="makeCall(\'' . htmlspecialchars($donor['mobile']) . '\')">Call</button>';
                echo '</div></div>';
            }
        } else {
            echo '<p>No donors found.</p>';
        }
        ?>
    </div>

    <script>
        function makeCall(contact) {
            window.location.href = 'tel:' + contact; // Open the phone dialer
        }

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
