<?php
session_start(); // Start een PHP-sessie om gebruikersgegevens of sessie-informatie op te slaan

// Databaseverbinding configureren
$servername = "localhost"; // Hostnaam voor de database, standaard "localhost" voor XAMPP
$username = "root"; // Standaard gebruikersnaam voor MySQL in XAMPP
$password = ""; // Standaard wachtwoord voor MySQL in XAMPP (meestal leeg)
$dbname = "quizpro"; // De naam van de database die je wilt gebruiken

// Maak een nieuwe databaseverbinding met MySQLi
$conn = new mysqli($servername, $username, $password, $dbname);

// Controleer of de databaseverbinding succesvol is
if ($conn->connect_error) { 
    // Als er een fout optreedt, beëindig het script en toon een foutmelding
    die("Connection failed: " . $conn->connect_error); 
}

// Controleer of de vereiste parameters `session_code` en `username` zijn meegegeven in de URL
if (isset($_GET['session_code']) && isset($_GET['username'])) {
    $session_code = $_GET['session_code']; // Haal de sessiecode op uit de URL
    $username = $_GET['username']; // Haal de gebruikersnaam op uit de URL

    // SQL-query om een deelnemer te verwijderen uit de sessie op basis van sessiecode en gebruikersnaam
    $sql_remove_participant = "DELETE FROM game_participants WHERE session_code = ? AND username = ?";
    
    // Bereid de SQL-query voor met een prepared statement
    $stmt_remove_participant = $conn->prepare($sql_remove_participant);
    
    if ($stmt_remove_participant === false) {
        // Als het voorbereiden van de statement mislukt, stop de uitvoering en toon een foutmelding
        die('Error preparing remove participant statement: ' . $conn->error);
    }

    // Koppel de sessiecode en gebruikersnaam als parameters aan de query
    $stmt_remove_participant->bind_param("ss", $session_code, $username); 
    // "ss" betekent dat beide parameters strings zijn

    // Voer de query uit om de deelnemer te verwijderen
    $stmt_remove_participant->execute(); 
}

// Sluit de verbinding met de database
$conn->close();
?>

