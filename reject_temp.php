<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'commissioner') {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['commission_id'])) {
    $commission_id = $_POST['commission_id'];

    // Set status back and clear temp_file
    $stmt = $conn->prepare("UPDATE commissions SET status = 'In Progress', temp_file = NULL WHERE id = ?");
    $stmt->bind_param("i", $commission_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: mycommissions.php");
exit;
