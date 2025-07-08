<?php
$host = "localhost";
$dbname = "budget_tracker_db"; // name we created in phpMyAdmin
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Optional: Remove comment to confirm it's working
// echo "Connected successfully!";
?>
