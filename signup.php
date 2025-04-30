<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
</head>
<body>
    <h2>Register</h2>
    <form action="register.php" method="POST">
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <label>Role:</label><br>
        <select name="role" required>
            <option value="">-- Select Role --</option>
            <option value="freelancer">Freelancer</option>
            <option value="commissioner">Commissioner</option>
        </select><br><br>

        <input type="submit" value="Register">
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</body>
</html>
