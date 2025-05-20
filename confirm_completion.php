<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'commissioner') {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['commission_id'])) {
    $commission_id = intval($_POST['commission_id']);

    $stmt = $conn->prepare("UPDATE commissions SET status = 'Awaiting Final' WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $commission_id, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();
}

header("Location: mycommissions.php");
exit;
?>
