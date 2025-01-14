<?php
$servername = "localhost";
$username = "root"; // Default XAMPP MySQL username
$password = ""; // Default XAMPP MySQL password (usually empty)
$dbname = "QuizPro"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert a user with hashed password
$username_to_insert = 'hello'; // Username
$password_to_insert = 'hello'; // Plaintext password

// Hash the password before inserting
$password_hashed = password_hash($password_to_insert, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (username, password) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username_to_insert, $password_hashed);

if ($stmt->execute()) {
    echo "User inserted successfully!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
