<?php
// Instellingen voor de databaseverbinding
$servername = "localhost"; // De hostnaam van de database (meestal 'localhost')
$username = "root"; // De gebruikersnaam voor de database (standaard in XAMPP is 'root')
$password = ""; // Het wachtwoord voor de database (standaard leeg bij XAMPP)
$dbname = "quizpro"; // De naam van de database die je wilt gebruiken

// Maak verbinding met de database
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Controleer of de verbinding succesvol is
if (!$conn) { // Als de verbinding niet is gelukt...
    die("Connection failed: " . mysqli_connect_error()); // Toon een foutmelding en stop het script
}
?>

