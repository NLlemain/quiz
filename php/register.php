<?php
// Start de sessie zodat we sessie-informatie kunnen opslaan
session_start();

// Verbinding maken met de database
$servername = "localhost"; // De server waarop de database draait
$username = "root"; // De standaard MySQL gebruikersnaam voor XAMPP
$password = ""; // Het standaard wachtwoord voor XAMPP MySQL, meestal leeg
$dbname = "quizpro"; // De naam van de database waarmee we verbinden

// Maak verbinding met de database
$conn = new mysqli($servername, $username, $password, $dbname);

// Controleer of de verbinding met de database is gelukt
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // Als de verbinding niet werkt, stop het script en geef de foutmelding
}

$error_message = ""; // Variabele om foutmeldingen op te slaan

// Controleer of het registratieformulier is ingediend
if (isset($_POST['register'])) {
    // Verkrijg de gegevens van het formulier
    $user = $_POST['username']; // Het gebruikersnaamveld
    $pass = $_POST['password']; // Het wachtwoordveld

    // Controleer of de gebruikersnaam al bestaat in de database
    $sql = "SELECT * FROM users WHERE username = ?"; // SQL-query om te zoeken naar een gebruiker met dezelfde gebruikersnaam
    if ($stmt = $conn->prepare($sql)) { // Bereid de SQL-query voor
        $stmt->bind_param("s", $user); // Bind de gebruikersnaamparameter aan de query
        $stmt->execute(); // Voer de query uit
        $result = $stmt->get_result(); // Verkrijg het resultaat van de query

        // Als er al een gebruiker bestaat met deze gebruikersnaam
        if ($result->num_rows > 0) {
            // Gebruiker bestaat al
            $error_message = "Username already taken."; // Zet de foutmelding
        } else {
            // Hasht het wachtwoord voor veilige opslag
            $hashed_pass = hash('sha256', $pass); // Gebruik de sha256 hashing methode voor het wachtwoord

            // Voeg de nieuwe gebruiker toe aan de database
            $sql = "INSERT INTO users (username, password) VALUES (?, ?)"; // SQL-query om een nieuwe gebruiker toe te voegen
            if ($stmt = $conn->prepare($sql)) { // Bereid de SQL-query voor
                $stmt->bind_param("ss", $user, $hashed_pass); // Bind de gebruikersnaam en het gehashte wachtwoord
                if ($stmt->execute()) { // Voer de query uit
                    // Succesvolle registratie, log de gebruiker automatisch in
                    $_SESSION['username'] = $user; // Sla de gebruikersnaam op in de sessie

                    // Zet een succesbericht voor de login
                    $_SESSION['login_message'] = "Registration successful! Welcome, " . $user;

                    // Redirect de gebruiker naar de indexpagina na registratie en login
                    header("Location: index.php");
                    exit(); // Stop de uitvoering van het script na de redirect
                } else {
                    $error_message = "Error registering user."; // Als er iets misgaat met de registratie
                }
            }
        }

        $stmt->close(); // Sluit de statement af
    } else {
        $error_message = "Database query failed."; // Als de SQL-query niet voorbereid kan worden
    }
}

// Sluit de databaseverbinding
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - QuizPro</title>
    <style>
        /* Stijlregels voor de pagina */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9fafb;
        }
        .header {
            background-color: #fff;
            padding: 20px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #ddd;
        }
        .header a {
            text-decoration: none;
            color: #111827;
            margin: 0 10px;
        }
        .header .logo {
            font-size: 24px;
            font-weight: bold;
            color: #4F46E5;
        }
        .header .right-section {
            display: flex;
            padding-left: 900px;
            align-items: center;
        }
        .header .join-button {
            background-color: #4F46E5;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .content {
            display: flex;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 80px);
            flex-direction: column;
        }
        .signup-form {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 300px;
        }
        .signup-form h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .signup-form input {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 100%;
        }
        .password-container {
            position: relative;
            width: 100%;
        }
        .password-container input {
            width: 100%;
        }
        .password-container i {
            position: absolute;
            right: -5px;
            top: 20px;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 20px;
            color: #777;
        }
        .signup-form .signup-button {
            background-color: #4F46E5;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 7px;
        }
        .footer {
            background-color: #111827;
            color: #fff;
            padding: 40px;
            text-align: center;
        }
        .footer .footer-section {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }
        .footer .footer-section div {
            width: 20%;
            text-align: left;
        }
        .footer .footer-section h4 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        .footer .footer-section p {
            font-size: 14px;
            color: #9CA3AF;
            margin: 5px 0;
        }
    </style>
</head>
<body>
<div class="header">
    <div href="index.php" class="logo">QuizPro</div>
    <a href="#">Browse</a>
    <a href="#">Create</a>
    <a href="#">Teach</a>
    <a href="#">Help</a>
    <div class="right-section">
        <?php if (isset($_SESSION['username'])) { ?>
            <a href="logout.php" class="join-button">Logout</a>
        <?php } else { ?>
            <a href="login.php" class="login">Log In</a>
            <a href="register.php" class="join-button">Sign Up</a>
        <?php } ?>
    </div>
</div>

<div class="content">
    <div class="signup-form">
        <h1>Create Your Account</h1>
        <form method="POST" action="">
            <input type="text" id="username" name="username" placeholder="Username" required>
            <div class="password-container">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <i id="eyeIcon" class="fas fa-eye-slash" onclick="togglePassword()"></i>
            </div>
            <?php if (isset($error_message)) { echo "<p style='color:red;'>$error_message</p>"; } ?>
            <button class="signup-button" type="submit" name="register">Sign Up</button>
        </form>
    </div>
</div>

<div class="footer">
    <div class="footer-section">
        <div>
            <h4>QuizPro</h4>
            <p>Making learning awesome since 2025</p>
        </div>
        <div>
            <h4>Product</h4>
            <p>Features</p>
            <p>Pricing</p>
            <p>Templates</p>
        </div>
        <div>
            <h4>Resources</h4>
            <p>Help Center</p>
            <p>Blog</p>
            <p>Community</p>
        </div>
        <div>
            <h4>Connect</h4>
            <p>Follow us on social media</p>
        </div>
    </div>
    <p>© 2025 QuizPro. All rights reserved.</p>
</div>
</body>
</html>
