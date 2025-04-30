<?php
session_start();

// Check if the user is logged in, if not, redirect to index.php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Homepage</title>
</head>
<body>
    <h2>Welcome to your Homepage</h2>
    <p>You are logged in as <?php echo $_SESSION['username']; ?>.</p>

    <p><a href="logout.php">Logout</a></p>
</body>
</html>
