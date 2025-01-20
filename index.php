<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuizPro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: white;
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
        .content {
            display: flex;
            flex-direction: row-reverse;
        }
        .content .image-section {
            flex: 1;
            background-color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .content .image-section img {
            max-width: 100%;
            height: auto;
        }
        .content .main-section {
            flex: 1;
            padding: 40px;
            padding-top: 135px;
            text-align: center;
        }
        .main-section h1 {
            font-size: 36px;
            margin-bottom: 20px;
        }
        .main-section p {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .main-section input {
            padding: 10px;
            margin-bottom: 20px;
            margin-right: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 40%;
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
        .textforfeatures {
            padding-top: 180px;
            text-align: center;
        }
        .feature img {
            width: 12%; /* Take full width of the parent container */
            height: auto; /* Maintain aspect ratio */
            border-radius: 10px;
            object-fit: cover; /* Ensure image fits within the box without distortion */
            margin-top: 20px;
        }
        .features {
            display: flex;
            justify-content: space-around;
            margin-bottom: 80px;
        }
        .feature {
            background-color: #fff;
            padding: 10px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 25%;
            margin-top: 20px;
        }
        .feature h3 {
            font-size: 24px;
            margin-bottom: 10px;
            margin-top: 10px;
        }
        .feature p {
            font-size: 16px;
            color: #4B5563;
        }
        .popular-quizzes {
            margin: 150px 0;
            text-align: center;
        }
        .quiz {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: inline-block;
            margin: 20px;
            margin-left: -750px;
            overflow: hidden; /* Ensure content doesn't overflow */
            max-width: 270px;
        }

        .quiz img {
            width: 100%; /* Take full width of the parent container */
            height: auto; /* Maintain aspect ratio */
            border-radius: 10px;
            object-fit: cover; /* Ensure image fits within the box without distortion */
        }

        .quiz h4 {
            font-size: 20px;
            margin: 10px 0;
        }
        .quiz p {
            font-size: 16px;
            color: #4B5563;
        }
        .promo-section {
            background-color: #4F46E5;
            color: #fff;
            text-align: center;
            padding: 40px;
            margin: 40px 0;
            max-width: 800px;
            margin-left: 350px;
            border-radius: 15px;
        }
        .promo-section h2 {
            font-size: 32px;
            margin-bottom: 20px;
        }
        .promo-section p {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .promo-section .get-started-button {
            background-color: #fff;
            color: #4F46E5;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
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

        .footer .footer-section div {
            width: 20%;
            text-align: left;
        }

        .footer .footer-section h4 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .footer .footer-section p {
            font-size: 14px;
            color: #fff;
            margin: 5px 0;
            color: #9CA3AF
        }

        .footer .social-icons {
            margin-top: 10px;
        }

        .footer .social-icons a {
            margin: 0 10px;
        }

        .footer .social-icons img {
            width: 30px;
        }

        /* Notification styles */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #4F46E5;
            color: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            font-size: 16px;
            display: none;
            z-index: 9999;
        }

        .notification.show {
            display: block;
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @media (max-width: 768px) {
    .header {
        flex-direction: column;
        align-items: flex-start;
    }
    .header .right-section {
        padding-left: 0;
        margin-top: 10px;
    }
    .content {
        flex-direction: column;
        padding: 20px;
    }
    .content .image-section {
        order: 2;
        padding: 20px 0;
    }
    .content .main-section {
        order: 1;
        padding: 20px;
    }
    .main-section input {
        width: 100%;
        margin-right: 0;
    }
    .features {
        flex-direction: column;
        align-items: center;
    }
    .feature {
        width: 80%;
        margin: 10px 0;
    }
    .popular-quizzes {
        margin: 50px 0;
    }
    .quiz {
        width: 90%;
    }
    .promo-section {
        margin-left: 0;
        padding: 20px;
    }
    .footer .footer-section {
        flex-direction: column;
        align-items: center;
    }
    .footer .footer-section div {
        width: 100%;
        text-align: center;
        margin-bottom: 20px;
    }
}
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">QuizPro</div>
        <a href="#">Browse</a>
        <a href="create_quiz.php">Create</a>
        <a href="#">Teach</a>
        <a href="#">Help</a>
        <div class="right-section">
            <?php if (isset($_SESSION['username'])) { ?>
                <a href="logout.php" class="join-button">Logout</a>
            <?php } else { ?>
                <a href="login.php" class="login">Log In</a>
                <a href="register.php" class="join-button">Sign Up</a>
            <?php } ?>
        </div>
    </div>
    
    <div class="content">
        <div class="image-section">
            <img src="https://strongtestimonials.com/wp-content/uploads/2021/02/wordpress-quiz-plugins.jpg" alt="Placeholder Image">
        </div>
        <div class="main-section">
            <h1>Make Learning Awesome!</h1>
            <p>Create, play and share engaging quizzes. Perfect for classrooms, training, and fun!</p>
            <input type="text" placeholder="Enter Code">
            <button class="join-button">Join Game</button>
        </div>
    </div>
    
    <div class="textforfeatures">
        <H1>Everything you need for engaging quizzes</H1>
    </div>
    
    <div class="features">
        <div class="feature">
            <img src="feature1.png" alt="features Image">
            <h3>Easy Creation</h3>
            <p>Create quizzes in minutes with our intuitive editor.</p>
        </div>
        <div class="feature">
            <img src="feature2.png" alt="features Image">
            <h3>Live Gameplay</h3>
            <p>Join live games and see real-time feedback in the classroom.</p>
        </div>
        <div class="feature">
            <img src="feature3.png" alt="features Image">
            <h3>Detailed Reports</h3>
            <p>Get insights with comprehensive analytics and reports.</p>
        </div>
    </div>

    <div class="popular-quizzes">
        <h1>Popular Quizzes</h1>
        <div class="quiz">
            <img src="quiz img.png" alt="Quiz Image">
            <h4>Math Challenge</h4>
            <p>Test your math skills with this challenging quiz.</p>
        </div>
    </div>
    
    <div class="promo-section">
        <h2>Ready to make learning fun?</h2>
        <p>Join millions of teachers and students who are already using our platform to create engaging learning experiences.</p>
        <button class="get-started-button">Get Started Free</button>
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

    <!-- Notification -->
    <div id="loginNotification" class="notification">Welcome back, <?php echo $_SESSION['username']; ?>!</div>

    <script>
        // Show notification if logged in
        <?php if (isset($_SESSION['username'])) { ?>
            const notification = document.getElementById('loginNotification');
            notification.classList.add('show');
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000); // Hide after 3 seconds
        <?php } ?>
    </script>
</body>
</html>
