<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Predict Latitude and Longitude</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #f0f0f0;
        }
        h1 {
            margin-bottom: 20px;
        }
        form {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        input[type="text"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 10px;
            width: 300px;
        }
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            background-color: #007BFF;
            color: #fff;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 4px;
            width: 300px;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Enter Address to Predict Latitude and Longitude</h1>

    <!-- Form to Take Address Input -->
    <form method="POST">
        <input type="text" name="address" placeholder="Enter Address" required>
        <button type="submit">Get Coordinates</button>
    </form>

    <?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the entered address from the form and process it (lowercase, remove whitespace)
    $address = strtolower(trim($_POST['address']));
    $address = str_replace(' ', ',', $address);
    $address = ltrim($address);

    // Print the address for debugging purposes
    echo "<p>Address sent to Python: $address</p>";

    // Build the command with the processed address
    $command = escapeshellcmd("python predict_loc.py " . escapeshellarg($address) . " 2>&1");
    // Execute the Python script and capture the output
    $output = shell_exec($command);

    // Print the command executed and the output received from Python for debugging purposes
    echo "<pre>Command Executed: $command\nOutput Received from Python: $output</pre>";

    // Check if the output contains the latitude and longitude
    if (!empty($output) && strpos($output, ',') !== false) {
        list($latitude, $longitude) = explode(",", trim($output));
        echo "<div class='message success-message'>Predicted Coordinates: Latitude: $latitude, Longitude: $longitude</div>";
    } else {
        echo "<div class='message error-message'>Error: Unable to predict coordinates for the entered address.</div>";
    }
}
?>

</body>
</html>
