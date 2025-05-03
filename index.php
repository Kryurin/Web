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
    <title>Welcome</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 100px;
        }

        h2 {
            margin-bottom: 20px;
        }

        .login-button {
            padding: 15px 30px;
            margin: 10px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
        }

        .admin { background-color: #f44336; color: white; }
        .freelancer { background-color: #4CAF50; color: white; }
        .commissioner { background-color: #2196F3; color: white; }
    </style>
</head>
<body>
    <h2>Welcome to the Site</h2>
    <p>Select your login type:</p>

    <!-- Freelancer -->
<form action="login.php" method="get" style="display:inline;">
    <input type="hidden" name="role" value="freelancer">
    <button class="login-button freelancer" type="submit">Freelancer</button>
</form>

<!-- Commissioner -->
<form action="login.php" method="get" style="display:inline;">
    <input type="hidden" name="role" value="commissioner">
    <button class="login-button commissioner" type="submit">Commissioner</button>
</form>

<!-- Admin -->
<form action="login.php" method="get" style="display:inline;">
    <input type="hidden" name="role" value="admin">
    <button class="login-button admin" type="submit">Admin</button>
</form>

</body>
</html>
