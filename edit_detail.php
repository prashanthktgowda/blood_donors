<?php
ob_start();
session_start();
include 'db.php';
include 'donor_edit_header.html';

// Check if the donor is logged in
if (!isset($_SESSION['donor_id'])) {
    header("Location: login.php");
    exit;
}

// Get the logged-in donor's ID from the session
$donor_id = $_SESSION['donor_id'];

// Fetch the donor's details from the database
$query = "SELECT * FROM donors WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $donor_id);
$stmt->execute();
$result = $stmt->get_result();
$donor = $result->fetch_assoc();

$stmt->close();

// Update donor details if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $mobile = $_POST['mobile'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password


     // Receive individual location components
     $landmark = $_POST['landmark'];
     $street = $_POST['street'];
     $area = $_POST['area'];
     $city = $_POST['city'];

    $location = trim("$landmark, $street, $area, $city");


    // Update the donor's details in the database
    $updateQuery = "UPDATE donors SET name = ?, mobile = ?, location = ?, password = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("ssssi", $name, $mobile, $location,$password, $donor_id);
    
    if ($updateStmt->execute()) {
        // Redirect back to the dashboard with a success message
        $_SESSION['message'] = "Details updated successfully.";
        header("Location: dashboard.php");
        exit;
    } else {
        $error_message = "Error updating details. Please try again.";
    }
    
    $updateStmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Donor Details</title>
    <link rel="stylesheet" href="edit_detail.css"> <!-- Linking common CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    
        <main class="container">
            <h1 class="heading">Edit Your Details</h1>
            <?php if (isset($error_message)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            <form action="edit_detail.php" method="post">
                <div class="form-group">
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($donor['name']); ?>" placeholder="Name" required>
                </div>
                <div class="form-group">
                    <input class="mobile-input" type="text" name="mobile" placeholder="+91-Mobile Number" maxlength="10" pattern="[0-9]{10}" title="Please enter a valid 10-digit mobile number"  required>
            </div>
                <div class="form-group">
                    <input type="password" id="password" name="password" placeholder="password" required>
                    </div>
                    <div class="form-group">
                    <h4>Address:</h4>
                    <div class="form-group">
            <input type="text" name="landmark" placeholder="Landmark" required>
            <input type="text" name="street" placeholder="Street" required>
            <input type="text" name="area" placeholder="Area" required>
            <input type="text" name="city" placeholder="City" required>
            <select name="state" required>
                <option value="Andhra Pradesh">Andhra Pradesh</option>
                <option value="Arunachal Pradesh">Arunachal Pradesh</option>
                <option value="Assam">Assam</option>
                <option value="Bihar">Bihar</option>
                <option value="Chhattisgarh">Chhattisgarh</option>
                <option value="Goa">Goa</option>
                <option value="Gujarat">Gujarat</option>
                <option value="Haryana">Haryana</option>
                <option value="Himachal Pradesh">Himachal Pradesh</option>
                <option value="Jharkhand">Jharkhand</option>
                <option value="Karnataka">Karnataka</option>
                <option value="Kerala">Kerala</option>
                <option value="Madhya Pradesh">Madhya Pradesh</option>
                <option value="Maharashtra">Maharashtra</option>
                <option value="Manipur">Manipur</option>
                <option value="Meghalaya">Meghalaya</option>
                <option value="Mizoram">Mizoram</option>
                <option value="Nagaland">Nagaland</option>
                <option value="Odisha">Odisha</option>
                <option value="Punjab">Punjab</option>
                <option value="Rajasthan">Rajasthan</option>
                <option value="Sikkim">Sikkim</option>
                <option value="Tamil Nadu">Tamil Nadu</option>
                <option value="Telangana">Telangana</option>
                <option value="Tripura">Tripura</option>
                <option value="Uttar Pradesh">Uttar Pradesh</option>
                <option value="Uttarakhand">Uttarakhand</option>
                <option value="West Bengal">West Bengal</option>
                <option value="Andaman and Nicobar Islands">Andaman and Nicobar Islands</option>
                <option value="Chandigarh">Chandigarh</option>
                <option value="Dadra and Nagar Haveli and Daman and Diu">Dadra and Nagar Haveli and Daman and Diu</option>
                <option value="Lakshadweep">Lakshadweep</option>
                <option value="Delhi">Delhi</option>
                <option value="Puducherry">Puducherry</option>
            </select>
            </div>
            
                <button type="submit" class="btn">Update Details</button>
            </form>
        </main>
    </main>    
    <?php include 'footer.php'; ?>
</body>
</html>
