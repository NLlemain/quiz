<?php
// Start de sessie, dit zorgt ervoor dat we gegevens zoals de gebruikersnaam kunnen opslaan
session_start(); 

// Databaseverbinding configureren
$servername = "localhost"; // Dit is de server waar de database zich bevindt (lokale server)
$username = "root"; // De standaard gebruikersnaam voor MySQL in XAMPP
$password = ""; // Het standaard wachtwoord voor MySQL in XAMPP, meestal leeg
$dbname = "quizpro"; // De naam van de database waarmee we verbinding willen maken

// Maak de verbinding met de database
$conn = new mysqli($servername, $username, $password, $dbname);

// Controleer of de verbinding succesvol is gemaakt
if ($conn->connect_error) {
    // Als er een fout is in de verbinding, stop het script en toon de foutmelding
    die("Connection failed: " . $conn->connect_error); 
}

$error_message = ""; // Dit is een lege variabele die later gebruikt zal worden voor foutmeldingen

// Verwerk de inzending van het login formulier
if (isset($_POST['login'])) { 
    // Verkrijg de gebruikersnaam en het wachtwoord van het formulier
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Hash het wachtwoord (we gebruiken hier sha256, maar bcrypt is beter in echte toepassingen)
    $hashed_pass = hash('sha256', $pass); 

    // SQL-query die probeert de gebruiker te vinden in de database op basis van gebruikersnaam en wachtwoord
    $sql = "SELECT * FROM users WHERE username = ? AND password = ?";
    
    // Voorbereid de SQL-query voor uitvoering
    if ($stmt = $conn->prepare($sql)) { 
        // Koppel de gebruikersnaam en het gehashte wachtwoord aan de query
        $stmt->bind_param("ss", $user, $hashed_pass); 
        // Voer de query uit
        $stmt->execute(); 
        // Verkrijg het resultaat van de query
        $result = $stmt->get_result(); 
        
        // Controleer of er resultaten zijn (als er een gebruiker is met deze gebruikersnaam en wachtwoord)
        if ($result->num_rows > 0) {
            // Als de gebruiker wordt gevonden, sla de gebruikersnaam op in de sessie
            $_SESSION['username'] = $user;
            // Redirect de gebruiker naar de homepage of dashboard
            header("Location: index.php"); 
            exit(); // Stop de uitvoering van de rest van het script
        } else {
            // Als geen gebruiker wordt gevonden, stel een foutmelding in
            $error_message = "Invalid username or password."; 
        }
        $stmt->close(); // Sluit de voorbereid statement
    } else {
        // Als het voorbereiden van de query mislukt, stel een foutmelding in
        $error_message = "Database query failed."; 
    }
}

// Sluit de verbinding met de database
$conn->close(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - QuizPro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Laadt de FontAwesome iconen -->
    <style>
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
        .login-form {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 300px;
        }
        .login-form h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .login-form input {
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
        .login-form .login-button {
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
    </style>
    <script>
        // Functie om het wachtwoord zichtbaar te maken of onzichtbaar te maken
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            if (passwordInput.type === "password") {
                passwordInput.type = "text"; // Maak het wachtwoord zichtbaar
                eyeIcon.classList.remove("fa-eye-slash"); // Verwijder het gesloten oog-icoon
                eyeIcon.classList.add("fa-eye"); // Voeg het open oog-icoon toe
            } else {
                passwordInput.type = "password"; // Maak het wachtwoord onzichtbaar
                eyeIcon.classList.remove("fa-eye"); // Verwijder het open oog-icoon
                eyeIcon.classList.add("fa-eye-slash"); // Voeg het gesloten oog-icoon toe
            }
        }
    </script>
</head>
<body>
<div class="header">
    <div class="logo">QuizPro</div>
    <a href="#">Browse</a>
    <a href="#">Create</a>
    <a href="#">Teach</a>
    <a href="#">Help</a>
    <div class="right-section">
        <?php if (isset($_SESSION['username'])) { ?>
            <!-- Toon de Logout knop als de gebruiker is ingelogd -->
            <a href="logout.php" class="join-button">Logout</a>
        <?php } else { ?>
            <!-- Toon de Login en Register links als de gebruiker niet is ingelogd -->
            <a href="login.php" class="login">Log In</a>
            <a href="register.php" class="join-button">Sign Up</a>
        <?php } ?>
    </div>
</div>

<div class="content">
    <div class="login-form">
        <h1>Sign In to Your Account</h1>
        <form method="POST" action="">
            <input type="text" id="username" name="username" placeholder="Username" required>
            <div class="password-container">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <i id="eyeIcon" class="fas fa-eye-slash" onclick="togglePassword()"></i>
            </div>
            <!-- Toon een foutmelding als deze is ingesteld -->
            <?php if (isset($error_message)) { echo "<p style='color:red;'>$error_message</p>"; } ?>
            <button class="login-button" type="submit" name="login">Sign In</button>
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
