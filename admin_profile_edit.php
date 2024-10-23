<?php
session_start();

// Include the database connection
include 'db.php'; // Ensure this path is correct
include 'admin_dashboard_header.php';
// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php"); // Redirect to login page if not logged in
    exit;
}

// Fetch admin details from the database
$admin_id = $_SESSION['admin_id'];
$adminQuery = "SELECT * FROM admin WHERE id = ?";
$stmt = $conn->prepare($adminQuery);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
} else {
    // Handle error if admin not found
    echo "Admin not found.";
    exit;
}

// Handle form submission
$errorMessage = '';
$successMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $mobile_number = $_POST['mobile_number'];

    // Update admin details in the database
    $updateQuery = "UPDATE admin SET username = ?, email = ?, mobile_number = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sssi", $username, $email, $mobile_number, $admin_id);

    if ($stmt->execute()) {
        $successMessage = "Profile updated successfully!";
        // Update session data
        $_SESSION['admin_name'] = $username; // Update username in session
    } else {
        $errorMessage = "Error updating profile: " . $stmt->error;
    }

    // Close statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Admin Dashboard</title>
    <link rel="stylesheet" href="admin_profile_edit.css"> <!-- Link to your CSS -->
</head>
<body>
<?php include 'loading.php'; ?>
    <main>
        <div class="container">
            <h1>Edit Profile</h1>

            <?php if (!empty($errorMessage)): ?>
                <div class="error-message">
                    <p><?php echo $errorMessage; ?></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($successMessage)): ?>
                <div class="success-message">
                    <p><?php echo $successMessage; ?></p>
                </div>
            <?php endif; ?>

            <form method="POST">
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($admin['username']); ?>" placeholder="Current name"required>

                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" placeholder="email" required>

                <input type="text" id="mobile_number" name="mobile_number" value="<?php echo htmlspecialchars($admin['mobile_number']); ?>" placeholder="+91:phone number" required>

                <button type="submit">Update Profile</button>
            </form>
    </d>
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
