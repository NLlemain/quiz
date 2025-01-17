<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

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

// Get username from session
$user_username = $_SESSION['username']; // Use username instead of user_id

// Handle quiz creation and question insertion
if (isset($_POST['create_quiz'])) {
    $quiz_name = $_POST['quiz_name'];

    // Insert the quiz into the 'games' table using username
    $sql = "INSERT INTO games (username, game_name) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $user_username, $quiz_name);
    $stmt->execute();
    $game_id = $stmt->insert_id; // Get the last inserted game_id
    $stmt->close();

    // Insert questions into 'questions' table
    $questions = $_POST['questions'];
    foreach ($questions as $question) {
        $question_text = $question['question_text'];
        $answer_a = $question['answer_a'];
        $answer_b = $question['answer_b'];
        $answer_c = $question['answer_c'];
        $answer_d = $question['answer_d'];
        $correct_answer = $question['correct_answer'];

        $sql = "INSERT INTO questions (game_id, question_text, answer_a, answer_b, answer_c, answer_d, correct_answer) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssss", $game_id, $question_text, $answer_a, $answer_b, $answer_c, $answer_d, $correct_answer);
        $stmt->execute();
        $stmt->close();
    }

    // Redirect to dashboard after creating quiz
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Quiz - QuizPro</title>
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
        .header .right-section {
            display: flex;
            padding-left: 900px;
            align-items: center;
        }
        h1 {
            text-align: center;
            color: #4F46E5;
            margin-top: 40px;
            font-size: 28px;
        }
        .quiz-form {
            width: 80%;
            margin: 40px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .quiz-form label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }
        .quiz-form input, .quiz-form textarea, .quiz-form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .quiz-form textarea {
            resize: vertical;
            height: 100px;
        }
        .quiz-form button {
            background-color: #4F46E5;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        .quiz-form button:hover {
            background-color: #3B41D5;
        }
        .question-container {
            margin-bottom: 20px;
        }
        .add-question-btn {
            background-color: #4F46E5;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        .add-question-btn:hover {
            background-color: #3B41D5;
        }
    </style>
</head>
<body>

<div class="content">
    <div class="quiz-form">
        <h1>Create a New Quiz</h1>
        <form method="POST" action="">
            <label for="quiz_name">Quiz Name:</label>
            <input type="text" id="quiz_name" name="quiz_name" required>
            
            <div id="questions-container">
                <div class="question-container">
                    <label for="question_text_1">Question 1:</label>
                    <textarea name="questions[0][question_text]" id="question_text_1" placeholder="Enter question" required></textarea>
                    
                    <label for="answer_a_1">Answer A:</label>
                    <input type="text" name="questions[0][answer_a]" placeholder="Answer A" required>
                    
                    <label for="answer_b_1">Answer B:</label>
                    <input type="text" name="questions[0][answer_b]" placeholder="Answer B" required>
                    
                    <label for="answer_c_1">Answer C:</label>
                    <input type="text" name="questions[0][answer_c]" placeholder="Answer C" required>
                    
                    <label for="answer_d_1">Answer D:</label>
                    <input type="text" name="questions[0][answer_d]" placeholder="Answer D" required>
                    
                    <label for="correct_answer_1">Correct Answer:</label>
                    <select name="questions[0][correct_answer]" required>
                        <option value="a">A</option>
                        <option value="b">B</option>
                        <option value="c">C</option>
                        <option value="d">D</option>
                    </select>
                </div>
            </div>
            
            <button type="button" class="add-question-btn" onclick="addQuestion()">Add Another Question</button>
            <br><br>
            <button type="submit" name="create_quiz" class="submit-btn">Create Quiz</button>
        </form>
    </div>
</div>

<script>
    let questionIndex = 1;

    function addQuestion() {
        const container = document.getElementById("questions-container");

        const newQuestion = document.createElement("div");
        newQuestion.classList.add("question-container");
        newQuestion.innerHTML = `
            <label for="question_text_${questionIndex + 1}">Question ${questionIndex + 1}:</label>
            <textarea name="questions[${questionIndex}][question_text]" id="question_text_${questionIndex + 1}" placeholder="Enter question" required></textarea>

            <label for="answer_a_${questionIndex + 1}">Answer A:</label>
            <input type="text" name="questions[${questionIndex}][answer_a]" placeholder="Answer A" required>

            <label for="answer_b_${questionIndex + 1}">Answer B:</label>
            <input type="text" name="questions[${questionIndex}][answer_b]" placeholder="Answer B" required>

            <label for="answer_c_${questionIndex + 1}">Answer C:</label>
            <input type="text" name="questions[${questionIndex}][answer_c]" placeholder="Answer C" required>

            <label for="answer_d_${questionIndex + 1}">Answer D:</label>
            <input type="text" name="questions[${questionIndex}][answer_d]" placeholder="Answer D" required>

            <label for="correct_answer_${questionIndex + 1}">Correct Answer:</label>
            <select name="questions[${questionIndex}][correct_answer]" required>
                <option value="a">A</option>
                <option value="b">B</option>
                <option value="c">C</option>
                <option value="d">D</option>
            </select>
        `;
        container.appendChild(newQuestion);
        questionIndex++;
    }
</script>

</body>
</html>

<?php
// Close connection
$conn->close();
?>
