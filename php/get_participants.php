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

if (isset($_GET['session_code'])) {
    $session_code = $_GET['session_code'];

    // Fetch the list of participants
    $sql_participants = "SELECT username FROM game_participants WHERE session_code = ?";
    $stmt_participants = $conn->prepare($sql_participants);

    if ($stmt_participants === false) {
        die('Error preparing participants statement: ' . $conn->error);
    }

    $stmt_participants->bind_param("s", $session_code);
    $stmt_participants->execute();
    $participants_result = $stmt_participants->get_result();

    $participants = [];
    while ($participant = $participants_result->fetch_assoc()) {
        $participants[] = $participant['username'];
    }

    // Return the participants as JSON
    echo json_encode(['participants' => $participants]);
}

$conn->close();
?>
