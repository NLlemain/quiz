<?php
session_start();
include('db_connection.php');

// Redirect to login page if not logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['game_id'])) {
    $game_id = $_GET['game_id'];
} else {
    header('Location: dashboard.php');
    exit();
}

// Handle question addition
if (isset($_POST['add_question'])) {
    $question_text = $_POST['question_text'];
    $answer_a = $_POST['answer_a'];
    $answer_b = $_POST['answer_b'];
    $answer_c = $_POST['answer_c'];
    $answer_d = $_POST['answer_d'];
    $correct_answer = $_POST['correct_answer'];

    // Insert the question into the 'questions' table
    $sql = "INSERT INTO questions (game_id, question_text, answer_a, answer_b, answer_c, answer_d, correct_answer) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssss", $game_id, $question_text, $answer_a, $answer_b, $answer_c, $answer_d, $correct_answer);
    $stmt->execute();
    $stmt->close();

    // Redirect to view questions page
    header("Location: view_questions.php?game_id=$game_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Question - QuizPro</title>
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
        header {
            background-color: #fff;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #ddd;
        }
        header h1 {
            color: #4F46E5;
            margin: 0;
        }
        header nav a {
            text-decoration: none;
            color: #4F46E5;
            font-size: 16px;
            margin-left: 20px;
        }
        header nav a:hover {
            text-decoration: underline;
        }
        .form-container {
            width: 80%;
            margin: 40px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .form-container label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }
        .form-container input, .form-container textarea, .form-container select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .form-container textarea {
            resize: vertical;
            height: 100px;
        }
        .form-container button {
            background-color: #4F46E5;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .form-container button:hover {
            background-color: #3B41D5;
        }
    </style>
</head>
<body>

<header>
    <h1>Add a New Question</h1>
    <nav>
        <a href="dashboard.php">Back to Dashboard</a>
    </nav>
</header>

<div class="form-container">
    <form method="POST" action="">
        <label for="question_text">Question Text:</label>
        <textarea id="question_text" name="question_text" required></textarea>

        <label for="answer_a">Answer A:</label>
        <input type="text" id="answer_a" name="answer_a" required>

        <label for="answer_b">Answer B:</label>
        <input type="text" id="answer_b" name="answer_b" required>

        <label for="answer_c">Answer C:</label>
        <input type="text" id="answer_c" name="answer_c" required>

        <label for="answer_d">Answer D:</label>
        <input type="text" id="answer_d" name="answer_d" required>

        <label for="correct_answer">Correct Answer:</label>
        <select id="correct_answer" name="correct_answer" required>
            <option value="a">A</option>
            <option value="b">B</option>
            <option value="c">C</option>
            <option value="d">D</option>
        </select>

        <button type="submit" name="add_question">Add Question</button>
    </form>
</div>

</body>
</html>

<?php
// Close connection
$conn->close();
?>
