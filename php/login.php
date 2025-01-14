<?php
// Start the session
session_start();

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

$error_message = "";

// Handle login form submission
if (isset($_POST['login'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Hash the password (using sha256 for this example, but bcrypt is better in real applications)
    $hashed_pass = hash('sha256', $pass);

    $sql = "SELECT * FROM users WHERE username = ? AND password = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ss", $user, $hashed_pass);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $_SESSION['username'] = $user;
            header("Location: index.php"); // Redirect to homepage or dashboard
            exit();
        } else {
            $error_message = "Invalid username or password.";
        }
        $stmt->close();
    } else {
        $error_message = "Database query failed.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - QuizPro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                eyeIcon.classList.remove("fa-eye-slash");
                eyeIcon.classList.add("fa-eye");
            } else {
                passwordInput.type = "password";
                eyeIcon.classList.remove("fa-eye");
                eyeIcon.classList.add("fa-eye-slash");
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
            <!-- Show Logout Button -->
            <a href="logout.php" class="join-button">Logout</a>
        <?php } else { ?>
            <!-- Show Login and Register Links -->
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
