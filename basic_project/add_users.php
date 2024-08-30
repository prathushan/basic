<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Page</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="signup-container">
      
        <h2>Add New Users </h2>

        <form action="add_users.php" method="post" >
            <div class="form-row">
                <div class="form-group">
                    <label for="first-name">First Name:</label>
                    <input type="text" id="first-name" name="first-name" required>
                </div>
                <div class="form-group">
                    <label for="last-name">Last Name:</label>
                    <input type="text" id="last-name" name="last-name" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="contact-number">Contact Number:</label>
                <input type="tel" id="contact-number" name="contact" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm-password">Confirm Password:</label>
                    <input type="password" id="confirm-password" name="confirm-password" required>
                </div>
            </div>

            <button type="submit" class="btn" name="btn">Create Account</button>
        </form>

        
        <p><a href="adminhome.php">Back to Home</a></p>
    </div>
</body>
</html>

<?php
include("connection.php");

if (isset($_POST['btn'])) {

    // Correctly accessing POST variables with hyphens
    $firstname = $_POST['first-name'];
    $lastname = $_POST['last-name'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $password = password_hash($_POST['password'],PASSWORD_DEFAULT);
    $cpassword = password_hash($_POST['confirm-password'],PASSWORD_DEFAULT);

    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO signupusers (firstName, lastName, email, contact, password, confirmPwd) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $firstname, $lastname, $email, $contact, $password, $cpassword);

    // Executing the statement and checking for success
    if ($stmt->execute()) {
        header("Location:adminhome.php");
    } else {
        echo "Login failed";
    }
}
?>