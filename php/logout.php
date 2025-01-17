<?php
// Start de sessie
session_start(); 

// Vernietig alle sessiegegevens om de gebruiker uit te loggen
session_unset(); // Verwijdert alle variabelen die in de sessie zijn opgeslagen
session_destroy(); // Vernietigt de sessie zelf

// Redirect de gebruiker naar de inlogpagina
header("Location: login.php"); // Verstuurt de gebruiker naar de 'login.php' pagina
exit(); // Zorgt ervoor dat het script wordt gestopt na de redirect
?>
