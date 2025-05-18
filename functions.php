<?php

function addNotification(mysqli $conn, int $userId, string $message): void {
    $stmt = $conn->prepare("
      INSERT INTO notifications (user_id, message)
      VALUES (?, ?)
    ");
    $stmt->bind_param('is', $userId, $message);
    $stmt->execute();
    $stmt->close();
}
