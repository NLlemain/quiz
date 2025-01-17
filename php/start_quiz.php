<?php
session_start();  // Start de sessie om de loginstatus te controleren

// Verbind met de database
$servername = "localhost";
$username = "root"; // Standaard XAMPP MySQL gebruikersnaam
$password = ""; // Standaard XAMPP MySQL wachtwoord
$dbname = "quizpro"; // De naam van de database

// Maak de verbinding
$conn = new mysqli($servername, $username, $password, $dbname);

// Controleer of de verbinding succesvol is
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

// Controleer of de gebruiker is ingelogd door de sessie te controleren
if (!isset($_SESSION['username'])) {
    // Als de gebruiker niet is ingelogd, wordt hij doorgestuurd naar de inlogpagina
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];  // Verkrijg de gebruikersnaam van de ingelogde gebruiker

// Verkrijg de sessiecode uit de URL
if (isset($_GET['session_code'])) {
    $session_code = $_GET['session_code'];

    // Haal de sessiegegevens op via de sessiecode
    $sql = "SELECT * FROM game_sessions WHERE session_code = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die('Fout bij het voorbereiden van de statement: ' . $conn->error);
    }

    $stmt->bind_param("s", $session_code);
    $stmt->execute();
    $result = $stmt->get_result();
    $session = $result->fetch_assoc();

    if ($session) {
        $game_id = $session['game_id'];
        $host_username = $session['host_username'];
        $game_state = $session['game_state'];

        // Als de ingelogde gebruiker geen host is, moet deze alleen de "Join" knop zien
        if ($username != $host_username) {
            $is_host = false;
        } else {
            $is_host = true;
        }

        // Controleer of de gebruiker al deelnemer is in de sessie
        $sql_check_participant = "SELECT * FROM game_participants WHERE session_code = ? AND username = ?";
        $stmt_check_participant = $conn->prepare($sql_check_participant);

        if ($stmt_check_participant === false) {
            die('Fout bij het voorbereiden van de statement om deelnemer te controleren: ' . $conn->error);
        }

        $stmt_check_participant->bind_param("ss", $session_code, $username);
        $stmt_check_participant->execute();
        $participant_result = $stmt_check_participant->get_result();

        // Als de gebruiker nog geen deelnemer is, voeg deze dan toe aan de database
        if ($participant_result->num_rows === 0) {
            $sql_insert_participant = "INSERT INTO game_participants (session_code, username) VALUES (?, ?)";
            $stmt_insert_participant = $conn->prepare($sql_insert_participant);

            if ($stmt_insert_participant === false) {
                die('Fout bij het voorbereiden van de statement om deelnemer in te voegen: ' . $conn->error);
            }

            $stmt_insert_participant->bind_param("ss", $session_code, $username);
            $stmt_insert_participant->execute();
        }

        // Haal de lijst van deelnemers op
        $sql_participants = "SELECT username FROM game_participants WHERE session_code = ?";
        $stmt_participants = $conn->prepare($sql_participants);

        if ($stmt_participants === false) {
            die('Fout bij het voorbereiden van de statement voor deelnemers: ' . $conn->error);
        }

        $stmt_participants->bind_param("s", $session_code);
        $stmt_participants->execute();
        $participants_result = $stmt_participants->get_result();
        $participants = [];
        while ($participant = $participants_result->fetch_assoc()) {
            $participants[] = $participant['username'];
        }
    } else {
        echo "Sessie niet gevonden.";
        exit();
    }
} else {
    echo "Sessiecode niet opgegeven.";
    exit();
}

// Verwerk het starten van het spel (alleen toegestaan voor de host)
if (isset($_POST['start_game'])) {
    if ($is_host) {
        // Werk de spelstatus bij naar 'in_progress'
        $sql_update = "UPDATE game_sessions SET game_state = 'in_progress' WHERE session_code = ?";
        $stmt_update = $conn->prepare($sql_update);

        if ($stmt_update === false) {
            die('Fout bij het voorbereiden van de statement om het spel te starten: ' . $conn->error);
        }

        $stmt_update->bind_param("s", $session_code);
        $stmt_update->execute();

        // Stuur door naar de spelpagina (of een pagina waar het spel zal worden gespeeld)
        header("Location: play_game.php?session_code=$session_code");
        exit();
    } else {
        echo "Alleen de host kan het spel starten.";
    }
}

// Verwerk het verlaten van de sessie door de deelnemer
if (isset($_GET['leave'])) {
    $sql_remove_participant = "DELETE FROM game_participants WHERE session_code = ? AND username = ?";
    $stmt_remove_participant = $conn->prepare($sql_remove_participant);

    if ($stmt_remove_participant === false) {
        die('Fout bij het voorbereiden van de statement om deelnemer te verwijderen: ' . $conn->error);
    }

    $stmt_remove_participant->bind_param("ss", $session_code, $username);
    $stmt_remove_participant->execute();

    header("Location: waiting_room.php?session_code=$session_code"); // Doorsturen na verwijdering
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wachtkamer - QuizPro</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            color: #333;
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
        .header .login {
            color: #4F46E5;
        }
        .header .right-section {
            display: flex;
            padding-left: 900px;
            align-items: center;
        }
        .header .right-section img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }
        h2 {
            text-align: center;
            font-size: 28px;
            margin-top: 40px;
            color: #4F46E5;
        }
        .waiting-room {
            text-align: center;
            margin-top: 50px;
        }
        .waiting-room .session-code {
            font-size: 24px;
            font-weight: bold;
            color: #4F46E5;
            margin-bottom: 20px;
        }
        .waiting-room .participants {
            margin: 20px 0;
        }
        .waiting-room .participants ul {
            list-style-type: none;
            padding: 0;
        }
        .waiting-room .participants li {
            font-size: 18px;
            margin-bottom: 10px;
            color: #333;
        }
        .waiting-room button {
            background-color: #4F46E5;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .waiting-room button:hover {
            background-color: #3B41D5;
        }
        footer {
            background-color: #111827;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 40px;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo">QuizPro</div>
        <div>
            <a href="#">Browse</a>
            <a href="#">Create</a>
            <a href="#">Teach</a>
            <a href="#">Help</a>
        </div>
        <div class="right-section">
            <?php if (isset($_SESSION['username'])) { ?>
                <a href="logout.php" class="join-button">Logout</a>
            <?php } else { ?>
                <a href="login.php" class="join-button">Log In</a>
                <a href="register.php" class="join-button">Sign Up</a>
            <?php } ?>
        </div>
    </div>

    <div class="waiting-room">
        <h2>Wachtkamer</h2>
        <p class="session-code">Sessiecode: <?php echo htmlspecialchars($session_code); ?></p>
        
        <div class="participants">
            <h3>Deelnemers:</h3>
            <ul id="participants-list">
                <?php foreach ($participants as $participant) { ?>
                    <li><?php echo htmlspecialchars($participant); ?></li>
                <?php } ?>
            </ul>
        </div>
        
        <p>
            <?php if ($is_host) { ?>
                <form method="POST" action="">
                    <button type="submit" name="start_game">Start het spel</button>
                </form>
            <?php } else { ?>
                <button>Wachten op de host om het spel te starten...</button>
            <?php } ?>
        </p>

        <a href="?session_code=<?php echo htmlspecialchars($session_code); ?>&leave=true" style="color: red;">Verlaat de sessie</a>
    </div>

    <script>
        const sessionCode = "<?php echo htmlspecialchars($session_code); ?>";  // Verkrijg de sessiecode dynamisch

        // Poll elke 2 seconden om de bijgewerkte deelnemerslijst te controleren
        setInterval(function () {
            fetch(`get_participants.php?session_code=${sessionCode}`)
                .then(response => response.json())
                .then(data => {
                    const participantsList = document.getElementById('participants-list');
                    participantsList.innerHTML = '';

                    data.participants.forEach(participant => {
                        const listItem = document.createElement('li');
                        listItem.textContent = participant;
                        participantsList.appendChild(listItem);
                    });
                });
        }, 2000); // Poll elke 2 seconden

        // Meld de server wanneer de gebruiker de pagina verlaat
        window.addEventListener('beforeunload', function () {
            fetch(`leave_participant.php?session_code=${sessionCode}&username=${"<?php echo $username; ?>"}&leave=true`, {
                method: 'GET',
            });
        });
    </script>

</body>
</html>

<?php
// Sluit de verbinding
$conn->close();
?>
