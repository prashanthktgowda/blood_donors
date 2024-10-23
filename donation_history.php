<?php
ob_start(); // Start output buffering
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['donor_id'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';
include 'donation_history_header.html';
require_once('C:\xampp1\htdocs\BloodBank\vendor\composer\vendor\tecnickcom\tcpdf\tcpdf.php');

// Fetch the donation history
$donor_id = $_SESSION['donor_id'];
$query = "SELECT id, donation_date, hospital, blood_quantity FROM donation_history WHERE donor_id = ? ORDER BY donation_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $donor_id);
$stmt->execute();
$result = $stmt->get_result();

// Store donation details
$donations = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Calculate days left for next donation
$current_date = new DateTime();
$days_left = null;

if (!empty($donations)) {
    $last_donation_date = new DateTime($donations[0]['donation_date']);
    $interval = $current_date->diff($last_donation_date);

    if ($interval->days < 84) {
        $days_left = 84 - $interval->days;
    }
}

// Generate certificate function
function generate_certificate($donation) {
    ob_end_clean(); // Clean output buffer

    $donor_name = $_SESSION['donor_name'];
    $hospital = htmlspecialchars($donation['hospital']);
    $donation_date = htmlspecialchars($donation['donation_date']);
    $blood_quantity = htmlspecialchars($donation['blood_quantity']);

    // Create PDF document
    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Blood Donation System');
    $pdf->SetTitle('Donation Certificate');
    $pdf->SetHeaderData('', 0, 'Donation Certificate', "Blood Donation Appreciation\nGenerated on " . date('Y-m-d'));
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->AddPage();

    // PDF content
    $html = "
    <h1 style='text-align: center;'>Appreciation Certificate</h1>
    <p style='text-align: center;'>This certifies that <strong>$donor_name</strong></p>
    <p style='text-align: center;'>has donated <strong>$blood_quantity ml</strong> of blood at <strong>$hospital</strong></p>
    <p style='text-align: center;'>on <strong>$donation_date</strong>.</p>
    <p style='text-align: center;'>Thank you for your contribution!</p>
    <p style='text-align: center; border: 2px solid black; width: 150px; margin: 0 auto;'>Seal</p>";

    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output("certificate_$donation_date.pdf", 'D');
    exit(); // Stop further execution
}

// Check if donation_id is set for certificate generation
if (isset($_GET['donation_id'])) {
    $donation_id = $_GET['donation_id'];
    $query = "SELECT donation_date, hospital, blood_quantity FROM donation_history WHERE id = ? AND donor_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $donation_id, $donor_id);
    $stmt->execute();
    $donation = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($donation) {
        generate_certificate($donation); // Generate the certificate
    }
}

$conn->close();
ob_end_flush(); // Flush output buffer
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation History</title>
    <link rel="stylesheet" href="donation_history.css"> <!-- Link to your CSS file -->
</head>
<body>

    <main class="history-container">
        <h1>Your Donation History</h1>

        <?php if (!empty($donations)): ?>
            <div>
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Date of Donation</th>
                        <th>Hospital</th>
                        <th>Units Donated (ml)</th>
                        <th>Days Left for Next Donation</th>
                        <th>Certificate</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donations as $donation): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($donation['donation_date']); ?></td>
                            <td><?php echo htmlspecialchars($donation['hospital']); ?></td>
                            <td><?php echo htmlspecialchars($donation['blood_quantity']); ?> ml</td>
                            <td>
                                <?php
                                $donation_date = new DateTime($donation['donation_date']);
                                $interval = $current_date->diff($donation_date);
                                echo ($interval->days < 84) ? (84 - $interval->days) . ' days' : 'Eligible for donation';
                                ?>
                            </td>
                            <td>
                                <a href="?donation_id=<?php echo $donation['id']; ?>" class="download_btn">PDF</a>
                            </td>
                        </tr>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if (!is_null($days_left)): ?>
                <div class="next-donation">
                    You have <?php echo $days_left; ?> days left until your next eligible donation.
                </div>
            <?php endif; ?>
        <?php else: ?>
            <p>No donation history found.</p>
        <?php endif; ?>
    </main>

    <script>
        // Dark mode toggle functionality
        const toggleButton = document.getElementById('dark-mode-toggle');
        if (toggleButton) {
            toggleButton.addEventListener('click', () => {
                document.body.classList.toggle('dark-mode');
                localStorage.setItem('darkMode', document.body.classList.contains('dark-mode') ? 'enabled' : 'disabled');
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
