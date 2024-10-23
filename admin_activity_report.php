<?php
session_start();
include 'db.php'; // Database connection
include 'admin_activity_header.html';

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Fetch admin activity from the database
$admin_id = $_SESSION['admin_id'];
$activityQuery = "SELECT login_time, logout_time, ip_address FROM admin_activity WHERE admin_id = ? ORDER BY login_time DESC";
$stmt = $conn->prepare($activityQuery);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Activity Report</title>
    <link rel="stylesheet" href="admin_activity_report.css">
    </head>
    <body class="admin-activity-report-body">
    <?php include 'loading.php'; ?>
    <main>
        <div class="admin-activity-report-container">
            <h1 class="admin-activity-report-heading">Admin Activity Report</h1>
            <table class="admin-activity-report-table">
                <thead>
                    <tr>
                        <th>Login Time</th>
                        <th>Logout Time</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Table rows with data here -->
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['login_time']); ?></td>
                            <td><?php echo htmlspecialchars($row['logout_time']); ?></td>
                            <td><?php echo htmlspecialchars($row['ip_address']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
            </table>
        </div>
    </main>
</body>
</html>

<?php
include 'footer.php';
// Close the statement and connection
$stmt->close();
$conn->close();
?>
