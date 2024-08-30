<?php
// Include the database connection file
include("connection.php");

// Start the session
session_start();

// Check if the user is logged in; if not, redirect to login page
if (!isset($_SESSION['firstname'])) {
    header("Location: adminlogin.php");
    exit();
}

// Get user ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Delete user from database
$stmt = $conn->prepare("DELETE FROM signupusers WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: adminhome.php");
exit();
?>
