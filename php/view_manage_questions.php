<?php
session_start();  // Start de sessie om de inlogstatus te controleren
include('db_connection.php');  // Voeg de databaseverbinding toe

// Redirect naar de loginpagina als de gebruiker niet is ingelogd
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();  // Stop de uitvoering van de rest van de code
}

// Controleer of er een game_id in de URL staat, anders redirect naar dashboard
if (isset($_GET['game_id'])) {
    $game_id = $_GET['game_id'];  // Verkrijg het game_id van de URL
} else {
    header('Location: dashboard.php');  // Als geen game_id, redirect naar dashboard
    exit();
}

// Haal alle vragen op die horen bij het opgegeven game_id
$sql = "SELECT * FROM questions WHERE game_id = ?";  // SQL-query om vragen op te halen
$stmt = $conn->prepare($sql);  // Bereid de SQL-query voor
$stmt->bind_param("i", $game_id);  // Bind het game_id als parameter
$stmt->execute();  // Voer de query uit
$result = $stmt->get_result();  // Verkrijg het resultaat van de query
$questions = $result->fetch_all(MYSQLI_ASSOC);  // Zet de vragen om in een associatieve array
$stmt->close();  // Sluit de prepared statement

// Verwijder een vraag als er een delete_question_id in de URL staat
if (isset($_GET['delete_question_id'])) {
    $question_id = $_GET['delete_question_id'];  // Verkrijg het vraag-id uit de URL

    // Verwijder de vraag uit de database
    $sql = "DELETE FROM questions WHERE id = ?";  // SQL-query om de vraag te verwijderen
    $stmt = $conn->prepare($sql);  // Bereid de SQL-query voor
    $stmt->bind_param("i", $question_id);  // Bind het vraag-id als parameter
    $stmt->execute();  // Voer de query uit
    $stmt->close();  // Sluit de prepared statement

    // Redirect terug naar dezelfde pagina na het verwijderen van de vraag
    header("Location: view_manage_questions.php?game_id=$game_id");
    exit();
}

// Verwerk de bewerking van een vraag
if (isset($_POST['edit_question'])) {
    // Verkrijg de gegevens van het bewerkingsformulier
    $question_id = $_POST['question_id'];
    $question_text = $_POST['question_text'];
    $answer_a = $_POST['answer_a'];
    $answer_b = $_POST['answer_b'];
    $answer_c = $_POST['answer_c'];
    $answer_d = $_POST['answer_d'];
    $correct_answer = $_POST['correct_answer'];

    // Update de vraag in de database
    $sql = "UPDATE questions SET question_text = ?, answer_a = ?, answer_b = ?, answer_c = ?, answer_d = ?, correct_answer = ? WHERE id = ?";  // SQL-query voor het bijwerken van de vraag
    $stmt = $conn->prepare($sql);  // Bereid de SQL-query voor
    $stmt->bind_param("ssssssi", $question_text, $answer_a, $answer_b, $answer_c, $answer_d, $correct_answer, $question_id);  // Bind de parameters
    $stmt->execute();  // Voer de query uit
    $stmt->close();  // Sluit de prepared statement

    // Redirect naar de pagina voor het beheren van vragen
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
        /* Stijlinstellingen voor de pagina */
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
    <h1>Manage Questions</h1>  <!-- Titel van de pagina -->
    <nav>
        <a href="dashboard.php">Back to Dashboard</a>  <!-- Link naar het dashboard -->
    </nav>
</header>

<div class="questions-container">
    <?php if ($questions): ?>  <!-- Als er vragen zijn -->
        <?php foreach ($questions as $question): ?>  <!-- Voor elke vraag -->
            <div class="question-item">
                <h3>Question: <?php echo htmlspecialchars($question['question_text']); ?></h3>  <!-- Toon de vraagtekst -->
                <p><strong>Answer A:</strong> <?php echo htmlspecialchars($question['answer_a']); ?></p>  <!-- Toon het antwoord A -->
                <p><strong>Answer B:</strong> <?php echo htmlspecialchars($question['answer_b']); ?></p>  <!-- Toon het antwoord B -->
                <p><strong>Answer C:</strong> <?php echo htmlspecialchars($question['answer_c']); ?></p>  <!-- Toon het antwoord C -->
                <p><strong>Answer D:</strong> <?php echo htmlspecialchars($question['answer_d']); ?></p>  <!-- Toon het antwoord D -->
                <p><strong>Correct Answer:</strong> <?php echo strtoupper(htmlspecialchars($question['correct_answer'])); ?></p>  <!-- Toon het juiste antwoord -->
                <div class="manage-buttons">
                    <a href="view_manage_questions.php?game_id=<?php echo $game_id; ?>&delete_question_id=<?php echo $question['id']; ?>">Delete</a>  <!-- Link om de vraag te verwijderen -->
                    <a href="view_manage_questions.php?game_id=<?php echo $game_id; ?>&edit_question_id=<?php echo $question['id']; ?>">Edit</a>  <!-- Link om de vraag te bewerken -->
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>  <!-- Als er geen vragen zijn -->
        <p>No questions found for this quiz.</p>  <!-- Toon een bericht dat er geen vragen zijn -->
    <?php endif; ?>
</div>

<?php if (isset($_GET['edit_question_id'])): ?>  <!-- Als er een vraag is om te bewerken -->
    <?php
    // Haal de gegevens van de vraag op om te bewerken
    $question_id = $_GET['edit_question_id'];
    $sql = "SELECT * FROM questions WHERE id = ?";  // SQL-query om de vraag op te halen
    $stmt = $conn->prepare($sql);  // Bereid de SQL-query voor
    $stmt->bind_param("i", $question_id);  // Bind het vraag-id als parameter
    $stmt->execute();  // Voer de query uit
    $result = $stmt->get_result();  // Verkrijg het resultaat van de query
    $question = $result->fetch_assoc();  // Verkrijg de gegevens van de vraag
    $stmt->close();  // Sluit de prepared statement
    ?>

    <div class="edit-form">
        <h2>Edit Question</h2>  <!-- Titel voor het bewerken van de vraag -->
        <form method="POST" action="">  <!-- Formulier om de vraag te bewerken -->
            <input type="hidden" name="question_id" value="<?php echo $question['id']; ?>">  <!-- Verborgen veld voor het vraag-id -->

            <label for="question_text">Question Text:</label>
            <textarea name="question_text" id="question_text" required><?php echo htmlspecialchars($question['question_text']); ?></textarea>  <!-- Veld voor de vraagtekst -->

            <label for="answer_a">Answer A:</label>
            <input type="text" name="answer_a" id="answer_a" value="<?php echo htmlspecialchars($question['answer_a']); ?>" required>  <!-- Veld voor antwoord A -->

            <label for="answer_b">Answer B:</label>
            <input type="text" name="answer_b" id="answer_b" value="<?php echo htmlspecialchars($question['answer_b']); ?>" required>  <!-- Veld voor antwoord B -->

            <label for="answer_c">Answer C:</label>
            <input type="text" name="answer_c" id="answer_c" value="<?php echo htmlspecialchars($question['answer_c']); ?>" required>  <!-- Veld voor antwoord C -->

            <label for="answer_d">Answer D:</label>
            <input type="text" name="answer_d" id="answer_d" value="<?php echo htmlspecialchars($question['answer_d']); ?>" required>  <!-- Veld voor antwoord D -->

            <label for="correct_answer">Correct Answer:</label>
            <select name="correct_answer" id="correct_answer" required>  <!-- Dropdown voor het juiste antwoord -->
                <option value="a" <?php echo $question['correct_answer'] == 'a' ? 'selected' : ''; ?>>A</option>  <!-- Antwoord A selecteren -->
                <option value="b" <?php echo $question['correct_answer'] == 'b' ? 'selected' : ''; ?>>B</option>  <!-- Antwoord B selecteren -->
                <option value="c" <?php echo $question['correct_answer'] == 'c' ? 'selected' : ''; ?>>C</option>  <!-- Antwoord C selecteren -->
                <option value="d" <?php echo $question['correct_answer'] == 'd' ? 'selected' : ''; ?>>D</option>  <!-- Antwoord D selecteren -->
            </select>

            <button type="submit" name="edit_question">Save Changes</button>  <!-- Button om de wijzigingen op te slaan -->
        </form>
    </div>
<?php endif; ?>

</body>
</html>

<?php
// Sluit de databaseverbinding
$conn->close();
?>
