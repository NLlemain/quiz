<?php
session_start();  // Start the session to check the login status

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

// Check if the user is logged in by checking the session
if (!isset($_SESSION['username'])) {
    // If not logged in, redirect to the login page
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];  // Get the logged-in user's username

// Get the session code from the URL
if (isset($_GET['session_code'])) {
    $session_code = $_GET['session_code'];

    // Fetch the game session details using the session code
    $sql = "SELECT * FROM game_sessions WHERE session_code = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die('Error preparing statement: ' . $conn->error);
    }

    $stmt->bind_param("s", $session_code);
    $stmt->execute();
    $result = $stmt->get_result();
    $session = $result->fetch_assoc();

    if ($session) {
        $game_id = $session['game_id'];
        $host_username = $session['host_username'];
        $game_state = $session['game_state'];

        // If the logged-in user is not the host, they should only see the "Join" button
        if ($username != $host_username) {
            $is_host = false;
        } else {
            $is_host = true;
        }

        // Check if the user is already a participant in the session
        $sql_check_participant = "SELECT * FROM game_participants WHERE session_code = ? AND username = ?";
        $stmt_check_participant = $conn->prepare($sql_check_participant);

        if ($stmt_check_participant === false) {
            die('Error preparing check participant statement: ' . $conn->error);
        }

        $stmt_check_participant->bind_param("ss", $session_code, $username);
        $stmt_check_participant->execute();
        $participant_result = $stmt_check_participant->get_result();

        // If the user is not already a participant, insert them into the database
        if ($participant_result->num_rows === 0) {
            $sql_insert_participant = "INSERT INTO game_participants (session_code, username) VALUES (?, ?)";
            $stmt_insert_participant = $conn->prepare($sql_insert_participant);

            if ($stmt_insert_participant === false) {
                die('Error preparing insert participant statement: ' . $conn->error);
            }

            $stmt_insert_participant->bind_param("ss", $session_code, $username);
            $stmt_insert_participant->execute();
        }

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
    } else {
        echo "Session not found.";
        exit();
    }
} else {
    echo "Session code not provided.";
    exit();
}

// Handle starting the game (only allowed for the host)
if (isset($_POST['start_game'])) {
    if ($is_host) {
        // Update game state to 'in_progress'
        $sql_update = "UPDATE game_sessions SET game_state = 'in_progress' WHERE session_code = ?";
        $stmt_update = $conn->prepare($sql_update);
        
        if ($stmt_update === false) {
            die('Error preparing update statement: ' . $conn->error);
        }
        
        $stmt_update->bind_param("s", $session_code);
        $stmt_update->execute();

        // Redirect to the game page (or any page where the game will be played)
        header("Location: play_game.php?session_code=$session_code");
        exit();
    } else {
        echo "Only the host can start the game.";
    }
}

// Handle participant leaving the session
if (isset($_GET['leave'])) {
    $sql_remove_participant = "DELETE FROM game_participants WHERE session_code = ? AND username = ?";
    $stmt_remove_participant = $conn->prepare($sql_remove_participant);
    
    if ($stmt_remove_participant === false) {
        die('Error preparing remove participant statement: ' . $conn->error);
    }

    $stmt_remove_participant->bind_param("ss", $session_code, $username);
    $stmt_remove_participant->execute();
    
    header("Location: waiting_room.php?session_code=$session_code"); // Redirect after removal
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waiting Room - QuizPro</title>
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
        <h2>Waiting Room</h2>
        <p class="session-code">Session Code: <?php echo htmlspecialchars($session_code); ?></p>
        
        <div class="participants">
            <h3>Participants:</h3>
            <ul id="participants-list">
                <?php foreach ($participants as $participant) { ?>
                    <li><?php echo htmlspecialchars($participant); ?></li>
                <?php } ?>
            </ul>
        </div>
        
        <p>
            <?php if ($is_host) { ?>
                <form method="POST" action="">
                    <button type="submit" name="start_game">Start Game</button>
                </form>
            <?php } else { ?>
                <button>Waiting for the host to start the game...</button>
            <?php } ?>
        </p>

        <a href="?session_code=<?php echo htmlspecialchars($session_code); ?>&leave=true" style="color: red;">Leave Session</a>
    </div>

    <script>
        const sessionCode = "<?php echo htmlspecialchars($session_code); ?>";  // Get the session code dynamically

        // Poll the server every 5 seconds to check the updated list of participants
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
        }, 2000); // Poll every 5 seconds

        // Notify the server when the user is about to leave
        window.addEventListener('beforeunload', function () {
            fetch(`leave_participant.php?session_code=${sessionCode}&username=${"<?php echo $username; ?>"}&leave=true`, {
                method: 'GET',
            });
        });
    </script>

</body>
</html>

<?php
// Close connection
$conn->close();
?>
