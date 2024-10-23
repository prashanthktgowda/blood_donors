<?php
$host = 'localhost';   // Update with your database host
$db = 'blood_bank';    // Updated database name
$user = 'root';        // Update with your database username
$pass = 'root';            // Update with your database password

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

 //Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?> 