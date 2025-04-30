<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = htmlspecialchars(trim($_POST["username"]));
    $password = $_POST["password"];

    // Check if the user exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Store session data
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: homepage.php');
            exit;
        } else {
            echo "Invalid password. <a href='login.php'>Try again</a>";
        }
    } else {
        echo "No such user found. <a href='login.php'>Try again</a>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <form action="login.php" method="POST">
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <input type="submit" value="Login">
    </form>

    <p>Don't have an account? <a href="signup.php">Sign up</a></p>
</body>
</html>
