<?php
$servername = "localhost"; // De hostnaam van de database (meestal 'localhost' bij gebruik van XAMPP)
$username = "root"; // Standaard gebruikersnaam voor MySQL in XAMPP
$password = ""; // Standaard wachtwoord voor MySQL in XAMPP (meestal leeg)
$dbname = "QuizPro"; // Naam van de database die je wilt gebruiken

// Maak een verbinding met de database
$conn = new mysqli($servername, $username, $password, $dbname); // Maak een nieuwe MySQLi-verbinding

// Controleer of de verbinding succesvol is
if ($conn->connect_error) { // Controleer of er een fout is bij het verbinden
    die("Connection failed: " . $conn->connect_error); // Toon een foutmelding en stop de uitvoering
}

// Voeg een gebruiker toe met een gehashte wachtwoord
$username_to_insert = 'hello'; // De gebruikersnaam die in de database wordt ingevoerd
$password_to_insert = 'hello'; // Het gewone (plaintext) wachtwoord dat later wordt gehashd

// Hash het wachtwoord voordat het wordt opgeslagen
$password_hashed = password_hash($password_to_insert, PASSWORD_DEFAULT); 
// `password_hash` is een veilige manier om wachtwoorden te hashen en maakt gebruik van een standaard algoritme (zoals bcrypt)

// SQL-query om een nieuwe gebruiker in de database in te voegen
$sql = "INSERT INTO users (username, password) VALUES (?, ?)"; 
// De `?` zijn plaatsaanduidingen die later worden vervangen door de daadwerkelijke waarden

$stmt = $conn->prepare($sql); // Bereid de SQL-query voor met een prepared statement
$stmt->bind_param("ss", $username_to_insert, $password_hashed); 
// Koppel de waarden aan de plaatsaanduidingen:
// - "ss" betekent dat beide waarden strings zijn
// - `$username_to_insert` wordt gekoppeld aan de eerste `?`
// - `$password_hashed` wordt gekoppeld aan de tweede `?`

// Voer de SQL-query uit
if ($stmt->execute()) { 
    // Als de query succesvol wordt uitgevoerd, laat een succesbericht zien
    echo "User inserted successfully!";
} else {
    // Als er een fout optreedt, toon de foutmelding
    echo "Error: " . $stmt->error;
}

// Sluit de prepared statement om geheugen vrij te maken
$stmt->close(); 

// Sluit de verbinding met de database
$conn->close();
?>

