<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Donation</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Record a Donation</h1>
        <form method="POST">
            <input type="number" name="donor_id" placeholder="Donor ID" required>
            <input type="number" name="requester_id" placeholder="Requester ID" required>
            <input type="text" name="blood_group" placeholder="Blood Group" required>
            <input type="text" name="location" placeholder="Location" required>
            <button type="submit">Record Donation</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $donor_id = $_POST['donor_id'];
            $requester_id = $_POST['requester_id'];
            $blood_group = $_POST['blood_group'];
            $location = $_POST['location'];
            $donation_date = date('Y-m-d');

            // Database connection
            include 'db.php';

            // Insert into donations table
            $stmt = $pdo->prepare("INSERT INTO donations (donor_id, requester_id, blood_group, location, donation_date) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$donor_id, $requester_id, $blood_group, $location, $donation_date]);

            // Update donor's availability and last donation date
            $stmt = $pdo->prepare("UPDATE donors SET availability = 0, last_donation_date = ? WHERE donor_id = ?");
            $stmt->execute([$donation_date, $donor_id]);

            echo "<p>Donation recorded successfully!</p>";
        }
        ?>
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
