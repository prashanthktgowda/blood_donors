<?php
ob_start(); // Start output buffering to prevent any output before PDF generation
session_start();

include 'db.php';
include 'donor_dashboard_header.html';

// Check if the donor is logged in
if (!isset($_SESSION['donor_id'])) {
    header("Location: login.php");
    exit;
}

// Get the logged-in donor's ID from the session
$donor_id = $_SESSION['donor_id'];

// Handle account freezing
if (isset($_POST['freeze_account'])) {
    // Update donor's account status to frozen
    $updateQuery = "UPDATE donors SET account_frozen = TRUE WHERE id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("i", $donor_id);
    $updateStmt->execute();
    $updateStmt->close();

}

// Handle account unfreezing
if (isset($_POST['unfreeze_account'])) {
    // Update donor's account status to active
    $updateQuery = "UPDATE donors SET account_frozen = FALSE WHERE id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("i", $donor_id);
    $updateStmt->execute();
    $updateStmt->close();

}

// Fetch donor details
$query = "SELECT * FROM donors WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $donor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p>No donor found.</p>";
    exit;
}

$donor = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Dashboard</title>
    <link rel="stylesheet" href="donor_dashboard.css"> <!-- Linking common CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<?php include 'loading.php'; ?>
    <main>
        <div class="form-container">
          

            <!-- Welcome Heading -->
            <h1 class="heading">Welcome, <?php echo htmlspecialchars($donor['name']); ?></h1>

            <!-- Donor Details Form -->
            <div class="donor-details-form">
                  <!-- Edit Button -->
            <a href="edit_detail.php" class="edit-button">
                <i class="fas fa-edit"></i>
            </a>
                <h2>Donor Details</h2>
                <div class="detail-item">
                    <strong>Email:</strong> <?php echo htmlspecialchars($donor['email']); ?>
                </div>
                <div class="detail-item">
                    <strong>Mobile:</strong> +91 <?php echo htmlspecialchars($donor['mobile']); ?>
                </div>
                <div class="detail-item">
                    <strong>Blood Group:</strong> <?php echo htmlspecialchars($donor['blood_group']); ?>
                </div>
                <div class="detail-item">
                    <strong>Location:</strong> <?php echo htmlspecialchars($donor['location']); ?>
                </div>
                <div class="detail-item">
                    <strong>Registered On:</strong> <?php echo htmlspecialchars($donor['registered_at']); ?>
                </div>
            </div>

            <!-- Freeze Account Form -->
            <div class="freeze-account">
                <h2>Account Status</h2>
                <?php if ($donor['account_frozen']): ?>
                    <p>Your account is currently frozen.</p>
                    <form method="post" action="">
                        <button type="submit" name="unfreeze_account">Unfreeze Account</button>
                    </form>
                <?php else: ?>
                    <p>Your account is active.</p>
                    <form method="post" action="">
                        <button type="submit" name="freeze_account">Freeze Account</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
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
