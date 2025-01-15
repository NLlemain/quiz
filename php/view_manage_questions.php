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

// Fetch all questions for this game
$sql = "SELECT * FROM questions WHERE game_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $game_id);
$stmt->execute();
$result = $stmt->get_result();
$questions = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Handle question removal
if (isset($_GET['delete_question_id'])) {
    $question_id = $_GET['delete_question_id'];

    // Delete question from database
    $sql = "DELETE FROM questions WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $question_id);
    $stmt->execute();
    $stmt->close();

    // Redirect back to the same page
    header("Location: view_manage_questions.php?game_id=$game_id");
    exit();
}

// Handle question editing
if (isset($_POST['edit_question'])) {
    $question_id = $_POST['question_id'];
    $question_text = $_POST['question_text'];
    $answer_a = $_POST['answer_a'];
    $answer_b = $_POST['answer_b'];
    $answer_c = $_POST['answer_c'];
    $answer_d = $_POST['answer_d'];
    $correct_answer = $_POST['correct_answer'];

    // Update the question in the database
    $sql = "UPDATE questions SET question_text = ?, answer_a = ?, answer_b = ?, answer_c = ?, answer_d = ?, correct_answer = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $question_text, $answer_a, $answer_b, $answer_c, $answer_d, $correct_answer, $question_id);
    $stmt->execute();
    $stmt->close();

    // Redirect to view/manage questions page
    header("Location: view_manage_questions.php?game_id=$game_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Questions - QuizPro</title>
    <style>
        /* Include your styles (same as before) */
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
        .questions-container {
            width: 80%;
            margin: 40px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .question-item {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .question-item h3 {
            color: #4F46E5;
        }
        .question-item p {
            font-size: 14px;
            margin-bottom: 10px;
        }
        .manage-buttons a {
            text-decoration: none;
            color: #fff;
            background-color: #E57373;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 14px;
        }
        .manage-buttons a:hover {
            background-color: #D32F2F;
        }
        .edit-form {
            margin-top: 20px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .edit-form input, .edit-form select, .edit-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .edit-form button {
            background-color: #4F46E5;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 16px;
        }
    </style>
</head>
<body>

<header>
    <h1>Manage Questions</h1>
    <nav>
        <a href="dashboard.php">Back to Dashboard</a>
    </nav>
</header>

<div class="questions-container">
    <?php if ($questions): ?>
        <?php foreach ($questions as $question): ?>
            <div class="question-item">
                <h3>Question: <?php echo htmlspecialchars($question['question_text']); ?></h3>
                <p><strong>Answer A:</strong> <?php echo htmlspecialchars($question['answer_a']); ?></p>
                <p><strong>Answer B:</strong> <?php echo htmlspecialchars($question['answer_b']); ?></p>
                <p><strong>Answer C:</strong> <?php echo htmlspecialchars($question['answer_c']); ?></p>
                <p><strong>Answer D:</strong> <?php echo htmlspecialchars($question['answer_d']); ?></p>
                <p><strong>Correct Answer:</strong> <?php echo strtoupper(htmlspecialchars($question['correct_answer'])); ?></p>
                <div class="manage-buttons">
                    <a href="view_manage_questions.php?game_id=<?php echo $game_id; ?>&delete_question_id=<?php echo $question['id']; ?>">Delete</a>
                    <a href="view_manage_questions.php?game_id=<?php echo $game_id; ?>&edit_question_id=<?php echo $question['id']; ?>">Edit</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No questions found for this quiz.</p>
    <?php endif; ?>
</div>

<?php if (isset($_GET['edit_question_id'])): ?>
    <?php
    // Fetch question data for editing
    $question_id = $_GET['edit_question_id'];
    $sql = "SELECT * FROM questions WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $question_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $question = $result->fetch_assoc();
    $stmt->close();
    ?>

    <div class="edit-form">
        <h2>Edit Question</h2>
        <form method="POST" action="">
            <input type="hidden" name="question_id" value="<?php echo $question['id']; ?>">

            <label for="question_text">Question Text:</label>
            <textarea name="question_text" id="question_text" required><?php echo htmlspecialchars($question['question_text']); ?></textarea>

            <label for="answer_a">Answer A:</label>
            <input type="text" name="answer_a" id="answer_a" value="<?php echo htmlspecialchars($question['answer_a']); ?>" required>

            <label for="answer_b">Answer B:</label>
            <input type="text" name="answer_b" id="answer_b" value="<?php echo htmlspecialchars($question['answer_b']); ?>" required>

            <label for="answer_c">Answer C:</label>
            <input type="text" name="answer_c" id="answer_c" value="<?php echo htmlspecialchars($question['answer_c']); ?>" required>

            <label for="answer_d">Answer D:</label>
            <input type="text" name="answer_d" id="answer_d" value="<?php echo htmlspecialchars($question['answer_d']); ?>" required>

            <label for="correct_answer">Correct Answer:</label>
            <select name="correct_answer" id="correct_answer" required>
                <option value="a" <?php echo $question['correct_answer'] == 'a' ? 'selected' : ''; ?>>A</option>
                <option value="b" <?php echo $question['correct_answer'] == 'b' ? 'selected' : ''; ?>>B</option>
                <option value="c" <?php echo $question['correct_answer'] == 'c' ? 'selected' : ''; ?>>C</option>
                <option value="d" <?php echo $question['correct_answer'] == 'd' ? 'selected' : ''; ?>>D</option>
            </select>

            <button type="submit" name="edit_question">Save Changes</button>
        </form>
    </div>
<?php endif; ?>

</body>
</html>

<?php
// Close connection
$conn->close();
?>
