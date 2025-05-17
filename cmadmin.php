<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Handle activate/deactivate requests
if (isset($_GET['toggle']) && isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    $new_status = $_GET['toggle'] === 'deactivate' ? 'inactive' : 'active';
    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ? AND role = 'commissioner'");
    $stmt->bind_param("si", $new_status, $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: cmadmin.php");
    exit;
}

// Fetch all commissioners
$stmt = $conn->prepare("SELECT id, username, email, created_at, status FROM users WHERE role = 'commissioner'");
$stmt->execute();
$result = $stmt->get_result();
$commissioners = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Commissioners</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        h2 { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background-color: #f0f0f0; }
        a.button {
            padding: 6px 10px; background-color: #007BFF; color: white;
            text-decoration: none; border-radius: 4px;
        }
        a.button:hover { background-color: #0056b3; }
        .inactive { background-color: #dc3545; }
        .inactive:hover { background-color: #c82333; }
        .back-link { margin-top: 20px; display: inline-block; }
    </style>
</head>
<body>

<h2>Commissioners</h2>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Joined</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($commissioners as $c): ?>
            <tr>
                <td><?= htmlspecialchars($c['id']) ?></td>
                <td><?= htmlspecialchars($c['username']) ?></td>
                <td><?= htmlspecialchars($c['email']) ?></td>
                <td><?= htmlspecialchars($c['created_at']) ?></td>
                <td><?= htmlspecialchars($c['status']) ?></td>
                <td>
                    <?php if ($c['status'] === 'active'): ?>
                        <a class="button inactive" href="?toggle=deactivate&id=<?= $c['id'] ?>"
                           onclick="return confirm('Deactivate this user?');">Deactivate</a>
                    <?php else: ?>
                        <a class="button" href="?toggle=activate&id=<?= $c['id'] ?>"
                           onclick="return confirm('Reactivate this user?');">Reactivate</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<a href="admindash.php" class="back-link">← Back to Dashboard</a>

</body>
</html>
