<?php
ob_start();
session_start();

// Include PHPMailer and database connection
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';
include 'db.php';
include 'register_header.html';

$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    // Get form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $mobile = $_POST['mobile'];
    $blood_group = $_POST['blood_group'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    // Receive individual location components
    $landmark = $_POST['landmark'];
    $street = $_POST['street'];
    $area = $_POST['area'];
    $city = $_POST['city'];
    $location = trim("$landmark, $street, $area, $city");

    // Check if the email or mobile already exists
    $checkQuery = "SELECT * FROM donors WHERE email = ? OR mobile = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ss", $email, $mobile);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $errorMessage = "The email or mobile number is already registered.";
    } else {
        // If latitude and longitude are not available, predict based on address
        if (empty($latitude) || empty($longitude)) {
            $command = escapeshellcmd("python predict_location.py " . escapeshellarg($location));
            $output = shell_exec($command);
            $output = trim($output);

            if (!empty($output) && preg_match('/([-+]?\d*\.?\d+),([-+]?\d*\.?\d+)/', $output, $matches)) {
                $latitude = (float)$matches[1];
                $longitude = (float)$matches[2];
            } else {
                $errorMessage = "Error: Unable to predict coordinates for the entered address.";
            }
        }

        if (empty($errorMessage)) {
            $insertQuery = "INSERT INTO temp_donors (name, email, password, mobile, blood_group, location, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("ssssssdd", $name, $email, $password, $mobile, $blood_group, $location, $latitude, $longitude);
            if ($stmt->execute()) {
                $_SESSION['temp_email'] = $email;
                $_SESSION['verification_code'] = rand(100000, 999999);

                // Send verification email
                $mail = new PHPMailer(true);
                try {
                    $mail->isyoursmtpname();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'example@gmail.com';
                    $mail->Password = 'password';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;
                    $mail->setFrom('no-reply@yourdomain.com', 'New Life');
                    $mail->addAddress($email);
                    $mail->Subject = 'Email Verification';
                    $mail->Body = 'Your verification code is: ' . $_SESSION['verification_code'];
                    $mail->send();
                    header("Location: email_verification.php");
                    exit();
                } catch (Exception $e) {
                    $errorMessage = 'Mailer Error: ' . $mail->ErrorInfo;
                }
            } else {
                $errorMessage = "Error: " . $stmt->error;
            }
        }
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Donor</title>
    <link rel="stylesheet" href="register.css">
    <script>
        // Geolocation function to get current location
        function getCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                }, function(error) {
                    alert('Error: Unable to retrieve your location. Please ensure location services are enabled.');
                });
            } else {
                alert('Geolocation is not supported by this browser.');
            }
        }

        // Function to auto-hide error messages
        function hideErrorMessage() {
            const errorMessage = document.querySelector('.error-message');
            if (errorMessage) {
                setTimeout(() => {
                    errorMessage.style.display = 'none';
                }, 10000);
            }
        }

        window.onload = hideErrorMessage;
    </script>
</head>
<body>
    <?php if (!empty($errorMessage)): ?>
        <div class="error-message">
            <p><?php echo $errorMessage; ?></p>
            <script>
                setTimeout(function(){
                    window.location.href = "register.php";
                }, 4000);
            </script>
        </div>
    <?php else: ?>
        <main class="container">
            <h1>Register as a Donor</h1>
            <form method="POST">
                <input type="text" name="name" placeholder="Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input class="mobile-input" type="text" name="mobile" placeholder="+91-Mobile Number" maxlength="10" pattern="[0-9]{10}" title="Please enter a valid 10-digit mobile number" required>
                <select name="blood_group" required>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                </select>
                <h4>Address:</h4>
                <button type="btn" onclick="getCurrentLocation()" class="btn">Use Current Location</button><br><br>
                <input type="text" name="landmark" placeholder="Landmark" required>
                <input type="text" name="street" placeholder="Street" required>
                <input type="text" name="area" placeholder="Area" required>
                <input type="text" name="city" placeholder="City" required>

                <!-- Latitude and Longitude input fields (hidden initially) -->
                <input type="hidden" id="latitude" name="latitude">
                <input type="hidden" id="longitude" name="longitude">

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
            </select>                <button type="submit" name="register" class="btn">Register</button>
            </form>
        </main>
    <?php endif; ?>
    <?php include 'footer.php'; ?>
</body>
</html>
