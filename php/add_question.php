<?php
// Start een nieuwe sessie of hervat de bestaande sessie
session_start();

// Inclusief het databaseverbindingsbestand om interactie met de database mogelijk te maken
include('db_connection.php');

// Leid de gebruiker om naar de loginpagina als deze niet is ingelogd
if (!isset($_SESSION['username'])) {
    // Omleiden naar de loginpagina
    header('Location: login.php');
    exit(); // Zorg ervoor dat het script stopt na de omleiding
}

// Controleer of er een game-ID is opgegeven via het GET-verzoek
if (isset($_GET['game_id'])) {
    // Haal de game-ID op uit de URL
    $game_id = $_GET['game_id'];
} else {
    // Leid de gebruiker om naar het dashboard als er geen game-ID is opgegeven
    header('Location: dashboard.php');
    exit();
}

// Verwerk het toevoegen van een nieuwe vraag wanneer het formulier wordt ingediend
if (isset($_POST['add_question'])) {
    // Haal gegevens op uit het formulier en beveilig deze
    $question_text = $_POST['question_text'];
    $answer_a = $_POST['answer_a'];
    $answer_b = $_POST['answer_b'];
    $answer_c = $_POST['answer_c'];
    $answer_d = $_POST['answer_d'];
    $correct_answer = $_POST['correct_answer'];

    // Bereid de SQL-query voor om de nieuwe vraag in de database in te voegen
    $sql = "INSERT INTO questions (game_id, question_text, answer_a, answer_b, answer_c, answer_d, correct_answer) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql); // Bereid de SQL-instructie voor om SQL-injectie te voorkomen
    $stmt->bind_param("issssss", $game_id, $question_text, $answer_a, $answer_b, $answer_c, $answer_d, $correct_answer); // Koppel invoerparameters aan de query
    $stmt->execute(); // Voer de voorbereide instructie uit
    $stmt->close(); // Sluit de voorbereide instructie

    // Leid door naar de pagina met vragen bekijken voor de huidige game
    header("Location: view_questions.php?game_id=$game_id");
    exit(); // Zorg ervoor dat er geen verdere code wordt uitgevoerd na de omleiding
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> <!-- Stel de tekenencoding voor de pagina in -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Maak de pagina responsief -->
    <title>Add Question - QuizPro</title> <!-- Paginatitel -->
    <style>
        /* Reset standaardmarge en -opvulling voor alle elementen */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        /* Basisstyling voor de pagina-body */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }
        /* Styling voor de headersectie */
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
        /* Styling voor de formuliercontainer */
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
            resize: vertical; /* Alleen verticale aanpassing toestaan */
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
            background-color: #3B41D5; /* Iets donkerdere tint bij hover */
        }
    </style>
</head>
<body>

<header>
    <h1>Voeg een nieuwe vraag toe</h1> <!-- Koptekst titel -->
    <nav>
        <a href="dashboard.php">Terug naar Dashboard</a> <!-- Link naar het dashboard -->
    </nav>
</header>

<!-- Formulier om een nieuwe vraag toe te voegen -->
<div class="form-container">
    <form method="POST" action="">
        <!-- Invoer voor de vraagtekst -->
        <label for="question_text">Vraagtekst:</label>
        <textarea id="question_text" name="question_text" required></textarea>

        <!-- Invoer voor Antwoord A -->
        <label for="answer_a">Antwoord A:</label>
        <input type="text" id="answer_a" name="answer_a" required>

        <!-- Invoer voor Antwoord B -->
        <label for="answer_b">Antwoord B:</label>
        <input type="text" id="answer_b" name="answer_b" required>

        <!-- Invoer voor Antwoord C -->
        <label for="answer_c">Antwoord C:</label>
        <input type="text" id="answer_c" name="answer_c" required>

        <!-- Invoer voor Antwoord D -->
        <label for="answer_d">Antwoord D:</label>
        <input type="text" id="answer_d" name="answer_d" required>

        <!-- Dropdown om het juiste antwoord te selecteren -->
        <label for="correct_answer">Juiste Antwoord:</label>
        <select id="correct_answer" name="correct_answer" required>
            <option value="a">A</option>
            <option value="b">B</option>
            <option value="c">C</option>
            <option value="d">D</option>
        </select>

        <!-- Knop om het formulier in te dienen -->
        <button type="submit" name="add_question">Vraag toevoegen</button>
    </form>
</div>

</body>
</html>

<?php
// Sluit de databaseverbinding aan het einde van het script
$conn->close();
?>
