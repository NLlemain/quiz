<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root"; // Default XAMPP MySQL username
$password = ""; // Default XAMPP MySQL password
$dbname = "quizpro";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if session code and username are provided
if (isset($_GET['session_code']) && isset($_GET['username'])) {
    $session_code = $_GET['session_code'];
    $username = $_GET['username'];

    // Remove the participant from the session
    $sql_remove_participant = "DELETE FROM game_participants WHERE session_code = ? AND username = ?";
    $stmt_remove_participant = $conn->prepare($sql_remove_participant);
    
    if ($stmt_remove_participant === false) {
        die('Error preparing remove participant statement: ' . $conn->error);
    }

    $stmt_remove_participant->bind_param("ss", $session_code, $username);
    $stmt_remove_participant->execute();
}

$conn->close();
?>
