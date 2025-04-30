<?php
session_start();

// If user is logged in, redirect to the homepage
if (isset($_SESSION['user_id'])) {
    header('Location: homepage.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Page</title>
</head>
<body>
    <h2>Welcome to the Site</h2>
    <p>Please <a href="login.php">log in</a> to continue.</p>
</body>
</html>
