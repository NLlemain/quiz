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

// Fetch user data (optional, you can skip this if you don't need extra user info)
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Fetch the user's quizzes by `username` (not `user_id`)
$sql_quizzes = "SELECT * FROM games WHERE username = ?";
$stmt_quizzes = $conn->prepare($sql_quizzes);
$stmt_quizzes->bind_param("s", $username);
$stmt_quizzes->execute();
$quizzes = $stmt_quizzes->get_result();

// Start quiz logic
if (isset($_POST['start_quiz'])) {
    $game_id = $_POST['game_id'];
    
    // Generate a unique 9-digit code
    do {
        $session_code = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 9);
        // Check if the code already exists
        $check_code = "SELECT * FROM game_sessions WHERE session_code = ?";
        $stmt_check_code = $conn->prepare($check_code);
        $stmt_check_code->bind_param("s", $session_code);
        $stmt_check_code->execute();
        $stmt_check_code->store_result();
    } while ($stmt_check_code->num_rows > 0); // Keep generating a new code if the code already exists
    
    // Insert new game session with the generated session code
    $sql_start_quiz = "INSERT INTO game_sessions (game_id, host_username, session_code, game_state) 
                       VALUES (?, ?, ?, 'waiting')";
    $stmt_start_quiz = $conn->prepare($sql_start_quiz);
    $stmt_start_quiz->bind_param("iss", $game_id, $username, $session_code);
    $stmt_start_quiz->execute();
    $stmt_start_quiz->close();
    
    // Redirect to the session page (or game page) with the session code
    header("Location: start_quiz.php?session_code=$session_code");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - QuizPro</title>
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
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4F46E5;
            color: white;
        }
        td a {
            text-decoration: none;
            color: #4F46E5;
            font-weight: bold;
        }
        td a:hover {
            color: #3B41D5;
        }
        footer {
            background-color: #111827;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 40px;
        }
        .main-section .join-button {
            background-color: #4F46E5;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
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
                <!-- Login and Sign Up buttons styled -->
                <a href="login.php" class="join-button">Log In</a>
                <a href="register.php" class="join-button">Sign Up</a>
            <?php } ?>
        </div>
    </div>

    <h2>Your Quizzes</h2>

    <table>
        <thead>
            <tr>
                <th>Quiz Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($quiz = $quizzes->fetch_assoc()) { 
                $game_id = $quiz['id'];
                // Check the game session state before showing the start button
                $sql_check_session = "SELECT game_state FROM game_sessions WHERE game_id = ? LIMIT 1";
                $stmt_check_session = $conn->prepare($sql_check_session);
                $stmt_check_session->bind_param("i", $game_id);
                $stmt_check_session->execute();
                $stmt_check_session->bind_result($game_state);
                $stmt_check_session->fetch();
                $stmt_check_session->close();
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($quiz['game_name']); ?></td>
                    <td>
                        <?php if ($game_state != 'in_progress') { ?>
                            <form method="POST" action="" style="display:inline;">
                                <input type="hidden" name="game_id" value="<?php echo $quiz['id']; ?>">
                                <button type="submit" name="start_quiz" class="join-button">Start Quiz</button>
                            </form>
                        <?php } else { ?>
                            <span>Quiz Started</span>
                        <?php } ?>
                        | <a href="add_question.php?game_id=<?php echo $quiz['id']; ?>">Add Question</a> |
                        <a href="view_manage_questions.php?game_id=<?php echo $quiz['id']; ?>">View Questions</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>

<?php
// Close connection
$conn->close();
?>
