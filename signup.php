<?php
require 'db.php';

$role = isset($_GET['role']) ? htmlspecialchars($_GET['role']) : '';
$validRoles = ['freelancer', 'commissioner'];

if (!in_array($role, $validRoles)) {
    die("Invalid role selected. <a href='index.php'>Go back</a>");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = htmlspecialchars(trim($_POST["username"]));
    $email = htmlspecialchars(trim($_POST["email"]));
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // Check if user exists
    $check = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $check->bind_param("ss", $username, $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "Username or Email already taken. <a href='signup.php?role=$role'>Try again</a>";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, email, role, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $role, $password);
        if ($stmt->execute()) {
            echo "Registration successful. <a href='login.php?role=$role'>Go to login</a>";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }

    $check->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo ucfirst($role); ?> Sign Up</title>
</head>
<body>
    <h2><?php echo ucfirst($role); ?> Sign Up</h2>

    <form method="post">
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <input type="submit" value="Sign Up">
    </form>

    <p>Already have an account? <a href="login.php?role=<?php echo $role; ?>">Log in</a></p>
</body>
</html>
