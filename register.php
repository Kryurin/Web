<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = htmlspecialchars(trim($_POST["username"]));
    $email = htmlspecialchars(trim($_POST["email"]));
    $role = htmlspecialchars(trim($_POST["role"]));
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    if (empty($role)) {
        die("Please select a role. <a href='signup.php'>Back</a>");
    }

    // Check if user already exists
    $check = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $check->bind_param("ss", $username, $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "Username or Email already taken. <a href='signup.php'>Try again</a>";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, email, role, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $role, $password);
        if ($stmt->execute()) {
            echo "Registration successful. <a href='login.php'>Go to login</a>";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }

    $check->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
