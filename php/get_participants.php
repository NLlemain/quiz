<?php
session_start(); // Start een nieuwe sessie of hervat een bestaande sessie

// Instellingen voor de databaseverbinding
$servername = "localhost"; // De hostnaam van de database (meestal 'localhost' bij lokale servers zoals XAMPP)
$username = "root"; // Standaard gebruikersnaam voor MySQL in XAMPP
$password = ""; // Standaard wachtwoord voor MySQL in XAMPP (leeg)
$dbname = "quizpro"; // Naam van de database die wordt gebruikt

// Maak een nieuwe verbinding met de database
$conn = new mysqli($servername, $username, $password, $dbname);

// Controleer of de verbinding succesvol is
if ($conn->connect_error) { // Als er een fout is tijdens het verbinden...
    die("Connection failed: " . $conn->connect_error); // Toon een foutmelding en stop de uitvoering
}

// Controleer of er een 'session_code' in de URL is meegegeven
if (isset($_GET['session_code'])) {
    $session_code = $_GET['session_code']; // Haal de sessiecode op uit de URL

    // Haal de lijst van deelnemers op die horen bij de opgegeven sessiecode
    $sql_participants = "SELECT username FROM game_participants WHERE session_code = ?"; // SQL-query om gebruikersnamen op te halen
    $stmt_participants = $conn->prepare($sql_participants); // Bereid de SQL-statement voor om SQL-injecties te voorkomen

    if ($stmt_participants === false) { // Controleer of het voorbereiden van de query is gelukt
        die('Error preparing participants statement: ' . $conn->error); // Toon een foutmelding en stop het script
    }

    $stmt_participants->bind_param("s", $session_code); // Bind de sessiecode parameter aan de query
    $stmt_participants->execute(); // Voer de query uit
    $participants_result = $stmt_participants->get_result(); // Haal het resultaat van de query op

    $participants = []; // Maak een lege array om de deelnemers in op te slaan
    while ($participant = $participants_result->fetch_assoc()) { // Loop door elke rij in het resultaat
        $participants[] = $participant['username']; // Voeg de gebruikersnaam van de deelnemer toe aan de array
    }

    // Geef de deelnemerslijst terug in JSON-indeling
    echo json_encode(['participants' => $participants]); // Converteer de array naar JSON en stuur het terug naar de client
}

// Sluit de verbinding met de database
$conn->close();
?>
