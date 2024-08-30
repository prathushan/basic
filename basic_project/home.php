<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['firstname'])) {
    header("Location: login.php");
    exit();
}

// Get the user's first name
$firstname = $_SESSION['firstname'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .welcome-banner {
            background-color: #000;
            color: #fff;
            padding: 20px;
            text-align: center;
        }

        header {
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        nav {
            display: flex;
            gap: 15px;
        }

        nav a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        nav a:hover {
            text-decoration: underline;
        }

        .container {
            padding: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="welcome-banner">
        <h1>Welcome <?php echo htmlspecialchars($firstname); ?>!</h1>
    </div>

    <header>
        <div class="logo">
            <h2>Logo</h2>
        </div>
        <nav>
            <a href="home.php">Home</a>
            <a href="about.php">About Us</a>
            <a href="profile.php">Profile</a>
            <a href="contact.php">Contact Us</a>
            <a href="index.php">Logout</a>
        </nav>
    </header>


    <div class="container">
        <p>Thank you for logging in! Feel free to explore the website using the navigation links above.</p>
    </div>
</body>
</html>

