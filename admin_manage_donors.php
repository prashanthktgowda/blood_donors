<?php
ob_start();
session_start();

// Include the database connection
include 'db.php'; // Ensure this path is correct
include 'admin_manage_donors_header.html'; // Your header file

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php"); // Redirect to login page if not logged in
    exit;
}

// Fetch all donors, ordered by the most recent donation date
$donorsQuery = "SELECT d.id, d.name, d.email, d.mobile, dh.donation_date 
                FROM donors d 
                LEFT JOIN donation_history dh ON d.id = dh.donor_id 
                ORDER BY dh.donation_date DESC";
$result = $conn->query($donorsQuery);

// Handle donor removal
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_donor'])) {
    $donor_id = $_POST['donor_id'];

    // Delete the donor from the donors table
    $deleteQuery = "DELETE FROM donors WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $donor_id);

    if ($stmt->execute()) {
        echo "<script>alert('Donor removed successfully!');</script>";
    } else {
        echo "<script>alert('Error removing donor: " . $stmt->error . "');</script>";
    }

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
    <title>Manage Donors - Admin Dashboard</title>
    <link rel="stylesheet" href="admin_manage_donors.css"> <!-- Link to your CSS -->
</head>
<body>
    <div class="wrapper">
    <?php include 'loading.php'; ?>
        <main>
            
            <div class="admin-container">
                <h1>Manage Donors</h1>

                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile Number</th>
                            <th>Last Donation Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($donor = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($donor['name']); ?></td>
                                    <td><?php echo htmlspecialchars($donor['email']); ?></td>
                                    <td><?php echo htmlspecialchars($donor['mobile']); ?></td>
                                    <td><?php echo htmlspecialchars($donor['donation_date'] ? $donor['donation_date'] : 'Never Donated'); ?></td>
                                    <td>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="donor_id" value="<?php echo $donor['id']; ?>">
                                            <button type="submit" name="remove_donor" onclick="return confirm('Are you sure you want to remove this donor?');">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No donors found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

            </div>
        </main>
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

    <?php include 'footer.php'; ?> <!-- Ensure the footer is included after the main content -->
</body>
</html>
