<?php
// Database connection settings
$servername = "localhost"; // Database host
$username = "root"; // Database username
$password = ""; // Database password (default is empty for XAMPP)
$dbname = "quizpro"; // Name of your database

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
