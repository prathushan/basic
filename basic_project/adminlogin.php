<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login Page</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        form {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .form-row {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #007BFF;
        }

        button.btn {
            background-color: #007BFF;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 1em;
            margin-top: 10px;
        }

        button.btn:hover {
            background-color: #0056b3;
        }

        p {
            margin-top: 20px;
        }

        p a {
            color: #007BFF;
            text-decoration: none;
        }

        p a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Welcome to Admin Login Page</h1>
    <form action="#" method="POST">
        <div class="form-row">
            <div>
                <label for="email">Enter Admin Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="password">Enter Admin Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div>
                <button type="submit" class="btn" name="btn">Login</button>
            </div>
        </div>
        <br>
        <p><a href="index.php">Back to Home</a></p>
    </form>
</body>
</html>

<?php
include("connection.php");
session_start();

if (isset($_POST['btn'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Corrected query and logic
    $stmt = $conn->prepare("SELECT firstname, email, password FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($db_firstname, $db_email, $db_password);
        $stmt->fetch();

        // Assuming password is stored as plain text for admin (although not recommended)
        if ($password == $db_password) {
            $_SESSION['firstname'] = $db_firstname;  // Store first name in session
            header("Location: adminhome.php");
            exit();
        } else {
            echo "Wrong password";
        }
    } else {
        echo "Email did not match with records";
    }
}
?>
