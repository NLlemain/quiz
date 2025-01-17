<?php
session_start();  // Start de sessie om te controleren of de gebruiker is ingelogd

// Database verbinding instellen
$servername = "localhost";  // De naam van de server waarop de database draait
$username = "root"; // De standaardgebruikersnaam voor MySQL in XAMPP
$password = ""; // Het standaardwachtwoord voor MySQL in XAMPP
$dbname = "quizpro"; // De naam van de database waar de quizgegevens worden opgeslagen

// Verbind met de database
$conn = new mysqli($servername, $username, $password, $dbname);

// Controleer of de verbinding is gelukt
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // Stop script als verbinding mislukt
}

// Controleer of de gebruiker is ingelogd door te kijken naar de sessie
if (!isset($_SESSION['username'])) {
    // Als de gebruiker niet is ingelogd, stuur door naar de loginpagina
    header('Location: login.php');
    exit(); // Stop verdere uitvoer van dit script
}

$username = $_SESSION['username'];  // Haal de gebruikersnaam van de ingelogde gebruiker op

// Optioneel: Haal aanvullende gebruikersinformatie op uit de `users` tabel
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql); // Bereid de SQL-query voor
$stmt->bind_param("s", $username); // Verbind de parameter met de gebruikersnaam
$stmt->execute(); // Voer de query uit
$result = $stmt->get_result(); // Haal het resultaat op
$user = $result->fetch_assoc(); // Zet de resultaten om in een associatieve array

// Haal de quizzen van de gebruiker op uit de `games` tabel
$sql_quizzes = "SELECT * FROM games WHERE username = ?";
$stmt_quizzes = $conn->prepare($sql_quizzes);
$stmt_quizzes->bind_param("s", $username); // Verbind de gebruikersnaam
$stmt_quizzes->execute(); // Voer de query uit
$quizzes = $stmt_quizzes->get_result(); // Haal het resultaat op

// Logica voor het starten van een quiz
if (isset($_POST['start_quiz'])) {
    $game_id = $_POST['game_id']; // Haal het game-ID van de te starten quiz op
    
    // Genereer een unieke 9-cijferige code voor de quizsessie
    do {
        $session_code = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 9);
        // Controleer of de code al bestaat in de `game_sessions` tabel
        $check_code = "SELECT * FROM game_sessions WHERE session_code = ?";
        $stmt_check_code = $conn->prepare($check_code);
        $stmt_check_code->bind_param("s", $session_code);
        $stmt_check_code->execute();
        $stmt_check_code->store_result();
    } while ($stmt_check_code->num_rows > 0); // Blijf een nieuwe code genereren totdat deze uniek is
    
    // Voeg een nieuwe gamesessie toe aan de `game_sessions` tabel
    $sql_start_quiz = "INSERT INTO game_sessions (game_id, host_username, session_code, game_state) 
                       VALUES (?, ?, ?, 'waiting')";
    $stmt_start_quiz = $conn->prepare($sql_start_quiz);
    $stmt_start_quiz->bind_param("iss", $game_id, $username, $session_code); // Bind de parameters
    $stmt_start_quiz->execute(); // Voer de query uit
    $stmt_start_quiz->close(); // Sluit de statement
    
    // Redirect naar de startpagina van de sessie met de gegenereerde sessiecode
    header("Location: start_quiz.php?session_code=$session_code");
    exit();
}
?>
