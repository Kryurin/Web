<?php
session_start();
require 'db.php';

$role = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $role = htmlspecialchars($_POST['role'] ?? '');
} else {
    $role = htmlspecialchars($_GET['role'] ?? '');
}

$validRoles = ['freelancer', 'commissioner', 'admin'];

if (!in_array($role, $validRoles)) {
    die("Invalid role. <a href='index.php'>Go back</a>");
}

// Handle login submission
$error = '';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = htmlspecialchars(trim($_POST["username"]));
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND role = ?");
    $stmt->bind_param("ss", $username, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: admindash.php");
            } elseif ($user['role'] === 'freelancer') {
                header("Location: flhome.php");
            } elseif ($user['role'] === 'commissioner') {
                header("Location: chome.php");
            }
            exit;
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "User not found.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo ucfirst($role); ?> Login</title>
</head>
<body>
    <h2><?php echo ucfirst($role); ?> Login</h2>

    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="post">
        <input type="hidden" name="role" value="<?php echo $role; ?>">

        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <input type="submit" value="Login">
    </form>

    <p>Don't have an account? 
        <a href="signup.php?role=<?php echo $role; ?>">
            Sign up as a <?php echo $role; ?>
        </a>
    </p>
</body>
</html>
