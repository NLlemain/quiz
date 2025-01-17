<?php
// Start een nieuwe sessie of hervat de bestaande sessie
session_start();

// Controleer of de gebruiker is ingelogd. Als dat niet het geval is, wordt de gebruiker omgeleid naar de inlogpagina.
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Omleiden naar login.php
    exit(); // Stop verdere uitvoer van het script
}

// Databaseverbinding instellen
$servername = "localhost"; // Servernaam, standaard "localhost" voor XAMPP
$username = "root"; // Gebruikersnaam voor de database, standaard "root" in XAMPP
$password = ""; // Wachtwoord voor de database, standaard leeg in XAMPP
$dbname = "quizpro"; // De naam van de database

// Maak een verbinding met de MySQL-database
$conn = new mysqli($servername, $username, $password, $dbname);

// Controleer of de verbinding is gelukt
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // Stop het script en geef een foutmelding als de verbinding mislukt
}

// Haal de gebruikersnaam op uit de sessievariabelen
$user_username = $_SESSION['username']; // Gebruik de gebruikersnaam uit de sessie

// Verwerk de creatie van een nieuwe quiz en de invoeging van vragen als het formulier wordt ingediend
if (isset($_POST['create_quiz'])) {
    $quiz_name = $_POST['quiz_name']; // Haal de ingevoerde quiznaam op

    // Voeg de nieuwe quiz toe aan de tabel 'games' en koppel deze aan de gebruikersnaam
    $sql = "INSERT INTO games (username, game_name) VALUES (?, ?)";
    $stmt = $conn->prepare($sql); // Bereid de SQL-instructie voor
    $stmt->bind_param("ss", $user_username, $quiz_name); // Koppel de parameters (gebruikersnaam en quiznaam)
    $stmt->execute(); // Voer de SQL-query uit
    $game_id = $stmt->insert_id; // Verkrijg het ID van de net toegevoegde quiz
    $stmt->close(); // Sluit de voorbereide instructie

    // Voeg de vragen toe aan de tabel 'questions'
    $questions = $_POST['questions']; // Haal de vragen op uit het formulier
    foreach ($questions as $question) {
        // Haal de gegevens van elke vraag op
        $question_text = $question['question_text'];
        $answer_a = $question['answer_a'];
        $answer_b = $question['answer_b'];
        $answer_c = $question['answer_c'];
        $answer_d = $question['answer_d'];
        $correct_answer = $question['correct_answer'];

        // Voeg de vraag en antwoorden toe aan de tabel 'questions'
        $sql = "INSERT INTO questions (game_id, question_text, answer_a, answer_b, answer_c, answer_d, correct_answer) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql); // Bereid de SQL-instructie voor
        $stmt->bind_param("issssss", $game_id, $question_text, $answer_a, $answer_b, $answer_c, $answer_d, $correct_answer);
        $stmt->execute(); // Voer de SQL-query uit
        $stmt->close(); // Sluit de voorbereide instructie
    }

    // Leid de gebruiker om naar het dashboard nadat de quiz is aangemaakt
    header('Location: dashboard.php');
    exit(); // Stop verdere uitvoer van het script
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta-instellingen voor de HTML-pagina -->
    <meta charset="UTF-8"> <!-- Stel de tekenencoding in op UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Maak de pagina responsief -->
    <title>Create Quiz - QuizPro</title> <!-- Paginatitel -->
    <!-- CSS Styling -->
    <style>
        /* Basisreset voor alle elementen */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box; /* Zorg ervoor dat marges en opvulling in de breedte van elementen worden opgenomen */
        }
        /* Styling voor de pagina-body */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4; /* Lichtgrijze achtergrondkleur */
            color: #333; /* Donkergrijze tekstkleur */
        }
        /* Styling voor de header */
        .header {
            background-color: #fff;
            padding: 20px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #ddd;
        }
        /* Logo en navigatie in de header */
        .header a {
            text-decoration: none;
            color: #111827;
            margin: 0 10px;
        }
        .header .logo {
            font-size: 24px;
            font-weight: bold;
            color: #4F46E5; /* Blauwe kleur */
        }
        /* Styling voor de formuliercontainer */
        .quiz-form {
            width: 80%;
            margin: 40px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Lichte schaduw */
            border-radius: 8px; /* Ronde hoeken */
        }
        /* Styling voor labels en invoervelden */
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
            resize: vertical; /* Alleen verticale aanpassing toestaan */
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
    </style>
</head>
<body>

<div class="content">
    <!-- Formulier voor het aanmaken van een nieuwe quiz -->
    <div class="quiz-form">
        <h1>Create a New Quiz</h1>
        <form method="POST" action="">
            <!-- Veld voor de quiznaam -->
            <label for="quiz_name">Quiz Name:</label>
            <input type="text" id="quiz_name" name="quiz_name" required>
            
            <!-- Container voor vragen -->
            <div id="questions-container">
                <div class="question-container">
                    <!-- Vraagtekst -->
                    <label for="question_text_1">Question 1:</label>
                    <textarea name="questions[0][question_text]" id="question_text_1" placeholder="Enter question" required></textarea>
                    
                    <!-- Antwoorden A, B, C, en D -->
                    <label for="answer_a_1">Answer A:</label>
                    <input type="text" name="questions[0][answer_a]" placeholder="Answer A" required>
                    
                    <label for="answer_b_1">Answer B:</label>
                    <input type="text" name="questions[0][answer_b]" placeholder="Answer B" required>
                    
                    <label for="answer_c_1">Answer C:</label>
                    <input type="text" name="questions[0][answer_c]" placeholder="Answer C" required>
                    
                    <label for="answer_d_1">Answer D:</label>
                    <input type="text" name="questions[0][answer_d]" placeholder="Answer D" required>
                    
                    <!-- Correct antwoord selecteren -->
                    <label for="correct_answer_1">Correct Answer:</label>
                    <select name="questions[0][correct_answer]" required>
                        <option value="a">A</option>
                        <option value="b">B</option>
                        <option value="c">C</option>
                        <option value="d">D</option>
                    </select>
                </div>
            </div>
            
            <!-- Knop om een nieuwe vraag toe te voegen -->
            <button type="button" class="add-question-btn" onclick="addQuestion()">Add Another Question</button>
            <br><br>
            <!-- Knop om de quiz aan te maken -->
            <button type="submit" name="create_quiz" class="submit-btn">Create Quiz</button>
        </form>
    </div>
</div>

<script>
    let questionIndex = 1;

    // Functie om een nieuwe vraag toe te voegen aan het formulier
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
// Sluit de verbinding met de database
$conn->close();
?>
