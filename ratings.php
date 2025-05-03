<?php
session_start();
require 'db.php';

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT rating, comment, created_at FROM ratings WHERE freelancer_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

echo "<h3>Client Ratings</h3>";

if ($result->num_rows === 0) {
    echo "<p>No ratings yet.</p>";
} else {
    while ($row = $result->fetch_assoc()) {
        echo "<div style='margin-bottom:15px;'>";
        echo "<strong>Rating:</strong> " . intval($row['rating']) . "/5<br>";
        echo "<em>" . htmlspecialchars($row['comment']) . "</em><br>";
        echo "<small>" . date("F j, Y", strtotime($row['created_at'])) . "</small>";
        echo "</div><hr>";
    }
}

$stmt->close();
$conn->close();
?>
